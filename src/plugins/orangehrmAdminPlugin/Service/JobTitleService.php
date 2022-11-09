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

namespace OrangeHRM\Admin\Service;

use OrangeHRM\Admin\Dao\JobTitleDao;
use OrangeHRM\Admin\Service\Model\JobTitleModel;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Core\Traits\Service\NormalizerServiceTrait;
use OrangeHRM\Entity\JobSpecificationAttachment;
use OrangeHRM\Entity\JobTitle;

class JobTitleService
{
    use NormalizerServiceTrait;

    /**
     * @var JobTitleDao|null
     */
    private ?JobTitleDao $jobTitleDao = null;

    /**
     * @return JobTitleDao
     */
    public function getJobTitleDao(): JobTitleDao
    {
        if (!($this->jobTitleDao instanceof JobTitleDao)) {
            $this->jobTitleDao = new JobTitleDao();
        }
        return $this->jobTitleDao;
    }

    /**
     * @param JobTitleDao $jobTitleDao
     */
    public function setJobTitleDao(JobTitleDao $jobTitleDao): void
    {
        $this->jobTitleDao = $jobTitleDao;
    }

    /**
     * Returns JobTitlelist - By default this will returns the active jobTitle list
     * To get the all the jobTitles(with deleted) should pass the $activeOnly as false
     *
     * @param bool $activeOnly
     * @return int|JobTitle[]
     * @throws DaoException
     */
    public function getJobTitleList(bool $activeOnly = true)
    {
        return $this->getJobTitleDao()->getJobTitleList($activeOnly);
    }

    /**
     * This will flag the jobTitles as deleted
     *
     * @param array $toBeDeletedJobTitleIds
     * @return int number of affected rows
     * @throws DaoException
     */
    public function deleteJobTitle(array $toBeDeletedJobTitleIds): int
    {
        return $this->getJobTitleDao()->deleteJobTitle($toBeDeletedJobTitleIds);
    }

    /**
     * Will return the JobTitle doctrine object for a purticular id
     *
     * @param int $jobTitleId
     * @return JobTitle|null
     * @throws DaoException
     */
    public function getJobTitleById(int $jobTitleId): ?JobTitle
    {
        return $this->getJobTitleDao()->getJobTitleById($jobTitleId);
    }

    /**
     * Will return the JobSpecificationAttachment doctrine object for a purticular id
     *
     * @param int $attachId
     * @return JobSpecificationAttachment|null
     * @throws DaoException
     */
    public function getJobSpecAttachmentById($attachId): ?JobSpecificationAttachment
    {
        return $this->getJobTitleDao()->getJobSpecAttachmentById($attachId);
    }

    /**
     * @param JobTitle $jobTitle
     * @return JobTitle
     * @throws DaoException
     */
    public function saveJobTitle(JobTitle $jobTitle): JobTitle
    {
        return $this->getJobTitleDao()->saveJobTitle($jobTitle);
    }

    /**
     * @param JobSpecificationAttachment $jobSpecificationAttachment
     * @return JobSpecificationAttachment
     * @throws DaoException
     */
    public function saveJobSpecificationAttachment(
        JobSpecificationAttachment $jobSpecificationAttachment
    ): JobSpecificationAttachment {
        return $this->getJobTitleDao()->saveJobSpecificationAttachment($jobSpecificationAttachment);
    }

    /**
     * @param JobSpecificationAttachment $jobSpecificationAttachment
     * @return JobSpecificationAttachment
     * @throws DaoException
     */
    public function deleteJobSpecificationAttachment(
        JobSpecificationAttachment $jobSpecificationAttachment
    ): JobSpecificationAttachment {
        return $this->getJobTitleDao()->deleteJobSpecificationAttachment($jobSpecificationAttachment);
    }

    /**
     * @return array
     */
    public function getJobTitleArray(): array
    {
        $jobTitles = $this->getJobTitleList();
        return $this->getNormalizerService()->normalizeArray(JobTitleModel::class, $jobTitles);
    }

    /**
     * @param int $empNumber
     * @return array
     */
    public function getJobTitleArrayForEmployee(int $empNumber): array
    {
        $jobTitles = $this->getJobTitleDao()->getJobTitlesForEmployee($empNumber);
        return $this->getNormalizerService()->normalizeArray(JobTitleModel::class, $jobTitles);
    }
}
