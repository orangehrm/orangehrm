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

use OrangeHRM\Core\Api\V2\CollectionEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Dashboard\Dao\QuickLaunchDao;

class QuickLaunchAPI extends Endpoint implements CollectionEndpoint
{
    use UserRoleManagerTrait;

    protected const QUICK_LAUNCH_ITEMS = [
        QuickLaunchDao::ASSIGN_LEAVE => false,
        QuickLaunchDao::LEAVE_LIST => false,
        QuickLaunchDao::APPLY_LEAVE => false,
        QuickLaunchDao::MY_LEAVE => false,
        QuickLaunchDao::EMPLOYEE_TIMESHEET => false,
        QuickLaunchDao::MY_TIMESHEET => false,
    ];

    /**
     * @OA\Get(
     *     path="/api/v2/dashboard/shortcuts",
     *     tags={"Dashboard/Widgets"},
     *     summary="Get Quick Launch Shortcuts",
     *     operationId="get-quick-launch-shortcuts",
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="leave.assign_leave", type="boolean", example="true"),
     *                 @OA\Property(property="leave.leave_list", type="boolean", example="true"),
     *                 @OA\Property(property="leave.apply_leave", type="boolean", example="true"),
     *                 @OA\Property(property="leave.my_leave", type="boolean", example="true"),
     *                 @OA\Property(property="time.employee_timesheet", type="boolean", example="true"),
     *                 @OA\Property(property="time.my_timesheet", type="boolean", example="true"),
     *             ),
     *         )
     *     )
     * )
     *
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        return new EndpointResourceResult(
            ArrayModel::class,
            $this->getActiveQuickLaunchItemArray()
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection();
    }

    private function getActiveQuickLaunchItemArray(): array
    {
        $activeItems = self::QUICK_LAUNCH_ITEMS;
        $accessibleItems = $this->getUserRoleManager()->getAccessibleQuickLaunchList();

        foreach ($accessibleItems as $item) {
            $activeItems[$item] = true;
        }

        return $activeItems;
    }

    /**
     * @inheritDoc
     */
    public function create(): EndpointResult
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
