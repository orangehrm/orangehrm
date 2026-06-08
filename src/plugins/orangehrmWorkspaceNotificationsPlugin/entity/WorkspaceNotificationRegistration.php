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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ohrm_workspace_notification_registration")
 * @ORM\Entity
 */
class WorkspaceNotificationRegistration
{
    public const EVENT_TYPE_BIRTHDAY = 'BIRTHDAY';
    public const EVENT_TYPE_LEAVE_TODAY = 'LEAVE_TODAY';

    public const EVENT_TYPES = [
        self::EVENT_TYPE_BIRTHDAY,
        self::EVENT_TYPE_LEAVE_TODAY,
    ];

    public const PROVIDER_SLACK = 'slack';
    public const PROVIDER_GOOGLE_CHAT = 'google_chat';
    public const PROVIDER_TEAMS = 'teams';

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
     * @ORM\Column(name="provider", type="string", length=20, nullable=false, options={"default" : "slack"})
     */
    private string $provider = self::PROVIDER_SLACK;

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
     * @var Collection<int, Subunit>
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\Subunit")
     * @ORM\JoinTable(
     *     name="ohrm_workspace_notification_registration_subunit",
     *     joinColumns={@ORM\JoinColumn(name="registration_id", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="subunit_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    private Collection $subunits;

    /**
     * @var string
     *
     * @ORM\Column(name="timezone", type="string", length=64, nullable=false, options={"default" : "UTC"})
     */
    private string $timezone = 'UTC';

    /**
     * @var string
     *
     * @ORM\Column(name="daily_send_time", type="string", length=5, nullable=false, options={"default" : "09:00"})
     */
    private string $dailySendTime = '09:00';

    /**
     * @var bool
     *
     * @ORM\Column(name="is_active", type="boolean", options={"default" : true})
     */
    private bool $active = true;

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

    public function __construct()
    {
        $this->subunits = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function setProvider(string $provider): void
    {
        $this->provider = $provider;
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

    /**
     * @return Collection<int, Subunit>
     */
    public function getSubunits(): Collection
    {
        return $this->subunits;
    }

    public function addSubunit(Subunit $subunit): void
    {
        if (!$this->subunits->contains($subunit)) {
            $this->subunits->add($subunit);
        }
    }

    public function clearSubunits(): void
    {
        $this->subunits->clear();
    }

    public function getTimezone(): string
    {
        return $this->timezone;
    }

    public function setTimezone(string $timezone): void
    {
        $this->timezone = $timezone;
    }

    public function getDailySendTime(): string
    {
        return $this->dailySendTime;
    }

    public function setDailySendTime(string $dailySendTime): void
    {
        $this->dailySendTime = $dailySendTime;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
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
