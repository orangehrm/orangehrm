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

namespace OrangeHRM\Tests\Core\Service;

use Generator;
use OrangeHRM\Core\Dao\IDGeneratorDao;
use OrangeHRM\Core\Helper\ClassHelper;
use OrangeHRM\Core\Service\IDGeneratorService;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\JobCategory;
use OrangeHRM\Entity\JobTitle;
use OrangeHRM\Entity\Location;
use OrangeHRM\Entity\Membership;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\KernelTestCase;

/**
 * @group Core
 * @group Service
 */
class IDGeneratorServiceTest extends KernelTestCase
{
    private IDGeneratorService $idGeneratorService;

    /**
     * PHPUnit setup function
     */
    public function setup(): void
    {
        $this->idGeneratorService = new IDGeneratorService();
    }


    /**
     * @dataProvider getNextIDDataProvider
     * @param string $entity
     * @param int $currentId
     * @param string $expected
     */
    public function testGetNextID(string $entity, int $currentId, string $expected): void
    {
        $mockDao = $this->getMockBuilder(IDGeneratorDao::class)
            ->onlyMethods(['getCurrentID', 'updateNextId'])
            ->getMock();
        $mockDao->expects($this->once())
            ->method('getCurrentID')
            ->willReturn($currentId);
        $mockDao->expects($this->once())
            ->method('updateNextId')
            ->willReturn(1);
        $this->idGeneratorService->setIDGeneratorDao($mockDao);
        $this->createKernelWithMockServices([Services::CLASS_HELPER => new ClassHelper()]);

        $nextId = $this->idGeneratorService->getNextID($entity);
        $this->assertEquals($expected, $nextId);
    }

    /**
     * @return Generator
     */
    public function getNextIDDataProvider(): Generator
    {
        yield [Employee::class, 4, '0005'];
        yield [Employee::class, 5, '0006'];
        yield [Location::class, 5, 'LOC006'];
        yield [JobCategory::class, 5, 'EEC006'];
        yield [JobTitle::class, 5, 'JOB006'];
        yield [Membership::class, 5, 'MME006'];
    }

    public function testIncrementId(): void
    {
        $mockDao = $this->getMockBuilder(IDGeneratorDao::class)
            ->onlyMethods(['getCurrentID', 'updateNextId'])
            ->getMock();
        $mockDao->expects($this->once())
            ->method('getCurrentID')
            ->willReturn(1);
        $mockDao->expects($this->once())
            ->method('updateNextId')
            ->willReturn(1);
        $this->idGeneratorService->setIDGeneratorDao($mockDao);

        $this->idGeneratorService->incrementId(Employee::class);
    }
}
