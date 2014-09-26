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
class ProjectDaoTest extends PHPUnit_Framework_TestCase {

	private $projectDao;
	protected $fixture;

	/**
	 * Set up method
	 */
	protected function setUp() {

		$this->projectDao = new ProjectDao();
		$this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmAdminPlugin/test/fixtures/ProjectDao.yml';
		TestDataService::populate($this->fixture);
	}

	public function testSearchProjectsForNullArray() {
		$srchClues = array();
		$allowedProjectList = array(1, 2);
		$result = $this->projectDao->searchProjects($srchClues, $allowedProjectList);
		$this->assertEquals(2, count($result));
	}

	public function testSearchProjectsForProjectName() {
		$srchClues = array(
		    'project' => 'development'
		);
		$allowedProjectList = array(1);
		$result = $this->projectDao->searchProjects($srchClues, $allowedProjectList);
		$this->assertEquals(1, count($result));
		$this->assertEquals(1, $result[0]['projectId']);
	}

	public function testSearchProjectsForCustomerName() {
		$srchClues = array(
		    'customer' => 'Xavier'
		);
		$allowedProjectList = array(1, 4);
		$result = $this->projectDao->searchProjects($srchClues, $allowedProjectList);
		$this->assertEquals(2, count($result));
		$this->assertEquals('Xavier', $result[0]['customerName']);
	}

	public function testSearchProjectsForProjectAdmin() {
		$srchClues = array(
		    'projectAdmin' => 'Kayla Abbey'
		);
		$allowedProjectList = array(1);
		$result = $this->projectDao->searchProjects($srchClues, $allowedProjectList);
		$this->assertEquals(count($result), 1);
		$this->assertEquals(1, $result[0]['projectId']);
	}

	public function testGetProjectCountWithActiveOnly() {
		$result = $this->projectDao->getProjectCount();
		$this->assertEquals(3, $result);
	}
	
	public function testGetProjectCount() {
		$result = $this->projectDao->getProjectCount(false);
		$this->assertEquals(4, $result);
	}

	public function testDeleteProject() {
		$this->projectDao->deleteProject(1);
		$result = $this->projectDao->getProjectById(1);
		$this->assertEquals($result->getIsDeleted(), 1);
	}

	public function testGetProjectActivityById() {
		$result = $this->projectDao->getProjectActivityById(1);
		$this->assertEquals($result->getName(), 'project activity 1');
	}

	public function testGetProjectById() {
		$result = $this->projectDao->getProjectById(1);
		$this->assertEquals($result->getName(), 'development');
	}

	public function testGetAllActiveProjectsWithActiveOnly() {
		$result = $this->projectDao->getAllProjects();
		$this->assertEquals(3, count($result));
	}
	
	public function testGetAllActiveProjects() {
		$result = $this->projectDao->getAllProjects(false);
		$this->assertEquals(4, count($result));
	}

//	public function testGetActivityListByProjectId() {
//		$result = $this->projectDao->getActivityListByProjectId(1);
//		$this->assertEquals(count($result), 2);
//		$this->assertEquals($result[0], 'project activity 1');
//	}

	public function testGetSearchProjectListCount() {
		$srchClues = array(
		    'projectAdmin' => 'Kayla Abbey'
		);
		$allowedProjectList = array(1);
		$result = $this->projectDao->getSearchProjectListCount($srchClues,$allowedProjectList);
		$this->assertEquals(1, $result );
	}

	public function testGetActiveProjectList() {

		$activeProjects = $this->projectDao->getActiveProjectList();
		$this->assertTrue($activeProjects[0] instanceof Project);
		$this->assertEquals(3, count($activeProjects));
	}
        
        public function testGetActiveProjectsOrderedByCustomer() {
            $sortedProjects = $this->projectDao->getActiveProjectsOrderedByCustomer();
            $this->assertEquals(3, count($sortedProjects));
            
            $this->assertTrue($sortedProjects[0] instanceof Project);
            $this->assertEquals(2, $sortedProjects[0]->getProjectId()); // Av Ltd - Engineering
           
            $this->assertTrue($sortedProjects[1] instanceof Project);
            $this->assertEquals(1, $sortedProjects[1]->getProjectId()); // Xavier - development
            
            $this->assertTrue($sortedProjects[2] instanceof Project);
            $this->assertEquals(4, $sortedProjects[2]->getProjectId()); // Xavier - Training            
        }

