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

use DateRange;
use EmployeeService;
use LeavePeriodService;
use LeaveRequestService;
use Orangehrm\Rest\Api\EndPoint;
use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Api\Leave\Entity\LeaveRequest;
use Orangehrm\Rest\Api\Leave\Entity\LeaveType;
use Orangehrm\Rest\Api\User\Model\LeaveRequestModel;
use Orangehrm\Rest\Api\User\Model\LeaveTypeModel;
use Orangehrm\Rest\Http\Response;
use ParameterObject;

class MyLeaveRequestAPI extends EndPoint
{
    const PARAMETER_FROM_DATE = 'fromDate';
    const PARAMETER_TO_DATE = 'toDate';
    const PARAMETER_LIMIT = 'limit';
    const PARAMETER_PAGE = 'page';
    const PARAMETER_LEAVE_TYPE_ID = 'leaveTypeId';

    /**
     * @var null|EmployeeService
     */
    private $employeeService = null;

    /**
     * @var null|LeavePeriodService
     */
    private $leavePeriodService = null;

    /**
     * @var null|LeaveRequestService
     */
    private $leaveRequestService = null;

    /**
     * @return LeavePeriodService
     */
    public function getLeavePeriodService(): LeavePeriodService
    {
        if (is_null($this->leavePeriodService)) {
            $leavePeriodService = new LeavePeriodService();
            $leavePeriodService->setLeavePeriodDao(new \LeavePeriodDao());
            $this->leavePeriodService = $leavePeriodService;
        }
        return $this->leavePeriodService;
    }

    /**
     * @param LeavePeriodService $leavePeriodService
     */
    public function setLeavePeriodService(LeavePeriodService $leavePeriodService)
    {
        $this->leavePeriodService = $leavePeriodService;
    }

    /**
     * @returns EmployeeService
     */
    public function getEmployeeService(): EmployeeService
    {
        if (is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
        }
        return $this->employeeService;
    }

    /**
     * @param EmployeeService $employeeService
     */
    public function setEmployeeService(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

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
     * @param int $empNumber
     * @return Response
     * @throws InvalidParamException
     * @throws RecordNotFoundException
     * @throws \DaoException
     */
    public function getMyLeaveDetails(int $empNumber): Response
    {
        $filters = $this->getFilters($empNumber);
        $response = $this->getMyLeaveRequests($empNumber, $filters);
        return new Response($response, array());
    }

    /**
     * Fetch leave requests for given date period with pagination
     * @param int $empNumber
     * @param array $filters
     * @return array
     */
    public function getMyLeaveRequests(int $empNumber, array $filters)
    {
        $params = [
            'employeeFilter' => [$empNumber],
            'dateRange' => new DateRange($filters[self::PARAMETER_FROM_DATE], $filters[self::PARAMETER_TO_DATE]),
        ];
        $limit = $filters[self::PARAMETER_LIMIT];
        $page = empty($filters[self::PARAMETER_PAGE]) ? 1 : $filters[self::PARAMETER_PAGE];
        $disablePagination = false;

        if (empty($limit)) {
            $disablePagination = true;
        } else {
            $params['noOfRecordsPerPage'] = $limit;
        }

        if (!empty($filters[self::PARAMETER_LEAVE_TYPE_ID])) {
            $params['leaveTypeId'] = $filters[self::PARAMETER_LEAVE_TYPE_ID];
        }

        $searchParameters = new ParameterObject($params);
        $result = $this->getLeaveRequestService()->searchLeaveRequests(
            $searchParameters,
            $page,
            $disablePagination,
            false,
            false,
            false
        );

        if (!$disablePagination) {
            $result = $result['list'];
        }

        $leaveRequests = [];

        foreach ($result as $leaveRequest) {
            $leaveRequestEntity = $this->createLeaveRequestEntity($leaveRequest);
            $leaveRequestModel = new LeaveRequestModel($leaveRequestEntity);
            $leaveTypeModel = new LeaveTypeModel($leaveRequest->getLeaveType());
            $leaveRequests [] = array_merge(
                $leaveRequestModel->toArray(),
                [
                    'leaveType' => $leaveTypeModel->toArray(),
                ]
            );
        }
        return $leaveRequests;
    }

    /**
     * @param \LeaveRequest $leaveRequest
     * @return LeaveRequest
     */
    public function createLeaveRequestEntity(\LeaveRequest $leaveRequest): LeaveRequest
    {
        $leaveRequestEntity = new LeaveRequest($leaveRequest->getId(), $leaveRequest->getLeaveTypeName());
        $leaveRequestEntity->buildLeaveRequest($leaveRequest);
        return $leaveRequestEntity;
    }

    /**
     * Get request params with validation
     * @param int $empNumber
     * @return array
     * @throws InvalidParamException
     * @throws RecordNotFoundException
     * @throws \DaoException
     */
    public function getFilters(int $empNumber): array
    {
        $filters = [];
        $employee = $this->getEmployeeService()->getEmployee($empNumber);

        if (empty($employee)) {
            throw new RecordNotFoundException('Employee Not Found');
        }
        $fromDate = $this->getRequestParams()->getUrlParam(self::PARAMETER_FROM_DATE);
        $toDate = $this->getRequestParams()->getUrlParam(self::PARAMETER_TO_DATE);
        $filters[self::PARAMETER_LIMIT] = $this->getRequestParams()->getUrlParam(self::PARAMETER_LIMIT);
        $filters[self::PARAMETER_PAGE] = $this->getRequestParams()->getUrlParam(self::PARAMETER_PAGE);
        $filters[self::PARAMETER_LEAVE_TYPE_ID] = $this->getRequestParams()->getUrlParam(self::PARAMETER_LEAVE_TYPE_ID);

        if (empty($fromDate) && empty($toDate)) {
            $currentLeavePeriod = $this->getLeavePeriodService()->getCurrentLeavePeriodByDate(date('Y-m-d'));
            $fromDate = $currentLeavePeriod[0];
            $toDate = $currentLeavePeriod[1];
        }

        $filters[self::PARAMETER_FROM_DATE] = $fromDate;
        $filters[self::PARAMETER_TO_DATE] = $toDate;
        return $filters;
    }

    /**
     * @return array
     */
    public function getValidationRules(): array
    {
        return [
            self::PARAMETER_TO_DATE => ['Date' => ['Y-m-d']],
            self::PARAMETER_FROM_DATE => ['Date' => ['Y-m-d']],
            self::PARAMETER_LIMIT => ['Numeric' => true],
            self::PARAMETER_PAGE => ['Numeric' => true],
        ];
    }
}
