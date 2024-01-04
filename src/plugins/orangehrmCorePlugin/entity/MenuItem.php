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
 * @ORM\Table(name="ohrm_menu_item")
 * @ORM\Entity
 */
class MenuItem
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
     * @var string
     *
     * @ORM\Column(name="menu_title", type="string", length=255)
     */
    private string $menuTitle;

    /**
     * @var MenuItem|null
     *
     * @ORM\OneToOne(targetEntity="OrangeHRM\Entity\MenuItem")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
     */
    private ?MenuItem $parent = null;

    /**
     * @var int
     *
     * @ORM\Column(name="level", type="integer")
     */
    private int $level;

    /**
     * @var int
     *
     * @ORM\Column(name="order_hint", type="integer")
     */
    private int $orderHint;

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private bool $status;

    /**
     * @var Screen|null
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Screen")
     * @ORM\JoinColumn(name="screen_id", referencedColumnName="id", nullable=true)
     */
    private ?Screen $screen = null;

    /**
     * @var array|null
     *
     * @ORM\Column(name="additional_params", type="json", nullable=true)
     */
    private ?array $additionalParams = [];

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
    public function getMenuTitle(): string
    {
        return $this->menuTitle;
    }

    /**
     * @param string $menuTitle
     */
    public function setMenuTitle(string $menuTitle): void
    {
        $this->menuTitle = $menuTitle;
    }

    /**
     * @return MenuItem|null
     */
    public function getParent(): ?MenuItem
    {
        return $this->parent;
    }

    /**
     * @param MenuItem|null $parent
     */
    public function setParent(?MenuItem $parent): void
    {
        $this->parent = $parent;
    }

    /**
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * @param int $level
     */
    public function setLevel(int $level): void
    {
        $this->level = $level;
    }

    /**
     * @return int
     */
    public function getOrderHint(): int
    {
        return $this->orderHint;
    }

    /**
     * @param int $orderHint
     */
    public function setOrderHint(int $orderHint): void
    {
        $this->orderHint = $orderHint;
    }

    /**
     * @return bool
     */
    public function getStatus(): bool
    {
        return $this->status;
    }

    /**
     * @param bool $status
     */
    public function setStatus(bool $status): void
    {
        $this->status = $status;
    }

    /**
     * @return Screen|null
     */
    public function getScreen(): ?Screen
    {
        return $this->screen;
    }

    /**
     * @param Screen|null $screen
     */
    public function setScreen(?Screen $screen): void
    {
        $this->screen = $screen;
    }

    /**
     * @return array|null
     */
    public function getAdditionalParams(): ?array
    {
        return $this->additionalParams;
    }

    /**
     * @param array|null $additionalParams
     */
    public function setAdditionalParams(?array $additionalParams): void
    {
        $this->additionalParams = $additionalParams;
    }
}
