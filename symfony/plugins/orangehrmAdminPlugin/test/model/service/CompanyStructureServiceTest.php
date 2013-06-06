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
class CompanyStructureServiceTest extends PHPUnit_Framework_TestCase {

    private $companyStructureService;
    protected $fixture;

    /**
     * Set up method
     */
    protected function setUp() {
        $this->companyStructureService = new CompanyStructureService();
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmAdminPlugin/test/fixtures/CompanyStructureDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetSubunitById() {

        $subunit = TestDataService::fetchObject('Subunit', 1);

        $compStructureDao = $this->getMock('CompanyStructureDao');

        $compStructureDao->expects($this->once())
                ->method('getSubunitById')
                ->with($subunit->getId())
                ->will($this->returnValue($subunit));

        $this->companyStructureService->setCompanyStructureDao($compStructureDao);
        $result = $this->companyStructureService->getSubunitById($subunit->getId());
        $this->assertEquals($subunit, $result);
    }

    public function testSaveSubunit() {

        $subunit = new Subunit();
        $subunit->setName("subunit name");

        $compStructureDao = $this->getMock('CompanyStructureDao');

        $compStructureDao->expects($this->once())
                ->method('saveSubunit')
                ->with($subunit)
                ->will($this->returnValue(true));

        $this->companyStructureService->setCompanyStructureDao($compStructureDao);
        $result = $this->companyStructureService->saveSubunit($subunit);
        $this->assertTrue($result);
    }
    
    public function testAddSubunit() {

        $subunit = TestDataService::fetchObject('Subunit', 1);

        $parentSubunit = new Subunit();
        $parentSubunit->setName("new subunit");

        $compStructureDao = $this->getMock('CompanyStructureDao');

        $compStructureDao->expects($this->once())
                ->method('addSubunit')
                ->with($parentSubunit, $subunit)
                ->will($this->returnValue(true));

        $this->companyStructureService->setCompanyStructureDao($compStructureDao);
        $result = $this->companyStructureService->addSubunit($parentSubunit, $subunit);
        $this->assertTrue($result);
    }

    public function testDeleteSubunit() {

        $subunit = TestDataService::fetchObject('Subunit', 1);

        $parentSubunit = new Subunit();
        $parentSubunit->setName("new subunit");

        $compStructureDao = $this->getMock('CompanyStructureDao');

        $compStructureDao->expects($this->once())
                ->method('deleteSubunit')
                ->with($subunit)
                ->will($this->returnValue(true));

        $this->companyStructureService->setCompanyStructureDao($compStructureDao);
        $result = $this->companyStructureService->deleteSubunit($subunit);
        $this->assertTrue($result);
    }
    
    public function testSetOrganizationName() {

        $name = "Company Name";
        $returnvalue = 1;

        $compStructureDao = $this->getMock('CompanyStructureDao');

        $compStructureDao->expects($this->once())
                ->method('setOrganizationName')
                ->with($name)
                ->will($this->returnValue($returnvalue));

        $this->companyStructureService->setCompanyStructureDao($compStructureDao);
        $result = $this->companyStructureService->setOrganizationName($name);
        $this->assertEquals($returnvalue, $result);
    }
    
    public function testGetSubunitTreeObject() {

        $treeObject = Doctrine::getTable('Subunit')->getTree();

        $compStructureDao = $this->getMock('CompanyStructureDao');

        $compStructureDao->expects($this->once())
                ->method('getSubunitTreeObject')
                ->will($this->returnValue($treeObject));

        $this->companyStructureService->setCompanyStructureDao($compStructureDao);
        $result = $this->companyStructureService->getSubunitTreeObject();
        $this->assertEquals($treeObject, $result);
    }

}

