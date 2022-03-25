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

namespace OrangeHRM\Time\Api\Model;

use DoctrineProxies\__CG__\OrangeHRM\Entity\EmployeeTerminationRecord;
use OrangeHRM\Core\Api\V2\Serializer\ModelTrait;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Entity\TimesheetActionLog;
use OrangeHRM\Entity\User;

class TimesheetActionLogModel implements Normalizable
{
    use ModelTrait;

    private TimesheetActionLog $timesheetActionLog;

    public function __construct(TimesheetActionLog $timesheetActionLog)
    {
        $this->timesheetActionLog = $timesheetActionLog;
    }

    /**
     * @return TimesheetActionLog
     */
    public function getTimesheetActionLog(): TimesheetActionLog
    {
        return $this->timesheetActionLog;
    }

    public function toArray(): array
    {
        $performedUser = $this->getTimesheetActionLog()->getPerformedUser();
        if ($performedUser instanceof User) {
            $terminationRecord =$performedUser->getEmployee()->getEmployeeTerminationRecord();
        } else {
            $terminationRecord = null;
        }
        return [
            'id' => $this->timesheetActionLog->getId(),
            'action' => [
                'name' => $this->timesheetActionLog->getAction(),
                'label' =>$this->timesheetActionLog->getDecorator()->getActionLabel(),
            ],
            'comment' => $this->timesheetActionLog->getComment(),
            'date' => $this->timesheetActionLog->getDecorator()->getDate(),
            'performedEmployee' => [
                'empNumber' => $performedUser instanceof User ? $performedUser->getEmployee()->getEmpNumber() : null,
                'lastName' => $performedUser instanceof User ? $performedUser->getEmployee()->getLastName() : 'employee',
                'firstName' => $performedUser instanceof User ? $performedUser->getEmployee()->getFirstName() : 'purged',
                'middleName' => $performedUser instanceof User ? $performedUser->getEmployee()->getMiddleName() : null,
                'employeeId' => $performedUser instanceof User ? $performedUser->getEmployee()->getEmployeeId() : null,
                'terminationId' => $terminationRecord instanceof EmployeeTerminationRecord ? $terminationRecord->getId() : null,
            ],
        ];
    }
}
