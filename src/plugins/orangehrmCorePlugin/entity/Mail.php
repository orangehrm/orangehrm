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

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;

/**
 * @ORM\Table(name="ohrm_mail_queue")
 * @ORM\Entity
 */
class Mail implements \OrangeHRM\Core\Mail\Mail
{
    use DateTimeHelperTrait;

    public const STATUS_PENDING = 'pending';
    public const STATUS_STARTED = 'started';
    public const STATUS_SENT = 'sent';
    public const STATUS_FAILED = 'failed';

    public const CONTENT_TYPE_TEXT_PLAIN = 'text/plain';
    public const CONTENT_TYPE_TEXT_HTML = 'text/html';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var string[]
     *
     * @ORM\Column(name="to_list", type="array")
     */
    private array $toList = [];

    /**
     * @var string[]
     *
     * @ORM\Column(name="cc_list", type="array", nullable=true)
     */
    private array $ccList = [];

    /**
     * @var string[]
     *
     * @ORM\Column(name="bcc_list", type="array", nullable=true)
     */
    private array $bccList = [];

    /**
     * @var string|null
     *
     * @ORM\Column(name="subject", type="string", length=1000, nullable=true)
     */
    private ?string $subject;

    /**
     * @var string|null
     *
     * @ORM\Column(name="body", type="text")
     */
    private ?string $body;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private ?DateTime $createdAt;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="sent_at", type="datetime", nullable=true)
     */
    private ?DateTime $sentAt;

    /**
     * @var string|null
     *
     * @ORM\Column(name="status", type="string", length=12, nullable=true)
     */
    private ?string $status;

    /**
     * @var string|null
     *
     * @ORM\Column(name="content_type", type="string", length=20, nullable=true)
     */
    private ?string $contentType;

    public function __construct()
    {
        $this->createdAt = $this->getDateTimeHelper()->getNow();
        $this->status = self::STATUS_PENDING;
        $this->contentType = self::CONTENT_TYPE_TEXT_HTML;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
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
     * @return string[]
     */
    public function getToList(): array
    {
        return $this->toList;
    }

    /**
     * @param array $toList
     */
    public function setToList(array $toList): void
    {
        $this->toList = $toList;
    }

    /**
     * @return string[]|null
     */
    public function getCcList(): ?array
    {
        return $this->ccList;
    }

    /**
     * @param array $ccList
     */
    public function setCcList(array $ccList): void
    {
        $this->ccList = $ccList;
    }

    /**
     * @return string[]|null
     */
    public function getBccList(): ?array
    {
        return $this->bccList;
    }

    /**
     * @param string[] $bccList
     */
    public function setBccList(array $bccList): void
    {
        $this->bccList = $bccList;
    }

    /**
     * @return string|null
     */
    public function getSubject(): ?string
    {
        return $this->subject;
    }

    /**
     * @param string|null $subject
     */
    public function setSubject(?string $subject): void
    {
        $this->subject = $subject;
    }

    /**
     * @return string|null
     */
    public function getBody(): ?string
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

    /**
     * @return DateTime|null
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return DateTime|null
     */
    public function getSentAt(): ?DateTime
    {
        return $this->sentAt;
    }

    /**
     * @param DateTime|null $sentAt
     */
    public function setSentAt(?DateTime $sentAt): void
    {
        $this->sentAt = $sentAt;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string|null $status
     */
    public function setStatus(?string $status): void
    {
        if (!in_array($status, [
            self::STATUS_PENDING,
            self::STATUS_STARTED,
            self::STATUS_SENT,
            self::STATUS_FAILED,
        ])) {
            throw new InvalidArgumentException('Invalid status name');
        }

        $this->status = $status;
    }

    /**
     * @return string|null
     */
    public function getContentType(): ?string
    {
        return $this->contentType;
    }

    /**
     * @param string|null $contentType
     */
    public function setContentType(?string $contentType): void
    {
        if (!in_array($contentType, [self::CONTENT_TYPE_TEXT_PLAIN, self::CONTENT_TYPE_TEXT_HTML])) {
            throw new InvalidArgumentException('Invalid content type');
        }

        $this->contentType = $contentType;
    }
}
