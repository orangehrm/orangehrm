<?php

namespace OrangeHRM\Tests\Time\Dao;


use OrangeHRM\Entity\Customer;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Time\Dao\CustomerDao;

class CustomerDaoTest extends KernelTestCase {

    private CustomerDao $customerDao;
    protected string $fixture;


    /**
     * Set up method
     * @throws \Exception
     */
    protected function setUp(): void
    {
        $this->customerDao = new CustomerDao();

    }

    public function testAddCustomer(): void
    {
        $customer = new Customer();
        $customer->setName('Customer 1');
        $customer->setDescription('Description 2');
        $this->customerDao->saveCustomer($customer);


        $lastCustomer = $this->getEntityManager()->getRepository(Customer::class)->find(1);
        dump($lastCustomer);

        $this->assertTrue($lastCustomer instanceof Customer);
        $this->assertEquals('Customer 1', $customer->getName());


    }

}