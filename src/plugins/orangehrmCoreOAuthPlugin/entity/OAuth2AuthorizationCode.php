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
use DateTime;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\Traits\AuthCodeTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\TokenEntityTrait;

/**
 * @ORM\Table(name="ohrm_oauth2_authorization_codes")
 * @ORM\Entity
 */
class OAuth2AuthorizationCode implements AuthCodeEntityInterface
{
    use EntityTrait;
    use TokenEntityTrait;
    use AuthCodeTrait;

    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string", length=80)
     */
    protected $identifier;

    /**
     * @var ScopeEntityInterface[]
     */
    protected $scopes = [];

    /**
     * @var DateTime
     *
     * @ORM\Column(name="expiry", type="datetime_immutable")
     */
    protected $expiryDateTime;

    /**
     * @var string|int|null
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    protected $userIdentifier;

    /**
     * @var ClientEntityInterface
     */
    protected $client;

    /**
     * @var null|string
     *
     * @ORM\Column(name="redirect_uri", type="string", length=2000)
     */
    protected $redirectUri;
}
