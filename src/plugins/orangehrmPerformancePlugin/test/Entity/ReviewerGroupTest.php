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

use OrangeHRM\Entity\ReviewerGroup;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Performance
 * @group Entity
 */
class ReviewerGroupTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([ReviewerGroup::class]);
    }

    public function testReviewerGroupEntity(): void
    {
        $reviewerGroup = new ReviewerGroup();
        $reviewerGroup->setId(1);
        $reviewerGroup->setName(ReviewerGroup::REVIEWER_GROUP_SUPERVISOR);
        $reviewerGroup->setPiority(1);
        $this->persist($reviewerGroup);

        /** @var ReviewerGroup $result */
        $result = $this->getRepository(ReviewerGroup::class)->find(1);
        $this->assertEquals(1, $result->getId());
        $this->assertEquals(ReviewerGroup::REVIEWER_GROUP_SUPERVISOR, $result->getName());
        $this->assertEquals(1, $result->getPiority());
    }
}
