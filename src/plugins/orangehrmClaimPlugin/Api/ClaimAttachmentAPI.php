<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

namespace OrangeHRM\Claim\Api;

use Exception;
use OpenApi\Annotations as OA;
use OrangeHRM\Claim\Api\Model\ClaimAttachmentModel;
use OrangeHRM\Claim\Dto\ClaimAttachmentSearchFilterParams;
use OrangeHRM\Claim\Dto\PartialClaimAttachment;
use OrangeHRM\Claim\Traits\Service\ClaimServiceTrait;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
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
use OrangeHRM\Entity\ClaimRequest;
use OrangeHRM\Installer\Exception\NotImplementedException;
use OrangeHRM\ORM\Exception\TransactionException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class ClaimAttachmentAPI extends Endpoint implements CrudEndpoint
{
    use EntityManagerHelperTrait;
    use ClaimServiceTrait;
    use AuthUserTrait;
    use DateTimeHelperTrait;
    use UserRoleManagerTrait;

    public const PARAMETER_REQUEST_ID = 'requestId';
    public const PARAMETER_CLAIM_ATTACHMENT_CONTENT = 'attachment';
    public const PARAMETER_ATTACHMENT_DESCRIPTION = 'description';
    public const PARAMETER_ATTACHMENT_DESCRIPTION_MAX_LENGTH = 200;
    public const ALLOWED_CLAIM_ATTACHMENT_FILE_TYPES = [
        "image/jpeg",
        "text/plain",
        "text/rtf",
        "application/rtf",
        "application/pdf",
        "application/msword",
        "application/vnd.oasis.opendocument.text",
        "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
    ];

    /**
     * @OA\Get(
     *     path="/api/v2/claim/requests/{requestId}/attachments",
     *     tags={"Claim/Attachments"},
     *     @OA\Parameter(
     *         name="requestId",
     *         in="attribute",
     *         required=true,
     *     )
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
        $requestId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, self::PARAMETER_REQUEST_ID);
        $claimRequest = $this->getClaimService()->getClaimDao()->getClaimRequestById($requestId);
        $this->throwRecordNotFoundExceptionIfNotExist($claimRequest, ClaimRequest::class);
        if (!$this->getUserRoleManagerHelper()->isEmployeeAccessible($claimRequest->getEmployee()->getEmpNumber())) {
            throw $this->getForbiddenException();
        }
        $claimAttachmentSearchFilterParams->setRequestId(
            $this->getRequestParams()
                ->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, self::PARAMETER_REQUEST_ID)
        );
        $claimAttachments = $this->getClaimService()->getClaimDao()->getClaimAttachmentList(
            $claimAttachmentSearchFilterParams
        );
        $count = $this->getClaimService()->getClaimDao()->getClaimAttachmentCount($claimAttachmentSearchFilterParams);
        return new EndpointCollectionResult(
            ClaimAttachmentModel::class,
            $claimAttachments,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])
        );
    }

    /**
     * @return ParamRuleCollection
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
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="attachment", type="object"
     *                @OA\Property(property="name", type="string"),
     *                @OA\Property(property="type", type="string"),
     *                @OA\Property(property="size", type="string"),
     *                @OA\Property(property="base64", type="base64"),
     *             @OA\Property(property="description", type="string"),
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
            $claimRequest = $this->getClaimService()->getClaimDao()->getClaimRequestById($requestId);
            $this->throwRecordNotFoundExceptionIfNotExist($claimRequest, ClaimRequest::class);
            if (!$this->getUserRoleManagerHelper()->isEmployeeAccessible(
                $claimRequest->getEmployee()->getEmpNumber()
            )) {
                throw $this->getForbiddenException();
            }
            $claimAttachment->setRequestId($requestId);
            $userId = $this->getAuthUser()->getUserId();
            $claimAttachment->getDecorator()->setUserByUserId($userId);
            $claimAttachment->setAttachId($this->getClaimService()->getClaimDao()->getNextAttachmentId($requestId));
            $claimAttachment->setAttachedTime($this->getDateTimeHelper()->getNow());
            $this->setAttachment($claimAttachment);
            $this->commitTransaction();
        } catch (ResourceNotFoundException $e) {
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
            self::PARAMETER_CLAIM_ATTACHMENT_CONTENT
        );
        $claimAttachment->setSize($base64Attachment->getSize());
        $claimAttachment->setFileType($base64Attachment->getFileType());
        $claimAttachment->setFilename($base64Attachment->getFileName());
        $claimAttachment->setAttachment($base64Attachment->getContent());
        $claimAttachment->setDescription(
            $this->getRequestParams()
                ->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_ATTACHMENT_DESCRIPTION)
        );
        $this->getClaimService()->getClaimDao()->saveClaimAttachment($claimAttachment);
    }

    /**
     * @param ClaimAttachment $claimAttachment
     * @return PartialClaimAttachment
     */
    public function getPartialClaimAttachment(ClaimAttachment $claimAttachment): PartialClaimAttachment
    {
        return new PartialClaimAttachment(
            $claimAttachment->getRequestId(),
            $claimAttachment->getAttachId(),
            $claimAttachment->getSize(),
            $claimAttachment->getDescription(),
            $claimAttachment->getFilename(),
            $claimAttachment->getFileType(),
            $claimAttachment->getAttachedTime()
        );
    }

    /**
     * @return PartialClaimAttachment
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
                self::PARAMETER_CLAIM_ATTACHMENT_CONTENT,
                new Rule(Rules::BASE_64_ATTACHMENT, [self::ALLOWED_CLAIM_ATTACHMENT_FILE_TYPES])
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_ATTACHMENT_DESCRIPTION,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAMETER_ATTACHMENT_DESCRIPTION_MAX_LENGTH])
                )
            )
        );
    }

    /**
     * @throws NotImplementedException
     */
    public function delete(): EndpointResult
    {
        throw new NotImplementedException();
    }

    /**
     * @throws NotImplementedException
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        throw new NotImplementedException();
    }

    /**
     * @OA\Get(
     *     path="/api/v2/claim/requests/{requestId}/attachments/{id}",
     *     tags={"Claim/Attachments"},
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
        $requestId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, self::PARAMETER_REQUEST_ID);
        $eattachId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $claimRequest = $this->getClaimService()->getClaimDao()->getClaimRequestById($requestId);
        $this->throwRecordNotFoundExceptionIfNotExist($claimRequest, ClaimRequest::class);
        if (!$this->getUserRoleManagerHelper()->isEmployeeAccessible($claimRequest->getEmployee()->getEmpNumber())) {
            throw $this->getForbiddenException();
        }
        $claimAttachment = $this->getClaimService()->getClaimDao()->getPartialClaimAttachment($requestId, $eattachId);
        $this->throwRecordNotFoundExceptionIfNotExist($claimAttachment, PartialClaimAttachment::class);
        return new EndpointResourceResult(ClaimAttachmentModel::class, $claimAttachment);
    }

    /**
     * @return ParamRuleCollection
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
     * @throws NotImplementedException
     */
    public function update(): EndpointResult
    {
        throw new NotImplementedException();
    }

    /**
     * @return ParamRuleCollection
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
            new ParamRule(
                self::PARAMETER_CLAIM_ATTACHMENT_CONTENT,
                new Rule(Rules::BASE_64_ATTACHMENT, [self::ALLOWED_CLAIM_ATTACHMENT_FILE_TYPES])
            ),
            new ParamRule(
                self::PARAMETER_ATTACHMENT_DESCRIPTION,
                new Rule(Rules::STRING_TYPE)
            )
        );
    }
}
