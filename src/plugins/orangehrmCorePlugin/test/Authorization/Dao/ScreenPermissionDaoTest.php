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
use OrangeHRM\Core\Authorization\Dao\ScreenPermissionDao;
use OrangeHRM\Entity\User;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * Description of ScreenPermissionDaoTest
 * @group Core
 * @group Dao
 */
class ScreenPermissionDaoTest extends TestCase
{
    /**
     * @var ScreenPermissionDao
     */
    private ScreenPermissionDao $dao;

    /**
     * Set up method
     */
    protected function setUp(): void
    {
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmCorePlugin/test/fixtures/ScreenPermissionDao.yml';
        TestDataService::truncateSpecificTables([User::class]);
        TestDataService::populate($this->fixture);

        $this->dao = new ScreenPermissionDao();
    }

    public function testGetScreenPermission(): void
    {
        $permissions = $this->dao->getScreenPermissions('pim', 'viewEmployeeList', ['Admin']);
        $this->assertNotNull($permissions);
        $this->assertEquals(1, count($permissions));
        $this->verifyPermissions($permissions[0], true, true, true, true);


        $permissions = $this->dao->getScreenPermissions('pim', 'viewEmployeeList', ['ESS']);
        $this->assertNotNull($permissions);
        $this->assertEquals(1, count($permissions));
        $this->verifyPermissions($permissions[0], false, false, false, false);

        $permissions = $this->dao->getScreenPermissions('pim', 'viewEmployeeList', ['Supervisor']);
        $this->assertNotNull($permissions);
        $this->assertEquals(1, count($permissions));
        $this->verifyPermissions($permissions[0], true, false, true, false);

        $permissions = $this->dao->getScreenPermissions('pim', 'viewEmployeeList', ['Admin', 'Supervisor', 'ESS']);
        $this->assertNotNull($permissions);
        $this->assertEquals(3, count($permissions));

        foreach ($permissions as $permission) {
            $roleId = $permission->getUserRole()->getId();
            if ($roleId == 1) {
                // Admin
                $this->verifyPermissions($permission, true, true, true, true);
            } else {
                if ($roleId == 2) {
                    // Supervisor
                    $this->verifyPermissions($permission, false, false, false, false);
                } else {
                    if ($roleId == 3) {
                        // ESS
                        $this->verifyPermissions($permission, true, false, true, false);
                    } else {
                        $this->fail("Unexpected roleId=" . $roleId);
                    }
                }
            }
        }

        $permissions = $this->dao->getScreenPermissions(
            'pim',
            'viewEmployeeListNoneExisting',
            ['Admin', 'Supervisor', 'ESS']
        );
        $this->assertTrue(is_array($permissions));
        $this->assertEquals(0, count($permissions));
    }

    protected function verifyPermissions($permission, $read, $create, $update, $delete): void
    {
        $this->assertEquals($read, $permission->canRead());
        $this->assertEquals($create, $permission->canCreate());
        $this->assertEquals($update, $permission->canUpdate());
        $this->assertEquals($delete, $permission->canDelete());
    }
}
