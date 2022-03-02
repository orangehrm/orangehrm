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

namespace OrangeHRM\Tests\Time\Dao;

use Exception;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\Customer;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;
use OrangeHRM\Time\Dao\CustomerDao;
use OrangeHRM\Time\Dao\ProjectDao;
use OrangeHRM\Time\Dto\CustomerSearchFilterParams;
use OrangeHRM\Time\Dto\ProjectSearchFilterParams;
use OrangeHRM\Time\Exception\CustomerServiceException;

/**
 * @group Time
 * @group Dao
 */
class CustomerDaoTest extends KernelTestCase
{
    private CustomerDao $customerDao;
    private ProjectDao $projectDao;
    protected string $fixture;

    /**
     * Set up method
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->customerDao = new CustomerDao();
        $this->projectDao = new ProjectDao();
    }

    private function populateCustomerServiceFixture(): void
    {
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmTimePlugin/test/fixtures/CustomerService.yml';
        TestDataService::populate($this->fixture);
    }

    private function populateCustomerDaoFixture(): void
    {
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmTimePlugin/test/fixtures/CustomerDao.yaml';
        TestDataService::populate($this->fixture);
    }

    public function testAddCustomer(): void
    {
        $this->populateCustomerServiceFixture();
        $customer = new Customer();
        $customer->setName('Customer 2');
        $customer->setDescription('Description 2');
        $customer->setDeleted(false);
        $result = $this->customerDao->saveCustomer($customer);
        $this->assertTrue($result instanceof Customer);
        $this->assertEquals('Customer 2', $result->getName());
    }

    public function testGetCustomerList(): void
    {
        $this->populateCustomerServiceFixture();
        $customerFilterParams = new CustomerSearchFilterParams();
        $result = $this->customerDao->searchCustomers($customerFilterParams);
        $this->assertCount(3, $result);
        $this->assertTrue($result[0] instanceof Customer);
    }

    public function testFilterByCustomerName(): void
    {
        $customerFilterParams = new CustomerSearchFilterParams();
        $customerFilterParams->setName("Orange");
        $result = $this->customerDao->searchCustomers($customerFilterParams);
        $this->assertCount(1, $result);
        $this->assertTrue($result[0] instanceof Customer);
        $this->assertEquals('Orange', $result[0]->getName());
    }

    public function testGetCustomerById(): void
    {
        $this->populateCustomerServiceFixture();
        $result = $this->customerDao->getCustomerById(1);
        $this->assertEquals('Orange', $result->getName());
        $this->assertEquals('HRM', $result->getDescription());
    }

    public function testGetCustomerByIdOnNull(): void
    {
        $result = $this->customerDao->getCustomerById(10);
        $this->assertFalse($result instanceof Customer);
        $this->assertNull($result);
    }

    public function testGetCustomer(): void
    {
        $result = $this->customerDao->getCustomer(100);
        $this->assertFalse($result instanceof Customer);
        $this->assertNull($result);
    }

    public function testUpdateCustomer(): void
    {
        $this->populateCustomerServiceFixture();
        $customer = $this->customerDao->getCustomerById(1);
        $customer->setName('TTTT');
        $customer->setDescription('DDD');
        $result = $this->customerDao->saveCustomer($customer);
        $this->assertTrue($result instanceof Customer);
        $this->assertEquals('TTTT', $result->getName());
        $this->assertEquals('DDD', $result->getDescription());
        $this->assertEquals(1, $result->getId());
    }

    public function testDeleteCustomer(): void
    {
        $this->populateCustomerServiceFixture();
        $result = $this->customerDao->deleteCustomer([2, 3]);
        $projectCount = $this->projectDao->getProjectsCount(new ProjectSearchFilterParams());
        $this->assertEquals(2, $result);
        $this->assertEquals(1, $projectCount);
    }

    public function testGetCustomerIdList(): void
    {
        $this->populateCustomerServiceFixture();
        $result = $this->customerDao->getCustomerIdList();
        $this->assertEquals([1, 2, 3], $result);

        $result = $this->customerDao->getCustomerIdList(true);
        $this->assertEquals([1, 2, 3, 4], $result);
    }

    public function testGetCustomerIdListForProjectAdmin(): void
    {
        $this->populateCustomerDaoFixture();
        $result = $this->customerDao->getCustomerIdListForProjectAdmin(1);
        $this->assertEmpty($result);

        $result = $this->customerDao->getCustomerIdListForProjectAdmin(2);
        $this->assertEquals([7, 5, 12], $result);

        $result = $this->customerDao->getCustomerIdListForProjectAdmin(5);
        $this->assertEquals([7], $result);

        $result = $this->customerDao->getCustomerIdListForProjectAdmin(7);
        $this->assertEquals([9, 8], $result);

        $result = $this->customerDao->getCustomerIdListForProjectAdmin(9);
        $this->assertEmpty($result);

        $result = $this->customerDao->getCustomerIdListForProjectAdmin(5, true);
        $this->assertEquals([7, 11], $result);
    }

    public function testExceptionForDeleteCustomer(): void
    {
        try {
            $this->populateCustomerServiceFixture();
            $toTobedeletedIds = [1];
            $this->customerDao->deleteCustomer($toTobedeletedIds);
            $this->fail('Exception expected');
        } catch (Exception $exception) {
            $this->assertTrue($exception instanceof CustomerServiceException);
        }
    }

    public function testGetUnselectableCustomerIds(): void
    {
        $this->populateCustomerServiceFixture();
        $customerIds = $this->customerDao->getUnselectableCustomerIds();
        $this->assertCount(1, $customerIds);
        $this->assertEquals(1, $customerIds[0]);
    }
}
