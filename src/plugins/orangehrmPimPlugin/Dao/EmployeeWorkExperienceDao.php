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

namespace OrangeHRM\Pim\Dao;

use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\EmpWorkExperience;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\Pim\Dto\EmployeeWorkExperienceSearchFilterParams;
use InvalidArgumentException;

class EmployeeWorkExperienceDao extends BaseDao
{
    /**
     * @param EmpWorkExperience $employeeWorkExperience
     * @return EmpWorkExperience
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
     */
    public function getEmployeeWorkExperienceById(int $empNumber, int $seqNo): ?EmpWorkExperience
    {
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
    }

    /**
     * @param int[] $ids
     * @param int $empNumber
     * @return int[]
     */
    public function getExistingEmpWorkExperienceIdsForEmpNumber(array $ids, int $empNumber): array
    {
        $qb = $this->createQueryBuilder(EmpWorkExperience::class, 'employeeWorkExperience');

        $qb->select('employeeWorkExperience.seqNo')
            ->andWhere($qb->expr()->in('employeeWorkExperience.seqNo', ':ids'))
            ->andWhere($qb->expr()->eq('employeeWorkExperience.employee', ':empNumber'))
            ->setParameter('ids', $ids)
            ->setParameter('empNumber', $empNumber);

        return $qb->getQuery()->getSingleColumnResult();
    }

    /**
     * @param int $empNumber
     * @param array $toDeleteIds
     * @return int
     */
    public function deleteEmployeeWorkExperiences(int $empNumber, array $toDeleteIds): int
    {
        $q = $this->createQueryBuilder(EmpWorkExperience::class, 'we');
        $q->delete()
            ->andWhere('we.employee = :empNumber')
            ->setParameter('empNumber', $empNumber)
            ->andWhere($q->expr()->in('we.seqNo', ':ids'))
            ->setParameter('ids', $toDeleteIds);
        return $q->getQuery()->execute();
    }

    /**
     * Search EmployeeWorkExperience
     *
     * @param EmployeeWorkExperienceSearchFilterParams $employeeWorkExperienceSearchParams
     * @return EmpWorkExperience[]
     */
    public function searchEmployeeWorkExperience(
        EmployeeWorkExperienceSearchFilterParams $employeeWorkExperienceSearchParams
    ): array {
        $paginator = $this->getSearchEmployeeWorkExperiencePaginator($employeeWorkExperienceSearchParams);
        return $paginator->getQuery()->execute();
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
     */
    public function getSearchEmployeeWorkExperiencesCount(
        EmployeeWorkExperienceSearchFilterParams $employeeWorkExperienceSearchParams
    ): int {
        $paginator = $this->getSearchEmployeeWorkExperiencePaginator($employeeWorkExperienceSearchParams);
        return $paginator->count();
    }
}
