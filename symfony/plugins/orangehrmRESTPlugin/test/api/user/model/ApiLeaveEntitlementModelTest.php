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

use Orangehrm\Rest\Api\Leave\Entity\LeaveEntitlement;
use Orangehrm\Rest\Api\User\Model\LeaveEntitlementModel;

/**
 * @group API
 */
class ApiLeaveEntitlementModelTest extends PHPUnit\Framework\TestCase
{
    public function testToArray()
    {
        $testArray = [
            'id' => '1',
            'validFrom' => '2020-01-01',
            'validTo' => '2020-12-31',
            'creditedDate' => '2019-12-31',
        ];

        $leaveEntitlement = new LeaveEntitlement("1");
        $leaveEntitlement->setEntitlementType('Annual');
        $leaveEntitlement->setValidFrom('2020-01-01');
        $leaveEntitlement->setValidTo('2020-12-31');
        $leaveEntitlement->setCreditedDate('2019-12-31');
        $leaveEntitlement->setDays('14');

        $leaveEntitlementModel = new LeaveEntitlementModel($leaveEntitlement);

        $this->assertEquals($testArray, $leaveEntitlementModel->toArray());
    }
}
