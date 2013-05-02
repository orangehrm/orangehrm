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
 * Description of ScreenPermissionService
 *
 */
class ScreenPermissionService {
    
    private $screenPermissionDao;    
    private $screenDao;
    
    public function getScreenDao() {
        if (empty($this->screenDao)) {
            $this->screenDao = new ScreenDao();
        }         
        return $this->screenDao;
    }

    public function setScreenDao($screenDao) {       
        $this->screenDao = $screenDao;
    }

    public function getScreenPermissionDao() {
        if (empty($this->screenPermissionDao)) {
            $this->screenPermissionDao = new ScreenPermissionDao();
        }
        return $this->screenPermissionDao;
    }

    public function setScreenPermissionDao($screenPermissionDao) {
        $this->screenPermissionDao = $screenPermissionDao;
    }

        
    
    /**
     * Get Screen Permissions for given module, action for the given roles
     * @param string $module Module Name
     * @param string $actionUrl Action Name
     * @param string $roles Array of Role names or Array of UserRole objects
     */
    public function getScreenPermissions($module, $actionUrl, $roles) {
        $screenPermissions = $this->getScreenPermissionDao()->getScreenPermissions($module, $actionUrl, $roles);
        
        $permission = null;

        // if empty, give all permissions
        if (count($screenPermissions) == 0) {
            
            // If screen not defined, give all permissions, if screen is defined, 
            // but don't give any permissions.
            $screen = $this->getScreenDao()->getScreen($module, $actionUrl);
            if ($screen === false) {
                $permission = new ResourcePermission(true, true, true, true);
            } else {
                $permission = new ResourcePermission(false, false, false, false);
            }
        } else {
            $read = false;
            $create = false;            
            $update = false;
            $delete = false;
            
            foreach ($screenPermissions as $screenPermission) {
                if ($screenPermission->can_read) {
                    $read = true;
                }
                if ($screenPermission->can_create) {
                    $create = true;
                }
                if ($screenPermission->can_update) {
                    $update = true;
                }
                if ($screenPermission->can_delete) {
                    $delete = true;
                }             
            }
            
            $permission = new ResourcePermission($read, $create, $update, $delete);
        }
        
        return $permission;
    }
    
    public function getScreen($module, $actionUrl) {
        return $this->getScreenDao()->getScreen($module, $actionUrl);
    }
}

