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

use OrangeHRM\Admin\Dao\JobCategoryDao;
use OrangeHRM\Admin\Service\JobCategoryService;
use OrangeHRM\Config\Config;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;
use Exception;

/**
 * @group Admin
 * @group Service
 */
class JobCategoryServiceTest extends TestCase
{
    private JobCategoryService $jobCatService;
    private string $fixture;

    /**
     * Set up method
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->jobCatService = new JobCategoryService();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/JobCategoryDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetJobCategoryList(): void
    {
        $jobCatList = TestDataService::loadObjectList('JobCategory', $this->fixture, 'JobCategory');

        $jobCatDao = $this->getMockBuilder(JobCategoryDao::class)->getMock();
        $jobCatDao->expects($this->once())
            ->method('getJobCategoryList')
            ->will($this->returnValue($jobCatList));

        $this->jobCatService->setJobCategoryDao($jobCatDao);

        $result = $this->jobCatService->getJobCategoryList();
        $this->assertEquals($result, $jobCatList);
    }

    public function testGtJobCategoryById(): void
    {
        $jobCatList = TestDataService::loadObjectList('JobCategory', $this->fixture, 'JobCategory');

        $jobCatDao = $this->getMockBuilder(JobCategoryDao::class)->getMock();
        $jobCatDao->expects($this->once())
            ->method('getJobCategoryById')
            ->with(1)
            ->will($this->returnValue($jobCatList[0]));

        $this->jobCatService->setJobCategoryDao($jobCatDao);

        $result = $this->jobCatService->getJobCategoryById(1);
        $this->assertEquals($result, $jobCatList[0]);
    }
}
