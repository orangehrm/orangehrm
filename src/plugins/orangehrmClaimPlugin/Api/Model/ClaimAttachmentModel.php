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
 *
 */

namespace OrangeHRM\Claim\Api\Model;

use OpenApi\Annotations as OA;
use OrangeHRM\Claim\Dto\PartialClaimAttachment;
use OrangeHRM\Core\Api\V2\Serializer\ModelTrait;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;

class ClaimAttachmentModel implements Normalizable
{
    use ModelTrait;

    /**
     * @OA\Schema(
     *     schema="Claim-AttachmentModel",
     *     type="object",
     *     @OA\Property(
     *         property="requestId",
     *         type="integer"
     *     ),
     *     @OA\Property(
     *         property="attachmentId",
     *         type="integer"
     *     ),
     *     @OA\Property(
     *         property="fileName",
     *         type="string"
     *     ),
     *     @OA\Property(
     *         property="fileType",
     *         type="string"
     *     ),
     *     @OA\Property(
     *         property="fileSize",
     *         type="integer"
     *     ),
     *     @OA\Property(
     *         property="fileDescription",
     *         type="string"
     *     ),
     *     @OA\Property(
     *         property="date",
     *         type="string"
     *     )
     * )
     */
    public function __construct(PartialClaimAttachment $claimAttachment)
    {
        $this->setEntity($claimAttachment);
        $this->setFilters(
            [
                'attachId',
                'filename',
                'fileType',
                'size',
                'description',
                'attachedTime'
            ]
        );
        $this->setAttributeNames(
            [
                'id',
                'fileName',
                'fileType',
                'size',
                'fileDescription',
                'date'
            ]
        );
    }
}
