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
use OrangeHRM\Entity\BuzzLikeOnShare;
use OrangeHRM\Entity\BuzzShare;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Buzz
 * @group Entity
 */
class BuzzLikeOnShareTest extends EntityTestCase
{
    protected function setUp(): void
    {
        $fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmBuzzPlugin/test/fixtures/BuzzLikeOnShare.yaml';
        TestDataService::populate($fixture);
        TestDataService::truncateSpecificTables([BuzzLikeOnShare::class]);
    }

    public function testEntity(): void
    {
        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->method('getNow')
            ->willReturn(new DateTime('2022-11-03 19:20'));
        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => $dateTimeHelper]);

        $buzzLikeOnShare = new BuzzLikeOnShare();
        $buzzLikeOnShare->setEmployee($this->getReference(Employee::class, 1));
        $buzzLikeOnShare->setShare($this->getReference(BuzzShare::class, 1));
        $buzzLikeOnShare->setLikedAtUtc();
        $this->persist($buzzLikeOnShare);

        $this->assertEquals(1, $buzzLikeOnShare->getId());
        $this->assertEquals(1, $buzzLikeOnShare->getEmployee()->getEmployeeId());
        $this->assertEquals(1, $buzzLikeOnShare->getShare()->getId());
        $this->assertEquals('2022-11-03', $buzzLikeOnShare->getLikedAtUtc()->format('Y-m-d'));
        $this->assertEquals('19:20:00', $buzzLikeOnShare->getLikedAtUtc()->format('H:i:s'));

        $buzzLikeOnShare->setId(2);
        $this->assertEquals(2, $buzzLikeOnShare->getId());
    }
}
