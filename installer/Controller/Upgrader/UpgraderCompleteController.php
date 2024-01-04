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

namespace OrangeHRM\Installer\Controller\Upgrader;

use OrangeHRM\Config\Config;
use OrangeHRM\Core\Helper\VueControllerHelper;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Installer\Controller\AbstractInstallerVueController;
use OrangeHRM\Installer\Util\Logger;
use OrangeHRM\Installer\Util\StateContainer;

class UpgraderCompleteController extends AbstractInstallerVueController
{
    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        $component = new Component('upgrader-complete-screen');
        $component->addProp(
            new Prop(VueControllerHelper::PRODUCT_VERSION, Prop::TYPE_STRING, Config::PRODUCT_VERSION)
        );
        $this->setComponent($component);
        StateContainer::getInstance()->setCurrentScreen(self::UPGRADER_COMPLETE_SCREEN, true);
        StateContainer::getInstance()->clean();

        Logger::getLogger()->info(
            'OrangeHRM ' . Config::PRODUCT_VERSION . ' status: ' . var_export(Config::isInstalled(), true)
        );
    }
}
