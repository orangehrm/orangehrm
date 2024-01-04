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
 * @ORM\Table(name="ohrm_openid_user_identity")
 * @ORM\Entity
 */
class OpenIdUserIdentity
{
    /**
     * @var User
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private User $user;

    /**
     * @var OpenIdProvider
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="OrangeHRM\Entity\OpenIdProvider")
     * @ORM\JoinColumn(name="provider_id", referencedColumnName="id")
     */
    private OpenIdProvider $openIdProvider;

    /**
     * @var string
     *
     * @ORM\Column(name="user_identity", type="string")
     */
    private string $userIdentity;

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
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
    public function getUserIdentity(): string
    {
        return $this->userIdentity;
    }

    /**
     * @param string $userIdentity
     */
    public function setUserIdentity(string $userIdentity): void
    {
        $this->userIdentity = $userIdentity;
    }
}
