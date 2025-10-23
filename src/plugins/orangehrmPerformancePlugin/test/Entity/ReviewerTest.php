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

use DateTime;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\PerformanceReview;
use OrangeHRM\Entity\Reviewer;
use OrangeHRM\Entity\ReviewerGroup;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Performance
 * @group Entity
 */
class ReviewerTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([
            Employee::class,
            Reviewer::class,
            ReviewerGroup::class,
            PerformanceReview::class
        ]);
    }

    public function testReviewerEntity(): void
    {
        $employee = new Employee();
        $employee->setEmpNumber(1);
        $employee->setFirstName('Devi');
        $this->persist($employee);

        $reviewerGroup = new ReviewerGroup();
        $reviewerGroup->setId(1);
        $reviewerGroup->setName(ReviewerGroup::REVIEWER_GROUP_SUPERVISOR);
        $this->persist($reviewerGroup);

        $performanceReview = new PerformanceReview();
        $performanceReview->setId(1);
        $performanceReview->setEmployee($employee);
        $this->persist($performanceReview);

        $date = new DateTime('06/27/2022');

        $reviewer = new Reviewer();
        $reviewer->setId(1);
        $reviewer->setStatus(Reviewer::STATUS_ACTIVATED);
        $reviewer->setCompletedDate($date);
        $reviewer->setComment('Test comment');
        $reviewer->setGroup($reviewerGroup);
        $reviewer->setReview($performanceReview);
        $reviewer->setEmployee($employee);
        $this->persist($reviewer);

        /** @var Reviewer $result */
        $result = $this->getRepository(Reviewer::class)->find(1);
        $this->assertEquals(1, $result->getId());
        $this->assertEquals(Reviewer::STATUS_ACTIVATED, $result->getStatus());
        $this->assertEquals($date, $result->getCompletedDate());
        $this->assertEquals('Test comment', $result->getComment());
        $this->assertEquals($reviewerGroup, $result->getGroup());
        $this->assertEquals($performanceReview, $result->getReview());
        $this->assertEquals($employee, $result->getEmployee());
    }
}
