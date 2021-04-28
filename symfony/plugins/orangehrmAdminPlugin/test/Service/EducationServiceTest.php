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

use OrangeHRM\Admin\Dao\EducationDao;
use OrangeHRM\Admin\Service\EducationService;
use OrangeHRM\Config\Config;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Admin
 */
class EducationServiceTest extends TestCase
{

    private $educationService;
    private $fixture;

    /**
     * Set up method
     */
    protected function setUp(): void
    {
        $this->educationService = new EducationService();
        $this->fixture = Config::get('ohrm_plugins_dir') . '/orangehrmAdminPlugin/test/fixtures/EducationDao.yml'; //replace 'sf_plugins_dir'
        TestDataService::populate($this->fixture);
    }

    public function testGetEducationList(): void
    {
        $educationList = TestDataService::loadObjectList('Education', $this->fixture, 'Education');

        $educationDao = $this->getMockBuilder(EducationDao::class)->getMock();
        $educationDao->expects($this->once())
            ->method('getEducationList')
            ->will($this->returnValue($educationList));

        $this->educationService->setEducationDao($educationDao);

        $result = $this->educationService->getEducationList();
        $this->assertEquals($result, $educationList);
    }

    public function testDeleteEducations(): void
    {
        $toBeDeletedEducationIds = array(1, 2);

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
        $this->assertEquals($result, $educationList[0]);
    }

    // new test function

    public function testGetEducationByName(): void
    {
        $educationList = TestDataService::loadObjectList('Education', $this->fixture, 'Education');

        $educationDao = $this->getMockBuilder(EducationDao::class)->getMock();
        $educationDao->expects($this->once())
            ->method('getEducationByName')
            ->with(1)
            ->will($this->returnValue($educationList[0]));

        $this->educationService->setEducationDao($educationDao);

        $result = $this->educationService->getEducationByName(1); //why it should be 1 shouldn't it be name
        $this->assertEquals($result, $educationList[0]);
    }


}
