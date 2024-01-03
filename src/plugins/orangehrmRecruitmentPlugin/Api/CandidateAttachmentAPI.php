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

namespace OrangeHRM\Recruitment\Api;

use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Entity\Candidate;
use OrangeHRM\Entity\CandidateAttachment;
use OrangeHRM\Recruitment\Api\Model\CandidateAttachmentModel;
use OrangeHRM\Recruitment\Dto\RecruitmentAttachment;
use OrangeHRM\Recruitment\Service\RecruitmentAttachmentService;
use OrangeHRM\Recruitment\Traits\Service\RecruitmentAttachmentServiceTrait;

class CandidateAttachmentAPI extends Endpoint implements CrudEndpoint
{
    use RecruitmentAttachmentServiceTrait;

    public const PARAMETER_COMMENT = 'comment';
    public const PARAMETER_CANDIDATE_ID = 'candidateId';
    public const PARAMETER_ATTACHMENT = 'attachment';
    public const PARAMETER_CURRENT_ATTACHMENT = 'currentAttachment';

    public const PARAM_RULE_FILE_NAME_MAX_LENGTH = 200;
    public const PARAM_RULE_CURRENT_ATTACHMENT_MAX_LENGTH = 16;

    public const CANDIDATE_ATTACHMENT_REPLACE_CURRENT = 'replaceCurrent';
    public const CANDIDATE_ATTACHMENT_DELETE_CURRENT = 'deleteCurrent';

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @OA\Post(
     *     path="/api/v2/recruitment/candidate/attachments",
     *     tags={"Recruitment/Candidate Attachments"},
     *     summary="Add an Attachment to a Candidate",
     *     operationId="add-an-attachment-to-a-candidate",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="candidateId", type="integer"),
     *             @OA\Property(property="attachment", ref="#/components/schemas/Base64Attachment"),
     *             required={"candidateId", "attachment"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Recruitment-CandidateAttachmentModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     * @inheritDoc
     */
    public function create(): EndpointResult
    {
        $candidateId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_CANDIDATE_ID
        );
        $candidateAttachment = new CandidateAttachment();
        $this->setCandidateAttachment($candidateAttachment, $candidateId);
        $this->getRecruitmentAttachmentService()
            ->getRecruitmentAttachmentDao()
            ->saveCandidateAttachment($candidateAttachment);
        return new EndpointResourceResult(
            CandidateAttachmentModel::class,
            $this->getRecruitmentAttachment($candidateAttachment)
        );
    }

    /**
     * @param CandidateAttachment $candidateAttachment
     * @param int $candidateId
     */
    private function setCandidateAttachment(CandidateAttachment $candidateAttachment, int $candidateId)
    {
        $candidateAttachment->getDecorator()->setCandidateById($candidateId);
        $this->setBase64Attachment($candidateAttachment);
    }

