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
use OrangeHRM\Pim\Dto\PartialEmployeeAttachment;

/**
 * @OA\Schema(
 *     schema="Pim-EmployeeAttachmentModel",
 *     type="object",
 *     @OA\Property(property="id", description="The numerical ID of the attachment", type="integer"),
 *     @OA\Property(property="description", description="The description of the attachment", type="string"),
 *     @OA\Property(property="filename", description="The file name of the attachment", type="string"),
 *     @OA\Property(property="size", description="The size of the attachment", type="integer"),
 *     @OA\Property(property="fileType", description="The type of the attachment", type="string"),
 *     @OA\Property(property="attachedBy", description="The employee number of attachment uploader", type="integer"),
 *     @OA\Property(property="attachedByName", description="The name of the attachment uploader", type="string"),
 *     @OA\Property(property="attachedTime", description="The time the attachment was uploaded", type="string", format="time"),
 *     @OA\Property(property="attachedDate", description="The date the attachment was uplaoded", type="string", format="date")
 * )
 */
class EmployeeAttachmentModel implements Normalizable
{
    use ModelTrait;

    /**
     * @param PartialEmployeeAttachment $partialEmployeeAttachment
     */
    public function __construct(PartialEmployeeAttachment $partialEmployeeAttachment)
    {
        $this->setEntity($partialEmployeeAttachment);
        $this->setFilters(
            [
                'attachId',
                'description',
                'filename',
                'size',
                'fileType',
                'attachedBy',
                'attachedByName',
                'attachedTime',
                'attachedDate',
            ]
        );
        $this->setAttributeNames(
            [
                'id',
                'description',
                'filename',
                'size',
                'fileType',
                'attachedBy',
                'attachedByName',
                'attachedTime',
                'attachedDate',
            ]
        );
    }
}
