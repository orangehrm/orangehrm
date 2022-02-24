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

namespace OrangeHRM\Tests\Maintenance\Service;

use DateTime;
use Exception;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\AttendanceRecord;
use OrangeHRM\Entity\EmpContract;
use OrangeHRM\Entity\EmpDependent;
use OrangeHRM\Entity\EmpDirectDebit;
use OrangeHRM\Entity\EmpEmergencyContact;
use OrangeHRM\Entity\EmpLocations;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmployeeAttachment;
use OrangeHRM\Entity\EmployeeEducation;
use OrangeHRM\Entity\EmployeeImmigrationRecord;
use OrangeHRM\Entity\EmployeeLanguage;
use OrangeHRM\Entity\EmployeeLicense;
use OrangeHRM\Entity\EmployeeMembership;
use OrangeHRM\Entity\EmployeeSalary;
use OrangeHRM\Entity\EmployeeSkill;
use OrangeHRM\Entity\EmpPicture;
use OrangeHRM\Entity\EmpUsTaxExemption;
use OrangeHRM\Entity\EmpWorkExperience;
use OrangeHRM\Entity\LeaveComment;
use OrangeHRM\Entity\LeaveRequestComment;
use OrangeHRM\Entity\ReportTo;
use OrangeHRM\Entity\TimesheetItem;
use OrangeHRM\Entity\User;
use OrangeHRM\Framework\Services;
use OrangeHRM\Maintenance\Dao\PurgeEmployeeDao;
use OrangeHRM\Maintenance\PurgeStrategy\DestroyPurgeStrategy;
use OrangeHRM\Maintenance\PurgeStrategy\ReplaceWithValuePurgeStrategy;
use OrangeHRM\Maintenance\Service\PurgeEmployeeService;
use OrangeHRM\ORM\Exception\TransactionException;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Maintenance
 * @group Service
 */
class PurgeEmployeeServiceTest extends KernelTestCase
{
    use EntityManagerHelperTrait;

    private PurgeEmployeeService $purgeEmployeeService;
    protected string $fixture;

