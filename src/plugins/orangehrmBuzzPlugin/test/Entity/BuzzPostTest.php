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
use OrangeHRM\Entity\Employee;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Buzz
 * @group Entity
 */
class BuzzPostTest extends EntityTestCase
{
    protected function setUp(): void
    {
        $fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmBuzzPlugin/test/fixtures/Employee.yaml';
        TestDataService::populate($fixture);
        TestDataService::truncateSpecificTables([BuzzPost::class]);
    }

    public function testEntity(): void
    {
        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->method('getNow')
            ->willReturn(new DateTime('2022-11-01 09:20'), new DateTime('2022-11-02 13:20'));
        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => $dateTimeHelper]);

        $post = new BuzzPost();
        $post->setEmployee($this->getReference(Employee::class, 1));
        $post->setText(
            "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum."
        );
        $post->setCreatedAtUtc();
        $post->setUpdatedAtUtc();
        $this->persist($post);

        $this->assertEquals(1, $post->getId());
        $this->assertEquals('Odis', $post->getEmployee()->getFirstName());
        $this->assertEquals('Adalwin', $post->getEmployee()->getLastName());
        $this->assertEquals(574, strlen($post->getText()));
        $this->assertEquals('2022-11-01', $post->getCreatedAtUtc()->format('Y-m-d'));
        $this->assertEquals('09:20:00', $post->getCreatedAtUtc()->format('H:i:s'));
        $this->assertEquals('2022-11-02', $post->getUpdatedAtUtc()->format('Y-m-d'));
        $this->assertEquals('13:20:00', $post->getUpdatedAtUtc()->format('H:i:s'));
    }
}
