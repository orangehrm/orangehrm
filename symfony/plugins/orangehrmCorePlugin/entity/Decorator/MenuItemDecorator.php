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

namespace OrangeHRM\Entity\Decorator;

use OrangeHRM\Entity\MenuItem;

class MenuItemDecorator
{
    /**
     * @var MenuItem
     */
    private MenuItem $menuItem;

    /**
     * @var MenuItem[]
     */
    private array $childMenuItems = [];

    /**
     * @param MenuItem $menuItem
     */
    public function __construct(MenuItem $menuItem)
    {
        $this->menuItem = $menuItem;
    }

    /**
     * @return MenuItem
     */
    protected function getMenuItem(): MenuItem
    {
        return $this->menuItem;
    }

    /**
     * @param MenuItem $childMenuItem
     */
    public function addChild(MenuItem $childMenuItem)
    {
        $this->childMenuItems[] = $childMenuItem;
    }

    /**
     * @return MenuItem[]
     */
    public function getChildMenuItems(): array
    {
        return $this->childMenuItems;
    }
}
