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

use Countable;

class StackedColumn implements Countable
{
    private ?string $name = null;

    /**
     * @var Column[]
     */
    private array $children = [];

    /**
     * @param Column[] $children
     */
    public function __construct(array $children)
    {
        $this->setChildren($children);
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return $this
     */
    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Column[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @param Column[] $children
     * @return $this
     */
    public function setChildren(array $children): self
    {
        foreach ($children as $child) {
            $this->addChild($child);
        }
        return $this;
    }

    /**
     * @param Column $child
     * @return $this
     */
    public function addChild(Column $child): self
    {
        $this->children[] = $child;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $children = [];
        foreach ($this->getChildren() as $child) {
            $children[] = $child->toArray();
        }
        return [
            'name' => $this->getName(),
            'children' => $children,
        ];
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->getChildren());
    }
}
