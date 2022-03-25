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
        $this->assertEquals('', $result['EmpDependent'][0]['relationship']);
        $this->assertEquals('HVN0003472', $result['EmployeeImmigrationRecord'][0]['number']);
        $this->assertEquals('SE', $result['EmpWorkExperience'][0]['jobTitle']);

        $this->assertCount(1, $result['Employee']);
        $this->assertCount(2, $result['PerformanceReview']);
        $this->assertCount(2, $result['ReviewerRating']);
        $this->assertCount(2, $result['Reviewer']);
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
        $result=$this->maintenanceService->getAccessStrategy('Employee', 'Basic', []);
        $this->assertInstanceOf(AccessStrategy::class, $result);
    }
}
