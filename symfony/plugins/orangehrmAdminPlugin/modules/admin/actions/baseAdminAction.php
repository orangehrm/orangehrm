<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of baseAdminAction
 *
 * @author nirmal
 */
abstract class baseAdminAction extends ohrmBaseAction {

    public function getDataGroupPermissions($dataGroups) {
        return $this->getContext()->getUserRoleManager()->getDataGroupPermissions($dataGroups, array(), array(), false, array());
    }

}

?>
