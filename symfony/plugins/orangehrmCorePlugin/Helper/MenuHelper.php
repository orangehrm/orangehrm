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

namespace OrangeHRM\Core\Helper;

use OrangeHRM\Core\Authorization\Manager\UserRoleManagerFactory;
use OrangeHRM\Core\Dto\ModuleScreen;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Core\Exception\ServiceException;

class MenuHelper
{
    /**
     * @param string $baseUrl
     * @return array
     * @throws DaoException
     * @throws ServiceException
     */
    public static function getMenuItems(string $baseUrl): array
    {
        $moduleScreen = ModuleScreenHelper::getCurrentModuleAndScreen();
        // TODO:: cache menu items
        $menuItemDetails = UserRoleManagerFactory::getUserRoleManager()->getAccessibleMenuItemDetails();
        $menuItemArray = $menuItemDetails['menuItemArray'];
        $subMenuItemsArray = [];
        $sidePanelMenuItems = [];
        $selectedSidePanelMenuId = null;
        foreach ($menuItemArray as $menuItem) {
            if (!empty($menuItem['subMenuItems'])) {
                $subMenuItemsArray[$menuItem['id']] = $menuItem['subMenuItems'];
            }
            $active = $menuItem['module'] === $moduleScreen->getModule();
            if ($active) {
                $selectedSidePanelMenuId = $menuItem['id'];
            }
            unset($menuItem['subMenuItems']);
            $sidePanelMenuItems[] = self::mapMenuItem($menuItem, $baseUrl, $active);
        }

        $topMenuItemsArray = [];
        foreach ($subMenuItemsArray as $parentId => $subMenuItems) {
            $topMenuItems = [];
            foreach ($subMenuItems as $subMenuItem) {
                $active = $subMenuItem['action'] === $moduleScreen->getScreen();
                $topMenuItems[] = self::mapMenuItem($subMenuItem, $baseUrl, $active, $moduleScreen);
            }
            $topMenuItemsArray[$parentId] = $topMenuItems;
        }

        $topMenuItems = is_null($selectedSidePanelMenuId) ? [] : $topMenuItemsArray[$selectedSidePanelMenuId];
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
    private static function mapMenuItem(
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
                $newMenuItem['children'][] = self::mapMenuItem($subItem, $baseUrl, $active, $moduleScreen);
            }
        }

        return $newMenuItem;
    }
}
