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

namespace OrangeHRM\Tests\Util\Fixture;

use OrangeHRM\Entity\MenuItem;
use OrangeHRM\Entity\Module;
use OrangeHRM\Entity\Screen;
use OrangeHRM\Entity\ScreenPermission;
use OrangeHRM\Entity\UserRole;

class MenuItemFixture extends AbstractFixture
{
    /**
     * @inheritDoc
     */
    protected function getContent(): array
    {
        /** @var UserRole[] $userRoles */
        $userRoles = $this->getEntityManager()->getRepository(UserRole::class)->findAll();
        $userRoleResults = [];
        foreach ($userRoles as $userRole) {
            $result = [];
            $result['id'] = $userRole->getId();
            $result['name'] = $userRole->getName();
            $result['displayName'] = $userRole->getDisplayName();
            $result['isAssignable'] = $userRole->isAssignable();
            $result['isPredefined'] = $userRole->isPredefined();
            $userRoleResults[] = $result;
        }

        /** @var Module[] $modules */
        $modules = $this->getEntityManager()->getRepository(Module::class)->findAll();
        $moduleResults = [];
        foreach ($modules as $module) {
            $result = [];
            $result['id'] = $module->getId();
            $result['name'] = $module->getName();
            $result['status'] = $module->getStatus();
            $result['displayName'] = $module->getDisplayName();
            $moduleResults[] = $result;
        }

        /** @var Screen[] $screens */
        $screens = $this->getEntityManager()->getRepository(Screen::class)->findAll();
        $screenResults = [];
        foreach ($screens as $screen) {
            $result = [];
            $result['id'] = $screen->getId();
            $result['name'] = $screen->getName();
            $result['actionUrl'] = $screen->getActionUrl();
            $result['module_id'] = $screen->getModule()->getId();
            $result['menuConfigurator'] = $screen->getMenuConfigurator();
            $screenResults[] = $result;
        }

        /** @var ScreenPermission[] $screenPermissions */
        $screenPermissions = $this->getEntityManager()->getRepository(ScreenPermission::class)->findAll();
        $screenPermissionResults = [];
        foreach ($screenPermissions as $screenPermission) {
            $result = [];
            $result['id'] = $screenPermission->getId();
            $result['canRead'] = $screenPermission->canRead();
            $result['canCreate'] = $screenPermission->canCreate();
            $result['canUpdate'] = $screenPermission->canUpdate();
            $result['canDelete'] = $screenPermission->canDelete();
            $result['user_role_id'] = $screenPermission->getUserRole()->getId();
            $result['screen_id'] = $screenPermission->getScreen()->getId();
            $screenPermissionResults[] = $result;
        }

        /** @var MenuItem[] $menuItems */
        $menuItems = $this->getEntityManager()->getRepository(MenuItem::class)->findAll();
        $menuItemResults = [];
        foreach ($menuItems as $menuItem) {
            $result = [];
            $result['id'] = $menuItem->getId();
            $result['menuTitle'] = $menuItem->getMenuTitle();
            $result['parent_id'] = $menuItem->getParent() ? $menuItem->getParent()->getId() : null;
            $result['level'] = $menuItem->getLevel();
            $result['orderHint'] = $menuItem->getOrderHint();
            $result['status'] = $menuItem->getStatus();
            $result['screen_id'] = $menuItem->getScreen() ? $menuItem->getScreen()->getId() : null;
            $result['additionalParams'] = $menuItem->getAdditionalParams()
                ? json_encode($menuItem->getAdditionalParams())
                : null;
            $menuItemResults[] = $result;
        }

        return [
            'UserRole' => $userRoleResults,
            'Module' => $moduleResults,
            'Screen' => $screenResults,
            'ScreenPermission' => $screenPermissionResults,
            'MenuItem' => $menuItemResults
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getFileName(): string
    {
        return 'MenuItem.yaml';
    }
}
