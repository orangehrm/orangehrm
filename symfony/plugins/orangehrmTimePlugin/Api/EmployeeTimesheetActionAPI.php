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

namespace OrangeHRM\Time\Api;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CollectionEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Service\AccessFlowStateMachineService;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\WorkflowStateMachine;
use OrangeHRM\Time\Api\Model\EmployeeTimesheetModel;
use OrangeHRM\Time\Dto\EmployeeTimesheetActionSearchFilterParams;
use OrangeHRM\Time\Traits\Service\TimesheetServiceTrait;

class EmployeeTimesheetActionAPI extends Endpoint implements CollectionEndpoint
{
    use TimesheetServiceTrait;
    use UserRoleManagerTrait;

    public const FILTER_EMP_NUMBER = 'empNumber';

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $employeeTimesheetActionSearchParamHolder = new EmployeeTimesheetActionSearchFilterParams();
        $this->setSortingAndPaginationParams($employeeTimesheetActionSearchParamHolder);
        $empNumber = $this->getRequestParams()->getIntOrNull(
            RequestParams::PARAM_TYPE_QUERY,
            self::FILTER_EMP_NUMBER
        );

        if (!is_null($empNumber)) {
            $employeeTimesheetActionSearchParamHolder->setEmployeeNumbers([$empNumber]);
        } else {
            $accessibleEmpNumbers = $this->getUserRoleManager()->getAccessibleEntityIds(Employee::class);
            $employeeTimesheetActionSearchParamHolder->setEmployeeNumbers($accessibleEmpNumbers);
        }

        $actions = [WorkflowStateMachine::TIMESHEET_ACTION_APPROVE, WorkflowStateMachine::TIMESHEET_ACTION_REJECT];
        $actionableStatesList = $this->getUserRoleManager()
            ->getActionableStates(WorkflowStateMachine::FLOW_TIME_TIMESHEET, $actions);
        $employeeTimesheetActionSearchParamHolder->setActionableStatesList($actionableStatesList);

        $employeeTimesheetList = $this->getTimesheetService()
            ->getTimesheetDao()
            ->getEmployeeTimesheetListByState($employeeTimesheetActionSearchParamHolder);

        $employeeTimesheetListCount = $this->getTimesheetService()
            ->getTimesheetDao()
            ->getEmployeeTimesheetListCountByState($employeeTimesheetActionSearchParamHolder);

        return new EndpointCollectionResult(
            EmployeeTimesheetModel::class,
            $employeeTimesheetList,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $employeeTimesheetListCount])
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
                    CommonParams::PARAMETER_EMP_NUMBER,
                    new Rule(Rules::POSITIVE),
                    new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
                )
            ),
            ...$this->getSortingAndPaginationParamsRules(EmployeeTimesheetActionSearchFilterParams::ALLOWED_SORT_FIELDS)
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
}
