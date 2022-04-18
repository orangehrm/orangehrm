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

namespace OrangeHRM\Tests\Core\Service;

use OrangeHRM\Config\Config;
use OrangeHRM\Core\Dao\EmailQueueDao;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Core\Service\EmailQueueService;
use OrangeHRM\Core\Service\EmailService;
use OrangeHRM\Entity\Mail;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Core
 * @group Service
 */
class EmailQueueServiceTest extends KernelTestCase
{
    private EmailQueueService $emailQueueService;

    protected function setUp(): void
    {
        $this->emailQueueService = new EmailQueueService();
        $fixture = Config::get(Config::PLUGINS_DIR) .
            '/orangehrmCorePlugin/test/fixtures/EmailQueueDao.yml';
        TestDataService::populate($fixture);
    }

    public function testGetEmailQueueDao()
    {
        $this->assertTrue($this->emailQueueService->getEmailQueueDao() instanceof EmailQueueDao);
    }

    public function testGetEmailService()
    {
        $emailQueueService = $this->getMockBuilder(EmailQueueService::class)
            ->onlyMethods(['getEmailService'])
            ->getMock();
        $emailQueueService->expects($this->once())
            ->method('getEmailService');
        $emailQueueService->getEmailService();
    }

    public function testAddToQueue()
    {
        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService()]);

        $result = $this->emailQueueService->addToQueue(
            'test7 subject',
            'test7 body',
            ['test7@orangehrm.com', 'test8@orangehrm.com'],
            Mail::CONTENT_TYPE_TEXT_PLAIN,
            ['test9@orangehrm.com'],
            ['test10@orangehrm.com']
        );
        $this->assertTrue($result instanceof Mail);
        $this->assertEquals('test7 subject', $result->getSubject());
        $this->assertEquals('test7 body', $result->getBody());
        $this->assertEquals(['test7@orangehrm.com', 'test8@orangehrm.com'], $result->getToList());
        $this->assertEquals(['test9@orangehrm.com'], $result->getCcList());
        $this->assertEquals(['test10@orangehrm.com'], $result->getBccList());
    }

    public function testSendSingleMail()
    {
        $emailService = $this->getMockBuilder(EmailService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                ['setMessageSubject', 'setMessageBody', 'setMessageTo', 'setMessageCc', 'setMessageBcc', 'sendEmail']
            )
            ->getMock();
        $emailService->expects($this->once())
            ->method('setMessageSubject');
        $emailService->expects($this->once())
            ->method('setMessageBody');
        $emailService->expects($this->once())
            ->method('setMessageTo');
        $emailService->expects($this->once())
            ->method('setMessageCc');
        $emailService->expects($this->once())
            ->method('setMessageBcc');
        $emailService->expects($this->once())
            ->method('sendEmail');
        $emailQueueService = $this->getMockBuilder(EmailQueueService::class)
            ->onlyMethods(['getEmailService'])
            ->getMock();
        $emailQueueService->expects($this->exactly(6))
            ->method('getEmailService')
            ->willReturn($emailService);
        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService()]);

        $emailQueueService->sendSingleMail(1);
    }

    public function testResetEmailService()
    {
        $emailService = $this->getMockBuilder(EmailService::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();
        $emailService->setMessageSubject('test Subject');
        $emailService->setMessageBody('test Body');
        $emailService->setMessageTo(['test@orangehrm.com']);
        $emailService->setMessageCc(['test@orangehrm.com']);
        $emailService->setMessageBcc(['test@orangehrm.com']);

        $emailQueueService = $this->getMockBuilder(EmailQueueService::class)
            ->onlyMethods(['getEmailService'])
            ->getMock();
        $emailQueueService->expects($this->exactly(6))
            ->method('getEmailService')
            ->willReturn($emailService);
        $emailQueueService->resetEmailService();
    }

    public function testChangeMailStatus()
    {
        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService()]);
        $mail = new Mail();
        $mail->setSubject('test7 subject');
        $mail->setBody('test7 body');
        $mail->setToList(['test7@orangehrm.com', 'test8@orangehrm.com']);
        $mail->setCcList(['test9@orangehrm.com']);
        $mail->setBccList(['test10@orangehrm.com']);

        $emailQueueService = new EmailQueueService();
        $result = $emailQueueService->changeMailStatus($mail, Mail::STATUS_STARTED);
        $this->assertEquals(Mail::STATUS_STARTED, $result->getStatus());

        $result = $emailQueueService->changeMailStatus($mail, Mail::STATUS_SENT);
        $this->assertEquals(Mail::STATUS_SENT, $result->getStatus());
        $this->assertNotNull($result->getSentAt());
    }

    public function testSendAllPendingMails()
    {
        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService()]);
        $emailQueueDao = $this->getMockBuilder(EmailQueueDao::class)
            ->onlyMethods(['getAllPendingMailIds'])
            ->getMock();
        $emailQueueDao->expects($this->once())
            ->method('getAllPendingMailIds')
            ->willReturn([1, 2, 3]);

        $emailQueueService = $this->getMockBuilder(EmailQueueService::class)
            ->onlyMethods(['getEmailQueueDao', 'resetEmailService', 'sendSingleMail'])
            ->getMock();
        $emailQueueService->expects($this->once())
            ->method('getEmailQueueDao')
            ->willReturn($emailQueueDao);
        $emailQueueService->expects($this->exactly(3))
            ->method('resetEmailService');
        $emailQueueService->expects($this->exactly(3))
            ->method('sendSingleMail');
        $emailQueueService->sendAllPendingMails();
    }
}
