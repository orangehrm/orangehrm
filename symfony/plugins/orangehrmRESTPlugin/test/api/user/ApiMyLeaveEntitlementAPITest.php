<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http=>//www.orangehrm.com
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

use Orangehrm\Rest\Api\Leave\Entity\LeaveEntitlement;
use Orangehrm\Rest\Api\Leave\Entity\LeaveRequest;
use Orangehrm\Rest\Api\User\MyLeaveEntitlementAPI;
use Orangehrm\Rest\Http\Request;
use Orangehrm\Rest\Http\Response;

/**
 * @group API
 */
class ApiMyLeaveEntitlementAPITest extends PHPUnit\Framework\TestCase
{
    protected function setUp()
    {
    }

    public function testGetMyLeaveEntitlement()
    {
        $sfEvent = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);

        $leaveType = new \LeaveType();
        $leaveType->setId(10);
        $leaveType->setName('TestLeaveType');

        $leaveEntitlement = new \LeaveEntitlement();
        $leaveEntitlement->setLeaveTypeId(10);
        $leaveEntitlement->setLeaveType($leaveType);
        $leaveEntitlement->setEmpNumber(32);
        $leaveEntitlement->setNoOfDays(14);
        $leaveEntitlement->setFromDate('2019-04-20');
        $leaveEntitlement->setToDate('2020-04-20');
        $leaveEntitlement->setEntitlementType(1);
        $leaveEntitlement->setId(1);

        $entitlementsCollection = new Doctrine_Collection('LeaveEntitlement');
        $entitlementsCollection[] = $leaveEntitlement;
        $searchParameters = new \LeaveEntitlementSearchParameterHolder();
        $leaveEntitlementEntity = new LeaveEntitlement(1);
        $leaveEntitlementEntity->buildEntitlement($leaveEntitlement);

        $searchParameters->setEmpNumber(32);
        $searchParameters->setLeaveTypeId(1);
        $searchParameters->setFromDate('2019-04-20');
        $searchParameters->setToDate('2020-04-20');

        $myLeaveRequestApi = $this->getMockBuilder('Orangehrm\Rest\Api\User\MyLeaveEntitlementAPI')
            ->setMethods(array('getEntitlementSearchParams'))
            ->setConstructorArgs(array($request))
            ->getMock();
        $myLeaveRequestApi->expects($this->once())
            ->method('getEntitlementSearchParams')
            ->will($this->returnValue($searchParameters));

        $entitlementService = $this->getMockBuilder('LeaveEntitlementService')->getMock();
        $entitlementService->expects($this->once())
            ->method('searchLeaveEntitlements')
            ->with($searchParameters)
            ->will($this->returnValue($entitlementsCollection));

        $leaveTypeService = $this->getMockBuilder('LeaveTypeService')->getMock();
        $leaveTypeService->expects($this->once())
            ->method('getLeaveTypeList')
            ->will($this->returnValue([$leaveType]));

        $leaveBalance = new \LeaveBalance();
        $leaveBalance->setEntitled(14);
        $leaveBalance->setScheduled(1);
        $entitlementService->expects($this->any())
            ->method('getLeaveBalance')
            ->withAnyParameters()
            ->will($this->returnValue($leaveBalance));

        $myLeaveRequestApi->setLeaveEntitlementService($entitlementService);
        $myLeaveRequestApi->setLeaveTypeService($leaveTypeService);
        $responseEntitlement = $myLeaveRequestApi->getMyLeaveEntitlement(1, []);
        $testResponse = null;
        $testResponse[] = $leaveEntitlementEntity->toArray();

