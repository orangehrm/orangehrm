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

use OrangeHRM\Admin\Service\CompanyStructureService;
use OrangeHRM\Admin\Service\CountryService;
use OrangeHRM\Admin\Service\PayGradeService;
use OrangeHRM\Config\Config;
use OrangeHRM\Framework\Services;
use OrangeHRM\Maintenance\AccessStrategy\AccessStrategy;
use OrangeHRM\Maintenance\Service\MaintenanceService;
use OrangeHRM\Pim\Service\EmployeeService;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;
use OrangeHRM\Time\Service\TimesheetService;

class MaintenanceServiceTest extends KernelTestCase
{
    private string $fixture;
    private MaintenanceService $maintenanceService;

    protected function setUp(): void
    {
        $this->maintenanceService = new MaintenanceService();
        $this->fixture = Config::get(
            Config::PLUGINS_DIR
        ) . '/orangehrmMaintenancePlugin/test/fixtures/EmployeeMaintenence.yml';
        TestDataService::populate($this->fixture);
        $this->createKernelWithMockServices([
            Services::COUNTRY_SERVICE => new CountryService(),
            Services::PAY_GRADE_SERVICE => new PayGradeService(),
            Services::EMPLOYEE_SERVICE => new EmployeeService(),
            Services::TIMESHEET_SERVICE => new TimesheetService(),
            Services::COMPANY_STRUCTURE_SERVICE => new CompanyStructureService()
        ]);
    }

    public function testAccessEmployeeData(): void
    {
        $result = $this->maintenanceService->accessEmployeeData(1);
        $this->assertEquals('Kayla', $result['Employee'][0]['firstName']);
        $this->assertEquals('Abbey', $result['Employee'][0]['lastName']);
        $this->assertEquals('T', $result['Employee'][0]['middleName']);
        $this->assertEquals('E001', $result['Employee'][0]['employeeId']);
        $this->assertEquals('sd', $result['Employee'][0]['nickName']);
        $this->assertEquals('Smoker', $result['Employee'][0]['smoker']);
        $this->assertEquals('Male', $result['Employee'][0]['gender']);
        $this->assertEquals('2022-02-01', $result['Employee'][0]['joinedDate']);
        $this->assertEquals('Organization', $result['Employee'][0]['subDivision']);
        $this->assertEquals('Street 1', $result['Employee'][0]['street1']);
        $this->assertEquals('Street 2', $result['Employee'][0]['street2']);
        $this->assertEquals('City', $result['Employee'][0]['city']);
        $this->assertEquals('LK', $result['Employee'][0]['country']);
        $this->assertEquals('State', $result['Employee'][0]['province']);
        $this->assertEquals('12345', $result['Employee'][0]['zipcode']);
        $this->assertEquals('123', $result['Employee'][0]['homeTelephone']);
        $this->assertEquals('1975-10-15', $result['Employee'][0]['birthday']);
        $this->assertEquals('1234', $result['Employee'][0]['mobile']);
        $this->assertEquals('112-898-7612', $result['Employee'][0]['workTelephone']);
        $this->assertEquals('kayla@xample.com', $result['Employee'][0]['workEmail']);
        $this->assertEquals('kayla2@xample.com', $result['Employee'][0]['otherEmail']);
        $this->assertEquals('Job Category 1', $result['Employee'][0]['jobCategory']);
        $this->assertEquals('2022-02-01', $result['Employee'][0]['joinedDate']);
        $this->assertEquals('Custom 1', $result['Employee'][0]['custom1']);

        $this->assertEquals('Single', $result['Employee'][0]['maritalStatus']);
        $this->assertEquals('nationality 1', $result['Employee'][0]['nationCode']);

        $this->assertEquals('123', $result['Employee'][0]['ssnNumber']);
        $this->assertEquals('1234', $result['Employee'][0]['sinNumber']);
        $this->assertEquals('12345', $result['Employee'][0]['otherId']);

        $this->assertEquals('123', $result['Employee'][0]['drivingLicenseNo']);
        $this->assertEquals('123', $result['Employee'][0]['drivingLicenseNo']);
        $this->assertEquals('Yes', $result['Employee'][0]['militaryService']);
        $this->assertEquals('Full Time', $result['Employee'][0]['empStatus']);


        $this->assertCount(1, $result['Employee']);
    }

