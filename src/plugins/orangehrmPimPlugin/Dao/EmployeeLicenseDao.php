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

use Doctrine\ORM\Query\Expr;
use OrangeHRM\Core\Dao\BaseDao;
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
     */
    public function saveEmployeeLicense(EmployeeLicense $employeeLicense): EmployeeLicense
    {
        $this->persist($employeeLicense);
        return $employeeLicense;
    }

    /**
     * @param int $empNumber
     * @param int $licenseId
     * @return EmployeeLicense|null
     */
    public function getEmployeeLicense(int $empNumber, int $licenseId): ?EmployeeLicense
    {
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
    }

    /**
     * @param int $empNumber
     * @param array $entriesToDelete
     * @return int
     */
    public function deleteEmployeeLicenses(int $empNumber, array $entriesToDelete): int
    {
        $q = $this->createQueryBuilder(EmployeeLicense::class, 'el');
        $q->delete()
            ->where('el.employee = :empNumber')
            ->setParameter('empNumber', $empNumber);
        $q->andWhere($q->expr()->in('el.license', ':ids'))
            ->setParameter('ids', $entriesToDelete);
        return $q->getQuery()->execute();
    }

    /**
     * @param EmployeeLicenseSearchFilterParams $employeeLicenseSearchFilterParams
     * @return array
     */
    public function searchEmployeeLicense(EmployeeLicenseSearchFilterParams $employeeLicenseSearchFilterParams): array
    {
        $paginator = $this->getSearchEmployeeLicensesPaginator($employeeLicenseSearchFilterParams);
        return $paginator->getQuery()->execute();
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
     */
    public function getSearchEmployeeLicensesCount(
        EmployeeLicenseSearchFilterParams $employeeLicenseSearchFilterParams
    ): int {
        $paginator = $this->getSearchEmployeeLicensesPaginator($employeeLicenseSearchFilterParams);
        return $paginator->count();
    }

    /**
     * @param EmployeeAllowedLicenseSearchFilterParams $skillSearchFilterParams
     * @return License[]
     */
    public function getEmployeeAllowedLicenses(EmployeeAllowedLicenseSearchFilterParams $skillSearchFilterParams): array
    {
        $paginator = $this->getEmployeeAllowedLicensesPaginator($skillSearchFilterParams);
        return $paginator->getQuery()->execute();
    }

    /**
     * @param EmployeeAllowedLicenseSearchFilterParams $skillSearchFilterParams
     * @return int
     */
    public function getEmployeeAllowedLicensesCount(
        EmployeeAllowedLicenseSearchFilterParams $skillSearchFilterParams
    ): int {
        $paginator = $this->getEmployeeAllowedLicensesPaginator($skillSearchFilterParams);
        return $paginator->count();
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
