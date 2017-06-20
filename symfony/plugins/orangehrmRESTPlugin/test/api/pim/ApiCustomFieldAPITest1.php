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

use Orangehrm\Rest\Api\Pim\CustomFieldAPI;
use Orangehrm\Rest\Api\Pim\Entity\CustomField;
use Orangehrm\Rest\Http\Request;
use Orangehrm\Rest\Http\Response;

class ApiCustomFieldAPITest1 extends PHPUnit_Framework_TestCase
{
    private $employeeCustomFieldAPI;


    /**
     * Set up method
     */
    protected function setUp()
    {
        $sfEvent   = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);
        $this->employeeCustomFieldAPI = new CustomFieldAPI($request);
    }

    public function testGetCustomField(){

        $requestParams = $this->getMockBuilder('\Orangehrm\Rest\Http\RequestParams')
            ->disableOriginalConstructor()
            ->setMethods(array('getUrlParam'))
            ->getMock();
        $requestParams->expects($this->any())
            ->method('getUrlParam')
            ->with('id')
            ->will($this->returnValue(1));

        $customFieldObj =  new \CustomField();
        $customFieldObj->setId(1);
        $customFieldObj->setName('Field');
        $customFieldObj->setScreen('personal');
        $customFieldObj->setExtraData('');
        $customFieldList[] = $customFieldObj;

        $this->employeeCustomFieldAPI->setRequestParams($requestParams);

        $pimCustomFieldService = $this->getMockBuilder('CustomFieldConfigurationService')->getMock();
        $pimCustomFieldService->expects($this->any())
            ->method('getCustomFieldList')
            ->with(null, 'name', 'ASC')
            ->will($this->returnValue($customFieldList));

        $customEntField = new CustomField();
        $customEntField->setId(1);
        $customEntField->setName('Field');
        $customEntField->setType('Text or Number');
        $customEntField->setScreen('personal');
//
        $assetResArray[] = $customEntField->toArray();

        $this->employeeCustomFieldAPI->setCustomFieldService($pimCustomFieldService);
        $response= $this->employeeCustomFieldAPI->getCustomFields();

        $assertResponse = new Response($assetResArray);
        $this->assertEquals($assertResponse, $response);

    }

}