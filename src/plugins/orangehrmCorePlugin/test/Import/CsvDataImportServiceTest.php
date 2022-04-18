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

namespace OrangeHRM\Tests\Core\Import;

use OrangeHRM\Core\Exception\CSVUploadFailedException;
use OrangeHRM\Core\Service\CsvDataImportService;
use OrangeHRM\Tests\Util\TestCase;

/**
 * @group Core
 * @group Import
 */
class CsvDataImportServiceTest extends TestCase
{
    private CsvDataImportService $csvDataImportService;
    private array $headerValues;

    protected function setUp(): void
    {
        $this->csvDataImportService = new CsvDataImportService();
        $this->headerValues = [
            "first_name",
            "middle_name",
            "last_name",
            "employee_id",
            "other_id",
            "driver's_license_no",
            "license_expiry_date",
            "gender",
            "marital_status",
            "nationality",
            "date_of_birth",
            "address_street_1",
            "address_street_2",
            "city",
            "state/province",
            "zip/postal_code",
            "country",
            "home_telephone",
            "mobile",
            "work_telephone",
            "work_email",
            "other_email"
        ];
    }

    public function testGetEmployeeArrayFromCSVWithNormalCSVContent(): void
    {
        $fileContent = "first_name,middle_name,last_name,employee_id,other_id,driver's_license_no,license_expiry_date,gender,marital_status,nationality,date_of_birth,address_street_1,address_street_2,city,state/province,zip/postal_code,country,home_telephone,mobile,work_telephone,work_email,other_email\n" .
            "Devi,,DS,001,,,,,,,,,,,,,,,,,,";

        $result = $this->csvDataImportService->getEmployeeArrayFromCSV($fileContent, $this->headerValues);
        $expected = [
            [...$this->headerValues],
            [
                "Devi",
                "",
                "DS",
                "001",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                ""
            ]
        ];

        $this->assertEquals($expected, $result);
    }

    public function testGetEmployeeArrayFromCSVWithLineBreaksInCSV(): void
    {
        $fileContent = "first_name,middle_name,last_name,employee_id,other_id,driver's_license_no,license_expiry_date,gender,marital_status,nationality,date_of_birth,address_street_1,address_street_2,city,state/province,zip/postal_code,country,home_telephone,mobile,work_telephone,work_email,other_email\n" .
            "\"Devi\n\nRomesh\",,DS,\"001\n\n\",,,,,,,,,,,,,,,,,,\n";

        $result = $this->csvDataImportService->getEmployeeArrayFromCSV($fileContent, $this->headerValues);
        $expected = [
            [...$this->headerValues],
            [
                "Devi  Romesh",
                "",
                "DS",
                "001",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                ""
            ]
        ];

        $this->assertEquals($expected, $result);
    }

    public function testGetEmployeeArrayFromCSVWithSpecialCharactersInCSV(): void
    {
        $fileContent = "first_name,middle_name,last_name,employee_id,other_id,driver's_license_no,license_expiry_date,gender,marital_status,nationality,date_of_birth,address_street_1,address_street_2,city,state/province,zip/postal_code,country,home_telephone,mobile,work_telephone,work_email,other_email\n" .
            "\"Devi\rRomesh\",\"Narada\t\",DS,\"001\n\n\",,,,,,,,\"Number\vStreet\x00\",,,,,,,,,,\n";

        $result = $this->csvDataImportService->getEmployeeArrayFromCSV($fileContent, $this->headerValues);
        $expected = [
            [...$this->headerValues],
            [
                "Devi Romesh",
                "Narada",
                "DS",
                "001",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "Number Street",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                ""
            ]
        ];

        $this->assertEquals($expected, $result);
    }

    public function testGetEmployeeArrayFromCSVWithNonCSVContent(): void
    {
        $fileContent = "<?php\n\necho \"Hello World\";\n";
        $this->expectException(CSVUploadFailedException::class);
        $this->expectExceptionMessage("The CSV File Is Not Valid");

        $this->csvDataImportService->getEmployeeArrayFromCSV($fileContent, $this->headerValues);
    }
}
