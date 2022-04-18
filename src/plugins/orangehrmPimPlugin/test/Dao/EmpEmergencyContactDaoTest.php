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

namespace OrangeHRM\Tests\Pim\Dao;

use InvalidArgumentException;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\EmpEmergencyContact;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Pim\Dao\EmpEmergencyContactDao;
use OrangeHRM\Pim\Dto\EmpEmergencyContactSearchFilterParams;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 * @group Dao
 */
class EmpEmergencyContactDaoTest extends TestCase
{
    use EntityManagerHelperTrait;

    /**
     * @var EmpEmergencyContactDao
     */
    private EmpEmergencyContactDao $employeeEmergencyContactDao;

    protected function setUp(): void
    {
        $this->employeeEmergencyContactDao = new EmpEmergencyContactDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmPimPlugin/test/fixtures/EmpEmergencyContactDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testSaveEmployeeEmergencyContact(): void
    {
        $employee = $this->getEntityReference(Employee::class, 1);
        $empEmergencyContact = new EmpEmergencyContact();
        $empEmergencyContact->setEmployee($employee);
        $empEmergencyContact->setName("Yasitha");
        $empEmergencyContact->setRelationship("friend");
        $empEmergencyContact->setHomePhone("0335445678");
        $empEmergencyContact->setMobilePhone("0776734567");
        $empEmergencyContact->setOfficePhone("0113456787");


        $this->employeeEmergencyContactDao->saveEmployeeEmergencyContact($empEmergencyContact);
        /** @var EmpEmergencyContact $empEmergencyContactObj */
        $empEmergencyContactObj = $this->getRepository(EmpEmergencyContact::class)->findOneBy([
            'employee' => 1,
            'seqNo' => 3
        ]);
        $this->assertEquals("Yasitha", $empEmergencyContactObj->getName());
        $this->assertEquals("friend", $empEmergencyContactObj->getRelationship());
        $this->assertEquals("0335445678", $empEmergencyContactObj->getHomePhone());
        $this->assertEquals("0776734567", $empEmergencyContactObj->getMobilePhone());
        $this->assertEquals("0113456787", $empEmergencyContactObj->getOfficePhone());
        $this->assertEquals(1, $empEmergencyContactObj->getEmployee()->getEmpNumber());
        $this->assertEquals("Kayla", $empEmergencyContactObj->getEmployee()->getFirstName());
    }

    public function testSaveEmployeeEmergencyContactWithSeqNo(): void
    {
        $seqNo = "80";
        $employee = $this->getEntityReference(Employee::class, 1);
        $empEmergencyContact = new EmpEmergencyContact();
        $empEmergencyContact->setEmployee($employee);
        $empEmergencyContact->setSeqNo($seqNo);
        $empEmergencyContact->setName("Yasitha");
        $empEmergencyContact->setRelationship("friend");
        $empEmergencyContact->setHomePhone("0335445678");
        $empEmergencyContact->setMobilePhone("0776734567");
        $empEmergencyContact->setOfficePhone("0113456787");

        $this->employeeEmergencyContactDao->saveEmployeeEmergencyContact($empEmergencyContact);
        /** @var EmpEmergencyContact $empEmergencyContactObj */
        $empEmergencyContactObj = $this->getRepository(EmpEmergencyContact::class)->findOneBy([
            'employee' => 1,
            'seqNo' => $seqNo
        ]);
        $this->assertEquals("Yasitha", $empEmergencyContactObj->getName());
        $this->assertEquals("friend", $empEmergencyContactObj->getRelationship());
        $this->assertEquals("0335445678", $empEmergencyContactObj->getHomePhone());
        $this->assertEquals("0776734567", $empEmergencyContactObj->getMobilePhone());
        $this->assertEquals("0113456787", $empEmergencyContactObj->getOfficePhone());
        $this->assertEquals(1, $empEmergencyContactObj->getEmployee()->getEmpNumber());
        $this->assertEquals("Kayla", $empEmergencyContactObj->getEmployee()->getFirstName());
    }

    public function testSaveEmployeeEmergencyContactWithInvalidSeqNo(): void
    {
        $seqNo = "100";
        $employee = $this->getEntityReference(Employee::class, 1);
        $empEmergencyContact = new EmpEmergencyContact();
        $empEmergencyContact->setEmployee($employee);
        $empEmergencyContact->setSeqNo($seqNo);
        $empEmergencyContact->setName("Yasitha");
        $empEmergencyContact->setRelationship("friend");
        $empEmergencyContact->setHomePhone("0335445678");
        $empEmergencyContact->setMobilePhone("0776734567");
        $empEmergencyContact->setOfficePhone("0113456787");

        $this->expectException(InvalidArgumentException::class);
        $this->employeeEmergencyContactDao->saveEmployeeEmergencyContact($empEmergencyContact);
    }

    public function testGetEmployeeEmergencyContactList(): void
    {
        $empEmergencyContact = $this->employeeEmergencyContactDao->getEmployeeEmergencyContactList(1);
        $this->assertCount(2, $empEmergencyContact);
        $this->assertEquals('Rashmi', $empEmergencyContact[0]->getName());
        $this->assertEquals('Yasitha', $empEmergencyContact[1]->getName());
    }

    public function testGetEmployeeEmergencyContact(): void
    {
        $empEmergencyContact = $this->employeeEmergencyContactDao->getEmployeeEmergencyContact(1, 1);
        $this->assertEquals('Yasitha', $empEmergencyContact->getName());

        $empEmergencyContact = $this->employeeEmergencyContactDao->getEmployeeEmergencyContact(1, 5);
        $this->assertNull($empEmergencyContact);
    }

    public function testDeleteEmployeeEmergencyContacts(): void
    {
        $rows = $this->employeeEmergencyContactDao->deleteEmployeeEmergencyContacts(1, [1, 2]);
        $this->assertEquals(2, $rows);

        $empEmergencyContactObj = $this->getRepository(EmpEmergencyContact::class)->findOneBy([
            'employee' => 1,
            'seqNo' => 1
        ]);
        $this->assertNull($empEmergencyContactObj);
        $empEmergencyContactObj = $this->getRepository(EmpEmergencyContact::class)->findOneBy([
            'employee' => 1,
            'seqNo' => 2
        ]);
        $this->assertNull($empEmergencyContactObj);
    }

    public function testSearchEmployeeEmergencyContacts(): void
    {
        // search empNumber = 1
        $emergencyContactSearchParams = new EmpEmergencyContactSearchFilterParams();
        $emergencyContactSearchParams->setEmpNumber(1);
        $result = $this->employeeEmergencyContactDao->searchEmployeeEmergencyContacts($emergencyContactSearchParams);
        $this->assertCount(2, $result);
        $this->assertTrue($result[0] instanceof EmpEmergencyContact);

        // search empNumber = 1 and name = Yasitha
        $emergencyContactSearchParams->setEmpNumber(1);
        $emergencyContactSearchParams->setName('Yasitha');
        $result = $this->employeeEmergencyContactDao->searchEmployeeEmergencyContacts($emergencyContactSearchParams);
        $this->assertCount(1, $result);
        $this->assertTrue($result[0] instanceof EmpEmergencyContact);
    }

    public function testSearchEmployeeEmergencyContactsWithLimit(): void
    {
        $emergencyContactSearchParams = new EmpEmergencyContactSearchFilterParams();
        $emergencyContactSearchParams->setLimit(1);
        $emergencyContactSearchParams->setEmpNumber(1);
        $result = $this->employeeEmergencyContactDao->searchEmployeeEmergencyContacts($emergencyContactSearchParams);
        $this->assertCount(1, $result);
    }

    public function testGetSearchEmployeeEmergencyContactsCount(): void
    {
        $emergencyContactSearchParams = new EmpEmergencyContactSearchFilterParams();
        $emergencyContactSearchParams->setEmpNumber(1);
        $result = $this->employeeEmergencyContactDao->getSearchEmployeeEmergencyContactsCount($emergencyContactSearchParams);
        $this->assertEquals(2, $result);
    }
}
