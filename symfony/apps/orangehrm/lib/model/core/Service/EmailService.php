<?php
/**
 *
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
 *
 * @copyright 2006 OrangeHRM Inc., http://www.orangehrm.com
 */

require_once sfConfig::get('sf_root_dir').'/lib/vendor/symfony/lib/vendor/swiftmailer/swift_required.php';

class EmailService extends BaseService {

    const SMTP_SECURITY_NONE = 'none';
    const SMTP_SECURITY_TLS = 'tls';
    const SMTP_SECURITY_SSL = 'ssl';

    const SMTP_AUTH_NONE = 'none';
    const SMTP_AUTH_LOGIN = 'login';
    
    const FALLBACK_TEMPLATE_LOCALE = 'en_US';

    private $emailConfig;
    private $configSet = false;

    private $messageSubject;
    private $messageFrom;
    private $messageTo;
    private $messageBody;
    private $messageCc;
    private $messageBcc;
    
    protected $emailDao;
    
    protected $logger;
    
    protected $configService;
    
    protected $mailer;

    /**
     * to get confuguration service
     * @return <type>
     */
    public function getConfigService() {
        if (is_null($this->configService)) {
            $this->configService = new ConfigService();
            $this->configService->setConfigDao(new ConfigDao());
        }
        return $this->configService;
    }

    /**
     *  to set configuration service
     * @param ConfigService $configService
     */
    public function setConfigService(ConfigService $configService) {
        $this->configService = $configService;
    }
    
    /**
     * Get EmailDao
     * @return EmailDao
     */
    public function getEmailDao() {
        if (is_null($this->emailDao)) {
            $this->emailDao = new EmailDao();
        }
        return $this->emailDao;
    }
    
    public function setEmailDao($emailDao) {
        $this->emailDao = $emailDao;
    }

    /**
     *
     * @return EmailConfiguration 
     */
    public function getEmailConfig() {
        return $this->emailConfig;
    }

    public function setMessageSubject($messageSubject) {
        $this->messageSubject = $messageSubject;
    }
    public function setMessageFrom($messageFrom) {
        $this->messageFrom = $messageFrom;
    }
    public function setMessageTo($messageTo) {
        $this->messageTo = $messageTo;
    }
    public function setMessageBody($messageBody) {
        $this->messageBody = $messageBody;
    }
    public function setMessageCc($messageCc) {
        $this->messageCc = $messageCc;
    }
    public function setMessageBcc($messageBcc) {
        $this->messageBcc = $messageBcc;
    }

    public function __construct() {
        $emailConfigurationService = new EmailConfigurationService();
        $this->emailConfig = $emailConfigurationService->getEmailConfiguration();

        if ($this->emailConfig->getMailType() == 'smtp' ||
            $this->emailConfig->getMailType() == 'sendmail') {
            $this->configSet = true;
        }
        
        $this->logger = Logger::getLogger('core.email');

    }

    protected function getMailer($recreate = false) {
        
        if (empty($this->mailer) || $recreate) {
            $orangehrmMailTransport = new orangehrmMailTransport();
            $transport = $orangehrmMailTransport->getTransport();
            
            if (!empty($transport)) {
                $mailer = Swift_Mailer::newInstance($transport);
            } else {
                $this->logger->warn('Email configuration settings not available');
            }                        
        }
        
        return $mailer;
    }
    private function _getMailerDeprecated() {

        $mailer = null;

        if ($this->configSet) {

            switch ($this->emailConfig->getMailType()) {

                case 'smtp':

                    $transport = Swift_SmtpTransport::newInstance(
                                   $this->emailConfig->getSmtpHost(),
                                   $this->emailConfig->getSmtpPort());

                    if ($this->emailConfig->getSmtpAuthType() == self::SMTP_AUTH_LOGIN) {
                        $transport->setUsername($this->emailConfig->getSmtpUsername());
                        $transport->setPassword($this->emailConfig->getSmtpPassword());
                    }

                    if ($this->emailConfig->getSmtpSecurityType() == self::SMTP_SECURITY_SSL ||
                        $this->emailConfig->getSmtpSecurityType() == self::SMTP_SECURITY_TLS) {
                        $transport->setEncryption($this->emailConfig->getSmtpSecurityType());
                    }

                    $mailer = Swift_Mailer::newInstance($transport);

                    break;

                case 'sendmail':

                    $transport = Swift_SendmailTransport::newInstance($this->emailConfig->getSendmailPath());
                    $mailer = Swift_Mailer::newInstance($transport);

                    break;

            }

        }

        return $mailer;

    }

