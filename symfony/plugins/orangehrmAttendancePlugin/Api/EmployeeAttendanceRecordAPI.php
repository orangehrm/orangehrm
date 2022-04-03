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

namespace OrangeHRM\Attendance\Api;

use DateTime;
use DateTimeZone;
use Exception;
use OrangeHRM\Attendance\Api\Model\AttendanceRecordListModel;
use OrangeHRM\Attendance\Api\Model\AttendanceRecordModel;
use OrangeHRM\Attendance\Api\ValidationRules\EmployeeDataGroupReadPermissionRule;
use OrangeHRM\Attendance\Dto\AttendanceRecordSearchFilterParams;
use OrangeHRM\Attendance\Exception\AttendanceServiceException;
use OrangeHRM\Attendance\Traits\Service\AttendanceServiceTrait;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Exception\ForbiddenException;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Core\Traits\Service\NumberHelperTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\AttendanceRecord;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\WorkflowStateMachine;

class EmployeeAttendanceRecordAPI extends Endpoint implements CrudEndpoint
{
    use AttendanceServiceTrait;
    use AuthUserTrait;
    use DateTimeHelperTrait;
    use UserRoleManagerTrait;
    use NumberHelperTrait;

    public const PARAMETER_DATE = 'date';
    public const PARAMETER_TIME = 'time';
    public const PARAMETER_TIMEZONE_OFFSET = 'timezoneOffset';
    public const PARAMETER_TIMEZONE_NAME = 'timezoneName';
    public const PARAMETER_NOTE = 'note';

