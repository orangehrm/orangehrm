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

namespace OrangeHRM\Tests\Admin\Dao;

use Exception;
use OrangeHRM\Admin\Dao\EmailSubscriberDao;
use OrangeHRM\Admin\Dto\EmailSubscriberSearchFilterParams;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\EmailSubscriber;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Admin
 * @group Dao
 */
class EmailSubscriberDaoTest extends TestCase
{
    /**
     * @var EmailSubscriberDao
     */
    private EmailSubscriberDao $emailSubscriberDao;

    /**
     * @var string
     */
    protected string $fixture;

    /**
     * Set up method
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->emailSubscriberDao = new EmailSubscriberDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/EmailSubscriptionDao.yaml';
        TestDataService::populate($this->fixture);
    }

    public function testGetEmailSubscribersList(): void
    {
        $emailSubscriberSearchFilterParams = new EmailSubscriberSearchFilterParams();
        $result = $this->emailSubscriberDao->getEmailSubscribersByEmailSubscriptionId(1, $emailSubscriberSearchFilterParams);
        $this->assertCount(1, $result);
        $this->assertTrue($result[0] instanceof EmailSubscriber);
    }

    public function testGetEmailSubscriberById(): void
    {
        $emailSubscriber = $this->emailSubscriberDao->getEmailSubscriberById(1, 1);
        $this->assertEquals('rajitha', $emailSubscriber->getName());
        $this->assertEquals('rajitha@ohrm.com', $emailSubscriber->getEmail());
        $this->assertEquals('Leave Applications', $emailSubscriber->getEmailNotification()->getName());
        $this->assertTrue($emailSubscriber instanceof EmailSubscriber);
    }

    public function testSaveEmailSubscriber(): void
    {
        $emailSubscriber = new EmailSubscriber();
        $emailSubscription = $this->emailSubscriberDao->getEmailSubscriptionById(1);
        $emailSubscriber->setName("kavi");
        $emailSubscriber->setEmail("kavi@ohrm.com");
        $emailSubscriber->setEmailNotification($emailSubscription);
        $result = $this->emailSubscriberDao->saveEmailSubscriber($emailSubscriber);

        $this->assertTrue($result instanceof EmailSubscriber);
        $this->assertEquals('kavi', $emailSubscriber->getName());
        $this->assertEquals('kavi@ohrm.com', $emailSubscriber->getEmail());
        $this->assertEquals('Leave Applications', $emailSubscriber->getEmailNotification()->getName());
    }

    public function testEditEmailSubscriber(): void
    {
        $emailSubscriber = $this->emailSubscriberDao->getEmailSubscriberById(1, 1);
        $emailSubscription = $this->emailSubscriberDao->getEmailSubscriptionById(2);
        $emailSubscriber->setName("rajitha Kumara");
        $emailSubscriber->setEmail("rajitha@ohrm.com");
        $emailSubscriber->setEmailNotification($emailSubscription);
        $result = $this->emailSubscriberDao->saveEmailSubscriber($emailSubscriber);

        $this->assertTrue($result instanceof EmailSubscriber);
        $this->assertEquals('rajitha Kumara', $emailSubscriber->getName());
        $this->assertEquals('rajitha@ohrm.com', $emailSubscriber->getEmail());
        $this->assertEquals('Leave Assignments', $emailSubscriber->getEmailNotification()->getName());
    }

    public function testDeleteSubscriber(): void
    {
        $toTobedeletedIds = [1, 2];
        $result = $this->emailSubscriberDao->deleteEmailSubscribersByIds($toTobedeletedIds);
        $this->assertEquals(2, $result);
    }

    public function testIsSubscriberEmailUnique(): void
    {
        //Check non existent email
        $result = $this->emailSubscriberDao->isSubscriberEmailUnique("chenuka@ohrm.com", 1);
        $this->assertTrue($result);

        //Check existing email in a subscription
        $result = $this->emailSubscriberDao->isSubscriberEmailUnique("devi@ohrm.com", 3);
        $this->assertFalse($result);

        //Check existing email in a different subscription
        $result = $this->emailSubscriberDao->isSubscriberEmailUnique("sharuka@ohrm.com", 1);
        $this->assertTrue($result);

        //Check existing email in same subscription with ignore id set
        $result = $this->emailSubscriberDao->isSubscriberEmailUnique("devi@ohrm.com", 3, 3);
        $this->assertTrue($result);
    }
}
