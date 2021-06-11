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

namespace OrangeHRM\Tests\Pim\Dao;

use OrangeHRM\Admin\Dto\EmployeeSalarySearchFilterParams;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\CurrencyType;
use OrangeHRM\Entity\EmpDirectDebit;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmployeeSalary;
use OrangeHRM\ORM\ListSorter;
use OrangeHRM\Pim\Dao\EmployeeSalaryDao;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 * @group Dao
 */
class EmployeeSalaryDaoTest extends TestCase
{
    private EmployeeSalaryDao $employeeSalaryDao;
    private string $fixture;

    protected function setUp(): void
    {
        $this->employeeSalaryDao = new EmployeeSalaryDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmPimPlugin/test/fixtures/EmployeeSalaryDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testSaveEmployeeSalary(): void
    {
        $employee = $this->getEntityReference(Employee::class, 2);
        $currencyType = $this->getEntityReference(CurrencyType::class, 'USD');
        $employeeSalary = new EmployeeSalary();
        $employeeSalary->setEmployee($employee);
        $employeeSalary->setSalaryName('Main');
        $employeeSalary->setCurrencyType($currencyType);
        $employeeSalary->setAmount('1000');
        $this->employeeSalaryDao->saveEmployeeSalary($employeeSalary);

        /** @var EmployeeSalary $resultEmployeeSalary */
        $resultEmployeeSalary = TestDataService::fetchLastInsertedRecord(EmployeeSalary::class, 'id');
        $this->assertEquals('Main', $resultEmployeeSalary->getSalaryName());
        $this->assertEquals('Ashley', $resultEmployeeSalary->getEmployee()->getFirstName());
        $this->assertEquals('1000', $resultEmployeeSalary->getAmount());
        $this->assertNull($resultEmployeeSalary->getDirectDebit());
    }

    public function testSaveEmployeeSalaryWithEmployeeDirectDebit(): void
    {
        $employee = $this->getEntityReference(Employee::class, 2);
        $currencyType = $this->getEntityReference(CurrencyType::class, 'USD');
        $employeeSalary = new EmployeeSalary();
        $employeeSalary->setEmployee($employee);
        $employeeSalary->setSalaryName('Main');
        $employeeSalary->setCurrencyType($currencyType);
        $employeeSalary->setAmount('1000');

        $empDirectDebit = new EmpDirectDebit();
        $empDirectDebit->setAccount('11111111');
        $empDirectDebit->setAmount('1000');
        $empDirectDebit->setAccountType(EmpDirectDebit::ACCOUNT_TYPE_SAVINGS);
        $empDirectDebit->setRoutingNumber(1111);
        $empDirectDebit->setSalary($employeeSalary);
        $employeeSalary->setDirectDebit($empDirectDebit);
        $this->employeeSalaryDao->saveEmployeeSalary($employeeSalary);

        $this->getEntityManager()->clear(EmpDirectDebit::class);
        /** @var EmployeeSalary $resultEmployeeSalary */
        $resultEmployeeSalary = TestDataService::fetchLastInsertedRecord(EmployeeSalary::class, 'id');
        $this->assertEquals('Main', $resultEmployeeSalary->getSalaryName());
        $this->assertEquals('Ashley', $resultEmployeeSalary->getEmployee()->getFirstName());
        $this->assertEquals('1000', $resultEmployeeSalary->getAmount());
        $this->assertEquals('11111111', $resultEmployeeSalary->getDirectDebit()->getAccount());
        $this->assertEquals('1000.00', $resultEmployeeSalary->getDirectDebit()->getAmount());
        $this->assertEquals(
            EmpDirectDebit::ACCOUNT_TYPE_SAVINGS,
            $resultEmployeeSalary->getDirectDebit()->getAccountType()
        );
        $this->assertEquals(1111, $resultEmployeeSalary->getDirectDebit()->getRoutingNumber());
    }

    public function testDeleteEmployeeSalaries(): void
    {
        $this->assertEquals(0, $this->employeeSalaryDao->deleteEmployeeSalaries(2, [1]));
        $this->assertEquals(1, $this->employeeSalaryDao->deleteEmployeeSalaries(1, [1]));
        $this->assertNull($this->getEntityManager()->getRepository(EmployeeSalary::class)->find(1));
        $this->assertTrue(
            $this->getEntityManager()->getRepository(EmployeeSalary::class)->find(2) instanceof EmployeeSalary
        );
        $this->assertTrue(
            $this->getEntityManager()->getRepository(EmpDirectDebit::class)->find(1) instanceof EmpDirectDebit
        );
    }

    public function testDeleteEmployeeSalariesBulk(): void
    {
        $this->assertEquals(2, $this->employeeSalaryDao->deleteEmployeeSalaries(1, [1, 2]));
        $this->assertNull($this->getEntityManager()->getRepository(EmployeeSalary::class)->find(1));
        $this->assertNull($this->getEntityManager()->getRepository(EmployeeSalary::class)->find(2));
        $this->assertNull($this->getEntityManager()->getRepository(EmpDirectDebit::class)->find(1));
    }

    public function testGetEmployeeSalary(): void
    {
        $salary = $this->employeeSalaryDao->getEmployeeSalary(1, 1);
        $this->assertEquals('Main Salary', $salary->getSalaryName());
        $this->assertEquals('Kayla', $salary->getEmployee()->getFirstName());
        $this->assertNull($salary->getDirectDebit());

        $salary = $this->employeeSalaryDao->getEmployeeSalary(2, 1);
        $this->assertNull($salary);

        $salary = $this->employeeSalaryDao->getEmployeeSalary(1, 2);
        $this->assertEquals('Allowance', $salary->getSalaryName());
        $this->assertEquals('Kayla', $salary->getEmployee()->getFirstName());
        $this->assertEquals('11111111', $salary->getDirectDebit()->getAccount());
        $this->assertEquals('1000.00', $salary->getDirectDebit()->getAmount());
    }

    public function testGetEmployeeSalaries(): void
    {
        $employeeSalarySearchFilterParams = new EmployeeSalarySearchFilterParams();
        $employeeSalarySearchFilterParams->setEmpNumber(1);
        $salaries = $this->employeeSalaryDao->getEmployeeSalaries($employeeSalarySearchFilterParams);
        $this->assertCount(2, $salaries);
        $this->assertEquals('Allowance', $salaries[0]->getSalaryName());

        $employeeSalarySearchFilterParams->setSortOrder(ListSorter::DESCENDING);
        $salaries = $this->employeeSalaryDao->getEmployeeSalaries($employeeSalarySearchFilterParams);
        $this->assertEquals('Main Salary', $salaries[0]->getSalaryName());

        $employeeSalarySearchFilterParams->setEmpNumber(2);
        $salaries = $this->employeeSalaryDao->getEmployeeSalaries($employeeSalarySearchFilterParams);
        $this->assertCount(0, $salaries);
    }

    public function testGetEmployeeSalariesCount(): void
    {
        $employeeSalarySearchFilterParams = new EmployeeSalarySearchFilterParams();
        $employeeSalarySearchFilterParams->setEmpNumber(1);
        $salariesCount = $this->employeeSalaryDao->getEmployeeSalariesCount($employeeSalarySearchFilterParams);
        $this->assertEquals(2, $salariesCount);

        $employeeSalarySearchFilterParams->setEmpNumber(2);
        $salariesCount = $this->employeeSalaryDao->getEmployeeSalariesCount($employeeSalarySearchFilterParams);
        $this->assertEquals(0, $salariesCount);
    }
}
