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
class EducationService extends BaseService {
    
    private $educationDao;
    
    /**
     * @ignore
     */
    public function getEducationDao() {
        
        if (!($this->educationDao instanceof EducationDao)) {
            $this->educationDao = new EducationDao();
        }
        
        return $this->educationDao;
    }

    /**
     * @ignore
     */
    public function setEducationDao($educationDao) {
        $this->educationDao = $educationDao;
    }
    
    /**
     * Saves an education object
     * 
     * Can be used for a new record or updating.
     * 
     * @version 2.6.12 
     * @param Education $education 
     * @return NULL Doesn't return a value
     */
    public function saveEducation(Education $education) {        
        $this->getEducationDao()->saveEducation($education);        
    }
    
    /**
     * Retrieves an education object by ID
     * 
     * @version 2.6.12 
     * @param int $id 
     * @return Education An instance of Education or NULL
     */    
    public function getEducationById($id) {
        return $this->getEducationDao()->getEducationById($id);
    }
    
    /**
     * Retrieves an education object by name
     * 
     * Case insensitive
     * 
     * @version 2.6.12 
     * @param string $name 
     * @return Education An instance of Education or false
     */    
    public function getEducationByName($name) {
        return $this->getEducationDao()->getEducationByName($name);
    }    
  
    /**
     * Retrieves all education records ordered by name
     * 
     * @version 2.6.12 
     * @return Doctrine_Collection A doctrine collection of Education objects 
     */        
    public function getEducationList() {
        return $this->getEducationDao()->getEducationList();
    }
    
    /**
     * Deletes education records
     * 
     * @version 2.6.12 
     * @param array $toDeleteIds An array of IDs to be deleted
     * @return int Number of records deleted
     */    
    public function deleteEducations($toDeleteIds) {
        return $this->getEducationDao()->deleteEducations($toDeleteIds);
    }

    /**
     * Checks whether the given education name exists
     *
     * Case insensitive
     *
     * @version 2.6.12
     * @param string $educationName Education name that needs to be checked
     * @return boolean
     */
    public function isExistingEducationName($educationName) {
        return $this->getEducationDao()->isExistingEducationName($educationName);
    }
    
}