    protected function setUp(): void
    {
        $this->purgeEmployeeService = new PurgeEmployeeService();
        $this->fixture = Config::get(
            Config::PLUGINS_DIR
        ) . '/orangehrmMaintenancePlugin/test/fixtures/PurgeEmployeeService.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetEmployeePurgeDao(): void
    {
        $purgeEmployeeService = $this->getMockBuilder(PurgeEmployeeService::class)
            ->onlyMethods(['getPurgeEmployeeDao'])
            ->getMock();
        $purgeEmployeeService->expects($this->once())
            ->method('getPurgeEmployeeDao');

        $purgeEmployeeDao = $purgeEmployeeService->getPurgeEmployeeDao();
        $this->assertInstanceOf(PurgeEmployeeDao::class, $purgeEmployeeDao);
    }

    public function testGetPurgeableEntities(): void
    {
        $purgeableEntities = $this->purgeEmployeeService->getPurgeableEntities('gdpr_purge_employee_strategy');

        $this->assertCount(22, $purgeableEntities);
        $this->assertArrayHasKey("Employee", $purgeableEntities);
        $this->assertArrayHasKey("EmpPicture", $purgeableEntities);
        $this->assertArrayHasKey("EmployeeAttachment", $purgeableEntities);
        $this->assertArrayHasKey("EmpEmergencyContact", $purgeableEntities);
        $this->assertArrayHasKey("EmpDependent", $purgeableEntities);
        $this->assertArrayHasKey("EmployeeImmigrationRecord", $purgeableEntities);
        $this->assertArrayHasKey("EmpWorkExperience", $purgeableEntities);
        $this->assertArrayHasKey("EmployeeEducation", $purgeableEntities);
        $this->assertArrayHasKey("EmployeeSkill", $purgeableEntities);
        $this->assertArrayHasKey("EmployeeLanguage", $purgeableEntities);
        $this->assertArrayHasKey("EmployeeMembership", $purgeableEntities);
        $this->assertArrayHasKey("EmpUsTaxExemption", $purgeableEntities);
        $this->assertArrayHasKey("EmployeeLicense", $purgeableEntities);
        $this->assertArrayHasKey("EmployeeSalary", $purgeableEntities);
        $this->assertArrayHasKey("EmpLocations", $purgeableEntities);
        $this->assertArrayHasKey("EmpContract", $purgeableEntities);
        $this->assertArrayHasKey("User", $purgeableEntities);
        $this->assertArrayHasKey("ReportTo", $purgeableEntities);
        $this->assertArrayHasKey("LeaveRequestComment", $purgeableEntities);
        $this->assertArrayHasKey("LeaveComment", $purgeableEntities);
        $this->assertArrayHasKey("AttendanceRecord", $purgeableEntities);
        $this->assertArrayHasKey("TimesheetItem", $purgeableEntities);
    }

    public function testGetPurgeStrategy(): void
    {
        $purgeableEntityClassName = 'Employee';
        $strategy = 'ReplaceWithValue';
        $strategyInfoArray = [
            'match_by' => [
                ['match' => 'empNumber']
            ],
            'parameters' => [
                ['field' => 'firstName', 'class' => 'FormatWithPurgeString'],
                ['field' => 'lastName', 'class' => 'FormatWithPurgeString'],
                ['field' => 'middleName', 'class' => 'FormatWithEmptyString'],
            ]
        ];

        $purgeStrategy = $this->purgeEmployeeService->getPurgeStrategy(
            $purgeableEntityClassName,
            $strategy,
            $strategyInfoArray
        );
        $this->assertInstanceOf(ReplaceWithValuePurgeStrategy::class, $purgeStrategy);

        $matchByValues = $purgeStrategy->getMatchByValues(1);
        $expected = ['empNumber' => 1];
        $this->assertEquals($expected, $matchByValues);

        $parameters = $purgeStrategy->getParameters();
        $expected = [
            ['field' => 'firstName', 'class' => 'FormatWithPurgeString'],
            ['field' => 'lastName', 'class' => 'FormatWithPurgeString'],
            ['field' => 'middleName', 'class' => 'FormatWithEmptyString'],
        ];
        $this->assertEquals($expected, $parameters);
    }

    public function testGetPurgeStrategy2(): void
    {
        $purgeableEntityClassName = 'EmpPicture';
        $strategy = 'Destroy';
        $strategyInfoArray = [
            'match_by' => [
                ['match' => 'employee']
            ]
        ];

        $purgeStrategy = $this->purgeEmployeeService->getPurgeStrategy(
            $purgeableEntityClassName,
            $strategy,
            $strategyInfoArray
        );
        $this->assertInstanceOf(DestroyPurgeStrategy::class, $purgeStrategy);

        $matchByValues = $purgeStrategy->getMatchByValues(1);
        $expected = ['employee' => 1];
        $this->assertEquals($expected, $matchByValues);

        $parameters = $purgeStrategy->getParameters();
        $this->assertNull($parameters);
    }

    public function testPurgeEmployeeData(): void
    {
        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService()]);
        $this->purgeEmployeeService->purgeEmployeeData(1);

