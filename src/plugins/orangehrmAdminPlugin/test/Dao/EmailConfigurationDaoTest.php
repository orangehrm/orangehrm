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
use OrangeHRM\Admin\Dao\EmailConfigurationDao;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\EmailConfiguration;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Admin
 * @group Dao
 */
class EmailConfigurationDaoTest extends TestCase
{
    private EmailConfigurationDao $emailConfigurationDao;
    protected string $fixture;

    /**
     * Set up method
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->emailConfigurationDao = new EmailConfigurationDao();
        $this->fixture = Config::get(
            Config::PLUGINS_DIR
        ) . '/orangehrmAdminPlugin/test/fixtures/EmailConfigurationDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetEmailConfigurationGeneralInformation(): void
    {
        $this->assertTrue($this->emailConfigurationDao->getEmailConfiguration() instanceof EmailConfiguration);
    }

    public function testSaveEmailConfigurationGeneralInformation(): void
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
        $result = $this->emailConfigurationDao->saveEmailConfiguration($emailConfiguration);
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
