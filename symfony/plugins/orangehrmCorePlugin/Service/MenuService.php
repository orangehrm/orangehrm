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

namespace OrangeHRM\Core\Service;

use LogicException;
use OrangeHRM\Core\Authorization\Service\ScreenPermissionService;
use OrangeHRM\Core\Dao\MenuDao;
use OrangeHRM\Core\Dto\ModuleScreen;
use OrangeHRM\Core\Menu\DetailedMenuItem;
use OrangeHRM\Core\Menu\MenuConfigurator;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\ModuleScreenHelperTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\MenuItem;
use OrangeHRM\Entity\Screen;

class MenuService
{
    use UserRoleManagerTrait;
    use ModuleScreenHelperTrait;
    use AuthUserTrait;

    public const CORE_MENU_SIDE_PANEL_CACHE_KEY = 'core.menu.side_panel';
    public const CORE_MENU_TOP_RIBBON_CACHE_KEY = 'core.menu.top_ribbon';

    /**
     * @var MenuDao|null
     */
    protected ?MenuDao $menuDao = null;
    /**
     * @var ScreenPermissionService|null
     */
    protected ?ScreenPermissionService $screenPermissionService = null;

    /**
     * @return MenuDao
     */
    public function getMenuDao(): MenuDao
    {
        if (is_null($this->menuDao)) {
            $this->menuDao = new MenuDao();
        }

        return $this->menuDao;
    }

    /**
     * @return ScreenPermissionService
     */
    public function getScreenPermissionService(): ScreenPermissionService
    {
        if (!$this->screenPermissionService instanceof ScreenPermissionService) {
            $this->screenPermissionService = new ScreenPermissionService();
        }
        return $this->screenPermissionService;
    }

    /**
     * @param string $moduleName
     * @param array $menuTitles
     * @return int
     */
    public function enableModuleMenuItems(string $moduleName, array $menuTitles = []): int
    {
        return $this->getMenuDao()->enableModuleMenuItems($moduleName, $menuTitles);
    }

    /**
     * @param string $baseUrl
     * @return array
     */
    public function getMenuItems(string $baseUrl): array
    {
        $moduleScreen = $this->getCurrentModuleAndScreen();

        $configuratorMenuItems = [];
        $screen = $this->getScreenPermissionService()
            ->getScreenDao()
            ->getScreen($moduleScreen->getModule(), $moduleScreen->getScreen());
        if ($screen instanceof Screen && !is_null($screen->getMenuConfigurator())) {
            $configuratorClass = $screen->getMenuConfigurator();
            $configurator = new $configuratorClass();
            if (!$configurator instanceof MenuConfigurator) {
                throw new LogicException("Invalid configurator class: $configuratorClass");
            }
            $configuratorMenuItems = $this->getMenuItemChainForMenuItem($configurator->configure($screen));
        }

        $userRoles = $this->getUserRoleManager()->getUserRolesForAuthUser();
        $sidePanelMenuItems = $this->getMenuDao()->getSidePanelMenuItems($userRoles);

        $normalizedSidePanelMenuItems = [];
        $selectedSidePanelMenuId = null;
        foreach ($sidePanelMenuItems as $sidePanelMenuItem) {
            $screen = $sidePanelMenuItem->getScreen();
            if (is_null($screen)) {
                throw new LogicException('Side panel menu item should have screen assigned');
            }
            $detailedSidePanelMenuItem = DetailedMenuItem::createFromMenuItem($sidePanelMenuItem);

            $active = false;
            if (is_null($selectedSidePanelMenuId) && $active = $this->isActiveSidePanelMenuItem(
                $detailedSidePanelMenuItem,
                $moduleScreen,
                $configuratorMenuItems
            )) {
                $selectedSidePanelMenuId = $sidePanelMenuItem->getId();
            }
            $normalizedSidePanelMenuItems[] = $this->normalizeMenuItem($detailedSidePanelMenuItem, $baseUrl, $active);
        }

        $topMenuItems = $this->getMenuDao()->getTopMenuItems($userRoles, $selectedSidePanelMenuId);
        $normalizedTopMenuItems = [];
        foreach ($topMenuItems as $topMenuItem) {
            $normalizedTopMenuItems[] = $this->normalizeTopMenuItem($topMenuItem, $baseUrl, $moduleScreen);
        }

        return [
            $normalizedSidePanelMenuItems,
            $normalizedTopMenuItems,
        ];
    }

