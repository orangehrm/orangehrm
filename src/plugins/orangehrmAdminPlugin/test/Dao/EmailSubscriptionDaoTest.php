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

namespace OrangeHRM\Tests\Admin\Dao;

use Exception;
use OrangeHRM\Admin\Dao\EmailSubscriptionDao;
use OrangeHRM\Admin\Dto\EmailSubscriptionSearchFilterParams;
use OrangeHRM\Entity\EmailNotification;
use OrangeHRM\Config\Config;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Admin
 * @group Dao
 */
class EmailSubscriptionDaoTest extends TestCase
{
    private EmailSubscriptionDao $emailSubscriptionDao;
    protected string $fixture;

    /**
     * Set up method
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->emailSubscriptionDao = new EmailSubscriptionDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/EmailSubscriptionDao.yaml';
        TestDataService::populate($this->fixture);
    }

    public function testGetEmailSubscriptionList(): void
    {
        $emailSubscriptionSearchFilterParams = new EmailSubscriptionSearchFilterParams();
        $result = $this->emailSubscriptionDao->getEmailSubscriptions($emailSubscriptionSearchFilterParams);
        $this->assertCount(5, $result);
        $this->assertTrue($result[0] instanceof EmailNotification);
    }

    public function testEditEmailSubscriptionList(): void
    {
        $emailSubscription = $this->emailSubscriptionDao->getEmailSubscriptionById(1);
        $emailSubscription->setEnabled(true);
        $result = $this->emailSubscriptionDao->saveEmailSubscription($emailSubscription);
        $this->assertTrue($result instanceof EmailNotification);
        $this->assertEquals('Leave Applications', $emailSubscription->getName());
        $this->assertEquals(true, $emailSubscription->isEnabled());
        $this->assertEquals(1, $result->getId());
    }
}
