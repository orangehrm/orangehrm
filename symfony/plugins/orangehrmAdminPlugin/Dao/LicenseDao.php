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
use OrangeHRM\ORM\Paginator;
use OrangeHRM\Core\Exception\DaoException;
use Exception;
use OrangeHRM\ORM\Doctrine;
use OrangeHRM\Entity\License;

class LicenseDao
{


    /**
     * @param License $license
     * @return License
     * @throws \DaoException
     */
    public function saveLicense(License $license): License
    {
        try {
            Doctrine::getEntityManager()->persist($license);
            Doctrine::getEntityManager()->flush();
            return $license;
        } catch (Exception $e) {
            throw new \DaoException($e->getMessage(), $e->getCode(), $e);
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
            $education = Doctrine::getEntityManager()->getRepository(License::class)->find($id);
            if ($education instanceof License) {
                return $education;
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
            $trimmed = trim($name, ' ');
            $query = Doctrine::getEntityManager()->getRepository(
                License::class
            )->createQueryBuilder('l');
            $query->andWhere('l.name = :name');
            $query->setParameter('name', $trimmed);
            return $query->getQuery()->getOneOrNullResult();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param LicenseSearchFilterParams $licenseSearchParamHolder
     * @return array
     * @throws DaoException
     */
    public function getLicenseList(LicenseSearchFilterParams $licenseSearchParamHolder): array
    {
        try {
            $paginator = $this->getLicenseListPaginator($licenseSearchParamHolder);
            return $paginator->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param LicenseSearchFilterParams $licenseSearchParamHolder
     * @return Paginator
     */
    public function getLicenseListPaginator(LicenseSearchFilterParams $licenseSearchParamHolder): Paginator
    {
        $q = Doctrine::getEntityManager()->getRepository(License::class)->createQueryBuilder('l');
        if (!is_null($licenseSearchParamHolder->getSortField())) {
            $q->addOrderBy($licenseSearchParamHolder->getSortField(), $licenseSearchParamHolder->getSortOrder());
        }
        if (!empty($licenseSearchParamHolder->getLimit())) {
            $q->setFirstResult($licenseSearchParamHolder->getOffset())
                ->setMaxResults($licenseSearchParamHolder->getLimit());
        }
        return new Paginator($q);
    }

    /**
     * @param LicenseSearchFilterParams $licenseSearchParamHolder
     * @return int
     * @throws DaoException
     */
    public function getLicenseCount(LicenseSearchFilterParams $licenseSearchParamHolder): int
    {
        try {
            $paginator = $this->getLicenseListPaginator($licenseSearchParamHolder);
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
            $q = Doctrine::getEntityManager()->createQueryBuilder();
            $q->delete(License::class, 'l')
                ->set('l.deleted', true)
                ->where($q->expr()->in('l.id', $toDeleteIds));
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
    public function isExistingLicenseName($licenseName)
    {
        try {
            $q = Doctrine::getEntityManager()->getRepository(License::class)->createQueryBuilder('l');
            $trimmed = trim($licenseName, ' ');
            $q->Where('l.name = :name');
            $q->setParameter('name', $trimmed);
            $paginator = new Paginator($q, true);
            if ($paginator->count() > 0) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

}
