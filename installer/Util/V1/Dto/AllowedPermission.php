<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Installer\Util\V1\Dto;

class AllowedPermission
{
    private bool $read;
    private bool $create;
    private bool $update;
    private bool $delete;

    /**
     * @param bool $read
     * @param bool $create
     * @param bool $update
     * @param bool $delete
     */
    public function __construct(bool $read, bool $create = false, bool $update = false, bool $delete = false)
    {
        $this->read = $read;
        $this->create = $create;
        $this->update = $update;
        $this->delete = $delete;
    }

    /**
     * @param array $permission ['read' => true, 'create' => false, 'update' => true, 'delete' => false]
     * @return self
     */
    public static function createFromArray(array $permission): self
    {
        return new self(
            $permission['read'] ?? false,
            $permission['create'] ?? false,
            $permission['update'] ?? false,
            $permission['delete'] ?? false
        );
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
}
