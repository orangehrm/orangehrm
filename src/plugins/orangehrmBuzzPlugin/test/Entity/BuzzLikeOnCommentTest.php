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

namespace OrangeHRM\Tests\Buzz\Entity;

use DateTime;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Entity\BuzzComment;
use OrangeHRM\Entity\BuzzLikeOnComment;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Buzz
 * @group Entity
 */
class BuzzLikeOnCommentTest extends EntityTestCase
{
    protected function setUp(): void
    {
        $fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmBuzzPlugin/test/fixtures/BuzzLikeOnComment.yaml';
        TestDataService::populate($fixture);
        TestDataService::truncateSpecificTables([BuzzLikeOnComment::class]);
    }

    public function testEntity(): void
    {
        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->method('getNow')
            ->willReturn(new DateTime('2022-11-02 13:20'));
        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => $dateTimeHelper]);

        $buzzLikeOnComment = new BuzzLikeOnComment();
        $buzzLikeOnComment->setEmployee($this->getReference(Employee::class, 1));
        $buzzLikeOnComment->setComment($this->getReference(BuzzComment::class, 1));
        $buzzLikeOnComment->setLikedAtUtc();
        $this->persist($buzzLikeOnComment);

        $this->assertEquals(1, $buzzLikeOnComment->getId());
        $this->assertEquals(1, $buzzLikeOnComment->getEmployee()->getEmployeeId());
        $this->assertEquals('this is comment 01', $buzzLikeOnComment->getComment()->getText());
        $this->assertEquals('2022-11-02', $buzzLikeOnComment->getLikedAtUtc()->format('Y-m-d'));
        $this->assertEquals('13:20:00', $buzzLikeOnComment->getLikedAtUtc()->format('H:i:s'));

        $buzzLikeOnComment->setId(2);
        $this->assertEquals(2, $buzzLikeOnComment->getId());
    }
}
