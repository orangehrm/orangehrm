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

use OrangeHRM\Core\Dao\MenuDao;
use OrangeHRM\Core\Dto\ModuleScreen;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Core\Exception\ServiceException;
use OrangeHRM\Core\Traits\ModuleScreenHelperTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\MenuItem;
use OrangeHRM\Entity\Screen;
use OrangeHRM\Entity\UserRole;

class MenuService
{
    use UserRoleManagerTrait;
    use ModuleScreenHelperTrait;

    /**
     * @var MenuDao|null
     */
    protected ?MenuDao $menuDao = null;
    /**
     * @var int
     */
    protected int $numberOfLevels = 3;
    /**
     * @var array
     */
    protected array $actionArray = [];
    /**
     * @var array
     */
    protected array $parentIdArray = [];
    /**
     * @var array
     */
    protected array $levelArray = [];

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
     * @param MenuDao $menuDao
     */
    public function setMenuDao(MenuDao $menuDao): void
    {
        $this->menuDao = $menuDao;
    }

    /**
     * Returns menu array for given user roles
     *
     * Returned array is a multi-dimentional array
     * containing MenuItem objects
     *
     * @param UserRole[]|string[] $userRoleList Array of user role names or Array of UserRole objects
     *
     * @return MenuItem[] Array of MenuItem objects
     * @throws DaoException
     */
    public function getMenuItemCollection(array $userRoleList): array
    {
        $menuArray = $this->_getMenuItemListAsArray($userRoleList);

        for ($i = $this->numberOfLevels; $i > 0; $i--) {
            foreach ($menuArray as $menuItem) {
                $parentId = $menuItem->getParent() instanceof MenuItem ? $menuItem->getParent()->getId() : null;

                if ($menuItem->getLevel() == $i && array_key_exists($parentId, $menuArray)) {
                    if ($menuItem->getScreen() instanceof Screen || !$this->_areSubMenusEmpty($menuItem)) {
                        $menuArray[$parentId]->addSubMenuItem($menuItem);
                    }

                    unset($menuArray[$menuItem->getId()]);
                }
            }
        }

        foreach ($menuArray as $key => $value) {
            $subMenuItems = $value->getSubMenuItems();

            if (!($value->getScreen() instanceof Screen) && empty($subMenuItems)) {
                unset($menuArray[$key]);
            }
        }

        return $menuArray;
    }

    /**
     * @param UserRole[]|string[] $userRoleList
     * @return array
     * @throws DaoException
     */
    public function getMenuItemDetails(array $userRoleList): array
    {
        $firstLevelItems = $this->getMenuItemCollection($userRoleList);
        $firstLevelHolder = [];

        foreach ($firstLevelItems as $firstLevelItem) {
            $secondLevelItems = $firstLevelItem->getSubMenuItems();
            $secondLevelHolder = [];

            if (!empty($secondLevelItems)) {
                foreach ($secondLevelItems as $secondLevelItem) {
                    $thirdLevelItems = $secondLevelItem->getSubMenuItems();
                    $thirdLevelHolder = [];

                    if (!empty($thirdLevelItems)) {
                        foreach ($thirdLevelItems as $thirdLevelItem) {
                            $menuItemDetails = $this->_extractMenuItemToArray($thirdLevelItem);
                            $this->_populateMenuInfoArrays($thirdLevelItem, $menuItemDetails);
                            $thirdLevelHolder[] = $menuItemDetails;
                        }
                    }

                    $menuItemDetails = $this->_extractMenuItemToArray($secondLevelItem);
                    $menuItemDetails['subMenuItems'] = $thirdLevelHolder;
                    $this->_populateMenuInfoArrays($secondLevelItem, $menuItemDetails);
                    $secondLevelHolder[] = $menuItemDetails;
                }
            }

            $menuItemDetails = $this->_extractMenuItemToArray($firstLevelItem);
            $menuItemDetails['subMenuItems'] = $secondLevelHolder;
            $this->_populateMenuInfoArrays($firstLevelItem, $menuItemDetails);
            $firstLevelHolder[] = $menuItemDetails;
        }

        return [
            'menuItemArray' => $firstLevelHolder,
            'actionArray' => $this->actionArray,
            'parentIdArray' => $this->parentIdArray,
            'levelArray' => $this->levelArray
        ];
    }

    /**
     * @param MenuItem $menuItem
     * @param array $menuItemDetails
     */
    private function _populateMenuInfoArrays(MenuItem $menuItem, array $menuItemDetails): void
    {
        $this->parentIdArray[$menuItemDetails['id']] = $menuItem->getParent() instanceof MenuItem
            ? $menuItem->getParent()->getId() : null;
        $this->levelArray[$menuItemDetails['id']] = $menuItemDetails['level'];

        if (!empty($menuItemDetails['module']) && !empty($menuItemDetails['action'])) {
            $this->actionArray[$menuItemDetails['module'] . '_' . $menuItemDetails['action']] = $menuItemDetails['id'];
        }
    }

    /**
     * @param string $moduleName
     * @param array $menuTitles
     * @return int
     * @throws DaoException
     */
    public function enableModuleMenuItems(string $moduleName, array $menuTitles = []): int
    {
        return $this->getMenuDao()->enableModuleMenuItems($moduleName, $menuTitles);
    }

