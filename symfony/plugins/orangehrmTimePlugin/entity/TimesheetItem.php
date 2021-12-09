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
 * @ORM\Table(name="ohrm_timesheet_item")
 * @ORM\Entity
 */
class TimesheetItem
{
    /**
     * @var int
     *
     * @ORM\Column(name="timesheet_item_id", type="integer", length=4)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var Timesheet
     *
     * @ORM\ManyToOne(targetEntity="Timesheet", inversedBy="timesheetItem")
     * @ORM\JoinColumn(name="timesheet_id", referencedColumnName="timesheet_id")
     */
    private Timesheet $timesheet;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    private DateTime $date;

    /**
     * @var int
     *
     * @ORM\Column(name="duration", type="bigint", length=20)
     */
    private int $duration;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=255)
     */
    private string $comment;

    /**
     * @var int
     *
     * @ORM\Column(name="project_id", type="bigint", length=20)
     */
    private int $projectId;

    /**
     * @var int
     *
     * @ORM\Column(name="employee_id", type="bigint", length=20)
     */
    private int $employeeId;

    /**
     * @var ProjectActivity
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\ProjectActivity", inversedBy="timesheetItem")
     * @ORM\JoinColumn(name="activity_id", referencedColumnName="activity_id")
     */
    private ProjectActivity $projectActivity;

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
     * @return Timesheet
     */
    public function getTimesheet(): Timesheet
    {
        return $this->timesheet;
    }

    /**
     * @param Timesheet $timesheet
     */
    public function setTimesheet(Timesheet $timesheet): void
    {
        $this->timesheet = $timesheet;
    }

    /**
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     */
    public function setDate(DateTime $date): void
    {
        $this->date = $date;
    }

    /**
     * @return int
     */
    public function getDuration(): int
    {
        return $this->duration;
    }

    /**
     * @param int $duration
     */
    public function setDuration(int $duration): void
    {
        $this->duration = $duration;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }

    /**
     * @return int
     */
    public function getProjectId(): int
    {
        return $this->projectId;
    }

    /**
     * @param int $projectId
     */
    public function setProjectId(int $projectId): void
    {
        $this->projectId = $projectId;
    }

    /**
     * @return int
     */
    public function getEmployeeId(): int
    {
        return $this->employeeId;
    }

    /**
     * @param int $employeeId
     */
    public function setEmployeeId(int $employeeId): void
    {
        $this->employeeId = $employeeId;
    }

    /**
     * @return ProjectActivity
     */
    public function getProjectActivity(): ProjectActivity
    {
        return $this->projectActivity;
    }

    /**
     * @param ProjectActivity $projectActivity
     */
    public function setProjectActivity(ProjectActivity $projectActivity): void
    {
        $this->projectActivity = $projectActivity;
    }
}
