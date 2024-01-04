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

use Exception;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Exception\BadRequestException;
use OrangeHRM\Core\Api\V2\Exception\ForbiddenException;
use OrangeHRM\Core\Api\V2\Exception\RecordNotFoundException;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Core\Traits\Service\NormalizerServiceTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Timesheet;
use OrangeHRM\ORM\Exception\TransactionException;
use OrangeHRM\Pim\Api\Model\EmployeeModel;
use OrangeHRM\Time\Api\Model\DetailedTimesheetModel;
use OrangeHRM\Time\Api\Model\TimesheetModel;
use OrangeHRM\Time\Api\Model\TotalDurationModel;
use OrangeHRM\Time\Api\ValidationRules\TimesheetDeletedEntriesParamRule;
use OrangeHRM\Time\Api\ValidationRules\TimesheetEntriesParamRule;
use OrangeHRM\Time\Dto\DetailedTimesheet;
use OrangeHRM\Time\Service\TimesheetService;
use OrangeHRM\Time\Traits\Service\TimesheetServiceTrait;

class EmployeeTimesheetItemAPI extends Endpoint implements CrudEndpoint
{
    use AuthUserTrait;
    use TimesheetServiceTrait;
    use UserRoleManagerTrait;
    use DateTimeHelperTrait;
    use NormalizerServiceTrait;
    use EntityManagerHelperTrait;

    public const PARAMETER_TIMESHEET_ID = 'timesheetId';
    public const PARAMETER_ENTRIES = 'entries';
    public const PARAMETER_DELETED_ENTRIES = 'deletedEntries';
    public const PARAMETER_PROJECT_ID = 'projectId';
    public const PARAMETER_ACTIVITY_ID = 'activityId';
    public const PARAMETER_DATES = 'dates';
    public const PARAMETER_DURATION = 'duration';

    public const META_PARAMETER_DATES = 'dates';
    public const META_PARAMETER_SUM = 'sum';
    public const META_PARAMETER_COLUMNS = 'columns';
    public const META_PARAMETER_TIMESHEET = 'timesheet';
    public const META_PARAMETER_EMPLOYEE = 'employee';
    public const META_PARAMETER_ALLOWED_ACTIONS = 'allowedActions';

