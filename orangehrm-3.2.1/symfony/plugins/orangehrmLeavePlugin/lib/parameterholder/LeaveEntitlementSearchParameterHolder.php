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
 *
 */

/**
 * Parameter holder for leave entitlement search
 */
class LeaveEntitlementSearchParameterHolder extends SearchParameterHolder {
    protected $empNumber;    
    protected $leaveTypeId;    
    protected $fromDate;
    protected $toDate;
    protected $validDate ;
    protected $deletedFlag = false;
    protected $idList = array();
    protected $empIdList = array();
    protected $hydrationMode = null;
    protected $entitlementTypes = array();
    
    public function getHydrationMode() {
        return $this->hydrationMode;
    }

    public function setHydrationMode($hydrationMode) {
        $this->hydrationMode = $hydrationMode;
    }

        
    public function getEmpIdList() {
        return $this->empIdList;
    }

    public function setEmpIdList($empIdList) {
        $this->empIdList = $empIdList;
    }

    
    public function __construct() {
        $this->orderField = 'from_date';
    }

    public function getEmpNumber() {
        return $this->empNumber;
    }

    public function setEmpNumber($empNumber) {
        $this->empNumber = $empNumber;
    }

    public function getLeaveTypeId() {
        return $this->leaveTypeId;
    }

    public function setLeaveTypeId($leaveTypeId) {
        $this->leaveTypeId = $leaveTypeId;
    }

    public function getFromDate() {
        return $this->fromDate;
    }

    public function setFromDate($fromDate) {
        $this->fromDate = $fromDate;
    }

    public function getToDate() {
        return $this->toDate;
    }

    public function setToDate($toDate) {
        $this->toDate = $toDate;
    }    
    
    public function getDeletedFlag() {
        return $this->deletedFlag;
    }

    public function setDeletedFlag($deleted) {
        $this->deletedFlag = $deleted;
    }
    
    public function getIdList() {
        return $this->idList;
    }

    public function setIdList($idList) {
        $this->idList = $idList;
    }

    public function getValidDate() {
        return $this->validDate;
    }

    public function setValidDate($validDate) {
        $this->validDate = $validDate;
    }
    
    public function getEntitlementTypes() {
        return $this->entitlementTypes;
    }

    public function setEntitlementTypes(array $entitlementTypes) {
        $this->entitlementTypes = $entitlementTypes;
    }

    
}
