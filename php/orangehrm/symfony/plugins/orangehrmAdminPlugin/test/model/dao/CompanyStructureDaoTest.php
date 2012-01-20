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
require_once sfConfig::get('sf_test_dir') . '/util/TestDataService.php';

/**
 * @group Admin
 */
class CompanyStructureDaoTest extends PHPUnit_Framework_TestCase {

    private $companyStructureDao;
    protected $fixture;

    /**
     * Set up method
     */
    protected function setUp() {

        $this->companyStructureDao = new CompanyStructureDao();
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmAdminPlugin/test/fixtures/CompanyStructureDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testSetOrganizationName() {
        $this->assertEquals($this->companyStructureDao->setOrganizationName("OrangeHRM"), 1);
    }

    public function testGetSubunitById() {
        $savedSubunit = $this->companyStructureDao->getSubunitById(1);
        $this->assertTrue($savedSubunit instanceof Subunit);
        $this->assertEquals($savedSubunit->getId(), 1);
        $this->assertEquals($savedSubunit->getName(), 'Organization');
    }

    public function testSaveSubunit() {
        $subunit = new Subunit();
        $subunit->setName("Open Source");
        $subunit->setDescription("Handles OrangeHRM product");
        $this->assertTrue($this->companyStructureDao->saveSubunit($subunit));
        $this->assertNotNull($subunit->getId());
    }

    public function testDeleteSubunit() {
        $subunitList = TestDataService::loadObjectList('Subunit', $this->fixture, 'Subunit');
        $subunit = $subunitList[2];
        $this->assertTrue($this->companyStructureDao->deleteSubunit($subunit));
        $result = TestDataService::fetchObject('Subunit', 3);
        $this->assertFalse($result);
    }

    public function testAddSubunit() {
        $subunitList = TestDataService::loadObjectList('Subunit', $this->fixture, 'Subunit');
        $subunit = $subunitList[2];
        $parentSubunit = new Subunit();
        $parentSubunit->setName("New Department");
        $this->assertTrue($this->companyStructureDao->addSubunit($parentSubunit, $subunit));
        $this->assertNotNull($parentSubunit->getId());
    }

    public function testGetSubunitTreeObject() {
        $treeObject = $this->companyStructureDao->getSubunitTreeObject();
        $tree = $treeObject->fetchTree();
        $this->assertNotNull($tree[0]->getLevel());
        $this->assertNotNull($tree[0]->getRgt());
        $this->assertNotNull($tree[0]->getLft());
    }

}

