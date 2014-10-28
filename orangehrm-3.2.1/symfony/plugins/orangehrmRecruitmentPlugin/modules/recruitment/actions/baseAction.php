<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of baseAction
 *
 * @author nirmal
 */
abstract class baseAction extends sfAction {

    /**
     * Get data Group Permissions
     * @param type $dataGroups
     * @return type
     */
    public function getDataGroupPermissions($dataGroups) {
        return $this->getContext()->getUserRoleManager()->getDataGroupPermissions($dataGroups, array(), array(), false, array());
    }

}

?>
