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

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Serializer\NormalizeException;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Entity\Vacancy;
use OrangeHRM\Entity\VacancyAttachment;
use OrangeHRM\Recruitment\Api\Model\VacancyAttachmentModel;
use OrangeHRM\Recruitment\Dto\RecruitmentAttachment;
use OrangeHRM\Recruitment\Traits\Service\RecruitmentAttachmentServiceTrait;

class VacancyAttachmentAPI extends Endpoint implements CrudEndpoint
{
    use RecruitmentAttachmentServiceTrait;

    public const PARAMETER_ATTACHMENT_ID = 'attachmentId';
    public const PARAMETER_ATTACHMENT_TYPE = 'attachmentType';
    public const PARAMETER_COMMENT = 'comment';
    public const PARAMETER_VACANCY_ID = 'vacancyId';
    public const PARAMETER_ATTACHMENT = 'attachment';
    public const PARAMETER_CURRENT_ATTACHMENT = 'currentAttachment';

    public const PARAM_RULE_VACANCY_ID_MAX_LENGTH = 13;
    public const PARAM_RULE_COMMENT_MAX_LENGTH = 255;
    public const PARAM_RULE_ATTACHMENT_TYPE_MAX_LENGTH = 4;
    public const PARAM_RULE_FILE_NAME_MAX_LENGTH = 200;
    public const PARAM_RULE_CURRENT_ATTACHMENT_MAX_LENGTH = 16;