        $purgedEmployee = $this->getRepository(Employee::class)->findOneBy(['empNumber' => 1]);
        $this->assertEquals("Purge", $purgedEmployee->getFirstName());
        $this->assertEquals("Purge", $purgedEmployee->getLastName());
        $this->assertEquals('', $purgedEmployee->getMiddleName());
        $this->assertEquals('', $purgedEmployee->getNickName());
        $this->assertEquals(0, $purgedEmployee->getSmoker());
        $this->assertNull($purgedEmployee->getBirthday());
        $this->assertNull($purgedEmployee->getNationality());
        $this->assertNull($purgedEmployee->getGender());
        $this->assertNull($purgedEmployee->getMaritalStatus());
        $this->assertEquals('', $purgedEmployee->getSsnNumber());
        $this->assertEquals('', $purgedEmployee->getSinNumber());
        $this->assertEquals('', $purgedEmployee->getOtherId());
        $this->assertEquals('', $purgedEmployee->getDrivingLicenseNo());
        $this->assertNull($purgedEmployee->getDrivingLicenseExpiredDate());
        $this->assertEquals('', $purgedEmployee->getMilitaryService());
        $this->assertNull($purgedEmployee->getEmpStatus());
        $this->assertNull($purgedEmployee->getJobTitle());
        $this->assertNull($purgedEmployee->getJobCategory());
        $this->assertNull($purgedEmployee->getSubDivision());
        $this->assertEquals('', $purgedEmployee->getStreet1());
        $this->assertEquals('', $purgedEmployee->getStreet2());
        $this->assertEquals('', $purgedEmployee->getCity());
        $this->assertEquals('', $purgedEmployee->getCountry());
        $this->assertEquals('', $purgedEmployee->getProvince());
        $this->assertNull($purgedEmployee->getZipcode());
        $this->assertNull($purgedEmployee->getHomeTelephone());
        $this->assertNull($purgedEmployee->getMobile());
        $this->assertNull($purgedEmployee->getWorkTelephone());
        $this->assertNull($purgedEmployee->getWorkEmail());
        $this->assertNull($purgedEmployee->getJoinedDate());
        $this->assertNull($purgedEmployee->getOtherEmail());
        $this->assertNull($purgedEmployee->getCustom1());
        $this->assertNull($purgedEmployee->getCustom2());
        $this->assertNull($purgedEmployee->getCustom3());
        $this->assertNull($purgedEmployee->getCustom4());
        $this->assertNull($purgedEmployee->getCustom5());
        $this->assertNull($purgedEmployee->getCustom6());
        $this->assertNull($purgedEmployee->getCustom7());
        $this->assertNull($purgedEmployee->getCustom8());
        $this->assertNull($purgedEmployee->getCustom9());
        $this->assertNull($purgedEmployee->getCustom10());
        $this->assertInstanceOf(DateTime::class, $purgedEmployee->getPurgedAt());

        $empPictures = $this->getRepository(EmpPicture::class);
        $this->assertEmpty($empPictures->findBy(['employee' => 1]));
        $this->assertCount(1, $empPictures->findAll());

        $empAttachments = $this->getRepository(EmployeeAttachment::class);
        $this->assertEmpty($empAttachments->findBy(['employee' => 1]));
        $this->assertCount(2, $empAttachments->findAll());

        $empEmergencyContacts = $this->getRepository(EmpEmergencyContact::class);
        $this->assertEmpty($empEmergencyContacts->findBy(['employee' => 1]));
        $this->assertCount(1, $empEmergencyContacts->findAll());

        $empDependents = $this->getRepository(EmpDependent::class);
        $this->assertEmpty($empDependents->findBy(['employee' => 1]));
        $this->assertCount(1, $empDependents->findAll());

        $empImmigrationRecord = $this->getRepository(EmployeeImmigrationRecord::class);
        $this->assertEmpty($empImmigrationRecord->findBy(['employee' => 1]));
        $this->assertCount(1, $empImmigrationRecord->findAll());

        $empWorkExperience = $this->getRepository(EmpWorkExperience::class);
        $this->assertEmpty($empWorkExperience->findBy(['employee' => 1]));
        $this->assertCount(2, $empWorkExperience->findAll());

        $empEduQualifications = $this->getRepository(EmployeeEducation::class);
        $this->assertEmpty($empEduQualifications->findBy(['employee' => 1]));
        $this->assertCount(2, $empEduQualifications->findAll());

        $empSkills = $this->getRepository(EmployeeSkill::class);
        $this->assertEmpty($empSkills->findBy(['employee' => 1]));
        $this->assertCount(2, $empSkills->findAll());

