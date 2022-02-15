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

namespace OrangeHRM\Tests\Maintenance\Service;

use OrangeHRM\Config\Config;
use OrangeHRM\Maintenance\Dao\PurgeEmployeeDao;
use OrangeHRM\Maintenance\PurgeStrategy\PurgeStrategy;
use OrangeHRM\Maintenance\Service\PurgeEmployeeService;
use OrangeHRM\Pim\Service\EmployeeService;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Maintenance
 * @group Service
 */
class PurgeEmployeeServiceTest extends TestCase
{
    private PurgeEmployeeService $purgeEmployeeService;
    private EmployeeService $employeeService;
    protected string $fixture;

    protected function setUp(): void
    {
        $this->purgeEmployeeService = new PurgeEmployeeService();
        $this->employeeService = new EmployeeService();
        $this->fixture = Config::get(
                Config::PLUGINS_DIR
            ) . '/orangehrmMaintenancePlugin/test/fixtures/PurgeEmployeeService.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetEmployeePurgeDao(): void
    {
        $purgeEmployeeService = $this->getMockBuilder(PurgeEmployeeService::class)
            ->onlyMethods(['getPurgeEmployeeDao'])
            ->getMock();
        $purgeEmployeeService->expects($this->once())
            ->method('getPurgeEmployeeDao');

        $purgeEmployeeDao = $purgeEmployeeService->getPurgeEmployeeDao();
        $this->assertInstanceOf(PurgeEmployeeDao::class, $purgeEmployeeDao);
    }

    public function testPurgeEmployeeData(): void
    {
        $this->purgeEmployeeService->purgeEmployeeData(1);
        $purgedEmployee = $this->employeeService->getEmployeeDao()->getEmployeeByEmpNumber(1);

        $this->assertEquals("Purge", $purgedEmployee->getFirstName());
        $this->assertEquals("Purge", $purgedEmployee->getLastName());
        $this->assertEquals('', $purgedEmployee->getMiddleName());
    }

    public function testGetPurgeableEntities(): void
    {
        $purgeableEntities = $this->purgeEmployeeService->getPurgeableEntities('gdpr_purge_employee_strategy');
        $this->assertIsArray($purgeableEntities);
    }

    public function testGetPurgeStrategy(): void
    {
        $purgeableEntityClassName = 'Employee';
        $strategy = 'ReplaceWithValue';
        $strategyInfoArray = [
            'match_by' => [
                ['match' => 'empNumber']
            ],
            'parameters' => [
                ['field' => 'firstName', 'class' => 'FormatWithPurgeString'],
                ['field' => 'lastName', 'class' => 'FormatWithPurgeString'],
                ['field' => 'middleName', 'class' => 'FormatWithEmptyString'],
            ]
        ];

        $purgeStrategy = $this->purgeEmployeeService->getPurgeStrategy(
            $purgeableEntityClassName,
            $strategy,
            $strategyInfoArray
        );

        $this->assertInstanceOf(PurgeStrategy::class, $purgeStrategy);

        $matchByValues = $purgeStrategy->getMatchByValues(1);
        $this->assertCount(1, $purgeStrategy->getMatchByValues(1));
        $this->assertEquals('empNumber', key($matchByValues));

        $parameters = $purgeStrategy->getParameters();
        $this->assertCount(3, $parameters);
        $this->assertEquals('firstName', $parameters[0]['field']);
        $this->assertEquals('FormatWithEmptyString', $parameters[2]['class']);
    }
}
