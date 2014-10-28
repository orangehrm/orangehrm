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

/**
 * Description of ResourcePermission
 *
 */
class ResourcePermission {
    private $canRead;
    private $canCreate;
    private $canUpdate;
    private $canDelete;
    
    function __construct($canRead, $canCreate, $canUpdate, $canDelete) {
        $this->canRead = $canRead;
        $this->canCreate = $canCreate;
        $this->canUpdate = $canUpdate;
        $this->canDelete = $canDelete;
    }

    public function canRead() {
        return $this->canRead;
    }

    public function canCreate() {
        return $this->canCreate;
    }
    
    public function canUpdate() {
        return $this->canUpdate;
    }

    public function canDelete() {
        return $this->canDelete;
    }
    
    public function andWith(ResourcePermission $permission) {
        $permission = new ResourcePermission($this->canRead() && $permission->canRead(),
                $this->canCreate() && $permission->canCreate(),
                $this->canUpdate() && $permission->canUpdate(),
                $this->canDelete() && $permission->canDelete());
        
        return $permission;
    }
    
    /**
     * 
     * @param array $permissions
     * @param boolean $defaulft
     * @return ResourcePermission
     */
    public static function fromArray(array $permissions, $defaulft = false) {
        $canRead = isset($permissions['canRead']) ? $permissions['canRead'] : $defaulft;
        $canCreate = isset($permissions['canCreate']) ? $permissions['canCreate'] : $defaulft;
        $canUpdate = isset($permissions['canUpdate']) ? $permissions['canUpdate'] : $defaulft;
        $canDelete = isset($permissions['canDelete']) ? $permissions['canDelete'] : $defaulft;
        return new self($canRead, $canCreate, $canUpdate, $canDelete);
    }

}

