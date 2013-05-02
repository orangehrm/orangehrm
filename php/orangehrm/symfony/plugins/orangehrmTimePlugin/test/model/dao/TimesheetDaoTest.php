<?php
/*
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
 * Description of TimesheetDaoTest
 *
 * @group Time
 */
class TimesheetDaoTest extends PHPUnit_Framework_TestCase {

    private $timesheetDao;

    /**
     * Set up method
     */
    protected function setUp() {

        $this->timesheetDao = new TimesheetDao();
        TestDataService::truncateSpecificTables(array('SystemUser', 'Employee'));        
        TestDataService::populate(sfConfig::get('sf_plugins_dir') . '/orangehrmTimePlugin/test/fixtures/TimesheetDao.yml');
    }

    /**
     * Testing getTimesheetById for already existing id's
     */
    public function testGetTimesheetByIdForExistingId() {

        $timesheet = $this->timesheetDao->getTimesheetById(1);

        $this->assertTrue($timesheet instanceof Timesheet);
        $this->assertEquals("CREATED", $timesheet->getState());
        $this->assertEquals("2011-04-18", $timesheet->getStartDate());
    }

    /**
     * Testing getTimesheetById for non existing id's
     */
    public function testGetTimesheetByIdForNonExistingId() {

        $timesheet = $this->timesheetDao->getTimesheetById(-1);
        $this->assertEquals(null, $timesheet);
    }

    /**
     * Testing getTimesheetByStartDate for already existing dates
     */
    public function testGetTimesheetByStartDateForExistingDate() {

        $timesheet = $this->timesheetDao->getTimesheetByStartDate('2011-04-19');

        $this->assertTrue($timesheet instanceof Timesheet);
        $this->assertEquals("SUPERVISOR APPROVED", $timesheet->getState());
        $this->assertEquals("7", $timesheet->getTimesheetId());
    }

    public function testGetTimesheetByStartDateForNonExistingDate() {

        $timesheet = $this->timesheetDao->getTimesheetByStartDate('2014-04-12');
        $this->assertNull($timesheet);
    }

    /**
     * Testing getTimesheetByStartDateAndEmployeeId method
     */
    public function testGetTimesheetByStartDateAndEmployeeId() {

        $timesheet = $this->timesheetDao->getTimesheetByStartDateAndEmployeeId("2011-04-18", 1);
        $this->assertTrue($timesheet instanceof Timesheet);
        $this->assertEquals("CREATED", $timesheet->getState());
        $this->assertEquals("1", $timesheet->getTimesheetId());
    }

    /**
     * Testing for saving a timesheet for newly made timesheets
     */
    public function testSaveTimesheetWithNewTimesheet() {
        TestDataService::truncateTables(array('Timesheet'));
        
        $timesheet = new Timesheet();
        $timesheet->setState("CREATED");
        $timesheet->setEmployeeId(200);
        $timesheet->setStartDate("2011-04-07");
        $timesheet->setEndDate("2011-04-14");

        $savedNewTimesheet = $this->timesheetDao->saveTimesheet($timesheet);

        $this->assertNotNull($savedNewTimesheet->getTimesheetId());
        $this->assertEquals($savedNewTimesheet->getState(), "CREATED");
        $this->assertEquals($savedNewTimesheet->getStartDate(), "2011-04-07");
    }

    /**
     * Testing for saving a timesheet for existing timesheets
     */
    public function testSaveTimesheet() {

        $timesheet = TestDataService::fetchObject('Timesheet', 1);

        $timesheet->setState("SUBMITTED");

        $this->timesheetDao->saveTimesheet($timesheet);
        $savedTimesheet = TestDataService::fetchObject("Timesheet", $timesheet->getTimesheetId());

        $this->assertEquals($timesheet->getState(), $savedTimesheet->getState());
        $this->assertEquals($timesheet->getStartDate(), $savedTimesheet->getStartDate());
        $this->assertEquals($timesheet->getEmployeeId(), $savedTimesheet->getEmployeeId());
    }

    public function testSaveTimesheetReturnType() {

        $timesheet = TestDataService::fetchObject('Timesheet', 1);

        $timesheet->setState("SUBMITTED");

        $savedTimesheet = $this->timesheetDao->saveTimesheet($timesheet);

        $this->assertTrue($savedTimesheet instanceof Timesheet);
    }

