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
use Generator;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Service\NumberHelperService;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\LeaveEntitlement;
use OrangeHRM\Entity\LeaveEntitlementType;
use OrangeHRM\Entity\LeaveType;
use OrangeHRM\Entity\User;
use OrangeHRM\Framework\Services;
use OrangeHRM\Leave\Dao\LeaveEntitlementDao;
use OrangeHRM\Leave\Dto\EmployeeLeaveEntitlementUsageReportSearchFilterParams;
use OrangeHRM\Leave\Dto\LeaveEntitlementSearchFilterParams;
use OrangeHRM\Leave\Dto\LeaveTypeLeaveEntitlementUsageReportSearchFilterParams;
use OrangeHRM\Leave\Dto\LeaveWithDaysLeft;
use OrangeHRM\Leave\Service\LeaveConfigurationService;
use OrangeHRM\ORM\ListSorter;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Leave
 * @group Dao
 */
class LeaveEntitlementDaoTest extends KernelTestCase
{
    /**
     * @var LeaveEntitlementDao
     */
    private LeaveEntitlementDao $dao;
    private string $fixture;

    /**
     * Set up method
     */
    protected function setUp(): void
    {
        $this->dao = new LeaveEntitlementDao();
        $this->fixture = $this->getFixturePath();
        $this->createKernelWithMockServices(
            [Services::NUMBER_HELPER_SERVICE => new NumberHelperService()]
        );
        TestDataService::populate($this->fixture);
    }

    private function getFixturePath(): string
    {
        return Config::get(Config::PLUGINS_DIR) . '/orangehrmLeavePlugin/test/fixtures/LeaveEntitlement.yml';
    }

    public function testSearchLeaveEntitlementsWithNoFilters(): void
    {
        $parameterHolder = new LeaveEntitlementSearchFilterParams();
        $entitlementList = TestDataService::loadObjectList(LeaveEntitlement::class, $this->fixture, 'LeaveEntitlement');
        $expected = [
            $entitlementList[0],
            $entitlementList[2],
            $entitlementList[3],
            $entitlementList[5],
            $entitlementList[1],
            $entitlementList[6],
            $entitlementList[7]
        ];
        $results = $this->dao->getLeaveEntitlements($parameterHolder);
        $this->_compareEntitlements($expected, $results);

        $total = $this->dao->getLeaveEntitlementsCount($parameterHolder);
        $this->assertEquals(7, $total);

        $sum = $this->dao->getLeaveEntitlementsSum($parameterHolder);
        $this->assertEquals(27, $sum);
    }

    public function testSearchLeaveEntitlementsSorting(): void
    {
        $parameterHolder = new LeaveEntitlementSearchFilterParams();
        $entitlementList = TestDataService::loadObjectList(LeaveEntitlement::class, $this->fixture, 'LeaveEntitlement');

        // sort by leave type name
        $parameterHolder->setSortOrder(ListSorter::DESCENDING);
        $parameterHolder->setSortField('leaveType.name');

        $expected = [
            $entitlementList[1],
            $entitlementList[2],
            $entitlementList[3],
            $entitlementList[6],
            $entitlementList[0],
            $entitlementList[5],
            $entitlementList[7]
        ];
        $results = $this->dao->getLeaveEntitlements($parameterHolder);
        $this->_compareEntitlements($expected, $results);
    }

    public function testSearchLeaveEntitlementsWithAllFilters(): void
    {
        $parameterHolder = new LeaveEntitlementSearchFilterParams();
        $entitlementList = TestDataService::loadObjectList(LeaveEntitlement::class, $this->fixture, 'LeaveEntitlement');
        $parameterHolder->setEmpNumber(2);
        $parameterHolder->setLeaveTypeId(6);
        $parameterHolder->setFromDate(new DateTime('2013-08-01'));
        $parameterHolder->setToDate(new DateTime('2013-10-02'));
        $expected = [$entitlementList[1]];
        $results = $this->dao->getLeaveEntitlements($parameterHolder);
        $this->_compareEntitlements($expected, $results);

        $total = $this->dao->getLeaveEntitlementsCount($parameterHolder);
        $this->assertEquals(1, $total);

        $sum = $this->dao->getLeaveEntitlementsSum($parameterHolder);
        $this->assertEquals(4, $sum);
    }

