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
use OrangeHRM\Entity\MenuItem;
use OrangeHRM\Entity\Module;
use OrangeHRM\Entity\UserRole;
use OrangeHRM\ORM\Doctrine;
use OrangeHRM\ORM\ListSorter;

class MenuDao extends BaseDao
{
    /**
     * @param UserRole[]|string[] $userRoleList
     * @return MenuItem[]
     * @throws DaoException
     */
    public function getMenuItemList(array $userRoleList): array
    {
        try {
            if (count($userRoleList) == 0) {
                return [];
            }

            $roleNames = [];

            foreach ($userRoleList as $role) {
                if ($role instanceof UserRole) {
                    $roleNames[] = $role->getName();
                } elseif (is_string($role)) {
                    $roleNames[] = $role;
                }
            }

            $q = $this->createQueryBuilder(MenuItem::class, 'mi');
            $q->leftJoin('mi.screen', 'sc');
            $q->leftJoin('sc.module', 'mo');
            $q->leftJoin('sc.screenPermissions', 'sp');
            $q->leftJoin('sp.userRole', 'ur');

            $q->andWhere('mo.status = :moduleStatus');
            $q->setParameter('moduleStatus', Module::ENABLED);

            $q->andWhere('mi.status = :menuItemStatus');
            $q->setParameter('menuItemStatus', MenuItem::STATUS_ENABLED);

            $q->andWhere('sp.canRead = :screenPermission');
            $q->setParameter('screenPermission', true);

            $q->andWhere($q->expr()->in('ur.name', ':roleNames'))
                ->setParameter('roleNames', $roleNames);
            $q->orWhere($q->expr()->isNull('mi.screen'));
            $q->addOrderBy('mi.orderHint', ListSorter::ASCENDING);
            $q->addOrderBy('mi.id', ListSorter::ASCENDING);

            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param string $moduleName
     * @param array $menuTitles
     * @return int
     * @throws DaoException
     */
    public function enableModuleMenuItems(string $moduleName, array $menuTitles = []): int
    {
        try {
            $q = $this->createQueryBuilder(MenuItem::class, 'mi');
            $q->leftJoin('mi.screen', 'sc');
            $q->leftJoin('sc.module', 'mo');

            $q->andWhere('mo.name = :moduleName');
            $q->setParameter('moduleName', $moduleName);

            $q->andWhere('mi.status = :menuItemStatus');
            $q->setParameter('menuItemStatus', MenuItem::STATUS_DISABLED);

            if (!empty($menuTitles)) {
                $q->andWhere($q->expr()->in('mi.menuTitle', ':menuTitles'))
                    ->setParameter('menuTitles', $menuTitles);
            }
            $menuItemList = $q->getQuery()->execute();
            $i = 0;

            foreach ($menuItemList as $menuItem) {
                if ($menuItem instanceof MenuItem) {
                    $menuItem->setStatus(MenuItem::STATUS_ENABLED);
                    Doctrine::getEntityManager()->persist($menuItem);
                }
                $i++;
            }
            Doctrine::getEntityManager()->flush();

            return $i;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param string $moduleName
     * @param string $screenName
     * @return MenuItem|null
     */
    public function getMenuItemByModuleAndScreen(string $moduleName, string $screenName): ?MenuItem
    {
        $q = $this->createQueryBuilder(MenuItem::class, 'mi');
        $q->leftJoin('mi.screen', 'sc');
        $q->leftJoin('sc.module', 'mo');
        $q->andWhere('sc.actionUrl = :screenName');
        $q->andWhere('mo.name = :moduleName');
        $q->setParameter('screenName', $screenName);
        $q->setParameter('moduleName', $moduleName);
        return $this->fetchOne($q);
    }
}
