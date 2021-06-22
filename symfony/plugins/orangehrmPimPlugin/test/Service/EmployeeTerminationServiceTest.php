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

use OrangeHRM\Core\Service\NormalizerService;
use OrangeHRM\Entity\TerminationReason;
use OrangeHRM\Framework\Services;
use OrangeHRM\Pim\Dao\EmployeeTerminationDao;
use OrangeHRM\Pim\Service\EmployeeTerminationService;
use OrangeHRM\Tests\Util\KernelTestCase;

/**
 * @group Pim
 * @group Service
 */
class EmployeeTerminationServiceTest extends KernelTestCase
{
    public function testGetEmployeeTerminationDao(): void
    {
        $employeeTerminationService = new EmployeeTerminationService();
        $this->assertTrue($employeeTerminationService->getEmployeeTerminationDao() instanceof EmployeeTerminationDao);
    }

    public function testGetTerminationReasonsArray(): void
    {
        $terminationReason = new TerminationReason();
        $terminationReason->setId(1);
        $terminationReason->setName('Test Reason');

        $dao = $this->getMockBuilder(EmployeeTerminationDao::class)->onlyMethods(['getTerminationReasonList'])->getMock(
        );
        $dao->expects($this->once())
            ->method('getTerminationReasonList')
            ->willReturn([$terminationReason]);

        $service = $this->getMockBuilder(EmployeeTerminationService::class)->onlyMethods(
            ['getEmployeeTerminationDao']
        )->getMock();
        $service->expects($this->once())
            ->method('getEmployeeTerminationDao')
            ->willReturn($dao);

        $this->createKernelWithMockServices([Services::NORMALIZER_SERVICE => new NormalizerService()]);
        $this->assertEquals([['id' => 1, 'label' => 'Test Reason']], $service->getTerminationReasonsArray());
    }
}
