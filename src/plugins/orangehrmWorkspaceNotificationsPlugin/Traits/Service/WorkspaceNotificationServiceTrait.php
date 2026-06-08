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

namespace OrangeHRM\WorkspaceNotifications\Traits\Service;

use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Framework\Services;
use OrangeHRM\WorkspaceNotifications\Service\WorkspaceNotificationService;

trait WorkspaceNotificationServiceTrait
{
    use ServiceContainerTrait;

    public function getWorkspaceNotificationService(): WorkspaceNotificationService
    {
        $container = $this->getContainer();
        if (!$container->has(Services::WORKSPACE_NOTIFICATION_SERVICE)) {
            $container->register(Services::WORKSPACE_NOTIFICATION_SERVICE, WorkspaceNotificationService::class);
        }
        return $container->get(Services::WORKSPACE_NOTIFICATION_SERVICE);
    }
}
