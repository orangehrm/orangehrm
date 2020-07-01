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
use Orangehrm\Rest\Api\Leave\Entity\LeaveBalance;
use Orangehrm\Rest\Api\Mobile\Model\LeaveEntitlementModel;
use Orangehrm\Rest\Http\Response;

class MyLeaveEntitlementAPI extends EndPoint
{
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
     * @return LeaveEntitlementService
     */
    public function getLeaveEntitlementService(): LeaveEntitlementService
    {
        if (empty($this->leaveEntitlementService)) {
            $this->leaveEntitlementService = new LeaveEntitlementService();
        }
        return $this->leaveEntitlementService;
    }

    /**
     * @param LeaveEntitlementService $leaveEntitlementService
     */
    public function setLeaveEntitlementService(LeaveEntitlementService $leaveEntitlementService)
    {
        $this->leaveEntitlementService = $leaveEntitlementService;
    }

    /**
     * @return LeaveEntitlementAPI
     */
    public function getLeaveEntitlementApi(): LeaveEntitlementAPI
    {
        if (empty($this->leaveEntitlementApi)) {
            $this->leaveEntitlementApi = new LeaveEntitlementAPI($this->getRequest());
        }
        return $this->leaveEntitlementApi;
    }

    /**
     * @param LeaveEntitlementAPI $leaveEntitlementApi
     */
    public function setLeaveEntitlementApi(LeaveEntitlementAPI $leaveEntitlementApi)
    {
        $this->leaveEntitlementApi = $leaveEntitlementApi;
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
     * @param int $employeeId
     * @return Response
     * @throws InvalidParamException
     * @throws RecordNotFoundException
     * @throws \DaoException
     */
    public function getMyLeaveDetails(int $employeeId): Response
    {
        $filters = $this->getFilters($employeeId);
        $response = $this->getMyLeaveEntitlement($employeeId, $filters);
        return new Response($response, array());
    }

    /**
     * Fetch leave entitlements for given leave period
     * @param int $employeeId
     * @param array $filters
     * @return array
     * @throws RecordNotFoundException
     */
    public function getMyLeaveEntitlement(int $employeeId, array $filters)
    {
        $searchParameters = $this->getEntitlementSearchParams($employeeId, $filters);
        $results = $this->getLeaveEntitlementService()->searchLeaveEntitlements($searchParameters);
        $responseEntitlement = [];
        if (count($results) == 0) {
            throw new RecordNotFoundException('No Records Found');
        } else {
            foreach ($results as $entitlement) {
                $leaveEntitlementEntity = new LeaveEntitlement($entitlement->getId());
                $leaveEntitlementEntity->buildEntitlement($entitlement);
                $leaveEntitlementModel = new LeaveEntitlementModel($leaveEntitlementEntity);
                $leaveBalance = $this->getLeaveEntitlementService()->getLeaveBalance($employeeId, $entitlement->getLeaveTypeId());
                $leaveBalanceEntity = new LeaveBalance($leaveBalance);
                $leaveType = new LeaveType($entitlement->getLeaveTypeId(), $entitlement->getLeaveType()->getName());
                $responseEntitlement[] = array_merge(
                    $leaveEntitlementModel->toArray(),
                    array(
                        'leaveBalance' => $leaveBalanceEntity->toArray(),
                        'leaveType' => $leaveType->toArray(),
                    )
                );
            }
            return $responseEntitlement;
        }
    }

    /**
     * Get request params with validation
     * @param int $employeeId
     * @return array
     * @throws InvalidParamException
     * @throws RecordNotFoundException
     * @throws \DaoException
     */
    public function getFilters(int $employeeId): array
    {
        $filters = [];
        $employee = $this->getEmployeeService()->getEmployee($employeeId);

        if (empty($employee)) {
            throw new RecordNotFoundException('Employee Not Found');
        }
        $fromDate = $this->getRequestParams()->getUrlParam(self::PARAMETER_FROM_DATE);
        $toDate = $this->getRequestParams()->getUrlParam(self::PARAMETER_TO_DATE);

        if (empty($fromDate) && empty($toDate)) {
            $currentLeavePeriod = $this->getLeavePeriodService()->getCurrentLeavePeriodByDate(date('Y-m-d'));
            $fromDate = $currentLeavePeriod[0];
            $toDate = $currentLeavePeriod[1];
        } else {
            if (!$this->getLeaveEntitlementApi()->validateLeavePeriods($fromDate, $toDate)) {
                throw new InvalidParamException('No Leave Period Found');
            };
        }

        $filters[self::PARAMETER_FROM_DATE] = $fromDate;
        $filters[self::PARAMETER_TO_DATE] = $toDate;
        return $filters;
    }

    /**
     * @param int $employeeId
     * @param array $filter
     * @return \LeaveEntitlementSearchParameterHolder
     */
    protected function getEntitlementSearchParams(int $employeeId, array $filter): \LeaveEntitlementSearchParameterHolder
    {
        $searchParameters = new \LeaveEntitlementSearchParameterHolder();
        $searchParameters->setEmpNumber($employeeId);
        $searchParameters->setFromDate($filter[self::PARAMETER_FROM_DATE]);
        $searchParameters->setToDate($filter[self::PARAMETER_TO_DATE]);
        return $searchParameters;
    }

    /**
     * @return array
     */
    public function getValidationRules(): array
    {
        return [
            self::PARAMETER_TO_DATE => ['Date' => ['Y-m-d']],
            self::PARAMETER_FROM_DATE => ['Date' => ['Y-m-d']],
        ];
    }
}
