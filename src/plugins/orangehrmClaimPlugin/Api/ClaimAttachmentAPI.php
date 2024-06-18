<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Claim\Api;

use Exception;
use OpenApi\Annotations as OA;
use OrangeHRM\Claim\Api\Model\ClaimAttachmentModel;
use OrangeHRM\Claim\Api\Traits\ClaimRequestAPIHelperTrait;
use OrangeHRM\Claim\Dto\ClaimAttachmentSearchFilterParams;
use OrangeHRM\Claim\Dto\PartialClaimAttachment;
use OrangeHRM\Claim\Traits\Service\ClaimServiceTrait;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Exception\ForbiddenException;
use OrangeHRM\Core\Api\V2\Exception\InvalidParamException;
use OrangeHRM\Core\Api\V2\Exception\RecordNotFoundException;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\ClaimAttachment;
use OrangeHRM\Entity\WorkflowStateMachine;
use OrangeHRM\ORM\Exception\TransactionException;

class ClaimAttachmentAPI extends Endpoint implements CrudEndpoint
{
    use EntityManagerHelperTrait;
    use ClaimServiceTrait;
    use AuthUserTrait;
    use DateTimeHelperTrait;
    use UserRoleManagerTrait;
    use ClaimRequestAPIHelperTrait;

    public const PARAMETER_REQUEST_ID = 'requestId';
    public const PARAMETER_CLAIM_ATTACHMENT = 'attachment';
    public const PARAMETER_ATTACHMENT_DESCRIPTION = 'description';
    public const PARAMETER_ATTACHMENT_DESCRIPTION_MAX_LENGTH = 200;

    /**
     * @OA\Get(
     *     path="/api/v2/claim/requests/{requestId}/attachments",
     *     tags={"Claim/Attachments"},
     *     summary="List Attachements on a Claim",
     *     operationId="list-attachments-on-a-claim",
     *     @OA\PathParameter(
     *         name="requestId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="sortField",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=ClaimAttachmentSearchFilterParams::ALLOWED_SORT_FIELDS)
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/sortOrder"),
     *     @OA\Parameter(ref="#/components/parameters/limit"),
     *     @OA\Parameter(ref="#/components/parameters/offset"),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Claim-AttachmentModel")
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     )
     * )
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $claimAttachmentSearchFilterParams = new ClaimAttachmentSearchFilterParams();
        $this->setSortingAndPaginationParams($claimAttachmentSearchFilterParams);
        $requestId = $this->getRequestParams()
            ->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, self::PARAMETER_REQUEST_ID);
        $this->getClaimRequest($requestId);
        $claimAttachmentSearchFilterParams->setRequestId($requestId);
        $claimAttachments = $this->getClaimService()
            ->getClaimDao()
            ->getClaimAttachmentList($claimAttachmentSearchFilterParams);
        $count = $this->getClaimService()
            ->getClaimDao()
            ->getClaimAttachmentCount($claimAttachmentSearchFilterParams);

        return new EndpointCollectionResult(
            ClaimAttachmentModel::class,
            $claimAttachments,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_REQUEST_ID,
                    new Rule(Rules::POSITIVE)
                )
            ),
            ...$this->getSortingAndPaginationParamsRules()
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v2/claim/requests/{requestId}/attachments",
     *     tags={"Claim/Attachments"},
     *     summary="Add Attachments to a Claim",
     *     operationId="add-attachments-to-a-claim",
     *     @OA\PathParameter(
     *         name="requestId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="attachment", ref="#/components/schemas/Base64Attachment"),
     *             required={"attachment"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Claim-AttachmentModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     * @inheritDoc
     */
    public function create(): EndpointResult
    {
        $this->beginTransaction();
        try {
            $claimAttachment = new ClaimAttachment();
            $requestId = $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_ATTRIBUTE,
                self::PARAMETER_REQUEST_ID
            );
            $claimRequest = $this->getClaimRequest($requestId);
            $claimAttachment->setRequestId($requestId);

            $this->isActionAllowed(WorkflowStateMachine::CLAIM_ACTION_SUBMIT, $claimRequest);

            $claimAttachment->getDecorator()->setUserByUserId(
                $this->getAuthUser()->getUserId()
            );
            $claimAttachment->setAttachId($this->getClaimService()
                ->getClaimDao()
                ->getNextAttachmentId($requestId));
            $claimAttachment->setAttachedDate($this->getDateTimeHelper()->getNow());
            $this->setAttachment($claimAttachment);
            $this->commitTransaction();
        } catch (InvalidParamException|ForbiddenException | RecordNotFoundException $e) {
            $this->rollBackTransaction();
            throw $e;
        } catch (Exception $e) {
            $this->rollBackTransaction();
            throw new TransactionException($e);
        }

