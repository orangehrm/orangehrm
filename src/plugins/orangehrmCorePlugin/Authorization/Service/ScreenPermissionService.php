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

namespace OrangeHRM\Core\Authorization\Service;

use OrangeHRM\Core\Authorization\Dao\ScreenDao;
use OrangeHRM\Core\Authorization\Dao\ScreenPermissionDao;
use OrangeHRM\Core\Authorization\Dto\ResourcePermission;
use OrangeHRM\Core\Dto\ModuleScreen;
use OrangeHRM\Core\Helper\ModuleScreenHelper;
use OrangeHRM\Entity\Screen;
use OrangeHRM\Entity\UserRole;

class ScreenPermissionService
{
    /**
     * @var ScreenPermissionDao|null
     */
    private ?ScreenPermissionDao $screenPermissionDao = null;

    /**
     * @var ScreenDao|null
     */
    private ?ScreenDao $screenDao = null;

    /**
     * @return ScreenDao
     */
    public function getScreenDao(): ScreenDao
    {
        if (!$this->screenDao instanceof ScreenDao) {
            $this->screenDao = new ScreenDao();
        }
        return $this->screenDao;
    }

    /**
     * @param ScreenDao $screenDao
     */
    public function setScreenDao(ScreenDao $screenDao): void
    {
        $this->screenDao = $screenDao;
    }

    /**
     * @return ScreenPermissionDao
     */
    public function getScreenPermissionDao(): ScreenPermissionDao
    {
        if (!$this->screenPermissionDao instanceof ScreenPermissionDao) {
            $this->screenPermissionDao = new ScreenPermissionDao();
        }
        return $this->screenPermissionDao;
    }

    /**
     * @param ScreenPermissionDao $screenPermissionDao
     */
    public function setScreenPermissionDao(ScreenPermissionDao $screenPermissionDao): void
    {
        $this->screenPermissionDao = $screenPermissionDao;
    }

    /**
     * Get Screen Permissions for given module, action for the given roles
     * @param string $module Module Name
     * @param string $actionUrl Action Name
     * @param string[]|UserRole[] $roles Array of Role names or Array of UserRole objects
     * @return ResourcePermission
     */
    public function getScreenPermissions(string $module, string $actionUrl, array $roles): ResourcePermission
    {
        $screenPermissions = $this->getScreenPermissionDao()->getScreenPermissions($module, $actionUrl, $roles);

        // if empty, give all permissions
        if (count($screenPermissions) == 0) {
            // If screen not defined, give all permissions, if screen is defined,
            // but don't give any permissions.
            $screen = $this->getScreenDao()->getScreen($module, $actionUrl);
            if (is_null($screen)) {
                $permission = new ResourcePermission(true, true, true, true);
            } else {
                $permission = new ResourcePermission(false, false, false, false);
            }
        } else {
            $read = false;
            $create = false;
            $update = false;
            $delete = false;

            foreach ($screenPermissions as $screenPermission) {
                if ($screenPermission->canRead()) {
                    $read = true;
                }
                if ($screenPermission->canCreate()) {
                    $create = true;
                }
                if ($screenPermission->canUpdate()) {
                    $update = true;
                }
                if ($screenPermission->canDelete()) {
                    $delete = true;
                }
            }

            $permission = new ResourcePermission($read, $create, $update, $delete);
        }

        return $permission;
    }

    /**
     * @param string $module
     * @param string $actionUrl
     * @return Screen|null
     */
    public function getScreen(string $module, string $actionUrl): ?Screen
    {
        return $this->getScreenDao()->getScreen($module, $actionUrl);
    }

    /**
     * @return ModuleScreen
     */
    private function getCurrentModuleAndScreen(): ModuleScreen
    {
        return ModuleScreenHelper::getCurrentModuleAndScreen();
    }

    /**
     * @return Screen|null
     */
    public function getCurrentScreen(): ?Screen
    {
        $currentModuleAndScreen = $this->getCurrentModuleAndScreen();
        return $this->getScreen($currentModuleAndScreen->getModule(), $currentModuleAndScreen->getScreen());
    }
}
