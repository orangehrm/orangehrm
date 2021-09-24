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

namespace OrangeHRM\Core\Utility;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mailer\Transport\SendmailTransport;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

class MailTransport extends AbstractTransport
{
    /**
     * @var AbstractTransport|SendmailTransport|EsmtpTransport
     */
    private AbstractTransport $mailTransport;

    /**
     * MailTransport constructor.
     * @param string $host
     * @param int|null $port
     */
    public function __construct(string $host = 'localhost', int $port = null)
    {
        if ($port == null) { //sendmail
            $this->mailTransport = new SendmailTransport();
        } else { //smtp
            $this->mailTransport = new EsmtpTransport($host, $port);
        }
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username)
    {
        $this->mailTransport->setUsername($username);
    }

    /**
     * @param string $username
     */
    public function setPassword(string $username)
    {
        $this->mailTransport->setPassword($username);
    }

    /**
     * @param string $smtpSecurityType
     */
    public function setEncryption(string $smtpSecurityType)
    {
        // Automatically set by port
    }

    /**
     * @param SentMessage $message
     * @throws TransportExceptionInterface
     */
    protected function doSend(SentMessage $message): void
    {
        $this->mailTransport->doSend($message);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $this->mailTransport->__toString();
    }
}
