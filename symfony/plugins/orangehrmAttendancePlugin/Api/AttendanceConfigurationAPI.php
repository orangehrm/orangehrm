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

use OrangeHRM\Attendance\Dto\AttendanceConfiguration;
use OrangeHRM\Attendance\Traits\Service\AttendanceServiceTrait;
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
use OrangeHRM\Core\Service\AccessFlowStateMachineService;
use OrangeHRM\Entity\AttendanceRecord;
use OrangeHRM\Entity\WorkflowStateMachine;

class AttendanceConfigurationAPI extends Endpoint implements ResourceEndpoint
{
    use AttendanceServiceTrait;

    public const PARAMETER_USER_CAN_CHANGE_THE_CURRENT_TIME = 'userCanChangeCurrentTime';
    public const PARAMETER_USER_CAN_MODIFY_ATTENDANCE = 'userCanModifyAttendance';
    public const PARAMETER_SUPERVISOR_CAN_MODIFY_ATTENDANCE = 'supervisorCanModifyAttendance';

    public const ESS_USER = "ESS USER";
    public const SUPERVISOR = "SUPERVISOR";

    /**
     * @var AccessFlowStateMachineService|null
     */
    private ?AccessFlowStateMachineService $accessFlowStateMachineService = null;

    /**
     * @return AccessFlowStateMachineService
     */
    protected function getAccessFlowStateMachineService(): AccessFlowStateMachineService
    {
        if (is_null($this->accessFlowStateMachineService)) {
            $this->accessFlowStateMachineService = new AccessFlowStateMachineService();
        }
        return $this->accessFlowStateMachineService;
    }

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        $userCanChangeCurrentTime = $this->getAttendanceService()->getUserCanChangeCurrentTimeConfiguration();
        $userCanModifyAttendance = $this->getAttendanceService()->getUserCanModifyAttendanceConfiguration();
        $supervisorCanModifyAttendance = $this->getAttendanceService()->getSupervisorCanModifyAttendanceConfiguration();

        $attendanceConfiguration = new AttendanceConfiguration();
        $attendanceConfiguration->setUserCanChangeCurrentTime($userCanChangeCurrentTime);
        $attendanceConfiguration->setUserCanModifyAttendance($userCanModifyAttendance);
        $attendanceConfiguration->setSupervisorCanModifyAttendance($supervisorCanModifyAttendance);

        return new EndpointResourceResult(AttendanceConfigurationModel::class, $attendanceConfiguration);
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
        $userCanChangeCurrentTime = $this->getRequestParams()->getBoolean(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_USER_CAN_CHANGE_THE_CURRENT_TIME,
        );
        $userCanModifyAttendance = $this->getRequestParams()->getBoolean(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_USER_CAN_MODIFY_ATTENDANCE,
        );
        $supervisorCanModifyAttendance = $this->getRequestParams()->getBoolean(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_SUPERVISOR_CAN_MODIFY_ATTENDANCE,
        );

