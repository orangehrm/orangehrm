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

namespace OrangeHRM\Core\Dto;

class ModuleScreen
{
    /**
     * @var string|null
     */
    private ?string $module = null;

    /**
     * @var string|null
     */
    private ?string $screen = null;

    /**
     * @var string|null
     */
    private ?string $overriddenModule = null;

    /**
     * @var string|null
     */
    private ?string $overriddenScreen = null;

    /**
     * @param string|null $module
     * @param string|null $screen
     */
    public function __construct(?string $module = null, ?string $screen = null)
    {
        $this->module = $module;
        $this->screen = $screen;
        $this->overrideModule($module);
        $this->overrideScreen($screen);
    }

    /**
     * @return string|null
     */
    public function getModule(): ?string
    {
        return $this->module;
    }

    /**
     * @param string|null $module
     */
    public function setModule(?string $module): void
    {
        $this->module = $module;
        !is_null($this->overriddenModule) ?: $this->overrideModule($module);
    }

    /**
     * @return string|null
     */
    public function getScreen(): ?string
    {
        return $this->screen;
    }

    /**
     * @param string|null $screen
     */
    public function setScreen(?string $screen): void
    {
        $this->screen = $screen;
        !is_null($this->overriddenScreen) ?: $this->overrideScreen($screen);
    }

    /**
     * @return string|null
     */
    public function getOverriddenModule(): ?string
    {
        return $this->overriddenModule;
    }

    /**
     * @param string|null $overriddenModule
     */
    public function overrideModule(?string $overriddenModule): void
    {
        $this->overriddenModule = $overriddenModule;
    }

    /**
     * @return string|null
     */
    public function getOverriddenScreen(): ?string
    {
        return $this->overriddenScreen;
    }

    /**
     * @param string|null $overriddenScreen
     */
    public function overrideScreen(?string $overriddenScreen): void
    {
        $this->overriddenScreen = $overriddenScreen;
    }
}
