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

namespace OrangeHRM\Core\Utility;

use OrangeHRM\Core\Service\EmailService;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\TransportInterface;

class MailTransport extends AbstractTransport
{
    /**
     * @var Transport | TransportInterface
     */
    private $mailTransport;

    /**
     * @var string|null
     */
    private ?string $scheme = 'smtp';

    /**
     * @var string|null
     */
    private ?string $host = null;

    /**
     * @var string|null
     */
    private ?string $user = null;

    /**
     * @var string|null
     */
    private ?string $password = null;

    /**
     * @var int|null
     */
    private ?int $port = null;

    /**
     * @var array|null
     */
    private ?array $options = [];

    public const SCHEME_SMTP = 'smtp';
    public const SCHEME_SECURE_SMTP = 'smtps';
    public const SCHEME_SENDMAIL = 'sendmail';


    /**
     * MailTransport constructor.
     * @param string $scheme
     * @param string $host
     * @param int|null $port
     */
    public function __construct(string $scheme = self::SCHEME_SMTP, string $host = 'localhost', int $port = null)
    {
        if ($scheme == self::SCHEME_SENDMAIL) {
            $dsn = 'sendmail://default?command='.urlencode($host);
            $this->mailTransport = Transport::fromDsn($dsn);
        } elseif ($scheme == self::SCHEME_SMTP || $scheme == self::SCHEME_SECURE_SMTP) {
            $this->scheme = $scheme;
            $this->host = $host;
            $this->port = $port;
        }
    }

    public function loadTransport()
    {
        $this->mailTransport = new Transport(iterator_to_array(Transport::getDefaultFactories()));
        $dsn = new Dsn($this->scheme, $this->host, $this->user, $this->password, $this->port, $this->options);
        $this->mailTransport = $this->mailTransport->fromDsnObject($dsn);
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username)
    {
        $this->user = $username;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    /**
     * @param string $smtpSecurityType
     */
    public function setEncryption(string $smtpSecurityType)
    {
        if ($smtpSecurityType != EmailService::SMTP_SECURITY_TLS) {
            $this->options = ['verify_peer' => 0];
        }
    }

    /**
     * @param SentMessage $message
     */
    protected function doSend(SentMessage $message): void
    {
        $this->mailTransport->doSend($message);
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->mailTransport->__toString();
    }
}
