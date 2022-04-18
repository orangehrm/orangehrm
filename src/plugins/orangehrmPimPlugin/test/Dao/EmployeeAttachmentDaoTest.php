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

namespace OrangeHRM\Tests\Pim\Dao;

use DateTime;
use InvalidArgumentException;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmployeeAttachment;
use OrangeHRM\Framework\Services;
use OrangeHRM\Pim\Dao\EmployeeAttachmentDao;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 * @group Dao
 */
class EmployeeAttachmentDaoTest extends KernelTestCase
{
    use EntityManagerHelperTrait;

    private EmployeeAttachmentDao $employeeAttachmentDao;

    protected function setUp(): void
    {
        $this->employeeAttachmentDao = new EmployeeAttachmentDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmPimPlugin/test/fixtures/EmpAttachmentDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testSaveEmployeeAttachment(): void
    {
        $this->setDateTimeHelper();
        $employee = $this->getEntityReference(Employee::class, 1);
        $employeeAttachment = new EmployeeAttachment();
        $employeeAttachment->setEmployee($employee);
        $employeeAttachment->setDescription('Test Comment');
        $employeeAttachment->setFilename('new.txt');
        $employeeAttachment->setFileType('text/plain');
        $employeeAttachment->setSize(6);
        $employeeAttachment->setAttachment('test');
        $employeeAttachment->setScreen('personal');

        $this->employeeAttachmentDao->saveEmployeeAttachment($employeeAttachment);
        /** @var EmployeeAttachment[] $empAttachments */
        $empAttachments = $this->getRepository(EmployeeAttachment::class)->findBy(
            ['employee' => 1, 'screen' => 'personal']
        );

        $this->assertCount(3, $empAttachments);
        $empAttachment = $empAttachments[2];
        $this->assertEquals('Test Comment', $empAttachment->getDescription());
        $this->assertEquals('new.txt', $empAttachment->getFilename());
        $this->assertEquals('text/plain', $empAttachment->getFileType());
        $this->assertEquals('2021-10-04', $empAttachment->getAttachedTime()->format('Y-m-d'));
    }

    public function testSaveEmployeeAttachmentForFreshEmployee(): void
    {
        $this->setDateTimeHelper();
        $empNumber = 3;
        $employee = $this->getEntityReference(Employee::class, $empNumber);
        $employeeAttachment = new EmployeeAttachment();
        $employeeAttachment->setEmployee($employee);
        $employeeAttachment->setDescription('Test Comment');
        $employeeAttachment->setFilename('new.txt');
        $employeeAttachment->setFileType('text/plain');
        $employeeAttachment->setSize(6);
        $employeeAttachment->setAttachment('test');
        $employeeAttachment->setScreen('personal');

        $this->employeeAttachmentDao->saveEmployeeAttachment($employeeAttachment);
        /** @var EmployeeAttachment[] $empAttachments */
        $empAttachments = $this->getRepository(EmployeeAttachment::class)->findBy(
            ['employee' => $empNumber, 'screen' => 'personal']
        );

        $this->assertCount(1, $empAttachments);
        $empAttachment = $empAttachments[0];
        $this->assertEquals(1, $empAttachment->getAttachId());
        $this->assertEquals('Test Comment', $empAttachment->getDescription());
        $this->assertEquals('new.txt', $empAttachment->getFilename());
        $this->assertEquals('text/plain', $empAttachment->getFileType());
        $this->assertEquals('2021-10-04', $empAttachment->getAttachedTime()->format('Y-m-d'));
    }

    public function testSaveEmployeeAttachmentWithId(): void
    {
        $this->setDateTimeHelper();
        $id = 50;
        $employee = $this->getEntityReference(Employee::class, 1);
        $employeeAttachment = new EmployeeAttachment();
        $employeeAttachment->setAttachId($id);
        $employeeAttachment->setEmployee($employee);
        $employeeAttachment->setDescription('Test Comment');
        $employeeAttachment->setFilename('new.txt');
        $employeeAttachment->setFileType('text/plain');
        $employeeAttachment->setSize(6);
        $employeeAttachment->setAttachment('test');
        $employeeAttachment->setScreen('personal');

        $this->employeeAttachmentDao->saveEmployeeAttachment($employeeAttachment);
        /** @var EmployeeAttachment $empAttachment */
        $empAttachment = $this->getRepository(EmployeeAttachment::class)->findOneBy(
            ['employee' => 1, 'screen' => 'personal', 'attachId' => $id]
        );
        $this->assertEquals('Test Comment', $empAttachment->getDescription());
        $this->assertEquals('new.txt', $empAttachment->getFilename());
        $this->assertEquals('text/plain', $empAttachment->getFileType());
        $this->assertEquals('2021-10-04', $empAttachment->getAttachedTime()->format('Y-m-d'));
    }

    public function testEmployeeAttachmentWithInvalidScreen(): void
    {
        $this->setDateTimeHelper();
        $employeeAttachment = new EmployeeAttachment();
        foreach (EmployeeAttachment::SCREENS as $screen) {
            $employeeAttachment->setScreen($screen);
        }
        $this->expectException(InvalidArgumentException::class);
        $employeeAttachment->setScreen('InvalidScreen');
    }

    public function testGetEmployeeAttachments(): void
    {
        $screens = EmployeeAttachment::SCREENS;
        array_shift($screens);
        foreach ($screens as $screen) {
            $empDependents = $this->employeeAttachmentDao->getEmployeeAttachments(1, $screen);
            $this->assertCount(1, $empDependents);
        }
        $empDependents = $this->employeeAttachmentDao->getEmployeeAttachments(1, 'personal');
        $this->assertCount(2, $empDependents);
    }

    public function testGetEmployeeAttachment(): void
    {
        $employeeAttachment = $this->employeeAttachmentDao->getEmployeeAttachment(1, 1, 'personal');
        $this->assertEquals('Comment 1', $employeeAttachment->getDescription());
        $this->assertEquals('attachment.txt', $employeeAttachment->getFilename());
        $this->assertEquals('dGVzdA0K', $employeeAttachment->getDecorator()->getAttachment());

        $employeeAttachment = $this->employeeAttachmentDao->getEmployeeAttachment(1, 100, 'personal');
        $this->assertNull($employeeAttachment);
    }

    public function testGetEmployeeAttachmentWithoutScreen(): void
    {
        $employeeAttachment = $this->employeeAttachmentDao->getEmployeeAttachment(1, 10);
        $this->assertEquals('Comment 10', $employeeAttachment->getDescription());
        $this->assertEquals('attachment.txt', $employeeAttachment->getFilename());
        $this->assertEquals('test', $employeeAttachment->getDecorator()->getAttachment());
    }

    public function testDeleteEmployeeAttachments(): void
    {
        $rows = $this->employeeAttachmentDao->deleteEmployeeAttachments(1, 'personal', [1, 13]);
        $this->assertEquals(2, $rows);

        $empAttachmentObj = $this->getRepository(EmployeeAttachment::class)->findOneBy(
            ['employee' => 1, 'screen' => 'personal', 'attachId' => 1]
        );
        $this->assertNull($empAttachmentObj);
        $empAttachmentObj = $this->getRepository(EmployeeAttachment::class)->findOneBy(
            ['employee' => 1, 'screen' => 'personal', 'attachId' => 13]
        );
        $this->assertNull($empAttachmentObj);
    }

    public function testDeleteEmployeeAttachmentsWithWrongIdForScreen(): void
    {
        $rows = $this->employeeAttachmentDao->deleteEmployeeAttachments(1, 'personal', [1, 2]);
        $this->assertEquals(1, $rows);

        $empAttachmentObj = $this->getRepository(EmployeeAttachment::class)->findOneBy(
            ['employee' => 1, 'screen' => 'personal', 'attachId' => 1]
        );
        $this->assertNull($empAttachmentObj);
        $empAttachmentObj = $this->getRepository(EmployeeAttachment::class)->findOneBy(
            ['employee' => 1, 'screen' => 'contact', 'attachId' => 2]
        );
        $this->assertTrue($empAttachmentObj instanceof EmployeeAttachment);
    }

    private function setDateTimeHelper(): void
    {
        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->expects($this->once())
            ->method('getNow')
            ->willReturn(new DateTime('2021-10-04'));
        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => $dateTimeHelper]);
    }
}
