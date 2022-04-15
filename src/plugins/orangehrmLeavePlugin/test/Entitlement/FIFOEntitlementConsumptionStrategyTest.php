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

namespace OrangeHRM\Tests\Leave\Entitlement;

use DateTime;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Leave;
use OrangeHRM\Entity\LeaveEntitlement;
use OrangeHRM\Entity\LeaveEntitlementType;
use OrangeHRM\Leave\Dao\LeaveEntitlementDao;
use OrangeHRM\Leave\Dto\CurrentAndChangeEntitlement;
use OrangeHRM\Leave\Entitlement\FIFOEntitlementConsumptionStrategy;
use OrangeHRM\Leave\Service\LeaveEntitlementService;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Leave
 */
class FIFOEntitlementConsumptionStrategyTest extends TestCase
{
    private $strategy;

    /**
     * Set up method
     */
    protected function setUp(): void
    {
        $this->strategy = new FIFOEntitlementConsumptionStrategy();
        $this->fixture = Config::get(Config::PLUGINS_DIR) .
            '/orangehrmLeavePlugin/test/fixtures/FIFOEntitlementStrategy.yml';
        TestDataService::populate($this->fixture);
    }

    public function testHandleLeaveStatusChangeNoEntitlements(): void
    {
        $empNumber = 1;
        $leaveType = 2;

        $leave1 = new Leave();
        $leave1->setDate(new DateTime('2012-09-11'));
        $leave1->setLengthDays(1);

        $leave2 = new Leave();
        $leave2->setDate(new DateTime('2012-09-12'));
        $leave2->setLengthDays(1);

        $leaveDates = [$leave1, $leave2];
        $entitlements = [];

        $mockDao = $this->getMockBuilder(LeaveEntitlementDao::class)
            ->onlyMethods(['getValidLeaveEntitlements'])
            ->getMock();
        $mockDao->expects($this->exactly(2))
            ->method('getValidLeaveEntitlements')
            ->with(
                $empNumber,
                $leaveType,
                new DateTime('2012-09-11'),
                new DateTime('2012-09-12'),
                'le.fromDate',
                'ASC'
            )
            ->will($this->returnValue($entitlements));

        $mockService = $this->getMockBuilder(LeaveEntitlementService::class)
            ->onlyMethods(['getLeaveEntitlementDao'])
            ->getMock();
        $mockService->expects($this->exactly(2))
            ->method('getLeaveEntitlementDao')
            ->willReturn($mockDao);

        $this->strategy = $this->getMockBuilder(FIFOEntitlementConsumptionStrategy::class)
            ->onlyMethods(['getLeaveEntitlementService'])
            ->getMock();
        $this->strategy->expects($this->exactly(2))
            ->method('getLeaveEntitlementService')
            ->willReturn($mockService);

        // Apply, $allowNoEntitlements = false
        $results = $this->strategy->handleLeaveCreate($empNumber, $leaveType, $leaveDates);
        $this->assertNull($results);

        // Assign, $allowNoEntitlements = true
        $results = $this->strategy->handleLeaveCreate($empNumber, $leaveType, $leaveDates, true);
        $this->assertEquals(new CurrentAndChangeEntitlement(), $results);
    }

