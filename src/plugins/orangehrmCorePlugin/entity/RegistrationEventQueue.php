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

/**
 * @ORM\Table(name="ohrm_registration_event_queue")
 * @ORM\Entity
 */
class RegistrationEventQueue
{
    public const INSTALLATION_START = 0;
    public const ACTIVE_EMPLOYEE_COUNT = 1;
    public const INACTIVE_EMPLOYEE_COUNT = 2;
    public const INSTALLATION_SUCCESS = 3;
    public const UPGRADE_START = 4;

    public const PUBLISH_EVENT_BATCH_SIZE = 5;
    public const EMPLOYEE_COUNT_CHANGE_TRACKER_SIZE = 10;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var int
     *
     * @ORM\Column(name="event_type", type="integer")
     */
    private int $eventType;

    /**
     * @var bool
     *
     * @ORM\Column(name="published", type="boolean", nullable=false)
     */
    private bool $published;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="event_time", type="datetime", nullable=true)
     */
    private ?DateTime $event_time = null;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="publish_time", type="datetime", nullable=true)
     */
    private ?DateTime $publishTime = null;

    /**
     * @var array|null
     *
     * @ORM\Column(name="data", type="json", nullable=true)
     */
    private ?array $data = [];

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getEventType(): int
    {
        return $this->eventType;
    }

    /**
     * @param int $eventType
     */
    public function setEventType(int $eventType): void
    {
        $this->eventType = $eventType;
    }

    /**
     * @return bool
     */
    public function isPublished(): bool
    {
        return $this->published;
    }

    /**
     * @param bool $published
     */
    public function setPublished(bool $published): void
    {
        $this->published = $published;
    }

    /**
     * @return DateTime|null
     */
    public function getEventTime(): ?DateTime
    {
        return $this->event_time;
    }

    /**
     * @param DateTime|null $event_time
     */
    public function setEventTime(?DateTime $event_time): void
    {
        $this->event_time = $event_time;
    }

    /**
     * @return DateTime|null
     */
    public function getPublishTime(): ?DateTime
    {
        return $this->publishTime;
    }

    /**
     * @param DateTime|null $publishTime
     */
    public function setPublishTime(?DateTime $publishTime): void
    {
        $this->publishTime = $publishTime;
    }

    /**
     * @return array|null
     */
    public function getData(): ?array
    {
        return $this->data;
    }

    /**
     * @param array|null $data
     */
    public function setData(?array $data): void
    {
        $this->data = $data;
    }
}
