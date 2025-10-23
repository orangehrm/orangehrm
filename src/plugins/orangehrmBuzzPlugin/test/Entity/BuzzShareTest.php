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
use OrangeHRM\Entity\BuzzPost;
use OrangeHRM\Entity\BuzzShare;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Buzz
 * @group Entity
 */
class BuzzShareTest extends EntityTestCase
{
    protected function setUp(): void
    {
        $fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmBuzzPlugin/test/fixtures/BuzzShare.yaml';
        TestDataService::populate($fixture);
        TestDataService::truncateSpecificTables([BuzzShare::class]);
    }

    public function testEntity(): void
    {
        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
        ->onlyMethods(['getNow'])
        ->getMock();
        $dateTimeHelper->method('getNow')
            ->willReturn(new DateTime('2022-11-11 09:20'), new DateTime('2022-11-12 13:20'));
        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => $dateTimeHelper]);

        $share = new BuzzShare();
        $share->setEmployee($this->getReference(Employee::class, 1));
        $share->setPost($this->getReference(BuzzPost::class, 1));
        $share->setNumOfLikes(1);
        $share->setNumOfComments(2);
        $share->setCreatedAtUtc();
        $share->setUpdatedAtUtc();
        $this->persist($share);

        $this->assertEquals(1, $share->getId());
        $this->assertEquals(1, $share->getNumOfLikes());
        $this->assertEquals(2, $share->getNumOfComments());
        $this->assertEquals('this is post text 01', $share->getPost()->getText());
        $this->assertEquals('2022-11-11', $share->getCreatedAtUtc()->format('Y-m-d'));
        $this->assertEquals('09:20:00', $share->getCreatedAtUtc()->format('H:i:s'));
        $this->assertEquals('2022-11-12', $share->getUpdatedAtUtc()->format('Y-m-d'));
        $this->assertEquals('13:20:00', $share->getUpdatedAtUtc()->format('H:i:s'));

        $share->setId(2);
        $share->setText('test text');
        $this->assertEquals(2, $share->getId());
        $this->assertEquals('test text', $share->getText());
    }
}
