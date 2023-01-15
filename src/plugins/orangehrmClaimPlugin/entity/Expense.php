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

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ohrm_expense")
 * @ORM\Entity
 */
class Expense
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="ExpenseType", inversedBy="expenses")
     * @ORM\JoinColumn(nullable=false)
     */
    private $expenseType;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="date" type="date")
     */
    private DateTime $date;

    /**
     * @ORM\Column(type="decimal", precision=12, scale=2)
     */
    private $amount;

    /**
     * @ORM\Column(type="text")
     */
    private $note;

    /**
     * @ORM\ManyToOne(targetEntity="ClaimRequest", inversedBy="expenses")
     * @ORM\JoinColumn(name="request_id", referencedColumnName="id")
     */
    private ClaimRequest $claimRequest;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_deleted", type="boolean", nullable=false, options={"default" : 0})
     */
    private bool $isDeleted;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getExpenseType()
    {
        return $this->expenseType;
    }

    /**
     * @param mixed $expenseType
     */
    public function setExpenseType($expenseType): void
    {
        $this->expenseType = $expenseType;
    }

    /**
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     */
    public function setDate(DateTime $date): void
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return mixed
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param mixed $note
     */
    public function setNote($note): void
    {
        $this->note = $note;
    }

    /**
     * @return ClaimRequest
     */
    public function getClaimRequest(): ClaimRequest
    {
        return $this->claimRequest;
    }

    /**
     * @param ClaimRequest $claimRequest
     */
    public function setClaimRequest(ClaimRequest $claimRequest): void
    {
        $this->claimRequest = $claimRequest;
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    /**
     * @param bool $isDeleted
     */
    public function setIsDeleted(bool $isDeleted): void
    {
        $this->isDeleted = $isDeleted;
    }

}