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

namespace OrangeHRM\Pim\Dto;

use DateTime;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;

class PartialEmployeeAttachment
{
    use DateTimeHelperTrait;

    /**
     * @var int|null
     */
    private ?int $attachId;

    /**
     * @var string|null
     */
    private ?string $description;

    /**
     * @var string|null
     */
    private ?string $filename;

    /**
     * @var int|null
     */
    private ?int $size;

    /**
     * @var string|null
     */
    private ?string $fileType;

    /**
     * @var int|null
     */
    private ?int $attachedBy;

    /**
     * @var string|null
     */
    private ?string $attachedByName;

    /**
     * @var string|null
     */
    private ?string $attachedTime;

    /**
     * @var string|null
     */
    private ?string $attachedDate;

    /**
     * @param int|null $attachId
     * @param string|null $description
     * @param string|null $filename
     * @param int|null $size
     * @param string|null $fileType
     * @param int|null $attachedBy
     * @param string|null $attachedByName
     * @param DateTime|null $dateTime
     */
    public function __construct(
        ?int $attachId,
        ?string $description,
        ?string $filename,
        ?int $size,
        ?string $fileType,
        ?int $attachedBy,
        ?string $attachedByName,
        ?DateTime $dateTime
    ) {
        $this->attachId = $attachId;
        $this->description = $description;
        $this->filename = $filename;
        $this->size = $size;
        $this->fileType = $fileType;
        $this->attachedBy = $attachedBy;
        $this->attachedByName = $attachedByName;
        $this->setAttachedDate($dateTime);
        $this->setAttachedTime($dateTime);
    }


    /**
     * @return int|null
     */
    public function getAttachId(): ?int
    {
        return $this->attachId;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return string|null
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * @return int|null
     */
    public function getSize(): ?int
    {
        return $this->size;
    }

    /**
     * @return string|null
     */
    public function getFileType(): ?string
    {
        return $this->fileType;
    }

    /**
     * @return int|null
     */
    public function getAttachedBy(): ?int
    {
        return $this->attachedBy;
    }

    /**
     * @return string|null
     */
    public function getAttachedByName(): ?string
    {
        return $this->attachedByName;
    }

    /**
     * @return string|null
     */
    public function getAttachedTime(): ?string
    {
        return $this->attachedTime;
    }

    /**
     * @param DateTime|null $dateTime
     * @return void
     */
    public function setAttachedTime(?DateTime $dateTime): void
    {
        $this->attachedTime = $this->getDateTimeHelper()->formatDateTimeToTimeString($dateTime);
    }

    /**
     * @return string|null
     */
    public function getAttachedDate(): ?string
    {
        return $this->attachedDate;
    }

    /**
     * @param DateTime|null $dateTime
     * @return void
     */
    public function setAttachedDate(?DateTime $dateTime): void
    {
        $this->attachedDate = $this->getDateTimeHelper()->formatDate($dateTime);
    }
}
