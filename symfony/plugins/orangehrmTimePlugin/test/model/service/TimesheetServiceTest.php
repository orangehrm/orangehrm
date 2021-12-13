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
 * Description of TimesheetServiceTest
 *
 * @group Time
 */
class TimesheetServiceTest extends PHPUnit_Framework_Testcase
{
    private $timesheetService;
    private $fixture;

    protected function setUp()
    {
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmTimePlugin/test/fixtures/TimesheetService.yml';
        TestDataService::truncateSpecificTables(['SystemUser']);
        TestDataService::populate($this->fixture);
        $this->timesheetService = new TimesheetService();
    }

    /* test both getTimesheetDao() and setTimesheetDao() */

    public function testGetAndSetTimesheetDao()
    {
        $timesheetDao = new TimesheetDao();
        $this->timesheetService->setTimesheetDao($timesheetDao);

        $this->assertTrue($this->timesheetService->getTimesheetDao() instanceof TimesheetDao);
    }

    /* test getTimesheetDao() with no argument */

    public function testGetTimesheetDao()
    {
        $this->assertTrue($this->timesheetService->getTimesheetDao() instanceof TimesheetDao);
    }

    /* test both getEmployeeDao() and setEmployeeDao() */

    public function testGetAndSetEmployeeDao()
    {
        $employeeDao = new EmployeeDao();
        $this->timesheetService->setEmployeeDao($employeeDao);

        $this->assertTrue($this->timesheetService->getEmployeeDao() instanceof EmployeeDao);
    }

    /* test getEmployeeDao() with no argument */

    public function testGetEmployeeDao()
    {
        $this->assertTrue($this->timesheetService->getEmployeeDao() instanceof EmployeeDao);
    }

    /* test saveTimesheet() */

    public function testSaveTimesheet()
    {
        $timesheets = TestDataService::loadObjectList('Timesheet', $this->fixture, 'Timesheet');

        $timesheet = $timesheets[0];

        $timesheetDaoMock = $this->getMockBuilder('TimesheetDao')
            ->setMethods(['saveTimesheet'])
            ->getMock();

        $timesheetDaoMock->expects($this->once())
                ->method('saveTimesheet')
                ->with($timesheet)
                ->will($this->returnValue($timesheet));

        $this->timesheetService->setTimesheetDao($timesheetDaoMock);
        $this->assertTrue($this->timesheetService->saveTimesheet($timesheet) instanceof Timesheet);
    }

    /* test saveTimesheetActionLog */

    public function testSaveTimesheetActionLog()
    {
        $timesheetActionLogRecords = TestDataService::loadObjectList('TimesheetActionLog', $this->fixture, 'TimesheetActionLog');
        $timesheetActionLog = $timesheetActionLogRecords[0];

        $timesheetDaoMock = $this->getMockBuilder('TimesheetDao')
            ->setMethods(['saveTimesheetActionLog'])
            ->getMock();

        $timesheetDaoMock->expects($this->once())
                ->method('saveTimesheetActionLog')
                ->with($timesheetActionLog)
                ->will($this->returnValue($timesheetActionLog));

        $this->timesheetService->setTimesheetDao($timesheetDaoMock);
        $this->assertTrue($this->timesheetService->saveTimesheetActionLog($timesheetActionLog) instanceof TimesheetActionLog);
    }

    /* test getTimesheetById() */

    public function testGetTimesheetById()
    {
        $timesheetId = 1;
        $timesheet = TestDataService::fetchObject('Timesheet', $timesheetId);

        $timesheetDaoMock = $this->getMockBuilder('TimesheetDao')
            ->setMethods(['getTimesheetById'])
            ->getMock();
        $timesheetDaoMock->expects($this->once())
                ->method('getTimesheetById')
                ->with($timesheetId)
                ->will($this->returnValue($timesheet));

        $this->timesheetService->setTimesheetDao($timesheetDaoMock);
        $gotTimesheet = $this->timesheetService->getTimesheetById($timesheetId);

        $this->assertTrue($gotTimesheet instanceof Timesheet);
        $this->assertEquals($timesheet, $gotTimesheet);
    }

    /* test getTimesheetItemById() */

