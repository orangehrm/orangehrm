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

namespace OrangeHRM\Core\Authorization\UserRole;

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

    public function getAccessibleEntityIds($entityType, $operation = null, $returnType = null, $requiredPermissions = []
    ) {
        if (is_null($this->decoratedUserRole)) {
            return [];
        } else {
            return $this->getDecoratedUserRole()->getAccessibleEntityIds($entityType, $operation, $returnType, $requiredPermissions);
        }
    }

    public function getAccessibleEntities($entityType, $operation = null, $returnType = null, $requiredPermissions = []) {
        if (is_null($this->decoratedUserRole)) {
            return [];
        } else {
            return $this->getDecoratedUserRole()->getAccessibleEntities($entityType, $operation, $returnType, $requiredPermissions);
        }
    }

    public function getAccessibleEmployeeIds($operation = null, $returnType = null, $requiredPermissions = []) {
        if (is_null($this->decoratedUserRole)) {
            return [];
        } else {
            return $this->getDecoratedUserRole()->getAccessibleEmployeeIds($operation, $returnType, $requiredPermissions);
        }
    }

    public function getAccessibleEmployeePropertyList($properties, $orderField, $orderBy, $requiredPermissions = []) {
        if (is_null($this->decoratedUserRole)) {
            return [];
        } else {
            return $this->getDecoratedUserRole()->getAccessibleEmployeePropertyList($properties, $orderField, $orderBy, $requiredPermissions);
        }
    }

    public function getAccessibleEmployees($operation = null, $returnType = null, $requiredPermissions = []) {
        if (is_null($this->decoratedUserRole)) {
            return [];
        } else {
            return $this->getDecoratedUserRole()->getAccessibleEmployees($operation, $returnType, $requiredPermissions);
        }
    }

    public function getAccessibleLocationIds($operation = null, $returnType = null, $requiredPermissions = []) {
        if (is_null($this->decoratedUserRole)) {
            return [];
        } else {
            return $this->getDecoratedUserRole()->getAccessibleLocationIds($operation, $returnType);
        }
    }

    public function getAccessibleOperationalCountryIds($operation = null, $returnType = null, $requiredPermissions = []) {
        if (is_null($this->decoratedUserRole)) {
            return [];
        } else {
            return $this->getDecoratedUserRole()->getAccessibleOperationalCountryIds($operation, $returnType);
        }
    }

    public function getAccessibleSystemUserIds($operation = null, $returnType = null, $requiredPermissions = []) {
        if (is_null($this->decoratedUserRole)) {
            return [];
        } else {
            return $this->getDecoratedUserRole()->getAccessibleSystemUserIds($operation, $returnType);
        }
    }

    public function getAccessibleUserRoleIds($operation = null, $returnType = null, $requiredPermissions = []) {
        if (is_null($this->decoratedUserRole)) {
            return [];
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