    public function testSearchLeaveEntitlementsWithEmpNumbers(): void
    {
        $parameterHolder = new LeaveEntitlementSearchFilterParams();
        $entitlementList = TestDataService::loadObjectList(LeaveEntitlement::class, $this->fixture, 'LeaveEntitlement');
        $parameterHolder->setEmpNumbers([2]);
        $parameterHolder->setLeaveTypeId(6);
        $parameterHolder->setFromDate(new DateTime('2013-08-01'));
        $parameterHolder->setToDate(new DateTime('2013-10-02'));
        $expected = [$entitlementList[1]];
        $results = $this->dao->getLeaveEntitlements($parameterHolder);
        $this->_compareEntitlements($expected, $results);

        $total = $this->dao->getLeaveEntitlementsCount($parameterHolder);
        $this->assertEquals(1, $total);

        $sum = $this->dao->getLeaveEntitlementsSum($parameterHolder);
        $this->assertEquals(4, $sum);

        $parameterHolder = new LeaveEntitlementSearchFilterParams();
        $parameterHolder->setEmpNumber(2);
        $parameterHolder->setEmpNumbers([5, 7]);
        $results = $this->dao->getLeaveEntitlements($parameterHolder);
        $this->assertEmpty($results);

        $parameterHolder = new LeaveEntitlementSearchFilterParams();
        $parameterHolder->setEmpNumbers([5, 7]); // 5 is deleted
        $expected = [$entitlementList[5], $entitlementList[6]];
        $results = $this->dao->getLeaveEntitlements($parameterHolder);
        $this->_compareEntitlements($expected, $results);

        $parameterHolder = new LeaveEntitlementSearchFilterParams();
        $parameterHolder->setEmpNumbers([2, 7]);
        $expected = [$entitlementList[5], $entitlementList[1], $entitlementList[6]];
        $results = $this->dao->getLeaveEntitlements($parameterHolder);
        $this->_compareEntitlements($expected, $results);

        $total = $this->dao->getLeaveEntitlementsCount($parameterHolder);
        $this->assertEquals(3, $total);
    }

    public function testSearchLeaveEntitlementsByLeaveType(): void
    {
        $parameterHolder = new LeaveEntitlementSearchFilterParams();
        $entitlementList = TestDataService::loadObjectList(LeaveEntitlement::class, $this->fixture, 'LeaveEntitlement');

        $parameterHolder->setLeaveTypeId(2);

        $expected = [$entitlementList[2], $entitlementList[3]];
        $results = $this->dao->getLeaveEntitlements($parameterHolder);
        $this->_compareEntitlements($expected, $results);

        // Non existing leave type id
        $parameterHolder->setLeaveTypeId(21);

        $results = $this->dao->getLeaveEntitlements($parameterHolder);
        $this->assertEmpty($results);

        // Leave type with no entitlements
        $parameterHolder->setLeaveTypeId(7);

        $results = $this->dao->getLeaveEntitlements($parameterHolder);
        $this->assertEmpty($results);
    }

    public function testSearchLeaveEntitlementsByEmpNumber(): void
    {
        $parameterHolder = new LeaveEntitlementSearchFilterParams();
        $entitlementList = TestDataService::loadObjectList(LeaveEntitlement::class, $this->fixture, 'LeaveEntitlement');

        // employee with multiple records
        $parameterHolder->setEmpNumber(1);
        $expected = [$entitlementList[0], $entitlementList[2], $entitlementList[3]];
        $results = $this->dao->getLeaveEntitlements($parameterHolder);

        $this->_compareEntitlements($expected, $results);

        // employee with one record
        $parameterHolder->setEmpNumber(2);
        $expected = [$entitlementList[1]];
        $results = $this->dao->getLeaveEntitlements($parameterHolder);

        $this->_compareEntitlements($expected, $results);

        // employee with no records
        $parameterHolder->setEmpNumber(4);
        $results = $this->dao->getLeaveEntitlements($parameterHolder);
        $this->assertEmpty($results);

        // non existing employee
        $parameterHolder->setEmpNumber(100);
        $results = $this->dao->getLeaveEntitlements($parameterHolder);
        $this->assertEmpty($results);
    }

