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

class EmployeeDirectoryService extends BaseService {

    private $directoryDao;
    
    /**
     * Get Employee Dao
     * @return EmployeeDao
     * @ignore
     */
    public function getEmployeeDirectoryDao() {
        return $this->directoryDao;
    }

    public function setEmployeeDirectoryDao(EmployeeDirectoryDao $directoryDao) {

        $this->directoryDao = $directoryDao;
    }
    
     /**
     * Get Search Employee Count
     *
     * @param array $filters
     * 
     * @return int Number of employees matched to the filter criteria mentioned in $filters
     * @todo Use a parameter object instead of $filters
     */
    public function getSearchEmployeeCount(array $filters = null) {
        return $this->getEmployeeDirectoryDao()->getSearchEmployeeCount($filters);
    }
    
    /**
     * Get employee list after sorting and filtering using given parameters.
     *
     * @param EmployeeSearchParameterHolder $parameterHolder Object containing search parameters
     *
     * @return Employee array of Employee entities match with filters
     * 
     * @todo Rename to searchEmployees(ParameterHolder $parameterObject) [DONE]
     * @todo Use an instance of a parameter holder instead of set of parameters [DONE]
     */
    public function searchEmployees(EmployeeSearchParameterHolder $parameterHolder) {
        return $this->getEmployeeDirectoryDao()->searchEmployees($parameterHolder);
    }

}