    private function _getMessage() {

        if (empty($this->messageSubject)) {
            throw new Exception("Email subjet is not set");
        }

        if (empty($this->messageFrom)) {
            $this->_validateEmailAddress($this->emailConfig->getSentAs());
            $this->messageFrom = array($this->emailConfig->getSentAs() => 'OrangeHRM');
        }

        if (empty($this->messageTo)) {
            throw new Exception("Email 'to' is not set");
        }

        if (empty($this->messageBody)) {
            throw new Exception("Email body is not set");
        }

        $message = Swift_Message::newInstance();
        $message->setSubject($this->messageSubject);
        $message->setFrom($this->messageFrom);
        $message->setTo($this->messageTo);
        $message->setBody($this->messageBody);

        if (!empty($this->messageCc)) {
            $message->setCc($this->messageCc);
        }

        if (!empty($this->messageBcc)) {
            $message->setBcc($this->messageBcc);
        }

        return $message;

    }

    public function sendEmail() {

        if ($this->configSet) {

            try {

                $mailer = $this->_getMailerDeprecated();
                $message = $this->_getMessage();

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

                $logMessage .= ' using '.$this->emailConfig->getMailType();

                $this->_logResult('Success', $logMessage);

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

                $logMessage .= ' using '.$this->emailConfig->getMailType();

                $logMessage .= '. Reason: '.$e->getMessage();


                $this->_logResult('Failure', $logMessage);

                return false;

            }

        } else {

            $this->_logResult('Failure', 'Email configuration is not set.');

            return false;

        }

    }

    public function sendTestEmail($toEmail) {

        $mailType = $this->emailConfig->getMailType();

        if ($mailType == 'smtp') {

            $subject = "SMTP Configuration Test Email";

            $body = "This email confirms that SMTP details set in OrangeHRM are ";
            $body .= "correct. You received this email since your email address ";
            $body .= "was entered to test email in configuration screen.";
            
        } elseif ($mailType == 'sendmail') {

            $subject = "Sendmail Configuration Test Email";

            $body = "This email confirms that Sendmail details set in OrangeHRM ";
            $body .= "are correct. You received this email since your email ";
            $body .= "address was entered to test email in configuration screen.";

        }

        $this->_validateEmailAddress($toEmail);

        $this->messageSubject = $subject;
        $this->messageTo = array($toEmail);
        $this->messageBody = $body;

        return $this->sendEmail();

    }

    private function _validateEmailAddress($emailAddress) {

        if (!preg_match("/^[^@]*@[^@]*\.[^@]*$/", $emailAddress)) {
            throw new Exception("Invalid email address");
        }

    }

    private function _logResult($type = '', $logMessage = '') {

        $message = '========== Message Begins ==========';
        $message .= "\r\n\n";
        $message .= 'Time : '.date("F j, Y, g:i a");
        $message .= "\r\n";
        $message .= 'Message Type : '.$type;
        $message .= "\r\n";
        $message .= 'Message : '.$logMessage;
        $message .= "\r\n\n";
        $message .= '========== Message Ends ==========';
        $message .= "\r\n\n";

        $this->logger->info($message);
    }
    