    /**
     * Testing for getTimesheetItemById method
     */
    public function testGetTimesheetItemById() {

        $timesheetItem = $this->timesheetDao->getTimesheetItemById(1);

        $this->assertTrue($timesheetItem instanceof TimesheetItem);
        $this->assertEquals(1, $timesheetItem->getTimesheetId());
        $this->assertEquals("Good", $timesheetItem->getComment());
        $this->assertEquals(7200, $timesheetItem->getDuration());
    }

    /**
     * Testing getTimesheetItem method for given Timesheet Id and Employee Id
     */
    public function testGetTimesheetItem() {


        $result = $this->timesheetDao->getTimesheetItem(1, 2);

        $this->assertEquals(3, count($result));

        $timesheetItem = $result[0];

        $this->assertEquals("2011-04-10", $timesheetItem['date']);
        $this->assertEquals(1000, $timesheetItem['duration']);
        $this->assertEquals("Poor", $timesheetItem['comment']);
    }

    /**
     * Testing getTimesheetItem method for given Timesheet Id and Employee Id
     */
    public function testGetTimesheetItemByDateProjectId() {


        $timesheetItem = $this->timesheetDao->getTimesheetItemByDateProjectId(1, 1, 1, 1, "2011-04-12");
        $timesheetItem1 = $this->timesheetDao->getTimesheetItemByDateProjectId(1, 2, 1, 1, "2011-04-13");
        //print_r($timesheetItem);
        $this->assertTrue($timesheetItem[0] instanceof TimesheetItem);
        $this->assertEquals(7200, $timesheetItem[0]->getDuration());
        $this->assertEquals("Good", $timesheetItem[0]->getComment());
        $this->assertEquals(1000, $timesheetItem1[0]->getDuration());
        $this->assertEquals("Very Good", $timesheetItem1[0]->getComment());
    }

    /**
     * Testing getTimesheetItem method for the order
     */
    public function testGetTimesheetItemForTheOrder() {

        $result = $this->timesheetDao->getTimesheetItem(1, 2);

        $this->assertEquals("2011-04-10", $result[0]['date']);
        $this->assertEquals("2011-04-13", $result[1]['date']);
        $this->assertEquals("2011-04-15", $result[2]['date']);
    }

    /**
     * Testing getTimesheetItem for non existing Items
     */
    public function testGetTimesheetItemForNonExistingItems() {

        $result = $this->timesheetDao->getTimesheetItem(0, 0);
        $result1 = $this->timesheetDao->getTimesheetItem(1, 0);
        $result2 = $this->timesheetDao->getTimesheetItem(0, 1);

        $this->assertNull($result[0]['date']);
        $this->assertNull($result[0]['duration']);
        $this->assertNull($result[0]['comment']);

        $this->assertNull($result1[0]['date']);
        $this->assertNull($result1[0]['duration']);
        $this->assertNull($result1[0]['comment']);

        $this->assertNull($result2[0]['date']);
        $this->assertNull($result2[0]['duration']);
        $this->assertNull($result2[0]['comment']);
    }

    /**
     * Testing saveTimesheetItem method for the newly made timesheet Items
     */
    public function testSaveTimesheetItemWithNewTimesheetItem() {
        TestDataService::truncateTables(array('TimesheetItem'));
        
        $timesheetItem = new TimesheetItem();
        $timesheetItem->setTimesheetId(1);
        $timesheetItem->setDate("2011-04-23");
        $timesheetItem->setDuration("5700");
        $timesheetItem->setComment("New Timesheet Item");
        $timesheetItem->setProjectId(1);
        $timesheetItem->setEmployeeId(1);
        $timesheetItem->setActivityId(4);

        $savedNewTimesheetItem = $this->timesheetDao->saveTimesheetItem($timesheetItem);

        $this->assertTrue($savedNewTimesheetItem instanceof TimesheetItem);
        $this->assertEquals('001', $savedNewTimesheetItem->getTimesheetItemId());
        $this->assertEquals($timesheetItem->getTimesheetId(), $savedNewTimesheetItem->getTimesheetId());
        $this->assertEquals($timesheetItem->getDate(), $savedNewTimesheetItem->getDate());
        $this->assertEquals($timesheetItem->getDuration(), $savedNewTimesheetItem->getDuration());
        $this->assertEquals($timesheetItem->getComment(), $savedNewTimesheetItem->getComment());
        $this->assertEquals($timesheetItem->getProjectId(), $savedNewTimesheetItem->getProjectId());
        $this->assertEquals($timesheetItem->getEmployeeId(), $savedNewTimesheetItem->getEmployeeId());
        $this->assertEquals($timesheetItem->getActivityId(), $savedNewTimesheetItem->getActivityId());
    }

