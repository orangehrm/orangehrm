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

namespace OrangeHRM\Attendance\Dto;

use DateTime;
use OrangeHRM\Core\Dto\FilterParams;
use OrangeHRM\Core\Exception\SearchParamException;
use OrangeHRM\ORM\ListSorter;

class AttendanceRecordSearchFilterParams extends FilterParams
{
    public const ALLOWED_SORT_FIELDS = ['attendanceRecord.punchInUserTime'];

    /**
     * @var int[]|null
     */
    private ?array $employeeNumbers = null;

    /**
     * @var DateTime|null
     */
    protected ?DateTime $fromDate = null;

    /**
     * @var DateTime|null
     */
    protected ?DateTime $toDate = null;

    /**
     * @throws SearchParamException
     */
    public function __construct()
    {
        $this->setSortField('attendanceRecord.punchInUserTime');
        $this->setSortOrder(ListSorter::DESCENDING);
    }

    /**
     * @return int[]|null
     */
    public function getEmployeeNumbers(): ?array
    {
        return $this->employeeNumbers;
    }

    /**
     * @param int[]|null $employeeNumbers
     */
    public function setEmployeeNumbers(?array $employeeNumbers): void
    {
        $this->employeeNumbers = $employeeNumbers;
    }

    /**
     * @return DateTime|null
     */
    public function getFromDate(): ?DateTime
    {
        return $this->fromDate;
    }

    /**
     * @param DateTime|null $fromDate
     */
    public function setFromDate(?DateTime $fromDate): void
    {
        $this->fromDate = $fromDate;
    }

    /**
     * @return DateTime|null
     */
    public function getToDate(): ?DateTime
    {
        return $this->toDate;
    }

    /**
     * @param DateTime|null $toDate
     */
    public function setToDate(?DateTime $toDate): void
    {
        $this->toDate = $toDate;
    }
}