    /**
     * @param MenuItem|null $menuItem
     * @return array<int, MenuItem>
     */
    private function getMenuItemChainForMenuItem(?MenuItem $menuItem): array
    {
        if (is_null($menuItem)) {
            return [];
        }
        $chain[$menuItem->getId()] = $menuItem;
        while (!is_null($menuItem->getParent())) {
            $menuItem = $menuItem->getParent();
            $chain[$menuItem->getId()] = $menuItem;
        }
        return $chain;
    }

    /**
     * @param DetailedMenuItem $sidePanelMenuItem
     * @param ModuleScreen $currentModuleScreen
     * @param array<int, MenuItem> $configuratorMenuItems
     * @return bool
     */
    private function isActiveSidePanelMenuItem(
        DetailedMenuItem $sidePanelMenuItem,
        ModuleScreen $currentModuleScreen,
        array $configuratorMenuItems = []
    ): bool {
        if (!empty($configuratorMenuItems)) {
            return isset($configuratorMenuItems[$sidePanelMenuItem->getId()]);
        }
        return $sidePanelMenuItem->getModule() === $currentModuleScreen->getOverriddenModule();
    }

    /**
     * @param DetailedMenuItem $detailedMenuItem
     * @param string $baseUrl
     * @param bool $active
     * @return array
     */
    private function normalizeMenuItem(
        DetailedMenuItem $detailedMenuItem,
        string $baseUrl,
        bool $active = false
    ): array {
        $url = '#';
        if (!empty($detailedMenuItem->getScreen()) && !empty($detailedMenuItem->getModule())) {
            $url = $baseUrl . '/' . $detailedMenuItem->getModule() . '/' . $detailedMenuItem->getScreen();
        }
        $menuItem = [
            'id' => $detailedMenuItem->getId(),
            'name' => $detailedMenuItem->getMenuTitle(),
            'url' => $url,
        ];

        if (!is_null($detailedMenuItem->getAdditionalParams()) &&
            isset($detailedMenuItem->getAdditionalParams()['icon'])) {
            $menuItem = array_merge($menuItem, $detailedMenuItem->getAdditionalParams());
        }

        if ($active) {
            $menuItem['active'] = true;
        }
        return $menuItem;
    }

    /**
     * @param DetailedMenuItem $detailedMenuItem
     * @param string $baseUrl
     * @param ModuleScreen|null $moduleScreen
     * @return array
     */
    private function normalizeTopMenuItem(
        DetailedMenuItem $detailedMenuItem,
        string $baseUrl,
        ?ModuleScreen $moduleScreen = null
    ): array {
        $active = $detailedMenuItem->getScreen() === $moduleScreen->getScreen();
        $newMenuItem = $this->normalizeMenuItem($detailedMenuItem, $baseUrl, $active);
        $newMenuItem['children'] = [];
        if ($active) {
            $newMenuItem['active'] = true;
        }

        // if sub menu item exists
        if (!empty($detailedMenuItem->getChildMenuItems())) {
            foreach ($detailedMenuItem->getChildMenuItems() as $subItem) {
                $active = $subItem->getScreen() === $moduleScreen->getScreen();
                if ($active) {
                    $newMenuItem['active'] = true;
                }
                $newMenuItem['children'][] = $this->normalizeTopMenuItem($subItem, $baseUrl, $moduleScreen);
            }
        }
        return $newMenuItem;
    }
}