    public function testReportTo(): void
    {
        $result = $this->maintenanceService->accessEmployeeData(2);
        $this->assertEquals('Kayla T Abbey', $result['ReportTo'][0]['supervisor']);
        $this->assertEquals('Ashley ST Abel', $result['ReportTo'][0]['subordinate']);
        $this->assertEquals('Direct', $result['ReportTo'][0]['reportingMethod']);
        $this->assertCount(2, $result['ReportTo']);
    }

    public function testEmpPicture(): void
    {
        $result = $this->maintenanceService->accessEmployeeData(1);
        $this->assertEquals('eWFzaXRoYQ==', $result['EmpPicture'][0]['picture']);
        $this->assertEquals('test_file.jpg', $result['EmpPicture'][0]['filename']);
        $this->assertEquals('test/type', $result['EmpPicture'][0]['fileType']);
        $this->assertCount(1, $result['EmpPicture']);
    }

    public function testEmployeeAttachment(): void
    {
        $result = $this->maintenanceService->accessEmployeeData(1);
        $this->assertEquals('attachment.txt', $result['EmployeeAttachment'][0]['filename']);
        $this->assertEquals('6', $result['EmployeeAttachment'][0]['size']);
        $this->assertEquals('ZEdWemRBMEs=', $result['EmployeeAttachment'][0]['attachment']);
        $this->assertEquals('text/plain', $result['EmployeeAttachment'][0]['fileType']);
        $this->assertEquals('2021-02-23 00:00:00', $result['EmployeeAttachment'][0]['attachedTime']);
        $this->assertCount(1, $result['EmpPicture']);
    }

    public function testEmpEmergencyContact(): void
    {
        $result = $this->maintenanceService->accessEmployeeData(1);
        $this->assertEquals('Yasitha', $result['EmpEmergencyContact'][0]['name']);
        $this->assertEquals('friend', $result['EmpEmergencyContact'][0]['relationship']);
        $this->assertEquals('0335445678', $result['EmpEmergencyContact'][0]['homePhone']);
        $this->assertEquals('0776734567', $result['EmpEmergencyContact'][0]['mobilePhone']);
        $this->assertEquals('0113456787', $result['EmpEmergencyContact'][0]['officePhone']);
        $this->assertCount(2, $result['EmpEmergencyContact']);
    }

    public function testEmpDependent(): void
    {
        $result = $this->maintenanceService->accessEmployeeData(1);
        $this->assertEquals('yasitha', $result['EmpDependent'][0]['name']);
        $this->assertEquals('friend', $result['EmpDependent'][0]['relationship']);
        $this->assertEquals('child', $result['EmpDependent'][0]['relationshipType']);
        $this->assertEquals('2007-02-23', $result['EmpDependent'][0]['dateOfBirth']);
        $this->assertCount(2, $result['EmpDependent']);
    }

    public function testEmployeeImmigrationRecord(): void
    {
        $result = $this->maintenanceService->accessEmployeeData(1);
        $this->assertEquals('HVN0003472', $result['EmployeeImmigrationRecord'][0]['number']);
        $this->assertEquals('2010-12-12', $result['EmployeeImmigrationRecord'][0]['issuedDate']);
        $this->assertEquals('2011-12-12', $result['EmployeeImmigrationRecord'][0]['expiryDate']);
        $this->assertEquals('test comment', $result['EmployeeImmigrationRecord'][0]['comment']);
        $this->assertEquals('Passport', $result['EmployeeImmigrationRecord'][0]['type']);
        $this->assertEquals('some status', $result['EmployeeImmigrationRecord'][0]['status']);
        $this->assertEquals('2011-12-30', $result['EmployeeImmigrationRecord'][0]['reviewDate']);
        $this->assertEquals('SRI LANKA', $result['EmployeeImmigrationRecord'][0]['countryCode']);
        $this->assertCount(2, $result['EmployeeImmigrationRecord']);
    }

    public function testEmpWorkExperience(): void
    {
        $result = $this->maintenanceService->accessEmployeeData(1);
        $this->assertEquals('OrangeHRM', $result['EmpWorkExperience'][0]['employer']);
        $this->assertEquals('SE', $result['EmpWorkExperience'][0]['jobTitle']);
        $this->assertEquals('2005-04-03', $result['EmpWorkExperience'][0]['fromDate']);
        $this->assertEquals('2010-04-04', $result['EmpWorkExperience'][0]['toDate']);
        $this->assertEquals('Worked Hard', $result['EmpWorkExperience'][0]['comments']);
        $this->assertEquals('2', $result['EmpWorkExperience'][0]['internal']);
        $this->assertCount(2, $result['EmpWorkExperience']);
    }