    public function testHandleLeaveStatusChangeOneEntitlementBeforeApply(): void
    {
        $empNumber = 1;
        $leaveType = 2;

        $leave1 = new Leave();
        $leave1->setDate(new DateTime('2012-09-11'));
        $leave1->setLengthDays(1);

        $leave2 = new Leave();
        $leave2->setDate(new DateTime('2012-09-12'));
        $leave2->setLengthDays(1);

        $leaveDates = [$leave1, $leave2];

        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $employee->setFirstName('Kayla');
        $employee->setLastName('Abbey');

        $leaveEntitlementType = new LeaveEntitlementType();
        $leaveEntitlementType->setId(1);
        $entitlement1 = new LeaveEntitlement();
        $entitlement1->setId(1);
        $entitlement1->setNoOfDays(3);
        $entitlement1->setDaysUsed(0);
        $entitlement1->setFromDate(new DateTime('2012-09-01'));
        $entitlement1->setToDate(new DateTime('2012-09-11'));
        $entitlement1->setCreditedDate(new DateTime('2012-05-01'));
        $entitlement1->setEmployee($employee);
        $entitlement1->setEntitlementType($leaveEntitlementType);

        $entitlements = [$entitlement1];

        $mockDao = $this->getMockBuilder(LeaveEntitlementDao::class)
            ->onlyMethods(['getValidLeaveEntitlements'])
            ->getMock();
        $mockDao->expects($this->once())
            ->method('getValidLeaveEntitlements')
            ->with(
                $empNumber,
                $leaveType,
                new DateTime('2012-09-11'),
                new DateTime('2012-09-12'),
                'le.fromDate',
                'ASC'
            )
            ->will($this->returnValue($entitlements));

        $mockService = $this->getMockBuilder(LeaveEntitlementService::class)
            ->onlyMethods(['getLeaveEntitlementDao'])
            ->getMock();
        $mockService->expects($this->once())
            ->method('getLeaveEntitlementDao')
            ->willReturn($mockDao);

        $this->strategy = $this->getMockBuilder(FIFOEntitlementConsumptionStrategy::class)
            ->onlyMethods(['getLeaveEntitlementService'])
            ->getMock();
        $this->strategy->expects($this->once())
            ->method('getLeaveEntitlementService')
            ->willReturn($mockService);

        // Apply, $allowNoEntitlements = false
        $results = $this->strategy->handleLeaveCreate($empNumber, $leaveType, $leaveDates);
        $this->assertNull($results);
    }

    public function testHandleLeaveStatusChangeOneEntitlementBeforeAssign(): void
    {
        $empNumber = 1;
        $leaveType = 2;

        $leave1 = new Leave();
        $leave1->setDate(new DateTime('2012-09-11'));
        $leave1->setLengthDays(1);

        $leave2 = new Leave();
        $leave2->setDate(new DateTime('2012-09-12'));
        $leave2->setLengthDays(1);

        $leaveDates = [$leave1, $leave2];

        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $employee->setFirstName('Kayla');
        $employee->setLastName('Abbey');

        $leaveEntitlementType = new LeaveEntitlementType();
        $leaveEntitlementType->setId(1);
        $entitlement1 = new LeaveEntitlement();
        $entitlement1->setId(5);
        $entitlement1->setNoOfDays(3);
        $entitlement1->setDaysUsed(0);
        $entitlement1->setFromDate(new DateTime('2012-09-01'));
        $entitlement1->setToDate(new DateTime('2012-09-11'));
        $entitlement1->setCreditedDate(new DateTime('2012-05-01'));
        $entitlement1->setEmployee($employee);
        $entitlement1->setEntitlementType($leaveEntitlementType);

        $entitlements = [$entitlement1];

        $mockDao = $this->getMockBuilder(LeaveEntitlementDao::class)
            ->onlyMethods(['getValidLeaveEntitlements'])
            ->getMock();
        $mockDao->expects($this->once())
            ->method('getValidLeaveEntitlements')
            ->with(
                $empNumber,
                $leaveType,
                new DateTime('2012-09-11'),
                new DateTime('2012-09-12'),
                'le.fromDate',
                'ASC'
            )
            ->will($this->returnValue($entitlements));

        $mockService = $this->getMockBuilder(LeaveEntitlementService::class)
            ->onlyMethods(['getLeaveEntitlementDao'])
            ->getMock();
        $mockService->expects($this->once())
            ->method('getLeaveEntitlementDao')
            ->willReturn($mockDao);

        $this->strategy = $this->getMockBuilder(FIFOEntitlementConsumptionStrategy::class)
            ->onlyMethods(['getLeaveEntitlementService'])
            ->getMock();
        $this->strategy->expects($this->once())
            ->method('getLeaveEntitlementService')
            ->willReturn($mockService);

        // Assign, $allowNoEntitlements = true
        $results = $this->strategy->handleLeaveCreate($empNumber, $leaveType, $leaveDates, true);
        $this->assertTrue($results instanceof CurrentAndChangeEntitlement);

        $current = ['2012-09-11' => [5 => 1]];
        $changes = [];

        $this->verifyEntitlements(
            ['change' => $results->getChange(), 'current' => $results->getCurrent()],
            $current,
            $changes
        );
    }

