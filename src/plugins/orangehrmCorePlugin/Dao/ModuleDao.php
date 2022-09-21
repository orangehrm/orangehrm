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

use OrangeHRM\Entity\DataGroup;
use OrangeHRM\Entity\DataGroupPermission;
use OrangeHRM\Entity\Module;

/**
 * Module Dao: Manages module entries in ohrm_module
 *
 */
class ModuleDao extends BaseDao
{
    /**
     * Get Module object collection from ohrm_module table
     * @return Module[]
     */
    public function getModuleList(): array
    {
        $q = $this->createQueryBuilder(Module::class, 'm');
        return $q->getQuery()->execute();
    }

    /**
     * Update Module Status
     * Accept a module array with key as module name and value as enabled status
     * $modules = ['leave' => 1, 'admin' => 0]
     * @param array<string, bool> $modules
     * @return Module[]
     */
    public function updateModuleStatus(array $modules): array
    {
        $allModules = $this->getModuleList();
        foreach ($allModules as $module) {
            if (in_array($module->getName(), $modules)
                && array_key_exists($module->getName(), $modules)
                && $module->getStatus() !== $modules[$module->getName()]) {
                $module->setStatus((bool)$modules[$module->getName()]);
                $this->getEntityManager()->persist($module);
                //If the module is affecting the widget, update the relevant permissions
                if ($module->getName() === 'leave') {
                    $this->updateDataGroupPermissionForWidgetModules(
                        'dashboard_leave_widget',
                        (bool)$modules[$module->getName()]
                    );
                } elseif ($module->getName() === 'time') {
                    $this->updateDataGroupPermissionForWidgetModules(
                        'dashboard_time_widget',
                        (bool)$modules[$module->getName()]
                    );
                }
            }
        }
        $this->getEntityManager()->flush();
        return $allModules;
    }

    /**
     * @return Module[]
     */
    public function getDisabledModuleList(): array
    {
        $q = $this->createQueryBuilder(Module::class, 'm');
        $q->andWhere('m.status = :status');
        $q->setParameter('status', false);
        $q->select('m.name');
        return $q->getQuery()->execute();
    }

    /**
     * @return Module[]
     */
    public function getEnabledModuleList(): array
    {
        $q = $this->createQueryBuilder(Module::class, 'm');
        $q->andWhere('m.status = :status');
        $q->setParameter('status', true);
        $q->select('m.name');
        return $q->getQuery()->getSingleColumnResult();
    }

    /**
     * @param string $dataGroupName
     * @param bool $status
     * @return void
     */
    private function updateDataGroupPermissionForWidgetModules(string $dataGroupName, bool $status)
    {
        $dataGroup = $this->getDataGroupByDataGroupName($dataGroupName);
        if (!is_null($dataGroup)) {
            $userRolePermissions = $this->getUserRolePermissionsByDataGroupId($dataGroup->getId());
            foreach ($userRolePermissions as $userRolePermission) {
                $userRolePermission->setCanRead($status);
                $this->getEntityManager()->persist($userRolePermission);
            }
        }
    }

    /**
     * @param string $dataGroupName
     * @return DataGroup|null
     */
    private function getDataGroupByDataGroupName(string $dataGroupName): ?DataGroup
    {
        return $this->getRepository(DataGroup::class)->findOneBy(['name' => $dataGroupName]);
    }

    /**
     * @param int $dataGroupId
     * @return DataGroupPermission[]
     */
    private function getUserRolePermissionsByDataGroupId(int $dataGroupId): array
    {
        return $this->getRepository(DataGroupPermission::class)->findBy(['dataGroup' => $dataGroupId]);
    }
}
