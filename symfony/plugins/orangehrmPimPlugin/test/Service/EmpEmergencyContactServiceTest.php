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

namespace OrangeHRM\Tests\Pim\Service;

use OrangeHRM\Config\Config;
use OrangeHRM\Entity\EmpEmergencyContact;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Pim\Dao\EmpEmergencyContactDao;
use OrangeHRM\Pim\Dto\EmpEmergencyContactSearchFilterParams;
use OrangeHRM\Pim\Service\EmpEmergencyContactService;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 * @group Service
 */
class EmpEmergencyContactServiceTest extends TestCase
{
    private EmpEmergencyContactService $empEmergencyContactService;
    protected string $fixture;

    protected function setUp(): void
    {
        $this->empEmergencyContactService = new EmpEmergencyContactService();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmPimPlugin/test/fixtures/EmpEmergencyContactDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetEmployeeEmergencyContactList(): void
    {
        $empEmergencyContact1 = new EmpEmergencyContact();
        $empEmergencyContact1->getDecorator()->setEmployeeByEmpNumber(1);
        $empEmergencyContact1->setName('Yasitha');
        $empEmergencyContact1->SetRelationship('friend');
        $empEmergencyContact1->setHomePhone('0335445678');
        $empEmergencyContact1->setMobilePhone("0776734567");
        $empEmergencyContact1->setOfficePhone("0113456787");

        $empEmergencyContact2 = new EmpEmergencyContact();
        $empEmergencyContact2->getDecorator()->setEmployeeByEmpNumber(1);
        $empEmergencyContact2->setName('Rashmi');
        $empEmergencyContact2->setRelationship('friend');
        $empEmergencyContact2->setHomePhone('0335445678');
        $empEmergencyContact2->setMobilePhone("0776734567");
        $empEmergencyContact2->setOfficePhone("0113456787");

        $empEmergencyContactDao = $this->getMockBuilder(EmpEmergencyContactDao::class)->getMock();

        $empEmergencyContactDao->expects($this->once())
            ->method('getEmployeeEmergencyContactList')
            ->with(1)
            ->will($this->returnValue([$empEmergencyContact1, $empEmergencyContact2]));

        $this->empEmergencyContactService->setEmpEmergencyContactDao($empEmergencyContactDao);
        $empEmergencyContacts = $this->empEmergencyContactService->getEmployeeEmergencyContactList(1);
        $this->assertCount(2, $empEmergencyContacts);
        $this->assertEquals('Yasitha', $empEmergencyContacts[0]->getName());
        $this->assertEquals('Rashmi', $empEmergencyContacts[1]->getName());
    }

    public function testGetEmployeeEmergencyContact(): void
    {
        $empEmergencyContact1 = new EmpEmergencyContact();
        $empEmergencyContact1->getDecorator()->setEmployeeByEmpNumber(1);
        $empEmergencyContact1->setName('Yasitha');
        $empEmergencyContact1->setRelationship('friend');
        $empEmergencyContact1->setHomePhone('0335445678');
        $empEmergencyContact1->setMobilePhone("0776734567");
        $empEmergencyContact1->setOfficePhone("0113456787");

        $empEmergencyContactDao = $this->getMockBuilder(EmpEmergencyContactDao::class)->getMock();


        $empEmergencyContactDao->expects($this->once())
            ->method('getEmployeeEmergencyContact')
            ->with(1, 1)
            ->will($this->returnValue($empEmergencyContact1));

        $this->empEmergencyContactService->setEmpEmergencyContactDao($empEmergencyContactDao);
        $empEmergencyContact = $this->empEmergencyContactService->getEmployeeEmergencyContact(1, 1);
        $this->assertEquals('Yasitha', $empEmergencyContact->getName());
    }

    public function testSaveEmpEmergencyContact(): void
    {
        $employee = $this->getEntityReference(Employee::class, 1);
        $empEmergencyContact = new EmpEmergencyContact();
        $empEmergencyContact->setEmployee($employee);
        $empEmergencyContact->setName("shashi");
        $empEmergencyContact->setRelationship("sister");
        $empEmergencyContact->setHomePhone('0335445678');
        $empEmergencyContact->setMobilePhone("0776734567");
        $empEmergencyContact->setOfficePhone("0113456787");

        $empEmergencyContactDao = $this->getMockBuilder(EmpEmergencyContactDao::class)->getMock();

        $empEmergencyContactDao->expects($this->once())
            ->method('saveEmployeeEmergencyContact')
            ->with($empEmergencyContact)
            ->will($this->returnValue($empEmergencyContact));

        $this->empEmergencyContactService->setEmpEmergencyContactDao($empEmergencyContactDao);

        $empEmergencyContactObj = $this->empEmergencyContactService->saveEmpEmergencyContact($empEmergencyContact);
        $this->assertEquals("shashi", $empEmergencyContactObj->getName());
        $this->assertEquals("sister", $empEmergencyContactObj->getRelationship());
        $this->assertEquals("0335445678", $empEmergencyContactObj->getHomePhone());
        $this->assertEquals("0776734567", $empEmergencyContactObj->getMobilePhone());
        $this->assertEquals("0113456787", $empEmergencyContactObj->getOfficePhone());
        $this->assertEquals(1, $empEmergencyContactObj->getEmployee()->getEmpNumber());
        $this->assertEquals("Kayla", $empEmergencyContactObj->getEmployee()->getFirstName());
    }

    public function testDeleteEmployeeEmergencyContacts(): void
    {
        $empEmergencyContactDao = $this->getMockBuilder(EmpEmergencyContactDao::class)->getMock();

        $empEmergencyContactDao->expects($this->once())
            ->method('deleteEmployeeEmergencyContacts')
            ->with(1, [1, 2])
            ->will($this->returnValue(2));

        $this->empEmergencyContactService->setEmpEmergencyContactDao($empEmergencyContactDao);

        $rows = $this->empEmergencyContactService->deleteEmployeeEmergencyContacts(1, [1, 2]);
        $this->assertEquals(2, $rows);
    }

    public function testSearchEmployeeEmergencyContacts(): void
    {
        $empEmergencyContact1 = new EmpEmergencyContact();
        $empEmergencyContact1->getDecorator()->setEmployeeByEmpNumber(1);
        $empEmergencyContact1->setName('Yasitha');

        $empEmergencyContact2 = new EmpEmergencyContact();
        $empEmergencyContact2->getDecorator()->setEmployeeByEmpNumber(1);
        $empEmergencyContact2->setName('Rashmi');


        $empEmergencyContactList = [$empEmergencyContact1, $empEmergencyContact2];
        $empEmergencyContactSearchParams = new EmpEmergencyContactSearchFilterParams();
        $empEmergencyContactSearchParams->setEmpNumber(1);
        $empEmergencyContactDao = $this->getMockBuilder(EmpEmergencyContactDao::class)->getMock();

        $empEmergencyContactDao->expects($this->once())
            ->method('searchEmployeeEmergencyContacts')
            ->with($empEmergencyContactSearchParams)
            ->will($this->returnValue($empEmergencyContactList));

        $this->empEmergencyContactService->setEmpEmergencyContactDao($empEmergencyContactDao);
        $result = $this->empEmergencyContactService->searchEmployeeEmergencyContacts($empEmergencyContactSearchParams);
        $this->assertCount(2, $result);
        $this->assertTrue($result[0] instanceof EmpEmergencyContact);
    }

    public function testGetSearchEmployeeEmergencyContactsCount(): void
    {
        $empEmergencyContactSearchParams = new EmpEmergencyContactSearchFilterParams();
        $empEmergencyContactSearchParams->setEmpNumber(1);
        $empEmergencyContactDao = $this->getMockBuilder(EmpEmergencyContactDao::class)->getMock();

        $empEmergencyContactDao->expects($this->once())
            ->method('getSearchEmployeeEmergencyContactsCount')
            ->with($empEmergencyContactSearchParams)
            ->will($this->returnValue(2));
        $this->empEmergencyContactService->setEmpEmergencyContactDao($empEmergencyContactDao);
        $result = $this->empEmergencyContactService->getSearchEmployeeEmergencyContactsCount($empEmergencyContactSearchParams);
        $this->assertEquals(2, $result);
    }
}
