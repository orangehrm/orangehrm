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
    private ?int $eattachId;

    /**
     * @var int|null
     */
    private ?int $eattachSize;

    /**
     * @var string|null
     */
    private ?string $eattachDesc;

    /**
     * @var string|null
     */
    private ?string $eattachFileName;

    /**
     * @var string|null
     */
    private ?string $eattachFileType;

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
        $this->eattachId = $eattachId;
        $this->eattachSize = $eattachSize;
        $this->eattachDesc = $eattachDesc;
        $this->eattachFileName = $eattachFileName;
        $this->eattachFileType = $eattachFileType;
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
    public function getEattachId(): ?int
    {
        return $this->eattachId;
    }

    /**
     * @param int|null $eattachId
     */
    public function setEattachId(?int $eattachId): void
    {
        $this->eattachId = $eattachId;
    }

    /**
     * @return int|null
     */
    public function getEattachSize(): ?int
    {
        return $this->eattachSize;
    }

    /**
     * @param int|null $eattachSize
     */
    public function setEattachSize(?int $eattachSize): void
    {
        $this->eattachSize = $eattachSize;
    }

    /**
     * @return string|null
     */
    public function getEattachDesc(): ?string
    {
        return $this->eattachDesc;
    }

    /**
     * @param string|null $eattachDesc
     */
    public function setEattachDesc(?string $eattachDesc): void
    {
        $this->eattachDesc = $eattachDesc;
    }

    /**
     * @return string|null
     */
    public function getEattachFileName(): ?string
    {
        return $this->eattachFileName;
    }

    /**
     * @param string|null $eattachFileName
     */
    public function setEattachFileName(?string $eattachFileName): void
    {
        $this->eattachFileName = $eattachFileName;
    }

    /**
     * @return string|null
     */
    public function getEattachFileType(): ?string
    {
        return $this->eattachFileType;
    }

    /**
     * @param string|null $eattachFileType
     */
    public function setEattachFileType(?string $eattachFileType): void
    {
        $this->eattachFileType = $eattachFileType;
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
