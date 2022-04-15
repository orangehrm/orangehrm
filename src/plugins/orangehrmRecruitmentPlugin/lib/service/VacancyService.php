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
 * Vacancy Service
 *
 */
class VacancyService extends BaseService {

    private $vacancyDao;

    /**
     * Get Vacancy Dao
     * @return VacancyDao
     */
    public function getVacancyDao() {
        return $this->vacancyDao;
    }

    /**
     * Set Vacancy Dao
     * @param VacancyDao $vacancyDao
     * @return void
     */
    public function setVacancyDao(VacancyDao $vacancyDao) {
        $this->vacancyDao = $vacancyDao;
    }

    /**
     * Construct
     */
    public function __construct() {
        $this->vacancyDao = new VacancyDao();
    }

    /**
     * Retrieve hiring managers list
     * @returns array
     * @throws RecruitmentException
     */
    public function getHiringManagersList($jobTitle, $vacancyId, $allowedVacancyList) {
        return $this->getVacancyDao()->getHiringManagersList($jobTitle, $vacancyId, $allowedVacancyList);
    }

    /**
     * Retrieve hiring managers list
     * @returns array
     * @throws RecruitmentException
     */
    public function getVacancyListForJobTitle($jobTitle, $allowedVacancyList, $asArray = false) {
        return $this->getVacancyDao()->getVacancyListForJobTitle($jobTitle, $allowedVacancyList, $asArray);
    }

    /**
     * Retrieve vacancy list
     * @returns array
     * @throws RecruitmentException
     */
    public function getVacancyList() {
        return $this->getVacancyDao()->getVacancyList();
    }

    /**
     * Retrieve vacancy list
     * @returns array
     * @throws RecruitmentException
     */
    public function getAllVacancies($status = "") {
        return $this->getVacancyDao()->getAllVacancies($status);
    }

    /**
     * Return an array of vacancy properties
     * 
     * <pre>
     * Ex: $properties = array('name', 'id');
     * 
     * For above $properties parameter there will be an array like below as the response.
     * 
     * array(
     *          0 => array('name' => 'Software Architect', 'id' => 1),
     *          1 => array('name' => 'Accountant Grade', 'id' => 2)
     * )
     * </pre>
     * 
     * @version 2.7.1
     * @param Array $properties List of Vacancy properties
     * @param Integer $status Vacancy Status
     * @returns Array Vacancy Property List
     */
    public function getVacancyPropertyList($properties = array(), $status) {
        return $this->getVacancyDao()->getVacancyPropertyList($properties, $status);
    }
    
    /**
     * Get list of vacancies published to web/rss
     * 
     * @return type Array of JobVacancy objects
     * @throws RecruitmentException
     */
    public function getPublishedVacancies() {
        return $this->getVacancyDao()->getPublishedVacancies();
    }

    /**
     * Retrieve vacancy list
     * @returns array
     * @throws RecruitmentException
     */
    public function saveJobVacancy(JobVacancy $jobVacancy) {
        return $this->getVacancyDao()->saveJobVacancy($jobVacancy);
    }

    /**
     *
     * @param <type> $srchParams
     * @return doctrine collection
     */
    public function searchVacancies($srchParams) {
        return $this->getVacancyDao()->searchVacancies($srchParams);
    }

    /**
     *
     * @param <type> $srchParams
     * @return count
     */
    public function searchVacanciesCount($srchParams) {
        return $this->getVacancyDao()->searchVacanciesCount($srchParams);
    }

    /**
     *
     */
    public function getVacancyById($vacancyId) {
        return $this->getVacancyDao()->getVacancyById($vacancyId);
    }

    /**
     * Delete vacancies
     * @param array $toBeDeletedVacancyIds
     * @return boolean
     */
    public function deleteVacancies($toBeDeletedVacancyIds) {

        if (count($toBeDeletedVacancyIds) > 0) {

            $isDeletionSucceeded = $this->getVacancyDao()->deleteVacancies($toBeDeletedVacancyIds);

            if ($isDeletionSucceeded) {
                return true;
            }
        }

        return false;
    }

    public function getVacancyListForUserRole($role, $empNumber) {
        return $this->getVacancyDao()->getVacancyListForUserRole($role, $empNumber);
    }

    public function getVacancyIdList() {
        return $this->getVacancyDao()->getVacancyIdList();       
    }

    public function getVacancyIdListForHiringManager($empNumber) {
        return $this->getVacancyDao()->getVacancyIdListForHiringManager($empNumber);
    }
    
    public function getVacancyIdListForInterviewer($empNumber) {
        return $this->getVacancyDao()->getVacancyIdListForInterviewer($empNumber);        
    }    
    
    /**
     *
     * @param int $empNumber
     * @return bool 
     */
    public function isHiringManager($empNumber) {
        try {
            $results = $this->searchVacancies(array(
                'jobTitle' => null,
                'jobVacancy' => null,
                'status' => null,
                'hiringManager' => $empNumber,
                'offset' => 0,
                'noOfRecords' => 1,
                    ));

            return ($results->count() > 0);
        } catch (DaoException $e) {
            // TODO: Warn
            return false;
        }
    }
    
    /**
     *
     * @param int $empNumber
     * @return bool 
     */
    public function isInterviewer($empNumber) {
        try {
            $result = $this->getVacancyDao()->searchInterviews($empNumber);
            return ($result->count() > 0);
        } catch (DaoException $e) {
            // TODO: Warn
            return false;
        }
    }

}

