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

use OrangeHRM\Admin\Service\EmailConfigurationService;
use OrangeHRM\Core\Exception\CoreServiceException;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Core\Exception\ServiceException as Exception;
use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Core\Utility\Mailer;
use OrangeHRM\Core\Utility\MailMessage;
use OrangeHRM\Core\Utility\MailTransport;
use OrangeHRM\Entity\EmailConfiguration;
use OrangeHRM\Framework\Logger\Logger;
use OrangeHRM\Framework\Services;

class EmailService
{
    use ServiceContainerTrait;

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
     * @var Logger
     */
    protected Logger $logger;

    /**
     * @var ConfigService|null
     */
    protected ?ConfigService $configService = null;

    /**
     * @var Mailer
     */
    protected Mailer $mailer;

    /**
     * @var MailTransport|null
     */
    private ?MailTransport $transport = null;

    /**
     * @var EmailConfigurationService|null
     */
    private ?EmailConfigurationService $emailConfigurationService = null;

    /**
     * @return Logger
     */
    protected function getLogger(): Logger
    {
        return $this->getContainer()->get(Services::LOGGER);
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
     * to get configuration service
     * @return ConfigService
     */
    public function getConfigService()
    {
        if (is_null($this->configService)) {
            $this->configService = new ConfigService();
        }
        return $this->configService;
    }

    /**
     *  to set configuration service
     * @param ConfigService $configService
     */
    public function setConfigService(ConfigService $configService)
    {
        $this->configService = $configService;
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
     * @throws DaoException
     */
    public function loadConfiguration(): void
    {
        $emailConfig = $this->getEmailConfigurationService()->getEmailConfigurationDao()->getEmailConfiguration();
        $this->setEmailConfig($emailConfig);
        $this->setSendmailPath($this->getConfigService()->getSendmailPath());

        if ($emailConfig instanceof EmailConfiguration && in_array(
            $this->getEmailConfig()->getMailType(),
            [MailTransport::SCHEME_SENDMAIL, MailTransport::SCHEME_SMTP, MailTransport::SCHEME_SECURE_SMTP]
        )){
            $this->setConfigSet(true);
        }
        $this->logger = $this->getLogger();
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
     * @param false $recreate
     * @return Mailer
     * @throws CoreServiceException
     */
    public function getMailer($recreate = false)
    {
        if (empty($this->mailer) || $recreate) {
            $transport = $this->getTransport();
            if (!empty($transport)) {
                $this->mailer = new Mailer($transport);
            } else {
                $this->logger->warning('Email configuration settings not available');
            }
        }

        return $this->mailer;
    }

    /**
     * @return MailTransport
     * @throws CoreServiceException
     */
    public function getTransport()
    {
        $transport = null;
        if ($this->isConfigSet()) {
            if (in_array(
                $this->getEmailConfig()->getMailType(),
                [MailTransport::SCHEME_SMTP, MailTransport::SCHEME_SECURE_SMTP]
            )){
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
                $this->transport = $transport;
            } else {
                if ($this->getEmailConfig()->getMailType() == MailTransport::SCHEME_SENDMAIL) {
                    $this->transport = new MailTransport(
                        $this->getEmailConfig()->getMailType(),
                        $this->getConfigService()->getSendmailPath()
                    );
                }
            }
        }

        return $this->transport;
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
            $this->validateEmailAddress($this->emailConfig->getSentAs());
            $this->messageFrom = array($this->emailConfig->getSentAs() => "OrangeHRM");
        }

        if (empty($this->messageTo)) {
            throw new Exception("Email 'to' is not set");
        }

        if (empty($this->messageBody)) {
            throw new Exception("Email body is not set");
        }

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
    public function sendEmail()
    {
        if ($this->isConfigSet()) {
            try {
                $mailer = $this->getMailer();
                $message = $this->getMessage();
                $result = $mailer->send($message);

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

                $logMessage .= ' using ' . $this->emailConfig->getMailType();

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

                $logMessage .= ' using ' . $this->emailConfig->getMailType();

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
     * @param $toEmail
     * @return bool
     * @throws Exception
     */
    public function sendTestEmail($toEmail)
    {
        $mailType = $this->getEmailConfig()->getMailType();

        if ($mailType == MailTransport::SCHEME_SMTP || $mailType == MailTransport::SCHEME_SECURE_SMTP) {
            $subject = "SMTP Configuration Test Email";

            $body = "This email confirms that SMTP details set in OrangeHRM are ";
            $body .= "correct. You received this email since your email address ";
            $body .= "was entered to test email in configuration screen.";
        } elseif ($mailType == MailTransport::SCHEME_SENDMAIL) {
            $subject = "Sendmail Configuration Test Email";

            $body = "This email confirms that Sendmail details set in OrangeHRM ";
            $body .= "are correct. You received this email since your email ";
            $body .= "address was entered to test email in configuration screen.";
        }

        $this->validateEmailAddress($toEmail);

        $this->messageSubject = $subject;
        $this->messageTo = array($toEmail);
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
    private function logResult($type = '', $logMessage = '')
    {
        $message = '========== Message Begins ==========';
        $message .= "\r\n\n";
        $message .= 'Time : ' . date("F j, Y, g:i a");
        $message .= "\r\n";
        $message .= 'Message Type : ' . $type;
        $message .= "\r\n";
        $message .= 'Message : ' . $logMessage;
        $message .= "\r\n\n";
        $message .= '========== Message Ends ==========';
        $message .= "\r\n\n";

        $this->logger->info($message);
    }
}
