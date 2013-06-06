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
 * Description of ohrmAuthorizationFilter
 *
 */
class ohrmAuthorizationFilter extends sfFilter {

    /**
     * Executes the authorization filter.
     *
     * @param sfFilterChain $filterChain A sfFilterChain instance
     */
    public function execute($filterChain) {
        
        $moduleName = $this->context->getModuleName();
        $actionName = $this->context->getActionName();
        
        // disable security on login and secure actions
        if ((sfConfig::get('sf_login_module') == $moduleName) 
                    && (sfConfig::get('sf_login_action') == $actionName)
                || (sfConfig::get('sf_secure_module') == $moduleName) 
                    && (sfConfig::get('sf_secure_action') == $actionName) 
                || ('auth' == $moduleName && 
                            (($actionName == 'retryLogin') || 
                             ($actionName == 'validateCredentials') || 
                             ($actionName == 'logout')))) {
            $filterChain->execute();

            return;
        }        
        

        $logger = Logger::getLogger('filter.ohrmAuthorizationFilter');

        try {
            $userRoleManager = UserRoleManagerFactory::getUserRoleManager();
            $this->context->setUserRoleManager($userRoleManager);

        } catch (Exception $e) {
            $logger->error('Exception: ' . $e);
            $this->forwardToSecureAction();
        }

        // disable security on non-secure actions
        try {
            $secure = $this->context->getController()->getActionStack()->getLastEntry()->getActionInstance() ->getSecurityValue('is_secure');

            if (!$secure || ($secure === "false") || ($secure === "off")) {

                $filterChain->execute();
                return;            
            }
        } catch (Exception $e) {
            $logger->error('Error getting is_secure value for action: ' . $e);            
            $this->forwardToSecureAction();              
        }    

        try {
            $permissions = $userRoleManager->getScreenPermissions($moduleName, $actionName);
        } catch (Exception $e) {                    
            $logger->error('Exception: ' . $e);            
            $this->forwardToSecureAction();                     
        }

        // user does not have read permissions
        if (!$permissions->canRead()) {

            $logger->warn('User does not have access read access to ' . $moduleName . ' - ' . $actionName);
                        
            // the user doesn't have access
            $this->forwardToSecureAction();
        } else {
            // set permissions in context
            $this->context->set('screen_permissions', $permissions);
        }

        // the user has access, continue
        $filterChain->execute();
    }

    /**
     * Forwards the current request to the secure action.
     *
     * @throws sfStopException
     */
    protected function forwardToSecureAction() {
        $this->context->getController()->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));

        throw new sfStopException();
    }

    /**
     * Forwards the current request to the login action.
     *
     * @throws sfStopException
     */
    protected function forwardToLoginAction() {
        $this->context->getController()->forward(sfConfig::get('sf_login_module'), sfConfig::get('sf_login_action'));

        throw new sfStopException();
    }

}

