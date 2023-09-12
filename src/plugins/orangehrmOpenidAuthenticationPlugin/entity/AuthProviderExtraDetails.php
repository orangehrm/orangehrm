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
 * @ORM\Table(name="ohrm_auth_provider_extra_details")
 * @ORM\Entity
 */
class AuthProviderExtraDetails
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", length=10)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var OpenIdProvider
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\OpenIdProvider")
     * @ORM\JoinColumn(name="provider_id", referencedColumnName="id")
     */
    private OpenIdProvider $openIdProvider;

    /**
     * @var int
     *
     * @ORM\Column(name="provider_type", type="integer")
     */
    private int $providerType;

    /**
     * @var string
     *
     * @ORM\Column(name="client_id", type="string")
     */
    private string $clientId;

    /**
     * @var string
     *
     * @ORM\Column(name="client_secret", type="string")
     */
    private string $clientSecret;

    /**
     * @var string
     *
     * @ORM\Column(name="developer_key", type="string")
     */
    private string $developerKey;

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
     * @return OpenIdProvider
     */
    public function getOpenIdProvider(): OpenIdProvider
    {
        return $this->openIdProvider;
    }

    /**
     * @param OpenIdProvider $openIdProvider
     */
    public function setOpenIdProvider(OpenIdProvider $openIdProvider): void
    {
        $this->openIdProvider = $openIdProvider;
    }

    /**
     * @return int
     */
    public function getProviderType(): int
    {
        return $this->providerType;
    }

    /**
     * @param int $providerType
     */
    public function setProviderType(int $providerType): void
    {
        $this->providerType = $providerType;
    }

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @param string $clientId
     */
    public function setClientId(string $clientId): void
    {
        $this->clientId = $clientId;
    }

    /**
     * @return string
     */
    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    /**
     * @param string $clientSecret
     */
    public function setClientSecret(string $clientSecret): void
    {
        $this->clientSecret = $clientSecret;
    }

    /**
     * @return string
     */
    public function getDeveloperKey(): string
    {
        return $this->developerKey;
    }

    /**
     * @param string $developerKey
     */
    public function setDeveloperKey(string $developerKey): void
    {
        $this->developerKey = $developerKey;
    }
}
