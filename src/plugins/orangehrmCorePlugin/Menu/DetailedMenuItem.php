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

namespace OrangeHRM\Core\Menu;

use OrangeHRM\Entity\MenuItem;
use OrangeHRM\Entity\Screen;

class DetailedMenuItem
{
    private int $id;
    private string $menuTitle;
    private ?array $additionalParams = [];
    private ?string $module = null;
    private ?string $screen = null;

    /**
     * @var DetailedMenuItem[]
     */
    private array $childMenuItems = [];

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
     * @return string
     */
    public function getMenuTitle(): string
    {
        return $this->menuTitle;
    }

    /**
     * @param string $menuTitle
     */
    public function setMenuTitle(string $menuTitle): void
    {
        $this->menuTitle = $menuTitle;
    }

    /**
     * @return array|null
     */
    public function getAdditionalParams(): ?array
    {
        return $this->additionalParams;
    }

    /**
     * @param array|null $additionalParams
     */
    public function setAdditionalParams(?array $additionalParams): void
    {
        $this->additionalParams = $additionalParams;
    }

    /**
     * @return string|null
     */
    public function getModule(): ?string
    {
        return $this->module;
    }

    /**
     * @param string|null $module
     */
    public function setModule(?string $module): void
    {
        $this->module = $module;
    }

    /**
     * @return string|null
     */
    public function getScreen(): ?string
    {
        return $this->screen;
    }

    /**
     * @param string|null $screen
     */
    public function setScreen(?string $screen): void
    {
        $this->screen = $screen;
    }

    /**
     * @param MenuItem $sidePanelMenuItem
     * @return static
     */
    public static function createFromMenuItem(MenuItem $sidePanelMenuItem): self
    {
        $menuItem = new DetailedMenuItem();
        $menuItem->setId($sidePanelMenuItem->getId());
        $menuItem->setMenuTitle($sidePanelMenuItem->getMenuTitle());
        $menuItem->setAdditionalParams($sidePanelMenuItem->getAdditionalParams());

        $screen = $sidePanelMenuItem->getScreen();
        if ($screen instanceof Screen) {
            $menuItem->setModule($screen->getModule()->getName());
            $menuItem->setScreen($screen->getActionUrl());
        }
        return $menuItem;
    }

    /**
     * @param DetailedMenuItem $childMenuItem
     */
    public function addChild(self $childMenuItem)
    {
        $this->childMenuItems[] = $childMenuItem;
    }

    /**
     * @return DetailedMenuItem[]
     */
    public function getChildMenuItems(): array
    {
        return $this->childMenuItems;
    }
}
