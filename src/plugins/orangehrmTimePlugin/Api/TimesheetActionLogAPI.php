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
use OrangeHRM\Core\Api\V2\CollectionEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\Timesheet;
use OrangeHRM\Time\Api\Model\TimesheetActionLogModel;
use OrangeHRM\Time\Dto\TimesheetActionLogSearchFilterParams;
use OrangeHRM\Time\Traits\Service\TimesheetServiceTrait;

class TimesheetActionLogAPI extends Endpoint implements CollectionEndpoint
{
    use TimesheetServiceTrait;
    use UserRoleManagerTrait;

    public const PARAMETER_TIMESHEET_ID = 'timesheetId';

    /**
     * @OA\Get(
     *     path="/api/v2/time/timesheets/{timesheetId}/action-logs",
     *     tags={"Time/Timesheet Action Log"},
     *     summary="Get a Timesheet's Action Logs",
     *     operationId="get-a-timesheets-action-logs",
     *     @OA\PathParameter(
     *         name="timesheetId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="sortField",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=TimesheetActionLogSearchFilterParams::ALLOWED_SORT_FIELDS)
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/sortOrder"),
     *     @OA\Parameter(ref="#/components/parameters/limit"),
     *     @OA\Parameter(ref="#/components/parameters/offset"),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Time-TimesheetActionLogModel"
     *             ),
     *             @OA\Property(property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     )
     * )
     *
     * @inheritDoc
     */
    public function getAll(): EndpointCollectionResult
    {
        $timesheetId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_TIMESHEET_ID
        );
        $timesheet = $this->getTimesheetService()->getTimesheetDao()->getTimesheetById($timesheetId);
        $this->throwRecordNotFoundExceptionIfNotExist($timesheet, Timesheet::class);
        if (!$this->getUserRoleManagerHelper()->isEmployeeAccessible($timesheet->getEmployee()->getEmpNumber())) {
            throw $this->getForbiddenException();
        }
        $timesheetActionLogParamHolder = new TimesheetActionLogSearchFilterParams();
        $this->setSortingAndPaginationParams($timesheetActionLogParamHolder);

        $timesheetActionLogs = $this->getTimesheetService()->getTimesheetDao()->getTimesheetActionLogs(
            $timesheetId,
            $timesheetActionLogParamHolder
        );
        $count = $this->getTimesheetService()->getTimesheetDao()->getTimesheetActionLogsCount(
            $timesheetId,
            $timesheetActionLogParamHolder
        );
        return new EndpointCollectionResult(
            TimesheetActionLogModel::class,
            $timesheetActionLogs,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_TIMESHEET_ID,
                new Rule(Rules::POSITIVE)
            ),
            ...$this->getSortingAndPaginationParamsRules(TimesheetActionLogSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
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
