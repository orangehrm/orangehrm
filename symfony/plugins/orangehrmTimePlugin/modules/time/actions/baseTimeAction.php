<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of baseTimeAction
 *
 * @author nirmal
 */
abstract class baseTimeAction extends sfAction {

    public function getDataGroupPermissions($dataGroups, $empNumber = null) {
        $loggedInEmpNum = $this->getUser()->getEmployeeNumber();

        $entities = array();
        $self = false;
        if (isset($empNumber)) {
            $entities = array('Employee' => $empNumber);
            if ($empNumber == $loggedInEmpNum) {
                $self = true;
            }
        }

        return $this->getContext()->getUserRoleManager()->getDataGroupPermissions($dataGroups, array(), array(), $self, $entities);
    }

}

?>
