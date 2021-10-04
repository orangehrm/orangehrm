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

use OrangeHRM\Core\Dao\EmailQueueDao;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Core\Service\EmailQueueService;
use OrangeHRM\Core\Service\EmailService;
use OrangeHRM\Entity\Mail;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\KernelTestCase;

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
            "test7 subject",
            "test7 body",
            ['test7@orangehrm.com', 'test8@orangehrm.com'],
            ['test9@orangehrm.com'],
            ['test10@orangehrm.com']
        );
        $this->assertTrue($result instanceof Mail);
        $this->assertEquals("test7 subject", $result->getSubject());
        $this->assertEquals("test7 body", $result->getBody());
        $this->assertEquals(['test7@orangehrm.com', 'test8@orangehrm.com'], $result->getToList());
        $this->assertEquals(['test9@orangehrm.com'], $result->getCcList());
        $this->assertEquals(['test10@orangehrm.com'], $result->getBccList());
    }

    public function testSendSingleMail()
    {
        $emailService = $this->getMockBuilder(EmailService::class)
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
        $mail = new Mail();
        $mail->setSubject("test7 subject");
        $mail->setBody("test7 body");
        $mail->setToList(['test7@orangehrm.com', 'test8@orangehrm.com']);
        $mail->setCcList(['test9@orangehrm.com']);
        $mail->setBccList(['test10@orangehrm.com']);

        $emailQueueService->sendSingleMail($mail);
    }

    public function testResetEmailService()
    {
        $emailService = new EmailService();
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
        $mail->setSubject("test7 subject");
        $mail->setBody("test7 body");
        $mail->setToList(['test7@orangehrm.com', 'test8@orangehrm.com']);
        $mail->setCcList(['test9@orangehrm.com']);
        $mail->setBccList(['test10@orangehrm.com']);

        $emailQueueService = new EmailQueueService();
        $result = $emailQueueService->changeMailStatus($mail, Mail::STATUS_IN_PROGRESS);
        $this->assertEquals(Mail::STATUS_IN_PROGRESS, $result->getStatus());

        $result = $emailQueueService->changeMailStatus($mail, Mail::STATUS_COMPLETED);
        $this->assertEquals(Mail::STATUS_COMPLETED, $result->getStatus());
        $this->assertNotNull($result->getSentAt());
    }

    public function testSendAllPendingMails()
    {
        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService()]);
        $mail1 = new Mail();
        $mail1->setId(1);
        $mail1->setSubject("test1 subject");
        $mail2 = new Mail();
        $mail2->setId(1);
        $mail2->setSubject("test1 subject");
        $mail3 = new Mail();
        $mail3->setId(1);
        $mail3->setSubject("test1 subject");
        $emailQueueDao = $this->getMockBuilder(EmailQueueDao::class)
            ->onlyMethods(['getAllPendingMails'])
            ->getMock();
        $emailQueueDao->expects($this->once())
            ->method('getAllPendingMails')
            ->willReturn([$mail1, $mail2, $mail3]);

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
