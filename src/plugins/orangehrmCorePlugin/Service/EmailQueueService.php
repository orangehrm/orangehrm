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

namespace OrangeHRM\Core\Service;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\PessimisticLockException;
use OrangeHRM\Core\Dao\EmailQueueDao;
use OrangeHRM\Core\Traits\LoggerTrait;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\Mail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface as MailerException;

class EmailQueueService
{
    use DateTimeHelperTrait;
    use EntityManagerHelperTrait;
    use LoggerTrait;

    private ?EmailQueueDao $emailQueueDao = null;

    private ?EmailService $emailService = null;

    /**
     * @return EmailQueueDao
     */
    public function getEmailQueueDao(): EmailQueueDao
    {
        if (!($this->emailQueueDao instanceof EmailQueueDao)) {
            $this->emailQueueDao = new EmailQueueDao();
        }
        return $this->emailQueueDao;
    }

    /**
     * @return EmailService
     */
    public function getEmailService(): EmailService
    {
        if (!($this->emailService instanceof EmailService)) {
            $this->emailService = new EmailService();
        }
        return $this->emailService;
    }

    /**
     * @param string $subject
     * @param string $body
     * @param array $toList
     * @param string $contentType
     * @param array $ccList
     * @param array $bccList
     * @return Mail
     */
    public function addToQueue(
        string $subject,
        string $body,
        array $toList = [],
        string $contentType = Mail::CONTENT_TYPE_TEXT_HTML,
        array $ccList = [],
        array $bccList = []
    ): Mail {
        $mail = new Mail();
        $mail->setSubject($subject);
        $mail->setBody($body);
        $mail->setToList($toList);
        $mail->setContentType($contentType);
        $mail->setCcList($ccList);
        $mail->setBccList($bccList);
        return $this->getEmailQueueDao()->saveEmail($mail);
    }

    /**
     * @param int $mailId
     */
    public function sendSingleMail(int $mailId): void
    {
        $this->beginTransaction();
        try {
            $mail = $this->getEntityManager()->find(Mail::class, $mailId, LockMode::PESSIMISTIC_WRITE);
            // This method only handle if mail in pending status.
            if ($mail->getStatus() !== Mail::STATUS_PENDING) {
                return;
            }

            $this->changeMailStatus($mail, Mail::STATUS_STARTED);
        } catch (PessimisticLockException | OptimisticLockException $e) {
            $this->getLogger()->error($e->getMessage());
            $this->getLogger()->error($e->getTraceAsString());
            return;
        }

        $this->getEmailService()->setMessageSubject($mail->getSubject());
        $this->getEmailService()->setMessageBody($mail->getBody());
        $this->getEmailService()->setMessageTo($mail->getToList());
        $this->getEmailService()->setMessageCc($mail->getCcList());
        $this->getEmailService()->setMessageBcc($mail->getBccList());

        try {
            $result = $this->getEmailService()->sendEmail();
            if ($result) {
                $this->changeMailStatus($mail, Mail::STATUS_SENT);
            } else {
                $this->changeMailStatus($mail, Mail::STATUS_PENDING);
            }
        } catch (MailerException $e) {
            $this->changeMailStatus($mail, Mail::STATUS_FAILED);
            $this->getLogger()->error($e->getMessage());
            $this->getLogger()->error($e->getTraceAsString());
        } finally {
            $this->commitTransaction();
        }
    }

    public function resetEmailService(): void
    {
        if ($this->getEmailService() instanceof EmailService) {
            $this->getEmailService()->setMessageSubject('');
            $this->getEmailService()->setMessageBody('');
            $this->getEmailService()->setMessageTo([]);
            $this->getEmailService()->setMessageCc([]);
            $this->getEmailService()->setMessageBcc([]);
        }
    }

    /**
     * @param Mail $mail
     * @param string $status
     * @return Mail
     */
    public function changeMailStatus(Mail $mail, string $status): Mail
    {
        $mail->setStatus($status);
        if ($status == Mail::STATUS_SENT) {
            $mail->setSentAt($this->getDateTimeHelper()->getNow());
        }
        return $this->getEmailQueueDao()->saveEmail($mail);
    }

    public function sendAllPendingMails(): void
    {
        $mailIds = $this->getEmailQueueDao()->getAllPendingMailIds();
        foreach ($mailIds as $mailId) {
            $this->resetEmailService();
            $this->sendSingleMail($mailId);
        }
    }
}
