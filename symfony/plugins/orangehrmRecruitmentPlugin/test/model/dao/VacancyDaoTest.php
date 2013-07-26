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
 * @group Recruitment
 */
class VacancyDaoTest extends PHPUnit_Framework_TestCase {

    private $vacancyDao;
    protected $fixture;

    /**
     * Set up method
     */
    protected function setUp() {

        $this->vacancyDao = new VacancyDao();
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmRecruitmentPlugin/test/fixtures/CandidateDao.yml';
        TestDataService::populate($this->fixture);
    }

    /**
     * Testing getHiringManagerList
     */
    public function testGetHiringManagersListWithNullJobTilteAndNullVacancyId() {

        $hiringMangersList = $this->vacancyDao->getHiringManagersList("", "");

        // Expected (ordered by last name ASC)
        $expected = array(array('id' => 1, 'name' => "Kayla Abbey"),
            array('id' => 2, 'name' => "Ashley Abel"),
            array('id' => 4, 'name' => "Chaturanga Namal"),
            array('id' => 3, 'name' => "Renukshan Saputhanthri"));
        $this->assertEquals($expected, $hiringMangersList);
    }

    /**
     * Testing getHiringManagerList
     */
    public function testGetHiringManagersListWthNUllVacancyId() {

        $hiringMangersList = $this->vacancyDao->getHiringManagersList(2, "");
        $this->assertEquals($hiringMangersList, array(array('id' => 2, 'name' => "Ashley Abel"), array('id' => 3, 'name' => "Renukshan Saputhanthri")));
    }

    /**
     * Testing getHiringManagerList
     */
    public function testGetHiringManagersListWthNUllJobTitle() {

        $hiringMangersList = $this->vacancyDao->getHiringManagersList("", 1);
        $this->assertEquals($hiringMangersList, array(array('id' => 1, 'name' => "Kayla Abbey")));
    }

    /**
     * Testing getHiringManagerList
     */
    public function testGetHiringManagersListWithVacancyList() {
        $hiringMangersList = $this->vacancyDao->getHiringManagersList("", 1, array(5, 1, 4));
        $this->assertEquals($hiringMangersList, array(array('id' => 1, 'name' => "Kayla Abbey")));
    }

    /**
     * Testing getVacancyListForJobTitle
     */
    public function testGetVacancyListForJobTitle() {

        $jobTitle = 2;
        $allowedVacancyList = array(2, 3);
        $vacancyList = $this->vacancyDao->getVacancyListForJobTitle($jobTitle, $allowedVacancyList);
        $this->assertTrue($vacancyList[0] instanceof JobVacancy);
    }

    /**
     * Testing getVacancyListForJobTitle Hydrate array
     */
    public function testGetVacancyListForJobTitleHydrateMode() {

        $jobTitle = 2;
        $allowedVacancyList = array(2, 3);
        $expected = array(array('id' => 3, 'name' => 'Software Architect 2010', 'status' => 1),
            array('id' => 2, 'name' => 'Software Architect 2011', 'status' => 1)
        );
        $vacancyList = $this->vacancyDao->getVacancyListForJobTitle($jobTitle, $allowedVacancyList, true);
        $this->assertEquals($expected, $vacancyList);
    }

    /**
     * Testing getVacancyListForJobTitle Hydrate array
     */
    public function testGetVacancyListForNullJobTitleHydrateMode() {

        $jobTitle = '';
        $allowedVacancyList = array(1, 2, 3, 4);
        $expected = array(array('id' => 3, 'name' => 'Software Architect 2010', 'status' => 1),
            array('id' => 2, 'name' => 'Software Architect 2011', 'status' => 1),
            array('id' => 4, 'name' => 'Software Architect 2012', 'status' => 2),
            array('id' => 1, 'name' => 'Software Engineer 2011', 'status' => 1));

        $vacancyList = $this->vacancyDao->getVacancyListForJobTitle($jobTitle, $allowedVacancyList, true);
        $this->assertEquals($expected, $vacancyList);
    }

    /**
     * Testing getVacancyList for correct objects
     */
    public function testGetVacancyList() {

        $vacancyList = $this->vacancyDao->getVacancyList();
        $this->assertTrue($vacancyList[0] instanceof JobVacancy);
    }