	public function testGetProjectsByProjectIdsWithActiveOnly() {

		$projectIdArray = array(1, 2);
		$activeProjects = $this->projectDao->getProjectsByProjectIds($projectIdArray);
		$this->assertTrue($activeProjects[0] instanceof Project);
		$this->assertEquals(2, count($activeProjects));
	}
	
	public function testGetActiveProjectsByProjectIds() {

		$projectIdArray = array(1, 2);
		$activeProjects = $this->projectDao->getProjectsByProjectIds($projectIdArray);
		$this->assertTrue($activeProjects[0] instanceof Project);
		$this->assertEquals(2, count($activeProjects));
	}

	public function testGetProjectAdminRecordsByEmpNo() {

		$empNo = 1;
		$projectAdmin = $this->projectDao->getProjectAdminByEmpNumber($empNo);
		$this->assertTrue($projectAdmin[0] instanceof ProjectAdmin);
		$this->assertEquals(1, count($projectAdmin));
	}
	
	public function testGetProjectAdminByProjectId() {

		$projectAdmin = $this->projectDao->getProjectAdminByProjectId(1);
		$this->assertTrue($projectAdmin[0] instanceof ProjectAdmin);
		$this->assertEquals(2, count($projectAdmin));
	}
	
	public function testDeleteProjectActivities() {

		$this->projectDao->deleteProjectActivities(1);
		$projectActivity = $this->projectDao->getProjectActivityById(1);
		$this->assertEquals($projectActivity->getIsDeleted(), 1);
	}

	public function testHasProjectGotTimesheetItems() {

		$result = $this->projectDao->hasProjectGotTimesheetItems(2);
		$this->assertTrue($result);
	}
	
	public function testHasActivityGotTimesheetItems() {

		$result = $this->projectDao->hasActivityGotTimesheetItems(1);
		$this->assertTrue($result);
	}
	
	public function testGetProjectsByCustomerId() {

		$result = $this->projectDao->getProjectsByCustomerId(1);
		$this->assertEquals(count($result), 2);
		$this->assertTrue($result[0] instanceof Project);
	}
	
	public function testGetProjectListForUserRole() {

		$result = $this->projectDao->getProjectListForUserRole(AdminUserRoleDecorator::ADMIN_USER, null);
		$this->assertEquals(4, count($result));
	}
    
    public function testGetCustomerIdListByProjectId() {
        
        $projectIdList = array(1, 2);
        $result = $this->projectDao->getCustomerIdListByProjectId($projectIdList);
        
        $this->assertEquals(1, $result[0]);
        $this->assertEquals(4, $result[1]);
        
        $result = $this->projectDao->getCustomerIdListByProjectId(null);
        $this->assertNull($result);
    }

    public function testGetProjectNameList() {
        
        $allowedProjectIdList = array(1, 2);
        $result = $this->projectDao->getProjectNameList($allowedProjectIdList);
        
        $this->assertEquals(2, count($result));
        $this->assertEquals(1, $result[0]['projectId']);
        $this->assertEquals('development', $result[0]['name']);
        $this->assertEquals(2, $result[1]['projectId']);
        
        $result = $this->projectDao->getProjectNameList(null);
        $this->assertNull($result);
    }
    
    
    public function testGetProjectActivityCount() {
        $count = $this->projectDao->getProjectActivityCount();
        $this->assertEquals(2, $count);
    }
    
    public function testGetProjectActivityCountWithDeleted() {
        $includeDeleted = true;
        $count = $this->projectDao->getProjectActivityCount($includeDeleted);
        $this->assertEquals(3, $count);
    }    
    
    public function testGetProjectActivityCountWithNoActivities() {
        $query = Doctrine_Query::create()
                ->delete()
                ->from('ProjectActivity');        
        $query->execute();
        $count = $this->projectDao->getProjectActivityCount();
        $this->assertEquals(0, $count);        
    }    
}
