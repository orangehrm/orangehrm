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
 * Description of DataGroupService
 *
 */
class DataGroupService {
    
    public $dao;
    
    /**
     * Get the Data group dao
     * @return DataGroupDao dao instance
     */
    public function getDao() {
        if (empty($this->dao)) {
            $this->dao = new DataGroupDao();
        }
        return $this->dao;
    }

    /**
     * Set the data group dao
     * @param DataGroupDao $dao
     */
    public function setDao(DataGroupDao $dao) {
        $this->dao = $dao;
    }
    
    /**
     * Get Data Group permissions 
     * 
     * @param mixed $dataGroup A single data group name (string), an array of data group names or null (to return all data group permissions)
     * @param int $userRoleId User role id
     * @param bool $selfPermission If true, self permissions are returned. If false non-self permissions are returned
     * 
     * @return Doctrine_Collection Collection of DataGroupPermission objects
     */
    public function getDataGroupPermission($dataGroup, $userRoleId , $selfPermission = false){
        return $this->getDao()->getDataGroupPermission($dataGroup, $userRoleId, $selfPermission );
    }
    
    /**
     * Get All defined data groups in the system
     * 
     * @return Doctrine_Collection Colelction of DataGroup objects
     */
    public function getDataGroups(){
        return $this->getDao()->getDataGroups();
    }
    
    /**
     * Get Data Group with given name
     * 
     * @param string $name Data Group name
     * @return DataGroup DataGroup or false if no match.
     */
    public function getDataGroup($name) {
        return $this->getDao()->getDataGroup($name);       
    }    


}