    public function testSearchLeaveEntitlementsByDates(): void
    {
        $parameterHolder = new LeaveEntitlementSearchFilterParams();
        $entitlementList = TestDataService::loadObjectList(LeaveEntitlement::class, $this->fixture, 'LeaveEntitlement');

        // date range with multiple records
        $parameterHolder->setFromDate(new DateTime('2012-01-01'));
        $parameterHolder->setToDate(new DateTime('2012-12-31'));

        $expected = [$entitlementList[0], $entitlementList[2]];
        $results = $this->dao->getLeaveEntitlements($parameterHolder);
        $this->_compareEntitlements($expected, $results);

        $parameterHolder->setFromDate(new DateTime('2012-01-01'));
        $parameterHolder->setToDate(new DateTime('2012-08-01'));
        $expected = [$entitlementList[0]];
        $results = $this->dao->getLeaveEntitlements($parameterHolder);
        $this->_compareEntitlements($expected, $results);

        $parameterHolder->setFromDate(new DateTime('2012-01-01'));
        $parameterHolder->setToDate(new DateTime('2013-12-31'));
        $expected = [
            $entitlementList[0],
            $entitlementList[2],
            $entitlementList[3],
            $entitlementList[5],
            $entitlementList[1]
        ];
        $results = $this->dao->getLeaveEntitlements($parameterHolder);
        $this->_compareEntitlements($expected, $results);

        $parameterHolder->setFromDate(new DateTime('2011-01-01'));
        $parameterHolder->setToDate(new DateTime('2012-01-01'));
        $results = $this->dao->getLeaveEntitlements($parameterHolder);
        $this->assertEmpty($results);

        $parameterHolder->setFromDate(new DateTime('2013-09-01'));
        $parameterHolder->setToDate(new DateTime('2013-12-01'));
        $results = $this->dao->getLeaveEntitlements($parameterHolder);
        $this->assertEmpty($results);
    }

    public function testGetLeaveEntitlement(): void
    {
        $id = 3;
        $leaveEntitlement = $this->dao->getLeaveEntitlement($id);

        $this->assertTrue($leaveEntitlement instanceof LeaveEntitlement);
        $this->getEntityManager()->clear(LeaveEntitlement::class);
        $fromDb = $this->getEntityManager()->getRepository(LeaveEntitlement::class)->find($id);

        $this->_compareEntitlement($fromDb, $leaveEntitlement);

        // non existing id
        $nonExisting = $this->dao->getLeaveEntitlement(111);
        $this->assertTrue(is_null($nonExisting));
    }

    public function testSaveLeaveEntitlementNew(): void
    {
        $em = $this->getEntityManager();
        $leaveEntitlement = new LeaveEntitlement();
        $leaveEntitlement->setEmployee($em->getReference(Employee::class, 1));
        $leaveEntitlement->setNoOfDays(12);
        $leaveEntitlement->setLeaveType($em->getReference(LeaveType::class, 2));
        $leaveEntitlement->setFromDate(new DateTime('2012-09-13'));
        $leaveEntitlement->setToDate(new DateTime('2012-11-28'));
        $leaveEntitlement->setCreditedDate(new DateTime('2012-05-01'));
        $leaveEntitlement->setNote('Created by Unit test');
        $leaveEntitlement->setEntitlementType(
            $em->getReference(LeaveEntitlementType::class, LeaveEntitlement::ENTITLEMENT_TYPE_ADD)
        );
        $leaveEntitlement->setDeleted(false);

        $savedObj = $this->dao->saveLeaveEntitlement($leaveEntitlement);
        $this->assertTrue($savedObj instanceof LeaveEntitlement);

        $savedId = $savedObj->getId();
        $this->assertTrue(!empty($savedId));

        $leaveEntitlement->setId($savedId);
        $this->_compareEntitlement($leaveEntitlement, $savedObj);

        $em->clear(LeaveEntitlement::class);
        $fromDb = $em->getRepository(LeaveEntitlement::class)->find($savedId);
        $this->_compareEntitlement($leaveEntitlement, $fromDb);
    }

