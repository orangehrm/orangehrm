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
use OrangeHRM\Core\Service\ModuleService;
use OrangeHRM\Core\Traits\Service\MenuServiceTrait;
use OrangeHRM\OAuth\Traits\OAuthServiceTrait;

class ModulesAPI extends Endpoint implements CrudEndpoint
{
    use MenuServiceTrait;
    use OAuthServiceTrait;

    public const PARAMETER_ADMIN = 'admin';
    public const PARAMETER_PIM = 'pim';
    public const PARAMETER_LEAVE = 'leave';
    public const PARAMETER_TIME = 'time';
    public const PARAMETER_RECRUITMENT = 'recruitment';
    public const PARAMETER_PERFORMANCE = 'performance';
    public const PARAMETER_MAINTENANCE = 'maintenance';
    public const PARAMETER_MOBILE = 'mobile';
    public const PARAMETER_DIRECTORY = 'directory';
    public const PARAMETER_CLAIM = 'claim';
    public const PARAMETER_BUZZ = 'buzz';

    /**
     * @var ModuleService|null
     */
    protected ?ModuleService $moduleService = null;

    /**
     * @var array
     */
    protected const CONFIGURABLE_MODULES = [
        self::PARAMETER_ADMIN => true,
        self::PARAMETER_PIM => true,
        self::PARAMETER_LEAVE => false,
        self::PARAMETER_TIME => false,
        self::PARAMETER_RECRUITMENT => false,
        self::PARAMETER_PERFORMANCE => false,
        self::PARAMETER_MAINTENANCE => false,
        self::PARAMETER_MOBILE => false,
        self::PARAMETER_DIRECTORY => false,
        self::PARAMETER_CLAIM => false,
        self::PARAMETER_BUZZ => false,
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
     * @OA\Get(
     *     path="/api/v2/admin/modules",
     *     tags={"Admin/Modules"},
     *     summary="List All Modules",
     *     operationId="list-all-modules",
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 description="Module name and the boolean value to denote if it is enabled",
     *                 @OA\Items(
     *                     @OA\Property(property="admin", type="boolean"),
     *                     @OA\Property(property="pim", type="boolean"),
     *                     @OA\Property(property="leave", type="boolean"),
     *                     @OA\Property(property="time", type="boolean"),
     *                     @OA\Property(property="recruitment", type="boolean"),
     *                     @OA\Property(property="performance", type="boolean"),
     *                     @OA\Property(property="maintenance", type="boolean"),
     *                     @OA\Property(property="mobile", type="boolean"),
     *                     @OA\Property(property="directory", type="boolean"),
     *                     @OA\Property(property="claim", type="boolean"),
     *                 ),
     *                 example="admin: true, pim: true, leave: false, time: true,...",
     *             )
     *         )
     *     )
     * )
     *
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

        $configurableModules[self::PARAMETER_MOBILE] = $this->getOAuthService()->getMobileClientStatus();

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
     * @OA\Put(
     *     path="/api/v2/admin/modules",
     *     tags={"Admin/Modules"},
     *     summary="Update a Module",
     *     operationId="update-a-module",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             description="Module name and the boolean value to denote if it is enabled",
     *             @OA\Property(property="admin", type="boolean"),
     *             @OA\Property(property="pim", type="boolean"),
     *             @OA\Property(property="leave", type="boolean"),
     *             @OA\Property(property="time", type="boolean"),
     *             @OA\Property(property="recruitment", type="boolean"),
     *             @OA\Property(property="performance", type="boolean"),
     *             @OA\Property(property="maintenance", type="boolean"),
     *             @OA\Property(property="mobile", type="boolean"),
     *             @OA\Property(property="directory", type="boolean"),
     *             @OA\Property(property="claim", type="boolean"),
     *             @OA\Property(property="buzz", type="boolean"),
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 description="Module name and the boolean value to denote if it is enabled",
     *                 @OA\Items(
     *                     @OA\Property(property="module", type="boolean"),
     *                 ),
     *                 example="admin: true, pim: true, leave: true, time: true,...",
     *             )
     *         )
     *     ),
     * )
     *
     * @inheritDoc
     */
    public function update(): EndpointResourceResult
    {
        $modules = self::CONFIGURABLE_MODULES;
        foreach (self::CONFIGURABLE_MODULES as $key => $module) {
            $modules[$key] = $this->getRequestParams()
                    ->getBoolean(RequestParams::PARAM_TYPE_BODY, $key, self::CONFIGURABLE_MODULES[$key]);
        }
        $this->getModuleService()->updateModuleStatus($modules);
        $this->getMenuService()->invalidateCachedMenuItems();

        $this->getOAuthService()->updateMobileClientStatus($modules[self::PARAMETER_MOBILE]);

        return new EndpointResourceResult(ArrayModel::class, $this->getConfigurableModulesArray());
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_ADMIN,
                    new Rule(Rules::TRUE_VAL),
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_PIM,
                    new Rule(Rules::TRUE_VAL),
                )
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
            new ParamRule(
                self::PARAMETER_DIRECTORY,
                new Rule(Rules::BOOL_TYPE),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_CLAIM,
                    new Rule(Rules::BOOL_TYPE),
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_BUZZ,
                    new Rule(Rules::BOOL_TYPE),
                )
            )
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