    /**
     * Testing saveTimesheetItem method for existing timesheet items
     */
    public function testSaveTimesheetItem() {

        $timesheetItem = TestDataService::fetchObject('TimesheetItem', 1);

        $this->assertEquals("Good", $timesheetItem->getComment());

        $timesheetItem->setComment("Bad");


        $this->timesheetDao->saveTimesheetItem($timesheetItem);
        $savedTimesheetItem = TestDataService::fetchObject('TimesheetItem', $timesheetItem->getTimesheetItemId());


        $this->assertEquals($timesheetItem->getDuration(), $savedTimesheetItem->getDuration());
        $this->assertEquals($timesheetItem->getComment(), $savedTimesheetItem->getComment());
        $this->assertEquals($timesheetItem->getDate(), $savedTimesheetItem->getDate());
    }

    /**
     * Testing saveTimesheetItem method for deleting timesheet items
     */
    public function testDeleteTimesheetItems() {
        $deleted = $this->timesheetDao->deleteTimesheetItems(2, 1, 1, 1);
        $this->assertTrue($deleted);
    }

    /**
     * Testing getTimesheetActionLogById method for existing id's
     */
    public function testGetTimesheetActionLogById() {

        $timesheetActionLog = $this->timesheetDao->getTimesheetActionLogById(2);

        $this->assertTrue($timesheetActionLog instanceof TimesheetActionLog);
        $this->assertEquals("REJECTED", $timesheetActionLog->getComment());
        $this->assertEquals("REJECTED", $timesheetActionLog->getAction());
        $this->assertEquals("2011-04-19", $timesheetActionLog->getDateTime());
    }

    /**
     * Testing getTimesheetActionLogById method for non existing id's
     */
    public function testGetTimesheetActionLogByIdForNonExistingId() {

        $timesheetActionLog = $this->timesheetDao->getTimesheetActionLogById(-3);

        $this->assertEquals(null, $timesheetActionLog);
    }

    /**
     * Testing saveTimesheetActionLog mthod
     */
    public function testSaveTimesheetActionLog() {


        $timesheetActionLog = TestDataService::fetchObject("TimesheetActionLog", 2);


        $this->assertEquals("REJECTED", $timesheetActionLog->getComment());

        $timesheetActionLog->setComment("ACCEPTED");
        $timesheetActionLog->setAction("REJECTED");
        $this->timesheetDao->saveTimesheetActionLog($timesheetActionLog);
        $savedTimesheetActionLog = TestDataService::fetchObject("TimesheetActionLog", $timesheetActionLog->getTimesheetActionLogId());


        $this->assertEquals($timesheetActionLog->getAction(), $savedTimesheetActionLog->getAction());
        $this->assertEquals($timesheetActionLog->getComment(), $savedTimesheetActionLog->getComment());
        $this->assertEquals($timesheetActionLog->getDateTime(), $savedTimesheetActionLog->getDateTime());
        $this->assertEquals($timesheetActionLog->getPerformedBy(), $savedTimesheetActionLog->getPerformedBy());
    }

    /**
     * Testing saveTimesheetActionLog mthod for newly made timesheet action logs
     */
    public function testSaveTimesheetActionLogWithNewTimesheetActionLog() {
        TestDataService::truncateSpecificTables(array('TimesheetActionLog'), true);
        
        $timesheetActionLog = new TimesheetActionLog();
        $timesheetActionLog->setTimesheetId(1);
        $timesheetActionLog->setDateTime('2011-04-23');
        $timesheetActionLog->setComment('New Timesheet Item');
        $timesheetActionLog->setAction('ACCEPTED');
        $timesheetActionLog->setPerformedBy('3');

        $savedNewTimesheetActionLog = $this->timesheetDao->saveTimesheetActionLog($timesheetActionLog);

        $this->assertTrue($savedNewTimesheetActionLog instanceof TimesheetActionLog);
        $this->assertEquals('001', $savedNewTimesheetActionLog->getTimesheetActionLogId());
        $this->assertEquals($timesheetActionLog->getTimesheetId(), $savedNewTimesheetActionLog->getTimesheetId());
        $this->assertEquals($timesheetActionLog->getDateTime(), $savedNewTimesheetActionLog->getDateTime());
        $this->assertEquals($timesheetActionLog->getComment(), $savedNewTimesheetActionLog->getComment());
        $this->assertEquals($timesheetActionLog->getAction(), $savedNewTimesheetActionLog->getAction());
        $this->assertEquals($timesheetActionLog->getPerformedBy(), $savedNewTimesheetActionLog->getPerformedBy());
    }

