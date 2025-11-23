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

namespace OrangeHRM\Tests\Performance\Service;

use Exception;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\Kpi;
use OrangeHRM\ORM\Exception\TransactionException;
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
    private KpiService $kpiService;
    protected string $fixture;

    protected function setUp(): void
    {
        $this->kpiService = new KpiService();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmPerformancePlugin/test/fixtures/KpiService.yaml';
        TestDataService::populate($this->fixture);
    }

    public function testGetKpiDao(): void
    {
        $result = $this->kpiService->getKpiDao();
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

    public function testSaveKpiWithDefaultTrue(): void
    {
        $kpi = new Kpi();
        $kpi->getDecorator()->setJobTitleById(1);
        $kpi->setTitle('indicator 4');
        $kpi->setMinRating(0);
        $kpi->setMaxRating(100);
        $kpi->setDefaultKpi(true);

        $result = $this->getEntityManager()->getRepository(Kpi::class)->findBy(['defaultKpi' => true]);
        $this->assertCount(1, $result);
        $this->assertEquals('Planning Methodologies', $result[0]->getTitle());

        $this->assertInstanceOf(Kpi::class, $this->kpiService->saveKpi($kpi));

        $result = $this->getEntityManager()->getRepository(Kpi::class)->findBy(['defaultKpi' => true]);
        $this->assertCount(1, $result);
        $this->assertEquals('indicator 4', $result[0]->getTitle());
    }

    public function testSaveKpiWithDefaultTrue2(): void
    {
        // Testing whether currently set default true will get unset if saved again
        $kpi = new Kpi();
        $kpi->getDecorator()->setJobTitleById(3);
        $kpi->setTitle('Planning Methodologies');
        $kpi->setMinRating(10);
        $kpi->setMaxRating(20);
        $kpi->setDefaultKpi(true);

        $result = $this->getEntityManager()->getRepository(Kpi::class)->findBy(['defaultKpi' => true]);
        $this->assertCount(1, $result);
        $this->assertEquals('Planning Methodologies', $result[0]->getTitle());
        $this->assertEquals(1, $result[0]->getMinRating());
        $this->assertEquals(50, $result[0]->getMaxRating());

        $this->assertInstanceOf(Kpi::class, $this->kpiService->saveKpi($kpi));

        $result = $this->getEntityManager()->getRepository(Kpi::class)->findBy(['defaultKpi' => true]);
        $this->assertCount(1, $result);
        $this->assertEquals('Planning Methodologies', $result[0]->getTitle());
        $this->assertEquals(10, $result[0]->getMinRating());
        $this->assertEquals(20, $result[0]->getMaxRating());
    }

    public function testKpiServiceExceptionForSaveKpi(): void
    {
        $kpi = new Kpi();
        $kpi->getDecorator()->setJobTitleById(1);
        $kpi->setTitle('indicator 4');
        $kpi->setMinRating(40);
        $kpi->setMaxRating(20);

        $this->expectException(KpiServiceException::class);
        $this->expectExceptionMessage("Minimum rating should be less than Maximum rating");

        $this->kpiService->saveKpi($kpi);
    }

    public function testTransactionExceptionForSaveKpi(): void
    {
        $kpi = new Kpi();
        $kpi->getDecorator()->setJobTitleById(1);
        $kpi->setTitle('indicator 4');
        $kpi->setMinRating(2);
        $kpi->setMaxRating(20);

        $kpiService = $this->getMockBuilder(KpiService::class)
            ->onlyMethods(['getKpiDao'])
            ->getMock();
        $kpiService->expects($this->once())
            ->method('getKpiDao')
            ->willReturnCallback(function () {
                throw new Exception();
            });

        $this->expectException(TransactionException::class);
        $kpiService->saveKpi($kpi);
    }
}
