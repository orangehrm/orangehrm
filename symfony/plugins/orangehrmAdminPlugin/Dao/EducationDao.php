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

use OrangeHRM\Admin\Dto\QualificationEducationSearchFilterParams;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\Core\Exception\DaoException;
use Exception;
use OrangeHRM\Entity\Education;

class EducationDao extends BaseDao
{
    /**
     * @param Education $education
     * @return Education
     * @throws \DaoException
     */
    public function saveEducation(Education $education): Education
    {
        try {
            $this->persist($education);
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
            $education = $this->getRepository(Education::class)->find($id);
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
            $query = $this->getRepository(Education::class)->createQueryBuilder('e');
            $trimmed = trim($name, ' ');
            $query->andWhere('e.name = :name');
            $query->setParameter('name', $trimmed);
            return $query->getQuery()->getOneOrNullResult();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param QualificationEducationSearchFilterParams $educationSearchParamHolder
     * @return array
     * @throws DaoException
     */
    public function getEducationList(QualificationEducationSearchFilterParams $educationSearchParamHolder): array
    {
        try {
            $paginator = $this->getEducationListPaginator($educationSearchParamHolder);
            return $paginator->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param QualificationEducationSearchFilterParams $educationSearchParamHolder
     * @return Paginator
     */
    public function getEducationListPaginator(QualificationEducationSearchFilterParams $educationSearchParamHolder
    ): Paginator {
        $q = $this->getRepository(Education::class)->createQueryBuilder('e');
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
     * @param QualificationEducationSearchFilterParams $educationSearchParamHolder
     * @return int
     * @throws DaoException
     */
    public function getEducationCount(QualificationEducationSearchFilterParams $educationSearchParamHolder): int
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
            $q = $this->createQueryBuilder(Education::class, 'e');
            $q->delete(Education::class, 'e')
                ->set('e.deleted', true)
                ->where($q->expr()->in('e.id', $toDeleteIds));
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
            $q = $this->getRepository(Education::class)->createQueryBuilder('e');
            $trimmed = trim($educationName, ' ');
            $q->Where('e.name = :name');
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
