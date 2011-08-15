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
class RecruitmentAttachmentService extends BaseService {

	private $recruitmentAttachmentDao;

	/**
	 * Get recruitmentAttachmentDao Dao
	 * @return recruitmentAttachmentDao
	 */
	public function getRecruitmentAttachmentDao() {
		return $this->recruitmentAttachmentDao;
	}

	/**
	 * Set Candidate Dao
	 * @param CandidateDao $candidateDao
	 * @return void
	 */
	public function setRecruitmentAttachmentDao(RecruitmentAttachmentDao $recruitmentAttachmentDao) {
		$this->recruitmentAttachmentDao = $recruitmentAttachmentDao;
	}

	/**
	 * Construct
	 */
	public function __construct() {
		$this->recruitmentAttachmentDao = new RecruitmentAttachmentDao();
	}

	/**
	 *
	 * @param JobVacancyAttachment $resume
	 * @return <type>
	 */
	public function saveVacancyAttachment(JobVacancyAttachment $attachment) {
		return $this->recruitmentAttachmentDao->saveVacancyAttachment($attachment);
	}

	/**
	 *
	 * @param JobCandidateAttachment $resume
	 * @return <type>
	 */
	public function saveCandidateAttachment(JobCandidateAttachment $attachment) {
		return $this->recruitmentAttachmentDao->saveCandidateAttachment($attachment);
	}

	/**
	 *
	 * @param <type> $attachId
	 * @return <type>
	 */
	public function getVacancyAttachment($attachId) {
		return $this->recruitmentAttachmentDao->getVacancyAttachment($attachId);
	}

	/**
	 *
	 * @param <type> $attachId
	 * @return <type>
	 */
	public function getCandidateAttachment($attachId) {
		return $this->recruitmentAttachmentDao->getCandidateAttachment($attachId);
	}

	/**
	 *
	 * @param <type> $vacancyId
	 */
	public function getVacancyAttachments($vacancyId) {
		return $this->recruitmentAttachmentDao->getVacancyAttachments($vacancyId);
	}

}

?>
