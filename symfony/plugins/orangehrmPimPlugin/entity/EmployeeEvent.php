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
 * @ORM\Table(name="ohrm_employee_event")
 * @ORM\Entity
 */
class EmployeeEvent
{
    /**
     * Types
     */
    public const EVENT_TYPE_EMPLOYEE = 'employee';
    public const EVENT_TYPE_CONTACT_DETAIL = 'contact';
    public const EVENT_TYPE_JOB_DETAIL = 'jobDetail';
    public const EVENT_TYPE_SUPERVISOR = 'supervisor';
    public const EVENT_TYPE_SUBORDINATE = 'subordinate';
    public const EVENT_TYPE_DEPENDENT = 'dependent';

    /**
     * Events
     */
    public const EVENT_UPDATE = 'UPDATE';
    public const EVENT_SAVE = 'SAVE';
    public const EVENT_DELETE = 'DELETE';

    /**
     * @var int
     *
     * @ORM\Column(name="event_id", type="integer", length=7)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $eventId;

    /**
     * @var int
     *
     * @ORM\Column(name="employee_id", type="integer", length=7)
     */
    private int $empNumber;

    /**
     * @var string|null
     *
     * @ORM\Column(name="type", type="string", length=45, nullable=true)
     */
    private ?string $type;

    /**
     * @var string|null
     *
     * @ORM\Column(name="event", type="string", length=45, nullable=true)
     */
    private ?string $event;

    /**
     * @var string|null
     *
     * @ORM\Column(name="note", type="string", length=150, nullable=true)
     */
    private ?string $note;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="created_date", type="datetime", nullable=true)
     */
    private ?DateTime $createdDate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="created_by", type="string", length=45, nullable=true)
     */
    private ?string $createdBy;

    /**
     * @return int
     */
    public function getEventId(): int
    {
        return $this->eventId;
    }

    /**
     * @param int $eventId
     */
    public function setEventId(int $eventId): void
    {
        $this->eventId = $eventId;
    }

    /**
     * @return int
     */
    public function getEmpNumber(): int
    {
        return $this->empNumber;
    }

    /**
     * @param int $empNumber
     */
    public function setEmpNumber(int $empNumber): void
    {
        $this->empNumber = $empNumber;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string|null $type
     */
    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string|null
     */
    public function getEvent(): ?string
    {
        return $this->event;
    }

    /**
     * @param string|null $event
     */
    public function setEvent(?string $event): void
    {
        $this->event = $event;
    }

    /**
     * @return string|null
     */
    public function getNote(): ?string
    {
        return $this->note;
    }

    /**
     * @param string|null $note
     */
    public function setNote(?string $note): void
    {
        $this->note = $note;
    }

    /**
     * @return DateTime|null
     */
    public function getCreatedDate(): ?DateTime
    {
        return $this->createdDate;
    }

    /**
     * @param DateTime|null $createdDate
     */
    public function setCreatedDate(?DateTime $createdDate): void
    {
        $this->createdDate = $createdDate;
    }

    /**
     * @return string|null
     */
    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    /**
     * @param string|null $createdBy
     */
    public function setCreatedBy(?string $createdBy): void
    {
        $this->createdBy = $createdBy;
    }
}