    public function testGetTimesheetActionLogByTimesheetId() {
        $timesheetActionLogResults = $this->timesheetDao->getTimesheetActionLogByTimesheetId(1);
        $this->assertEquals(2, count($timesheetActionLogResults));
        $this->assertEquals("ACCEPTED", $timesheetActionLogResults[0]->getComment());
        $this->assertEquals("2011-04-18", $timesheetActionLogResults[0]->getDateTime());
        $this->assertEquals("REJECTED", $timesheetActionLogResults[1]->getComment());
        $this->assertEquals(2, $timesheetActionLogResults[1]->getTimesheetActionLogId());
    }

    public function testGetStartAndEndDatesList() {

        $daysArray = $this->timesheetDao->getStartAndEndDatesList(2);
        $startDates = $daysArray[0];
        $endDates = $daysArray[1];
        $this->assertEquals($startDates[0]['startDate'], "2011-04-18");
        $this->assertEquals($endDates[0]['endDate'], "2011-04-19");
        
    }

    public function testGetCustomerByName() {

        $customer = $this->timesheetDao->getCustomerByName("user");

        $this->assertTrue($customer instanceof Customer);
        $this->assertEquals(1, $customer->getCustomerId());
        $this->assertEquals("user", $customer->getName());
    }

    public function testGetCustomerByNameForNonExistingName() {

        $customer = $this->timesheetDao->getCustomerByName("jason");

        $this->assertNull($customer);
    }

    public function testGetProjectByProjectNameAndCustomerId() {

        $project = $this->timesheetDao->getProjectByProjectNameAndCustomerId('OrangeHRM', 1);

        $this->assertTrue($project instanceof Project);
        $this->assertEquals(1, $project->getProjectId());
        $this->assertEquals('OrangeHRM', $project->getName());
        $this->assertEquals(1, $project->getCustomerId());
        $this->assertEquals('firstproject', $project->getDescription());
    }

    public function testGetProjectByProjectNameAndCustomerIdForNonExistingRecord() {

        $project = $this->timesheetDao->getProjectByProjectNameAndCustomerId('IFS', 1);

        $this->assertNull($project);
    }

    public function testGetProjectActivitiesByPorjectId() {


        $activities = $this->timesheetDao->getProjectActivitiesByPorjectId(1);

        $this->assertTrue($activities[0] instanceof ProjectActivity);
        $this->assertEquals(1, count($activities));
        $this->assertEquals(1, $activities[0]->getActivityId());
        $this->assertEquals('Activity1 For Pro1', $activities[0]->getName());
    }

    public function testGetProjectActivitiesByPorjectIdWithDeletedActivities() {


        $activities = $this->timesheetDao->getProjectActivitiesByPorjectId(1, true);

        $this->assertTrue($activities[0] instanceof ProjectActivity);
        $this->assertEquals(2, count($activities));
        $this->assertEquals(2, $activities[1]->getActivityId());
        $this->assertEquals('Activity2 For Pro1', $activities[1]->getName());
    }

    public function testGetProjectActivitiesByPorjectIdForNonExistingRecord() {

        $project = $this->timesheetDao->getProjectActivitiesByPorjectId(4);

        $this->assertNull($project);
    }

    public function testGetProjectActivityByProjectIdAndActivityName() {

        $activity = $this->timesheetDao->getProjectActivityByProjectIdAndActivityName(1, "Activity1 For Pro1");

        $this->assertTrue($activity instanceof ProjectActivity);
        $this->assertEquals(1, $activity->getActivityId());
    }

    public function testGetProjectActivityByProjectIdAndActivityNameForNonExistingRecord() {

        $activity = $this->timesheetDao->getProjectActivityByProjectIdAndActivityName(8, "Activity3 For Pro1");

        $this->assertNull($activity);
    }

