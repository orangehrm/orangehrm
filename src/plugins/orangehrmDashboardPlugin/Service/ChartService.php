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

namespace OrangeHRM\Dashboard\Service;

use OrangeHRM\Dashboard\Dao\ChartDao;
use OrangeHRM\Dashboard\Dto\EmployeeDistributionByLocation;
use OrangeHRM\Dashboard\Dto\EmployeeDistributionBySubunit;
use OrangeHRM\Dashboard\Dto\SubunitCountPair;

class ChartService
{
    /**
     * @var ChartDao
     */
    private ChartDao $chartDao;

    /**
     * @return ChartDao
     */
    public function getChartDao(): ChartDao
    {
        return $this->chartDao ??= new ChartDao();
    }

    /**
     * @param int $limit
     * @return EmployeeDistributionBySubunit
     */
    public function getEmployeeDistributionBySubunit(int $limit = 8): EmployeeDistributionBySubunit
    {
        $subunitCountPairs = $this->getChartDao()
            ->getEmployeeDistributionBySubunit();
        usort(
            $subunitCountPairs,
            static function (SubunitCountPair $x, SubunitCountPair $y) {
                return ($x->getCount() < $y->getCount()) ? 1 : -1;
            }
        );

        $totalSubunitCount = count($subunitCountPairs);
        $otherArray = [];
        if ($totalSubunitCount > $limit + 1) {
            $otherArray = array_slice($subunitCountPairs, $limit);
            $subunitCountPairs = array_slice($subunitCountPairs, 0, $limit);
        }

        $otherCount = 0;
        foreach ($otherArray as $subunitCountPair) {
            $otherCount += $subunitCountPair->getCount();
        }

        return new EmployeeDistributionBySubunit(
            $subunitCountPairs,
            $otherCount,
            $totalSubunitCount,
            $this->getChartDao()->getUnassignedEmployeeCount(),
            $limit
        );
    }

    /**
     * @param array $locationEmployeeCounts
     * @return int
     */
    public function getLocationUnassignedEmployeeCount(array $locationEmployeeCounts): int
    {
        $totalActiveEmployee = $this->getChartDao()->getTotalActiveEmployeeCount();

        $assignedEmployeeCount = 0;
        foreach ($locationEmployeeCounts as $locationEmployeeCount) {
            $assignedEmployeeCount += $locationEmployeeCount->getEmployeeCount();
        }
        return $totalActiveEmployee - $assignedEmployeeCount;
    }

    /**
     * @param int $limit
     * @return EmployeeDistributionByLocation
     */
    public function getEmployeeDistributionByLocation(int $limit = 8): EmployeeDistributionByLocation
    {
        $locationEmployeeCount = $this->getChartDao()->getEmployeeDistributionByLocation();
        $unassignedEmployeeCount =  $this->getLocationUnassignedEmployeeCount($locationEmployeeCount);

        $totalLocationCount = count($locationEmployeeCount);
        $otherArray = [];
        if ($totalLocationCount > $limit + 1) {
            $otherArray = array_slice($locationEmployeeCount, $limit);
            $locationEmployeeCount = array_slice($locationEmployeeCount, 0, $limit);
        }

        $otherCount = 0;
        foreach ($otherArray as $locationCountPair) {
            $otherCount += $locationCountPair->getEmployeeCount();
        }

        return new EmployeeDistributionByLocation(
            $locationEmployeeCount,
            $otherCount,
            $totalLocationCount,
            $unassignedEmployeeCount,
            $limit,
        );
    }
}
