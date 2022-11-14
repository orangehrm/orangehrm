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
 * @ORM\Table(name="ohrm_oauth2_authorization_scopes")
 * @ORM\Entity
 */
class OAuth2AuthorizationCodeScope
{
    /**
     * @var OAuth2AuthorizationCode
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\OAuth2AuthorizationCode")
     * @ORM\JoinColumn(name="authorization_code_id", referencedColumnName="id", nullable=false)
     */
    private OAuth2AuthorizationCode $authorizationCode;

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
     * @return OAuth2AuthorizationCode
     */
    public function getAuthorizationCode(): OAuth2AuthorizationCode
    {
        return $this->authorizationCode;
    }

    /**
     * @param OAuth2AuthorizationCode $authorizationCode
     */
    public function setAuthorizationCode(OAuth2AuthorizationCode $authorizationCode): void
    {
        $this->authorizationCode = $authorizationCode;
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
