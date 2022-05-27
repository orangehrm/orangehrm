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
use OrangeHRM\Entity\VacancyAttachment;

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
    public function getAttachmentCount(int $vacancyId): int
    {
        $qb = $this->createQueryBuilder(VacancyAttachment::class, 'attachment');
        $qb->leftJoin('attachment.vacancy', 'vacancy');
        $qb->select('count(attachment.id)')
            ->where('vacancy.id = :vacancyId')
            ->setParameter('vacancyId', $vacancyId);
        return $qb->getQuery()->getSingleScalarResult();
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
}