        //Configuration - Employee can change current time when punching in/out
        if ($userCanChangeCurrentTime) {
            $isPunchInEditable = $this->getAttendanceService()->getAttendanceDao()->getSavedConfiguration(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                AttendanceRecord::STATE_INITIAL,
                self::ESS_USER,
                WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_TIME,
                AttendanceRecord::STATE_INITIAL
            );

            if (!$isPunchInEditable) {
                $this->saveConfiguration(
                    WorkflowStateMachine::FLOW_ATTENDANCE,
                    AttendanceRecord::STATE_INITIAL,
                    self::ESS_USER,
                    WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_TIME,
                    AttendanceRecord::STATE_INITIAL
                );
            }
            $isPunchOutEditable = $this->getAttendanceService()->getAttendanceDao()->getSavedConfiguration(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                AttendanceRecord::STATE_PUNCHED_IN,
                self::ESS_USER,
                WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_TIME,
                AttendanceRecord::STATE_PUNCHED_IN
            );

            if (!$isPunchOutEditable) {
                $this->saveConfiguration(
                    WorkflowStateMachine::FLOW_ATTENDANCE,
                    AttendanceRecord::STATE_PUNCHED_IN,
                    self::ESS_USER,
                    WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_TIME,
                    AttendanceRecord::STATE_PUNCHED_IN
                );
            }
        } else {
            $this->getAccessFlowStateMachineService()->deleteWorkflowStateMachineRecord(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                AttendanceRecord::STATE_INITIAL,
                self::ESS_USER,
                WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_TIME,
                AttendanceRecord::STATE_INITIAL
            );
            $this->getAccessFlowStateMachineService()->deleteWorkflowStateMachineRecord(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                AttendanceRecord::STATE_PUNCHED_IN,
                self::ESS_USER,
                WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_TIME,
                AttendanceRecord::STATE_PUNCHED_IN
            );
        }

        //Configuration - Employee can edit/delete own attendance records
        if ($userCanModifyAttendance) {
            $isPunchInRecordEditable = $this->getAttendanceService()->getAttendanceDao()->getSavedConfiguration(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                AttendanceRecord::STATE_PUNCHED_IN,
                self::ESS_USER,
                WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_IN_TIME,
                AttendanceRecord::STATE_PUNCHED_IN
            );

            if (!$isPunchInRecordEditable) {
                $this->saveConfiguration(
                    WorkflowStateMachine::FLOW_ATTENDANCE,
                    AttendanceRecord::STATE_PUNCHED_IN,
                    self::ESS_USER,
                    WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_IN_TIME,
                    AttendanceRecord::STATE_PUNCHED_IN
                );
            }
            $isPunchOutRecordEditable = $this->getAttendanceService()->getAttendanceDao()->getSavedConfiguration(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                AttendanceRecord::STATE_PUNCHED_OUT,
                self::ESS_USER,
                WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_OUT_TIME,
                AttendanceRecord::STATE_PUNCHED_OUT
            );

            if (!$isPunchOutRecordEditable) {
                $this->saveConfiguration(
                    WorkflowStateMachine::FLOW_ATTENDANCE,
                    AttendanceRecord::STATE_PUNCHED_OUT,
                    self::ESS_USER,
                    WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_OUT_TIME,
                    AttendanceRecord::STATE_PUNCHED_OUT
                );
            }

            $isPunchInTimeEditableWhenTheStateIsPunchedIn = $this->getAttendanceService()->getAttendanceDao()->getSavedConfiguration(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                AttendanceRecord::STATE_PUNCHED_OUT,
                self::ESS_USER,
                WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_IN_TIME,
                AttendanceRecord::STATE_PUNCHED_OUT
            );

            if (!$isPunchInTimeEditableWhenTheStateIsPunchedIn) {
                $this->saveConfiguration(
                    WorkflowStateMachine::FLOW_ATTENDANCE,
                    AttendanceRecord::STATE_PUNCHED_OUT,
                    self::ESS_USER,
                    WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_IN_TIME,
                    AttendanceRecord::STATE_PUNCHED_OUT
                );
            }

            $isPunchInRecordDeletable = $this->getAttendanceService()->getAttendanceDao()->getSavedConfiguration(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                AttendanceRecord::STATE_PUNCHED_IN,
                self::ESS_USER,
                WorkflowStateMachine::ATTENDANCE_ACTION_DELETE,
                AttendanceRecord::STATE_NA
            );

            if (!$isPunchInRecordDeletable) {
                $this->saveConfiguration(
                    WorkflowStateMachine::FLOW_ATTENDANCE,
                    AttendanceRecord::STATE_PUNCHED_IN,
                    self::ESS_USER,
                    WorkflowStateMachine::ATTENDANCE_ACTION_DELETE,
                    AttendanceRecord::STATE_NA
                );
            }

            $isPunchOutRecordDeletable = $this->getAttendanceService()->getAttendanceDao()->getSavedConfiguration(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                AttendanceRecord::STATE_PUNCHED_OUT,
                self::ESS_USER,
                WorkflowStateMachine::ATTENDANCE_ACTION_DELETE,
                AttendanceRecord::STATE_NA
            );

            if (!$isPunchOutRecordDeletable) {
                $this->saveConfiguration(
                    WorkflowStateMachine::FLOW_ATTENDANCE,
                    AttendanceRecord::STATE_PUNCHED_OUT,
                    self::ESS_USER,
                    WorkflowStateMachine::ATTENDANCE_ACTION_DELETE,
                    AttendanceRecord::STATE_NA
                );
            }
        } else {
            $this->getAccessFlowStateMachineService()->deleteWorkflowStateMachineRecord(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                AttendanceRecord::STATE_PUNCHED_IN,
                self::ESS_USER,
                WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_IN_TIME,
                AttendanceRecord::STATE_PUNCHED_IN
            );
            $this->getAccessFlowStateMachineService()->deleteWorkflowStateMachineRecord(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                AttendanceRecord::STATE_PUNCHED_OUT,
                self::ESS_USER,
                WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_OUT_TIME,
                AttendanceRecord::STATE_PUNCHED_OUT
            );
            $this->getAccessFlowStateMachineService()->deleteWorkflowStateMachineRecord(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                AttendanceRecord::STATE_PUNCHED_OUT,
                self::ESS_USER,
                WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_IN_TIME,
                AttendanceRecord::STATE_PUNCHED_OUT
            );
            $this->getAccessFlowStateMachineService()->deleteWorkflowStateMachineRecord(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                AttendanceRecord::STATE_PUNCHED_IN,
                self::ESS_USER,
                WorkflowStateMachine::ATTENDANCE_ACTION_DELETE,
                AttendanceRecord::STATE_NA
            );
            $this->getAccessFlowStateMachineService()->deleteWorkflowStateMachineRecord(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                AttendanceRecord::STATE_PUNCHED_OUT,
                self::ESS_USER,
                WorkflowStateMachine::ATTENDANCE_ACTION_DELETE,
                AttendanceRecord::STATE_NA
            );
        }

