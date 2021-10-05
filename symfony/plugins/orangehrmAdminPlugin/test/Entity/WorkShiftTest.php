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

namespace OrangeHRM\Tests\Admin\Entity;

use DateTime;
use OrangeHRM\Entity\WorkShift;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group @Admin
 * @group @Entity
 */
class WorkShiftTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateTables([WorkShift::class]);
    }

    public function testCountryEntity(): void
    {
        $workShift = new WorkShift();
        $workShift->setName("TEST");
        $workShift->setHoursPerDay(8.00);
        $workShift->setStartTime(new DateTime('08:00:00'));
        $workShift->setEndTime(new DateTime('17:00:00'));
        $this->persist($workShift);

        /** @var WorkShift $workShift */
        $workShift = $this->getRepository(WorkShift::class)->find(1);
        $this->assertEquals('TEST', $workShift->getName());
        $this->assertEquals(8.00, $workShift->getHoursPerDay());
        $this->assertEquals('08:00:00', $workShift->getStartTime()->format('H:i:s'));
        $this->assertEquals('17:00:00', $workShift->getEndTime()->format('H:i:s'));
    }
}