    /**
     * @param MenuItem $menuItem
     * @return array
     */
    private function _extractMenuItemToArray(MenuItem $menuItem): array
    {
        $menu['id'] = $menuItem->getId();
        $menu['menuTitle'] = $menuItem->getMenuTitle();
        $menu['level'] = $menuItem->getLevel();
        $menu['additionalParams'] = $menuItem->getAdditionalParams();
        $menu['module'] = '';
        $menu['action'] = '';
        $menu['subMenuItems'] = [];

        $screen = $menuItem->getScreen();

        if ($screen instanceof Screen) {
            $menu['module'] = $screen->getModule()->getName();
            $menu['action'] = $screen->getActionUrl();
            $menu['urlExtras'] = $menuItem->getUrlExtras();
        }

        return $menu;
    }

    /**
     * @param UserRole[]|string[] $userRoleList
     * @return MenuItem[]
     * @throws DaoException
     */
    protected function _getMenuItemListAsArray(array $userRoleList): array
    {
        $menuItemList = $this->getMenuDao()->getMenuItemList($userRoleList);
        $menuArray = [];

        foreach ($menuItemList as $menuItem) {
            $menuArray[$menuItem->getId()] = $menuItem;
        }

        return $menuArray;
    }

    /**
     * @param MenuItem $menuItem
     * @return bool
     */
    protected function _areSubMenusEmpty(MenuItem $menuItem): bool
    {
        $subMenus = $menuItem->getSubMenuItems();

        foreach ($subMenus as $subMenu) {
            if ($subMenu->getScreen() instanceof Screen) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array
     * @throws DaoException
     * @throws ServiceException
     */
    private function getAccessibleMenuItemDetails(): array
    {
        return $this->getUserRoleManager()->getAccessibleMenuItemDetails();
    }

    /**
     * @param string $baseUrl
     * @return array
     * @throws DaoException
     * @throws ServiceException
     */
    public function getMenuItems(string $baseUrl): array
    {
        $moduleScreen = $this->getCurrentModuleAndScreen();
        // TODO:: cache menu items
        $menuItemDetails = $this->getAccessibleMenuItemDetails();
        $menuItemArray = $menuItemDetails['menuItemArray'];
        $subMenuItemsArray = [];
        $sidePanelMenuItems = [];
        $selectedSidePanelMenuId = null;
        foreach ($menuItemArray as $menuItem) {
            if (!empty($menuItem['subMenuItems'])) {
                $subMenuItemsArray[$menuItem['id']] = $menuItem['subMenuItems'];
            }
            $active = $menuItem['module'] === $moduleScreen->getModule();
            // TODO:: Should fix with OHRM5X-171
            if ($moduleScreen->getScreen() == 'viewMyDetails') {
                $active = $menuItem['action'] === $moduleScreen->getScreen();
            } elseif ($moduleScreen->getModule() == 'pim') {
                $active = $menuItem['module'] === $moduleScreen->getModule() && $menuItem['action'] != 'viewMyDetails';
            }

            if ($active) {
                $selectedSidePanelMenuId = $menuItem['id'];
            }
            unset($menuItem['subMenuItems']);
            $sidePanelMenuItems[] = $this->mapMenuItem($menuItem, $baseUrl, $active);
        }

        $topMenuItemsArray = [];
        foreach ($subMenuItemsArray as $parentId => $subMenuItems) {
            $topMenuItems = [];
            foreach ($subMenuItems as $subMenuItem) {
                $active = $subMenuItem['action'] === $moduleScreen->getScreen();
                $topMenuItems[] = $this->mapMenuItem($subMenuItem, $baseUrl, $active, $moduleScreen);
            }
            $topMenuItemsArray[$parentId] = $topMenuItems;
        }

        $topMenuItems = is_null($selectedSidePanelMenuId) ? [] : ($topMenuItemsArray[$selectedSidePanelMenuId] ?? []);
        return [
            $sidePanelMenuItems,
            $topMenuItems,
        ];
    }

    /**
     * @param array $menuItem
     * @param string $baseUrl
     * @param bool $active
     * @param ModuleScreen|null $moduleScreen
     * @return array
     */
    private function mapMenuItem(
        array $menuItem,
        string $baseUrl,
        bool $active = false,
        ?ModuleScreen $moduleScreen = null
    ): array {
        $url = '#';
        if (!empty($menuItem['action']) && !empty($menuItem['module'])) {
            $url = $baseUrl . '/' . $menuItem['module'] . '/' . $menuItem['action'];
        }
        $newMenuItem = [
            'id' => $menuItem['id'],
            'name' => $menuItem['menuTitle'],
            'url' => $url,
        ];

        if (!is_null($menuItem['additionalParams']) && isset($menuItem['additionalParams']['icon'])) {
            $newMenuItem = array_merge($newMenuItem, $menuItem['additionalParams']);
        }

        if ($active) {
            $newMenuItem['active'] = true;
        }

        // if sub menu item exists
        if (isset($menuItem['subMenuItems'])) {
            $newMenuItem['children'] = [];
            foreach ($menuItem['subMenuItems'] as $subItem) {
                $active = $subItem['action'] === $moduleScreen->getScreen();
                if ($active) {
                    $newMenuItem['active'] = true;
                }
                unset($subItem['subMenuItems']);
                $newMenuItem['children'][] = $this->mapMenuItem($subItem, $baseUrl, $active, $moduleScreen);
            }
        }

        return $newMenuItem;
    }
}
