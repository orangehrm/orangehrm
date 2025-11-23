<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Tests\Dashboard\Dao;

use DateInterval;
use DateTime;
use DateTimeZone;
use Exception;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Dashboard\Dao\EmployeeTimeAtWorkDao;
use OrangeHRM\Entity\AttendanceRecord;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Dashboard
 * @group Dao
 */
class EmployeeTimeAtWorkDaoTest extends KernelTestCase
{
    protected string $fixture;
    protected EmployeeTimeAtWorkDao $employeeTimeAtWorkDao;

    /**
     * Set up method
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->employeeTimeAtWorkDao = new EmployeeTimeAtWorkDao();
        $this->fixture = Config::get(
            Config::PLUGINS_DIR
        ) . '/orangehrmDashboardPlugin/test/fixtures/EmployeeTimeAtWork.yaml';
        TestDataService::populate($this->fixture);
    }

    public function testGetLatestAttendanceRecordByEmpNumber()
    {
        $lastAttendanceRecord = $this->employeeTimeAtWorkDao->getLatestAttendanceRecordByEmpNumber(2);
        $this->assertEquals('PUNCHED OUT', $lastAttendanceRecord->getState());
        $this->assertEquals(new DateTime('2022-09-06 18:15:00'), $lastAttendanceRecord->getPunchInUtcTime());
        $this->assertEquals(new DateTime('2022-09-06 23:45:00'), $lastAttendanceRecord->getPunchInUserTime());
        $this->assertEquals('5.5', $lastAttendanceRecord->getPunchInTimeOffset());
        $this->assertEquals(new DateTime('2022-09-06 20:29:00'), $lastAttendanceRecord->getPunchOutUtcTime());
        $this->assertEquals(new DateTime('2022-09-07 01:59:00'), $lastAttendanceRecord->getPunchOutUserTime());
        $this->assertEquals('5.5', $lastAttendanceRecord->getPunchOutTimeOffset());

        /**
         * No last attendance record found
         */
        $lastAttendanceRecord = $this->employeeTimeAtWorkDao->getLatestAttendanceRecordByEmpNumber(6);
        $this->assertNotInstanceOf(AttendanceRecord::class, $lastAttendanceRecord);
    }

    public function testGetAttendanceRecordsByEmployeeAndDate()
    {
        $startUTCDateTime = new DateTime('2022-09-05 00:00:00', new DateTimeZone(DateTimeHelperService::TIMEZONE_UTC));
        $endUTCDateTime = (clone $startUTCDateTime)->add(new DateInterval('P1D'));
        $attendanceRecords = $this->employeeTimeAtWorkDao->getAttendanceRecordsByEmployeeAndDate(
            2,
            $startUTCDateTime,
            $endUTCDateTime
        );
        $this->assertCount(3, $attendanceRecords);
        $this->assertEquals('PUNCHED OUT', $attendanceRecords[0]->getState());
        $this->assertEquals(new DateTime('2022-09-05 17:00:00'), $attendanceRecords[0]->getPunchInUtcTime());
        $this->assertEquals(new DateTime('2022-09-05 22:30:00'), $attendanceRecords[0]->getPunchInUserTime());
        $this->assertEquals('5.5', $attendanceRecords[0]->getPunchInTimeOffset());
        $this->assertEquals(new DateTime('2022-09-05 18:00:00'), $attendanceRecords[0]->getPunchOutUtcTime());
        $this->assertEquals(new DateTime('2022-09-05 23:30:00'), $attendanceRecords[0]->getPunchOutUserTime());
        $this->assertEquals('5.5', $attendanceRecords[0]->getPunchOutTimeOffset());

        /**
         * punch in 2022-09-13 and punch out 2022-09-15, given date 2022-09-14 00:00:00 +5.5
         */
        $startUTCDateTime = new DateTime('2022-09-13 18:30:00', new DateTimeZone(DateTimeHelperService::TIMEZONE_UTC));
        $endUTCDateTime = (clone $startUTCDateTime)->add(new DateInterval('P1D'));
        $attendanceRecords = $this->employeeTimeAtWorkDao->getAttendanceRecordsByEmployeeAndDate(
            4,
            $startUTCDateTime,
            $endUTCDateTime
        );
        $this->assertCount(1, $attendanceRecords);
        $this->assertEquals('PUNCHED OUT', $attendanceRecords[0]->getState());
        $this->assertEquals(new DateTime('2022-09-13 16:30:00'), $attendanceRecords[0]->getPunchInUtcTime());
        $this->assertEquals(new DateTime('2022-09-13 22:00:00'), $attendanceRecords[0]->getPunchInUserTime());
        $this->assertEquals('5.5', $attendanceRecords[0]->getPunchInTimeOffset());
        $this->assertEquals(new DateTime('2022-09-14 20:30:00'), $attendanceRecords[0]->getPunchOutUtcTime());
        $this->assertEquals(new DateTime('2022-09-15 02:00:00'), $attendanceRecords[0]->getPunchOutUserTime());
        $this->assertEquals('5.5', $attendanceRecords[0]->getPunchOutTimeOffset());
    }
}
