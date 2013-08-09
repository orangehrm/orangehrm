<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Abstract user role decorator class
 */
abstract class AbstractUserRoleDecorator extends AbstractUserRole {

    private $decoratedUserRole = null;

    public function __construct($roleName, $userRoleManager, $decoratedUserRole) {
        parent::__construct($roleName, $userRoleManager);
        $this->decoratedUserRole = $decoratedUserRole;
    }

    /**
     * 
     * @param AbstractUserRole $decoratedUserRole 
     */
    public function setDecoratedUserRole(AbstractUserRole $decoratedUserRole) {
        $this->decoratedUserRole = $decoratedUserRole;
    }

    /**
     *
     * @return AbstractUserRole 
     */
    public function getDecoratedUserRole() {
        return $this->decoratedUserRole;
    }

    public function getAccessibleEntityIds($entityType, $operation = null, $returnType = null, $requiredPermissions = array()) {
        if (is_null($this->decoratedUserRole)) {
            return array();
        } else {
            return $this->getDecoratedUserRole()->getAccessibleEntityIds($entityType, $operation, $returnType, $requiredPermissions);
        }
    }

    public function getAccessibleEntities($entityType, $operation = null, $returnType = null, $requiredPermissions = array()) {
        if (is_null($this->decoratedUserRole)) {
            return array();
        } else {
            return $this->getDecoratedUserRole()->getAccessibleEntities($entityType, $operation, $returnType, $requiredPermissions);
        }
    }

    public function getAccessibleEmployeeIds($operation = null, $returnType = null, $requiredPermissions = array()) {
        if (is_null($this->decoratedUserRole)) {
            return array();
        } else {
            return $this->getDecoratedUserRole()->getAccessibleEmployeeIds($operation, $returnType, $requiredPermissions);
        }
    }

    public function getAccessibleEmployeePropertyList($properties, $orderField, $orderBy, $requiredPermissions = array()) {
        if (is_null($this->decoratedUserRole)) {
            return array();
        } else {
            return $this->getDecoratedUserRole()->getAccessibleEmployeePropertyList($properties, $orderField, $orderBy, $requiredPermissions);
        }
    }

    public function getAccessibleEmployees($operation = null, $returnType = null, $requiredPermissions = array()) {
        if (is_null($this->decoratedUserRole)) {
            return array();
        } else {
            return $this->getDecoratedUserRole()->getAccessibleEmployees($operation, $returnType, $requiredPermissions);
        }
    }

    public function getAccessibleLocationIds($operation = null, $returnType = null, $requiredPermissions = array()) {
        if (is_null($this->decoratedUserRole)) {
            return array();
        } else {
            return $this->getDecoratedUserRole()->getAccessibleLocationIds($operation, $returnType);
        }
    }

    public function getAccessibleOperationalCountryIds($operation = null, $returnType = null, $requiredPermissions = array()) {
        if (is_null($this->decoratedUserRole)) {
            return array();
        } else {
            return $this->getDecoratedUserRole()->getAccessibleOperationalCountryIds($operation, $returnType);
        }
    }

    public function getAccessibleSystemUserIds($operation = null, $returnType = null, $requiredPermissions = array()) {
        if (is_null($this->decoratedUserRole)) {
            return array();
        } else {
            return $this->getDecoratedUserRole()->getAccessibleSystemUserIds($operation, $returnType);
        }
    }

    public function getAccessibleUserRoleIds($operation = null, $returnType = null, $requiredPermissions = array()) {
        if (is_null($this->decoratedUserRole)) {
            return array();
        } else {
            return $this->getDecoratedUserRole()->getAccessibleUserRoleIds($operation, $returnType);
        }
    }

    protected function mergeEntities($list1, $list2) {

        foreach ($list2 as $id => $ent) {
            if (!isset($list1[$id])) {
                $list1[$id] = $ent;
            }
        }
        return $list1;
    }    
}

