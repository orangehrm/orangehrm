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

class EmployeeSupervisorSearchFilterParams extends FilterParams
{
    public const ALLOWED_SORT_FIELDS = ['supervisor.lastName'];


    /**
     * @var string|null
     */
    protected ?string $empNumber = null;

    /**
     * EmployeeSupervisorSearchFilterParams constructor.
     */
    public function __construct()
    {
        $this->setSortField('supervisor.lastName');
    }

    /**
     * @return string|null
     */
    public function getEmpNumber(): ?string
    {
        return $this->empNumber;
    }

    /**
     * @param string|null $empNumber
     */
    public function setEmpNumber(?string $empNumber): void
    {
        $this->empNumber = $empNumber;
    }
}
