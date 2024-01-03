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

namespace OrangeHRM\Dashboard\Subscriber;

use OrangeHRM\Core\Event\ModuleEvent;
use OrangeHRM\Core\Event\ModuleStatusChange;
use OrangeHRM\Dashboard\Traits\Service\ModuleServiceTrait;
use OrangeHRM\Framework\Event\AbstractEventSubscriber;

class TimeModuleStatusChangeSubscriber extends AbstractEventSubscriber
{
    use ModuleServiceTrait;

    public const MODULE_TIME = 'time';

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ModuleEvent::MODULE_STATUS_CHANGE => [['onStatusChangeEvent', 0]]
        ];
    }

    /**
     * @param ModuleStatusChange $moduleStatusChange
     */
    public function onStatusChangeEvent(ModuleStatusChange $moduleStatusChange)
    {
        $previousModule = $moduleStatusChange->getPreviousModule();
        $currentModule = $moduleStatusChange->getCurrentModule();

        if ($previousModule->getName() === self::MODULE_TIME) {
            $this->getModuleService()
                ->getModuleDao()
                ->updateDataGroupPermissionForWidgetModules('dashboard_time_widget', $currentModule->getStatus());
        }
    }
}
