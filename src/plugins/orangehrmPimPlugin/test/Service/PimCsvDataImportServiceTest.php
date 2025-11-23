<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Tests\Pim\Service;

use OrangeHRM\Admin\Service\CountryService;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Service\CsvDataImportService;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Province;
use OrangeHRM\Framework\Services;
use OrangeHRM\Pim\Service\EmployeeService;
use OrangeHRM\Pim\Service\PimCsvDataImportService;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 */
class PimCsvDataImportServiceTest extends KernelTestCase
{
    private PimCsvDataImportService $pimDataImportService;

    public static function setUpBeforeClass(): void
    {
        TestDataService::populate(Config::get(Config::TEST_DIR) . '/phpunit/fixtures/Country.yaml', true);
        TestDataService::populate(Config::get(Config::TEST_DIR) . '/phpunit/fixtures/Nationality.yaml', true);
    }

    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([Employee::class, Province::class]);
        $this->pimDataImportService = new PimCsvDataImportService();
    }

    public function testGetCsvDataImportService()
    {
        $result = $this->pimDataImportService->getCsvDataImportService();
        $this->assertTrue($result instanceof CsvDataImportService);
    }

    public function testImport(): void
    {
        $fileContent = "first_name,middle_name,last_name,employee_id,other_id,driver's_license_no,license_expiry_date,gender,marital_status,nationality,date_of_birth,address_street_1,address_street_2,city,state/province,zip/postal_code,country,home_telephone,mobile,work_telephone,work_email,other_email
Yasiru,,Nilan,Emp-002,,,,Male,,,1992-10-29,“Wimalasewana”,Hingurupanagala,Kotapola,Southern,81500,Sri
Lanka0412271230,,702132850,,yasiru@orangehrmlive.com,nilanyasiru@gmail.com
Krishan,,Madhushanka,,,,,Male,,,,,,,,,,,,,,
";
        $importType = 'pim';
        $mockService = $this->getMockBuilder(CsvDataImportService::class)->getMock();
        $mockService->expects($this->once())
                ->method('import')
                ->with($fileContent, $importType)
                ->will($this->returnValue(['success' => 3, 'failed' => 0, 'failedRows' => []]));

        $this->pimDataImportService->setCsvDataImportService($mockService);

        $result = $this->pimDataImportService->import($fileContent);

        $this->assertEquals(['success' => 3, 'failed' => 0, 'failedRows' => []], $result);
    }

    public function testImportWithDuplicateEmployeeId(): void
    {
        $fileContent = "
first_name,middle_name,last_name,employee_id,other_id,driver's_license_no,license_expiry_date,gender,marital_status,nationality,date_of_birth,address_street_1,address_street_2,city,state/province,zip/postal_code,country,home_telephone,mobile,work_telephone,work_email,other_email
Abbey,,Kyla,0002,,,,Female,,,,,,,,,,,,,,
John,,Smith,0002,,,,Male,,,,,,,,,,,,,,
Kevin,,Mathews,0003,,,,Male,,,,,,,,,,,,,,
Lisa,,Andrews,0004,,,,,,,,,,,,,,,,,,
Jasmine,,Morgan,0005,,,,,,,,,,,,,,,,,,
Jacqueline,,White,0006,,,,,,,,,,,,,,,,,,
Linda,Jane,Anderson,0007,,,,,,,,,,,,,,,,,,
";

        $employeeService = $this->getMockBuilder(EmployeeService::class)
            ->onlyMethods(['saveEmployee'])
            ->getMock();
        $employeeService->expects($this->any())
            ->method('saveEmployee')
            ->willReturnCallback(function (Employee $employee) {
                $this->getEntityManager()->persist($employee);
                $this->getEntityManager()->flush();
                return $employee;
            });
        $this->createKernelWithMockServices([
            Services::EMPLOYEE_SERVICE => $employeeService,
        ]);
        $result = $this->pimDataImportService->import(trim($fileContent));

        $this->assertEquals(['success' => 6, 'failed' => 1, 'failedRows' => [3]], $result);
    }

    public function testImportWithInvalidFirstNameAndLastName(): void
    {
        $fileContent = "
first_name,middle_name,last_name,employee_id,other_id,driver's_license_no,license_expiry_date,gender,marital_status,nationality,date_of_birth,address_street_1,address_street_2,city,state/province,zip/postal_code,country,home_telephone,mobile,work_telephone,work_email,other_email
Abbey,,Kyla,1,abcdefghijklmnopqrstuvwxyzabcd,,,Female,,,,,,,,,,,,,,
John,,Smith,2,,,,Male,,,,,,,,,,,,,,
Kevin,,Mathews,3,,,,Male,,,,,,,,,,,,,,
Lisa,,Andrews,4,,,,,,,,,,,,,,,,,,
abcdefghijklmnopqrstuvwxyzabcde,,Morgan,5,,,,,,,,,,,,,,,,,,
Jacqueline,,White,6,,0123456789,,,,,,,,,,,,,,,,
Linda,Jane,abcdefghijklmnopqrstuvwxyzabcde,7,,,,,,,,,,,,,,,,,,
";

        $employeeService = $this->getMockBuilder(EmployeeService::class)
            ->onlyMethods(['saveEmployee'])
            ->getMock();
        $employeeService->expects($this->any())
            ->method('saveEmployee')
            ->willReturnCallback(function (Employee $employee) {
                $this->getEntityManager()->persist($employee);
                $this->getEntityManager()->flush();
                return $employee;
            });
        $this->createKernelWithMockServices([
            Services::EMPLOYEE_SERVICE => $employeeService,
        ]);
        $result = $this->pimDataImportService->import(trim($fileContent));

        $this->assertEquals(['success' => 5, 'failed' => 2, 'failedRows' => [6, 8]], $result);
    }

    public function testImportWithInvalidLicenseExpiryDate(): void
    {
        $fileContent = "
first_name,middle_name,last_name,employee_id,other_id,driver's_license_no,license_expiry_date,gender,marital_status,nationality,date_of_birth,address_street_1,address_street_2,city,state/province,zip/postal_code,country,home_telephone,mobile,work_telephone,work_email,other_email
Abbey,,Kyla,1,,,,Female,,,,,,,,,,,,,,
John,,Smith,2,,,,Male,,,,,,,,,,,,,,
Kevin,,Mathews,3,,,invalid date,Male,,,,,,,,,,,,,,
Lisa,,Andrews,4,,,2021-02-30,,,,,,,,,,,,,,,
Jasmine,,Morgan,5,,,,,,,,,,,,,,,,,,
Jacqueline,,White,6,,,2022-10-14,,,,,,,,,,,,,,,
Linda,Jane,Anderson,7,,,,,,,,,,,,,,,,,,
";

        $employeeService = $this->getMockBuilder(EmployeeService::class)
            ->onlyMethods(['saveEmployee'])
            ->getMock();
        $employeeService->expects($this->any())
            ->method('saveEmployee')
            ->willReturnCallback(function (Employee $employee) {
                $this->getEntityManager()->persist($employee);
                $this->getEntityManager()->flush();
                return $employee;
            });
        $this->createKernelWithMockServices([
            Services::EMPLOYEE_SERVICE  => $employeeService,
        ]);
        $result = $this->pimDataImportService->import(trim($fileContent));

        // $this->assertEquals(['success' => 5, 'failed' => 2, 'failedRows' => [4,5]], $result); // should fail for invalid dates
        $this->assertEquals(['success' => 7, 'failed' => 0, 'failedRows' => []], $result);
    }

    public function testImportWithInvalidMaritalStatus(): void
    {
        $fileContent = "
first_name,middle_name,last_name,employee_id,other_id,driver's_license_no,license_expiry_date,gender,marital_status,nationality,date_of_birth,address_street_1,address_street_2,city,state/province,zip/postal_code,country,home_telephone,mobile,work_telephone,work_email,other_email
Abbey,,Kyla,1,,,,Female,,,,,,,,,,,,,,
John,,Smith,2,,,,Male,,,,,,,,,,,,,,
Kevin,,Mathews,3,,,,Male,single,,,,,,,,,,,,,
Lisa,,Andrews,4,,,,,Married,,,,,,,,,,,,,
Jasmine,,Morgan,5,,,,,other,,,,,,,,,,,,,
Jacqueline,,White,6,,,,,invalid,,,,,,,,,,,,,
Linda,Jane,Anderson,7,,,,,,,,,,,,,,,,,,
";

        $employeeService = $this->getMockBuilder(EmployeeService::class)
            ->onlyMethods(['saveEmployee'])
            ->getMock();
        $employeeService->expects($this->any())
            ->method('saveEmployee')
            ->willReturnCallback(function (Employee $employee) {
                $this->getEntityManager()->persist($employee);
                $this->getEntityManager()->flush();
                return $employee;
            });
        $this->createKernelWithMockServices([
            Services::EMPLOYEE_SERVICE => $employeeService,
        ]);
        $result = $this->pimDataImportService->import(trim($fileContent));

        $this->assertEquals(['success' => 7, 'failed' => 0, 'failedRows' => []], $result);
    }

    public function testImportWithNationality(): void
    {
        $fileContent = "
first_name,middle_name,last_name,employee_id,other_id,driver's_license_no,license_expiry_date,gender,marital_status,nationality,date_of_birth,address_street_1,address_street_2,city,state/province,zip/postal_code,country,home_telephone,mobile,work_telephone,work_email,other_email
Abbey,,Kyla,1,,,,Female,,,,,,,,,,,,,,
John,,Smith,2,,,,Male,,,,,,,,,,,,,,
Kevin,,Mathews,3,,,,Male,,Australian,,,,,,,,,,,,
Lisa,,Andrews,4,,,,,,,,,,,,,,,,,,
Jasmine,,Morgan,5,,,,,,invalid,,,,,,,,,,,,
Jacqueline,,White,6,,,,,,,,,,,,,,,,,,
Linda,Jane,Anderson,7,,,,,,,,,,,,,,,,,,
";

        $employeeService = $this->getMockBuilder(EmployeeService::class)
            ->onlyMethods(['saveEmployee'])
            ->getMock();
        $employeeService->expects($this->any())
            ->method('saveEmployee')
            ->willReturnCallback(function (Employee $employee) {
                $this->getEntityManager()->persist($employee);
                $this->getEntityManager()->flush();
                return $employee;
            });
        $this->createKernelWithMockServices([
            Services::EMPLOYEE_SERVICE => $employeeService,
        ]);
        $result = $this->pimDataImportService->import(trim($fileContent));

        $this->assertEquals(['success' => 7, 'failed' => 0, 'failedRows' => []], $result);
    }

    public function testImportWithContactDetails(): void
    {
        $fileContent = "
first_name,middle_name,last_name,employee_id,other_id,driver's_license_no,license_expiry_date,gender,marital_status,nationality,date_of_birth,address_street_1,address_street_2,city,state/province,zip/postal_code,country,home_telephone,mobile,work_telephone,work_email,other_email
Abbey,,Kyla,1,,,,Female,,,,,,,,,,,,,,
John,,Smith,2,,,,Male,,,,,,,,11114,,1122334455,,,,
Kevin,,Mathews,3,,,,Male,,,,Street 1,Street 2,City,Western,,,,1122334455,1122334455,,
Lisa,,Andrews,4,,,,,,,,,,,,,,,,1122334455,,
Jasmine,,Morgan,5,,,,,,,,,,,,,United States,,,,,
Jacqueline,,White,6,,,,,,,,,,,,,,,,,,
Linda,Jane,Anderson,7,,,,,,,,,,,,,,,,,,
";

        $employeeService = $this->getMockBuilder(EmployeeService::class)
            ->onlyMethods(['saveEmployee'])
            ->getMock();
        $employeeService->expects($this->any())
            ->method('saveEmployee')
            ->willReturnCallback(function (Employee $employee) {
                $this->getEntityManager()->persist($employee);
                $this->getEntityManager()->flush();
                return $employee;
            });
        $this->createKernelWithMockServices([
            Services::EMPLOYEE_SERVICE => $employeeService,
            Services::COUNTRY_SERVICE => new CountryService(),
        ]);
        $result = $this->pimDataImportService->import(trim($fileContent));

        $this->assertEquals(['success' => 7, 'failed' => 0, 'failedRows' => []], $result);
    }

    public function testImportWithDuplicateMailForSameEmployee(): void
    {
        $fileContent = "
first_name,middle_name,last_name,employee_id,other_id,driver's_license_no,license_expiry_date,gender,marital_status,nationality,date_of_birth,address_street_1,address_street_2,city,state/province,zip/postal_code,country,home_telephone,mobile,work_telephone,work_email,other_email
Abbey,,Kyla,1,,,,Female,,,,,,,,,,,,,Abbey@example.org,
John,,Smith,2,,,,Male,,,,,,,,,,,,,John@example.org,
Kevin,,Mathews,3,,,,Male,,,,,,,,,,,,,Kevin@example.org,Kevin@example.org
Lisa,,Andrews,4,,,,,,,,,,,,,,,,,,
Jasmine,,Morgan,5,,,,,,,,,,,,,,,,,,
Jacqueline,,White,6,,,,,,,,,,,,,,,,,,Jacqueline@example.org
Linda,Jane,Anderson,7,,,,,,,,,,,,,,,,,,
";

        $employeeService = $this->getMockBuilder(EmployeeService::class)
            ->onlyMethods(['saveEmployee'])
            ->getMock();
        $employeeService->expects($this->any())
            ->method('saveEmployee')
            ->willReturnCallback(function (Employee $employee) {
                $this->getEntityManager()->persist($employee);
                $this->getEntityManager()->flush();
                return $employee;
            });
        $this->createKernelWithMockServices([
            Services::EMPLOYEE_SERVICE => $employeeService,
        ]);
        $result = $this->pimDataImportService->import(trim($fileContent));

        $this->assertEquals(['success' => 6, 'failed' => 1, 'failedRows' => [4]], $result);
    }

    public function testImportWithDuplicateMail(): void
    {
        $fileContent = "
first_name,middle_name,last_name,employee_id,other_id,driver's_license_no,license_expiry_date,gender,marital_status,nationality,date_of_birth,address_street_1,address_street_2,city,state/province,zip/postal_code,country,home_telephone,mobile,work_telephone,work_email,other_email
Abbey,,Kyla,1,,,,Female,,,,,,,,,,,,,Abbey@example.org,
John,,Smith,2,,,,Male,,,,,,,,,,,,,John@example.org,
Kevin,,Mathews,3,,,,Male,,,,,,,,,,,,,Kevin@example.org,
Lisa,,Andrews,4,,,,,,,,,,,,,,,,,,
Jasmine,,Morgan,5,,,,,,,,,,,,,,,,,,Kevin@example.org
Jacqueline,,White,6,,,,,,,,,,,,,,,,,,Jacqueline@example.org
Linda,Jane,Anderson,7,,,,,,,,,,,,,,,,,John@example.org,
";

        $employeeService = $this->getMockBuilder(EmployeeService::class)
            ->onlyMethods(['saveEmployee'])
            ->getMock();
        $employeeService->expects($this->any())
            ->method('saveEmployee')
            ->willReturnCallback(function (Employee $employee) {
                $this->getEntityManager()->persist($employee);
                $this->getEntityManager()->flush();
                return $employee;
            });
        $this->createKernelWithMockServices([
            Services::EMPLOYEE_SERVICE => $employeeService,
        ]);
        $result = $this->pimDataImportService->import(trim($fileContent));

        $this->assertEquals(['success' => 5, 'failed' => 2, 'failedRows' => [6, 8]], $result);
    }

    public function testImportWithoutFirstNameAndLastName(): void
    {
        $fileContent = "
first_name,middle_name,last_name,employee_id,other_id,driver's_license_no,license_expiry_date,gender,marital_status,nationality,date_of_birth,address_street_1,address_street_2,city,state/province,zip/postal_code,country,home_telephone,mobile,work_telephone,work_email,other_email
Abbey,,Kyla,1,,,,Female,,,,,,,,,,,,,,
John,,Smith,2,,,,Male,,,,,,,,11114,,1122334455,,,,
Kevin,,Mathews,3,,,,Male,Single,Australian,1998-10-01,Street 1,Street 2,City,Western,1234,Australia,334455667,1122334455,1122334455,Kevin@example.org,Kevin@example.com
Lisa,,Andrews,4,,,,,,,,,,,,,,,,1122334455,,
Jasmine,,Morgan,5,,,,,,,,,,,,,United State,,,,,
,,White,6,,,,,,,,,,,,,,,,,,
Linda,Jane,,7,,,,,,,,,,,,,,,,,,
";

        $employeeService = $this->getMockBuilder(EmployeeService::class)
            ->onlyMethods(['saveEmployee'])
            ->getMock();
        $employeeService->expects($this->any())
            ->method('saveEmployee')
            ->willReturnCallback(function (Employee $employee) {
                $this->getEntityManager()->persist($employee);
                $this->getEntityManager()->flush();
                return $employee;
            });
        $this->createKernelWithMockServices([
            Services::EMPLOYEE_SERVICE => $employeeService,
            Services::COUNTRY_SERVICE => new CountryService(),
        ]);
        $result = $this->pimDataImportService->import(trim($fileContent));

        $this->assertEquals(['success' => 5, 'failed' => 2, 'failedRows' => [7, 8]], $result);
    }
}
