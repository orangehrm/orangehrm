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
class ModuleService extends BaseService {
    
    private $moduleDao;
    
    /**
     * @ignore
     */
    public function getModuleDao() {
        
        if (!($this->moduleDao instanceof ModuleDao)) {
            $this->moduleDao = new ModuleDao();
        }
        
        return $this->moduleDao;
    }

    /**
     * @ignore
     */
    public function setModuleDao($moduleDao) {
        $this->moduleDao = $moduleDao;
    }
    
    /**
     * Retrieves disabled module list
     * 
     * @version 2.6.12.2 
     * @return Doctrine_Collection A collection of Module objects
     */    
    public function getDisabledModuleList() {
        return $this->getModuleDao()->getDisabledModuleList();
    }
    
    /**
     * Changes the status of set of modules
     * 
     * @version 2.6.12.2 
     * @param array $moduleList Names of modules
     * @return int Number of records updated
     */
    public function updateModuleStatus($moduleList, $status) {
        return $this->getModuleDao()->updateModuleStatus($moduleList, $status);
    }
    
}