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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use OrangeHRM\Entity\Decorator\DecoratorTrait;
use OrangeHRM\Entity\Decorator\ProjectActivityDecorator;

/**
 * @method ProjectActivityDecorator getDecorator()
 *
 * @ORM\Table(name="ohrm_project_activity")
 * @ORM\Entity
 */
class ProjectActivity
{
    use DecoratorTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="activity_id", type="integer", length=4)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var Project
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Project")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="project_id")
     */
    private Project $project;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_deleted", type="boolean", options={"default":0})
     */
    private bool $deleted = false;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=110)
     */
    private string $name;

    /**
     * @var TimesheetItem[]
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\TimesheetItem", mappedBy="projectActivity")
     */
    private iterable $timesheetItems;

    public function __construct()
    {
        $this->timesheetItems = new ArrayCollection();
    }

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
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    /**
     * @param bool $deleted
     */
    public function setDeleted(bool $deleted): void
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
