<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

namespace Orangehrm\Rest\Api\PublicApi;

use ConfigService;
use ModuleService;
use Orangehrm\Rest\Api\EndPoint;
use Orangehrm\Rest\Http\Response;

class EnabledModulesAPI extends EndPoint
{
    /**
     * @var null|ModuleService
     */
    protected $moduleService = null;

    /**
     * @var null|ConfigService
     */
    protected $configService = null;

    /**
     * @return ModuleService
     */
    public function getModuleService(): ModuleService
    {
        if (is_null($this->moduleService)) {
            $this->moduleService = new ModuleService();
        }
        return $this->moduleService;
    }

    /**
     * @param ModuleService $moduleService
     */
    public function setModuleService(ModuleService $moduleService)
    {
        $this->moduleService = $moduleService;
    }

    public function getConfigService(): ConfigService
    {
        if (is_null($this->configService)) {
            $this->configService = new ConfigService();
        }
        return $this->configService;
    }

    public function setConfigService(ConfigService $configService)
    {
        $this->configService = $configService;
    }

    /**
     * @return Response
     */
    public function getEnabledModules()
    {
        $modules = [
            'admin' => true,
            'pim' => true,
            'leave' => true,
            'time' => true,
            'recruitment' => true,
            'performance' => true,
            'directory' => true,
            'maintenance' => true,
        ];

        $disabledModules = $this->getModuleService()->getDisabledModuleList();
        foreach ($disabledModules as $module) {
            $modules[$module->getName()] = false;
        }
        $modules[ModuleService::MODULE_MOBILE] = $this->getModuleService()->isMobileEnabled();

        $meta = $this->getModuleMetaData();
        return new Response(['modules' => $modules, 'meta' => $meta]);
    }

    /**
     * @return array
     */
    public function getModuleMetaData(): array
    {
        return [
            'leave' => [
                'isLeavePeriodDefined' => $this->getConfigService()->isLeavePeriodDefined(),
            ],
            'time' => [
                'isTimesheetPeriodDefined' => $this->getConfigService()->isTimesheetPeriodDefined(),
            ]
        ];
    }
}