    public function testGetValidLeaveEntitlements(): void
    {
        $entitlementList = TestDataService::loadObjectList(LeaveEntitlement::class, $this->fixture, 'LeaveEntitlement');

        $empNumber = 1;
        $leaveTypeId = 2;
        $fromDate = new DateTime('2012-06-01');
        $toDate = new DateTime('2012-06-05');
        $orderField = 'le.fromDate';
        $order = 'ASC';

        $expected = [$entitlementList[2]];
        $results = $this->dao->getValidLeaveEntitlements(
            $empNumber,
            $leaveTypeId,
            $fromDate,
            $toDate,
            $orderField,
            $order
        );
        $this->_compareEntitlements($expected, $results);

        $empNumber = 1;
        $leaveTypeId = 2;
        $fromDate = new DateTime('2013-01-01');
        $toDate = new DateTime('2013-01-10');
        $orderField = 'le.fromDate';
        $order = 'ASC';

        $expected = [$entitlementList[3]];
        $results = $this->dao->getValidLeaveEntitlements(
            $empNumber,
            $leaveTypeId,
            $fromDate,
            $toDate,
            $orderField,
            $order
        );
        $this->_compareEntitlements($expected, $results);
    }

    public function testGetValidLeaveEntitlementsMultipleLeavePeriods(): void
    {
        $entitlementList = TestDataService::loadObjectList(LeaveEntitlement::class, $this->fixture, 'LeaveEntitlement');

        // both leave period have entitlements
        $empNumber = 1;
        $leaveTypeId = 2;
        $fromDate = new DateTime('2012-12-30');
        $toDate = new DateTime('2013-01-02');
        $orderField = 'le.fromDate';
        $order = 'ASC';

        $expected = [$entitlementList[2], $entitlementList[3]];
        $results = $this->dao->getValidLeaveEntitlements(
            $empNumber,
            $leaveTypeId,
            $fromDate,
            $toDate,
            $orderField,
            $order
        );
        $this->_compareEntitlements($expected, $results);

        // only second leave period has entitlements
        $empNumber = 1;
        $leaveTypeId = 2;
        $fromDate = new DateTime('2011-12-30');
        $toDate = new DateTime('2012-01-02');
        $orderField = 'le.fromDate';
        $order = 'ASC';

        $expected = [$entitlementList[2]];
        $results = $this->dao->getValidLeaveEntitlements(
            $empNumber,
            $leaveTypeId,
            $fromDate,
            $toDate,
            $orderField,
            $order
        );
        $this->_compareEntitlements($expected, $results);

        // only first leave period has entitlements
        $empNumber = 1;
        $leaveTypeId = 2;
        $fromDate = new DateTime('2013-12-30');
        $toDate = new DateTime('2014-01-02');
        $orderField = 'le.fromDate';
        $order = 'ASC';

        $expected = [$entitlementList[3]];
        $results = $this->dao->getValidLeaveEntitlements(
            $empNumber,
            $leaveTypeId,
            $fromDate,
            $toDate,
            $orderField,
            $order
        );
        $this->_compareEntitlements($expected, $results);
    }

