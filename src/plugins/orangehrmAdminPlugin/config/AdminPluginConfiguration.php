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

use OrangeHRM\Admin\Service\CompanyStructureService;
use OrangeHRM\Admin\Service\CountryService;
use OrangeHRM\Admin\Service\LocalizationService;
use OrangeHRM\Admin\Service\PayGradeService;
use OrangeHRM\Admin\Service\UserService;
use OrangeHRM\Admin\Service\WorkShiftService;
use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\PluginConfigurationInterface;
use OrangeHRM\Framework\Services;

class AdminPluginConfiguration implements PluginConfigurationInterface
{
    use ServiceContainerTrait;

    /**
     * @inheritDoc
     */
    public function initialize(Request $request): void
    {
        $this->getContainer()->register(
            Services::COUNTRY_SERVICE,
            CountryService::class
        );
        $this->getContainer()->register(
            Services::USER_SERVICE,
            UserService::class
        );
        $this->getContainer()->register(
            Services::PAY_GRADE_SERVICE,
            PayGradeService::class
        );
        $this->getContainer()->register(
            Services::COMPANY_STRUCTURE_SERVICE,
            CompanyStructureService::class
        );
        $this->getContainer()->register(
            Services::WORK_SHIFT_SERVICE,
            WorkShiftService::class
        );
        $this->getContainer()->register(
            Services::LOCALIZATION_SERVICE,
            LocalizationService::class
        );
    }
}
