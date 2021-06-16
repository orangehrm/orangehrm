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

namespace OrangeHRM\Pim\Dao;

use Doctrine\ORM\Query\Expr;
use Exception;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\EmployeeSkill;
use OrangeHRM\Entity\Skill;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\Pim\Dto\EmployeeAllowedSkillSearchFilterParams;
use OrangeHRM\Pim\Dto\EmployeeSkillSearchFilterParams;

class EmployeeSkillDao extends BaseDao
{
    /**
     * @param EmployeeSkill $employeeSkill
     * @return EmployeeSkill
     * @throws DaoException
     */
    public function saveEmployeeSkill(EmployeeSkill $employeeSkill): EmployeeSkill
    {
        try {
            $this->persist($employeeSkill);
            return $employeeSkill;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param int $empNumber
     * @param int $skillId
     * @return EmployeeSkill|null
     * @throws DaoException
     */
    public function getEmployeeSkillById(int $empNumber, int $skillId): ?EmployeeSkill
    {
        try {
            $employeeSkill = $this->getRepository(EmployeeSkill::class)->findOneBy(
                [
                    'employee' => $empNumber,
                    'skill' => $skillId,
                ]
            );
            if ($employeeSkill instanceof EmployeeSkill) {
                return $employeeSkill;
            }
            return null;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param int $empNumber
     * @param array $toDeleteIds
     * @return int
     * @throws DaoException
     */
    public function deleteEmployeeSkills(int $empNumber, array $toDeleteIds): int
    {
        try {
            $q = $this->createQueryBuilder(EmployeeSkill::class, 'es');
            $q->delete()
                ->andWhere('es.employee = :empNumber')
                ->andWhere($q->expr()->in('es.skill', ':ids'))
                ->setParameter('ids', $toDeleteIds)
                ->setParameter('empNumber', $empNumber);
            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Search EmployeeSkill
     *
     * @param EmployeeSkillSearchFilterParams $employeeSkillSearchParams
     * @return EmployeeSkill[]
     * @throws DaoException
     */
    public function searchEmployeeSkill(EmployeeSkillSearchFilterParams $employeeSkillSearchParams): array
    {
        try {
            $paginator = $this->getSearchEmployeeSkillPaginator($employeeSkillSearchParams);
            return $paginator->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param EmployeeSkillSearchFilterParams $employeeSkillSearchParams
     * @return Paginator
     */
    private function getSearchEmployeeSkillPaginator(
        EmployeeSkillSearchFilterParams $employeeSkillSearchParams
    ): Paginator {
        $q = $this->createQueryBuilder(EmployeeSkill::class, 'es');
        $q->leftJoin('es.skill', 's');
        $this->setSortingAndPaginationParams($q, $employeeSkillSearchParams);

        $q->andWhere('es.employee = :empNumber')
            ->setParameter('empNumber', $employeeSkillSearchParams->getEmpNumber());

        return $this->getPaginator($q);
    }

    /**
     * Get Count of Search Query
     *
     * @param EmployeeSkillSearchFilterParams $employeeSkillSearchParams
     * @return int
     * @throws DaoException
     */
    public function getSearchEmployeeSkillsCount(EmployeeSkillSearchFilterParams $employeeSkillSearchParams): int
    {
        try {
            $paginator = $this->getSearchEmployeeSkillPaginator($employeeSkillSearchParams);
            return $paginator->count();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param EmployeeAllowedSkillSearchFilterParams $skillSearchFilterParams
     * @return Skill[]
     * @throws DaoException
     */
    public function getEmployeeAllowedSkills(EmployeeAllowedSkillSearchFilterParams $skillSearchFilterParams): array
    {
        try {
            $paginator = $this->getEmployeeAllowedSkillsPaginator($skillSearchFilterParams);
            return $paginator->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param EmployeeAllowedSkillSearchFilterParams $skillSearchFilterParams
     * @return int
     * @throws DaoException
     */
    public function getEmployeeAllowedSkillsCount(EmployeeAllowedSkillSearchFilterParams $skillSearchFilterParams): int
    {
        try {
            $paginator = $this->getEmployeeAllowedSkillsPaginator($skillSearchFilterParams);
            return $paginator->count();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param EmployeeAllowedSkillSearchFilterParams $skillSearchFilterParams
     * @return Paginator
     */
    private function getEmployeeAllowedSkillsPaginator(
        EmployeeAllowedSkillSearchFilterParams $skillSearchFilterParams
    ): Paginator {
        $q = $this->createQueryBuilder(Skill::class, 's');
        $q->leftJoin('s.employeeSkills', 'es', Expr\Join::WITH, 'es.employee = :empNumber');
        $this->setSortingAndPaginationParams($q, $skillSearchFilterParams);

        $q->andWhere($q->expr()->isNull('es.employee'));
        $q->setParameter('empNumber', $skillSearchFilterParams->getEmpNumber());

        return $this->getPaginator($q);
    }
}
