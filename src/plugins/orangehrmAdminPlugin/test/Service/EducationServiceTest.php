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

namespace OrangeHRM\Tests\Admin\Service;

use OrangeHRM\Admin\Dao\EducationDao;
use OrangeHRM\Admin\Dto\QualificationEducationSearchFilterParams;
use OrangeHRM\Admin\Service\EducationService;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\Education;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Admin
 * @group Service
 */
class EducationServiceTest extends TestCase
{
    private EducationService $educationService;
    private string $fixture;

    public function testGetEducationList(): void
    {
        $educationList = TestDataService::loadObjectList('Education', $this->fixture, 'Education');
        $educationFilterParams = new QualificationEducationSearchFilterParams();
        $educationDao = $this->getMockBuilder(EducationDao::class)->getMock();
        $educationDao->expects($this->once())
            ->method('getEducationList')
            ->with($educationFilterParams)
            ->will($this->returnValue($educationList));
        $this->educationService->setEducationDao($educationDao);
        $result = $this->educationService->getEducationList($educationFilterParams);
        $this->assertCount(3, $result);
        $this->assertTrue($result[0] instanceof Education);
    }

    public function testDeleteEducations(): void
    {
        $toBeDeletedEducationIds = [1, 2];
        $educationDao = $this->getMockBuilder(EducationDao::class)->getMock();
        $educationDao->expects($this->once())
            ->method('deleteEducations')
            ->with($toBeDeletedEducationIds)
            ->will($this->returnValue(2));
        $this->educationService->setEducationDao($educationDao);
        $result = $this->educationService->deleteEducations($toBeDeletedEducationIds);
        $this->assertEquals(2, $result);
    }

    public function testGetEducationById(): void
    {
        $educationList = TestDataService::loadObjectList('Education', $this->fixture, 'Education');
        $educationDao = $this->getMockBuilder(EducationDao::class)->getMock();
        $educationDao->expects($this->once())
            ->method('getEducationById')
            ->with(1)
            ->will($this->returnValue($educationList[0]));
        $this->educationService->setEducationDao($educationDao);
        $result = $this->educationService->getEducationById(1);
        $this->assertEquals($educationList[0], $result);
    }

    public function testGetEducationByName(): void
    {
        $educationList = TestDataService::loadObjectList('Education', $this->fixture, 'Education');
        $educationDao = $this->getMockBuilder(EducationDao::class)->getMock();
        $educationDao->expects($this->once())
            ->method('getEducationByName')
            ->with(1)
            ->will($this->returnValue($educationList[0]));
        $this->educationService->setEducationDao($educationDao);
        $result = $this->educationService->getEducationByName(1);
        $this->assertEquals($result, $educationList[0]);
    }

    /**
     * Set up method
     */
    protected function setUp(): void
    {
        $this->educationService = new EducationService();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/EducationDao.yml';
        TestDataService::populate($this->fixture);
    }
}
