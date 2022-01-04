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

namespace OrangeHRM\Time\Dto;

use OrangeHRM\Core\Dto\FilterParams;

class ProjectSearchFilterParams extends FilterParams
{
    public const ALLOWED_SORT_FIELDS = [
        'project.name',
        'customer.name'
    ];

    /**
     * @var int[]|null
     */
    protected ?array $projectIds = null;

    /**
     * @var int|null
     */
    protected ?int $customerId = null;

    /**
     * @var int|null
     */
    protected ?int $empNumber = null;

    /**
     * @var string|null
     */
    protected ?string $customerOrProjectName = null;

    /**
     * @var string|null
     */
    protected ?string $name = null;

    /**
     * @var int[]|null
     */
    protected ?array $excludeProjectIds = null;

    public function __construct()
    {
        $this->setSortField('project.name');
    }

    /**
     * @return int[]|null
     */
    public function getProjectIds(): ?array
    {
        return $this->projectIds;
    }

    /**
     * @param int[]|null $projectIds
     */
    public function setProjectIds(?array $projectIds): void
    {
        $this->projectIds = $projectIds;
    }

    /**
     * @return int|null
     */
    public function getCustomerId(): ?int
    {
        return $this->customerId;
    }

    /**
     * @param int|null $customerId
     */
    public function setCustomerId(?int $customerId): void
    {
        $this->customerId = $customerId;
    }

    /**
     * @return int|null
     */
    public function getEmpNumber(): ?int
    {
        return $this->empNumber;
    }

    /**
     * @param int|null $empNumber
     */
    public function setEmpNumber(?int $empNumber): void
    {
        $this->empNumber = $empNumber;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getCustomerOrProjectName(): ?string
    {
        return $this->customerOrProjectName;
    }

    /**
     * @param string|null $customerOrProjectName
     */
    public function setCustomerOrProjectName(?string $customerOrProjectName): void
    {
        $this->customerOrProjectName = $customerOrProjectName;
    }

    /**
     * @return int[]|null
     */
    public function getExcludeProjectIds(): ?array
    {
        return $this->excludeProjectIds;
    }

    /**
     * @param int[]|null $excludeProjectIds
     */
    public function setExcludeProjectIds(?array $excludeProjectIds): void
    {
        $this->excludeProjectIds = $excludeProjectIds;
    }
}
