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

namespace OrangeHRM\Pim\Service;

use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmployeeAttachment;
use OrangeHRM\Pim\Dao\EmployeeAttachmentDao;
use OrangeHRM\Pim\Dto\PartialEmployeeAttachment;

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
     * @return PartialEmployeeAttachment[]
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
     */
    public function getEmployeeAttachment(int $empNumber, int $attachId, ?string $screen = null): ?EmployeeAttachment
    {
        return $this->getEmployeeAttachmentDao()->getEmployeeAttachment($empNumber, $attachId, $screen);
    }

    /**
     * @param int $empNumber
     * @param int $attachId
     * @param string|null $screen
     * @return PartialEmployeeAttachment|null
     */
    public function getEmployeeAttachmentDetails(int $empNumber, int $attachId, ?string $screen = null): ?PartialEmployeeAttachment
    {
        return $this->getEmployeeAttachmentDao()->getPartialEmployeeAttachment($empNumber, $attachId, $screen);
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
     */
    public function deleteEmployeeAttachments(int $empNumber, string $screen, array $toBeDeletedIds): int
    {
        return $this->getEmployeeAttachmentDao()->deleteEmployeeAttachments($empNumber, $screen, $toBeDeletedIds);
    }

    /**
     * @param int $empNumber
     * @param int $attachId
     * @return EmployeeAttachment|null
     */
    public function getAccessibleEmployeeAttachment(int $empNumber, int $attachId): ?EmployeeAttachment
    {
        $accessibleEmpNumbers = $this->getUserRoleManager()->getAccessibleEntityIds(Employee::class);
        if (in_array($empNumber, $accessibleEmpNumbers) || $this->getUserRoleManagerHelper()->isSelfByEmpNumber($empNumber)) {
            return $this->getEmployeeAttachment($empNumber, $attachId);
        }
        return null;
    }
}
