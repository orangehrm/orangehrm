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
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\ClientTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;

/**
 * @ORM\Table(name="ohrm_oauth2_clients")
 * @ORM\Entity
 */
class OAuth2Client implements ClientEntityInterface
{
    use ClientTrait;
    use EntityTrait;

    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string", length=80, nullable=false)
     */
    protected $identifier;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=80)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="client_secret", type="string", length=80)
     */
    protected string $clientSecret;

    /**
     * @var string|string[]
     *
     * @ORM\Column(name="redirect_uri", type="string", length=2000)
     */
    protected $redirectUri;

    /**
     * @var bool
     */
    protected $isConfidential = false;

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
