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

namespace OrangeHRM\Tests\Leave\Entity;

use OrangeHRM\Entity\WorkWeek;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Leave
 * @group Entity
 */
class WorkWeekTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([WorkWeek::class]);
    }

    public function testWorkWeek(): void
    {
        $workWeek = new WorkWeek();
        $workWeek->setMonday(WorkWeek::WORKWEEK_LENGTH_FULL_DAY);
        $workWeek->setTuesday(WorkWeek::WORKWEEK_LENGTH_FULL_DAY);
        $workWeek->setWednesday(WorkWeek::WORKWEEK_LENGTH_FULL_DAY);
        $workWeek->setThursday(WorkWeek::WORKWEEK_LENGTH_FULL_DAY);
        $workWeek->setFriday(WorkWeek::WORKWEEK_LENGTH_HALF_DAY);
        $workWeek->setSaturday(WorkWeek::WORKWEEK_LENGTH_NON_WORKING_DAY);
        $workWeek->setSunday(WorkWeek::WORKWEEK_LENGTH_FULL_DAY);

        $this->persist($workWeek);

        /** @var WorkWeek[] $workWeeks */
        $workWeeks = $this->getRepository(WorkWeek::class)->findAll();
        $resultWorkWeek = $workWeeks[0];
        $this->assertEquals(1, $resultWorkWeek->getId());
        $this->assertEquals(0, $resultWorkWeek->getMonday());
        $this->assertEquals(0, $resultWorkWeek->getTuesday());
        $this->assertEquals(0, $resultWorkWeek->getWednesday());
        $this->assertEquals(0, $resultWorkWeek->getThursday());
        $this->assertEquals(4, $resultWorkWeek->getFriday());
        $this->assertEquals(8, $resultWorkWeek->getSaturday());
        $this->assertEquals(0, $resultWorkWeek->getSunday());
    }
}