    public function testSaveLeaveEntitlementUpdate(): void
    {
        $id = 3;
        $existingEntitlement = $this->getEntityManager()->getRepository(LeaveEntitlement::class)->find($id);

        $existingEntitlement->setNoOfDays(41);
        $savedObj = $this->dao->saveLeaveEntitlement($existingEntitlement);

        $this->_compareEntitlement($existingEntitlement, $savedObj);

        $this->getEntityManager()->clear(LeaveEntitlement::class);
        $fromDb = $this->getEntityManager()->getRepository(LeaveEntitlement::class)->find($id);

        $this->_compareEntitlement($existingEntitlement, $fromDb);
    }

    public function testDeleteLeaveEntitlementsMultiple(): void
    {
        $deleted = [5];

        // delete with invalid ids
        $ids = [21, 31];
        $count = $this->dao->deleteLeaveEntitlements($ids);
        $this->assertEquals(0, $count);
        $this->_verifyDeletedFlags($deleted);


        // delete multiple
        $ids = [2, 3];
        $count = $this->dao->deleteLeaveEntitlements($ids);
        $this->assertEquals(2, $count);

        // verify deleted
        $deleted = array_merge($deleted, $ids);
        $this->_verifyDeletedFlags($deleted);

        // delete one
        $ids = [4];

        $count = $this->dao->deleteLeaveEntitlements($ids);
        $this->assertEquals(1, $count);

        // verify deleted
        $deleted = array_merge($deleted, $ids);
        $this->_verifyDeletedFlags($deleted);

        // delete already deleted entry
        $count = $this->dao->deleteLeaveEntitlements([2]);
        $this->_verifyDeletedFlags($deleted);
    }

    public function testMatchingEntitlementsNoMatches(): void
    {
        $empNumber = 1;
        $leaveTypeId = 1;
        $fromDate = new DateTime('2012-01-01');
        $toDate = new DateTime('2012-08-02');

        $matches = $this->dao->getMatchingEntitlements($empNumber, $fromDate, $toDate, $leaveTypeId);
        $this->assertEquals(0, count($matches));
    }

    public function testMatchingEntitlementsVerifyNoPartialMatches(): void
    {
        $empNumber = 2;
        $leaveTypeId = 6;
        $fromDate = new DateTime('2013-08-05');
        $toDate = new DateTime('2013-09-04');

        $matches = $this->dao->getMatchingEntitlements($empNumber, $fromDate, $toDate, $leaveTypeId);
        $this->assertEquals(0, count($matches));
    }

    public function testMatchingEntitlementsOneMatch(): void
    {
        $empNumber = 2;
        $leaveTypeId = 6;
        $fromDate = new DateTime('2013-08-05');
        $toDate = new DateTime('2013-09-01');

        $matches = $this->dao->getMatchingEntitlements($empNumber, $fromDate, $toDate, $leaveTypeId);

        $this->assertEquals(1, count($matches));
        $this->assertEquals(2, $matches[0]->getId());
    }

    public function testMatchingEntitlementsDoesNotMatchDeleted(): void
    {
        $empNumber = 5;
        $leaveTypeId = 1;
        $fromDate = new DateTime('2012-06-06');
        $toDate = new DateTime('2012-09-01');

        $matches = $this->dao->getMatchingEntitlements($empNumber, $fromDate, $toDate, $leaveTypeId);
        $this->assertEquals(0, count($matches));
    }

    public function testGetLeaveEntitlementsByIds(): void
    {
        $entitlementList = $this->dao->getLeaveEntitlementsByIds([1, 2, 3]);
        $this->assertCount(3, $entitlementList);

        $entitlementList = $this->dao->getLeaveEntitlementsByIds([1, 2, 100]);
        $this->assertCount(2, $entitlementList);

        $entitlementList = $this->dao->getLeaveEntitlementsByIds([100]);
        $this->assertEmpty($entitlementList);
    }

    /**
     * @param LeaveEntitlement[] $expected
     * @param LeaveEntitlement[] $results
     */
    protected function _compareEntitlements(array $expected, array $results): void
    {
        $this->assertCount(count($expected), $results);

        for ($i = 0; $i < count($expected); $i++) {
            $this->_compareEntitlement($expected[$i], $results[$i]);
        }
    }

