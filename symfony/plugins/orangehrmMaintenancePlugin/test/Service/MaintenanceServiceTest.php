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
        $this->fixture=Config::get(Config::PLUGINS_DIR).'/orangehrmMaintenancePlugin/test/fixtures/EmployeeDao.yml';
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
        $this->assertCount(1, $result['Employee']);
    }


    public function testGetAccessStrategy(): void
    {
        $result=$this->maintenanceService->getAccessStrategy('Employee', 'Basic', []);
        $this->assertInstanceOf(AccessStrategy::class, $result);
    }
}
