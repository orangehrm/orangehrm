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

namespace OrangeHRM\Tests\Admin\Entity;

use OrangeHRM\Entity\EmailNotification;
use OrangeHRM\Entity\EmailSubscriber;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group @Admin
 * @group @Entity
 */
class EmailSubscriberTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateTables([EmailSubscriber::class]);
        TestDataService::truncateTables([EmailNotification::class]);
    }

    public function testCountryEntity(): void
    {
        $emailSubscriber = new EmailSubscriber();
        $emailNotification = new EmailNotification();
        $emailNotification->setName('Leave Assignments');
        $emailNotification->setEnabled(true);
        $this->persist($emailNotification);

        $emailSubscriber->setName('Subs');
        $emailSubscriber->setEmail('Subs@subs.com');
        $emailSubscriber->setEmailNotification($emailNotification);
        $this->persist($emailSubscriber);

        /** @var EmailSubscriber $emailSubscriber */
        $emailSubscriber = $this->getRepository(EmailSubscriber::class)->find(1);
        $this->assertEquals('Subs', $emailSubscriber->getName());
        $this->assertEquals('Subs@subs.com', $emailSubscriber->getEmail());
        $this->assertEquals(1, $emailSubscriber->getEmailNotification()->getId());
    }
}
