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

namespace OrangeHRM\Tests\Recruitment\Entity;

use DateTime;
use OrangeHRM\Entity\Vacancy;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Recruitment
 * @group Entity
 */
class VacancyTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([Vacancy::class]);
    }

    public function testVacancyEntity(): void
    {
        $vacancy = new Vacancy();
        $vacancy->setName('Test Vacancy');
        $vacancy->getDecorator()->setJobTitleById(1);
        $vacancy->getDecorator()->setEmployeeById(1);
        $vacancy->setNumOfPositions(3);
        $vacancy->setIsPublished(true);
        $vacancy->setStatus(1);
        $vacancy->setDescription('Sample description');
        $vacancy->setDefinedTime(new DateTime('2022-05-25 10:42'));
        $vacancy->setUpdatedTime(new DateTime('2022-02-25 10:42'));
        $this->persist($vacancy);

        $vacancy = $this->getRepository(Vacancy::class)->find(1);
        $this->assertEquals('Test Vacancy', $vacancy->getName());
        $this->assertEquals('Sample description', $vacancy->getDescription());
    }
}
