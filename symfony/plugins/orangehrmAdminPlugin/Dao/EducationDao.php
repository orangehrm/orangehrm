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

use OrangeHRM\Admin\Dto\UserSearchFilterParams;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\Core\Exception\DaoException;
use Exception;
use OrangeHRM\ORM\Doctrine;
use OrangeHRM\Entity\Education;

class EducationDao
{
    /**
     * @param Education $education
     * @return Education
     * @throws \DaoException
     */
    public function saveEducation(Education $education): Education
    {
        try {
            Doctrine::getEntityManager()->persist($education);
            Doctrine::getEntityManager()->flush();
            return $education;
        } catch (Exception $e) {
            throw new \DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * get education names according to the Id
     * @param int $id
     * @return Education|null
     * @throws DaoException
     */
    public function getEducationById(int $id): ?Education
    {
        try {
            $education = Doctrine::getEntityManager()->getRepository(Education::class)->find($id);
            if ($education instanceof Education) {
                return $education;
            }
            return null;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param $name
     * @return Education|null
     * @throws DaoException
     */
    public function getEducationByName(string $name): ?Education
    {
        try {
            $trimmed = trim($name, ' ');
            $query = Doctrine::getEntityManager()->getRepository(
                Education::class
            )->createQueryBuilder('e');
            $query->andWhere('e.name = :name');
            $query->setParameter('name', $trimmed);
            return $query->getQuery()->getOneOrNullResult();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param UserSearchFilterParams $educationSearchParamHolder
     * @return array
     * @throws DaoException
     */
    public function getEducationList(UserSearchFilterParams $educationSearchParamHolder): array
    {
        try {
            $paginator = $this->getEducationListPaginator($educationSearchParamHolder);
            return $paginator->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param UserSearchFilterParams $educationSearchParamHolder
     * @return Paginator
     */
    public function getEducationListPaginator(UserSearchFilterParams $educationSearchParamHolder): Paginator
    {
        $q = Doctrine::getEntityManager()->getRepository(Education::class)->createQueryBuilder('e');
        if (!is_null($educationSearchParamHolder->getSortField())) {
            $q->addOrderBy($educationSearchParamHolder->getSortField(), $educationSearchParamHolder->getSortOrder());
        }
        if (!empty($educationSearchParamHolder->getLimit())) {
            $q->setFirstResult($educationSearchParamHolder->getOffset())
                ->setMaxResults($educationSearchParamHolder->getLimit());
        }
        return new Paginator($q);
    }

    /**
     * @param UserSearchFilterParams $educationSearchParamHolder
     * @return int
     * @throws DaoException
     */
    public function getEducationCount(UserSearchFilterParams $educationSearchParamHolder): int
    {
        try {
            $paginator = $this->getEducationListPaginator($educationSearchParamHolder);
            return $paginator->count();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Soft Delete Education field
     * @param array $toDeleteIds
     * @return int
     * @throws DaoException
     */
    public function deleteEducations(array $toDeleteIds): int
    {
        try {
            $q = Doctrine::getEntityManager()->createQueryBuilder();
            $q->delete(Education::class, 'E')
                ->set('E.deleted', true)
                ->where($q->expr()->in('E.id', $toDeleteIds));
            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param $educationName
     * @return bool
     * @throws DaoException
     */
    public function isExistingEducationName(string $educationName): bool
    {
        try {
            $q = Doctrine::getEntityManager()->getRepository(Education::class)->createQueryBuilder('e');
            $trimmed = trim($educationName, ' ');
            $q->Where('e.name = :name');
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
