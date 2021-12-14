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

namespace OrangeHRM\Tests\Core\Authorization\Dao;

use OrangeHRM\Config\Config;
use OrangeHRM\Core\Authorization\Dao\HomePageDao;
use OrangeHRM\Entity\HomePage;
use OrangeHRM\Entity\Module;
use OrangeHRM\Entity\ModuleDefaultPage;
use OrangeHRM\Entity\UserRole;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * Test class for home page dao
 * @group Core
 * @group Dao
 */
class HomePageDaoTest extends TestCase
{
    private HomePageDao $homePageDao;
    private string $fixture;

    /**
     * Set up method
     */
    protected function setUp(): void
    {
        TestDataService::truncateTables([ModuleDefaultPage::class, HomePage::class, UserRole::class, Module::class]);
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmCorePlugin/test/fixtures/HomePageDao.yml';
        TestDataService::populate($this->fixture);
        $this->homePageDao = new HomePageDao();
    }

    public function testGetHomePagesInPriorityOrderOneRole(): void
    {
        $homePagesFixture = TestDataService::loadFixtures($this->fixture, 'HomePage');
        $expected = [$homePagesFixture[3], $homePagesFixture[2], $homePagesFixture[0]];
        $homePages = $this->homePageDao->getHomePagesInPriorityOrder([1]);
        $this->compareHomePages($expected, $homePages);

        $expected = [$homePagesFixture[1]];
        $homePages = $this->homePageDao->getHomePagesInPriorityOrder([2]);
        $this->compareHomePages($expected, $homePages);

        $expected = [$homePagesFixture[4]];
        $homePages = $this->homePageDao->getHomePagesInPriorityOrder([3]);
        $this->compareHomePages($expected, $homePages);
    }

    public function testGetHomePagesInPriorityOrderMultipleRole(): void
    {
        $homePagesFixture = TestDataService::loadFixtures($this->fixture, 'HomePage');
        $expected = [
            $homePagesFixture[3],
            $homePagesFixture[4],
            $homePagesFixture[2],
            $homePagesFixture[0],
            $homePagesFixture[1]
        ];
        $homePages = $this->homePageDao->getHomePagesInPriorityOrder([1, 2, 3]);
        $this->compareHomePages($expected, $homePages);
    }

    /**
     * Test case for no matching home pages for user role
     */
    public function testGetHomePagesInPriorityOrderNoMatches(): void
    {
        $homePages = $this->homePageDao->getHomePagesInPriorityOrder([4]);
        $this->assertEquals(0, count($homePages));
    }

    /**
     * Test case for no matching home pages for user role
     */
    public function testGetHomePagesInPriorityNoUserRoles(): void
    {
        $homePages = $this->homePageDao->getHomePagesInPriorityOrder([]);
        $this->assertEquals(0, count($homePages));
    }

    protected function compareHomePages($expected, $result): void
    {
        $this->assertEquals(count($expected), count($result));

        for ($i = 0; $i < count($expected); $i++) {
            $exp = $expected[$i];
            $res = $result[$i];

            $this->assertEquals($exp['id'], $res->getId());
            $this->assertEquals($exp['user_role_id'], $res->getUserRole()->getId());
            $this->assertEquals($exp['action'], $res->getAction());
            isset($exp['enable_class']) ?? $this->assertEquals($exp['enable_class'], $res->getEnableClass());
            $this->assertEquals($exp['priority'], $res->getPriority());
        }
    }

    public function testGetModuleDefaultPagesInPriorityOrderOneRole(): void
    {
        $pagesFixture = TestDataService::loadFixtures($this->fixture, 'ModuleDefaultPage');
        $expected = [$pagesFixture[7], $pagesFixture[4]];
        $homePages = $this->homePageDao->getModuleDefaultPagesInPriorityOrder('leave', [1]);
        $this->compareModuleDefaultPages($expected, $homePages);

        $expected = [$pagesFixture[3]];
        $homePages = $this->homePageDao->getModuleDefaultPagesInPriorityOrder('pim', [2]);
        $this->compareModuleDefaultPages($expected, $homePages);

        $expected = [$pagesFixture[5]];
        $homePages = $this->homePageDao->getModuleDefaultPagesInPriorityOrder('leave', [3]);
        $this->compareModuleDefaultPages($expected, $homePages);
    }

    public function testGetModuleDefaultPagesInPriorityOrderMultipleRole(): void
    {
        $pagesFixture = TestDataService::loadFixtures($this->fixture, 'ModuleDefaultPage');
        $expected = [$pagesFixture[7], $pagesFixture[8], $pagesFixture[4], $pagesFixture[5], $pagesFixture[6]];
        $homePages = $this->homePageDao->getModuleDefaultPagesInPriorityOrder('leave', [1, 2, 3]);
        $this->compareModuleDefaultPages($expected, $homePages);
    }

    /**
     * Test case for no matching home pages for user role
     */
    public function testGetModuleDefaultPagesInPriorityOrderNoMatches(): void
    {
        $pagesFixture = $this->homePageDao->getModuleDefaultPagesInPriorityOrder('leave', [4]);
        $this->assertEquals(0, count($pagesFixture));
    }

    /**
     * Test case for no matching home pages for user role
     */
    public function testGetModuleDefaultPagesInPriorityNoUserRoles(): void
    {
        $pagesFixture = $this->homePageDao->getModuleDefaultPagesInPriorityOrder('leave', []);
        $this->assertEquals(0, count($pagesFixture));
    }

    protected function compareModuleDefaultPages($expected, $result): void
    {
        $this->assertEquals(count($expected), count($result));

        for ($i = 0; $i < count($expected); $i++) {
            $exp = $expected[$i];
            $res = $result[$i];

            $this->assertEquals($exp['id'], $res->getId());
            $this->assertEquals($exp['module_id'], $res->getModule()->getId());
            $this->assertEquals($exp['user_role_id'], $res->getUserRole()->getId());
            $this->assertEquals($exp['action'], $res->getAction());
            isset($exp['enable_class']) ?? $this->assertEquals($exp['enable_class'], $res->getEnableClass());
            $this->assertEquals($exp['priority'], $res->getPriority());
        }
    }
}
