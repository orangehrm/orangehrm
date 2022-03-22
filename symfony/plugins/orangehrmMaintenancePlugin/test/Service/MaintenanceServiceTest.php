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
        $this->maintenanceService=new MaintenanceService();
        $this->fixture=Config::get(Config::PLUGINS_DIR).'/orangehrmMaintenancePlugin/test/fixtures/EmployeeMaintenence.yml';
        TestDataService::populate($this->fixture);
        $this->createKernelWithMockServices([Services::COUNTRY_SERVICE=>new CountryService(),
            Services::PAY_GRADE_SERVICE=>new PayGradeService(),Services::EMPLOYEE_SERVICE=>new EmployeeService(),
            Services::TIMESHEET_SERVICE=>new TimesheetService(),
            ]);
    }

    public function testAccessEmployeeData(): void
    {
        $result=$this->maintenanceService->accessEmployeeData(1);
        $this->assertEquals('Kayla', $result['Employee'][0]['firstName']);
        $this->assertEquals('test_file.jpg', $result['EmpPicture'][0]['filename']);
        $this->assertEquals('attachment.txt', $result['EmployeeAttachment'][0]['filename']);
        $this->assertEquals('Yasitha', $result['EmpEmergencyContact'][0]['name']);
        $this->assertEquals('friend', $result['EmpDependent'][0]['relationship']);
        $this->assertEquals('HVN0003472', $result['EmployeeImmigrationRecord'][0]['number']);
        $this->assertEquals('SE', $result['EmpWorkExperience'][0]['jobTitle']);
        $this->assertEquals('LKR', $result['EmployeeSalary'][0]['currencyType']);

        $this->assertCount(1, $result['Employee']);
    }

    public function testReportTo():void{
        $result=$this->maintenanceService->accessEmployeeData(2);
        $this->assertEquals('Kayla T Abbey', $result['ReportTo'][0]['supervisor']);
        $this->assertEquals('Ashley ST Abel', $result['ReportTo'][0]['subordinate']);
        $this->assertEquals('Direct', $result['ReportTo'][0]['reportingMethod']);
        $this->assertCount(2, $result['ReportTo']);
    }

    public function testEmpPicture():void{
        $result=$this->maintenanceService->accessEmployeeData(1);
        $this->assertEquals('eWFzaXRoYQ==', $result['EmpPicture'][0]['picture']);
        $this->assertEquals('test_file.jpg', $result['EmpPicture'][0]['filename']);
        $this->assertEquals('test/type', $result['EmpPicture'][0]['fileType']);
        $this->assertCount(1, $result['EmpPicture']);
    }

    public function testEmployeeAttachment():void{
        $result=$this->maintenanceService->accessEmployeeData(1);
        $this->assertEquals('attachment.txt', $result['EmployeeAttachment'][0]['filename']);
        $this->assertEquals('6', $result['EmployeeAttachment'][0]['size']);
        $this->assertEquals('ZEdWemRBMEs=', $result['EmployeeAttachment'][0]['attachment']);
        $this->assertEquals('text/plain', $result['EmployeeAttachment'][0]['fileType']);
        $this->assertEquals('2021-02-23 00:00:00', $result['EmployeeAttachment'][0]['attachedTime']);
        $this->assertCount(1, $result['EmpPicture']);
    }

    public function testEmpEmergencyContact():void{
        $result=$this->maintenanceService->accessEmployeeData(1);
        $this->assertEquals('Yasitha', $result['EmpEmergencyContact'][0]['name']);
        $this->assertEquals('friend', $result['EmpEmergencyContact'][0]['relationship']);
        $this->assertEquals('0335445678', $result['EmpEmergencyContact'][0]['homePhone']);
        $this->assertEquals('0776734567', $result['EmpEmergencyContact'][0]['mobilePhone']);
        $this->assertEquals('0113456787', $result['EmpEmergencyContact'][0]['officePhone']);
        $this->assertCount(2, $result['EmpEmergencyContact']);
    }

    public function testEmpDependent():void{
        $result=$this->maintenanceService->accessEmployeeData(1);
        $this->assertEquals('yasitha', $result['EmpDependent'][0]['name']);
        $this->assertEquals('friend', $result['EmpDependent'][0]['relationship']);
        $this->assertEquals('child', $result['EmpDependent'][0]['relationshipType']);
        $this->assertEquals('2007-02-23', $result['EmpDependent'][0]['dateOfBirth']);
        $this->assertCount(2, $result['EmpDependent']);
    }

    public function testEmployeeImmigrationRecord():void{
        $result=$this->maintenanceService->accessEmployeeData(1);
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

    public function testEmpWorkExperience():void{
        $result=$this->maintenanceService->accessEmployeeData(1);
        $this->assertEquals('OrangeHRM', $result['EmpWorkExperience'][0]['employer']);
        $this->assertEquals('SE', $result['EmpWorkExperience'][0]['jobTitle']);
        $this->assertEquals('2005-04-03', $result['EmpWorkExperience'][0]['fromDate']);
        $this->assertEquals('2010-04-04', $result['EmpWorkExperience'][0]['toDate']);
        $this->assertEquals('Worked Hard', $result['EmpWorkExperience'][0]['comments']);
        $this->assertEquals('2', $result['EmpWorkExperience'][0]['internal']);
        $this->assertCount(2, $result['EmpWorkExperience']);
    }

    public function testEmployeeEducation():void{
        $result=$this->maintenanceService->accessEmployeeData(1);
        $this->assertEquals('PhD', $result['EmployeeEducation'][0]['education']);
        $this->assertEquals('ENG', $result['EmployeeEducation'][0]['major']);
        $this->assertEquals('2007', $result['EmployeeEducation'][0]['year']);
        $this->assertEquals('3.7', $result['EmployeeEducation'][0]['score']);
        $this->assertEquals('2006-03-04', $result['EmployeeEducation'][0]['startDate']);
        $this->assertEquals('2010-03-05', $result['EmployeeEducation'][0]['endDate']);
        $this->assertCount(2, $result['EmployeeEducation']);
    }

    public function testEmployeeSkill():void{
        $result=$this->maintenanceService->accessEmployeeData(1);
        $this->assertEquals('Driving', $result['EmployeeSkill'][0]['skill']);
        $this->assertEquals('4', $result['EmployeeSkill'][0]['yearsOfExp']);
        $this->assertEquals('com1', $result['EmployeeSkill'][0]['comments']);
        $this->assertCount(2, $result['EmployeeSkill']);
    }

    public function testEmployeeLanguage():void{
        $result=$this->maintenanceService->accessEmployeeData(1);
        $this->assertEquals('Spanish', $result['EmployeeLanguage'][0]['language']);
        $this->assertEquals('Speaking', $result['EmployeeLanguage'][0]['fluency']);
        $this->assertEquals('Poor', $result['EmployeeLanguage'][0]['competency']);
        $this->assertEquals('comment1', $result['EmployeeLanguage'][0]['comment']);
        $this->assertCount(2, $result['EmployeeLanguage']);
    }

    public function testEmployeeMembership():void{
        $result=$this->maintenanceService->accessEmployeeData(1);
        $this->assertEquals('membership 1', $result['EmployeeMembership'][0]['membership']);
        $this->assertEquals('4.00', $result['EmployeeMembership'][0]['subscriptionFee']);
        $this->assertEquals('individual', $result['EmployeeMembership'][0]['subscriptionPaidBy']);
        $this->assertEquals('Rs', $result['EmployeeMembership'][0]['subscriptionCurrency']);
        $this->assertEquals('2011-05-20', $result['EmployeeMembership'][0]['subscriptionCommenceDate']);
        $this->assertEquals('2011-05-22', $result['EmployeeMembership'][0]['subscriptionRenewalDate']);
        $this->assertCount(2, $result['EmployeeMembership']);
    }

    public function testEmpUsTaxExemption():void{
        $result=$this->maintenanceService->accessEmployeeData(1);
        $this->assertEquals('Single', $result['EmpUsTaxExemption'][0]['federalStatus']);
        $this->assertEquals('2', $result['EmpUsTaxExemption'][0]['federalExemptions']);
        $this->assertEquals('Alaska', $result['EmpUsTaxExemption'][0]['state']);
        $this->assertEquals('Single', $result['EmpUsTaxExemption'][0]['stateStatus']);
        $this->assertEquals('1', $result['EmpUsTaxExemption'][0]['stateExemptions']);
        $this->assertEquals('Alaska', $result['EmpUsTaxExemption'][0]['unemploymentState']);
        $this->assertCount(1, $result['EmpUsTaxExemption']);
    }

    public function testEmployeeLicense():void{
        $result=$this->maintenanceService->accessEmployeeData(1);
        $this->assertEquals('li1', $result['EmployeeLicense'][0]['license']);
        $this->assertEquals('2bja8768', $result['EmployeeLicense'][0]['licenseNo']);
        $this->assertEquals('2004-02-23', $result['EmployeeLicense'][0]['licenseIssuedDate']);
        $this->assertEquals('2007-02-23', $result['EmployeeLicense'][0]['licenseExpiryDate']);
        $this->assertCount(2, $result['EmployeeLicense']);
    }

    public function testEmployeeSalary():void{
        $result=$this->maintenanceService->accessEmployeeData(1);
        $this->assertEquals('Salary Grade A', $result['EmployeeSalary'][0]['payGrade']);
        $this->assertEquals('LKR', $result['EmployeeSalary'][0]['currencyType']);
        $this->assertEquals('10000.00', $result['EmployeeSalary'][0]['amount']);
        $this->assertEquals('Weekly', $result['EmployeeSalary'][0]['payPeriod']);
        $this->assertEquals('Main Salary', $result['EmployeeSalary'][0]['salaryName']);
        $this->assertEquals('com1', $result['EmployeeSalary'][0]['comment']);
        $this->assertCount(2, $result['EmployeeSalary']);
    }

    public function testEmpLocations():void{
        $result=$this->maintenanceService->accessEmployeeData(1);
        $this->assertEquals('location 1', $result['EmpLocations'][0]['location']);
        $this->assertCount(2, $result['EmpLocations']);
    }

    public function testEmpContract():void{
        $result=$this->maintenanceService->accessEmployeeData(1);
        $this->assertEquals('2020-05-23', $result['EmpContract'][0]['startDate']);
        $this->assertEquals('2021-05-23', $result['EmpContract'][0]['endDate']);
        $this->assertCount(2, $result['EmpContract']);
    }

    public function testUser():void{
        $result=$this->maintenanceService->accessEmployeeData(1);
        $this->assertEquals('samantha', $result['User'][0]['userName']);
        $this->assertEquals('2011-04-12', $result['User'][0]['dateEntered']);
        $this->assertEquals('2011-04-13', $result['User'][0]['dateModified']);
        $this->assertCount(2, $result['User']);
    }

    public function testLeaveRequest():void{
        $result=$this->maintenanceService->accessEmployeeData(1);
        $this->assertEquals('Casual', $result['LeaveRequest'][0]['leaveType']);
        $this->assertEquals('2010-08-30', $result['LeaveRequest'][0]['dateApplied']);
        $this->assertCount(3, $result['LeaveRequest']);
    }

    public function testLeaveRequestComment():void{
        $result=$this->maintenanceService->accessEmployeeData(1);
        $this->assertEquals('2010-08-30', $result['LeaveRequestComment'][0]['createdAt']);
        $this->assertEquals('2010-08-30', $result['LeaveRequestComment'][0]['createdBy']);
        $this->assertEquals('2010-08-30', $result['LeaveRequestComment'][0]['comment']);
        $this->assertCount(3, $result['LeaveRequestComment']);
    }


    public function testGetPurgeableEntities(): void
    {
        $purgeableEntities = $this->maintenanceService->getPurgeableEntities('gdpr_access_employee_strategy');
        $this->assertCount(24, $purgeableEntities);
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
        $result=$this->maintenanceService->getAccessStrategy('Employee', 'Basic', []);
        $this->assertInstanceOf(AccessStrategy::class, $result);
    }
}
