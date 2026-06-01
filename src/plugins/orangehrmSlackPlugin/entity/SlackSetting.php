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
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ohrm_slack_setting")
 * @ORM\Entity
 */
class SlackSetting
{
    public const DEFAULT_TIMEZONE = 'UTC';
    public const DEFAULT_SEND_TIME = '09:00';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_enabled", type="boolean", options={"default": false})
     */
    private bool $enabled = false;

    /**
     * @var string
     *
     * @ORM\Column(name="timezone", type="string", length=64, nullable=false, options={"default": "UTC"})
     */
    private string $timezone = self::DEFAULT_TIMEZONE;

    /**
     * Stored as `HH:mm` in the admin's local timezone.
     *
     * @var string
     *
     * @ORM\Column(name="daily_send_time", type="string", length=5, nullable=false, options={"default": "09:00"})
     */
    private string $dailySendTime = self::DEFAULT_SEND_TIME;

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

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
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

    /**
     * Translate the admin's local HH:mm in the configured timezone to a [hour, minute] tuple in UTC,
     * for use in a UTC-evaluated cron expression. DST shifts on the configured timezone are picked up
     * on every scheduler tick because this is recomputed from current time.
     *
     * @return array{0:int,1:int}
     */
    public function getSendTimeInUtc(): array
    {
        $parts = explode(':', $this->dailySendTime, 2);
        $hour = isset($parts[0]) ? (int)$parts[0] : 9;
        $minute = isset($parts[1]) ? (int)$parts[1] : 0;

        try {
            $local = new DateTime('today', new DateTimeZone($this->timezone));
        } catch (\Throwable $e) {
            $local = new DateTime('today', new DateTimeZone(self::DEFAULT_TIMEZONE));
        }
        $local->setTime($hour, $minute, 0);
        $local->setTimezone(new DateTimeZone(self::DEFAULT_TIMEZONE));

        return [(int)$local->format('G'), (int)$local->format('i')];
    }
}
