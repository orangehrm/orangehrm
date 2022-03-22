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

namespace OrangeHRM\Tests\Core\Dao;

use OrangeHRM\Config\Config;
use OrangeHRM\Core\Dao\MenuDao;
use OrangeHRM\Entity\MenuItem;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Core
 * @group Dao
 */
class MenuDaoTest extends KernelTestCase
{
    /**
     * @var MenuDao
     */
    private MenuDao $menuDao;

    /**
     * Set up method
     */
    protected function setUp(): void
    {
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmCorePlugin/test/fixtures/MenuDao.yml';
        TestDataService::populate($this->fixture);
        $this->menuDao = new MenuDao();
    }

    public function testEnableModuleMenuItems(): void
    {
        $q = $this->getEntityManager()->createQueryBuilder()
            ->update()
            ->from(MenuItem::class, 'menuItem');
        $q->andWhere($q->expr()->in('menuItem.parent', ':ids'))
            ->setParameter('ids', [12, 13])
            ->set('menuItem.status', ':status')
            ->setParameter('status', false);
        $this->assertEquals(8, $q->getQuery()->execute());

        // Update parents of menu items also
        $count = $this->menuDao->enableModuleMenuItems('leave');
        $this->assertEquals(8, $count);

        $q = $this->getEntityManager()->createQueryBuilder()
            ->select('menuItem')
            ->from(MenuItem::class, 'menuItem')
            ->andWhere('menuItem.status = false');
        $this->assertEquals(0, (new Paginator($q))->count());
        $q->andWhere($q->expr()->in('menuItem.id', ':ids'))
            ->setParameter('ids', [12, 13]);
        $this->assertEquals(0, (new Paginator($q))->count());
    }

    public function testEnableModuleMenuItemsByTitle(): void
    {
        $q = $this->getEntityManager()->createQueryBuilder()
            ->update()
            ->from(MenuItem::class, 'menuItem');
        $q->andWhere($q->expr()->in('menuItem.parent', ':ids'))
            ->setParameter('ids', [12, 13])
            ->set('menuItem.status', ':status')
            ->setParameter('status', false);
        $this->assertEquals(8, $q->getQuery()->execute());

        // Update parents of menu items also
        $count = $this->menuDao->enableModuleMenuItems('leave', ['Leave Types', 'Leave Summary']);
        $this->assertEquals(3, $count);

        $q = $this->getEntityManager()->createQueryBuilder()
            ->select('menuItem')
            ->from(MenuItem::class, 'menuItem')
            ->andWhere('menuItem.status = false');
        $this->assertEquals(5, (new Paginator($q))->count());
        $q->andWhere($q->expr()->in('menuItem.id', ':ids'))
            ->setParameter('ids', [15, 16]);
        $this->assertEquals(0, (new Paginator($q))->count());
    }

    public function testGetMenuLevel(): void
    {
        $menuItem = $this->menuDao->getMenuItemByModuleAndScreen('leave', 'assignLeave');
        $this->assertEquals('Assign Leave', $menuItem->getMenuTitle());
        $this->assertEquals(12, $menuItem->getParent()->getId());
        $this->assertEquals(2, $menuItem->getLevel());
        $this->assertEquals(400, $menuItem->getOrderHint());
        $this->assertEquals(true, $menuItem->getStatus());
    }
}
