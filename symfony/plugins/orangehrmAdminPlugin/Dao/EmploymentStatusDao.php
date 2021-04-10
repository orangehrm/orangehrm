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

use Doctrine\ORM\Tools\Pagination\Paginator;
use OrangeHRM\Entity\EmploymentStatus;
use OrangeHRM\Entity\JobCategory;
use OrangeHRM\ORM\Doctrine;
use \DaoException;
use \Exception;

class EmploymentStatusDao{// extends \BaseDao {

        /**
         * @param string $sortField
         * @param string $sortOrder
         * @param null $limit
         * @param null $offset
         * @param false $count
         * @return int|mixed|string
         * @throws DaoException
         */
        public function getEmploymentStatusList(
            $sortField = 'es.name',
            $sortOrder = 'ASC',
            $limit = null,
            $offset = null,
            $count = false
        ) {
            $sortField = ($sortField == "") ? 'es.name' : $sortField;
            $sortOrder = strcasecmp($sortOrder, 'DESC') === 0 ? 'DESC' : 'ASC';

            try {
                $q = Doctrine::getEntityManager()->getRepository(EmploymentStatus::class)->createQueryBuilder('es');
                $q->addOrderBy($sortField, $sortOrder);
                if (!empty($limit)) {
                    $q->setFirstResult($offset)
                        ->setMaxResults($limit);
                }
                if ($count) {
                    $paginator = new Paginator($q, true);
                    return count($paginator);
                }
                return $q->getQuery()->execute();
            } catch (Exception $e) {
                throw new DaoException($e->getMessage());
            }
        }

    /**
     * @param $id
     * @return object|null
     * @throws DaoException
     */
	public function getEmploymentStatusById($id) {
        try {
            return Doctrine::getEntityManager()->getRepository(EmploymentStatus::class)->find($id);
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
	}

    /**
     * @param EmploymentStatus $employmentStatus
     * @return EmploymentStatus
     * @throws DaoException
     */
    public function saveEmploymentStatus(EmploymentStatus $employmentStatus): EmploymentStatus
    {
        try {
            Doctrine::getEntityManager()->persist($employmentStatus);
            Doctrine::getEntityManager()->flush();
            return $employmentStatus;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @param array $toBeDeletedEmploymentStatusIds
     * @return int|mixed|string
     * @throws DaoException
     */
    public function deleteEmploymentStatus(array $toBeDeletedEmploymentStatusIds)
    {
        try {
            $q = Doctrine::getEntityManager()->createQueryBuilder();
            $q->delete(EmploymentStatus::class, 'jc')
                ->where($q->expr()->in('jc.id', $toBeDeletedEmploymentStatusIds));
            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }
}

