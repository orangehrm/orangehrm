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
 * Description of UserRoleManagerService
 *
 */
class UserRoleManagerService {
    
    const KEY_USER_ROLE_MANAGER_CLASS = "authorize_user_role_manager_class";
    
    protected $configDao;    
    protected $authenticationService;
    protected $systemUserService;
    
    public function getConfigDao() {
        
        if (empty($this->configDao)) {
            $this->configDao = new ConfigDao();
        }
        return $this->configDao;
    }

    public function setConfigDao($configDao) {
        $this->configDao = $configDao;
    }

    public function getAuthenticationService() {
        if (empty($this->authenticationService)) {
            $this->authenticationService = new AuthenticationService();
        }        
        return $this->authenticationService;
    }

    public function setAuthenticationService($authenticationService) {
        $this->authenticationService = $authenticationService;
    }

    public function getSystemUserService() {
        if (empty($this->systemUserService)) {
            $this->systemUserService = new SystemUserService();
        }          
        return $this->systemUserService;
    }

    public function setSystemUserService($systemUserService) {
        $this->systemUserService = $systemUserService;
    }

    
    public function getUserRoleManagerClassName() {
        return $this->getConfigDao()->getValue(self::KEY_USER_ROLE_MANAGER_CLASS);
    }
    
    public function getUserRoleManager() {
        
        $logger = Logger::getLogger('core.UserRoleManagerService');
        
        $class = $this->getUserRoleManagerClassName();
        
        $manager = null;
        
        if (class_exists($class)) {
            try {
                $manager = new $class;
            } catch (Exception $e) {
                throw new ServiceException('Exception when initializing user role manager:' . $e->getMessage());
            }
        } else {
            throw new ServiceException('User Role Manager class ' . $class . ' not found.');
        }
        
        if (!$manager instanceof AbstractUserRoleManager) {
            throw new ServiceException('User Role Manager class ' . $class . ' is not a subclass of AbstractUserRoleManager');
        }
        
        // Set System User object in manager
        $userId = $this->getAuthenticationService()->getLoggedInUserId();
        $systemUser = $this->getSystemUserService()->getSystemUser($userId);  
        
        if ($systemUser instanceof SystemUser) {
            $manager->setUser($systemUser);
        } else {
            if ($logger->isInfoEnabled() ) {
                $logger->info('No logged in system user when creating UserRoleManager');
            }            
        }
        
        return $manager;
    }
}

