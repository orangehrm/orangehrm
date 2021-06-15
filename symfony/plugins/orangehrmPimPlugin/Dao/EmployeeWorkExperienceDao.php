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

use Exception;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\EmpWorkExperience;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\Pim\Dto\EmployeeWorkExperienceSearchFilterParams;
use InvalidArgumentException;

class EmployeeWorkExperienceDao extends BaseDao
{
    /**
     * @param EmpWorkExperience $employeeWorkExperience
     * @return EmpWorkExperience
     * @throws DaoException
     */
    public function saveEmployeeWorkExperience(EmpWorkExperience $employeeWorkExperience): EmpWorkExperience
    {
        // increment seqNo if not set explicitly
        if ($employeeWorkExperience->getSeqNo() === 0) {
            $q = $this->createQueryBuilder(EmpWorkExperience::class, 'we');
            $empNumber = $employeeWorkExperience->getEmployee()->getEmpNumber();
            $q->andWhere('we.employee = :empNumber')
                ->setParameter('empNumber', $empNumber);
            $q->select($q->expr()->max('we.seqNo'));
            $maxSeqNo = $q->getQuery()->getSingleScalarResult();
            $seqNo = 1;
            if (!is_null($maxSeqNo)) {
                $seqNo += intval($maxSeqNo);
            }
            $employeeWorkExperience->setSeqNo($seqNo);
        }
        $seqNo = intval($employeeWorkExperience->getSeqNo());
        if (!(strlen((string)$seqNo) <= 10 && $seqNo > 0)) {
            throw new InvalidArgumentException('Invalid `seqNo`');
        }

        $this->persist($employeeWorkExperience);
        return $employeeWorkExperience;
    }

    /**
     * @param int $empNumber
     * @param int $seqNo
     * @return EmpWorkExperience|null
     * @throws DaoException
     */
    public function getEmployeeWorkExperienceById(int $empNumber, int $seqNo): ?EmpWorkExperience
    {
        try {
            $employeeWorkExperience = $this->getRepository(EmpWorkExperience::class)->findOneBy(
                [
                    'employee' => $empNumber,
                    'seqNo' => $seqNo,
                ]
            );
            if ($employeeWorkExperience instanceof EmpWorkExperience) {
                return $employeeWorkExperience;
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
    public function deleteEmployeeWorkExperiences(int $empNumber, array $toDeleteIds): int
    {
        try {
            $q = $this->createQueryBuilder(EmpWorkExperience::class, 'we');
            $q->delete()
                ->andWhere('we.employee = :empNumber')
                ->setParameter('empNumber', $empNumber)
                ->andWhere($q->expr()->in('we.seqNo', ':ids'))
                ->setParameter('ids', $toDeleteIds);
            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Search EmployeeWorkExperience
     *
     * @param EmployeeWorkExperienceSearchFilterParams $employeeWorkExperienceSearchParams
     * @return EmpWorkExperience[]
     * @throws DaoException
     */
    public function searchEmployeeWorkExperience(
        EmployeeWorkExperienceSearchFilterParams $employeeWorkExperienceSearchParams
    ): array {
        try {
            $paginator = $this->getSearchEmployeeWorkExperiencePaginator($employeeWorkExperienceSearchParams);
            return $paginator->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param EmployeeWorkExperienceSearchFilterParams $employeeWorkExperienceSearchParams
     * @return Paginator
     */
    private function getSearchEmployeeWorkExperiencePaginator(
        EmployeeWorkExperienceSearchFilterParams $employeeWorkExperienceSearchParams
    ): Paginator {
        $q = $this->createQueryBuilder(EmpWorkExperience::class, 'we');
        $this->setSortingAndPaginationParams($q, $employeeWorkExperienceSearchParams);

        $q->andWhere('we.employee = :empNumber')
            ->setParameter('empNumber', $employeeWorkExperienceSearchParams->getEmpNumber());
        return $this->getPaginator($q);
    }

    /**
     * Get Count of Search Query
     *
     * @param EmployeeWorkExperienceSearchFilterParams $employeeWorkExperienceSearchParams
     * @return int
     * @throws DaoException
     */
    public function getSearchEmployeeWorkExperiencesCount(
        EmployeeWorkExperienceSearchFilterParams $employeeWorkExperienceSearchParams
    ): int {
        try {
            $paginator = $this->getSearchEmployeeWorkExperiencePaginator($employeeWorkExperienceSearchParams);
            return $paginator->count();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
