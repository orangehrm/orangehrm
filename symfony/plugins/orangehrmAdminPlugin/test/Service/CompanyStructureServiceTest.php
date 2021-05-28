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

namespace OrangeHRM\Tests\Admin\Service;

use OrangeHRM\Admin\Dao\CompanyStructureDao;
use OrangeHRM\Admin\Service\CompanyStructureService;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\Subunit;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Admin
 * @group Service
 */
class CompanyStructureServiceTest extends TestCase
{

    private CompanyStructureService $companyStructureService;
    protected string $fixture;

    /**
     * Set up method
     */
    protected function setUp(): void
    {
        $this->companyStructureService = new CompanyStructureService();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/CompanyStructureDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetSubunitById()
    {
        $subunit = TestDataService::fetchObject(Subunit::class, 1);

        $compStructureDao = $this->getMockBuilder(CompanyStructureDao::class)->getMock();

        $compStructureDao->expects($this->once())
            ->method('getSubunitById')
            ->with($subunit->getId())
            ->will($this->returnValue($subunit));

        $this->companyStructureService->setCompanyStructureDao($compStructureDao);
        $result = $this->companyStructureService->getSubunitById($subunit->getId());
        $this->assertEquals($subunit, $result);
    }

    public function testSaveSubunit(): void
    {
        $subunit = new Subunit();
        $subunit->setName("subunit name");

        $compStructureDao = $this->getMockBuilder(CompanyStructureDao::class)->getMock();

        $compStructureDao->expects($this->once())
            ->method('saveSubunit')
            ->with($subunit)
            ->will($this->returnValue($subunit));

        $this->companyStructureService->setCompanyStructureDao($compStructureDao);
        $result = $this->companyStructureService->saveSubunit($subunit);
        $this->assertTrue($result instanceof Subunit);
    }

    public function testAddSubunit()
    {
        $parentSubunit = TestDataService::fetchObject(Subunit::class, 1);

        $subunit = new Subunit();
        $subunit->setName("new subunit");

        $compStructureDao = $this->getMockBuilder(CompanyStructureDao::class)->getMock();

        $compStructureDao->expects($this->once())
            ->method('addSubunit')
            ->with($parentSubunit, $subunit)
            ->will($this->returnValue($subunit));

        $this->companyStructureService->setCompanyStructureDao($compStructureDao);
        $result = $this->companyStructureService->addSubunit($parentSubunit, $subunit);
        $this->assertTrue($result instanceof Subunit);
    }

    public function testDeleteSubunit()
    {
        $subunit = TestDataService::fetchObject(Subunit::class, 1);

        $parentSubunit = new Subunit();
        $parentSubunit->setName("new subunit");

        $compStructureDao = $this->getMockBuilder(CompanyStructureDao::class)->getMock();

        $compStructureDao->expects($this->once())
            ->method('deleteSubunit')
            ->with($subunit);

        $this->companyStructureService->setCompanyStructureDao($compStructureDao);
        $this->companyStructureService->deleteSubunit($subunit);
    }

    public function testSetOrganizationName()
    {
        $name = "Company Name";
        $returnvalue = 1;

        $compStructureDao = $this->getMockBuilder(CompanyStructureDao::class)->getMock();

        $compStructureDao->expects($this->once())
            ->method('setOrganizationName')
            ->with($name)
            ->will($this->returnValue($returnvalue));

        $this->companyStructureService->setCompanyStructureDao($compStructureDao);
        $result = $this->companyStructureService->setOrganizationName($name);
        $this->assertEquals($returnvalue, $result);
    }

    public function testGetSubunitTreeObject()
    {
        $tree = Subunit::fetchTree();

        $compStructureDao = $this->getMockBuilder(CompanyStructureDao::class)->getMock();

        $compStructureDao->expects($this->once())
            ->method('getSubunitTree')
            ->will($this->returnValue($tree));

        $this->companyStructureService->setCompanyStructureDao($compStructureDao);
        $result = $this->companyStructureService->getSubunitTree();
        $this->assertEquals($tree, $result);
    }
}
