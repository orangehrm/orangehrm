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

/**
 * TerminationReason Service
 * 
 * Rename to TerminationConfigurationReasonService
 * 
  * @package pim
 */

class TerminationReasonConfigurationService extends BaseService {
    
    /**
     * @ignore
     * @var TerminationReasonConfigurationDao 
     */
    private $terminationReasonConfigurationDao;
    
    /**
     * @ignore
     */
    public function getTerminationReasonDao() {
        
        if (!($this->terminationReasonConfigurationDao instanceof TerminationReasonConfigurationDao)) {
            $this->terminationReasonConfigurationDao = new TerminationReasonConfigurationDao();
        }
        
        return $this->terminationReasonConfigurationDao;
    }

    /**
     * @ignore
     */
    public function setTerminationReasonDao($terminationReasonConfigurationDao) {
        $this->terminationReasonConfigurationDao = $terminationReasonConfigurationDao;
    }
    
    /**
     * Saves a termination reason
     * 
     * Can be used for a new record or updating.
     * 
     * @version 2.6.12 
     * @param TerminationReason $terminationReason 
     * @return NULL Doesn't return a value
     * 
     * @todo return saved entity [DONE]
     */
    public function saveTerminationReason(TerminationReason $terminationReason) {        
        return $this->getTerminationReasonDao()->saveTerminationReason($terminationReason);        
    }
    
    /**
     * Retrieves a termination reason by ID
     * 
     * @version 2.6.12 
     * @param int $id 
     * @return TerminationReason An instance of TerminationReason or NULL
     * 
     * @todo rename method as getTerminationReason( $id ) [DONE]
     */    
    public function getTerminationReason($id) {
        return $this->getTerminationReasonDao()->getTerminationReason($id);
    }
    
    /**
     * Retrieves a termination reason by name
     * 
     * Case insensitive
     * 
     * @version 2.6.12 
     * @param string $name 
     * @return TerminationReason An instance of TerminationReason or false
     */    
    public function getTerminationReasonByName($name) {
        return $this->getTerminationReasonDao()->getTerminationReasonByName($name);
    }      
  
    /**
     * Retrieves all termination reasons ordered by name
     * 
     * @version 2.6.12 
     * @return Doctrine_Collection A doctrine collection of TerminationReason objects 
     */        
    public function getTerminationReasonList() {
        return $this->getTerminationReasonDao()->getTerminationReasonList();
    }
    
    /**
     * Deletes termination reasons
     * 
     * @version 2.6.12 
     * 
     * @param array $ids An array of IDs to be deleted
     * @return int Number of records deleted
     */    
    public function deleteTerminationReasons($ids) {
        return $this->getTerminationReasonDao()->deleteTerminationReasons($ids);
    }

    /**
     * Checks whether the given termination reason name exists
     *
     * Case insensitive
     *
     * @version 2.6.12
     * @param string $terminationReasonName Termination reason name that needs to be checked
     * @return boolean
     * 
     */
    public function isExistingTerminationReasonName($terminationReasonName) {
        return $this->getTerminationReasonDao()->isExistingTerminationReasonName($terminationReasonName);
    }
    
    /**
     * Checks whether the given IDs have been assigned to any employee
     * 
     * @ignore
     * 
     * @param array $idArray Reason IDs
     * @return boolean 
     * 
     * @todo rename method as isTerminationReasonsInUse 
     */
    public function isReasonInUse($idArray) {
        return $this->getTerminationReasonDao()->isReasonInUse($idArray);
    }
    
}