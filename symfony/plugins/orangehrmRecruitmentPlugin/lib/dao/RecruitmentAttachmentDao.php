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
class RecruitmentAttachmentDao extends BaseDao {

    /**
     *
     * @param JobVacancyAttachment $attachment
     * @return <type>
     */
    public function saveVacancyAttachment(JobVacancyAttachment $attachment) {
        try {
            $attachment->save();
            return true;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     *
     * @param JobCandidateAttachment $attachment
     * @return <type>
     */
    public function saveCandidateAttachment(JobCandidateAttachment $attachment) {
        try {
            $attachment->save();
            return true;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     *
     * @param <type> $attachId
     * @return <type>
     */
    public function getVacancyAttachment($attachId) {
        try {
            $q = Doctrine_Query:: create()
                            ->from('JobVacancyAttachment a')
                            ->where('a.id = ?', $attachId);
            return $q->fetchOne();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     *
     * @param <type> $attachId
     * @return <type>
     */
    public function getInterviewAttachment($attachId) {
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
     * @param <type> $attachId
     * @return <type>
     */
    public function getCandidateAttachment($attachId) {
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
     *
     * @param <type> $vacancyId
     * @return <type>
     */
    public function getVacancyAttachments($vacancyId) {
        try {
            $q = Doctrine_Query :: create()
                            ->from('JobVacancyAttachment')
                            ->where('vacancyId =?', $vacancyId)
                            ->orderBy('fileName ASC');
            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    
    /**
     *
     * @param <type> $interviewId
     * @return <type>
     */
    public function getInterviewAttachments($interviewId) {
        try {
            $q = Doctrine_Query :: create()
                            ->from('JobInterviewAttachment')
                            ->where('interview_id =?', $interviewId)
                            ->orderBy('fileName ASC');
            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

}
