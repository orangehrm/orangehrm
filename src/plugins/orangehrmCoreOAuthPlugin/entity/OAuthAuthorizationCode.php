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

use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;
use OrangeHRM\Core\Service\DateTimeHelperService;

/**
 * @ORM\Table(name="ohrm_oauth2_authorization_code")
 * @ORM\Entity
 */
class OAuthAuthorizationCode
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var string
     *
     * @ORM\Column(name="authorization_code", type="string", length=255, nullable=false)
     */
    private string $authCode;

    /**
     * @var int
     *
     * @ORM\Column(name="client_id", type="bigint", nullable=false)
     */
    private int $clientId;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     */
    private int $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="redirect_uri", type="string", length=2000, nullable=false)
     */
    private string $redirectUri;

    /**
     * @var DateTimeImmutable
     *
     * @ORM\Column(name="expiry_date_time_utc", type="datetime_immutable", nullable=false)
     */
    private DateTimeImmutable $expiryDateTime;

    /**
     * @var bool
     *
     * @ORM\Column(name="revoked", type="boolean", nullable=false)
     */
    private bool $revoked = false;

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
     * @return string
     */
    public function getAuthCode(): string
    {
        return $this->authCode;
    }

    /**
     * @param string $authCode
     */
    public function setAuthCode(string $authCode): void
    {
        $this->authCode = $authCode;
    }

    /**
     * @return int
     */
    public function getClientId(): int
    {
        return $this->clientId;
    }

    /**
     * @param int $clientId
     */
    public function setClientId(int $clientId): void
    {
        $this->clientId = $clientId;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @return string
     */
    public function getRedirectUri(): string
    {
        return $this->redirectUri;
    }

    /**
     * @param string $redirectUri
     */
    public function setRedirectUri(string $redirectUri): void
    {
        $this->redirectUri = $redirectUri;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getExpiryDateTime(): DateTimeImmutable
    {
        return new DateTimeImmutable(
            $this->expiryDateTime->format('Y-m-d H:i:s'),
            new DateTimeZone(DateTimeHelperService::TIMEZONE_UTC)
        );
    }

    /**
     * @param DateTimeImmutable $expiryDateTime
     */
    public function setExpiryDateTime(DateTimeImmutable $expiryDateTime): void
    {
        $this->expiryDateTime = $expiryDateTime;
    }

    /**
     * @return bool
     */
    public function isRevoked(): bool
    {
        return $this->revoked;
    }

    /**
     * @param bool $revoked
     */
    public function setRevoked(bool $revoked): void
    {
        $this->revoked = $revoked;
    }
}
