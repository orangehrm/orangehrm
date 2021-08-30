<?php

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