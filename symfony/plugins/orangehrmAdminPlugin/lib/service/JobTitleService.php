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
class JobTitleService extends BaseService {

    private $jobTitleDao;

    public function __construct() {
        $this->jobTitleDao = new JobTitleDao();
    }

    public function getJobTitleDao() {
        if (!($this->jobTitleDao instanceof JobTitleDao)) {
            $this->jobTitleDao = new JobTitleDao();
        }
        return $this->jobTitleDao;
    }

    public function setJobTitleDao(JobTitleDao $jobTitleDao) {
        $this->jobTitleDao = $jobTitleDao;
    }

    /**
     * Returns JobTitlelist - By default this will returns the active jobTitle list
     * To get the all the jobTitles(with deleted) should pass the $activeOnly as false
     *
     * @param string $sortField
     * @param string $sortOrder
     * @param boolean $activeOnly
     * @return JobTitle Doctrine collection
     */
    public function getJobTitleList($sortField='jobTitleName', $sortOrder='ASC', $activeOnly = true, $limit = null, $offset = null) {
        return $this->getJobTitleDao()->getJobTitleList($sortField, $sortOrder, $activeOnly, $limit, $offset);
    }

    /**
     * This will flag the jobTitles as deleted
     *
     * @param array $toBeDeletedJobTitleIds
     * @return int number of affected rows
     */
    public function deleteJobTitle($toBeDeletedJobTitleIds) {
        return $this->getJobTitleDao()->deleteJobTitle($toBeDeletedJobTitleIds);
    }

    /**
     * Will return the JobTitle doctrine object for a purticular id
     *
     * @param int $jobTitleId
     * @return JobTitle doctrine object
     */
    public function getJobTitleById($jobTitleId) {
        return $this->getJobTitleDao()->getJobTitleById($jobTitleId);
    }

    /**
     * Will return the JobSpecificationAttachment doctrine object for a purticular id
     *
     * @param int $attachId
     * @return JobSpecificationAttachment doctrine object
     */
    public function getJobSpecAttachmentById($attachId) {
        return $this->getJobTitleDao()->getJobSpecAttachmentById($attachId);
    }

}

