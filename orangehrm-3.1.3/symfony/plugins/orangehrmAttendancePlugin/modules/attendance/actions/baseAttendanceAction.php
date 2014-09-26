<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of baseAttendanceAction
 *
 * @author nirmal
 */
abstract class baseAttendanceAction extends sfAction {

    public function getDataGroupPermissions($dataGroups, $self = false) {
        return $this->getContext()->getUserRoleManager()->getDataGroupPermissions($dataGroups, array(), array(), $self, array());
    }

}

?>
