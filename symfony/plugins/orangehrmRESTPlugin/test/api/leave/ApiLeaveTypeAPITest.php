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

use Orangehrm\Rest\Api\Leave\LeaveTypeAPI;
use Orangehrm\Rest\Api\Leave\Entity\LeaveType;
use Orangehrm\Rest\Http\Request;
use Orangehrm\Rest\Http\Response;

class ApiLeaveTypeAPITest extends PHPUnit_Framework_TestCase
{
    private $leaveTypeApi;


    /**
     * Set up method
     */
    protected function setUp()
    {
        $sfEvent = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);
        $this->leaveTypeApi = new LeaveTypeAPI($request);

    }


    public function testGetLeaveTypes()
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

        $leaveType = new \LeaveType();
        $leaveType->setId(1);
        $leaveType->setName('Annual');

        $leaveTypeEntity = new LeaveType(1,'Annual');

        $typesCollection = new Doctrine_Collection('LeaveType');
        $typesCollection[] = $leaveType;

        $leaveTypeService = $this->getMockBuilder('LeaveTypeService')->getMock();
        $leaveTypeService->expects($this->any())
            ->method('getLeaveTypeList')
            ->will($this->returnValue($typesCollection));

        $this->leaveTypeApi->setLeaveTypeService($leaveTypeService);
        $ResponseReturned =  $this->leaveTypeApi->getLeaveTypes();
        $testResponse = null;
        $testResponse[] = $leaveTypeEntity->toArray();

        $success =  new Response($testResponse, array());

        $this->assertEquals($success, $ResponseReturned);

    }


}