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

namespace OrangeHRM\Entity\Decorator;

use OrangeHRM\Admin\Service\PayGradeService;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmployeeMembership;
use OrangeHRM\Entity\Membership;
use OrangeHRM\Framework\Services;

class EmployeeMembershipDecorator
{
    use EntityManagerHelperTrait;
    use DateTimeHelperTrait;
    use ServiceContainerTrait;

    /**
     * @var EmployeeMembership
     */
    protected EmployeeMembership $employeeMembership;

    /**
     * EmployeeMembershipDecorator constructor.
     * @param EmployeeMembership $employeeMembership
     */
    public function __construct(EmployeeMembership $employeeMembership)
    {
        $this->employeeMembership = $employeeMembership;
    }

    /**
     * @return EmployeeMembership
     */
    protected function getEmployeeMembership(): EmployeeMembership
    {
        return $this->employeeMembership;
    }

    /**
     * @param int $empNumber
     */
    public function setEmployeeByEmpNumber(int $empNumber): void
    {
        /** @var Employee|null $employee */
        $employee = $this->getReference(Employee::class, $empNumber);
        $this->getEmployeeMembership()->setEmployee($employee);
    }

    /**
     * @param int $membershipId
     */
    public function setMembershipByMembershipId(int $membershipId): void
    {
        /** @var Membership|null $membership */
        $membership = $this->getReference(Membership::class, $membershipId);
        $this->getEmployeeMembership()->setMembership($membership);
    }

    /**
     * @return string|null
     */
    public function getSubscriptionCommenceDate(): ?string
    {
        $date = $this->getEmployeeMembership()->getSubscriptionCommenceDate();
        return $this->getDateTimeHelper()->formatDate($date);
    }

    /**
     * @return string|null
     */
    public function getSubscriptionRenewalDate(): ?string
    {
        $date = $this->getEmployeeMembership()->getSubscriptionRenewalDate();
        return $this->getDateTimeHelper()->formatDate($date);
    }

    /**
     * @return string|null
     */
    public function getCurrencyName(): ?string
    {
        $currencyCode = $this->getEmployeeMembership()->getSubscriptionCurrency();
        /** @var PayGradeService $payGradeService */
        $payGradeService = $this->getContainer()->get(Services::PAY_GRADE_SERVICE);
        if (is_null($currencyCode)) {
            return null;
        }
        $currency = $payGradeService->getCurrencyById($currencyCode);
        return $currency ? $currency->getName() : null;
    }
}
