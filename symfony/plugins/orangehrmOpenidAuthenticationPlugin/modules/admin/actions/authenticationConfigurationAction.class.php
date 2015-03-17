<?php

/*
 * OrangeHRM Enterprise is a closed sourced comprehensive Human Resource Management (HRM) 
 * System that captures all the essential functionalities required for any enterprise. 
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com 
 * 
 * OrangeHRM Inc is the owner of the patent, copyright, trade secrets, trademarks and any 
 * other intellectual property rights which subsist in the Licensed Materials. OrangeHRM Inc 
 * is the owner of the media / downloaded OrangeHRM Enterprise software files on which the 
 * Licensed Materials are received. Title to the Licensed Materials and media shall remain 
 * vested in OrangeHRM Inc. For the avoidance of doubt title and all intellectual property 
 * rights to any design, new software, new protocol, new interface, enhancement, update, 
 * derivative works, revised screen text or any other items that OrangeHRM Inc creates for 
 * Customer shall remain vested in OrangeHRM Inc. Any rights not expressly granted herein are 
 * reserved to OrangeHRM Inc. 
 * 
 * Please refer http://www.orangehrm.com/Files/OrangeHRM_Commercial_License.pdf for the license which includes terms and conditions on using this software. 
 *  
 */

/**
 * Description of authenticationConfigurationAction
 *
 */
class authenticationConfigurationAction extends sfAction {
    
    public function execute($request) {
        $authenticationTypes = array(
            array('securityAuthentication', 'securityAuthenticationConfigure'),
            array('ldapAuthentication', 'configureLDAPAuthentication'),
            array('admin', 'openIdProvider')
        );
        $userRoleManager = $this->getContext()->getUserRoleManager();
        foreach ($authenticationTypes as $authenticationType) {
            list($module, $action) = $authenticationType;
            $permissions = $userRoleManager->getScreenPermissions($module, $action);
            if ($permissions->canRead()) {
                $this->forward($module, $action);
            }
        }
    }

}
