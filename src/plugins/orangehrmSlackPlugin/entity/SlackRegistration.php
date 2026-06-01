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
 * @ORM\Table(name="ohrm_slack_registration")
 * @ORM\Entity
 */
class SlackRegistration
{
    public const EVENT_TYPE_BIRTHDAY = 'BIRTHDAY';
    public const EVENT_TYPE_LEAVE_TODAY = 'LEAVE_TODAY';

    public const EVENT_TYPES = [
        self::EVENT_TYPE_BIRTHDAY,
        self::EVENT_TYPE_LEAVE_TODAY,
    ];

    public const DELIVERY_STATUS_SUCCESS = 'SUCCESS';
    public const DELIVERY_STATUS_FAILED = 'FAILED';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var string
     *
     * @ORM\Column(name="event_type", type="string", length=32, nullable=false)
     */
    private string $eventType;

    /**
     * Encrypted with the OHRM Cryptographer at persistence time.
     *
     * @var string
     *
     * @ORM\Column(name="webhook_url", type="text", nullable=false)
     */
    private string $webhookUrl;

    /**
     * @var string|null
     *
     * @ORM\Column(name="channel_label", type="string", length=100, nullable=true)
     */
    private ?string $channelLabel = null;

    /**
     * @var Subunit|null
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Subunit")
     * @ORM\JoinColumn(name="subunit_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private ?Subunit $subunit = null;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_active", type="boolean", options={"default": true})
     */
    private bool $active = true;

    /**
     * @var string|null
     *
     * @ORM\Column(name="last_delivery_status", type="string", length=20, nullable=true)
     */
    private ?string $lastDeliveryStatus = null;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="last_delivery_at", type="datetime", nullable=true)
     */
    private ?DateTime $lastDeliveryAt = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="last_delivery_error", type="text", nullable=true)
     */
    private ?string $lastDeliveryError = null;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private ?DateTime $createdAt = null;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private ?DateTime $updatedAt = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getEventType(): string
    {
        return $this->eventType;
    }

    public function setEventType(string $eventType): void
    {
        $this->eventType = $eventType;
    }

    public function getWebhookUrl(): string
    {
        return $this->webhookUrl;
    }

    public function setWebhookUrl(string $webhookUrl): void
    {
        $this->webhookUrl = $webhookUrl;
    }

    public function getChannelLabel(): ?string
    {
        return $this->channelLabel;
    }

    public function setChannelLabel(?string $channelLabel): void
    {
        $this->channelLabel = $channelLabel;
    }

    public function getSubunit(): ?Subunit
    {
        return $this->subunit;
    }

    public function setSubunit(?Subunit $subunit): void
    {
        $this->subunit = $subunit;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getLastDeliveryStatus(): ?string
    {
        return $this->lastDeliveryStatus;
    }

    public function setLastDeliveryStatus(?string $lastDeliveryStatus): void
    {
        $this->lastDeliveryStatus = $lastDeliveryStatus;
    }

    public function getLastDeliveryAt(): ?DateTime
    {
        return $this->lastDeliveryAt;
    }

    public function setLastDeliveryAt(?DateTime $lastDeliveryAt): void
    {
        $this->lastDeliveryAt = $lastDeliveryAt;
    }

    public function getLastDeliveryError(): ?string
    {
        return $this->lastDeliveryError;
    }

    public function setLastDeliveryError(?string $lastDeliveryError): void
    {
        $this->lastDeliveryError = $lastDeliveryError;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
