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
 * Description of DashboardService
 */
class DashboardService {

    const NO_REC_MESSAGE = 'No Records are Available';
    const NO_DATA_MESSAGE = 'No Data to Display';
    const LINK_FILE = 'links.yml';
    const ADMIN_LINK_FILE = 'links_admin.yml';
    
    private $moduleService;
    
    public function getModuleService() {
        if(is_null($this->moduleService)){
            $this->moduleService = new ModuleService();
        }
        return $this->moduleService;
    }
        
    public function getQuickLaunchLinksForUser($userDetails) {
        if ((($userDetails['userType'] == 'Admin') || (($userDetails['userType'] == 'Supervisor'))) && ($userDetails['loggedUserEmpId'] != 0)) {
            $links = $this->getLinks(array(self::ADMIN_LINK_FILE, self::LINK_FILE));
        } elseif ($userDetails['loggedUserEmpId'] != 0) {
            $links = $this->getLinks(array(self::LINK_FILE));
        } else {
            $links = $this->getLinks(array(self::ADMIN_LINK_FILE));
        }
        foreach ($links as $index => $link) {
            if (!$this->isModuleEnable($link['ohrm_module'])) {
                unset($links[$index]);
            }
            if (isset($link['requiredPermissions'])) {
                $requiredPermissions = $link['requiredPermissions'];
                $available = true;
                foreach ($requiredPermissions as $permissionType => $permissions) {
                    if ($permissionType == BasicUserRoleManager::PERMISSION_TYPE_DATA_GROUP) {
                        $available = $this->areDataGroupPermissionsAvailable($permissions);
                    }
                    if ($permissionType == BasicUserRoleManager::PERMISSION_TYPE_WORKFLOW_ACTION) {
                        $available = $this->areWorkflowPermissionAvailable($permissions);
                    }
                    if (!$available) {
                        break;
                    }
                }
                if (!$available) {
                    unset($links[$index]);
                }
            }
        }
        return array_values($links);
    }

    public function getLinks(array $files) {
        $links = array();
        $configPath = sfConfig::get('sf_plugins_dir') . DIRECTORY_SEPARATOR . 'orangehrmDashboardPlugin' . DIRECTORY_SEPARATOR . 'config';
        foreach ($files as $file) {
            $linksFile = $configPath . DIRECTORY_SEPARATOR . $file;
            $link = sfYaml::load($linksFile);
            if (is_array($link)) {
                $links = $this->mergeLinks($link, $links);
            }
        }
        return array_values($links);
    }

    public function areDataGroupPermissionsAvailable($permissions) {
        $permitted = true;
        foreach ($permissions as $dataGroupName => $requestedResourcePermission) {
            $requestedResourcePermission = ResourcePermission::fromArray($requestedResourcePermission);
            $dataGroupPermissions = UserRoleManagerFactory::getUserRoleManager()->getDataGroupPermissions($dataGroupName);
            if ($permitted && $requestedResourcePermission->canRead()) {
                $permitted = $permitted && $dataGroupPermissions->canRead();
            }
            if ($permitted && $requestedResourcePermission->canCreate()) {
                $permitted = $dataGroupPermissions->canCreate();
            }
            if ($permitted && $requestedResourcePermission->canUpdate()) {
                $permitted = $dataGroupPermissions->canUpdate();
            }
            if ($permitted && $requestedResourcePermission->canDelete()) {
                $permitted = $dataGroupPermissions->canDelete();
            }
            if (!$permitted) {
                break;
            }
        }
        return $permitted;
    }

    public function areWorkflowPermissionAvailable($permissions) {
        $permitted = true;
        foreach ($permissions as $workflowId => $workflow) {
            $state = $workflow['state'];
            $action = $workflow['action'];
            $permitted = UserRoleManagerFactory::getUserRoleManager()->isActionAllowed($workflowId, $state, $action);
            if (!$permitted) {
                break;
            }
        }
        return $permitted;
    }

    public function mergeLinks($link, $links) {
        foreach ($link as $single) {
            $links[] = $single;
        }
        return $links;
    }

    public function isModuleEnable($module) {
        $disabledModuleList = $this->getDisabledModuleArray();
        if (in_array($module, $disabledModuleList)) {
            return false;
        }
        return true;
    }

    public function getDisabledModuleArray() {
        $disabledModuleArray = array();
        $disabledModuleList = $this->getModuleService()->getDisabledModuleList();
        foreach($disabledModuleList as $disabledModule){
            $disabledModuleArray[] = $disabledModule->getName();
        }
        return $disabledModuleArray;
    }

}
