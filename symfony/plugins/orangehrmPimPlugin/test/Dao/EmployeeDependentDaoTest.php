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

namespace OrangeHRM\Pim\Tests\Dao;

use DateTime;
use InvalidArgumentException;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\EmpDependent;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Pim\Dao\EmployeeDependentDao;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 * @group Dao
 */
class EmployeeDependentDaoTest extends TestCase
{
    use EntityManagerHelperTrait;

    private EmployeeDependentDao $employeeDependentDao;

    protected function setUp(): void
    {
        $this->employeeDependentDao = new EmployeeDependentDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmPimPlugin/test/fixtures/EmpDependentDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testSaveEmployeeDependent(): void
    {
        $employee = $this->getEntityReference(Employee::class, 1);
        $empDependent = new EmpDependent();
        $empDependent->setEmployee($employee);
        $empDependent->setName("Barker");
        $empDependent->setRelationshipType(EmpDependent::RELATIONSHIP_TYPE_CHILD);

        $this->employeeDependentDao->saveEmployeeDependent($empDependent);
        /** @var EmpDependent $empDependentObj */
        $empDependentObj = $this->getRepository(EmpDependent::class)->findOneBy(['employee' => 1, 'seqNo' => 3]);
        $this->assertEquals("Barker", $empDependentObj->getName());
        $this->assertEquals("child", $empDependentObj->getRelationshipType());
        $this->assertEquals("", $empDependentObj->getRelationship());
        $this->assertNull($empDependentObj->getDateOfBirth());
        $this->assertEquals(1, $empDependentObj->getEmployee()->getEmpNumber());
        $this->assertEquals("Kayla", $empDependentObj->getEmployee()->getFirstName());
    }

    public function testSaveEmployeeDependentWithSeqNo(): void
    {
        $seqNo = "80";
        $employee = $this->getEntityReference(Employee::class, 1);
        $empDependent = new EmpDependent();
        $empDependent->setEmployee($employee);
        $empDependent->setSeqNo($seqNo);
        $empDependent->setName("Barker");
        $empDependent->setRelationshipType(EmpDependent::RELATIONSHIP_TYPE_OTHER);
        $empDependent->setRelationship("friend");
        $empDependent->setDateOfBirth(new DateTime("2000-01-01"));

        $this->employeeDependentDao->saveEmployeeDependent($empDependent);
        /** @var EmpDependent $empDependentObj */
        $empDependentObj = $this->getRepository(EmpDependent::class)->findOneBy(['employee' => 1, 'seqNo' => $seqNo]);
        $this->assertEquals("Barker", $empDependentObj->getName());
        $this->assertEquals("other", $empDependentObj->getRelationshipType());
        $this->assertEquals("friend", $empDependentObj->getRelationship());
        $this->assertEquals("2000-01-01", $empDependentObj->getDateOfBirth()->format("Y-m-d"));
        $this->assertEquals(1, $empDependentObj->getEmployee()->getEmpNumber());
        $this->assertEquals("Kayla", $empDependentObj->getEmployee()->getFirstName());
    }

    public function testSaveEmployeeDependentWithInvalidSeqNo(): void
    {
        $seqNo = "100";
        $employee = $this->getEntityReference(Employee::class, 1);
        $empDependent = new EmpDependent();
        $empDependent->setEmployee($employee);
        $empDependent->setSeqNo($seqNo);
        $empDependent->setName("Barker");
        $empDependent->setRelationshipType(EmpDependent::RELATIONSHIP_TYPE_OTHER);

        $this->expectException(InvalidArgumentException::class);
        $this->employeeDependentDao->saveEmployeeDependent($empDependent);
    }

    public function testGetEmployeeDependents(): void
    {
        $empDependents = $this->employeeDependentDao->getEmployeeDependents(1);
        $this->assertCount(2, $empDependents);
        $this->assertEquals('Abrahamson', $empDependents[0]->getName());
        $this->assertEquals('Abram', $empDependents[1]->getName());
    }

    public function testGetEmployeeDependent(): void
    {
        $empDependent = $this->employeeDependentDao->getEmployeeDependent(1, 1);
        $this->assertEquals('Abrahamson', $empDependent->getName());

        $empDependent = $this->employeeDependentDao->getEmployeeDependent(1, 5);
        $this->assertNull($empDependent);
    }

    public function testDeleteEmployeeDependents(): void
    {
        $rows = $this->employeeDependentDao->deleteEmployeeDependents(1, [1, 2]);
        $this->assertEquals(2, $rows);

        $empDependentObj = $this->getRepository(EmpDependent::class)->findOneBy(['employee' => 1, 'seqNo' => 1]);
        $this->assertNull($empDependentObj);
        $empDependentObj = $this->getRepository(EmpDependent::class)->findOneBy(['employee' => 1, 'seqNo' => 2]);
        $this->assertNull($empDependentObj);
    }
}
