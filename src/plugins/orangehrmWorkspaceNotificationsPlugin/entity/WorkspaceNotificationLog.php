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

/**
 * @ORM\Table(name="ohrm_workspace_notification_log")
 * @ORM\Entity
 */
class WorkspaceNotificationLog
{
    public const STATUS_SUCCESS = 'SUCCESS';
    public const STATUS_FAILED = 'FAILED';
    public const STATUS_SKIPPED = 'SKIPPED';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var WorkspaceNotificationRegistration|null
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\WorkspaceNotificationRegistration")
     * @ORM\JoinColumn(name="registration_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    private ?WorkspaceNotificationRegistration $registration = null;

    /**
     * @var string
     *
     * @ORM\Column(name="event_type", type="string", length=32, nullable=false)
     */
    private string $eventType;

    /**
     * Calendar day in the admin's configured timezone — used as the idempotency key
     * together with registration_id.
     *
     * @var DateTime
     *
     * @ORM\Column(name="event_date", type="date", nullable=false)
     */
    private DateTime $eventDate;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=20, nullable=false)
     */
    private string $status;

    /**
     * @var int
     *
     * @ORM\Column(name="recipient_count", type="integer", options={"default" : 0})
     */
    private int $recipientCount = 0;

    /**
     * @var string|null
     *
     * @ORM\Column(name="error_message", type="text", nullable=true)
     */
    private ?string $errorMessage = null;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private ?DateTime $createdAt = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getRegistration(): ?WorkspaceNotificationRegistration
    {
        return $this->registration;
    }

    public function setRegistration(?WorkspaceNotificationRegistration $registration): void
    {
        $this->registration = $registration;
    }

    public function getEventType(): string
    {
        return $this->eventType;
    }

    public function setEventType(string $eventType): void
    {
        $this->eventType = $eventType;
    }

    public function getEventDate(): DateTime
    {
        return $this->eventDate;
    }

    public function setEventDate(DateTime $eventDate): void
    {
        $this->eventDate = $eventDate;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getRecipientCount(): int
    {
        return $this->recipientCount;
    }

    public function setRecipientCount(int $recipientCount): void
    {
        $this->recipientCount = $recipientCount;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function setErrorMessage(?string $errorMessage): void
    {
        $this->errorMessage = $errorMessage;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}
