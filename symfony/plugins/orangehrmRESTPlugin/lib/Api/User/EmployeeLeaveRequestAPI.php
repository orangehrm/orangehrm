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

namespace Orangehrm\Rest\Api\User;

use DaoException;
use EmployeeService;
use LeaveRequest;
use LeaveRequestComment;
use LeaveRequestService;
use Orangehrm\Rest\Api\EndPoint;
use Orangehrm\Rest\Api\Exception\BadRequestException;
use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Http\Response;
use ResourcePermission;
use ServiceException;
use sfContext;
use sfException;
use SystemUser;
use UserRoleManagerFactory;

class EmployeeLeaveRequestAPI extends EndPoint
{
    /**
     * @var null|LeaveRequestService
     */
    private $leaveRequestService = null;

    /**
     * @var null|EmployeeService
     */
    private $employeeService = null;

    const PARAMETER_ACTION_TYPE = 'actionType';
    const PARAMETER_LEAVE_REQUEST_ID = "id";
    const PARAMETER_COMMENT = "comment";
    const PARAMETER_STATUS = "status";

    const ACTION_TYPE_COMMENT = 'comment';
    const ACTION_TYPE_CHANGE_STATUS = 'changeStatus';

    /**
     * @return LeaveRequestService
     */
    public function getLeaveRequestService(): LeaveRequestService
    {
        if (is_null($this->leaveRequestService)) {
            $this->leaveRequestService = new LeaveRequestService();
        }
        return $this->leaveRequestService;
    }


    /**
     * @param LeaveRequestService $leaveRequestService
     */
    public function setLeaveRequestService(LeaveRequestService $leaveRequestService)
    {
        $this->leaveRequestService = $leaveRequestService;
    }

    /**
     * @return EmployeeService
     */
    public function getEmployeeService(): EmployeeService
    {
        if (is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
        }
        return $this->employeeService;
    }

    /**
     * Sets EmployeeService
     * @param EmployeeService $service
     */
    public function setEmployeeService(EmployeeService $service)
    {
        $this->employeeService = $service;
    }

    /**
     * @return Response
     * @throws BadRequestException
     * @throws InvalidParamException
     * @throws DaoException
     * @throws ServiceException
     * @throws sfException
     */
    public function saveLeaveRequestAction(): Response
    {
        $actionType = $this->getRequestParams()->getPostParam(self::PARAMETER_ACTION_TYPE);
        if ($actionType === self::ACTION_TYPE_COMMENT) {
            return $this->saveLeaveRequestComment();
        } else {
            if ($actionType === self::ACTION_TYPE_CHANGE_STATUS) {
                return $this->changeLeaveRequestStatus();
            }
        }

        throw new InvalidParamException(sprintf("Invalid `%s` Value", self::PARAMETER_ACTION_TYPE));
    }

    /**
     * @return Response
     * @throws BadRequestException
     * @throws InvalidParamException
     * @throws ServiceException
     * @throws sfException
     */
    public function changeLeaveRequestStatus(): Response
    {
        $actionPerformerUserType = SystemUser::USER_TYPE_EMPLOYEE;
        if (!empty($this->getUserAttribute("auth.isSupervisor"))) {
            $actionPerformerUserType = SystemUser::USER_TYPE_SUPERVISOR;
        } else {
            if (!empty($this->getUserAttribute("auth.isAdmin"))) {
                $actionPerformerUserType = SystemUser::USER_TYPE_ADMIN;
            }
        }

        $loggedInEmpNumber = $this->getUserAttribute("auth.empNumber");
        $params = $this->filterChangeLeaveRequestStatusParameters();
        $leaveRequest = $this->getLeaveRequest($params);

        $accessible = ($loggedInEmpNumber == $leaveRequest->getEmpNumber()) || in_array(
                $leaveRequest->getEmpNumber(),
                $this->getAccessibleEmployeeIds()
            );
        if (!$accessible) {
            throw new BadRequestException('Access Denied');
        }

        $allowedActions = $this->getLeaveRequestService()->getLeaveRequestActions($leaveRequest, $loggedInEmpNumber);
        $leaveStatus = $params[self::PARAMETER_STATUS];

        if (!in_array($leaveStatus, array_values($allowedActions))) {
            throw new BadRequestException('Action Not Allowed');
        }
        $this->getLeaveRequestService()->changeLeaveRequestStatus(
            $leaveRequest,
            $leaveStatus,
            $actionPerformerUserType,
            $loggedInEmpNumber
        );
        return new Response(['success' => 'Successfully Saved']);
    }

