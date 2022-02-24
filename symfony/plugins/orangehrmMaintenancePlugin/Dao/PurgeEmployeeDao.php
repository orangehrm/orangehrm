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

use Doctrine\Persistence\Mapping\MappingException;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\EmpDirectDebit;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmployeeSalary;

class PurgeEmployeeDao extends BaseDao
{
    /**
     * @return Employee[]
     */
    public function getEmployeePurgingList(): array
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
        $empNumber = reset($matchByValues);
        $field = key($matchByValues);

        $qb = $this->createQueryBuilder('OrangeHRM\\Entity\\' . $table, 'entity');
        $qb->andWhere($qb->expr()->eq('entity.' . $field, ':empNumber'))
            ->setParameter('empNumber', $empNumber);

        return $qb->getQuery()->execute();
    }

    /**
     * @param object $entity
     * @return object
     * @throws MappingException
     */
    public function saveEntity(object $entity): object
    {
        $this->persist($entity);

        if ($entity instanceof Employee) {
            $this->getEntityManager()->clear();
        }

        return $entity;
    }

    /**
     * @param object $entity
     * @throws MappingException
     */
    public function deleteEntity(object $entity): void
    {
        $this->remove($entity);

        if ($entity instanceof EmployeeSalary) {
            $this->getEntityManager()->clear(EmpDirectDebit::class);
        }
    }
}
