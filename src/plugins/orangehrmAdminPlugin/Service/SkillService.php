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

namespace OrangeHRM\Admin\Service;

use OrangeHRM\Admin\Dao\SkillDao;
use OrangeHRM\Admin\Dto\SkillSearchFilterParams;
use OrangeHRM\Core\Exception\ServiceException;
use OrangeHRM\Entity\Skill;
use Exception;

class SkillService
{
    /**
     * @var SkillDao|null
     */
    private ?SkillDao $skillDao = null;

    /**
     * @return SkillDao|null
     */
    public function getSkillDao(): SkillDao
    {
        if (!($this->skillDao instanceof SkillDao)) {
            $this->skillDao = new SkillDao();
        }

        return $this->skillDao;
    }

    /**
     * @param SkillDao $skillDao
     */
    public function setSkillDao(SkillDao $skillDao): void
    {
        $this->skillDao = $skillDao;
    }


    /**
     * @param Skill $skill
     * @return Skill
     */
    public function saveSkill(Skill $skill): Skill
    {
        return $this->getSkillDao()->saveSkill($skill);
    }

    /**
     * @param int $id
     * @return Skill
     */
    public function getSkillById(int $id): ?Skill
    {
        return $this->getSkillDao()->getSkillById($id);
    }

    /**
     * @param array $toDeleteIds
     * @return int
     */
    public function deleteSkills(array $toDeleteIds): int
    {
        return $this->getSkillDao()->deleteSkills($toDeleteIds);
    }

    /**
     * @param SkillSearchFilterParams $skillSearchParams
     * @return array
     * @throws ServiceException
     */
    public function searchSkill(SkillSearchFilterParams $skillSearchParams): array
    {
        try {
            return $this->getSkillDao()->searchSkill($skillSearchParams);
        } catch (Exception $e) {
            throw new ServiceException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param SkillSearchFilterParams $skillSearchParams
     * @return int
     * @throws ServiceException
     */
    public function getSearchSkillsCount(SkillSearchFilterParams $skillSearchParams): int
    {
        try {
            return $this->getSkillDao()->getSearchSkillsCount($skillSearchParams);
        } catch (Exception $e) {
            throw new ServiceException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
