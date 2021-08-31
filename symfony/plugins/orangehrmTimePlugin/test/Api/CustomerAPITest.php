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

namespace OrangeHRM\Tests\Time\Api;

use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Entity\Customer;
use OrangeHRM\Tests\Util\EndpointTestCase;
use OrangeHRM\Tests\Util\MockObject;
use OrangeHRM\Time\Api\CustomerAPI;
use OrangeHRM\Time\Dao\CustomerDao;
use OrangeHRM\Time\Service\CustomerService;

class CustomerAPITest extends EndpointTestCase
{

    public function testGetCustomerService(): void
    {
        $api = new CustomerAPI($this->getRequest());
        $this->assertTrue($api->getCustomerService() instanceof CustomerService);
    }

    public function testCreate()
    {
        $customerDao = $this->getMockBuilder(CustomerDao::class)
            ->onlyMethods(['saveCustomer'])
            ->getMock();
        $customerDao->expects($this->once())
            ->method('saveCustomer')
            ->will(
                $this->returnCallback(
                    function (Customer $customer) {
                        $customer->setCustomerId(3);
                        return $customer;
                    }
                )
            );

        $customerService = $this->getMockBuilder(CustomerService::class)
            ->onlyMethods(['getCustomerDao'])
            ->getMock();

        $customerService->expects($this->once())
            ->method('getCustomerDao')
            ->willReturn($customerDao);

        /** @var MockObject&CustomerAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            CustomerAPI::class,
            [
                RequestParams::PARAM_TYPE_BODY => [
                    CustomerAPI::PARAMETER_NAME => 'ccc',
                    CustomerAPI::PARAMETER_DESCRIPTION => 'ddd',
                ]
            ]
        )->onlyMethods(['getCustomerService'])
            ->getMock();
        $api->expects($this->once())
            ->method('getCustomerService')
            ->will($this->returnValue($customerService));

        $result = $api->create();
        $this->assertEquals(
            [
                "customerId" => 3,
                "name" => 'ccc',
                "description" => 'ddd'
            ],
            $result->normalize()
        );
    }

    public function testGetAll()
    {
        $customerDao = $this->getMockBuilder(CustomerDao::class)
            ->onlyMethods(['searchCustomers', 'getSearchCustomersCount'])
            ->getMock();

        $customer1 = new Customer();
        $customer1->setCustomerId(3);
        $customer1->setName('CUSGET1');
        $customer1->setDescription('CUSGETDES1');

        $customer2 = new Customer();
        $customer2->setCustomerId(4);
        $customer2->setName('CUSGET2');
        $customer2->setDescription('CUSGETDES2');

        $customerDao->expects($this->exactly(1))
            ->method('searchCustomers')
            ->willReturn([$customer1, $customer2]);

        $customerDao->expects($this->exactly(1))
            ->method('getSearchCustomersCount')
            ->willReturn(2);

        $customerService = $this->getMockBuilder(CustomerService::class)
            ->onlyMethods(['getCustomerDao'])
            ->getMock();

        $customerService->expects($this->exactly(2))
            ->method('getCustomerDao')
            ->willReturn($customerDao);

        /** @var MockObject&CustomerAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            CustomerAPI::class,
            [
                RequestParams::PARAM_TYPE_BODY => [
                    CustomerAPI::PARAMETER_NAME,
                ]
            ]
        )->onlyMethods(['getCustomerService'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getCustomerService')
            ->will($this->returnValue($customerService));

        $result = $api->getAll();

        $this->assertEquals(
            [
                [
                    "customerId" => 3,
                    "name" => 'CUSGET1',
                    "description" => 'CUSGETDES1'
                ],
                [
                    "customerId" => 4,
                    "name" => 'CUSGET2',
                    "description" => 'CUSGETDES2'
                ]
            ],
            $result->normalize()
        );
        $this->assertEquals(
            [
                "total" => 2
            ],
            $result->getMeta()->all()
        );
    }
}
