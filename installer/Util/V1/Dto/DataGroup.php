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

class DataGroup
{
    protected string $name;
    protected string $description;
    protected AllowedPermission $allowed;
    /**
     * @var DataGroupPermission[]
     */
    protected array $permissions;

    /**
     * @param string $name
     * @param string $description
     * @param AllowedPermission $allowed
     * @param DataGroupPermission[] $permissions
     */
    public function __construct(string $name, string $description, AllowedPermission $allowed, array $permissions)
    {
        $this->name = $name;
        $this->description = $description;
        $this->allowed = $allowed;
        $this->permissions = $permissions;
    }

    /**
     * @param array $dataGroup
     * @return self
     */
    public static function createFromArray(string $name, array $dataGroup): self
    {
        $permissions = [];
        foreach ($dataGroup['permissions'] as $userRolePermission) {
            $permissions[] = DataGroupPermission::createFromArray($userRolePermission);
        }
        return new self(
            $name,
            $dataGroup['description'],
            AllowedPermission::createFromArray($dataGroup['allowed']),
            $permissions
        );
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return AllowedPermission
     */
    public function getAllowed(): AllowedPermission
    {
        return $this->allowed;
    }

    /**
     * @return DataGroupPermission[]
     */
    public function getPermissions(): array
    {
        return $this->permissions;
    }
}
