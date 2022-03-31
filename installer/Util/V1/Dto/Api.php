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

class Api extends DataGroup
{
    private string $api;
    private string $module;

    /**
     * @param string $name
     * @param string $description
     * @param AllowedPermission $allowed
     * @param DataGroupPermission[] $permissions
     */
    public function __construct(
        string $name,
        string $description,
        string $api,
        string $module,
        AllowedPermission $allowed,
        array $permissions
    ) {
        parent::__construct($name, $description, $allowed, $permissions);
        $this->api = $api;
        $this->module = $module;
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
            $dataGroup['api'],
            $dataGroup['module'],
            AllowedPermission::createFromArray($dataGroup['allowed']),
            $permissions
        );
    }

    /**
     * @return string
     */
    public function getApi(): string
    {
        return $this->api;
    }

    /**
     * @return string
     */
    public function getModule(): string
    {
        return $this->module;
    }
}
