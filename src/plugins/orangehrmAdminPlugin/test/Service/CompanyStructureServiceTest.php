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
use OrangeHRM\Core\Service\NormalizerService;
use OrangeHRM\Entity\Subunit;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Admin
 * @group Service
 */
class CompanyStructureServiceTest extends KernelTestCase
{
    private CompanyStructureService $companyStructureService;
    protected string $fixture;

    /**
     * Set up method
     */
    protected function setUp(): void
    {
        $this->companyStructureService = new CompanyStructureService();
        $this->fixture = Config::get(Config::PLUGINS_DIR) .
            '/orangehrmAdminPlugin/test/fixtures/CompanyStructureDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetSubunitById(): void
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
        $subunit->setName('subunit name');

        $compStructureDao = $this->getMockBuilder(CompanyStructureDao::class)->getMock();

        $compStructureDao->expects($this->once())
            ->method('saveSubunit')
            ->with($subunit)
            ->will($this->returnValue($subunit));

        $this->companyStructureService->setCompanyStructureDao($compStructureDao);
        $result = $this->companyStructureService->saveSubunit($subunit);
        $this->assertTrue($result instanceof Subunit);
    }

    public function testAddSubunit(): void
    {
        $parentSubunit = TestDataService::fetchObject(Subunit::class, 1);

        $subunit = new Subunit();
        $subunit->setName('new subunit');

        $compStructureDao = $this->getMockBuilder(CompanyStructureDao::class)->getMock();

        $compStructureDao->expects($this->once())
            ->method('addSubunit')
            ->with($parentSubunit, $subunit)
            ->will($this->returnValue($subunit));

        $this->companyStructureService->setCompanyStructureDao($compStructureDao);
        $this->companyStructureService->addSubunit($parentSubunit, $subunit);
    }

    public function testDeleteSubunit(): void
    {
        $subunit = TestDataService::fetchObject(Subunit::class, 1);

        $parentSubunit = new Subunit();
        $parentSubunit->setName('new subunit');

        $compStructureDao = $this->getMockBuilder(CompanyStructureDao::class)->getMock();

        $compStructureDao->expects($this->once())
            ->method('deleteSubunit')
            ->with($subunit);

        $this->companyStructureService->setCompanyStructureDao($compStructureDao);
        $this->companyStructureService->deleteSubunit($subunit);
    }

    public function testSetOrganizationName(): void
    {
        $name = 'Company Name';
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

    public function testGetSubunitTreeObject(): void
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

    public function testGetSubunitChainById(): void
    {
        $subunit = $this->companyStructureService->getSubunitById(1);
        $newSubunit4 = new Subunit();
        $newSubunit4->setName('Department 2'); // id: 4
        $this->companyStructureService->addSubunit($subunit, $newSubunit4);

        $newSubunit5 = new Subunit();
        $newSubunit5->setName('Department 3'); // id: 5
        $this->companyStructureService->addSubunit($subunit, $newSubunit5);

        $newSubunit5 = $this->companyStructureService->getSubunitById(5);

        $newSubunit6 = new Subunit();
        $newSubunit6->setName('Department 3 - Sub 1'); // id: 6
        $this->companyStructureService->addSubunit($newSubunit5, $newSubunit6);

        $newSubunit7 = new Subunit();
        $newSubunit7->setName('Department 3 - Sub 2'); // id: 7
        $this->companyStructureService->addSubunit($newSubunit5, $newSubunit7);

        $newSubunit8 = new Subunit();
        $newSubunit8->setName('Department 3 - Sub 3'); // id: 8
        $this->companyStructureService->addSubunit($newSubunit5, $newSubunit8);

        $newSubunit7 = $this->companyStructureService->getSubunitById(7);

        $newSubunit9 = new Subunit();
        $newSubunit9->setName('Department 3 - Sub 2 - Sub1'); // id: 9
        $this->companyStructureService->addSubunit($newSubunit7, $newSubunit9);

        $newSubunit10 = new Subunit();
        $newSubunit10->setName('Department 3 - Sub 2 - Sub2'); // id: 10
        $this->companyStructureService->addSubunit($newSubunit7, $newSubunit10);

        $newSubunit11 = new Subunit();
        $newSubunit11->setName('Department 3 - Sub 2 - Sub3'); // id: 11
        $this->companyStructureService->addSubunit($newSubunit7, $newSubunit11);

        $newSubunit9 = $this->companyStructureService->getSubunitById(9);

        $newSubunit12 = new Subunit();
        $newSubunit12->setName('Department 3 - Sub 2 - Sub1 - Child 1'); // id: 12
        $this->companyStructureService->addSubunit($newSubunit9, $newSubunit12);

        $newSubunit11 = $this->companyStructureService->getSubunitById(11);

        $newSubunit13 = new Subunit();
        $newSubunit13->setName('Department 3 - Sub 2 - Sub3 - Child 1'); // id: 13
        $this->companyStructureService->addSubunit($newSubunit11, $newSubunit13);

        $this->createKernelWithMockServices(
            [
                Services::NORMALIZER_SERVICE => new NormalizerService(),
            ]
        );

        $result = $this->companyStructureService->getSubunitChainById(1);
        sort($result);
        $this->assertEquals([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13], $result);

        $result = $this->companyStructureService->getSubunitChainById(2);
        sort($result);
        $this->assertEquals([2, 3], $result);

        $result = $this->companyStructureService->getSubunitChainById(3);
        $this->assertEquals([3], $result);

        $result = $this->companyStructureService->getSubunitChainById(4);
        $this->assertEquals([4], $result);

        $result = $this->companyStructureService->getSubunitChainById(5);
        sort($result);
        $this->assertEquals([5, 6, 7, 8, 9, 10, 11, 12, 13], $result);

        $result = $this->companyStructureService->getSubunitChainById(6);
        $this->assertEquals([6], $result);

        $result = $this->companyStructureService->getSubunitChainById(7);
        sort($result);
        $this->assertEquals([7, 9, 10, 11, 12, 13], $result);

        $result = $this->companyStructureService->getSubunitChainById(8);
        $this->assertEquals([8], $result);

        $result = $this->companyStructureService->getSubunitChainById(9);
        sort($result);
        $this->assertEquals([9, 12], $result);

        $result = $this->companyStructureService->getSubunitChainById(10);
        $this->assertEquals([10], $result);

        $result = $this->companyStructureService->getSubunitChainById(11);
        sort($result);
        $this->assertEquals([11, 13], $result);

        $result = $this->companyStructureService->getSubunitChainById(12);
        $this->assertEquals([12], $result);

        $result = $this->companyStructureService->getSubunitChainById(13);
        $this->assertEquals([13], $result);
    }
}
