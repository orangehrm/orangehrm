<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EssUserRole
 *
 * @author samith
 */
class EssUserRole extends AbstractUserRole {

    public function getAccessibleEmployeeIds($operation = null, $returnType = null, $requiredPermissions = array()) {
        return array();
    }

    public function getAccessibleEmployeePropertyList($properties, $orderField, $orderBy, $requiredPermissions = array()) {
        return array();
    }

    public function getAccessibleEmployees($operation = null, $returnType = null, $requiredPermissions = array()) {
        return array();
    }

    public function getAccessibleLocationIds($operation = null, $returnType = null, $requiredPermissions = array()) {

        return array();
    }

    public function getAccessibleOperationalCountryIds($operation = null, $returnType = null, $requiredPermissions = array()) {

        return array();
    }

    public function getAccessibleSystemUserIds($operation = null, $returnType = null, $requiredPermissions = array()) {

        return array();
    }

    public function getAccessibleUserRoleIds($operation = null, $returnType = null, $requiredPermissions = array()) {

        return array();
    }

}