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

namespace OrangeHRM\Core\Dao;

use Exception;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\Module;
use OrangeHRM\Entity\Province;
use OrangeHRM\Entity\User;

/**
 * Module Dao: Manages module entries in ohrm_module
 *
 */
class ModuleDao extends BaseDao
{
    public function getModuleList(): array
    {
        try {
            $q = $this->createQueryBuilder(Module::class, 'm');
            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    public function updateModules(array $modules): array
    {
        try {
            $allModules = $this->getModuleList();
            foreach ($allModules as $module) {
                if (in_array($module->getName(), $modules)
                    && $module->getStatus() !== $modules[$module->getName()]) {
                    $module->setStatus($modules[$module->getName()] ? 1 : 0);
                }
                $this->persist($module);
            }
            return $allModules;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
