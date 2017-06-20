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
 * Test class of Api/EmployeeService
 *
 * @group API
 */

use Orangehrm\Rest\Api\Admin\UserAPI;
use Orangehrm\Rest\Api\Admin\Entity\User;
use Orangehrm\Rest\Http\Request;
use Orangehrm\Rest\Http\Response;

class ApiUsersAPITest extends PHPUnit_Framework_TestCase
{
    private $usersAPI;


    /**
     * Set up method
     */
    protected function setUp()
    {
        $sfEvent   = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);
        $this->usersAPI = new UserAPI($request);
    }

    public function testGetUsers(){

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


        $filters = array();
        $filters['userType'] = null;
        $filters['employeeId'] = null;
        $filters['offset'] = null;
        $filters['limit'] = null;
        $filters['userName'] = null;
        $filters['use_ids'] = array(1, 2);

        $adminUser =  new \SystemUser();
        $adminUser->setId(1);
        $adminUser->setEmpNumber(1);
        $adminUser->setUserName('lakshitha');

        $adminUsersList[] = $adminUser;

        $systemUserService = $this->getMockBuilder('SystemUserService')->getMock();
        $systemUserService->expects($this->any())
            ->method('searchSystemUsers')
            ->with($filters)
            ->will($this->returnValue($adminUsersList));

//        $this->usersAPI = $this->getMock('Orangehrm\Rest\Api\Admin\UserAPI',array('getSearchParameters'),array($request));
//        $this->usersAPI->expects($this->once())
//            ->method('getSearchParameters')
//            ->will($this->returnValue($filters));

        $this->usersAPI->setSystemUserService($systemUserService);
        $response = $this->usersAPI->getSystemUsers();

        $user = new User();
        $user->buildUser($adminUser);
        $userList[] = $user->toArray();
        $mockResponse = new Response($userList);

        $this->assertEquals($mockResponse, $response);

    }


}