        return new EndpointResourceResult(
            ClaimAttachmentModel::class,
            $this->getPartialClaimAttachment($claimAttachment)
        );
    }

    /**
     * @param ClaimAttachment $claimAttachment
     */
    private function setAttachment(ClaimAttachment $claimAttachment): void
    {
        $base64Attachment = $this->getRequestParams()->getAttachment(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_CLAIM_ATTACHMENT
        );
        $claimAttachment->setSize($base64Attachment->getSize());
        $claimAttachment->setFileType($base64Attachment->getFileType());
        $claimAttachment->setFilename($base64Attachment->getFileName());
        $claimAttachment->setAttachment($base64Attachment->getContent());
        $claimAttachment->setDescription(
            $this->getRequestParams()
                ->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_ATTACHMENT_DESCRIPTION)
        );
        $this->getClaimService()
            ->getClaimDao()
            ->saveClaimAttachment($claimAttachment);
    }

    /**
     * @param ClaimAttachment $claimAttachment
     * @return PartialClaimAttachment
     */
    private function getPartialClaimAttachment(ClaimAttachment $claimAttachment): PartialClaimAttachment
    {
        return new PartialClaimAttachment(
            $claimAttachment->getRequestId(),
            $claimAttachment->getAttachId(),
            $claimAttachment->getSize(),
            $claimAttachment->getDescription(),
            $claimAttachment->getFilename(),
            $claimAttachment->getFileType(),
            $claimAttachment->getUser()->getId(),
            $claimAttachment->getAttachedDate()
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_REQUEST_ID,
                new Rule(Rules::POSITIVE)
            ),
            new ParamRule(
                self::PARAMETER_CLAIM_ATTACHMENT,
                new Rule(Rules::BASE_64_ATTACHMENT)
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_ATTACHMENT_DESCRIPTION,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAMETER_ATTACHMENT_DESCRIPTION_MAX_LENGTH])
                ),
                true
            )
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/v2/claim/requests/{requestId}/attachments",
     *     tags={"Claim/Attachments"},
     *     summary="Remove Attachments from a Claim",
     *     operationId="remove-attachments-from-a-claim",
     *     @OA\PathParameter(
     *         name="requestId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="ids", type="array",
     *                 @OA\Items(type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(type="integer")
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response="403", ref="#/components/responses/ForbiddenResponse"),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     * @inheritDoc
     */
    public function delete(): EndpointResult
    {
        $requestId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_REQUEST_ID
        );
        $claimRequest = $this->getClaimRequest($requestId);

        $this->isActionAllowed(WorkflowStateMachine::CLAIM_ACTION_SUBMIT, $claimRequest);

        $ids = $this->getClaimService()->getClaimDao()->getExistingClaimAttachmentIdsForRequestId(
            $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS),
            $requestId
        );
        $this->throwRecordNotFoundExceptionIfEmptyIds($ids);
        $this->getClaimService()
            ->getClaimDao()
            ->deleteClaimAttachments($requestId, $ids);

        return new EndpointResourceResult(ArrayModel::class, $ids);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_REQUEST_ID,
                new Rule(Rules::POSITIVE)
            ),
            new ParamRule(
                CommonParams::PARAMETER_IDS,
                new Rule(Rules::INT_ARRAY)
            ),
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v2/claim/requests/{requestId}/attachments/{id}",
     *     tags={"Claim/Attachments"},
     *     summary="View an Attachment on a Claim",
     *     operationId="view-an-attachment-on-a-claim",
     *     @OA\PathParameter(
     *         name="requestId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\PathParameter(
     *         name="id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Claim-AttachmentModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        $requestId = $this->getRequestParams()
            ->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, self::PARAMETER_REQUEST_ID);
        $attachId = $this->getRequestParams()
            ->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $this->getClaimRequest($requestId);
        $claimAttachment = $this->getClaimService()
            ->getClaimDao()
            ->getPartialClaimAttachment($requestId, $attachId);
        $this->throwRecordNotFoundExceptionIfNotExist($claimAttachment, PartialClaimAttachment::class);

        return new EndpointResourceResult(ClaimAttachmentModel::class, $claimAttachment);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_REQUEST_ID,
                new Rule(Rules::POSITIVE)
            ),
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            )
        );
    }

    /**
     * @OA\Put(
     *     path="/api/v2/claim/requests/{requestId}/attachments/{id}",
     *     tags={"Claim/Attachments"},
     *     summary="Update an Attachment on a Claim",
     *     operationId="update-an-attachment-on-a-claim",
     *     @OA\PathParameter(
     *         name="requestId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\PathParameter(
     *         name="id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="attachment", ref="#/components/schemas/Base64Attachment"),
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Claim-AttachmentModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        $this->beginTransaction();
        try {
            $requestId = $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_ATTRIBUTE,
                self::PARAMETER_REQUEST_ID
            );
            $attachId = $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_ATTRIBUTE,
                CommonParams::PARAMETER_ID
            );
            $claimRequest = $this->getClaimRequest($requestId);

            $this->isActionAllowed(WorkflowStateMachine::CLAIM_ACTION_SUBMIT, $claimRequest);

            $claimAttachment = $this->getClaimService()
                ->getClaimDao()
                ->getClaimAttachment($requestId, $attachId);
            $this->throwRecordNotFoundExceptionIfNotExist($claimAttachment, ClaimAttachment::class);
            $attachment = $this->getRequestParams()->getAttachmentOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_CLAIM_ATTACHMENT
            );
            $description = $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_ATTACHMENT_DESCRIPTION,
                null,
                false
            );

            if (!is_null($description)) {
                $claimAttachment->setDescription($description);
            }

            if (!is_null($attachment)) {
                $claimAttachment->getDecorator()->setUserByUserId(
                    $this->getAuthUser()->getUserId()
                );
                $claimAttachment->setSize($attachment->getSize());
                $claimAttachment->setFileType($attachment->getFileType());
                $claimAttachment->setFilename($attachment->getFileName());
                $claimAttachment->setAttachment($attachment->getContent());
            }

            $this->getClaimService()->getClaimDao()->saveClaimAttachment($claimAttachment);
            $this->commitTransaction();
        } catch (ForbiddenException | InvalidParamException | RecordNotFoundException $e) {
            $this->rollBackTransaction();
            throw $e;
        } catch (Exception $e) {
            $this->rollBackTransaction();
            throw new TransactionException($e);
        }

        return new EndpointResourceResult(
            ClaimAttachmentModel::class,
            $this->getPartialClaimAttachment($claimAttachment)
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_REQUEST_ID,
                new Rule(Rules::POSITIVE)
            ),
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_ATTACHMENT_DESCRIPTION,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAMETER_ATTACHMENT_DESCRIPTION_MAX_LENGTH])
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_CLAIM_ATTACHMENT,
                    new Rule(Rules::BASE_64_ATTACHMENT)
                )
            )
        );
    }
}
