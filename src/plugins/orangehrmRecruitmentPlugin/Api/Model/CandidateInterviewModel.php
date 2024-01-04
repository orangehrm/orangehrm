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
use OrangeHRM\Entity\Interview;

/**
 * @OA\Schema(
 *     schema="Recruitment-CandidateInterviewModel",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
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
 *         @OA\Property(property="name", type="string")
 *     ),
 *     @OA\Property(
 *         property="interviewers",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(property="empNumber", type="integer"),
 *             @OA\Property(property="lastName", type="string"),
 *             @OA\Property(property="firstName", type="string"),
 *             @OA\Property(property="middleName", type="string"),
 *             @OA\Property(property="terminationId", type="integer")
 *         )
 *     ),
 *     @OA\Property(property="interviewDate", type="string", format="date"),
 *     @OA\Property(property="interviewTime", type="string", format="time"),
 *     @OA\Property(property="note", type="string")
 * )
 */
class CandidateInterviewModel implements Normalizable
{
    use ModelTrait;

    public function __construct(Interview $interview)
    {
        $this->setEntity($interview);
        $this->setFilters([
            'id',
            'interviewName',
            ['getCandidate', 'getId'],
            ['getCandidate', 'getFirstName'],
            ['getCandidate', 'getMiddleName'],
            ['getCandidate', 'getLastName'],
            ['getCandidateVacancy', 'getVacancy', 'getId'],
            ['getCandidateVacancy', 'getVacancy', 'getName'],
            ['getInterviewers', ['getEmpNumber', 'getLastName', 'getFirstName', 'getMiddleName','getEmployeeTerminationRecord']],
            ['getDecorator', 'getInterviewDate'],
            ['getDecorator', 'getInterviewTime'],
            'note',
        ]);

        $this->setAttributeNames([
            'id',
            'name',
            ['candidate', 'id'],
            ['candidate', 'firstName'],
            ['candidate', 'middleName'],
            ['candidate', 'lastName'],
            ['vacancy', 'id'],
            ['vacancy', 'name'],
            ['interviewers', ['empNumber', 'lastName', 'firstName', 'middleName','terminationId']],
            'interviewDate',
            'interviewTime',
            'note'
        ]);
    }
}
