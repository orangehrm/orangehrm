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

namespace OrangeHRM\Dashboard\Dto\ActionSummary;

use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\WorkflowStateMachine;
use OrangeHRM\Time\Dto\EmployeeTimesheetListSearchFilterParams;
use OrangeHRM\Time\Traits\Service\TimesheetServiceTrait;

class PendingTimesheetSummary implements ActionSummary
{
    use TimesheetServiceTrait;
    use UserRoleManagerTrait;

    /**
     * @var EmployeeTimesheetListSearchFilterParams
     */
    private EmployeeTimesheetListSearchFilterParams $employeeTimesheetFilterParams;

    /**
     * @param int[] $accessibleEmpNumbers
     */
    public function __construct(array $accessibleEmpNumbers)
    {
        $employeeTimesheetFilterParams = new EmployeeTimesheetListSearchFilterParams();
        $employeeTimesheetFilterParams->setEmployeeNumbers(array_values($accessibleEmpNumbers));
        $actionableStatesList = $this->getUserRoleManager()
            ->getActionableStates(
                WorkflowStateMachine::FLOW_TIME_TIMESHEET,
                [
                    WorkflowStateMachine::TIMESHEET_ACTION_APPROVE,
                    WorkflowStateMachine::TIMESHEET_ACTION_REJECT
                ]
            );
        $employeeTimesheetFilterParams->setActionableStatesList($actionableStatesList);
        $this->employeeTimesheetFilterParams = $employeeTimesheetFilterParams;
    }

    /**
     * @inheritDoc
     */
    public function getGroupId(): int
    {
        return 2;
    }

    /**
     * @inheritDoc
     */
    public function getGroup(): string
    {
        return 'Timesheets To Approve';
    }

    /**
     * @inheritDoc
     */
    public function getPendingActionCount(): int
    {
        return $this->getTimesheetService()
            ->getTimesheetDao()
            ->getEmployeeTimesheetListCount($this->employeeTimesheetFilterParams);
    }
}
