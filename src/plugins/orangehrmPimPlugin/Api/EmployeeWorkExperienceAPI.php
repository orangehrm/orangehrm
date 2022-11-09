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

namespace OrangeHRM\Pim\Api;

use Exception;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\EmpWorkExperience;
use OrangeHRM\Pim\Api\Model\EmployeeWorkExperienceModel;
use OrangeHRM\Pim\Dto\EmployeeWorkExperienceSearchFilterParams;
use OrangeHRM\Pim\Service\EmployeeWorkExperienceService;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;

class EmployeeWorkExperienceAPI extends Endpoint implements CrudEndpoint
{
    public const PARAMETER_EMPLOYER = 'company';
    public const PARAMETER_JOB_TITLE = 'jobTitle';
    public const PARAMETER_FROM_DATE = 'fromDate';
    public const PARAMETER_TO_DATE = 'toDate';
    public const PARAMETER_COMMENTS = 'comment';

    public const PARAM_RULE_EMPLOYER_MAX_LENGTH = 100;
    public const PARAM_RULE_JOB_TITLE_MAX_LENGTH = 120;
    public const PARAM_RULE_COMMENTS_MAX_LENGTH = 200;

    /**
     * @var null|EmployeeWorkExperienceService
     */
    protected ?EmployeeWorkExperienceService $employeeWorkExperienceService = null;

    /**
     * @return EmployeeWorkExperienceService
     */
    public function getEmployeeWorkExperienceService(): EmployeeWorkExperienceService
    {
        if (is_null($this->employeeWorkExperienceService)) {
            $this->employeeWorkExperienceService = new EmployeeWorkExperienceService();
        }
        return $this->employeeWorkExperienceService;
    }

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResourceResult
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $seqNo = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_ID
        );
        $employeeWorkExperience = $this->getEmployeeWorkExperienceService()->getEmployeeWorkExperienceDao()->getEmployeeWorkExperienceById(
            $empNumber,
            $seqNo
        );
        $this->throwRecordNotFoundExceptionIfNotExist($employeeWorkExperience, EmpWorkExperience::class);

        return new EndpointResourceResult(
            EmployeeWorkExperienceModel::class,
            $employeeWorkExperience,
            new ParameterBag([CommonParams::PARAMETER_EMP_NUMBER => $empNumber])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            ),
            $this->getEmpNumberRule(),
        );
    }

    /**
     * @return EndpointGetAllResult
     * @throws Exception
     */
    public function getAll(): EndpointCollectionResult
    {
        $employeeWorkExperienceSearchParams = new EmployeeWorkExperienceSearchFilterParams();
        $this->setSortingAndPaginationParams($employeeWorkExperienceSearchParams);
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $employeeWorkExperienceSearchParams->setEmpNumber(
            $empNumber
        );

        $employeeWorkExperiences = $this->getEmployeeWorkExperienceService()->getEmployeeWorkExperienceDao()->searchEmployeeWorkExperience(
            $employeeWorkExperienceSearchParams
        );

        return new EndpointCollectionResult(
            EmployeeWorkExperienceModel::class,
            $employeeWorkExperiences,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                    CommonParams::PARAMETER_TOTAL => $this->getEmployeeWorkExperienceService()->getEmployeeWorkExperienceDao(
                    )->getSearchEmployeeWorkExperiencesCount(
                        $employeeWorkExperienceSearchParams
                    )
                ]
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getEmpNumberRule(),
            ...$this->getSortingAndPaginationParamsRules(EmployeeWorkExperienceSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function create(): EndpointResourceResult
    {
        $employeeWorkExperience = $this->saveEmployeeWorkExperience();
        return new EndpointResourceResult(
            EmployeeWorkExperienceModel::class,
            $employeeWorkExperience,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => $employeeWorkExperience->getEmployee()->getEmpNumber(),
                ]
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getEmpNumberRule(),
            ...$this->getCommonBodyValidationRules(),
        );
    }

    /**
     * @return ParamRule[]
     */
    private function getCommonBodyValidationRules(): array
    {
        return [
            new ParamRule(
                self::PARAMETER_EMPLOYER,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::REQUIRED),
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_EMPLOYER_MAX_LENGTH]),
            ),
            new ParamRule(
                self::PARAMETER_JOB_TITLE,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::REQUIRED),
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_JOB_TITLE_MAX_LENGTH]),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_COMMENTS,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_COMMENTS_MAX_LENGTH]),
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_FROM_DATE,
                    new Rule(Rules::API_DATE),
                ),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_TO_DATE,
                    new Rule(Rules::API_DATE),
                ),
            ),
        ];
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function update(): EndpointResourceResult
    {
        $employeeWorkExperience = $this->saveEmployeeWorkExperience();

        return new EndpointResourceResult(
            EmployeeWorkExperienceModel::class,
            $employeeWorkExperience,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => $employeeWorkExperience->getEmployee()->getEmpNumber(),
                ]
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_ID, new Rule(Rules::REQUIRED), new Rule(Rules::POSITIVE)),
            $this->getEmpNumberRule(),
            ...$this->getCommonBodyValidationRules(),
        );
    }

    /**
     * @inheritDoc
     * @throws DaoException
     * @throws Exception
     */
    public function delete(): EndpointResourceResult
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS);
        $this->getEmployeeWorkExperienceService()->getEmployeeWorkExperienceDao()->deleteEmployeeWorkExperiences($empNumber, $ids);
        return new EndpointResourceResult(
            ArrayModel::class,
            $ids,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                ]
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getEmpNumberRule(),
            new ParamRule(
                CommonParams::PARAMETER_IDS,
                new Rule(Rules::ARRAY_TYPE)
            )
        );
    }

    /**
     * @return EmpWorkExperience
     * @throws DaoException
     * @throws Exception
     */
    public function saveEmployeeWorkExperience(): EmpWorkExperience
    {
        $seqNo = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $employer = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_EMPLOYER
        );
        $jobTitle = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_JOB_TITLE
        );
        $fromDate = $this->getRequestParams()->getDateTimeOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_FROM_DATE
        );
        $toDate = $this->getRequestParams()->getDateTimeOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_TO_DATE
        );
        $comments = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_COMMENTS
        );

        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        if (!empty($seqNo)) { // update operation
            $employeeWorkExperience = $this->getEmployeeWorkExperienceService()->getEmployeeWorkExperienceDao()->getEmployeeWorkExperienceById(
                $empNumber,
                $seqNo
            );
        } else {
            $employeeWorkExperience = new EmpWorkExperience();
            $employeeWorkExperience->getDecorator()->setEmployeeByEmpNumber($empNumber);
        }
        $employeeWorkExperience->setEmployer($employer);
        $employeeWorkExperience->setJobTitle($jobTitle);
        $employeeWorkExperience->setComments($comments);

        $employeeWorkExperience->setFromDate($fromDate);
        $employeeWorkExperience->setToDate($toDate);

        return $this->getEmployeeWorkExperienceService()->getEmployeeWorkExperienceDao()->saveEmployeeWorkExperience(
            $employeeWorkExperience
        );
    }

    /**
     * @return ParamRule
     */
    private function getEmpNumberRule(): ParamRule
    {
        return new ParamRule(
            CommonParams::PARAMETER_EMP_NUMBER,
            new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
        );
    }
}
