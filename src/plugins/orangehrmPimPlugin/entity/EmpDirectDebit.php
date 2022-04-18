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

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="hs_hr_emp_directdebit")
 * @ORM\Entity
 */
class EmpDirectDebit
{
    public const ACCOUNT_TYPE_SAVINGS = 'SAVINGS';
    public const ACCOUNT_TYPE_CHECKING = 'CHECKING';
    public const ACCOUNT_TYPE_OTHER = 'OTHER';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var EmployeeSalary
     *
     * @ORM\OneToOne(targetEntity="OrangeHRM\Entity\EmployeeSalary", inversedBy="directDebit")
     * @ORM\JoinColumn(name="salary_id", referencedColumnName="id", onDelete="Cascade")
     */
    private EmployeeSalary $salary;

    /**
     * @var int
     *
     * @ORM\Column(name="dd_routing_num", type="integer", length=9)
     */
    private int $routingNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="dd_account", type="string", length=100, options={"default" : ""})
     */
    private string $account = "";

    /**
     * @var string
     *
     * @ORM\Column(name="dd_amount", type="decimal", precision=11, scale=2)
     */
    private string $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="dd_account_type", type="string", length=20, options={"default" : ""})
     */
    private string $accountType = "";

    /**
     * @var string
     *
     * @ORM\Column(name="dd_transaction_type", type="string", length=20, options={"default" : ""})
     */
    private string $transactionType = "";

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
     * @return EmployeeSalary
     */
    public function getSalary(): EmployeeSalary
    {
        return $this->salary;
    }

    /**
     * @param EmployeeSalary $salary
     */
    public function setSalary(EmployeeSalary $salary): void
    {
        $this->salary = $salary;
    }

    /**
     * @return int
     */
    public function getRoutingNumber(): int
    {
        return $this->routingNumber;
    }

    /**
     * @param int $routingNumber
     */
    public function setRoutingNumber(int $routingNumber): void
    {
        $this->routingNumber = $routingNumber;
    }

    /**
     * @return string
     */
    public function getAccount(): string
    {
        return $this->account;
    }

    /**
     * @param string $account
     */
    public function setAccount(string $account): void
    {
        $this->account = $account;
    }

    /**
     * @return string
     */
    public function getAmount(): string
    {
        return $this->amount;
    }

    /**
     * @param string $amount
     */
    public function setAmount(string $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getAccountType(): string
    {
        return $this->accountType;
    }

    /**
     * @param string $accountType
     */
    public function setAccountType(string $accountType): void
    {
        $this->accountType = $accountType;
    }

    /**
     * @return string
     */
    public function getTransactionType(): string
    {
        return $this->transactionType;
    }

    /**
     * @param string $transactionType
     */
    public function setTransactionType(string $transactionType): void
    {
        $this->transactionType = $transactionType;
    }
}
