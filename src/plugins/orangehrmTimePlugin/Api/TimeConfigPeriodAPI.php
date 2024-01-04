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

namespace OrangeHRM\Time\Api;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\ResourceEndpoint;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Service\MenuService;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\Framework\Services;
use OrangeHRM\Time\Api\Model\TimeConfigPeriodModel;
use OrangeHRM\Time\Dto\TimeConfigPeriod;
use OrangeHRM\Time\Service\TimesheetPeriodService;

class TimeConfigPeriodAPI extends Endpoint implements ResourceEndpoint
{
    use ConfigServiceTrait;

    public const PARAMETER_START_DAY = 'startDay';

    /**
     * @var TimesheetPeriodService|null
     */
    protected ?TimesheetPeriodService $timesheetPeriodService = null;

    /**
     * @return TimesheetPeriodService
     */
    protected function getTimesheetPeriodService(): TimesheetPeriodService
    {
        if (!$this->timesheetPeriodService instanceof TimesheetPeriodService) {
            $this->timesheetPeriodService = new TimesheetPeriodService();
        }
        return $this->timesheetPeriodService;
    }

    /**
     * @OA\Get(
     *     path="/api/v2/time/time-sheet-period",
     *     tags={"Time/Timesheet Period"},
     *     summary="Get the Timesheet Period",
     *     operationId="get-the-timesheet-period",
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Time-TimeConfigPeriodModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         ),
     *     )
     * )
     *
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        $status = $this->getTimesheetPeriodService()->isTimesheetPeriodDefined();
        $startDay = $status ? $this->getTimesheetPeriodService()->getTimesheetStartDate() : '1'; // to set monday as default
        $timeConfigPeriod = new TimeConfigPeriod();
        $timeConfigPeriod->setStartDay($startDay);
        return new EndpointResourceResult(TimeConfigPeriodModel::class, $timeConfigPeriod);
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
     * @OA\Put(
     *     path="/api/v2/time/time-sheet-period",
     *     tags={"Time/Timesheet Period"},
     *     summary="Update the Timesheet Period",
     *     operationId="update-the-timesheet-period",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="startDay", type="integer", minimum=1, maximum=7),
     *             required={"startDay"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Time-TimeConfigPeriodModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Bad Request - Already defined",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="object",
     *                 @OA\Property(property="status", type="string", default="400"),
     *                 @OA\Property(property="message", type="string", default="Already defined")
     *             )
     *         )
     *     ),
     * )
     *
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        $startDay = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_START_DAY);
        $this->getTimesheetPeriodService()->setTimesheetPeriod($startDay);
        //After configuring the start day, we need to enable the menu items for time module
        /** @var MenuService $menuService */
        $menuService = $this->getContainer()->get(Services::MENU_SERVICE);
        $menuService->enableModuleMenuItems('time');
        $menuService->enableModuleMenuItems('attendance');
        $timeConfigPeriod = new TimeConfigPeriod();
        $timeConfigPeriod->setStartDay($startDay);
        return new EndpointResourceResult(TimeConfigPeriodModel::class, $timeConfigPeriod);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        $status = $this->getTimesheetPeriodService()->isTimesheetPeriodDefined();
        if ($status) {
            throw $this->getBadRequestException('Already defined');
        }
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_START_DAY,
                    new Rule(Rules::BETWEEN, [1, 7])
                ),
            ),
        );
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
