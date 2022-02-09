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

namespace OrangeHRM\Tests\Performance\Service;

use OrangeHRM\Config\Config;
use OrangeHRM\Entity\Kpi;
use OrangeHRM\Performance\Dao\KpiDao;
use OrangeHRM\Performance\Exception\KpiServiceException;
use OrangeHRM\Performance\Service\KpiService;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Performance
 * @group Service
 */
class KpiServiceTest extends KernelTestCase
{
    private KpiService $KpiService;
    protected string $fixture;

    protected function setUp(): void
    {
        $this->KpiService = new KpiService();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmPerformancePlugin/test/fixtures/KpiService.yaml';
        TestDataService::populate($this->fixture);
    }

    public function testGetKpiDao(): void
    {
        $result = $this->KpiService->getKpiDao();
        $this->assertInstanceOf(KpiDao::class, $result);
    }

    public function testSaveKpi(): void
    {
        $kpi = new Kpi();
        $kpi->getDecorator()->setJobTitleById(1);
        $kpi->setTitle('indicator 4');
        $kpi->setMinRating(0);
        $kpi->setMaxRating(100);

        $kpiDao = $this->getMockBuilder(KpiDao::class)
            ->onlyMethods(['saveKpi'])
            ->getMock();
        $kpiDao->expects($this->once())
            ->method('saveKpi')
            ->with($kpi)
            ->willReturn($kpi);

        $kpiService = $this->getMockBuilder(KpiService::class)
            ->onlyMethods(['getKpiDao'])
            ->getMock();
        $kpiService->expects($this->once())
            ->method('getKpiDao')
            ->willReturn($kpiDao);

        $this->assertInstanceOf(Kpi::class, $kpiService->saveKpi($kpi));
    }

    public function testExceptionForSaveKpi(): void
    {
        $kpi = new Kpi();
        $kpi->getDecorator()->setJobTitleById(1);
        $kpi->setTitle('indicator 4');
        $kpi->setMinRating(40);
        $kpi->setMaxRating(20);

        $this->expectException(KpiServiceException::class);
        $this->expectExceptionMessage("Minimum rating should be less than Maximum rating");

        $this->KpiService->saveKpi($kpi);
    }
}
