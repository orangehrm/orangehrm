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

namespace OrangeHRM\Core\Service;

use Doctrine\Common\Collections\Collection;
use OrangeHRM\Core\Dao\ModuleDao;

/**
 * Module Service: Manages module entries in ohrm_module
 *
 */
class ModuleService
{
    /**
     * @var ModuleDao|null
     */
    protected ?ModuleDao $moduleDao = null;

    /**
     * Get Module Dao
     * @return ModuleDao
     */
    public function getModuleDao(): ModuleDao
    {
        if (!$this->moduleDao instanceof ModuleDao) {
            $this->moduleDao = new ModuleDao();
        }

        return $this->moduleDao;
    }

    /**
     * Set Module Dao
     * @param ModuleDao $moduleDao
     */
    public function setModuleDao(ModuleDao $moduleDao): void
    {
        $this->moduleDao = $moduleDao;
    }

    /**
     * Get Module object collection from ohrm_module table
     *
     * @return Module[]
     */
    public function getModuleList(): array
    {
        return $this->getModuleDao()->getModuleList();
    }

    /**
     * Update Module Status
     * Accept a module array with key as module name and value as enabled status
     * $modules = ['leave' => 1, 'admin' => 0]
     *
     * @param array<string, bool> $modules
     * @return Module[]
     */
    public function updateModuleStatus(?array $modules): array
    {
        return $this->getModuleDao()->updateModuleStatus($modules);
    }
}
