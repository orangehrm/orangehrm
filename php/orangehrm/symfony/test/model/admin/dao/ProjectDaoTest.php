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
class ProjectDaoTest extends PHPUnit_Framework_TestCase {

    private $projectDao;
    /**
     * Set up method
     */
    protected function setUp() {

        $this->projectDao = new ProjectDao();
        TestDataService::truncateTables(array('ProjectAdmin', 'Employee', 'Project'));
        TestDataService::populate(sfConfig::get('sf_test_dir') . '/fixtures/admin/dao/ProjectDao.yml');
    }

    /* Tests getActiveProjectList method */

    public function testGetActiveProjectList() {

        $activeProjects = $this->projectDao->getActiveProjectList();

        $this->assertTrue($activeProjects[0] instanceof Project);
        $this->assertEquals(4, count($activeProjects));
        $this->assertEquals("RedHat", $activeProjects[2]->getName());
    }

    /* Tests getProjectsByProjectIds method */
    public function testGetActiveProjectsByProjectIds() {

        $projectIdArray = array(1, 3, 5, 7);
        $activeProjects = $this->projectDao->getProjectsByProjectIds($projectIdArray);

        $this->assertTrue($activeProjects[0] instanceof Project);
        $this->assertEquals(2, count($activeProjects));
        $this->assertEquals("NUS", $activeProjects[1]->getName());
    }

    /** Tests getAllProjectsByProjectIds method */
    public function testGetAllProjectsByProjectIds() {
        
        $projectIdArray = array(1, 4, 7);
        $activeProjects = $this->projectDao->getAllProjectsByProjectIds($projectIdArray);

        $this->assertTrue($activeProjects[0] instanceof Project);
        $this->assertEquals(3, count($activeProjects));
        $this->assertEquals("UOM", $activeProjects[2]->getName());
    }

    public function testGetProjectAdminRecordsByEmpNo() {

        $empNo = 1;
        $projectAdmin = $this->projectDao->getProjectAdminByEmpNumber($empNo);

        $this->assertTrue($projectAdmin[0] instanceof ProjectAdmin);
        $this->assertEquals(3, count($projectAdmin));
        $this->assertEquals(5, $projectAdmin[2]->getProjectId());
    }

}

