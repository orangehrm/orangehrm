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

use Exception;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\Candidate;
use OrangeHRM\Entity\CandidateHistory;
use OrangeHRM\Entity\CandidateVacancy;
use OrangeHRM\Entity\Interview;
use OrangeHRM\Entity\InterviewInterviewer;
use OrangeHRM\Entity\WorkflowStateMachine;
use OrangeHRM\ORM\ListSorter;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\Recruitment\Dto\CandidateHistorySearchFilterParams;
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
        if (!is_null($candidateSearchFilterParams->getCandidateIds())) {
            $qb->andWhere($qb->expr()->in('candidate.id', ':candidateIds'))
                ->setParameter('candidateIds', $candidateSearchFilterParams->getCandidateIds());
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
        if (!is_null($candidateSearchFilterParams->isConsentToKeepData())) {
            $qb->andWhere($qb->expr()->eq('candidate.consentToKeepData', ':consentToKeepData'))
                ->setParameter('consentToKeepData', $candidateSearchFilterParams->isConsentToKeepData());
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
     * @param int[] $ids
     * @return int[]
     */
    public function getExistingCandidateIds(array $ids): array
    {
        $qb = $this->createQueryBuilder(Candidate::class, 'candidate');

        $qb->select('candidate.id')
            ->andWhere($qb->expr()->in('candidate.id', ':ids'))
            ->setParameter('ids', $ids);

        return $qb->getQuery()->getSingleColumnResult();
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
     * @param int $vacancyId
     * @return int
     */
    public function getInterviewCountByCandidateIdAndVacancyId(int $candidateId, int $vacancyId): int
    {
        $qb = $this->createQueryBuilder(CandidateHistory::class, 'candidateHistory');
        $qb->leftJoin('candidateHistory.interview', 'interview');
        $qb->andWhere($qb->expr()->isNotNull('interview.candidateVacancy'));
        $qb->andWhere('candidateHistory.candidate = :candidateId')
            ->setParameter('candidateId', $candidateId);
        $qb->andWhere('candidateHistory.vacancy = :vacancyId')
            ->setParameter('vacancyId', $vacancyId);
        $qb->andWhere('candidateHistory.action = :actionId')
            ->setParameter('actionId', WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_SHEDULE_INTERVIEW);
        return $this->getPaginator($qb)->count();
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

    /**
     * @param int $interviewId
     * @return Interview|null
     */
    public function getInterviewById(int $interviewId): ?Interview
    {
        return $this->getRepository(Interview::class)->find($interviewId);
    }

    /**
     * @param int $empNumber
     * @return int[]
     */
    public function getCandidateListForInterviewer(int $empNumber): array
    {
        $q = $this->createQueryBuilder(InterviewInterviewer::class, 'interviewerInterview');
        $q->leftJoin('interviewerInterview.interview', 'interview');
        $q->leftJoin('interview.candidate', 'candidate');
        $q->select('candidate.id');
        $q->andWhere('interviewerInterview.interviewer = :empNumber');
        $q->andWhere($q->expr()->isNotNull('interview.candidateVacancy'));
        $q->setParameter('empNumber', $empNumber);
        $result = $q->getQuery()->getArrayResult();
        return array_column($result, 'id');
    }

    /**
     * @param int $empNumber
     * @return int[]
     */
    public function getInterviewListForInterviewer(int $empNumber): array
    {
        $q = $this->createQueryBuilder(InterviewInterviewer::class, 'interviewInterviewer');
        $q->leftJoin('interviewInterviewer.interview', 'interview');
        $q->select('interview.id');
        $q->andWhere('interviewInterviewer.interviewer = :empNumber');
        $q->setParameter('empNumber', $empNumber);
        $q->andWhere($q->expr()->isNotNull('interview.candidateVacancy'));
        $result = $q->getQuery()->getArrayResult();
        return array_column($result, 'id');
    }

    /**
     * @param int|null $empNumber
     * @return bool
     */
    public function isInterviewer(?int $empNumber): bool
    {
        if (is_null($empNumber)) {
            return false;
        }
        $q = $this->createQueryBuilder(InterviewInterviewer::class, 'interviewInterviewer');
        $q->leftJoin('interviewInterviewer.interview', 'interview');
        $q->andWhere('interviewInterviewer.interviewer = :empNumber');
        $q->setParameter('empNumber', $empNumber);
        $q->andWhere($q->expr()->isNotNull('interview.candidateVacancy'));

        return $this->getPaginator($q)->count() > 0;
    }

    /**
     * @param CandidateHistorySearchFilterParams $candidateHistorySearchFilterParams
     * @return CandidateHistory[]
     */
    public function getCandidateHistoryRecords(
        CandidateHistorySearchFilterParams $candidateHistorySearchFilterParams
    ): array {
        $qb = $this->getCandidateHistoryPaginator($candidateHistorySearchFilterParams);
        return $qb->getQuery()->execute();
    }

    /**
     * @param CandidateHistorySearchFilterParams $candidateHistorySearchFilterParams
     * @return int
     */
    public function getCandidateHistoryRecordsCount(
        CandidateHistorySearchFilterParams $candidateHistorySearchFilterParams
    ): int {
        return $this->getCandidateHistoryPaginator($candidateHistorySearchFilterParams)->count();
    }

    /**
     * @param CandidateHistorySearchFilterParams $candidateHistorySearchFilterParams
     * @return Paginator
     */
    private function getCandidateHistoryPaginator(
        CandidateHistorySearchFilterParams $candidateHistorySearchFilterParams
    ): Paginator {
        $q = $this->createQueryBuilder(CandidateHistory::class, 'candidateHistory');
        $this->setSortingAndPaginationParams($q, $candidateHistorySearchFilterParams);
        $q->andWhere('candidateHistory.candidate = :candidateId');
        $q->setParameter('candidateId', $candidateHistorySearchFilterParams->getCandidateId());
        $q->andWhere($q->expr()->in('candidateHistory.action', ':actionIds'));
        $q->setParameter('actionIds', $candidateHistorySearchFilterParams->getActionIds());
        $q->addOrderBy('candidateHistory.id', ListSorter::DESCENDING);
        return $this->getPaginator($q);
    }

    /**
     * @param int $candidateId
     * @param int $historyId
     * @return CandidateHistory|null
     */
    public function getCandidateHistoryRecordByCandidateIdAndHistoryId(
        int $candidateId,
        int $historyId
    ): ?CandidateHistory {
        return $this->getRepository(CandidateHistory::class)
            ->findOneBy(['candidate' => $candidateId, 'id' => $historyId]);
    }

    /**
     * @param int $candidateId
     * @param int $interviewId
     * @return Interview|null
     */
    public function getInterviewByCandidateIdAndInterviewId(int $candidateId, int $interviewId): ?Interview
    {
        return $this->getRepository(Interview::class)
            ->findOneBy([
                'candidate' => $candidateId,
                'id' => $interviewId
            ]);
    }

    /**
     * @param int $candidateId
     * @return array
     */
    public function getInterviewIdsByCandidateId(int $candidateId): array
    {
        $qb = $this->createQueryBuilder(Interview::class, 'interview');
        $qb->select('interview.id');
        $qb->andWhere('interview.candidate = :candidateId');
        $qb->setParameter('candidateId', $candidateId);
        $qb->addOrderBy('interview.id', ListSorter::DESCENDING);
        $result = $qb->getQuery()->getArrayResult();
        return array_column($result, 'id');
    }

    /**
     * @return int []
     */
    public function getCandidateHistoryIdList(): array
    {
        $q = $this->createQueryBuilder(CandidateHistory::class, 'history');
        $q->select('history.id');
        $result = $q->getQuery()->getArrayResult();
        return array_column($result, 'id');
    }

    /**
     * @param int $empNumber
     * @return int []
     */
    public function getCandidateHistoryIdListForHiringManager(int $empNumber): array
    {
        $q = $this->createQueryBuilder(CandidateHistory::class, 'history');
        $q->leftJoin('history.vacancy', 'vacancy');
        $q->select('history.id');
        $q->andWhere('vacancy.hiringManager = :empNumber');
        $q->setParameter('empNumber', $empNumber);
        $result = $q->getQuery()->getArrayResult();
        return array_column($result, 'id');
    }

    /**
     * @param int $empNumber
     * @return int []
     */
    public function getCandidateHistoryIdListForInterviewer(int $empNumber): array
    {
        $q = $this->createQueryBuilder(CandidateHistory::class, 'history');
        $q->select('history.id');
        $q->andWhere($q->expr()->in('history.candidate', ':ids'));
        $q->setParameter('ids', $this->getCandidateListForInterviewer($empNumber));
        $result = $q->getQuery()->getArrayResult();
        return array_column($result, 'id');
    }

    /**
     * @param int $empNumber
     * @return int[]
     */
    public function getVacancyIdListForInterviewer(int $empNumber): array
    {
        $q = $this->createQueryBuilder(CandidateVacancy::class, 'candidateVacancy');
        $q->leftJoin('candidateVacancy.vacancy', 'vacancy');
        $q->select('vacancy.id');
        $q->andWhere($q->expr()->in('candidateVacancy.candidate', ':ids'));
        $q->setParameter('ids', $this->getCandidateListForInterviewer($empNumber));
        $result = $q->getQuery()->getArrayResult();
        return array_column($result, 'id');
    }

    /**
     * @param int $candidateId
     * @return int|null
     */
    public function getCurrentVacancyIdByCandidateId(int $candidateId)
    {
        try {
            $q = $this->createQueryBuilder(CandidateVacancy::class, 'candidateVacancy');
            $q->leftJoin('candidateVacancy.vacancy', 'vacancy');
            $q->select('vacancy.id');
            $q->andWhere('candidateVacancy.candidate = :candidateId');
            $q->setParameter('candidateId', $candidateId);
            $q->setMaxResults(1);
            return $q->getQuery()->getSingleScalarResult();
        } catch (Exception $exception) {
            return null;
        }
    }

    /**
     * @param int $candidateId
     * @param int $vacancyId
     * @return CandidateHistory[]
     */
    public function getCandidateHistoryByCandidateIdAndVacancyId(int $candidateId, int $vacancyId): array
    {
        $q = $this->createQueryBuilder(CandidateHistory::class, 'candidateHistory');
        $q->andWhere('candidateHistory.candidate = :candidateId');
        $q->setParameter('candidateId', $candidateId);
        $q->andWhere('candidateHistory.vacancy = :vacancyId');
        $q->setParameter('vacancyId', $vacancyId);
        return $q->getQuery()->execute();
    }
}
