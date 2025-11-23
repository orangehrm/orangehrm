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

namespace OrangeHRM\Tests\Pim\Service;

use Generator;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Authorization\Helper\UserRoleManagerHelper;
use OrangeHRM\Core\Authorization\Manager\BasicUserRoleManager;
use OrangeHRM\Core\Traits\ETagHelperTrait;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmpPicture;
use OrangeHRM\Framework\Services;
use OrangeHRM\Pim\Dao\EmployeePictureDao;
use OrangeHRM\Pim\Service\EmployeePictureService;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\Mock\MockCacheService;

/**
 * @group Pim
 * @group Service
 */
class EmployeePictureServiceTest extends KernelTestCase
{
    use ETagHelperTrait;
    use EntityManagerHelperTrait;

    public function testGetEmployeePictureDao(): void
    {
        $service = new EmployeePictureService();
        $this->assertTrue($service->getEmployeePictureDao() instanceof EmployeePictureDao);
    }

    public function testGetAccessibleEmpPictureByEmpNumber(): void
    {
        $empNumber = 5;
        $userRoleManager = $this->getMockBuilder(BasicUserRoleManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAccessibleEntityIds'])
            ->getMock();
        $userRoleManager->expects($this->once())
            ->method('getAccessibleEntityIds')
            ->willReturn([1, 2, 3]);

        $userRoleManagerHelper = $this->getMockBuilder(UserRoleManagerHelper::class)
            ->onlyMethods(['isSelfByEmpNumber'])
            ->getMock();
        $userRoleManagerHelper->expects($this->once())
            ->method('isSelfByEmpNumber')
            ->willReturn(true);

        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $empPicture = new EmpPicture();
        $empPicture->setEmployee($employee);
        $dao = $this->getMockBuilder(EmployeePictureDao::class)
            ->onlyMethods(['getEmpPictureByEmpNumber'])
            ->getMock();
        $dao->expects($this->once())
            ->method('getEmpPictureByEmpNumber')
            ->with($empNumber)
            ->willReturn($empPicture);

        $service = $this->getMockBuilder(EmployeePictureService::class)
            ->onlyMethods(['getEmployeePictureDao'])
            ->getMock();
        $service->expects($this->once())
            ->method('getEmployeePictureDao')
            ->willReturn($dao);

        $this->createKernelWithMockServices(
            [
                Services::USER_ROLE_MANAGER => $userRoleManager,
                Services::USER_ROLE_MANAGER_HELPER => $userRoleManagerHelper
            ]
        );

        $this->assertTrue($service->getAccessibleEmpPictureByEmpNumber($empNumber) instanceof EmpPicture);
    }

    public function testGetAccessibleEmpPictureByEmpNumberNotAllowed(): void
    {
        $empNumber = 5;
        $userRoleManager = $this->getMockBuilder(BasicUserRoleManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAccessibleEntityIds'])
            ->getMock();
        $userRoleManager->expects($this->once())
            ->method('getAccessibleEntityIds')
            ->willReturn([1, 2, 3]);

        $userRoleManagerHelper = $this->getMockBuilder(UserRoleManagerHelper::class)
            ->onlyMethods(['isSelfByEmpNumber'])
            ->getMock();
        $userRoleManagerHelper->expects($this->once())
            ->method('isSelfByEmpNumber')
            ->willReturn(false);

        $service = $this->getMockBuilder(EmployeePictureService::class)
            ->onlyMethods(['getEmployeePictureDao'])
            ->getMock();
        $service->expects($this->never())
            ->method('getEmployeePictureDao');

        $this->createKernelWithMockServices(
            [
                Services::USER_ROLE_MANAGER => $userRoleManager,
                Services::USER_ROLE_MANAGER_HELPER => $userRoleManagerHelper
            ]
        );

        $this->assertNull($service->getAccessibleEmpPictureByEmpNumber($empNumber));
    }

    public function testGetEmpPictureByEmpNumber(): void
    {
        $empNumber = 5;
        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $empPicture = new EmpPicture();
        $empPicture->setEmployee($employee);
        $dao = $this->getMockBuilder(EmployeePictureDao::class)
            ->onlyMethods(['getEmpPictureByEmpNumber'])
            ->getMock();
        $dao->expects($this->once())
            ->method('getEmpPictureByEmpNumber')
            ->with($empNumber)
            ->willReturn($empPicture);

        $service = $this->getMockBuilder(EmployeePictureService::class)
            ->onlyMethods(['getEmployeePictureDao'])
            ->getMock();
        $service->expects($this->once())
            ->method('getEmployeePictureDao')
            ->willReturn($dao);

        $this->assertTrue($service->getEmpPictureByEmpNumber($empNumber) instanceof EmpPicture);
    }

    public function testSaveEmployeePicture(): void
    {
        $empNumber = 5;
        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $empPicture = new EmpPicture();
        $empPicture->setEmployee($employee);
        $dao = $this->getMockBuilder(EmployeePictureDao::class)
            ->onlyMethods(['saveEmployeePicture'])
            ->getMock();
        $dao->expects($this->once())
            ->method('saveEmployeePicture')
            ->willReturnCallback(
                function (EmpPicture $empPicture) {
                    return $empPicture;
                }
            );

        $service = $this->getMockBuilder(EmployeePictureService::class)
            ->onlyMethods(['getEmployeePictureDao'])
            ->getMock();
        $service->expects($this->once())
            ->method('getEmployeePictureDao')
            ->willReturn($dao);

        $this->assertTrue($service->saveEmployeePicture($empPicture) instanceof EmpPicture);
    }

    /**
     * @dataProvider getPictureSizeAdjustDataProvider
     */
    public function testPictureSizeAdjust($picture, array $expected): void
    {
        $service = new EmployeePictureService();
        $this->assertEquals($expected, $service->pictureSizeAdjust($picture));
    }

    /**
     * @return Generator
     */
    public function getPictureSizeAdjustDataProvider(): Generator
    {
        $fixturesBasePath = Config::get(Config::PLUGINS_DIR) . '/orangehrmPimPlugin/test/fixtures/images/';
        yield [file_get_contents($fixturesBasePath . '115x225.jpeg'), [102, 200]];
        yield [file_get_contents($fixturesBasePath . '200x200.jpeg'), [200, 200]];
        yield [file_get_contents($fixturesBasePath . '225x115.jpeg'), [200, 102]];
        yield [file_get_contents($fixturesBasePath . '225x225.jpeg'), [200, 200]];
    }

    public function testGetETagByEmpPicture(): void
    {
        $this->createKernelWithMockServices([Services::CACHE => MockCacheService::getCache()]);
        $pictureData = 'test_picture';
        $employee = new Employee();
        $employee->setEmpNumber(1);
        $empPicture = new EmpPicture();
        $empPicture->setPicture($pictureData);
        $empPicture->setEmployee($employee);

        $service = $this->getMockBuilder(EmployeePictureService::class)
            ->onlyMethods(['getEmpPictureByEmpNumber'])
            ->getMock();
        $service->expects($this->once())
            ->method('getEmpPictureByEmpNumber')
            ->with(1)
            ->willReturn($empPicture);

        $expected = $this->generateEtag($empPicture->getDecorator()->getPicture());
        $this->assertEquals($expected, $service->getEmpPictureETagByEmpNumber(1));

        $this->getEntityManager()->flush();
    }
}
