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

use OrangeHRM\Entity\Report;
use OrangeHRM\Entity\ReportGroup;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 * @group Entity
 */
class PimDefinedReportTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateTables([Report::class]);
        TestDataService::truncateTables([ReportGroup::class]);
    }

    public function testAssignLeave(): void
    {
        $reportGroup = new ReportGroup();
        $reportGroup->setId(1);
        $reportGroup->setName("pim");
        $reportGroup->setCoreSql("sql");
        $this->persist($reportGroup);

        $report = new Report();
        $report->setName("PIM Sample Report");
        $report->setReportGroup($reportGroup);
        $report->setUseFilterField(1);
        $report->setType("PIM_DEFINED");
        $this->persist($report);

        /**@var Report $report */
        $report = $this->getRepository(Report::class)->find(1);
        $this->assertEquals("PIM Sample Report", $report->getName());
        $this->assertEquals(1, $report->getReportGroup()->getId());
        $this->assertEquals(true, $report->isUseFilterField());
        $this->assertEquals("PIM_DEFINED", $report->getType());
    }
}
