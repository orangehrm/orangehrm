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

namespace OrangeHRM\ORM\NestedSet;

interface NodeInterface
{
    /**
     * @return bool
     */
    public function hasChildren(): bool;

    /**
     * @return NestedSetInterface[]
     */
    public function getChildren(int $depth = 1): array;

    /**
     * @return bool
     */
    public function hasParent(): bool;

    /**
     * @return NestedSetInterface|null
     */
    public function getParent(): ?NestedSetInterface;

    /**
     * @return int
     */
    public function getLevel(): int;

    /**
     * @param NestedSetInterface $parent
     */
    public function insertAsLastChildOf(NestedSetInterface $parent): void;

    /**
     * @param NestedSetInterface $child
     */
    public function addChild(NestedSetInterface $child): void;

    /**
     * @return bool
     */
    public function isLeaf(): bool;

    /**
     * @return bool
     */
    public function isRoot(): bool;

    /**
     * Deletes node and it's descendants
     */
    public function delete();
}
