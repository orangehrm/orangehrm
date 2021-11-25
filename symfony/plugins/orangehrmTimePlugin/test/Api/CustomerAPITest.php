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

use OrangeHRM\Core\Api\CommonParams;
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

    public function testCreate(): void
    {
        $customerDao = $this->getMockBuilder(CustomerDao::class)
            ->onlyMethods(['saveCustomer'])
            ->getMock();
        $customerDao->expects($this->once())
            ->method('saveCustomer')
            ->will(
                $this->returnCallback(
                    function (Customer $customer) {
                        $customer->setId(3);
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
                    CustomerAPI::PARAMETER_DELETED => false,
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
                "id" => 3,
                "name" => 'ccc',
                "description" => 'ddd',
                "deleted" => false,
            ],
            $result->normalize()
        );
    }

    public function testGetAll(): void
    {
        $customerDao = $this->getMockBuilder(CustomerDao::class)
            ->onlyMethods(['searchCustomers', 'getSearchCustomersCount'])
            ->getMock();
        $customer1 = new Customer();
        $customer1->setId(3);
        $customer1->setName('CUSGET1');
        $customer1->setDescription('CUSGETDES1');
        $customer1->setDeleted(false);

        $customer2 = new Customer();
        $customer2->setId(4);
        $customer2->setName('CUSGET2');
        $customer2->setDescription('CUSGETDES2');
        $customer2->setDeleted(false);

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
                    "id" => 3,
                    "name" => 'CUSGET1',
                    "description" => 'CUSGETDES1',
                    "deleted" => false
                ],
                [
                    "id" => 4,
                    "name" => 'CUSGET2',
                    "description" => 'CUSGETDES2',
                    "deleted" => false
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

    public function testGetOne(): void
    {
        $customerDao = $this->getMockBuilder(CustomerDao::class)
            ->onlyMethods(['getCustomerById'])
            ->getMock();
        $customer = new Customer();
        $customer->setId(4);
        $customer->setName('CUS10');
        $customer->setDescription('DESC10');
        $customer->setDeleted(false);

        $customerDao->expects($this->exactly(1))
            ->method('getCustomerById')
            ->with(1)
            ->will($this->returnValue($customer));
        $customerService = $this->getMockBuilder(CustomerService::class)
            ->onlyMethods(['getCustomerDao'])
            ->getMock();
        $customerService->expects($this->exactly(1))
            ->method('getCustomerDao')
            ->willReturn($customerDao);

        /** @var MockObject&CustomerAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            CustomerAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_ID => 1
                ]
            ]
        )->onlyMethods(['getCustomerService'])
            ->getMock();
        $api->expects($this->once())
            ->method('getCustomerService')
            ->will($this->returnValue($customerService));

        $result = $api->getOne();
        $this->assertEquals(
            [
                "id" => 4,
                "name" => "CUS10",
                "description" => "DESC10",
                "deleted" => false
            ],
            $result->normalize()
        );
    }

    public function testUpdate(): void
    {
        $customerDao = $this->getMockBuilder(CustomerDao::class)
            ->onlyMethods(['saveCustomer', 'getCustomerById'])
            ->getMock();
        $customer = new Customer();
        $customer->setId(1);
        $customer->setName('Dev');
        $customer->setDescription('Dev');
        $customer->setDeleted(false);
        $customerDao->expects($this->exactly(1))
            ->method('getCustomerById')
            ->with(1)
            ->willReturn($customer);
        $customerDao->expects($this->exactly(1))
            ->method('saveCustomer')
            ->with($customer)
            ->will($this->returnValue($customer));
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
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_ID => 1
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    CustomerAPI::PARAMETER_NAME => 'COVID',
                    CustomerAPI::PARAMETER_DESCRIPTION => 'COVID',
                    CustomerAPI::PARAMETER_DELETED => false
                ]
            ]
        )->onlyMethods(['getCustomerService'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getCustomerService')
            ->will($this->returnValue($customerService));

        $result = $api->update();
        $this->assertEquals(
            [
                "id" => 1,
                "name" => "COVID",
                "description" => 'COVID',
                "deleted" => false

            ],
            $result->normalize()
        );
    }

    public function testDelete(): void
    {
        $customerDao = $this->getMockBuilder(CustomerDao::class)
            ->onlyMethods(['deleteCustomer'])
            ->getMock();

        $customer = new Customer();
        $customer->setId(1);
        $customer->setName('Dev');
        $customer->setDescription('Dev');
        $customer->setDeleted(false);

        $customerDao->expects($this->exactly(1))
            ->method('deleteCustomer')
            ->with([1])
            ->willReturn(1);
        $customerService = $this->getMockBuilder(CustomerService::class)
            ->onlyMethods(['getCustomerDao'])
            ->getMock();
        $customerService->expects($this->exactly(1))
            ->method('getCustomerDao')
            ->willReturn($customerDao);

        /** @var MockObject&CustomerAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            CustomerAPI::class,
            [

                RequestParams::PARAM_TYPE_BODY => [
                    CommonParams::PARAMETER_IDS => [1],
                ]
            ]
        )->onlyMethods(['getCustomerService'])
            ->getMock();
        $api->expects($this->exactly(1))
            ->method('getCustomerService')
            ->will($this->returnValue($customerService));

        $result = $api->delete();
        $this->assertEquals(
            [
                1
            ],
            $result->normalize()
        );
    }
}
