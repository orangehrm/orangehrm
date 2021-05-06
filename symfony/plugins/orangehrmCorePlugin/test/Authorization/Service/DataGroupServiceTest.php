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
 *
 */

namespace OrangeHRM\Core\Tests\Authorization\Service;

use OrangeHRM\Core\Authorization\Dao\DataGroupDao;
use OrangeHRM\Core\Authorization\Service\DataGroupService;
use OrangeHRM\Entity\DataGroup;
use OrangeHRM\Entity\DataGroupPermission;
use OrangeHRM\Entity\UserRole;
use OrangeHRM\Tests\Util\TestCase;

/**
 * Description of DataGroupServiceTest
 * @group Core
 * @group Service
 */
class DataGroupServiceTest extends TestCase
{
    /**
     * @var DataGroupService
     */
    private DataGroupService $service;

    /**
     * Set up method
     */
    protected function setUp(): void
    {
        $this->service = new DataGroupService();
    }

    public function testGetDataGroupPermission()
    {
        $userRole = new UserRole();
        $userRole->setId(1);

        $dataGroup = new DataGroup();
        $dataGroup->setId(1);

        $dataGroupPermission = new DataGroupPermission();
        $dataGroupPermission->setId(2);
        $dataGroupPermission->setUserRole($userRole);
        $dataGroupPermission->setDataGroup($dataGroup);
        $dataGroupPermission->setCanRead(true);
        $dataGroupPermission->setCanCreate(true);
        $dataGroupPermission->setCanUpdate(true);
        $dataGroupPermission->setCanDelete(true);
        $dataGroupPermission->setSelf(true);

        $dao = $this->getMockBuilder(DataGroupDao::class)
            ->onlyMethods(['getDataGroupPermission'])
            ->getMock();
        $dao->expects($this->once())
            ->method('getDataGroupPermission')
            ->with('test', 2, true)
            ->will($this->returnValue([$dataGroupPermission]));

        $this->service->setDao($dao);
        $result = $this->service->getDataGroupPermission('test', 2, true);
        $this->assertEquals([$dataGroupPermission], $result);
    }

    public function testGetDataGroups(): void
    {
        $dao = $this->getMockBuilder(DataGroupDao::class)
            ->onlyMethods(['getDataGroups'])
            ->getMock();
        $dao->expects($this->once())
            ->method('getDataGroups')
            ->will($this->returnValue([]));

        $this->service->setDao($dao);
        $result = $this->service->getDataGroups();
        $this->assertEquals([], $result);
    }

    public function testGetDataGroup(): void
    {
        $expected = new DataGroup();
        $expected->setId(2);
        $expected->setCanRead(true);
        $expected->setCanRead(true);
        $expected->setCanUpdate(true);
        $expected->setCanDelete(true);

        $dao = $this->getMockBuilder(DataGroupDao::class)
            ->onlyMethods(['getDataGroup'])
            ->getMock();
        $dao->expects($this->once())
            ->method('getDataGroup')
            ->with('xyz')
            ->will($this->returnValue($expected));

        $this->service->setDao($dao);
        $result = $this->service->getDataGroup('xyz');
        $this->assertEquals($expected, $result);
    }
}
