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

namespace OrangeHRM\Tests\Pim\Entity;

use DateTime;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmployeeAttachment;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 * @group Entity
 */
class EmployeeAttachmentTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([EmployeeAttachment::class, Employee::class]);
    }

    public function testEmployeeAttachmentEntity(): void
    {
        $employee = new Employee();
        $employee->setFirstName('Kayla');
        $employee->setLastName('Abbey');
        $employee->setEmployeeId('0001');
        $this->persist($employee);

        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->method('getNow')
            ->willReturn(new DateTime('2021-10-04'));
        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => $dateTimeHelper]);
        $employeeAttachment = new EmployeeAttachment();
        $employeeAttachment->setEmployee($employee);
        $employeeAttachment->setAttachId(1);
        $employeeAttachment->setDescription("Test");
        $employeeAttachment->setFilename("attachment.txt");
        $employeeAttachment->setSize(6);
        $employeeAttachment->setAttachment('test');
        $employeeAttachment->setScreen('personal');
        $employeeAttachment->setFileType('text/plain');
        $employeeAttachment->setAttachedBy(1);
        $employeeAttachment->setAttachedByName('Admin');
        $employeeAttachment->setAttachedTime(new DateTime('2021-05-23'));
        $this->persist($employeeAttachment);

        /** @var EmployeeAttachment[] $empContracts */
        $empContracts = $this->getRepository(EmployeeAttachment::class)->findBy(['employee' => 1, 'attachId' => 1]);
        $employeeAttachment = $empContracts[0];
        $this->assertEquals('0001', $employeeAttachment->getEmployee()->getEmployeeId());
        $this->assertEquals(1, $employeeAttachment->getAttachId());
        $this->assertEquals("Test", $employeeAttachment->getDescription());
        $this->assertEquals("attachment.txt", $employeeAttachment->getFilename());
        $this->assertEquals(6, $employeeAttachment->getSize());
        $this->assertEquals('test', $employeeAttachment->getAttachment());
        $this->assertEquals('personal', $employeeAttachment->getScreen());
        $this->assertEquals('text/plain', $employeeAttachment->getFileType());
        $this->assertEquals(1, $employeeAttachment->getAttachedBy());
        $this->assertEquals('Admin', $employeeAttachment->getAttachedByName());
        $this->assertEquals('2021-05-23', $employeeAttachment->getAttachedTime()->format('Y-m-d'));
    }
}
