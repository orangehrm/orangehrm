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

namespace OrangeHRM\Tests\Admin\Dao;

use OrangeHRM\Admin\Dao\CompanyStructureDao;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\Subunit;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Admin
 * @group Dao
 */
class CompanyStructureDaoTest extends TestCase
{
    private CompanyStructureDao $companyStructureDao;
    protected string $fixture;

    /**
     * Set up method
     */
    protected function setUp(): void
    {
        $this->companyStructureDao = new CompanyStructureDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) .
            '/orangehrmAdminPlugin/test/fixtures/CompanyStructureDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testSetOrganizationName(): void
    {
        $this->assertEquals($this->companyStructureDao->setOrganizationName("OrangeHRM"), 1);
    }

    public function testGetSubunitById(): void
    {
        $savedSubunit = $this->companyStructureDao->getSubunitById(1);
        $this->assertTrue($savedSubunit instanceof Subunit);
        $this->assertEquals($savedSubunit->getId(), 1);
        $this->assertEquals($savedSubunit->getName(), 'Organization');
    }

    public function testSaveSubunit(): void
    {
        $subunit = new Subunit();
        $subunit->setName("Open Source");
        $subunit->setDescription("Handles OrangeHRM product");
        $this->assertTrue($this->companyStructureDao->saveSubunit($subunit) instanceof Subunit);
        $this->assertNotNull($subunit->getId());
    }

    public function testDeleteSubunit(): void
    {
        $subunitList = TestDataService::loadObjectList(Subunit::class, $this->fixture, 'Subunit');
        $subunit = $subunitList[2];
        $this->companyStructureDao->deleteSubunit($subunit);

        $subUnit1 = new Subunit();
        $subUnit1->setId(1);
        $subUnit1->setName('Organization');
        $subUnit1->setUnitId('company');
        $subUnit1->setDescription('company description');
        $subUnit1->setLft(1);
        $subUnit1->setRgt(4);
        $subUnit1->setLevel(0);

        $subUnit2 = new Subunit();
        $subUnit2->setId(2);
        $subUnit2->setName('Department 1');
        $subUnit2->setUnitId('dept');
        $subUnit2->setDescription('department description');
        $subUnit2->setLft(2);
        $subUnit2->setRgt(3);
        $subUnit2->setLevel(1);

        $subUnits = [$subUnit1, $subUnit2];
        $this->assertEquals($subUnits, Subunit::fetchTree());
        $result = TestDataService::fetchObject(Subunit::class, 3);
        $this->assertNull($result);
    }

    public function testAddSubunit(): void
    {
        $subunitList = TestDataService::loadObjectList(Subunit::class, $this->fixture, 'Subunit');
        $parentSubunit = $subunitList[2];
        $subunit = new Subunit();
        $subunit->setName("New Department");
        $this->companyStructureDao->addSubunit($parentSubunit, $subunit);

        $subUnit1 = new Subunit();
        $subUnit1->setId(1);
        $subUnit1->setName('Organization');
        $subUnit1->setUnitId('company');
        $subUnit1->setDescription('company description');
        $subUnit1->setLft(1);
        $subUnit1->setRgt(8);
        $subUnit1->setLevel(0);

        $subUnit2 = new Subunit();
        $subUnit2->setId(2);
        $subUnit2->setName('Department 1');
        $subUnit2->setUnitId('dept');
        $subUnit2->setDescription('department description');
        $subUnit2->setLft(2);
        $subUnit2->setRgt(7);
        $subUnit2->setLevel(1);

        $subUnit3 = new Subunit();
        $subUnit3->setId(3);
        $subUnit3->setName('Sub Department 1');
        $subUnit3->setUnitId('sub dept');
        $subUnit3->setDescription('sub department description');
        $subUnit3->setLft(3);
        $subUnit3->setRgt(6);
        $subUnit3->setLevel(2);

        $tempSubUnit4 = TestDataService::fetchLastInsertedRecord(Subunit::class, 'id', false);
        $subUnit4 = new Subunit();
        $subUnit4->setId($tempSubUnit4->getId());
        $subUnit4->setName('New Department');
        $subUnit4->setLft(4);
        $subUnit4->setRgt(5);
        $subUnit4->setLevel(3);

        $subUnits = [$subUnit1, $subUnit2, $subUnit3, $subUnit4];

        $this->assertEquals($subUnits, Subunit::fetchTree());
        $this->assertNotNull($subunit->getId());
    }

    public function testGetSubunitTreeObject(): void
    {
        $tree = $this->companyStructureDao->getSubunitTree();
        $this->assertNotNull($tree[0]->getLevel());
        $this->assertNotNull($tree[0]->getRgt());
        $this->assertNotNull($tree[0]->getLft());
    }
}
