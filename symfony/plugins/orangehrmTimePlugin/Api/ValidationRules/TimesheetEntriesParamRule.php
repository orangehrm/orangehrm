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

namespace OrangeHRM\Time\Api\ValidationRules;

use OrangeHRM\Core\Api\V2\Validator\Rules\AbstractRule;
use OrangeHRM\Core\Api\V2\Validator\Rules\ApiDate;
use OrangeHRM\Time\Api\EmployeeTimesheetItemAPI;
use Respect\Validation\Rules\Time;

class TimesheetEntriesParamRule extends AbstractRule
{
    private ?ApiDate $apiDateRule = null;
    private ?Time $timeRule = null;
    private bool $isDeletedEntries;

    /**
     * @param bool $isDeletedEntries
     */
    public function __construct(bool $isDeletedEntries = false)
    {
        $this->isDeletedEntries = $isDeletedEntries;
    }

    /**
     * @return ApiDate
     */
    private function getApiDateRule(): ApiDate
    {
        if (is_null($this->apiDateRule)) {
            $this->apiDateRule = new ApiDate();
        }
        return $this->apiDateRule;
    }

    /**
     * @return Time
     */
    private function getTimeRule(): Time
    {
        if (is_null($this->timeRule)) {
            $this->timeRule = new Time('H:i');
        }
        return $this->timeRule;
    }

    /**
     * @inheritDoc
     */
    public function validate($entries): bool
    {
        if (!is_array($entries)) {
            return false;
        }
        foreach ($entries as $entry) {
            // `projectId`, `activityId`, `dates` required fields
            if (!(isset($entry[EmployeeTimesheetItemAPI::PARAMETER_PROJECT_ID]) &&
                isset($entry[EmployeeTimesheetItemAPI::PARAMETER_ACTIVITY_ID]))) {
                return false;
            }

            $projectId = $entry[EmployeeTimesheetItemAPI::PARAMETER_PROJECT_ID];
            if (!is_numeric($projectId) && !($projectId > 0)) {
                return false;
            }
            $activityId = $entry[EmployeeTimesheetItemAPI::PARAMETER_ACTIVITY_ID];
            if (!is_numeric($activityId) && !($activityId > 0)) {
                return false;
            }

            // If validating deleted entries, skip below checks
            if ($this->isDeletedEntries) {
                continue;
            }

            // `dates` field is required
            if (!isset($entry[EmployeeTimesheetItemAPI::PARAMETER_DATES])) {
                return false;
            }
            $dates = $entry[EmployeeTimesheetItemAPI::PARAMETER_DATES];
            // `dates` field should not empty
            if (empty($dates)) {
                return false;
            }

            foreach ($dates as $date => $dateValue) {
                if (!isset($dateValue[EmployeeTimesheetItemAPI::PARAMETER_DURATION])) {
                    return false;
                }
                if (!$this->getApiDateRule()->validate($date)) {
                    return false;
                }
                // TODO:: $date should between timesheet startDate and endDate
                // TODO:: validate max 24 hours
                if (!$this->getTimeRule()->validate($dateValue[EmployeeTimesheetItemAPI::PARAMETER_DURATION])) {
                    return false;
                }
            }
        }
        return true;
    }
}
