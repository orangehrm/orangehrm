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
class RegistrationEmployeeTerminationEventProcessor extends AbstractRegistrationEventProcessor
{

    public function getEventType()
    {
        return RegistrationEventQueue::INACTIVE_EMPLOYEE_COUNT;
    }

    public function getEventData()
    {
        $registrationData = $this->getRegistrationEventGeneralData();
        $totalEmployeeCount = $this->getEmployeeCount(true);
        $activeEmployeeCOunt = $this->getEmployeeCount();
        $employeeCount = $totalEmployeeCount - $activeEmployeeCOunt;
        $registrationData['employee_count'] = $employeeCount;
        return $registrationData;
    }

    public function getEventToBeSavedOrNot()
    {
        $totalEmployeeCount = $this->getEmployeeCount(true);
        $activeEmployeeCOunt = $this->getEmployeeCount();
        $employeeCount = $totalEmployeeCount - $activeEmployeeCOunt;
        if ($employeeCount % RegistrationEventQueue::EMPLOYEE_COUNT_CHANGE_TRACKER_SIZE == 0) {
            return true;
        }
        return false;
    }

    public function getEmployeeCount($includeTerminated = false) {
        return $this->getEmployeeDao()->getEmployeeCount($includeTerminated);
    }
}
