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

namespace Orangehrm\Rest\Api\Mobile;

use EmployeeService;
use LeaveEntitlementService;
use LeavePeriodService;
use Orangehrm\Rest\Api\EndPoint;
use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Api\Leave\Entity\LeaveEntitlement;
use Orangehrm\Rest\Api\Leave\Entity\LeaveType;
use Orangehrm\Rest\Api\Leave\LeaveEntitlementAPI;
use Orangehrm\Rest\Http\Response;

class MyLeaveRequestAPI extends EndPoint
{
    const PARAMETER_LEAVE_TYPE = 'leaveType';
    const PARAMETER_FROM_DATE = 'fromDate';
    const PARAMETER_TO_DATE = 'toDate';

    /**
     * @var null|LeaveEntitlementService
     */
    private $leaveEntitlementService = null;

    /**
     * @var null|EmployeeService
     */
    private $employeeService = null;

    /**
     * @var null|LeavePeriodService
     */
    private $leavePeriodService = null;

    /**
     * @var null|LeaveEntitlementAPI
     */
    private $leaveEntitlementApi = null;

    /**
     * @return LeavePeriodService
     */
    public function getLeavePeriodService()
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
    public function setLeavePeriodService($leavePeriodService)
    {
        $this->leavePeriodService = $leavePeriodService;
    }

    /**
     * @return LeaveEntitlementService
     */
    public function getLeaveEntitlementService()
    {
        if (empty($this->leaveEntitlementService)) {
            $this->leaveEntitlementService = new LeaveEntitlementService();
        }
        return $this->leaveEntitlementService;
    }

    /**
     * @param LeaveEntitlementService $leaveEntitlementService
     */
    public function setLeaveEntitlementService($leaveEntitlementService)
    {
        $this->leaveEntitlementService = $leaveEntitlementService;
    }

    /**
     * @return LeaveEntitlementAPI
     */
    public function getLeaveEntitlementApi()
    {
        if (empty($this->leaveEntitlementApi)) {
            $this->leaveEntitlementApi = new LeaveEntitlementAPI($this->getRequest());
        }
        return $this->leaveEntitlementApi;
    }

    /**
     * @param LeaveEntitlementAPI $leaveEntitlementApi
     */
    public function setLeaveEntitlementApi($leaveEntitlementApi)
    {
        $this->leaveEntitlementApi = $leaveEntitlementApi;
    }

    /**
     * @returns EmployeeService
     */
    public function getEmployeeService()
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
     * @param $employeeId
     * @return Response
     * @throws InvalidParamException
     * @throws RecordNotFoundException
     * @throws \DaoException
     */
    public function getMyLeaveDetails($employeeId)
    {
        $searchParameters = $this->getFilters($employeeId);
        $results = $this->getLeaveEntitlementService()->searchLeaveEntitlements($searchParameters);
        $response = null;
        $responseEntitlement = null;
        if (count($results) == 0) {
            throw new RecordNotFoundException('No Records Found');
        } else {
            foreach ($results as $entitlement) {
                $leaveEntitlement = new LeaveEntitlement($entitlement->getId());
                $leaveEntitlement->buildEntitlement($entitlement);
                $leaveBalance = (array)$this->getLeaveEntitlementService()->getLeaveBalance($employeeId, $entitlement->getLeaveTypeId());
                $leaveType = new LeaveType($entitlement->getLeaveTypeId(), $entitlement->getLeaveType()->getName());
                array_walk($leaveBalance, function (&$value, $key) {
                    $value = (float)$value;
                });
                $responseEntitlement[] = array_merge(
                    $leaveEntitlement->toArray(),
                    array(
                        'leaveBalance' => $leaveBalance,
                        'leaveType' => $leaveType->toArray(),
                    )
                );
            }
            $response['entitlement'] = $responseEntitlement;
            return new Response($response, array());
        }
    }

    /**
     * @param $employeeId
     * @return \LeaveEntitlementSearchParameterHolder
     * @throws InvalidParamException
     * @throws RecordNotFoundException
     * @throws \DaoException
     */
    protected function getFilters($employeeId)
    {
        $searchParameters = new \LeaveEntitlementSearchParameterHolder();
        $employee = $this->getEmployeeService()->getEmployee($employeeId);

        if (empty($employee)) {
            throw new RecordNotFoundException('Employee Not Found');
        }
        $leaveType = $this->getRequestParams()->getUrlParam(self::PARAMETER_LEAVE_TYPE);
        $fromDate = $this->getRequestParams()->getUrlParam(self::PARAMETER_FROM_DATE);
        $toDate = $this->getRequestParams()->getUrlParam(self::PARAMETER_TO_DATE);

        if (!is_null($leaveType)) {
            $this->getLeaveEntitlementApi()->validateLeaveType($leaveType);
        }

        if (empty($fromDate) && empty($toDate)) {
            $currentLeavePeriod = $this->getLeavePeriodService()->getCurrentLeavePeriodByDate(date('Y-m-d'));
            $fromDate = $currentLeavePeriod[0];
            $toDate = $currentLeavePeriod[1];
        }

        $searchParameters->setEmpNumber($employeeId);
        $searchParameters->setLeaveTypeId($leaveType);
        $searchParameters->setFromDate($fromDate);
        $searchParameters->setToDate($toDate);

        if (!$this->getLeaveEntitlementApi()->validateLeavePeriods($searchParameters->getFromDate(), $searchParameters->getToDate())) {
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
}