    public function testGetTimesheetItemById()
    {
        $timesheetItemId = 2;
        $timesheetItem = TestDataService::fetchObject('TimesheetItem', $timesheetItemId);

        $timesheetDaoMock = $this->getMockBuilder('TimesheetDao')
            ->setMethods(['getTimesheetItemById'])
            ->getMock();
        $timesheetDaoMock->expects($this->once())
                ->method('getTimesheetItemById')
                ->with($timesheetItemId)
                ->will($this->returnValue($timesheetItem));

        $this->timesheetService->setTimesheetDao($timesheetDaoMock);
        $recievedTimesheetItem = $this->timesheetService->getTimesheetItemById($timesheetItemId);

        //$this->assertTrue($recievedTimesheetItem instanceof TimesheetItem);
        $this->assertEquals($timesheetItem, $recievedTimesheetItem);
    }

    /* test getTimesheetByStartDate() */

    public function testGetTimesheetByStartDate()
    {
        $startDate = "2011-04-18";
        $timesheets = TestDataService::loadObjectList('Timesheet', $this->fixture, 'Timesheet');
        $temp = $timesheets[0];

        $timesheetDaoMock = $this->getMockBuilder('TimesheetDao')
            ->setMethods(['getTimesheetByStartDate'])
            ->getMock();
        $timesheetDaoMock->expects($this->once())
                ->method('getTimesheetByStartDate')
                ->with($startDate)
                ->will($this->returnValue($temp));

        $this->timesheetService->setTimesheetDao($timesheetDaoMock);
        $gotTimesheet = $this->timesheetService->getTimesheetByStartDate($startDate);

        $this->assertTrue($gotTimesheet instanceof Timesheet);
        //$this->assertEquals( 1 , count($gotTimesheet));
        $this->assertEquals("2011-04-18", $gotTimesheet->getStartDate());
    }



    public function testGetTimesheetByStartDateAndEmployeeId()
    {
        $employeeId = 1;
        $timesheetId = 1;
        $startDate = "2011-04-18";
        $timesheet = TestDataService::fetchObject('Timesheet', $timesheetId);

        $timesheetDaoMock = $this->getMockBuilder('TimesheetDao')
            ->setMethods(['getTimesheetByStartDateAndEmployeeId'])
            ->getMock();
        $timesheetDaoMock->expects($this->once())
                ->method('getTimesheetByStartDateAndEmployeeId')
                ->with($startDate, $employeeId)
                ->will($this->returnValue($timesheet));

        $this->timesheetService->setTimesheetDao($timesheetDaoMock);
        $gotTimesheet = $this->timesheetService->getTimesheetByStartDateAndEmployeeId($startDate, $employeeId);

        $this->assertTrue($gotTimesheet instanceof Timesheet);
        $this->assertEquals($timesheet, $gotTimesheet);
    }

    /* test getTimesheetByEmployeeId()  */

    public function testGetTimesheetByEmployeeId()
    {
        $employeeId = 2;
        $timesheetId = 2;
        $timesheet = TestDataService::fetchObject('Timesheet', $timesheetId);

        $timesheetDaoMock = $this->getMockBuilder('TimesheetDao')
            ->setMethods(['getTimesheetByEmployeeId'])
            ->getMock();
        $timesheetDaoMock->expects($this->once())
                ->method('getTimesheetByEmployeeId')
                ->with($employeeId)
                ->will($this->returnValue($timesheet));

        $this->timesheetService->setTimesheetDao($timesheetDaoMock);
        $retrievedTimesheet = $this->timesheetService->getTimesheetByEmployeeId($employeeId);

        $this->assertTrue($retrievedTimesheet instanceof Timesheet);
        $this->assertEquals($timesheet, $retrievedTimesheet);
    }

    /* test getTimesheetByEmployeeIdAndState()  */

