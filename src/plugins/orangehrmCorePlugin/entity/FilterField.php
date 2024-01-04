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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ohrm_filter_field")
 * @ORM\Entity
 */
class FilterField
{
    /**
     * @var int
     *
     * @ORM\Column(name="filter_field_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
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
     * @var string
     *
     * @ORM\Column(name="where_clause_part", type="text")
     */
    private string $whereClausePart;

    /**
     * @var string|null
     *
     * @ORM\Column(name="filter_field_widget", type="string", length=255, nullable=true)
     */
    private ?string $filterFieldWidget = null;

    /**
     * @var int
     *
     * @ORM\Column(name="condition_no", type="integer")
     */
    private int $conditionNo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="required", type="string", length=10, nullable=true)
     */
    private ?string $required = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="class_name", type="string", length=255, nullable=true)
     */
    private ?string $className = null;

    /**
     * @var SelectedFilterField[]
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\SelectedFilterField", mappedBy="filterField")
     */
    private iterable $selectedFilterFields;

    public function __construct()
    {
        $this->selectedFilterFields = new ArrayCollection();
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
     * @return string
     */
    public function getWhereClausePart(): string
    {
        return $this->whereClausePart;
    }

    /**
     * @param string $whereClausePart
     */
    public function setWhereClausePart(string $whereClausePart): void
    {
        $this->whereClausePart = $whereClausePart;
    }

    /**
     * @return string|null
     */
    public function getFilterFieldWidget(): ?string
    {
        return $this->filterFieldWidget;
    }

    /**
     * @param string|null $filterFieldWidget
     */
    public function setFilterFieldWidget(?string $filterFieldWidget): void
    {
        $this->filterFieldWidget = $filterFieldWidget;
    }

    /**
     * @return int
     */
    public function getConditionNo(): int
    {
        return $this->conditionNo;
    }

    /**
     * @param int $conditionNo
     */
    public function setConditionNo(int $conditionNo): void
    {
        $this->conditionNo = $conditionNo;
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required === 'true';
    }

    /**
     * @param bool $required
     */
    public function setRequired(bool $required): void
    {
        $this->required = $required ? 'true' : 'false';
    }

    /**
     * @return string|null
     */
    public function getClassName(): ?string
    {
        return $this->className;
    }

    /**
     * @param string $className
     */
    public function setClassName(string $className): void
    {
        $this->className = $className;
    }
}
