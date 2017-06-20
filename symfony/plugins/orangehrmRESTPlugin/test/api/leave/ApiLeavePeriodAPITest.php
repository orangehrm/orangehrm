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

use Orangehrm\Rest\Api\Leave\LeavePeriodAPI;
use Orangehrm\Rest\Http\Request;
use Orangehrm\Rest\Http\Response;

class ApiLeavePeriodAPITest extends PHPUnit_Framework_TestCase
{
    private $leavePeriodApi;


    /**
     * Set up method
     */
    protected function setUp()
    {
        $sfEvent = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);
        $this->leavePeriodApi = new LeavePeriodAPI($request);

    }


    public function testGetLeavePeriod()
    {
        $requestParams = $this->getMockBuilder('\Orangehrm\Rest\Http\RequestParams')
            ->disableOriginalConstructor()
            ->setMethods(array('getUrlParam'))
            ->getMock();
        $requestParams->expects($this->any())
            ->method('getUrlParam')
            ->with('id')
            ->will($this->returnValue(1));


        $leavePeriodList = array(array('2017-01-01','2018-01-01'),array('2018-01-01','2019-01-01'));


        $leavePeriodService = $this->getMockBuilder('LeavePeriodService')->getMock();
        $leavePeriodService->expects($this->any())
            ->method('getGeneratedLeavePeriodList')
            ->will($this->returnValue($leavePeriodList));

        $this->leavePeriodApi->setLeavePeriodService($leavePeriodService);
        $ResponseReturned =  $this->leavePeriodApi->getLeavePeriod();
        $testResponse = null;
        $testResponse[] = new Response($leavePeriodList);

        $success =  new Response($leavePeriodList);

        $this->assertEquals($success, $ResponseReturned);

    }


}