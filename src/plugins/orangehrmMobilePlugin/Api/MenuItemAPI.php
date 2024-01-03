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

namespace OrangeHRM\Mobile\Api;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\ResourceEndpoint;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Dashboard\Traits\Service\ModuleServiceTrait;
use OrangeHRM\Leave\Traits\Service\LeaveConfigServiceTrait;
use OrangeHRM\Pim\Traits\Service\EmployeeServiceTrait;

class MenuItemAPI extends Endpoint implements ResourceEndpoint
{
    use UserRoleManagerTrait;
    use AuthUserTrait;
    use LeaveConfigServiceTrait;
    use EmployeeServiceTrait;
    use ConfigServiceTrait;
    use ModuleServiceTrait;

    public const META_PARAMETER_IS_LEAVE_PERIOD_DEFINED =  'isLeavePeriodDefined';
    public const META_PARAMETER_IS_TIMESHEET_PERIOD_DEFINED = 'isTimesheetPeriodDefined';

    private bool $timeModuleEnabled;
    private bool $leaveModuleEnabled;


    /**
     * @var array
     */
    private array $timeModuleModuleMenuItems = [
        [
            'name' => 'Time',
            'children' => [
                ["name" => "Punch In/Out"],
                ["name" => "My Attendance"],
                ["name" => "Employee Attendance"]
            ],
        ]
    ];

    /**
     * @var array
     */
    private array $leaveModuleMenuItems = [
        [
            'name' => 'Leave',
            'children' => [
                ["name" => "Apply Leave"],
                ["name" => "My Leave Usage"],
                ["name" => "Leave List"],
                ["name" => "Assign Leave"]
            ],
        ]
    ];

    /**
     * @var array
     */
    private array $timeModuleModuleMenuItemsForEss = [
        [
            'name' => 'Time',
            'children' => [
                ["name" => "Punch In/Out"],
                ["name" => "My Attendance"],
            ],
        ]
    ];

    /**
     * @var array
     */
    private array $leaveModuleMenuItemsForEss = [
        [
            'name' => 'Leave',
            'children' => [
                ["name" => "Apply Leave"],
                ["name" => "My Leave Usage"],
            ],
        ]
    ];

    /**
     * @return array
     */
    private function getAvailableMenuItemsForESSUser(): array
    {
        $menuItems = [];
        if ($this->leaveModuleEnabled) {
            $menuItems = array_merge($menuItems, $this->leaveModuleMenuItemsForEss);
        }

        if ($this->timeModuleEnabled) {
            $menuItems = array_merge($menuItems, $this->timeModuleModuleMenuItemsForEss);
        }
        return $menuItems;
    }

    /**
     * @OA\Get(
     *     path="/api/v2/mobile/menus",
     *     tags={"Mobile/Menu Items"},
     *     summary="List Mobile Menu Items",
     *     operationId="list-mobile-menu-items",
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="children", type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="name", type="string"),
     *                         ),
     *                         example="name: Apply Leave"
     *                     ),
     *                 )
     *             ),
     *             @OA\Property(property="meta",
     *                 type="object",
     *                 @OA\Property(property="isLeavePeriodDefined", type="boolean"),
     *                 @OA\Property(property="isTimesheetPeriodDefined", type="boolean")
     *             )
     *         )
     *     )
     * )
     *
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        $menuItems = [];
        $user = $this->getAuthUser();
        $isSupervisor = $this->getEmployeeService()->getEmployeeDao()->isSupervisor($user->getEmpNumber());
        $leavePeriodDefined = $this->getLeaveConfigService()->isLeavePeriodDefined();
        $timesheetPeriodDefined = $this->getConfigService()->isTimesheetPeriodDefined();

        $modules = $this->getModuleService()->getModuleList();
        array_filter($modules, function ($module) {
            if ($module->getName() == 'leave') {
                $this->leaveModuleEnabled = $module->getStatus();
            } elseif ($module->getName() == 'time') {
                $this->timeModuleEnabled = $module->getStatus();
            }
        });

        if ($user->getUserRoleName() === 'ESS' && !$isSupervisor) {
            $menuItems = $this->getAvailableMenuItemsForESSUser();
        } else {
            if ($this->leaveModuleEnabled) {
                $menuItems = array_merge($menuItems, $this->leaveModuleMenuItems);
            }

            if ($this->timeModuleEnabled) {
                $menuItems = array_merge($menuItems, $this->timeModuleModuleMenuItems);
            }
        }

        return new EndpointResourceResult(
            ArrayModel::class,
            $menuItems,
            new ParameterBag([
                self::META_PARAMETER_IS_LEAVE_PERIOD_DEFINED => $leavePeriodDefined,
                self::META_PARAMETER_IS_TIMESHEET_PERIOD_DEFINED => $timesheetPeriodDefined
            ])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        $paramRules = new ParamRuleCollection();
        $paramRules->addExcludedParamKey(CommonParams::PARAMETER_ID);
        return $paramRules;
    }

    /**
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
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
