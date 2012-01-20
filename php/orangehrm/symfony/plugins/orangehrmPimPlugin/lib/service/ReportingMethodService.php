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
class ReportingMethodService extends BaseService {
    
    private $reportingMethodDao;
    
    /**
     * @ignore
     */
    public function getReportingMethodDao() {
        
        if (!($this->reportingMethodDao instanceof ReportingMethodDao)) {
            $this->reportingMethodDao = new ReportingMethodDao();
        }
        
        return $this->reportingMethodDao;
    }

    /**
     * @ignore
     */
    public function setReportingMethodDao($reportingMethodDao) {
        $this->reportingMethodDao = $reportingMethodDao;
    }
    
    /**
     * Saves a reportingMethod
     * 
     * Can be used for a new record or updating.
     * 
     * @version 2.6.12 
     * @param ReportingMethod $reportingMethod 
     * @return NULL Doesn't return a value
     */
    public function saveReportingMethod(ReportingMethod $reportingMethod) {        
        return $this->getReportingMethodDao()->saveReportingMethod($reportingMethod);        
    }
    
    /**
     * Retrieves a reportingMethod by ID
     * 
     * @version 2.6.12 
     * @param int $id 
     * @return ReportingMethod An instance of ReportingMethod or NULL
     */    
    public function getReportingMethodById($id) {
        return $this->getReportingMethodDao()->getReportingMethodById($id);
    }
    
    /**
     * Retrieves a reporting method by name
     * 
     * Case insensitive
     * 
     * @version 2.6.12 
     * @param string $name 
     * @return ReportingMethod An instance of ReportingMethod or false
     */    
    public function getReportingMethodByName($name) {
        return $this->getReportingMethodDao()->getReportingMethodByName($name);
    }     
  
    /**
     * Retrieves all reportingMethods ordered by name
     * 
     * @version 2.6.12 
     * @return Doctrine_Collection A doctrine collection of ReportingMethod objects 
     */        
    public function getReportingMethodList() {
        return $this->getReportingMethodDao()->getReportingMethodList();
    }
    
    /**
     * Deletes reportingMethods
     * 
     * @version 2.6.12 
     * @param array $toDeleteIds An array of IDs to be deleted
     * @return int Number of records deleted
     */    
    public function deleteReportingMethods($toDeleteIds) {
        return $this->getReportingMethodDao()->deleteReportingMethods($toDeleteIds);
    }

    /**
     * Checks whether the given reportingMethod name exists
     *
     * Case insensitive
     *
     * @version 2.6.12
     * @param string $reportingMethodName ReportingMethod name that needs to be checked
     * @return boolean
     */
    public function isExistingReportingMethodName($reportingMethodName) {
        return $this->getReportingMethodDao()->isExistingReportingMethodName($reportingMethodName);
    }
    
}