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

use OrangeHRM\Admin\Service\EmailConfigurationService;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Dao\EmailDao;
use OrangeHRM\Core\Exception\CoreServiceException;
use OrangeHRM\Core\Exception\ServiceException as Exception;
use OrangeHRM\Core\Mail\AbstractRecipient;
use OrangeHRM\Core\Mail\MailProcessor;
use OrangeHRM\Core\Mail\TemplateHelper;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\CacheTrait;
use OrangeHRM\Core\Traits\ClassHelperTrait;
use OrangeHRM\Core\Traits\LoggerTrait;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\Core\Utility\Mailer;
use OrangeHRM\Core\Utility\MailMessage;
use OrangeHRM\Core\Utility\MailTransport;
use OrangeHRM\Entity\EmailConfiguration;
use OrangeHRM\Entity\EmailTemplate;
use OrangeHRM\Framework\Event\Event;

class EmailService
{
    use LoggerTrait;
    use ConfigServiceTrait;
    use ClassHelperTrait;
    use AuthUserTrait;
    use CacheTrait;

    public const SMTP_SECURITY_NONE = 'none';
    public const SMTP_SECURITY_TLS = 'tls';
    public const SMTP_SECURITY_SSL = 'ssl';

    public const SMTP_AUTH_NONE = 'none';
    public const SMTP_AUTH_LOGIN = 'login';

    public const FALLBACK_TEMPLATE_LOCALE = 'en_US';

    /**
     * @var string|null
     */
    private ?string $sendmailPath = null;

    /**
     * @var EmailConfiguration|null
     */
    private ?EmailConfiguration $emailConfig = null;

    /**
     * @var EmailDao|null
     */
    private ?EmailDao $emailDao = null;

    /**
     * @var EmailQueueService|null
     */
    private ?EmailQueueService $emailQueueService = null;

    /**
     * @var bool
     */
    private bool $configSet = false;

    /**
     * @var string
     */
    private string $messageSubject;

    /**
     * @var array
     */
    private array $messageFrom;

    /**
     * @var array
     */
    private array $messageTo;

    /**
     * @var string
     */
    private string $messageBody;

    /**
     * @var array
     */
    private array $messageCc;

    /**
     * @var array
     */
    private array $messageBcc;

    /**
     * @var Mailer
     */
    protected Mailer $mailer;

    /**
     * @var EmailConfigurationService|null
     */
    private ?EmailConfigurationService $emailConfigurationService = null;

    public function __construct()
    {
        $this->loadConfiguration();
    }

    /**
     * @return EmailDao
     */
    public function getEmailDao(): EmailDao
    {
        if (!$this->emailDao instanceof EmailDao) {
            $this->emailDao = new EmailDao();
        }
        return $this->emailDao;
    }

    /**
     * @return EmailQueueService
     */
    public function getEmailQueueService(): EmailQueueService
    {
        if (!$this->emailQueueService instanceof EmailQueueService) {
            $this->emailQueueService = new EmailQueueService();
        }
        return $this->emailQueueService;
    }

    /**
     * @return string|null
     */
    public function getSendmailPath(): ?string
    {
        return $this->sendmailPath;
    }

    /**
     * @param string|null $sendmailPath
     */
    public function setSendmailPath(?string $sendmailPath): void
    {
        $this->sendmailPath = $sendmailPath;
    }

    /**
     * @return bool
     */
    public function isConfigSet(): bool
    {
        return $this->configSet;
    }

    /**
     * @param bool $configSet
     */
    public function setConfigSet(bool $configSet): void
    {
        $this->configSet = $configSet;
    }

    /**
     *
     * @return EmailConfiguration|null
     */
    public function getEmailConfig(): ?EmailConfiguration
    {
        return $this->emailConfig;
    }

    /**
     * @param $messageSubject
     */
    public function setMessageSubject($messageSubject)
    {
        $this->messageSubject = $messageSubject;
    }

    /**
     * @param $messageFrom
     */
    public function setMessageFrom($messageFrom)
    {
        $this->messageFrom = $messageFrom;
    }

    /**
     * @param $messageTo
     */
    public function setMessageTo($messageTo)
    {
        $this->messageTo = $messageTo;
    }

    /**
     * @param $messageBody
     */
    public function setMessageBody($messageBody)
    {
        $this->messageBody = $messageBody;
    }

