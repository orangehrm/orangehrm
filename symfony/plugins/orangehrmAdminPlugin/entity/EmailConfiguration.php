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

use Doctrine\ORM\Mapping as ORM;

/**
 * EmailConfiguration
 *
 * @ORM\Table("ohrm_email_configuration")
 * @ORM\Entity
 */
class EmailConfiguration
{
    /**
     * @var int
     *
     * @ORM\Column(name="id",type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;


    /**
     * @var string|null
     *
     * @ORM\Column(name="mail_type", type="string", length=50, nullable=true)
     */
    private ?string $mailType = null;

    /**
     * @var string
     *
     * @ORM\Column(name="sent_as", type="string", length=250, nullable=false)
     */
    private string $sentAs;

    /**
     * @var string|null
     *
     * @ORM\Column(name="smtp_host", type="string", length=250, nullable=true)
     */
    private ?string $smtpHost = null;

    /**
     * @var int|null
     *
     * @ORM\Column(name="smtp_port", type="integer", length=10, nullable=true)
     */
    private ?int $smtpPort = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="smtp_username", type="string", length=250, nullable=true)
     */
    private ?string $smtpUsername = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="smtp_password", type="string", length=250, nullable=true)
     */
    private ?string $smtpPassword = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="smtp_auth_type", type="string", length=50, nullable=true)
     */
    private ?string $smtpAuthType = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="smtp_security_type", type="string", length=50, nullable=true)
     */
    private ?string $smtpSecurityType = null;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getMailType(): ?string
    {
        return $this->mailType;
    }

    /**
     * @param string|null $mailType
     */
    public function setMailType(?string $mailType): void
    {
        $this->mailType = $mailType;
    }

    /**
     * @return string
     */
    public function getSentAs(): string
    {
        return $this->sentAs;
    }

    /**
     * @param string $sentAs
     */
    public function setSentAs(string $sentAs): void
    {
        $this->sentAs = $sentAs;
    }

    /**
     * @return string|null
     */
    public function getSmtpHost(): ?string
    {
        return $this->smtpHost;
    }

    /**
     * @param string|null $smtpHost
     */
    public function setSmtpHost(?string $smtpHost): void
    {
        $this->smtpHost = $smtpHost;
    }

    /**
     * @return int|null
     */
    public function getSmtpPort(): ?int
    {
        return $this->smtpPort;
    }

    /**
     * @param int|null $smtpPort
     */
    public function setSmtpPort(?int $smtpPort): void
    {
        $this->smtpPort = $smtpPort;
    }

    /**
     * @return string|null
     */
    public function getSmtpUsername(): ?string
    {
        return $this->smtpUsername;
    }

    /**
     * @param string|null $smtpUsername
     */
    public function setSmtpUsername(?string $smtpUsername): void
    {
        $this->smtpUsername = $smtpUsername;
    }

    /**
     * @return string|null
     */
    public function getSmtpPassword(): ?string
    {
        return $this->smtpPassword;
    }

    /**
     * @param string|null $smtpPassword
     */
    public function setSmtpPassword(?string $smtpPassword): void
    {
        $this->smtpPassword = $smtpPassword;
    }

    /**
     * @return string|null
     */
    public function getSmtpAuthType(): ?string
    {
        return $this->smtpAuthType;
    }

    /**
     * @param string|null $smtpAuthType
     */
    public function setSmtpAuthType(?string $smtpAuthType): void
    {
        $this->smtpAuthType = $smtpAuthType;
    }

    /**
     * @return string|null
     */
    public function getSmtpSecurityType(): ?string
    {
        return $this->smtpSecurityType;
    }

    /**
     * @param string|null $smtpSecurityType
     */
    public function setSmtpSecurityType(?string $smtpSecurityType): void
    {
        $this->smtpSecurityType = $smtpSecurityType;
    }
}
