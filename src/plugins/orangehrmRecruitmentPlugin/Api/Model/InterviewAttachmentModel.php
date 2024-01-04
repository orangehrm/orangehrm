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
use OrangeHRM\Recruitment\Dto\RecruitmentAttachment;

/**
 * @OA\Schema(
 *     schema="Recruitment-InterviewAttachmentModel",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="interviewId", type="integer"),
 *     @OA\Property(property="attachment", type="object",
 *         @OA\Property(property="fileName", type="string"),
 *         @OA\Property(property="fileType", type="string"),
 *         @OA\Property(property="fileSize", type="integer")
 *     ),
 *     @OA\Property(property="comment", type="string")
 * )
 */
class InterviewAttachmentModel implements Normalizable
{
    use ModelTrait;

    public function __construct(RecruitmentAttachment $interviewAttachment)
    {
        $this->setEntity($interviewAttachment);
        $this->setFilters(
            [
                'id',
                'fkIdentity', //this represents the interviewId
                'fileName',
                'fileType',
                'fileSize',
                'comment',
            ]
        );
        $this->setAttributeNames(
            [
                'id',
                'interviewId',
                ['attachment', 'fileName'],
                ['attachment', 'fileType'],
                ['attachment', 'fileSize'],
                'comment',
            ]
        );
    }
}
