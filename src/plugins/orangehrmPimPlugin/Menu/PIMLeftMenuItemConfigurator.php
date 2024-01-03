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

namespace OrangeHRM\Pim\Menu;

use OrangeHRM\Core\Menu\MenuConfigurator;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\ControllerTrait;
use OrangeHRM\Core\Traits\ModuleScreenHelperTrait;
use OrangeHRM\Core\Traits\Service\MenuServiceTrait;
use OrangeHRM\Entity\MenuItem;
use OrangeHRM\Entity\Screen;

class PIMLeftMenuItemConfigurator implements MenuConfigurator
{
    use MenuServiceTrait;
    use ControllerTrait;
    use AuthUserTrait;
    use ModuleScreenHelperTrait;

    /**
     * @inheritDoc
     */
    public function configure(Screen $screen): ?MenuItem
    {
        if ($this->getAuthUser()->getEmpNumber() == $this->getCurrentRequest()->attributes->get('empNumber')) {
            return $this->getMenuService()->getMenuDao()->getMenuItemByTitle('My Info', 1);
        }
        $this->getCurrentModuleAndScreen()->overrideScreen('viewEmployeeList');
        return null;
    }
}
