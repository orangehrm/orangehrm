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

namespace OrangeHRM\Recruitment\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\ModelTrait;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Entity\CandidateHistory;

/**
 * @OA\Schema(
 *     schema="Recruitment-CandidateHistoryDefaultModel",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(
 *         property="candidate",
 *         type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="firstName", type="string"),
 *         @OA\Property(property="middleName", type="string"),
 *         @OA\Property(property="lastName", type="string")
 *     ),
 *     @OA\Property(
 *         property="vacancy",
 *         type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="name", type="string"),
 *         @OA\Property(
 *             property="hiringManager",
 *             type="object",
 *             @OA\Property(property="empNumber", type="string"),
 *             @OA\Property(property="firstName", type="string"),
 *             @OA\Property(property="middleName", type="string"),
 *             @OA\Property(property="lastName", type="string"),
 *             @OA\Property(property="terminationId", type="integer")
 *         )
 *     ),
 *     @OA\Property(property="note", type="string"),
 *     @OA\Property(property="action", type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="label", type="string")
 *     )
 * )
 */
class CandidateHistoryDefaultModel implements Normalizable
{
    use ModelTrait;

    public function __construct(CandidateHistory $candidateHistory)
    {
        $this->setEntity($candidateHistory);
        $this->setFilters([
            'id',
            ['getCandidate', 'getId'],
            ['getCandidate', 'getFirstName'],
            ['getCandidate', 'getMiddleName'],
            ['getCandidate', 'getLastName'],
            ['getVacancy', 'getId'],
            ['getVacancy', 'getName'],
            ['getVacancy', 'getHiringManager', 'getEmpNumber'],
            ['getVacancy', 'getHiringManager', 'getFirstName'],
            ['getVacancy', 'getHiringManager', 'getMiddleName'],
            ['getVacancy', 'getHiringManager', 'getLastName'],
            ['getVacancy', 'getHiringManager', 'getEmployeeTerminationRecord', 'getId'],
            'note',
            'action',
            ['getDecorator', 'getCandidateHistoryAction'],
        ]);

        $this->setAttributeNames([
            'id',
            ['candidate', 'id'],
            ['candidate', 'firstName'],
            ['candidate', 'middleName'],
            ['candidate', 'lastName'],
            ['vacancy', 'id'],
            ['vacancy', 'name'],
            ['vacancy', 'hiringManager', 'empNumber'],
            ['vacancy', 'hiringManager', 'firstName'],
            ['vacancy', 'hiringManager', 'middleName'],
            ['vacancy', 'hiringManager', 'lastName'],
            ['vacancy', 'hiringManager', 'terminationId'],
            'note',
            ['action', 'id'],
            ['action', 'label']
        ]);
    }
}
