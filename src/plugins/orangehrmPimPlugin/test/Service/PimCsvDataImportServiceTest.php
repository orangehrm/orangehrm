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

use OrangeHRM\Core\Service\CsvDataImportService;
use OrangeHRM\Pim\Service\PimCsvDataImportService;
use OrangeHRM\Tests\Util\TestCase;

/**
 * @group Pim
 */
class PimCsvDataImportServiceTest extends TestCase
{
    private PimCsvDataImportService $pimDataImportService;

    /**
     * Set up method
     */
    protected function setUp(): void
    {
        $this->pimDataImportService = new PimCsvDataImportService();
    }

    public function testGetCsvDataImportService()
    {
        $result = $this->pimDataImportService->getCsvDataImportService();
        $this->assertTrue($result instanceof CsvDataImportService);
    }

    public function testImport()
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
                ->will($this->returnValue(2));

        $this->pimDataImportService->setCsvDataImportService($mockService);

        $result = $this->pimDataImportService->import($fileContent);

        $this->assertEquals(2, $result);
    }
}
