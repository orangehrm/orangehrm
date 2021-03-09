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

use OrangeHRM\Entity\JobCategory;

class JobCategoryService
{
    /**
     * @var JobCategoryDao
     */
    private $jobCatDao;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->jobCatDao = new JobCategoryDao();
    }

    /**
     * @return JobCategoryDao
     */
    public function getJobCategoryDao()
    {
        return $this->jobCatDao;
    }

    /**
     * @param JobCategoryDao $jobCategoryDao
     */
    public function setJobCategoryDao(JobCategoryDao $jobCategoryDao)
    {
        $this->jobCatDao = $jobCategoryDao;
    }

    /**
     * @param string $sortField
     * @param string $sortOrder
     * @param null $limit
     * @param null $offset
     * @param false $count
     * @return int|mixed|string
     * @throws DaoException
     */
    public function getJobCategoryList(
        $sortField = 'jc.name',
        $sortOrder = 'ASC',
        $limit = null,
        $offset = null,
        $count = false
    ) {
        return $this->jobCatDao->getJobCategoryList($sortField, $sortOrder, $limit, $offset, $count);
    }

    /**
     * @param int $jobCatId
     * @return object|JobCategory|null
     * @throws DaoException
     */
    public function getJobCategoryById(int $jobCatId)
    {
        return $this->jobCatDao->getJobCategoryById($jobCatId);
    }

    /**
     * @param JobCategory $jobCategory
     * @return JobCategory
     * @throws DaoException
     */
    public function saveJobCategory(JobCategory $jobCategory): JobCategory
    {
        return $this->jobCatDao->saveJobCategory($jobCategory);
    }

    /**
     * @param array $toBeDeletedJobCategoryIds
     * @return int|mixed|string
     * @throws DaoException
     */
    public function deleteJobCategory(array $toBeDeletedJobCategoryIds)
    {
        return $this->jobCatDao->deleteJobCategory($toBeDeletedJobCategoryIds);
    }
}
