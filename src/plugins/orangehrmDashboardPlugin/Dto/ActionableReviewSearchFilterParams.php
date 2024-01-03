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

namespace OrangeHRM\Dashboard\Dto;

use OrangeHRM\Core\Dto\FilterParams;

class ActionableReviewSearchFilterParams extends FilterParams
{
    public const INCLUDE_EMPLOYEES_ONLY_CURRENT = 'onlyCurrent';
    public const INCLUDE_EMPLOYEES_ONLY_PAST = 'onlyPast';
    public const INCLUDE_EMPLOYEES_CURRENT_AND_PAST = 'currentAndPast';

    public const INCLUDE_EMPLOYEES = [
        self::INCLUDE_EMPLOYEES_ONLY_CURRENT,
        self::INCLUDE_EMPLOYEES_ONLY_PAST,
        self::INCLUDE_EMPLOYEES_CURRENT_AND_PAST,
    ];

    /**
     * @var int|null
     */
    protected ?int $empNumber = null;

    /**
     * @var int|null
     */
    protected ?int $reviewerEmpNumber = null;

    /**
     * @var string
     */
    protected string $includeEmployees = self::INCLUDE_EMPLOYEES_ONLY_CURRENT;

    /**
     * @var array|null
     */
    protected ?array $actionableStatuses = null;

    /**
     * @var array|null
     */
    protected ?array $selfReviewStatuses = null;

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
     * @return int|null
     */
    public function getReviewerEmpNumber(): ?int
    {
        return $this->reviewerEmpNumber;
    }

    /**
     * @param int|null $reviewerEmpNumber
     */
    public function setReviewerEmpNumber(?int $reviewerEmpNumber): void
    {
        $this->reviewerEmpNumber = $reviewerEmpNumber;
    }

    /**
     * @return string
     */
    public function getIncludeEmployees(): string
    {
        return $this->includeEmployees;
    }

    /**
     * @param string $includeEmployees
     */
    public function setIncludeEmployees(string $includeEmployees): void
    {
        $this->includeEmployees = $includeEmployees;
    }

    /**
     * @return array|null
     */
    public function getActionableStatuses(): ?array
    {
        return $this->actionableStatuses;
    }

    /**
     * @param array $actionableStatuses
     */
    public function setActionableStatuses(array $actionableStatuses): void
    {
        $this->actionableStatuses = $actionableStatuses;
    }

    /**
     * @return array|null
     */
    public function getSelfReviewStatuses(): ?array
    {
        return $this->selfReviewStatuses;
    }

    /**
     * @param array $selfReviewStatuses
     */
    public function setSelfReviewStatuses(array $selfReviewStatuses): void
    {
        $this->selfReviewStatuses = $selfReviewStatuses;
    }
}