    public function testGetTimesheetByEmployeeIdAndState()
    {
        $employeeId = 2;

        $timesheetId1 = 2;
        $timesheetId2 = 8;

        $stateList = ['SUBMITTED', 'ACCEPTED'];

        $timesheet1 = TestDataService::fetchObject('Timesheet', $timesheetId1);
        $timesheet2 = TestDataService::fetchObject('Timesheet', $timesheetId2);

        $timesheetArray = [$timesheet1, $timesheet2];

        $timesheetDaoMock = $this->getMockBuilder('TimesheetDao')
            ->setMethods(['getTimesheetByEmployeeIdAndState'])
            ->getMock();
        $timesheetDaoMock->expects($this->once())
                ->method('getTimesheetByEmployeeIdAndState')
                ->with($employeeId, $stateList)
                ->will($this->returnValue($timesheetArray));

        $this->timesheetService->setTimesheetDao($timesheetDaoMock);
        $retrievedTimesheet = $this->timesheetService->getTimesheetByEmployeeIdAndState($employeeId, $stateList);

        $this->assertEquals(2, count($retrievedTimesheet));
        $this->assertTrue($retrievedTimesheet[0] instanceof Timesheet);
        $this->assertEquals($timesheet1, $retrievedTimesheet[0]);
        $this->assertEquals($timesheet2, $retrievedTimesheet[1]);
    }

    public function testGetStartAndEndDatesList()
    {
        $daysArray = $this->timesheetService->getStartAndEndDatesList(1);
        $startDates = $daysArray[0];
        $endDates = $daysArray[1];
        $this->assertEquals($startDates[0]['startDate'], "2011-04-18");
        $this->assertEquals($endDates[0]['endDate'], "2011-04-19");
    }

    public function testGetPendingApprovelTimesheetsForAdmin()
    {
        $timesheetId = 6;
        $timesheet = TestDataService::fetchObject('Timesheet', $timesheetId);
        $timesheetDaoMock = $this->getMockBuilder('TimesheetDao')
            ->setMethods(['getPendingApprovelTimesheetsForAdmin'])
            ->getMock();
        $timesheetDaoMock->expects($this->once())
                ->method('getPendingApprovelTimesheetsForAdmin')
                ->will($this->returnValue($timesheet));
        $this->timesheetService->setTimesheetDao($timesheetDaoMock);
        $retrievedTimesheets = $this->timesheetService->getPendingApprovelTimesheetsForAdmin();


        $this->assertTrue($retrievedTimesheets instanceof Timesheet);
        $this->assertEquals($timesheet, $retrievedTimesheets);
    }

    public function testConvertDurationToHours()
    {
        $timesheetService = $this->getMockBuilder('TimesheetService')
            ->setMethods(['getTimesheetTimeFormat'])
            ->getMock();
        $timesheetService->expects($this->exactly(2))
                         ->method('getTimesheetTimeFormat')
                         ->will($this->returnValue(1));

        $durationInHours = $timesheetService->convertDurationToHours(3600);
        $durationInHours1 = $timesheetService->convertDurationToHours(5400);

        $this->assertEquals($durationInHours, '1:00');
        $this->assertEquals($durationInHours1, '1:30');
    }

    public function testConvertDurationToSeconds()
    {
        $durationInSecs = $this->timesheetService->convertDurationToSeconds(1);
        $durationInSecs1 = $this->timesheetService->convertDurationToSeconds(1.5);
        $this->assertEquals($durationInSecs, 3600);
        $this->assertEquals($durationInSecs1, 5400);
    }

    public function testgetTimesheetActionLogByTimesheetId()
    {
        $timesheetActionLogId = 1;
        $timesheetActionLogRecord = TestDataService::fetchObject('TimesheetActionLog', $timesheetActionLogId);
//                $timesheetActionLog = $timesheetActionLogRecords[0];

        $timesheetDaoMock = $this->getMockBuilder('TimesheetDao')
            ->setMethods(['getTimesheetActionLogByTimesheetId'])
            ->getMock();

        $timesheetDaoMock->expects($this->once())
                ->method('getTimesheetActionLogByTimesheetId')
                ->with($timesheetActionLogId)
                ->will($this->returnValue($timesheetActionLogRecord));
        $this->timesheetService->setTimesheetDao($timesheetDaoMock);

        $retrievedTimesheetActionLog = $this->timesheetService->getTimesheetActionLogByTimesheetId($timesheetActionLogId);

        $this->assertTrue($retrievedTimesheetActionLog instanceof TimesheetActionLog);
        $this->assertEquals($timesheetActionLogRecord, $retrievedTimesheetActionLog);
    }

