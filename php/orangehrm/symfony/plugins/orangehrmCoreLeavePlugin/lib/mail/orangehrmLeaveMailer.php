<?php
/**
 *
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
 *
*/

abstract class orangehrmLeaveMailer extends orangehrmMailer {

    protected $performer; // Type of Employee
    protected $performerType; // 'admin', 'supervisor' or 'ess'
    protected $recipient; // Type of Employee
    protected $leaveRequest; // Type of LeaveRequest
    protected $leaveList; // Type of Leave
    protected $requestType; // Either 'request' or 'single'
    protected $employeeService; // Type of EmployeeService

    public function getPerformer() {
        return $this->performer;
    }

    public function setPerformer($performer) {
        $this->performer = $performer;
    }

    public function getPerformerType() {
        return $this->performerType;
    }

    public function setPerformerType($performerType) {
        $this->performerType = $performerType;
    }

    public function getRecipient() {
        return $this->recipient;
    }

    public function setRecipient($recipient) {
        $this->recipient = $recipient;
    }

    public function getLeaveRequest() {
        return $this->leaveRequest;
    }

    public function setLeaveRequest($leaveRequest) {
        $this->leaveRequest = $leaveRequest;
    }

    public function getLeaveList() {
        return $this->leaveList;
    }

    public function setLeaveList($leaveList) {
        $this->leaveList = $leaveList;
    }

    public function getRequestType() {
        return $this->requestType;
    }

    public function setRequestType($requestType) {
        $this->requestType = $requestType;
    }

    public function getEmployeeService() {
        return $this->employeeService;
    }

    public function setEmployeeService($employeeService) {
        $this->employeeService = $employeeService;
    }

}
