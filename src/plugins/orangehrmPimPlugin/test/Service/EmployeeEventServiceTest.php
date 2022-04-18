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

namespace OrangeHRM\Tests\Pim\Service;

use DateTime;
use Generator;
use OrangeHRM\Core\Authorization\Manager\BasicUserRoleManager;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Entity\EmployeeEvent;
use OrangeHRM\Entity\User;
use OrangeHRM\Entity\UserRole;
use OrangeHRM\Framework\Services;
use OrangeHRM\Pim\Dao\EmployeeEventDao;
use OrangeHRM\Pim\Dto\EmployeeEventSearchFilterParams;
use OrangeHRM\Pim\Service\EmployeeEventService;
use OrangeHRM\Tests\Util\KernelTestCase;

/**
 * @group Pim
 * @group Service
 */
class EmployeeEventServiceTest extends KernelTestCase
{
    public function testSaveEmployeeEvent(): void
    {
        $mockDao = $this->getMockBuilder(EmployeeEventDao::class)
            ->onlyMethods(['saveEmployeeEvent'])
            ->getMock();

        $employeeEvent = new EmployeeEvent();
        $employeeEvent->setEventId(1);

        $mockDao->expects($this->once())
            ->method('saveEmployeeEvent')
            ->willReturn($employeeEvent);

        $employeeEventService = new EmployeeEventService();
        $employeeEventService->setEmployeeEventDao($mockDao);
        $resultEmployeeEvent = $employeeEventService->saveEmployeeEvent($employeeEvent);
        $this->assertEquals(1, $resultEmployeeEvent->getEventId());
    }

    public function testSaveEvent(): void
    {
        $employeeEventService = $this->getMockBuilder(EmployeeEventService::class)
            ->onlyMethods(['saveEmployeeEvent', 'getUserRole'])
            ->getMock();

        $employeeEvent = new EmployeeEvent();
        $employeeEvent->setEventId(1);
        $employeeEvent->setEmpNumber(1);
        $employeeEvent->setType(EmployeeEvent::EVENT_TYPE_EMPLOYEE);
        $employeeEvent->setEvent(EmployeeEvent::EVENT_SAVE);
        $employeeEvent->setNote('Test Note');

        $employeeEventService->expects($this->once())
            ->method('saveEmployeeEvent')
            ->willReturnCallback(
                function ($employeeEvent) {
                    $employeeEvent->setEventId(1);
                    return $employeeEvent;
                }
            );
        $employeeEventService->expects($this->once())
            ->method('getUserRole')
            ->willReturn('Admin');

        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->expects($this->once())
            ->method('getNow')
            ->willReturn(new DateTime('2021-10-04'));
        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => $dateTimeHelper]);

        $resultEmployeeEvent = $employeeEventService->saveEvent(
            1,
            EmployeeEvent::EVENT_TYPE_EMPLOYEE,
            EmployeeEvent::EVENT_SAVE,
            'Test Note'
        );
        $this->assertEquals($employeeEvent->getEventId(), $resultEmployeeEvent->getEventId());
        $this->assertEquals($employeeEvent->getEmpNumber(), $resultEmployeeEvent->getEmpNumber());
        $this->assertEquals($employeeEvent->getEvent(), $resultEmployeeEvent->getEvent());
        $this->assertEquals($employeeEvent->getType(), $resultEmployeeEvent->getType());
        $this->assertEquals($employeeEvent->getNote(), $resultEmployeeEvent->getNote());
        $this->assertEquals('Admin', $resultEmployeeEvent->getCreatedBy());
        $this->assertTrue($resultEmployeeEvent->getCreatedDate() instanceof DateTime);
        $this->assertEquals('2021-10-04', $resultEmployeeEvent->getCreatedDate()->format('Y-m-d'));
    }

    /**
     * @dataProvider getUserRoleDataProvider
     * @param User|null $user
     * @param string $expected
     */
    public function testGetUserRole(?User $user, string $expected): void
    {
        $userRoleManager = $this->getMockBuilder(BasicUserRoleManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUser'])
            ->getMock();
        $userRoleManager->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $employeeEventService = $this->getMockBuilder(EmployeeEventService::class)
            ->onlyMethods(['getUserRoleManager'])
            ->getMock();
        $employeeEventService->expects($this->once())
            ->method('getUserRoleManager')
            ->willReturn($userRoleManager);

        $this->assertEquals($expected, $employeeEventService->getUserRole());
    }

    /**
     * @return Generator
     */
    public function getUserRoleDataProvider(): Generator
    {
        $userRole = new UserRole();
        $userRole->setName('Test Admin');
        $user = new User();
        $user->setUserRole($userRole);
        yield [$user, 'Test Admin'];
        yield [null, 'System'];
    }

    public function testGetEmployeeEvents(): void
    {
        $mockDao = $this->getMockBuilder(EmployeeEventDao::class)
            ->onlyMethods(['getEmployeeEvents'])
            ->getMock();

        $employeeEvent = new EmployeeEvent();
        $employeeEvent->setEventId(1);

        $mockDao->expects($this->once())
            ->method('getEmployeeEvents')
            ->willReturn([$employeeEvent]);

        $employeeEventService = new EmployeeEventService();
        $employeeEventService->setEmployeeEventDao($mockDao);
        $employeeEventSearchFilterParams = new EmployeeEventSearchFilterParams();
        $resultEmployeeEvents = $employeeEventService->getEmployeeEvents($employeeEventSearchFilterParams);
        $this->assertCount(1, $resultEmployeeEvents);
    }
}
