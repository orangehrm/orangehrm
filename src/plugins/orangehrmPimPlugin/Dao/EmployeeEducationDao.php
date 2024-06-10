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
use OrangeHRM\Entity\EmployeeEducation;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\Pim\Dto\EmployeeEducationSearchFilterParams;

class EmployeeEducationDao extends BaseDao
{
    /**
     * @param EmployeeEducation $employeeEducation
     * @return EmployeeEducation
     */
    public function saveEmployeeEducation(EmployeeEducation $employeeEducation): EmployeeEducation
    {
        $this->persist($employeeEducation);
        return $employeeEducation;
    }

    /**
     * @param int $empNumber
     * @param int $id
     * @return EmployeeEducation|null
     */
    public function getEmployeeEducationById(int $empNumber, int $id): ?EmployeeEducation
    {
        $employeeEducation = $this->getRepository(EmployeeEducation::class)->findOneBy(
            [
                'employee' => $empNumber,
                'id' => $id,
            ]
        );
        if ($employeeEducation instanceof EmployeeEducation) {
            return $employeeEducation;
        }
        return null;
    }

    /**
     * @param int[] $ids
     * @param int $empNumber
     * @return int[]
     */
    public function getExistingEmpEducationIdsByEmpNumber(array $ids, int $empNumber): array
    {
        $qb = $this->createQueryBuilder(EmployeeEducation::class, 'employeeEducation');

        $qb->select('employeeEducation.id')
            ->andWhere($qb->expr()->in('employeeEducation.employee', ':ids'))
            ->andWhere($qb->expr()->eq('employeeEducation.employee', ':empNumber'))
            ->setParameter('ids', $ids)
            ->setParameter('empNumber', $empNumber);

        return $qb->getQuery()->getSingleColumnResult();
    }

    /**
     * @param int $empNumber
     * @param array $toDeleteIds
     * @return int
     */
    public function deleteEmployeeEducations(int $empNumber, array $toDeleteIds): int
    {
        $q = $this->createQueryBuilder(EmployeeEducation::class, 'ee');
        $q->delete()
            ->andWhere('ee.employee = :empNumber')
            ->setParameter('empNumber', $empNumber)
            ->andWhere($q->expr()->in('ee.id', ':ids'))
            ->setParameter('ids', $toDeleteIds);
        return $q->getQuery()->execute();
    }

    /**
     * Search EmployeeEducation
     *
     * @param EmployeeEducationSearchFilterParams $employeeEducationSearchParams
     * @return EmployeeEducation[]
     */
    public function searchEmployeeEducation(EmployeeEducationSearchFilterParams $employeeEducationSearchParams): array
    {
        $paginator = $this->getSearchEmployeeEducationPaginator($employeeEducationSearchParams);
        return $paginator->getQuery()->execute();
    }

    /**
     * @param EmployeeEducationSearchFilterParams $employeeEducationSearchParams
     * @return Paginator
     */
    private function getSearchEmployeeEducationPaginator(
        EmployeeEducationSearchFilterParams $employeeEducationSearchParams
    ): Paginator {
        $q = $this->createQueryBuilder(EmployeeEducation::class, 'ee');
        $q->leftJoin('ee.education', 'e');
        $this->setSortingAndPaginationParams($q, $employeeEducationSearchParams);

        $q->andWhere('ee.employee = :empNumber')
            ->setParameter('empNumber', $employeeEducationSearchParams->getEmpNumber());
        return $this->getPaginator($q);
    }

    /**
     * Get Count of Search Query
     *
     * @param EmployeeEducationSearchFilterParams $employeeEducationSearchParams
     * @return int
     */
    public function getSearchEmployeeEducationsCount(
        EmployeeEducationSearchFilterParams $employeeEducationSearchParams
    ): int {
        $paginator = $this->getSearchEmployeeEducationPaginator($employeeEducationSearchParams);
        return $paginator->count();
    }
}
