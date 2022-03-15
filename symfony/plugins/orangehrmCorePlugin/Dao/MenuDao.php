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

use OrangeHRM\Core\Menu\DetailedMenuItem;
use OrangeHRM\Entity\MenuItem;
use OrangeHRM\Entity\UserRole;
use OrangeHRM\ORM\ListSorter;
use OrangeHRM\ORM\QueryBuilderWrapper;

class MenuDao extends BaseDao
{
    /**
     * @param string $moduleName
     * @param array $menuTitles
     * @return int
     */
    public function enableModuleMenuItems(string $moduleName, array $menuTitles = []): int
    {
        return $this->updateStatusOfModuleMenuItems($moduleName, true, $menuTitles);
    }

    /**
     * @param string $moduleName
     * @param bool $status
     * @param array $menuTitles
     * @return int
     */
    private function updateStatusOfModuleMenuItems(string $moduleName, bool $status, array $menuTitles = []): int
    {
        $q = $this->createQueryBuilder(MenuItem::class, 'menuItem');
        $q->leftJoin('menuItem.screen', 'screen');
        $q->leftJoin('screen.module', 'module');

        $q->andWhere('module.name = :moduleName')
            ->setParameter('moduleName', $moduleName);

        if (!empty($menuTitles)) {
            $q->andWhere($q->expr()->in('menuItem.menuTitle', ':menuTitles'))
                ->setParameter('menuTitles', $menuTitles);
        }
        $q->select('menuItem.id', 'IDENTITY(menuItem.parent) AS parentId');
        $menuItemList = $q->getQuery()->execute();

        $ids = array_column($menuItemList, 'id');
        $parentIds = array_column($menuItemList, 'parentId');
        $menuItemIds = array_unique(array_merge($ids, $parentIds));

        $q = $this->createQueryBuilder(MenuItem::class, 'menuItem')
            ->update()
            ->andWhere($q->expr()->in('menuItem.id', ':ids'))
            ->andWhere('menuItem.status != :status')
            ->setParameter('ids', $menuItemIds)
            ->set('menuItem.status', ':status')
            ->setParameter('status', $status);

        return $q->getQuery()->execute();
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

    /**
     * @param UserRole[] $userRoles
     * @return MenuItem[]
     */
    public function getSidePanelMenuItems(array $userRoles): array
    {
        $q = $this->getMenuItemQueryBuilderWrapper($userRoles);
        if (is_null($q)) {
            return [];
        }
        $q = $q->getQueryBuilder();
        $q->andWhere('mi.level = :menuItemLevel')
            ->setParameter('menuItemLevel', 1);
        $q->addOrderBy('mi.orderHint', ListSorter::ASCENDING)
            ->addOrderBy('mi.id', ListSorter::ASCENDING);

        return $q->getQuery()->execute();
    }

    /**
     * @param UserRole[] $userRoles
     * @return DetailedMenuItem[]
     */
    public function getTopMenuItems(array $userRoles, int $sideMenuItemId): array
    {
        $q = $this->getMenuItemQueryBuilderWrapper($userRoles);
        if (is_null($q)) {
            return [];
        }
        $q = $q->getQueryBuilder();
        $q->andWhere('mi.level = :menuItemLevel')
            ->andWhere('mi.parent = :parentId')
            ->setParameter('menuItemLevel', 2)
            ->setParameter('parentId', $sideMenuItemId);
        $q->addOrderBy('mi.orderHint', ListSorter::ASCENDING)
            ->addOrderBy('mi.id', ListSorter::ASCENDING);

        /** @var MenuItem[] $secondLevelMenuItems */
        $secondLevelMenuItems = $q->getQuery()->execute();
        $secondLevelMenuItemIds = [];
        foreach ($secondLevelMenuItems as $secondLevelMenuItem) {
            $secondLevelMenuItemIds[$secondLevelMenuItem->getId()] = DetailedMenuItem::createFromMenuItem(
                $secondLevelMenuItem
            );
        }

        $q = $this->getMenuItemQueryBuilderWrapper($userRoles)->getQueryBuilder();
        $q->andWhere('mi.level = :menuItemLevel')
            ->andWhere($q->expr()->in('mi.parent', ':parentIds'))
            ->setParameter('menuItemLevel', 3)
            ->setParameter('parentIds', array_keys($secondLevelMenuItemIds));
        $q->addOrderBy('mi.orderHint', ListSorter::ASCENDING)
            ->addOrderBy('mi.id', ListSorter::ASCENDING);

        /** @var MenuItem[] $thirdLevelMenuItems */
        $thirdLevelMenuItems = $q->getQuery()->execute();
        foreach ($thirdLevelMenuItems as $thirdLevelMenuItem) {
            if (isset($secondLevelMenuItemIds[$thirdLevelMenuItem->getParent()->getId()])) {
                $secondLevelMenuItemIds[$thirdLevelMenuItem->getParent()->getId()]
                    ->addChild(DetailedMenuItem::createFromMenuItem($thirdLevelMenuItem));
            }
        }
        return array_values($secondLevelMenuItemIds);
    }

    /**
     * @param UserRole[] $userRoles
     * @return QueryBuilderWrapper|null
     */
    private function getMenuItemQueryBuilderWrapper(array $userRoles): ?QueryBuilderWrapper
    {
        $userRoleIds = array_map(fn (UserRole $userRole) => $userRole->getId(), $userRoles);
        if (empty($userRoleIds)) {
            return null;
        }

        $q = $this->createQueryBuilder(MenuItem::class, 'mi');
        $q->leftJoin('mi.screen', 'sc');
        $q->leftJoin('sc.module', 'mo');
        $q->leftJoin('sc.screenPermissions', 'sp');
        $q->leftJoin('sp.userRole', 'ur');

        $q->andWhere('mo.status = :moduleStatus')
            ->setParameter('moduleStatus', true);

        $q->andWhere('sp.canRead = :screenPermission')
            ->setParameter('screenPermission', true);

        $q->andWhere($q->expr()->in('ur.id', ':roleIds'))
            ->setParameter('roleIds', $userRoleIds);

        $q->orWhere($q->expr()->isNull('mi.screen'));
        $q->andWhere('mi.status = :menuItemStatus')
            ->setParameter('menuItemStatus', true);

        return $this->getQueryBuilderWrapper($q);
    }

    /**
     * @param string $title
     * @param int|null $level
     * @return MenuItem|null
     */
    public function getMenuItemByTitle(string $title, ?int $level = null): ?MenuItem
    {
        $q = $this->createQueryBuilder(MenuItem::class, 'menuItem')
            ->andWhere('menuItem.menuTitle = :title')
            ->setParameter('title', $title);
        if (!is_null($level)) {
            $q->andWhere('menuItem.level = :level')
                ->setParameter('level', $level);
        }
        return $this->fetchOne($q);
    }
}
