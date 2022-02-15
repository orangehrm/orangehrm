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

namespace OrangeHRM\Maintenance\Dao;

use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\Employee;

class PurgeEmployeeDao extends BaseDao
{
    /**
     * @return Employee[]
     */
    public function getEmployeePurgingList(): array //searchParamFilter
    {
        $qb = $this->createQueryBuilder(Employee::class, 'employee');
        $qb->andWhere($qb->expr()->isNotNull('employee.employeeTerminationRecord'));
        $qb->andWhere($qb->expr()->isNull('employee.purgedAt'));
        $qb->addOrderBy('employee.empNumber');

        return $qb->getQuery()->execute();
    }

    /**
     * @param array $matchByValues
     * @param string $table
     * @return array
     */
    public function extractDataFromEmpNumber(array $matchByValues, string $table): array
    {
        //This function extracts data from a given table by matching the empNumber field in the table
        $employeeId = reset($matchByValues);
        $field = key($matchByValues);

        $qb = $this->createQueryBuilder('OrangeHRM\\Entity\\' . $table, 't');
        $qb->andWhere($qb->expr()->eq('t.' . $field, ':employeeId'))
            ->setParameter('employeeId', $employeeId);

        return $qb->getQuery()->execute();
    }
//        $table2 = $matchByValues['join'] ?? null;
//        if (!is_null($table2)) {
//            $qb->innerJoin('t.' . $table2, 'tt');
//        }

//        $table2 = $matchByValues['join'];
//        if ($matchByValues['join']) {
//            $q = Doctrine_Query::create()
//                ->select('*')
//                ->from($table . ' l')
//                ->innerJoin('l.' . $table2 . ' t')
//                ->where($field . " = ?", $employeeId);
//        } else {
//            $q = Doctrine_Query::create()
//                ->select('*')
//                ->from($table)
//                ->where($field . " = ?", $employeeId);
//        }

    /**
     * @param $entity
     * @return mixed
     */
    public function saveEntity($entity) //HOW TO PUT TYPE??
    {
        $this->persist($entity);
        return $entity;
    }
}