    public function testGetActivityByActivityId()
    {
        $activityId = 1;
        $activity = TestDataService::fetchObject('ProjectActivity', $activityId);

        $activityDaoMock = $this->getMockBuilder('TimesheetDao')
            ->setMethods(['getActivityByActivityId'])
            ->getMock();
        $activityDaoMock->expects($this->once())
                ->method('getActivityByActivityId')
                ->with($activityId)
                ->will($this->returnValue($activity));

        $this->timesheetService->setTimesheetDao($activityDaoMock);
        $gotActivity = $this->timesheetService->getActivityByActivityId($activityId);

        $this->assertTrue($gotActivity instanceof ProjectActivity);
        $this->assertEquals($activity, $gotActivity);
    }

    public function testAddConvertTime()
    {
        $firstTime = '4:30';
        $timeToAdd = '1:40';
        $total = $this->timesheetService->addConvertTime($firstTime, $timeToAdd);
        $this->assertEquals('6:10', $total);
    }

    public function testDateDiff()
    {
        $start = "2011-06-27";
        $end = "2011-07-03";
        $noOfDays = $this->timesheetService->dateDiff($start, $end);
        $this->assertEquals('7', $noOfDays);
    }

    public function testGetLatestTimesheetEndDate()
    {
        $latestEndDate = "2011-04-28";
        $employeeId = 1;

        $timehseetDaoMock = $this->getMockBuilder('TimesheetDao')
            ->setMethods(['getLatestTimesheetEndDate'])
            ->getMock();
        $timehseetDaoMock->expects($this->once())
                ->method('getLatestTimesheetEndDate')
                ->with($employeeId)
                ->will($this->returnValue($latestEndDate));

        $this->timesheetService->setTimesheetDao($timehseetDaoMock);
        $obtaindeDate = $this->timesheetService->getLatestTimesheetEndDate($employeeId);

        $this->assertEquals($obtaindeDate, $latestEndDate);
    }

    public function testCheckForOverlappingTimesheets()
    {
        $employeeId = 1;
        $startDate = "2011-04-17";
        $endDate = "2011-04-20";
        $isValid = 0;

        $timehseetDaoMock = $this->getMockBuilder('TimesheetDao')
            ->setMethods(['checkForOverlappingTimesheets'])
            ->getMock();
        $timehseetDaoMock->expects($this->once())
                ->method('checkForOverlappingTimesheets')
                ->with($startDate, $endDate, $employeeId)
                ->will($this->returnValue($isValid));

        $this->timesheetService->setTimesheetDao($timehseetDaoMock);
        $testValue = $this->timesheetService->checkForOverlappingTimesheets($startDate, $endDate, $employeeId);

        $this->assertEquals($testValue, $isValid);
    }

    public function testCheckForMatchingTimesheetForCurrentDate()
    {
        $employeeId = 6;
        $currentDate = "2011-02-24";
        $timesheetId = 9;
        $timesheet = TestDataService::fetchObject('Timesheet', $timesheetId);

        $timehseetDaoMock = $this->getMockBuilder('TimesheetDao')
            ->setMethods(['checkForMatchingTimesheetForCurrentDate'])
            ->getMock();
        $timehseetDaoMock->expects($this->once())
                ->method('checkForMatchingTimesheetForCurrentDate')
                ->with($employeeId, $currentDate)
                ->will($this->returnValue($timesheet));

        $this->timesheetService->setTimesheetDao($timehseetDaoMock);
        $testTimesheet = $this->timesheetService->checkForMatchingTimesheetForCurrentDate($employeeId, $currentDate);

        $this->assertTrue($testTimesheet instanceof Timesheet);
        $this->assertEquals($timesheet, $testTimesheet);
    }

