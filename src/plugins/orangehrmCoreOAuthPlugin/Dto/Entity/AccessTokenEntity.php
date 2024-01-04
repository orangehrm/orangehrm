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

namespace OrangeHRM\OAuth\Dto\Entity;

use DateTimeImmutable;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\CryptTrait;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\OAuth\Traits\OAuthServerTrait;

class AccessTokenEntity implements AccessTokenEntityInterface
{
    use CryptTrait;
    use DateTimeHelperTrait;
    use OAuthServerTrait;

    private string $identifier;
    private DateTimeImmutable $expiryDateTime;
    private ?int $userIdentifier = null;
    private ClientEntityInterface $client;
    /**
     * @var ScopeEntityInterface[]
     */
    private array $scopes = [];

    /**
     * @inheritDoc
     */
    public function setPrivateKey(CryptKey $privateKey): void
    {
        // Ignored as not using JWT
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->encrypt($this->getIdentifier());
    }

    /**
     * @inheritDoc
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @inheritDoc
     */
    public function setIdentifier($identifier): void
    {
        $this->identifier = $identifier;
    }


    /**
     * @inheritDoc
     */
    public function getExpiryDateTime(): DateTimeImmutable
    {
        return $this->expiryDateTime;
    }

    /**
     * @inheritDoc
     */
    public function setExpiryDateTime(DateTimeImmutable $dateTime): void
    {
        // Override expiry time to UTC
        $this->expiryDateTime = DateTimeImmutable::createFromMutable($this->getDateTimeHelper()->getNowInUTC())
            ->add($this->getOAuthServer()->getAccessTokenTTL());
    }

    /**
     * @inheritDoc
     * @return int|null
     */
    public function getUserIdentifier(): ?int
    {
        return $this->userIdentifier;
    }

    /**
     * @inheritDoc
     * @param int|null $identifier
     */
    public function setUserIdentifier($identifier): void
    {
        $this->userIdentifier = $identifier;
    }

    /**
     * @inheritDoc
     */
    public function getClient(): ClientEntityInterface
    {
        return $this->client;
    }

    /**
     * @inheritDoc
     */
    public function setClient(ClientEntityInterface $client): void
    {
        $this->client = $client;
    }

    /**
     * @inheritDoc
     */
    public function addScope(ScopeEntityInterface $scope): void
    {
        $this->scopes[$scope->getIdentifier()] = $scope;
    }

    /**
     * @inheritDoc
     */
    public function getScopes(): array
    {
        return array_values($this->scopes);
    }
}
