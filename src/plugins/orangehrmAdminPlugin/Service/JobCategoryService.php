<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Admin\Service;

use OrangeHRM\Admin\Dao\JobCategoryDao;
use OrangeHRM\Admin\Service\Model\JobCategoryModel;
use OrangeHRM\Core\Traits\Service\NormalizerServiceTrait;
use OrangeHRM\Entity\JobCategory;

class JobCategoryService
{
    use NormalizerServiceTrait;

    /**
     * @var JobCategoryDao
     */
    private JobCategoryDao $jobCatDao;

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
    public function getJobCategoryDao(): JobCategoryDao
    {
        return $this->jobCatDao;
    }

    /**
     * @param JobCategoryDao $jobCategoryDao
     */
    public function setJobCategoryDao(JobCategoryDao $jobCategoryDao): void
    {
        $this->jobCatDao = $jobCategoryDao;
    }

    /**
     * @param string $sortField
     * @param string $sortOrder
     * @param int|null $limit
     * @param int|null $offset
     * @param false $count
     * @return int|JobCategory[]
     */
    public function getJobCategoryList(
        string $sortField = 'jc.name',
        string $sortOrder = 'ASC',
        ?int $limit = null,
        ?int $offset = null,
        bool $count = false
    ) {
        return $this->jobCatDao->getJobCategoryList($sortField, $sortOrder, $limit, $offset, $count);
    }

    /**
     * @param int $jobCatId
     * @return object|JobCategory|null
     */
    public function getJobCategoryById(int $jobCatId): ?JobCategory
    {
        return $this->jobCatDao->getJobCategoryById($jobCatId);
    }

    /**
     * @param JobCategory $jobCategory
     * @return JobCategory
     */
    public function saveJobCategory(JobCategory $jobCategory): JobCategory
    {
        return $this->jobCatDao->saveJobCategory($jobCategory);
    }

    /**
     * @param array $toBeDeletedJobCategoryIds
     * @return int
     */
    public function deleteJobCategory(array $toBeDeletedJobCategoryIds): int
    {
        return $this->jobCatDao->deleteJobCategory($toBeDeletedJobCategoryIds);
    }

    /**
     * @return array
     */
    public function getJobCategoryArray(): array
    {
        $jobCategories = $this->getJobCategoryList();
        return $this->getNormalizerService()->normalizeArray(JobCategoryModel::class, $jobCategories);
    }
}
