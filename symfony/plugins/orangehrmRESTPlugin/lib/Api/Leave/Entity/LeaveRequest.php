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

class LeaveRequest implements Serializable
{

    /**
     * @var
     */
    private $appliedDate;
    private $employeeName;
    private $leaveType;
    private $leaveBalance;
    private $numberOfDays;
    private $status;
    private $comments;
    private $id;
    private $action;
    private $empId;
    private $fromDate;
    private $toDate;
    private $date;
    private $duration;
    private $days;

    /**
     * LeaveType constructor.
     *
     * @param $id
     * @param $type
     */
    public function __construct($id, $type)
    {
        $this->setId($id);
        $this->setLeaveType($type);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFromDate()
    {
        return $this->fromDate;
    }

    /**
     * @param mixed $fromDate
     */
    public function setFromDate($fromDate)
    {
        $this->fromDate = $fromDate;
    }

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
    public function getToDate()
    {
        return $this->toDate;
    }

    /**
     * @param mixed $toDate
     */
    public function setToDate($toDate)
    {
        $this->toDate = $toDate;
    }


    /**
     * @return mixed
     */
    public function getAppliedDate()
    {
        return $this->appliedDate;
    }

    /**
     * @param mixed $date
     */
    public function setAppliedDate($date)
    {
        $this->appliedDate = $date;
    }

    /**
     * @return mixed
     */
    public function getEmployeeName()
    {
        return $this->employeeName;
    }

    /**
     * @param mixed $employeeName
     */
    public function setEmployeeName($employeeName)
    {
        $this->employeeName = $employeeName;
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
    public function getLeaveBalance()
    {
        return $this->leaveBalance;
    }

    /**
     * @param mixed $leaveBalance
     */
    public function setLeaveBalance($leaveBalance)
    {
        $this->leaveBalance = $leaveBalance;
    }

    /**
     * @return mixed
     */
    public function getNumberOfDays()
    {
        return $this->numberOfDays;
    }

    /**
     * @param mixed $numberOfDays
     */
    public function setNumberOfDays($numberOfDays)
    {
        $this->numberOfDays = $numberOfDays;
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
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param mixed $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @return mixed
     */
    public function getEmpId()
    {
        return $this->empId;
    }

    /**
     * @param mixed $empId
     */
    public function setEmpId($empId)
    {
        $this->empId = $empId;
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
    public function getDays()
    {
        return $this->days;
    }

    /**
     * @param mixed $days
     */
    public function setDays($days)
    {
        $this->days = $days;
    }


    public function toArray()
    {
        return array(
            'employeeName' => $this->getEmployeeName(),
            'employeeId' => $this->getEmpId(),
            'id' => $this->getId(),
            'fromDate' => $this->getFromDate(),
            'toDate' => $this->getToDate(),
            'type'   => $this->getLeaveType(),
            'leaveBalance' => $this->getLeaveBalance(),
            'numberOfDays' => $this->getNumberOfDays(),
            'comments' => $this->getComments(),
            'days' => $this->getDays()


        );
    }

    /**
     * build
     *
     * @param \LeaveRequest $leaveRequest
     */
    public function buildLeaveRequest(\LeaveRequest $leaveRequest)
    {

        $this->setAppliedDate($leaveRequest->getDateApplied());
        $leaveDates = $leaveRequest->getLeaveStartAndEndDate();
        $this->setFromDate($leaveDates[0]);
        $this->setToDate($leaveDates[1]);
        $this->setLeaveBalance(number_format((float)$leaveRequest->getLeaveBalance(), 2, '.', ''));
        $this->setNumberOfDays($leaveRequest->getNumberOfDays());
        $this->setEmployeeName($leaveRequest->getEmployee()->getFullName());
        $this->setEmpId($leaveRequest->getEmpNumber());
        $this->setLeaveType($leaveRequest->getLeaveTypeName());

        $commentsList = '';

        if (!empty($leaveRequest->getLeaveRequestComment())) {
            foreach ($leaveRequest->getLeaveRequestComment() as $comment) {
                $datetime = explode(" ", $comment->getCreated());
                $leaveComment = new LeaveRequestComment($comment->getCreatedByName(), $datetime[0],
                    $datetime[1], $comment->getComments());
                $commentsList = $leaveComment->toArray();
            }
        }
        $this->setComments($commentsList);

        $days = null;

        foreach ($leaveRequest->getLeave() as $leave) {

            $leaveEntity = new Leave();
            $leaveEntity->buildLeave($leave);
            $days[] = $leaveEntity->toArray();

        }

        $this->setDays($days);
    }


    protected function getStatusText($statusId)
    {
        $statusList = \Leave::getStatusTextList();
        return $statusList[$statusId];
    }
}
