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

namespace OrangeHRM\Pim\Dao;

use Exception;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\EmployeeMembership;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\Pim\Dto\EmployeeMembershipSearchFilterParams;

class EmployeeMembershipDao extends BaseDao
{
    /**
     * @param EmployeeMembership $employeeMembership
     * @return EmployeeMembership
     * @throws DaoException
     */
    public function saveEmployeeMembership(EmployeeMembership $employeeMembership): EmployeeMembership
    {
        try {
            $this->persist($employeeMembership);
            return $employeeMembership;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param int $empNumber
     * @param int $id
     * @return EmployeeMembership|null
     * @throws DaoException
     */
    public function getEmployeeMembershipById(int $empNumber, int $id): ?EmployeeMembership
    {
        try {
            $employeeMembership = $this->getRepository(EmployeeMembership::class)->findOneBy(
                [
                    'employee' => $empNumber,
                    'id' => $id,
                ]
            );
            if ($employeeMembership instanceof EmployeeMembership) {
                return $employeeMembership;
            }
            return null;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param int $empNumber
     * @param array $toDeleteIds
     * @return int
     * @throws DaoException
     */
    public function deleteEmployeeMemberships(int $empNumber, array $toDeleteIds): int
    {
        try {
            $q = $this->createQueryBuilder(EmployeeMembership::class, 'em');
            $q->delete()
                ->andWhere('em.employee = :empNumber')
                ->setParameter('empNumber', $empNumber)
                ->andWhere($q->expr()->in('em.id', ':ids'))
                ->setParameter('ids', $toDeleteIds);
            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @param EmployeeMembershipSearchFilterParams $employeeMembershipSearchParams
     * @return array
     * @throws DaoException
     */
    public function searchEmployeeMembership(
        EmployeeMembershipSearchFilterParams $employeeMembershipSearchParams
    ): array {
        try {
            $paginator = $this->getSearchEmployeeMembershipPaginator($employeeMembershipSearchParams);
            return $paginator->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param EmployeeMembershipSearchFilterParams $employeeMembershipSearchParams
     * @return Paginator
     */
    private function getSearchEmployeeMembershipPaginator(
        EmployeeMembershipSearchFilterParams $employeeMembershipSearchParams
    ): Paginator {
        $q = $this->createQueryBuilder(EmployeeMembership::class, 'em');
        $q->leftJoin('em.membership', 'm');
        $this->setSortingAndPaginationParams($q, $employeeMembershipSearchParams);

        $q->andWhere('em.employee = :empNumber')
            ->setParameter('empNumber', $employeeMembershipSearchParams->getEmpNumber());
        return $this->getPaginator($q);
    }

    /**
     * @param EmployeeMembershipSearchFilterParams $employeeMembershipSearchParams
     * @return int
     * @throws DaoException
     */
    public function getSearchEmployeeMembershipsCount(
        EmployeeMembershipSearchFilterParams $employeeMembershipSearchParams
    ): int {
        try {
            $paginator = $this->getSearchEmployeeMembershipPaginator($employeeMembershipSearchParams);
            return $paginator->count();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
