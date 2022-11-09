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

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\Exception\RecordNotFoundException;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Core\Exception\ServiceException;
use OrangeHRM\Entity\EmpDependent;
use OrangeHRM\Pim\Api\Model\EmployeeDependentModel;
use OrangeHRM\Pim\Dto\EmployeeDependentSearchFilterParams;
use OrangeHRM\Pim\Service\EmployeeDependentService;

class EmployeeDependentAPI extends Endpoint implements CrudEndpoint
{
    public const PARAMETER_NAME = 'name';
    public const PARAMETER_RELATIONSHIP_TYPE = 'relationshipType';
    public const PARAMETER_RELATIONSHIP = 'relationship';
    public const PARAMETER_DATE_OF_BIRTH = 'dateOfBirth';

    public const FILTER_NAME = 'name';
    public const FILTER_RELATIONSHIP_TYPE = 'relationshipType';

    public const PARAM_RULE_NAME_MAX_LENGTH = 100;
    public const PARAM_RULE_RELATIONSHIP_MAX_LENGTH = 100;

    /**
     * @var EmployeeDependentService|null
     */
    protected ?EmployeeDependentService $employeeDependentService = null;

    /**
     * @return EmployeeDependentService
     */
    public function getEmployeeDependentService(): EmployeeDependentService
    {
        if (!$this->employeeDependentService instanceof EmployeeDependentService) {
            $this->employeeDependentService = new EmployeeDependentService();
        }
        return $this->employeeDependentService;
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
        $id = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_ID
        );

        $empDependent = $this->getEmployeeDependentService()->getEmployeeDependent($empNumber, $id);
        $this->throwRecordNotFoundExceptionIfNotExist($empDependent, EmpDependent::class);

        return new EndpointResourceResult(
            EmployeeDependentModel::class,
            $empDependent,
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
                CommonParams::PARAMETER_EMP_NUMBER,
                new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
            ),
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::BETWEEN, [0, 100])
            ),
        );
    }

    /**
     * @inheritDoc
     * @throws ServiceException|DaoException
     */
    public function getAll(): EndpointCollectionResult
    {
        $employeeDependentSearchParams = new EmployeeDependentSearchFilterParams();
        $this->setSortingAndPaginationParams($employeeDependentSearchParams);
        $employeeDependentSearchParams->setName(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_NAME
            )
        );
        $employeeDependentSearchParams->setRelationshipType(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_RELATIONSHIP_TYPE
            )
        );

        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );

        $employeeDependentSearchParams->setEmpNumber(
            $empNumber
        );
        $empDependents = $this->getEmployeeDependentService()->searchEmployeeDependent($employeeDependentSearchParams);

        return new EndpointCollectionResult(
            EmployeeDependentModel::class,
            $empDependents,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                    CommonParams::PARAMETER_TOTAL => $this->getEmployeeDependentService(
                    )->getSearchEmployeeDependentsCount(
                        $employeeDependentSearchParams
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
            new ParamRule(
                CommonParams::PARAMETER_EMP_NUMBER,
                new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
            ),
            new ParamRule(self::FILTER_NAME),
            new ParamRule(self::FILTER_RELATIONSHIP_TYPE),
            ...$this->getSortingAndPaginationParamsRules(EmployeeDependentSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @inheritDoc
     */
    public function create(): EndpointResourceResult
    {
        $empDependent = $this->saveEmpDependent();

        return new EndpointResourceResult(
            EmployeeDependentModel::class,
            $empDependent,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => $empDependent->getEmployee()->getEmpNumber(),
                ]
            )
        );
    }

    /**
     * @return EmpDependent
     * @throws RecordNotFoundException
     * @throws DaoException
     */
    protected function saveEmpDependent(): EmpDependent
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $id = $this->getRequestParams()->getIntOrNull(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_ID
        );
        if ($id) {
            $empDependent = $this->getEmployeeDependentService()->getEmployeeDependent($empNumber, $id);
            $this->throwRecordNotFoundExceptionIfNotExist($empDependent, EmpDependent::class);
        } else {
            $empDependent = new EmpDependent();
            $empDependent->getDecorator()->setEmployeeByEmpNumber($empNumber);
        }

        $empDependent->setName(
            $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_NAME)
        );
        $empDependent->setRelationshipType(
            $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_RELATIONSHIP_TYPE)
        );
        $empDependent->setRelationship(
            $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_RELATIONSHIP)
        );
        $empDependent->setDateOfBirth(
            $this->getRequestParams()->getDateTimeOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_DATE_OF_BIRTH)
        );

        return $this->getEmployeeDependentService()->saveEmployeeDependent($empDependent);
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
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_NAME_MAX_LENGTH]),
            ),
            new ParamRule(
                self::PARAMETER_RELATIONSHIP_TYPE,
                new Rule(Rules::IN, [[EmpDependent::RELATIONSHIP_TYPE_CHILD, EmpDependent::RELATIONSHIP_TYPE_OTHER]]),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_RELATIONSHIP,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_RELATIONSHIP_MAX_LENGTH]),
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_DATE_OF_BIRTH,
                    new Rule(Rules::API_DATE),
                )
            ),
        ];
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_EMP_NUMBER,
                new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
            ),
            ...$this->getCommonBodyValidationRules(),
        );
    }

    /**
     * @inheritDoc
     */
    public function update(): EndpointResourceResult
    {
        $empDependent = $this->saveEmpDependent();

        return new EndpointResourceResult(
            EmployeeDependentModel::class,
            $empDependent,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => $empDependent->getEmployee()->getEmpNumber(),
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
            new ParamRule(
                CommonParams::PARAMETER_EMP_NUMBER,
                new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
            ),
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::BETWEEN, [0, 100])
            ),
            ...$this->getCommonBodyValidationRules(),
        );
    }

    /**
     * @inheritDoc
     * @throws DaoException
     */
    public function delete(): EndpointResourceResult
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS);
        $this->getEmployeeDependentService()->deleteEmployeeDependents($empNumber, $ids);
        return new EndpointResourceResult(ArrayModel::class, $ids);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_EMP_NUMBER,
                new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
            ),
            new ParamRule(CommonParams::PARAMETER_IDS),
        );
    }
}
