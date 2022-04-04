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

namespace OrangeHRM\Admin\Api;

use OrangeHRM\Admin\Api\Model\JobTitleModel;
use OrangeHRM\Admin\Dto\JobTitleSearchFilterParams;
use OrangeHRM\Admin\Service\JobTitleService;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Exception\RecordNotFoundException;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Api\V2\Validator\Rules\EntityUniquePropertyOption;
use OrangeHRM\Core\Dto\Base64Attachment;
use OrangeHRM\Entity\JobSpecificationAttachment;
use OrangeHRM\Entity\JobTitle;

class JobTitleAPI extends Endpoint implements CrudEndpoint
{
    /**
     * @var null|JobTitleService
     */
    protected ?JobTitleService $jobTitleService = null;

    public const PARAMETER_TITLE = 'title';
    public const PARAMETER_DESCRIPTION = 'description';
    public const PARAMETER_NOTE = 'note';
    public const PARAMETER_SPECIFICATION = 'specification';
    public const PARAMETER_ACTIVE_ONLY = 'activeOnly';
    public const PARAMETER_CURRENT_JOB_SPECIFICATION = 'currentJobSpecification';

    public const PARAM_RULE_TITLE_MAX_LENGTH = 100;
    public const PARAM_RULE_DESCRIPTION_MAX_LENGTH = 400;
    public const PARAM_RULE_NOTE_MAX_LENGTH = 400;
    public const PARAM_RULE_SPECIFICATION_FILE_NAME_MAX_LENGTH = 200;

    public const JOB_SPECIFICATION_KEEP_CURRENT = 'keepCurrent';
    public const JOB_SPECIFICATION_DELETE_CURRENT = 'deleteCurrent';
    public const JOB_SPECIFICATION_REPLACE_CURRENT = 'replaceCurrent';

    /**
     * @param JobTitleService $jobTitleService
     */
    public function setJobTitleService(JobTitleService $jobTitleService)
    {
        $this->jobTitleService = $jobTitleService;
    }

    /**
     * @return JobTitleService
     */
    public function getJobTitleService(): JobTitleService
    {
        if (is_null($this->jobTitleService)) {
            $this->jobTitleService = new JobTitleService();
        }
        return $this->jobTitleService;
    }

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $jobTitle = $this->getJobTitleService()->getJobTitleById($id);
        if (!$jobTitle instanceof JobTitle) {
            throw new RecordNotFoundException();
        }

