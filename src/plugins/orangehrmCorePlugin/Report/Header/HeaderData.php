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

namespace OrangeHRM\Core\Report\Header;

class HeaderData
{
    /**
     * @var Array<StackedColumn|Column>
     */
    private array $columns = [];
    private int $columnCount = 0;
    private int $groupCount = 0;
    private int $groupedColumnCount = 0;

    /**
     * @return Array<StackedColumn|Column>
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @param Array<StackedColumn|Column> $columns
     */
    public function setColumns(array $columns): void
    {
        $this->columns = $columns;
    }

    /**
     * @return int
     */
    public function getColumnCount(): int
    {
        return $this->columnCount;
    }

    /**
     * @param int $columnCount
     */
    public function setColumnCount(int $columnCount): void
    {
        $this->columnCount = $columnCount;
    }

    /**
     * @param int $by
     * @return int
     */
    public function incrementColumnCount(int $by = 1): int
    {
        $this->columnCount = $this->columnCount + $by;
        return $this->columnCount;
    }

    /**
     * @return int
     */
    public function getGroupCount(): int
    {
        return $this->groupCount;
    }

    /**
     * @param int $groupCount
     */
    public function setGroupCount(int $groupCount): void
    {
        $this->groupCount = $groupCount;
    }

    /**
     * @param int $by
     * @return int
     */
    public function incrementGroupCount(int $by = 1): int
    {
        $this->groupCount = $this->groupCount + $by;
        return $this->groupCount;
    }

    /**
     * @return int
     */
    public function getGroupedColumnCount(): int
    {
        return $this->groupedColumnCount;
    }

    /**
     * @param int $groupedColumnCount
     */
    public function setGroupedColumnCount(int $groupedColumnCount): void
    {
        $this->groupedColumnCount = $groupedColumnCount;
    }

    /**
     * @param int $by
     * @return int
     */
    public function incrementGroupedColumnCount(int $by = 1): int
    {
        $this->groupedColumnCount = $this->groupedColumnCount + $by;
        return $this->groupedColumnCount;
    }
}
