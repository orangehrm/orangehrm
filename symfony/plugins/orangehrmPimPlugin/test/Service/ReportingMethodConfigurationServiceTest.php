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

use OrangeHRM\Config\Config;
use OrangeHRM\Entity\ReportingMethod;
use OrangeHRM\Pim\Dao\ReportingMethodConfigurationDao;
use OrangeHRM\Pim\Dto\ReportingMethodSearchFilterParams;
use OrangeHRM\Pim\Service\ReportingMethodConfigurationService;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 * @group Service
 */
class ReportingMethodConfigurationServiceTest extends TestCase
{
    private ReportingMethodConfigurationService $reportingMethodService;
    private string $fixture;

    /**
     * Set up method
     */
    protected function setUp(): void
    {
        $this->reportingMethodService = new ReportingMethodConfigurationService();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmPimPlugin/test/fixtures/ReportingMethodConfigurationDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetReportingMethodList(): void
    {
        $reportingMethodList = TestDataService::loadObjectList(
            ReportingMethod::class,
            $this->fixture,
            'ReportingMethod'
        );
        $reportingMethodFilterParams = new ReportingMethodSearchFilterParams();
        $reportingMethodDao = $this->getMockBuilder(ReportingMethodConfigurationDao::class)->getMock();
        $reportingMethodDao->expects($this->once())
            ->method('getReportingMethodList')
            ->with($reportingMethodFilterParams)
            ->will($this->returnValue($reportingMethodList));
        $this->reportingMethodService->setReportingMethodDao($reportingMethodDao);
        $result = $this->reportingMethodService->getReportingMethodList($reportingMethodFilterParams);
        $this->assertCount(3, $result);
        $this->assertTrue($result[0] instanceof ReportingMethod);
    }

    public function testGetReportingMethodById(): void
    {
        $reportingMethod = TestDataService::loadObjectList(ReportingMethod::class, $this->fixture, 'ReportingMethod');

        $reportingMethodDao = $this->getMockBuilder(ReportingMethodConfigurationDao::class)->getMock();
        $reportingMethodDao->expects($this->once())
            ->method('getReportingMethodById')
            ->with(1)
            ->will($this->returnValue($reportingMethod[0]));
        $this->reportingMethodService->setReportingMethodDao($reportingMethodDao);
        $result = $this->reportingMethodService->getReportingMethodById(1);
        $this->assertEquals($reportingMethod[0], $result);
    }

    public function testDeleteReportingMethods(): void
    {
        $reportingMethodList = [1, 2, 3];

        $reportingMethodDao = $this->getMockBuilder(ReportingMethodConfigurationDao::class)->getMock();
        $reportingMethodDao->expects($this->once())
            ->method('deleteReportingMethods')
            ->with($reportingMethodList)
            ->will($this->returnValue(3));
        $this->reportingMethodService->setReportingMethodDao($reportingMethodDao);
        $result = $this->reportingMethodService->deleteReportingMethods($reportingMethodList);
        $this->assertEquals(3, $result);
    }

    public function testGetReportingMethodByName(): void
    {
        $reportingMethod = TestDataService::loadObjectList(ReportingMethod::class, $this->fixture, 'ReportingMethod');
        $reportingMethodDao = $this->getMockBuilder(ReportingMethodConfigurationDao::class)->getMock();
        $reportingMethodDao->expects($this->once())
            ->method('getReportingMethodByName')
            ->with(1)
            ->will($this->returnValue($reportingMethod[0]));
        $this->reportingMethodService->setReportingMethodDao($reportingMethodDao);
        $result = $this->reportingMethodService->getReportingMethodByName(1);
        $this->assertEquals($reportingMethod[0], $result);
    }

    public function testSaveReportingMethod(): void
    {
        $reportingMethod = new ReportingMethod();
        $reportingMethod->setName("Direct");

        $reportingMethodDao = $this->getMockBuilder(ReportingMethodConfigurationDao::class)->getMock();

        $reportingMethodDao->expects($this->once())
            ->method('saveReportingMethod')
            ->with($reportingMethod)
            ->will($this->returnValue($reportingMethod));

        $this->reportingMethodService->setReportingMethodDao($reportingMethodDao);
        $result = $this->reportingMethodService->saveReportingMethod($reportingMethod);
        $this->assertEquals($reportingMethod, $result);
    }

    public function testGetReportingMethodCount(): void
    {
        $reportingMethodFilterParams = new ReportingMethodSearchFilterParams();
        $reportingMethodDao = $this->getMockBuilder(ReportingMethodConfigurationDao::class)->getMock();

        $reportingMethodDao->expects($this->once())
            ->method('getReportingMethodCount')
            ->with($reportingMethodFilterParams)
            ->will($this->returnValue(3));

        $this->reportingMethodService->setReportingMethodDao($reportingMethodDao);
        $result = $this->reportingMethodService->getReportingMethodCount($reportingMethodFilterParams);
        $this->assertEquals(3, $result);
    }
}
