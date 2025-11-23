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
use Doctrine\Common\Collections\ArrayCollection;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\JobTitle;
use OrangeHRM\Entity\PerformanceReview;
use OrangeHRM\Entity\Reviewer;
use OrangeHRM\Entity\Subunit;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Performance
 * @group Entity
 */
class PerformanceReviewTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([
            Employee::class,
            JobTitle::class,
            Subunit::class,
            PerformanceReview::class,
            Reviewer::class
        ]);
    }

    public function testPerformanceReviewEntity(): void
    {
        $employee = new Employee();
        $employee->setEmpNumber(1);
        $employee->setFirstName('Devi');
        $this->persist($employee);

        $jobTitle = new JobTitle();
        $jobTitle->setId(1);
        $jobTitle->setJobTitleName('Test Job');
        $this->persist($jobTitle);

        $subunit = new Subunit();
        $subunit->setId(1);
        $subunit->setName('Test Unit');
        $this->persist($subunit);

        $date = new DateTime('06/27/2022');

        $performanceReview = new PerformanceReview();
        $performanceReview->setId(1);
        $performanceReview->setStatusId(PerformanceReview::STATUS_INACTIVE);
        $performanceReview->setReviewPeriodStart($date);
        $performanceReview->setReviewPeriodEnd($date);
        $performanceReview->setDueDate($date);
        $performanceReview->setCompletedDate($date);
        $performanceReview->setActivatedDate($date);
        $performanceReview->setFinalComment('Test final');
        $performanceReview->setFinalRate(32.5);
        $performanceReview->setJobTitle($jobTitle);
        $performanceReview->setSubunit($subunit);
        $performanceReview->setEmployee($employee);
        $this->persist($performanceReview);

        $reviewer = new Reviewer();
        $reviewer->setId(1);
        $reviewer->setEmployee($employee);
        $reviewer->setReview($performanceReview);
        $this->persist($reviewer);

        $performanceReview->setReviewers(new ArrayCollection([$reviewer]));
        $this->persist($performanceReview);

        /** @var PerformanceReview $result */
        $result = $this->getRepository(PerformanceReview::class)->find(1);
        $this->assertEquals(1, $result->getId());
        $this->assertEquals(PerformanceReview::STATUS_INACTIVE, $result->getStatusId());
        $this->assertEquals('Inactive', $result->getDecorator()->getStatusName());
        $this->assertEquals($date, $result->getReviewPeriodStart());
        $this->assertEquals($date, $result->getReviewPeriodEnd());
        $this->assertEquals($date, $result->getDueDate());
        $this->assertEquals($date, $result->getCompletedDate());
        $this->assertEquals($date, $result->getActivatedDate());
        $this->assertEquals('Test final', $result->getFinalComment());
        $this->assertEquals(32.5, $result->getFinalRate());
        $this->assertEquals($jobTitle, $result->getJobTitle());
        $this->assertEquals($subunit, $result->getSubunit());
        $this->assertEquals($employee, $result->getEmployee());
        $this->assertEquals($reviewer, $result->getReviewers()[0]);

        $performanceReview->getDecorator()->setJobTitleById(1);
        $this->persist($performanceReview);

        /** @var PerformanceReview $result */
        $result = $this->getRepository(PerformanceReview::class)->find(1);
        $this->assertEquals($jobTitle, $result->getJobTitle());

        $performanceReview->setStatusId(null);
        $this->persist($performanceReview);

        /** @var PerformanceReview $result */
        $result = $this->getRepository(PerformanceReview::class)->find(1);
        $this->assertEmpty($result->getDecorator()->getStatusName());
    }
}
