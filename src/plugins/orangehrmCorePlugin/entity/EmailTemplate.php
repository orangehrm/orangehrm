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

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ohrm_email_template")
 * @ORM\Entity
 */
class EmailTemplate
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", length=6)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var Email
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Email")
     * @ORM\JoinColumn(name="email_id", referencedColumnName="id")
     */
    private Email $email;

    /**
     * @var string|null
     *
     * @ORM\Column(name="locale", type="string", length=20, nullable=true)
     */
    private ?string $locale = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="performer_role", type="string", length=50, nullable=true)
     */
    private ?string $performerRole;

    /**
     * @var string
     *
     * @ORM\Column(name="recipient_role", type="string", length=50)
     */
    private string $recipientRole;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=255)
     */
    private string $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="body", type="text")
     */
    private string $body;

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
     * @return Email
     */
    public function getEmail(): Email
    {
        return $this->email;
    }

    /**
     * @param Email $email
     */
    public function setEmail(Email $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string|null
     */
    public function getLocale(): ?string
    {
        return $this->locale;
    }

    /**
     * @param string|null $locale
     */
    public function setLocale(?string $locale): void
    {
        $this->locale = $locale;
    }

    /**
     * @return string|null
     */
    public function getPerformerRole(): ?string
    {
        return $this->performerRole;
    }

    /**
     * @param string|null $performerRole
     */
    public function setPerformerRole(?string $performerRole): void
    {
        $this->performerRole = $performerRole;
    }

    /**
     * @return string
     */
    public function getRecipientRole(): string
    {
        return $this->recipientRole;
    }

    /**
     * @param string $recipientRole
     */
    public function setRecipientRole(string $recipientRole): void
    {
        $this->recipientRole = $recipientRole;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody(string $body): void
    {
        $this->body = $body;
    }
}
