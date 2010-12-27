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
*/

abstract class orangehrmMailer {

    protected $mailer;
    protected $transport;
    protected $message;
    protected $logPath;

    public function getMailer() {
        return $this->mailer;
    }

    public function setMailer($mailer) {
        $this->mailer = $mailer;
    }

    public function getTransport() {
        return $this->transport;
    }

    public function setTransport($transport) {
        $this->transport = $transport;
    }

    public function getMessage() {
        return $this->message;
    }

    public function setMessage($message) {
        $this->message = $message;
    }

    public function getLogPath() {
        return $this->logPath;
    }

    public function setLogPath($logPath) {
        $this->logPath = $logPath;
    }

    public function  __construct() {

        $orangehrmMailTransport = new orangehrmMailTransport();
        $this->transport = $orangehrmMailTransport->getTransport();
        $this->mailer = empty($this->transport)?null:Swift_Mailer::newInstance($this->transport);
        $this->message = Swift_Message::newInstance();
        $this->logPath = ROOT_PATH . '/lib/logs/notification_mails.log';

    }

    public function getSystemFrom() {

        $emailConfig = new EmailConfiguration();
        return array($emailConfig->getSentAs() => 'OrangeHRM');

    }

    public function logResult($type = '', $logMessage = '') {

        if (file_exists($this->logPath) && !is_writable($this->logPath)) {
            throw new Exception("Email Notifications : Log file is not writable");
        }

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

        file_put_contents($this->logPath, $message, FILE_APPEND);

    }


}

