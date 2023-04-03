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
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Leave\Traits\Service\LeaveConfigServiceTrait;
use OrangeHRM\Pim\Traits\Service\EmployeeServiceTrait;
use OrangeHRM\Time\Service\TimesheetPeriodService;

class MenuItemAPI extends Endpoint implements ResourceEndpoint
{
    use UserRoleManagerTrait;
    use AuthUserTrait;
    use LeaveConfigServiceTrait;
    use EmployeeServiceTrait;

    /**
     * @var TimesheetPeriodService|null
     */
    protected ?TimesheetPeriodService $timesheetPeriodService = null;
    public const META_PARAMETER_IS_LEAVE_PERIOD_DEFINED =  'isLeavePeriodDefined';
    public const META_PARAMETER_IS_TIMESHEET_PERIOD_DEFINED = 'isTimesheetPeriodDefined';

    /**
     * @var array
     */
    private array $mobileMenuItems = [
        'leaveModule' => [
            'name' => 'Leave',
            'children' => ['Apply Leave', 'My Leave Usage', 'Leave List', 'Assign Leave'],
        ],
        'timeModule' => [
            'name' => 'Time',
            'children' => ['Punch In/Out', 'My Attendance', 'Employee Attendance'],
        ]
    ];

    /**
     * @var array
     */
    private array $mobileAdminOnlyMenuItems = [
        'leaveModule' => [
            'children' => ['Assign Leave'],
        ],
        'timeModule' => [
            'children' => ['Employee Attendance'],
        ]
    ];

    /**
     * @return array
     */
    private function getAvailableMenuItemsForESSUser(): array
    {
        $menuItems = [];
        $mobileMenuItems = $this->mobileMenuItems;

        foreach ($mobileMenuItems as $key => $value) {
            $result = array_diff(
                $value['children'],
                $this->mobileAdminOnlyMenuItems[$key]['children']
            );

            $menuItems [] = [
                'name' => $value['name'],
                'children' => $result,
            ];
        }
        return $menuItems;
    }

    /**
     * @return array
     */
    private function getAvailableMenuItems(): array
    {
        $menuItems = [];
        $mobileMenuItems = $this->mobileMenuItems;

        foreach ($mobileMenuItems as $value) {
            $menuItems [] = [
                'name' => $value['name'],
                'children' => $value['children'],
            ];
        }
        return $menuItems;
    }

    /**
     * @return bool
     */
    private function getIsTimesheetPeriodDefined(): bool
    {
        if (!$this->timesheetPeriodService instanceof TimesheetPeriodService) {
            $this->timesheetPeriodService = new TimesheetPeriodService();
        }
        return $this->timesheetPeriodService->isTimesheetPeriodDefined();
    }

    /**
     * @OA\Get(
     *     path="/api/v2/mobile/modules",
     *     tags={"Mobile/Menu Items"},
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
     *                         @OA\Items(type="string")
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
        $user = $this->getAuthUser();
        $isSupervisor = $this->getEmployeeService()->getEmployeeDao()->isSupervisor($user->getEmpNumber());
        $leavePeriodDefined = $this->getLeaveConfigService()->isLeavePeriodDefined();
        $timesheetPeriodDefined = $this->getIsTimesheetPeriodDefined();
        $this->getAvailableMenuItemsForESSUser();
        if ($user->getUserRoleName() === 'ESS' && !$isSupervisor) {
            $menuItems = $this->getAvailableMenuItemsForESSUser();
        } else {
            $menuItems = $this->getAvailableMenuItems();
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
