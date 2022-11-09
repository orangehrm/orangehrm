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
use OrangeHRM\Entity\DataGroup;
use OrangeHRM\Entity\User;
use OrangeHRM\ORM\Doctrine;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

use function count;

/**
 * Description of DataGroupDaoTest
 * @group Core
 * @group Dao
 */
class DataGroupDaoTest extends TestCase
{
    /**
     * @var DataGroupDao
     */
    private DataGroupDao $dao;

    /**
     * Set up method
     */
    protected function setUp(): void
    {
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmCorePlugin/test/fixtures/DataGroupDao.yml';
        TestDataService::truncateSpecificTables([User::class]);
        TestDataService::populate($this->fixture);

        $this->dao = new DataGroupDao();
    }

    public function testGetDataGroupPermission(): void
    {
        $permissions = $this->dao->getDataGroupPermission('pim_1', 1);
        $this->assertEquals(1, count($permissions));
        $this->assertEquals(1, $permissions[0]->canRead());
    }

    public function testGetDataGroups(): void
    {
        $this->assertEquals(4, sizeof($this->dao->getDataGroups()));
    }

    public function testGetDataGroupsNoneDefined(): void
    {
        $pdo = Doctrine::getEntityManager()->getConnection();
        $pdo->executeStatement('DELETE FROM ohrm_data_group');
        $this->assertEquals(0, sizeof($this->dao->getDataGroups()));
    }


    public function testGetDataGroup(): void
    {
        $dataGroup1 = $this->dao->getDataGroup('pim_1');
        $this->assertTrue($dataGroup1 instanceof DataGroup);
        $this->assertEquals(1, $dataGroup1->getId());

        $dataGroup2 = $this->dao->getDataGroup('pim_2');
        $this->assertTrue($dataGroup2 instanceof DataGroup);
        $this->assertEquals(2, $dataGroup2->getId());
    }

    public function testGetDataGroupInvalid(): void
    {
        $dataGroup = $this->dao->getDataGroup('xyz_not_exist');
        $this->assertNull($dataGroup);
    }
}
