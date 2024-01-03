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

namespace OrangeHRM\Time\Menu;

use OrangeHRM\Core\Menu\MenuConfigurator;
use OrangeHRM\Core\Traits\ControllerTrait;
use OrangeHRM\Core\Traits\ModuleScreenHelperTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\MenuItem;
use OrangeHRM\Entity\Screen;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Time\Traits\Service\TimesheetServiceTrait;

class TimesheetMenuConfigurator implements MenuConfigurator
{
    use ModuleScreenHelperTrait;
    use ControllerTrait;
    use TimesheetServiceTrait;
    use UserRoleManagerTrait;

    /**
     * @inheritDoc
     */
    public function configure(Screen $screen): ?MenuItem
    {
        $screen = 'viewEmployeeTimesheet';
        $request = $this->getCurrentRequest();
        if ($request instanceof Request && $request->attributes->has('id')) {
            $id = $request->attributes->getInt('id');
            $timesheet = $this->getTimesheetService()->getTimesheetDao()->getTimesheetById($id);
            $self = $this->getUserRoleManagerHelper()->isSelfByEmpNumber($timesheet->getEmployee()->getEmpNumber());
            !$self ?: $screen = 'viewMyTimesheet';
        }
        $this->getCurrentModuleAndScreen()->overrideScreen($screen);
        return null;
    }
}