    public const PARAMETER_RULE_NOTE_MAX_LENGTH = 250;

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $attendanceRecordSearchFilterParams = new AttendanceRecordSearchFilterParams();
        $this->setSortingAndPaginationParams($attendanceRecordSearchFilterParams);
        $employeeNumber = $this->getEmpNumber();
        $date = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_QUERY,
            self::PARAMETER_DATE,
        );

        $attendanceRecordSearchFilterParams->setEmployeeNumbers([$employeeNumber]);
        $attendanceRecordSearchFilterParams->setFromDate(new DateTime($date . ' ' . '00:00:00'));
        $attendanceRecordSearchFilterParams->setToDate(new DateTime($date . ' ' . '23:59:59'));

        $attendanceRecords = $this->getAttendanceService()
            ->getAttendanceDao()
            ->getAttendanceRecordList($attendanceRecordSearchFilterParams);

        $attendanceRecordCount = $this->getAttendanceService()
            ->getAttendanceDao()
            ->getAttendanceRecordListCount($attendanceRecordSearchFilterParams);

        $attendanceRecordTotalDuration = $this->getAttendanceService()
            ->getAttendanceDao()
            ->getTotalWorkingTime($attendanceRecordSearchFilterParams) === null ? 0 : $this->getAttendanceService()
            ->getAttendanceDao()
            ->getTotalWorkingTime($attendanceRecordSearchFilterParams)['total'];

        return new EndpointCollectionResult(
            AttendanceRecordListModel::class,
            [$attendanceRecords],
            new ParameterBag([
                CommonParams::PARAMETER_TOTAL => $attendanceRecordCount,
                'sum' => [
                    'hours' => floor($attendanceRecordTotalDuration / 3600),
                    'minutes' => ($attendanceRecordTotalDuration / 60) % 60,
                    'label' => $this->getNumberHelper()->numberFormat($attendanceRecordTotalDuration / 3600, 2),
                ],
            ])
        );
    }

    /**
     * @return int|null
     */
    protected function getEmpNumber(): ?int
    {
        return $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_DATE,
                    new Rule(Rules::API_DATE)
                ),
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    CommonParams::PARAMETER_EMP_NUMBER,
                    new Rule(Rules::POSITIVE),
                    new Rule(Rules::ENTITY_ID_EXISTS, [Employee::class]),
                    new Rule(
                        EmployeeDataGroupReadPermissionRule::class,
                        ['apiv2_attendance_employee_attendance_record']
                    ),
                    new Rule(Rules::IN_ACCESSIBLE_ENTITY_ID, [Employee::class])
                )
            ),
            ...$this->getSortingAndPaginationParamsRules(AttendanceRecordSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function create(): EndpointResourceResult
    {
        try {
            list($empNumber, $date, $time, $timezoneOffset, $timezoneName, $note) = $this->getCommonRequestParams();
            $allowedWorkflowItems = $this->getUserRoleManager()->getAllowedActions(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                AttendanceRecord::STATE_INITIAL,
                [],
                [],
                [Employee::class => $empNumber]
            );
            $this->userAllowedPunchInActions(array_keys($allowedWorkflowItems));
            $attendanceRecord = new AttendanceRecord();
            $attendanceRecord->getDecorator()->setEmployeeByEmpNumber($empNumber);
            $punchInDateTime = $this->extractPunchDateTime($date . ' ' . $time, $timezoneOffset);
            $punchInUTCDateTime = (clone $punchInDateTime)->setTimezone(
                new DateTimeZone(DateTimeHelperService::TIMEZONE_UTC)
            );
            $overlappingPunchInRecords = $this->getAttendanceService()
                ->getAttendanceDao()
                ->checkForPunchInOverLappingRecords($punchInUTCDateTime, $empNumber);
            if ($overlappingPunchInRecords) {
                throw AttendanceServiceException::punchInOverlapFound();
            }
            $this->setPunchInAttendanceRecord(
                $attendanceRecord,
                AttendanceRecord::STATE_PUNCHED_IN,
                $punchInUTCDateTime,
                $punchInDateTime,
                $timezoneOffset,
                $timezoneName,
                $note
            );
            $attendanceRecord = $this->getAttendanceService()->getAttendanceDao()->savePunchRecord($attendanceRecord);
            return new EndpointResourceResult(AttendanceRecordModel::class, $attendanceRecord);
        } catch (AttendanceServiceException $e) {
            throw $this->getBadRequestException($e->getMessage());
        }
    }

    /**
     * @return array
     */
    protected function getCommonRequestParams(): array
    {
        return [
            $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_ATTRIBUTE,
                CommonParams::PARAMETER_EMP_NUMBER,
                $this->getAuthUser()->getEmpNumber()
            ),
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_DATE
            ),
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_TIME
            ),
            $this->getRequestParams()->getFloat(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_TIMEZONE_OFFSET
            ),
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_TIMEZONE_NAME
            ),
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_NOTE
            )
        ];
    }

    /**
     * @param array $allowedActions
     * @return void
     * @throws ForbiddenException
     */
    protected function userAllowedPunchInActions(array $allowedActions): void
    {
        if (!in_array(
            WorkflowStateMachine::ATTENDANCE_ACTION_PROXY_PUNCH_IN,
            $allowedActions
        )) {
            throw $this->getForbiddenException();
        }
    }

    /**
     * @param string $dateTime
     * @param float $timezoneOffset
     * @return DateTime
     * @throws Exception
     */
    protected function extractPunchDateTime(string $dateTime, float $timezoneOffset): DateTime
    {
        return new DateTime(
            $dateTime,
            $this->getDateTimeHelper()->getTimezoneByTimezoneOffset($timezoneOffset)
        );
    }

    /**
     * @param AttendanceRecord $attendanceRecord
     * @param string $state
     * @param DateTime $punchInUtcTime
     * @param DateTime $punchInUserTime
     * @param float $punchInTimezoneOffset
     * @param string $punchInTimezoneName
     * @param string|null $punchInNote
     */
    protected function setPunchInAttendanceRecord(
        AttendanceRecord $attendanceRecord,
        string $state,
        DateTime $punchInUtcTime,
        DateTime $punchInUserTime,
        float $punchInTimezoneOffset,
        string $punchInTimezoneName,
        ?string $punchInNote
    ): void {
        $attendanceRecord->setState($state);
        $attendanceRecord->setPunchInUtcTime($punchInUtcTime);
        $attendanceRecord->setPunchInUserTime($punchInUserTime);
        $attendanceRecord->setPunchInTimeOffset($punchInTimezoneOffset);
        $attendanceRecord->setPunchInTimezoneName($punchInTimezoneName);
        $attendanceRecord->setPunchInNote($punchInNote);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            ...$this->getCommonValidationRules()
        );
    }

    /**
     * @return array
     */
    protected function getCommonValidationRules(): array
    {
        return [
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    CommonParams::PARAMETER_EMP_NUMBER,
                    new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
                )
            ),
            new ParamRule(
                self::PARAMETER_DATE,
                new Rule(Rules::API_DATE)
            ),
            new ParamRule(
                self::PARAMETER_TIME,
                new Rule(Rules::TIME, ['H:i'])
            ),
            new ParamRule(
                self::PARAMETER_TIMEZONE_OFFSET,
                new Rule(Rules::TIMEZONE_OFFSET)
            ),
            new ParamRule(
                self::PARAMETER_TIMEZONE_NAME,
                new Rule(Rules::TIMEZONE_NAME)
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_NOTE,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAMETER_RULE_NOTE_MAX_LENGTH])
                ),
                true
            )
        ];
    }

    /**
     * @inheritDoc
     */
    public function delete(): EndpointResult
    {
        try {
            $attendanceRecordIds = $this->getRequestParams()->getArray(
                RequestParams::PARAM_TYPE_BODY,
                CommonParams::PARAMETER_IDS
            );
            $attendanceRecordOwnedEmpNumber = $this->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_ATTRIBUTE,
                CommonParams::PARAMETER_EMP_NUMBER,
                $this->getAuthUser()->getEmpNumber()
            );
            if (!$this->isAuthUserAllowedToPerformDeleteActions($attendanceRecordOwnedEmpNumber)) {
                throw $this->getForbiddenException();
            }
            if (empty($attendanceRecordIds)) {
                throw AttendanceServiceException::deletableAttendanceRecordIdsEmpty();
            }
            $userAllowedAttendanceRecords = $this->getAttendanceService()
                ->getAttendanceDao()
                ->getAttendanceRecordsByEmpNumberAndIds($attendanceRecordOwnedEmpNumber, $attendanceRecordIds);
            $userAllowedAttendanceRecordIds = array_map(
                fn (AttendanceRecord $attendanceRecord) => $attendanceRecord->getId(),
                $userAllowedAttendanceRecords
            );
            if (count($userAllowedAttendanceRecordIds) !== count($attendanceRecordIds)) {
                throw $this->getForbiddenException();
            }
            $this->getAttendanceService()->getAttendanceDao()->deleteAttendanceRecords($attendanceRecordIds);
            return new EndpointResourceResult(ArrayModel::class, $attendanceRecordIds);
        } catch (AttendanceServiceException $e) {
            throw $this->getBadRequestException($e->getMessage());
        }
    }

    /**
     * @param int $attendanceRecordOwnedEmpNumber
     * @return bool
     */
    private function isAuthUserAllowedToPerformDeleteActions(int $attendanceRecordOwnedEmpNumber): bool
    {
        $loggedInUserEmpNumber = $this->getAuthUser()->getEmpNumber();
        $rolesToInclude = [];
        //check the configuration as ESS since Admin user is always allowed to delete self records
        if ($attendanceRecordOwnedEmpNumber === $loggedInUserEmpNumber) {
            $rolesToInclude = ['ESS'];
        }
        //If delete own attendance record, get the allowed actions list as an ESS user
        //If delete someone else's attendance record, get the allowed actions list as a Supervisor
        //Admin is always allowed to edit others records
        $allowedWorkflowItems = $this->getUserRoleManager()->getAllowedActions(
            WorkflowStateMachine::FLOW_ATTENDANCE,
            AttendanceRecord::STATE_PUNCHED_IN,
            [],
            $rolesToInclude,
            [Employee::class => $attendanceRecordOwnedEmpNumber]
        );
        //check whether work flow item allowed for the user
        if (!in_array(WorkflowStateMachine::ATTENDANCE_ACTION_DELETE, array_keys($allowedWorkflowItems))) {
            return false;
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    CommonParams::PARAMETER_EMP_NUMBER,
                    new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
                )
            ),
            new ParamRule(
                CommonParams::PARAMETER_IDS,
                new Rule(Rules::ARRAY_TYPE)
            ),
        );
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
     * @inheritDoc
     * @throws Exception
     */
    public function update(): EndpointResult
    {
        try {
            list($empNumber, $date, $time, $timezoneOffset, $timezoneName, $note) = $this->getCommonRequestParams();
            $allowedWorkflowItems = $this->getUserRoleManager()->getAllowedActions(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                AttendanceRecord::STATE_PUNCHED_IN,
                [],
                [],
                [Employee::class => $empNumber]
            );
            $this->userAllowedPunchOutActions(array_keys($allowedWorkflowItems));
            $lastPunchInRecord = $this->getAttendanceService()
                ->getAttendanceDao()
                ->getLastPunchRecordByEmployeeNumberAndActionableList($empNumber, [AttendanceRecord::STATE_PUNCHED_IN]);
            if (is_null($lastPunchInRecord)) {
                throw AttendanceServiceException::punchOutAlreadyExist();
            }
            $punchOutDateTime = $this->extractPunchDateTime($date . ' ' . $time, $timezoneOffset);
            $punchOutUTCDateTime = (clone $punchOutDateTime)->setTimezone(
                new DateTimeZone(DateTimeHelperService::TIMEZONE_UTC)
            );
            $overlappingPunchOutRecords = $this->getAttendanceService()
                ->getAttendanceDao()
                ->checkForPunchOutOverLappingRecords($punchOutUTCDateTime, $empNumber);
            if (!$overlappingPunchOutRecords) {
                throw AttendanceServiceException::punchOutOverlapFound();
            }
            $this->setPunchOutAttendanceRecord(
                $lastPunchInRecord,
                AttendanceRecord::STATE_PUNCHED_OUT,
                $punchOutUTCDateTime,
                $punchOutDateTime,
                $timezoneOffset,
                $timezoneName,
                $note
            );
            $attendanceRecord = $this->getAttendanceService()->getAttendanceDao()->savePunchRecord($lastPunchInRecord);
            return new EndpointResourceResult(AttendanceRecordModel::class, $attendanceRecord);
        } catch (AttendanceServiceException $e) {
            throw $this->getBadRequestException($e->getMessage());
        }
    }

    /**
     * @param array $allowedActions
     * @return void
     * @throws ForbiddenException
     */
    protected function userAllowedPunchOutActions(array $allowedActions): void
    {
        if (!in_array(
            WorkflowStateMachine::ATTENDANCE_ACTION_PROXY_PUNCH_OUT,
            $allowedActions
        )) {
            throw $this->getForbiddenException();
        }
    }

    /**
     * @param AttendanceRecord $attendanceRecord
     * @param string $state
     * @param DateTime $punchOutUtcTime
     * @param DateTime $punchOutUserTime
     * @param float $punchOutTimezoneOffset
     * @param string $punchOutTimezoneName
     * @param string|null $punchOutNote
     */
    protected function setPunchOutAttendanceRecord(
        AttendanceRecord $attendanceRecord,
        string $state,
        DateTime $punchOutUtcTime,
        DateTime $punchOutUserTime,
        float $punchOutTimezoneOffset,
        string $punchOutTimezoneName,
        ?string $punchOutNote
    ): void {
        $attendanceRecord->setState($state);
        $attendanceRecord->setPunchOutUtcTime($punchOutUtcTime);
        $attendanceRecord->setPunchOutUserTime($punchOutUserTime);
        $attendanceRecord->setPunchOutTimeOffset($punchOutTimezoneOffset);
        $attendanceRecord->setPunchOutTimezoneName($punchOutTimezoneName);
        $attendanceRecord->setPunchOutNote($punchOutNote);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            ...$this->getCommonValidationRules()
        );
    }
}
