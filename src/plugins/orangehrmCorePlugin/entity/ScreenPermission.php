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
 * @ORM\Table(name="ohrm_user_role_screen")
 * @ORM\Entity
 */
class ScreenPermission
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="can_read", type="boolean", nullable=true)
     */
    private ?bool $canRead = null;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="can_create", type="boolean", nullable=true)
     */
    private ?bool $canCreate = null;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="can_update", type="boolean", nullable=true)
     */
    private ?bool $canUpdate = null;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="can_delete", type="boolean", nullable=true)
     */
    private ?bool $canDelete = null;

    /**
     * @var UserRole
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\UserRole")
     * @ORM\JoinColumn(name="user_role_id", referencedColumnName="id")
     */
    private UserRole $userRole;

    /**
     * @var Screen
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Screen", inversedBy="screenPermissions")
     * @ORM\JoinColumn(name="screen_id", referencedColumnName="id")
     */
    private Screen $screen;

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
     * @return bool
     */
    public function canRead(): bool
    {
        return (bool)$this->canRead;
    }

    /**
     * @param bool $canRead
     */
    public function setCanRead(bool $canRead): void
    {
        $this->canRead = $canRead;
    }

    /**
     * @return bool
     */
    public function canCreate(): bool
    {
        return (bool)$this->canCreate;
    }

    /**
     * @param bool $canCreate
     */
    public function setCanCreate(bool $canCreate): void
    {
        $this->canCreate = $canCreate;
    }

    /**
     * @return bool
     */
    public function canUpdate(): bool
    {
        return (bool)$this->canUpdate;
    }

    /**
     * @param bool $canUpdate
     */
    public function setCanUpdate(bool $canUpdate): void
    {
        $this->canUpdate = $canUpdate;
    }

    /**
     * @return bool
     */
    public function canDelete(): bool
    {
        return (bool)$this->canDelete;
    }

    /**
     * @param bool $canDelete
     */
    public function setCanDelete(bool $canDelete): void
    {
        $this->canDelete = $canDelete;
    }

    /**
     * @return UserRole
     */
    public function getUserRole(): UserRole
    {
        return $this->userRole;
    }

    /**
     * @param UserRole $userRole
     */
    public function setUserRole(UserRole $userRole): void
    {
        $this->userRole = $userRole;
    }

    /**
     * @return Screen
     */
    public function getScreen(): Screen
    {
        return $this->screen;
    }

    /**
     * @param Screen $screen
     */
    public function setScreen(Screen $screen): void
    {
        $this->screen = $screen;
    }
}