    /**
     * @param $messageCc
     */
    public function setMessageCc($messageCc)
    {
        $this->messageCc = $messageCc;
    }

    /**
     * @param $messageBcc
     */
    public function setMessageBcc($messageBcc)
    {
        $this->messageBcc = $messageBcc;
    }

    /**
     * @throws CoreServiceException
     */
    protected function loadConfiguration(): void
    {
        $emailConfig = $this->getEmailConfigurationService()->getEmailConfigurationDao()->getEmailConfiguration();
        $this->setEmailConfig($emailConfig);
        $this->setSendmailPath($this->getConfigService()->getSendmailPath());

        if ($emailConfig instanceof EmailConfiguration && in_array(
            $this->getEmailConfig()->getMailType(),
            [MailTransport::SCHEME_SENDMAIL, MailTransport::SCHEME_SMTP, MailTransport::SCHEME_SECURE_SMTP]
        )) {
            $this->setConfigSet(true);
        }
    }

    /**
     * @param EmailConfiguration|null $emailConfiguration
     */
    public function setEmailConfig(?EmailConfiguration $emailConfiguration): void
    {
        $this->emailConfig = $emailConfiguration;
    }

    /**
     * @return EmailConfigurationService
     */
    public function getEmailConfigurationService(): EmailConfigurationService
    {
        if (is_null($this->emailConfigurationService)) {
            $this->emailConfigurationService = new EmailConfigurationService();
        }
        return $this->emailConfigurationService;
    }

    /**
     * @param EmailConfigurationService $emailConfigurationService
     */
    public function setEmailConfigurationService(EmailConfigurationService $emailConfigurationService): void
    {
        $this->emailConfigurationService = $emailConfigurationService;
    }

    /**
     * @param bool $recreate
     * @return Mailer
     */
    public function getMailer(bool $recreate = false): Mailer
    {
        if (empty($this->mailer) || $recreate) {
            $transport = $this->getTransport();
            if (!empty($transport)) {
                $this->mailer = new Mailer($transport);
            } else {
                $this->getLogger()->warning('Email configuration settings not available');
            }
        }

        return $this->mailer;
    }

    /**
     * @return MailTransport
     */
    public function getTransport(): ?MailTransport
    {
        $transport = null;
        if ($this->isConfigSet()) {
            if (in_array(
                $this->getEmailConfig()->getMailType(),
                [MailTransport::SCHEME_SMTP, MailTransport::SCHEME_SECURE_SMTP]
            )) {
                $transport = new MailTransport(
                    $this->getEmailConfig()->getMailType(),
                    $this->getEmailConfig()->getSmtpHost(),
                    $this->getEmailConfig()->getSmtpPort()
                );
                if ($this->getEmailConfig()->getSmtpAuthType() == self::SMTP_AUTH_LOGIN) {
                    $transport->setUsername($this->getEmailConfig()->getSmtpUsername());
                    $transport->setPassword($this->getEmailConfig()->getSmtpPassword());
                }

                if ($this->getEmailConfig()->getSmtpSecurityType() == self::SMTP_SECURITY_SSL ||
                    $this->getEmailConfig()->getSmtpSecurityType() == self::SMTP_SECURITY_TLS) {
                    $transport->setEncryption($this->getEmailConfig()->getSmtpSecurityType());
                }
                $transport->loadTransport();
            } elseif ($this->getEmailConfig()->getMailType() == MailTransport::SCHEME_SENDMAIL) {
                $transport = new MailTransport(
                    $this->getEmailConfig()->getMailType(),
                    $this->getConfigService()->getSendmailPath()
                );
            }
        }

        return $transport;
    }

    /**
     * @return MailMessage
     * @throws Exception
     */
    public function getMessage()
    {
        if (empty($this->messageSubject)) {
            throw new Exception("Email subject is not set");
        }

        if (empty($this->messageFrom)) {
            $this->validateEmailAddress($this->getEmailConfig()->getSentAs());
            $this->messageFrom = [$this->getEmailConfig()->getSentAs() => "OrangeHRM"];
        }

        if (empty($this->messageTo)) {
            throw new Exception("Email 'to' is not set");
        }

        if (empty($this->messageBody)) {
            throw new Exception("Email body is not set");
        }

        // TODO
        $message = new MailMessage();
        $message->setSubject($this->messageSubject);
        $message->setFrom($this->messageFrom);
        $message->setTo($this->messageTo);
        $message->setMailBody($this->messageBody);

        if (!empty($this->messageCc)) {
            $message->setCc($this->messageCc);
        }

        if (!empty($this->messageBcc)) {
            $message->setBcc($this->messageBcc);
        }
        return $message;
    }

