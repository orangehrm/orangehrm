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
 * Boston, MA 02110-1301, USA
 */

namespace OrangeHRM\Tests\Core\Dao;

use OrangeHRM\Config\Config;
use OrangeHRM\Core\Dao\ValidationUniqueDao;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Admin
 * @group Dao
 */
class ValidationUniqueDaoTest extends TestCase
{
    private ValidationUniqueDao $uniqueDao;
    protected string $fixture;

    protected function setUp(): void
    {
        $this->uniqueDao = new ValidationUniqueDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmCorePlugin/test/fixtures/ValidationUniqueDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testIsValueUniqueForEmployeeId(): void
    {
        $this->assertFalse(
            $this->uniqueDao->isValueUnique(
                "0001",
                'Employee',
                'employeeId',
                null,
                null,
                null,
                null,
            )
        );
        $this->assertTrue(
            $this->uniqueDao->isValueUnique(
                "0001",
                'Employee',
                'employeeId',
                1,
                'empNumber',
                null,
                null,
            )
        );
    }

    public function testIsValueUniqueForSystemUser(): void
    {
        $this->assertFalse(
            $this->uniqueDao->isValueUnique(
                "admin",
                'User',
                'userName',
                null,
                null,
                'deleted',
                'false',
            )
        );

        $this->assertTrue(
            $this->uniqueDao->isValueUnique(
                "admin",
                'User',
                'userName',
                1,
                null,
                'deleted',
                'false',
            )
        );

        $this->assertTrue(
            $this->uniqueDao->isValueUnique(
                "sharuka",
                'User',
                'userName',
                null,
                null,
                'deleted',
                'false',
            )
        );
    }

    public function testIsValueUniqueForEmailSubscriber(): void
    {
        $this->assertFalse(
            $this->uniqueDao->isValueUnique(
                "devi@admin.com",
                'EmailSubscriber',
                'email',
                null,
                null,
                'emailNotification',
                '1',
            )
        );

        $this->assertTrue(
            $this->uniqueDao->isValueUnique(
                "devi@admin.com",
                'EmailSubscriber',
                'email',
                1,
                null,
                'emailNotification',
                '1',
            )
        );

        $this->assertTrue(
            $this->uniqueDao->isValueUnique(
                "sharuka@admin.com",
                'EmailSubscriber',
                'email',
                null,
                null,
                'emailNotification',
                '3',
            )
        );
    }
}
