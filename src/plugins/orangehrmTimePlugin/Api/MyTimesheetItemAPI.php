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

use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Entity\Timesheet;

class MyTimesheetItemAPI extends EmployeeTimesheetItemAPI
{
    /**
     * @OA\Get(
     *     path="/api/v2/time/timesheets/{timesheetId}/entries",
     *     tags={"Time/My Timesheet"},
     *     summary="List My Timesheet Entries",
     *     operationId="list-my-timesheet-entries",
     *     @OA\PathParameter(
     *         name="timesheetId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Time-DetailedTimesheetModel",
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(
     *                     property="timesheet",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(
     *                         property="status",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="integer"),
     *                     ),
     *                     @OA\Property(property="startDate", type="string", format="date"),
     *                     @OA\Property(property="endDate", type="string", format="date"),
     *                 ),
     *                 @OA\Property(
     *                     property="sum",
     *                     type="object",
     *                     @OA\Property(property="hours", type="integer"),
     *                     @OA\Property(property="minutes", type="integer"),
     *                     @OA\Property(property="label", type="string"),
     *                 ),
     *                 @OA\Property(
     *                     property="columns",
     *                     type="object",
     *                     @OA\AdditionalProperties(
     *                         @OA\Property(
     *                             property="total",
     *                             type="object",
     *                             @OA\Property(property="hours", type="integer"),
     *                             @OA\Property(property="minutes", type="integer"),
     *                             @OA\Property(property="label", type="string"),
     *                         ),
     *                     ),
     *                 ),
     *                 @OA\Property(
     *                     property="dates",
     *                     type="array",
     *                     @OA\Items(type="string", format="date")
     *                 ),
     *                 @OA\Property(
     *                     property="employee",
     *                     type="object",
     *                     @OA\Property(property="empNumber", type="integer"),
     *                     @OA\Property(property="firstName", type="string"),
     *                     @OA\Property(property="lastName", type="string"),
     *                     @OA\Property(property="middleName", type="string"),
     *                     @OA\Property(property="employeeId", type="string"),
     *                     @OA\Property(property="terminationId", type="integer"),
     *                 ),
     *                 @OA\Property(
     *                     property="allowedActions",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="action", type="string"),
     *                         @OA\Property(property="name", type="string")
     *                     )
     *                 ),
     *             )
     *         )
     *     )
     * )
     *
     * @OA\Put(
     *     path="/api/v2/time/timesheets/{timesheetId}/entries",
     *     tags={"Time/My Timesheet"},
     *     summary="Update My Timesheet Entries",
     *     operationId="update-my-timesheet-entries",
     *     @OA\PathParameter(
     *         name="timesheetId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="entries",
     *                 type="object",
     *                 @OA\Property(property="projectId", type="integer"),
     *                 @OA\Property(property="activityId", type="integer"),
     *                 @OA\Property(property="dates", type="object",
     *                     @OA\AdditionalProperties(
     *                         type="object",
     *                         @OA\Property(property="duration", type="string")
     *                     )
     *                 ),
     *                 required={"projectId", "activityId"}
     *             )
     *         ),
     *         @OA\Property(
     *             property="deletedEntries",
     *             type="object",
     *             @OA\Property(property="projectId", type="integer"),
     *             @OA\Property(property="activityId", type="integer"),
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Time-DetailedTimesheetModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     *
     * @inheritDoc
     * @return Timesheet
     */
    protected function getTimesheet(): Timesheet
    {
        $timesheetId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_TIMESHEET_ID
        );
        $timesheet = $this->getTimesheetService()->getTimesheetDao()->getTimesheetById($timesheetId);
        $this->throwRecordNotFoundExceptionIfNotExist($timesheet, Timesheet::class);
        if (!$this->getUserRoleManagerHelper()->isSelfByEmpNumber($timesheet->getEmployee()->getEmpNumber())) {
            throw $this->getForbiddenException();
        }
        return $timesheet;
    }
}