    public function testEmployeeEducation(): void
    {
        $result = $this->maintenanceService->accessEmployeeData(1);
        $this->assertEquals('PhD', $result['EmployeeEducation'][0]['education']);
        $this->assertEquals('ENG', $result['EmployeeEducation'][0]['major']);
        $this->assertEquals('2007', $result['EmployeeEducation'][0]['year']);
        $this->assertEquals('3.7', $result['EmployeeEducation'][0]['score']);
        $this->assertEquals('2006-03-04', $result['EmployeeEducation'][0]['startDate']);
        $this->assertEquals('2010-03-05', $result['EmployeeEducation'][0]['endDate']);
        $this->assertCount(2, $result['EmployeeEducation']);
    }

    public function testEmployeeSkill(): void
    {
        $result = $this->maintenanceService->accessEmployeeData(1);
        $this->assertEquals('Driving', $result['EmployeeSkill'][0]['skill']);
        $this->assertEquals('4', $result['EmployeeSkill'][0]['yearsOfExp']);
        $this->assertEquals('com1', $result['EmployeeSkill'][0]['comments']);
        $this->assertCount(2, $result['EmployeeSkill']);
    }

    public function testEmployeeLanguage(): void
    {
        $result = $this->maintenanceService->accessEmployeeData(1);
        $this->assertEquals('Spanish', $result['EmployeeLanguage'][0]['language']);
        $this->assertEquals('Speaking', $result['EmployeeLanguage'][0]['fluency']);
        $this->assertEquals('Poor', $result['EmployeeLanguage'][0]['competency']);
        $this->assertEquals('comment1', $result['EmployeeLanguage'][0]['comment']);
        $this->assertCount(2, $result['EmployeeLanguage']);
    }

    public function testEmployeeMembership(): void
    {
        $result = $this->maintenanceService->accessEmployeeData(1);
        $this->assertEquals('membership 1', $result['EmployeeMembership'][0]['membership']);
        $this->assertEquals('4.00', $result['EmployeeMembership'][0]['subscriptionFee']);
        $this->assertEquals('individual', $result['EmployeeMembership'][0]['subscriptionPaidBy']);
        $this->assertEquals('Rs', $result['EmployeeMembership'][0]['subscriptionCurrency']);
        $this->assertEquals('2011-05-20', $result['EmployeeMembership'][0]['subscriptionCommenceDate']);
        $this->assertEquals('2011-05-22', $result['EmployeeMembership'][0]['subscriptionRenewalDate']);
        $this->assertCount(2, $result['EmployeeMembership']);
    }

    public function testEmpUsTaxExemption(): void
    {
        $result = $this->maintenanceService->accessEmployeeData(1);
        $this->assertEquals('Single', $result['EmpUsTaxExemption'][0]['federalStatus']);
        $this->assertEquals('2', $result['EmpUsTaxExemption'][0]['federalExemptions']);
        $this->assertEquals('Alaska', $result['EmpUsTaxExemption'][0]['state']);
        $this->assertEquals('Single', $result['EmpUsTaxExemption'][0]['stateStatus']);
        $this->assertEquals('1', $result['EmpUsTaxExemption'][0]['stateExemptions']);
        $this->assertEquals('Alaska', $result['EmpUsTaxExemption'][0]['unemploymentState']);
        $this->assertCount(1, $result['EmpUsTaxExemption']);
    }

    public function testEmployeeLicense(): void
    {
        $result = $this->maintenanceService->accessEmployeeData(1);
        $this->assertEquals('li1', $result['EmployeeLicense'][0]['license']);
        $this->assertEquals('2bja8768', $result['EmployeeLicense'][0]['licenseNo']);
        $this->assertEquals('2004-02-23', $result['EmployeeLicense'][0]['licenseIssuedDate']);
        $this->assertEquals('2007-02-23', $result['EmployeeLicense'][0]['licenseExpiryDate']);
        $this->assertCount(2, $result['EmployeeLicense']);
    }

