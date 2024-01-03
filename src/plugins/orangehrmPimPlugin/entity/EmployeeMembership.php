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

namespace OrangeHRM\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use OrangeHRM\Entity\Decorator\DecoratorTrait;
use OrangeHRM\Entity\Decorator\EmployeeMembershipDecorator;

/**
 * @method EmployeeMembershipDecorator getDecorator()
 *
 * @ORM\Table(name="hs_hr_emp_member_detail")
 * @ORM\Entity
 */
class EmployeeMembership
{
    use DecoratorTrait;

    public const COMPANY = 'Company';
    public const INDIVIDUAL = 'Individual';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", length=6)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var Employee
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee", inversedBy="memberships", cascade={"persist"})
     * @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     */
    private Employee $employee;

    /**
     * @var Membership
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Membership")
     * @ORM\JoinColumn(name="membship_code", referencedColumnName="id")
     */
    private Membership $membership;

    /**
     * @var string | null
     *
     * @ORM\Column(name="ememb_subscript_amount", type="decimal", precision=15, scale=2, nullable=true)
     */
    private ?string $subscriptionFee;

    /**
     * @var string | null
     *
     * @ORM\Column(name="ememb_subscript_ownership", type="string", length=20, nullable=true)
     */
    private ?string $subscriptionPaidBy;

    /**
     * @var string | null
     *
     * @ORM\Column(name="ememb_subs_currency", type="string", length=20, nullable=true)
     */
    private ?string $subscriptionCurrency;

    /**
     * @var DateTime | null
     *
     * @ORM\Column(name="ememb_commence_date", type="date", nullable=true)
     */
    private ?DateTime $subscriptionCommenceDate;

    /**
     * @var DateTime | null
     *
     * @ORM\Column(name="ememb_renewal_date", type="date", nullable=true)
     */
    private ?DateTime $subscriptionRenewalDate;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return Employee
     */
    public function getEmployee(): Employee
    {
        return $this->employee;
    }

    /**
     * @param Employee $employee
     */
    public function setEmployee(Employee $employee): void
    {
        $this->employee = $employee;
    }

    /**
     * @return Membership
     */
    public function getMembership(): Membership
    {
        return $this->membership;
    }

    /**
     * @param Membership $membership
     */
    public function setMembership(Membership $membership): void
    {
        $this->membership = $membership;
    }

    /**
     * @return string|null
     */
    public function getSubscriptionFee(): ?string
    {
        return $this->subscriptionFee;
    }

    /**
     * @param string|null $subscriptionFee
     */
    public function setSubscriptionFee(?string $subscriptionFee): void
    {
        $this->subscriptionFee = $subscriptionFee;
    }

    /**
     * @return string|null
     */
    public function getSubscriptionPaidBy(): ?string
    {
        return $this->subscriptionPaidBy;
    }

    /**
     * @param string|null $subscriptionPaidBy
     */
    public function setSubscriptionPaidBy(?string $subscriptionPaidBy): void
    {
        $this->subscriptionPaidBy = $subscriptionPaidBy;
    }

    /**
     * @return string|null
     */
    public function getSubscriptionCurrency(): ?string
    {
        return $this->subscriptionCurrency;
    }

    /**
     * @param string|null $subscriptionCurrency
     */
    public function setSubscriptionCurrency(?string $subscriptionCurrency): void
    {
        $this->subscriptionCurrency = $subscriptionCurrency;
    }

    /**
     * @return DateTime|null
     */
    public function getSubscriptionCommenceDate(): ?DateTime
    {
        return $this->subscriptionCommenceDate;
    }

    /**
     * @param DateTime|null $subscriptionCommenceDate
     */
    public function setSubscriptionCommenceDate(?DateTime $subscriptionCommenceDate): void
    {
        $this->subscriptionCommenceDate = $subscriptionCommenceDate;
    }

    /**
     * @return DateTime|null
     */
    public function getSubscriptionRenewalDate(): ?DateTime
    {
        return $this->subscriptionRenewalDate;
    }

    /**
     * @param DateTime|null $subscriptionRenewalDate
     */
    public function setSubscriptionRenewalDate(?DateTime $subscriptionRenewalDate): void
    {
        $this->subscriptionRenewalDate = $subscriptionRenewalDate;
    }
}
