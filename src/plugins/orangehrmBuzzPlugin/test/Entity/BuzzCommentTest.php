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

namespace OrangeHRM\Tests\Buzz\Entity;

use DateTime;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\BuzzComment;
use OrangeHRM\Entity\BuzzShare;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

class BuzzCommentTest extends EntityTestCase
{
    protected function setUp(): void
    {
        $fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmBuzzPlugin/test/fixtures/BuzzComment.yaml';
        TestDataService::populate($fixture);
        TestDataService::truncateSpecificTables([BuzzComment::class]);
    }

    public function testEntity(): void
    {
        $buzzComment = new BuzzComment();
        $buzzComment->setShare($this->getReference(BuzzShare::class, 1));
        $buzzComment->setEmployee($this->getReference(Employee::class,1));
        $buzzComment->setNumOfLikes(2);
        $buzzComment->setText('this is comment for post 01');
        $buzzComment->setCreatedAt(new DateTime('2022-11-01 09:20'));
        $buzzComment->setUpdatedAt(new DateTime('2022-11-04 19:20'));
        $this->persist($buzzComment);

        $this->assertEquals(1, $buzzComment->getEmployee()->getEmployeeId());
        $this->assertEquals(1, $buzzComment->getShare()->getId());
        $this->assertEquals('this is comment for post 01', $buzzComment->getText());
        $this->assertEquals('2022-11-01', $buzzComment->getCreatedAt()->format('Y-m-d'));
        $this->assertEquals('09:20:00', $buzzComment->getCreatedAt()->format('H:i:s'));
        $this->assertEquals('2022-11-04', $buzzComment->getUpdatedAt()->format('Y-m-d'));
        $this->assertEquals('19:20:00', $buzzComment->getUpdatedAt()->format('H:i:s'));
    }
}