    public function testEmployeeSalary(): void
    {
        $result = $this->maintenanceService->accessEmployeeData(1);
        $this->assertEquals('Salary Grade A', $result['EmployeeSalary'][0]['payGrade']);
        $this->assertEquals('LKR', $result['EmployeeSalary'][0]['currencyType']);
        $this->assertEquals('10000.00', $result['EmployeeSalary'][0]['amount']);
        $this->assertEquals('Weekly', $result['EmployeeSalary'][0]['payPeriod']);
        $this->assertEquals('Main Salary', $result['EmployeeSalary'][0]['salaryName']);
        $this->assertEquals('com1', $result['EmployeeSalary'][0]['comment']);
        $this->assertCount(2, $result['EmployeeSalary']);
    }

    public function testEmpLocations(): void
    {
        $result = $this->maintenanceService->accessEmployeeData(1);
        $this->assertEquals('location 1', $result['EmpLocations'][0]['location']);
        $this->assertCount(2, $result['EmpLocations']);
    }

    public function testEmpContract(): void
    {
        $result = $this->maintenanceService->accessEmployeeData(1);
        $this->assertEquals('2020-05-23', $result['EmpContract'][0]['startDate']);
        $this->assertEquals('2021-05-23', $result['EmpContract'][0]['endDate']);
        $this->assertCount(2, $result['EmpContract']);
    }

    public function testUser(): void
    {
        $result = $this->maintenanceService->accessEmployeeData(1);
        $this->assertEquals('samantha', $result['User'][0]['userName']);
        $this->assertEquals('2011-04-12', $result['User'][0]['dateEntered']);
        $this->assertEquals('2011-04-13', $result['User'][0]['dateModified']);
        $this->assertCount(2, $result['User']);
    }

    public function testLeaveRequest(): void
    {
        $result = $this->maintenanceService->accessEmployeeData(1);
        $this->assertEquals('Casual', $result['LeaveRequest'][0]['leaveType']);
        $this->assertEquals('2010-08-30', $result['LeaveRequest'][0]['dateApplied']);
        $this->assertCount(3, $result['LeaveRequest']);
    }

    public function testLeaveRequestComment(): void
    {
        $result = $this->maintenanceService->accessEmployeeData(1);
        $this->assertEquals('2010-08-29 04:55:00', $result['LeaveRequestComment'][0]['createdAt']);
        $this->assertEquals('samantha', $result['LeaveRequestComment'][0]['createdBy']);
        $this->assertEquals('employee 3 comment on emp 1 leave request', $result['LeaveRequestComment'][0]['comment']);
        $this->assertCount(3, $result['LeaveRequestComment']);
    }

    public function testLeaveComment(): void
    {
        $result = $this->maintenanceService->accessEmployeeData(1);
        $this->assertEquals('samantha', $result['LeaveComment'][0]['createdBy']);
        $this->assertEquals('Kayla T Abbey', $result['LeaveComment'][0]['createdByEmployee']);
        $this->assertEquals('Cancelled upon request', $result['LeaveComment'][0]['comment']);
        $this->assertCount(2, $result['LeaveComment']);
    }

    public function testLeave(): void
    {
        $result = $this->maintenanceService->accessEmployeeData(1);
        $this->assertEquals('2010-09-01', $result['Leave'][0]['date']);
        $this->assertEquals('8', $result['Leave'][0]['lengthHours']);
        $this->assertEquals('1', $result['Leave'][0]['lengthDays']);
        $this->assertEquals('10:00:00', $result['Leave'][0]['startTime']);
        $this->assertEquals('10:00:00', $result['Leave'][0]['endTime']);
        $this->assertCount(1, $result['Leave']);
    }

    public function testAttendanceRecord(): void
    {
        $result = $this->maintenanceService->accessEmployeeData(1);
        $this->assertEquals('2011-05-27 12:10:00', $result['AttendanceRecord'][0]['punchInUtcTime']);
        $this->assertEquals('Im punched In', $result['AttendanceRecord'][0]['punchInNote']);
        $this->assertEquals('Asia/Calcutta', $result['AttendanceRecord'][0]['punchInTimeOffset']);
        $this->assertEquals('2011-05-27 12:10:00', $result['AttendanceRecord'][0]['punchInUserTime']);
        $this->assertEquals('2011-05-27 12:10:00', $result['AttendanceRecord'][0]['punchOutUtcTime']);
        $this->assertEquals(' Punched Out', $result['AttendanceRecord'][0]['punchOutNote']);
        $this->assertEquals('Asia/Calcutta', $result['AttendanceRecord'][0]['punchOutTimeOffset']);
        $this->assertEquals('2011-05-27 12:10:00', $result['AttendanceRecord'][0]['punchOutUserTime']);
        $this->assertEquals('PUNCHED IN', $result['AttendanceRecord'][0]['state']);
        $this->assertCount(2, $result['AttendanceRecord']);
    }

