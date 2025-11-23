<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Tests\CorporateDirectory\Dao;

use Exception;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Exception\SearchParamException;
use OrangeHRM\CorporateDirectory\Dao\EmployeeDirectoryDao;
use OrangeHRM\CorporateDirectory\Dto\EmployeeDirectorySearchFilterParams;
use OrangeHRM\ORM\ListSorter;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Directory
 * @group Dao
 */
class EmployeeDirectoryDaoTest extends KernelTestCase
{
    /**
     * @var string
     */
    protected string $fixture;

    /**
     * @var EmployeeDirectoryDao
     */
    private EmployeeDirectoryDao $employeeDirectoryDao;

    /**
     * @throws SearchParamException
     */
    public function testGetEmployeeList(): void
    {
        $employeeDirectorySearchFilterParams = new EmployeeDirectorySearchFilterParams();
        $empList = $this->employeeDirectoryDao->getEmployeeList($employeeDirectorySearchFilterParams);
        $this->assertCount(6, $empList);
        $this->assertEquals('Saman', $empList[0]->getFirstName());

        $employeeDirectorySearchFilterParams->setLimit(2);
        $empList = $this->employeeDirectoryDao->getEmployeeList($employeeDirectorySearchFilterParams);
        $this->assertCount(2, $empList);
        $this->assertEquals('Chuck', $empList[1]->getFirstName());

        $employeeDirectorySearchFilterParams = new EmployeeDirectorySearchFilterParams();
        $employeeDirectorySearchFilterParams->setSortOrder(ListSorter::DESCENDING);
        $empList = $this->employeeDirectoryDao->getEmployeeList($employeeDirectorySearchFilterParams);
        $this->assertCount(6, $empList);
        $this->assertEquals('Ashan', $empList[0]->getFirstName());

        $employeeDirectorySearchFilterParams = new EmployeeDirectorySearchFilterParams();
        $employeeDirectorySearchFilterParams->setJobTitleId(2);
        $empList = $this->employeeDirectoryDao->getEmployeeList($employeeDirectorySearchFilterParams);
        $this->assertCount(2, $empList);
        $this->assertEquals('Software Engineer', $empList[0]->getJobTitle()->getJobTitleName());

        $employeeDirectorySearchFilterParams = new EmployeeDirectorySearchFilterParams();
        $employeeDirectorySearchFilterParams->setJobTitleId(1);
        $empList = $this->employeeDirectoryDao->getEmployeeList($employeeDirectorySearchFilterParams);
        $this->assertCount(1, $empList);

        $employeeDirectorySearchFilterParams = new EmployeeDirectorySearchFilterParams();
        $employeeDirectorySearchFilterParams->setNameOrId('Ashan');
        $empList = $this->employeeDirectoryDao->getEmployeeList($employeeDirectorySearchFilterParams);
        $this->assertCount(1, $empList);
    }

    /**
     * @return void
     */
    public function testGetEmployeeCount(): void
    {
        $employeeDirectorySearchFilterParams = new EmployeeDirectorySearchFilterParams();
        $empList = $this->employeeDirectoryDao->getEmployeeList($employeeDirectorySearchFilterParams);
        $this->assertCount(6, $empList);

        $employeeDirectorySearchFilterParams = new EmployeeDirectorySearchFilterParams();
        $employeeDirectorySearchFilterParams->setSortField('employee.firstName');
        $empList = $this->employeeDirectoryDao->getEmployeeList($employeeDirectorySearchFilterParams);
        $this->assertCount(6, $empList);

        $employeeDirectorySearchFilterParams = new EmployeeDirectorySearchFilterParams();
        $employeeDirectorySearchFilterParams->setJobTitleId(1);
        $empList = $this->employeeDirectoryDao->getEmployeeList($employeeDirectorySearchFilterParams);
        $this->assertCount(1, $empList);

        $employeeDirectorySearchFilterParams = new EmployeeDirectorySearchFilterParams();
        $employeeDirectorySearchFilterParams->setNameOrId('Ashan');
        $empList = $this->employeeDirectoryDao->getEmployeeList($employeeDirectorySearchFilterParams);
        $this->assertCount(1, $empList);
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->employeeDirectoryDao = new EmployeeDirectoryDao();
        $this->fixture = Config::get(
            Config::PLUGINS_DIR
        ) . '/orangehrmCorporateDirectoryPlugin/test/fixtures/EmployeeDirectoryDao.yml';
        TestDataService::populate($this->fixture);
    }
}
