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
use OrangeHRM\ORM\Doctrine;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Core
 * @group Dao
 */
class MenuDaoTest extends TestCase
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

    public function testEnableModuleMenuItems()
    {
        $conn = Doctrine::getEntityManager()->getConnection()->getWrappedConnection();
        $statement = $conn->prepare('UPDATE ohrm_menu_item SET status = 0 WHERE parent_id IN (12, 13)');
        $result = $statement->execute();
        $this->assertEquals(8, $result->rowCount());

        // Items with screen_id NULL are not enabled (because they are not linked to a screen and hense to a module)
        $count = $this->menuDao->enableModuleMenuItems('leave');
        $this->assertEquals(7, $count);

        $statement = $conn->prepare(
            'SELECT count(*) FROM ohrm_menu_item WHERE status = 0 AND parent_id IN (12, 13) AND screen_id IS NOT NULL'
        );
        $this->assertEquals(0, $statement->execute()->fetchOne());
    }

    public function testEnableModuleMenuItemsByTitle()
    {
        $conn = Doctrine::getEntityManager()->getConnection()->getWrappedConnection();
        $statement = $conn->prepare('UPDATE ohrm_menu_item SET status = 0 WHERE parent_id IN (12, 13)');
        $this->assertEquals(8, $statement->execute()->rowCount());

        // Items with screen_id NULL are not enabled (because they are not linked to a screen and hense to a module)
        $count = $this->menuDao->enableModuleMenuItems('leave', ['Leave Types', 'Leave Summary']);
        $this->assertEquals(2, $count);

        $statement = $conn->prepare('SELECT count(*) FROM ohrm_menu_item WHERE status = 0 AND id IN (15,16)');

        $count = $statement->execute()->fetchOne();
        $this->assertEquals(0, $count);
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
