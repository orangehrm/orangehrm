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
 * @ORM\Table(name="ohrm_selected_composite_display_field")
 * @ORM\Entity
 */
class SelectedCompositeDisplayField
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private int $id;

    /**
     * @var CompositeDisplayField
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\CompositeDisplayField", inversedBy="selectedCompositeDisplayFields")
     * @ORM\JoinColumn(name="composite_display_field_id", referencedColumnName="composite_display_field_id")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private CompositeDisplayField $compositeDisplayField;

    /**
     * @var Report
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Report")
     * @ORM\JoinColumn(name="report_id", referencedColumnName="report_id")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private Report $report;

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
     * @return CompositeDisplayField
     */
    public function getCompositeDisplayField(): CompositeDisplayField
    {
        return $this->compositeDisplayField;
    }

    /**
     * @param CompositeDisplayField $compositeDisplayField
     */
    public function setCompositeDisplayField(CompositeDisplayField $compositeDisplayField): void
    {
        $this->compositeDisplayField = $compositeDisplayField;
    }

    /**
     * @return Report
     */
    public function getReport(): Report
    {
        return $this->report;
    }

    /**
     * @param Report $report
     */
    public function setReport(Report $report): void
    {
        $this->report = $report;
    }
}
