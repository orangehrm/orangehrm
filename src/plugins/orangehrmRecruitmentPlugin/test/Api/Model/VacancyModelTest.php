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

namespace OrangeHRM\Tests\Recruitment\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\NormalizeException;
use OrangeHRM\Entity\Vacancy;
use OrangeHRM\Recruitment\Api\Model\VacancyModel;
use OrangeHRM\Tests\Util\TestCase;

class VacancyModelTest extends TestCase
{
    /**
     * @return void
     * @throws NormalizeException
     */
    public function testToArray(): void
    {
        $resultArray = [
            'id' => 1,
            'name' => 'Title',
            'description' => 'Description',
            'status' => true,
            'isPublished' => false,
            'numOfPositions' => null,
        ];

        $vacancy = new Vacancy();
        $vacancy->setId(1);
        $vacancy->setName('Title');
        $vacancy->setDescription('Description');
        $vacancyModel = new VacancyModel($vacancy);
        $this->assertEquals($resultArray, $vacancyModel->toArray());
    }
}
