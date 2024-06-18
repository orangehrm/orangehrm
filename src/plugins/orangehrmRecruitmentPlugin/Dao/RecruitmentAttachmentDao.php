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
use OrangeHRM\Entity\CandidateAttachment;
use OrangeHRM\Entity\InterviewAttachment;
use OrangeHRM\Entity\InterviewInterviewer;
use OrangeHRM\Entity\VacancyAttachment;
use OrangeHRM\ORM\ListSorter;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\Recruitment\Dto\InterviewAttachmentSearchFilterParams;
use OrangeHRM\Recruitment\Dto\RecruitmentAttachment;

class RecruitmentAttachmentDao extends BaseDao
{
    /**
     * @param VacancyAttachment $vacancyAttachment
     * @return VacancyAttachment
     */
    public function saveVacancyAttachment(VacancyAttachment $vacancyAttachment): VacancyAttachment
    {
        $this->persist($vacancyAttachment);
        return $vacancyAttachment;
    }

    /**
     * @param CandidateAttachment $candidateAttachment
     * @return CandidateAttachment
     */
    public function saveCandidateAttachment(CandidateAttachment $candidateAttachment): CandidateAttachment
    {
        $this->persist($candidateAttachment);
        return $candidateAttachment;
    }

    /**
     * @param InterviewAttachment $interviewAttachment
     * @return InterviewAttachment
     */
    public function saveInterviewAttachment(InterviewAttachment $interviewAttachment): InterviewAttachment
    {
        $this->persist($interviewAttachment);
        return $interviewAttachment;
    }

    /**
     * @param int $attachId
     * @return VacancyAttachment|null
     */
    public function getVacancyAttachmentById(int $attachId): ?VacancyAttachment
    {
        $attachment = $this->getRepository(VacancyAttachment::class)->find($attachId);
        if ($attachment instanceof VacancyAttachment) {
            return $attachment;
        }
        return null;
    }

    /**
     * @param int[] $ids
     * @return int[]
     */
    public function getExistingVacancyAttachmentIds(array $ids): array
    {
        $qb = $this->createQueryBuilder(VacancyAttachment::class, 'vacancyAttachment');

        $qb->select('vacancyAttachment.id')
            ->andWhere($qb->expr()->in('vacancyAttachment.id', ':ids'))
            ->setParameter('ids', $ids);

        return $qb->getQuery()->getSingleColumnResult();
    }

