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

namespace OrangeHRM\Core\Helper;

use OrangeHRM\Core\Dto\ModuleScreen;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\RequestStack;
use OrangeHRM\Framework\ServiceContainer;
use OrangeHRM\Framework\Services;

class ModuleScreenHelper
{
    /**
     * @var ModuleScreen|null
     */
    private static ?ModuleScreen $moduleScreen = null;

    /**
     * @return ModuleScreen
     */
    public static function getCurrentModuleAndScreen(): ModuleScreen
    {
        if (!self::$moduleScreen instanceof ModuleScreen) {
            $moduleScreen = new ModuleScreen();
            $request = self::getCurrentRequest();
            if ($request) {
                $pathChunks = explode('/', $request->getPathInfo());
                if (isset($pathChunks[1])) {
                    $moduleScreen->setModule($pathChunks[1]);
                }
                if (isset($pathChunks[2])) {
                    $moduleScreen->setScreen($pathChunks[2]);
                }
            }
            self::$moduleScreen = $moduleScreen;
        }

        return self::$moduleScreen;
    }

    /**
     * @return Request|null
     */
    public static function getCurrentRequest(): ?Request
    {
        /** @var RequestStack $requestStack */
        $requestStack = ServiceContainer::getContainer()->get(Services::REQUEST_STACK);
        return $requestStack->getCurrentRequest();
    }

    /**
     * Reset current module and screen
     */
    public static function resetCurrentModuleAndScreen(): void
    {
        self::$moduleScreen = null;
    }
}
