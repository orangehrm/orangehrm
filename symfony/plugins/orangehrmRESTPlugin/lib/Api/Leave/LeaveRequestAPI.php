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

namespace Orangehrm\Rest\Api\Leave;

use Orangehrm\Rest\Api\EndPoint;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Api\Leave\Entity\LeaveRequest;
use Orangehrm\Rest\Api\Exception\InvalidParamException;

use Orangehrm\Rest\Http\Response;

class LeaveRequestAPI extends EndPoint
{
    /**
     * @var \EmployeeService
     */
    private $employeeService;

    /**
     * @var \LeaveRequestService
     */
    private $leaveRequestService;

    private $leaveEntitlementService;

    private $statusList;

    private $subunit;

    /**
     * Constants
     */
    const PARAMETER_FROM_DATE = "fromDate";
    const PARAMETER_TO_DATE = "toDate";
    const PARAMETER_REJECTED = "rejected";
    const PARAMETER_CANCELLED = "cancelled";
    const PARAMETER_PENDING_APPROVAL = "pendingApproval";
    const PARAMETER_SCHEDULED = "scheduled";
    const PARAMETER_TAKEN = 'taken';
    const PARAMETER_ID = 'id';
    const PARAMETER_PAST_EMPLOYEE = 'pastEmployee';
    const PARAMETER_LEAVE_TYPE = 'type';
    CONST PARAMETER_SUBUNIT = 'subunit';
    const PARAMETER_LIMIT = 'limit';
    const PARAMETER_PAGE = 'page';


    /**
     * @return \EmployeeService
     */
    public function getEmployeeService()
    {
        if (is_null($this->employeeService)) {
            $this->employeeService = new \EmployeeService();
        }
        return $this->employeeService;
    }

    /**
     * Sets EmployeeService
     * @param \EmployeeService $service
     */
    public function setEmployeeService(\EmployeeService $service)
    {
        $this->employeeService = $service;
    }

    /**
     *
     * @return \LeaveRequestService
     */
    public function getLeaveRequestService()
    {
        if (is_null($this->leaveRequestService)) {
            $this->leaveRequestService = new \LeaveRequestService();
        }

        return $this->leaveRequestService;
    }

    /**
     * Get entitlement service
     *
     * @return \LeaveEntitlementService
     */
    public function getLeaveEntitlementService()
    {
        if (empty($this->leaveEntitlementService)) {
            $this->leaveEntitlementService = new \LeaveEntitlementService();
        }
        return $this->leaveEntitlementService;
    }

    /**
     * Set entitlement service
     *
     * @param $leaveEntitlementService
     */
    public function setLeaveEntitlementService($leaveEntitlementService)
    {
        $this->leaveEntitlementService = $leaveEntitlementService;
    }

    /**
     * @return mixed
     */
    public function getSubunit()
    {
        return $this->subunit;
    }

    /**
     * @param mixed $subunit
     */
    public function setSubunit($subunit)
    {
        $this->subunit = $subunit;
    }

    /**
     *
     * @param \LeaveRequestService $leaveRequestService
     * @return void
     */
    public function setLeaveRequestService(\LeaveRequestService $leaveRequestService)
    {
        $this->leaveRequestService = $leaveRequestService;
    }

    /**
     * search Leave requests
     *
     * @return Response
     * @throws RecordNotFoundException
     */
    public function searchRequests()
    {
        $filters = $this->filterParameters();
        $this->validateInputs($filters);

        $searchParams = $this->createParameters($filters);

        $result = $this->getLeaveRequestService()->searchLeaveRequests($searchParams, 0, false, false,
            true, true);
        $list = $result['list'];
        foreach ($list as $request) {

            $leaveRequest = new LeaveRequest($request->getId(), $request->getLeaveTypeName());
            $leaveBalance = $this->getLeaveEntitlementService()->getLeaveBalance($request->getEmpNumber(),
                $request->getLeaveTypeId(), $request->getLeaveDates()[0]);
            $leaveRequest->buildLeaveRequest($request);
            $leaveRequest->setLeaveBalance(number_format((float)$leaveBalance->balance, 2, '.', ''));
            $response [] = $leaveRequest->toArray();

        }

        if (empty($response)) {
            throw new RecordNotFoundException('No Records Found');
        }

        return new Response($response, array());
    }