    /**
     * @param LeaveEntitlement $expected
     * @param LeaveEntitlement $actual
     */
    protected function _compareEntitlement(LeaveEntitlement $expected, LeaveEntitlement $actual): void
    {
        $this->assertEquals($expected->getId(), $actual->getId());
        $this->assertEquals($expected->getEmployee()->getEmpNumber(), $actual->getEmployee()->getEmpNumber());
        $this->assertEquals($expected->getNoOfDays(), $actual->getNoOfDays());
        $this->assertEquals($expected->getLeaveType()->getId(), $actual->getLeaveType()->getId());
        $this->assertEquals($expected->getFromDate(), $actual->getFromDate());
        $this->assertEquals($expected->getToDate(), $actual->getToDate());
        $this->assertEquals($expected->getCreditedDate(), $actual->getCreditedDate());
        $this->assertEquals($expected->getNote(), $actual->getNote());
        $this->assertEquals($expected->getEntitlementType(), $actual->getEntitlementType());
        $this->assertEquals($expected->isDeleted(), $actual->isDeleted());
    }

    protected function _verifyDeletedFlags($deleted): void
    {
        $ids = [1, 2, 3, 4, 5];

        $nonDeleted = array_diff($ids, $deleted);

        // verify deleted
        foreach ($deleted as $id) {
            /** @var LeaveEntitlement $entitlement */
            $entitlement = TestDataService::fetchObject(LeaveEntitlement::class, $id);
            $this->assertEquals(true, $entitlement->isDeleted(), 'id=' . $id);
        }

        // verify non deleted
        foreach ($nonDeleted as $id) {
            /** @var LeaveEntitlement $entitlement */
            $entitlement = TestDataService::fetchObject(LeaveEntitlement::class, $id);
            $this->assertEquals(false, $entitlement->isDeleted(), 'id=' . $id);
        }
    }

    public function testBulkAssignLeaveEntitlements(): void
    {
        $empList = [1,2,3];

        $leaveEntitlement = new LeaveEntitlement();
        $leaveEntitlement->setLeaveType($this->getEntityReference(LeaveType::class, 1));
        $leaveEntitlement->setCreditedDate(new DateTime());
        $leaveEntitlement->setEntitlementType($this->getEntityReference(LeaveEntitlementType::class, LeaveEntitlement::ENTITLEMENT_TYPE_ADD));
        $leaveEntitlement->setDeleted(false);
        $leaveEntitlement->setNoOfDays(2);
        $leaveEntitlement->setFromDate(new DateTime('2012-01-01'));
        $leaveEntitlement->setToDate(new DateTime('2012-08-01'));
        $leaveEntitlement->setCreatedBy($this->getEntityReference(User::class, 1));

        $this->createKernelWithMockServices([
                                                Services::LEAVE_CONFIG_SERVICE =>new LeaveConfigurationService(),
                                            ]);
        $result = $this->dao->bulkAssignLeaveEntitlements($empList, $leaveEntitlement);


        $this->assertEquals(count($empList), 3);


        $result = $this->dao->bulkAssignLeaveEntitlements($empList, $leaveEntitlement);
        $leaveEntitlement->setNoOfDays(1);

        list($leaveEntitlements, $count) = $this->dao->bulkAssignLeaveEntitlements($empList, $leaveEntitlement);


        $this->assertEquals(count($empList), $count);
    }

