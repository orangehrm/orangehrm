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

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Lcobucci\JWT\Configuration;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;

/**
 * @ORM\Table(name="ohrm_oauth2_access_tokens")
 * @ORM\Entity
 */
class OAuth2AccessToken implements AccessTokenEntityInterface
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
     * @ORM\Column(name="access_token", type="string", length=80)
     */
    private string $accessToken;

    /**
     * @var ScopeEntityInterface[]
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\OAuth2Scope", inversedBy="accessTokens")
     * @ORM\JoinTable(
     *     name="ohrm_oauth2_access_scopes",
     *     joinColumns={@ORM\JoinColumn(name="access_token_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="scope_id", referencedColumnName="id")}
     * )
     */
    private iterable $scopes;

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
     * @var CryptKey
     */
    private CryptKey $privateKey;

    /**
     * @var Configuration
     */
    private Configuration $jwtConfiguration;

    public function __construct()
    {
        $this->scopes = new ArrayCollection();
    }

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
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     */
    public function setAccessToken(string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return ScopeEntityInterface[]
     */
    public function getScopes(): iterable
    {
        return $this->scopes;
    }

    /**
     * @param ScopeEntityInterface[] $scopes
     */
    public function setScopes(iterable $scopes): void
    {
        $this->scopes = $scopes;
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
     * @return CryptKey
     */
    public function getPrivateKey(): CryptKey
    {
        return $this->privateKey;
    }

    /**
     * @param CryptKey $privateKey
     */
    public function setPrivateKey(CryptKey $privateKey): void
    {
        $this->privateKey = $privateKey;
    }

    /**
     * @param ScopeEntityInterface $scope
     */
    public function addScope(ScopeEntityInterface $scope): void
    {
        $this->scopes[] = $scope;
    }

    public function __toString()
    {
        // TODO: Implement __toString() method.
        return 'Access Token To String';
    }
}
