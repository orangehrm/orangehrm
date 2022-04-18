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

namespace OrangeHRM\Tests\Time\Service;

use Exception;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\Customer;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;
use OrangeHRM\Time\Dao\CustomerDao;
use OrangeHRM\Time\Dto\CustomerSearchFilterParams;
use OrangeHRM\Time\Service\CustomerService;

/**
 * @group Time
 * @group Service
 */
class CustomerServiceTest extends TestCase
{
    private CustomerService $customerService;
    protected string $fixture;

    /**
     * Set up method
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->customerService = new CustomerService();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmTimePlugin/test/fixtures/CustomerService.yml';
        TestDataService::populate($this->fixture);
    }

    public function testSaveCustomer(): void
    {
        $customer = new Customer();
        $customer->setName("CUSSER001");
        $customer->setDescription("DESCSER002");
        $customerDao = $this->getMockBuilder(CustomerDao::class)->getMock();
        $customerDao->expects($this->once())
            ->method('saveCustomer')
            ->with($customer)
            ->will($this->returnValue($customer));
        $result = $customerDao->saveCustomer($customer);
        $this->assertEquals($customer, $result);
    }

    public function testGetAllCustomer(): void
    {
        $customerList = TestDataService::loadObjectList('Customer', $this->fixture, 'Customer');
        $customerSearchParam = new CustomerSearchFilterParams();
        $customerDao = $this->getMockBuilder(CustomerDao::class)->getMock();
        $customerDao->expects($this->once())
            ->method('searchCustomers')
            ->with($customerSearchParam)
            ->will($this->returnValue($customerList));
        $this->customerService->setCustomerDao($customerDao);
        $result = $this->customerService->searchCustomers($customerSearchParam);
        $this->assertCount(4, $result);
        $this->assertTrue($result[0] instanceof Customer);
    }

    public function testGetCustomerById(): void
    {
        $customerList = TestDataService::loadObjectList('Customer', $this->fixture, 'Customer');
        $customerDao = $this->getMockBuilder(CustomerDao::class)->getMock();
        $customerDao->expects($this->once())
            ->method('getCustomerById')
            ->with(1)
            ->will($this->returnValue($customerList[0]));
        $this->customerService->setCustomerDao($customerDao);
        $result = $this->customerService->getCustomer(1);
        $this->assertEquals($customerList[0], $result);
    }
}
