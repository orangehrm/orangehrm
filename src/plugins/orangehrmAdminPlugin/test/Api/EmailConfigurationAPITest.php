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

namespace OrangeHRM\Tests\Admin\Api;

use OrangeHRM\Admin\Api\EmailConfigurationAPI;
use OrangeHRM\Admin\Dao\EmailConfigurationDao;
use OrangeHRM\Admin\Service\EmailConfigurationService;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Entity\EmailConfiguration;
use OrangeHRM\Tests\Util\EndpointTestCase;
use OrangeHRM\Tests\Util\MockObject;

/**
 * @group Pim
 * @group APIv2
 */
class EmailConfigurationAPITest extends EndpointTestCase
{
    public function testGetEmailConfigurationService(): void
    {
        $api = new EmailConfigurationAPI($this->getRequest());
        $this->assertTrue($api->getEmailConfigurationService() instanceof EmailConfigurationService);
    }

    public function testGetOne(): void
    {
        $emailConfigurationDao = $this->getMockBuilder(EmailConfigurationDao::class)
            ->onlyMethods(['getEmailConfiguration'])
            ->getMock();

        $emailConfiguration = new EmailConfiguration();
        $emailConfiguration->setId(1);
        $emailConfiguration->setMailType("smtp");
        $emailConfiguration->setSentAs("test@orangehrm.com");
        $emailConfiguration->setSmtpHost("smtp.gmail.com");
        $emailConfiguration->setSmtpPort(587);
        $emailConfiguration->setSmtpUsername("testUN");
        $emailConfiguration->setSmtpPassword("testPW");
        $emailConfiguration->setSmtpAuthType("login");
        $emailConfiguration->setSmtpSecurityType("tls");

        $emailConfigurationDao->expects($this->exactly(1))
            ->method('getEmailConfiguration')
            ->will($this->returnValue($emailConfiguration));

        $emailConfigurationService = $this->getMockBuilder(EmailConfigurationService::class)
            ->onlyMethods(['getEmailConfigurationDao'])
            ->getMock();

        $emailConfigurationService->expects($this->exactly(1))
            ->method('getEmailConfigurationDao')
            ->willReturn($emailConfigurationDao);

        /** @var MockObject&EmailConfigurationAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmailConfigurationAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                ]
            ]
        )->onlyMethods(['getEmailConfigurationService'])
            ->getMock();
        $api->expects($this->once())
            ->method('getEmailConfigurationService')
            ->will($this->returnValue($emailConfigurationService));

        $result = $api->getOne();
        $this->assertEquals(
            [
                "mailType" => "smtp",
                "sentAs" => "test@orangehrm.com",
                "smtpHost" => "smtp.gmail.com",
                "smtpPort" => 587,
                "smtpUsername" => "testUN",
                "smtpAuthType" => "login",
                "smtpSecurityType" => "tls"
            ],
            $result->normalize()
        );
    }

    public function testGetValidationRuleForGetOne(): void
    {
        $api = new EmailConfigurationAPI($this->getRequest());
        $rules = $api->getValidationRuleForGetOne();
        $this->assertTrue(
            $this->validate(
                [CommonParams::PARAMETER_ID => 1],
                $rules
            )
        );
    }

    public function testUpdate()
    {
        $emailConfigurationDao = $this->getMockBuilder(EmailConfigurationDao::class)
            ->onlyMethods(['saveEmailConfiguration', 'getEmailConfiguration'])
            ->getMock();

        $emailConfiguration = new EmailConfiguration();
        $emailConfiguration->setMailType("smtp");
        $emailConfiguration->setSentAs("test@orangehrm.com");
        $emailConfiguration->setSmtpHost("smtp.gmail.com");
        $emailConfiguration->setSmtpPort(587);
        $emailConfiguration->setSmtpUsername("testUN");
        $emailConfiguration->setSmtpPassword("testPW");
        $emailConfiguration->setSmtpAuthType("login");
        $emailConfiguration->setSmtpSecurityType("tls");

        $emailConfigurationDao->expects($this->exactly(1))
            ->method('getEmailConfiguration')
            ->willReturn($emailConfiguration);

        $emailConfigurationDao->expects($this->exactly(1))
            ->method('saveEmailConfiguration')
            ->will(
                $this->returnCallback(
                    function (EmailConfiguration $emailConfiguration) {
                        return $emailConfiguration;
                    }
                )
            );

        $emailConfigurationService = $this->getMockBuilder(EmailConfigurationService::class)
            ->onlyMethods(['getEmailConfigurationDao', 'sendTestMail'])
            ->getMock();

        $emailConfigurationService->expects($this->exactly(2))
            ->method('getEmailConfigurationDao')
            ->willReturn($emailConfigurationDao);

        $emailConfigurationService->expects($this->exactly(1))
            ->method('sendTestMail')
            ->with('test1@orangehrm.com')
            ->willReturn(true);

        /** @var MockObject&EmailConfigurationAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmailConfigurationAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    EmailConfigurationAPI::PARAMETER_MAIL_TYPE => 'smtp',
                    EmailConfigurationAPI::PARAMETER_SENT_AS => 'test@orangehrm.com',
                    EmailConfigurationAPI::PARAMETER_SMTP_HOST => 'smtp.gmail.com',
                    EmailConfigurationAPI::PARAMETER_SMTP_PORT => 587,
                    EmailConfigurationAPI::PARAMETER_SMTP_USERNAME => 'testUN',
                    EmailConfigurationAPI::PARAMETER_SMTP_PASSWORD => 'testPW',
                    EmailConfigurationAPI::PARAMETER_SMTP_AUTH_TYPE => 'login',
                    EmailConfigurationAPI::PARAMETER_SMTP_SECURITY_TYPE => 'tls',
                    EmailConfigurationAPI::PARAMETER_TEST_EMAIL_ADDRESS => 'test1@orangehrm.com'
                ]
            ]
        )->onlyMethods(['getEmailConfigurationService'])
            ->getMock();
        $api->expects($this->exactly(3))
            ->method('getEmailConfigurationService')
            ->will($this->returnValue($emailConfigurationService));

        $result = $api->update();
        $this->assertEquals(
            [
                "mailType" => "smtp",
                "sentAs" => "test@orangehrm.com",
                "smtpHost" => "smtp.gmail.com",
                "smtpPort" => 587,
                "smtpUsername" => "testUN",
                "smtpAuthType" => "login",
                "smtpSecurityType" => "tls"
            ],
            $result->normalize()
        );
    }

    public function testGetValidationRuleForUpdate(): void
    {
        $api = new EmailConfigurationAPI($this->getRequest());
        $rules = $api->getValidationRuleForUpdate();
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_ID => 1,
                    EmailConfigurationAPI::PARAMETER_MAIL_TYPE => 'smtp',
                    EmailConfigurationAPI::PARAMETER_SENT_AS => 'test@orangehrm.com',
                    EmailConfigurationAPI::PARAMETER_SMTP_HOST => 'smtp.gmail.com',
                    EmailConfigurationAPI::PARAMETER_SMTP_PORT => 587,
                    EmailConfigurationAPI::PARAMETER_SMTP_USERNAME => 'testUN',
                    EmailConfigurationAPI::PARAMETER_SMTP_PASSWORD => 'testPW',
                    EmailConfigurationAPI::PARAMETER_SMTP_AUTH_TYPE => 'login',
                    EmailConfigurationAPI::PARAMETER_SMTP_SECURITY_TYPE => 'tls',
                    EmailConfigurationAPI::PARAMETER_TEST_EMAIL_ADDRESS => 'test1@orangehrm.com'
                ],
                $rules
            )
        );
    }

    public function testDelete(): void
    {
        $api = new EmailConfigurationAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->delete();
    }

    public function testGetValidationRuleForDelete(): void
    {
        $api = new EmailConfigurationAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForDelete();
    }
}
