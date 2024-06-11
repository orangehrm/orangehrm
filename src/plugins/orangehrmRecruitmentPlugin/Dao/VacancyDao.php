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

namespace OrangeHRM\Recruitment\Dao;

use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\JobTitle;
use OrangeHRM\Entity\Vacancy;
use OrangeHRM\ORM\Doctrine;
use OrangeHRM\ORM\ListSorter;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\ORM\QueryBuilderWrapper;
use OrangeHRM\Recruitment\Dto\VacancySearchFilterParams;

class VacancyDao extends BaseDao
{
    /**
     * @param VacancySearchFilterParams $vacancySearchFilterParamHolder
     * @return Vacancy[]
     */
    public function getVacancies(VacancySearchFilterParams $vacancySearchFilterParamHolder): array
    {
        $qb = $this->getVacancyListPaginator($vacancySearchFilterParamHolder);
        return $qb->getQuery()->execute();
    }

    /**
     * @param VacancySearchFilterParams $vacancySearchFilterParams
     * @return Paginator
     */
    private function getVacancyListPaginator(VacancySearchFilterParams $vacancySearchFilterParams): Paginator
    {
        $q = $this->getVacanciesQueryBuilderWrapper($vacancySearchFilterParams)->getQueryBuilder();
        $this->setSortingAndPaginationParams($q, $vacancySearchFilterParams);
        return $this->getPaginator($q);
    }

    /**
     * @param VacancySearchFilterParams $vacancySearchFilterParamHolder
     * @return QueryBuilderWrapper
     */
    protected function getVacanciesQueryBuilderWrapper(
        VacancySearchFilterParams $vacancySearchFilterParamHolder
    ): QueryBuilderWrapper {
        $q = $this->createQueryBuilder(Vacancy::class, 'vacancy');
        $q->leftJoin('vacancy.jobTitle', 'jobTitle');
        $q->leftJoin('vacancy.hiringManager', 'hiringManager');

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
        if (!is_null($vacancySearchFilterParamHolder->getVacancyIds())) {
            $q->andWhere($q->expr()->in('vacancy.id', ':vacancyIds'))
                ->setParameter(
                    'vacancyIds',
                    $vacancySearchFilterParamHolder->getVacancyIds()
                );
        }
        if (!is_null($vacancySearchFilterParamHolder->getStatus())) {
            $q->andWhere('vacancy.status = :status')
                ->setParameter(
                    'status',
                    $vacancySearchFilterParamHolder->getStatus()
                );
        }
        if (!is_null($vacancySearchFilterParamHolder->getName())) {
            $q->andWhere(
                $q->expr()->like('vacancy.name', ':name')
            )->setParameter('name', '%' . $vacancySearchFilterParamHolder->getName() . '%');
        }
        if (!is_null($vacancySearchFilterParamHolder->isPublished())) {
            $q->andWhere('vacancy.isPublished = :isPublished')
                ->setParameter('isPublished', $vacancySearchFilterParamHolder->isPublished());
        }
        return $this->getQueryBuilderWrapper($q);
    }


    /**
     * @returns doctrine collection
     */
    public function saveJobVacancy(Vacancy $jobVacancy): Vacancy
    {
        $this->persist($jobVacancy);
        return $jobVacancy;
    }

    /**
     * @param $vacancySearchFilterParamHolder
     * @return int
     */
    public function getVacanciesCount($vacancySearchFilterParamHolder): int
    {
        return $this->getVacancyListPaginator($vacancySearchFilterParamHolder)->count();
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
     * @param int[] $ids
     * @return int[]
     */
    public function getExistingVacancyIds(array $ids): array
    {
        $qb = $this->createQueryBuilder(Vacancy::class, 'vacancy');

        $qb->select('vacancy.id')
            ->andWhere($qb->expr()->in('vacancy.id', ':ids'))
            ->setParameter('ids', $ids);

        return $qb->getQuery()->getSingleColumnResult();
    }

    /**
     * @param array $toBeDeletedVacancyIds
     * @return bool
     */

    public function deleteVacancies(array $toBeDeletedVacancyIds): bool
    {
        $qr = $this->createQueryBuilder(Vacancy::class, 'vacancy');
        $qr->delete()
            ->andWhere($qr->expr()->in('vacancy.id', ':ids'))
            ->setParameter('ids', $toBeDeletedVacancyIds);
        return $qr->getQuery()->execute() > 0;
    }

    /**
     * @param VacancySearchFilterParams $vacancySearchFilterParams
     * @return int[]
     */
    public function getHiringManagerEmpNumberList(VacancySearchFilterParams $vacancySearchFilterParams): array
    {
        $qb = $this->getVacanciesQueryBuilderWrapper($vacancySearchFilterParams)->getQueryBuilder();
        $qb->select('hiringManager.empNumber');
        $qb->groupBy('hiringManager.empNumber');
        return $qb->getQuery()->execute();
    }

    /**
     * @return int[]
     */
    public function getVacancyIdList(): array
    {
        $q = $this->createQueryBuilder(Vacancy::class, 'vacancy');
        $q->select('vacancy.id');
        $result = $q->getQuery()->getArrayResult();
        return array_column($result, 'id');
    }

    /**
     * @param int $empNumber
     * @return int[]
     */
    public function getVacancyIdListForHiringManager(int $empNumber): array
    {
        $q = $this->createQueryBuilder(Vacancy::class, 'vacancy');
        $q->select('vacancy.id');
        $q->andWhere('vacancy.hiringManager = :empNumber');
        $q->setParameter('empNumber', $empNumber);
        $result = $q->getQuery()->getArrayResult();
        return array_column($result, 'id');
    }

    /**
     * @param int|null $empNumber
     * @return bool
     */
    public function isHiringManager(?int $empNumber): bool
    {
        if (is_null($empNumber)) {
            return false;
        }
        $q = $this->createQueryBuilder(Vacancy::class, 'vacancy')
            ->andWhere('vacancy.hiringManager = :empNumber')
            ->setParameter('empNumber', $empNumber);
        return $this->getPaginator($q)->count() > 0;
    }

    /**
     * @return Vacancy[]
     */
    public function getPublishedVacancyList(): array
    {
        $qb = $this->createQueryBuilder(Vacancy::class, 'vacancy');
        $qb->andWhere('vacancy.isPublished = :isPublished')
            ->setParameter('isPublished', true)
            ->andWhere('vacancy.status = :status')
            ->setParameter('status', true);
        $qb->addOrderBy('vacancy.updatedTime', ListSorter::DESCENDING);
        return $qb->getQuery()->execute();
    }

    /**
     * @param int $jobTitleId
     * @return bool
     */
    public function isActiveJobTitle(int $jobTitleId): bool
    {
        return $this->getRepository(JobTitle::class)->findOneBy(
            [
                    'id' => $jobTitleId,
                    'isDeleted' => false
                ]
        ) instanceof JobTitle;
    }

    /**
     * @param int $hiringManagerId
     * @return bool
     */
    public function isActiveHiringManger(int $hiringManagerId): bool
    {
        return $this->getRepository(Employee::class)->findOneBy(
            [
                    'empNumber' => $hiringManagerId,
                    'employeeTerminationRecord' => null
                ]
        ) instanceof Employee;
    }
}
