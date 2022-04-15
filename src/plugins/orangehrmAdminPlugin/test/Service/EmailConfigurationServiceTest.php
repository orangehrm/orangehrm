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

namespace OrangeHRM\Tests\Admin\Service;

use OrangeHRM\Admin\Dao\EmailConfigurationDao;
use OrangeHRM\Admin\Service\EmailConfigurationService;
use OrangeHRM\Core\Service\EmailService;
use OrangeHRM\Tests\Util\TestCase;

/**
 * @group Admin
 * @group Service
 */
class EmailConfigurationServiceTest extends TestCase
{
    private EmailConfigurationService $emailConfigurationService;

    protected function setUp(): void
    {
        $this->emailConfigurationService = new EmailConfigurationService();
    }

    public function testGetEmailConfigurationDao()
    {
        $this->assertTrue($this->emailConfigurationService->getEmailConfigurationDao() instanceof EmailConfigurationDao);
    }

    public function xtestGetEmailService()
    {
        // TODO
        $emailConfigurationService = $this->getMockBuilder(EmailConfigurationService::class)
            ->onlyMethods([])
            ->getMock();
        $this->assertTrue($emailConfigurationService->getEmailService() instanceof EmailService);
    }

    public function testSendTestMail(): void
    {
        $emailService = $this->getMockBuilder(EmailService::class)
            ->onlyMethods(['sendTestEmail'])
            ->disableOriginalConstructor()
            ->getMock();

        $emailService->expects($this->once())
            ->method('sendTestEmail')
            ->with('test1@orangehrm.com')
            ->willReturn(true);

        $this->emailConfigurationService->setEmailService($emailService);
        $result = $this->emailConfigurationService->sendTestMail('test1@orangehrm.com');
        $this->assertEquals(true, $result);
    }
}
