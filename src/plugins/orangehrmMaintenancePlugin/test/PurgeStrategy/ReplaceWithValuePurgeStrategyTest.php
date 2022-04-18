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
use OrangeHRM\Maintenance\FormatValueStrategy\ValueFormatter;
use OrangeHRM\Maintenance\PurgeStrategy\FormatValue\FormatWithEmptyString;
use OrangeHRM\Maintenance\PurgeStrategy\FormatValue\FormatWithNull;
use OrangeHRM\Maintenance\PurgeStrategy\FormatValue\FormatWithPurgeString;
use OrangeHRM\Maintenance\PurgeStrategy\FormatValue\FormatWithPurgeTime;
use OrangeHRM\Maintenance\PurgeStrategy\FormatValue\FormatWithZero;
use OrangeHRM\Maintenance\PurgeStrategy\ReplaceWithValuePurgeStrategy;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Maintenance
 */
class ReplaceWithValuePurgeStrategyTest extends TestCase
{
    use EntityManagerHelperTrait;

    private ReplaceWithValuePurgeStrategy $replaceWithValuePurgeStrategy;
    protected string $fixture;

    protected function setUp(): void
    {
        $entityClassName = 'Employee';
        $strategyInfoArray = [
            'match_by' => [
                ['match' => 'empNumber']
            ],
            'parameters' => [
                ['field' => 'firstName', 'class' => 'FormatWithPurgeString'],
                ['field' => 'lastName', 'class' => 'FormatWithPurgeString'],
                ['field' => 'middleName', 'class' => 'FormatWithEmptyString'],
                ['field' => 'smoker', 'class' => 'FormatWithZero'],
                ['field' => 'birthday', 'class' => 'FormatWithNull']
            ]
        ];
        $infoArray = new InfoArray($strategyInfoArray);

        $this->replaceWithValuePurgeStrategy = new ReplaceWithValuePurgeStrategy($entityClassName, $infoArray);
        $this->fixture = Config::get(
            Config::PLUGINS_DIR
        ) . '/orangehrmMaintenancePlugin/test/fixtures/ReplaceWithValuePurgeStrategy.yml';
        TestDataService::populate($this->fixture);
    }

    public function testPurge(): void
    {
        $this->replaceWithValuePurgeStrategy->purge(1);

        $employee = $this->getRepository(Employee::class)->find(1);
        $this->assertEquals('Purged', $employee->getFirstName());
        $this->assertEquals('Purged', $employee->getLastName());
        $this->assertEquals('', $employee->getMiddleName());
        $this->assertEquals(0, $employee->getSmoker());
        $this->assertNull($employee->getBirthday());
    }

    public function testPurgeRecord(): void
    {
        $purgeEmployee = $this->getRepository(Employee::class)->find(1);
        $this->replaceWithValuePurgeStrategy->purgeRecord($purgeEmployee);
        $this->getEntityManager()->flush();

        $employee = $this->getRepository(Employee::class)->find(1);
        $this->assertEquals('Purged', $employee->getFirstName());
        $this->assertEquals('Purged', $employee->getLastName());
        $this->assertEquals('', $employee->getMiddleName());
        $this->assertEquals(0, $employee->getSmoker());
        $this->assertNull($employee->getBirthday());
    }

    public function testGetReplaceStrategy(): void
    {
        $replaceStrategy = $this->invokeProtectedGetReplaceStrategy('FormatWithPurgeString');
        $this->assertInstanceOf(FormatWithPurgeString::class, $replaceStrategy);

        $replaceStrategy = $this->invokeProtectedGetReplaceStrategy('FormatWithEmptyString');
        $this->assertInstanceOf(FormatWithEmptyString::class, $replaceStrategy);

        $replaceStrategy = $this->invokeProtectedGetReplaceStrategy('FormatWithZero');
        $this->assertInstanceOf(FormatWithZero::class, $replaceStrategy);

        $replaceStrategy = $this->invokeProtectedGetReplaceStrategy('FormatWithNull');
        $this->assertInstanceOf(FormatWithNull::class, $replaceStrategy);

        $replaceStrategy = $this->invokeProtectedGetReplaceStrategy('FormatWithPurgeTime');
        $this->assertInstanceOf(FormatWithPurgeTime::class, $replaceStrategy);
    }

    private function invokeProtectedGetReplaceStrategy(string $strategy): ValueFormatter
    {
        return $this->invokeProtectedMethod(
            ReplaceWithValuePurgeStrategy::class,
            'getReplaceStrategy',
            [$strategy],
            ['', new InfoArray([])]
        );
    }
}
