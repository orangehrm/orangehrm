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

use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\Candidate;
use OrangeHRM\Entity\CandidateHistory;
use OrangeHRM\Entity\CandidateVacancy;
use OrangeHRM\Entity\Interview;
use OrangeHRM\ORM\ListSorter;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\Recruitment\Dto\CandidateSearchFilterParams;

class CandidateDao extends BaseDao
{
    /**
     * @param CandidateSearchFilterParams $candidateSearchFilterParams
     * @return Candidate[]
     */
    public function getCandidatesList(CandidateSearchFilterParams $candidateSearchFilterParams): array
    {
        $qb = $this->getCandidateListPaginator($candidateSearchFilterParams);
        return $qb->getQuery()->execute();
    }

    /**
     * @param CandidateSearchFilterParams $candidateSearchFilterParams
     * @return Paginator
     */
    protected function getCandidateListPaginator(CandidateSearchFilterParams $candidateSearchFilterParams): Paginator
    {
        $qb = $this->createQueryBuilder(Candidate::class, 'candidate');
        $qb->leftJoin('candidate.candidateVacancy', 'candidateVacancy');
        $qb->leftJoin('candidate.addedPerson', 'added_person');
        $qb->leftJoin('candidateVacancy.vacancy', 'vacancy');
        $qb->leftJoin('vacancy.hiringManager', 'hiringManager');

        $this->setSortingAndPaginationParams($qb, $candidateSearchFilterParams);

        if (!is_null($candidateSearchFilterParams->getCandidateId())) {
            $qb->andWhere('candidate.id = :candidateId')
                ->setParameter('candidateId', $candidateSearchFilterParams->getCandidateId());
        }
        //candidate auto-complete filter support
        if (!is_null($candidateSearchFilterParams->getCandidateName())) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('candidate.firstName', ':candidateName'),
                    $qb->expr()->like('candidate.middleName', ':candidateName'),
                    $qb->expr()->like('candidate.lastName', ':candidateName'),
                )
            );
            $qb->setParameter(
                'candidateName',
                '%' . $candidateSearchFilterParams->getCandidateName() . '%'
            );
        }
        if (!is_null($candidateSearchFilterParams->getVacancyId())) {
            $qb->andWhere('vacancy.id = :vacancyId')
                ->setParameter('vacancyId', $candidateSearchFilterParams->getVacancyId());
        }
        if (!is_null($candidateSearchFilterParams->getJobTitleId())) {
            $qb->andWhere('vacancy.jobTitle = :jobTitleId')
                ->setParameter('jobTitleId', $candidateSearchFilterParams->getJobTitleId());
        }
        if (!is_null($candidateSearchFilterParams->getHiringManagerId())) {
            $qb->andWhere('vacancy.hiringManager = :hiringManagerId')
                ->setParameter('hiringManagerId', $candidateSearchFilterParams->getHiringManagerId());
        }
        if (!is_null($candidateSearchFilterParams->getStatus())) {
            $qb->andWhere('candidateVacancy.status = :status')
                ->setParameter('status', $candidateSearchFilterParams->getStatus());
        }
        if (!empty($candidateSearchFilterParams->getKeywords())) {
            $qb->andWhere($qb->expr()->like('candidate.keywords', ':keywords'))
                ->setParameter('keywords', '%' . $candidateSearchFilterParams->getKeywords() . '%');
        }
        if (!is_null($candidateSearchFilterParams->getMethodOfApplication())) {
            $qb->andWhere('candidate.modeOfApplication = :modeOfApplication')
                ->setParameter('modeOfApplication', $candidateSearchFilterParams->getMethodOfApplication());
        }
        if (!is_null($candidateSearchFilterParams->getFromDate()) && !is_null(
            $candidateSearchFilterParams->getToDate()
        )) {
            $qb->andWhere(
                $qb->expr()->between(
                    'candidate.dateOfApplication',
                    ':fromDate',
                    ':toDate'
                )
            )
                ->setParameter('fromDate', $candidateSearchFilterParams->getFromDate())
                ->setParameter('toDate', $candidateSearchFilterParams->getToDate());
        } elseif (!is_null($candidateSearchFilterParams->getFromDate())) {
            $qb->andWhere($qb->expr()->gte('candidate.dateOfApplication', ':fromDate'));
            $qb->setParameter('fromDate', $candidateSearchFilterParams->getFromDate());
        } elseif (!is_null($candidateSearchFilterParams->getToDate())) {
            $qb->andWhere($qb->expr()->lte('candidate.dateOfApplication', ':toDate'));
            $qb->setParameter('toDate', $candidateSearchFilterParams->getToDate());
        }
        $qb->addOrderBy('candidate.lastName', ListSorter::ASCENDING);
        //else: neither fromDate nor toDate is available
        return $this->getPaginator($qb);
    }

    /**
     * @param CandidateSearchFilterParams $candidateSearchFilterParams
     * @return int
     */
    public function getCandidatesCount(CandidateSearchFilterParams $candidateSearchFilterParams): int
    {
        return $this->getCandidateListPaginator($candidateSearchFilterParams)->count();
    }

    /**
     * @param int $candidateId
     * @return Candidate|null
     */
    public function getCandidateById(int $candidateId): ?Candidate
    {
        $candidate = $this->getRepository(Candidate::class)->find($candidateId);
        if ($candidate instanceof Candidate) {
            return $candidate;
        }
        return null;
    }

    /**
     * @param int $candidateId
     * @return CandidateVacancy|null
     */
    public function getCandidateVacancyByCandidateId(int $candidateId): ?CandidateVacancy
    {
        $qb = $this->createQueryBuilder(CandidateVacancy::class, 'candidateVacancy');
        $qb->where('candidateVacancy.candidate = :candidateId');
        $qb->setParameter('candidateId', $candidateId);
        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @param Candidate $candidate
     * @return Candidate
     */
    public function saveCandidate(Candidate $candidate): Candidate
    {
        $this->persist($candidate);
        return $candidate;
    }

    /**
     *
     * @param CandidateVacancy $candidateVacancy
     * @return CandidateVacancy
     */
    public function saveCandidateVacancy(CandidateVacancy $candidateVacancy): CandidateVacancy
    {
        $this->persist($candidateVacancy);
        return $candidateVacancy;
    }

    /**
     * @param array $toBeDeletedCandidateIds
     * @return bool
     */
    public function deleteCandidates(array $toBeDeletedCandidateIds): bool
    {
        $qb = $this->createQueryBuilder(Candidate::class, 'candidate');
        $qb->delete()
            ->andWhere('candidate.id IN (:ids)')
            ->setParameter('ids', $toBeDeletedCandidateIds);

        return $qb->getQuery()->execute() > 0;
    }

    /**
     * @param int $candidateId
     * @return bool
     */
    public function deleteCandidateVacancy(int $candidateId): bool
    {
        $qb = $this->createQueryBuilder(CandidateVacancy::class, 'candidateVacancy');
        $qb->delete()
            ->where('candidateVacancy.candidate = :candidateId')
            ->setParameter('candidateId', $candidateId);
        return $qb->getQuery()->execute() > 0;
    }

    /**
     * @param CandidateHistory $candidateHistory
     * @return CandidateHistory
     */
    public function saveCandidateHistory(CandidateHistory $candidateHistory): CandidateHistory
    {
        $this->persist($candidateHistory);
        return $candidateHistory;
    }

    /**
     * @param int $candidateId
     * @return int
     */
    public function getInterviewCountByCandidateId(int $candidateId): int
    {
        $candidateInterviews = $this->getRepository(Interview::class)->findBy(['candidate'=> $candidateId]);
        return count($candidateInterviews);
    }

    /**
     * @param Interview $interview
     * @return Interview
     */
    public function saveCandidateInterview(Interview $interview): Interview
    {
        $this->persist($interview);
        return $interview;
    }

    /**
     * @return int[]
     */
    public function getCandidateIdList(): array
    {
        $q = $this->createQueryBuilder(Candidate::class, 'candidate');
        $q->select('candidate.id');
        $result = $q->getQuery()->getArrayResult();
        return array_column($result, 'id');
    }

    /**
     * @return int[]
     */
    public function getInterviewIdList(): array
    {
        $q = $this->createQueryBuilder(Interview::class, 'interview');
        $q->select('interview.id');
        $result = $q->getQuery()->getArrayResult();
        return array_column($result, 'id');
    }

    /**
     * @param int $empNumber
     * @return int[]
     */
    public function getCandidateIdListForHiringManger(int $empNumber): array
    {
        $q = $this->createQueryBuilder(CandidateVacancy::class, 'candidateVacancy');
        $q->leftJoin('candidateVacancy.candidate', 'candidate');
        $q->leftJoin('candidateVacancy.vacancy', 'vacancy');
        $q->select('candidate.id');
        $q->andWhere('vacancy.hiringManager = :empNumber');
        $q->setParameter('empNumber', $empNumber);
        $result = $q->getQuery()->getArrayResult();
        return array_column($result, 'id');
    }

    /**
     * @param int $empNumber
     * @return int[]
     */
    public function getInterviewIdListForHiringManager(int $empNumber): array
    {
        $q = $this->createQueryBuilder(Interview::class, 'interview');
        $q->leftJoin('interview.candidateVacancy', 'candidateVacancy');
        $q->leftJoin('candidateVacancy.vacancy', 'vacancy');
        $q->select('interview.id');
        $q->andWhere('vacancy.hiringManager = :empNumber');
        $q->setParameter('empNumber', $empNumber);
        $result = $q->getQuery()->getArrayResult();
        return array_column($result, 'id');
    }

    public function getInterviewById(int $interviewId)
    {
        return $this->getRepository(Interview::class)->find($interviewId);
    }
}
