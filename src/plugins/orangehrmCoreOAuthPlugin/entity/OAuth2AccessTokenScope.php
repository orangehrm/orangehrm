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

/**
 * @ORM\Table(name="ohrm_oauth2_access_scopes")
 * @ORM\Entity
 */
class OAuth2AccessTokenScope
{
    /**
     * @var OAuth2AccessToken
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\OAuth2AccessToken")
     * @ORM\JoinColumn(name="access_token_id", referencedColumnName="id", nullable=false)
     */
    private OAuth2AccessToken $accessToken;

    /**
     * @var OAuth2Scope
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\OAuth2Scope")
     * @ORM\JoinColumn(name="scope_id", referencedColumnName="id", nullable=false)
     */
    private OAuth2Scope $scope;

    /**
     * @return OAuth2AccessToken
     */
    public function getAccessToken(): OAuth2AccessToken
    {
        return $this->accessToken;
    }

    /**
     * @param OAuth2AccessToken $accessToken
     */
    public function setAccessToken(OAuth2AccessToken $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return OAuth2Scope
     */
    public function getScope(): OAuth2Scope
    {
        return $this->scope;
    }

    /**
     * @param OAuth2Scope $scope
     */
    public function setScope(OAuth2Scope $scope): void
    {
        $this->scope = $scope;
    }
}
