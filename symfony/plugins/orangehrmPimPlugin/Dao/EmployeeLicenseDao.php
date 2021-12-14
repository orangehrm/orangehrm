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

use Doctrine\ORM\Query\Expr;
use Exception;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\EmployeeLicense;
use OrangeHRM\Entity\License;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\Pim\Dto\EmployeeAllowedLicenseSearchFilterParams;
use OrangeHRM\Pim\Dto\EmployeeLicenseSearchFilterParams;

class EmployeeLicenseDao extends BaseDao
{
    /**
     * @param EmployeeLicense $employeeLicense
     * @return EmployeeLicense
     * @throws DaoException
     */
    public function saveEmployeeLicense(EmployeeLicense $employeeLicense): EmployeeLicense
    {
        try {
            $this->persist($employeeLicense);
            return $employeeLicense;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param int $empNumber
     * @param int $licenseId
     * @return EmployeeLicense|null
     * @throws DaoException
     */
    public function getEmployeeLicense(int $empNumber, int $licenseId): ?EmployeeLicense
    {
        try {
            $employeeLicense = $this->getRepository(EmployeeLicense::class)->findOneBy(
                [
                    'employee' => $empNumber,
                    'license' => $licenseId,
                ]
            );
            if ($employeeLicense instanceof EmployeeLicense) {
                return $employeeLicense;
            }
            return null;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param int $empNumber
     * @param array $entriesToDelete
     * @return int
     * @throws DaoException
     */
    public function deleteEmployeeLicenses(int $empNumber, array $entriesToDelete): int
    {
        try {
            $q = $this->createQueryBuilder(EmployeeLicense::class, 'el');
            $q->delete()
                ->where('el.employee = :empNumber')
                ->setParameter('empNumber', $empNumber);
            $q->andWhere($q->expr()->in('el.license', ':ids'))
                ->setParameter('ids', $entriesToDelete);
            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param EmployeeLicenseSearchFilterParams $employeeLicenseSearchFilterParams
     * @return array
     * @throws DaoException
     */
    public function searchEmployeeLicense(EmployeeLicenseSearchFilterParams $employeeLicenseSearchFilterParams): array
    {
        try {
            $paginator = $this->getSearchEmployeeLicensesPaginator($employeeLicenseSearchFilterParams);
            return $paginator->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }


    private function getSearchEmployeeLicensesPaginator(
        EmployeeLicenseSearchFilterParams $employeeLicenseSearchFilterParams
    ): Paginator {
        $q = $this->createQueryBuilder(EmployeeLicense::class, 'el');
        $this->setSortingAndPaginationParams($q, $employeeLicenseSearchFilterParams);

        if (!empty($employeeLicenseSearchFilterParams->getEmpNumber())) {
            $q->andWhere('el.employee = :empNumber');
            $q->setParameter('empNumber', $employeeLicenseSearchFilterParams->getEmpNumber());
        }
        return $this->getPaginator($q);
    }

    /**
     * @param EmployeeLicenseSearchFilterParams $employeeLicenseSearchFilterParams
     * @return int
     * @throws DaoException
     */
    public function getSearchEmployeeLicensesCount(
        EmployeeLicenseSearchFilterParams $employeeLicenseSearchFilterParams
    ): int {
        try {
            $paginator = $this->getSearchEmployeeLicensesPaginator($employeeLicenseSearchFilterParams);
            return $paginator->count();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param EmployeeAllowedLicenseSearchFilterParams $skillSearchFilterParams
     * @return License[]
     * @throws DaoException
     */
    public function getEmployeeAllowedLicenses(EmployeeAllowedLicenseSearchFilterParams $skillSearchFilterParams): array
    {
        try {
            $paginator = $this->getEmployeeAllowedLicensesPaginator($skillSearchFilterParams);
            return $paginator->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param EmployeeAllowedLicenseSearchFilterParams $skillSearchFilterParams
     * @return int
     * @throws DaoException
     */
    public function getEmployeeAllowedLicensesCount(
        EmployeeAllowedLicenseSearchFilterParams $skillSearchFilterParams
    ): int {
        try {
            $paginator = $this->getEmployeeAllowedLicensesPaginator($skillSearchFilterParams);
            return $paginator->count();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param EmployeeAllowedLicenseSearchFilterParams $skillSearchFilterParams
     * @return Paginator
     */
    private function getEmployeeAllowedLicensesPaginator(
        EmployeeAllowedLicenseSearchFilterParams $skillSearchFilterParams
    ): Paginator {
        $q = $this->createQueryBuilder(License::class, 'l');
        $q->leftJoin('l.employeeLicenses', 'el', Expr\Join::WITH, 'el.employee = :empNumber');
        $this->setSortingAndPaginationParams($q, $skillSearchFilterParams);

        $q->andWhere($q->expr()->isNull('el.employee'));
        $q->setParameter('empNumber', $skillSearchFilterParams->getEmpNumber());

        return $this->getPaginator($q);
    }
}
