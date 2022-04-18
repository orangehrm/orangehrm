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
use OrangeHRM\Core\Authorization\Dao\DataGroupDao;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Core
 * @group Dao
 */
class DataGroupDaoGetApiPermissionsTest extends TestCase
{
    /**
     * @var DataGroupDao
     */
    private DataGroupDao $dao;

    protected function setUp(): void
    {
        $this->fixture = Config::get(Config::PLUGINS_DIR)
            . '/orangehrmCorePlugin/test/fixtures/DataGroupDaoGetApiPermissions.yml';
        TestDataService::populate($this->fixture);

        $this->dao = new DataGroupDao();
    }

    public function testGetApiPermissionsForEss(): void
    {
        $permissions = $this->dao->getApiPermissions('OrangeHRM\Admin\Api\JobTitleAPI', ['ESS']);

        $this->assertCount(1, $permissions);
        $this->assertTrue($permissions[0]->canRead());
        $this->assertFalse($permissions[0]->canCreate());
        $this->assertFalse($permissions[0]->canUpdate());
        $this->assertFalse($permissions[0]->canDelete());
        $this->assertFalse($permissions[0]->isSelf());
    }

    public function testGetApiPermissionsForEssAndAdmin(): void
    {
        $permissions = $this->dao->getApiPermissions('OrangeHRM\Admin\Api\JobTitleAPI', ['ESS', 'Admin']);

        $this->assertCount(2, $permissions);
        $this->assertTrue($permissions[0]->canRead());
        $this->assertTrue($permissions[0]->canCreate());
        $this->assertFalse($permissions[0]->canUpdate());
        $this->assertFalse($permissions[0]->canDelete());
        $this->assertFalse($permissions[0]->isSelf());

        $this->assertTrue($permissions[1]->canRead());
        $this->assertFalse($permissions[1]->canCreate());
        $this->assertFalse($permissions[1]->canUpdate());
        $this->assertFalse($permissions[1]->canDelete());
        $this->assertFalse($permissions[1]->isSelf());
    }
}