    /**
     * @return bool
     */
    public function sendEmail(): bool
    {
        if ($this->isConfigSet()) {
            try {
                $mailer = $this->getMailer();
                $message = $this->getMessage();
                $mailer->send($message);

                $logMessage = 'Emails was sent to ';
                $logMessage .= implode(', ', $this->messageTo);

                if (!empty($this->messageCc)) {
                    $logMessage .= ' and CCed to ';
                    $logMessage .= implode(', ', $this->messageCc);
                }

                if (!empty($this->messageBcc)) {
                    $logMessage .= ' and BCCed to ';
                    $logMessage .= implode(', ', $this->messageBcc);
                }

                $logMessage .= ' using ' . $this->getEmailConfig()->getMailType();

                $this->logResult('Success', $logMessage);

                return true;
            } catch (Exception $e) {
                $logMessage = 'Sending email failed to ';
                $logMessage .= implode(', ', $this->messageTo);

                if (!empty($this->messageCc)) {
                    $logMessage .= ' and CCing to ';
                    $logMessage .= implode(', ', $this->messageCc);
                }

                if (!empty($this->messageBcc)) {
                    $logMessage .= ' and BCCing to ';
                    $logMessage .= implode(', ', $this->messageBcc);
                }

                $logMessage .= ' using ' . $this->getEmailConfig()->getMailType();

                $logMessage .= '. Reason: ' . $e->getMessage();

                $this->logResult('Failure', $logMessage);

                return false;
            }
        } else {
            $this->logResult('Failure', 'Email configuration is not set.');
            return false;
        }
    }

    /**
     * @param string $toEmail
     * @return bool
     */
    public function sendTestEmail(string $toEmail): bool
    {
        $mailType = $this->getEmailConfig()->getMailType();

        $type = 'SMTP';
        if ($mailType == MailTransport::SCHEME_SECURE_SMTP) {
            $type = 'SMTPS';
        } elseif ($mailType == MailTransport::SCHEME_SENDMAIL) {
            $type = 'Sendmail';
        }
        $subject = "$type Configuration Test Email";

        $body = "This email confirms that $type details set in OrangeHRM ";
        $body .= 'are correct. You received this email since your email ';
        $body .= 'address was entered to test email in configuration screen.';

        $this->validateEmailAddress($toEmail);

        $this->messageSubject = $subject;
        $this->messageTo = [$toEmail];
        $this->messageBody = $body;

        return $this->sendEmail();
    }

    /**
     * @param $emailAddress
     * @throws Exception
     */
    private function validateEmailAddress($emailAddress)
    {
        if (!preg_match("/^[^@]*@[^@]*\.[^@]*$/", $emailAddress)) {
            throw new Exception("Invalid email address");
        }
    }

    /**
     * @param string $type
     * @param string $logMessage
     */
    private function logResult(string $type = '', string $logMessage = ''): void
    {
        $message = '========== Message Begins ==========';
        $message .= "\r\n\n";
        $message .= 'Time : ' . date('F j, Y, g:i a');
        $message .= "\r\n";
        $message .= 'Message Type : ' . $type;
        $message .= "\r\n";
        $message .= 'Message : ' . $logMessage;
        $message .= "\r\n\n";
        $message .= '========== Message Ends ==========';
        $message .= "\r\n\n";

        $this->getLogger()->info($message);
    }

    /**
     * @param string $emailName
     * @param array $recipientRoles
     * @param string $performerRole
     * @param Event $eventData
     */
    public function queueEmailNotifications(
        string $emailName,
        array $recipientRoles,
        string $performerRole,
        Event $eventData
    ): void {
        if ($this->isConfigSet()) {
            $cacheItem = $this->getCache()->getItem('core.send_email');
            if (!$cacheItem->isHit()) {
                $cacheItem->expiresAfter(600);
                $cacheItem->set(true);
                $this->getCache()->save($cacheItem);
            } elseif (!$cacheItem->get()) {
                $cacheItem->set(true);
                $this->getCache()->save($cacheItem);
            }
            foreach ($recipientRoles as $role) {
                $this->queueEmailNotification($emailName, $role, $performerRole, $eventData);
            }
        }
    }