    public const VACANCY_ATTACHMENT_REPLACE_CURRENT = 'replaceCurrent';

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
     *     path="/api/v2/recruitment/vacancy/attachments",
     *     tags={"Recruitment/Vacancy Attachments"},
     *     summary="Add an Attachment to a Vacancy",
     *     operationId="add-an-attachment-to-a-vacancy",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="vacancyId", type="integer"),
     *             @OA\Property(
     *                 property="comment",
     *                 type="string",
     *                 maxLength=OrangeHRM\Recruitment\Api\VacancyAttachmentAPI::PARAM_RULE_COMMENT_MAX_LENGTH
     *             ),
     *             @OA\Property(
     *                 property="attachmentType",
     *                 type="integer",
     *                 maximum=OrangeHRM\Recruitment\Api\VacancyAttachmentAPI::PARAM_RULE_ATTACHMENT_TYPE_MAX_LENGTH
     *             ),
     *             @OA\Property(property="attachment", ref="#/components/schemas/Base64Attachment"),
     *             required={"vacancyId", "attachment"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Recruitment-VacancyAttachmentModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     * @inheritDoc
     */
    public function create(): EndpointResult
    {
        $vacancyAttachment = new VacancyAttachment();
        $this->setVacancyAttachment($vacancyAttachment);
        $this->setBase64Attachment($vacancyAttachment);
        $vacancyAttachment = $this->getRecruitmentAttachmentService()
            ->getRecruitmentAttachmentDao()
            ->saveVacancyAttachment($vacancyAttachment);
        return new EndpointResourceResult(
            VacancyAttachmentModel::class,
            $this->getRecruitmentAttachment($vacancyAttachment)
        );
    }

    /**
     * @param VacancyAttachment $vacancyAttachment
     */
    private function setVacancyAttachment(VacancyAttachment $vacancyAttachment): void
    {
        $vacancyAttachment->getDecorator()->setVacancyById(
            $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_VACANCY_ID
            )
        );
        $vacancyAttachment->setComment(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_COMMENT
            )
        );
        $vacancyAttachment->setAttachmentType(
            $this->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_ATTACHMENT_TYPE
            )
        );
    }

    /**
     * @param VacancyAttachment $vacancyAttachment
     */
    private function setBase64Attachment(VacancyAttachment $vacancyAttachment): void
    {
        $base64Attachment = $this->getRequestParams()->getAttachmentOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_ATTACHMENT
        );
        if (is_null($base64Attachment)) {
            return;
        }
        $vacancyAttachment->setFileName($base64Attachment->getFilename());
        $vacancyAttachment->setFileType($base64Attachment->getFileType());
        $vacancyAttachment->setFileSize($base64Attachment->getSize());
        $vacancyAttachment->setFileContent($base64Attachment->getContent());
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->requiredParamRule(
                $this->getAttachmentRule()
            ),
            ...$this->getCommonBodyValidationRules(),
        );
    }

    /**
     * @return ParamRule
     */
    private function getAttachmentRule(): ParamRule
    {
        return new ParamRule(
            self::PARAMETER_ATTACHMENT,
            new Rule(
                Rules::BASE_64_ATTACHMENT,
                [null, null, self::PARAM_RULE_FILE_NAME_MAX_LENGTH]
            )
        );
    }

    /**
     * @return ParamRule[]
     */
    protected function getCommonBodyValidationRules(): array
    {
        return [
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_COMMENT,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_COMMENT_MAX_LENGTH]),
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_ATTACHMENT_TYPE,
                    new Rule(Rules::INT_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_ATTACHMENT_TYPE_MAX_LENGTH]),
                ),
                true
            ),
            new ParamRule(
                self::PARAMETER_VACANCY_ID,
                new Rule(Rules::POSITIVE),
                new Rule(Rules::ENTITY_ID_EXISTS, [Vacancy::class])
            ),
        ];
    }

    /**
     * @OA\Delete(
     *     path="/api/v2/recruitment/vacancy/attachments",
     *     tags={"Recruitment/Vacancy Attachments"},
     *     summary="Delete Vacancy Attachments",
     *     operationId="delete-vacancy-attachments",
     *     @OA\RequestBody(ref="#/components/requestBodies/DeleteRequestBody"),
     *     @OA\Response(response="200", ref="#/components/responses/DeleteResponse"),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function delete(): EndpointResult
    {
        $ids = $this->getRecruitmentAttachmentService()->getRecruitmentAttachmentDao()->getExistingVacancyAttachmentIds(
            $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS)
        );
        $this->throwRecordNotFoundExceptionIfEmptyIds($ids);
        $this->getRecruitmentAttachmentService()->getRecruitmentAttachmentDao()->deleteVacancyAttachments($ids);
        return new EndpointResourceResult(ArrayModel::class, $ids);
    }

    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_IDS),
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v2/recruitment/vacancies/{vacancyId}/attachments",
     *     tags={"Recruitment/Vacancy Attachments"},
     *     summary="Get a Vacancy Attachment",
     *     operationId="get-a-vacancy-attachment",
     *     @OA\PathParameter(
     *         name="vacancyId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Recruitment-VacancyAttachmentModel"
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
        $vacancyId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_VACANCY_ID
        );

        return $this->getVacancyAttachments($vacancyId);
    }

    /**
     * @param int $vacancyId
     * @return EndpointCollectionResult
     * @throws NormalizeException
     */
    private function getVacancyAttachments(int $vacancyId): EndpointCollectionResult
    {
        $attachments = $this->getRecruitmentAttachmentService()
            ->getRecruitmentAttachmentDao()
            ->getVacancyAttachmentsByVacancyId($vacancyId);
        $count = $this->getRecruitmentAttachmentService()
            ->getRecruitmentAttachmentDao()
            ->getVacancyAttachmentsCountByVacancyId($vacancyId);
        return new EndpointCollectionResult(
            VacancyAttachmentModel::class,
            $attachments,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_VACANCY_ID,
                new Rule(Rules::POSITIVE),
                new Rule(Rules::ENTITY_ID_EXISTS, [Vacancy::class])
            )
        );
    }

    /**
     * @OA\Put(
     *     path="/api/v2/recruitment/vacancies/{vacancyId}/attachments/{attachmentId}",
     *     tags={"Recruitment/Vacancy Attachments"},
     *     summary="Update a Vacancy Attachment",
     *     operationId="update-a-vacancy-attachment",
     *     @OA\PathParameter(
     *         name="attachmentId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\PathParameter(
     *         name="vacancyId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="currentAttachment", type="string"),
     *             @OA\Property(property="attachment", ref="#/components/schemas/Base64Attachment"),
     *             @OA\Property(property="vacancyId", type="integer"),
     *             @OA\Property(property="comment", type="string"),
     *             @OA\Property(property="attachmentType", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Recruitment-VacancyAttachmentModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        $attachmentId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_ATTACHMENT_ID
        );
        $currentAttachment = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_CURRENT_ATTACHMENT
        );
        $vacancyAttachment = $this->getRecruitmentAttachmentService()
            ->getRecruitmentAttachmentDao()
            ->getVacancyAttachmentById($attachmentId);

        $this->throwRecordNotFoundExceptionIfNotExist($vacancyAttachment, VacancyAttachment::class);
        $this->setVacancyAttachment($vacancyAttachment);
        if ($currentAttachment == self::VACANCY_ATTACHMENT_REPLACE_CURRENT) {
            $this->setBase64Attachment($vacancyAttachment);
        }
        $this->getRecruitmentAttachmentService()
            ->getRecruitmentAttachmentDao()
            ->saveVacancyAttachment($vacancyAttachment);
        return new EndpointResourceResult(
            VacancyAttachmentModel::class,
            $this->getRecruitmentAttachment($vacancyAttachment)
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_ATTACHMENT_ID,
                new Rule(Rules::POSITIVE),
                new Rule(Rules::LENGTH, [!null, self::PARAM_RULE_VACANCY_ID_MAX_LENGTH]),
            ),
            new ParamRule(
                self::PARAMETER_CURRENT_ATTACHMENT,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [!null, self::PARAM_RULE_CURRENT_ATTACHMENT_MAX_LENGTH]),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                $this->getAttachmentRule()
            ),
            ...$this->getCommonBodyValidationRules(),
        );
    }

    /**
     * @param VacancyAttachment $vacancyAttachment
     * @return RecruitmentAttachment
     */
    private function getRecruitmentAttachment(VacancyAttachment $vacancyAttachment): RecruitmentAttachment
    {
        return new RecruitmentAttachment(
            $vacancyAttachment->getId(),
            $vacancyAttachment->getFileName(),
            $vacancyAttachment->getFileType(),
            $vacancyAttachment->getFileSize(),
            $vacancyAttachment->getVacancy()->getId(),
            $vacancyAttachment->getComment()
        );
    }
}