    /**
     * @OA\Get(
     *     path="/api/v2/time/employees/timesheets/{timesheetId}/entries",
     *     tags={"Time/Employee Timesheet"},
     *     summary="Get a Timesheet's Entries",
     *     operationId="get-a-timesheets-entries",
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
     *             @OA\Property(property="meta",
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
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $timesheet = $this->getTimesheet();
        $detailedTimesheet = $this->getTimesheetService()->getDetailedTimesheet($timesheet->getId());
        return new EndpointCollectionResult(
            DetailedTimesheetModel::class,
            $detailedTimesheet,
            $this->getResultMetaForGetAll($detailedTimesheet),
        );
    }

    /**
     * @return Timesheet
     * @throws ForbiddenException
     * @throws RecordNotFoundException
     */
    protected function getTimesheet(): Timesheet
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
        return $timesheet;
    }

    /**
     * @param DetailedTimesheet $detailedTimesheet
     * @return ParameterBag
     */
    protected function getResultMetaForGetAll(DetailedTimesheet $detailedTimesheet): ParameterBag
    {
        $dates = [];
        $columns = [];
        $sum = 0;
        foreach ($detailedTimesheet->getColumns() as $column) {
            $sum += $column->getTotal();
            $date = $this->getDateTimeHelper()->formatDateTimeToYmd($column->getDate());
            $dates[] = $date;
            $columns[$date] = [
                'total' => $this->getNormalizedTotalDuration($column->getTotal()),
            ];
        }

        $allowedActions = [];
        foreach (
            $this->getTimesheetService()->getAllowedWorkflowsForTimesheet(
                $this->getAuthUser()->getEmpNumber(),
                $detailedTimesheet->getTimesheet()
            ) as $workflow
        ) {
            $action = $workflow->getAction();
            $allowedActions[] = [
                'action' => TimesheetService::TIMESHEET_ACTION_MAP[$action],
                'name' => ucwords(strtolower(TimesheetService::TIMESHEET_ACTION_MAP[$action]))
            ];
        }

        return new ParameterBag([
            self::META_PARAMETER_TIMESHEET => $this->getNormalizedTimesheet($detailedTimesheet->getTimesheet()),
            self::META_PARAMETER_SUM => $this->getNormalizedTotalDuration($sum),
            self::META_PARAMETER_COLUMNS => $columns,
            self::META_PARAMETER_DATES => $dates,
            self::META_PARAMETER_EMPLOYEE => $this->getNormalizedEmployee(
                $detailedTimesheet->getTimesheet()->getEmployee()
            ),
            self::META_PARAMETER_ALLOWED_ACTIONS => $allowedActions
        ]);
    }

    /**
     * @param Employee $employee
     * @return array
     */
    protected function getNormalizedEmployee(Employee $employee): array
    {
        return $this->getNormalizerService()->normalize(
            EmployeeModel::class,
            $employee
        );
    }

    /**
     * @param Timesheet $timesheet
     * @return array
     */
    protected function getNormalizedTimesheet(Timesheet $timesheet): array
    {
        return $this->getNormalizerService()->normalize(
            TimesheetModel::class,
            $timesheet
        );
    }

    /**
     * @param int $duration
     * @return array
     */
    protected function getNormalizedTotalDuration(int $duration): array
    {
        return $this->getNormalizerService()->normalize(
            TotalDurationModel::class,
            $duration
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getTimesheetIdParamRule(),
        );
    }

    /**
     * @return ParamRule
     */
    protected function getTimesheetIdParamRule(): ParamRule
    {
        return new ParamRule(
            self::PARAMETER_TIMESHEET_ID,
            new Rule(Rules::POSITIVE),
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

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResult
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
     * @OA\Put(
     *     path="/api/v2/time/employees/timesheets/{timesheetId}/entries",
     *     tags={"Time/Employee Timesheet"},
     *     summary="Update a Timesheet's Entries",
     *     operationId="update-a-timesheets-entries",
     *     @OA\PathParameter(
     *         name="timesheetId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="countryCode", type="string"),
     *             @OA\Property(property="province", type="string"),
     *             @OA\Property(property="city", type="string"),
     *             @OA\Property(property="address", type="string"),
     *             @OA\Property(property="zipCode", type="string"),
     *             @OA\Property(property="phone", type="string"),
     *             @OA\Property(property="fax", type="string"),
     *             @OA\Property(property="note", type="string"),
     *             required={"name", "countryCode"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Time-DetailedTimesheetModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound"),
     *     @OA\Response(
     *         response="400",
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="object",
     *                 @OA\Property(property="status", type="string", default="400"),
     *                 @OA\Property(property="message", type="string", example="Performed action not allowed")
     *             )
     *         )
     *     ),
     * )
     *
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        $this->beginTransaction();
        try {
            $timesheet = $this->getTimesheet();
            $allowedActions = $this->getTimesheetService()
                ->getAllowedWorkflowsForTimesheet($this->getAuthUser()->getEmpNumber(), $timesheet);

            if (count($allowedActions) === 1 && $allowedActions[0]->getResultingState() === 'APPROVED') {
                throw $this->getBadRequestException('Performed action not allowed');
            }

            // delete
            $toBeDeletedEntries = $this->getRequestParams()->getArray(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_DELETED_ENTRIES
            );
            $this->getTimesheetService()
                ->getTimesheetDao()
                ->deleteTimesheetRows($timesheet->getId(), $toBeDeletedEntries);

            // update & create
            $entries = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_ENTRIES);
            $this->getTimesheetService()
                ->saveAndUpdateTimesheetItemsFromRows($timesheet, $entries);

            $detailedTimesheet = $this->getTimesheetService()->getDetailedTimesheet($timesheet->getId());

            $this->commitTransaction();
            return new EndpointCollectionResult(
                DetailedTimesheetModel::class,
                $detailedTimesheet,
                $this->getResultMetaForGetAll($detailedTimesheet),
            );
        } catch (RecordNotFoundException|ForbiddenException|BadRequestException $e) {
            $this->rollBackTransaction();
            throw $e;
        } catch (Exception $e) {
            $this->rollBackTransaction();
            throw new TransactionException($e);
        }
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getTimesheetIdParamRule(),
            new ParamRule(
                self::PARAMETER_ENTRIES,
                new Rule(
                    TimesheetEntriesParamRule::class,
                    [$this->getRequest()->getAttributes()->get(self::PARAMETER_TIMESHEET_ID)]
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(self::PARAMETER_DELETED_ENTRIES, new Rule(TimesheetDeletedEntriesParamRule::class))
            ),
        );
    }
}
