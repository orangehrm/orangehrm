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

namespace OrangeHRM\Buzz\test\Dao;

use DateTime;
use Exception;
use OrangeHRM\Buzz\Dao\UpcomingAnniversariesDao;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Framework\Services;
use OrangeHRM\Buzz\Dto\EmployeeAnniversarySearchFilterParams;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Buzz
 * @group Dao
 */
class UpcomingAnniversariesDaoTest extends KernelTestCase
{
    private UpcomingAnniversariesDao $upcomingAnniversariesDao;

    /**
     * Set up method
     *
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->upcomingAnniversariesDao = new UpcomingAnniversariesDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR)
            . '/orangehrmBuzzPlugin/test/fixtures/UpcomingAnniversariesDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetUpcomingAnniversariesList(): void
    {
        $employeeAnniversarySearchFilterParams = new EmployeeAnniversarySearchFilterParams();

        $this->createKernelWithMockServices([
            Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
        ]);

        $employeeAnniversarySearchFilterParams->setThisYear('2022');
        $employeeAnniversarySearchFilterParams->setNextDate(new DateTime('2022-01-25'));
        $result = $this->upcomingAnniversariesDao->getUpcomingAnniversariesList($employeeAnniversarySearchFilterParams);
        $this->assertEquals('3', $result[0]->getEmpNumber());
        $this->assertCount(2, $result);

        $employeeAnniversarySearchFilterParams->setThisYear('2024');
        $employeeAnniversarySearchFilterParams->setNextDate(new DateTime('2024-02-29'));
        $result = $this->upcomingAnniversariesDao->getUpcomingAnniversariesList($employeeAnniversarySearchFilterParams);
        $this->assertEquals('Adalwin', $result[0]->getFirstName());
        $this->assertCount(3, $result);
    }

    public function testGetUpcomingAnniversariesCount(): void
    {
        $employeeAnniversarySearchFilterParams = new EmployeeAnniversarySearchFilterParams();

        $this->createKernelWithMockServices([
            Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
        ]);

        $employeeAnniversarySearchFilterParams->setThisYear('2022');
        $employeeAnniversarySearchFilterParams->setNextDate(new DateTime('2022-01-25'));
        $result = $this->upcomingAnniversariesDao->getUpcomingAnniversariesCount(
            $employeeAnniversarySearchFilterParams
        );
        $this->assertEquals(2, $result);
    }
}
