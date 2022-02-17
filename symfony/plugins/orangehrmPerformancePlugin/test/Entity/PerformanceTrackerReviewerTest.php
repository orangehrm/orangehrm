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

use DateTime;
use OrangeHRM\Entity\PerformanceTracker;
use OrangeHRM\Entity\PerformanceTrackerReviewer;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

class PerformanceTrackerReviewerTest extends EntityTestCase
{
    /**
     * @return void
     */
    public function testerformanceTrackerEntity(): void
    {
        $performanceTracker = $this->getRepository(PerformanceTracker::class)->find(1);
        $performanceTrackerReviewer = new PerformanceTrackerReviewer();
        $performanceTrackerReviewer->setPerformanceTracker($performanceTracker);
        $performanceTrackerReviewer->getDecorator()->setReviewerByEmpNumber(1);
        $performanceTrackerReviewer->setAddedDate(new DateTime('03/02/2022'));
        $this->persist($performanceTrackerReviewer);

        $result = $this->getRepository(PerformanceTrackerReviewer::class)->find(1);
        $this->assertEquals(1, $result->getPerformanceTracker()->getId());
    }

    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([PerformanceTrackerReviewer::class]);
    }
}
