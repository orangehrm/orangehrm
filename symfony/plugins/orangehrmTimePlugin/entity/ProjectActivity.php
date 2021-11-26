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

use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectActivity
 *
 * @ORM\Table(name="ohrm_project_activity")
 * @ORM\Entity
 */
class ProjectActivity
{
    /**
     * @var int
     *
     * @ORM\Column(name="activity_id", type="integer", length=4)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $activityId;

    /**
     * @var Project
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Project", inversedBy="projectActivity")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="project_id")
     */
    private Project $project;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="is_deleted", type="boolean", nullable=true)
     */
    private ?bool $deleted = false;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=110)
     */
    private string $name;

    /**
     * @var TimeSheetItem
     *
     * @ORM\OneToOne(targetEntity="OrangeHRM\Entity\TimeSheetItem", mappedBy="projectActivity")
     */
    private TimeSheetItem $timeSheetItem;

    /**
     * @return int
     */
    public function getActivityId(): int
    {
        return $this->activityId;
    }

    /**
     * @param int $activityId
     */
    public function setActivityId(int $activityId): void
    {
        $this->activityId = $activityId;
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
     * @return bool|null
     */
    public function getDeleted(): ?bool
    {
        return $this->deleted;
    }

    /**
     * @param bool|null $deleted
     */
    public function setDeleted(?bool $deleted): void
    {
        $this->deleted = $deleted;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
