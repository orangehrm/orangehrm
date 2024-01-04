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

use DateTime;
use OrangeHRM\Core\Api\V2\Validator\Rules\AbstractRule;
use OrangeHRM\Core\Api\V2\Validator\Rules\ApiDate;
use OrangeHRM\Entity\Timesheet;
use OrangeHRM\Time\Api\EmployeeTimesheetItemAPI;
use OrangeHRM\Time\Traits\Service\ProjectServiceTrait;
use OrangeHRM\Time\Traits\Service\TimesheetServiceTrait;
use Respect\Validation\Rules\Time;

class TimesheetEntriesParamRule extends AbstractRule
{
    use TimesheetServiceTrait;
    use ProjectServiceTrait;

    private ?ApiDate $apiDateRule = null;
    private ?Time $timeRule = null;
    private ?Timesheet $timesheet = null;
    private $timesheetId;

    /**
     * @param int $timesheetId
     */
    public function __construct($timesheetId)
    {
        $this->timesheetId = $timesheetId;
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

        if (is_numeric($this->timesheetId) && $this->timesheetId > 0) {
            $timesheet = $this->getTimesheetService()->getTimesheetDao()->getTimesheetById($this->timesheetId);
            if ($timesheet instanceof Timesheet) {
                $this->timesheet = $timesheet;
            }
        }

        $projectActivityIdPairs = [];
        $rowCount = 0;
        foreach ($entries as $entry) {
            if (count(array_keys($entry)) != 3) {
                return false;
            }
            // `projectId`, `activityId`, `dates` required fields
            if (!(isset($entry[EmployeeTimesheetItemAPI::PARAMETER_PROJECT_ID]) &&
                isset($entry[EmployeeTimesheetItemAPI::PARAMETER_ACTIVITY_ID]) &&
                isset($entry[EmployeeTimesheetItemAPI::PARAMETER_DATES]))) {
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

            $dates = $entry[EmployeeTimesheetItemAPI::PARAMETER_DATES];
            // `dates` field should not empty
            if (empty($dates)) {
                return false;
            }

            $validatedDates = [];
            foreach ($dates as $date => $dateValue) {
                if (!isset($dateValue[EmployeeTimesheetItemAPI::PARAMETER_DURATION])) {
                    return false;
                }
                if (!$this->getApiDateRule()->validate($date)) {
                    return false;
                }

                // check date is unique
                if (isset($validatedDates[$date])) {
                    return false;
                }
                $validatedDates[$date] = true;

                $dateObj = new DateTime($date);
                // check date within the startDate and endDate of the timesheet
                if ($this->timesheet instanceof Timesheet &&
                    !($this->timesheet->getStartDate() <= $dateObj && $dateObj <= $this->timesheet->getEndDate())) {
                    return false;
                }

                // only duration allowed
                if (count(array_keys($dateValue)) != 1) {
                    return false;
                }
                // check format and duration should less than 24:00
                if (!$this->getTimeRule()->validate($dateValue[EmployeeTimesheetItemAPI::PARAMETER_DURATION])) {
                    return false;
                }
            }

            $projectActivityIdPairs[] = [$activityId, $projectId];
            $rowCount++;
        }

        $count = $this->getProjectService()
            ->getProjectActivityDao()
            ->getActivitiesCountByProjectActivityIdPairs($projectActivityIdPairs);
        /**
         * 1. Check project ids, and activity ids available
         * 2. Check an activity belongs to particular project id
         * 3. Validate duplicated project and activity rows
         * 4. Consider deleted project ids, and deleted activity ids
         */
        if ($rowCount !== $count) {
            return false;
        }
        return true;
    }
}
