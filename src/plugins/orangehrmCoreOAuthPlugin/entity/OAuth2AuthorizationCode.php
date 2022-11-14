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
 * Boston, MA 02110-1301, USA
 */

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;

/**
 * @ORM\Table(name="ohrm_oauth2_authorization_codes")
 * @ORM\Entity
 */
class OAuth2AuthorizationCode implements AuthCodeEntityInterface
{
    use EntityManagerHelperTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $identifier;

    /**
     * @var string
     *
     * @ORM\Column(name="authorization_code", type="string", length=80)
     */
    private string $authorizationCode;

    /**
     * @var DateTimeImmutable
     *
     * @ORM\Column(name="expiry_date_time", type="datetime_immutable")
     */
    private DateTimeImmutable $expiryDateTime;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private User $userIdentifier;

    /**
     * @var OAuth2Client
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\OAuth2Client")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     */
    private OAuth2Client $client;

    /**
     * @var null|string
     *
     * @ORM\Column(name="redirect_uri", type="string", length=2000)
     */
    private string $redirectUri;

    /**
     * TODO
     * @var ScopeEntityInterface[]
     */
    private iterable $scopes = [];

    /**
     * @return int
     */
    public function getIdentifier(): int
    {
        return $this->identifier;
    }

    /**
     * @param int $identifier
     */
    public function setIdentifier($identifier): void
    {
        $this->identifier = $identifier;
    }

    /**
     * @return string
     */
    public function getAuthorizationCode(): string
    {
        return $this->authorizationCode;
    }

    /**
     * @param string $authorizationCode
     */
    public function setAuthorizationCode(string $authorizationCode): void
    {
        $this->authorizationCode = $authorizationCode;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getExpiryDateTime(): DateTimeImmutable
    {
        return $this->expiryDateTime;
    }

    /**
     * @param DateTimeImmutable $dateTime
     */
    public function setExpiryDateTime(DateTimeImmutable $dateTime): void
    {
        $this->expiryDateTime = $dateTime;
    }

    /**
     * @return User
     */
    public function getUserIdentifier(): User
    {
        return $this->userIdentifier;
    }

    /**
     * @param int $identifier
     */
    public function setUserIdentifier($identifier): void
    {
        $user = $this->getReference(User::class, $identifier);
        $this->userIdentifier = $user;
    }

    /**
     * @return OAuth2Client
     */
    public function getClient(): OAuth2Client
    {
        return $this->client;
    }

    /**
     * @param ClientEntityInterface $client
     */
    public function setClient(ClientEntityInterface $client): void
    {
        $this->client = $client;
    }

    /**
     * @return string
     */
    public function getRedirectUri(): string
    {
        return $this->redirectUri;
    }

    /**
     * @param string $uri
     */
    public function setRedirectUri($uri): void
    {
        $this->redirectUri = $uri;
    }

    /**
     * @return ScopeEntityInterface[]
     */
    public function getScopes(): array
    {
        return $this->scopes;
    }

    /**
     * @param ScopeEntityInterface[] $scopes
     */
    public function setScopes(array $scopes): void
    {
        $this->scopes = $scopes;
    }

    public function addScope(ScopeEntityInterface $scope)
    {
        // TODO: Implement addScope() method.
    }
}
