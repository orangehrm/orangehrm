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
class WorkShiftServiceTest extends PHPUnit_Framework_TestCase {

    private $workShiftService;
    private $fixture;

    /**
     * Set up method
     */
    protected function setUp() {
        $this->workShiftService = new WorkShiftService();
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmAdminPlugin/test/fixtures/WorkShiftDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetWorkShiftList() {

        $workShiftList = TestDataService::loadObjectList('WorkShift', $this->fixture, 'WorkShift');

        $workShiftDao = $this->getMock('WorkShiftDao');
        $workShiftDao->expects($this->once())
                ->method('getWorkShiftList')
                ->will($this->returnValue($workShiftList));

        $this->workShiftService->setWorkShiftDao($workShiftDao);

        $result = $this->workShiftService->getWorkShiftList();
        $this->assertEquals($result, $workShiftList);
    }

    public function testGetWorkShiftById() {

        $workShiftList = TestDataService::loadObjectList('WorkShift', $this->fixture, 'WorkShift');

        $workShiftDao = $this->getMock('WorkShiftDao');
        $workShiftDao->expects($this->once())
                ->method('getWorkShiftById')
                ->with(1)
                ->will($this->returnValue($workShiftList[0]));

        $this->workShiftService->setWorkShiftDao($workShiftDao);

        $result = $this->workShiftService->getWorkShiftById(1);
        $this->assertEquals($result, $workShiftList[0]);
    }

    public function testGetWorkShiftEmployeeListById() {

        $workShiftList = TestDataService::loadObjectList('EmployeeWorkShift', $this->fixture, 'EmployeeWorkShift');

        $workShiftDao = $this->getMock('WorkShiftDao');
        $workShiftDao->expects($this->once())
                ->method('getWorkShiftEmployeeListById')
                ->with(1)
                ->will($this->returnValue($workShiftList));

        $this->workShiftService->setWorkShiftDao($workShiftDao);

        $result = $this->workShiftService->getWorkShiftEmployeeListById(1);
        $this->assertEquals($result, $workShiftList);
    }

    public function testGetWorkShiftEmployeeList() {

        $workShiftList = TestDataService::loadObjectList('EmployeeWorkShift', $this->fixture, 'EmployeeWorkShift');

        $workShiftDao = $this->getMock('WorkShiftDao');
        $workShiftDao->expects($this->once())
                ->method('getWorkShiftEmployeeList')
                ->will($this->returnValue($workShiftList));

        $this->workShiftService->setWorkShiftDao($workShiftDao);

        $result = $this->workShiftService->getWorkShiftEmployeeList();
        $this->assertEquals($result, $workShiftList);
    }

    public function testUpdateWorkShift() {

        $workShiftList = TestDataService::loadObjectList('WorkShift', $this->fixture, 'WorkShift');

        $workShiftDao = $this->getMock('WorkShiftDao');
        $workShiftDao->expects($this->once())
                ->method('updateWorkShift')
                ->with($workShiftList[0])
                ->will($this->returnValue(true));

        $this->workShiftService->setWorkShiftDao($workShiftDao);

        $result = $this->workShiftService->updateWorkShift($workShiftList[0]);
        $this->assertTrue($result);
    }

    public function testGetWorkShiftDefaultStartAndEndTimeWhenDefaultConfigured() {
        $startTime = '07:23';
        $endTime = '19:00';

        $configService = $this->getMock('ConfigService', array('getDefaultWorkShiftStartTime', 'getDefaultWorkShiftEndTime'));
        $configService->expects($this->once())
                ->method('getDefaultWorkShiftStartTime')
                ->will($this->returnValue($startTime));

        $configService->expects($this->once())
                ->method('getDefaultWorkShiftEndTime')
                ->will($this->returnValue($endTime));

        $this->workShiftService->setConfigService($configService);

        $returnValue = $this->workShiftService->getWorkShiftDefaultStartAndEndTime();
        $expected = array('start_time' => $startTime, 'end_time' => $endTime);

        $this->assertEquals($expected, $returnValue);
    }

    public function testGetWorkShiftDefaultStartAndEndTimeWhenNoDefaultConfigured() {
        $configService = $this->getMock('ConfigService', array('getDefaultWorkShiftStartTime', 'getDefaultWorkShiftEndTime'));
        $configService->expects($this->once())
                ->method('getDefaultWorkShiftStartTime')
                ->will($this->returnValue(null));

        $configService->expects($this->once())
                ->method('getDefaultWorkShiftEndTime')
                ->will($this->returnValue(null));

        $this->workShiftService->setConfigService($configService);

        $returnValue = $this->workShiftService->getWorkShiftDefaultStartAndEndTime();
        $expected = array('start_time' => WorkShiftService::DEFAULT_WORKSHIFT_START_TIME, 'end_time' => WorkShiftService::DEFAULT_WORKSHIFT_END_TIME);

        $this->assertEquals($expected, $returnValue);
    }

    public function testGetWorkShiftDefaultStartAndEndTimeWithInvalidDefaultStartTime() {
        $configService = $this->getMock('ConfigService', array('getDefaultWorkShiftStartTime', 'getDefaultWorkShiftEndTime'));
        $configService->expects($this->once())
                ->method('getDefaultWorkShiftStartTime')
                ->will($this->returnValue('II:00'));

        $configService->expects($this->once())
                ->method('getDefaultWorkShiftEndTime')
                ->will($this->returnValue('23:00'));

        $this->workShiftService->setConfigService($configService);

        $returnValue = $this->workShiftService->getWorkShiftDefaultStartAndEndTime();
        $expected = array('start_time' => WorkShiftService::DEFAULT_WORKSHIFT_START_TIME, 'end_time' => WorkShiftService::DEFAULT_WORKSHIFT_END_TIME);

        $this->assertEquals($expected, $returnValue);
    }

    public function testGetWorkShiftDefaultStartAndEndTimeWithInvalidDefaultEndTime() {
        $configService = $this->getMock('ConfigService', array('getDefaultWorkShiftStartTime', 'getDefaultWorkShiftEndTime'));
        $configService->expects($this->once())
                ->method('getDefaultWorkShiftStartTime')
                ->will($this->returnValue('08:00'));

        $configService->expects($this->once())
                ->method('getDefaultWorkShiftEndTime')
                ->will($this->returnValue('25:00'));

        $this->workShiftService->setConfigService($configService);

        $returnValue = $this->workShiftService->getWorkShiftDefaultStartAndEndTime();
        $expected = array('start_time' => WorkShiftService::DEFAULT_WORKSHIFT_START_TIME, 'end_time' => WorkShiftService::DEFAULT_WORKSHIFT_END_TIME);

        $this->assertEquals($expected, $returnValue);
    }

    public function testGetWorkShiftDefaultStartAndEndTimeWhenDefaultDurationNegative() {
        $configService = $this->getMock('ConfigService', array('getDefaultWorkShiftStartTime', 'getDefaultWorkShiftEndTime'));
        $configService->expects($this->once())
                ->method('getDefaultWorkShiftStartTime')
                ->will($this->returnValue('17:00'));

        $configService->expects($this->once())
                ->method('getDefaultWorkShiftEndTime')
                ->will($this->returnValue('09:00'));

        $this->workShiftService->setConfigService($configService);

        $returnValue = $this->workShiftService->getWorkShiftDefaultStartAndEndTime();
        $expected = array('start_time' => WorkShiftService::DEFAULT_WORKSHIFT_START_TIME, 'end_time' => WorkShiftService::DEFAULT_WORKSHIFT_END_TIME);

        $this->assertEquals($expected, $returnValue);
    }

    public function testGetWorkShiftDefaultStartAndEndTimeWhenDefaultDurationLessThanHour() {
        $startTime = '07:23';
        $endTime = '07:50';

        $configService = $this->getMock('ConfigService', array('getDefaultWorkShiftStartTime', 'getDefaultWorkShiftEndTime'));
        $configService->expects($this->once())
                ->method('getDefaultWorkShiftStartTime')
                ->will($this->returnValue($startTime));

        $configService->expects($this->once())
                ->method('getDefaultWorkShiftEndTime')
                ->will($this->returnValue($endTime));

        $this->workShiftService->setConfigService($configService);

        $returnValue = $this->workShiftService->getWorkShiftDefaultStartAndEndTime();
        $expected = array('start_time' => $startTime, 'end_time' => $endTime);

        $this->assertEquals($expected, $returnValue);
    }

}

