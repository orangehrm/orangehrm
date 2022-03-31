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

namespace OrangeHRM\Installer\Util\V1\Dto;

class DataGroupPermission
{
    private string $userRole;
    private bool $read;
    private bool $create;
    private bool $update;
    private bool $delete;
    private bool $self;

    /**
     * @param string $userRole
     * @param bool $read
     * @param bool $create
     * @param bool $update
     * @param bool $delete
     * @param bool $self
     */
    public function __construct(
        string $userRole,
        bool $read,
        bool $create = false,
        bool $update = false,
        bool $delete = false,
        bool $self = false
    ) {
        $this->userRole = $userRole;
        $this->read = $read;
        $this->create = $create;
        $this->update = $update;
        $this->delete = $delete;
        $this->self = $self;
    }

    /**
     * @param array $userRolePermission ['role' => 'Admin', 'permission' => ['read' => true, 'create' => false, 'update' => true, 'delete' => false, 'self' => false]]
     * @return self
     */
    public static function createFromArray(array $userRolePermission): self
    {
        return new self(
            $userRolePermission['role'],
            $userRolePermission['permission']['read'] ?? false,
            $userRolePermission['permission']['create'] ?? false,
            $userRolePermission['permission']['update'] ?? false,
            $userRolePermission['permission']['delete'] ?? false,
            $userRolePermission['permission']['self'] ?? false
        );
    }

    /**
     * @return string
     */
    public function getUserRole(): string
    {
        return $this->userRole;
    }

    /**
     * @return bool
     */
    public function canRead(): bool
    {
        return $this->read;
    }

    /**
     * @return bool
     */
    public function canCreate(): bool
    {
        return $this->create;
    }

    /**
     * @return bool
     */
    public function canUpdate(): bool
    {
        return $this->update;
    }

    /**
     * @return bool
     */
    public function canDelete(): bool
    {
        return $this->delete;
    }

    /**
     * @return bool
     */
    public function isSelf(): bool
    {
        return $this->self;
    }
}
