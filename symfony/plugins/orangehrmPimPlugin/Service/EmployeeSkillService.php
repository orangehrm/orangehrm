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

namespace OrangeHRM\Pim\Service;

use OrangeHRM\Pim\Dao\EmployeeSkillDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Pim\Dto\EmployeeSkillSearchFilterParams;
use OrangeHRM\Core\Exception\ServiceException;
use OrangeHRM\Entity\EmployeeSkill;
use Exception;

class EmployeeSkillService
{
    /**
     * @var EmployeeSkillDao|null
     */
    private ?EmployeeSkillDao $EmployeeSkillDao = null;

    /**
     * @return EmployeeSkillDao|null
     */
    public function getEmployeeSkillDao(): EmployeeSkillDao
    {
        if (!($this->EmployeeSkillDao instanceof EmployeeSkillDao)) {
            $this->EmployeeSkillDao = new EmployeeSkillDao();
        }

        return $this->EmployeeSkillDao;
    }

    /**
     * @param $EmployeeSkillDao
     */
    public function setEmployeeSkillDao(EmployeeSkillDao $EmployeeSkillDao): void
    {
        $this->EmployeeSkillDao = $EmployeeSkillDao;
    }


    /**
     * @param EmployeeSkill $EmployeeSkill
     * @return EmployeeSkill
     * @throws DaoException
     */
    public function saveEmployeeSkill(EmployeeSkill $EmployeeSkill): EmployeeSkill
    {
        return $this->getEmployeeSkillDao()->saveEmployeeSkill($EmployeeSkill);
    }

    /**
     * @param int $empNumber
     * @param int $id
     * @return EmployeeSkill|null
     * @throws DaoException
     */
    public function getEmployeeSkillById(int $empNumber, int $id): ?EmployeeSkill
    {
        return $this->getEmployeeSkillDao()->getEmployeeSkillById($empNumber, $id);
    }

    /**
     * @param int $empNumber
     * @param array $toDeleteIds
     * @return int
     * @throws DaoException
     */
    public function deleteEmployeeSkills(int $empNumber, array $toDeleteIds): int
    {
        return $this->getEmployeeSkillDao()->deleteEmployeeSkills($empNumber, $toDeleteIds);
    }

    /**
     * @param EmployeeSkillSearchFilterParams $EmployeeSkillSearchParams
     * @return array
     * @throws ServiceException
     */
    public function searchEmployeeSkill(EmployeeSkillSearchFilterParams $EmployeeSkillSearchParams): array
    {
        try {
            return $this->getEmployeeSkillDao()->searchEmployeeSkill($EmployeeSkillSearchParams);
        } catch (Exception $e) {
            throw new ServiceException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param EmployeeSkillSearchFilterParams $EmployeeSkillSearchParams
     * @return int
     * @throws ServiceException
     */
    public function getSearchEmployeeSkillsCount(EmployeeSkillSearchFilterParams $EmployeeSkillSearchParams): int
    {
        try {
            return $this->getEmployeeSkillDao()->getSearchEmployeeSkillsCount($EmployeeSkillSearchParams);
        } catch (Exception $e) {
            throw new ServiceException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