    public function testHandleLeaveStatusChangeOneEntitlementAfterApply(): void
    {
        $empNumber = 1;
        $leaveType = 2;

        $leave1 = new Leave();
        $leave1->setDate(new DateTime('2012-09-11'));
        $leave1->setLengthDays(1);

        $leave2 = new Leave();
        $leave2->setDate(new DateTime('2012-09-12'));
        $leave2->setLengthDays(1);

        $leaveDates = [$leave1, $leave2];

        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $employee->setFirstName('Kayla');
        $employee->setLastName('Abbey');

        $leaveEntitlementType = new LeaveEntitlementType();
        $leaveEntitlementType->setId(1);
        $entitlement1 = new LeaveEntitlement();
        $entitlement1->setId(3);
        $entitlement1->setNoOfDays(4);
        $entitlement1->setDaysUsed(1);
        $entitlement1->setFromDate(new DateTime('2012-09-12'));
        $entitlement1->setToDate(new DateTime('2012-09-13'));
        $entitlement1->setCreditedDate(new DateTime('2012-05-01'));
        $entitlement1->setEmployee($employee);
        $entitlement1->setEntitlementType($leaveEntitlementType);

        $entitlements = [$entitlement1];

        $mockDao = $this->getMockBuilder(LeaveEntitlementDao::class)
            ->onlyMethods(['getValidLeaveEntitlements'])
            ->getMock();
        $mockDao->expects($this->once())
            ->method('getValidLeaveEntitlements')
            ->with(
                $empNumber,
                $leaveType,
                new DateTime('2012-09-11'),
                new DateTime('2012-09-12'),
                'le.fromDate',
                'ASC'
            )
            ->will($this->returnValue($entitlements));

        $mockService = $this->getMockBuilder(LeaveEntitlementService::class)
            ->onlyMethods(['getLeaveEntitlementDao'])
            ->getMock();
        $mockService->expects($this->once())
            ->method('getLeaveEntitlementDao')
            ->willReturn($mockDao);

        $this->strategy = $this->getMockBuilder(FIFOEntitlementConsumptionStrategy::class)
            ->onlyMethods(['getLeaveEntitlementService'])
            ->getMock();
        $this->strategy->expects($this->once())
            ->method('getLeaveEntitlementService')
            ->willReturn($mockService);

        $results = $this->strategy->handleLeaveCreate($empNumber, $leaveType, $leaveDates);
        $this->assertNull($results);
    }