        return new EndpointResourceResult(JobTitleModel::class, $jobTitle);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_ID),
        );
    }

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $jobTitleSearchFilterParams = new JobTitleSearchFilterParams();
        $this->setSortingAndPaginationParams($jobTitleSearchFilterParams);

        $activeOnly = $this->getRequestParams()->getBoolean(
            RequestParams::PARAM_TYPE_QUERY,
            self::PARAMETER_ACTIVE_ONLY,
            true
        );
        $jobTitleSearchFilterParams->setActiveOnly($activeOnly);

        $count = $this->getJobTitleService()->getJobTitleDao()->getJobTitlesCount($jobTitleSearchFilterParams);

        $jobTitles = $this->getJobTitleService()->getJobTitleDao()->getJobTitles($jobTitleSearchFilterParams);
        return new EndpointCollectionResult(
            JobTitleModel::class,
            $jobTitles,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_ACTIVE_ONLY,
                new Rule(Rules::BOOL_VAL)
            ),
            ...$this->getSortingAndPaginationParamsRules(JobTitleSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @inheritDoc
     */
    public function create(): EndpointResult
    {
        $jobTitle = new JobTitle();
        $this->setJobTitle($jobTitle);
        $jobSpecification = $this->setJobSpecification(new JobSpecificationAttachment());
        if ($jobSpecification) {
            $jobTitle->setJobSpecificationAttachment($jobSpecification);
            $jobSpecification->setJobTitle($jobTitle);
        }

        $jobTitle = $this->getJobTitleService()->saveJobTitle($jobTitle);

        return new EndpointResourceResult(JobTitleModel::class, $jobTitle);
    }

    /**
     * @param JobTitle $jobTitle
     */
    private function setJobTitle(JobTitle $jobTitle): void
    {
        $jobTitle->setJobTitleName(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_TITLE
            )
        );
        $jobTitle->setJobDescription(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_DESCRIPTION
            )
        );
        $jobTitle->setNote(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_NOTE
            )
        );
    }

    /**
     * @param JobSpecificationAttachment $jobSpecificationAttachment
     * @return JobSpecificationAttachment|null
     */
    private function setJobSpecification(
        JobSpecificationAttachment $jobSpecificationAttachment
    ): ?JobSpecificationAttachment {
        $base64Attachment = $this->getBase64JobSpecification();
        if (is_null($base64Attachment)) {
            return null;
        }
        $jobSpecificationAttachment->setFileName($base64Attachment->getFilename());
        $jobSpecificationAttachment->setFileType($base64Attachment->getFileType());
        $jobSpecificationAttachment->setFileSize($base64Attachment->getSize());
        $jobSpecificationAttachment->setFileContent($base64Attachment->getContent());
        return $jobSpecificationAttachment;
    }

    /**
     * @return Base64Attachment|null
     */
    private function getBase64JobSpecification(): ?Base64Attachment
    {
        return $this->getRequestParams()->getAttachmentOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_SPECIFICATION
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getTitleRule(false),
            $this->getValidationDecorator()->notRequiredParamRule(
                $this->getSpecificationRule()
            ),
            ...$this->getCommonBodyValidationRules(),
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
                    self::PARAMETER_DESCRIPTION,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_DESCRIPTION_MAX_LENGTH]),
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_NOTE,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_NOTE_MAX_LENGTH]),
                ),
                true
            ),
        ];
    }

    /**
     * @param bool $update
     * @return ParamRule
     */
    protected function getTitleRule(bool $update): ParamRule
    {
        $entityProperties = new EntityUniquePropertyOption();
        $ignoreValues = ['isDeleted' => true];
        if ($update) {
            $ignoreValues['getId'] = $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_ATTRIBUTE,
                CommonParams::PARAMETER_ID
            );
        }
        $entityProperties->setIgnoreValues($ignoreValues);

        return new ParamRule(
            self::PARAMETER_TITLE,
            new Rule(Rules::STRING_TYPE),
            new Rule(Rules::LENGTH, [null, self::PARAM_RULE_TITLE_MAX_LENGTH]),
            new Rule(Rules::ENTITY_UNIQUE_PROPERTY, [JobTitle::class, 'jobTitleName', $entityProperties])
        );
    }

    /**
     * @return ParamRule
     */
    private function getSpecificationRule(): ParamRule
    {
        return new ParamRule(
            self::PARAMETER_SPECIFICATION,
            new Rule(
                Rules::BASE_64_ATTACHMENT,
                [null, null, self::PARAM_RULE_SPECIFICATION_FILE_NAME_MAX_LENGTH]
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $currentJobSpecification = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_CURRENT_JOB_SPECIFICATION
        );

        $jobTitle = $this->getJobTitleService()->getJobTitleById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($jobTitle, JobTitle::class);
        $this->setJobTitle($jobTitle);

        $jobSpecification = $jobTitle->getJobSpecificationAttachment();
        $base64Attachment = $this->getBase64JobSpecification();

        if (!$jobSpecification instanceof JobSpecificationAttachment && $currentJobSpecification) {
            throw $this->getBadRequestException(
                "`" . self::PARAMETER_CURRENT_JOB_SPECIFICATION . "` should not define if there is no job specification"
            );
        } elseif ($jobSpecification instanceof JobSpecificationAttachment && !$currentJobSpecification) {
            throw $this->getBadRequestException(
                "`" . self::PARAMETER_CURRENT_JOB_SPECIFICATION . "` should define if there is a job specification"
            );
        }

        if (!$jobSpecification instanceof JobSpecificationAttachment && $base64Attachment) {
            $jobSpecification = new JobSpecificationAttachment();
            $this->setJobSpecification($jobSpecification);
            $jobTitle->setJobSpecificationAttachment($jobSpecification);
            $jobSpecification->setJobTitle($jobTitle);
        } elseif ($currentJobSpecification === self::JOB_SPECIFICATION_DELETE_CURRENT) {
            $jobTitle->setJobSpecificationAttachment(null);
            $this->getJobTitleService()->deleteJobSpecificationAttachment($jobSpecification);
        } elseif ($currentJobSpecification === self::JOB_SPECIFICATION_REPLACE_CURRENT) {
            $this->setJobSpecification($jobSpecification);
            $jobTitle->setJobSpecificationAttachment($jobSpecification);
            $jobSpecification->setJobTitle($jobTitle);
        } // else self::JOB_SPECIFICATION_KEEP_CURRENT

        $this->getJobTitleService()->saveJobTitle($jobTitle);

        return new EndpointResourceResult(JobTitleModel::class, $jobTitle);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        $currentJobSpecification = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_CURRENT_JOB_SPECIFICATION
        );
        $paramRules = new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            ),
            $this->getTitleRule(true),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_CURRENT_JOB_SPECIFICATION,
                    new Rule(
                        Rules::IN,
                        [
                            [
                                self::JOB_SPECIFICATION_KEEP_CURRENT,
                                self::JOB_SPECIFICATION_DELETE_CURRENT,
                                self::JOB_SPECIFICATION_REPLACE_CURRENT
                            ]
                        ]
                    ),
                )
            ),
            ...$this->getCommonBodyValidationRules(),
        );
        if (!in_array(
            $currentJobSpecification,
            [self::JOB_SPECIFICATION_KEEP_CURRENT, self::JOB_SPECIFICATION_DELETE_CURRENT]
        )) {
            if (is_null($currentJobSpecification)) {
                $paramRules->addParamValidation(
                    $this->getValidationDecorator()->notRequiredParamRule($this->getSpecificationRule())
                );
            } elseif ($currentJobSpecification === self::JOB_SPECIFICATION_REPLACE_CURRENT) {
                $paramRules->addParamValidation($this->getSpecificationRule());
            }
        }
        return $paramRules;
    }

    /**
     * @inheritDoc
     */
    public function delete(): EndpointResult
    {
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS);
        $this->getJobTitleService()->deleteJobTitle($ids);
        return new EndpointResourceResult(ArrayModel::class, $ids);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_IDS),
        );
    }
}
