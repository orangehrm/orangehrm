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

namespace OrangeHRM\Performance\Dao;

use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Performance\Dto\PerformanceTrackerReviewerSearchFilterParams;

class PerformanceTrackReviewerDao extends BaseDao
{
    public function getReviewerList(PerformanceTrackerReviewerSearchFilterParams $performanceTrackerReviewerSearchFilterParams)
    {
        $q = $this->createQueryBuilder(Employee::class,'employee');
        $q->select();
        $this->setSortingAndPaginationParams($q, $performanceTrackerReviewerSearchFilterParams);
        if(!is_null($performanceTrackerReviewerSearchFilterParams->getTrackerempNumber()))
        {
            $q->andWhere('employee.empNumber != :excludeEmployee')
                ->setParameter('excludeEmployee',$performanceTrackerReviewerSearchFilterParams->getTrackerempNumber());
        }
        if (!is_null($performanceTrackerReviewerSearchFilterParams->getNameOrId())) {
            $q->andWhere(
                $q->expr()->orX(
                    $q->expr()->like('employee.firstName', ':nameOrId'),
                    $q->expr()->like('employee.lastName', ':nameOrId'),
                    $q->expr()->like('employee.middleName', ':nameOrId'),
                    $q->expr()->like('employee.employeeId', ':nameOrId'),
                )
            );
            $q->setParameter('nameOrId', '%' . $performanceTrackerReviewerSearchFilterParams->getNameOrId() . '%');
        }
        $q->andWhere($q->expr()->isNull('employee.employeeTerminationRecord'));
        return $q->getQuery()->execute();
    }

}
