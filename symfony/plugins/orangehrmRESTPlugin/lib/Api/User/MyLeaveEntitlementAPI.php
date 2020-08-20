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
use LeaveEntitlementSearchParameterHolder;
use LeaveEntitlementService;
use LeavePeriodService;
use LeaveTypeService;
use Orangehrm\Rest\Api\EndPoint;
use Orangehrm\Rest\Api\Exception\BadRequestException;
use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Api\Leave\Entity\LeaveEntitlement;
use Orangehrm\Rest\Api\Leave\LeaveEntitlementAPI;
use Orangehrm\Rest\Api\Leave\Entity\LeaveBalance;
use Orangehrm\Rest\Api\User\Model\LeaveEntitlementModel;
use Orangehrm\Rest\Api\User\Model\LeaveTypeModel;
use Orangehrm\Rest\Http\Response;

class MyLeaveEntitlementAPI extends EndPoint
{
    const PARAMETER_FROM_DATE = 'fromDate';
    const PARAMETER_TO_DATE = 'toDate';
    const PARAMETER_DELETED_LEAVE_TYPES = 'deletedLeaveTypes';
    const PARAMETER_AS_AT_DATE = 'balanceAsAtDate';
    const PARAMETER_END_DATE = 'balanceEndDate';

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
     * @var null|LeaveTypeService
     */
    private $leaveTypeService = null;

    /**
     * @return LeavePeriodService
     */
    public function getLeavePeriodService(): LeavePeriodService
    {
        if (is_null($this->leavePeriodService)) {
            $leavePeriodService = new LeavePeriodService();
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
     * @return LeaveTypeService
     */
    public function getLeaveTypeService(): LeaveTypeService
    {
        if (is_null($this->leaveTypeService)) {
            $this->leaveTypeService = new LeaveTypeService();
        }
        return $this->leaveTypeService;
    }

    /**
     * @param LeaveTypeService $leaveTypeService
     */
    public function setLeaveTypeService(LeaveTypeService $leaveTypeService)
    {
        $this->leaveTypeService = $leaveTypeService;
    }

    /**
     * @param int $employeeId
     * @return Response
     * @throws InvalidParamException
     * @throws RecordNotFoundException
     * @throws DaoException
     * @throws BadRequestException
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
     * @throws BadRequestException
     */
    public function getMyLeaveEntitlement(int $employeeId, array $filters)
    {
        $leaveTypeList = $this->getLeaveTypeService()->getLeaveTypeList();
        if (count($leaveTypeList) === 0) {
            throw new BadRequestException('No Leave Types Defined.');
        }

        $searchParameters = $this->getEntitlementSearchParams($employeeId, $filters);
        $results = $this->getLeaveEntitlementService()->searchLeaveEntitlements($searchParameters);
        $responseEntitlement = [];
        if (count($results) == 0) {
            throw new RecordNotFoundException('No Records Found');
        } else {
            $withDeletedLeaveTypes = $filters[self::PARAMETER_DELETED_LEAVE_TYPES];
            foreach ($results as $entitlement) {
                if (!$withDeletedLeaveTypes && $entitlement->getLeaveType()->getDeleted() == '1') {
                    continue;
                }

                $leaveEntitlementEntity = new LeaveEntitlement($entitlement->getId());
                $leaveEntitlementEntity->buildEntitlement($entitlement);
                $leaveEntitlementModel = new LeaveEntitlementModel($leaveEntitlementEntity);
                $leaveBalance = $this->getLeaveEntitlementService()->getLeaveBalance(
                    $employeeId,
                    $entitlement->getLeaveTypeId(),
                    $filters[self::PARAMETER_AS_AT_DATE],
                    $filters[self::PARAMETER_END_DATE]
                );
                $leaveBalanceEntity = new LeaveBalance($leaveBalance);
                $leaveTypeModel = new LeaveTypeModel($entitlement->getLeaveType());
                $responseEntitlement[] = array_merge(
                    $leaveEntitlementModel->toArray(),
                    array(
                        'leaveBalance' => $leaveBalanceEntity->toArray(),
                        'leaveType' => $leaveTypeModel->toArray(),
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
     * @throws DaoException
     */
    public function getFilters(int $employeeId): array
    {
        $filters = [];
        $employee = $this->getEmployeeService()->getEmployee($employeeId);

        if (empty($employee)) {
            throw new RecordNotFoundException('Employee Not Found');
        }
        $fromDate = $this->getRequestParams()->getQueryParam(self::PARAMETER_FROM_DATE);
        $toDate = $this->getRequestParams()->getQueryParam(self::PARAMETER_TO_DATE);

        if (empty($fromDate) && empty($toDate)) {
            $currentLeavePeriod = $this->getLeavePeriodService()->getCurrentLeavePeriodByDate(date('Y-m-d'));
            $fromDate = $currentLeavePeriod[0];
            $toDate = $currentLeavePeriod[1];
        } else {
            if (!$this->getLeaveEntitlementApi()->validateLeavePeriods($fromDate, $toDate)) {
                throw new InvalidParamException('No Leave Period Found');
            }
        }

        $deletedLeaveTypes = $this->getRequestParams()->getQueryParam(self::PARAMETER_DELETED_LEAVE_TYPES, 'false');
        if (!($deletedLeaveTypes == 'true' || $deletedLeaveTypes == 'false')) {
            throw new InvalidParamException(sprintf("Invalid `%s` Value", self::PARAMETER_DELETED_LEAVE_TYPES));
        }
        $deletedLeaveTypes = $deletedLeaveTypes == 'true';
        $asAtDate = $this->getRequestParams()->getQueryParam(self::PARAMETER_AS_AT_DATE);
        $endDate = $this->getRequestParams()->getQueryParam(self::PARAMETER_END_DATE);

        $filters[self::PARAMETER_FROM_DATE] = $fromDate;
        $filters[self::PARAMETER_TO_DATE] = $toDate;
        $filters[self::PARAMETER_DELETED_LEAVE_TYPES] = $deletedLeaveTypes;
        $filters[self::PARAMETER_AS_AT_DATE] = $asAtDate;
        $filters[self::PARAMETER_END_DATE] = $endDate;
        return $filters;
    }

    /**
     * @param int $employeeId
     * @param array $filter
     * @return LeaveEntitlementSearchParameterHolder
     */
    protected function getEntitlementSearchParams(
        int $employeeId,
        array $filter
    ): LeaveEntitlementSearchParameterHolder {
        $searchParameters = new LeaveEntitlementSearchParameterHolder();
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
            self::PARAMETER_AS_AT_DATE => ['Date' => ['Y-m-d']],
            self::PARAMETER_END_DATE => ['Date' => ['Y-m-d']],
        ];
    }
}
