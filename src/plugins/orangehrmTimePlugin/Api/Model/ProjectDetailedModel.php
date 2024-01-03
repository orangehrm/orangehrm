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

namespace OrangeHRM\Time\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\ModelTrait;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Entity\Project;

/**
 * @OA\Schema(
 *     schema="Time-ProjectDetailedModel",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="description", type="description"),
 *     @OA\Property(property="customer", type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="name", type="string"),
 *         @OA\Property(property="deleted", type="boolean"),
 *     ),
 *     @OA\Property(property="deleted", type="boolean"),
 *     @OA\Property(
 *         property="projectAdmins",
 *         type="array",
 *         @OA\Items(
 *             @OA\Property(property="empNumber", type="integer"),
 *             @OA\Property(property="lastName", type="string"),
 *             @OA\Property(property="firstName", type="string"),
 *             @OA\Property(property="middleName", type="string"),
 *             @OA\Property(property="terminationId", type="integer")
 *         )
 *     )
 * )
 */
class ProjectDetailedModel implements Normalizable
{
    use ModelTrait {
        ModelTrait::toArray as entityToArray;
    }

    public function __construct(Project $project)
    {
        $this->setEntity($project);
        $this->setFilters([
            'id',
            'name',
            'description',
            ['getCustomer', 'getId'],
            ['getCustomer', 'getName'],
            ['getCustomer', 'isDeleted'],
            ['isDeleted'],
        ]);

        $this->setAttributeNames([
            'id',
            'name',
            'description',
            ['customer', 'id'],
            ['customer', 'name'],
            ['customer', 'deleted'],
            'deleted',
        ]);
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        $normalizedProject = $this->entityToArray();
        $normalizedProject['projectAdmins'] = [];
        /** @var Project $project */
        $project = $this->getEntity();
        foreach ($project->getProjectAdmins() as $projectAdmin) {
            $normalizedProjectAdmin = [];
            $normalizedProjectAdmin['empNumber'] = $projectAdmin->getEmpNumber();
            $normalizedProjectAdmin['lastName'] = $projectAdmin->getLastName();
            $normalizedProjectAdmin['firstName'] = $projectAdmin->getFirstName();
            $normalizedProjectAdmin['middleName'] = $projectAdmin->getMiddleName();
            $normalizedProjectAdmin['terminationId'] = $projectAdmin->getEmployeeTerminationRecord() ?
                $projectAdmin->getEmployeeTerminationRecord()->getId() : null;
            $normalizedProject['projectAdmins'][] = $normalizedProjectAdmin;
        }
        return $normalizedProject;
    }
}
