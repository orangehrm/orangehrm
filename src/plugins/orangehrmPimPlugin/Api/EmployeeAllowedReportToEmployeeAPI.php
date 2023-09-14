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

namespace OrangeHRM\Pim\Api;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CollectionEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Pim\Api\Model\EmployeeAllowedReportToEmployeeModel;
use OrangeHRM\Pim\Dto\EmployeeSearchFilterParams;
use OrangeHRM\Pim\Service\EmployeeReportingMethodService;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Pim\Traits\Service\EmployeeServiceTrait;

class EmployeeAllowedReportToEmployeeAPI extends Endpoint implements CollectionEndpoint
{
    use UserRoleManagerTrait;
    use EmployeeServiceTrait;

    public const FILTER_NAME_OR_ID = 'nameOrId';

    public const PARAM_RULE_FILTER_NAME_OR_ID_MAX_LENGTH = 100;

    /**
     * @var EmployeeReportingMethodService|null
     */
    protected ?EmployeeReportingMethodService $employeeReportingMethodService = null;

    /**
     * @return EmployeeReportingMethodService
     */
    public function getEmployeeReportingMethodService(): EmployeeReportingMethodService
    {
        if (!$this->employeeReportingMethodService instanceof EmployeeReportingMethodService) {
            $this->employeeReportingMethodService = new EmployeeReportingMethodService();
        }
        return $this->employeeReportingMethodService;
    }

    /**
     * @OA\Get(
     *     path="/api/v2/pim/employees/{empNumber}/report-to/allowed",
     *     tags={"PIM/Employee Report To"},
     *     summary="List Available Employees",
     *     operationId="list-available-employees",
     *     description="List employees that are available to be assigned as supervisors/subordinates to the given employee.",
     *     @OA\PathParameter(
     *         name="empNumber",
     *         description="Specify the employee number of desired employee",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="nameOrId",
     *         description="Specify the name or employee ID of the desired employee",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             maxLength=OrangeHRM\Pim\Api\EmployeeAllowedReportToEmployeeAPI::PARAM_RULE_FILTER_NAME_OR_ID_MAX_LENGTH
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sortField",
     *         description="Sort the employee list by last name, first name, middle name, employee number, employee ID, job title name, employee status name, subunit name or supervisor's first name",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=EmployeeSearchFilterParams::ALLOWED_SORT_FIELDS)
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
     *                 @OA\Items(ref="#/components/schemas/Pim-EmployeeAllowedReportToEmployeeModel")
     *             ),
     *             @OA\Property(property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", description="The total number of available employees", type="integer")
     *             )
     *         )
     *     ),
     * )
     *
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $employeeParamHolder = new EmployeeSearchFilterParams();
        $this->setSortingAndPaginationParams($employeeParamHolder);
        if (!is_null($empNumber)) {
            $subordinateIdArray = $this->getEmployeeService()->getSubordinateIdListBySupervisorId($empNumber);
            $supervisorIdArray = $this->getEmployeeService()->getSupervisorIdListBySubordinateId($empNumber);
            $accessibleEmpNumbers = $this->getUserRoleManager()->getAccessibleEntityIds(Employee::class);
            $existingSupervisorsAndSubordinates = $this->getEmployeeReportingMethodService()->getAlreadyAssignedSupervisorsSubordinatesAndSelfIdCombinedList($supervisorIdArray, $subordinateIdArray, $empNumber);
            $accessibleAndAvailableEmpNumbers = $this->getEmployeeReportingMethodService()->getAccessibleAndAvailableSupervisorsIdCombinedList($accessibleEmpNumbers, $existingSupervisorsAndSubordinates);
            $employeeParamHolder->setEmployeeNumbers($accessibleAndAvailableEmpNumbers);
        }
        $employeeParamHolder->setNameOrId(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_NAME_OR_ID
            )
        );

        $employees = $this->getEmployeeService()->getEmployeeList($employeeParamHolder);
        $count = $this->getEmployeeService()->getEmployeeCount($employeeParamHolder);
        return new EndpointCollectionResult(
            EmployeeAllowedReportToEmployeeModel::class,
            $employees,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getEmpNumberRule(),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_NAME_OR_ID,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_FILTER_NAME_OR_ID_MAX_LENGTH]),
                )
            ),
            ...$this->getSortingAndPaginationParamsRules(EmployeeSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @inheritDoc
     */
    public function create(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
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