        $this->assertEquals($testResponse[0]['id'], $responseEntitlement[0]['id']);
        $this->assertEquals($testResponse[0]['validFrom'], $responseEntitlement[0]['validFrom']);
        $this->assertEquals($testResponse[0]['validTo'], $responseEntitlement[0]['validTo']);
        $this->assertEquals(14, $responseEntitlement[0]['leaveBalance']['entitled']);
        $this->assertEquals(1, $responseEntitlement[0]['leaveBalance']['scheduled']);
        $this->assertEquals('TestLeaveType', $responseEntitlement[0]['leaveType']['type']);
    }

    public function testGetMyLeaveDetails()
    {
        $sfEvent = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);

        $entitlement = [
            [
                "id" => "1",
                "validFrom" => "2020-01-01",
                "validTo" => "2020-12-31",
                "creditedDate" => "2020-06-26",
                "leaveBalance" => [
                    "entitled" => 4,
                    "used" => 1,
                    "scheduled" => 0.5,
                    "pending" => 0,
                    "notLinked" => 0,
                    "taken" => 0.5,
                    "adjustment" => 0,
                    "balance" => 3
                ],
                "leaveType" => [
                    "type" => "Casual",
                    "id" => "2",
                    "deleted" => null,
                    "situational" => false
                ]
            ]
        ];

        $myLeaveRequestApi = $this->getMockBuilder('Orangehrm\Rest\Api\User\MyLeaveEntitlementAPI')
            ->setMethods(['getMyLeaveEntitlement', 'getMyLeaveRequests', 'getFilters'])
            ->setConstructorArgs(array($request))
            ->getMock();
        $myLeaveRequestApi->expects($this->once())
            ->method('getMyLeaveEntitlement')
            ->will($this->returnValue($entitlement));
        $myLeaveRequestApi->expects($this->once())
            ->method('getFilters')
            ->will($this->returnValue([]));

        $leaveDetailsResponse = $myLeaveRequestApi->getMyLeaveDetails(1);

        $success = new Response($entitlement, array());

        $this->assertEquals($success, $leaveDetailsResponse);
    }

    /**
     * @dataProvider requestParamProvider
     * @param $id
     * @param $returnParamCallback
     * @param $fromDate
     * @param $toDate
     * @throws DaoException
     * @throws Doctrine_Connection_Exception
     * @throws Doctrine_Record_Exception
     * @throws \Orangehrm\Rest\Api\Exception\InvalidParamException
     * @throws \Orangehrm\Rest\Api\Exception\RecordNotFoundException
     */
    public function testGetFilters($id, $returnParamCallback, $fromDate, $toDate)
    {
        $requestParams = $this->getMockBuilder('\Orangehrm\Rest\Http\RequestParams')
            ->disableOriginalConstructor()
            ->setMethods(['getQueryParam'])
            ->getMock();
        $requestParams->expects($this->exactly(5))
            ->method('getQueryParam')
            ->will($this->returnCallback($returnParamCallback));

        $sfEvent = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);

        $myLeaveRequestApi = new MyLeaveEntitlementAPI($request);
        $myLeaveRequestApi->setRequestParams($requestParams);

        $employeeService = $this->getMockBuilder('EmployeeService')->getMock();
        $employeeService->expects($this->once())
            ->method('getEmployee')
            ->withAnyParameters()
            ->will($this->returnValue(new \Employee()));
        $myLeaveRequestApi->setEmployeeService($employeeService);

        if ($id == 1) {
            $leavePeriodService = $this->getMockBuilder('LeavePeriodService')->getMock();
            $leavePeriodService->expects($this->once())
                ->method('getCurrentLeavePeriodByDate')
                ->withAnyParameters()
                ->will($this->returnValue(['2021-01-01', '2021-12-31']));

            $myLeaveRequestApi->setLeavePeriodService($leavePeriodService);
        } else {
            $leaveEntitlementApi = $this->getMockBuilder('\Orangehrm\Rest\Api\Leave\LeaveEntitlementAPI')
                ->setMethods(['validateLeavePeriods'])
                ->setConstructorArgs([$request])
                ->getMock();
            $leaveEntitlementApi->expects($this->once())
                ->method('validateLeavePeriods')
                ->withAnyParameters()
                ->will($this->returnValue(true));
            $myLeaveRequestApi->setLeaveEntitlementApi($leaveEntitlementApi);
        }

        $filters = $myLeaveRequestApi->getFilters(1);

        $this->assertEquals($fromDate, $filters['fromDate']);
        $this->assertEquals($toDate, $filters['toDate']);
    }

    /**
     * @return \Generator
     */
    public function requestParamProvider()
    {
        yield [1, function ($param) {
            if ($param == 'deletedLeaveTypes') {
                return 'true';
            }
            return null;
        }, '2021-01-01', '2021-12-31'];
        yield [2, function ($param) {
            if ($param == 'fromDate') {
                return '2020-01-01';
            } elseif ($param == 'toDate') {
                return '2020-12-31';
            } elseif ($param == 'deletedLeaveTypes') {
                return 'false';
            }
            return null;
        }, '2020-01-01', '2020-12-31'];
    }
}
