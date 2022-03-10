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
use OrangeHRM\Entity\Decorator\DecoratorTrait;
use OrangeHRM\Entity\Decorator\EmployeeImmigrationRecordDecorator;

/**
 *
 * @method EmployeeImmigrationRecordDecorator getDecorator()
 *
 * @ORM\Table(name="hs_hr_emp_passport")
 * @ORM\Entity
 */
class EmployeeImmigrationRecord
{
    use DecoratorTrait;

    public const DOCUMENT_TYPE_PASSPORT = 1;
    public const DOCUMENT_TYPE_VISA = 2;

    public const DOCUMENT_TYPE_MAP = [
        self::DOCUMENT_TYPE_PASSPORT => 'Passport',
        self::DOCUMENT_TYPE_VISA => 'Visa',
    ];

    /**
     * @var Employee
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee", inversedBy="immigrationRecords", cascade={"persist"})
     * @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     */
    private Employee $employee;

    /**
     * @var string
     *
     * @ORM\Column(name="ep_seqno", type="decimal", precision=2, scale=0, options={"default" : 0})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private string $recordId = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="ep_passport_num", type="string", length=100, options={"default" : ""})
     */
    private string $number;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="ep_passportissueddate", type="datetime", nullable=true)
     */
    private ?DateTime $issuedDate;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="ep_passportexpiredate", type="datetime", nullable=true)
     */
    private ?DateTime $expiryDate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ep_comments", type="string", length=255, nullable=true)
     */
    private ?string $comment;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ep_passport_type_flg", type="smallint", nullable=true)
     */
    private ?int $type;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ep_i9_status", type="string", length=100, nullable=true, options={"default" : ""})
     */
    private ?string $status = "";

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="ep_i9_review_date", type="date", nullable=true)
     */
    private ?DateTime $reviewDate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="cou_code", type="string", length=6, nullable=true)
     */
    private ?string $countryCode;

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
     * @return string
     */
    public function getRecordId(): string
    {
        return $this->recordId;
    }

    /**
     * @param string $recordId
     */
    public function setRecordId(string $recordId): void
    {
        $this->recordId = $recordId;
    }

    /**
     * @return string
     */
    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * @param string $number
     */
    public function setNumber(string $number): void
    {
        $this->number = $number;
    }

    /**
     * @return DateTime|null
     */
    public function getIssuedDate(): ?DateTime
    {
        return $this->issuedDate;
    }

    /**
     * @param DateTime|null $issuedDate
     */
    public function setIssuedDate(?DateTime $issuedDate): void
    {
        $this->issuedDate = $issuedDate;
    }

    /**
     * @return DateTime|null
     */
    public function getExpiryDate(): ?DateTime
    {
        return $this->expiryDate;
    }

    /**
     * @param DateTime|null $expiryDate
     */
    public function setExpiryDate(?DateTime $expiryDate): void
    {
        $this->expiryDate = $expiryDate;
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
     * @return int|null
     */
    public function getType(): ?int
    {
        return $this->type;
    }

    /**
     * @param int|null $type
     */
    public function setType(?int $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string|null $status
     */
    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return DateTime|null
     */
    public function getReviewDate(): ?DateTime
    {
        return $this->reviewDate;
    }

    /**
     * @param DateTime|null $reviewDate
     */
    public function setReviewDate(?DateTime $reviewDate): void
    {
        $this->reviewDate = $reviewDate;
    }

    /**
     * @return string|null
     */
    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    /**
     * @param string|null $countryCode
     */
    public function setCountryCode(?string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }
}
