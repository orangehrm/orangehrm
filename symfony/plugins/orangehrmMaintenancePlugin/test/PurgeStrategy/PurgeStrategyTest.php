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

namespace OrangeHRM\Tests\Maintenance\PurgeStrategy;

use OrangeHRM\Config\Config;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Maintenance\Dto\InfoArray;
use OrangeHRM\Maintenance\PurgeStrategy\PurgeStrategy;
use OrangeHRM\Maintenance\Service\PurgeEmployeeService;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

class PurgeStrategyTest extends TestCase
{
    use EntityManagerHelperTrait;

    private PurgeStrategy $purgeStrategyMock;
    protected string $fixture;

    protected function setUp(): void
    {
        $entityClassName = 'Employee';
        $strategyInfoArray = [
            'match_by' => [
                ['match' => 'empNumber', 'join' => 'AnotherEntity']
            ],
            'parameters' => [
                ['field' => 'firstName', 'class' => 'FormatWithPurgeStringTest'],
                ['field' => 'lastName', 'class' => 'FormatWithPurgeStringTest']
            ]
        ];
        $infoArray = new InfoArray($strategyInfoArray);

        $this->purgeStrategyMock = $this->getMockForAbstractClass(
            PurgeStrategy::class,
            [$entityClassName, $infoArray]
        );
        $this->fixture = Config::get(
            Config::PLUGINS_DIR
        ) . '/orangehrmMaintenancePlugin/test/fixtures/PurgeStrategy.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetPurgeEmployeeService(): void
    {
        $purgeEmployeeService = $this->purgeStrategyMock->getPurgeEmployeeService();

        $this->assertInstanceOf(PurgeEmployeeService::class, $purgeEmployeeService);
    }

    public function testGetMatchByValues(): void
    {
        $result = $this->purgeStrategyMock->getMatchByValues(1);
        $expected = [
            'empNumber' => 1,
            'join' => 'AnotherEntity'
        ];

        $this->assertEquals($expected, $result);
    }

    public function testGetPurgeRecords(): void
    {
        $matchByValues = ['empNumber' => 1];
        $table = 'Employee';
        $purgeRecords = $this->invokeProtectedMethodOnMock(
            PurgeStrategy::class,
            $this->purgeStrategyMock,
            'getEntityRecords',
            [$matchByValues, $table]
        );
        $expectedRecord = $this->getRepository(Employee::class)->find(1);

        $this->assertEquals($expectedRecord, $purgeRecords[0]);
    }

    public function testGetParameters(): void
    {
        $result = $this->purgeStrategyMock->getParameters();
        $expected = [
            ['field' => 'firstName', 'class' => 'FormatWithPurgeStringTest'],
            ['field' => 'lastName', 'class' => 'FormatWithPurgeStringTest']
        ];

        $this->assertEquals($expected, $result);
    }

    public function testGetEntityClassName(): void
    {
        $result = $this->purgeStrategyMock->getEntityClassName();
        $this->assertEquals('Employee', $result);
    }

    public function testGetEntityFieldMap(): void
    {
        $result = $this->purgeStrategyMock->getEntityFieldMap();
        $expected = ['match' => 'empNumber', 'join' => 'AnotherEntity'];

        $this->assertEquals($expected, $result);
    }
}