    /**
     * Get the best matching email template for the recipient and performer
     *
     * @param string $emailName
     * @param string $recipientRole Recipient role
     * @param string $performerRole Performer role
     * @return EmailTemplate|null Email Template or Null if no match
     */
    public function getEmailTemplateBestMatch(
        string $emailName,
        string $recipientRole,
        string $performerRole
    ): ?EmailTemplate {
        $template = null;

        $defaultLocale = $this->getConfigService()->getAdminLocalizationDefaultLanguage();
        $localesToTry = [$defaultLocale];

        if ($defaultLocale != self::FALLBACK_TEMPLATE_LOCALE) {
            $localesToTry[] = self::FALLBACK_TEMPLATE_LOCALE;
        }

        /* Look through the locals in preferred order */
        foreach ($localesToTry as $locale) {
            $templates = $this->getEmailDao()->getEmailTemplateMatches(
                $emailName,
                $locale,
                $recipientRole,
                $performerRole
            );
            if (count($templates) > 0) {
                break;
            } // else: No Email templates found for $locale
        }

        if (count($templates) == 1) {
            $template = $templates[0];
        } elseif (count($templates) > 0) {
            $maxWeight = -1;
            foreach ($templates as $t) {
                $weight = 0;
                if ($t->getPerformerRole() == $performerRole) {
                    $weight += 1;
                }
                if ($t->getRecipientRole() == $recipientRole) {
                    $weight += 2;
                }

                if ($weight > $maxWeight) {
                    $maxWeight = $weight;
                    $template = $t;
                }
            }
        }

        return $template;
    }

    /**
     * @param string $emailName
     * @param string $recipientRole
     * @param string $performerRole
     * @param Event $eventData
     */
    public function queueEmailNotification(
        string $emailName,
        string $recipientRole,
        string $performerRole,
        Event $eventData
    ): void {
        $email = $this->getEmailDao()->getEmailByName($emailName);

        if (is_null($email)) {
            $this->getLogger()->error("No email found for: $emailName, and role: $recipientRole");
            return;
        }
        $template = $this->getEmailTemplateBestMatch($emailName, $recipientRole, $performerRole);
        if (is_null($template)) {
            $this->getLogger()->error("No email template found for: $emailName, role: $recipientRole");
            return;
        }

        $subject = $this->readFile($template->getSubject());
        $body = $this->readFile($template->getBody());
        $templateHelper = new TemplateHelper();

        $processors = $email->getEmailProcessors();
        $allRecipients = [];
        foreach ($processors as $emailProcessor) {
            $class = $emailProcessor->getClassName();
            if (!$this->getClassHelper()->classExists($class)) {
                throw new \Exception("`$class` class not found");
            }

            $processor = new $class();
            if ($processor instanceof MailProcessor) {
                $recipients = $processor->getRecipients($emailName, $recipientRole, $performerRole, $eventData);

                foreach ($recipients as $recipient) {
                    if (!$recipient instanceof AbstractRecipient) {
                        continue;
                    }
                    $to = $recipient->getEmailAddress();
                    if (isset($allRecipients[$to])) {
                        continue;
                    }
                    $allRecipients[$to] = $recipient;

                    $replacements = $processor->getReplacements(
                        $emailName,
                        $recipient,
                        $recipientRole,
                        $performerRole,
                        $eventData
                    );
                    $emailBody = $templateHelper->renderTemplate($body, $replacements);
                    $emailSubject = $templateHelper->renderTemplate($subject, $replacements);

                    $this->getEmailQueueService()->addToQueue($emailSubject, $emailBody, [$to]);
                }
            }
        }
    }

    /**
     * @param string $path
     * @return string
     */
    protected function readFile(string $path): string
    {
        $path = Config::get(Config::PLUGINS_DIR) . $path;
        $absolutePath = realpath($path);
        if (!$absolutePath || !is_readable($absolutePath)) {
            throw new \Exception('File is not readable: ' . $path);
        }

        return file_get_contents($path);
    }
}
