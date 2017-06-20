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

use Orangehrm\Rest\Api\Pim\EmployeeContactDetailAPI;
use Orangehrm\Rest\Api\Pim\Entity\EmployeeContactDetail;
use Orangehrm\Rest\Http\Request;
use Orangehrm\Rest\Http\Response;

class ApiEmployeeContactDetailAPITest extends PHPUnit_Framework_TestCase
{
    /**
     * @var EmployeeContactDetailAPI
     */
    private $employeeContactDetailAPI;


    /**
     * Set up method
     */
    protected function setUp()
    {
        $sfEvent = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);
        $this->employeeContactDetailAPI = new EmployeeContactDetailAPI($request);
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmRESTPlugin/test/fixtures/contact-detail.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetEmployeeContactDetails()
    {

        $requestParams = $this->getMockBuilder('\Orangehrm\Rest\Http\RequestParams')
            ->disableOriginalConstructor()
            ->setMethods(array('getUrlParam'))
            ->getMock();
        $requestParams->expects($this->any())
            ->method('getUrlParam')
            ->with('id')
            ->will($this->returnValue(1));

        $empNumber = 1;
        $employee = new \Employee();
        $employee->setLastName('Last Name');
        $employee->setFirstName('First Name');
        $employee->setEmpNumber($empNumber);
        $employee->setEmployeeId($empNumber);
        $employee->setJoinedDate("2016-04-15");
        $employee->setEmpWorkEmail("mdriggs@hrm.com");
        $employee->setEmpMobile(0754343435);

        $employeeCategory = new JobCategory();
        $employeeCategory->setName("Engineer");

        //   $employee->setContracts(array($employeeContract)) ;
        $employee->setJobCategory($employeeCategory);


        $this->employeeContactDetailAPI->setRequestParams($requestParams);

        $pimEmployeeService = $this->getMockBuilder('EmployeeService')->getMock();
        $pimEmployeeService->expects($this->any())
            ->method('getEmployee')
            ->with($empNumber)
            ->will($this->returnValue($employee));

        $this->employeeContactDetailAPI->setEmployeeService($pimEmployeeService);
        $employeeReturned = $this->employeeContactDetailAPI->getEmployeeContactDetails();

        // creating the employee json array
        $employeeContactDetails = new EmployeeContactDetail($employee->getFullName(), $employee->getEmployeeId());

        $employeeContactDetails->buildContactDetails($employee);

        $jsonEmployeeContactDetailArray = $employeeContactDetails->toArray();

        $assertResponse = new Response($jsonEmployeeContactDetailArray, array());

        $this->assertEquals($assertResponse, $employeeReturned);

    }

    public function testSaveEmployeeContactDetails()
    {

        $empNumber = 1;
        $employee = new \Employee();
        $employee->setLastName('Last Name');
        $employee->setFirstName('First Name');
        $employee->setEmpNumber($empNumber);
        $employee->setEmployeeId($empNumber);
        $employee->setJoinedDate("2016-04-15");
        $employee->setEmpWorkEmail("nina@yahoo.com");
        $employee->setEmpMobile(0754343435);
        $employee->setEmpOthEmail('');

        $filterCountry = 'Canada';

        $filters = array();
        $filters[EmployeeContactDetailAPI::PARAMETER_ADDRESS_STREET_1] = 'No 50 Park road';
        $filters[EmployeeContactDetailAPI::PARAMETER_ADDRESS_STREET_2] = 'No 34 River view Canada';
        $filters[EmployeeContactDetailAPI::PARAMETER_CITY] = 'River view';
        $filters[EmployeeContactDetailAPI::PARAMETER_COUNTRY] = $filterCountry;
        $filters[EmployeeContactDetailAPI::PARAMETER_HOME_TELEPHONE] = '0972432623';
        $filters[EmployeeContactDetailAPI::PARAMETER_MOBILE] = '097124353';
        $filters[EmployeeContactDetailAPI::PARAMETER_ID] = '1';
        $filters[EmployeeContactDetailAPI::PARAMETER_OTHER_EMAIL] = '';
        $filters[EmployeeContactDetailAPI::PARAMETER_STATE] = 'Vancour';
        $filters[EmployeeContactDetailAPI::PARAMETER_WORK_EMAIL] = 'nina@yahoo.com';
        $filters[EmployeeContactDetailAPI::PARAMETER_ZIP] = '1550';

        $sfEvent   = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);

        $this->employeeContactDetailAPI = $this->getMockBuilder('Orangehrm\Rest\Api\Pim\EmployeeContactDetailApi')
            ->setMethods(array('filterParameters','validateEmployeeEmails'))
            ->setConstructorArgs(array($request))
            ->getMock();
        $this->employeeContactDetailAPI->expects($this->once())
            ->method('filterParameters')
            ->will($this->returnValue($filters));

        $this->employeeContactDetailAPI->expects($this->any())
            ->method('validateEmployeeEmails')
            ->with($employee,$employee->getEmpWorkEmail(),$employee->getOtherId())
            ->will($this->returnValue(true));

        $pimEmployeeService = $this->getMockBuilder('EmployeeService')->getMock();
        $pimEmployeeService->expects($this->any())
            ->method('getEmployee')
            ->with(1)
            ->will($this->returnValue($employee));

        $pimEmployeeService->expects($this->any())
            ->method('saveEmployee')
            ->with($employee)
            ->will($this->returnValue($employee));

        $this->employeeContactDetailAPI->setEmployeeService($pimEmployeeService);