    /**
     * Get leave request per employee
     *
     * @return Response
     * @throws RecordNotFoundException
     */
    public function getLeaveRequestPerEmployee()
    {
        $employee = $this->getEmployeeService()->getEmployee($this->getRequestParams()->getUrlParam(self::PARAMETER_ID));
        $filters = $this->filterParameters();

        if (!empty($employee)) {

            $searchParams = new \ParameterObject(array(
                'employeeFilter' => array($employee->getEmpNumber()),
                'noOfRecordsPerPage' => $filters[self::PARAMETER_LIMIT],
                'employeeName' => $employee->getFullName()
            ));
            $result = $result = $this->getLeaveRequestService()->searchLeaveRequests($searchParams, 0, false, false,
                true, true);
            $list = $result['list'];

            $leaveRequestList = null;

            foreach ($list as $request) {

                $leaveRequest = new LeaveRequest($request->getId(), $request->getLeaveTypeName());
                $leaveBalance = $this->getLeaveEntitlementService()->getLeaveBalance($request->getEmpNumber(),
                    $request->getLeaveTypeId(), $request->getLeaveDates()[0]);
                $leaveRequest->buildLeaveRequest($request);
                $leaveRequest->setLeaveBalance(number_format((float)$leaveBalance->balance, 2, '.', ''));
                $response [] = $leaveRequest->toArray();

            }
            if (empty($response)) {
                throw new RecordNotFoundException('No Records Found');
            }
            return new Response($response, array());
        } else {
            throw  new RecordNotFoundException('Employee Not Found');
        }


    }


    /**
     * Filters
     *
     * @return array
     */
    protected function filterParameters()
    {
        $filters[] = array();

        $filters[self::PARAMETER_ID] = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);
        $filters[self::PARAMETER_CANCELLED] = ($this->getRequestParams()->getUrlParam(self::PARAMETER_CANCELLED));
        $filters[self::PARAMETER_FROM_DATE] = ($this->getRequestParams()->getUrlParam(self::PARAMETER_FROM_DATE));
        $filters[self::PARAMETER_TO_DATE] = ($this->getRequestParams()->getUrlParam(self::PARAMETER_TO_DATE));
        $filters[self::PARAMETER_TAKEN] = ($this->getRequestParams()->getUrlParam(self::PARAMETER_TAKEN));
        $filters[self::PARAMETER_PAST_EMPLOYEE] = ($this->getRequestParams()->getUrlParam(self::PARAMETER_PAST_EMPLOYEE));
        $filters[self::PARAMETER_REJECTED] = ($this->getRequestParams()->getUrlParam(self::PARAMETER_REJECTED));
        $filters[self::PARAMETER_SUBUNIT] = ($this->getRequestParams()->getUrlParam(self::PARAMETER_SUBUNIT));
        $filters[self::PARAMETER_PENDING_APPROVAL] = ($this->getRequestParams()->getUrlParam(self::PARAMETER_PENDING_APPROVAL));
        $filters[self::PARAMETER_SCHEDULED] = ($this->getRequestParams()->getUrlParam(self::PARAMETER_SCHEDULED));
        $filters[self::PARAMETER_LIMIT] = ($this->getRequestParams()->getUrlParam(self::PARAMETER_LIMIT));
        $filters[self::PARAMETER_PAGE] = ($this->getRequestParams()->getUrlParam(self::PARAMETER_PAGE));
        $filters[self::PARAMETER_LEAVE_TYPE] = ($this->getRequestParams()->getUrlParam(self::PARAMETER_LEAVE_TYPE));

