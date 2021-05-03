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

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use OrangeHRM\Admin\Dto\SkillSearchFilterParams;
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
     * @return object|null|Skill
     * @throws DaoException
     */
    public function getSkillById(int $id): ?Skill
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

    /**
     * Search Skill
     *
     * @param SkillSearchFilterParams $skillSearchParams
     * @return array
     * @throws DaoException
     */
    public function searchSkill(SkillSearchFilterParams $skillSearchParams): array
    {
        try {
            $q = $this->_buildSearchQuery($skillSearchParams);
            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param SkillSearchFilterParams $skillSearchParams
     * @return QueryBuilder
     */
    private function _buildSearchQuery(SkillSearchFilterParams $skillSearchParams): QueryBuilder
    {
        $q = Doctrine::getEntityManager()->getRepository(
            Skill::class
        )->createQueryBuilder('s');

        if (!is_null($skillSearchParams->getSortField())) {
            $q->addOrderBy($skillSearchParams->getSortField(), $skillSearchParams->getSortOrder());
        }
        if (!empty($skillSearchParams->getLimit())) {
            $q->setFirstResult($skillSearchParams->getOffset())
                ->setMaxResults($skillSearchParams->getLimit());
        }

        if (!empty($skillSearchParams->getName())) {
            $q->andWhere('s.name = :name');
            $q->setParameter('name', $skillSearchParams->getName());
        }
        if (!empty($skillSearchParams->getDescription())) {
            $q->andWhere('s.description = :description');
            $q->setParameter('description', $skillSearchParams->getDescription());
        }
        return $q;
    }

    /**
     * Get Employment Statuses
     *
     * @return Skill[]
     * @throws \OrangeHRM\Core\Exception\DaoException
     */
    public function getSkills(): array
    {
        try {
            return Doctrine::getEntityManager()->getRepository(
                Skill::class
            )->findAll();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get Count of Search Query
     *
     * @param SkillSearchFilterParams $skillSearchParams
     * @return int
     * @throws DaoException
     */
    public function getSearchSkillsCount(SkillSearchFilterParams $skillSearchParams): int
    {
        try {
            $q = $this->_buildSearchQuery($skillSearchParams);
            $paginator = new \OrangeHRM\ORM\Paginator($q);
            return $paginator->count();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
