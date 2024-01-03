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

namespace OrangeHRM\Pim\Dao;

use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\EmployeeTerminationRecord;
use OrangeHRM\Entity\TerminationReason;
use OrangeHRM\ORM\ListSorter;

class EmployeeTerminationDao extends BaseDao
{
    /**
     * @param EmployeeTerminationRecord $employeeTerminationRecord
     * @return EmployeeTerminationRecord
     */
    public function saveEmployeeTermination(
        EmployeeTerminationRecord $employeeTerminationRecord
    ): EmployeeTerminationRecord {
        $this->persist($employeeTerminationRecord);
        return $employeeTerminationRecord;
    }

    /**
     * @param int $id
     * @return EmployeeTerminationRecord|null
     */
    public function getEmployeeTermination(int $id): ?EmployeeTerminationRecord
    {
        return $this->getRepository(EmployeeTerminationRecord::class)->find($id);
    }

    /**
     * @return TerminationReason[]
     */
    public function getTerminationReasonList(): array
    {
        $q = $this->createQueryBuilder(TerminationReason::class, 'tr');
        $q->addOrderBy('tr.name', ListSorter::ASCENDING);
        return $q->getQuery()->execute();
    }
}
