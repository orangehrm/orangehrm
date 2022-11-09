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

namespace OrangeHRM\Tests\Pim\Api\Model;

use DateTime;
use OrangeHRM\Admin\Dto\WorkShiftStartAndEndTime;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Framework\Services;
use OrangeHRM\Pim\Api\Model\EmployeeWorkShiftModel;
use OrangeHRM\Tests\Util\KernelTestCase;

/**
 * @group Pim
 * @group Model
 */
class EmployeeWorkShiftModelTest extends KernelTestCase
{
    public function testToArray()
    {
        $resultArray = [
            "startTime" => '09:00',
            "endTime" => '17:00',
        ];
        $startDate = new DateTime('2021-12-25 09:00');
        $endDate = new DateTime('2021-12-25 17:00');
        $defaultWorkShift = new WorkShiftStartAndEndTime($startDate, $endDate);

        $employeeWorkShiftModel = new EmployeeWorkShiftModel($defaultWorkShift);
        $this->createKernelWithMockServices(
            [
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            ]
        );
        $this->assertEquals($resultArray, $employeeWorkShiftModel->toArray());
    }
}
