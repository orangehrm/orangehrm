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
use OrangeHRM\Entity\Decorator\ClaimAttachmentDecorator;
use OrangeHRM\Entity\Decorator\DecoratorTrait;

/**
 * @method ClaimAttachmentDecorator getDecorator()
 * @ORM\Table(name="ohrm_claim_attachment")
 * @ORM\Entity
 */
class ClaimAttachment
{
    use DecoratorTrait;

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(name="request_id", type="integer")
     */
    private int $requestId;

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(name="eattach_id", type="bigint")
     */
    private int $attachId;

    /**
     * @var int
     * @ORM\Column(name="eattach_size", type="integer")
     */
    private int $fileSize;

    /**
     * @var string
     * @ORM\Column(name="eattach_desc", type="string")
     */
    private string $description;

    /**
     * @var string
     * @ORM\Column(name="eattach_filename", type="string")
     */
    private string $fileName;

    /**
     * @var string
     * @ORM\Column(name="eattach_attachment", type="blob")
     */
    private $attachment;

    /**
     * @var string
     * @ORM\Column(name="eattach_type", type="string")
     */
    private string $fileType;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\User")
     * @ORM\JoinColumn(name="attached_by", referencedColumnName="id")
     */
    private User $user;

    /**
     * @var DateTime
     * @ORM\Column(name="attached_time", type="datetime")
     */
    private DateTime $attachedTime;

    /**
     * @return int
     */
    public function getRequestId(): int
    {
        return $this->requestId;
    }

    /**
     * @param int $requestId
     */
    public function setRequestId(int $requestId): void
    {
        $this->requestId = $requestId;
    }

    /**
     * @return int
     */
    public function getAttachId(): int
    {
        return $this->attachId;
    }

    /**
     * @param int $attachId
     */
    public function setAttachId(int $attachId): void
    {
        $this->attachId = $attachId;
    }

    /**
     * @return int
     */
    public function getFileSize(): int
    {
        return $this->fileSize;
    }

    /**
     * @param int $fileSize
     */
    public function setFileSize(int $fileSize): void
    {
        $this->fileSize = $fileSize;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     */
    public function setFileName(string $fileName): void
    {
        $this->fileName = $fileName;
    }

    /**
     * @return string
     */
    public function getAttachment(): string
    {
        return $this->attachment;
    }

    /**
     * @param string $attachment
     */
    public function setAttachment(string $attachment): void
    {
        $this->attachment = $attachment;
    }

    /**
     * @return string
     */
    public function getFileType(): string
    {
        return $this->fileType;
    }

    /**
     * @param string $fileType
     */
    public function setFileType(string $fileType): void
    {
        $this->fileType = $fileType;
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
     * @return DateTime
     */
    public function getAttachedTime(): DateTime
    {
        return $this->attachedTime;
    }

    /**
     * @param DateTime $attachedTime
     */
    public function setAttachedTime(DateTime $attachedTime): void
    {
        $this->attachedTime = $attachedTime;
    }
}
