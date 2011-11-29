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
require_once sfConfig::get('sf_test_dir') . '/util/TestDataService.php';

/**
 * @group Admin
 */
class JobTitleServiceTest extends PHPUnit_Framework_TestCase {

    private $JobTitleService;
    protected $fixture;

    /**
     * Set up method
     */
    protected function setUp() {
        $this->JobTitleService = new JobTitleService();
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmAdminPlugin/test/fixtures/JobTitleDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetJobTitleList() {
        
        $jobTitleList = TestDataService::loadObjectList('JobTitle', $this->fixture, 'JobTitle');

        $jobTitleDao = $this->getMock('JobTitleDao');

        $jobTitleDao->expects($this->once())
                ->method('getJobTitleList')
                ->with("", "", "")
                ->will($this->returnValue($jobTitleList));

        $this->JobTitleService->setJobTitleDao($jobTitleDao);
        $result = $this->JobTitleService->getJobTitleList("", "", "");
        $this->assertEquals($jobTitleList, $result);
    }

    public function testDeleteJobTitle() {

        $toBeDeletedJobTitleIds = array(1, 2);

        $jobTitleDao = $this->getMock('JobTitleDao');

        $jobTitleDao->expects($this->once())
                ->method('deleteJobTitle')
                ->with($toBeDeletedJobTitleIds)
                ->will($this->returnValue(2));

        $this->JobTitleService->setJobTitleDao($jobTitleDao);
        $result = $this->JobTitleService->deleteJobTitle($toBeDeletedJobTitleIds);
        $this->assertEquals(2, $result);
    }

    public function testGetJobTitleById() {

        $jobTitleList = TestDataService::loadObjectList('JobTitle', $this->fixture, 'JobTitle');
        $jobTitleDao = $this->getMock('JobTitleDao');

        $jobTitleDao->expects($this->once())
                ->method('getJobTitleById')
                ->with(1)
                ->will($this->returnValue($jobTitleList[0]));

        $this->JobTitleService->setJobTitleDao($jobTitleDao);
        $result = $this->JobTitleService->getJobTitleById(1);
        $this->assertEquals($jobTitleList[0], $result);
    }

}

