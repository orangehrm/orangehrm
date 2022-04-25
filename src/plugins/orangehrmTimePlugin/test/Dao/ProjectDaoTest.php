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

use DateTime;
use Exception;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\Project;
use OrangeHRM\Entity\ProjectAdmin;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;
use OrangeHRM\Time\Dao\ProjectDao;
use OrangeHRM\Time\Dto\ProjectActivityDetailedReportSearchFilterParams;
use OrangeHRM\Time\Dto\ProjectReportSearchFilterParams;
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

    public function testFilterProjectByCustomerOrProjectName(): void
    {
        $projectParamHolder = new ProjectSearchFilterParams();
        $projectParamHolder->setCustomerOrProjectName("Orange");
        $projects = $this->projectDao->getProjects($projectParamHolder);
        $this->assertCount(1, $projects);
        $this->assertInstanceOf(Project::class, $projects[0]);
        $this->assertEquals("Orange", $projects[0]->getCustomer()->getName());
        $this->assertEquals("project_03", $projects[0]->getName());
    }

    public function testFilterProjectByExcludeProjectIds(): void
    {
        $projectParamHolder = new ProjectSearchFilterParams();
        $projectParamHolder->setExcludeProjectIds([1]);
        $projects = $this->projectDao->getProjects($projectParamHolder);
        $this->assertCount(1, $projects);
        $this->assertInstanceOf(Project::class, $projects[0]);
        $this->assertEquals('project_02', $projects[0]->getName());
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
        $result = $this->projectDao->isProjectNameTaken('Project_03_updated', 1);
        $this->assertTrue($result);
    }

    public function testIsProjectAdmin(): void
    {
        $this->assertTrue($this->projectDao->isProjectAdmin(1));
        $this->assertTrue($this->projectDao->isProjectAdmin(3));
        $this->assertFalse($this->projectDao->isProjectAdmin(4)); // employee who don't have any project
        $this->assertFalse($this->projectDao->isProjectAdmin(5)); // employee who have only deleted project
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

    public function testGetProjectReportCriteriaListAndTotalHours(): void
    {
        $projectReportSearchFilterParams = new ProjectReportSearchFilterParams();
        $projectReportSearchFilterParams->setProjectId(1);
        $projectReportSearchFilterParams->setIncludeApproveTimesheet(
            ProjectReportSearchFilterParams::INCLUDE_TIMESHEET_ALL
        );
        $result = $this->projectDao->getProjectReportCriteriaList($projectReportSearchFilterParams);
        $totalHours = $this->projectDao->getTotalDurationForProjectReport($projectReportSearchFilterParams);
        $this->assertEquals("Debug", $result[0]['name']);
        $this->assertEquals("10800", $result[0]['totalDuration']);
        $this->assertEquals(24800, $totalHours);

        $projectReportSearchFilterParams = new ProjectReportSearchFilterParams();
        $projectReportSearchFilterParams->setProjectId(1);
        $projectReportSearchFilterParams->setFromDate(new DateTime("2011-01-01"));
        $result = $this->projectDao->getProjectReportCriteriaList($projectReportSearchFilterParams);
        $totalHours = $this->projectDao->getTotalDurationForProjectReport($projectReportSearchFilterParams);
        $this->assertEquals("QA", $result[1]['name']);
        $this->assertEquals("7000", $result[1]['totalDuration']);
        $this->assertEquals(24800, $totalHours);

        $projectReportSearchFilterParams = new ProjectReportSearchFilterParams();
        $projectReportSearchFilterParams->setProjectId(1);
        $projectReportSearchFilterParams->setToDate(new DateTime("2011-12-31"));
        $result = $this->projectDao->getProjectReportCriteriaList($projectReportSearchFilterParams);
        $totalHours = $this->projectDao->getTotalDurationForProjectReport($projectReportSearchFilterParams);
        $this->assertEquals("TBS", $result[2]['name']);
        $this->assertEquals("7000", $result[2]['totalDuration']);
        $this->assertEquals(24800, $totalHours);

        $projectReportSearchFilterParams = new ProjectReportSearchFilterParams();
        $projectReportSearchFilterParams->setProjectId(1);
        $projectReportSearchFilterParams->setFromDate(new DateTime("2011-01-01"));
        $projectReportSearchFilterParams->setToDate(new DateTime("2011-12-31"));
        $result = $this->projectDao->getProjectReportCriteriaList($projectReportSearchFilterParams);
        $totalHours = $this->projectDao->getTotalDurationForProjectReport($projectReportSearchFilterParams);
        $this->assertCount(3, $result);
        $this->assertEquals(24800, $totalHours);

        $projectReportSearchFilterParams = new ProjectReportSearchFilterParams();
        $projectReportSearchFilterParams->setProjectId(1);
        $projectReportSearchFilterParams->setFromDate(new DateTime("2011-01-01"));
        $projectReportSearchFilterParams->setToDate(new DateTime("2011-12-31"));
        $projectReportSearchFilterParams->setIncludeApproveTimesheet(
            ProjectReportSearchFilterParams::INCLUDE_TIMESHEET_ONLY_APPROVED
        );
        $result = $this->projectDao->getProjectReportCriteriaList($projectReportSearchFilterParams);
        $totalHours = $this->projectDao->getTotalDurationForProjectReport($projectReportSearchFilterParams);
        $this->assertCount(0, $result);
        $this->assertEquals(0, $totalHours);
    }

    public function testGetProjectReportCriteriaListCount(): void
    {
        $projectReportSearchFilterParams = new ProjectReportSearchFilterParams();
        $projectReportSearchFilterParams->setProjectId(1);
        $projectReportSearchFilterParams->setFromDate(new DateTime("2011-01-01"));
        $projectReportSearchFilterParams->setToDate(new DateTime("2011-12-31"));
        $projectReportSearchFilterParams->setIncludeApproveTimesheet(
            ProjectReportSearchFilterParams::INCLUDE_TIMESHEET_ONLY_APPROVED
        );
        $result = $this->projectDao->getProjectReportCriteriaListCount($projectReportSearchFilterParams);
        $this->assertEquals(0, $result);
    }

    public function testGetAccessibleEmpNumbersForProjectAdmin(): void
    {
        $result = $this->projectDao->getAccessibleEmpNumbersForProjectAdmin(1);
        $this->assertEquals([1, 2, 3], $result);
        $result = $this->projectDao->getAccessibleEmpNumbersForProjectAdmin(2);
        $this->assertEquals([1, 2, 3], $result);
        $result = $this->projectDao->getAccessibleEmpNumbersForProjectAdmin(6);
        $this->assertEquals([3, 6], $result);
        $result = $this->projectDao->getAccessibleEmpNumbersForProjectAdmin(4);
        $this->assertEmpty($result);
        $result = $this->projectDao->getAccessibleEmpNumbersForProjectAdmin(5);
        $this->assertEmpty($result);
        $result = $this->projectDao->getAccessibleEmpNumbersForProjectAdmin(100);
        $this->assertEmpty($result);
        $result = $this->projectDao->getAccessibleEmpNumbersForProjectAdmin(null);
        $this->assertEmpty($result);
    }

    public function testGetProjectActivityDetailedReportCriteriaListAndTotalHours(): void
    {
        $projectActivityDetailedReportSearchFilterParams = new ProjectActivityDetailedReportSearchFilterParams();
        $projectActivityDetailedReportSearchFilterParams->setProjectId(1);
        $projectActivityDetailedReportSearchFilterParams->setProjectActivityId(1);
        $projectActivityDetailedReportSearchFilterParams->setIncludeApproveTimesheet(
            ProjectReportSearchFilterParams::INCLUDE_TIMESHEET_ALL
        );
        $result = $this->projectDao->getProjectActivityDetailedReportCriteriaList(
            $projectActivityDetailedReportSearchFilterParams
        );
        $totalHours = $this->projectDao->getTotalDurationForProjectActivityDetailedReport(
            $projectActivityDetailedReportSearchFilterParams
        );
        $this->assertEquals("Kayla Abbey", $result[0]['fullName']);
        $this->assertEquals("10800", $result[0]['totalDuration']);
        $this->assertEquals(10800, $totalHours);

        $projectActivityDetailedReportSearchFilterParams = new ProjectActivityDetailedReportSearchFilterParams();
        $projectActivityDetailedReportSearchFilterParams->setProjectId(1);
        $projectActivityDetailedReportSearchFilterParams->setProjectActivityId(1);
        $projectActivityDetailedReportSearchFilterParams->setFromDate(new DateTime("2011-01-01"));
        $result = $this->projectDao->getProjectActivityDetailedReportCriteriaList(
            $projectActivityDetailedReportSearchFilterParams
        );
        $totalHours = $this->projectDao->getTotalDurationForProjectActivityDetailedReport(
            $projectActivityDetailedReportSearchFilterParams
        );
        $this->assertEquals("Kayla Abbey", $result[0]['fullName']);
        $this->assertEquals("10800", $result[0]['totalDuration']);
        $this->assertEquals(10800, $totalHours);


        $projectActivityDetailedReportSearchFilterParams = new ProjectActivityDetailedReportSearchFilterParams();
        $projectActivityDetailedReportSearchFilterParams->setProjectId(1);
        $projectActivityDetailedReportSearchFilterParams->setProjectActivityId(1);
        $projectActivityDetailedReportSearchFilterParams->setToDate(new DateTime("2011-12-31"));
        $result = $this->projectDao->getProjectActivityDetailedReportCriteriaList(
            $projectActivityDetailedReportSearchFilterParams
        );
        $totalHours = $this->projectDao->getTotalDurationForProjectActivityDetailedReport(
            $projectActivityDetailedReportSearchFilterParams
        );
        $this->assertEquals("Kayla Abbey", $result[0]['fullName']);
        $this->assertEquals("10800", $result[0]['totalDuration']);
        $this->assertEquals(10800, $totalHours);

        $projectActivityDetailedReportSearchFilterParams = new ProjectActivityDetailedReportSearchFilterParams();
        $projectActivityDetailedReportSearchFilterParams->setProjectId(1);
        $projectActivityDetailedReportSearchFilterParams->setProjectActivityId(2);
        $projectActivityDetailedReportSearchFilterParams->setFromDate(new DateTime("2011-01-01"));
        $projectActivityDetailedReportSearchFilterParams->setToDate(new DateTime("2011-12-31"));
        $result = $this->projectDao->getProjectActivityDetailedReportCriteriaList(
            $projectActivityDetailedReportSearchFilterParams
        );
        $totalHours = $this->projectDao->getTotalDurationForProjectActivityDetailedReport(
            $projectActivityDetailedReportSearchFilterParams
        );
        $this->assertCount(1, $result);
        $this->assertEquals(7000, $totalHours);

        $projectActivityDetailedReportSearchFilterParams = new ProjectActivityDetailedReportSearchFilterParams();
        $projectActivityDetailedReportSearchFilterParams->setProjectId(1);
        $projectActivityDetailedReportSearchFilterParams->setProjectActivityId(2);
        $projectActivityDetailedReportSearchFilterParams->setFromDate(new DateTime("2011-01-01"));
        $projectActivityDetailedReportSearchFilterParams->setToDate(new DateTime("2011-12-31"));
        $projectActivityDetailedReportSearchFilterParams->setIncludeApproveTimesheet(
            ProjectReportSearchFilterParams::INCLUDE_TIMESHEET_ONLY_APPROVED
        );
        $result = $this->projectDao->getProjectActivityDetailedReportCriteriaList(
            $projectActivityDetailedReportSearchFilterParams
        );
        $totalHours = $this->projectDao->getTotalDurationForProjectActivityDetailedReport(
            $projectActivityDetailedReportSearchFilterParams
        );
        $this->assertCount(0, $result);
        $this->assertEquals(0, $totalHours);
    }
}
