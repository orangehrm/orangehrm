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
 * @ORM\Table(name="ohrm_display_field_group")
 * @ORM\Entity
 */
class DisplayFieldGroup
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var ReportGroup
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\ReportGroup")
     * @ORM\JoinColumn(name="report_group_id", referencedColumnName="report_group_id")
     */
    private ReportGroup $reportGroup;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private string $name;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_list", type="boolean")
     */
    private bool $isList;

    /**
     * @var SelectedDisplayFieldGroup[]
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\SelectedDisplayFieldGroup", mappedBy="displayFieldGroup")
     */
    private iterable $selectedDisplayFieldGroups;

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
     * @return ReportGroup
     */
    public function getReportGroup(): ReportGroup
    {
        return $this->reportGroup;
    }

    /**
     * @param ReportGroup $reportGroup
     */
    public function setReportGroup(ReportGroup $reportGroup): void
    {
        $this->reportGroup = $reportGroup;
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

    /**
     * @return bool
     */
    public function isList(): bool
    {
        return $this->isList;
    }

    /**
     * @param bool $isList
     */
    public function setIsList(bool $isList): void
    {
        $this->isList = $isList;
    }
}
