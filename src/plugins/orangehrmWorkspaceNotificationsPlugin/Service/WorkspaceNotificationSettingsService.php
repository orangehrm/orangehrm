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

namespace OrangeHRM\WorkspaceNotifications\Service;

use OrangeHRM\Core\Dao\ConfigDao;

class WorkspaceNotificationSettingsService
{
    public const KEY_WORKSPACE_ENABLED = 'workspace.notifications.enabled';

    private ?ConfigDao $configDao = null;

    public function getConfigDao(): ConfigDao
    {
        if ($this->configDao === null) {
            $this->configDao = new ConfigDao();
        }
        return $this->configDao;
    }

    public function isEnabled(): bool
    {
        return $this->getConfigDao()->getValue(self::KEY_WORKSPACE_ENABLED) === '1';
    }

    public function setEnabled(bool $enabled): void
    {
        $this->getConfigDao()->setValue(self::KEY_WORKSPACE_ENABLED, $enabled ? '1' : '0');
    }
}
