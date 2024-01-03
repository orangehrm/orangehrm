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

use League\OAuth2\Server\Entities\ClientEntityInterface;
use OrangeHRM\Entity\OAuthClient;

class ClientEntity implements ClientEntityInterface
{
    private int $identifier;
    private string $name;
    private string $redirectUri;
    private bool $confidential;
    private string $displayName;

    public function __construct(
        int $identifier,
        string $name,
        string $redirectUri,
        bool $confidential,
        string $displayName
    ) {
        $this->identifier = $identifier;
        $this->name = $name;
        $this->redirectUri = $redirectUri;
        $this->confidential = $confidential;
        $this->displayName = $displayName;
    }

    /**
     * @inheritDoc
     * @return int
     */
    public function getIdentifier(): int
    {
        return $this->identifier;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getRedirectUri(): string
    {
        return $this->redirectUri;
    }

    /**
     * @inheritDoc
     */
    public function isConfidential(): bool
    {
        return $this->confidential;
    }

    /**
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * @param OAuthClient $authClient
     * @return self
     */
    public static function createFromEntity(OAuthClient $authClient): self
    {
        return new self(
            $authClient->getId(),
            $authClient->getClientId(),
            $authClient->getRedirectUri(),
            $authClient->isConfidential(),
            $authClient->getName(),
        );
    }
}
