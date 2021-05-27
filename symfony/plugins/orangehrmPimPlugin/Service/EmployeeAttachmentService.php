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

namespace OrangeHRM\Pim\Service;

use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmployeeAttachment;
use OrangeHRM\Pim\Dao\EmployeeAttachmentDao;

class EmployeeAttachmentService
{
    use UserRoleManagerTrait;

    /**
     * @var EmployeeAttachmentDao|null
     */
    protected ?EmployeeAttachmentDao $employeeAttachmentDao = null;

    /**
     * @return EmployeeAttachmentDao
     */
    public function getEmployeeAttachmentDao(): EmployeeAttachmentDao
    {
        if (!$this->employeeAttachmentDao instanceof EmployeeAttachmentDao) {
            $this->employeeAttachmentDao = new EmployeeAttachmentDao();
        }
        return $this->employeeAttachmentDao;
    }

    /**
     * @param int $empNumber
     * @param string $screen
     * @return EmployeeAttachment[]
     * @throws DaoException
     */
    public function getEmployeeAttachments(int $empNumber, string $screen): array
    {
        return $this->getEmployeeAttachmentDao()->getEmployeeAttachments($empNumber, $screen);
    }

    /**
     * @param int $empNumber
     * @param int $attachId
     * @param string|null $screen
     * @return EmployeeAttachment|null
     * @throws DaoException
     */
    public function getEmployeeAttachment(int $empNumber, int $attachId, ?string $screen = null): ?EmployeeAttachment
    {
        return $this->getEmployeeAttachmentDao()->getEmployeeAttachment($empNumber, $attachId, $screen);
    }

    /**
     * @param EmployeeAttachment $employeeAttachment
     * @return EmployeeAttachment
     */
    public function saveEmployeeAttachment(EmployeeAttachment $employeeAttachment): EmployeeAttachment
    {
        return $this->getEmployeeAttachmentDao()->saveEmployeeAttachment($employeeAttachment);
    }

    /**
     * @param int $empNumber
     * @param string $screen
     * @param array $toBeDeletedIds
     * @return int
     * @throws DaoException
     */
    public function deleteEmployeeAttachments(int $empNumber, string $screen, array $toBeDeletedIds): int
    {
        return $this->getEmployeeAttachmentDao()->deleteEmployeeAttachments($empNumber, $screen, $toBeDeletedIds);
    }

    /**
     * @param int $empNumber
     * @param int $attachId
     * @return EmployeeAttachment|null
     * @throws DaoException
     */
    public function getAccessibleEmployeeAttachment(int $empNumber, int $attachId): ?EmployeeAttachment
    {
        $accessibleEmpNumbers = $this->getUserRoleManager()->getAccessibleEntityIds(Employee::class);
        if (in_array($empNumber, $accessibleEmpNumbers)) {
            return $this->getEmployeeAttachment($empNumber, $attachId);
        }
        return null;
    }
}
