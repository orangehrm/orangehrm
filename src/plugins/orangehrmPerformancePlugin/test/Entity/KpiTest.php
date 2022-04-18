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

namespace OrangeHRM\Tests\Performance\Entity;

use OrangeHRM\Entity\Kpi;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Performance
 * @group Entity
 */
class KpiTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([Kpi::class]);
    }

    public function testKpiEntity(): void
    {
        $kpi = new Kpi();
        $kpi->setId(1);
        $kpi->setTitle('Code Clarity');
        $kpi->getDecorator()->setJobTitleById(1);
        $kpi->setMinRating(0);
        $kpi->setMaxRating(100);
        $kpi->setDefaultKpi(true);
        $kpi->setDeletedAt(null);
        $this->persist($kpi);

        /** @var Kpi $kpi */
        $kpi = $this->getRepository(Kpi::class)->find(1);
        $this->assertEquals(1, $kpi->getId());
        $this->assertEquals('Code Clarity', $kpi->getTitle());
        $this->assertEquals(1, $kpi->getJobTitle()->getId());
        $this->assertEquals(0, $kpi->getMinRating());
        $this->assertEquals(100, $kpi->getMaxRating());
        $this->assertTrue($kpi->isDefaultKpi());
        $this->assertNull($kpi->getDeletedAt());
    }
}
