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
 * Description of NewLeaveTypeDao
 */
class LeaveTypeDao {
    
    public function getLeaveTypeList($operationalCountryId = null) {
        try {
            $q = Doctrine_Query::create()
                            ->from('LeaveType lt')
                            ->where('lt.deleted = 0')
                            ->orderBy('lt.name');
            
            if (!is_null($operationalCountryId)) {
                if (is_array($operationalCountryId)) {
                    $q->andWhereIn('lt.operational_country_id', $operationalCountryId);
                } else {
                    $q->andWhere('lt.operational_country_id = ? ', $operationalCountryId);
                }
            }
            $leaveTypeList = $q->execute();

            return $leaveTypeList;
        } catch (Exception $e) {
            $this->getLogger()->error("Exception in getLeaveTypeList:" . $e);
            throw new DaoException($e->getMessage(), 0, $e);
        }        
    }    
    
    /**
     * Get Leave Type by ID
     * @return LeaveType
     */
    public function readLeaveType($id) {
        try {
            return Doctrine::getTable('LeaveType')->find($id);
        } catch (Exception $e) {
            $this->getLogger()->error("Exception in readLeaveType:" . $e);
            throw new DaoException($e->getMessage(), 0, $e);
        }
    }    
    
    /**
     * Get Logger instance. Creates if not already created.
     *
     * @return Logger
     */
    protected function getLogger() {
        if (is_null($this->logger)) {
            $this->logger = Logger::getLogger('leave.LeaveTypeDao');
        }

        return($this->logger);
    }    
    
    /**
     *
     * @param LeaveType $leaveType
     * @return boolean
     */
    public function saveLeaveType(LeaveType $leaveType) {
        try {
            $leaveType->save();

            return true;
        } catch (Exception $e) {
            $this->getLogger()->error("Exception in saveLeaveType:" . $e);
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Delete Leave Type
     * @param array leaveTypeList
     * @returns boolean
     * @throws DaoException
     */
    public function deleteLeaveType($leaveTypeList) {

        try {

            $q = Doctrine_Query::create()
                            ->update('LeaveType lt')
                            ->set('lt.deleted', '?', 1)
                            ->whereIn('lt.id', $leaveTypeList);
            $numDeleted = $q->execute();
            if ($numDeleted > 0) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            $this->getLogger()->error("Exception in deleteLeaveType:" . $e);
            throw new DaoException($e->getMessage());
        }
    }



    public function getDeletedLeaveTypeList($operationalCountryId = null) {
        try {
            $q = Doctrine_Query::create()
                            ->from('LeaveType lt')
                            ->where('lt.deleted = 1')
                            ->orderBy('lt.id');

            if (!is_null($operationalCountryId)) {
                $q->andWhere('lt.operational_country_id = ? ', $operationalCountryId);
            }
            
            $leaveTypeList = $q->execute();

            return $leaveTypeList;
        } catch (Exception $e) {
            $this->getLogger()->error("Exception in getDeletedLeaveTypeList:" . $e);
            throw new DaoException($e->getMessage());
        }
    }

    public function readLeaveTypeByName($leaveTypeName) {
        try {
            $q = Doctrine_Query::create()
                            ->from('LeaveType lt')
                            ->where("lt.name = ?", $leaveTypeName)
                            ->andWhere('lt.deleted = 0');

            $leaveTypeCollection = $q->execute();

            return $leaveTypeCollection[0];
        } catch (Exception $e) {
            $this->getLogger()->error("Exception in readLeaveTypeByName:" . $e);
            throw new DaoException($e->getMessage());
        }
    }

    public function undeleteLeaveType($leaveTypeId) {

        try {

            $q = Doctrine_Query::create()
                            ->update('LeaveType lt')
                            ->set('lt.deleted', 0)
                            ->where("lt.id = ?", $leaveTypeId);

            $numUpdated = $q->execute();

            if ($numUpdated > 0) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            $this->getLogger()->error("Exception in undeleteLeaveType:" . $e);
            throw new DaoException($e->getMessage());
        }
    }    
}
