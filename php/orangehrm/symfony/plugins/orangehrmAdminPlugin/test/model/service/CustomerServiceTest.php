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
require_once sfConfig::get('sf_test_dir') . '/util/TestDataService.php';

/** 
 * @group Admin 
 */
class CustomerServiceTest extends PHPUnit_Framework_TestCase {
	
	private $customerService;
	private $fixture;

	/**
	 * Set up method
	 */
	protected function setUp() {
		$this->customerService = new CustomerService();
		$this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmAdminPlugin/test/fixtures/ProjectDao.yml';
		TestDataService::populate($this->fixture);
	}
	
	public function testGetCustomerList() {

		$customerList = TestDataService::loadObjectList('Customer', $this->fixture, 'Customer');

		$customerDao = $this->getMock('CustomerDao');
		$customerDao->expects($this->once())
			->method('getCustomerList')
			->with("","","","","")
			->will($this->returnValue($customerList));

		$this->customerService->setCustomerDao($customerDao);

		$result = $this->customerService->getCustomerList("","","","","");
		$this->assertEquals($result, $customerList);
	}
	
	public function testGetCustomerCount() {

		$customerDao = $this->getMock('CustomerDao');
		$customerDao->expects($this->once())
			->method('getCustomerCount')
			->with("")
			->will($this->returnValue(2));

		$this->customerService->setCustomerDao($customerDao);

		$result = $this->customerService->getCustomerCount("");
		$this->assertEquals($result,2);
	}
	
	public function testGetCustomerById() {

		$customerList = TestDataService::loadObjectList('Customer', $this->fixture, 'Customer');

		$customerDao = $this->getMock('CustomerDao');
		$customerDao->expects($this->once())
			->method('getCustomerById')
			->with(1)
			->will($this->returnValue($customerList[0]));

		$this->customerService->setCustomerDao($customerDao);

		$result = $this->customerService->getCustomerById(1);
		$this->assertEquals($result,$customerList[0]);
	}
	
	public function testDeleteCustomer() {

		$customerList = TestDataService::loadObjectList('Customer', $this->fixture, 'Customer');

		$customerDao = $this->getMock('CustomerDao');
		$customerDao->expects($this->once())
			->method('deleteCustomer')
			->with(1)
			->will($this->returnValue(1));

		$this->customerService->setCustomerDao($customerDao);

		$result = $this->customerService->deleteCustomer(1);
		$this->assertEquals($result,1);
	}
	
	public function testGetAllCustomers() {

		$customerList = TestDataService::loadObjectList('Customer', $this->fixture, 'Customer');

		$customerDao = $this->getMock('CustomerDao');
		$customerDao->expects($this->once())
			->method('getAllCustomers')
			->with(false)
			->will($this->returnValue($customerList));

		$this->customerService->setCustomerDao($customerDao);

		$result = $this->customerService->getAllCustomers(false);
		$this->assertEquals($result,$customerList);
	}
	
	public function testHasCustomerGotTimesheetItems() {

		$customerDao = $this->getMock('CustomerDao');
		$customerDao->expects($this->once())
			->method('hasCustomerGotTimesheetItems')
			->with(1)
			->will($this->returnValue(true));

		$this->customerService->setCustomerDao($customerDao);

		$result = $this->customerService->hasCustomerGotTimesheetItems(1);
		$this->assertEquals($result,true);
	}
	
    public function testGetCustomerNameList() {

        $allowdCustomers = array(1, 2);
        $customer1['customerId'] = 1;
        $customer1['name'] = 'Xavier';
        
        $customer2['customerId'] = 2;
        $customer2['name'] = 'ACME';
        
        $customers = array($customer1, $customer2);
        
        $customerDao = $this->getMock('CustomerDao');
        $customerDao->expects($this->once())
            ->method('getCustomerNameList')
            ->with($allowdCustomers, true)
            ->will($this->returnValue($customers));
        
        $this->customerService->setCustomerDao($customerDao);
        
        $result = $this->customerService->getCustomerNameList($allowdCustomers, true);
        $this->assertEquals($customers[0],$result[0]);
    }
	
	
}

?>
