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

namespace OrangeHRM\Tests\Leave\Dao;

use DateTime;
use Exception;
use Generator;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Leave;
use OrangeHRM\Entity\LeaveEntitlement;
use OrangeHRM\Entity\LeaveLeaveEntitlement;
use OrangeHRM\Entity\LeaveRequest;
use OrangeHRM\Entity\LeaveType;
use OrangeHRM\Framework\Services;
use OrangeHRM\Leave\Dao\LeaveRequestDao;
use OrangeHRM\Leave\Dto\CurrentAndChangeEntitlement;
use OrangeHRM\Leave\Dto\LeaveSearchFilterParams;
use OrangeHRM\ORM\Exception\TransactionException;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;
use OrangeHRM\Entity\LeaveStatus;

/**
 * @group Leave
 * @group Dao
 */
class LeaveRequestDaoTest extends KernelTestCase
{

  	public $leaveRequestDao ;
  	public $leaveType ;
  	public $leavePeriod ;
  	public $employee ;
        private $fixture;

    protected function setUp(): void
    {
        $this->leaveRequestDao = new LeaveRequestDao();
//            $this->leaveRequestDao->markApprovedLeaveAsTaken();
        $fixtureFile = Config::get(Config::PLUGINS_DIR)
            . '/orangehrmLeavePlugin/test/fixtures/LeaveRequestDao.yml';
        TestDataService::populate($fixtureFile);
//            $this->fixture = sfYaml::load($fixtureFile);
//            sfConfig::set('app_items_per_page', 50);
        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService()]);
    }

    /* Tests for fetchLeaveRequest() */

    public function xtestFetchLeaveRequest() {

        $leaveRequest = $this->leaveRequestDao->fetchLeaveRequest(1);

        $this->assertTrue($leaveRequest instanceof LeaveRequest);

        $this->assertEquals(1, $leaveRequest->getLeaveTypeId());
        $this->assertEquals('Casual', $leaveRequest->getLeaveTypeName());
        $this->assertEquals('2010-08-30', $leaveRequest->getDateApplied());
        $this->assertEquals(1, $leaveRequest->getEmpNumber());

    }

    /* Tests for fetchLeave() */

    public function xtestFetchLeave() {

        $leaveList = $this->leaveRequestDao->fetchLeave(1);

        foreach ($leaveList as $leave) {
            $this->assertTrue($leave instanceof Leave);
        }

        $this->assertEquals(2, count($leaveList));

        $this->assertEquals(1, $leaveList[0]->getId());
        $this->assertEquals(1, $leaveList[0]->getLeaveTypeId());
        $this->assertEquals(1, $leaveList[0]->getEmpNumber());
        $this->assertEquals('2010-09-01', $leaveList[0]->getDate());
        $this->assertEquals(1, $leaveList[0]->getStatus());

        $this->assertEquals(2, $leaveList[1]->getId());
        $this->assertEquals(1, $leaveList[1]->getLeaveTypeId());
        $this->assertEquals(1, $leaveList[1]->getEmpNumber());
        $this->assertEquals('2010-09-02', $leaveList[1]->getDate());
        $this->assertEquals(1, $leaveList[1]->getStatus());

    }

    /* Tests for getLeaveById() */

    public function xtestGetLeaveById() {

        $leave = $this->leaveRequestDao->getLeaveById(1);

        $this->assertTrue($leave instanceof Leave);

        $this->assertEquals(1, $leave->getId());
        $this->assertEquals(8, $leave->getLengthHours());
        $this->assertEquals(1, $leave->getLengthDays());
        $this->assertEquals(1, $leave->getLeaveRequestId());
        $this->assertEquals(1, $leave->getLeaveTypeId());
        $this->assertEquals(1, $leave->getEmpNumber());
        $this->assertEquals('2010-09-01', $leave->getDate());
        $this->assertEquals(1, $leave->getStatus());

    }

    /* Tests for getScheduledLeavesSum() */

    public function xtestGetScheduledLeavesSum() {

        $this->assertEquals(2.75, $this->leaveRequestDao->getScheduledLeavesSum(1, 1, 1));
        $this->assertEquals(1, $this->leaveRequestDao->getScheduledLeavesSum(2, 2, 1));

    }

    /* Tests for getTakenLeaveSum() */

    public function xtestGetTakenLeaveSum() {
        $this->assertEquals(2, $this->leaveRequestDao->getTakenLeaveSum(5, 2, 1));

    }

    public function testGetOverlappingLeaveMultipleFullDayLeave(): void
    {
        $overlapLeave = [];
        $leaveList = $this->leaveRequestDao->getOverlappingLeave(
            new DateTime('2010-01-01'),
            new DateTime('2010-12-31'),
            1
        );

        foreach ($leaveList as $leave) {
            $this->assertTrue($leave instanceof Leave);
        }

        $this->assertCount(11, $leaveList);

        $this->assertEquals(1, $leaveList[0]->getId());
        $this->assertEquals(18, $leaveList[10]->getId());
    }

    public function testGetOverlappingLeaveInSameDay1(): void
    {
        $leaveList = $this->leaveRequestDao->getOverlappingLeave(
            new DateTime('2011-01-01'),
            new DateTime('2011-01-01'),
            6,
            new DateTime('11:00:00'),
            new DateTime('12:00:00')
        );

        $this->assertCount(1, $leaveList);
        $leave = $leaveList[0];
        $this->assertEquals('Annual', $leave->getLeaveType()->getName());
        $this->assertEquals('11:00', $leave->getStartTime()->format('H:i'));
        $this->assertEquals('12:00', $leave->getEndTime()->format('H:i'));
    }

    public function testGetOverlappingLeaveInSameDay2(): void
    {
        $leaveList = $this->leaveRequestDao->getOverlappingLeave(
            new DateTime('2011-01-01'),
            new DateTime('2011-01-01'),
            6,
            new DateTime('10:00:00'),
            new DateTime('11:00:00')
        );

        $this->assertCount(1, $leaveList);
        $leave = $leaveList[0];
        $this->assertEquals('Annual', $leave->getLeaveType()->getName());
        $this->assertEquals('10:00', $leave->getStartTime()->format('H:i'));
        $this->assertEquals('11:00', $leave->getEndTime()->format('H:i'));
    }

    public function testGetOverlappingLeaveInSameDay3(): void
    {
        $leaveList = $this->leaveRequestDao->getOverlappingLeave(
            new DateTime('2011-01-01'),
            new DateTime('2011-01-01'),
            6,
            new DateTime('10:00:00'),
            new DateTime('12:00:00')
        );

        $this->assertCount(2, $leaveList);
        $leave = $leaveList[0];
        $this->assertEquals('Annual', $leave->getLeaveType()->getName());
        $this->assertEquals('10:00', $leave->getStartTime()->format('H:i'));
        $this->assertEquals('11:00', $leave->getEndTime()->format('H:i'));
        $leave = $leaveList[1];
        $this->assertEquals('Annual', $leave->getLeaveType()->getName());
        $this->assertEquals('11:00', $leave->getStartTime()->format('H:i'));
        $this->assertEquals('12:00', $leave->getEndTime()->format('H:i'));
    }

    public function testGetOverlappingLeaveInSameDay4(): void
    {
        $leaveList = $this->leaveRequestDao->getOverlappingLeave(
            new DateTime('2011-01-01'),
            new DateTime('2011-01-01'),
            6,
            new DateTime('09:15:00'),
            new DateTime('10:10:00')
        );

        $this->assertCount(1, $leaveList);
        $leave = $leaveList[0];
        $this->assertEquals('Annual', $leave->getLeaveType()->getName());
        $this->assertEquals('10:00', $leave->getStartTime()->format('H:i'));
        $this->assertEquals('11:00', $leave->getEndTime()->format('H:i'));
    }

    public function testGetOverlappingLeaveInSameDay5(): void
    {
        $leaveList = $this->leaveRequestDao->getOverlappingLeave(
            new DateTime('2011-01-01'),
            new DateTime('2011-01-01'),
            6,
            new DateTime('12:00:00'),
            new DateTime('13:00:00')
        );
        $this->assertEmpty($leaveList);
    }

    public function testGetOverlappingLeaveInSameDay6(): void
    {
        $leaveList = $this->leaveRequestDao->getOverlappingLeave(
            new DateTime('2011-01-01'),
            new DateTime('2011-01-01'),
            6,
            new DateTime('09:00:00'),
            new DateTime('10:00:00')
        );
        $this->assertEmpty($leaveList);
    }

    public function testGetOverlappingLeaveInSameDay7(): void
    {
        $leaveList = $this->leaveRequestDao->getOverlappingLeave(
            new DateTime('2011-01-01'),
            new DateTime('2011-01-01'),
            6,
            new DateTime('15:00:00'),
            new DateTime('16:00:00')
        );
        $this->assertEmpty($leaveList);
    }

    public function testGetOverlappingLeaveInSameDay8(): void
    {
        $leaveList = $this->leaveRequestDao->getOverlappingLeave(
            new DateTime('2011-01-01'),
            new DateTime('2011-01-01'),
            6
        );

        $this->assertCount(3, $leaveList);
        $leave = $leaveList[0];
        $this->assertEquals('Annual', $leave->getLeaveType()->getName());
        $this->assertEquals('10:00', $leave->getStartTime()->format('H:i'));
        $this->assertEquals('11:00', $leave->getEndTime()->format('H:i'));
        $leave = $leaveList[1];
        $this->assertEquals('Annual', $leave->getLeaveType()->getName());
        $this->assertEquals('11:00', $leave->getStartTime()->format('H:i'));
        $this->assertEquals('12:00', $leave->getEndTime()->format('H:i'));
        $leave = $leaveList[2];
        $this->assertEquals('Annual', $leave->getLeaveType()->getName());
        $this->assertEquals('13:00', $leave->getStartTime()->format('H:i'));
        $this->assertEquals('14:00', $leave->getEndTime()->format('H:i'));
    }

    public function testGetOverlappingLeaveInSameDay9(): void
    {
        $leaveList = $this->leaveRequestDao->getOverlappingLeave(
            new DateTime('2011-01-01'),
            new DateTime('2011-01-01'),
            6,
            new DateTime('10:30:00'),
            new DateTime('10:45:00')
        );

        $this->assertCount(1, $leaveList);
        $leave = $leaveList[0];
        $this->assertEquals('10:00', $leave->getStartTime()->format('H:i'));
        $this->assertEquals('11:00', $leave->getEndTime()->format('H:i'));
    }

    public function testGetOverlappingLeaveInSameDay10(): void
    {
        $leaveList = $this->leaveRequestDao->getOverlappingLeave(
            new DateTime('2011-01-01'),
            new DateTime('2011-01-01'),
            6,
            new DateTime('13:30:00'),
            new DateTime('15:00:00')
        );

        $this->assertCount(1, $leaveList);
        $leave = $leaveList[0];
        $this->assertEquals('13:00', $leave->getStartTime()->format('H:i'));
        $this->assertEquals('14:00', $leave->getEndTime()->format('H:i'));
    }

    public function testGetOverlappingLeaveInSameDay11(): void
    {
        $leaveList = $this->leaveRequestDao->getOverlappingLeave(
            new DateTime('2011-01-01'),
            new DateTime('2011-01-01'),
            6,
            new DateTime('09:00:00'),
            new DateTime('10:00:00')
        );
        $this->assertEmpty($leaveList);
    }

    public function testGetOverlappingLeaveInSameDay12(): void
    {
        $leaveList = $this->leaveRequestDao->getOverlappingLeave(
            new DateTime('2011-01-01'),
            new DateTime('2011-01-01'),
            6,
            new DateTime('09:00:00'),
            new DateTime('10:30:00')
        );

        $this->assertCount(1, $leaveList);
        $leave = $leaveList[0];
        $this->assertEquals('10:00', $leave->getStartTime()->format('H:i'));
        $this->assertEquals('11:00', $leave->getEndTime()->format('H:i'));
    }

    public function testGetOverlappingLeaveMultiDayFullNoOverlap(): void
    {
        $leaveList = $this->leaveRequestDao->getOverlappingLeave(
            new DateTime('2011-01-05'),
            new DateTime('2011-01-10'),
            6
        );
        $this->assertEmpty($leaveList);
    }

    public function testGetOverlappingLeaveMultiDayFullOverlapWithFullDay(): void
    {
        $leaveList = $this->leaveRequestDao->getOverlappingLeave(
            new DateTime('2010-08-01'),
            new DateTime('2010-08-10'),
            1
        );
        $this->assertCount(1, $leaveList);

        $dates = ['2010-08-09'];
        $this->validateLeaveListDates($leaveList, $dates);
    }

    /**
     * @param Leave[] $leaveList
     * @param string[] $dates
     */
    private function validateLeaveListDates(array $leaveList, array $dates): void
    {
        for ($i = 0; $i < count($leaveList); $i++) {
            $leave = $leaveList[$i];
            $this->assertTrue($leave instanceof Leave);
            $this->assertEquals($dates[$i], $leave->getDate()->format('Y-m-d'));
        }
    }

    public function testGetOverlappingLeaveMultiDayFullOverlapWithPartialDay(): void
    {
        $leaveList = $this->leaveRequestDao->getOverlappingLeave(
            new DateTime('2010-12-30'),
            new DateTime('2011-01-01'),
            6
        );
        $this->assertCount(3, $leaveList);

        $dates = ['2011-01-01', '2011-01-01', '2011-01-01'];
        $this->validateLeaveListDates($leaveList, $dates);
    }

    public function testGetOverlappingLeaveMultiDayPartialStartDayFullDayNonStart(): void
    {
        $leaveList = $this->leaveRequestDao->getOverlappingLeave(
            new DateTime('2010-08-01'),
            new DateTime('2010-08-10'),
            1,
            new DateTime('10:00:00'),
            new DateTime('13:00:00')
        );
        $this->assertCount(1, $leaveList);

        $dates = ['2010-08-09'];
        $this->validateLeaveListDates($leaveList, $dates);
    }

    public function testGetOverlappingLeaveMultiDayPartialStartDayFullDayNonStartNoMatch(): void
    {
        $leaveList = $this->leaveRequestDao->getOverlappingLeave(
            new DateTime('2010-08-01'),
            new DateTime('2010-08-08'),
            1,
            new DateTime('10:00:00'),
            new DateTime('13:00:00')
        );
        $this->assertEmpty($leaveList);
    }

    public function testGetOverlappingLeaveMultiDayPartialStartDayPartialDayNonStart(): void
    {
        $leaveList = $this->leaveRequestDao->getOverlappingLeave(
            new DateTime('2011-04-01'),
            new DateTime('2011-04-03'),
            6,
            new DateTime('10:00:00'),
            new DateTime('13:00:00')
        );
        $this->assertEquals(1, count($leaveList));

        $dates = ['2011-04-02'];
        $this->validateLeaveListDates($leaveList, $dates);
    }

    public function testGetOverlappingLeaveMultiDayPartialStartDayFullDayEnd(): void
    {
        $leaveList = $this->leaveRequestDao->getOverlappingLeave(
            new DateTime('2010-08-01'),
            new DateTime('2010-08-09'),
            1,
            new DateTime('10:00:00'),
            new DateTime('13:00:00')
        );
        $this->assertCount(1, $leaveList);

        $dates = ['2010-08-09'];
        $this->validateLeaveListDates($leaveList, $dates);
    }

    public function testGetOverlappingLeaveMultiDayPartialStartDayPartialDayEnd(): void
    {
        $leaveList = $this->leaveRequestDao->getOverlappingLeave(
            new DateTime('2011-03-30'),
            new DateTime('2011-04-02'),
            6,
            new DateTime('10:00:00'),
            new DateTime('13:20:00')
        );
        $this->assertCount(1, $leaveList);

        $dates = ['2011-04-02'];
        $this->validateLeaveListDates($leaveList, $dates);
    }

    public function testGetOverlappingLeaveMultiDayPartialStartDayPartialDayStartNoMatch(): void
    {
        $leaveList = $this->leaveRequestDao->getOverlappingLeave(
            new DateTime('2011-04-02'),
            new DateTime('2011-04-06'),
            6,
            new DateTime('10:00:00'),
            new DateTime('13:00:00')
        );
        $this->assertEmpty($leaveList);
    }

    public function testGetOverlappingLeaveMultiDayPartialStartDayPartialDayStart(): void
    {
        $leaveList = $this->leaveRequestDao->getOverlappingLeave(
            new DateTime('2011-04-02'),
            new DateTime('2011-04-06'),
            6,
            new DateTime('10:00:00'),
            new DateTime('13:20:00')
        );
        $this->assertCount(1, $leaveList);

        $dates = ['2011-04-02'];
        $this->validateLeaveListDates($leaveList, $dates);
    }

    public function testGetOverlappingLeaveMultiDayPartialStartDayFullDayStart(): void
    {
        $leaveList = $this->leaveRequestDao->getOverlappingLeave(
            new DateTime('2010-08-20'),
            new DateTime('2010-08-25'),
            1,
            new DateTime('10:00:00'),
            new DateTime('13:20:00')
        );
        $this->assertCount(1, $leaveList);

        $dates = ['2010-08-20'];
        $this->validateLeaveListDates($leaveList, $dates);
    }

    public function testGetOverlappingLeaveMultiDayPartialStartDayEndDay(): void
    {
        $leaveList = $this->leaveRequestDao->getOverlappingLeave(
            new DateTime('2011-01-01'),
            new DateTime('2011-04-02'),
            6,
            new DateTime('14:00:00'),
            new DateTime('15:20:00'),
            false,
            new DateTime('12:00:00'),
            new DateTime('13:00:00')
        );
        $this->assertEmpty($leaveList);
    }

    public function testGetOverlappingLeaveMultiDayPartialStartDayEndDayStartMatch(): void
    {
        $leaveList = $this->leaveRequestDao->getOverlappingLeave(
            new DateTime('2011-01-01'),
            new DateTime('2011-04-02'),
            6,
            new DateTime('13:50:00'),
            new DateTime('15:20:00'),
            false,
            new DateTime('12:00:00'),
            new DateTime('13:00:00')
        );
        $this->assertCount(1, $leaveList);

        $dates = ['2011-01-01'];
        $this->validateLeaveListDates($leaveList, $dates);
    }

    public function testGetOverlappingLeaveMultiDayPartialStartDayEndDayEndMatch(): void
    {
        $leaveList = $this->leaveRequestDao->getOverlappingLeave(
            new DateTime('2011-01-01'),
            new DateTime('2011-04-02'),
            6,
            new DateTime('14:00:00'),
            new DateTime('15:20:00'),
            false,
            new DateTime('12:10:00'),
            new DateTime('13:10:00')
        );
        $this->assertCount(1, $leaveList);

        $dates = ['2011-04-02'];
        $this->validateLeaveListDates($leaveList, $dates);
    }

    public function testGetOverlappingLeaveMultiDayPartialStartDayEndDayBothMatch()
    {
        $leaveList = $this->leaveRequestDao->getOverlappingLeave(
            new DateTime('2011-01-01'),
            new DateTime('2011-04-02'),
            6,
            new DateTime('13:50:00'),
            new DateTime('15:20:00'),
            false,
            new DateTime('12:10:00'),
            new DateTime('13:10:00')
        );
        $this->assertCount(2, $leaveList);

        $dates = ['2011-01-01', '2011-04-02'];
        $this->validateLeaveListDates($leaveList, $dates);
    }

    public function testGetOverlappingLeaveMultiDayPartialAllDaysMatchPartialDayMiddle(): void
    {
        $leaveList = $this->leaveRequestDao->getOverlappingLeave(
            new DateTime('2011-03-30'),
            new DateTime('2011-04-05'),
            6,
            new DateTime('13:50:00'),
            new DateTime('15:20:00'),
            true
        );
        $this->assertCount(1, $leaveList);

        $dates = ['2011-04-02'];
        $this->validateLeaveListDates($leaveList, $dates);
    }

    public function testGetOverlappingLeaveMultiDayPartialAllDaysMatchPartialDayStart(): void
    {
        $leaveList = $this->leaveRequestDao->getOverlappingLeave(
            new DateTime('2011-04-02'),
            new DateTime('2011-04-05'),
            6,
            new DateTime('13:50:00'),
            new DateTime('15:20:00'),
            true
        );
        $this->assertCount(1, $leaveList);

        $dates = ['2011-04-02'];
        $this->validateLeaveListDates($leaveList, $dates);
    }

    public function testGetOverlappingLeaveMultiDayPartialAllDaysMatchPartialDayEnd(): void
    {
        $leaveList = $this->leaveRequestDao->getOverlappingLeave(
            new DateTime('2011-03-25'),
            new DateTime('2011-04-02'),
            6,
            new DateTime('13:50:00'),
            new DateTime('15:20:00'),
            true
        );
        $this->assertCount(1, $leaveList);

        $dates = ['2011-04-02'];
        $this->validateLeaveListDates($leaveList, $dates);
    }

    public function testGetOverlappingLeaveMultiDayPartialAllDaysMatchFullDayMiddle()
    {
        $leaveList = $this->leaveRequestDao->getOverlappingLeave(
            new DateTime('2010-08-10'),
            new DateTime('2010-08-13'),
            1,
            new DateTime('13:50:00'),
            new DateTime('15:20:00'),
            true
        );
        $this->assertCount(1, $leaveList);

        $dates = ['2010-08-11'];
        $this->validateLeaveListDates($leaveList, $dates);
    }

    public function testGetOverlappingLeaveMultiDayPartialAllDaysMatchFullDayStart(): void
    {
        $leaveList = $this->leaveRequestDao->getOverlappingLeave(
            new DateTime('2010-08-20'),
            new DateTime('2010-08-23'),
            1,
            new DateTime('13:50:00'),
            new DateTime('15:20:00'),
            true
        );
        $this->assertCount(1, $leaveList);

        $dates = ['2010-08-20'];
        $this->validateLeaveListDates($leaveList, $dates);
    }

    public function testGetOverlappingLeaveMultiDayPartialAllDaysMatchFullDayEnd(): void
    {
        $leaveList = $this->leaveRequestDao->getOverlappingLeave(
            new DateTime('2010-08-01'),
            new DateTime('2010-08-09'),
            1,
            new DateTime('13:50:00'),
            new DateTime('15:20:00'),
            true
        );
        $this->assertCount(1, $leaveList);

        $dates = ['2010-08-09'];
        $this->validateLeaveListDates($leaveList, $dates);
    }

    public function testGetOverlappingLeaveMultiDayPartialEndDayMatchPartialDayEnd(): void
    {
        $leaveList = $this->leaveRequestDao->getOverlappingLeave(
            new DateTime('2011-03-25'),
            new DateTime('2011-04-02'),
            6,
            null,
            null,
            false,
            new DateTime('13:50:00'),
            new DateTime('15:20:00')
        );
        $this->assertCount(1, $leaveList);

        $dates = ['2011-04-02'];
        $this->validateLeaveListDates($leaveList, $dates);
    }

    public function testGetOverlappingLeaveMultiDayPartialEndDayMatchPartialDayEndNoMatch(): void
    {
        $leaveList = $this->leaveRequestDao->getOverlappingLeave(
            new DateTime('2011-03-25'),
            new DateTime('2011-04-02'),
            6,
            null,
            null,
            false,
            new DateTime('12:50:00'),
            new DateTime('13:00:00')
        );
        $this->assertEmpty($leaveList);
    }

    public function testGetOverlappingLeaveMultiDayPartialEndDayMatchFullDayEnd(): void
    {
        $leaveList = $this->leaveRequestDao->getOverlappingLeave(
            new DateTime('2010-08-01'),
            new DateTime('2010-08-09'),
            1,
            null,
            null,
            false,
            new DateTime('13:50:00'),
            new DateTime('15:20:00')
        );
        $this->assertCount(1, $leaveList);

        $dates = ['2010-08-09'];
        $this->validateLeaveListDates($leaveList, $dates);
    }

    public function testGetOverlappingLeaveMultiDayPartialEndDayMatchFullDayStart(): void
    {
        $leaveList = $this->leaveRequestDao->getOverlappingLeave(
            new DateTime('2010-08-20'),
            new DateTime('2010-08-25'),
            1,
            null,
            null,
            false,
            new DateTime('13:50:00'),
            new DateTime('15:20:00')
        );
        $this->assertCount(1, $leaveList);

        $dates = ['2010-08-20'];
        $this->validateLeaveListDates($leaveList, $dates);
    }

    public function testGetOverlappingLeaveMultiDayPartialEndDayMatchFullDayMiddle(): void
    {
        $leaveList = $this->leaveRequestDao->getOverlappingLeave(
            new DateTime('2010-08-03'),
            new DateTime('2010-08-10'),
            1,
            null,
            null,
            false,
            new DateTime('13:50:00'),
            new DateTime('15:20:00')
        );
        $this->assertCount(1, $leaveList);

        $dates = ['2010-08-09'];
        $this->validateLeaveListDates($leaveList, $dates);
    }

    /**
     * @dataProvider getTotalLeaveDurationDataProvider
     */
    public function testGetTotalLeaveDuration(int $empNumber, DateTime $date, ?float $expected): void
    {
        $duration = $this->leaveRequestDao->getTotalLeaveDuration($empNumber, $date);
        $this->assertEquals($expected, $duration);
    }

    public function getTotalLeaveDurationDataProvider(): Generator
    {
        yield [1, new DateTime('2011-01-01'), null];
        yield [6, new DateTime('2011-01-01'), 3.00];
        yield [6, new DateTime('2011-04-02'), 1.00];
    }

    public function testGetTotalLeaveDurationByChangingStatus(): void
    {
        $leave = new Leave();
        $leave->setDate(new DateTime('2011-04-02'));
        $leave->setLeaveRequest($this->getEntityReference(LeaveRequest::class, 21));
        $leave->setLeaveType($this->getEntityReference(LeaveType::class, 4));
        $leave->setEmployee($this->getEntityReference(Employee::class, 6));
        $leave->setLengthHours(1.25);
        $leave->setStatus(Leave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL);
        $this->getEntityManager()->persist($leave);
        $this->getEntityManager()->flush();
        $duration = $this->leaveRequestDao->getTotalLeaveDuration(6, new DateTime('2011-04-02'));
        $this->assertEquals(2.25, $duration);

        $leave->setStatus(Leave::LEAVE_STATUS_LEAVE_REJECTED);
        $this->getEntityManager()->persist($leave);
        $this->getEntityManager()->flush();
        $duration = $this->leaveRequestDao->getTotalLeaveDuration(6, new DateTime('2011-04-02'));
        $this->assertEquals(1, $duration);

        $leave = $this->getEntityReference(Leave::class, 33);
        $leave->setStatus(Leave::LEAVE_STATUS_LEAVE_CANCELLED);
        $this->getEntityManager()->persist($leave);
        $this->getEntityManager()->flush();
        $duration = $this->leaveRequestDao->getTotalLeaveDuration(6, new DateTime('2011-01-01'));
        $this->assertEquals(2, $duration);

        $leave = $this->getEntityReference(Leave::class, 34);
        $leave->setStatus(Leave::LEAVE_STATUS_LEAVE_HOLIDAY);
        $this->getEntityManager()->persist($leave);
        $this->getEntityManager()->flush();
        $duration = $this->leaveRequestDao->getTotalLeaveDuration(6, new DateTime('2011-01-01'));
        $this->assertEquals(1, $duration);

        $leave = $this->getEntityReference(Leave::class, 35);
        $leave->setStatus(Leave::LEAVE_STATUS_LEAVE_WEEKEND);
        $this->getEntityManager()->persist($leave);
        $this->getEntityManager()->flush();
        $duration = $this->leaveRequestDao->getTotalLeaveDuration(6, new DateTime('2011-01-01'));
        $this->assertNull($duration);
    }

    public function testGetLeavesByEmpNumberAndDates(): void
    {
        $empNumber = 6;
        $date = new DateTime('2011-01-01');
        $leaves = $this->leaveRequestDao->getLeavesByEmpNumberAndDates($empNumber, [$date]);
        $this->assertCount(3, $leaves);
        foreach ($leaves as $leave) {
            $this->assertEquals($empNumber, $leave->getEmployee()->getEmpNumber());
            $this->assertEquals($date, $leave->getDate());
        }

        $empNumber = 6;
        $dates = [new DateTime('2011-01-01'), new DateTime('2011-04-02')];
        $leaves = $this->leaveRequestDao->getLeavesByEmpNumberAndDates($empNumber, $dates);
        $this->assertCount(4, $leaves);
        foreach ($leaves as $leave) {
            $this->assertEquals($empNumber, $leave->getEmployee()->getEmpNumber());
            $this->assertContainsEquals($leave->getDate(), $dates);
        }

        $empNumber = 1;
        $dates = [new DateTime('2011-01-01'), new DateTime('2011-04-02')];
        $leaves = $this->leaveRequestDao->getLeavesByEmpNumberAndDates($empNumber, $dates);
        $this->assertEmpty($leaves);
    }

    private function _getLeaveRequestData(): array
    {
        $leaveRequest = new LeaveRequest();
        $leaveRequest->setLeaveType($this->getEntityReference(LeaveType::class, 1));
        $leaveRequest->setDateApplied(new DateTime('2010-09-01'));
        $leaveRequest->setEmployee($this->getEntityReference(Employee::class, 1));

        $leave1 = new Leave();
        $leave1->setLengthHours(8);
        $leave1->setLengthDays(1);
        $leave1->setDate(new DateTime('2010-12-01'));
        $leave1->setStatus(1);

        $leave2 = new Leave();
        $leave2->setLengthHours(6);
        $leave2->setLengthDays(0.75);
        $leave2->setDate(new DateTime('2010-12-02'));
        $leave2->setStatus(1);

        return [$leaveRequest, [$leave1, $leave2]];
    }

    public function testSaveLeaveRequestNewRequestNoEntitlement(): void
    {
        $leaveRequestIds = $this->getLeaveRequestIdsFromDb();
        $leaveIds = $this->getLeaveIdsFromDb();

        // These are the leave requests defined in the fixture (LeaveRequestDao.yml
        $expected = range(1,21);
        $this->assertEquals($expected, $leaveRequestIds);

        $leaveRequestData = $this->_getLeaveRequestData();
        /** @var LeaveRequest $request */
        $request = $leaveRequestData[0];
        /** @var Leave[] $leave */
        $leave = $leaveRequestData[1];

        $leaveRequest = $this->leaveRequestDao->saveLeaveRequest($request, $leave, new CurrentAndChangeEntitlement());
        $this->assertTrue($leaveRequest instanceof LeaveRequest);

        $leaveRequestList = $this->getNewLeaveRequests($leaveRequestIds);
        $this->assertCount(1, $leaveRequestList);
        $leaveRequest = $leaveRequestList[0];
        $this->compareLeaveRequest($request, $leaveRequest);

        $leaveList = $this->getNewLeave($leaveIds);

        $this->assertCount(count($leave), $leaveList);

        // update leave type, leave request id , emp number in leave requests
        for ($i = 0; $i < count($leave); $i++) {
            $expected = $leave[$i];
            $actual = $leaveList[$i];
            $expected->setLeaveType($request->getLeaveType());
            $expected->setEmployee($request->getEmployee());
            $expected->setId($request->getId());

            $this->compareLeave($expected, $actual);
        }
    }

    public function testSaveLeaveRequestNewRequestWithEntitlement(): void
    {
        $leaveRequestIds = $this->getLeaveRequestIdsFromDb();
        $leaveIds = $this->getLeaveIdsFromDb();

        // These are the leave requests defined in the fixture (LeaveRequestDao.yml
        $expected = range(1, 21);
        $this->assertEquals($expected, $leaveRequestIds);

        $leaveRequestData = $this->_getLeaveRequestData();
        /** @var LeaveRequest $request */
        $request = $leaveRequestData[0];
        /** @var Leave[] $leave */
        $leave = $leaveRequestData[1];

        $entitlementAssignmentIds = $this->getEntitlementAssignmentIdsFromDb();

        // entitlements to be assigned to leave
        $entitlements = [
            'current' => [
                '2010-12-01' => [1 => 0.4, 2 => 0.6],
                '2010-12-02' => [1 => 1]
            ]
        ];

        $leaveRequest = $this->leaveRequestDao->saveLeaveRequest(
            $request,
            $leave,
            new CurrentAndChangeEntitlement(
                $entitlements['current']
            )
        );
        $this->assertTrue($leaveRequest instanceof LeaveRequest);

        $leaveRequestList = $this->getNewLeaveRequests($leaveRequestIds);
        $this->assertCount(1, $leaveRequestList);
        $leaveRequest = $leaveRequestList[0];
        $this->compareLeaveRequest($request, $leaveRequest);

        $newEntitlements = $this->getNewEntitlementAssignments($entitlementAssignmentIds);
        $this->assertCount(3, $newEntitlements);

        $leaveList = $this->getNewLeave($leaveIds);

        $this->assertCount(count($leave), $leaveList);

        // update leave type, leave request id , emp number in leave requests
        for ($i = 0; $i < count($leave); $i++) {
            $expected = $leave[$i];
            $actual = $leaveList[$i];

            $this->compareLeave($expected, $actual);

            // verify entitlement assignments
            $leaveId = $actual->getId();
            $leaveEntitlements = $entitlements['current'][$expected->getDate()->format('Y-m-d')];
            $newEntitlementsForThisLeave = $this->filterEntitlementsForLeave($leaveId, $newEntitlements);
            $this->validateLeaveEntitlementAssignment($leaveId, $leaveEntitlements, $newEntitlementsForThisLeave);
        }
    }

    public function testSaveLeaveRequestNewRequestWithEntitlementChanges(): void
    {
        $leaveRequestIds = $this->getLeaveRequestIdsFromDb();
        $leaveIds = $this->getLeaveIdsFromDb();

        // These are the leave requests defined in the fixture (LeaveRequestDao.yml
        $expected = range(1, 21);
        $this->assertEquals($expected, $leaveRequestIds);

        $leaveRequestData = $this->_getLeaveRequestData();
        /** @var LeaveRequest $request */
        $request = $leaveRequestData[0];
        /** @var Leave[] $leave */
        $leave = $leaveRequestData[1];

        $entitlementAssignmentIds = $this->getEntitlementAssignmentIdsFromDb();

        $savedEntitlements = $this->getEntitlementsFromDb();
        // to avoid update Doctrine unit of work
        $savedEntitlements = array_map(
            fn(LeaveEntitlement $leaveEntitlement) => clone $leaveEntitlement,
            $savedEntitlements
        );

        // Verify all entitlements in fixture retrieved.
        $this->assertCount(4, $savedEntitlements);

        // entitlements to be assigned to leave
        $entitlements = [
            'current' => [
                '2010-12-01' => [1 => 0.4, 2 => 0.6],
                '2010-12-02' => [4 => 1]
            ],
            'change' => [
                34 => [2 => 1, 3 => 0.4, 4 => 1], // new entitlements for leave without any
                1 => [1 => 1, 2 => 1, 4 => 0.5], // changes to existing values + new
                2 => [4 => 1, 3 => 1, 2 => 1], // no changes to existing, new ones added
                4 => [] // no entitlements
            ]
        ];

        $leaveRequest = $this->leaveRequestDao->saveLeaveRequest(
            $request,
            $leave,
            new CurrentAndChangeEntitlement(
                $entitlements['current'],
                $entitlements['change']
            )
        );
        $this->assertTrue($leaveRequest instanceof LeaveRequest);

        $leaveRequestList = $this->getNewLeaveRequests($leaveRequestIds);
        $this->assertCount(1, $leaveRequestList);
        $leaveRequest = $leaveRequestList[0];
        $this->compareLeaveRequest($request, $leaveRequest);

        $newEntitlements = $this->getNewEntitlementAssignments($entitlementAssignmentIds);
        $this->assertCount(12, $newEntitlements);

        $leaveList = $this->getNewLeave($leaveIds);

        $this->assertCount(count($leave), $leaveList);

        $entitlementUsedDaysChanges = [];

        // update leave type, leave request id , emp number in leave requests
        for ($i = 0; $i < count($leave); $i++) {
            $expected = $leave[$i];
            $actual = $leaveList[$i];

            $this->compareLeave($expected, $actual);

            // verify entitlement assignments
            $leaveId = $actual->getId();
            $leaveEntitlements = $entitlements['current'][$expected->getDate()->format('Y-m-d')];
            $newEntitlementsForThisLeave = $this->filterEntitlementsForLeave($leaveId, $newEntitlements);
            $this->validateLeaveEntitlementAssignment($leaveId, $leaveEntitlements, $newEntitlementsForThisLeave);

            // update leave entitlement used days
            foreach ($leaveEntitlements as $entitlementId => $length) {
                if (!isset($entitlementUsedDaysChanges[$entitlementId])) {
                    $entitlementUsedDaysChanges[$entitlementId] = $length;
                } else {
                    $entitlementUsedDaysChanges[$entitlementId] += $length;
                }
            }
        }

        // verify entitlement changes
        foreach ($entitlements['change'] as $leaveId => $change) {
            $entitlementsForThisLeave = $this->getEntitlementAssignmentsForLeave($leaveId);
            $this->validateLeaveEntitlementAssignment($leaveId, $change, $entitlementsForThisLeave);
        }

        // Verify no entitlement has changed - since leave request status is: pending approval
        $savedEntitlementsAfter = $this->getEntitlementsFromDb();
        $this->assertEquals(count($savedEntitlements), count($savedEntitlementsAfter));

        for ($i = 0; $i < count($savedEntitlements); $i++) {
            $savedEntitlement = $savedEntitlements[$i];

            if (isset($entitlementUsedDaysChanges[$savedEntitlement->getId()])) {
                $savedEntitlement->setDaysUsed(
                    $savedEntitlement->getDaysUsed() + $entitlementUsedDaysChanges[$savedEntitlement->getId()]
                );
            }

            $this->compareEntitlement($savedEntitlement, $savedEntitlementsAfter[$i]);
        }
    }

    public function testSaveLeaveRequestNewRequestWithEntitlementChangesAndTakenLeave(): void
    {
        $leaveRequestIds = $this->getLeaveRequestIdsFromDb();
        $leaveIds = $this->getLeaveIdsFromDb();

        // These are the leave requests defined in the fixture (LeaveRequestDao.yml
        $expected = range(1, 21);
        $this->assertEquals($expected, $leaveRequestIds);

        $leaveRequestData = $this->_getLeaveRequestData();
        $request = $leaveRequestData[0];
        $leave = $leaveRequestData[1];

        // convert first leave request to taken
        $leave[0]->setStatus(Leave::LEAVE_STATUS_LEAVE_TAKEN);

        $entitlementAssignmentIds = $this->getEntitlementAssignmentIdsFromDb();

        $savedEntitlements = $this->getEntitlementsFromDb();
        // to avoid update Doctrine unit of work
        $savedEntitlements = array_map(
            fn(LeaveEntitlement $leaveEntitlement) => clone $leaveEntitlement,
            $savedEntitlements
        );

        // Verify all entitlements in fixture retrieved.
        $this->assertEquals(4, count($savedEntitlements));

        // entitlements to be assigned to leave
        $entitlements = [
            'current' => [
                '2010-12-01' => [1 => 0.4, 2 => 0.6],
                '2010-12-02' => [4 => 1]
            ],
            'change' => [
                34 => [2 => 1, 3 => 0.4, 4 => 1], // new entitlements for leave without any
                1 => [1 => 1, 2 => 1, 4 => 0.5], // changes to existing values + new
                2 => [4 => 1, 3 => 1, 2 => 1], // no changes to existing, new ones added
                4 => [] // no entitlements
            ]
        ];

        $leaveRequest = $this->leaveRequestDao->saveLeaveRequest(
            $request,
            $leave,
            new CurrentAndChangeEntitlement(
                $entitlements['current'],
                $entitlements['change']
            )
        );
        $this->assertTrue($leaveRequest instanceof LeaveRequest);

        $leaveRequestList = $this->getNewLeaveRequests($leaveRequestIds);
        $this->assertCount(1, $leaveRequestList);
        $leaveRequest = $leaveRequestList[0];
        $this->compareLeaveRequest($request, $leaveRequest);

        $newEntitlements = $this->getNewEntitlementAssignments($entitlementAssignmentIds);
        $this->assertCount(12, $newEntitlements);

        $leaveList = $this->getNewLeave($leaveIds);

        $this->assertCount(count($leave), $leaveList);

        $takenLeaveId = null;

        $entitlementUsedDaysChanges = [];

        // update leave type, leave request id , emp number in leave requests
        for ($i = 0; $i < count($leave); $i++) {
            $expected = $leave[$i];
            $actual = $leaveList[$i];

            $this->compareLeave($expected, $actual);

            // verify entitlement assignments
            $leaveId = $actual->getId();

            if ($i == 1) {
                $takenLeaveId = $leaveId;
            }

            $leaveEntitlements = $entitlements['current'][$expected->getDate()->format('Y-m-d')];
            $newEntitlementsForThisLeave = $this->filterEntitlementsForLeave($leaveId, $newEntitlements);
            $this->validateLeaveEntitlementAssignment($leaveId, $leaveEntitlements, $newEntitlementsForThisLeave);

            // update leave entitlement used days
            foreach ($leaveEntitlements as $entitlementId => $length) {
                if (!isset($entitlementUsedDaysChanges[$entitlementId])) {
                    $entitlementUsedDaysChanges[$entitlementId] = $length;
                } else {
                    $entitlementUsedDaysChanges[$entitlementId] += $length;
                }
            }
        }

        // verify entitlement changes
        foreach ($entitlements['change'] as $leaveId => $change) {
            $entitlementsForThisLeave = $this->getEntitlementAssignmentsForLeave($leaveId);
            $this->validateLeaveEntitlementAssignment($leaveId, $change, $entitlementsForThisLeave);
        }

        // Verify days_used for entitlement for leave is updated
        $this->assertTrue(!is_null($takenLeaveId));

        $savedEntitlementsAfter = $this->getEntitlementsFromDb();
        $this->assertCount(count($savedEntitlements), $savedEntitlementsAfter);

        for ($i = 0; $i < count($savedEntitlements); $i++) {
            $saved = $savedEntitlements[$i];
            $after = $savedEntitlementsAfter[$i];

            // verify used_days incremented
            $change = $entitlementUsedDaysChanges[$saved->getId()] ?? 0;
            $this->assertEquals($saved->getDaysUsed() + $change, $after->getDaysUsed());

            // Compare other fields
            $saved->setDaysUsed($saved->getDaysUsed() + $change);
            $this->compareEntitlement($saved, $after);
        }
    }

    /**
     * TODO:: handle entity manager closing
     */
    public function xtestSaveLeaveRequestAbortTransaction(): void
    {
        // Get current records
        $leaveRequestIds = $this->getLeaveRequestIdsFromDb();
        $leaveIds = $this->getLeaveIdsFromDb();
        $entitlementAssignmentIds = $this->getEntitlementAssignmentIdsFromDb();

        // These are the leave requests defined in the fixture (LeaveRequestDao.yml
        $expected = range(1, 21);
        $this->assertEquals($expected, $leaveRequestIds);

        $leaveRequestData = $this->_getLeaveRequestData();
        $request = $leaveRequestData[0];
        $leave = $leaveRequestData[1];

        // entitlements to be assigned to leave
        $entitlements = [
            'current' => [
                '2010-12-01' => [1 => 0.4, 2 => 0.6],
                '2010-12-02' => [4 => 1]
            ],
            'change' => [
                34 => [2 => 1, 3 => 0.4, 4 => 1], // new entitlements for leave without any
                1 => [111 => 1, 2 => 1, 4 => 0.5], // Transaction should abort because of this non-existing
                // entitlement id (111)
                2 => [4 => 1, 3 => 1, 2 => 1], // no changes to existing, new ones added
                4 => [] // no entitlements
            ]
        ];

        try {
            $this->leaveRequestDao->saveLeaveRequest(
                $request,
                $leave,
                new CurrentAndChangeEntitlement(
                    $entitlements['current'],
                    $entitlements['change']
                )
            );
            $this->fail('Exception expected');
        } catch (Exception $e) {
            $this->assertTrue($e instanceof TransactionException);
        }

        // verify no new records created.
        $leaveRequestList = $this->getNewLeaveRequests($leaveRequestIds);
        $this->assertEquals(0, count($leaveRequestList));

        $leaveList = $this->getNewLeave($leaveIds);
        $this->assertEquals(0, count($leaveList));

        $entitlementList = $this->getNewEntitlementAssignments($entitlementAssignmentIds);
        $this->assertEquals(0, count($entitlementList));

        // verify old records still exist
        $leaveRequestList = $this->getLeaveRequests($leaveRequestIds);
        $this->assertEquals(count($leaveRequestIds), count($leaveRequestList));

        $leaveList = $this->getLeave($leaveIds);
        $this->assertEquals(count($leaveIds), count($leaveList));

        $entitlementList = $this->getEntitlementAssignments($entitlementAssignmentIds);
        $this->assertEquals(count($entitlementAssignmentIds), count($entitlementList));
    }

    public function xtestSearchLeaveRequestsAll() {

        $searchParameters = new ParameterStub();
        $dateRange = new DateRangeStub();

        $searchParameters->setParameter('dateRange', $dateRange);
        $searchParameters->setParameter('noOfRecordsPerPage', 50);
        $searchParameters->setParameter('cmbWithTerminated','on');

        $searchResult = $this->leaveRequestDao->searchLeaveRequests($searchParameters, 1);
        $requestList = $searchResult['list'];
        $requestCount = $searchResult['meta']['record_count'];

        /* Checking type */

        foreach ($requestList as $request) {
            $this->assertTrue($request instanceof LeaveRequest);
        }

        /* Checking count */

        $this->assertEquals(21, count($requestList));
        $this->assertEquals(21, $requestCount);


        /* Checking values and order */

        $this->assertEquals(21, $requestList[0]->getId());
        $this->assertEquals(4, $requestList[0]->getLeaveTypeId());
        $this->assertEquals('2011-04-02', $requestList[0]->getDateApplied());
        $this->assertEquals(6, $requestList[0]->getEmpNumber());

        $this->assertEquals(9, $requestList[19]->getId());
        $this->assertEquals(3, $requestList[19]->getLeaveTypeId());
        $this->assertEquals('2010-06-08', $requestList[19]->getDateApplied());
        $this->assertEquals(1, $requestList[19]->getEmpNumber());

    }

    public function xtestSearchLeaveRequestsAllTerminatedEmployee() {

        $searchParameters = new ParameterStub();
        $dateRange = new DateRangeStub();

        $searchParameters->setParameter('dateRange', $dateRange);
        $searchParameters->setParameter('noOfRecordsPerPage', 50);
        $searchParameters->setParameter('cmbWithTerminated','');

        $searchResult = $this->leaveRequestDao->searchLeaveRequests($searchParameters, 1);
        $requestList = $searchResult['list'];
        $requestCount = $searchResult['meta']['record_count'];

        /* Checking type */

        foreach ($requestList as $request) {
            $this->assertTrue($request instanceof LeaveRequest);
        }

        /* Checking count */

        $this->assertEquals(10, count($requestList));
        $this->assertEquals(10, $requestCount);
    }

    public function xtestSearchLeaveRequestsDateRange() {

        $searchParameters = new ParameterStub();
        $dateRange = new DateRangeStub();
        $dateRange->setFromDate('2010-09-01');
        $dateRange->setToDate('2010-09-30');

        $searchParameters->setParameter('dateRange', $dateRange);
        $searchParameters->setParameter('noOfRecordsPerPage', 50);
        $searchParameters->setParameter('cmbWithTerminated','on');

        $searchResult = $this->leaveRequestDao->searchLeaveRequests($searchParameters, 1);
        $requestList = $searchResult['list'];
        $requestCount = $searchResult['meta']['record_count'];

        /* Checking type */

        foreach ($requestList as $request) {
            $this->assertTrue($request instanceof LeaveRequest);
        }

        /* Checking count */

        $this->assertEquals(9, count($requestList));
        $this->assertEquals(9, $requestCount);

        /* Checking values and order */

        $this->assertEquals(8, $requestList[0]->getId());
        $this->assertEquals(1, $requestList[0]->getLeaveTypeId());
        $this->assertEquals('2010-08-08', $requestList[0]->getDateApplied());
        $this->assertEquals(1, $requestList[0]->getEmpNumber());

        $this->assertEquals(1, $requestList[8]->getId());
        $this->assertEquals(1, $requestList[8]->getLeaveTypeId());
        $this->assertEquals('2010-08-30', $requestList[8]->getDateApplied());
        $this->assertEquals(1, $requestList[8]->getEmpNumber());

    }

    public function xtestSearchLeaveRequestsStates() {

        $searchParameters = new ParameterStub();
        $dateRange = new DateRangeStub();
        $dateRange->setFromDate('2010-01-01');
        $dateRange->setToDate('2010-12-31');

        $searchParameters->setParameter('dateRange', $dateRange);
        $searchParameters->setParameter('statuses', [1, -1, 3]);
        $searchParameters->setParameter('noOfRecordsPerPage', 50);
        $searchParameters->setParameter('cmbWithTerminated','on');
        $searchResult = $this->leaveRequestDao->searchLeaveRequests($searchParameters, 1);

        $requestList = $searchResult['list'];
        $requestCount = $searchResult['meta']['record_count'];

        /* Checking type */

        foreach ($requestList as $request) {
            $this->assertTrue($request instanceof LeaveRequest);
        }

        /* Checking count */

        $this->assertEquals(11, count($requestList));
        $this->assertEquals(11, $requestCount);

        /* Checking values and order */

        $this->assertEquals(17, $requestList[0]->getId());
        $this->assertEquals(2, $requestList[0]->getLeaveTypeId());
        $this->assertEquals('2010-08-15', $requestList[0]->getDateApplied());
        $this->assertEquals(5, $requestList[0]->getEmpNumber());

        $this->assertEquals(10, $requestList[8]->getId());
        $this->assertEquals(1, $requestList[8]->getLeaveTypeId());
        $this->assertEquals('2010-06-09', $requestList[8]->getDateApplied());
        $this->assertEquals(1, $requestList[8]->getEmpNumber());

    }

    public function xtestSearchLeaveRequestsEmployeeFilterId() {

        $searchParameters = new ParameterStub();
        $dateRange = new DateRangeStub();

        $searchParameters->setParameter('dateRange', $dateRange);
        $searchParameters->setParameter('employeeFilter', 1);
        $searchParameters->setParameter('noOfRecordsPerPage', 50);
        $searchParameters->setParameter('cmbWithTerminated','on');

        $searchResult = $this->leaveRequestDao->searchLeaveRequests($searchParameters, 1);
        $requestList = $searchResult['list'];
        $requestCount = $searchResult['meta']['record_count'];

        /* Checking type */

        foreach ($requestList as $request) {
            $this->assertTrue($request instanceof LeaveRequest);
        }

        /* Checking count */

        $this->assertEquals(11, count($requestList));
        $this->assertEquals(11, $requestCount);

        /* Checking values and order */

        $this->assertEquals(8, $requestList[0]->getId());
        $this->assertEquals(1, $requestList[0]->getLeaveTypeId());
        $this->assertEquals('2010-08-08', $requestList[0]->getDateApplied());
        $this->assertEquals(1, $requestList[0]->getEmpNumber());

        $this->assertEquals(9, $requestList[10]->getId());
        $this->assertEquals(3, $requestList[10]->getLeaveTypeId());
        $this->assertEquals('2010-06-08', $requestList[10]->getDateApplied());
        $this->assertEquals(1, $requestList[10]->getEmpNumber());

    }

    public function xtestSearchLeaveRequestsEmployeeFilterSingleEmployee() {

        $searchParameters = new ParameterStub();
        $dateRange = new DateRangeStub();

        $searchParameters->setParameter('dateRange', $dateRange);
        $searchParameters->setParameter('noOfRecordsPerPage', 50);
        $searchParameters->setParameter('cmbWithTerminated','on');

        $employee = new Employee();
        $employee->setEmpNumber(1);
        $searchParameters->setParameter('employeeFilter', $employee);

        $searchResult = $this->leaveRequestDao->searchLeaveRequests($searchParameters, 1);
        $requestList = $searchResult['list'];
        $requestCount = $searchResult['meta']['record_count'];

        /* Checking type */

        foreach ($requestList as $request) {
            $this->assertTrue($request instanceof LeaveRequest);
        }

        /* Checking count */

        $this->assertEquals(11, count($requestList));
        $this->assertEquals(11, $requestCount);

        /* Checking values and order */

        $this->assertEquals(8, $requestList[0]->getId());
        $this->assertEquals(1, $requestList[0]->getLeaveTypeId());
        $this->assertEquals('2010-08-08', $requestList[0]->getDateApplied());
        $this->assertEquals(1, $requestList[0]->getEmpNumber());

        $this->assertEquals(9, $requestList[10]->getId());
        $this->assertEquals(3, $requestList[10]->getLeaveTypeId());
        $this->assertEquals('2010-06-08', $requestList[10]->getDateApplied());
        $this->assertEquals(1, $requestList[10]->getEmpNumber());

    }

    public function xtestSearchLeaveRequestsEmployeeFilterMultipleEmployees() {

        $searchParameters = new ParameterStub();
        $dateRange = new DateRangeStub();

        $searchParameters->setParameter('dateRange', $dateRange);
        $searchParameters->setParameter('noOfRecordsPerPage', 50);
        $searchParameters->setParameter('cmbWithTerminated','on');

        $employee1 = new Employee();
        $employee1->setEmpNumber(1);
        $employee2 = new Employee();
        $employee2->setEmpNumber(2);

        $searchParameters->setParameter('employeeFilter', [$employee1, $employee2]);

        $searchResult = $this->leaveRequestDao->searchLeaveRequests($searchParameters, 1);
        $requestList = $searchResult['list'];
        $requestCount = $searchResult['meta']['record_count'];

        /* Checking type */

        foreach ($requestList as $request) {
            $this->assertTrue($request instanceof LeaveRequest);
        }

        /* Checking count */

        $this->assertEquals(14, count($requestList));
        $this->assertEquals(14, $requestCount);

        /* Checking values and order */

        $this->assertEquals(14, $requestList[0]->getId());
        $this->assertEquals(1, $requestList[0]->getLeaveTypeId());
        $this->assertEquals('2010-08-12', $requestList[0]->getDateApplied());
        $this->assertEquals(2, $requestList[0]->getEmpNumber());

        $this->assertEquals(9, $requestList[13]->getId());
        $this->assertEquals(3, $requestList[13]->getLeaveTypeId());
        $this->assertEquals('2010-06-08', $requestList[13]->getDateApplied());
        $this->assertEquals(1, $requestList[13]->getEmpNumber());

    }

    public function xtestSearchLeaveRequestsLeavePeriod() {

        $searchParameters = new ParameterStub();
        $dateRange = new DateRangeStub();

        $searchParameters->setParameter('dateRange', $dateRange);
        $searchParameters->setParameter('leavePeriod', 1);
        $searchParameters->setParameter('noOfRecordsPerPage', 50);
        $searchParameters->setParameter('cmbWithTerminated','on');

        $searchResult = $this->leaveRequestDao->searchLeaveRequests($searchParameters, 1);
        $requestList = $searchResult['list'];
        $requestCount = $searchResult['meta']['record_count'];

        /* Checking type */

        foreach ($requestList as $request) {
            $this->assertTrue($request instanceof LeaveRequest);
        }

        /* Checking count */

        $this->assertEquals(18, count($requestList));
        $this->assertEquals(18, $requestCount);

        /* Checking values and order */

        $this->assertEquals(17, $requestList[0]->getId());
        $this->assertEquals(2, $requestList[0]->getLeaveTypeId());
        $this->assertEquals('2010-08-15', $requestList[0]->getDateApplied());
        $this->assertEquals(5, $requestList[0]->getEmpNumber());

        $this->assertEquals(18, $requestList[17]->getId());
        $this->assertEquals(2, $requestList[17]->getLeaveTypeId());
        $this->assertEquals('2010-03-15', $requestList[17]->getDateApplied());
        $this->assertEquals(5, $requestList[17]->getEmpNumber());

    }

    public function xtestSearchLeaveRequestsLeaveType() {

        $searchParameters = new ParameterStub();
        $dateRange = new DateRangeStub();

        $searchParameters->setParameter('dateRange', $dateRange);
        $searchParameters->setParameter('leaveType', 1);
        $searchParameters->setParameter('noOfRecordsPerPage', 50);
        $searchParameters->setParameter('cmbWithTerminated','on');

        $searchResult = $this->leaveRequestDao->searchLeaveRequests($searchParameters, 1);
        $requestList = $searchResult['list'];
        $requestCount = $searchResult['meta']['record_count'];

        /* Checking type */

        foreach ($requestList as $request) {
            $this->assertTrue($request instanceof LeaveRequest);
        }

        /* Checking count */

        $this->assertEquals(10, count($requestList));
        $this->assertEquals(10, $requestCount);

        /* Checking values and order */

        $this->assertEquals(19, $requestList[0]->getId());
        $this->assertEquals(1, $requestList[0]->getLeaveTypeId());
        $this->assertEquals('2010-08-20', $requestList[0]->getDateApplied());
        $this->assertEquals(5, $requestList[0]->getEmpNumber());

        $this->assertEquals(10, $requestList[9]->getId());
        $this->assertEquals(1, $requestList[9]->getLeaveTypeId());
        $this->assertEquals('2010-06-09', $requestList[9]->getDateApplied());
        $this->assertEquals(1, $requestList[9]->getEmpNumber());

    }

    /**
     * Test funtion to verify searching for leave requests of an employee
     * in a particular subunit.
     */
    public function xtestSearchLeaveRequestsByEmployeeSubUnit() {

        $searchParameters = new ParameterObject();

        // Employees under engineering, and support (2,5) : employees 2,5,6
        $searchParameters->setParameter('subUnit', 2);
        $searchParameters->setParameter('noOfRecordsPerPage', 50);
        $leaveFixture = $this->fixture['LeaveRequest'];
        $expected = [
            $leaveFixture[20], $leaveFixture[18], $leaveFixture[19], $leaveFixture[16],
                          $leaveFixture[13], $leaveFixture[12], $leaveFixture[11],
                          $leaveFixture[15], $leaveFixture[17]
        ];

        $searchResult = $this->leaveRequestDao->searchLeaveRequests($searchParameters);
        $requestList = $searchResult['list'];
        $requestCount = $searchResult['meta']['record_count'];

        /* Checking type */

        foreach ($requestList as $request) {
            $this->assertTrue($request instanceof LeaveRequest);
        }

        /* Checking count */

        $this->assertEquals(count($expected), count($requestList));
        $this->assertEquals(count($expected), $requestCount);

        /* Checking values and order */
        $this->compareLeaveRequests($expected, $requestList);
    }

    /**
     * Test funtion to verify searching for leave requests of an employee
     * in a particular location
     */
    public function xtestSearchLeaveRequestsByLocation() {

        $searchParameters = new ParameterObject();

        // Location 1: employees 1
        $searchParameters->setParameter('locations', [1]);

        // need to include terminated since employee 1 is terminated.
        $searchParameters->setParameter('cmbWithTerminated', true);
        $searchParameters->setParameter('noOfRecordsPerPage', 50);
        $leaveFixture = $this->fixture['LeaveRequest'];
        $expected = [
            $leaveFixture[7], $leaveFixture[6], $leaveFixture[5],
                          $leaveFixture[4], $leaveFixture[3], $leaveFixture[2],
                          $leaveFixture[1], $leaveFixture[0], $leaveFixture[10],
                          $leaveFixture[9], $leaveFixture[8]
        ];

        $searchResult = $this->leaveRequestDao->searchLeaveRequests($searchParameters);
        $requestList = $searchResult['list'];
        $requestCount = $searchResult['meta']['record_count'];

        /* Checking type */

        foreach ($requestList as $request) {
            $this->assertTrue($request instanceof LeaveRequest);
        }

        /* Checking count */

        $this->assertEquals(count($expected), count($requestList));
        $this->assertEquals(count($expected), $requestCount);

        /* Checking values and order */
        $this->compareLeaveRequests($expected, $requestList);
    }

    /**
     * Test funtion to verify searching for leave requests of an employee
     * in a multiple locations
     */
    public function xtestSearchLeaveRequestsByMultipleLocations() {

        $searchParameters = new ParameterObject();

        // Location 3,4: employees 5,6
        $searchParameters->setParameter('locations', [3,4]);

        $leaveFixture = $this->fixture['LeaveRequest'];
        $expected = [
            $leaveFixture[20], $leaveFixture[18], $leaveFixture[19], $leaveFixture[16],
                          $leaveFixture[15], $leaveFixture[17]
        ];

        $searchResult = $this->leaveRequestDao->searchLeaveRequests($searchParameters);
        $requestList = $searchResult['list'];
        $requestCount = $searchResult['meta']['record_count'];

        /* Checking type */

        foreach ($requestList as $request) {
            $this->assertTrue($request instanceof LeaveRequest);
        }

        /* Checking count */

        $this->assertEquals(count($expected), count($requestList));
        $this->assertEquals(count($expected), $requestCount);

        /* Checking values and order */
        $this->compareLeaveRequests($expected, $requestList);
    }

    /**
     * Test funtion to verify searching for leave requests of by
     * Employee Name.
     */
    public function xtestSearchLeaveRequestsByEmployeeName() {

        $leaveFixture = $this->fixture['LeaveRequest'];
        $ashleyLeave = [$leaveFixture[13], $leaveFixture[12], $leaveFixture[11]];

        $tylorLandonJamesLeave = [
            $leaveFixture[18], $leaveFixture[16], $leaveFixture[15],
                                       $leaveFixture[14], $leaveFixture[17]
        ];

        $names = ['Ashley Aldis Abel', 'Aldis', 'ldis', 'Aldis', 'Abr'];
        $expectedArray = [$ashleyLeave, $ashleyLeave, $ashleyLeave, $ashleyLeave, $tylorLandonJamesLeave];

        for ($i = 0; $i < count($names); $i++) {
            $name = $names[$i];
            $expected = $expectedArray[$i];

            $searchParameters = new ParameterObject();
            $searchParameters->setParameter('employeeName', $name);

            $searchResult = $this->leaveRequestDao->searchLeaveRequests($searchParameters);
            $requestList = $searchResult['list'];
            $requestCount = $searchResult['meta']['record_count'];

            /* Checking type */

            foreach ($requestList as $request) {
                $this->assertTrue($request instanceof LeaveRequest);
            }

            /* Checking count */

            $this->assertEquals(count($expected), count($requestList));
            $this->assertEquals(count($expected), $requestCount);

            /* Checking values and order */
            $this->compareLeaveRequests($expected, $requestList);
        }
    }

    /**
     * Test the readLeave() function
     */
    public function xtestReadLeave() {

        //
        // Unavailable leave id
        //
        Doctrine_Query::create()
		->delete('*')
		->from('Leave l')
		->where('id = 999');

        $leave = $this->leaveRequestDao->readLeave(999);

        $this->assertFalse($leave, 'should return false for unavailable leave id');

        //
        // Available leave id
        //
        $leaveFixture = $this->fixture['Leave'][1];

        $savedLeave = $this->leaveRequestDao->readLeave($leaveFixture['id']);

        // Compare leave id
        $this->assertEquals($savedLeave->id, $leaveFixture['id'], 'leave id should match');

        // Compare other properties
        foreach ($leaveFixture as $property => $value) {
            $this->assertEquals($savedLeave->$property, $value, $property . ' should match ');
        }
    }

    public function xtestSaveLeave() {

        // Try and save leave with id that exists - should throw error
        $existingLeave = new Leave();
        $existingLeave->fromArray($this->fixture['Leave'][1]);

        try {
            $this->leaveRequestDao->saveLeave($existingLeave);
            $this->fail("Dao exception expected");
        } catch (DaoException $e) {
            // expected
        }

        // Try to save new leave (without id)
        $leaveRequestId = $this->fixture['LeaveRequest'][1]['id'];
        $leave = new Leave();
        $leave->length_hours = 8;
        $leave->length_days = 1;
        $leave->leave_request_id = $leaveRequestId;
        $leave->leave_type_id = $this->fixture['LeaveType'][0]['id'];
        $leave->emp_number = $this->fixture['Employee'][0]['empNumber'];
        $leave->date = '2010-09-09';
        $leave->status = 1;
        $this->leaveRequestDao->saveLeave($leave);

        // Verify id assigned
        $this->assertTrue(!empty($leave->id));


        // Verify saved by retrieving
        $result = Doctrine_Query::create()
                                    ->select()
                                    ->from('Leave l')
                                    ->where('id = ?', $leave->id)
                                    ->execute();
        $this->assertTrue($result->count() == 1);
        $this->assertTrue(is_a($result[0], 'Leave'));

        $origAsArray = $leave->toArray();
        $savedAsArray = $result[0]->toArray();

        $this->assertEquals($origAsArray, $savedAsArray);
    }

    public function xtestGetEmployeesInSubUnits() {

        $this->assertEquals([2, 6], $this->getEmployeesInSubUnits([2]));

        $this->assertEquals([1, 2, 3, 4, 5, 6], $this->getEmployeesInSubUnits([1,2,3,4,5]));

        $this->assertEquals([5], $this->getEmployeesInSubUnits([5]));
    }

    public function testChangeLeaveStatusNoEntitlementChanges(): void
    {
        $leaves = $this->getLeave([1]);
        $this->assertCount(1, $leaves);
        $savedEntitlements = $this->getEntitlementsFromDb();

        $leave = $leaves[0];
        $leave->setStatus(Leave::LEAVE_STATUS_LEAVE_CANCELLED);
        $this->leaveRequestDao->changeLeaveStatus($leave, null, false);

        $leavesAfterChange = $this->getLeave([1]);
        $this->assertCount(1, $leavesAfterChange);
        $leaveAfterChange = $leavesAfterChange[0];

        // Verify status changed
        $this->assertEquals(Leave::LEAVE_STATUS_LEAVE_CANCELLED, $leaveAfterChange->getStatus());

        // Verify no entitlement changes
        $savedEntitlementsAfter = $this->getEntitlementsFromDb();
        $this->assertCount(count($savedEntitlements), $savedEntitlementsAfter);

        for ($i = 0; $i < count($savedEntitlements); $i++) {
            $this->compareEntitlement($savedEntitlements[$i], $savedEntitlementsAfter[$i]);
        }
    }

    public function testChangeLeaveStatusNoEntitlementChangesRemoveLinked(): void
    {
        $leaveId = 1;
        $leaves = $this->getLeave([$leaveId]);
        $this->assertEquals(1, count($leaves));
        $leave = $leaves[0];

        $savedEntitlements = $this->getEntitlementsFromDb();
        // to avoid update Doctrine unit of work
        $savedEntitlements = array_map(
            fn(LeaveEntitlement $leaveEntitlement) => clone $leaveEntitlement,
            $savedEntitlements
        );

        $thisLeaveEntitlementAssignments = $this->getEntitlementAssignmentsForLeave($leaveId);
        $this->assertEquals(2, count($thisLeaveEntitlementAssignments));

        $entitlementAssignmentIds = $this->getEntitlementAssignmentIdsFromDb();

        $leave->setStatus(Leave::LEAVE_STATUS_LEAVE_CANCELLED);
        $this->leaveRequestDao->changeLeaveStatus($leave, null, true);

        $leavesAfterChange = $this->getLeave([1]);
        $this->assertCount(1, $leavesAfterChange);
        $leaveAfterChange = $leavesAfterChange[0];

        // Verify status changed
        $this->assertEquals(Leave::LEAVE_STATUS_LEAVE_CANCELLED, $leaveAfterChange->getStatus());

        // Verify entitlement links to leave removed
        $thisLeaveEntitlementIdsAfter = $this->getEntitlementAssignmentsForLeave(1);
        $this->assertCount(0, $thisLeaveEntitlementIdsAfter);

        $entitlementAssignmentIdsAfter = $this->getEntitlementAssignmentIdsFromDb();
        $this->assertCount(
            count($entitlementAssignmentIds) - count($thisLeaveEntitlementAssignments),
            $entitlementAssignmentIdsAfter
        );

        // Verify entitlement changes
        $savedEntitlementsAfter = $this->getEntitlementsFromDb();

        $this->assertCount(count($savedEntitlements), $savedEntitlementsAfter);

        for ($i = 0; $i < count($savedEntitlements); $i++) {
            $savedEntitlement = $savedEntitlements[$i];

            foreach ($thisLeaveEntitlementAssignments as $assignment) {
                if ($assignment->getEntitlement()->getId() == $savedEntitlement->getId()) {
                    $savedEntitlement->setDaysUsed($savedEntitlement->getDaysUsed() - $assignment->getLengthDays());
                }
            }

            $this->compareEntitlement($savedEntitlement, $savedEntitlementsAfter[$i]);
        }
    }

    public function testChangeLeaveStatusEntitlementChangesRemoveLinked(): void
    {
        $leaveId = 1;
        $leaves = $this->getLeave([$leaveId]);
        $this->assertEquals(1, count($leaves));
        $leave = $leaves[0];

        $savedEntitlements = $this->getEntitlementsFromDb();
        // to avoid update Doctrine unit of work
        $savedEntitlements = array_map(
            fn(LeaveEntitlement $leaveEntitlement) => clone $leaveEntitlement,
            $savedEntitlements
        );

        $thisLeaveEntitlementAssignments = $this->getEntitlementAssignmentsForLeave($leaveId);
        $this->assertEquals(2, count($thisLeaveEntitlementAssignments));

        $entitlementAssignmentIds = $this->getEntitlementAssignmentIdsFromDb();

        // entitlements to be assigned to leave
        $entitlements = new CurrentAndChangeEntitlement([], [
            34 => [2 => 1, 3 => 0.4, 4 => 1], // new entitlements for leave without any
            2 => [4 => 1, 3 => 0.5, 2 => 1], // no changes to existing, new ones added
            4 => [] // additions to existing, new ones
        ]);

        //
        // Before: entitlement id: days: days_used
        // 1: 3: 2.25
        // 2: 6: 3.5
        // 3: 1: 0
        // 4: 5: 3
        //
        // Leave id: 1 is linked to the following (entitlement id: length_days)
        // 1: 0.5
        // 2: 0.5
        //
        // Removing links for leave id: 1 results in the following:
        //
        // entitlement id: days: days_used
        // 1: 3: 1.75
        // 2: 6: 3
        // 3: 1: 0
        // 4: 5: 3
        //
        // Changes in above array: (entitlement id: delta days_used)
        //
        // 1: 0
        // 2: 2
        // 3: 0.9
        // 4: 2
        //
        // Final result should be:
        //
        // entitlement id: days: days_used
        // 1: 3: 1.75
        // 2: 6: 5
        // 3: 1: 0.9
        // 4: 5: 5
        //
        // Entitlement assignments
        // leave_id: entitlement_id: before: add: after
        // 34 : 2 : 0 : 1 : 1
        // 34 : 3 : 0 : 0.4 : 0.4
        // 34 : 4 : 0 : 1 : 1
        // 2 : 2 : 0 : 1 : 1
        // 2 : 3 : 0 : 0.5 : 0.5
        // 2 : 4 : 1 : 1 : 2

        // 4
        //

        $leave->setStatus(Leave::LEAVE_STATUS_LEAVE_CANCELLED);

        $this->leaveRequestDao->changeLeaveStatus($leave, $entitlements, true);

        $leavesAfterChange = $this->getLeave([1]);
        $this->assertEquals(1, count($leavesAfterChange));
        $leaveAfterChange = $leavesAfterChange[0];

        // Verify status changed
        $this->assertEquals(Leave::LEAVE_STATUS_LEAVE_CANCELLED, $leaveAfterChange->getStatus());

        // Verify entitlement links to leave removed
        $thisLeaveEntitlementIdsAfter = $this->getEntitlementAssignmentsForLeave(1);
        $this->assertEquals(0, count($thisLeaveEntitlementIdsAfter));

        $entitlementAssignmentIdsAfter = $this->getEntitlementAssignmentIdsFromDb();

        $newlyInsertedAssignments = 5;
        $this->assertEquals(
            count($entitlementAssignmentIds) - count($thisLeaveEntitlementAssignments) + $newlyInsertedAssignments,
            count($entitlementAssignmentIdsAfter)
        );

        // Verify entitlement changes
        $savedEntitlementsAfter = $this->getEntitlementsFromDb();

        $this->assertEquals(count($savedEntitlements), count($savedEntitlementsAfter));
        for ($i = 0; $i < count($savedEntitlements); $i++) {
            $savedEntitlement = $savedEntitlements[$i];

            // Apply changes due to removing links for changed leave
            foreach ($thisLeaveEntitlementAssignments as $assignment) {
                if ($assignment->getEntitlement()->getId() == $savedEntitlement->getId()) {
                    $savedEntitlement->setDaysUsed($savedEntitlement->getDaysUsed() - $assignment->getLengthDays());
                }
            }

            // apply changes due to specified entitlement changes
            foreach ($entitlements->getChange() as $change) {
                foreach ($change as $entitlementId => $length) {
                    if ($entitlementId == $savedEntitlement->getId()) {
                        $savedEntitlement->setDaysUsed($savedEntitlement->getDaysUsed() + $length);
                    }
                }
            }

            $this->compareEntitlement($savedEntitlement, $savedEntitlementsAfter[$i]);
        }

        // Verify entitlement assignments to leave
        $expectedEntitlementAssignments = [
            34 => [2 => 1, 3 => 0.4, 4 => 1],
            2 => [2 => 1, 3 => 0.5, 4 => 2]
        ];

        foreach ($expectedEntitlementAssignments as $leaveId => $assignments) {
            $actualAssignments = $this->getEntitlementAssignmentsForLeave($leaveId, 'l.entitlement');
            $this->assertEquals(count($assignments), count($actualAssignments));

            $i = 0;
            foreach ($assignments as $entitlementId => $length) {
                $actualAssignment = $actualAssignments[$i++];
                $this->assertEquals($entitlementId, $actualAssignment->getEntitlement()->getId());
                $this->assertEquals($length, $actualAssignment->getLengthDays());
            }
        }
    }

    /**
     * Get Employees under given subunit
     * @param array $subUnits array of subunit ids
     *
     * @return array Array of employee numbers.
     */
    protected function getEmployeesInSubUnits(array $subUnits) {
        $empNumbers = [];
        $employees = $this->fixture['Employee'];

        foreach($employees as $employee) {
            if (isset($employee['work_station']) &&
                    in_array($employee['work_station'], $subUnits)) {
                $empNumbers[] = $employee['empNumber'];
            }
        }

        return $empNumbers;
    }

    public function xtestGetLeaveRequestsForEmployees() {
        $this->assertEquals(range(1, 11),
                $this->getLeaveRequestIds($this->getLeaveRequestsForEmployees([1])));

        $this->assertEquals(range(1, 14),
                $this->getLeaveRequestIds($this->getLeaveRequestsForEmployees([1, 2])));

        $this->assertEquals(
            [20],
            $this->getLeaveRequestIds($this->getLeaveRequestsForEmployees([6])));

        $this->assertEquals(range(16, 19),
                $this->getLeaveRequestIds($this->getLeaveRequestsForEmployees([5])));

    }

    protected function getLeaveRequestsForEmployees($empNumbers) {

        $leaveRequests = [];
        $allLeaveRequests = $this->fixture['LeaveRequest'];

        foreach($allLeaveRequests as $request) {
            if (in_array($request['empNumber'], $empNumbers)) {
                $leaveRequests[] = $request;
            }
        }

        return $leaveRequests;
    }

    protected function getLeaveRequestIds($leaveRequests) {
        $ids = [];
        foreach ($leaveRequests as $request) {
            $ids[] = $request['leave_request_id'];
        }

        return $ids;
    }

    protected function compareLeaveRequests($expected, $requestList) {
        $this->assertEquals(count($expected), count($requestList));

        for ($i = 0; $i < count($expected); $i++) {

            $item = $expected[$i];
            $result = $requestList[$i];
            $str = $item['id'] . '->' . $result->getId() . "\n" .
            $item['date_applied'] . '->' . $result->getDateApplied() . "\n" .
            $item['emp_number'] . '->' . $result->getEmpNumber() . "\n" .
            $item['comments'] . '->' . $result->getComments() . "\n\n";

            //echo $str;

            $this->assertEquals($item['id'], $result->getId());
            $this->assertEquals($item['leave_type_id'], $result->getLeaveTypeId());
            $this->assertEquals($item['date_applied'], $result->getDateApplied());
            $this->assertEquals($item['emp_number'], $result->getEmpNumber());
            $this->assertEquals($item['comments'], $result->getComments());
        }
    }

    /**
     * @return int[]
     */
    protected function getLeaveRequestIdsFromDb(): array
    {
        $q = $this->getEntityManager()
            ->getRepository(LeaveRequest::class)
            ->createQueryBuilder('lr')
            ->select('lr.id');
        return array_column($q->getQuery()->getScalarResult(), 'id');
    }

    /**
     * @return int[]
     */
    protected function getLeaveIdsFromDb(): array
    {
        $q = $this->getEntityManager()
            ->getRepository(Leave::class)
            ->createQueryBuilder('l')
            ->select('l.id');
        return array_column($q->getQuery()->getScalarResult(), 'id');
    }

    /**
     * @return int[]
     */
    protected function getEntitlementAssignmentIdsFromDb(): array
    {
        $q = $this->getEntityManager()
            ->getRepository(LeaveLeaveEntitlement::class)
            ->createQueryBuilder('le')
            ->select('le.id');
        return array_column($q->getQuery()->getScalarResult(), 'id');
    }

    /**
     * @return LeaveEntitlement[]
     */
    protected function getEntitlementsFromDb(): array
    {
        $this->getEntityManager()->clear(LeaveEntitlement::class);
        $q = $this->getEntityManager()
            ->getRepository(LeaveEntitlement::class)
            ->createQueryBuilder('le')
            ->addOrderBy('le.id');

        return $q->getQuery()->execute();
    }

    /**
     * @param int[] $existingIds
     * @return LeaveRequest[]
     */
    protected function getNewLeaveRequests(array $existingIds): array
    {
        $q = $this->getEntityManager()
            ->getRepository(LeaveRequest::class)
            ->createQueryBuilder('lr')
            ->addOrderBy('lr.id');
        $q->andWhere($q->expr()->notIn('lr.id', ':ids'))
            ->setParameter('ids', $existingIds);

        return $q->getQuery()->execute();
    }

    /**
     * @param int[] $ids
     * @return LeaveRequest[]
     */
    protected function getLeaveRequests(array $ids): array
    {
        $q = $this->getEntityManager()
            ->getRepository(LeaveRequest::class)
            ->createQueryBuilder('lr')
            ->addOrderBy('lr.id');
        $q->andWhere($q->expr()->in('lr.id', ':ids'))
            ->setParameter('ids', $ids);

        return $q->getQuery()->execute();
    }

    /**
     * @param int[] $existingIds
     * @return Leave[]
     */
    protected function getNewLeave(array $existingIds): array
    {
        $q = $this->getEntityManager()
            ->getRepository(Leave::class)
            ->createQueryBuilder('l')
            ->addOrderBy('l.id');
        $q->andWhere($q->expr()->notIn('l.id', ':ids'))
            ->setParameter('ids', $existingIds);

        return $q->getQuery()->execute();
    }

    /**
     * @param int[] $ids
     * @return Leave[]
     */
    protected function getLeave(array $ids): array
    {
        $q = $this->getEntityManager()
            ->getRepository(Leave::class)
            ->createQueryBuilder('l')
            ->addOrderBy('l.id');
        $q->andWhere($q->expr()->in('l.id', ':ids'))
            ->setParameter('ids', $ids);

        return $q->getQuery()->execute();
    }

    /**
     * @param int[] $existingIds
     * @return LeaveLeaveEntitlement[]
     */
    protected function getNewEntitlementAssignments(array $existingIds): array
    {
        $q = $this->getEntityManager()
            ->getRepository(LeaveLeaveEntitlement::class)
            ->createQueryBuilder('l')
            ->addOrderBy('l.id');
        $q->andWhere($q->expr()->notIn('l.id', ':ids'))
            ->setParameter('ids', $existingIds);

        return $q->getQuery()->execute();
    }

    /**
     * @param int[] $ids
     * @return LeaveLeaveEntitlement[]
     */
    protected function getEntitlementAssignments(array $ids): array
    {
        $q = $this->getEntityManager()
            ->getRepository(LeaveLeaveEntitlement::class)
            ->createQueryBuilder('l')
            ->addOrderBy('l.id');
        $q->andWhere($q->expr()->in('l.id', ':ids'))
            ->setParameter('ids', $ids);

        return $q->getQuery()->execute();
    }

    /**
     * @param int $leaveId
     * @param string $sortField
     * @return LeaveLeaveEntitlement[]
     */
    protected function getEntitlementAssignmentsForLeave(int $leaveId, string $sortField = 'l.id'): array
    {
        $q = $this->getEntityManager()
            ->getRepository(LeaveLeaveEntitlement::class)
            ->createQueryBuilder('l')
            ->addOrderBy($sortField)
            ->andWhere('l.leave = :leaveId')
            ->setParameter('leaveId', $leaveId);

        return $q->getQuery()->execute();
    }

    protected function compareLeaveRequest(LeaveRequest $expected, LeaveRequest $result): void
    {
        $this->assertTrue($result instanceof LeaveRequest);

        $expectedId = $expected->getId();

        if (!empty($expectedId)) {
            $this->assertEquals($expectedId, $result->getId());
        } else {
            $leaveRequestId = $result->getId();
            $this->assertTrue(!empty($leaveRequestId));
        }

        $this->assertEquals($expected->getLeaveType()->getId(), $result->getLeaveType()->getId());
        $this->assertEquals($expected->getDateApplied(), $result->getDateApplied());
        $this->assertEquals($expected->getEmployee()->getEmpNumber(), $result->getEmployee()->getEmpNumber());
    }

    /**
     * @param Leave $expected
     * @param Leave $result
     */
    protected function compareLeave(Leave $expected, Leave $result): void
    {
        $this->assertTrue($result instanceof Leave);

        $expectedId = $expected->getId();

        if (!empty($expectedId)) {
            $this->assertEquals($expectedId, $result->getId());
        } else {
            $leaveId = $result->getId();
            $this->assertTrue(!empty($leaveId));
        }

        $this->assertEquals($expected->getLeaveType()->getId(), $result->getLeaveType()->getId());
        $this->assertEquals($expected->getDate(), $result->getDate());
        $this->assertEquals($expected->getEmployee()->getEmpNumber(), $result->getEmployee()->getEmpNumber());
        $this->assertEquals($expected->getLengthHours(), $result->getLengthHours());
        $this->assertEquals($expected->getLengthDays(), $result->getLengthDays());
        $this->assertEquals($expected->getStatus(), $result->getStatus());
        $this->assertEquals($expected->getLeaveRequest()->getId(), $result->getLeaveRequest()->getId());
    }

    /**
     * @param int $leaveId
     * @param LeaveLeaveEntitlement[] $expectedEntitlements
     * @param LeaveLeaveEntitlement[] $newEntitlements
     */
    protected function validateLeaveEntitlementAssignment(
        int $leaveId,
        array $expectedEntitlements,
        array $newEntitlements
    ): void {
        $this->assertCount(count($expectedEntitlements), $newEntitlements);
        $usedEntitlements = [];

        foreach ($expectedEntitlements as $entitlementId => $length) {
            $found = false;

            foreach ($newEntitlements as $new) {
                if (!in_array($new->getEntitlement()->getId(), $usedEntitlements)) {
                    $this->assertEquals($leaveId, $new->getLeave()->getId());

                    if ($new->getEntitlement()->getId() == $entitlementId) {
                        $found = true;
                        $usedEntitlements[] = $new->getEntitlement()->getId();
                        $this->assertEquals($length, $new->getLengthDays());
                        break;
                    }
                }
            }

            $this->assertTrue($found);
        }
    }

    /**
     * @param int $leaveId
     * @param LeaveLeaveEntitlement[] $newEntitlements
     * @return LeaveLeaveEntitlement[]
     */
    protected function filterEntitlementsForLeave(int $leaveId, array $newEntitlements): array
    {
        $filteredEntitlements = [];
        foreach ($newEntitlements as $entitlement) {
            if ($entitlement->getLeave()->getId() == $leaveId) {
                $filteredEntitlements[] = $entitlement;
            }
        }
        return $filteredEntitlements;
    }

    /**
     * @param LeaveEntitlement $expected
     * @param LeaveEntitlement $actual
     */
    protected function compareEntitlement(LeaveEntitlement $expected, LeaveEntitlement $actual): void
    {
        $this->assertEquals($expected->getId(), $actual->getId());
        $this->assertEquals($expected->getEmployee()->getEmpNumber(), $actual->getEmployee()->getEmpNumber());
        $this->assertEquals($expected->getNoOfDays(), $actual->getNoOfDays());
        $this->assertEquals($expected->getDaysUsed(), $actual->getDaysUsed());
        $this->assertEquals($expected->getLeaveType()->getId(), $actual->getLeaveType()->getId());
        $this->assertEquals($expected->getFromDate(), $actual->getFromDate());
        $this->assertEquals($expected->getToDate(), $actual->getToDate());
        $this->assertEquals($expected->getCreditedDate(), $actual->getCreditedDate());
        $this->assertEquals($expected->getNote(), $actual->getNote());
        $this->assertEquals($expected->getEntitlementType()->getName(), $actual->getEntitlementType()->getName());
        $this->assertEquals($expected->isDeleted(), $actual->isDeleted());
    }

    public function xtestGetLeaveRecordsBetweenTwoDays() {
         $employeeId = 1;
         $fromDate = '2010-09-01';
         $toDate = '2010-09-21';
         $status =[-1,0,1,2,3];
         $leaveRecords = $this->leaveRequestDao->getLeaveRecordsBetweenTwoDays($fromDate,$toDate,$employeeId,$status);
         $this->assertEquals(10,count($leaveRecords));
     }

     public function xtestGetLeaveRecordsBetweenTwoDaysForOnlySelectedStates() {
         $employeeId = 1;
         $fromDate = '2010-09-01';
         $toDate = '2010-09-21';
         $status =[1];
         $leaveRecords = $this->leaveRequestDao->getLeaveRecordsBetweenTwoDays($fromDate,$toDate,$employeeId,$status);
         $this->assertEquals(2,count($leaveRecords));
     }

    public function testGetLeaveRequestsByEmpNumberAndDateRange(): void
    {
        $empNumber = 1;
        $fromDate = new DateTime('2010-09-01');
        $toDate = new DateTime('2010-09-21');
        $leaveRequests = $this->leaveRequestDao->getLeaveRequestsByEmpNumberAndDateRange(
            $empNumber,
            $fromDate,
            $toDate
        );
        $this->assertCount(6, $leaveRequests);
        foreach ($leaveRequests as $i => $leaveRequest) {
            $this->assertEquals($i + 1, $leaveRequest->getId());
            $this->assertEquals($empNumber, $leaveRequest->getEmployee()->getEmpNumber());
            foreach ($leaveRequest->getLeaves() as $leave) {
                $this->assertGreaterThanOrEqual($fromDate, $leave->getDate());
                $this->assertLessThanOrEqual($toDate, $leave->getDate());
            }
        }
    }

    public function testGetLeavesByLeaveRequestIds(): void
    {
        $result = $this->leaveRequestDao->getLeavesByLeaveRequestIds([5]);
        $this->assertCount(3, $result);
        foreach ($result as $i => $leave) {
            $this->assertEquals(5, $leave->getLeaveRequest()->getId());
            $this->assertEquals(1, $leave->getEmployee()->getEmpNumber());
            $this->assertEquals(new DateTime('2010-09-' . (15 + $i)), $leave->getDate());
        }

        $result = $this->leaveRequestDao->getLeavesByLeaveRequestIds([1, 2]);
        $this->assertCount(4, $result);
        foreach ($result as $leave) {
            $this->assertContains($leave->getLeaveRequest()->getId(), [1, 2]);
            $this->assertEquals(1, $leave->getEmployee()->getEmpNumber());
        }

        $result = $this->leaveRequestDao->getLeavesByLeaveRequestIds([1, 1000]);
        $this->assertCount(2, $result);
        foreach ($result as $leave) {
            $this->assertEquals(1, $leave->getLeaveRequest()->getId());
            $this->assertEquals(1, $leave->getEmployee()->getEmpNumber());
        }

        $result = $this->leaveRequestDao->getLeavesByLeaveRequestIds([1000]);
        $this->assertEmpty($result);
    }

    public function testGetAllLeaveStatuses()
    {
        $status1 = new LeaveStatus();
        $status1->setId(1);
        $status1->setStatus(-1);
        $status1->setName('REJECTED');
        $status2 = new LeaveStatus();
        $status2->setId(2);
        $status2->setStatus(0);
        $status2->setName('CANCELLED');
        $status3 = new LeaveStatus();
        $status3->setId(3);
        $status3->setStatus(1);
        $status3->setName('PENDING APPROVAL');
        $status4 = new LeaveStatus();
        $status4->setId(4);
        $status4->setStatus(2);
        $status4->setName('SCHEDULED');
        $status5 = new LeaveStatus();
        $status5->setId(5);
        $status5->setStatus(3);
        $status5->setName('TAKEN');
        $status6 = new LeaveStatus();
        $status6->setId(6);
        $status6->setStatus(4);
        $status6->setName('WEEKEND');
        $status7 = new LeaveStatus();
        $status7->setId(7);
        $status7->setStatus(5);
        $status7->setName('HOLIDAY');
        $result = [$status1,$status2,$status3,$status4,$status5,$status6,$status7];
        $this->assertEquals($result,$this->leaveRequestDao->getAllLeaveStatuses());
    }

    public function testGetLeaveRequestById()
    {
        $this->assertEquals(1, $this->leaveRequestDao->getLeaveRequestById(1)->getId());
        $this->assertEquals(1, $this->leaveRequestDao->getLeaveRequestById(1)->getLeaveType()->getId());
        $this->assertEquals('2010-08-30', $this->leaveRequestDao->getLeaveRequestById(1)->getDateApplied()->format('Y-m-d'));
        $this->assertEquals(1, $this->leaveRequestDao->getLeaveRequestById(1)->getEmployee()->getEmpNumber());

        $this->assertEquals(null, $this->leaveRequestDao->getLeaveRequestById(22));
    }

    public function testGetLeavesByLeaveRequestId()
    {
        $leaveRequestIds = $this->getLeaveRequestIdsFromDb();
        $allLeaves = $this->getAllLeaves();
        foreach ($leaveRequestIds as $leaveRequestId){
            $leaves = $this->leaveRequestDao->getLeavesByLeaveRequestId($leaveRequestId);
            foreach ($leaves as $leave){
                $this->assertEquals($leaveRequestId,$leave->getLeaveRequest()->getId());
            }
            foreach ($allLeaves as $leave){
                if(!in_array($leave,$leaves)){
                    $this->assertNotEquals($leaveRequestId, $leave->getLeaveRequest()->getId());
                }
            }
        }
    }

    /**
     * @return Leave[]
     */
    protected function getAllLeaves(): array
    {
        $q = $this->getEntityManager()
            ->getRepository(Leave::class)
            ->createQueryBuilder('l');
        return $q->getQuery()->execute();
    }

    public function testGetLeaves()
    {
        //check leave request id
        $leaveRequestIds = $this->getLeaveRequestIdsFromDb();
        $allLeaves = $this->getAllLeaves();
        foreach ($leaveRequestIds as $leaveRequestId){
            $leaveSearchFilterParams = new LeaveSearchFilterParams();
            $leaveSearchFilterParams->setLeaveRequestId($leaveRequestId);
            $leaves = $this->leaveRequestDao->getLeaves($leaveSearchFilterParams);
            foreach ($leaves as $leave){
                $this->assertEquals($leaveRequestId,$leave->getLeaveRequest()->getId());
            }
            foreach ($allLeaves as $leave){
                if(!in_array($leave,$leaves)){
                    $this->assertNotEquals($leaveRequestId, $leave->getLeaveRequest()->getId());
                }
            }
        }

        //check limit
        $leaveSearchFilterParams = new LeaveSearchFilterParams();
        $leaveSearchFilterParams->setLimit(5);
        $leaves = $this->leaveRequestDao->getLeaves($leaveSearchFilterParams);
        $this->assertCount(5,$leaves);
        $ids = [];
        foreach ($leaves as $leave){
            array_push($ids, $leave->getId());
        }
        $this->assertEquals([29,30,14,15,16],$ids);

        //check limit & offset 0
        $leaveSearchFilterParams = new LeaveSearchFilterParams();
        $leaveSearchFilterParams->setOffset(0);
        $leaveSearchFilterParams->setLimit(5);
        $leaves = $this->leaveRequestDao->getLeaves($leaveSearchFilterParams);
        $this->assertCount(5,$leaves);
        $ids = [];
        foreach ($leaves as $leave){
            array_push($ids, $leave->getId());
        }
        $this->assertEquals([29,30,14,15,16],$ids);

        //check limit & offset 1
        $leaveSearchFilterParams = new LeaveSearchFilterParams();
        $leaveSearchFilterParams->setOffset(1);
        $leaveSearchFilterParams->setLimit(5);
        $leaves = $this->leaveRequestDao->getLeaves($leaveSearchFilterParams);
        $this->assertCount(5,$leaves);
        $ids = [];
        foreach ($leaves as $leave){
            array_push($ids, $leave->getId());
        }
        $this->assertEquals([30,14,15,16,17],$ids);

        //check limit & offset 2
        $leaveSearchFilterParams = new LeaveSearchFilterParams();
        $leaveSearchFilterParams->setOffset(2);
        $leaveSearchFilterParams->setLimit(5);
        $leaves = $this->leaveRequestDao->getLeaves($leaveSearchFilterParams);
        $this->assertCount(5,$leaves);
        $ids = [];
        foreach ($leaves as $leave){
            array_push($ids, $leave->getId());
        }
        $this->assertEquals([14,15,16,17,18],$ids);
    }

    public function testGetLeavesCount()
    {
        $leaveSearchFilterParams = new LeaveSearchFilterParams();
        $leaveSearchFilterParams->setLeaveRequestId(4);
        $result = $this->leaveRequestDao->getLeavesCount($leaveSearchFilterParams);
        $this->assertEquals(1,$result);

        $leaveSearchFilterParams->setLeaveRequestId(5);
        $result = $this->leaveRequestDao->getLeavesCount($leaveSearchFilterParams);
        $this->assertEquals(3,$result);
    }

    public function testGetLeaveRequestsByLeaveRequestIds()
    {
        $leaveRequestIds = [1,4,3];
        $leaveRequests = $this->leaveRequestDao->getLeaveRequestsByLeaveRequestIds($leaveRequestIds);
        $this->assertEquals($this->getLeaveRequests($leaveRequestIds),$leaveRequests);
    }

    public function testGetLeavesByLeaveIds()
    {
        $allLeaves = $this->getAllLeaves();
        $leaveIds = [1,4,3];
        $leaves = $this->leaveRequestDao->getLeavesByLeaveIds($leaveIds);
        foreach ($allLeaves as $leave){
            $this->assertTrue(!in_array($leave,$leaves)||in_array($leave->getId(),$leaveIds));
            $this->assertTrue(in_array($leave,$leaves)||!in_array($leave->getId(),$leaveIds));
        }
    }
}


 class ParameterStub {

     private $dateRange;
     private $statuses;
     private $employeeFilter;
     private $leavePeriod;
     private $leaveType;

     public function setParameter($property, $value) {
         $this->$property = $value;
     }

     public function getParameter($property) {
         return $this->$property;
     }

 }

 class DateRangeStub {

     private $fromDate;
     private $toDate;

     public function setFromDate($fromDate) {
         $this->fromDate = $fromDate;
     }

     public function getFromDate() {
         return $this->fromDate;
     }

     public function setToDate($toDate) {
         $this->toDate = $toDate;
     }

     public function getToDate() {
         return $this->toDate;
     }

 }
