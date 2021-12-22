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

namespace OrangeHRM\Tests\Time\Dao;

use Exception;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\Project;
use OrangeHRM\Entity\ProjectAdmin;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;
use OrangeHRM\Time\Dao\ProjectDao;
use OrangeHRM\Time\Dto\ProjectSearchFilterParams;

class ProjectDaoTest extends KernelTestCase
{
    private ProjectDao $projectDao;
    protected string $fixture;

    /**
     * Set up method
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->projectDao = new ProjectDao();
        TestDataService::truncateSpecificTables([ProjectAdmin::class]);
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmTimePlugin/test/fixtures/ProjectDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testAddProject(): void
    {
        $project = new Project();
        $project->setName('Project_04');
        $project->getDecorator()->setCustomerById(1);
        $project->setDescription('Project_04 sample description');
        $project->getDecorator()->setProjectAdminsByEmpNumbers([1, 2]);
        $project->setDeleted(false);
        $result = $this->projectDao->saveProject($project);
        $this->assertInstanceOf(Project::class, $result);
        $this->assertEquals('Project_04', $result->getName());
    }

    public function testGetProjects(): void
    {
        $projectParamHolder = new ProjectSearchFilterParams();
        $projects = $this->projectDao->getProjects($projectParamHolder);
        $this->assertCount(2, $projects);
        $this->assertInstanceOf(Project::class, $projects[1]);
    }

    public function testGetProjectById(): void
    {
        $project = $this->projectDao->getProjectById(1);
        $this->assertInstanceOf(Project::class, $project);
        $this->assertEquals('project_03', $project->getName());
        $this->assertFalse($project->isDeleted());
    }

    public function testUpdateProject(): void
    {
        $project = $this->projectDao->getProjectById(1);
        $project->setName('Project_03 updated');
        $project->getDecorator()->setCustomerById(2);
        $project->getDecorator()->setProjectAdminsByEmpNumbers([3]);
        $result = $this->projectDao->saveProject($project);
        $this->assertInstanceOf(Project::class, $result);
        $this->assertEquals('Project_03 updated', $project->getName());
        $this->assertEquals(3, $project->getProjectAdmins()->count());
    }

    public function testDeleteProjects(): void
    {
        $result = $this->projectDao->deleteProjects([1, 2]);
        $this->assertEquals(2, $result);
    }

    public function testIsProjectNameTaken(): void
    {
        $result = $this->projectDao->isProjectNameTaken('Project_03_updated');
        $this->assertTrue($result);
    }

    public function testIsProjectAdmin(): void
    {
        $this->assertTrue($this->projectDao->isProjectAdmin(1));
        $this->assertTrue($this->projectDao->isProjectAdmin(3));
        $this->assertFalse($this->projectDao->isProjectAdmin(4));
        $this->assertFalse($this->projectDao->isProjectAdmin(5));
        $this->assertFalse($this->projectDao->isProjectAdmin(100));
        $this->assertFalse($this->projectDao->isProjectAdmin(null));
    }

    public function testGetProjectIdList(): void
    {
        $result = $this->projectDao->getProjectIdList();
        $this->assertEquals([1, 2], $result);

        // With deleted
        $result = $this->projectDao->getProjectIdList(true);
        $this->assertEquals([1, 3, 2], $result);

        // Test for single result
        $this->getEntityManager()->remove($this->getEntityReference(Project::class, 2));
        $this->getEntityManager()->remove($this->getEntityReference(Project::class, 3));
        $this->getEntityManager()->flush();
        $result = $this->projectDao->getProjectIdList();
        $this->assertEquals([1], $result);

        // Test for empty result
        $this->getEntityManager()->remove($this->getEntityReference(Project::class, 1));
        $this->getEntityManager()->flush();
        $result = $this->projectDao->getProjectIdList();
        $this->assertEmpty($result);
    }

    public function testGetProjectIdListForProjectAdmin(): void
    {
        $result = $this->projectDao->getProjectIdListForProjectAdmin(1);
        $this->assertEquals([1], $result);

        $result = $this->projectDao->getProjectIdListForProjectAdmin(3);
        $this->assertEquals([1, 2], $result);

        // Employee not a project admin
        $result = $this->projectDao->getProjectIdListForProjectAdmin(4);
        $this->assertEmpty($result);

        // Employee not exists
        $result = $this->projectDao->getProjectIdListForProjectAdmin(100);
        $this->assertEmpty($result);

        // With deleted
        $result = $this->projectDao->getProjectIdListForProjectAdmin(1, true);
        $this->assertEquals([1, 3], $result);
    }
}
