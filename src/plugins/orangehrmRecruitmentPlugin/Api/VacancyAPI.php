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
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\JobTitle;
use OrangeHRM\Entity\Vacancy;
use OrangeHRM\Recruitment\Api\Model\VacancyModel;
use OrangeHRM\Recruitment\Dto\VacancySearchFilterParams;
use OrangeHRM\Recruitment\Traits\Service\VacancyServiceTrait;

class VacancyAPI extends Endpoint implements CrudEndpoint
{
    use VacancyServiceTrait;
    use DateTimeHelperTrait;

    public const FILTER_JOB_TITLE_ID = 'jobTitleId';
    public const FILTER_VACANCY_ID = 'vacancyId';
    public const FILTER_HIRING_MANAGER_ID = 'hiringManagerId';
    public const FILTER_STATUS = 'status';
    public const FILTER_NAME = 'name';

    public const PARAMETER_NAME = 'name';
    public const PARAMETER_DESCRIPTION = 'description';
    public const PARAMETER_NUM_OF_POSITIONS = 'numOfPositions';
    public const PARAMETER_STATUS = 'status';
    public const PARAMETER_IS_PUBLISHED = 'isPublished';
    public const PARAMETER_JOB_TITLE_ID = 'jobTitleId';
    public const PARAMETER_EMPLOYEE_ID = 'employeeId';

    public const PARAMETER_RULE_NAME_MAX_LENGTH = 100;
    public const PARAMETER_RULE_NO_OF_POSITIONS_MAX_LENGTH = 13;
    public const PARAMETER_RULE_STATUS_MAX_LENGTH = 4;

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResourceResult
    {
        $id = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_ID
        );
        $vacancy = $this->getVacancyService()->getVacancyDao()->getVacancyById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($vacancy, Vacancy::class);
        return new EndpointResourceResult(VacancyModel::class, $vacancy);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(new ParamRule(CommonParams::PARAMETER_ID));
    }

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointCollectionResult
    {
        $vacancyParamHolder = new VacancySearchFilterParams();
        $this->setSortingAndPaginationParams($vacancyParamHolder);

        $vacancyParamHolder->setJobTitleId(
            $this->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_JOB_TITLE_ID
            )
        );
        $vacancyParamHolder->setEmpNumber(
            $this->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_HIRING_MANAGER_ID
            )
        );
        $vacancyParamHolder->setVacancyId(
            $this->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_VACANCY_ID
            )
        );
        $vacancyParamHolder->setStatus(
            $this->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_STATUS
            )
        );
        $vacancyParamHolder->setName(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_NAME
            )
        );
        $vacancies = $this->getVacancyService()->getVacancyDao()->getVacancies($vacancyParamHolder);
        $count = $this->getVacancyService()->getVacancyDao()->searchVacanciesCount($vacancyParamHolder);
        return new EndpointCollectionResult(
            VacancyModel::class,
            $vacancies,
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
                    self::FILTER_VACANCY_ID,
                    new Rule(Rules::POSITIVE)
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_HIRING_MANAGER_ID,
                    new Rule(Rules::POSITIVE)
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_JOB_TITLE_ID,
                    new Rule(Rules::POSITIVE)
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_STATUS,
                    new Rule(Rules::POSITIVE)
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_NAME,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [0, self::PARAMETER_RULE_NAME_MAX_LENGTH])
                )
            ),
            ...$this->getSortingAndPaginationParamsRules(VacancySearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @inheritDoc
     */
    public function create(): EndpointResult
    {
        $vacancy = new Vacancy();
        $vacancy->setDefinedTime($this->getDateTimeHelper()->getNow());
        $this->setVacancy($vacancy);
        $vacancy = $this->getVacancyService()->getVacancyDao()->saveJobVacancy($vacancy);

        return new EndpointResourceResult(VacancyModel::class, $vacancy);
    }

    /**
     * @param Vacancy $vacancy
     */
    private function setVacancy(Vacancy $vacancy): void
    {
        $vacancy->setName(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_NAME
            )
        );
        $vacancy->setDescription(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_DESCRIPTION
            )
        );
        $vacancy->setNumOfPositions(
            $this->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_NUM_OF_POSITIONS
            )
        );
        $vacancy->getDecorator()->setIsPublished(
            $this->getRequestParams()->getBooleanOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_IS_PUBLISHED
            )
        );
        $vacancy->setUpdatedTime($this->getDateTimeHelper()->getNow());
        $vacancy->setStatus(
            $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_STATUS
            )
        );
        $vacancy->getDecorator()->setJobTitleById(
            $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_JOB_TITLE_ID
            )
        );
        $vacancy->getDecorator()->setEmployeeById(
            $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_EMPLOYEE_ID
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            ...$this->getCommonBodyValidationRules(),
        );
    }

    /**
     * @return ParamRule[]
     */
    protected function getCommonBodyValidationRules(): array
    {
        return [
            new ParamRule(
                self::PARAMETER_NAME,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, self::PARAMETER_RULE_NAME_MAX_LENGTH])
            ),
            new ParamRule(
                self::PARAMETER_STATUS,
                new Rule(Rules::INT_TYPE),
                new Rule(Rules::LENGTH, [null, self::PARAMETER_RULE_STATUS_MAX_LENGTH])
            ),
            new ParamRule(
                self::PARAMETER_JOB_TITLE_ID,
                new Rule(Rules::POSITIVE),
                new Rule(Rules::ENTITY_ID_EXISTS, [JobTitle::class]),
            ),
            new ParamRule(
                self::PARAMETER_IS_PUBLISHED,
                new Rule(Rules::BOOL_TYPE),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_DESCRIPTION,
                    new Rule(Rules::STRING_TYPE),
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_NUM_OF_POSITIONS,
                    new Rule(Rules::INT_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAMETER_RULE_NO_OF_POSITIONS_MAX_LENGTH])
                )
            ),
            new ParamRule(
                self::PARAMETER_EMPLOYEE_ID,
                new Rule(Rules::POSITIVE),
                new Rule(Rules::ENTITY_ID_EXISTS, [Employee::class])
            ),
        ];
    }

    /**
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $vacancy = $this->getVacancyService()->getVacancyDao()->getVacancyById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($vacancy, Vacancy::class);
        $this->setVacancy($vacancy);
        $this->getVacancyService()->getVacancyDao()->saveJobVacancy($vacancy);
        return new EndpointResourceResult(VacancyModel::class, $vacancy);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            ),
            ...$this->getCommonBodyValidationRules(),
        );
    }

    /**
     * @inheritDoc
     */
    public function delete(): EndpointResult
    {
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS);
        $this->getVacancyService()->getVacancyDao()->deleteVacancies($ids);
        return new EndpointResourceResult(ArrayModel::class, $ids);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_IDS,
                new Rule(Rules::ARRAY_TYPE)
            ),
        );
    }
}
