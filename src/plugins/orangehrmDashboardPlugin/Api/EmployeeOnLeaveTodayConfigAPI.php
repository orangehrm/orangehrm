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

namespace OrangeHRM\Dashboard\Api;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\ResourceEndpoint;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;

class EmployeeOnLeaveTodayConfigAPI extends Endpoint implements ResourceEndpoint
{
    use ConfigServiceTrait;

    public const PARAMETER_SHOW_ONLY_ACCESSIBLE_EMPLOYEES_ON_LEAVE_TODAY = 'showOnlyAccessibleEmployeesOnLeaveToday';

    /**
     * @OA\Get(
     *     path="/api/v2/dashboard/config/employee-on-leave-today",
     *     tags={"Dashboard/Widgets"},
     *     summary="Get Employees on Leave Today Configuration",
     *     operationId="get-employees-on-leave-today-configuration",
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="showOnlyAccessibleEmployeesOnLeaveToday", type="boolean", example="false")
     *         )
     *     )
     * )
     *
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        $showOnlyAccessibleEmployeesOnLeaveToday = $this->getConfigService()
            ->getDashboardEmployeesOnLeaveTodayShowOnlyAccessibleConfig();
        return new EndpointResourceResult(
            ArrayModel::class,
            [self::PARAMETER_SHOW_ONLY_ACCESSIBLE_EMPLOYEES_ON_LEAVE_TODAY => $showOnlyAccessibleEmployeesOnLeaveToday]
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        $paramsRules = new ParamRuleCollection();
        $paramsRules->addExcludedParamKey(CommonParams::PARAMETER_ID);
        return $paramsRules;
    }

    /**
     * @OA\Put(
     *     path="/api/v2/dashboard/config/employee-on-leave-today",
     *     tags={"Dashboard/Widgets"},
     *     summary="Configure Employees on Leave Today",
     *     operationId="configure-employees-on-leave-today",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="showOnlyAccessibleEmployeesOnLeaveToday", type="boolean"),
     *             required={"showOnlyAccessibleEmployeesOnLeaveToday"}
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="showOnlyAccessibleEmployeesOnLeaveToday", type="boolean", example="true"),
     *         )
     *     )
     * )
     *
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        $showOnlyAccessibleEmployeesOnLeaveToday = $this->getRequestParams()->getBoolean(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_SHOW_ONLY_ACCESSIBLE_EMPLOYEES_ON_LEAVE_TODAY
        );

        $this->getConfigService()->setDashboardEmployeesOnLeaveTodayShowOnlyAccessibleConfig(
            $showOnlyAccessibleEmployeesOnLeaveToday
        );

        return new EndpointResourceResult(
            ArrayModel::class,
            [self::PARAMETER_SHOW_ONLY_ACCESSIBLE_EMPLOYEES_ON_LEAVE_TODAY => $showOnlyAccessibleEmployeesOnLeaveToday]
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        $paramsRules = new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_SHOW_ONLY_ACCESSIBLE_EMPLOYEES_ON_LEAVE_TODAY,
                new Rule(Rules::BOOL_TYPE)
            )
        );
        $paramsRules->addExcludedParamKey(CommonParams::PARAMETER_ID);
        return $paramsRules;
    }

    /**
     * @inheritDoc
     */
    public function delete(): EndpointResult
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
