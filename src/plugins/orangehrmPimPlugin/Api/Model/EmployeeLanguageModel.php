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

namespace OrangeHRM\Pim\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\ModelTrait;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Entity\EmployeeLanguage;

/**
 * @OA\Schema(
 *     schema="Pim-EmployeeLanguageModel",
 *     type="object",
 *     @OA\Property(property="language", type="object",
 *         @OA\Property(property="id", description="The numerical ID of the language", type="integer"),
 *         @OA\Property(property="name", description="The name of the language", type="string")
 *     ),
 *     @OA\Property(property="fluency", type="object",
 *         @OA\Property(property="id", description="The numerical ID of the fluency", type="integer"),
 *         @OA\Property(property="name", description="The name of the fluency", type="string")
 *     ),
 *     @OA\Property(property="competency", type="object",
 *         @OA\Property(property="id", description="The numerical ID of the competency", type="integer"),
 *         @OA\Property(property="name", description="The name of the competency", type="string")
 *     ),
 *     @OA\Property(property="comment", description="The comment regarding the language and fluency", type="string")
 * )
 */
class EmployeeLanguageModel implements Normalizable
{
    use ModelTrait;

    /**
     * @param EmployeeLanguage $employeeLanguage
     */
    public function __construct(EmployeeLanguage $employeeLanguage)
    {
        $this->setEntity($employeeLanguage);
        $this->setFilters(
            [
                ['getLanguage', 'getId'],
                ['getLanguage', 'getName'],
                ['getFluency'],
                ['getDecorator', 'getFluency'],
                ['getCompetency'],
                ['getDecorator', 'getCompetency'],
                'comment',
            ]
        );
        $this->setAttributeNames(
            [
                ['language', 'id'],
                ['language', 'name'],
                ['fluency', 'id'],
                ['fluency', 'name'],
                ['competency', 'id'],
                ['competency', 'name'],
                'comment',
            ]
        );
    }
}
