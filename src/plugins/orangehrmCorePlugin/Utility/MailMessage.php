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

use OrangeHRM\Entity\Mail;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

class MailMessage extends Email
{
    /**
     * @var string
     */
    private string $contentType;

    /**
     * @param string $contentType
     */
    public function __construct(string $contentType = Mail::CONTENT_TYPE_TEXT_HTML)
    {
        $this->contentType = $contentType;
        parent::__construct();
    }

    /**
     * @param string $subject
     * @return $this
     */
    public function setSubject(string $subject): MailMessage
    {
        return $this->subject($subject);
    }

    /**
     * @param string[] $addresses
     * @return $this
     */
    public function setFrom(array $addresses): MailMessage
    {
        $symfonyAddresses = [];
        foreach ($addresses as $address => $name) {
            $symfonyAddresses[] = new Address($address, $name);
        }

        return $this->from(...$symfonyAddresses);
    }

    /**
     * @param string[] $addresses
     * @return $this
     */
    public function setTo(array $addresses): MailMessage
    {
        return $this->to(...$addresses);
    }

    /**
     * @param string $body
     * @return $this
     */
    public function setMailBody(string $body): MailMessage
    {
        if ($this->contentType === Mail::CONTENT_TYPE_TEXT_PLAIN) {
            return $this->text($body);
        } else {
            return $this->html($body);
        }
    }

    /**
     * @param string[] $addresses
     * @return $this
     */
    public function setCc(array $addresses): MailMessage
    {
        return $this->cc(...$addresses);
    }

    /**
     * @param string[] $addresses
     * @return $this
     */
    public function setBcc(array $addresses): MailMessage
    {
        return $this->bcc(...$addresses);
    }
}
