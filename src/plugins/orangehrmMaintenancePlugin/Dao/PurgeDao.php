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

namespace OrangeHRM\Maintenance\Dao;

use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\Employee;

class PurgeDao extends BaseDao
{
    /**
     * @param int $empNumber
     * @return bool
     */
    public function isEmployeePurgeable(int $empNumber): bool
    {
        $qb = $this->createQueryBuilder(Employee::class, 'employee');
        $qb->andWhere('employee.empNumber = :empNumber')
            ->setParameter('empNumber', $empNumber);
        $qb->andWhere($qb->expr()->isNotNull('employee.employeeTerminationRecord'));
        $qb->andWhere($qb->expr()->isNull('employee.purgedAt'));

        return !empty($qb->getQuery()->execute());
    }

    /**
     * @param array $matchByValues
     * @param string $table
     * @return array
     */
    public function extractDataFromEmpNumber(array $matchByValues, string $table): array
    {
        $empNumber = reset($matchByValues);
        $field = key($matchByValues);
        $entity = 'entity';

        $qb = $this->createQueryBuilder('OrangeHRM\\Entity\\' . $table, 'entity');
        if (isset($matchByValues['join'])) {
            $qb->innerJoin('entity.' . $matchByValues['join'], 'joinEntity');
            $entity = 'joinEntity';
        }
        $qb->andWhere($qb->expr()->eq($entity . '.' . $field, ':empNumber'))
            ->setParameter('empNumber', $empNumber);

        return $qb->getQuery()->execute();
    }
}
