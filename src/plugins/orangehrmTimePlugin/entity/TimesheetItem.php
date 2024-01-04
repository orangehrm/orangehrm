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
use OrangeHRM\Entity\Decorator\DecoratorTrait;
use OrangeHRM\Entity\Decorator\TimesheetItemDecorator;

/**
 * @method TimesheetItemDecorator getDecorator()
 *
 * @ORM\Table(name="ohrm_timesheet_item")
 * @ORM\Entity
 */
class TimesheetItem
{
    use DecoratorTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="timesheet_item_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    private DateTime $date;

    /**
     * @var int|null
     *
     * @ORM\Column(name="duration", type="bigint", length=20, nullable=true)
     */
    private ?int $duration = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private ?string $comment = null;

    /**
     * @var Timesheet
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Timesheet")
     * @ORM\JoinColumn(name="timesheet_id", referencedColumnName="timesheet_id")
     */
    private Timesheet $timesheet;

    /**
     * @var Project
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Project")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="project_id")
     */
    private Project $project;

    /**
     * @var ProjectActivity
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\ProjectActivity", inversedBy="timesheetItems")
     * @ORM\JoinColumn(name="activity_id", referencedColumnName="activity_id")
     */
    private ProjectActivity $projectActivity;

    /**
     * @var Employee
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee")
     * @ORM\JoinColumn(name="employee_id", referencedColumnName="emp_number")
     */
    private Employee $employee;

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
     * @return int|null
     */
    public function getDuration(): ?int
    {
        return $this->duration;
    }

    /**
     * @param int|null $duration
     */
    public function setDuration(?int $duration): void
    {
        $this->duration = $duration;
    }

    /**
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param string|null $comment
     */
    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
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
     * @return Project
     */
    public function getProject(): Project
    {
        return $this->project;
    }

    /**
     * @param Project $project
     */
    public function setProject(Project $project): void
    {
        $this->project = $project;
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

    /**
     * @return Employee
     */
    public function getEmployee(): Employee
    {
        return $this->employee;
    }

    /**
     * @param Employee $employee
     */
    public function setEmployee(Employee $employee): void
    {
        $this->employee = $employee;
    }
}
