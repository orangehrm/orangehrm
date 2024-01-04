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

namespace OrangeHRM\Core\HomePage;

use OrangeHRM\Core\Exception\CoreServiceException;
use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Entity\User;

class TimesheetPeriodDefinedHomePageEnabler implements HomePageEnablerInterface
{
    /**
     * @var ConfigService|null
     */
    protected ?ConfigService $configService = null;

    /**
     * @return ConfigService
     */
    public function getConfigService(): ConfigService
    {
        if (!$this->configService instanceof ConfigService) {
            $this->configService = new ConfigService();
        }
        return $this->configService;
    }

    /**
     * @param ConfigService $configService
     */
    public function setConfigService(ConfigService $configService): void
    {
        $this->configService = $configService;
    }

    /**
     * Returns true if timesheet period is not defined.
     * This class is used to direct the user to the define timesheet period page if timesheet period is not defined.
     *
     * @param User $user
     * @return bool
     * @throws CoreServiceException
     */
    public function isEnabled(User $user): bool
    {
        return !$this->getConfigService()->isTimesheetPeriodDefined();
    }
}