    public function testHandleLeaveStatusChangeOneEntitlementAfterAssign(): void
    {
        $empNumber = 1;
        $leaveType = 2;

        $leave1 = new Leave();
        $leave1->setDate(new DateTime('2012-09-11'));
        $leave1->setLengthDays(1);

        $leave2 = new Leave();
        $leave2->setDate(new DateTime('2012-09-12'));
        $leave2->setLengthDays(1);
        $leaveDates = [$leave1, $leave2];

        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $employee->setFirstName('Kayla');
        $employee->setLastName('Abbey');

        $leaveEntitlementType = new LeaveEntitlementType();
        $leaveEntitlementType->setId(1);
        $entitlement1 = new LeaveEntitlement();
        $entitlement1->setId(3);
        $entitlement1->setNoOfDays(4);
        $entitlement1->setDaysUsed(1);
        $entitlement1->setFromDate(new DateTime('2012-09-12'));
        $entitlement1->setToDate(new DateTime('2012-09-13'));
        $entitlement1->setCreditedDate(new DateTime('2012-05-01'));
        $entitlement1->setEmployee($employee);
        $entitlement1->setEntitlementType($leaveEntitlementType);

        $entitlements = [$entitlement1];

        $mockDao = $this->getMockBuilder(LeaveEntitlementDao::class)
            ->onlyMethods(['getValidLeaveEntitlements'])
            ->getMock();
        $mockDao->expects($this->once())
            ->method('getValidLeaveEntitlements')
            ->with(
                $empNumber,
                $leaveType,
                new DateTime('2012-09-11'),
                new DateTime('2012-09-12'),
                'le.fromDate',
                'ASC'
            )
            ->will($this->returnValue($entitlements));

        $mockService = $this->getMockBuilder(LeaveEntitlementService::class)
            ->onlyMethods(['getLeaveEntitlementDao'])
            ->getMock();
        $mockService->expects($this->once())
            ->method('getLeaveEntitlementDao')
            ->willReturn($mockDao);

        $this->strategy = $this->getMockBuilder(FIFOEntitlementConsumptionStrategy::class)
            ->onlyMethods(['getLeaveEntitlementService'])
            ->getMock();
        $this->strategy->expects($this->once())
            ->method('getLeaveEntitlementService')
            ->willReturn($mockService);

        // Assign, $allowNoEntitlements = true
        $results = $this->strategy->handleLeaveCreate($empNumber, $leaveType, $leaveDates, true);
        $this->assertTrue($results instanceof CurrentAndChangeEntitlement);

        $current = ['2012-09-12' => [3 => 1]];
        $changes = [];

        $this->verifyEntitlements(
            ['change' => $results->getChange(), 'current' => $results->getCurrent()],
            $current,
            $changes
        );
    }

    public function testHandleLeaveStatusChangeOneEntitlementNotSufficientApply(): void
    {
        $empNumber = 1;
        $leaveType = 2;

        $leave1 = new Leave();
        $leave1->setDate(new DateTime('2012-09-11'));
        $leave1->setLengthDays(1);

        $leave2 = new Leave();
        $leave2->setDate(new DateTime('2012-09-12'));
        $leave2->setLengthDays(1);

        $leave3 = new Leave();
        $leave3->setDate(new DateTime('2012-09-13'));
        $leave3->setLengthDays(1);
        $leaveDates = [$leave1, $leave2, $leave3];

        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $employee->setFirstName('Kayla');
        $employee->setLastName('Abbey');

        $leaveEntitlementType = new LeaveEntitlementType();
        $leaveEntitlementType->setId(1);
        $entitlement1 = new LeaveEntitlement();
        $entitlement1->setId(6);
        $entitlement1->setNoOfDays(3);
        $entitlement1->setDaysUsed(2);
        $entitlement1->setFromDate(new DateTime('2012-09-10'));
        $entitlement1->setToDate(new DateTime('2012-09-14'));
        $entitlement1->setCreditedDate(new DateTime('2012-05-01'));
        $entitlement1->setEmployee($employee);
        $entitlement1->setEntitlementType($leaveEntitlementType);

        $entitlements = [$entitlement1];

        $mockDao = $this->getMockBuilder(LeaveEntitlementDao::class)
            ->onlyMethods(['getValidLeaveEntitlements'])
            ->getMock();
        $mockDao->expects($this->once())
            ->method('getValidLeaveEntitlements')
            ->with(
                $empNumber,
                $leaveType,
                new DateTime('2012-09-11'),
                new DateTime('2012-09-13'),
                'le.fromDate',
                'ASC'
            )
            ->will($this->returnValue($entitlements));

        $mockService = $this->getMockBuilder(LeaveEntitlementService::class)
            ->onlyMethods(['getLeaveEntitlementDao'])
            ->getMock();
        $mockService->expects($this->once())
            ->method('getLeaveEntitlementDao')
            ->willReturn($mockDao);

        $this->strategy = $this->getMockBuilder(FIFOEntitlementConsumptionStrategy::class)
            ->onlyMethods(['getLeaveEntitlementService'])
            ->getMock();
        $this->strategy->expects($this->once())
            ->method('getLeaveEntitlementService')
            ->willReturn($mockService);

        // Apply, $allowNoEntitlements = false
        $results = $this->strategy->handleLeaveCreate($empNumber, $leaveType, $leaveDates);
        $this->assertNull($results);
    }

