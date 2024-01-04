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
use OrangeHRM\Entity\Decorator\AttendanceRecordDecorator;
use OrangeHRM\Entity\Decorator\DecoratorTrait;

/**
 * @method AttendanceRecordDecorator getDecorator()
 *
 * @ORM\Table(name="ohrm_attendance_record")
 * @ORM\Entity
 *
 */
class AttendanceRecord
{
    use DecoratorTrait;

    public const STATE_PUNCHED_IN = "PUNCHED IN";
    public const STATE_PUNCHED_OUT = "PUNCHED OUT";
    public const STATE_CREATED = "CREATED";
    public const STATE_INITIAL = "INITIAL";
    public const STATE_NA = "NA";

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", length=20)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     */
    private int $id;

    /**
     * @var Employee
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee", inversedBy="attendanceRecords", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="employee_id", referencedColumnName="emp_number", nullable=false)
     */
    private Employee $employee;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="punch_in_utc_time", type="datetime")
     */
    private DateTime $punchInUtcTime;

    /**
     * @var string|null
     *
     * @ORM\Column(name="punch_in_note", type="string", length=255, nullable=true)
     */
    private ?string $punchInNote = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="punch_in_time_offset", type="string", length=255, nullable=true)
     */
    private ?string $punchInTimeOffset;

    /**
     * @var string|null
     *
     * @ORM\Column(name="punch_in_timezone_name", type="string", length=100, nullable=true)
     */
    private ?string $punchInTimezoneName;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="punch_in_user_time", type="datetime")
     */
    private DateTime $punchInUserTime;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="punch_out_utc_time", type="datetime", nullable=true)
     */
    private ?DateTime $punchOutUtcTime = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="punch_out_note", type="string", length=255, nullable=true)
     */
    private ?string $punchOutNote = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="punch_out_time_offset", type="string", length=255, nullable=true)
     */
    private ?string $punchOutTimeOffset = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="punch_out_timezone_name", type="string", length=100, nullable=true)
     */
    private ?string $punchOutTimezoneName = null;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="punch_out_user_time", type="datetime", nullable=true)
     */
    private ?DateTime $punchOutUserTime = null;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=255, nullable=false)
     */
    private string $state;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param  int  $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return Employee
     */
    public function getEmployee(): Employee
    {
        return $this->employee;
    }

    /**
     * @param  Employee  $employee
     */
    public function setEmployee(Employee $employee): void
    {
        $this->employee = $employee;
    }

    /**
     * @return DateTime|null
     */
    public function getPunchInUtcTime(): ?DateTime
    {
        return $this->punchInUtcTime;
    }

    /**
     * @param  DateTime|null  $punchInUtcTime
     */
    public function setPunchInUtcTime(?DateTime $punchInUtcTime): void
    {
        $this->punchInUtcTime = $punchInUtcTime;
    }

    /**
     * @return string|null
     */
    public function getPunchInNote(): ?string
    {
        return $this->punchInNote;
    }

    /**
     * @param  string|null  $punchInNote
     */
    public function setPunchInNote(?string $punchInNote): void
    {
        $this->punchInNote = $punchInNote;
    }

    /**
     * @return string|null
     */
    public function getPunchInTimeOffset(): ?string
    {
        return $this->punchInTimeOffset;
    }

    /**
     * @param  string|null  $punchInTimeOffset
     */
    public function setPunchInTimeOffset(?string $punchInTimeOffset): void
    {
        $this->punchInTimeOffset = $punchInTimeOffset;
    }

    /**
     * @return DateTime|null
     */
    public function getPunchInUserTime(): ?DateTime
    {
        return $this->punchInUserTime;
    }

    /**
     * @param  DateTime|null  $punchInUserTime
     */
    public function setPunchInUserTime(?DateTime $punchInUserTime): void
    {
        $this->punchInUserTime = $punchInUserTime;
    }

    /**
     * @return DateTime|null
     */
    public function getPunchOutUtcTime(): ?DateTime
    {
        return $this->punchOutUtcTime;
    }

    /**
     * @param  DateTime|null  $punchOutUtcTime
     */
    public function setPunchOutUtcTime(?DateTime $punchOutUtcTime): void
    {
        $this->punchOutUtcTime = $punchOutUtcTime;
    }

    /**
     * @return string|null
     */
    public function getPunchOutNote(): ?string
    {
        return $this->punchOutNote;
    }

    /**
     * @param  string|null  $punchOutNote
     */
    public function setPunchOutNote(?string $punchOutNote): void
    {
        $this->punchOutNote = $punchOutNote;
    }

    /**
     * @return string|null
     */
    public function getPunchOutTimeOffset(): ?string
    {
        return $this->punchOutTimeOffset;
    }

    /**
     * @param  string|null  $punchOutTimeOffset
     */
    public function setPunchOutTimeOffset(?string $punchOutTimeOffset): void
    {
        $this->punchOutTimeOffset = $punchOutTimeOffset;
    }

    /**
     * @return DateTime|null
     */
    public function getPunchOutUserTime(): ?DateTime
    {
        return $this->punchOutUserTime;
    }

    /**
     * @param  DateTime|null  $punchOutUserTime
     */
    public function setPunchOutUserTime(?DateTime $punchOutUserTime): void
    {
        $this->punchOutUserTime = $punchOutUserTime;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @param  string  $state
     */
    public function setState(string $state): void
    {
        $this->state = $state;
    }

    /**
     * @return string|null
     */
    public function getPunchInTimezoneName(): ?string
    {
        return $this->punchInTimezoneName;
    }

    /**
     * @param string|null $punchInTimezoneName
     */
    public function setPunchInTimezoneName(?string $punchInTimezoneName): void
    {
        $this->punchInTimezoneName = $punchInTimezoneName;
    }

    /**
     * @return string|null
     */
    public function getPunchOutTimezoneName(): ?string
    {
        return $this->punchOutTimezoneName;
    }

    /**
     * @param string|null $punchOutTimezoneName
     */
    public function setPunchOutTimezoneName(?string $punchOutTimezoneName): void
    {
        $this->punchOutTimezoneName = $punchOutTimezoneName;
    }
}