    /**
     * Testing getPublishedVacancies()
     */
    public function testGetPublishedVacancies() {

        // Get Published, active objects from fixture

        $allVacancyList = TestDataService::loadObjectList('JobVacancy', $this->fixture, 'JobVacancy');
        $publishedActiveList = array();

        foreach ($allVacancyList as $vacancy) {
            if (($vacancy->getStatus() == JobVacancy::ACTIVE) &&
                    ($vacancy->getPublishedInFeed() == JobVacancy::PUBLISHED)) {
                $publishedActiveList[] = $vacancy;
            }
        }

        // Get Vacancy list and compare with fixture
        $vacancyList = $this->vacancyDao->getPublishedVacancies();

        $this->assertEquals(count($vacancyList), count($publishedActiveList));

        foreach ($vacancyList as $vacancy) {
            $this->assertEquals(JobVacancy::ACTIVE, $vacancy->getStatus());
            $this->assertTrue($vacancy->getPublishedInFeed());
        }
    }

    /**
     * Testing getVacancyList for correct number of objects
     */
    public function testGetVacancyListForCorrectNumberOfObjectsReturn() {

        $vacancyList = $this->vacancyDao->getVacancyList();
        $this->assertEquals(2, count($vacancyList));
    }

    /**
     * Testing getAllVacancies
     */
    public function testGetAllVacancies() {

        $vacancyList = $this->vacancyDao->getAllVacancies();
        $this->assertTrue($vacancyList[0] instanceof JobVacancy);
    }
    
    /**
     * Testing getVacancyPropertyList
     */
    public function testGetVacancyPropertyListByName() {
        $properties = array('name', 'id');
        $vacancyList = $this->vacancyDao->getVacancyPropertyList($properties, JobVacancy::ACTIVE);
        $this->assertEquals(3, count($vacancyList));
        
        $vacancyList = $this->vacancyDao->getVacancyPropertyList($properties, JobVacancy::ACTIVE);
        $this->assertEquals(3, $vacancyList[0]['id']);
        $this->assertEquals('Software Architect 2011', $vacancyList[1]['name']);
    }
    
    /**
     * Testing getVacancyPropertyList
     */
    public function testGetVacancyPropertyListByHiringmanagerId() {
        $properties = array('name', 'id', 'hiringManagerId');
        $vacancyList = $this->vacancyDao->getVacancyPropertyList($properties, JobVacancy::ACTIVE);
        $this->assertEquals(3, count($vacancyList));
        
        $vacancyList = $this->vacancyDao->getVacancyPropertyList($properties, JobVacancy::ACTIVE);
        $this->assertEquals(3, $vacancyList[0]['id']);
        $this->assertEquals('Software Architect 2011', $vacancyList[1]['name']);
        $this->assertEquals(1, $vacancyList[2]['hiringManagerId']);
    }

    /**
     * Testing getAllVacancies
     */
    public function testGetAllVacanciesWithStatus() {
        $vacancyList = $this->vacancyDao->getAllVacancies(JobVacancy::ACTIVE);
        $this->assertEquals(3, count($vacancyList));
    }

    /**
     * Testing getVacancyList
     */
    public function testSaveJobVacancy() {
        TestDataService::truncateSpecificTables(array('JobVacancy'));

        $jobVacancy = new JobVacancy();
        $jobVacancy->jobTitleCode = 2;
        $jobVacancy->name = "BA 2010";
        $jobVacancy->hiringManagerId = 2;
        $jobVacancy->noOfPositions = 2;
        $jobVacancy->description = "test";
        $jobVacancy->status = 1;
        $jobVacancy->definedTime = "2011-08-09 10:38:39";
        $jobVacancy->updatedTime = "2011-08-09 10:38:39";
        $result = $this->vacancyDao->saveJobVacancy($jobVacancy);
        $this->assertTrue($result);
    }

