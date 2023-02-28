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

namespace OrangeHRM\Claim\Dto;

use DateTime;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;

class PartialClaimAttachment
{
    use DateTimeHelperTrait;

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
    private ?int $fileSize;

    /**
     * @var string|null
     */
    private ?string $description;

    /**
     * @var string|null
     */
    private ?string $fileName;

    /**
     * @var string|null
     */
    private ?string $fileType;

    /**
     * @var string|null
     */
    private ?string $attachedTime;

    /**
     * @param int|null $requestId
     * @param int|null $eattachId
     * @param int|null $eattachSize
     * @param string|null $eattachDesc
     * @param string|null $eattachFileName
     * @param string|null $eattachFileType
     * @param DateTime|null $attachedTime
     */
    public function __construct(
        ?int $requestId,
        ?int $eattachId,
        ?int $eattachSize,
        ?string $eattachDesc,
        ?string $eattachFileName,
        ?string $eattachFileType,
        ?DateTime $attachedTime
    ) {
        $this->requestId = $requestId;
        $this->attachId = $eattachId;
        $this->fileSize = $eattachSize;
        $this->description = $eattachDesc;
        $this->fileName = $eattachFileName;
        $this->fileType = $eattachFileType;
        $this->setAttachedTime($attachedTime);
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
    public function getFileSize(): ?int
    {
        return $this->fileSize;
    }

    /**
     * @param int|null $fileSize
     */
    public function setFileSize(?int $fileSize): void
    {
        $this->fileSize = $fileSize;
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
    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    /**
     * @param string|null $fileName
     */
    public function setFileName(?string $fileName): void
    {
        $this->fileName = $fileName;
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
     * @return int|null
     */
    public function getEattachAttachedBy(): ?int
    {
        return $this->eattachAttachedBy;
    }

    /**
     * @param int|null $eattachAttachedBy
     */
    public function setEattachAttachedBy(?int $eattachAttachedBy): void
    {
        $this->eattachAttachedBy = $eattachAttachedBy;
    }

    /**
     * @return DateTime|null
     */
    public function getAttachedTime(): ?string
    {
        return $this->attachedTime;
    }

    /**
     * @param DateTime|null $date
     * @return void
     */
    public function setAttachedTime(?DateTime $date): void
    {
        $this->attachedTime = $this->getDateTimeHelper()->formatDate($date);
    }
}
