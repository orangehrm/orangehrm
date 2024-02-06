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

use Doctrine\ORM\Mapping as ORM;
use OrangeHRM\Entity\Decorator\DecoratorTrait;
use OrangeHRM\Entity\Decorator\EmpUsTaxExemptionDecorator;

/**
 * @method EmpUsTaxExemptionDecorator getDecorator()
 *
 * @ORM\Table(name="hs_hr_emp_us_tax")
 * @ORM\Entity
 */
class EmpUsTaxExemption
{
    use DecoratorTrait;

    public const SINGLE = 'Single';
    public const MARRIED = 'Married';
    public const NON_RESIDENT_ALIEN = 'Non Resident Alien';
    public const NOT_APPLICABLE = 'Not Applicable';
    public const STATUS_SINGLE = 'S';
    public const STATUS_MARRIED = 'M';
    public const STATUS_NON_RESIDENT_ALIEN = 'NRA';
    public const STATUS_NOT_APPLICABLE = 'NA';
    public const STATUSES = [
        self::STATUS_SINGLE,
        self::STATUS_MARRIED,
        self::STATUS_NON_RESIDENT_ALIEN,
        self::STATUS_NOT_APPLICABLE
    ];

    /**
     * @var Employee
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="OrangeHRM\Entity\Employee", inversedBy="empUsTax", cascade={"persist"})
     * @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     */
    private Employee $employee;

    /**
     * @var string|null
     *
     * @ORM\Column(name="tax_federal_status", type="string", length=13, nullable=true)
     */
    private ?string $federalStatus = null;

    /**
     * @var int|null
     *
     * @ORM\Column(name="tax_federal_exceptions", type="integer", length=2, nullable=true, options={"default" : 0})
     */
    private ?int $federalExemptions = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="tax_state", type="string", length=13, nullable=true)
     */
    private ?string $state = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="tax_state_status", type="string", length=13, nullable=true)
     */
    private ?string $stateStatus = null;

    /**
     * @var int|null
     *
     * @ORM\Column(name="tax_state_exceptions", type="integer", length=2, nullable=true, options={"default" : 0})
     */
    private ?int $stateExemptions = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="tax_unemp_state", type="string", length=13, nullable=true)
     */
    private ?string $unemploymentState = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="tax_work_state", type="string", length=13, nullable=true)
     */
    private ?string $workState = null;

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
     * @return string|null
     */
    public function getFederalStatus(): ?string
    {
        return $this->federalStatus;
    }

    /**
     * @param string|null $federalStatus
     */
    public function setFederalStatus(?string $federalStatus): void
    {
        $this->federalStatus = $federalStatus;
    }

    /**
     * @return int|null
     */
    public function getFederalExemptions(): ?int
    {
        return $this->federalExemptions;
    }

    /**
     * @param int|null $federalExemptions
     */
    public function setFederalExemptions(?int $federalExemptions): void
    {
        $this->federalExemptions = $federalExemptions;
    }

    /**
     * @return string|null
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * @param string|null $state
     */
    public function setState(?string $state): void
    {
        $this->state = $state;
    }

    /**
     * @return string|null
     */
    public function getStateStatus(): ?string
    {
        return $this->stateStatus;
    }

    /**
     * @param string|null $stateStatus
     */
    public function setStateStatus(?string $stateStatus): void
    {
        $this->stateStatus = $stateStatus;
    }

    /**
     * @return int|null
     */
    public function getStateExemptions(): ?int
    {
        return $this->stateExemptions;
    }

    /**
     * @param int|null $stateExemptions
     */
    public function setStateExemptions(?int $stateExemptions): void
    {
        $this->stateExemptions = $stateExemptions;
    }

    /**
     * @return string|null
     */
    public function getUnemploymentState(): ?string
    {
        return $this->unemploymentState;
    }

    /**
     * @param string|null $unemploymentState
     */
    public function setUnemploymentState(?string $unemploymentState): void
    {
        $this->unemploymentState = $unemploymentState;
    }

    /**
     * @return string|null
     */
    public function getWorkState(): ?string
    {
        return $this->workState;
    }

    /**
     * @param string|null $workState
     */
    public function setWorkState(?string $workState): void
    {
        $this->workState = $workState;
    }
}
