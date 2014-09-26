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
    
    /**
     * Return an array of candidate names
     * 
     * @version 2.7.1
     * @param Array $allowedCandidateIdList Allowed candidate Id List
     * @param Integer $status Cadidate Status
     * @returns Array Candidate Name List
     * @throws DaoException
     */
    public function getCandidateNameList($allowedCandidateIdList, $status = JobCandidate::ACTIVE) {
        try {
            
            if (!empty($allowedCandidateIdList)) {
                
                $escapeString = implode(',', array_fill(0, count($allowedCandidateIdList), '?'));
                $pdo = Doctrine_Manager::connection()->getDbh();
                $q = "SELECT jc.first_name AS firstName, jc.middle_name AS middleName, jc.last_name AS lastName, jc.id
                		FROM ohrm_job_candidate jc
                		WHERE jc.id IN ({$escapeString}) AND
                		jc.status = ?";
                
                $escapeValueArray = array_values($allowedCandidateIdList);
                $escapeValueArray[] = $status;
                
                $query = $pdo->prepare($q); 
                $query->execute($escapeValueArray);
                $results = $query->fetchAll(PDO::FETCH_ASSOC);
            }
            
            return $results;
        
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
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
                        ->orWhere('jc.id NOT IN (SELECT ojcv.candidateId FROM JobCandidateVacancy ojcv) AND jc.addedPerson = ?', $empNumber);
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
    public function searchCandidates($searchCandidateQuery) {

        try {
            $pdo = Doctrine_Manager::connection()->getDbh();
            $res = $pdo->query($searchCandidateQuery);

            $candidateList = $res->fetchAll();

            $candidatesList = array();
            foreach ($candidateList as $candidate) {

                $param = new CandidateSearchParameters();
                $param->setVacancyName($candidate['name']);
                $param->setVacancyStatus($candidate['vacancyStatus']);
                $param->setCandidateId($candidate['id']);
                $param->setVacancyId($candidate['vacancyId']);
                $param->setCandidateName($candidate['first_name'] . " " . $candidate['middle_name'] . " " . $candidate['last_name'] . $this->_getCandidateNameSuffix($candidate['candidateStatus']));
                $employeeName = $candidate['emp_firstname'] . " " . $candidate['emp_middle_name'] . " " . $candidate['emp_lastname'];
                $hmName = (!empty($candidate['termination_id'])) ? $employeeName." (".__("Past Employee").")" : $employeeName;
                $param->setHiringManagerName($hmName);
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
    public function getCandidateRecordsCount($countQuery) {

        try {
            $pdo = Doctrine_Manager::connection()->getDbh();
            $res = $pdo->query($countQuery);
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
     * @param JobCandidate $candidate
     * @return <type>
     */
    public function updateCandidateHistory(CandidateHistory $candidateHistory) {
        try {
            $q = Doctrine_Query:: create()->update('CandidateHistory')
                    ->set('interviewers', '?', $candidateHistory->interviewers)
                    ->where('id = ?', $candidateHistory->id);

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
                    ->whereIn('ch.id', $allowedHistoryList)
                    ->andWhere('ch.candidateId = ?', $candidateId)
                    ->orderBy('ch.performedDate DESC');
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

    /**
     * Return an array of Candidate History Ids based on user role
     * 
     * @version 2.7.1
     * @param String $role User Role
     * @param Integer $empNumber Employee Number
     * @param Integer $candidateId Candidate Id
     * @return Array of Candidate History Ids
     * @throws DaoException
     */
    public function getCanidateHistoryForUserRole($role, $empNumber, $candidateId) {
        try {
            $q = Doctrine_Query :: create()
                    ->select('ch.id')
                    ->from('CandidateHistory ch');
            if ($role == HiringManagerUserRoleDecorator::HIRING_MANAGER) {
                 $q->leftJoin('ch.JobVacancy jv')
                        ->leftJoin('ch.JobCandidate jc')
                        ->where('ch.candidateId = ?', $candidateId)
                        ->andWhere('jv.hiringManagerId = ? OR ( ch.action IN (?) OR (ch.candidateId NOT IN (SELECT ojcv.candidateId FROM JobCandidateVacancy ojcv) AND jc.addedPerson = ?) OR ch.performedBy = ? )', array($empNumber, CandidateHistory::RECRUITMENT_CANDIDATE_ACTION_ADD, $empNumber, $empNumber));
            }
            if ($role == InterviewerUserRoleDecorator::INTERVIEWER) {
                $q->leftJoin('ch.JobInterview ji ON ji.id = ch.interview_id')
                        ->leftJoin('ji.JobInterviewInterviewer jii')
                        ->where('ch.candidateId = ?', $candidateId)
                        ->andWhere('jii.interviewerId = ? OR (ch.performedBy = ? OR ch.action IN (?, ?, ?, ?, ?, ?))',  array($empNumber, $empNumber, CandidateHistory::RECRUITMENT_CANDIDATE_ACTION_ADD, WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_ATTACH_VACANCY, WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_SHORTLIST, WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_SHEDULE_INTERVIEW, WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_MARK_INTERVIEW_FAILED, WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_MARK_INTERVIEW_PASSED));
            }
            if ($role == AdminUserRoleDecorator::ADMIN_USER) {
                $q->where('ch.candidateId = ?', $candidateId);
            }
            $result = $q->fetchArray();
            $idList = array();
            foreach ($result as $item) {
                $idList[] = $item['id'];
            }
            return $idList;

        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
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
            return true;
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

    public function buildSearchQuery(CandidateSearchParameters $paramObject, $countQuery = false) {

        try {
            $pdo = Doctrine_Manager::connection()->getDbh();
            
            $query = ($countQuery) ? "SELECT COUNT(*)" : "SELECT jc.id, jc.first_name, jc.middle_name, jc.last_name, jc.date_of_application, jcv.status, jv.name, e.emp_firstname, e.emp_middle_name, e.emp_lastname, e.termination_id, jv.status as vacancyStatus, jv.id as vacancyId, ca.id as attachmentId, jc.status as candidateStatus";
            $query .= "  FROM ohrm_job_candidate jc";
            $query .= " LEFT JOIN ohrm_job_candidate_vacancy jcv ON jc.id = jcv.candidate_id";
            $query .= " LEFT JOIN ohrm_job_vacancy jv ON jcv.vacancy_id = jv.id";
            $query .= " LEFT JOIN hs_hr_employee e ON jv.hiring_manager_id = e.emp_number";
            $query .= " LEFT JOIN ohrm_job_candidate_attachment ca ON jc.id = ca.candidate_id";
            $query .= ' WHERE jc.date_of_application BETWEEN ' .
                    $pdo->quote($paramObject->getFromDate(), PDO::PARAM_STR) . ' AND ' .
                    $pdo->quote($paramObject->getToDate(), PDO::PARAM_STR);

            $candidateStatuses = $paramObject->getCandidateStatus();
            if (!empty($candidateStatuses)) {
                $query .= " AND jc.status IN (";
                $comma = '';
                foreach ($candidateStatuses as $candidateStatus) {
                    $query .= $comma . $pdo->quote($candidateStatus, PDO::PARAM_INT);
                    $comma = ',';
                }
                $query .= ")";
            }

            $query .= $this->_buildKeywordsQueryClause($paramObject->getKeywords());
            $query .= $this->_buildAdditionalWhereClauses($paramObject);
            $query .= " ORDER BY " . $this->_buildSortQueryClause($paramObject->getSortField(), $paramObject->getSortOrder());
            if (!$countQuery) {
                $query .= " LIMIT " . $paramObject->getOffset() . ", " . $paramObject->getLimit();
            }
            return $query;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     *
     * @param array $keywords
     * @return string
     */
    private function _buildKeywordsQueryClause($keywords) {
        $keywordsQueryClause = '';
        if (!empty($keywords)) {
            $dbh = Doctrine_Manager::connection()->getDbh();
            $words = explode(',', $keywords);
            foreach ($words as $word) {
                $keywordsQueryClause .= ' AND jc.keywords LIKE ' . $dbh->quote('%' . trim($word) . '%');
            }
        }

        return $keywordsQueryClause;
    }

    /**
     *
     * @param string $sortField
     * @param string $sortOrder
     * @return string
     */
    private function _buildSortQueryClause($sortField, $sortOrder) {
        $sortQuery = '';
        $sortOrder = strcasecmp($sortOrder, 'DESC') === 0 ? 'DESC' : 'ASC';
        if ($sortField == 'jc.first_name') {
            $sortQuery = 'jc.last_name ' . $sortOrder . ', ' . 'jc.first_name ' . $sortOrder;
        } elseif ($sortField == 'e.emp_firstname') {
            $sortQuery = 'e.emp_lastname ' . $sortOrder . ', ' . 'e.emp_firstname ' . $sortOrder;
        } elseif ($sortField == 'jc.date_of_application') {
            $sortQuery = 'jc.date_of_application ' . $sortOrder . ', ' . 'jc.last_name ASC, jc.first_name ASC';
        } else {
            $sortQuery = $sortField . " " . $sortOrder;
        }

        return $sortQuery;
    }

    /**
     * @param CandidateSearchParameters $paramObject
     * @return string
     */
    private function _buildAdditionalWhereClauses(CandidateSearchParameters $paramObject) {

        $allowedCandidateList = $paramObject->getAllowedCandidateList();
        $jobTitleCode = $paramObject->getJobTitleCode();
        $jobVacancyId = $paramObject->getVacancyId();
        $hiringManagerId = $paramObject->getHiringManagerId();
        $status = $paramObject->getStatus();
        $allowedVacancyList = $paramObject->getAllowedVacancyList();
        $isAdmin = $paramObject->getIsAdmin();
        $empNumber = $paramObject->getEmpNumber();

        $whereClause = '';
        $whereFilters = array();
        if ($allowedVacancyList != null && !$isAdmin) {
            $this->_addAdditionalWhereClause($whereFilters, 'jv.id', '(' . implode(',', $allowedVacancyList) . ')', 'IN');
        }
        if ($allowedCandidateList != null && !$isAdmin) {
            $this->_addAdditionalWhereClause($whereFilters, 'jc.id', '(' . implode(',', $allowedCandidateList) . ')', 'IN');
        }
        if (!empty($jobTitleCode) || !empty($jobVacancyId) || !empty($hiringManagerId) || !empty($status)) {
            $this->_addAdditionalWhereClause($whereFilters, 'jv.status', $paramObject->getVacancyStatus());
        }


        $this->_addAdditionalWhereClause($whereFilters, 'jv.job_title_code', $paramObject->getJobTitleCode());
        $this->_addAdditionalWhereClause($whereFilters, 'jv.id', $paramObject->getVacancyId());
        $this->_addAdditionalWhereClause($whereFilters, 'jv.hiring_manager_id', $paramObject->getHiringManagerId());
        $this->_addAdditionalWhereClause($whereFilters, 'jcv.status', $paramObject->getStatus());

        $this->_addCandidateNameClause($whereFilters, $paramObject);

        $this->_addAdditionalWhereClause($whereFilters, 'jc.mode_of_application', $paramObject->getModeOfApplication());


        $whereClause .= ( count($whereFilters) > 0) ? (' AND ' . implode('AND ', $whereFilters)) : '';
        if ($empNumber != null) {
            $whereClause .= " OR jc.id NOT IN (SELECT ojcv.candidate_id FROM ohrm_job_candidate_vacancy ojcv) AND jc.added_person = " . $empNumber;
        }
        if(!empty($status)){
            $whereClause .=" AND NOT ISNULL(jcv.status)";
        }
        return $whereClause;
    }

    /**
     *
     * @param array_pointer $where
     * @param string $field
     * @param mixed $value
     * @param string $operator
     */
    private function _addAdditionalWhereClause(&$where, $field, $value, $operator = '=') {
        if (!empty($value)) {
            if ($operator === '=') {
                $dbh = Doctrine_Manager::connection()->getDbh();
                $value = $dbh->quote($value);
            }
            $where[] = "{$field}  {$operator} {$value}";
        }
    }

    /**
     * Add where clause to search by candidate name.
     * 
     * @param type $where Where Clause
     * @param type $paramObject Search Parameter object
     */
    private function _addCandidateNameClause(&$where, $paramObject) {

        // Search by Name
        $candidateName = $paramObject->getCandidateName();

        if (!empty($candidateName)) {

            // Trimming to avoid issues with names that have leading/trailing spaces (due to bug in older jobs.php)
            $candidateFullNameClause = "concat_ws(' ', trim(jc.first_name), " .
                    "IF(trim(jc.middle_name) <> '', trim(jc.middle_name), NULL), " .
                    "trim(jc.last_name))";

            // Replace multiple spaces in string with single space
            $candidateName = preg_replace('!\s+!', ' ', $candidateName);
            $candidateName = "%" . $candidateName . "%";
            $dbh = Doctrine_Manager::connection()->getDbh();
            $candidateName = $dbh->quote($candidateName);

            $this->_addAdditionalWhereClause($where, $candidateFullNameClause, $candidateName, 'LIKE');
        }
    }

    public function isHiringManager($candidateVacancyId, $empNumber) {
        try {
            $q = Doctrine_Query :: create()
                    ->select('COUNT(*)')
                    ->from('JobCandidateVacancy jcv')
                    ->leftJoin('jcv.JobVacancy jv')
                    ->where('jcv.id = ?', $candidateVacancyId)
                    ->andWhere('jv.hiringManagerId = ?', $empNumber);

            $count = $q->fetchOne(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
            return ($count > 0);
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    public function isInterviewer($candidateVacancyId, $empNumber) {
        try {
            $q = Doctrine_Query :: create()
                    ->select('COUNT(*)')
                    ->from('JobInterviewInterviewer jii')
                    ->leftJoin('jii.JobInterview ji')
                    ->leftJoin('ji.JobCandidateVacancy jcv')
                    ->where('jcv.id = ?', $candidateVacancyId)
                    ->andWhere('jii.interviewerId = ?', $empNumber);

            $count = $q->fetchOne(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
            return ($count > 0);
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Get candidate name suffix according to the candidate status
     * @param integer $statusCode
     * return string $suffix
     */
    private function _getCandidateNameSuffix($statusCode) {

        $suffix = "";

        if ($statusCode == JobCandidate::ARCHIVED) {
            $suffix = " (" . __('Archived') . ")";
        }

        return $suffix;
    }

    public function getCandidateVacancyByCandidateIdAndVacancyId($candidateId, $vacancyId) {
        try {
            $q = Doctrine_Query :: create()
                    ->from('JobCandidateVacancy jcv')
                    ->where('jcv.candidateId = ?', $candidateId)
                    ->andWhere('jcv.vacancyId = ?', $vacancyId);
            return $q->fetchOne();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

}
