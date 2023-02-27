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
    private int $eattachId;

    /**
     * @var int
     * @ORM\Column(name="eattach_size", type="integer")
     */
    private int $eattachSize;

    /**
     * @var string
     * @ORM\Column(name="eattach_desc", type="string")
     */
    private string $eattachDesc;

    /**
     * @var string
     * @ORM\Column(name="eattach_filename", type="string")
     */
    private string $eattachFileName;

    /**
     * @var string
     * @ORM\Column(name="eattach_attachment", type="blob")
     */
    private $eattachAttachment;

    /**
     * @var string
     * @ORM\Column(name="eattach_type", type="string")
     */
    private string $eattachType;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\User")
     * @ORM\JoinColumn(name="attached_by", referencedColumnName="id")
     */
    private User $user;

    /**
     * @var string
     * @ORM\Column(name="attached_by_name", type="string")
     */
    private string $attachedByName;

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
    public function getEattachId(): int
    {
        return $this->eattachId;
    }

    /**
     * @param int $eattachId
     */
    public function setEattachId(int $eattachId): void
    {
        $this->eattachId = $eattachId;
    }

    /**
     * @return int
     */
    public function getEattachSize(): int
    {
        return $this->eattachSize;
    }

    /**
     * @param int $eattachSize
     */
    public function setEattachSize(int $eattachSize): void
    {
        $this->eattachSize = $eattachSize;
    }

    /**
     * @return string
     */
    public function getEattachDesc(): string
    {
        return $this->eattachDesc;
    }

    /**
     * @param string $eattachDesc
     */
    public function setEattachDesc(string $eattachDesc): void
    {
        $this->eattachDesc = $eattachDesc;
    }

    /**
     * @return string
     */
    public function getEattachFileName(): string
    {
        return $this->eattachFileName;
    }

    /**
     * @param string $eattachFileName
     */
    public function setEattachFileName(string $eattachFileName): void
    {
        $this->eattachFileName = $eattachFileName;
    }

    /**
     * @return string
     */
    public function getEattachAttachment(): string
    {
        return $this->eattachAttachment;
    }

    /**
     * @param string $eattachAttachment
     */
    public function setEattachAttachment(string $eattachAttachment): void
    {
        $this->eattachAttachment = $eattachAttachment;
    }

    /**
     * @return string
     */
    public function getEattachType(): string
    {
        return $this->eattachType;
    }

    /**
     * @param string $eattachType
     */
    public function setEattachType(string $eattachType): void
    {
        $this->eattachType = $eattachType;
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
    public function getAttachedByName(): string
    {
        return $this->attachedByName;
    }

    /**
     * @param string $attachedByName
     */
    public function setAttachedByName(string $attachedByName): void
    {
        $this->attachedByName = $attachedByName;
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