    /**
     * @return Response
     * @throws BadRequestException
     * @throws InvalidParamException
     * @throws DaoException
     * @throws ServiceException
     * @throws sfException
     */
    public function saveLeaveRequestComment(): Response
    {
        $loggedInUserId = $this->getUserAttribute('auth.userId');
        $loggedInEmpNumber = $this->getUserAttribute("auth.empNumber");

        $createdBy = $this->getUserAttribute('auth.firstName');
        if (!empty($loggedInEmpNumber)) {
            $employee = $this->getEmployeeService()->getEmployee($loggedInEmpNumber);
            $createdBy = $employee->getFullName();
        }

        $params = $this->filterCommentActionParameters();
        $comment = $params[self::PARAMETER_COMMENT];
        $leaveRequest = $this->getLeaveRequest($params);

        $permissions = $this->getCommentPermissions($loggedInEmpNumber == $leaveRequest->getEmpNumber());
        if ($permissions->canCreate()) {
            $leaveRequestComment = $this->getLeaveRequestService()->saveLeaveRequestComment(
                $leaveRequest->getId(),
                $comment,
                $createdBy,
                $loggedInUserId,
                $loggedInEmpNumber
            );

            if ($leaveRequestComment instanceof LeaveRequestComment) {
                return new Response(['success' => 'Successfully Saved']);
            } else {
                throw new BadRequestException("Saving Failed");
            }
        } else {
            throw new BadRequestException('Access Denied');
        }
    }

    /**
     * @param array $params
     * @return LeaveRequest
     * @throws InvalidParamException
     */
    protected function getLeaveRequest(array $params): LeaveRequest
    {
        $leaveRequestId = $params[self::PARAMETER_LEAVE_REQUEST_ID];
        $leaveRequest = $this->getLeaveRequestService()->fetchLeaveRequest($leaveRequestId);
        if ($leaveRequest instanceof LeaveRequest) {
            return $leaveRequest;
        }
        throw new InvalidParamException('Invalid Leave Request Id');
    }

    /**
     * @param string $name
     * @return string
     * @throws sfException
     */
    protected function getUserAttribute(string $name): string
    {
        return sfContext::getInstance()->getUser()->getAttribute($name);
    }

    /**
     * @return array
     * @throws InvalidParamException
     */
    protected function filterChangeLeaveRequestStatusParameters(): array
    {
        $params = [];
        $params[self::PARAMETER_LEAVE_REQUEST_ID] = $this->getRequestParams()->getUrlParam(
            self::PARAMETER_LEAVE_REQUEST_ID
        );
        $params[self::PARAMETER_STATUS] = $this->getRequestParams()->getPostParam(self::PARAMETER_STATUS);
        if (empty($params[self::PARAMETER_STATUS])) {
            throw new InvalidParamException(sprintf("Invalid `%s` Value", self::PARAMETER_STATUS));
        }
        return $params;
    }

    /**
     * @return array
     * @throws InvalidParamException
     */
    protected function filterCommentActionParameters(): array
    {
        $params = [];
        $params[self::PARAMETER_LEAVE_REQUEST_ID] = $this->getRequestParams()->getUrlParam(
            self::PARAMETER_LEAVE_REQUEST_ID
        );
        $params[self::PARAMETER_COMMENT] = $this->getRequestParams()->getPostParam(self::PARAMETER_COMMENT);
        if (empty($params[self::PARAMETER_COMMENT])) {
            throw new InvalidParamException(sprintf("Invalid `%s` Value", self::PARAMETER_COMMENT));
        }
        return $params;
    }

    /**
     * @param bool $self
     * @return ResourcePermission
     * @throws ServiceException
     */
    protected function getCommentPermissions(bool $self): ResourcePermission
    {
        return $this->getDataGroupPermissions('leave_list_comments', $self);
    }

    /**
     * @param string $dataGroups
     * @param bool $self
     * @return ResourcePermission
     * @throws ServiceException
     */
    protected function getDataGroupPermissions(string $dataGroups, bool $self = false): ResourcePermission
    {
        return UserRoleManagerFactory::getUserRoleManager()->getDataGroupPermissions($dataGroups, [], [], $self, []);
    }

    public function getValidationRules()
    {
        return [
            self::PARAMETER_ACTION_TYPE => ['NotEmpty' => true],
            self::PARAMETER_LEAVE_REQUEST_ID => ['Numeric' => true],
        ];
    }

    /**
     * Return accessible leave list employees for current request user
     * @return array
     * @throws ServiceException
     */
    protected function getAccessibleEmployeeIds(): array
    {
        $properties = ["empNumber"];
        $requiredPermissions = [\BasicUserRoleManager::PERMISSION_TYPE_ACTION => ['view_leave_list']];

        $employeeList = UserRoleManagerFactory::getUserRoleManager()->getAccessibleEntityProperties(
            'Employee',
            $properties,
            null,
            null,
            [],
            [],
            $requiredPermissions
        );

        return array_keys($employeeList);
    }
}
