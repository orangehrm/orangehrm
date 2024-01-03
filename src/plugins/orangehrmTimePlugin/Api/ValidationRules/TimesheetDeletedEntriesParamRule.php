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

namespace OrangeHRM\Time\Api\ValidationRules;

use OrangeHRM\Core\Api\V2\Validator\Rules\AbstractRule;
use OrangeHRM\Time\Api\EmployeeTimesheetItemAPI;

class TimesheetDeletedEntriesParamRule extends AbstractRule
{
    /**
     * @inheritDoc
     */
    public function validate($entries): bool
    {
        if (!is_array($entries)) {
            return false;
        }
        foreach ($entries as $entry) {
            if (count(array_keys($entry)) != 2) {
                return false;
            }
            // `projectId`, `activityId` required fields
            if (!(isset($entry[EmployeeTimesheetItemAPI::PARAMETER_PROJECT_ID]) &&
                isset($entry[EmployeeTimesheetItemAPI::PARAMETER_ACTIVITY_ID]))) {
                return false;
            }
            $projectId = $entry[EmployeeTimesheetItemAPI::PARAMETER_PROJECT_ID];
            if (!(is_numeric($projectId) && ($projectId > 0))) {
                return false;
            }
            $activityId = $entry[EmployeeTimesheetItemAPI::PARAMETER_ACTIVITY_ID];
            if (!(is_numeric($activityId) && ($activityId > 0))) {
                return false;
            }
        }
        return true;
    }
}
