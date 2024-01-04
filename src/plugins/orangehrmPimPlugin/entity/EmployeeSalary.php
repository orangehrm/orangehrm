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
use OrangeHRM\Entity\Decorator\EmployeeSalaryDecorator;

/**
 * @method EmployeeSalaryDecorator getDecorator()
 *
 * @ORM\Table(name="hs_hr_emp_basicsalary")
 * @ORM\Entity
 * @ORM\EntityListeners({"OrangeHRM\Entity\Listener\EmployeeSalaryListener"})
 */
class EmployeeSalary
{
    use DecoratorTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var Employee
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee", inversedBy="salaries", cascade={"persist"})
     * @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     */
    private Employee $employee;

    /**
     * @var PayGrade|null
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\PayGrade")
     * @ORM\JoinColumn(name="sal_grd_code", referencedColumnName="id", nullable=true)
     */
    private ?PayGrade $payGrade = null;

    /**
     * @var CurrencyType
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\CurrencyType")
     * @ORM\JoinColumn(name="currency_id", referencedColumnName="currency_id")
     */
    private CurrencyType $currencyType;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ebsal_basic_salary", type="string", length=100, nullable=true)
     */
    private ?string $amount = null;

    /**
     * @var PayPeriod|null
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\PayPeriod")
     * @ORM\JoinColumn(name="payperiod_code", referencedColumnName="payperiod_code", nullable=true)
     */
    private ?PayPeriod $payPeriod = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="salary_component", type="string", length=100, nullable=true)
     */
    private ?string $salaryName = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="comments", type="string", length=255, nullable=true)
     */
    private ?string $comment = null;

    /**
     * @var EmpDirectDebit|null
     *
     * @ORM\OneToOne(targetEntity="OrangeHRM\Entity\EmpDirectDebit", mappedBy="salary", cascade={"persist"})
     */
    private ?EmpDirectDebit $directDebit = null;

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
     * @return PayGrade|null
     */
    public function getPayGrade(): ?PayGrade
    {
        return $this->payGrade;
    }

    /**
     * @param PayGrade|null $payGrade
     */
    public function setPayGrade(?PayGrade $payGrade): void
    {
        $this->payGrade = $payGrade;
    }

    /**
     * @return CurrencyType
     */
    public function getCurrencyType(): CurrencyType
    {
        return $this->currencyType;
    }

    /**
     * @param CurrencyType $currencyType
     */
    public function setCurrencyType(CurrencyType $currencyType): void
    {
        $this->currencyType = $currencyType;
    }

    /**
     * @return string|null
     */
    public function getAmount(): ?string
    {
        return $this->amount;
    }

    /**
     * @param string|null $amount
     */
    public function setAmount(?string $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return PayPeriod|null
     */
    public function getPayPeriod(): ?PayPeriod
    {
        return $this->payPeriod;
    }

    /**
     * @param PayPeriod|null $payPeriod
     */
    public function setPayPeriod(?PayPeriod $payPeriod): void
    {
        $this->payPeriod = $payPeriod;
    }

    /**
     * @return string|null
     */
    public function getSalaryName(): ?string
    {
        return $this->salaryName;
    }

    /**
     * @param string|null $salaryName
     */
    public function setSalaryName(?string $salaryName): void
    {
        $this->salaryName = $salaryName;
    }

    /**
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param string|null $comment
     */
    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }

    /**
     * @return EmpDirectDebit|null
     */
    public function getDirectDebit(): ?EmpDirectDebit
    {
        return $this->directDebit;
    }

    /**
     * @param EmpDirectDebit|null $directDebit
     */
    public function setDirectDebit(?EmpDirectDebit $directDebit): void
    {
        $this->directDebit = $directDebit;
    }
}
