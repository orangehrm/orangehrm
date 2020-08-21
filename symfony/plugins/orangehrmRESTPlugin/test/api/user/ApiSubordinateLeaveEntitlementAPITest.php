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

use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Http\Request;

/**
 * @group API
 */
class ApiSubordinateLeaveEntitlementAPITest extends PHPUnit\Framework\TestCase
{
    protected function setUp()
    {
    }

    public function testGetLeaveEntitlements()
    {
        $sfEvent = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);

        $leaveType = new LeaveType();
        $leaveType->setId(10);
        $leaveType->setName('TestLeaveType');

        $leaveEntitlement = new LeaveEntitlement();
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
        $searchParameters = new LeaveEntitlementSearchParameterHolder();
        $leaveEntitlementEntity = new \Orangehrm\Rest\Api\Leave\Entity\LeaveEntitlement(1);
        $leaveEntitlementEntity->buildEntitlement($leaveEntitlement);

        $searchParameters->setEmpNumber(32);
        $searchParameters->setLeaveTypeId(1);
        $searchParameters->setFromDate('2019-04-20');
        $searchParameters->setToDate('2020-04-20');

        $myLeaveRequestApi = $this->getMockBuilder('Orangehrm\Rest\Api\User\SubordinateLeaveEntitlementAPI')
            ->setMethods(['getEntitlementSearchParams'])
            ->setConstructorArgs([$request])
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

        $leaveBalance = new LeaveBalance();
        $leaveBalance->setEntitled(14);
        $leaveBalance->setScheduled(1);
        $entitlementService->expects($this->any())
            ->method('getLeaveBalance')
            ->withAnyParameters()
            ->will($this->returnValue($leaveBalance));

        $myLeaveRequestApi->setLeaveEntitlementService($entitlementService);
        $myLeaveRequestApi->setLeaveTypeService($leaveTypeService);
        $responseEntitlement = $myLeaveRequestApi->getLeaveEntitlements([]);
        $testResponse = null;
        $testResponse[] = $leaveEntitlementEntity->toArray();

        $this->assertEquals($testResponse[0]['id'], $responseEntitlement[0]['id']);
        $this->assertEquals($testResponse[0]['validFrom'], $responseEntitlement[0]['validFrom']);
        $this->assertEquals($testResponse[0]['validTo'], $responseEntitlement[0]['validTo']);
        $this->assertEquals(14, $responseEntitlement[0]['leaveBalance']['entitled']);
        $this->assertEquals(1, $responseEntitlement[0]['leaveBalance']['scheduled']);
        $this->assertEquals('TestLeaveType', $responseEntitlement[0]['leaveType']['type']);
    }

    /**
     * @dataProvider requestParamProvider
     * @param $id
     * @param $returnParamCallback
     * @param $fromDate
     * @param $toDate
     */
    public function testGetFilters($id, $returnParamCallback, $fromDate, $toDate)
    {
        $requestParams = $this->getMockBuilder('\Orangehrm\Rest\Http\RequestParams')
            ->disableOriginalConstructor()
            ->setMethods(['getUrlParam','getQueryParam'])
            ->getMock();
        $requestParams->expects($this->once())
            ->method('getUrlParam')
            ->will($this->returnCallback($returnParamCallback));
        $requestParams->expects($this->exactly(6))
            ->method('getQueryParam')
            ->will($this->returnCallback($returnParamCallback));

        $sfEvent = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);

        $subordinateLeaveEntitlementAPI = $this->getMockBuilder(
            'Orangehrm\Rest\Api\User\SubordinateLeaveEntitlementAPI'
        )
            ->setMethods(['getAccessibleEmpNumbers'])
            ->setConstructorArgs([$request])
            ->getMock();
        $subordinateLeaveEntitlementAPI->setRequestParams($requestParams);
        $subordinateLeaveEntitlementAPI->expects($this->once())
            ->method('getAccessibleEmpNumbers')
            ->will($this->returnValue([1, 2, 3]));


        if ($id == 1) {
            $leavePeriodService = $this->getMockBuilder('LeavePeriodService')->getMock();
            $leavePeriodService->expects($this->once())
                ->method('getCurrentLeavePeriodByDate')
                ->withAnyParameters()
                ->will($this->returnValue(['2021-01-01', '2021-12-31']));
            $subordinateLeaveEntitlementAPI->setLeavePeriodService($leavePeriodService);
        } else {
            $leaveEntitlementAPI = $this->getMockBuilder('Orangehrm\Rest\Api\Leave\LeaveEntitlementAPI')
                ->setMethods(['validateLeavePeriods'])
                ->setConstructorArgs([$request])
                ->getMock();
            $leaveEntitlementAPI->expects($this->once())
                ->method('validateLeavePeriods')
                ->withAnyParameters()
                ->will($this->returnValue(true));
            $subordinateLeaveEntitlementAPI->setLeaveEntitlementApi($leaveEntitlementAPI);
        }

        $filters = $subordinateLeaveEntitlementAPI->getFilters();

        $this->assertEquals($fromDate, $filters['fromDate']);
        $this->assertEquals($toDate, $filters['toDate']);
    }

    /**
     * @return Generator
     */
    public function requestParamProvider()
    {
        yield [
            1,
            function ($param) {
                if ($param == 'fromDate' || $param == 'toDate') {
                    return null;
                } elseif ($param == 'deletedLeaveTypes') {
                    return 'true';
                }
                return 1;
            },
            '2021-01-01',
            '2021-12-31'
        ];
        yield [
            2,
            function ($param) {
                if ($param == 'fromDate') {
                    return '2020-01-01';
                } elseif ($param == 'toDate') {
                    return '2020-12-31';
                } elseif ($param == 'deletedLeaveTypes') {
                    return 'true';
                }
                return 2;
            },
            '2020-01-01',
            '2020-12-31'
        ];
    }
}
