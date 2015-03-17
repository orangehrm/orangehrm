<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of authLeftMenuComponent
 *
 * @author damith
 */
class authLeftMenuComponent extends sfComponent {
    
    private $availableActions = array(
        'securityAuthenticationConfigure' => array(
            'module' => 'securityAuthentication',
            'data_groups' => array(),
            'label' => 'Default'),
        'configureLDAPAuthentication' => array(
            'module' => 'ldapAuthentication',
            'data_groups' => array(),
            'label' => "LDAP"),
        'openIdProvider' => array(
            'module' => 'admin',
            'data_groups' => array(),
            'label' => 'Social Media')
    );
    
    public function getMenuItems() {
        $menu = array();
        $userRoleManager = $this->getContext()->getUserRoleManager();
        foreach ($this->availableActions as $action => $properties) {
            $permissions = $userRoleManager->getScreenPermissions($properties['module'], $action);
            if ($permissions->canRead()) {
                $menu[$action] = $properties;
            }
        }
        return $menu;
    }

    public function execute($request) {
        $this->menuItems = $this->getMenuItems();
        $this->currentAction = $this->getContext()->getActionName();
    }

}
