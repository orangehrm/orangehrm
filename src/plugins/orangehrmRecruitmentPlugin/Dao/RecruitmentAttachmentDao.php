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
use OrangeHRM\Entity\CandidateAttachment;
use OrangeHRM\Entity\InterviewAttachment;
use OrangeHRM\Entity\InterviewInterviewer;
use OrangeHRM\Entity\VacancyAttachment;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\Recruitment\Dto\InterviewAttachmentSearchFilterParams;

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
     * @param int $attachId
     * @return CandidateAttachment|null
     */
    public function getCandidateAttachment(int $attachId): ?CandidateAttachment
    {
        $attachment = $this->getRepository(CandidateAttachment::class)->find($attachId);
        if ($attachment instanceof CandidateAttachment) {
            return $attachment;
        }
        return null;
    }

    /**
     * @param int $vacancyId
     * @return VacancyAttachment[]
     */
    public function getVacancyAttachmentsByVacancyId(int $vacancyId): array
    {
        $qb = $this->createQueryBuilder(VacancyAttachment::class, 'attachment');
        $qb->leftJoin('attachment.vacancy', 'vacancy');
        $qb->where('vacancy.id = :vacancyId')
            ->setParameter('vacancyId', $vacancyId)
            ->orderBy('attachment.fileName', 'ASC');
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
     * @return InterviewAttachment[]
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
        $qb = $this->createQueryBuilder(InterviewAttachment::class, 'attachment');
        $qb->where('attachment.interview = :interviewId')
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
        return $this->getInterviewAttachmentPaginator($interviewAttachmentParamHolder)->count();
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
        $q->andWhere('interview IN (:ids)');
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