    public function testTimesheetItem(): void
    {
        $result = $this->maintenanceService->accessEmployeeData(1);
        $this->assertEquals('BUS', $result['TimesheetItem'][0]['project']);
        $this->assertEquals('Debug', $result['TimesheetItem'][0]['projectActivity']);
        $this->assertEquals('2011-04-12', $result['TimesheetItem'][0]['date']);
        $this->assertEquals('7200', $result['TimesheetItem'][0]['duration']);
        $this->assertEquals('Good', $result['TimesheetItem'][0]['comment']);
        $this->assertCount(1, $result['TimesheetItem']);
    }

    public function testPerformanceReview(): void
    {
        $result = $this->maintenanceService->accessEmployeeData(1);
        $this->assertEquals('Software Architect', $result['PerformanceReview'][0]['jobTitle']);
        $this->assertEquals('2011-01-01', $result['PerformanceReview'][0]['workPeriodStart']);
        $this->assertEquals('2011-01-01', $result['PerformanceReview'][0]['workPeriodEnd']);
        $this->assertEquals('Organization', $result['PerformanceReview'][0]['department']);
        $this->assertEquals('2011-01-01', $result['PerformanceReview'][0]['dueDate']);
        $this->assertEquals('2011-01-01', $result['PerformanceReview'][0]['completedDate']);
        $this->assertEquals('2011-01-02 00:00:00', $result['PerformanceReview'][0]['activatedDate']);
        $this->assertEquals('last', $result['PerformanceReview'][0]['finalComment']);
        $this->assertEquals('2.00', $result['PerformanceReview'][0]['finalRate']);
        $this->assertCount(2, $result['PerformanceReview']);
    }

    public function testReviewerRating(): void
    {
        $result = $this->maintenanceService->accessEmployeeData(1);
        $this->assertEquals('Code Clarity', $result['ReviewerRating'][0]['kpi']);
        $this->assertEquals('5.50', $result['ReviewerRating'][0]['rating']);
        $this->assertEquals('Test comment 1', $result['ReviewerRating'][0]['comment']);
        $this->assertCount(2, $result['ReviewerRating']);
    }

    public function testReviewer(): void
    {
        $result = $this->maintenanceService->accessEmployeeData(1);
        $this->assertEquals('Kayla T Abbey', $result['Reviewer'][0]['employee']);
        $this->assertEquals('2014-10-30 00:00:00', $result['Reviewer'][0]['completedDate']);
        $this->assertEquals('Test Comment 2', $result['Reviewer'][0]['comment']);
        $this->assertCount(2, $result['Reviewer']);
    }

    public function testPerformanceTrackerLog(): void
    {
        $result = $this->maintenanceService->accessEmployeeData(1);
        $this->assertEquals('log by 2', $result['PerformanceTrackerLog'][0]['log']);
        $this->assertEquals('test comment by 2', $result['PerformanceTrackerLog'][0]['comment']);
        $this->assertEquals('Positive', $result['PerformanceTrackerLog'][0]['achievement']);
        $this->assertEquals('2011-12-12 00:00:00', $result['PerformanceTrackerLog'][0]['addedDate']);
        $this->assertEquals('2011-12-12 00:00:00', $result['PerformanceTrackerLog'][0]['modifiedDate']);
        $this->assertCount(1, $result['PerformanceTrackerLog']);
    }

    public function testGetPurgeableEntities(): void
    {
        $purgeableEntities = $this->maintenanceService->getPurgeableEntities('gdpr_access_employee_strategy');
        $this->assertCount(28, $purgeableEntities);
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


    public function testGetAccessStrategy(): void
    {
        $result = $this->maintenanceService->getAccessStrategy('Employee', 'Basic', []);
        $this->assertInstanceOf(AccessStrategy::class, $result);
    }
}
