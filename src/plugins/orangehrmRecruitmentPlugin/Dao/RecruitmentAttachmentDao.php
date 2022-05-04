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
use OrangeHRM\Entity\VacancyAttachment;

class RecruitmentAttachmentDao extends BaseDao
{

    /**
     * @param  VacancyAttachment  $vacancyAttachment
     * @return VacancyAttachment
     */
    public function saveVacancyAttachment(VacancyAttachment $vacancyAttachment): VacancyAttachment
    {
        $this->persist($vacancyAttachment);
        return $vacancyAttachment;
    }

    /**
     *
     * @param  JobCandidateAttachment  $attachment
     * @return <type>
     */
    public function saveCandidateAttachment(JobCandidateAttachment $attachment)
    {
        try {
            $attachment->save();
            return true;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @param  int  $attachId
     * @return VacancyAttachment|null
     */
    public function getVacancyAttachment(int $attachId): ?VacancyAttachment
    {
        $attachment = $this->getRepository(VacancyAttachment::class)->find($attachId);
        if ($attachment instanceof VacancyAttachment) {
            return $attachment;
        }
        return null;
    }

    /**
     *
     * @param  <type>  $attachId
     * @return <type>
     */
    public function getInterviewAttachment($attachId)
    {
        try {
            $q = Doctrine_Query:: create()
                ->from('JobInterviewAttachment a')
                ->where('a.id = ?', $attachId);
            return $q->fetchOne();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     *
     * @param  <type>  $attachId
     * @return <type>
     */
    public function getCandidateAttachment($attachId)
    {
        try {
            $q = Doctrine_Query:: create()
                ->from('JobCandidateAttachment a')
                ->where('a.id = ?', $attachId);
            return $q->fetchOne();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @param $vacancyId
     * @return VacancyAttachment[]
     */
    public function getVacancyAttachments($vacancyId): array
    {
        $qb = $this->createQueryBuilder(VacancyAttachment::class, 'attachment');
        $qb->leftJoin('attachment.vacancy', 'vacancy');
        $qb->where('vacancy.id = :vacancyId')
            ->setParameter('vacancyId', $vacancyId)
            ->orderBy('attachment.fileName', 'ASC');
        return $qb->getQuery()->execute();
    }

    /**
     * @param $vacancyId
     * @return int
     */
    public function getAttachmentCount($vacancyId): int
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

        $result = $qr->getQuery()->execute();
        if ($result > 0) {
            return true;
        }
        return false;
    }


    /**
     *
     * @param  <type>  $interviewId
     * @return <type>
     */
    public function getInterviewAttachments($interviewId)
    {
        try {
            $q = Doctrine_Query:: create()
                ->from('JobInterviewAttachment')
                ->where('interview_id =?', $interviewId)
                ->orderBy('fileName ASC');
            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }


}