    public function testSearchVacancies() {

        $srchParams = array('jobTitle' => 2, 'jobVacancy' => 2, 'hiringManager' => 2, 'status' => 1, 'offset' => 0, 'noOfRecords' => 1);
        $vacancyList = TestDataService::fetchObject('JobVacancy', 2);
        $result = $this->vacancyDao->searchVacancies($srchParams);
        $this->assertEquals($vacancyList, $result[0]);
    }

    /**
     * Testing searchVacancies
     */
    public function testSearchVacanciesWithEmployeeFirstName() {

        $srchParams = array('jobTitle' => 2, 'jobVacancy' => 2, 'hiringManager' => 2, 'status' => 1, 'offset' => 0, 'noOfRecords' => 1, 'orderField' => 'e.emp_firstname');
        $vacancyList = TestDataService::fetchObject('JobVacancy', 2);
        $result = $this->vacancyDao->searchVacancies($srchParams);
        $this->assertEquals($vacancyList, $result[0]);
    }

    /**
     * Testing deleteVacancies true arguments
     */
    public function testDeleteVacanciesForTrue() {

        $vacancyIds = array(1, 3);
        $result = $this->vacancyDao->deleteVacancies($vacancyIds);
        $this->assertEquals(true, $result);

        $vacancyIds = array(2);
        $result = $this->vacancyDao->deleteVacancies($vacancyIds);
        $this->assertEquals(true, $result);
    }

    /**
     * Testing deleteVacancies false arguments
     */
    public function testDeleteVacanciesForFalse() {

        $vacancyIds = array(15);
        $result = $this->vacancyDao->deleteVacancies($vacancyIds);
        $this->assertEquals(false, $result);
    }

    public function testGetVacancyListForHiringManagerUserRole() {
        $result = $this->vacancyDao->getVacancyListForUserRole(HiringManagerUserRoleDecorator::HIRING_MANAGER, 2);
        $this->assertEquals(count($result), 1);
    }

    public function testGetVacancyListForInterviewerUserRole() {
        $result = $this->vacancyDao->getVacancyListForUserRole(InterviewerUserRoleDecorator::INTERVIEWER, 3);
        $this->assertEquals(count($result), 1);
    }

    public function testGetVacancyListForAdminUserRole() {
        $result = $this->vacancyDao->getVacancyListForUserRole(AdminUserRoleDecorator::ADMIN_USER, null);
        $this->assertEquals(count($result), 5);
    }

    public function testGetVacancyById() {
        $vacancyId = 1;
        $result = $this->vacancyDao->getVacancyById($vacancyId);
        $this->assertTrue($result instanceof JobVacancy);
        $this->assertEquals($result->getJobTitleCode(), 1);
        $this->assertEquals($result->getNoOfPositions(), 2);
        $this->assertEquals($result->getStatus(), 1);
    }

    public function testsSearchVacanciesCount() {

        $srchParams = array('jobTitle' => 1, 'jobVacancy' => 1, 'hiringManager' => 1, 'status' => 1);
        $result = $this->vacancyDao->searchVacanciesCount($srchParams);
        $this->assertEquals($result, 1);
    }
    
    public function testGetVacancyIdList() {
        $result = $this->vacancyDao->getVacancyIdList();
        $expected = array(1, 2, 3, 4, 5);
        sort($expected);        
        sort($result);

        $this->assertEquals(count($expected), count($result));
        $this->assertEquals($expected, $result);
    }
    
    public function testGetVacancyIdListForHiringManager() {
        $result = $this->vacancyDao->getVacancyIdListForHiringManager(1);
        $expected = array(1, 5);
        sort($expected);        
        sort($result);

        $this->assertEquals(count($expected), count($result));
        $this->assertEquals($expected, $result);        
    }
    
    public function testGetVacancyIdListForNotHiringManager() {
        $result = $this->vacancyDao->getVacancyIdListForHiringManager(5);
        $expected = array();

        $this->assertEquals(count($expected), count($result));
        $this->assertEquals($expected, $result);        
    }    

    public function testGetVacancyIdListForInterviewer() {
        $result = $this->vacancyDao->getVacancyIdListForInterviewer(3);
        $expected = array(1);
        sort($expected);        
        sort($result);

        $this->assertEquals(count($expected), count($result));
        $this->assertEquals($expected, $result);        
    }
}
