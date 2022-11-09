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

namespace OrangeHRM\Core\Report\FilterField;

use OrangeHRM\ORM\QueryBuilderWrapper;

abstract class FilterField
{
    private int $order;
    private ?string $x;
    private ?string $y;
    private ?string $operator;

    /**
     * @param int $order
     * @param string|null $x
     * @param string|null $y
     * @param string|null $operator
     */
    final public function __construct(?string $operator, ?string $x = null, ?string $y = null, int $order = 0)
    {
        $this->order = $order;
        $this->x = $x;
        $this->y = $y;
        $this->operator = $operator;
    }

    /**
     * @param QueryBuilderWrapper $queryBuilderWrapper
     */
    abstract public function addWhereToQueryBuilder(QueryBuilderWrapper $queryBuilderWrapper): void;

    /**
     * @return string[]
     */
    abstract public function getEntityAliases(): array;

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * @return string|null
     */
    public function getX(): ?string
    {
        return $this->x;
    }

    /**
     * @return string|null
     */
    public function getY(): ?string
    {
        return $this->y;
    }

    /**
     * @return string|null
     */
    public function getOperator(): ?string
    {
        return $this->operator;
    }

    /**
     * @param callable $closure e.g. fn($x) => trim($x)
     */
    public function modifyX(callable $closure)
    {
        $this->x = $closure($this->getX());
    }

    /**
     * @param callable $closure e.g. fn($y) => trim($y)
     */
    public function modifyY(callable $closure)
    {
        $this->y = $closure($this->getY());
    }
}
