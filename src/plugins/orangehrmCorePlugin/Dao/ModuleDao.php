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

namespace OrangeHRM\Core\Dao;

use OrangeHRM\Core\Event\ModuleEvent;
use OrangeHRM\Core\Event\ModuleStatusChange;
use OrangeHRM\Core\Traits\EventDispatcherTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\DataGroup;
use OrangeHRM\Entity\DataGroupPermission;
use OrangeHRM\Entity\Module;

/**
 * Module Dao: Manages module entries in ohrm_module
 *
 */
class ModuleDao extends BaseDao
{
    use EventDispatcherTrait;
    use UserRoleManagerTrait;

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
                $previousModule = clone $module;
                $module->setStatus((bool)$modules[$module->getName()]);
                $this->getEntityManager()->persist($module);
                $this->getEventDispatcher()->dispatch(
                    new ModuleStatusChange($previousModule, $module),
                    ModuleEvent::MODULE_STATUS_CHANGE
                );
            }
        }
        $this->getEntityManager()->flush();
        return $allModules;
    }

    /**
     * @return string[]
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
     * @return string[]
     */
    public function getEnabledModuleNameList(): array
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
    public function updateDataGroupPermissionForWidgetModules(string $dataGroupName, bool $status)
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
