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
}
