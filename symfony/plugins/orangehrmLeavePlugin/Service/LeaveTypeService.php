<?php

/*
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

class LeaveTypeService extends BaseService {

    private $leaveTypeDao;

    public function getLeaveTypeDao() {
        if (!($this->leaveTypeDao instanceof LeaveTypeDao)) {
            $this->leaveTypeDao = new LeaveTypeDao();
        }
        return $this->leaveTypeDao;
    }

    public function setLeaveTypeDao(LeaveTypeDao $leaveTypeDao) {
        $this->leaveTypeDao = $leaveTypeDao;
    }

    /**
     *
     * @param LeaveType $leaveType
     * @return boolean
     */
    public function saveLeaveType(LeaveType $leaveType) {

        $this->getLeaveTypeDao()->saveLeaveType($leaveType);

        return true;
    }

    /**
     * Delete Leave Type
     * @param array $leaveTypeList
     * @returns boolean
     * @throws LeaveServiceException
     */
    public function deleteLeaveType($leaveTypeList) {

        return $this->getLeaveTypeDao()->deleteLeaveType($leaveTypeList);
    }

    /**
     *
     * @return LeaveType Collection
     */
    public function getLeaveTypeList($operationalCountryId = null) {

        return $this->getLeaveTypeDao()->getLeaveTypeList($operationalCountryId);
    }

    /**
     *
     * @return LeaveType
     */
    public function readLeaveType($leaveTypeId) {

        return $this->getLeaveTypeDao()->readLeaveType($leaveTypeId);
    }

    public function readLeaveTypeByName($leaveTypeName) {

        return $this->getLeaveTypeDao()->readLeaveTypeByName($leaveTypeName);
    }

    public function undeleteLeaveType($leaveTypeId) {

        return $this->getLeaveTypeDao()->undeleteLeaveType($leaveTypeId);
    }

    public function getDeletedLeaveTypeList($operationalCountryId = null) {

        return $this->getLeaveTypeDao()->getDeletedLeaveTypeList($operationalCountryId);
    }
    
    /**
     *
     * @return array
     */
    public function getActiveLeaveTypeNamesArray($operationalCountryId = null) {

        $activeLeaveTypes = $this->getLeaveTypeList($operationalCountryId);

        $activeTypeNamesArray = array();

        foreach ($activeLeaveTypes as $activeLeaveType) {
            $activeTypeNamesArray[] = $activeLeaveType->getName();
        }

        return $activeTypeNamesArray;
    }
    
    public function getDeletedLeaveTypeNamesArray($operationalCountryId = null) {

        $deletedLeaveTypes = $this->getDeletedLeaveTypeList($operationalCountryId);

        $deletedTypeNamesArray = array();

        foreach ($deletedLeaveTypes as $deletedLeaveType) {

            $deletedLeaveTypeObject = new stdClass();
            $deletedLeaveTypeObject->id = $deletedLeaveType->getId();
            $deletedLeaveTypeObject->name = $deletedLeaveType->getName();
            $deletedTypeNamesArray[] = $deletedLeaveTypeObject;
        }

        return $deletedTypeNamesArray;
    }

}