    /**
     * @param CandidateAttachment $candidateAttachment
     */
    private function setBase64Attachment(CandidateAttachment $candidateAttachment): void
    {
        $base64Attachment = $this->getRequestParams()->getAttachmentOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_ATTACHMENT
        );
        if (is_null($base64Attachment)) {
            return;
        }
        $candidateAttachment->setFileName($base64Attachment->getFilename());
        $candidateAttachment->setFileType($base64Attachment->getFileType());
        $candidateAttachment->setFileSize($base64Attachment->getSize());
        $candidateAttachment->setFileContent($base64Attachment->getContent());
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_CANDIDATE_ID,
                new Rule(Rules::ENTITY_ID_EXISTS, [Candidate::class])
            ),
            new ParamRule(
                self::PARAMETER_ATTACHMENT,
                new Rule(
                    Rules::BASE_64_ATTACHMENT,
                    [
                        RecruitmentAttachmentService::ALLOWED_CANDIDATE_ATTACHMENT_FILE_TYPES,
                        null,
                        self::PARAM_RULE_FILE_NAME_MAX_LENGTH
                    ]
                )
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function delete(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @OA\Get(
     *     path="/api/v2/recruitment/candidate/{candidateId}/attachment",
     *     tags={"Recruitment/Candidate Attachments"},
     *     summary="Get a Candidate's Attachment",
     *     operationId="get-a-candidates-attachment",
     *     @OA\PathParameter(
     *         name="candidateId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Recruitment-CandidateAttachmentModel"
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
        $candidateId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_CANDIDATE_ID
        );
        $candidateAttachment = $this->getRecruitmentAttachmentService()
            ->getRecruitmentAttachmentDao()
            ->getPartialCandidateAttachmentByCandidateId($candidateId);
        $this->throwRecordNotFoundExceptionIfNotExist($candidateAttachment, RecruitmentAttachment::class);

        return new EndpointResourceResult(CandidateAttachmentModel::class, $candidateAttachment);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(self::PARAMETER_CANDIDATE_ID),
        );
    }

    /**
     * @OA\Put(
     *     path="/api/v2/recruitment/candidate/{candidateId}/attachment",
     *     tags={"Recruitment/Candidate Attachments"},
     *     summary="Update a Candidate's Attachment",
     *     operationId="update-a-candidates-attachment",
     *     @OA\PathParameter(
     *         name="candidateId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="currentAttachment",
     *                 type="string",
     *                 maxLength=OrangeHRM\Recruitment\Api\CandidateAttachmentAPI::PARAM_RULE_CURRENT_ATTACHMENT_MAX_LENGTH
     *             ),
     *             @OA\Property(property="attachment", ref="#/components/schemas/Base64Attachment"),
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Recruitment-CandidateAttachmentModel"
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
        $currentAttachment = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_CURRENT_ATTACHMENT
        );
        $candidateId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_CANDIDATE_ID
        );
        $candidateAttachment = $this->getRecruitmentAttachmentService()
            ->getRecruitmentAttachmentDao()
            ->getCandidateAttachmentByCandidateId($candidateId);

        if (is_null($candidateAttachment)) {
            $candidateAttachment = new CandidateAttachment();
        }

        $this->setCandidateAttachment($candidateAttachment, $candidateId);
        if ($currentAttachment === self::CANDIDATE_ATTACHMENT_REPLACE_CURRENT) {
            $this->setBase64Attachment($candidateAttachment);
            $this->getRecruitmentAttachmentService()
                ->getRecruitmentAttachmentDao()
                ->saveCandidateAttachment($candidateAttachment);
        } elseif ($currentAttachment === self::CANDIDATE_ATTACHMENT_DELETE_CURRENT) {
            $this->getRecruitmentAttachmentService()
                ->getRecruitmentAttachmentDao()
                ->deleteCandidateAttachment($candidateId);
            return new EndpointResourceResult(ArrayModel::class, [$candidateId]);
        }
        return new EndpointResourceResult(
            CandidateAttachmentModel::class,
            $this->getRecruitmentAttachment($candidateAttachment)
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_CANDIDATE_ID,
                new Rule(Rules::POSITIVE),
            ),
            new ParamRule(
                self::PARAMETER_CURRENT_ATTACHMENT,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [!null, self::PARAM_RULE_CURRENT_ATTACHMENT_MAX_LENGTH]),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_ATTACHMENT,
                    new Rule(
                        Rules::BASE_64_ATTACHMENT,
                        [null, null, self::PARAM_RULE_FILE_NAME_MAX_LENGTH]
                    )
                )
            ),
        );
    }

    /**
     * @param CandidateAttachment $candidateAttachment
     * @return RecruitmentAttachment
     */
    private function getRecruitmentAttachment(CandidateAttachment $candidateAttachment): RecruitmentAttachment
    {
        return new RecruitmentAttachment(
            $candidateAttachment->getId(),
            $candidateAttachment->getFileName(),
            $candidateAttachment->getFileType(),
            $candidateAttachment->getFileSize(),
            $candidateAttachment->getCandidate()->getId()
        );
    }
}
