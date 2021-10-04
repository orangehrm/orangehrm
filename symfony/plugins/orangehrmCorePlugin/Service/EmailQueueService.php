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

namespace OrangeHRM\Core\Service;

use OrangeHRM\Core\Dao\EmailQueueDao;
use OrangeHRM\Entity\Mail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface as MailerException;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Core\Exception\CoreServiceException;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;

class EmailQueueService
{
    use DateTimeHelperTrait;

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
     * @throws DaoException
     * @throws CoreServiceException
     */
    public function getEmailService(): EmailService
    {
        if (!($this->emailService instanceof EmailService)) {
            $this->emailService = new EmailService();
            $this->getEmailService()->loadConfiguration();
        }
        return $this->emailService;
    }

    /**
     * @param string $subject
     * @param string $body
     * @param array $toList
     * @param array $ccList
     * @param array $bccList
     * @return Mail
     */
    public function addToQueue(string $subject, string $body, array $toList = [], array $ccList = [], array $bccList = []): Mail
    {
        $mail = new Mail();
        $mail->setSubject($subject);
        $mail->setBody($body);
        $mail->setToList($toList);
        $mail->setCcList($ccList);
        $mail->setBccList($bccList);
        return $this->getEmailQueueDao()->saveEmail($mail);
    }

    /**
     * @param Mail $mail
     * @throws CoreServiceException
     * @throws DaoException
     */
    public function sendSingleMail(Mail $mail): void
    {
        $this->changeMailStatus($mail, Mail::STATUS_IN_PROGRESS);
        $this->getEmailService()->setMessageSubject($mail->getSubject());
        $this->getEmailService()->setMessageBody($mail->getBody());
        $this->getEmailService()->setMessageTo($mail->getToList());
        $this->getEmailService()->setMessageCc($mail->getCcList());
        $this->getEmailService()->setMessageBcc($mail->getBccList());

        try {
            $result = $this->getEmailService()->sendEmail();
            if($result){
                $this->changeMailStatus($mail, Mail::STATUS_COMPLETED);
            } else {
                $this->changeMailStatus($mail, Mail::STATUS_PENDING);
            }
        } catch (MailerException $e){
            $this->changeMailStatus($mail, Mail::STATUS_PENDING);
        }
    }

    /**
     * @throws CoreServiceException
     * @throws DaoException
     */
    public function resetEmailService(): void
    {
        if($this->getEmailService() instanceof EmailService){
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
        if($status == Mail::STATUS_COMPLETED){
            $mail->setSentAt($this->getDateTimeHelper()->getNow());
        }
        return $this->getEmailQueueDao()->saveEmail($mail);
    }

    /**
     * @throws CoreServiceException
     * @throws DaoException
     */
    public function sendAllPendingMails(): void
    {
        $mails = $this->getEmailQueueDao()->getAllPendingMails();
        foreach ($mails as $mail){
            $this->resetEmailService();
            $this->sendSingleMail($mail);
        }
    }
}
