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
    public const KEY_BIRTHDAY_LEAP_YEAR_MODE = 'workspace.notifications.birthday.leap_year_mode';

    public const LEAP_YEAR_MODE_ONCE_IN_4_YEARS = 'once_every_4_years';
    public const LEAP_YEAR_MODE_FEB_28 = 'feb_28';
    public const LEAP_YEAR_MODE_MARCH_1 = 'march_1';

    public const LEAP_YEAR_MODES = [
        self::LEAP_YEAR_MODE_ONCE_IN_4_YEARS,
        self::LEAP_YEAR_MODE_FEB_28,
        self::LEAP_YEAR_MODE_MARCH_1,
    ];

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

    public function getBirthdayLeapYearMode(): string
    {
        $value = $this->getConfigDao()->getValue(self::KEY_BIRTHDAY_LEAP_YEAR_MODE);
        if ($value !== null && in_array($value, self::LEAP_YEAR_MODES, true)) {
            return $value;
        }
        return self::LEAP_YEAR_MODE_ONCE_IN_4_YEARS;
    }

    public function setBirthdayLeapYearMode(string $mode): void
    {
        $this->getConfigDao()->setValue(self::KEY_BIRTHDAY_LEAP_YEAR_MODE, $mode);
    }
}
