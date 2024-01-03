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

use OrangeHRM\Entity\EmployeeAttachment;
use OrangeHRM\Pim\Dao\EmploymentContractDao;
use OrangeHRM\Pim\Dto\PartialEmployeeAttachment;

class EmploymentContractService
{
    /**
     * @var EmploymentContractDao|null
     */
    protected ?EmploymentContractDao $employmentContractDao = null;

    /**
     * @var EmployeeAttachmentService|null
     */
    protected ?EmployeeAttachmentService $employeeAttachmentService = null;

    /**
     * @return EmploymentContractDao
     */
    public function getEmploymentContractDao(): EmploymentContractDao
    {
        if (!$this->employmentContractDao instanceof EmploymentContractDao) {
            $this->employmentContractDao = new EmploymentContractDao();
        }
        return $this->employmentContractDao;
    }

    /**
     * @return EmployeeAttachmentService
     */
    public function getEmployeeAttachmentService(): EmployeeAttachmentService
    {
        if (!$this->employeeAttachmentService instanceof EmployeeAttachmentService) {
            $this->employeeAttachmentService = new EmployeeAttachmentService();
        }
        return $this->employeeAttachmentService;
    }

    /**
     * @param int $empNumber
     * @return PartialEmployeeAttachment|null
     */
    public function getContractAttachment(int $empNumber): ?PartialEmployeeAttachment
    {
        $employeeAttachments = $this->getEmployeeAttachmentService()->getEmployeeAttachments(
            $empNumber,
            EmployeeAttachment::SCREEN_JOB_CONTRACT
        );
        if (empty($employeeAttachments) || !isset($employeeAttachments[0])) {
            return null;
        }
        return $employeeAttachments[0];
    }

    /**
     * @param int $empNumber
     * @param int $attachId
     * @return EmployeeAttachment|null
     */
    public function getContractAttachmentById(int $empNumber, int $attachId): ?EmployeeAttachment
    {
        return $this->getEmployeeAttachmentService()->getEmployeeAttachment(
            $empNumber,
            $attachId,
            EmployeeAttachment::SCREEN_JOB_CONTRACT
        );
    }

    /**
     * @param EmployeeAttachment $employeeAttachment
     * @return EmployeeAttachment
     */
    public function saveContractAttachment(EmployeeAttachment $employeeAttachment): EmployeeAttachment
    {
        $employeeAttachment->setScreen(EmployeeAttachment::SCREEN_JOB_CONTRACT);
        return $this->getEmployeeAttachmentService()->saveEmployeeAttachment($employeeAttachment);
    }

    /**
     * @param EmployeeAttachment $employeeAttachment
     */
    public function deleteContractAttachment(EmployeeAttachment $employeeAttachment): void
    {
        $this->getEmployeeAttachmentService()->getEmployeeAttachmentDao()->deleteEmployeeAttachment(
            $employeeAttachment
        );
    }
}