        //Supervisor can add/edit/delete attendance records of subordinates
        if ($supervisorCanModifyAttendance) {
            $isPunchInEditable = $this->getAttendanceService()->getAttendanceDao()->getSavedConfiguration(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                AttendanceRecord::STATE_PUNCHED_IN,
                self::SUPERVISOR,
                WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_IN_TIME,
                AttendanceRecord::STATE_PUNCHED_IN
            );
            if (!$isPunchInEditable) {
                $this->saveConfiguration(
                    WorkflowStateMachine::FLOW_ATTENDANCE,
                    AttendanceRecord::STATE_PUNCHED_IN,
                    self::SUPERVISOR,
                    WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_IN_TIME,
                    AttendanceRecord::STATE_PUNCHED_IN
                );
            }

            $isPunchInEditableInStatePunchedOut = $this->getAttendanceService()->getAttendanceDao()->getSavedConfiguration(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                AttendanceRecord::STATE_PUNCHED_OUT,
                self::SUPERVISOR,
                WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_IN_TIME,
                AttendanceRecord::STATE_PUNCHED_OUT
            );
            if (!$isPunchInEditableInStatePunchedOut) {
                $this->saveConfiguration(
                    WorkflowStateMachine::FLOW_ATTENDANCE,
                    AttendanceRecord::STATE_PUNCHED_OUT,
                    self::SUPERVISOR,
                    WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_IN_TIME,
                    AttendanceRecord::STATE_PUNCHED_OUT
                );
            }

            $isPunchOutEditable = $this->getAttendanceService()->getAttendanceDao()->getSavedConfiguration(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                AttendanceRecord::STATE_PUNCHED_OUT,
                self::SUPERVISOR,
                WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_OUT_TIME,
                AttendanceRecord::STATE_PUNCHED_OUT
            );

            if (!$isPunchOutEditable) {
                $this->saveConfiguration(
                    WorkflowStateMachine::FLOW_ATTENDANCE,
                    AttendanceRecord::STATE_PUNCHED_OUT,
                    self::SUPERVISOR,
                    WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_OUT_TIME,
                    AttendanceRecord::STATE_PUNCHED_OUT
                );
            }

            $isPunchInDeletable = $this->getAttendanceService()->getAttendanceDao()->getSavedConfiguration(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                AttendanceRecord::STATE_PUNCHED_IN,
                self::SUPERVISOR,
                WorkflowStateMachine::ATTENDANCE_ACTION_DELETE,
                AttendanceRecord::STATE_NA
            );

            if (!$isPunchInDeletable) {
                $this->saveConfiguration(
                    WorkflowStateMachine::FLOW_ATTENDANCE,
                    AttendanceRecord::STATE_PUNCHED_IN,
                    self::SUPERVISOR,
                    WorkflowStateMachine::ATTENDANCE_ACTION_DELETE,
                    AttendanceRecord::STATE_NA
                );
            }

            $isPunchOutDeletable = $this->getAttendanceService()->getAttendanceDao()->getSavedConfiguration(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                AttendanceRecord::STATE_PUNCHED_OUT,
                self::SUPERVISOR,
                WorkflowStateMachine::ATTENDANCE_ACTION_DELETE,
                AttendanceRecord::STATE_NA
            );

            if (!$isPunchOutDeletable) {
                $this->saveConfiguration(
                    WorkflowStateMachine::FLOW_ATTENDANCE,
                    AttendanceRecord::STATE_PUNCHED_OUT,
                    self::SUPERVISOR,
                    WorkflowStateMachine::ATTENDANCE_ACTION_DELETE,
                    AttendanceRecord::STATE_NA
                );
            }

            $isProxyPunchIn = $this->getAttendanceService()->getAttendanceDao()->getSavedConfiguration(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                AttendanceRecord::STATE_INITIAL,
                self::SUPERVISOR,
                WorkflowStateMachine::ATTENDANCE_ACTION_PROXY_PUNCH_IN,
                AttendanceRecord::STATE_PUNCHED_IN
            );

            if (!$isProxyPunchIn) {
                $this->saveConfiguration(
                    WorkflowStateMachine::FLOW_ATTENDANCE,
                    AttendanceRecord::STATE_INITIAL,
                    self::SUPERVISOR,
                    WorkflowStateMachine::ATTENDANCE_ACTION_PROXY_PUNCH_IN,
                    AttendanceRecord::STATE_PUNCHED_IN
                );
            }

            $isProxyPunchOut = $this->getAttendanceService()->getAttendanceDao()->getSavedConfiguration(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                AttendanceRecord::STATE_PUNCHED_IN,
                self::SUPERVISOR,
                WorkflowStateMachine::ATTENDANCE_ACTION_PROXY_PUNCH_OUT,
                AttendanceRecord::STATE_PUNCHED_OUT
            );

            if (!$isProxyPunchOut) {
                $this->saveConfiguration(
                    WorkflowStateMachine::FLOW_ATTENDANCE,
                    AttendanceRecord::STATE_PUNCHED_IN,
                    self::SUPERVISOR,
                    WorkflowStateMachine::ATTENDANCE_ACTION_PROXY_PUNCH_OUT,
                    AttendanceRecord::STATE_PUNCHED_OUT
                );
            }
        } else {
            $this->getAccessFlowStateMachineService()->deleteWorkflowStateMachineRecord(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                AttendanceRecord::STATE_PUNCHED_IN,
                self::SUPERVISOR,
                WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_IN_TIME,
                AttendanceRecord::STATE_PUNCHED_IN
            );
            $this->getAccessFlowStateMachineService()->deleteWorkflowStateMachineRecord(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                AttendanceRecord::STATE_PUNCHED_OUT,
                self::SUPERVISOR,
                WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_OUT_TIME,
                AttendanceRecord::STATE_PUNCHED_OUT
            );
            $this->getAccessFlowStateMachineService()->deleteWorkflowStateMachineRecord(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                AttendanceRecord::STATE_PUNCHED_OUT,
                self::SUPERVISOR,
                WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_IN_TIME,
                AttendanceRecord::STATE_PUNCHED_OUT
            );
            $this->getAccessFlowStateMachineService()->deleteWorkflowStateMachineRecord(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                AttendanceRecord::STATE_PUNCHED_IN,
                self::SUPERVISOR,
                WorkflowStateMachine::ATTENDANCE_ACTION_DELETE,
                AttendanceRecord::STATE_NA
            );
            $this->getAccessFlowStateMachineService()->deleteWorkflowStateMachineRecord(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                AttendanceRecord::STATE_PUNCHED_OUT,
                self::SUPERVISOR,
                WorkflowStateMachine::ATTENDANCE_ACTION_DELETE,
                AttendanceRecord::STATE_NA
            );

            $this->getAccessFlowStateMachineService()->deleteWorkflowStateMachineRecord(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                AttendanceRecord::STATE_INITIAL,
                self::SUPERVISOR,
                WorkflowStateMachine::ATTENDANCE_ACTION_PROXY_PUNCH_IN,
                AttendanceRecord::STATE_PUNCHED_IN
            );
            $this->getAccessFlowStateMachineService()->deleteWorkflowStateMachineRecord(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                AttendanceRecord::STATE_PUNCHED_IN,
                self::SUPERVISOR,
                WorkflowStateMachine::ATTENDANCE_ACTION_PROXY_PUNCH_OUT,
                AttendanceRecord::STATE_PUNCHED_OUT
            );
        }
        $attendanceConfiguration = new AttendanceConfiguration();
        $attendanceConfiguration->setUserCanChangeCurrentTime($userCanChangeCurrentTime);
        $attendanceConfiguration->setUserCanModifyAttendance($userCanModifyAttendance);
        $attendanceConfiguration->setSupervisorCanModifyAttendance($supervisorCanModifyAttendance);

