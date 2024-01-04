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

use OrangeHRM\Entity\Timesheet;

class DetailedTimesheet
{
    private Timesheet $timesheet;
    private array $rows;
    private array $columns;

    /**
     * @param Timesheet $timesheet
     * @param TimesheetRow[] $rows
     * @param TimesheetColumn[] $columns
     */
    public function __construct(Timesheet $timesheet, array $rows, array $columns)
    {
        $this->timesheet = $timesheet;
        $this->rows = $rows;
        $this->columns = $columns;
    }

    /**
     * @return Timesheet
     */
    public function getTimesheet(): Timesheet
    {
        return $this->timesheet;
    }

    /**
     * @return TimesheetRow[]
     */
    public function getRows(): array
    {
        return $this->rows;
    }

    /**
     * @return TimesheetColumn[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }
}
