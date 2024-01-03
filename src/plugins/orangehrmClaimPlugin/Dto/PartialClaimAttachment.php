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

namespace OrangeHRM\Claim\Dto;

use DateTime;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\ClaimRequest;
use OrangeHRM\Entity\Decorator\DecoratorTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\User;
use OrangeHRM\Pim\Traits\Service\EmployeeServiceTrait;

class PartialClaimAttachment
{
    use DateTimeHelperTrait;
    use DecoratorTrait;
    use EntityManagerHelperTrait;
    use EmployeeServiceTrait;

    /**
     * @var int|null
     */
    private ?int $requestId;

    /**
     * @var int|null
     */
    private ?int $attachId;

    /**
     * @var int|null
     */
    private ?int $size;

    /**
     * @var string|null
     */
    private ?string $description;

    /**
     * @var string|null
     */
    private ?string $filename;

    /**
     * @var string|null
     */
    private ?string $fileType;

    /**
     * @var int|null
     */
    private ?int $addedBy;

    /**
     * @var Employee|null
     */
    private ?Employee $addedByEmployee = null;

    /**
     * @var string|null
     */
    private ?string $attachedDate;

    /**
     * @param int|null $requestId
     * @param int|null $attachId
     * @param int|null $size
     * @param string|null $description
     * @param string|null $filename
     * @param string|null $fileType
     * @param int|null $addedBy
     * @param DateTime|null $attachedDate
     */
    public function __construct(
        ?int      $requestId,
        ?int      $attachId,
        ?int      $size,
        ?string   $description,
        ?string   $filename,
        ?string   $fileType,
        ?int     $addedBy,
        ?DateTime $attachedDate
    ) {
        $this->requestId = $requestId;
        $this->attachId = $attachId;
        $this->size = $size;
        $this->description = $description;
        $this->filename = $filename;
        $this->fileType = $fileType;
        $this->addedBy = $addedBy;
        $this->setAttachedDate($attachedDate);
        $this->addedByEmployee = $this->getReference(
            Employee::class,
            $this->getReference(User::class, $this->addedBy)->getEmpNumber()
        );
    }

    /**
     * @return int|null
     */
    public function getRequestId(): ?int
    {
        return $this->requestId;
    }

    /**
     * @param int|null $requestId
     */
    public function setRequestId(?int $requestId): void
    {
        $this->requestId = $requestId;
    }

    /**
     * @return int|null
     */
    public function getAttachId(): ?int
    {
        return $this->attachId;
    }

    /**
     * @param int|null $attachId
     */
    public function setAttachId(?int $attachId): void
    {
        $this->attachId = $attachId;
    }

    /**
     * @return int|null
     */
    public function getSize(): ?int
    {
        return $this->size;
    }

    /**
     * @param int|null $size
     */
    public function setSize(?int $size): void
    {
        $this->size = $size;
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
     * @return string|null
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * @param string|null $filename
     */
    public function setFilename(?string $filename): void
    {
        $this->filename = $filename;
    }

    /**
     * @return string|null
     */
    public function getFileType(): ?string
    {
        return $this->fileType;
    }

    /**
     * @param string|null $fileType
     */
    public function setFileType(?string $fileType): void
    {
        $this->fileType = $fileType;
    }

    /**
     * @return DateTime|null
     */
    public function getAttachedDate(): ?string
    {
        return $this->attachedDate;
    }

    /**
     * @param DateTime|null $date
     * @return void
     */
    public function setAttachedDate(?DateTime $date): void
    {
        $this->attachedDate = $this->getDateTimeHelper()->formatDate($date);
    }

    /**
     * @return ClaimRequest|null
     */
    public function getClaimRequest(): ?ClaimRequest
    {
        return $this->getReference(ClaimRequest::class, $this->getRequestId());
    }

    /**
     * @return int|null
     */
    public function getAddedBy(): ?int
    {
        return $this->addedBy;
    }

    /**
     * @param int|null $addedBy
     */
    public function setAddedBy(?int $addedBy): void
    {
        $this->addedBy = $addedBy;
    }

    /**
     * @return Employee|null
     */
    public function getAddedByEmployee(): ?Employee
    {
        return $this->addedByEmployee;
    }

    /**
     * @return void
     */
    public function setAddedByEmployee(): void
    {
        $this->getReference(Employee::class, $this->getAddedBy()->getEmpNumber());
    }
}
