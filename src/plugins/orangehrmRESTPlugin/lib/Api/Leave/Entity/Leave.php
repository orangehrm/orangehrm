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

namespace Orangehrm\Rest\Api\Leave\Entity;

use Orangehrm\Rest\Api\Entity\Serializable;

class Leave implements Serializable
{

    /**
     * @var
     */

    private $status;
    private $comments;
    private $date;
    private $duration;
    private $durationString;

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $fromDate
     */
    public function setDate($date)
    {
        $this->date = $date;
    }





    /**
     * @return mixed
     */
    public function getLeaveType()
    {
        return $this->leaveType;
    }

    /**
     * @param mixed $leaveType
     */
    public function setLeaveType($leaveType)
    {
        $this->leaveType = $leaveType;
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

    /**
     * @return mixed
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param mixed $comments
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
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
    public function getDurationString()
    {
        return $this->durationString;
    }

    /**
     * @param mixed $durationString
     */
    public function setDurationString($durationString)
    {
        $this->durationString = $durationString;
    }


    public function toArray()
    {
        return array(

            'date' => $this->getDate(),
            'status' => $this->getStatus(),
            'duration' => $this->getDuration(),
            'durationString' => $this->getDurationString(),
            'comments' => $this->getComments()

        );
    }

    /**
     * Build leave
     *
     * @param \Leave $leave
     */
    public function buildLeave(\Leave $leave)
    {
        $this->setStatus($leave->getTextLeaveStatus());
        $this->setDate($leave->getDate());
        $this->setDuration($leave->getLengthHours());
        $this->setDurationString($leave->getLeaveDurationAsAString());

        $commentsList = [];

        if (!empty($leave->getLeaveComment())) {
            foreach ($leave->getLeaveComment() as $comment) {

                $datetime = explode(" ", $comment->getCreated());
                $leaveComment = new LeaveRequestComment($comment->getCreatedByName(), $datetime[0], $datetime[1], $comment->getComments());
                $commentsList[] = $leaveComment->toArray();
            }
        }
        $this->setComments($commentsList);
    }

    protected function getStatusText($statusId)
    {
        $statusList = \Leave::getStatusTextList();
        return $statusList[$statusId];
    }
}
