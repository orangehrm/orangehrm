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
use Orangehrm\Rest\Api\Exception\BadRequestException;
use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Api\Leave\Entity\LeaveEntitlement;

use Orangehrm\Rest\Http\Response;

class LeaveEntitlementAPI extends EndPoint
{

    const PARAMETER_LEAVE_TYPE = 'leaveType';
    const PARAMETER_ID = 'id';
    const PARAMETER_FROM_DATE = 'fromDate';
    const PARAMETER_TO_DATE = 'toDate';
    const PARAMETER_DAYS = 'days';


    private $leaveEntitlementService;
    private $employeeService;
    private $leavePeriodService;
    private $leaveTypeService;
    /**
     * @return \LeavePeriodService
     */
    public function getLeavePeriodService()
    {

        if (is_null($this->leavePeriodService)) {
            $leavePeriodService = new \LeavePeriodService();
            $leavePeriodService->setLeavePeriodDao(new \LeavePeriodDao());
            $this->leavePeriodService = $leavePeriodService;
        }

        return $this->leavePeriodService;
    }

    /**
     * @param mixed $leavePeriodService
     */
    public function setLeavePeriodService($leavePeriodService)
    {
        $this->leavePeriodService = $leavePeriodService;
    }

    /**
     * @return mixed
     */
    public function getLeaveTypeService()
    {
        if ($this->leaveTypeService == null) {
            return new \LeaveTypeService();
        } else {
            return $this->leaveTypeService;
        }
    }