    public function xtestHandleLeaveStatusChangeOneEntitlementNotSufficientAssign()
    {
        $empNumber = 1;
        $leaveType = 2;

        $leave1 = new Leave();
        $leave1->fromArray(['id' => null, 'date' => '2012-09-11', 'length_days' => 1]);
        $leave2 = new Leave();
        $leave2->fromArray(['id' => null, 'date' => '2012-09-12', 'length_days' => 1]);
        $leave3 = new Leave();
        $leave3->fromArray(['id' => null, 'date' => '2012-09-13', 'length_days' => 1]);

        $leaveDates = [$leave1, $leave2, $leave3];

        $entitlement1 = new LeaveEntitlement();
        $entitlement1->fromArray([
            'id' => 6,
            'emp_number' => 1,
            'no_of_days' => 3,
            'days_used' => 2,
            'leave_type_id' => 2,
            'from_date' => '2012-09-10',
            'to_date' => '2012-09-14',
            'credited_date' => '2012-05-01',
            'note' => 'Created by Unit test',
            'entitlement_type' => LeaveEntitlement::ENTITLEMENT_TYPE_ADD,
            'deleted' => 0
                                 ]);

        $entitlements = [$entitlement1];

        $mockService = $this->getMockBuilder('LeaveEntitlementService')
            ->setMethods(['getValidLeaveEntitlements'])
            ->getMock();
        $mockService->expects($this->once())
                ->method('getValidLeaveEntitlements')
                ->with($empNumber, $leaveType, '2012-09-11', '2012-09-13', 'from_date', 'ASC')
                ->will($this->returnValue($entitlements));
        $this->strategy->setLeaveEntitlementService($mockService);


        // Assign, $allowNoEntitlements = true
        $results = $this->strategy->handleLeaveCreate($empNumber, $leaveType, $leaveDates, true);
        $current = ['2012-09-11' => [6 => 1]];
        $changes = [];

        $this->verifyEntitlements($results, $current, $changes);
    }

    public function xtestHandleLeaveStatusChangeOneEntitlementExactAmount()
    {
        $empNumber = 1;
        $leaveType = 2;

        $leave1 = new Leave();
        $leave1->fromArray(['id' => null, 'date' => '2012-09-11', 'length_days' => 1]);
        $leave2 = new Leave();
        $leave2->fromArray(['id' => null, 'date' => '2012-09-12', 'length_days' => 1]);

        $leaveDates = [$leave1, $leave2];

        $entitlement1 = new LeaveEntitlement();
        $entitlement1->fromArray([
            'id' => 6,
            'emp_number' => 1,
            'no_of_days' => 3,
            'days_used' => 1,
            'leave_type_id' => 2,
            'from_date' => '2012-09-10',
            'to_date' => '2012-09-14',
            'credited_date' => '2012-05-01',
            'note' => 'Created by Unit test',
            'entitlement_type' => LeaveEntitlement::ENTITLEMENT_TYPE_ADD,
            'deleted' => 0
                                 ]);

        $entitlements = [$entitlement1];

        $mockService = $this->getMockBuilder('LeaveEntitlementService')
            ->setMethods(['getValidLeaveEntitlements'])
            ->getMock();
        $mockService->expects($this->once())
                ->method('getValidLeaveEntitlements')
                ->with($empNumber, $leaveType, '2012-09-11', '2012-09-12', 'from_date', 'ASC')
                ->will($this->returnValue($entitlements));
        $this->strategy->setLeaveEntitlementService($mockService);

        // Apply, $allowNoEntitlements = false
        $results = $this->strategy->handleLeaveCreate($empNumber, $leaveType, $leaveDates);

        $expected = ['current' => ['2012-09-11' => [6=>1], '2012-09-12' => [6=>1]], 'change' => []];
        $this->assertEquals($expected, $results);
    }

