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

namespace OrangeHRM\Admin\Tests\Service;

use OrangeHRM\Admin\Dao\NationalityDao;
use OrangeHRM\Admin\Service\NationalityService;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\Nationality;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Admin
 * @group Service
 */
class NationalityServiceTest extends TestCase
{
    private NationalityService $nationalityService;
    private string $fixture;

    /**
     * Set up method
     */
    protected function setUp(): void
    {
        $this->nationalityService = new NationalityService();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/NationalityDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetNationalityList(): void
    {
        $nationalityList = TestDataService::loadObjectList(Nationality::class, $this->fixture, 'Nationality');

        $nationalityDao = $this->getMockBuilder(NationalityDao::class)
            ->onlyMethods(['getNationalityList'])
            ->getMock();
        $nationalityDao->expects($this->once())
            ->method('getNationalityList')
            ->will($this->returnValue($nationalityList));

        $this->nationalityService->setNationalityDao($nationalityDao);

        $result = $this->nationalityService->getNationalityList();
        $this->assertEquals($result, $nationalityList);
    }

    public function testGetNationalityById(): void
    {
        $nationalityList = TestDataService::loadObjectList(Nationality::class, $this->fixture, 'Nationality');

        $nationalityDao = $this->getMockBuilder(NationalityDao::class)
            ->onlyMethods(['getNationalityById'])
            ->getMock();
        $nationalityDao->expects($this->once())
            ->method('getNationalityById')
            ->with(1)
            ->will($this->returnValue($nationalityList[0]));

        $this->nationalityService->setNationalityDao($nationalityDao);

        $result = $this->nationalityService->getNationalityById(1);
        $this->assertEquals($result, $nationalityList[0]);
    }

    public function testDeleteNationalities(): void
    {
        $nationalityList = [1, 2, 3];

        $nationalityDao = $this->getMockBuilder(NationalityDao::class)
            ->onlyMethods(['deleteNationalities'])
            ->getMock();
        $nationalityDao->expects($this->once())
            ->method('deleteNationalities')
            ->with($nationalityList)
            ->will($this->returnValue(3));

        $this->nationalityService->setNationalityDao($nationalityDao);

        $result = $this->nationalityService->deleteNationalities($nationalityList);
        $this->assertEquals($result, 3);
    }
}
