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

namespace OrangeHRM\Tests\Admin\Dao;

use Exception;
use OrangeHRM\Admin\Dao\WorkShiftDao;
use OrangeHRM\Admin\Dto\WorkShiftSearchFilterParams;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\WorkShift;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Admin
 * @group Dao
 */
class WorkShiftDaoTest extends TestCase
{
    /**
     * @var WorkShiftDao
     */
    private WorkShiftDao $workShiftDao;

    /**
     * @var string
     */
    protected string $fixture;

    /**
     * Set up method
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->workShiftDao = new WorkShiftDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/WorkShiftDao.yaml';
        TestDataService::populate($this->fixture);
    }

    public function testGetSkillById(): void
    {
        $workShift = $this->workShiftDao->getWorkShiftById(1);
        $this->assertEquals('Shift 1', $workShift->getName());
        $this->assertEquals(8, $workShift->getHoursPerDay());
        $this->assertEquals('07:00:00', $workShift->getStartTime()->format('H:i:s'));
        $this->assertEquals('15:00:00', $workShift->getEndTime()->format('H:i:s'));
    }

    public function testGetNationalityList(): void
    {
        $workShiftSearchFilterParams = new WorkShiftSearchFilterParams();
        $result = $this->workShiftDao->getWorkShiftList($workShiftSearchFilterParams);
        $this->assertCount(2, $result);
        $this->assertTrue($result[0] instanceof WorkShift);
    }
}
