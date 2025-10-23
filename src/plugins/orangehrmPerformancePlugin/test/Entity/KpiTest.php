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

namespace OrangeHRM\Tests\Performance\Entity;

use OrangeHRM\Entity\JobTitle;
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
        TestDataService::truncateSpecificTables([JobTitle::class]);
    }

    public function testKpiEntity(): void
    {
        $jobTitle = new JobTitle();
        $jobTitle->setId(1);
        $jobTitle->setJobTitleName('Software Engineer');
        $jobTitle->setJobDescription('SE position');
        $this->persist($jobTitle);

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
