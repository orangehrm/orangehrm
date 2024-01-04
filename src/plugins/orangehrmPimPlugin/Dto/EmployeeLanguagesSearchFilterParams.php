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

namespace OrangeHRM\Pim\Dto;

use OrangeHRM\Core\Dto\FilterParams;

class EmployeeLanguagesSearchFilterParams extends FilterParams
{
    public const ALLOWED_SORT_FIELDS = ['l.name'];

    /**
     * @var int|null
     */
    private ?int $empNumber = null;

    /**
     * @var int[]|null
     */
    private ?array $languageIds = null;

    public function __construct()
    {
        $this->setSortField('l.name');
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
     * @return int[]|null
     */
    public function getLanguageIds(): ?array
    {
        return $this->languageIds;
    }

    /**
     * @param int[]|null $languageIds
     */
    public function setLanguageIds(?array $languageIds): void
    {
        $this->languageIds = $languageIds;
    }
}
