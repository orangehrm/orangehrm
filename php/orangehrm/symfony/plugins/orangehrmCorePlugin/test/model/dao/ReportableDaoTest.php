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

/**
 * @group Core
 */
class ReportableDaoTest extends PHPUnit_Framework_TestCase {

    private $reportableDao;

    /* Set up method */

    protected function setUp() {

        $this->reportableDao = new ReportableDao();
        TestDataService::truncateTables(array('SelectedCompositeDisplayField','CompositeDisplayField','DisplayField', 'SelectedGroupField', 'GroupField', 'SelectedDisplayField', 'SelectedFilterField', 'FilterField', 'Report', 'ReportGroup', 'ProjectActivity', 'Project', 'Customer'));
        TestDataService::populate(sfConfig::get('sf_plugins_dir') . '/orangehrmCorePlugin/test/fixtures/ReportableDao.yml');
    }

    /* Test case for getSelectedFilterFields method */

    public function testGetSelectedFilterFields() {

        $reportId = 1;
        $results = $this->reportableDao->getSelectedFilterFields($reportId);

        $this->assertTrue($results[0] instanceOf SelectedFilterField);
        $this->assertEquals(2, count($results));
        $this->assertEquals(1, $results[0]->getFilterFieldId());
        $this->assertEquals('3000', $results[0]->getValue1());
    }

    /* Test case for getSelectedDisplayFields method */

    public function testGetSelectedDisplayFields() {

        $reportId = 1;
        $results = $this->reportableDao->getSelectedDisplayFields($reportId);

        $this->assertTrue($results[0] instanceOf SelectedDisplayField);
        $this->assertEquals(2, count($results));
        $this->assertEquals(2, $results[1]->getDisplayFieldId());
        $this->assertEquals(1, $results[1]->getReportId());
        $this->assertEquals(2, $results[1]->getId());
    }

    /* Test case for getSelectedCompositeDisplayFields method */

    public function testGetSelectedCompositeDisplayFields() {

        $reportId = 1;
        $results = $this->reportableDao->getSelectedCompositeDisplayFields($reportId);

        $this->assertTrue($results[0] instanceOf SelectedCompositeDisplayField);
        $this->assertEquals(2, count($results));
        $this->assertEquals(2, $results[1]->getCompositeDisplayFieldId());
        $this->assertEquals(1, $results[1]->getReportId());
        $this->assertEquals(2, $results[1]->getId());
    }

    /* Test case for getMetaDisplayFields method */

    public function testGetMetaDisplayFields() {

        $reportGroupId = 1;
        $results = $this->reportableDao->getMetaDisplayFields($reportGroupId);

        $this->assertTrue($results[0] instanceOf DisplayField);
        $this->assertEquals(2, count($results));
    }

    /* Test case for getMetaDisplayFields for non existing reportId method */

    public function testGetMetaDisplayFieldsNonExistingReportGroupId() {

        $reportGroupId = 111;
        $results = $this->reportableDao->getMetaDisplayFields($reportId);

        $this->assertEquals(0, count($results));
    }

    /* Test case for getReport method */

    public function testGetReport() {

        $reportId = 1;
        $result = $this->reportableDao->getReport($reportId);

        $this->assertTrue($result instanceof Report);
        $this->assertEquals('project report', $result->getName());
    }

    /* Test case for getReportGroup method */

    public function testGetReportGroup() {

        $reportGroupId = 1;
        $result = $this->reportableDao->getReportGroup($reportGroupId);

        $this->assertTrue($result instanceof ReportGroup);
        $this->assertEquals('timesheet', $result->getName());
    }

    /* Tests getGroupField method */

    public function testGetGroupField() {

        $groupFieldId = 1;

        $groupField = $this->reportableDao->getGroupField($groupFieldId);

        $this->assertTrue($groupField instanceof GroupField);
        $this->assertEquals(1, $groupField->getGroupFieldId());
        $this->assertEquals("activity id", $groupField->getName());
        $this->assertEquals("GROUP BY ohrm_project_activity.activity_id", $groupField->getGroupByClause());
    }

    /* Tests getGroupField method */

    public function testGetGroupFieldForNonExistingGroupField() {

        $groupFieldId = 34;

        $groupField = $this->reportableDao->getGroupField($groupFieldId);

        $this->assertEquals(null, $groupField);
    }

    /* Tests getSelectedGroupField method */

    public function testGetSelectedGroupField() {

        $reportId = 1;

        $selectedGroupField = $this->reportableDao->getSelectedGroupField($reportId);

        $this->assertTrue($selectedGroupField instanceof SelectedGroupField);
        $this->assertEquals(1, $selectedGroupField->getGroupFieldId());
        $this->assertEquals(1, $selectedGroupField->getSummaryDisplayFieldId());
        $this->assertEquals(1, $selectedGroupField->getReportId());
    }

    /* Tests getSelectedGroupField method */

    public function testGetSelectedGroupFieldForNonExistingSelectedGroupField() {

        $reportId = 43;

        $selectedGroupField = $this->reportableDao->getSelectedGroupField($reportId);

        $this->assertEquals(null, $selectedGroupField);
    }

    /* Test executeSql method */

    public function testExecuteSql() {

        $sql = "SELECT * FROM ohrm_report";

        $result = $this->reportableDao->executeSql($sql);

        $this->assertEquals(2, count($result));
        $this->assertEquals("project report", $result[0]["name"]);
        $this->assertEquals(2, $result[1]["report_id"]);
    }

    /* Test getFilterFieldById when the existing id is given as parameter method */

    public function testGetFilterFieldByIdWithExistingId() {

        $filterFieldId = 1;

        $filterField = $this->reportableDao->getFilterFieldById($filterFieldId);

        $this->assertTrue($filterField instanceof FilterField);
        $this->assertEquals(1, $filterField->getFilterFieldId());
        $this->assertEquals("project_name", $filterField->getName());
    }

    /* Test getFilterFieldById when the non existing id is given as parameter method */

    public function testGetFilterFieldByIdWithNonExistingId() {

        $filterFieldId = 130;

        $filterField = $this->reportableDao->getFilterFieldById($filterFieldId);

        $this->assertEquals(null, $filterField);
    }

    /* Test getProjectActivityByActivityId method */

    public function testGetProjectActivityByActivityId() {

        $activityId = 1;

        $activity = $this->reportableDao->getProjectActivityByActivityId($activityId);

        $this->assertTrue($activity instanceof ProjectActivity);
        $this->assertEquals(1, $activity->getActivityId());
        $this->assertEquals("Create Schema", $activity->getName());
    }

}
