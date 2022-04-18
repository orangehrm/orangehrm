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

class TimeSheetItem implements Serializable
{

    /**
     * @var
     */
    private $timeSheetItemId;
    private $projectName;
    private $activityName;
    private $projectId;
    private $activityId;
    private $status;
    private $date;
    private $duration;
    private $comment;

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
    public function getTimeSheetItemId()
    {
        return $this->timeSheetItemId;
    }

    /**
     * @param mixed $timeSheetItemId
     */
    public function setTimeSheetItemId($timeSheetItemId)
    {
        $this->timeSheetItemId = $timeSheetItemId;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param mixed $duration
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }


    public function buildTimeSheetItem(\TimesheetItem $timeSheetItem)
    {
        $this->setTimeSheetItemId($timeSheetItem->getTimesheetItemId());
        $this->setProjectName($timeSheetItem->getProject()->getName());
        $this->setProjectId($timeSheetItem->getProject()->getProjectId());
        $this->setActivityName($timeSheetItem->getProjectActivity()->getName());
        $this->setActivityId($timeSheetItem->getProjectActivity()->getActivityId());
        $this->setDate($timeSheetItem->getData()['date']);
        $this->setDuration($timeSheetItem->getData()['duration']);
        $this->setComment($timeSheetItem->getData()['comment']);
    }

    /**
     * TimeSheet to array method
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'timesheetItemId' => $this->getTimeSheetItemId(),
            'projectName' => $this->getProjectName(),
            'projectId' => $this->getProjectId(),
            'activityName' => $this->getActivityName(),
            'activityId' => $this->getActivityId(),
            'date'       => $this->getDate(),
            'duration'   => $this->getDuration(),
            'comment'    => $this->getComment()
        );
    }

}
