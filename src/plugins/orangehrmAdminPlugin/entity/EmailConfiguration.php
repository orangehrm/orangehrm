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

namespace OrangeHRM\Entity;

class EmailConfiguration
{
    private ?string $mailType = null;

    private string $sentAs;

    private ?string $smtpHost = null;

    private ?int $smtpPort = null;

    private ?string $smtpUsername = null;

    private ?string $smtpPassword = null;

    private ?string $smtpAuthType = null;

    private ?string $smtpSecurityType = null;

    /**
     * @return string|null
     */
    public function getMailType(): ?string
    {
        return $this->mailType;
    }

    public function setMailType(?string $mailType): EmailConfiguration
    {
        $this->mailType = $mailType;
        return $this;
    }

    /**
     * @return string
     */
    public function getSentAs(): string
    {
        return $this->sentAs;
    }

    public function setSentAs(string $sentAs): EmailConfiguration
    {
        $this->sentAs = $sentAs;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSmtpHost(): ?string
    {
        return $this->smtpHost;
    }

    public function setSmtpHost(?string $smtpHost): EmailConfiguration
    {
        $this->smtpHost = $smtpHost;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getSmtpPort(): ?int
    {
        return $this->smtpPort;
    }

    public function setSmtpPort(?int $smtpPort): EmailConfiguration
    {
        $this->smtpPort = $smtpPort;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSmtpUsername(): ?string
    {
        return $this->smtpUsername;
    }

    public function setSmtpUsername(?string $smtpUsername): EmailConfiguration
    {
        $this->smtpUsername = $smtpUsername;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSmtpPassword(): ?string
    {
        return $this->smtpPassword;
    }

    public function setSmtpPassword(?string $smtpPassword): EmailConfiguration
    {
        $this->smtpPassword = $smtpPassword;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSmtpAuthType(): ?string
    {
        return $this->smtpAuthType;
    }

    public function setSmtpAuthType(?string $smtpAuthType): EmailConfiguration
    {
        $this->smtpAuthType = $smtpAuthType;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSmtpSecurityType(): ?string
    {
        return $this->smtpSecurityType;
    }

    public function setSmtpSecurityType(?string $smtpSecurityType): EmailConfiguration
    {
        $this->smtpSecurityType = $smtpSecurityType;
        return $this;
    }

    public static function instance(): EmailConfiguration
    {
        return new EmailConfiguration();
    }
}