        // getCountryService()->getCountryByCountryName
        $countryService = $this->getMockBuilder('CountryService')
            ->setMethods(array('getCountryByCountryName'))
            ->getMock();

        $countryObject = new Country();
        $countryObject->setName($filterCountry);

        $countryService->expects($this->once())
            ->method('getCountryByCountryName')
            ->with($filterCountry)
            ->will($this->returnValue($countryObject));
        $this->employeeContactDetailAPI->setCountryService($countryService);

        $returned = $this->employeeContactDetailAPI->saveEmployeeContactDetails();
        $testResponse = new Response(array('success' => 'Successfully Saved'));

        $this->assertEquals($returned, $testResponse);

    }

    public function testUpdateEmployeeContactDetails()
    {

        $empNumber = 1;
        $employee = new \Employee();
        $employee->setLastName('Last Name');
        $employee->setFirstName('First Name');
        $employee->setEmpNumber($empNumber);
        $employee->setEmployeeId($empNumber);
        $employee->setJoinedDate("2016-04-15");
        $employee->setEmpWorkEmail("nina@yahoo.com");
        $employee->setEmpOthEmail('');
        $employee->setEmpMobile(0754343435);

        $filterCountry = 'Canada';

        $filters = array();
        $filters[EmployeeContactDetailAPI::PARAMETER_ADDRESS_STREET_1] = 'No 50 Park road';
        $filters[EmployeeContactDetailAPI::PARAMETER_ADDRESS_STREET_2] = 'No 34 River view Canada';
        $filters[EmployeeContactDetailAPI::PARAMETER_CITY] = 'River view';
        $filters[EmployeeContactDetailAPI::PARAMETER_COUNTRY] = $filterCountry;
        $filters[EmployeeContactDetailAPI::PARAMETER_HOME_TELEPHONE] = '0972432623';
        $filters[EmployeeContactDetailAPI::PARAMETER_MOBILE] = '097124353';
        $filters[EmployeeContactDetailAPI::PARAMETER_ID] = '1';
        $filters[EmployeeContactDetailAPI::PARAMETER_OTHER_EMAIL] = '';
        $filters[EmployeeContactDetailAPI::PARAMETER_STATE] = 'Vancour';
        $filters[EmployeeContactDetailAPI::PARAMETER_WORK_EMAIL] = 'nina@yahoo.com';
        $filters[EmployeeContactDetailAPI::PARAMETER_ZIP] = '1550';

        $sfEvent   = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);

        $this->employeeContactDetailAPI = $this->getMockBuilder('Orangehrm\Rest\Api\Pim\EmployeeContactDetailApi')
            ->setMethods(array('filterParameters','validateEmployeeEmails'))
            ->setConstructorArgs(array($request))
            ->getMock();
        $this->employeeContactDetailAPI->expects($this->once())
            ->method('filterParameters')
            ->will($this->returnValue($filters));

        $this->employeeContactDetailAPI->expects($this->any())
            ->method('validateEmployeeEmails')
            ->with($employee,$employee->getEmpWorkEmail(),$employee->getOtherId())
            ->will($this->returnValue(true));

        $pimEmployeeService = $this->getMockBuilder('EmployeeService')->getMock();
        $pimEmployeeService->expects($this->any())
            ->method('getEmployee')
            ->with(1)
            ->will($this->returnValue($employee));

        $pimEmployeeService->expects($this->any())
            ->method('saveEmployee')
            ->with($employee)
            ->will($this->returnValue($employee));

        $countryService = $this->getMockBuilder('CountryService')
            ->setMethods(array('getCountryByCountryName'))
            ->getMock();

        $countryObject = new Country();
        $countryObject->setName($filterCountry);

        $countryService->expects($this->once())
            ->method('getCountryByCountryName')
            ->with($filterCountry)
            ->will($this->returnValue($countryObject));
        $this->employeeContactDetailAPI->setCountryService($countryService);

        $this->employeeContactDetailAPI->setEmployeeService($pimEmployeeService);

        $returned = $this->employeeContactDetailAPI->updateEmployeeContactDetails();
        $testResponse = new Response(array('success' => 'Successfully Updated'));

        $this->assertEquals($returned, $testResponse);

    }

//    private function testValidateInputs(){
//
//        $filters = array();
//        $filters[EmployeeContactDetailAPI::PARAMETER_PHONE] = '071-45363737';
//        $filters[EmployeeContactDetailAPI::PARAMETER_COUNTRY] = 'India';
//        $filters[EmployeeContactDetailAPI::PARAMETER_ADDRESS] = 'No 45 Karei Nagar Sri vihar';
//        $filters[EmployeeContactDetailAPI::PARAMETER_ID] = '1';
//        $filters[EmployeeContactDetailAPI::PARAMETER_EMAIL] = 'shanidatta@utl.com';
//
//        $sfEvent   = new sfEventDispatcher();
//        $sfRequest = new sfWebRequest($sfEvent);
//        $request = new Request($sfRequest);
//
//         $employeeContactDetailAPI = new EmployeeContactDetailAPI($request);
//
//       // $employeeContactDetailAPI = $this->getMockBuilder('Orangehrm\Rest\Api\Pim\EmployeeContactDetailApi')
//              ->setConstructorArgs(array($request))
//              ->getMock();
//        $returned = $employeeContactDetailAPI->validateInputs($filters);
//        $this->assertEquals($returned, true);
//
//    }


}