    /**
     * @param int $candidateId
     * @return RecruitmentAttachment|null
     */
    public function getPartialCandidateAttachmentByCandidateId(int $candidateId): ?RecruitmentAttachment
    {
        $select = 'NEW ' . RecruitmentAttachment::class . "(candidateAttachment.id,candidateAttachment.fileName,candidateAttachment.fileType,candidateAttachment.fileSize,IDENTITY(candidateAttachment.candidate))";
        $qb = $this->createQueryBuilder(CandidateAttachment::class, 'candidateAttachment');
        $qb->select($select);
        $qb->where('candidateAttachment.candidate = :candidateId');
        $qb->setParameter('candidateId', $candidateId);
        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @param int $candidateId
     * @return CandidateAttachment|null
     */
    public function getCandidateAttachmentByCandidateId(int $candidateId): ?CandidateAttachment
    {
        $qb = $this->createQueryBuilder(CandidateAttachment::class, 'candidateAttachment');
        $qb->where('candidateAttachment.candidate = :candidateId');
        $qb->setParameter('candidateId', $candidateId);
        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @param int $candidateId
     * @return bool
     */
    public function hasCandidateAttachmentByCandidateId(int $candidateId): bool
    {
        $qb = $this->createQueryBuilder(CandidateAttachment::class, 'candidateAttachment');
        $qb->where('candidateAttachment.candidate = :candidateId');
        $qb->setParameter('candidateId', $candidateId);
        return $this->count($qb) > 0;
    }

    /**
     * @param int $attachId
     * @param int $interviewId
     * @return RecruitmentAttachment|null
     */
    public function getPartialInterviewAttachmentByAttachmentIdAndInterviewId(
        int $attachId,
        int $interviewId
    ): ?RecruitmentAttachment {
        $select = 'NEW ' . RecruitmentAttachment::class . "(interviewAttachment.id,interviewAttachment.fileName,interviewAttachment.fileType,interviewAttachment.fileSize,IDENTITY(interviewAttachment.interview),interviewAttachment.comment)";
        $qb = $this->createQueryBuilder(InterviewAttachment::class, 'interviewAttachment');
        $qb->select($select);
        $qb->andWhere('interviewAttachment.id = :id');
        $qb->setParameter('id', $attachId);
        $qb->andWhere('interviewAttachment.interview = :interviewId');
        $qb->setParameter('interviewId', $interviewId);
        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @param int $attachId
     * @param int $interviewId
     * @return InterviewAttachment|null
     */
    public function getInterviewAttachmentByAttachmentIdAndInterviewId(
        int $attachId,
        int $interviewId
    ): ?InterviewAttachment {
        return $this->getRepository(InterviewAttachment::class)
            ->findOneBy(['id' => $attachId, 'interview' => $interviewId]);
    }

    /**
     * @param int $vacancyId
     * @return RecruitmentAttachment[]
     */
    public function getVacancyAttachmentsByVacancyId(int $vacancyId): array
    {
        $select = 'NEW ' . RecruitmentAttachment::class . "(vacancyAttachment.id,vacancyAttachment.fileName,vacancyAttachment.fileType,vacancyAttachment.fileSize,IDENTITY(vacancyAttachment.vacancy),vacancyAttachment.comment)";
        $qb = $this->createQueryBuilder(VacancyAttachment::class, 'vacancyAttachment');
        $qb->select($select);
        $qb->leftJoin('vacancyAttachment.vacancy', 'vacancy');
        $qb->where('vacancy.id = :vacancyId')
            ->setParameter('vacancyId', $vacancyId)
            ->orderBy('vacancyAttachment.fileName', ListSorter::ASCENDING);
        return $qb->getQuery()->execute();
    }

    /**
     * @param int $vacancyId
     * @return int
     */
    public function getVacancyAttachmentsCountByVacancyId(int $vacancyId): int
    {
        $qb = $this->createQueryBuilder(VacancyAttachment::class, 'attachment');
        $qb->leftJoin('attachment.vacancy', 'vacancy');
        $qb->where('vacancy.id = :vacancyId');
        $qb->setParameter('vacancyId', $vacancyId);
        return $this->count($qb);
    }

    /**
     * @param array $toBeDeletedAttachmentIds
     * @return bool
     */
    public function deleteVacancyAttachments(array $toBeDeletedAttachmentIds): bool
    {
        $qr = $this->createQueryBuilder(VacancyAttachment::class, 'attachment');
        $qr->delete()
            ->andWhere('attachment.id IN (:ids)')
            ->setParameter('ids', $toBeDeletedAttachmentIds);

        return $qr->getQuery()->execute() > 0;
    }

    /**
     * @param int $candidateId
     * @return bool
     */
    public function deleteCandidateAttachment(int $candidateId): bool
    {
        $qb = $this->createQueryBuilder(CandidateAttachment::class, 'candidateAttachment');
        $qb->delete()
            ->where('candidateAttachment.candidate = :candidateId')
            ->setParameter('candidateId', $candidateId);
        return $qb->getQuery()->execute() > 0;
    }

    /**
     * @param InterviewAttachmentSearchFilterParams $interviewAttachmentParamHolder
     * @return RecruitmentAttachment[]
     */
    public function getInterviewAttachments(
        InterviewAttachmentSearchFilterParams $interviewAttachmentParamHolder
    ): array {
        $qb = $this->getInterviewAttachmentPaginator($interviewAttachmentParamHolder);
        return $qb->getQuery()->execute();
    }

    /**
     * @param InterviewAttachmentSearchFilterParams $interviewAttachmentSearchFilterParams
     * @return Paginator
     */
    protected function getInterviewAttachmentPaginator(
        InterviewAttachmentSearchFilterParams $interviewAttachmentSearchFilterParams
    ): Paginator {
        $select = 'NEW ' . RecruitmentAttachment::class . "(interviewAttachment.id,interviewAttachment.fileName,interviewAttachment.fileType,interviewAttachment.fileSize,interview.id,interviewAttachment.comment)";
        $qb = $this->createQueryBuilder(InterviewAttachment::class, 'interviewAttachment');
        $qb->leftJoin('interviewAttachment.interview', 'interview');
        $qb->select($select);
        $qb->where('interviewAttachment.interview = :interviewId')
            ->setParameter('interviewId', $interviewAttachmentSearchFilterParams->getInterviewId());
        return $this->getPaginator($qb);
    }

    /**
     * @param InterviewAttachmentSearchFilterParams $interviewAttachmentParamHolder
     * @return int
     */
    public function getInterviewAttachmentsCount(
        InterviewAttachmentSearchFilterParams $interviewAttachmentParamHolder
    ): int {
        return $this->getInterviewAttachmentPaginator($interviewAttachmentParamHolder)->setUseOutputWalkers(false)->count();
    }

    /**
     * @return int[]
     */
    public function getInterviewAttachmentIdList(): array
    {
        $q = $this->createQueryBuilder(InterviewAttachment::class, 'attachment');
        $q->select('attachment.id');
        $result = $q->getQuery()->getArrayResult();
        return array_column($result, 'id');
    }

    /**
     * @return int[]
     */
    public function getVacancyAttachmentIdList(): array
    {
        $q = $this->createQueryBuilder(VacancyAttachment::class, 'attachment');
        $q->select('attachment.id');
        $result = $q->getQuery()->getArrayResult();
        return array_column($result, 'id');
    }

    /**
     * @param int $empNumber
     * @return int[]
     */
    public function getVacancyAttachmentListForHiringManger(int $empNumber): array
    {
        $q = $this->createQueryBuilder(VacancyAttachment::class, 'attachment');
        $q->leftJoin('attachment.vacancy', 'vacancy');
        $q->select('attachment.id');
        $q->andWhere('vacancy.hiringManager = :empNumber');
        $q->setParameter('empNumber', $empNumber);
        $result = $q->getQuery()->getArrayResult();
        return array_column($result, 'id');
    }

    /**
     * @param int $empNumber
     * @return int[]
     */
    public function getInterviewAttachmentListForHiringManger(int $empNumber): array
    {
        $q = $this->createQueryBuilder(InterviewAttachment::class, 'attachment');
        $q->leftJoin('attachment.interview', 'interview');
        $q->leftJoin('interview.candidateVacancy', 'candidateVacancy');
        $q->leftJoin('candidateVacancy.vacancy', 'vacancy');
        $q->select('attachment.id');
        $q->andWhere('vacancy.hiringManager = :empNumber');
        $q->setParameter('empNumber', $empNumber);
        $result = $q->getQuery()->getArrayResult();
        return array_column($result, 'id');
    }

    /**
     * @param int $empNumber
     * @return int[]
     */
    public function getInterviewAttachmentListForInterviewer(int $empNumber): array
    {
        $q = $this->createQueryBuilder(InterviewAttachment::class, 'attachment');
        $q->leftJoin('attachment.interview', 'interview');
        $q->select('attachment.id');
        $q->andWhere($q->expr()->in('interview', ':ids'));
        $q->setParameter('ids', $this->getAccessibleInterviewIdsForInterviewer($empNumber));
        $result = $q->getQuery()->getArrayResult();
        return array_column($result, 'id');
    }

    /**
     * @param int $empNumber
     * @return int []
     */
    private function getAccessibleInterviewIdsForInterviewer(int $empNumber): array
    {
        $q = $this->createQueryBuilder(InterviewInterviewer::class, 'interviewInterviewer');
        $q->leftJoin('interviewInterviewer.interview', 'interview');
        $q->select('interview.id');
        $q->andWhere('interviewInterviewer.interviewer = :empNumber');
        $q->setParameter('empNumber', $empNumber);
        $result = $q->getQuery()->getArrayResult();
        return array_column($result, 'id');
    }

    /**
     * @param int[] $ids
     * @param int $interviewId
     * @return int[]
     */
    public function getExistingInterviewAttachmentIdsForInterview(array $ids, int $interviewId): array
    {
        $qb = $this->createQueryBuilder(InterviewAttachment::class, 'interviewAttachment');

        $qb->select('interviewAttachment.id')
            ->andWhere($qb->expr()->in('interviewAttachment.id', ':ids'))
            ->andWhere($qb->expr()->eq('interviewAttachment.interview', ':interviewId'))
            ->setParameter('ids', $ids)
            ->setParameter('interviewId', $interviewId);

        return $qb->getQuery()->getSingleColumnResult();
    }

    /**
     * @param int $interviewId
     * @param array $toBeDeletedAttachmentIds
     * @return bool
     */
    public function deleteInterviewAttachments(int $interviewId, array $toBeDeletedAttachmentIds): bool
    {
        $qb = $this->createQueryBuilder(InterviewAttachment::class, 'attachment');
        $qb->delete()
            ->andWhere('attachment.id IN (:ids)')
            ->setParameter('ids', $toBeDeletedAttachmentIds)
            ->andWhere('attachment.interview = :interviewId')
            ->setParameter('interviewId', $interviewId);
        return $qb->getQuery()->execute() > 0;
    }
}