    public function xtestHandleLeaveStatusChangeOneEntitlementExtra()
    {
        $empNumber = 1;
        $leaveType = 2;

        $leave1 = new Leave();
        ;
        $leave1->fromArray(['id' => null, 'date' => '2012-09-11', 'length_days' => 1]);
        $leave2 = new Leave();
        ;
        $leave2->fromArray(['id' => null, 'date' => '2012-09-12', 'length_days' => 1]);

        $leaveDates = [$leave1, $leave2];

        $entitlement1 = new LeaveEntitlement();
        $entitlement1->fromArray([
            'id' => 6,
            'emp_number' => 1,
            'no_of_days' => 4,
            'days_used' => 1,
            'leave_type_id' => 2,
            'from_date' => '2012-09-10',
            'to_date' => '2012-09-14',
            'credited_date' => '2012-05-01',
            'note' => 'Created by Unit test',
            'entitlement_type' => LeaveEntitlement::ENTITLEMENT_TYPE_ADD,
            'deleted' => 0
                                 ]);

        $entitlements = [$entitlement1];

        $mockService = $this->getMockBuilder('LeaveEntitlementService')
            ->setMethods(['getValidLeaveEntitlements'])
            ->getMock();
        $mockService->expects($this->once())
                ->method('getValidLeaveEntitlements')
                ->with($empNumber, $leaveType, '2012-09-11', '2012-09-12', 'from_date', 'ASC')
                ->will($this->returnValue($entitlements));
        $this->strategy->setLeaveEntitlementService($mockService);

        // Apply, $allowNoEntitlements = false
        $results = $this->strategy->handleLeaveCreate($empNumber, $leaveType, $leaveDates);
        $expected = ['current' => ['2012-09-11' => [6=>1], '2012-09-12' => [6=>1]], 'change' => []];
        $this->assertEquals($expected, $results);
    }

    public function xtestGetLeaveWithoutEntitlementDateLimitsForLeaveBalance()
    {
        $mockService = $this->getMockBuilder('LeavePeriodService')
            ->setMethods(['getCurrentLeavePeriodByDate'])
            ->getMock();
        $mockService->expects($this->any())
                ->method('getCurrentLeavePeriodByDate')
                ->will($this->returnValue(['2012-01-01', '2012-12-31']));

        $this->strategy->setLeavePeriodService($mockService);

        $result = $this->strategy->getLeaveWithoutEntitlementDateLimitsForLeaveBalance('2012-01-03', '2012-02-02');

        $this->assertEquals(2, count($result));

        $this->assertEquals('2012-01-01', $result[0]);
        $this->assertEquals('2012-12-31', $result[1]);
    }

    // Check leap year

    // check does not affect entitlements unless both from/to date match previous from/to date

    // Check leave assigned to old entitlements are redistributed

    /**
     * @param array $results
     * @param array $current
     * @param array $change
     */
    public function verifyEntitlements(array $results, array $current, array $change): void
    {
        $this->assertTrue(isset($results['current']));
        $this->assertTrue(is_array($results['current']));
        $this->assertEquals($current, $results['current']);

        $this->assertTrue(isset($results['change']));
        $this->assertTrue(is_array($results['change']));
        $this->assertEquals($change, $results['change']);
    }
}
