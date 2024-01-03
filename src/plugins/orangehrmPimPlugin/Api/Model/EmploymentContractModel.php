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
use OrangeHRM\Entity\EmpContract;

/**
 * @OA\Schema(
 *     schema="Pim-EmploymentContractModel",
 *     type="object",
 *     @OA\Property(property="startDate", type="string", format="date"),
 *     @OA\Property(property="endDate", type="string", format="date"),
 *     @OA\Property(property="contractAttachment", type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="filename", type="string"),
 *         @OA\Property(property="size", type="integer"),
 *         @OA\Property(property="fileType", type="string"),
 *         @OA\Property(property="attachedBy", type="string"),
 *         @OA\Property(property="attachedByName", type="string"),
 *         @OA\Property(property="attachedTime", type="string", format="time"),
 *         @OA\Property(property="attachedDate", type="string", format="date"),
 *     ),
 * )
 */
class EmploymentContractModel implements Normalizable
{
    use ModelTrait;

    public function __construct(EmpContract $empContract)
    {
        $this->setEntity($empContract);
        $this->setFilters(
            [
                ['getDecorator', 'getStartDate'],
                ['getDecorator', 'getEndDate'],
                ['getDecorator', 'getContractAttachment', 'getAttachId'],
                ['getDecorator', 'getContractAttachment', 'getFilename'],
                ['getDecorator', 'getContractAttachment', 'getSize'],
                ['getDecorator', 'getContractAttachment', 'getFileType'],
                ['getDecorator', 'getContractAttachment', 'getAttachedBy'],
                ['getDecorator', 'getContractAttachment', 'getAttachedByName'],
                ['getDecorator', 'getContractAttachment', 'getAttachedTime'],
                ['getDecorator', 'getContractAttachment', 'getAttachedDate'],
            ]
        );
        $this->setAttributeNames(
            [
                'startDate',
                'endDate',
                ['contractAttachment', 'id'],
                ['contractAttachment', 'filename'],
                ['contractAttachment', 'size'],
                ['contractAttachment', 'fileType'],
                ['contractAttachment', 'attachedBy'],
                ['contractAttachment', 'attachedByName'],
                ['contractAttachment', 'attachedTime'],
                ['contractAttachment', 'attachedDate'],
            ]
        );
    }
}
