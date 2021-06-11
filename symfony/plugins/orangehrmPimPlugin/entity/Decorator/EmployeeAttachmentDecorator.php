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

namespace OrangeHRM\Entity\Decorator;

use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmployeeAttachment;

class EmployeeAttachmentDecorator
{
    use EntityManagerHelperTrait;
    use DateTimeHelperTrait;

    /**
     * @var EmployeeAttachment
     */
    protected EmployeeAttachment $employeeAttachment;

    /**
     * This property to read `attachment` resource from `EmployeeAttachment`
     * @var string|null
     */
    protected ?string $attachmentString = null;

    /**
     * @param EmployeeAttachment $employee
     */
    public function __construct(EmployeeAttachment $employee)
    {
        $this->employeeAttachment = $employee;
    }

    /**
     * @return EmployeeAttachment
     */
    protected function getEmployeeAttachment(): EmployeeAttachment
    {
        return $this->employeeAttachment;
    }

    /**
     * @return string
     */
    public function getAttachedDate(): string
    {
        $dateTime = $this->getEmployeeAttachment()->getAttachedTime();
        return $this->getDateTimeHelper()->formatDateTimeToYmd($dateTime);
    }

    /**
     * @return string
     */
    public function getAttachedTime(): string
    {
        $dateTime = $this->getEmployeeAttachment()->getAttachedTime();
        return $this->getDateTimeHelper()->formatDateTimeToTimeString($dateTime);
    }

    /**
     * @param int $empNumber
     */
    public function setEmployeeByEmpNumber(int $empNumber): void
    {
        /** @var Employee|null $employee */
        $employee = $this->getReference(Employee::class, $empNumber);
        $this->getEmployeeAttachment()->setEmployee($employee);
    }

    /**
     * @return string
     */
    public function getAttachment(): string
    {
        if (is_null($this->attachmentString)) {
            $this->attachmentString = stream_get_contents($this->getEmployeeAttachment()->getAttachment());
        }
        return $this->attachmentString;
    }
}
