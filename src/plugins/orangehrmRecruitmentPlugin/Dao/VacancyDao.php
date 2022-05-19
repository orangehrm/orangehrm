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

namespace OrangeHRM\Recruitment\Dao;

use Exception;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\Vacancy;
use OrangeHRM\ORM\Doctrine;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\Recruitment\Dto\VacancySearchFilterParams;

/**
 * VacancyDao for CRUD operation
 *
 */
class VacancyDao extends BaseDao
{
    /**
     * @param VacancySearchFilterParams $vacancySearchFilterParamHolder
     * @return Vacancy[]
     */
    public function getVacancies(VacancySearchFilterParams $vacancySearchFilterParamHolder): array
    {
        $qb = $this->getVacanciesPaginator($vacancySearchFilterParamHolder);
        return $qb->getQuery()->execute();
    }

    /**
     * @param VacancySearchFilterParams $vacancySearchFilterParamHolder
     * @return Paginator
     */
    protected function getVacanciesPaginator(VacancySearchFilterParams $vacancySearchFilterParamHolder): Paginator
    {
        $q = $this->createQueryBuilder(Vacancy::class, 'vacancy');
        $q->leftJoin('vacancy.jobTitle', 'jobTitle');
        $q->leftJoin('vacancy.hiringManager', 'hiringManager');

        $this->setSortingAndPaginationParams($q, $vacancySearchFilterParamHolder);

        if (!is_null($vacancySearchFilterParamHolder->getJobTitleId())) {
            $q->andWhere('jobTitle.id = :jobTitleCode')
                ->setParameter(
                    'jobTitleCode',
                    $vacancySearchFilterParamHolder->getJobTitleId()
                );
        }
        if (!is_null($vacancySearchFilterParamHolder->getEmpNumber())) {
            $q->andWhere('hiringManager.empNumber  = :hiringManagerId')
                ->setParameter(
                    'hiringManagerId',
                    $vacancySearchFilterParamHolder->getEmpNumber()
                );
        }
        if (!is_null($vacancySearchFilterParamHolder->getVacancyId())) {
            $q->andWhere('vacancy.id = :vacancyId')
                ->setParameter(
                    'vacancyId',
                    $vacancySearchFilterParamHolder->getVacancyId()
                );
        }
        if (!is_null($vacancySearchFilterParamHolder->getStatus())) {
            $q->andWhere('vacancy.status = :status')
                ->setParameter(
                    'status',
                    $vacancySearchFilterParamHolder->getStatus()
                );
        }

        return $this->getPaginator($q);
    }


    /**
     * Retrieve vacancy list
     * @returns doctrine collection
     * @throws DaoException
     */
    public function saveJobVacancy(Vacancy $jobVacancy): Vacancy
    {
        try {
            $this->persist($jobVacancy);
            return $jobVacancy;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @param $vacancySearchFilterParamHolder
     * @return int
     */
    public function searchVacanciesCount($vacancySearchFilterParamHolder): int
    {
        return $this->getVacanciesPaginator($vacancySearchFilterParamHolder)->count();
    }

    /**
     * @param int $vacancyId
     * @return Vacancy|null
     */
    public function getVacancyById(int $vacancyId): ?Vacancy
    {
        $vacancy = $this->getRepository(Vacancy::class)->find($vacancyId);
        if ($vacancy instanceof Vacancy) {
            return $vacancy;
        }
        return null;
    }

    /**
     * @param array $toBeDeletedVacancyIds
     * @return bool
     */

    public function deleteVacancies(array $toBeDeletedVacancyIds): bool
    {
        $qr = $this->createQueryBuilder(Vacancy::class, 'v');
        $qr->delete()
            ->andWhere('v.id IN (:ids)')
            ->setParameter('ids', $toBeDeletedVacancyIds);

        $result = $qr->getQuery()->execute();
        if ($result > 0) {
            return true;
        }
        return false;
    }

    /**
     * @return Vacancy[]
     */
    public function getVacanciesOrderByHiringManagers(): array
    {
        $qb = $this->createQueryBuilder(Vacancy::class, 'vacancy');
        $qb->leftJoin('vacancy.hiringManager', 'hiringManager');
        $qb->groupBy('hiringManager.empNumber');
        return $qb->getQuery()->execute();
    }
}