    public function testGetTimesheetListByEmployeeIdAndState()
    {
        $empIdList = [1, 2];
        $stateList = ['SUBMITTED', 'ACCEPTED'];

        $timesheet1['timesheetId'] = 11;
        $timesheet1['timesheetStartday'] = '2011-04-18';
        $timesheet1['timesheetEndDate'] = '2011-04-28';
        $timesheet1['employeeId'] = 2;
        $timesheet1['employeeFirstName'] = null;
        $timesheet1['employeeLastName'] = null;

        $timesheet2['timesheetId'] = 2;
        $timesheet2['timesheetStartday'] = '2011-04-22';
        $timesheet2['timesheetEndDate'] = '2011-04-19';
        $timesheet2['employeeId'] = 2;
        $timesheet2['employeeFirstName'] = null;
        $timesheet2['employeeLastName'] = null;

        $timesheet3['timesheetId'] = 8;
        $timesheet3['timesheetStartday'] = '2011-04-22';
        $timesheet3['timesheetEndDate'] = '2011-04-28';
        $timesheet3['employeeId'] = 1;
        $timesheet3['employeeFirstName'] = null;
        $timesheet3['employeeLastName'] = null;

        $timesheets[] = $timesheet1;
        $timesheets[] = $timesheet2;
        $timesheets[] = $timesheet3;

        $timesheetDaoMock = $this->getMockBuilder('TimesheetDao')
            ->setMethods(['getTimesheetListByEmployeeIdAndState'])
            ->getMock();
        $timesheetDaoMock->expects($this->once())
                ->method('getTimesheetListByEmployeeIdAndState')
                ->with($empIdList, $stateList, 100)
                ->will($this->returnValue($timesheets));

        $this->timesheetService->setTimesheetDao($timesheetDaoMock);
        $result = $this->timesheetService->getTimesheetListByEmployeeIdAndState($empIdList, $stateList, 100);

        $this->assertEquals(3, count($result));
        $this->assertEquals($timesheets[0], $result[0]);
        $this->assertEquals($timesheets[1], $result[1]);

        $timesheetDaoMock = $this->getMockBuilder('TimesheetDao')
            ->setMethods(['getTimesheetListByEmployeeIdAndState'])
            ->getMock();
        $timesheetDaoMock->expects($this->once())
                ->method('getTimesheetListByEmployeeIdAndState')
                ->with(null, null, null)
                ->will($this->returnValue(null));

        $this->timesheetService->setTimesheetDao($timesheetDaoMock);
        $result = $this->timesheetService->getTimesheetListByEmployeeIdAndState(null, null, null);
        $this->assertNull(null, $result);
    }

    public function testGetProjectNameList()
    {
        $project1['projectId'] = 1;
        $project1['projectName'] = 'OrangeHRM';
        $project1['customerName'] = 'user';

        $project2['projectId'] = 2;
        $project2['projectName'] = 'OrangeHRM2';
        $project2['customerName'] = 'user';

        $projects = [$project1, $project2];

        $timesheetDaoMock = $this->getMockBuilder('TimesheetDao')
            ->setMethods(['getProjectNameList'])
            ->getMock();
        $timesheetDaoMock->expects($this->once())
                ->method('getProjectNameList')
                ->with(true, 'project_id', 'ASC')
                ->will($this->returnValue($projects));

        $this->timesheetService->setTimesheetDao($timesheetDaoMock);
        $result = $this->timesheetService->getProjectNameList(true, 'project_id', 'ASC');

        $this->assertEquals(2, count($result));
        $this->assertEquals($projects[0], $result[0]);
        $this->assertEquals($projects[1], $result[1]);
    }

    public function testGetProjectActivityListByPorjectId()
    {
        $activity1['activityId'] = 1;
        $activity1['projectId'] = 1;
        $activity1['is_deleted'] = 0;
        $activity1['name'] = 'Activity1 For Pro1';

        $activities = [$project1];

        $timesheetDaoMock = $this->getMockBuilder('TimesheetDao')
            ->setMethods(['getProjectActivityListByPorjectId'])
            ->getMock();
        $timesheetDaoMock->expects($this->once())
                ->method('getProjectActivityListByPorjectId')
                ->with(1, true)
                ->will($this->returnValue($activities));

        $this->timesheetService->setTimesheetDao($timesheetDaoMock);
        $result = $this->timesheetService->getProjectActivityListByPorjectId(1, true);

        $this->assertEquals(1, count($result));
        $this->assertEquals($projects[0], $result[0]);
    }

    /**
     * Testing saveTimesheetItem method for deleting timesheet items
     */
    public function testDeleteTimesheetItemsByTimesheetId()
    {
        $this->timesheetService->setTimesheetDao(new TimesheetDao());
        $noOfItemsDeleted = $this->timesheetService->deleteTimesheetItemsByTimesheetId(8, 10);
        $this->assertTrue($noOfItemsDeleted);
    }

//    public function testCreatePreviousTimesheets(){
//
//        $currentTimesheetStartDate="2010-04-08";
//        $employeeId=8;
//        $r  =$this->timesheetService->createPreviousTimesheets($currentTimesheetStartDate, $employeeId);
//
//
//    }
}
