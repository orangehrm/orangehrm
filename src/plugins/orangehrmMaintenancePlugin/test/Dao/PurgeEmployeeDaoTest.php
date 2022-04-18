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

namespace OrangeHRM\Tests\Maintenance\Dao;

use OrangeHRM\Config\Config;
use OrangeHRM\Maintenance\Dao\PurgeEmployeeDao;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Maintenance
 * @group Dao
 */
class PurgeEmployeeDaoTest extends TestCase
{
    private PurgeEmployeeDao $employeePurgeDao;
    protected string $fixture;

    protected function setUp(): void
    {
        $this->employeePurgeDao = new PurgeEmployeeDao();
        $this->fixture = Config::get(
            Config::PLUGINS_DIR
        ) . '/orangehrmMaintenancePlugin/test/fixtures/PurgeEmployeeDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testIsEmployeePurgeable(): void
    {
        $this->assertTrue($this->employeePurgeDao->isEmployeePurgeable(1));
        $this->assertTrue($this->employeePurgeDao->isEmployeePurgeable(2));
        $this->assertFalse($this->employeePurgeDao->isEmployeePurgeable(3));
        $this->assertFalse($this->employeePurgeDao->isEmployeePurgeable(4));
    }

    public function testExtractDataFromEmpNumber(): void
    {
        $table = 'Employee';
        $matchByValues = ['empNumber' => '1'];

        $data = $this->employeePurgeDao->extractDataFromEmpNumber($matchByValues, $table);
        $this->assertCount(1, $data);
        $this->assertEquals('Odis', $data[0]->getFirstName());
        $this->assertEquals('Alwin', $data[0]->getLastName());
        $this->assertEquals('Heath', $data[0]->getMiddleName());

        $table = 'LeaveRequestComment';
        $matchByValues = ['employee' => 1, 'join' => 'leaveRequest'];

        $data = $this->employeePurgeDao->extractDataFromEmpNumber($matchByValues, $table);
        $this->assertCount(3, $data);
        $this->assertEquals('employee 3 comment on emp 1 leave request', $data[0]->getComment());
        $this->assertEquals('employee 3 comment on emp 1 leave request', $data[1]->getComment());
        $this->assertEquals('employee 4 comment on emp 1 leave request', $data[2]->getComment());
    }
}
