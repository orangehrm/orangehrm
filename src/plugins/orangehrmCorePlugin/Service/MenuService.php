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
use OrangeHRM\I18N\Traits\Service\I18NHelperTrait;

class MenuService
{
    use UserRoleManagerTrait;
    use ModuleScreenHelperTrait;
    use AuthUserTrait;
    use I18NHelperTrait;

    public const CORE_MENU_SIDE_PANEL_CACHE_KEY = 'core.menu.side_panel';
    public const CORE_MENU_TOP_RIBBON_CACHE_KEY = 'core.menu.top_ribbon';
    public const CORE_MENU_TOP_RIBBON_KEYS_CACHE_KEY = 'core.menu.top_ribbon_keys';

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
        $this->getAuthUser()->removeAttribute(self::CORE_MENU_SIDE_PANEL_CACHE_KEY);
        foreach ($this->getAuthUser()->getAttribute(self::CORE_MENU_TOP_RIBBON_KEYS_CACHE_KEY, []) as $topRibbonKey) {
            $cacheKey = $this->generateCacheKeyForTopMenuItem($topRibbonKey);
            $this->getAuthUser()->removeAttribute($cacheKey);
        }
        $this->getAuthUser()->removeAttribute(self::CORE_MENU_TOP_RIBBON_KEYS_CACHE_KEY);

