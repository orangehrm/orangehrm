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

namespace Orangehrm\Rest\Api\Time\Entity;

use Orangehrm\Rest\Api\Entity\Serializable;

class TimeSheet implements Serializable
{

    /**
     * @var
     */
    private $projectName;
    private $activityName;
    private $projectId;
    private $activityId;
    private $employeeId;
    private $timeSheetId;
    private $timeSheetStartDate;
    private $timeSheetEndDate;
    private $timeSheetItems;
    private $status;

    /**
     * @return mixed
     */
    public function getProjectName()
    {
        return $this->projectName;
    }

    /**
     * @param mixed $projectName
     */
    public function setProjectName($projectName)
    {
        $this->projectName = $projectName;
    }

    /**
     * @return mixed
     */
    public function getActivityName()
    {
        return $this->activityName;
    }

    /**
     * @param mixed $activityName
     */
    public function setActivityName($activityName)
    {
        $this->activityName = $activityName;
    }

    /**
     * @return mixed
     */
    public function getProjectId()
    {
        return $this->projectId;
    }

    /**
     * @param mixed $projectId
     */
    public function setProjectId($projectId)
    {
        $this->projectId = $projectId;
    }

    /**
     * @return mixed
     */
    public function getActivityId()
    {
        return $this->activityId;
    }

    /**
     * @param mixed $activityId
     */
    public function setActivityId($activityId)
    {
        $this->activityId = $activityId;
    }

    /**
     * @return mixed
     */
    public function getEmployeeId()
    {
        return $this->employeeId;
    }

    /**
     * @param mixed $employeeId
     */
    public function setEmployeeId($employeeId)
    {
        $this->employeeId = $employeeId;
    }

    /**
     * @return mixed
     */
    public function getTimeSheetId()
    {
        return $this->timeSheetId;
    }

    /**
     * @param mixed $timeSheetId
     */
    public function setTimeSheetId($timeSheetId)
    {
        $this->timeSheetId = $timeSheetId;
    }

    /**
     * @return mixed
     */
    public function getTimeSheetStartDate()
    {
        return $this->timeSheetStartDate;
    }

    /**
     * @param mixed $timeSheetStartDate
     */
    public function setTimeSheetStartDate($timeSheetStartDate)
    {
        $this->timeSheetStartDate = $timeSheetStartDate;
    }

    /**
     * @return mixed
     */
    public function getTimeSheetEndDate()
    {
        return $this->timeSheetEndDate;
    }

    /**
     * @param mixed $timeSheetEndDate
     */
    public function setTimeSheetEndDate($timeSheetEndDate)
    {
        $this->timeSheetEndDate = $timeSheetEndDate;
    }



    /**
     * @return mixed
     */
    public function getTimeSheetItems()
    {
        return $this->timeSheetItems;
    }

    /**
     * @param mixed $timeSheetItems
     */
    public function setTimeSheetItems($timeSheetItems)
    {
        $this->timeSheetItems = $timeSheetItems;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }




    public function buildTimeSheet(\Timesheet $sheet)
    {
        $this->setTimeSheetId($sheet->getTimesheetId());
        $this->setEmployeeId($sheet->getEmployeeId());
        $this->setTimeSheetStartDate($sheet->getStartDate());
        $this->setTimeSheetEndDate($sheet->getEndDate());
        $this->setStatus($sheet->getState());
    }

    /**
     * TimeSheet to array method
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'timeSheetId' => $this->getTimeSheetId(),
            'employeeId'   => $this->getEmployeeId(),
            'startDate' => $this->getTimeSheetStartDate(),
            'endDate'   => $this->getTimeSheetEndDate(),
            'state' => $this->getStatus(),
            'timeSheetItems'   => $this->getTimeSheetItems()

        );
    }


}
