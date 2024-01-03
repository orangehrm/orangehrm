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
use OrangeHRM\Entity\Decorator\ClaimExpenseDecorator;
use OrangeHRM\Entity\Decorator\DecoratorTrait;

/**
 * @method ClaimExpenseDecorator getDecorator()
 * @ORM\Table(name="ohrm_expense")
 * @ORM\Entity
 */
class ClaimExpense
{
    use DecoratorTrait;

    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var ExpenseType
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\ExpenseType")
     * @ORM\JoinColumn(name="expense_type_id", referencedColumnName="id")
     */
    private ExpenseType $expenseType;

    /**
     * @var DateTime
     * @ORM\Column(name="date", type="datetime")
     */
    private DateTime $date;

    /**
     * @var float
     * @ORM\Column(name="amount", type="float", nullable=false)
     */
    private float $amount;

    /**
     * @var string|null
     * @ORM\Column(name="note", type="string", nullable=true, length=1000)
     */
    private ?string $note;

    /**
     * @var ClaimRequest
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\ClaimRequest", inversedBy="claimExpenses")
     * @ORM\JoinColumn(name="request_id", referencedColumnName="id")
     */
    private ClaimRequest $claimRequest;

    /**
     * @var bool
     * @ORM\Column(name="is_deleted", type="boolean", nullable=false)
     */
    private bool $isDeleted = false;

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
     * @return ExpenseType
     */
    public function getExpenseType(): ExpenseType
    {
        return $this->expenseType;
    }

    /**
     * @param ExpenseType $expenseType
     */
    public function setExpenseType(ExpenseType $expenseType): void
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
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return ?string
     */
    public function getNote(): ?string
    {
        return $this->note;
    }

    /**
     * @param ?string $note
     */
    public function setNote(?string $note): void
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
