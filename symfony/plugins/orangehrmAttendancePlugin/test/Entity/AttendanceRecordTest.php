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

namespace OrangeHRM\Tests\Attendance\Entity;

use DateTime;
use OrangeHRM\Entity\AttendanceRecord;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

class AttendanceRecordTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([AttendanceRecord::class]);
    }

    public function testAttendanceRecordEntity(): void
    {
        //punch in
        $attendanceRecord = new AttendanceRecord();
        $attendanceRecord->getDecorator()->setEmployeeByEmpNumber(1);
        $attendanceRecord->setPunchInUtcTime(new DateTime('5:30'));
        $attendanceRecord->setPunchInNote('started work');
        $attendanceRecord->setPunchInTimeOffset('+05:30');
        $attendanceRecord->setPunchInUserTime(new DateTime('11:00'));
        $attendanceRecord->setState('PUNCH IN');
        $this->persist($attendanceRecord);

        $result = $this->getRepository(AttendanceRecord::class)->find(1);
        $this->assertInstanceOf(AttendanceRecord::class, $result);
        $this->assertEquals('started work', $result->getPunchInNote());

        //punch out
        $attendanceRecord = new AttendanceRecord();
        $attendanceRecord->getDecorator()->setEmployeeByEmpNumber(1);
        $attendanceRecord->setPunchOutUtcTime(new DateTime('12:30'));
        $attendanceRecord->setPunchOutNote('ended work');
        $attendanceRecord->setPunchOutTimeOffset('+05:30');
        $attendanceRecord->setPunchOutUserTime(new DateTime('18:00'));
        $attendanceRecord->setState('PUNCH OUT');
        $this->persist($attendanceRecord);

        $result = $this->getRepository(AttendanceRecord::class)->find(2);
        $this->assertInstanceOf(AttendanceRecord::class, $result);
        $this->assertEquals('ended work', $result->getPunchOutNote());
    }
}
