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

use Doctrine\ORM\Mapping as ORM;
use OrangeHRM\Entity\Decorator\DecoratorTrait;
use OrangeHRM\Entity\Decorator\SelectedFilterFieldDecorator;

/**
 * @method SelectedFilterFieldDecorator getDecorator()
 *
 * @ORM\Table(name="ohrm_selected_filter_field")
 * @ORM\Entity
 */
class SelectedFilterField
{
    use DecoratorTrait;

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
     * @var FilterField
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\FilterField", inversedBy="selectedFilterFields")
     * @ORM\JoinColumn(name="filter_field_id", referencedColumnName="filter_field_id")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private FilterField $filterField;

    /**
     * @var int
     *
     * @ORM\Column(name="filter_field_order", type="integer")
     */
    private int $filterFieldOrder;

    /**
     * @var string|null
     *
     * @ORM\Column(name="value1", type="string", length=255, nullable=true)
     */
    private ?string $x = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="value2", type="string", length=255, nullable=true)
     */
    private ?string $y = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="where_condition", type="string", length=255, nullable=true)
     */
    private ?string $operator = null;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private string $type;

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

    /**
     * @return FilterField
     */
    public function getFilterField(): FilterField
    {
        return $this->filterField;
    }

    /**
     * @param FilterField $filterField
     */
    public function setFilterField(FilterField $filterField): void
    {
        $this->filterField = $filterField;
    }

    /**
     * @return int
     */
    public function getFilterFieldOrder(): int
    {
        return $this->filterFieldOrder;
    }

    /**
     * @param int $filterFieldOrder
     */
    public function setFilterFieldOrder(int $filterFieldOrder): void
    {
        $this->filterFieldOrder = $filterFieldOrder;
    }

    /**
     * @return string|null
     */
    public function getX(): ?string
    {
        return $this->x;
    }

    /**
     * @param string|null $x
     */
    public function setX(?string $x): void
    {
        $this->x = $x;
    }

    /**
     * @return string|null
     */
    public function getY(): ?string
    {
        return $this->y;
    }

    /**
     * @param string|null $y
     */
    public function setY(?string $y): void
    {
        $this->y = $y;
    }

    /**
     * @return string|null
     */
    public function getOperator(): ?string
    {
        return $this->operator;
    }

    /**
     * @param string|null $operator
     */
    public function setOperator(?string $operator): void
    {
        $this->operator = $operator;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }
}
