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

use OrangeHRM\Admin\Api\Model\LocationModel;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Entity\Location;
use OrangeHRM\Entity\Module;
use OrangeHRM\Entity\OAuthClient;
use OrangeHRM\Entity\Organization;
use OrangeHRM\Core\Service\ModuleService;;
use OrangeHRM\OAuth\Service\OAuthService;;

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

    private ?ModuleService $moduleService = null;
    private ?OAuthService $oAuthService = null;

    private ?array $configurableModules = array(
                self::PARAMETER_ADMIN => false,
                self::PARAMETER_PIM => false,
                self::PARAMETER_LEAVE => false,
                self::PARAMETER_TIME => false,
                self::PARAMETER_RECRUITMENT => false,
                self::PARAMETER_PERFORMANCE => false,
                self::PARAMETER_MAINTENANCE => false,
                self::PARAMETER_MOBILE => false
            );

    public function getModuleService() {
        if (is_null($this->moduleService)) {
            $this->moduleService = new ModuleService();
        }
        return $this->moduleService;
    }

    public function setModuleService(ModuleService $moduleService) {
        $this->moduleService = $moduleService;
    }

    public function getOAuthService() {
        if (is_null($this->oAuthService)) {
            $this->oAuthService = new OAuthService();
        }
        return $this->oAuthService;
    }

    public function setOAuthService(OAuthService $oAuthService) {
        $this->oAuthService = $oAuthService;
    }

    private function getConfigurableModulesArray(): array
    {
        $configurableModules = $this->configurableModules;
        $modules = $this->getModuleService()->getModuleList();

        foreach ($modules as $module) {
            if (key_exists($module->getName(), $configurableModules)) {
                $configurableModules[$module->getName()] = $module->getStatus() ? true : false;
            }
        }

        $configurableModules[self::PARAMETER_MOBILE] = $this->getOAuthService()->getOAuthClientByClientId(OAuthService::PUBLIC_MOBILE_CLIENT_ID) instanceof OAuthClient;

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
        $modules = $this->configurableModules;
        foreach ($modules as $key => $module) {
            $modules[$key] = $this->getRequestParams()->getBoolean(RequestParams::PARAM_TYPE_BODY, $key);
        }

        $this->getModuleService()->updateModules($modules);
        if (
            $modules[self::PARAMETER_MOBILE] == false
            && $this->getOAuthService()->getOAuthClientByClientId(OAuthService::PUBLIC_MOBILE_CLIENT_ID) instanceof OAuthClient
        ) {
           $this->getOAuthService()->deleteOAuthClients(array(OAuthService::PUBLIC_MOBILE_CLIENT_ID));
        } elseif (
            $modules[self::PARAMETER_MOBILE] == true
            && !($this->getOAuthService()->getOAuthClientByClientId(OAuthService::PUBLIC_MOBILE_CLIENT_ID) instanceof OAuthClient)
        ) {
            $this->getOAuthService()->createMobileClient();
        }

        return new EndpointResourceResult(ArrayModel::class, $this->getConfigurableModulesArray());
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_ADMIN,
                    new Rule(Rules::TRUE_VAL),
                )
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_PIM,
                    new Rule(Rules::TRUE_VAL),
                )
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_LEAVE,
                    new Rule(Rules::BOOL_TYPE),
                )
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_TIME,
                    new Rule(Rules::BOOL_TYPE),
                )
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_RECRUITMENT,
                    new Rule(Rules::BOOL_TYPE),
                )
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_PERFORMANCE,
                    new Rule(Rules::BOOL_TYPE),
                )
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_MAINTENANCE,
                    new Rule(Rules::BOOL_TYPE),
                )
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_MOBILE,
                    new Rule(Rules::BOOL_TYPE),
                )
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
