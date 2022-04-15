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

/**
 * CandidateDao for CRUD operation
 *
 */
class JobInterviewDao extends BaseDao {

	/**
	 *
	 * @param <type> $interviewId
	 * @return <type> 
	 */
	public function getInterviewById($interviewId) {
		try {
			return Doctrine :: getTable('JobInterview')->find($interviewId);
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}

	public function getInterviewersByInterviewId($interviewId) {

		try {
			$q = Doctrine_Query :: create()
					->from('JobInterviewInterviewer')
					->where('interview_id =?', $interviewId);
			return $q->execute();
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}

	public function getInterviewsByCandidateVacancyId($candidateVacancyId) {

		try {
			$q = Doctrine_Query :: create()
					->from('JobInterview')
					->where('candidate_vacancy_id =?', $candidateVacancyId);
			return $q->execute();
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}

	public function getInterviewScheduledHistoryByInterviewId($interviewId) {

		try {
			$q = Doctrine_Query :: create()
					->from('CandidateHistory')
					->where('interview_id =?', $interviewId)
					->andWhere('action =?', WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_SHEDULE_INTERVIEW);
			return $q->fetchOne();
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}

    public function saveJobInterview(JobInterview $jobInterview) {
		try {
			$jobInterview->save();
			return true;
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}
    
    /**
     * Get interviw objects for relevent candidate in specific date
     * @param int $candidateId
     * @param dateISO $interviewDate
     * @param time $fromTime (actual interview time - 01:00:00)
     * @param time $toTime (actual interview time + 01:00:00)
     * @return array JobInterview Doctrine Objects
     */
    public function getInterviewListByCandidateIdAndInterviewDateAndTime($candidateId, $interviewDate, $fromTime, $toTime) {
        
        try {
            
            $query = Doctrine_Query::create()
                    ->from('JobInterview ji')
                    ->leftJoin('ji.JobCandidateVacancy jcv')
                    ->where('jcv.candidateId = ?', $candidateId)
                    ->andWhere('ji.interviewDate = ?', $interviewDate)
                    ->andWhere('ji.interviewTime > ?', $fromTime)
                    ->andWhere('ji.interviewTime < ?', $toTime);
            
            return $query->execute();
            
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
        
    }

	public function updateJobInterview(JobInterview $jobInterview) {
		try {
			$q = Doctrine_Query:: create()->update('JobInterview')
					->set('candidateVacancyId', '?', $jobInterview->candidateVacancyId)
					->set('candidateId', '?', $jobInterview->candidateId)
					->set('interviewName', '?', $jobInterview->interviewName)
					->set('interviewDate', '?', $jobInterview->interviewDate)
					->set('interviewTime', '?', $jobInterview->interviewTime)
					->set('note', '?', $jobInterview->note)
					->where('id = ?', $jobInterview->id);

			return $q->execute();
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}

}