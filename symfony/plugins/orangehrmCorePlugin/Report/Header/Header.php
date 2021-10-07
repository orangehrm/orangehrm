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

namespace OrangeHRM\Core\Report\Header;

use InvalidArgumentException;
use OrangeHRM\Core\Api\V2\ParameterBag;

class Header implements HeaderDefinition
{
    /**
     * @var Column[]|StackedColumn[]
     */
    private array $columns = [];

    /**
     * @var ParameterBag|null
     */
    private ?ParameterBag $meta = null;

    /**
     * @param Column[]|StackedColumn[] $columns
     */
    public function __construct(array $columns)
    {
        $this->setColumns($columns);
    }

    /**
     * @return Column[]|StackedColumn[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @param Column[]|StackedColumn[] $columns
     */
    public function setColumns(array $columns): self
    {
        foreach ($columns as $column) {
            $this->addColumn($column);
        }
        return $this;
    }

    /**
     * @param Column|StackedColumn $column
     */
    public function addColumn($column): self
    {
        if (!($column instanceof Column || $column instanceof StackedColumn)) {
            throw new InvalidArgumentException('Invalid type');
        }
        $this->columns[] = $column;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function normalize(): array
    {
        $columns = [];
        foreach ($this->getColumns() as $column) {
            $columns[] = $column->toArray();
        }
        return $columns;
    }

    /**
     * @inheritDoc
     */
    public function getMeta(): ?ParameterBag
    {
        return $this->meta;
    }

    /**
     * @param ParameterBag|null $meta
     * @return self
     */
    public function setMeta(?ParameterBag $meta): self
    {
        $this->meta = $meta;
        return $this;
    }
}
