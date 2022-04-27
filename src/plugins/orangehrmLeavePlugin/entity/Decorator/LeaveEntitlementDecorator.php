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

use DateTime;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\LeaveEntitlement;
use OrangeHRM\Entity\LeaveEntitlementType;
use OrangeHRM\Entity\LeaveType;
use OrangeHRM\Leave\Traits\Service\LeaveEntitlementServiceTrait;

class LeaveEntitlementDecorator
{
    use EntityManagerHelperTrait;
    use DateTimeHelperTrait;
    use LeaveEntitlementServiceTrait;

    /**
     * @var LeaveEntitlement
     */
    private LeaveEntitlement $leaveEntitlement;

    /**
     * @param LeaveEntitlement $leaveEntitlement
     */
    public function __construct(LeaveEntitlement $leaveEntitlement)
    {
        $this->leaveEntitlement = $leaveEntitlement;
    }

    /**
     * @return LeaveEntitlement
     */
    protected function getLeaveEntitlement(): LeaveEntitlement
    {
        return $this->leaveEntitlement;
    }

    /**
     * @return float
     */
    public function getAvailableDays(): float
    {
        $available = $this->getLeaveEntitlement()->getNoOfDays();
        $daysUsed = $this->getLeaveEntitlement()->getDaysUsed();

        if (!empty($daysUsed)) {
            $available -= $daysUsed;
        }

        return $available;
    }

    /**
     * @param DateTime $date
     * @return bool
     */
    public function withinPeriod(DateTime $date): bool
    {
        return ($date >= $this->getLeaveEntitlement()->getFromDate()) &&
            ($date <= $this->getLeaveEntitlement()->getToDate());
    }

    /**
     * @param int $id
     */
    public function setEntitlementTypeById(int $id): void
    {
        /** @var LeaveEntitlementType $entitlementType */
        $entitlementType = $this->getReference(LeaveEntitlementType::class, $id);
        $this->getLeaveEntitlement()->setEntitlementType($entitlementType);
    }

    /**
     * @param int $empNumber
     */
    public function setEmployeeByEmpNumber(int $empNumber): void
    {
        /** @var Employee $employee */
        $employee = $this->getReference(Employee::class, $empNumber);
        $this->getLeaveEntitlement()->setEmployee($employee);
    }

    /**
     * @param int $id
     */
    public function setLeaveTypeById(int $id): void
    {
        /** @var LeaveType $leaveType */
        $leaveType = $this->getReference(LeaveType::class, $id);
        $this->getLeaveEntitlement()->setLeaveType($leaveType);
    }

    /**
     * @return string
     */
    public function getFromDate(): string
    {
        return $this->getDateTimeHelper()->formatDateTimeToYmd($this->getLeaveEntitlement()->getFromDate());
    }

    /**
     * @return string
     */
    public function getToDate(): string
    {
        return $this->getDateTimeHelper()->formatDateTimeToYmd($this->getLeaveEntitlement()->getToDate());
    }

    /**
     * @return string|null
     */
    public function getCreditedDate(): ?string
    {
        return $this->getDateTimeHelper()->formatDateTimeToYmd($this->getLeaveEntitlement()->getCreditedDate());
    }

    /**
     * @return bool
     */
    public function isDeletable(): bool
    {
        return $this->getLeaveEntitlementService()->isDeletable($this->getLeaveEntitlement());
    }
}
