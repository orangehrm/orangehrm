<?php
/*
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
 * Home Page Service
 */
class HomePageService {

    protected $userSession;
    protected $configService;
    protected $loginPath = 'auth/login';
    protected $validatePath = 'auth/validateCredentials';
    protected $homePageDao;
    protected $userRoleManager;
    
    public function getUserRoleManager() {
        if (!$this->userRoleManager instanceof AbstractUserRoleManager) {
            $this->userRoleManager = UserRoleManagerFactory::getUserRoleManager();
        }        
        return $this->userRoleManager;
    }

    public function setUserRoleManager($userRoleManager) {
        $this->userRoleManager = $userRoleManager;
    }  
    
    public function __construct(myUser $userSession) {
        $this->userSession = $userSession;
    }
    
    public function getHomePagePath() {
        return $this->getUserRoleManager()->getHomePage();
    }
    
    public function getTimeModuleDefaultPath() {
        return $this->getModuleDefaultPage('time'); 
    }
    
    public function getLeaveModuleDefaultPath() {
        return $this->getModuleDefaultPage('leave');  
    }
    
    public function getAdminModuleDefaultPath() {
        return $this->getModuleDefaultPage('admin');           
    }
    
    public function getPimModuleDefaultPath() {
        return $this->getModuleDefaultPage('pim');   
    }
    
    public function getRecruitmentModuleDefaultPath() {
        return $this->getModuleDefaultPage('recruitment'); 
    }
    
    public function getPerformanceModuleDefaultPath() {        
        return $this->getModuleDefaultPage('performance');        
    }
    
    public function getModuleDefaultPage($module) {
        return $this->getUserRoleManager()->getModuleDefaultPage($module);
    }
    
    public function getPathAfterLoggingIn(sfContext $context) {
        
        $logger = Logger::getLogger('core.homepageservice');
               
        $redirectToReferer = true;                   
        $request = $context->getRequest();
        
        $referer = $request->getReferer();
        $host = $request->getHost();           
        
        // get base url: ie something like: http://host:port/symfony/web/index.php        
        $baseUrl = $request->getUriPrefix() . $request->getPathInfoPrefix();
        
        if ($logger->isDebugEnabled()) {
            $logger->debug("referer: $referer, host: $host, base url: $baseUrl");
        }
        
        if (strpos($referer, $this->loginPath)) { // Check whether referer is login page            
            $redirectToReferer = false;
            if ($logger->isDebugEnabled()) {        
                $logger->debug("referrer is the login page. Skipping redirect:" . $this->loginPath);
            }
        } elseif (strpos($referer, $this->validatePath)) { // Check whether referer is validate action            
            $redirectToReferer = false;            
            
            if ($logger->isDebugEnabled()) {        
                $logger->debug("referrer is the validate action. Skipping redirect:" . $this->validatePath);
            }
        } else {
            
            if (false === strpos($referer, $baseUrl)) { // Check whether from same host                
                $redirectToReferer = false;                
                if ($logger->isDebugEnabled()) {        
                    $logger->debug("referrer does not have same base url. Skipping redirect");
                }
            }            
        }
        
        /* 
         * Try to get action and module, skip redirecting to referrer and show homepage if:
         * 1) Action is not secure (probably a login related url we should not redirect to)
         * 2) Action is not accessible to current user.
         */        
        if ($redirectToReferer) {            
            try {
                $moduleAndAction = str_replace($baseUrl, '', $referer);
                if ($logger->isDebugEnabled()) {        
                    $logger->debug('referrer module and action: ' . $moduleAndAction);
                }
                
                $params = $context->getRouting()->parse($moduleAndAction);
                if ($params && isset($params['module']) && isset($params['action'])) {

                    $moduleName = $params['module'];
                    $actionName = $params['action'];

                    if ($logger->isDebugEnabled()) {
                        $logger->debug("module: $moduleName, action: $actionName");
                    }
                    
                    if ($context->getController()->actionExists($moduleName, $actionName)) {
                        $action = $context->getController()->getAction($moduleName, $actionName);

                        if ($action instanceof sfAction) {
                            if ($action->isSecure()) {

                                $permissions = UserRoleManagerFactory::getUserRoleManager()->getScreenPermissions($moduleName, $actionName);
                                if ($permissions instanceof ResourcePermission) {
                                    if ($permissions->canRead()) {
                                        return $referer;
                                    }
                                } else {
                                    $logger->debug("action does not exist");
                                }
                            } else {
                                $logger->debug("action is not secure");
                            }
                        } else {
                            $logger->debug("action not an instance of sfAction");
                        }
                    } else {
                        $logger->debug("action does not exist");
                    }                
                } else {
                    $logger->debug("referrer does not match a route");
                }
            } catch (Exception $e) {
                $logger->warn('Error when trying to get referrer action: ' . $e);
            }
        }        
        
        return $this->getHomePagePath();
        
    }
    
}