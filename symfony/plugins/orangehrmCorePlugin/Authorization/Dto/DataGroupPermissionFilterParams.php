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
}
