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


/**
 * Test classes
 *
 * @group API
 */

use Orangehrm\Rest\Api\Leave\LeaveEntitlementAPI;
use Orangehrm\Rest\Api\Leave\Entity\LeaveEntitlement;
use Orangehrm\Rest\Http\Request;
use Orangehrm\Rest\Http\Response;

class ApiLeaveEntitlementAPITest extends PHPUnit_Framework_TestCase
{
    private $entitlementApi;

    /**
     * Set up method
     */
    protected function setUp()
    {
        $sfEvent = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);
        $this->entitlementApi = new LeaveEntitlementAPI($request);

    }

    public function testSaveEntitlement()
    {
        $requestParams = $this->getMockBuilder('\Orangehrm\Rest\Http\RequestParams')
            ->disableOriginalConstructor()
            ->setMethods(array('getUrlParam'))
            ->getMock();
        $requestParams->expects($this->any())
            ->method('getUrlParam')
            ->with('id')
            ->will($this->returnValue(1));

        $sfEvent   = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);

        $leaveEntitlement = new \LeaveEntitlement();
        $leaveEntitlement->setLeaveTypeId('1');
        $leaveEntitlement->setEmpNumber(32);
        $leaveEntitlement->setNoOfDays(14);
        $leaveEntitlement->setFromDate('2016-04-20');
        $leaveEntitlement->setToDate('2017-04-20');
        $leaveEntitlement->setEntitlementType(1);

        $entitlementsCollection = new Doctrine_Collection('LeaveEntitlement');
        $entitlementsCollection[] = $leaveEntitlement;

        $entitlementApi = $this->getMockBuilder('Orangehrm\Rest\Api\Leave\LeaveEntitlementAPI')
            ->setMethods(array('createEntitlement'))
            ->setConstructorArgs(array($request))
            ->getMock();
        $entitlementApi->expects($this->once())
            ->method('createEntitlement')
            ->will($this->returnValue($leaveEntitlement));


        $entitlementApi->setRequestParams($requestParams);

        $entitlementService = $this->getMockBuilder('LeaveEntitlementService')->getMock();
        $entitlementService->expects($this->any())
            ->method('saveLeaveEntitlement')
            ->with($leaveEntitlement)
            ->will($this->returnValue($entitlementsCollection));

        $entitlementApi->setLeaveEntitlementService($entitlementService);
        $ResponseReturned = $entitlementApi->saveEntitlement($leaveEntitlement);

        $success =  new Response(array('success' => 'Successfully Saved'), array());

        $this->assertEquals($success, $ResponseReturned);

    }

    public function testGetLeaveEntitlements()
    {
        $requestParams = $this->getMockBuilder('\Orangehrm\Rest\Http\RequestParams')
            ->disableOriginalConstructor()
            ->setMethods(array('getUrlParam'))
            ->getMock();
        $requestParams->expects($this->any())
            ->method('getUrlParam')
            ->with('id')
            ->will($this->returnValue(1));

        $sfEvent   = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);

        $leaveEntitlement = new \LeaveEntitlement();
        $leaveEntitlement->setLeaveTypeId(1);
        $leaveEntitlement->setEmpNumber(32);
        $leaveEntitlement->setNoOfDays(14);
        $leaveEntitlement->setFromDate('2016-04-20');
        $leaveEntitlement->setToDate('2017-04-20');
        $leaveEntitlement->setEntitlementType(1);
        $leaveEntitlement->setId(1);

        $entitlementsCollection = new Doctrine_Collection('LeaveEntitlement');
        $entitlementsCollection[] = $leaveEntitlement;
        $searchParameters = new \LeaveEntitlementSearchParameterHolder();
        $leaveEntitlementEntity = new LeaveEntitlement(1);
        $leaveEntitlementEntity->buildEntitlement($leaveEntitlement);

        $searchParameters->setEmpNumber(32);
        $searchParameters->setLeaveTypeId(1);
        $searchParameters->setFromDate('2016-04-20');
        $searchParameters->setToDate('2017-04-20');

        $entitlementApi = $this->getMockBuilder('Orangehrm\Rest\Api\Leave\LeaveEntitlementAPI')
            ->setMethods(array('getFilters'))
            ->setConstructorArgs(array($request))
            ->getMock();
        $entitlementApi->expects($this->once())
            ->method('getFilters')
            ->will($this->returnValue($searchParameters));

        $entitlementApi->setRequestParams($requestParams);

        $entitlementService = $this->getMockBuilder('LeaveEntitlementService')->getMock();
        $entitlementService->expects($this->any())
            ->method('searchLeaveEntitlements')
            ->with($searchParameters)
            ->will($this->returnValue($entitlementsCollection));

        $entitlementApi->setLeaveEntitlementService($entitlementService);
        $ResponseReturned = $entitlementApi->getLeaveEntitlements();
        $testResponse = null;
        $testResponse[] = $leaveEntitlementEntity->toArray();


        $success =  new Response($testResponse, array());

        $this->assertEquals($success, $ResponseReturned);

    }


}