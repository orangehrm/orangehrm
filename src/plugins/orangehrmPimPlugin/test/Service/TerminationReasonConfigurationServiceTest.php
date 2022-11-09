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

use OrangeHRM\Pim\Dao\TerminationReasonConfigurationDao;
use OrangeHRM\Pim\Dto\TerminationReasonConfigurationSearchFilterParams;
use OrangeHRM\Pim\Service\TerminationReasonConfigurationService;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\TerminationReason;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 * @group Service
 */
class TerminationReasonConfigurationServiceTest extends TestCase
{
    private TerminationReasonConfigurationService $terminationReasonService;
    private string $fixture;

    /**
     * Set up method
     */
    protected function setUp(): void
    {
        $this->terminationReasonService = new TerminationReasonConfigurationService();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmPimPlugin/test/fixtures/TerminationReasonConfigurationDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetTerminationReasonList(): void
    {
        $terminationReasonList = TestDataService::loadObjectList(TerminationReason::class, $this->fixture, 'TerminationReason');
        $terminationReasonConfigurationFilterParams = new TerminationReasonConfigurationSearchFilterParams();
        $TerminationReasonDao = $this->getMockBuilder(TerminationReasonConfigurationDao::class)->getMock();
        $TerminationReasonDao->expects($this->once())
            ->method('getTerminationReasonList')
            ->with($terminationReasonConfigurationFilterParams)
            ->will($this->returnValue($terminationReasonList));
        $this->terminationReasonService->setTerminationReasonDao($TerminationReasonDao);
        $result = $this->terminationReasonService->getTerminationReasonList($terminationReasonConfigurationFilterParams);
        $this->assertCount(3, $result);
        $this->assertTrue($result[0] instanceof TerminationReason);
    }

    public function testGetTerminationReasonById(): void
    {
        $terminationReasonList = TestDataService::loadObjectList(TerminationReason::class, $this->fixture, 'TerminationReason');

        $TerminationReasonDao = $this->getMockBuilder(TerminationReasonConfigurationDao::class)->getMock();
        $TerminationReasonDao->expects($this->once())
            ->method('getTerminationReasonById')
            ->with(1)
            ->will($this->returnValue($terminationReasonList[0]));
        $this->terminationReasonService->setTerminationReasonDao($TerminationReasonDao);
        $result = $this->terminationReasonService->getTerminationReasonById(1);
        $this->assertEquals($terminationReasonList[0], $result);
    }

    public function testDeleteTerminationReasons(): void
    {
        $terminationReasonList = [1, 2, 3];

        $TerminationReasonDao = $this->getMockBuilder(TerminationReasonConfigurationDao::class)->getMock();
        $TerminationReasonDao->expects($this->once())
            ->method('deleteTerminationReasons')
            ->with($terminationReasonList)
            ->will($this->returnValue(3));
        $this->terminationReasonService->setTerminationReasonDao($TerminationReasonDao);
        $result = $this->terminationReasonService->deleteTerminationReasons($terminationReasonList);
        $this->assertEquals(3, $result);
    }

    public function testGetTerminationReasonByName(): void
    {
        $terminationReasonList = TestDataService::loadObjectList('TerminationReason', $this->fixture, 'TerminationReason');
        $TerminationReasonDao = $this->getMockBuilder(TerminationReasonConfigurationDao::class)->getMock();
        $TerminationReasonDao->expects($this->once())
            ->method('getTerminationReasonByName')
            ->with(1)
            ->will($this->returnValue($terminationReasonList[0]));
        $this->terminationReasonService->setTerminationReasonDao($TerminationReasonDao);
        $result = $this->terminationReasonService->getTerminationReasonByName(1);
        $this->assertEquals($terminationReasonList[0], $result);
    }
}
