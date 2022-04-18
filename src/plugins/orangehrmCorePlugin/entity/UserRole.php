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
 * @ORM\Table(name="ohrm_user_role")
 * @ORM\Entity
 */
class UserRole
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", length=4)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private string $name;

    /**
     * @var string
     *
     * @ORM\Column(name="display_name", type="string", length=255)
     */
    private string $displayName;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_assignable", type="boolean", options={"default" : 0})
     */
    private bool $isAssignable;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_predefined", type="boolean", options={"default" : 0})
     */
    private bool $isPredefined;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->isAssignable = false;
        $this->isPredefined = false;
    }

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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * @param string $displayName
     */
    public function setDisplayName(string $displayName): void
    {
        $this->displayName = $displayName;
    }

    /**
     * @return bool
     */
    public function isAssignable(): bool
    {
        return $this->isAssignable;
    }

    /**
     * @param bool $isAssignable
     */
    public function setIsAssignable(bool $isAssignable): void
    {
        $this->isAssignable = $isAssignable;
    }

    /**
     * @return bool
     */
    public function isPredefined(): bool
    {
        return $this->isPredefined;
    }

    /**
     * @param bool $isPredefined
     */
    public function setIsPredefined(bool $isPredefined): void
    {
        $this->isPredefined = $isPredefined;
    }
}
