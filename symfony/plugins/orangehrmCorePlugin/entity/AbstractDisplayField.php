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

abstract class AbstractDisplayField
{
    /**
     * @var ReportGroup
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\ReportGroup")
     * @ORM\JoinColumn(name="report_group_id", referencedColumnName="report_group_id")
     */
    protected ReportGroup $reportGroup;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected string $name;

    /**
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=255)
     */
    protected string $label;

    /**
     * @var string|null
     *
     * @ORM\Column(name="field_alias", type="string", length=255, nullable=true)
     */
    protected ?string $fieldAlias = null;

    /**
     * @var bool
     * @deprecated
     *
     * @ORM\Column(name="is_sortable", type="boolean", options={"default":0})
     */
    protected bool $sortable = false;

    /**
     * @var string|null
     * @deprecated
     *
     * @ORM\Column(name="sort_order", type="string", length=255, nullable=true)
     */
    protected ?string $sortOrder = null;

    /**
     * @var string|null
     * @deprecated
     *
     * @ORM\Column(name="sort_field", type="string", length=255, nullable=true)
     */
    protected ?string $sortField = null;

    /**
     * @var string
     * @deprecated
     *
     * @ORM\Column(name="element_type", type="string", length=255)
     */
    protected string $elementType;

    /**
     * @var string
     * @deprecated
     *
     * @ORM\Column(name="element_property", type="string", length=1000)
     */
    protected string $elementProperty;

    /**
     * @var string
     *
     * @ORM\Column(name="width", type="string", length=255)
     */
    protected string $width;

    /**
     * @var bool
     * @deprecated
     *
     * @ORM\Column(name="is_exportable", type="boolean", options={"default":0})
     */
    protected bool $exportable = false;

    /**
     * @var string|null
     * @deprecated
     *
     * @ORM\Column(name="text_alignment_style", type="string", length=20, nullable=true)
     */
    protected ?string $textAlignmentStyle = null;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_value_list", type="boolean", options={"default":0})
     */
    protected bool $isValueList = false;

    /**
     * @var DisplayFieldGroup|null
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\DisplayFieldGroup")
     * @ORM\JoinColumn(name="display_field_group_id", referencedColumnName="id", nullable=true)
     */
    protected ?DisplayFieldGroup $displayFieldGroup = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="default_value", type="string", length=255, nullable=true)
     */
    protected ?string $defaultValue = null;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_encrypted", type="boolean", options={"default":0})
     */
    protected bool $encrypted = false;

    /**
     * @var bool
     * @deprecated
     *
     * @ORM\Column(name="is_meta", type="boolean", options={"default":0})
     */
    protected bool $meta = false;

    /**
     * @return int
     */
    abstract public function getId(): int;

    /**
     * @param int $id
     */
    abstract public function setId(int $id): void;

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
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    /**
     * @return string|null
     */
    public function getFieldAlias(): ?string
    {
        return $this->fieldAlias;
    }

    /**
     * @param string|null $fieldAlias
     */
    public function setFieldAlias(?string $fieldAlias): void
    {
        $this->fieldAlias = $fieldAlias;
    }

    /**
     * @return bool
     */
    public function isSortable(): bool
    {
        return $this->sortable;
    }

    /**
     * @param bool $sortable
     */
    public function setSortable(bool $sortable): void
    {
        $this->sortable = $sortable;
    }

    /**
     * @return string|null
     */
    public function getSortOrder(): ?string
    {
        return $this->sortOrder;
    }

    /**
     * @param string|null $sortOrder
     */
    public function setSortOrder(?string $sortOrder): void
    {
        $this->sortOrder = $sortOrder;
    }

    /**
     * @return string|null
     */
    public function getSortField(): ?string
    {
        return $this->sortField;
    }

    /**
     * @param string|null $sortField
     */
    public function setSortField(?string $sortField): void
    {
        $this->sortField = $sortField;
    }

    /**
     * @return string
     */
    public function getElementType(): string
    {
        return $this->elementType;
    }

    /**
     * @param string $elementType
     */
    public function setElementType(string $elementType): void
    {
        $this->elementType = $elementType;
    }

    /**
     * @return string
     */
    public function getElementProperty(): string
    {
        return $this->elementProperty;
    }

    /**
     * @param string $elementProperty
     */
    public function setElementProperty(string $elementProperty): void
    {
        $this->elementProperty = $elementProperty;
    }

    /**
     * @return string
     */
    public function getWidth(): string
    {
        return $this->width;
    }

    /**
     * @param string $width
     */
    public function setWidth(string $width): void
    {
        $this->width = $width;
    }

    /**
     * @return bool
     */
    public function isExportable(): bool
    {
        return $this->exportable;
    }

    /**
     * @param bool $exportable
     */
    public function setExportable(bool $exportable): void
    {
        $this->exportable = $exportable;
    }

    /**
     * @return string|null
     */
    public function getTextAlignmentStyle(): ?string
    {
        return $this->textAlignmentStyle;
    }

    /**
     * @param string|null $textAlignmentStyle
     */
    public function setTextAlignmentStyle(?string $textAlignmentStyle): void
    {
        $this->textAlignmentStyle = $textAlignmentStyle;
    }

    /**
     * @return bool
     */
    public function isValueList(): bool
    {
        return $this->isValueList;
    }

    /**
     * @param bool $isValueList
     */
    public function setIsValueList(bool $isValueList): void
    {
        $this->isValueList = $isValueList;
    }

    /**
     * @return DisplayFieldGroup|null
     */
    public function getDisplayFieldGroup(): ?DisplayFieldGroup
    {
        return $this->displayFieldGroup;
    }

    /**
     * @param DisplayFieldGroup|null $displayFieldGroup
     */
    public function setDisplayFieldGroup(?DisplayFieldGroup $displayFieldGroup): void
    {
        $this->displayFieldGroup = $displayFieldGroup;
    }

    /**
     * @return string|null
     */
    public function getDefaultValue(): ?string
    {
        return $this->defaultValue;
    }

    /**
     * @param string|null $defaultValue
     */
    public function setDefaultValue(?string $defaultValue): void
    {
        $this->defaultValue = $defaultValue;
    }

    /**
     * @return bool
     */
    public function isEncrypted(): bool
    {
        return $this->encrypted;
    }

    /**
     * @param bool $encrypted
     */
    public function setEncrypted(bool $encrypted): void
    {
        $this->encrypted = $encrypted;
    }

    /**
     * @return bool
     */
    public function isMeta(): bool
    {
        return $this->meta;
    }

    /**
     * @param bool $meta
     */
    public function setMeta(bool $meta): void
    {
        $this->meta = $meta;
    }
}