        return new EndpointResourceResult(AttendanceConfigurationModel::class, $attendanceConfiguration);
    }

    /**
     * @param  string  $flow
     * @param  string  $state
     * @param  string  $role
     * @param  string  $action
     * @param  string  $resultingState
     * @return void
     */
    private function saveConfiguration(
        string $flow,
        string $state,
        string $role,
        string $action,
        string $resultingState
    ) {
        $workflowStateMachineRecord = new WorkflowStateMachine();
        $workflowStateMachineRecord->setWorkflow($flow);
        $workflowStateMachineRecord->setState($state);
        $workflowStateMachineRecord->setRole($role);
        $workflowStateMachineRecord->setAction($action);
        $workflowStateMachineRecord->setResultingState($resultingState);
        $this->getAccessFlowStateMachineService()->saveWorkflowStateMachineRecord($workflowStateMachineRecord);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        $paramRules = new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_USER_CAN_CHANGE_THE_CURRENT_TIME,
                new Rule(Rules::BOOL_TYPE)
            ),
            new ParamRule(
                self::PARAMETER_USER_CAN_MODIFY_ATTENDANCE,
                new Rule(Rules::BOOL_TYPE)
            ),
            new ParamRule(
                self::PARAMETER_SUPERVISOR_CAN_MODIFY_ATTENDANCE,
                new Rule(Rules::BOOL_TYPE)
            )
        );
        $paramRules->addExcludedParamKey(CommonParams::PARAMETER_ID);
        return $paramRules;
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