    public function xtestBulkAssignLeaveEntitlementsLinkingOfUnlinkedLeave()
    {
        // TODO
        $empList = [7];

        $leaveEntitlement = new LeaveEntitlement();
        $leaveEntitlement->setLeaveTypeId(1);
        $leaveEntitlement->setCreditedDate(date('Y-m-d'));
        $leaveEntitlement->setEntitlementType(LeaveEntitlement::ENTITLEMENT_TYPE_ADD);
        $leaveEntitlement->setDeleted(0);
        $leaveEntitlement->setNoOfDays(3);
        $leaveEntitlement->setFromDate('2014-01-01');
        $leaveEntitlement->setToDate('2014-12-31');

        $result = $this->dao->bulkAssignLeaveEntitlements($empList, $leaveEntitlement);
        $this->assertEquals(count($empList), $result);

        // verify unlinked leave is now linked
        $conn = Doctrine_Manager::connection()->getDbh();
        $statement = $conn->prepare('SELECT * FROM ohrm_leave_leave_entitlement e WHERE e.leave_id = ?');
        $leaveIds = [11, 12];

        foreach ($leaveIds as $leaveId) {
            $this->assertTrue($statement->execute([$leaveId]));
            $results = $statement->fetchAll();
            $this->assertEquals(1, count($results));
            $this->assertEquals($leaveId, $results[0]['leave_id']);
            $this->assertEquals(1, $results[0]['length_days']);
        }
    }

    /**
     * @dataProvider dataProviderForGetEntitlementUsageForLeave
     */
    public function testGetEntitlementUsageForLeave(
        int $leaveId,
        array $leaveLeaveEntitlements,
        array $expectedDaysUsed,
        array $expectedNoOfDays
    ): void {
        $result = $this->dao->getEntitlementUsageForLeave($leaveId);
        $this->assertCount(count($leaveLeaveEntitlements), $result);
        foreach ($leaveLeaveEntitlements as $i => $leaveLeaveEntitlement) {
            $this->assertEquals($leaveLeaveEntitlement->getLengthDays(), $result[$i]->getLengthDays());
            $this->assertEquals($expectedDaysUsed[$i], $result[$i]->getDaysUsed());
            $this->assertEquals($expectedNoOfDays[$i], $result[$i]->getNoOfDays());
        }
    }

    public function dataProviderForGetEntitlementUsageForLeave(): Generator
    {
        $leaveLeaveEntitlements = TestDataService::loadObjectList(
            'LeaveLeaveEntitlement',
            $this->getFixturePath(),
            'LeaveLeaveEntitlement'
        );
        yield [1, [$leaveLeaveEntitlements[0], $leaveLeaveEntitlements[1]], [0, 1], [3, 4]];
        yield [5, [$leaveLeaveEntitlements[5]], [0], [3]];
        yield [7, [$leaveLeaveEntitlements[6]], [0], [3]];
        yield [8, [$leaveLeaveEntitlements[7]], [2], [5]];

        // check with deleted entitlement
        yield [2, [], [], []];
    }

    /**
     * @dataProvider dataProviderForGetLeaveWithoutEntitlements
     */
    public function testGetLeaveWithoutEntitlements(
        array $empNumbers,
        int $leaveTypeId,
        DateTime $fromDate,
        DateTime $toDate,
        array $expected
    ): void {
        $result = $this->dao->getLeaveWithoutEntitlements($empNumbers, $leaveTypeId, $fromDate, $toDate);
        $this->assertCount(count($expected), $result);
        foreach ($expected as $i => $leaveLeaveEntitlement) {
            $this->assertEquals($leaveLeaveEntitlement, $result[$i]);
        }
    }

