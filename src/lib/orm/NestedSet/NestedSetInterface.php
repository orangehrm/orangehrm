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

interface NestedSetInterface
{
    /**
     * @return int|null
     */
    public function getLft(): ?int;

    /**
     * @param int|null $lft
     */
    public function setLft(?int $lft): void;

    /**
     * @return int|null
     */
    public function getRgt(): ?int;

    /**
     * @param int|null $rgt
     */
    public function setRgt(?int $rgt): void;

    /**
     * @return int|null
     */
    public function getLevel(): ?int;

    /**
     * @param int|null $level
     */
    public function setLevel(?int $level): void;

    /**
     * @return NodeInterface
     */
    public function getNode(): NodeInterface;

    /**
     * @param NodeInterface|null $node
     */
    public function setNode(?NodeInterface $node): void;
}