    /**
     * Get the best matching email template for the recipient and performer
     * 
     * @param Email $email The email object
     * @param string $recipientRole Recipient role
     * @param string $performerRole Performer role
     * @return EmailTemplate Email Template or Null if no match
     */
    public function getEmailTemplateBestMatch(Email $email, $recipientRole, $performerRole) {
        
        $template = NULL;
        
        $defaultLocale = $this->getConfigService()->getAdminLocalizationDefaultLanguage(); 
        $localesToTry = array($defaultLocale);
        
        if ($defaultLocale != self::FALLBACK_TEMPLATE_LOCALE) {
            $localesToTry[] = self::FALLBACK_TEMPLATE_LOCALE;
        }
        
        /* Look through the locals in preferred order */
        foreach ($localesToTry as $locale) {
            $templates = $this->getEmailDao()->getEmailTemplateMatches($email->getName(), $locale, $recipientRole, $performerRole);
            if (count($templates) > 0) {
                break;
            } else {
                $this->logger->debug("No Email templates found for $locale");
            }
        }        
            
        if (count($templates) == 1) {
            $template = $templates[0];
        } else if (count($templates) > 0) {
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
    
    public function sendEmailNotification($emailName, $recipientRole, $eventData, $performerRole = null) {

        $this->logger->debug("Sending email : $emailName for role $recipientRole and performer Role: $performerRole");
        
        $mailer = $this->getMailer();
        if (empty($mailer)) {
            $this->logger->warn("Mail configuration not available. Skipping email sending");
        }
        
        $email = $this->getEmailDao()->getEmailByName($emailName);
                
        if (!empty($email)) {

            $template = $this->getEmailTemplateBestMatch($email, $recipientRole, $performerRole);
            
            if (!empty($template)) {                
                    
                $subject = $this->readFile($template->getSubject());
                $body = $this->readFile($template->getBody());
                
                $processors = $email->getEmailProcessor();
                $allRecipients = array();
                
                $this->logger->debug('email processor count : ' . count($processors));
                
                foreach ($processors as $processor) {                                        
                    $class = $processor->getClassName();
                    
                    $this->logger->debug('email processor class: ' . $class);
                    
                    $processorObj = new $class();
                    $recipients = $processorObj->getRecipients($emailName, $recipientRole, $eventData);
                    $allRecipients = array_merge($allRecipients, $recipients);                            
                }
                
                $this->logger->debug('recipient count : ' . count($allRecipients));
                
                foreach ($allRecipients as $recipient) {
                    
                    $to = null;

                    if ($recipient instanceof Employee) {
                        $to = $recipient->getEmpWorkEmail();
                    } else if ($recipient instanceof EmailSubscriber) {
                        $to = $recipient->getEmail();
                    }
                                                
                    if (!empty($to)) {
                        $data = $eventData;
                        foreach ($processors as $processor) {
                            $class = $processor->getClassName();
                            $processorObj = new $class();
                            $data['recipient'] = $recipient;
                            $data = $processorObj->getReplacements($data);                            
                        }


                        $emailBody = $this->replaceContent($body, $data);
                        $emailSubject = $this->replaceContent($subject, $data);


                        $message = Swift_Message::newInstance();
          

                        try {
                            $message->setTo($to);
                            $message->setFrom(array($this->emailConfig->getSentAs() => 'OrangeHRM'));

                            $message->setSubject($emailSubject);
                            $message->setBody($emailBody);

                            $mailer->send($message);

                            $logMessage = "Leave application email was sent to $to";
                            $this->_logResult('Success', $logMessage);
                        } catch (Exception $e) {

                            $logMessage = "Couldn't send leave application email to $to";
                            $logMessage .= '. Reason: ' . $e->getMessage();
                            $this->_logResult('Failure', $logMessage);
                        }                    
                    }
                }
                
                                    
            } else {
                $this->logger->error("No email template found for: $emailName, role: $recipientRole");
            }
        } else {
            $this->logger->error("No email found for: $emailName, and role: $recipientRole");
        }
    }
    
    public function sendEmailNotifications($emailName, $recipientRoles, $eventData, $performerRole = null) {
        
        $mailer = $this->getMailer();
        if (!empty($mailer)) {        
            foreach ($recipientRoles as $role) {
                $this->sendEmailNotification($emailName, $role, $eventData, $performerRole);
            }
        }
    }
    
    protected function readFile($path) {
        $path = sfConfig::get('sf_root_dir')."/plugins/" . $path;
        if (!is_readable($path)) {
            throw new Exception("File is not readable: " . $path);
        }

        return trim(file_get_contents($path));        
    }

    protected function replaceContent($template, $replacements, $wrapper = '%') {

        $keys = array_keys($replacements);

        foreach ($keys as $value) {
            $needls[] = $wrapper . $value . $wrapper;
        }

        return str_replace($needls, $replacements, $template);

    }
}
