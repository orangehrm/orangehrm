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
}
