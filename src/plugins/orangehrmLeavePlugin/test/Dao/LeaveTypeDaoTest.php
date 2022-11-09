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

use OrangeHRM\Config\Config;
use OrangeHRM\Entity\LeaveType;
use OrangeHRM\Leave\Dao\LeaveTypeDao;
use OrangeHRM\Leave\Dto\LeaveTypeSearchFilterParams;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Leave
 * @group Dao
 */
class LeaveTypeDaoTest extends TestCase
{
    /**
     * @var LeaveTypeDao
     */
    private $dao;
    private $fixture;

    /**
     * Set up method
     */
    protected function setUp(): void
    {
        $this->dao = new LeaveTypeDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmLeavePlugin/test/fixtures/LeaveType.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetLeaveTypeList(): void
    {
        $entitlementList = TestDataService::loadObjectList(LeaveType::class, $this->fixture, 'LeaveType');
        $expected = [
            $entitlementList[3],
            $entitlementList[0],
            $entitlementList[6],
            $entitlementList[1],
            $entitlementList[5]
        ];
        $results = $this->dao->getLeaveTypeList();

        $this->_compareLeaveTypes($expected, $results);
    }

    /**
     * @return LeaveType
     */
    private function _getLeaveTypeObjectWithValues(): LeaveType
    {
        $leaveType = new LeaveType();
        $leaveType->setName('Special');
        $leaveType->setDeleted(false);

        return $leaveType;
    }

    public function testSaveLeaveTypeReturnValue(): void
    {
        TestDataService::truncateTables([LeaveType::class]);
        $leaveType = $this->dao->saveLeaveType($this->_getLeaveTypeObjectWithValues());
        $this->assertTrue($leaveType instanceof LeaveType);
        $this->assertEquals(1, $leaveType->getId());
    }

    public function testSaveLeaveTypeCheckSavedType(): void
    {
        TestDataService::truncateTables([LeaveType::class]);
        $this->dao->saveLeaveType($this->_getLeaveTypeObjectWithValues());

        $savedLeaveTypes = TestDataService::fetchLastInsertedRecords(LeaveType::class, 1);

        $this->assertFalse($savedLeaveTypes[0]->isDeleted());
        $this->assertEquals('Special', $savedLeaveTypes[0]->getName());
    }

    public function testDeleteLeaveTypeReturnValue(): void
    {
        $this->assertEquals(2, $this->dao->deleteLeaveType([1, 2]));
        $this->assertEquals(1, $this->dao->deleteLeaveType([4]));
    }

    public function testDeleteLeaveTypeValues(): void
    {
        $this->assertEquals(1, $this->dao->deleteLeaveType([1]));
        $deletedTypeObject = TestDataService::fetchObject(LeaveType::class, 1);

        $this->assertEquals(1, $deletedTypeObject->getId());
        $this->assertTrue($deletedTypeObject->isDeleted());
    }

    public function testDeleteLeaveTypeList(): void
    {
        $this->assertEquals(2, $this->dao->deleteLeaveType([1, 2]));
        $leaveTypeList = $this->dao->getDeletedLeaveTypeList();

        $this->assertCount(4, $leaveTypeList);

        $this->assertEquals(1, $leaveTypeList[0]->getId());
        $this->assertEquals(3, $leaveTypeList[1]->getId());
        $this->assertEquals(2, $leaveTypeList[2]->getId());
        $this->assertEquals(5, $leaveTypeList[3]->getId());
    }

    public function testGetDeletedLeaveTypeListObjectTypes(): void
    {
        $leaveTypeList = $this->dao->getDeletedLeaveTypeList();

        foreach ($leaveTypeList as $leaveTypeObj) {
            $this->assertTrue($leaveTypeObj instanceof LeaveType);
        }
    }

    public function testGetDeletedLeaveTypeListCount(): void
    {
        $leaveTypeList = $this->dao->getDeletedLeaveTypeList();
        $this->assertCount(2, $leaveTypeList);
    }

    public function testGetDeletedLeaveTypeListInverseResult(): void
    {
        $leaveTypeList = $this->dao->getDeletedLeaveTypeList();

        foreach ($leaveTypeList as $leaveTypeObj) {
            $this->assertNotContains($leaveTypeObj->getId(), [1, 2, 4, 6, 7]);
            $this->assertNotContains($leaveTypeObj->getName(), ['Casual', 'Medical', 'Annual', 'Wesak', 'Christmas']);
        }
    }

    public function testGetDeletedLeaveTypeListValuesAndOrder(): void
    {
        $leaveTypeList = $this->dao->getDeletedLeaveTypeList();

        $this->assertEquals(3, $leaveTypeList[0]->getId());
        $this->assertEquals('Company', $leaveTypeList[0]->getName());

        $this->assertEquals(5, $leaveTypeList[1]->getId());
        $this->assertEquals('Sick', $leaveTypeList[1]->getName());
    }

    public function testUndeleteLeaveTypeReturnValue(): void
    {
        $leaveType = $this->dao->undeleteLeaveType(3);
        $this->assertTrue($leaveType instanceof LeaveType);
        $this->assertEquals('Company', $leaveType->getName());

        $leaveType = $this->dao->undeleteLeaveType(1);
        $this->assertTrue($leaveType instanceof LeaveType);
        $this->assertEquals('Casual', $leaveType->getName());
    }

    public function testUndeleteLeaveTypeValues(): void
    {
        $this->assertTrue($this->dao->undeleteLeaveType(3) instanceof LeaveType);
        $undeletedTypeObject = TestDataService::fetchObject(LeaveType::class, 3);

        $this->assertEquals(3, $undeletedTypeObject->getId());
        $this->assertFalse($undeletedTypeObject->isDeleted());
    }

    public function testUndeleteLeaveTypeList(): void
    {
        $this->assertTrue($this->dao->undeleteLeaveType(3) instanceof LeaveType);

        $leaveTypeList = $this->dao->getLeaveTypeList();

        $this->assertCount(6, $leaveTypeList);
        $this->assertEquals('Company', $leaveTypeList[3]->getName());
    }

    public function testGetLeaveTypeByNameType(): void
    {
        $this->assertTrue($this->dao->getLeaveTypeByName('Casual') instanceof LeaveType);
        $this->assertNull($this->dao->getLeaveTypeByName('Invalid '));
    }

    public function testGetLeaveTypeByNameValues(): void
    {
        $leaveTypeObject = $this->dao->getLeaveTypeByName('Casual');

        $this->assertEquals(1, $leaveTypeObject->getId());
        $this->assertEquals('Casual', $leaveTypeObject->getName());
    }

    public function testGetLeaveTypeByIdObjectType(): void
    {
        // Active type
        $this->assertTrue($this->dao->getLeaveTypeById(1) instanceof LeaveType);

        // Deleted type
        $this->assertTrue($this->dao->getLeaveTypeById(3) instanceof LeaveType);
    }

    public function testGetLeaveTypeByIdObjectWrongArgument(): void
    {
        $this->assertNull($this->dao->getLeaveTypeById(0));
    }

    public function testGetLeaveTypeByIdValues(): void
    {
        $leaveTypeObject = $this->dao->getLeaveTypeById(1);

        $this->assertEquals(1, $leaveTypeObject->getId());
        $this->assertEquals('Casual', $leaveTypeObject->getName());
    }

    public function testGetLeaveTypeListObjectTypes(): void
    {
        $leaveTypeList = $this->dao->getLeaveTypeList();

        foreach ($leaveTypeList as $leaveTypeObj) {
            $this->assertTrue($leaveTypeObj instanceof LeaveType);
        }
    }

    public function testGetLeaveTypeListCount(): void
    {
        $leaveTypeList = $this->dao->getLeaveTypeList();
        $this->assertCount(5, $leaveTypeList);
    }

    public function testGetLeaveTypeListInverseResult(): void
    {
        $leaveTypeList = $this->dao->getLeaveTypeList();

        foreach ($leaveTypeList as $leaveTypeObj) {
            $this->assertNotContains($leaveTypeObj->getId(), [3, 5]);
            $this->assertNotContains($leaveTypeObj->getName(), ['Company', 'Sick']);
        }
    }

    public function testGetLeaveTypeListValuesAndOrder()
    {
        $leaveTypeList = $this->dao->getLeaveTypeList();

        $this->assertEquals(4, $leaveTypeList[0]->getId());
        $this->assertEquals('Annual', $leaveTypeList[0]->getName());

        $this->assertEquals(6, $leaveTypeList[4]->getId());
        $this->assertEquals('Wesak', $leaveTypeList[4]->getName());
    }

    /**
     * @param array $expected
     * @param array $results
     */
    protected function _compareLeaveTypes(array $expected, array $results): void
    {
        $this->assertEquals(count($expected), count($results));

        for ($i = 0; $i < count($expected); $i++) {
            $this->_compareLeaveType($expected[$i], $results[$i]);
        }
    }

    /**
     * @param LeaveType $expected
     * @param LeaveType $actual
     */
    protected function _compareLeaveType(LeaveType $expected, LeaveType $actual): void
    {
        $this->assertEquals($expected->getId(), $actual->getId());
        $this->assertEquals($expected->getName(), $actual->getName());
        $this->assertEquals($expected->isDeleted(), $actual->isDeleted());
    }

    public function testGetSearchLeaveTypesCount(): void
    {
        $leaveTypeSearchParams = new LeaveTypeSearchFilterParams();
        $result = $this->dao->getSearchLeaveTypesCount($leaveTypeSearchParams);
        $this->assertEquals(5, $result);
    }


    public function testSearchLeaveType(): void
    {
        $leaveTypeSearchParams = new LeaveTypeSearchFilterParams();
        $result = $this->dao->searchLeaveType($leaveTypeSearchParams);
        $this->assertCount(5, $result);
        $this->assertTrue($result[0] instanceof LeaveType);
        $this->assertEquals('Annual', $result[0]->getName());
        $this->assertEquals('Casual', $result[1]->getName());
        $this->assertEquals('Christmas', $result[2]->getName());
        $this->assertEquals('Medical', $result[3]->getName());
        $this->assertEquals('Wesak', $result[4]->getName());
    }

    public function testSearchLeaveTypeSearchByName(): void
    {
        $leaveTypeSearchParams = new LeaveTypeSearchFilterParams();
        $leaveTypeSearchParams->setName('Casual');
        $result = $this->dao->searchLeaveType($leaveTypeSearchParams);
        $this->assertCount(1, $result);
        $this->assertTrue($result[0] instanceof LeaveType);
        $this->assertEquals('Casual', $result[0]->getName());
    }

    public function testSearchLeaveTypeWithLimit(): void
    {
        $leaveTypeSearchParams = new LeaveTypeSearchFilterParams();
        $leaveTypeSearchParams->setLimit(1);

        $result = $this->dao->searchLeaveType($leaveTypeSearchParams);
        $this->assertCount(1, $result);
    }
}