    public function dataProviderForGetLeaveWithoutEntitlements(): Generator
    {
        yield [[1], 1, new DateTime('2010-01-01'), new DateTime('2010-12-31'), []];
        yield [[1], 1, new DateTime('2011-01-01'), new DateTime('2011-12-31'), []];
        yield [[1], 1, new DateTime('2012-01-01'), new DateTime('2012-12-31'), []];
        yield [[1], 1, new DateTime('2013-01-01'), new DateTime('2013-12-31'), []];
        yield [[1], 1, new DateTime('2014-01-01'), new DateTime('2014-12-31'), []];

        yield [[], 1, new DateTime('2014-01-01'), new DateTime('2014-12-31'), []];

        yield [[7], 1, new DateTime('2010-01-01'), new DateTime('2010-12-31'), []];
        yield [[7], 1, new DateTime('2011-01-01'), new DateTime('2011-12-31'), []];
        yield [
            [7],
            1,
            new DateTime('2012-01-01'),
            new DateTime('2012-12-31'),
            [new LeaveWithDaysLeft(10, new DateTime('2012-08-21'), 8, 1, 2, 1, 7, 1)]
        ];
        yield [
            [7],
            1,
            new DateTime('2013-01-01'),
            new DateTime('2013-12-31'),
            [new LeaveWithDaysLeft(13, new DateTime('2013-03-05'), 8, 1, 2, 1, 7, 1)]
        ];
        yield [
            [7],
            1,
            new DateTime('2014-01-01'),
            new DateTime('2014-12-31'),
            [
                new LeaveWithDaysLeft(11, new DateTime('2014-01-03'), 8, 1, 2, 1, 7, 1),
                new LeaveWithDaysLeft(12, new DateTime('2014-01-04'), 8, 1, 2, 1, 7, 1)
            ]
        ];

        // multiple employees
        yield [
            [7, 1],
            1,
            new DateTime('2012-01-01'),
            new DateTime('2012-12-31'),
            [new LeaveWithDaysLeft(10, new DateTime('2012-08-21'), 8, 1, 2, 1, 7, 1)]
        ];
    }


    public function testGetLeaveTypesForEntitlementUsageReport(): void
    {
        $filterParams = new EmployeeLeaveEntitlementUsageReportSearchFilterParams();
        $filterParams->setEmpNumber(1);
        $result = $this->dao->getLeaveTypesForEntitlementUsageReport($filterParams);
        $this->assertCount(8, $result);
        $this->assertEquals('Annual', $result[0]->getName());
        $this->assertEquals('Medical', $result[4]->getName());
        $this->assertEquals('Wesak', $result[7]->getName());
        $this->assertEquals(8, $this->dao->getLeaveTypesCountForEntitlementUsageReport($filterParams));

        // situational leave get when employee have entitlement in given leave period
        $filterParams->setEmpNumber(7);
        $filterParams->setFromDate(new DateTime('2014-01-01'));
        $filterParams->setToDate(new DateTime('2014-12-31'));
        $result = $this->dao->getLeaveTypesForEntitlementUsageReport($filterParams);
        $this->assertCount(9, $result);
        $this->assertEquals('Annual', $result[0]->getName());
        $this->assertEquals('Maternity', $result[4]->getName());
        $this->assertEquals('Wesak', $result[8]->getName());
        $this->assertEquals(9, $this->dao->getLeaveTypesCountForEntitlementUsageReport($filterParams));

        // situational leave should not get when employee don't have entitlement in given leave period
        $filterParams->setEmpNumber(7);
        $filterParams->setFromDate(new DateTime('2013-01-01'));
        $filterParams->setToDate(new DateTime('2013-12-31'));
        $result = $this->dao->getLeaveTypesForEntitlementUsageReport($filterParams);
        $this->assertCount(8, $result);
        $this->assertEquals('Annual', $result[0]->getName());
        $this->assertEquals('Medical', $result[4]->getName());
        $this->assertEquals('Wesak', $result[7]->getName());
        $this->assertEquals(8, $this->dao->getLeaveTypesCountForEntitlementUsageReport($filterParams));
    }

    public function testGetEmployeesForEntitlementUsageReportForPastEmployees(): void
    {
        $filterParams = new LeaveTypeLeaveEntitlementUsageReportSearchFilterParams();
        $filterParams->setLeaveTypeId(1);
        $filterParams->setIncludeEmployees('onlyPast');
        $filterParams->setFromDate(new DateTime('2022-01-01'));
        $filterParams->setToDate(new DateTime('2022-12-31'));

        $employees = $this->dao->getEmployeesForEntitlementUsageReport($filterParams);

        $this->assertCount(1, $employees);
        $this->assertEquals('Kayla', $employees[0]->getFirstName());
    }
}
