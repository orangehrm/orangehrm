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

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ohrm_screen")
 * @ORM\Entity
 */
class Screen
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
     * @ORM\Column(name="name", type="string", length=100)
     */
    private string $name;

    /**
     * @var string
     *
     * @ORM\Column(name="action_url", type="string", length=255)
     */
    private string $actionUrl;

    /**
     * @var Module
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Module")
     * @ORM\JoinColumn(name="module_id", referencedColumnName="id")
     */
    private Module $module;

    /**
     * @var string|null
     *
     * @ORM\Column(name="menu_configurator", type="string", length=255, nullable=true)
     */
    private ?string $menuConfigurator = null;

    /**
     * @var ScreenPermission[]|Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\ScreenPermission", mappedBy="screen")
     */
    private $screenPermissions;

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
    public function getActionUrl(): string
    {
        return $this->actionUrl;
    }

    /**
     * @param string $actionUrl
     */
    public function setActionUrl(string $actionUrl): void
    {
        $this->actionUrl = $actionUrl;
    }

    /**
     * @return Module
     */
    public function getModule(): Module
    {
        return $this->module;
    }

    /**
     * @param Module $module
     */
    public function setModule(Module $module): void
    {
        $this->module = $module;
    }

    /**
     * @return string|null
     */
    public function getMenuConfigurator(): ?string
    {
        return $this->menuConfigurator;
    }

    /**
     * @param string|null $menuConfigurator
     */
    public function setMenuConfigurator(?string $menuConfigurator): void
    {
        $this->menuConfigurator = $menuConfigurator;
    }

    /**
     * @return Collection|ScreenPermission[]
     */
    public function getScreenPermissions()
    {
        return $this->screenPermissions;
    }

    /**
     * @param Collection|ScreenPermission[] $screenPermissions
     */
    public function setScreenPermissions($screenPermissions): void
    {
        $this->screenPermissions = $screenPermissions;
    }
}
