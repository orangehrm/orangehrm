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

use OrangeHRM\Entity\UserRole;

class DataGroupPermissionFilterParams
{
    /**
     * @var UserRole[]
     */
    private array $userRoles = [];

    /**
     * @var bool
     */
    private bool $withApiDataGroups = false;

    /**
     * @var bool
     * Define to fetch only if, at least one permission there from read, create, update, delete
     */
    private bool $onlyAccessible = true;

    /**
     * @var string[]
     * e.g. ['ESS']
     */
    private array $rolesToExclude = [];

    /**
     * @var string[]
     * e.g. ['Admin', 'Supervisor']
     */
    private array $rolesToInclude = [];

    /**
     * @var array<string,int>
     * e.g. [Employee::class => 1]
     */
    private array $entities = [];

    /**
     * @var string[]
     * e.g. ['personal_information', 'contact_details']
     */
    private array $dataGroups = [];

    /**
     * @var bool
     */
    private bool $selfPermissions = false;

    /**
     * @return UserRole[]
     */
    public function getUserRoles(): array
    {
        return $this->userRoles;
    }

    /**
     * @param UserRole[] $userRoles
     */
    public function setUserRoles(array $userRoles): void
    {
        $this->userRoles = $userRoles;
    }

    /**
     * @return bool
     */
    public function isWithApiDataGroups(): bool
    {
        return $this->withApiDataGroups;
    }

    /**
     * @param bool $withApiDataGroups
     */
    public function setWithApiDataGroups(bool $withApiDataGroups): void
    {
        $this->withApiDataGroups = $withApiDataGroups;
    }

    /**
     * @return bool
     */
    public function isOnlyAccessible(): bool
    {
        return $this->onlyAccessible;
    }

    /**
     * @param bool $onlyAccessible
     */
    public function setOnlyAccessible(bool $onlyAccessible): void
    {
        $this->onlyAccessible = $onlyAccessible;
    }

    /**
     * @return string[]
     */
    public function getRolesToExclude(): array
    {
        return $this->rolesToExclude;
    }

    /**
     * @param string[] $rolesToExclude
     */
    public function setRolesToExclude(array $rolesToExclude): void
    {
        $this->rolesToExclude = $rolesToExclude;
    }

    /**
     * @return string[]
     */
    public function getRolesToInclude(): array
    {
        return $this->rolesToInclude;
    }

    /**
     * @param string[] $rolesToInclude
     */
    public function setRolesToInclude(array $rolesToInclude): void
    {
        $this->rolesToInclude = $rolesToInclude;
    }

    /**
     * @return int[]
     */
    public function getEntities(): array
    {
        return $this->entities;
    }

    /**
     * @param int[] $entities
     */
    public function setEntities(array $entities): void
    {
        $this->entities = $entities;
    }

    /**
     * @return string[]
     */
    public function getDataGroups(): array
    {
        return $this->dataGroups;
    }

    /**
     * @param string[] $dataGroups
     */
    public function setDataGroups(array $dataGroups): void
    {
        $this->dataGroups = $dataGroups;
    }

    /**
     * @return bool
     */
    public function isSelfPermissions(): bool
    {
        return $this->selfPermissions;
    }

    /**
     * @param bool $selfPermissions
     */
    public function setSelfPermissions(bool $selfPermissions): void
    {
        $this->selfPermissions = $selfPermissions;
    }
}
