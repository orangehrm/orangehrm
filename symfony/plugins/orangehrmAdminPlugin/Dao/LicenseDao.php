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

namespace OrangeHRM\Admin\Dao;

use OrangeHRM\Admin\Dto\LicenseSearchFilterParams;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\Core\Exception\DaoException;
use Exception;
use OrangeHRM\Entity\License;

class LicenseDao extends BaseDao
{
    /**
     * @param License $license
     * @return License
     * @throws DaoException
     */
    public function saveLicense(License $license): License
    {
        try {
            $this->persist($license);
            return $license;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param $id
     * @return License|null
     * @throws DaoException
     */
    public function getLicenseById($id): ?License
    {
        try {
            $license = $this->getRepository(License::class)->find($id);
            if ($license instanceof License) {
                return $license;
            }
            return null;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param $name
     * @return License|null
     * @throws DaoException
     */
    public function getLicenseByName($name): ?License
    {
        try {
            $query = $this->createQueryBuilder(License::class, 'l');
            $trimmed = trim($name, ' ');
            $query->andWhere('l.name = :name');
            $query->setParameter('name', $trimmed);
            return $query->getQuery()->getOneOrNullResult();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param LicenseSearchFilterParams $licenseSearchFilterParams
     * @return array
     * @throws DaoException
     */
    public function getLicenseList(LicenseSearchFilterParams $licenseSearchFilterParams): array
    {
        try {
            $paginator = $this->getLicenseListPaginator($licenseSearchFilterParams);
            return $paginator->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param LicenseSearchFilterParams $licenseSearchFilterParams
     * @return Paginator
     */
    public function getLicenseListPaginator(LicenseSearchFilterParams $licenseSearchFilterParams): Paginator
    {
        $q = $this->createQueryBuilder(License::class, 'l');
        $this->setSortingAndPaginationParams($q, $licenseSearchFilterParams);
        return new Paginator($q);
    }

    /**
     * @param LicenseSearchFilterParams $licenseSearchFilterParams
     * @return int
     * @throws DaoException
     */
    public function getLicenseCount(LicenseSearchFilterParams $licenseSearchFilterParams): int
    {
        try {
            $paginator = $this->getLicenseListPaginator($licenseSearchFilterParams);
            return $paginator->count();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param array $toDeleteIds
     * @return int
     * @throws DaoException
     */
    public function deleteLicenses(array $toDeleteIds): int
    {
        try {
            $q = $this->createQueryBuilder(License::class, 'l');
            $q->delete()
                ->where($q->expr()->in('l.id', ':ids'))
                ->setParameter('ids', $toDeleteIds);
            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param $licenseName
     * @return bool
     * @throws DaoException
     */
    public function isExistingLicenseName($licenseName): bool
    {
        try {
            $q = $this->createQueryBuilder(License::class, 'l');
            $trimmed = trim($licenseName, ' ');
            $q->Where('l.name = :name');
            $q->setParameter('name', $trimmed);
            $count = $this->count($q);
            if ($count > 0) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