    /**
     * @param mixed $leaveTypeService
     */
    public function setLeaveTypeService($leaveTypeService)
    {
        $this->leaveTypeService = $leaveTypeService;
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
     * Get EmployeeService
     * @returns \EmployeeService
     */
    public function getEmployeeService()
    {
        if (is_null($this->employeeService)) {
            $this->employeeService = new \EmployeeService();
        }
        return $this->employeeService;
    }

    /**
     * Set EmployeeService
     * @param \EmployeeService $employeeService
     */
    public function setEmployeeService(\EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    /**
     * Get leave entitlements
     *
     * @return Response
     * @throws RecordNotFoundException
     */
    public function getLeaveEntitlements()
    {
        $searchParameters = $this->getFilters();
        $results = $this->getLeaveEntitlementService()->searchLeaveEntitlements($searchParameters);
        $response = null;
        if (count($results) == 0) {
            throw new RecordNotFoundException('No Records Found');
        } else {
            foreach ($results as $entitlement) {

                $leaveEntitlement = new LeaveEntitlement($entitlement->getId());
                $leaveEntitlement->buildEntitlement($entitlement);
                $response [] = $leaveEntitlement->toArray();

            }
            return new Response($response, array());

        }

    }

    /**
     * save
     *
     * @return Response
     * @throws BadRequestException
     */
    public function saveEntitlement()
    {
        $entitlement = $this->createEntitlement();
        $leaveEntitlement = $this->getLeaveEntitlementService()->saveLeaveEntitlement($entitlement);

        if (!empty($leaveEntitlement)) {
            return new Response(array('success' => 'Successfully Saved'));
        } else {
            throw new BadRequestException('Saving Failed');
        }

    }

    /**
     * Get filters for search
     *
     * @return \LeaveEntitlementSearchParameterHolder
     * @throws InvalidParamException
     * @throws RecordNotFoundException
     */
    protected function getFilters()
    {
        $searchParameters = new \LeaveEntitlementSearchParameterHolder();
        $id = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);
        $employee = $this->getEmployeeService()->getEmployee($id);

        if (empty($employee)) {
            throw new RecordNotFoundException('Employee Not Found');
        }
        $leaveType = $this->getRequestParams()->getUrlParam(self::PARAMETER_LEAVE_TYPE);
        $fromDate = $this->getRequestParams()->getUrlParam(self::PARAMETER_FROM_DATE);
        $toDate = $this->getRequestParams()->getUrlParam(self::PARAMETER_TO_DATE);

        $this->validateLeaveType($leaveType);

        $searchParameters->setEmpNumber($id);
        $searchParameters->setLeaveTypeId($leaveType);
        $searchParameters->setFromDate($fromDate);
        $searchParameters->setToDate($toDate);

        if (!$this->validateLeavePeriods($searchParameters->getFromDate(), $searchParameters->getToDate())) {
            throw new InvalidParamException('No Leave Period Found');
        };

        return $searchParameters;

    }

    public function getValidationRules()
    {
        return array(
            self::PARAMETER_TO_DATE => array('Date' => array('Y-m-d')),
            self::PARAMETER_FROM_DATE => array('Date' => array('Y-m-d')),

        );
    }

    public function postValidationRules()
    {
        return array(
            self::PARAMETER_TO_DATE => array('Date' => array('Y-m-d')),
            self::PARAMETER_FROM_DATE => array('Date' => array('Y-m-d')),
            self::PARAMETER_DAYS => array('IntVal' => true, 'NotEmpty' => true, 'Length' => array(1, 2)),

        );
    }

    /**
     * Create entitlement
     *
     * @return \LeaveEntitlement
     * @throws InvalidParamException
     * @throws RecordNotFoundException
     */
    protected function createEntitlement()
    {
        $leaveEntitlement = new \LeaveEntitlement();
        $leaveEntitlementType = null;

        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_LEAVE_TYPE))) {
            $leaveEntitlementType = $this->getRequestParams()->getPostParam(self::PARAMETER_LEAVE_TYPE);
            $this->validateLeaveType($leaveEntitlementType);
        } else {
            throw new InvalidParamException('Leave Type Cannot be Empty');
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_FROM_DATE))) {
            $fromDate = $this->getRequestParams()->getPostParam(self::PARAMETER_FROM_DATE);
        } else {
            throw new InvalidParamException('From Date Cannot be Empty');
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_TO_DATE))) {
            $toDate = $this->getRequestParams()->getPostParam(self::PARAMETER_TO_DATE);
        } else {
            throw new InvalidParamException('To Date Cannot be Empty');
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_DAYS))) {
            $days = $this->getRequestParams()->getPostParam(self::PARAMETER_DAYS);
        } else {
            throw new InvalidParamException('Days Cannot be Empty');
        }

        $empId = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);

        $employee = $this->getEmployeeService()->getEmployee($empId);

        if (empty($employee)) {
            throw new RecordNotFoundException('Employee Not Found');
        }
        if (strtotime($fromDate) > strtotime($toDate)) {
            throw new InvalidParamException('To Date Should Be After From Date');
        }

        $leaveEntitlement->setLeaveTypeId($leaveEntitlementType);
        $leaveEntitlement->setEmpNumber($empId);
        $leaveEntitlement->setNoOfDays($days);
        $leaveEntitlement->setFromDate($fromDate);
        $leaveEntitlement->setToDate($toDate);
        $leaveEntitlement->setEntitlementType(1);

        if (!$this->validateLeavePeriods($leaveEntitlement->getFromDate(), $leaveEntitlement->getToDate())) {
            throw new InvalidParamException('No Leave Period Found');
        };

        return $leaveEntitlement;

    }

    /**
     * Validating the leave period
     * fromDate | toDate
     *
     * @param $fromDate
     * @param $toDate
     * @return bool
     * @throws RecordNotFoundException
     */
    public function validateLeavePeriods($fromDate, $toDate)
    {
        $leavePeriodList = $this->getLeavePeriodService()->getGeneratedLeavePeriodList();
        if (empty($leavePeriodList)) {
            throw new RecordNotFoundException('No Leave Periods Found');
        }
        foreach ($leavePeriodList as $period) {
            if ($period[0] === $fromDate && $period[1] === $toDate) {
                return true;
            } else {
                return false;
            }

        }
        return false;
    }

    /**
     * @param $typeId
     * @return bool
     * @throws InvalidParamException
     */
    protected function validateLeaveType($typeId)
    {
        $leaveTypeList = $this->getLeaveTypeService()->getLeaveTypeList();

        foreach ($leaveTypeList as $leaveType) {
            if ($leaveType->getId() == $typeId) {
                return true;
            }
        }
        throw new InvalidParamException('No Leave Types Available :'.$typeId);
    }

}


