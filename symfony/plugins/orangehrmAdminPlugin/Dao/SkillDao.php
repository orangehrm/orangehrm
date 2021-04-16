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
use OrangeHRM\Entity\Skill;
use OrangeHRM\ORM\Doctrine;
use \DaoException;
use \Exception;

class SkillDao
{
    /**
     * @param Skill $skill
     * @return Skill
     * @throws DaoException
     */
    public function saveSkill(Skill $skill): Skill
    {
        try {
            Doctrine::getEntityManager()->persist($skill);
            Doctrine::getEntityManager()->flush();
            return $skill;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @param int $id
     * @return Skill
     * @throws DaoException
     */
    public function getSkillById(int $id): Skill
    {
        try {
            return Doctrine::getEntityManager()->getRepository(Skill::class)->find($id);
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    public function getSkillByName($name)
    {
        try {
            $q = Doctrine_Query::create()
                ->from('Skill')
                ->where('name = ?', trim($name));

            return $q->fetchOne();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

//    public function getSkillList() {
//
//        try {
//
//            $q = Doctrine_Query::create()->from('Skill')
//                                         ->orderBy('name');
//
//            return $q->execute();
//
//        } catch (Exception $e) {
//            throw new DaoException($e->getMessage(), $e->getCode(), $e);
//        }
//    }

    /**
     * @param string $sortField
     * @param string $sortOrder
     * @param null $limit
     * @param null $offset
     * @param false $count
     * @return int|mixed|string|array
     * @throws DaoException
     */
    public function getSkillList(
        string $sortField = 's.name',
        string $sortOrder = 'ASC',
        int $limit = null,
        int $offset = null,
        $count = false
    ) {
        $sortField = ($sortField == "") ? 's.name' : $sortField;
        $sortOrder = strcasecmp($sortOrder, 'DESC') === 0 ? 'DESC' : 'ASC';

        try {
            $q = Doctrine::getEntityManager()->getRepository(Skill::class)->createQueryBuilder('s');
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
     * @param array $toDeleteIds
     * @return int
     * @throws DaoException
     */
    public function deleteSkills(array $toDeleteIds): int
    {
        try {
            $q = Doctrine::getEntityManager()->createQueryBuilder();
            $q->delete(Skill::class, 's')
                ->where($q->expr()->in('s.id', $toDeleteIds));
            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    public function isExistingSkillName($skillName)
    {
        try {
            $q = Doctrine_Query:: create()->from('Skill s')
                ->where('s.name = ?', trim($skillName));

            if ($q->count() > 0) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

}