    public function testGetProjectActivityByActivityId() {

        $activity = $this->timesheetDao->getProjectActivityByActivityId(1);

        $this->assertTrue($activity instanceof ProjectActivity);
        $this->assertEquals("Activity1 For Pro1", $activity->getName());
    }

    public function testGetProjectActivityByActivityIdForNonExistingRecord() {

        $activity = $this->timesheetDao->getProjectActivityByActivityId(100);
        $this->assertNull($activity);
    }

    /* Testing getTimesheetByEmployeeId method */

    public function testGetTimesheetByEmployeeId() {

        $timesheets = $this->timesheetDao->getTimesheetByEmployeeId(2);
        $this->assertTrue($timesheets[0] instanceof Timesheet);
        $this->assertEquals(11, $timesheets[0]->getTimesheetId());
        $this->assertEquals('ACCEPTED', $timesheets[0]->getState());
        $this->assertEquals('2011-04-18', $timesheets[0]->getStartDate());
        $this->assertEquals('2011-04-22', $timesheets[1]->getStartDate());
    }

    /* Testing getTimesheetByEmployeeIdAndState method */

    public function testGetTimesheetByEmployeeIdAndState() {

        $stateList = array('SUBMITTED', 'ACCEPTED');
        $timesheets = $this->timesheetDao->getTimesheetByEmployeeIdAndState(2, $stateList);

        $this->assertTrue($timesheets[0] instanceof Timesheet);
        $this->assertEquals(2, count($timesheets));

        $this->assertEquals(2, $timesheets[0]->getTimesheetId());
        $this->assertEquals('SUBMITTED', $timesheets[0]->getState());
        $this->assertEquals('2011-04-22', $timesheets[0]->getStartDate());

        $this->assertEquals(11, $timesheets[1]->getTimesheetId());
        $this->assertEquals('ACCEPTED', $timesheets[1]->getState());
        $this->assertEquals('2011-04-18', $timesheets[1]->getStartDate());
    }

    public function testGetPendingApprovelTimesheetsForAdmin() {

        $timesheets = $this->timesheetDao->getPendingApprovelTimesheetsForAdmin();

        $this->assertTrue($timesheets[0] instanceof Timesheet);
        $this->assertTrue($timesheets[1] instanceof Timesheet);
        $this->assertEquals($timesheets[0]->getState(), "SUPERVISOR APPROVED");
    }

    public function testGetActivityByActivityIdForExistingId() {

        $activity = $this->timesheetDao->getActivityByActivityId(1);

        $this->assertTrue($activity instanceof ProjectActivity);
        $this->assertEquals("Activity1 For Pro1", $activity->getName());
        $this->assertEquals("1", $activity->getProjectId());
    }

    /**
     * Testing getActivityById for non existing id's
     */
    public function testGetActivityByActivityIdForNonExistingId() {

        $activity = $this->timesheetDao->getActivityByActivityId(-1);
        $this->assertEquals(null, $activity);
    }

    public function testGetTimesheetTimeFormat() {

        $configDao = $this->getMock('ConfigDao', array('getValue'));
        $configDao->expects($this->once())
                     ->method('getValue')
                     ->with(ConfigService::KEY_TIMESHEET_TIME_FORMAT)
                     ->will($this->returnValue(1));
        
        $this->timesheetDao->setConfigDao($configDao);      
        
        $format = $this->timesheetDao->getTimesheetTimeFormat();
        
        $this->assertEquals('1', $format);
        
    }

    public function testGetLatestTimesheetEndDate() {

        $latestEndDate = "2011-04-28";
        $employeeId = 1;
        $obtainedEndDate = $this->timesheetDao->getLatestTimesheetEndDate($employeeId);

        $this->assertEquals($obtainedEndDate, $latestEndDate);
    }

