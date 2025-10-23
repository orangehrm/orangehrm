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
use OrangeHRM\Entity\PerformanceReview;
use OrangeHRM\Entity\Reviewer;
use OrangeHRM\Entity\ReviewerRating;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Performance
 * @group Entity
 */
class ReviewerRatingTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([
            PerformanceReview::class,
            Reviewer::class,
            Kpi::class,
            JobTitle::class,
            ReviewerRating::class
        ]);
    }

    public function testReviewerRatingEntity(): void
    {
        $jobTitle = new JobTitle();
        $jobTitle->setId(1);
        $jobTitle->setJobTitleName('Test job');
        $this->persist($jobTitle);

        $kpi = new Kpi();
        $kpi->setId(1);
        $kpi->setTitle('Test KPI');
        $kpi->setJobTitle($jobTitle);
        $this->persist($kpi);

        $performanceReview = new PerformanceReview();
        $performanceReview->setId(1);
        $this->persist($performanceReview);

        $reviewer = new Reviewer();
        $reviewer->setId(1);
        $reviewer->setReview($performanceReview);
        $this->persist($reviewer);

        $reviewerRating = new ReviewerRating();
        $reviewerRating->setId(1);
        $reviewerRating->setRating('24.3');
        $reviewerRating->setComment('Test comment');
        $reviewerRating->setPerformanceReview($performanceReview);
        $reviewerRating->setReviewer($reviewer);
        $reviewerRating->setKpi($kpi);
        $this->persist($reviewerRating);

        /** @var ReviewerRating $result */
        $result = $this->getRepository(ReviewerRating::class)->find(1);
        $this->assertEquals(1, $result->getId());
        $this->assertEquals('24.3', $result->getRating());
        $this->assertEquals('Test comment', $result->getComment());
        $this->assertEquals($performanceReview, $result->getPerformanceReview());
        $this->assertEquals($reviewer, $result->getReviewer());
        $this->assertEquals($kpi, $result->getKpi());
    }
}
