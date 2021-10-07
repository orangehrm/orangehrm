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

/**
 * @ORM\Table(name="ohrm_summary_display_field")
 * @ORM\Entity
 */
class SummaryDisplayField
{
    /**
     * @var int
     *
     * @ORM\Column(name="summary_display_field_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private int $id;

    /**
     * @var string
     *
     * @ORM\Column(name="function", type="string", length=1000)
     */
    private string $function;

    /**
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=255)
     */
    private string $label;

    /**
     * @var string|null
     *
     * @ORM\Column(name="field_alias", type="string", length=255, nullable=true)
     */
    private ?string $fieldAlias = null;

    /**
     * @var string
     *
     * @ORM\Column(name="is_sortable", type="string", length=10)
     */
    private string $sortable;

    /**
     * @var string|null
     *
     * @ORM\Column(name="sort_order", type="string", length=255, nullable=true)
     */
    private ?string $sortOrder = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="sort_field", type="string", length=255, nullable=true)
     */
    private ?string $sortField = null;

    /**
     * @var string
     *
     * @ORM\Column(name="element_type", type="string", length=255)
     */
    private string $elementType;

    /**
     * @var string
     *
     * @ORM\Column(name="element_property", type="string", length=1000)
     */
    private string $elementProperty;

    /**
     * @var string
     *
     * @ORM\Column(name="width", type="string", length=255)
     */
    private string $width;

    /**
     * @var string|null
     *
     * @ORM\Column(name="is_exportable", type="string", length=10, nullable=true)
     */
    private ?string $exportable = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="text_alignment_style", type="string", length=20, nullable=true)
     */
    private ?string $textAlignmentStyle = null;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_value_list", type="boolean", options={"default":0})
     */
    private bool $isValueList = false;

    /**
     * @var DisplayFieldGroup
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\DisplayFieldGroup")
     * @ORM\JoinColumn(name="display_field_group_id", referencedColumnName="id")
     */
    private DisplayFieldGroup $displayFieldGroup;

    /**
     * @var string|null
     *
     * @ORM\Column(name="default_value", type="string", length=255, nullable=true)
     */
    private ?string $defaultValue = null;

    /**
     * @var SelectedGroupField[]
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\SelectedGroupField", mappedBy="summaryDisplayField")
     */
    private iterable $selectedGroupFields;

    public function __construct()
    {
        $this->selectedGroupFields = new ArrayCollection();
    }
}