    public function testCheckForOverlappingTimesheets() {
        //test case 1
        $startDate = "2011-04-17";
        $endDate = "2011-04-20";
        $employeeId = 1;

        //test case 2
        $employeeId2 = 5;
        $startDate2 = "2011-03-25";
        $endDate2 = "2011-04-07";

        //test case3
        $employeeId3 = 7;
        $startDate3 = "2011-04-15";
        $endDate3 = "2011-05-15";

        //test case4-timesheet with a non overlapping time period

        $employeeId4 = 6;
        $startDate4 = "2011-02-26";
        $endDate4 = "2011-03-18";

        //test case 5-EndDate becoms equals to another timesheets startDate

        $employeeId5 = 4;
        $startDate5 = "2011-03-17";
        $endDate5 = "2011-04-18";

        $isValid1 = $this->timesheetDao->checkForOverlappingTimesheets($startDate, $endDate, $employeeId);
        $isValid2 = $this->timesheetDao->checkForOverlappingTimesheets($startDate2, $endDate2, $employeeId2);
        $isValid3 = $this->timesheetDao->checkForOverlappingTimesheets($startDate3, $endDate3, $employeeId3);
        $isValid4 = $this->timesheetDao->checkForOverlappingTimesheets($startDate4, $endDate4, $employeeId4);
        $isValid5 = $this->timesheetDao->checkForOverlappingTimesheets($startDate5, $endDate5, $employeeId5);

        $this->assertEquals($isValid1, 0);
        $this->assertEquals($isValid2, 0);
        $this->assertEquals($isValid3, 0);
        $this->assertEquals($isValid4, 1);
        $this->assertEquals($isValid5, 0);
    }

    public function testCheckForMatchingTimesheetForCurrentDate() {
        $employeeId = 6;
        $currentDate = "2011-02-24";

        $timesheet = $this->timesheetDao->checkForMatchingTimesheetForCurrentDate($employeeId, $currentDate);
        $this->assertTrue($timesheet instanceof Timesheet);
        $this->assertEquals($timesheet->getStartDate(), "2011-02-19");
        $this->assertEquals($timesheet->getEndDate(), "2011-02-25");
    }

    public function testGetProjectList() {

        $projectList = $this->timesheetDao->getProjectList();
        $this->assertTrue($projectList[0] instanceof Project);
        $this->assertEquals($projectList[0]->getProjectId(), 1);
        $this->assertEquals($projectList[0]->getName(), "OrangeHRM");
        $this->assertEquals(2, count($projectList));
    }

    public function testGetProjectListForValidation() {

        $allProjects = $this->timesheetDao->getProjectListForValidation();
        $this->assertEquals(3, count($allProjects));
         $this->assertTrue($allProjects[1] instanceof Project);
        $this->assertEquals($allProjects[1]->getProjectId(), 2);
        $this->assertEquals($allProjects[1]->getName(), "OrangeHRM2");
    }
    
    public function testGetTimesheetListByEmployeeIdAndState() {

        $empIdList = array(1, 2);
        $stateList = array('SUBMITTED', 'ACCEPTED');
        
        $timesheets = $this->timesheetDao->getTimesheetListByEmployeeIdAndState($empIdList, $stateList, 100);
        $this->assertEquals(3, count($timesheets));

        $this->assertEquals(8, $timesheets[0]['timesheetId']);
        $this->assertEquals('2011-04-22', $timesheets[0]['timesheetStartday']);
        $this->assertEquals(1, $timesheets[0]['employeeId']);

        $this->assertEquals('last2', $timesheets[1]['employeeLastName']);
        
        $timesheets = $this->timesheetDao->getTimesheetListByEmployeeIdAndState($empIdList, $stateList, 1);
        $this->assertEquals(1, count($timesheets));
        
        $timesheets = $this->timesheetDao->getTimesheetListByEmployeeIdAndState(null, null, null);
        $this->assertNull($timesheets);
    }
    
    public function testGetProjectNameList() {

        $projectList = $this->timesheetDao->getProjectNameList();
        
        $this->assertEquals(1, $projectList[0]['projectId']);
        $this->assertEquals('OrangeHRM', $projectList[0]['projectName']);
        
        $this->assertEquals(2, $projectList[1]['projectId']);
        $this->assertEquals('OrangeHRM2', $projectList[1]['projectName']);
        
        $this->assertEquals(2, count($projectList));
    }
    
    public function testGetProjectActivityListByPorjectId() {

        $activities = $this->timesheetDao->getProjectActivityListByPorjectId(1);
        
        $this->assertEquals(1, count($activities));
        $this->assertEquals(1, $activities[0]['activityId']);
        $this->assertEquals(1, $activities[0]['projectId']);
        $this->assertEquals(0, $activities[0]['is_deleted']);
        $this->assertEquals('Activity1 For Pro1', $activities[0]['name']);
        
        $activities = $this->timesheetDao->getProjectActivityListByPorjectId(null);
        $this->assertEquals(array(), $activities);
    }    

}

?>
