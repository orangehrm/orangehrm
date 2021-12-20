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

use DateTime;
use OrangeHRM\Core\Dto\FilterParams;
use OrangeHRM\Entity\Employee;

class MyTimesheetSearchFilterParams extends FilterParams
{
    public const ALLOWED_SORT_FIELDS = [
        'timesheet.startDate',
    ];

    /**
     * @var DateTime
     */
    protected DateTime $fromDate;

    /**
     * @var DateTime
     */
    protected DateTime $toDate;

    /**
     * @var DateTime
     */
    protected DateTime $date;

    /**
     * @var int
     */
    protected int $authEmpNumber;

    public function __construct()
    {
        $this->setSortField('timesheet.startDate');
    }

    /**
     * @return DateTime
     */
    public function getFromDate(): DateTime
    {
        return $this->fromDate;
    }

    /**
     * @param  DateTime  $fromDate
     */
    public function setFromDate(DateTime $fromDate): void
    {
        $this->fromDate = $fromDate;
    }

    /**
     * @return DateTime
     */
    public function getToDate(): DateTime
    {
        return $this->toDate;
    }

    /**
     * @param  DateTime  $toDate
     */
    public function setToDate(DateTime $toDate): void
    {
        $this->toDate = $toDate;
    }

    /**
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @param  DateTime  $date
     */
    public function setDate(DateTime $date): void
    {
        $this->date = $date;
    }

    /**
     * @return int
     */
    public function getAuthEmpNumber(): int
    {
        return $this->authEmpNumber;
    }

    /**
     * @param  int  $authEmpNumber
     */
    public function setAuthEmpNumber(int $authEmpNumber): void
    {
        $this->authEmpNumber = $authEmpNumber;
    }
}
