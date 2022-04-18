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

namespace OrangeHRM\Admin\Dto;

class AboutOrganization
{
    /**
     * @var string
     */
    private string $companyName;

    /**
     * @var string
     */
    private string $version;

    /**
     * @var int
     */
    private int $numberOfActiveEmployee;

    /**
     * @var int
     */
    private int $numberOfPastEmployee;

    /**
     * @return string
     */
    public function getCompanyName(): string
    {
        return $this->companyName;
    }

    /**
     * @param string $companyName
     */
    public function setCompanyName(string $companyName): void
    {
        $this->companyName = $companyName;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    /**
     * @return int
     */
    public function getNumberOfActiveEmployee(): int
    {
        return $this->numberOfActiveEmployee;
    }

    /**
     * @param int $numberOfActiveEmployee
     */
    public function setNumberOfActiveEmployee(int $numberOfActiveEmployee): void
    {
        $this->numberOfActiveEmployee = $numberOfActiveEmployee;
    }

    /**
     * @return int
     */
    public function getNumberOfPastEmployee(): int
    {
        return $this->numberOfPastEmployee;
    }

    /**
     * @param int $numberOfPastEmployee
     */
    public function setNumberOfPastEmployee(int $numberOfPastEmployee): void
    {
        $this->numberOfPastEmployee = $numberOfPastEmployee;
    }
}
