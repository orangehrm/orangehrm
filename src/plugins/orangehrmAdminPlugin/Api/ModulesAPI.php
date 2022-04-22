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

namespace OrangeHRM\Admin\Api;

use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Service\MenuService;
use OrangeHRM\Core\Service\ModuleService;
use OrangeHRM\Entity\OAuthClient;
use OrangeHRM\OAuth\Service\OAuthService;

class ModulesAPI extends Endpoint implements CrudEndpoint
{
    public const PARAMETER_ADMIN = 'admin';
    public const PARAMETER_PIM = 'pim';
    public const PARAMETER_LEAVE = 'leave';
    public const PARAMETER_TIME = 'time';
    public const PARAMETER_RECRUITMENT = 'recruitment';
    public const PARAMETER_PERFORMANCE = 'performance';
    public const PARAMETER_MAINTENANCE = 'maintenance';
    public const PARAMETER_MOBILE = 'mobile';

    /**
     * @var ModuleService|null
     */
    protected ?ModuleService $moduleService = null;
    /**
     * @var OAuthService|null
     */
    protected ?OAuthService $oAuthService = null;
    /**
     * @var MenuService|null
     */
    protected ?MenuService $menuService = null;

    /**
     * @var array
     */
    protected const CONFIGURABLE_MODULES = [
        self::PARAMETER_ADMIN => false,
        self::PARAMETER_PIM => false,
        self::PARAMETER_LEAVE => false,
        self::PARAMETER_TIME => false,
        self::PARAMETER_RECRUITMENT => false,
        self::PARAMETER_PERFORMANCE => false,
        self::PARAMETER_MAINTENANCE => false,
        self::PARAMETER_MOBILE => false
    ];

    /**
     * Get Module Service
     * @return ModuleService|null
     */
    public function getModuleService(): ModuleService
    {
        if (is_null($this->moduleService)) {
            $this->moduleService = new ModuleService();
        }
        return $this->moduleService;
    }

    /**
     * Set Module Service
     * @param ModuleService $moduleService
     * @return void
     */
    public function setModuleService(ModuleService $moduleService): void
    {
        $this->moduleService = $moduleService;
    }

    /**
     * Get OAuth Service
     * @return OAuthService|null
     */
    public function getOAuthService(): OAuthService
    {
        if (is_null($this->oAuthService)) {
            $this->oAuthService = new OAuthService();
        }
        return $this->oAuthService;
    }

    /**
     * Set OAuth Service
     * @param OAuthService $oAuthService
     * @return void
     */
    public function setOAuthService(OAuthService $oAuthService): void
    {
        $this->oAuthService = $oAuthService;
    }

    /**
     * @return MenuService
     */
    public function getMenuService(): MenuService
    {
        if (is_null($this->menuService)) {
            $this->menuService = new MenuService();
        }
        return $this->menuService;
    }

    /**
     * Get Configurable Modules Array
     *
     * This function fetch the configurable modules from a predefined array and return them with
     * enabled or disabled status by comparing with the statuses fetched from database
     *
     * @return array
     */
    protected function getConfigurableModulesArray(): array
    {
        $configurableModules = self::CONFIGURABLE_MODULES;
        $modules = $this->getModuleService()->getModuleList();

        foreach ($modules as $module) {
            if (key_exists($module->getName(), $configurableModules)) {
                $configurableModules[$module->getName()] = $module->getStatus();
            }
        }
        $configurableModules[self::PARAMETER_MOBILE] = $this->getOAuthService()->getOAuthClientByClientId(
            OAuthService::PUBLIC_MOBILE_CLIENT_ID
        ) instanceof OAuthClient;
        return $configurableModules;
    }

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResourceResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointResourceResult
    {
        $parameters = $this->getConfigurableModulesArray();
        return new EndpointResourceResult(ArrayModel::class, $parameters);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection();
    }

    /**
     * @inheritDoc
     */
    public function create(): EndpointResourceResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function update(): EndpointResourceResult
    {
        $modules = self::CONFIGURABLE_MODULES;
        foreach ($modules as $key => $module) {
            $modules[$key] = $this->getRequestParams()->getBoolean(RequestParams::PARAM_TYPE_BODY, $key);
        }
        $this->getModuleService()->updateModuleStatus($modules);
        $this->updateMobileStatus($modules[self::PARAMETER_MOBILE]);
        $this->getMenuService()->invalidateCachedMenuItems();

        return new EndpointResourceResult(ArrayModel::class, $this->getConfigurableModulesArray());
    }

    /**
     * Update Mobile Enable Status
     *
     * If the request is to enable mobile and if the mobile related OAuth client is not there, this will add the
     * Mobile related OAuth client. If the request is to disable mobile and if the mobile related OAuth client is there,
     * this will delete the Mobile related OAuth client
     *
     * @param bool|null $enableMobile
     * @return void
     */
    protected function updateMobileStatus(?bool $enableMobile): void
    {
        $enableMobile ? $this->deleteMobileClient() : $this->createMobileClient();
    }

    /**
     * @return void
     */
    private function deleteMobileClient(): void
    {
        if ($this->getOAuthService()->getOAuthClientByClientId(
            OAuthService::PUBLIC_MOBILE_CLIENT_ID
        ) instanceof OAuthClient) {
            $this->getOAuthService()->deleteOAuthClients([OAuthService::PUBLIC_MOBILE_CLIENT_ID]);
        }
    }

    /**
     * @return void
     */
    private function createMobileClient(): void
    {
        if (!$this->getOAuthService()->getOAuthClientByClientId(
            OAuthService::PUBLIC_MOBILE_CLIENT_ID
        ) instanceof OAuthClient) {
            $this->getOAuthService()->createMobileClient();
        }
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_ADMIN,
                new Rule(Rules::TRUE_VAL),
            ),
            new ParamRule(
                self::PARAMETER_PIM,
                new Rule(Rules::TRUE_VAL),
            ),
            new ParamRule(
                self::PARAMETER_LEAVE,
                new Rule(Rules::BOOL_TYPE),
            ),
            new ParamRule(
                self::PARAMETER_TIME,
                new Rule(Rules::BOOL_TYPE),
            ),
            new ParamRule(
                self::PARAMETER_RECRUITMENT,
                new Rule(Rules::BOOL_TYPE),
            ),
            new ParamRule(
                self::PARAMETER_PERFORMANCE,
                new Rule(Rules::BOOL_TYPE),
            ),
            new ParamRule(
                self::PARAMETER_MAINTENANCE,
                new Rule(Rules::BOOL_TYPE),
            ),
            new ParamRule(
                self::PARAMETER_MOBILE,
                new Rule(Rules::BOOL_TYPE),
            ),
        );
    }

    /**
     * @inheritDoc
     */
    public function delete(): EndpointResourceResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }
}
