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

namespace OrangeHRM\Slack\Service;

use DateTime;
use OrangeHRM\Entity\SlackSetting;
use OrangeHRM\Slack\Dao\SlackSettingsDao;

class SlackSettingsService
{
    private ?SlackSettingsDao $dao = null;

    public function getDao(): SlackSettingsDao
    {
        if ($this->dao === null) {
            $this->dao = new SlackSettingsDao();
        }
        return $this->dao;
    }

    public function getSettings(): SlackSetting
    {
        return $this->getDao()->getSettings();
    }

    public function updateSettings(bool $enabled, string $timezone, string $dailySendTime): SlackSetting
    {
        $settings = $this->getSettings();
        $now = new DateTime();
        if ($settings->getCreatedAt() === null) {
            $settings->setCreatedAt($now);
        }
        $settings->setEnabled($enabled);
        $settings->setTimezone($timezone);
        $settings->setDailySendTime($dailySendTime);
        $settings->setUpdatedAt($now);
        return $this->getDao()->saveSettings($settings);
    }
}
