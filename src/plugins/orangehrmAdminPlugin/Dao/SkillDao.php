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

namespace OrangeHRM\Admin\Dao;

use OrangeHRM\Admin\Dto\SkillSearchFilterParams;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\Skill;
use OrangeHRM\ORM\Paginator;

class SkillDao extends BaseDao
{
    /**
     * @param Skill $skill
     * @return Skill
     */
    public function saveSkill(Skill $skill): Skill
    {
        $this->persist($skill);
        return $skill;
    }

    /**
     * @param int $id
     * @return object|null|Skill
     */
    public function getSkillById(int $id): ?Skill
    {
        $skill = $this->getRepository(Skill::class)->find($id);
        if ($skill instanceof Skill) {
            return $skill;
        }
        return null;
    }

    /**
     * @param int[] $ids
     * @return int[]
     */
    public function getExistingSkillIds(array $ids): array
    {
        $qb = $this->createQueryBuilder(Skill::class, 'skill');

        $qb->select('skill.id')
            ->andWhere($qb->expr()->in('skill.id', ':ids'))
            ->setParameter('ids', $ids);

        return $qb->getQuery()->getSingleColumnResult();
    }

    /**
     * @param array $toDeleteIds
     * @return int
     */
    public function deleteSkills(array $toDeleteIds): int
    {
        $q = $this->createQueryBuilder(Skill::class, 's');
        $q->delete()
            ->where($q->expr()->in('s.id', ':ids'))
            ->setParameter('ids', $toDeleteIds);
        return $q->getQuery()->execute();
    }

    /**
     * Search Skill
     *
     * @param SkillSearchFilterParams $skillSearchParams
     * @return array
     */
    public function searchSkill(SkillSearchFilterParams $skillSearchParams): array
    {
        $paginator = $this->getSearchSkillPaginator($skillSearchParams);
        return $paginator->getQuery()->execute();
    }

    /**
     * @param SkillSearchFilterParams $skillSearchParams
     * @return Paginator
     */
    private function getSearchSkillPaginator(SkillSearchFilterParams $skillSearchParams): Paginator
    {
        $q = $this->createQueryBuilder(Skill::class, 's');
        $this->setSortingAndPaginationParams($q, $skillSearchParams);

        if (!empty($skillSearchParams->getName())) {
            $q->andWhere('s.name = :name');
            $q->setParameter('name', $skillSearchParams->getName());
        }
        if (!empty($skillSearchParams->getDescription())) {
            $q->andWhere('s.description = :description');
            $q->setParameter('description', $skillSearchParams->getDescription());
        }
        return $this->getPaginator($q);
    }

    /**
     * Get Count of Search Query
     *
     * @param SkillSearchFilterParams $skillSearchParams
     * @return int
     */
    public function getSearchSkillsCount(SkillSearchFilterParams $skillSearchParams): int
    {
        $paginator = $this->getSearchSkillPaginator($skillSearchParams);
        return $paginator->count();
    }
}
