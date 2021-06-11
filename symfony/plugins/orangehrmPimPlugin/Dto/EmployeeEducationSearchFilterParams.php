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

namespace OrangeHRM\Pim\Dto;

use OrangeHRM\Core\Dto\FilterParams;

class EmployeeEducationSearchFilterParams extends FilterParams
{
    public const ALLOWED_SORT_FIELDS = ['ee.year', 'ee.score', 'ee.institute', 'ee.major'];

    /**
     * @var int|null
     */
    protected ?int $year = null;

    /**
     * @var string|null
     */
    protected ?string $score = null;

    /**
     * @var string|null
     */
    protected ?string $institute = null;

    /**
     * @var string|null
     */
    protected ?string $major = null;

    /**
     * @var string|null
     */
    protected ?string $empNumber = null;

    /**
     * EmployeeSkillSearchFilterParams constructor.
     */
    public function __construct()
    {
        $this->setSortField('ee.year');
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
     * @return string | null
     */
    public function getInstitute(): ?string
    {
        return $this->institute;
    }

    /**
     * @param string | null $institute
     */
    public function setInstitute(?string $institute): void
    {
        $this->institute = $institute;
    }

    /**
     * @return string | null
     */
    public function getMajor(): ?string
    {
        return $this->major;
    }

    /**
     * @param string | null $major
     */
    public function setMajor(?string $major): void
    {
        $this->major = $major;
    }

    /**
     * @return int | null
     */
    public function getYear(): ?int
    {
        return $this->year;
    }

    /**
     * @param int | null $year
     */
    public function setYear(?int $year): void
    {
        $this->year = $year;
    }

    /**
     * @return string | null
     */
    public function getScore(): ?string
    {
        return $this->score;
    }

    /**
     * @param string | null $score
     */
    public function setScore(?string $score): void
    {
        $this->score = $score;
    }
}
