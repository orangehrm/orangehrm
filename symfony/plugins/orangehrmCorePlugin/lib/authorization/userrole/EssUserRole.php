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

    protected $employeeNumber;

    public function getEmployeeNumber() {
        if (empty($this->employeeNumber)) {
            $this->employeeNumber = sfContext::getInstance()->getUser()->getEmployeeNumber();
        }
        return $this->employeeNumber;
    }

    public function setEmployeeNumber($employeeNumber) {
        $this->employeeNumber = $employeeNumber;
    }

    public function getAccessibleEmployeeIds($operation = null, $returnType = null, $requiredPermissions = array()) {
        return array();
    }

    public function getAccessibleEmployeePropertyList($properties, $orderField, $orderBy, $requiredPermissions = array()) {
        return array();
    }

    public function getAccessibleEmployees($operation = null, $returnType = null, $requiredPermissions = array()) {
        return array();
    }

    public function getAccessibleLocationIds($operation, $returnType) {

        return array();
    }

    public function getAccessibleOperationalCountryIds($operation, $returnType) {

        return array();
    }

    public function getAccessibleSystemUserIds($operation, $returnType) {

        return array();
    }

    public function getAccessibleUserRoleIds($operation, $returnType) {

        return array();
    }

}