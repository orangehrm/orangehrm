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
class CandidateDao extends BaseDao {

    /**
     * Retrieve candidate by candidateId
     * @param int $candidateId
     * @returns jobCandidate doctrine object
     * @throws DaoException
     */
    public function getCandidateById($candidateId) {
        try {
            return Doctrine :: getTable('JobCandidate')->find($candidateId);
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Retrieve all candidates
     * @returns JobCandidate doctrine collection
     * @throws DaoException
     */
    public function getCandidateList($allowedCandidateList, $status = JobCandidate::ACTIVE) {
        try {
            $q = Doctrine_Query :: create()
                            ->from('JobCandidate jc');
            if ($allowedCandidateList != null) {
                $q->whereIn('jc.id', $allowedCandidateList);
            }
            if (!empty($status)) {
                $q->addWhere('jc.status = ?', $status);
            }
            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    public function getCandidateListForUserRole($role, $empNumber) {

        try {
            $q = Doctrine_Query :: create()
                            ->select('jc.id')
                            ->from('JobCandidate jc');
            if ($role == HiringManagerUserRoleDecorator::HIRING_MANAGER) {
                $q->leftJoin('jc.JobCandidateVacancy jcv')
                        ->leftJoin('jcv.JobVacancy jv')
                        ->where('jv.hiringManagerId = ?', $empNumber)
                        ->orWhere('jc.addedPerson = ?', $empNumber);
            }
            if ($role == InterviewerUserRoleDecorator::INTERVIEWER) {
                $q->leftJoin('jc.JobCandidateVacancy jcv')
                        ->leftJoin('jcv.JobInterview ji')
                        ->leftJoin('ji.JobInterviewInterviewer jii')
                        ->where('jii.interviewerId = ?', $empNumber);
            }
            $result = $q->fetchArray();
            $idList = array();
            foreach ($result as $item) {
                $idList[] = $item['id'];
            }
            return $idList;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Retriving candidates based on the search criteria
     * @param CandidateSearchParameters $searchParam
     * @return CandidateSearchParameters
     */
    public function searchCandidates(CandidateSearchParameters $searchParam) {

        $jobTitleCode = $searchParam->getJobTitleCode();
        $allowedCandidateList = $searchParam->getAllowedCandidateList();
        $jobVacancyId = $searchParam->getVacancyId();
        $hiringManagerId = $searchParam->getHiringManagerId();
        $status = $searchParam->getStatus();
        $candidateId = $searchParam->getCandidateId();
        $modeOfApplication = $searchParam->getModeOfApplication();
        $fromDate = $searchParam->getFromDate();
        $toDate = $searchParam->getToDate();
        $keywords = $searchParam->getKeywords();
        $candidateStatus = $searchParam->getCandidateStatus();
        $vacancyStatus = $searchParam->getVacancyStatus();
        $sortField = $searchParam->getSortField();
        $sortOrder = $searchParam->getSortOrder();
        $offset = $searchParam->getOffset();
        $limit = $searchParam->getLimit();

        $sortQuery = "";
        if ($sortField == 'jc.first_name') {
            $sortQuery = 'jc.first_name ' . $sortOrder . ', ' . 'jc.last_name ' . $sortOrder;
        } elseif ($sortField == 'e.emp_firstname') {
            $sortQuery = 'e.emp_firstname ' . $sortOrder . ', ' . 'e.emp_lastname ' . $sortOrder;
        } else {
            $sortQuery = $sortField . " " . $sortOrder;
        }

        $keywordsQueryString = "";
        if (!empty($keywords)) {
            $keywords = str_replace("'", "\'", $keywords);
            $words = explode(",", $keywords);
            $length = count($words);
            for ($i = 0; $i < $length; $i++) {
                $keywordsQueryString .= ' AND jc.keywords LIKE ' . "'" . '%' . trim($words[$i]) . '%' . "'";
            }
        }

        try {

            $q = "SELECT jc.id, jc.first_name, jc.last_name, jc.date_of_application, jcv.status, jv.name, e.emp_firstname, e.emp_lastname, jv.status as vacancyStatus, jv.id as vacancyId, ca.id as attachmentId";
            $q .= "  FROM ohrm_job_candidate jc";
            $q .= " LEFT JOIN ohrm_job_candidate_vacancy jcv ON jc.id = jcv.candidate_id";
            $q .= " LEFT JOIN ohrm_job_vacancy jv ON jcv.vacancy_id = jv.id";
            $q .= " LEFT JOIN hs_hr_employee e ON jv.hiring_manager_id = e.emp_number";
            $q .= " LEFT JOIN ohrm_job_candidate_attachment ca ON jc.id = ca.candidate_id";
            $q .= ' where jc.date_of_application  BETWEEN ' . "'$fromDate'" . ' AND ' . "'$toDate'";
            $q .= " AND jc.status = '$candidateStatus'";
            if ($allowedCandidateList != null) {
                $q .= " AND jc.id IN (" . implode(",", $allowedCandidateList) . ")";
            }

            $where = array();

            if (!empty($jobTitleCode) || !empty($jobVacancyId) || !empty($hiringManagerId) || $status != "") {
                $q .= " AND jv.status = '$vacancyStatus'";
            }

            if (!empty($jobTitleCode)) {
                $where[] = "jv.job_title_code = '$jobTitleCode'";
            }
            if (!empty($jobVacancyId)) {
                $where[] = "jv.id  = '$jobVacancyId'";
            }
            if (!empty($hiringManagerId)) {
                $where[] = "jv.hiring_manager_id  = '$hiringManagerId'";
            }
            if ($status != "") {
                $where[] = "jcv.status  = '$status'";
            }
            if (!empty($candidateId)) {
                $where[] = "jc.id  = '$candidateId'";
            }
            if (!empty($modeOfApplication)) {
                $where[] = "jc.mode_of_application  = '$modeOfApplication'";
            }

            if (count($where) > 0) {
                $q .= " AND " . implode('AND ', $where);
            }

            if (!empty($keywordsQueryString)) {
                $q .= $keywordsQueryString;
            }

            $q .= " ORDER BY " . $sortQuery;
            $q .= " LIMIT " . $offset . ", " . $limit;

            $pdo = Doctrine_Manager::connection()->getDbh();
            $res = $pdo->query($q);

            $candidateList = $res->fetchAll();

            $candidatesList = array();
            foreach ($candidateList as $candidate) {

                $param = new CandidateSearchParameters();
                $param->setVacancyName($candidate['name']);
                $param->setVacancyStatus($candidate['vacancyStatus']);
                $param->setCandidateId($candidate['id']);
                $param->setVacancyId($candidate['vacancyId']);
                $param->setCandidateName($candidate['first_name'] . " " . $candidate['last_name']);
                $param->setHiringManagerName($candidate['emp_firstname'] . " " . $candidate['emp_lastname']);
                $param->setDateOfApplication($candidate['date_of_application']);
                $param->setAttachmentId($candidate['attachmentId']);
                $param->setStatusName(ucwords(strtolower($candidate['status'])));
                $candidatesList[] = $param;
            }
            return $candidatesList;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     *
     * @param CandidateSearchParameters $searchParam
     * @return <type>
     */
    public function getCandidateRecordsCount(CandidateSearchParameters $searchParam) {

        $allowedCandidateList = $searchParam->getAllowedCandidateList();
        $jobTitleCode = $searchParam->getJobTitleCode();
        $jobVacancyId = $searchParam->getVacancyId();
        $hiringManagerId = $searchParam->getHiringManagerId();
        $status = $searchParam->getStatus();
        $candidateId = $searchParam->getCandidateId();
        $modeOfApplication = $searchParam->getModeOfApplication();
        $fromDate = $searchParam->getFromDate();
        $toDate = $searchParam->getToDate();
        $keywords = $searchParam->getKeywords();
        $candidateStatus = $searchParam->getCandidateStatus();
        $vacancyStatus = $searchParam->getVacancyStatus();

        $keywordsQueryString = "";
        if (!empty($keywords)) {
            $keywords = str_replace("'", "\'", $keywords);
            $words = explode(",", $keywords);
            $length = count($words);
            for ($i = 0; $i < $length; $i++) {
                $keywordsQueryString .= ' AND jc.keywords LIKE ' . "'" . '%' . trim($words[$i]) . '%' . "'";
            }
        }
        try {

            $q = "SELECT COUNT(*)";
            $q .= " FROM ohrm_job_candidate jc";
            $q .= " LEFT JOIN ohrm_job_candidate_vacancy jcv ON jc.id = jcv.candidate_id";
            $q .= " LEFT JOIN ohrm_job_vacancy jv ON jcv.vacancy_id = jv.id";
            $q .= " LEFT JOIN hs_hr_employee e ON jv.hiring_manager_id = e.emp_number";
            $q .= ' where jc.date_of_application  BETWEEN ' . "'$fromDate'" . ' AND ' . "'$toDate'";
            $q .= " AND jc.status = '$candidateStatus'";
            if ($allowedCandidateList != null) {
                $q .= " AND jc.id IN (" . implode(",", $allowedCandidateList) . ")";
            }
            $where = array();

            if (!empty($jobTitleCode) || !empty($jobVacancyId) || !empty($hiringManagerId) || $status != "") {
                $q .= " AND jv.status = '$vacancyStatus'";
            }

            if (!empty($jobTitleCode)) {
                $where[] = "jv.job_title_code = '$jobTitleCode'";
            }
            if (!empty($jobVacancyId)) {
                $where[] = "jv.id  = '$jobVacancyId'";
            }
            if (!empty($hiringManagerId)) {
                $where[] = "jv.hiring_manager_id  = '$hiringManagerId'";
            }
            if ($status != "") {
                $where[] = "jcv.status  = '$status'";
            }
            if (!empty($candidateId)) {
                $where[] = "jc.id  = '$candidateId'";
            }
            if (!empty($modeOfApplication)) {
                $where[] = "jc.mode_of_application  = '$modeOfApplication'";
            }

            if (count($where) > 0) {
                $q .= " AND " . implode('AND ', $where);
            }

            if (!empty($keywordsQueryString)) {
                $q .= $keywordsQueryString;
            }

            $pdo = Doctrine_Manager::connection()->getDbh();
            $res = $pdo->query($q);
            $count = $res->fetch();
            return $count[0];
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     *
     * @param JobCandidate $candidate
     * @return <type>
     */
    public function saveCandidate(JobCandidate $candidate) {
        try {
            if ($candidate->getId() == "") {
                $idGenService = new IDGeneratorService();
                $idGenService->setEntity($candidate);
                $candidate->setId($idGenService->getNextID());
            }
            $candidate->save();
            return true;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     *
     * @param JobCandidateVacancy $candidateVacancy
     * @return <type>
     */
    public function saveCandidateVacancy(JobCandidateVacancy $candidateVacancy) {
        try {
            if ($candidateVacancy->getId() == '') {
                $idGenService = new IDGeneratorService();
                $idGenService->setEntity($candidateVacancy);
                $candidateVacancy->setId($idGenService->getNextID());
            }
            $candidateVacancy->save();
            return true;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     *
     * @param JobCandidate $candidate
     * @return <type>
     */
    public function updateCandidate(JobCandidate $candidate) {
        try {
            $q = Doctrine_Query:: create()->update('JobCandidate')
                            ->set('firstName', '?', $candidate->firstName)
                            ->set('lastName', '?', $candidate->lastName)
                            ->set('contactNumber', '?', $candidate->contactNumber)
                            ->set('keywords', '?', $candidate->keywords)
                            ->set('email', '?', $candidate->email)
                            ->set('middleName', '?', $candidate->middleName)
                            ->set('dateOfApplication', '?', $candidate->dateOfApplication)
                            ->set('comment', '?', $candidate->comment)
                            ->where('id = ?', $candidate->id);

            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     *
     * @param <type> $candidateVacancyId
     * @return <type>
     */
    public function getCandidateVacancyById($candidateVacancyId) {
        try {
            $q = Doctrine_Query :: create()
                            ->from('JobCandidateVacancy jcv')
                            ->where('jcv.id = ?', $candidateVacancyId);
            return $q->fetchOne();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     *
     * @param JobCandidateVacancy $candidateVacancy
     * @return <type>
     */
    public function updateCandidateVacancy(JobCandidateVacancy $candidateVacancy) {
        try {
            $q = Doctrine_Query:: create()->update('JobCandidateVacancy')
                            ->set('status', '?', $candidateVacancy->status)
                            ->where('id = ?', $candidateVacancy->id);
            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     *
     * @param CandidateHistory $candidateHistory
     * @return <type>
     */
    public function saveCandidateHistory(CandidateHistory $candidateHistory) {
        try {
            if ($candidateHistory->getId() == '') {
                $idGenService = new IDGeneratorService();
                $idGenService->setEntity($candidateHistory);
                $candidateHistory->setId($idGenService->getNextID());
            }
            $candidateHistory->save();
            return true;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     *
     * @param <type> $candidateId
     * @return <type>
     */
    public function getCandidateHistoryForCandidateId($candidateId, $allowedHistoryList) {
        try {
            $q = Doctrine_Query:: create()
                            ->from('CandidateHistory ch')
                            ->leftJoin('ch.JobCandidateVacancy jcv')
                            ->whereIn('ch.id', $allowedHistoryList)
                            ->andWhere('jcv.candidateId = ?', $candidateId);
            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     *
     * @param <type> $id
     * @return <type>
     */
    public function getCandidateHistoryById($id) {
        try {
            $q = Doctrine_Query:: create()
                            ->from('CandidateHistory')
                            ->where('id = ?', $id);
            return $q->fetchOne();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    public function getCanidateHistoryForUserRole($role, $empNumber, $candidateId) {
        try {
            $q = Doctrine_Query :: create()
                            ->select('ch.id')
                            ->from('CandidateHistory ch');
            if ($role == HiringManagerUserRoleDecorator::HIRING_MANAGER) {
                $q->leftJoin('ch.JobCandidateVacancy jcv')
                        ->leftJoin('jcv.JobVacancy jv')
                        ->leftJoin('jcv.JobCandidate jc')
                        ->where('jv.hiringManagerId = ?', $empNumber)
                        ->orWhere('jc.addedPerson = ?', $empNumber);
            }
            if ($role == InterviewerUserRoleDecorator::INTERVIEWER) {
                $q->leftJoin('ch.JobInterview ji ON ji.id = ch.interview_id')
                        ->leftJoin('ji.JobInterviewInterviewer jii')
                        ->where('jii.interviewerId = ?', $empNumber);
            }
            $q->addWhere('ch.candidateId = ?', $candidateId);
            $result = $q->fetchArray();
            $idList = array();
            foreach ($result as $item) {
                $idList[] = $item['id'];
            }
            return $idList;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Get all vacancy Ids for relevent candidate
     * @param int $candidateId
     * @return array $vacancies
     */
    public function getAllVacancyIdsForCandidate($candidateId) {

        try {

            $q = Doctrine_Query:: create()
                            ->from('JobCandidateVacancy v')
                            ->where('v.candidateId = ?', $candidateId);
            $vacancies = $q->execute();

            $vacancyIdsForCandidate = array();
            foreach ($vacancies as $value) {
                $vacancyIdsForCandidate[] = $value->getVacancyId();
            }
            return $vacancyIdsForCandidate;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Delete Candidate
     * @param array $toBeDeletedCandidateIds
     * @return boolean
     */
    public function deleteCandidates($toBeDeletedCandidateIds) {

        try {
            $q = Doctrine_Query:: create()
                            ->delete()
                            ->from('JobCandidate')
                            ->whereIn('id', $toBeDeletedCandidateIds);

            $result = $q->execute();
            if ($result > 0) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Delete Candidate-Vacancy Relations
     * @param array $toBeDeletedRecords
     * @return boolean
     */
    public function deleteCandidateVacancies($toBeDeletedRecords) {

        try {
            $q = Doctrine_Query:: create()
                            ->delete()
                            ->from('JobCandidateVacancy cv')
                            ->where('candidateId = ? AND vacancyId = ?', $toBeDeletedRecords[0]);
            for ($i = 1; $i < count($toBeDeletedRecords); $i++) {
                $q->orWhere('candidateId = ? AND vacancyId = ?', $toBeDeletedRecords[$i]);
            }

            $deleted = $q->execute();
            if ($deleted > 0) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

}
