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
use OrangeHRM\Core\Api\V2\Serializer\EndpointCreateResult;
use OrangeHRM\Core\Api\V2\Serializer\EndpointDeleteResult;
use OrangeHRM\Core\Api\V2\Serializer\EndpointGetAllResult;
use OrangeHRM\Core\Api\V2\Serializer\EndpointGetOneResult;
use OrangeHRM\Core\Api\V2\Serializer\EndpointUpdateResult;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\EmployeeEducation;
use OrangeHRM\Pim\Api\Model\EmployeeEducationModel;
use OrangeHRM\Pim\Dto\EmployeeEducationSearchFilterParams;
use OrangeHRM\Pim\Service\EmployeeEducationService;

class EmployeeEducationAPI extends Endpoint implements CrudEndpoint
{
    public const PARAMETER_EDUCATION_ID = 'educationId';
    public const PARAMETER_INSTITUTE = 'institute';
    public const PARAMETER_MAJOR = 'major';
    public const PARAMETER_YEAR = 'year';
    public const PARAMETER_SCORE = 'score';
    public const PARAMETER_START_DATE = 'startDate';
    public const PARAMETER_END_DATE = 'endDate';

    public const PARAM_RULE_INSTITUTE_MAX_LENGTH = 100;
    public const PARAM_RULE_MAJOR_MAX_LENGTH = 100;
    public const PARAM_RULE_YEAR_MAX_LENGTH = 4;
    public const PARAM_RULE_SCORE_MAX_LENGTH = 25;

    /**
     * @var null|EmployeeEducationService
     */
    protected ?EmployeeEducationService $employeeEducationService = null;

    /**
     * @return EmployeeEducationService
     */
    public function getEmployeeEducationService(): EmployeeEducationService
    {
        if (is_null($this->employeeEducationService)) {
            $this->employeeEducationService = new EmployeeEducationService();
        }
        return $this->employeeEducationService;
    }

    /**
     * @param EmployeeEducationService $employeeEducationService
     */
    public function setEmployeeEducationService(EmployeeEducationService $employeeEducationService): void
    {
        $this->employeeEducationService = $employeeEducationService;
    }

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointGetOneResult
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $educationId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_ID
        );
        $employeeEducation = $this->getEmployeeEducationService()->getEmployeeEducationDao()->getEmployeeEducationById(
            $empNumber,
            $educationId
        );
        $this->throwRecordNotFoundExceptionIfNotExist($employeeEducation, EmployeeEducation::class);

        return new EndpointGetOneResult(
            EmployeeEducationModel::class,
            $employeeEducation,
            new ParameterBag([CommonParams::PARAMETER_EMP_NUMBER => $empNumber])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_ID),
            $this->getEmpNumberRule(),
        );
    }

    /**
     * @return EndpointGetAllResult
     * @throws Exception
     */
    public function getAll(): EndpointGetAllResult
    {
        $employeeEducationSearchParams = new EmployeeEducationSearchFilterParams();
        $this->setSortingAndPaginationParams($employeeEducationSearchParams);
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $employeeEducationSearchParams->setEmpNumber(
            $empNumber
        );

        $employeeEducations = $this->getEmployeeEducationService()->getEmployeeEducationDao()->searchEmployeeEducation(
            $employeeEducationSearchParams
        );

        return new EndpointGetAllResult(
            EmployeeEducationModel::class,
            $employeeEducations,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                    CommonParams::PARAMETER_TOTAL => $this->getEmployeeEducationService()->getEmployeeEducationDao(
                    )->getSearchEmployeeEducationsCount(
                        $employeeEducationSearchParams
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
            ...$this->getSortingAndPaginationParamsRules(EmployeeEducationSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function create(): EndpointCreateResult
    {
        $employeeEducation = $this->saveEmployeeEducation();
        return new EndpointCreateResult(
            EmployeeEducationModel::class, $employeeEducation,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => $employeeEducation->getEmployee()->getEmpNumber(),
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
            new ParamRule(self::PARAMETER_EDUCATION_ID, new Rule(Rules::REQUIRED), new Rule(Rules::POSITIVE)),
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
                self::PARAMETER_INSTITUTE,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_INSTITUTE_MAX_LENGTH]),
            ),
            new ParamRule(
                self::PARAMETER_MAJOR,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_MAJOR_MAX_LENGTH]),
            ),
            new ParamRule(
                self::PARAMETER_YEAR,
                new Rule(Rules::INT_TYPE),
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_YEAR_MAX_LENGTH]),
            ),
            new ParamRule(
                self::PARAMETER_SCORE,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_SCORE_MAX_LENGTH]),
            ),
            new ParamRule(
                self::PARAMETER_START_DATE,
                new Rule(Rules::API_DATE),
            ),
            new ParamRule(
                self::PARAMETER_END_DATE,
                new Rule(Rules::API_DATE),
            ),

        ];
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function update(): EndpointUpdateResult
    {
        $employeeEducation = $this->saveEmployeeEducation();

        return new EndpointUpdateResult(
            EmployeeEducationModel::class, $employeeEducation,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => $employeeEducation->getEmployee()->getEmpNumber(),
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
    public function delete(): EndpointDeleteResult
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS);
        $this->getEmployeeEducationService()->getEmployeeEducationDao()->deleteEmployeeEducations($empNumber, $ids);
        return new EndpointDeleteResult(
            ArrayModel::class, $ids,
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
            new ParamRule(CommonParams::PARAMETER_IDS),
        );
    }

    /**
     * @return EmployeeEducation
     * @throws DaoException
     * @throws Exception
     */
    public function saveEmployeeEducation(): EmployeeEducation
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $educationId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_EDUCATION_ID);
        $year = $this->getRequestParams()->getIntOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_YEAR
        );
        $score = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_SCORE
        );
        $institute = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_INSTITUTE
        );
        $major = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_MAJOR
        );
        $startDate = $this->getRequestParams()->getDateTimeOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_START_DATE
        );
        $endDate = $this->getRequestParams()->getDateTimeOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_END_DATE
        );
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        if (!empty($educationId)) { // create operation
            $id = $educationId;
        }
        $employeeEducation = $this->getEmployeeEducationService()->getEmployeeEducationDao()->getEmployeeEducationById(
            $empNumber,
            $id
        );
        if ($employeeEducation == null) {
            $employeeEducation = new EmployeeEducation();
            $employeeEducation->getDecorator()->setEmployeeByEmpNumber($empNumber);
            $employeeEducation->getDecorator()->setEducationByEducationId($id);
        }
        $employeeEducation->setYear($year);
        $employeeEducation->setScore($score);
        $employeeEducation->setInstitute($institute);
        $employeeEducation->setMajor($major);

        // TODO:: API_DATE
        $employeeEducation->setStartDate($startDate);
        $employeeEducation->setEndDate($endDate);

        return $this->getEmployeeEducationService()->getEmployeeEducationDao()->saveEmployeeEducation(
            $employeeEducation
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
