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

namespace OrangeHRM\Admin\Service;

use OrangeHRM\Admin\Dao\SkillDao;
use \DaoException;
use OrangeHRM\Entity\Skill;

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
     * @param $skillDao
     */
    public function setSkillDao(SkillDao $skillDao): void
    {
        $this->skillDao = $skillDao;
    }


    /**
     * @param Skill $skill
     * @return Skill
     * @throws DaoException
     */
    public function saveSkill(Skill $skill): Skill
    {
        return $this->getSkillDao()->saveSkill($skill);
    }

//    /**
//     * Retrieves a skill by ID
//     *
//     * @version 2.6.12
//     * @param int $id
//     * @return Skill An instance of Skill or NULL
//     */

    /**
     * @param $id
     * @return mixed
     * @throws DaoException
     */
    public function getSkillById(int $id): Skill
    {
        return $this->getSkillDao()->getSkillById($id);
    }

//    /**
//     * Retrieves a skill by name
//     *
//     * Case insensitive
//     *
//     * @version 2.6.12
//     * @param string $name
//     * @return Skill An instance of Skill or false
//     */

    /**
     * @param string $name
     * @return Skill
     * @throws DaoException
     */
    public function getSkillByName(string $name): Skill
    {
        return $this->getSkillDao()->getSkillByName($name);
    }

//    /**
//     * Retrieves all skills ordered by name
//     *
//     * @return Doctrine_Collection A doctrine collection of Skill objects
//     * @version 2.6.12
//     */
//    public function getSkillList(): array
//    {
//        return $this->getSkillDao()->getSkillList();
//    }
    /**
     * @param string $sortField
     * @param string $sortOrder
     * @param int|null $limit
     * @param int|null $offset
     * @param false $count
     * @return array|int|mixed|string
     * @throws DaoException
     */
    public function getSkillList(
        string $sortField = 's.name',
        string $sortOrder = 'ASC',
        int $limit = null,
        int $offset = null,
        $count = false
    ) {
        return $this->getSkillDao()->getSkillList(
            $sortField,
            $sortOrder,
            $limit,
            $offset,
            $count
        );
    }


//    /**
//     * Deletes skills
//     *
//     * @param array $toDeleteIds An array of IDs to be deleted
//     * @return int Number of records deleted
//     * @version 2.6.12
//     */

    /**
     * @param array $toDeleteIds
     * @return int
     * @throws DaoException
     */
    public function deleteSkills(array $toDeleteIds): int
    {
        return $this->getSkillDao()->deleteSkills($toDeleteIds);
    }

    /**
     * Checks whether the given skill name exists
     *
     * Case insensitive
     *
     * @param string $skillName Skill name that needs to be checked
     * @return boolean
     * @version 2.6.12
     */
    public function isExistingSkillName(string $skillName): bool
    {
        return $this->getSkillDao()->isExistingSkillName($skillName);
    }


}