        return $this->getMenuDao()->enableModuleMenuItems($moduleName, $menuTitles);
    }

    public function invalidateCachedMenuItems(): void
    {
        $this->getAuthUser()->removeAttribute(self::CORE_MENU_SIDE_PANEL_CACHE_KEY);
        foreach ($this->getAuthUser()->getAttribute(self::CORE_MENU_TOP_RIBBON_KEYS_CACHE_KEY, []) as $topRibbonKey) {
            $cacheKey = $this->generateCacheKeyForTopMenuItem($topRibbonKey);
            $this->getAuthUser()->removeAttribute($cacheKey);
        }
        $this->getAuthUser()->removeAttribute(self::CORE_MENU_TOP_RIBBON_KEYS_CACHE_KEY);
    }

    /**
     * @return DetailedMenuItem[]
     */
    private function getDetailedSidePanelMenuItemsAlongWithCache(): array
    {
        if (!$this->getAuthUser()->hasAttribute(self::CORE_MENU_SIDE_PANEL_CACHE_KEY)) {
            $userRoles = $this->getUserRoleManager()->getUserRolesForAuthUser();
            $sidePanelMenuItems = $this->getMenuDao()->getSidePanelMenuItems($userRoles);

            $detailedSidePanelMenuItems = [];
            foreach ($sidePanelMenuItems as $sidePanelMenuItem) {
                $screen = $sidePanelMenuItem->getScreen();
                if (is_null($screen)) {
                    throw new LogicException('Side panel menu item should have screen assigned');
                }
                $detailedSidePanelMenuItems[] = DetailedMenuItem::createFromMenuItem($sidePanelMenuItem);
            }
            $this->getAuthUser()->setAttribute(self::CORE_MENU_SIDE_PANEL_CACHE_KEY, $detailedSidePanelMenuItems);
        }

        return $this->getAuthUser()->getAttribute(self::CORE_MENU_SIDE_PANEL_CACHE_KEY);
    }

    /**
     * @param int $sidePanelMenuItemId
     * @return DetailedMenuItem[]
     */
    private function getTopMenuItemsAlongWithCache(int $sidePanelMenuItemId): array
    {
        $cacheKey = $this->generateCacheKeyForTopMenuItem($sidePanelMenuItemId);
        $userRoles = $this->getUserRoleManager()->getUserRolesForAuthUser();
        if (!$this->getAuthUser()->hasAttribute($cacheKey)) {
            $topMenuItems = $this->getMenuDao()->getTopMenuItems($userRoles, $sidePanelMenuItemId);
            $this->getAuthUser()->setAttribute($cacheKey, $topMenuItems);

            $topRibbonKeys = $this->getAuthUser()->getAttribute(self::CORE_MENU_TOP_RIBBON_KEYS_CACHE_KEY, []);
            $topRibbonKeys[] = $sidePanelMenuItemId;
            $this->getAuthUser()->setAttribute(self::CORE_MENU_TOP_RIBBON_KEYS_CACHE_KEY, $topRibbonKeys);
        }

        return $this->getAuthUser()->getAttribute($cacheKey);
    }

    /**
     * @param int $sidePanelMenuItemId
     * @return string
     */
    private function generateCacheKeyForTopMenuItem(int $sidePanelMenuItemId): string
    {
        return self::CORE_MENU_TOP_RIBBON_CACHE_KEY . ".$sidePanelMenuItemId";
    }

    /**
     * @param string $baseUrl
     * @return array
     */
    public function getMenuItems(string $baseUrl): array
    {
        $currentModuleAndScreen = $this->getCurrentModuleAndScreen();

        $configuratorMenuItems = [];
        $screen = $this->getScreenPermissionService()
            ->getScreenDao()
            ->getScreen($currentModuleAndScreen->getModule(), $currentModuleAndScreen->getScreen());
        if ($screen instanceof Screen && !is_null($screen->getMenuConfigurator())) {
            $configuratorClass = $screen->getMenuConfigurator();
            $configurator = new $configuratorClass();
            if (!$configurator instanceof MenuConfigurator) {
                throw new LogicException("Invalid configurator class: $configuratorClass");
            }
            $configuratorMenuItems = $this->getMenuItemChainForMenuItem($configurator->configure($screen));
        }

        $detailedSidePanelMenuItems = $this->getDetailedSidePanelMenuItemsAlongWithCache();
        $normalizedSidePanelMenuItems = [];
        $selectedSidePanelMenuId = null;

        foreach ($detailedSidePanelMenuItems as $detailedSidePanelMenuItem) {
            $active = false;
            if (is_null($selectedSidePanelMenuId) && $active = $this->isActiveSidePanelMenuItem(
                $detailedSidePanelMenuItem,
                $currentModuleAndScreen,
                $configuratorMenuItems
            )) {
                $selectedSidePanelMenuId = $detailedSidePanelMenuItem->getId();
            }
            $normalizedSidePanelMenuItems[] = $this->normalizeMenuItem($detailedSidePanelMenuItem, $baseUrl, $active);
        }

        $normalizedTopMenuItems = [];
        if (!is_null($selectedSidePanelMenuId)) {
            $topMenuItems = $this->getTopMenuItemsAlongWithCache($selectedSidePanelMenuId);
            foreach ($topMenuItems as $topMenuItem) {
                $normalizedTopMenuItem = $this->normalizeTopMenuItem(
                    $topMenuItem,
                    $baseUrl,
                    $configuratorMenuItems,
                    $currentModuleAndScreen
                );
                is_null($normalizedTopMenuItem) ?: $normalizedTopMenuItems[] = $normalizedTopMenuItem;
            }
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
     * @param DetailedMenuItem $menuItem
     * @param ModuleScreen $currentModuleScreen
     * @param array<int, MenuItem> $configuratorMenuItems
     * @return bool
     */
    private function isActiveTopMenuItem(
        DetailedMenuItem $menuItem,
        ModuleScreen $currentModuleScreen,
        array $configuratorMenuItems = []
    ): bool {
        if (!empty($configuratorMenuItems)) {
            return isset($configuratorMenuItems[$menuItem->getId()]);
        }
        return $menuItem->getScreen() === $currentModuleScreen->getOverriddenScreen();
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
            'name' => $this->getI18NHelper()->transBySource($detailedMenuItem->getMenuTitle()),
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
     * @param array<int, MenuItem> $configuratorMenuItems
     * @param ModuleScreen|null $currentModuleAndScreen
     * @return array|null
     */
    private function normalizeTopMenuItem(
        DetailedMenuItem $detailedMenuItem,
        string $baseUrl,
        array $configuratorMenuItems = [],
        ?ModuleScreen $currentModuleAndScreen = null,
        bool $leaf = false
    ): ?array {
        $active = $this->isActiveTopMenuItem($detailedMenuItem, $currentModuleAndScreen, $configuratorMenuItems);
        $menuItem = $this->normalizeMenuItem($detailedMenuItem, $baseUrl, $active);
        $leaf ?: $menuItem['children'] = [];

        // if sub menu item exists
        if (!empty($detailedMenuItem->getChildMenuItems())) {
            foreach ($detailedMenuItem->getChildMenuItems() as $subItem) {
                $active = $this->isActiveTopMenuItem(
                    $subItem,
                    $currentModuleAndScreen,
                    $configuratorMenuItems
                );
                if ($active) {
                    $menuItem['active'] = true;
                }
                $normalizedTopMenuItem = $this->normalizeTopMenuItem(
                    $subItem,
                    $baseUrl,
                    $configuratorMenuItems,
                    $currentModuleAndScreen,
                    true
                );
                is_null($normalizedTopMenuItem) ?: $menuItem['children'][] = $normalizedTopMenuItem;
            }
        }

        /**
         * This is to hide second level menu items which don't have assigned screen &
         * no child menu items assigned. When there is no screen assigned to a menu item
         * no way to derive module or user permission
         */
        if (!$leaf && empty($menuItem['children']) && $menuItem['url'] === '#') {
            return null;
        }
        return $menuItem;
    }
}
