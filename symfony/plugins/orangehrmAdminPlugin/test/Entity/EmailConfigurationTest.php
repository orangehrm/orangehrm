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

use OrangeHRM\Entity\EmailConfiguration;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group @Admin
 * @group @Entity
 */
class EmailConfigurationTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateTables([EmailConfiguration::class]);
    }

    public function testEmailConfigurationEntity(): void
    {
        $emailConfiguration = new EmailConfiguration();
        $emailConfiguration->setMailType("smtp");
        $emailConfiguration->setSentAs("test@orangehrm.com");
        $emailConfiguration->setSmtpHost("smtp.gmail.com");
        $emailConfiguration->setSmtpPort(587);
        $emailConfiguration->setSmtpUsername("testUN");
        $emailConfiguration->setSmtpPassword("testPW");
        $emailConfiguration->setSmtpAuthType("login");
        $emailConfiguration->setSmtpSecurityType("tls");
        $this->persist($emailConfiguration);

        /** @var EmailConfiguration $emailConfiguration */
        $result = $this->getRepository(EmailConfiguration::class)->find(1);
        $this->assertTrue($result instanceof EmailConfiguration);
        $this->assertEquals("smtp", $result->getMailType());
        $this->assertEquals("test@orangehrm.com", $result->getSentAs());
        $this->assertEquals("smtp.gmail.com", $result->getSmtpHost());
        $this->assertEquals(587, $result->getSmtpPort());
        $this->assertEquals("testUN", $result->getSmtpUsername());
        $this->assertEquals("testPW", $result->getSmtpPassword());
        $this->assertEquals("login", $result->getSmtpAuthType());
        $this->assertEquals("tls", $result->getSmtpSecurityType());
    }
}
