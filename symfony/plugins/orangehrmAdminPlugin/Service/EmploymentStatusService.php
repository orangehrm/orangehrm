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

use OrangeHRM\Admin\Dao\EmploymentStatusDao;
use OrangeHRM\Admin\Dto\EmploymentStatusSearchFilterParams;
use OrangeHRM\Admin\Service\Model\EmploymentStatusModel;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Core\Traits\Service\NormalizerServiceTrait;
use OrangeHRM\Entity\EmploymentStatus;

class EmploymentStatusService
{
    use NormalizerServiceTrait;

    /**
     * @var EmploymentStatusDao|null
     */
    private ?EmploymentStatusDao $empStatusDao = null;

    /**
     *
     * @return EmploymentStatusDao
     */
    public function getEmploymentStatusDao(): EmploymentStatusDao
    {
        if (empty($this->empStatusDao)) {
            $this->empStatusDao = new EmploymentStatusDao();
        }
        return $this->empStatusDao;
    }

    /**
     * @param EmploymentStatusDao $employmentStatusDao
     */
    public function setEmploymentStatusDao(EmploymentStatusDao $employmentStatusDao): void
    {
        $this->empStatusDao = $employmentStatusDao;
    }

    /**
     * @param int $id
     * @return EmploymentStatus
     * @throws DaoException
     */
    public function getEmploymentStatusById(int $id): EmploymentStatus
    {
        return $this->getEmploymentStatusDao()->getEmploymentStatusById($id);
    }

    /**
     * @param EmploymentStatus $employmentStatus
     * @return EmploymentStatus
     * @throws DaoException
     */
    public function saveEmploymentStatus(EmploymentStatus $employmentStatus): EmploymentStatus
    {
        return $this->getEmploymentStatusDao()->saveEmploymentStatus($employmentStatus);
    }

    /**
     * @param array $toBeDeletedEmploymentStatusIds
     * @return int
     * @throws DaoException
     */
    public function deleteEmploymentStatus(array $toBeDeletedEmploymentStatusIds): int
    {
        return $this->getEmploymentStatusDao()->deleteEmploymentStatus($toBeDeletedEmploymentStatusIds);
    }

    /**
     * @param EmploymentStatusSearchFilterParams $employmentStatusSearchParams
     * @return array
     * @throws DaoException
     */
    public function searchEmploymentStatus(EmploymentStatusSearchFilterParams $employmentStatusSearchParams): array
    {
        return $this->getEmploymentStatusDao()->searchEmploymentStatus($employmentStatusSearchParams);
    }

    /**
     * @param EmploymentStatusSearchFilterParams $employmentStatusSearchParams
     * @return int
     * @throws DaoException
     */
    public function getSearchEmploymentStatusesCount(
        EmploymentStatusSearchFilterParams $employmentStatusSearchParams
    ): int {
        return $this->getEmploymentStatusDao()->getSearchEmploymentStatusesCount($employmentStatusSearchParams);
    }

    /**
     * @return array
     */
    public function getEmploymentStatusArray(): array
    {
        $employmentStatusSearchParams = new EmploymentStatusSearchFilterParams();
        $employmentStatusSearchParams->setLimit(0); // to get all records
        $employmentStatuses = $this->searchEmploymentStatus($employmentStatusSearchParams);
        return $this->getNormalizerService()->normalizeArray(EmploymentStatusModel::class, $employmentStatuses);
    }
}
