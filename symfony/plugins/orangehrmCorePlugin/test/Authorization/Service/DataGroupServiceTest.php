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

namespace OrangeHRM\Tests\Core\Authorization\Service;

use OrangeHRM\Core\Authorization\Dao\DataGroupDao;
use OrangeHRM\Core\Authorization\Dto\DataGroupPermissionFilterParams;
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
        $this->assertEmpty($result);
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

    public function testGetDataGroupPermissionCollectionWithOnlyOnePermissionForDataGroup(): void
    {
        $dataGroupJobTitle = new DataGroup();
        $dataGroupJobTitle->setId(2);
        $dataGroupJobTitle->setName('job_titles');
        $dataGroupJobTitle->setCanRead(true);
        $dataGroupJobTitle->setCanCreate(true);
        $dataGroupJobTitle->setCanUpdate(true);
        $dataGroupJobTitle->setCanDelete(true);

        $dataGroupPermission = new DataGroupPermission();
        $dataGroupPermission->setDataGroup($dataGroupJobTitle);
        $dataGroupPermission->setCanRead(true);
        $dataGroupPermission->setCanCreate(true);
        $dataGroupPermission->setCanUpdate(true);
        $dataGroupPermission->setCanDelete(true);


        $dataGroupPermissions = [$dataGroupPermission];

        $dataGroupPermissionFilterParams = new DataGroupPermissionFilterParams();
        $dao = $this->getMockBuilder(DataGroupDao::class)
            ->onlyMethods(['getDataGroupPermissions'])
            ->getMock();
        $dao->expects($this->once())
            ->method('getDataGroupPermissions')
            ->with($dataGroupPermissionFilterParams)
            ->willReturn($dataGroupPermissions);

        $this->service->setDao($dao);
        $result = $this->service->getDataGroupPermissionCollection($dataGroupPermissionFilterParams);
        $this->assertEquals(
            [
                'job_titles' => [
                    'canRead' => true,
                    'canCreate' => true,
                    'canUpdate' => true,
                    'canDelete' => true,
                ]
            ],
            $result->toArray()
        );
    }

    public function testGetDataGroupPermissionCollectionWithTwoPermissionsForDataGroup(): void
    {
        $dataGroupJobTitle = new DataGroup();
        $dataGroupJobTitle->setId(2);
        $dataGroupJobTitle->setName('job_titles');
        $dataGroupJobTitle->setCanRead(true);
        $dataGroupJobTitle->setCanCreate(true);
        $dataGroupJobTitle->setCanUpdate(true);
        $dataGroupJobTitle->setCanDelete(true);

        $dataGroupPermission = new DataGroupPermission();
        $dataGroupPermission->setDataGroup($dataGroupJobTitle);
        $dataGroupPermission->setCanRead(true);
        $dataGroupPermission->setCanCreate(false);
        $dataGroupPermission->setCanUpdate(false);
        $dataGroupPermission->setCanDelete(false);

        $dataGroupPermissionSupervisor = new DataGroupPermission();
        $dataGroupPermissionSupervisor->setDataGroup($dataGroupJobTitle);
        $dataGroupPermissionSupervisor->setCanRead(false);
        $dataGroupPermissionSupervisor->setCanCreate(true);
        $dataGroupPermissionSupervisor->setCanUpdate(true);
        $dataGroupPermissionSupervisor->setCanDelete(false);

        $dataGroupPermissions = [$dataGroupPermission, $dataGroupPermissionSupervisor];

        $dataGroupPermissionFilterParams = new DataGroupPermissionFilterParams();
        $dao = $this->getMockBuilder(DataGroupDao::class)
            ->onlyMethods(['getDataGroupPermissions'])
            ->getMock();
        $dao->expects($this->once())
            ->method('getDataGroupPermissions')
            ->with($dataGroupPermissionFilterParams)
            ->willReturn($dataGroupPermissions);

        $this->service->setDao($dao);
        $result = $this->service->getDataGroupPermissionCollection($dataGroupPermissionFilterParams);
        $this->assertEquals(
            [
                'job_titles' => [
                    'canRead' => true,
                    'canCreate' => true,
                    'canUpdate' => true,
                    'canDelete' => false,
                ]
            ],
            $result->toArray()
        );
    }

    public function testGetDataGroupPermissionCollectionWithMultiplePermissionsForDataGroup(): void
    {
        $dataGroupJobTitle = new DataGroup();
        $dataGroupJobTitle->setId(2);
        $dataGroupJobTitle->setName('job_titles');
        $dataGroupJobTitle->setCanRead(true);
        $dataGroupJobTitle->setCanCreate(true);
        $dataGroupJobTitle->setCanUpdate(true);
        $dataGroupJobTitle->setCanDelete(true);

        $dataGroupPermission = new DataGroupPermission();
        $dataGroupPermission->setDataGroup($dataGroupJobTitle);
        $dataGroupPermission->setCanRead(true);
        $dataGroupPermission->setCanCreate(false);
        $dataGroupPermission->setCanUpdate(false);
        $dataGroupPermission->setCanDelete(false);

        $dataGroupPermissionSupervisor = new DataGroupPermission();
        $dataGroupPermissionSupervisor->setDataGroup($dataGroupJobTitle);
        $dataGroupPermissionSupervisor->setCanRead(false);
        $dataGroupPermissionSupervisor->setCanCreate(true);
        $dataGroupPermissionSupervisor->setCanUpdate(true);
        $dataGroupPermissionSupervisor->setCanDelete(false);

        $dataGroupPermissionEss = new DataGroupPermission();
        $dataGroupPermissionEss->setDataGroup($dataGroupJobTitle);
        $dataGroupPermissionEss->setCanRead(false);
        $dataGroupPermissionEss->setCanCreate(false);
        $dataGroupPermissionEss->setCanUpdate(false);
        $dataGroupPermissionEss->setCanDelete(false);

        $dataGroupJobTitleApi = new DataGroup();
        $dataGroupJobTitleApi->setId(3);
        $dataGroupJobTitleApi->setName('apiv2_job_titles');
        $dataGroupJobTitleApi->setCanRead(true);
        $dataGroupJobTitleApi->setCanCreate(true);
        $dataGroupJobTitleApi->setCanUpdate(true);
        $dataGroupJobTitleApi->setCanDelete(true);

        $dataGroupPermissionApi = new DataGroupPermission();
        $dataGroupPermissionApi->setDataGroup($dataGroupJobTitleApi);
        $dataGroupPermissionApi->setCanRead(true);
        $dataGroupPermissionApi->setCanCreate(false);
        $dataGroupPermissionApi->setCanUpdate(false);
        $dataGroupPermissionApi->setCanDelete(false);

        $dataGroupPermissions = [
            $dataGroupPermission,
            $dataGroupPermissionSupervisor,
            $dataGroupPermissionEss,
            $dataGroupPermissionApi
        ];

        $dataGroupPermissionFilterParams = new DataGroupPermissionFilterParams();
        $dao = $this->getMockBuilder(DataGroupDao::class)
            ->onlyMethods(['getDataGroupPermissions'])
            ->getMock();
        $dao->expects($this->once())
            ->method('getDataGroupPermissions')
            ->with($dataGroupPermissionFilterParams)
            ->willReturn($dataGroupPermissions);

        $this->service->setDao($dao);
        $result = $this->service->getDataGroupPermissionCollection($dataGroupPermissionFilterParams);
        $this->assertEquals(
            [
                'job_titles' => [
                    'canRead' => true,
                    'canCreate' => true,
                    'canUpdate' => true,
                    'canDelete' => false,
                ],
                'apiv2_job_titles' => [
                    'canRead' => true,
                    'canCreate' => false,
                    'canUpdate' => false,
                    'canDelete' => false,
                ]
            ],
            $result->toArray()
        );
    }
}
