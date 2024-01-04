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

namespace OrangeHRM\Time\Dto;

use DateTime;
use OrangeHRM\Core\Dto\FilterParams;

class TimesheetSearchFilterParams extends FilterParams
{
    public const ALLOWED_SORT_FIELDS = [
        'timesheet.startDate',
    ];

    /**
     * @var DateTime|null
     */
    protected ?DateTime $fromDate = null;

    /**
     * @var DateTime|null
     */
    protected ?DateTime $toDate = null;

    /**
     * @var int
     */
    protected int $empNumber;

    public function __construct()
    {
        $this->setSortField('timesheet.startDate');
    }

    /**
     * @return DateTime|null
     */
    public function getFromDate(): ?DateTime
    {
        return $this->fromDate;
    }

    /**
     * @param  DateTime|null  $fromDate
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
     * @param  DateTime|null  $toDate
     */
    public function setToDate(?DateTime $toDate): void
    {
        $this->toDate = $toDate;
    }

    /**
     * @return int
     */
    public function getEmpNumber(): int
    {
        return $this->empNumber;
    }

    /**
     * @param  int  $empNumber
     */
    public function setEmpNumber(int $empNumber): void
    {
        $this->empNumber = $empNumber;
    }
}
