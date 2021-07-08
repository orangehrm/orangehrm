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

use OrangeHRM\Entity\ReportingMethod;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

class ReportingMethodTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([ReportingMethod::class]);
    }

    public function testReportingMethodEntity(): void
    {
        $reportingMethod = new ReportingMethod();
        $reportingMethod->setName('Indirect');
        $this->persist($reportingMethod);

        /** @var ReportingMethod $reportingMethod */
        $reportingMethod = $this->getRepository(ReportingMethod::class)->find(1);
        $this->assertEquals('Indirect', $reportingMethod->getName());
        $this->assertEquals(1, $reportingMethod->getId());
    }
}