        $empLanguages = $this->getRepository(EmployeeLanguage::class);
        $this->assertEmpty($empLanguages->findBy(['employee' => 1]));
        $this->assertCount(4, $empLanguages->findAll());

        $empMemberships = $this->getRepository(EmployeeMembership::class);
        $this->assertEmpty($empMemberships->findBy(['employee' => 1]));
        $this->assertCount(1, $empMemberships->findAll());

        $empUsTaxExemptions = $this->getRepository(EmpUsTaxExemption::class);
        $this->assertEmpty($empUsTaxExemptions->findBy(['employee' => 1]));
        $this->assertCount(1, $empUsTaxExemptions->findAll());

        $empLicenses = $this->getRepository(EmployeeLicense::class);
        $this->assertEmpty($empLicenses->findBy(['employee' => 1]));
        $this->assertCount(2, $empLicenses->findAll());

        $empSalaries = $this->getRepository(EmployeeSalary::class);
        $empDirectDebits = $this->getRepository(EmpDirectDebit::class);
        $this->assertEmpty($empSalaries->findBy(['employee' => 1]));
        $this->assertCount(1, $empSalaries->findAll());
        $this->assertCount(1, $empDirectDebits->findAll());

        $empLocations = $this->getRepository(EmpLocations::class);
        $this->assertEmpty($empLocations->findBy(['employee' => 1]));
        $this->assertCount(3, $empLocations->findAll());

        $empContracts = $this->getRepository(EmpContract::class);
        $this->assertEmpty($empContracts->findBy(['employee' => 1]));
        $this->assertCount(2, $empContracts->findAll());

        $users = $this->getRepository(User::class);
        $this->assertEmpty($users->findBy(['employee' => 1]));
        $this->assertCount(2, $users->findAll());

        $empReportTo = $this->getRepository(ReportTo::class);
        $this->assertEmpty($empReportTo->findBy(['subordinate' => 1]));
        $this->assertEmpty($empReportTo->findBy(['supervisor' => 1]));
        $this->assertCount(4, $empReportTo->findAll());

        $empLeaveRequestComments = $this->getRepository(LeaveRequestComment::class)->findBy(['createdByEmployee' => 1]);
        $this->assertCount(3, $empLeaveRequestComments);
        foreach ($empLeaveRequestComments as $empLeaveRequestComment) {
            $this->assertEquals("Purge", $empLeaveRequestComment->getComment());
        }

        $empLeaveComments = $this->getRepository(LeaveComment::class)->findBy(['createdByEmployee' => 1]);
        $this->assertCount(2, $empLeaveComments);
        foreach ($empLeaveComments as $empLeaveComment) {
            $this->assertEquals("Purge", $empLeaveComment->getComment());
        }

        $empAttendanceRecords = $this->getRepository(AttendanceRecord::class)->findBy(['employee' => 1]);
        $this->assertCount(2, $empAttendanceRecords);
        foreach ($empAttendanceRecords as $empAttendanceRecord) {
            $this->assertEquals("Purge", $empAttendanceRecord->getPunchInNote());
            $this->assertEquals("Purge", $empAttendanceRecord->getPunchOutNote());
        }

        $empTimesheetItems = $this->getRepository(TimesheetItem::class)->findBy(['employee' => 1]);
        $this->assertCount(2, $empTimesheetItems);
        foreach ($empTimesheetItems as $empTimesheetItem) {
            $this->assertEquals("Purge", $empTimesheetItem->getComment());
        }
    }

    public function testPurgeEmployeeDataWithTransactionException(): void
    {
        $purgeEmployeeServiceMock = $this->getMockBuilder(PurgeEmployeeService::class)
            ->onlyMethods(['getPurgeableEntities'])
            ->getMock();

        $purgeEmployeeServiceMock->expects($this->once())
            ->method('getPurgeableEntities')
            ->willReturnCallback(function () {
                throw new Exception();
            });

        $this->expectException(TransactionException::class);
        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService()]);
        $purgeEmployeeServiceMock->purgeEmployeeData(1);
    }
}
