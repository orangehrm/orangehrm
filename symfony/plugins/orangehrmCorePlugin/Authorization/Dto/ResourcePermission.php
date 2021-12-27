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

namespace OrangeHRM\Core\Authorization\Dto;

use OrangeHRM\Entity\DataGroupPermission;

class ResourcePermission
{
    /**
     * @var bool
     */
    private bool $canRead;

    /**
     * @var bool
     */
    private bool $canCreate;

    /**
     * @var bool
     */
    private bool $canUpdate;

    /**
     * @var bool
     */
    private bool $canDelete;

    /**
     * @param bool $canRead
     * @param bool $canCreate
     * @param bool $canUpdate
     * @param bool $canDelete
     */
    public function __construct(bool $canRead, bool $canCreate, bool $canUpdate, bool $canDelete)
    {
        $this->canRead = $canRead;
        $this->canCreate = $canCreate;
        $this->canUpdate = $canUpdate;
        $this->canDelete = $canDelete;
    }

    /**
     * @return bool
     */
    public function canRead(): bool
    {
        return $this->canRead;
    }

    /**
     * @return bool
     */
    public function canCreate(): bool
    {
        return $this->canCreate;
    }

    /**
     * @return bool
     */
    public function canUpdate(): bool
    {
        return $this->canUpdate;
    }

    /**
     * @return bool
     */
    public function canDelete(): bool
    {
        return $this->canDelete;
    }

    /**
     * @param ResourcePermission $permission
     * @return self
     */
    public function andWith(ResourcePermission $permission): self
    {
        return new ResourcePermission(
            $this->canRead() && $permission->canRead(),
            $this->canCreate() && $permission->canCreate(),
            $this->canUpdate() && $permission->canUpdate(),
            $this->canDelete() && $permission->canDelete()
        );
    }

    /**
     * @param ResourcePermission $permission
     * @return self
     */
    public function orWith(ResourcePermission $permission): self
    {
        return new ResourcePermission(
            $this->canRead() || $permission->canRead(),
            $this->canCreate() || $permission->canCreate(),
            $this->canUpdate() || $permission->canUpdate(),
            $this->canDelete() || $permission->canDelete()
        );
    }

    /**
     * @param array $permissions
     * @param bool $default
     * @return self
     */
    public static function fromArray(array $permissions, bool $default = false): self
    {
        $canRead = $permissions['canRead'] ?? $default;
        $canCreate = $permissions['canCreate'] ?? $default;
        $canUpdate = $permissions['canUpdate'] ?? $default;
        $canDelete = $permissions['canDelete'] ?? $default;
        return new self($canRead, $canCreate, $canUpdate, $canDelete);
    }

    /**
     * @param DataGroupPermission $dataGroupPermission
     * @return self
     */
    public static function createFromDataGroupPermission(DataGroupPermission $dataGroupPermission): self
    {
        return new self(
            $dataGroupPermission->canRead(),
            $dataGroupPermission->canCreate(),
            $dataGroupPermission->canUpdate(),
            $dataGroupPermission->canDelete()
        );
    }
}
