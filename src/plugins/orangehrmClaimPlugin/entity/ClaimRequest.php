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
use OrangeHRM\Entity\Decorator\ClaimRequestDecorator;
use OrangeHRM\Entity\Decorator\DecoratorTrait;

/**
 * @method ClaimRequestDecorator getDecorator()
 *
 * @ORM\Table(name="ohrm_claim_request")
 * @ORM\Entity
 */
class ClaimRequest
{
    use DecoratorTrait;

    public const REQUEST_STATUS_INITIATED = 'INITIATED';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var Employee
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee")
     * @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     */
    private Employee $employee;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\User")
     * @ORM\JoinColumn(name="added_by", referencedColumnName="id")
     */
    private User $user;

    /**
     * @var string
     *
     * @ORM\Column(name="reference_id", type="string", length=255)
     */
    private string $referenceId;

    /**
     * @var ClaimEvent
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\ClaimEvent")
     * @ORM\JoinColumn(name="event_type_id", referencedColumnName="id")
     */
    private ClaimEvent $claimEvent;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private ?string $description = null;

    /**
     * @var CurrencyType
     *
     * @ORM\OneToOne(targetEntity="OrangeHRM\Entity\CurrencyType", cascade={"persist"})
     * @ORM\JoinColumn(name="currency_id", referencedColumnName="currency_id")
     */
    private CurrencyType $currencyType;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_deleted", type="boolean", nullable=false, options={"default" : 0})
     */
    private bool $isDeleted = false;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private string $status;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="created_date", type="datetime")
     */
    private DateTime $createdDate;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="submitted_date", type="datetime")
     */
    private ?DateTime $submittedDate = null;

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
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getReferenceId(): string
    {
        return $this->referenceId;
    }

    /**
     * @param string $referenceId
     */
    public function setReferenceId(string $referenceId): void
    {
        $this->referenceId = $referenceId;
    }

    /**
     * @return ClaimEvent
     */
    public function getClaimEvent(): ClaimEvent
    {
        return $this->claimEvent;
    }

    /**
     * @param ClaimEvent $claimEvent
     */
    public function setClaimEvent(ClaimEvent $claimEvent): void
    {
        $this->claimEvent = $claimEvent;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
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

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return DateTime
     */
    public function getCreatedDate(): DateTime
    {
        return $this->createdDate;
    }

    /**
     * @param DateTime $createdDate
     */
    public function setCreatedDate(DateTime $createdDate): void
    {
        $this->createdDate = $createdDate;
    }

    /**
     * @return DateTime|null
     */
    public function getSubmittedDate(): ?DateTime
    {
        return $this->submittedDate;
    }

    /**
     * @param ?DateTime $submittedDate
     */
    public function setSubmittedDate(?DateTime $submittedDate): void
    {
        $this->submittedDate = $submittedDate;
    }
}
