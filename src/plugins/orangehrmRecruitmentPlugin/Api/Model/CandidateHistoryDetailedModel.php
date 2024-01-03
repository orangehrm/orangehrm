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
 *     schema="Recruitment-CandidateHistoryDetailedModel",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(
 *         property="action",
 *         type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="label", type="string")
 *     ),
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
 *             property="hiringManger",
 *             type="object",
 *             @OA\Property(property="empNumber", type="integer"),
 *             @OA\Property(property="firstName", type="string"),
 *             @OA\Property(property="middleName", type="string"),
 *             @OA\Property(property="lastName", type="string"),
 *             @OA\Property(property="terminationId", type="integer")
 *         )
 *     ),
 *     @OA\Property(
 *         property="performedBy",
 *         type="object",
 *         @OA\Property(property="empNumber", type="integer"),
 *         @OA\Property(property="firstName", type="string"),
 *         @OA\Property(property="middleName", type="string"),
 *         @OA\Property(property="lastName", type="string"),
 *         @OA\Property(property="terminationId", type="integer")
 *     ),
 *     @OA\Property(property="interview", type="object",
 *         @OA\Property(property="id", type="integer")
 *     ),
 *     @OA\Property(property="performedDate", type="string", format="date"),
 *     @OA\Property(property="note", type="string")
 * )
 */
class CandidateHistoryDetailedModel implements Normalizable
{
    use ModelTrait;

    public function __construct(CandidateHistory $candidateHistory)
    {
        $this->setEntity($candidateHistory);
        $this->setFilters([
            'id',
            'action',
            ['getDecorator', 'getCandidateHistoryAction'],
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
            ['getPerformedBy', 'getEmpNumber'],
            ['getPerformedBy', 'getFirstName'],
            ['getPerformedBy', 'getMiddleName'],
            ['getPerformedBy', 'getLastName'],
            ['getPerformedBy', 'getEmployeeTerminationRecord', 'getId'],
            ['getInterview', 'getId'],
            ['getDecorator', 'getPerformedDate'],
            'note',
        ]);

        $this->setAttributeNames([
            'id',
            ['action', 'id'],
            ['action', 'label'],
            ['candidate', 'id'],
            ['candidate', 'firstName'],
            ['candidate', 'middleName'],
            ['candidate', 'lastName'],
            ['vacancy', 'id'],
            ['vacancy', 'name'],
            ['vacancy', 'hiringManger', 'empNumber'],
            ['vacancy', 'hiringManger', 'firstName'],
            ['vacancy', 'hiringManger', 'middleName'],
            ['vacancy', 'hiringManger', 'lastName'],
            ['vacancy', 'hiringManger', 'terminationId'],
            ['performedBy', 'empNumber'],
            ['performedBy', 'firstName'],
            ['performedBy', 'middleName'],
            ['performedBy', 'lastName'],
            ['performedBy', 'terminationId'],
            ['interview', 'id'],
            'performedDate',
            'note'
        ]);
    }
}
