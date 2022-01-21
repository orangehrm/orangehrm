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

use OrangeHRM\Attendance\Traits\Service\AttendanceServiceTrait;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\ResourceEndpoint;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Entity\AttendanceRecord;
use OrangeHRM\Entity\WorkflowStateMachine;

class AttendanceConfigurationAPI extends Endpoint implements ResourceEndpoint
{
    use AttendanceServiceTrait;

    public const PARAMETER_USER_CAN_CHANGE_THE_CURRENT_TIME = 'userCanChangeCurrentTime';
    public const PARAMETER_USER_CAN_MODIFY_ATTENDANCE = 'userCanModifyAttendance';
    public const PARAMETER_SUPERVISOR_CAN_MODIFY_ATTENDANCE = 'supervisorCanModifyAttendance';

    public const ADMIN_USER = "ADMIN";
    public const ESS_USER = "ESS USER";
    public const SUPERVISOR = "SUPERVISOR";

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
     */
    public function update(): EndpointResult
    {
        $userCanChangeCurrentTime = $this->getRequestParams()->getBoolean(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_USER_CAN_CHANGE_THE_CURRENT_TIME,
            false
        );
        $userCanModifyAttendance = $this->getRequestParams()->getBoolean(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_USER_CAN_MODIFY_ATTENDANCE,
            false
        );
        $supervisorCanModifyAttendance = $this->getRequestParams()->getBoolean(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_SUPERVISOR_CAN_MODIFY_ATTENDANCE,
            false
        );

        if(self::PARAMETER_USER_CAN_CHANGE_THE_CURRENT_TIME){
            $isPunchInEditable = $this->getAttendanceService()->getSavedConfiguration(WorkflowStateMachine::FLOW_ATTENDANCE, AttendanceRecord::STATE_INITIAL, configureAction::ESS_USER, WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_TIME, AttendanceRecord::STATE_INITIAL);

            if (!$isPunchInEditable) {
                $this->saveConfigurartion(WorkflowStateMachine::FLOW_ATTENDANCE, AttendanceRecord::STATE_INITIAL, configureAction::ESS_USER, WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_TIME, AttendanceRecord::STATE_INITIAL);
            }
            $isPunchOutEditable = $this->getAttendanceService()->getSavedConfiguration(WorkflowStateMachine::FLOW_ATTENDANCE, AttendanceRecord::STATE_PUNCHED_IN, configureAction::ESS_USER, WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_TIME, AttendanceRecord::STATE_PUNCHED_IN);

            if (!$isPunchOutEditable) {
                $this->saveConfigurartion(WorkflowStateMachine::FLOW_ATTENDANCE, AttendanceRecord::STATE_PUNCHED_IN, configureAction::ESS_USER, WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_TIME, AttendanceRecord::STATE_PUNCHED_IN);
            }
        }
        if(self::PARAMETER_USER_CAN_MODIFY_ATTENDANCE){

        }
        if(self::PARAMETER_SUPERVISOR_CAN_MODIFY_ATTENDANCE){

        }

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