        return $filters;
    }

    /**
     * Validate inputs
     *
     * @param $filters
     * @return bool
     */
    protected function validateInputs($filters)
    {
        $valid = true;

        if (!empty($filters[self::PARAMETER_SUBUNIT]) && !$this->validateSubunit($filters)) {
            $valid = false;

        }
        if (!empty($filters[self::PARAMETER_FROM_DATE]) && !empty($filters[self::PARAMETER_TO_DATE])) {
            if ((strtotime($filters[self::PARAMETER_FROM_DATE])) > (strtotime($filters[self::PARAMETER_TO_DATE]))) {
                throw new InvalidParamException('To Date Should Be After From Date');
            }

        }

        return $valid;
    }

    /**
     * Get statuses
     *
     * @param $filter
     * @return array|null
     */
    protected function getStatusesArray($filter)
    {
        $statusIdArray = null;
        if (!empty($filter[self::PARAMETER_TAKEN]) && $filter[self::PARAMETER_TAKEN] == 'true') {
            $statusIdArray[] = \PluginLeave::LEAVE_STATUS_LEAVE_TAKEN;
        }
        if (!empty($filter[self::PARAMETER_CANCELLED]) && $filter[self::PARAMETER_CANCELLED] == 'true') {
            $statusIdArray[] = \PluginLeave::LEAVE_STATUS_LEAVE_CANCELLED;
        }
        if (!empty($filter[self::PARAMETER_PENDING_APPROVAL]) && $filter[self::PARAMETER_PENDING_APPROVAL] == 'true') {
            $statusIdArray[] = \PluginLeave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL;
        }
        if (!empty($filter[self::PARAMETER_REJECTED]) && $filter[self::PARAMETER_REJECTED] == 'true') {
            $statusIdArray[] = \PluginLeave::LEAVE_STATUS_LEAVE_REJECTED;
        }
        if (!empty($filter[self::PARAMETER_SCHEDULED]) && $filter[self::PARAMETER_SCHEDULED] == 'true') {
            $statusIdArray[] = \PluginLeave::LEAVE_STATUS_LEAVE_APPROVED;
        }

        return $statusIdArray;

    }


    /**
     * validateSubunit
     *
     * @param $filters
     * @return bool
     */
    public function validateSubunit($filters)
    {
        $companyStructureService = new \CompanyStructureService();
        $treeObject = $companyStructureService->getSubunitTreeObject();

        $tree = $treeObject->fetchTree();

        foreach ($tree as $node) {
            if ($node->getId() == $filters[self::PARAMETER_SUBUNIT]) {
                $this->subunit = $node->getId();
                return true;
            }
        }
        throw new InvalidParamException('Invalid Subunit');
    }

    /**
     * Past employee filter
     *
     * @param $pastEmp
     * @return bool
     */
    public function validatePastEmployee($pastEmp)
    {
        return $pastEmp === 'true';
    }

    public function getValidationRules()
    {
        return array(
            self::PARAMETER_TO_DATE => array('NotEmpty' => true, 'Date' => array('Y-m-d')),
            self::PARAMETER_FROM_DATE => array('NotEmpty' => true, 'Date' => array('Y-m-d')),

        );
    }

    /**
     * Create parameter object
     *
     * @param $filters
     * @return \ParameterObject
     */
    protected function createParameters($filters)
    {
        $parameters = array();
        $fromDate = $filters[self::PARAMETER_FROM_DATE];
        $employee = $this->getEmployeeService()->getEmployee($filters[self::PARAMETER_ID]);
        $toDate = $filters[self::PARAMETER_TO_DATE];
        $parameters['dateRange'] = new \DateRange($fromDate, $toDate);
        $parameters['statuses'] = $this->getStatusesArray($filters);

        if (!empty($employee)) {
            $parameters['employeeFilter'] = array($employee->getEmpNumber());
        }

        $parameters['noOfRecordsPerPage'] = $filters[self::PARAMETER_LIMIT];
        $parameters['cmbWithTerminated'] = $this->validatePastEmployee($filters[self::PARAMETER_PAST_EMPLOYEE]);
        $parameters['subUnit'] = $this->subunit;

        return new \ParameterObject($parameters);
    }

}
