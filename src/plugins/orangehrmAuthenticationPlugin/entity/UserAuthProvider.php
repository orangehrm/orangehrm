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
 * @ORM\Table(name="ohrm_user_auth_provider")
 * @ORM\Entity
 */
class UserAuthProvider
{
    public const TYPE_LOCAL = 1;
    public const TYPE_LDAP = 2;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\User", inversedBy="authProviders")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private User $user;

    /**
     * @var int
     *
     * @ORM\Column(name="provider_type", type="integer")
     */
    private int $type;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ldap_user_hash", type="string", length=255, nullable=true)
     */
    private ?string $ldapUserHash = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ldap_user_dn", type="string", length=255, nullable=true)
     */
    private ?string $ldapUserDN = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ldap_user_unique_id", type="string", length=255, nullable=true)
     */
    private ?string $ldapUserUniqueId = null;

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
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType(int $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string|null
     */
    public function getLDAPUserHash(): ?string
    {
        return $this->ldapUserHash;
    }

    /**
     * @param string|null $ldapUserHash
     */
    public function setLDAPUserHash(?string $ldapUserHash): void
    {
        $this->ldapUserHash = $ldapUserHash;
    }

    /**
     * @return string|null
     */
    public function getLDAPUserDN(): ?string
    {
        return $this->ldapUserDN;
    }

    /**
     * @param string|null $ldapUserDN
     */
    public function setLDAPUserDN(?string $ldapUserDN): void
    {
        $this->ldapUserDN = $ldapUserDN;
    }

    /**
     * @return string|null
     */
    public function getLDAPUserUniqueId(): ?string
    {
        return $this->ldapUserUniqueId;
    }

    /**
     * @param string|null $ldapUserUniqueId
     */
    public function setLDAPUserUniqueId(?string $ldapUserUniqueId): void
    {
        $this->ldapUserUniqueId = $ldapUserUniqueId;
    }
}
