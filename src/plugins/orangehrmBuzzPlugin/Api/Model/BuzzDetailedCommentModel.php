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

namespace OrangeHRM\Buzz\Api\Model;

use OrangeHRM\Buzz\Traits\Service\BuzzServiceTrait;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Entity\BuzzComment;
use OrangeHRM\Entity\EmployeeTerminationRecord;

/**
 * @OA\Schema(
 *     schema="Buzz-BuzzDetailedCommentModel",
 *     type="object",
 *     @OA\Property(
 *         property="comment",
 *         type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="text", type="string"),
 *         @OA\Property(property="numOfLikes", type="integer"),
 *         @OA\Property(property="liked", type="boolean"),
 *         @OA\Property(property="createdDate", type="string"),
 *         @OA\Property(property="createdTime", type="string"),
 *     ),
 *     @OA\Property(
 *         property="share",
 *         type="object",
 *         @OA\Property(property="id", type="integer"),
 *     ),
 *     @OA\Property(
 *         property="employee",
 *         type="object",
 *         @OA\Property(property="empNumber", type="integer"),
 *         @OA\Property(property="lastName", type="string"),
 *         @OA\Property(property="firstName", type="string"),
 *         @OA\Property(property="middleName", type="string"),
 *         @OA\Property(property="employeeId", type="string"),
 *         @OA\Property(property="terminationId", type="integer")
 *     ),
 *     @OA\Property(
 *         property="permission",
 *         type="object",
 *         @OA\Property(property="canUpdate", type="boolean"),
 *         @OA\Property(property="canDelete", type="boolean"),
 *     )
 * )
 */
class BuzzDetailedCommentModel implements Normalizable
{
    use BuzzServiceTrait;

    private BuzzComment $buzzComment;

    public function __construct(BuzzComment $buzzComment)
    {
        $this->buzzComment = $buzzComment;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        $empNumber = $this->buzzComment->getEmployee()->getEmpNumber();
        $terminationRecord = $this->buzzComment->getEmployee()->getEmployeeTerminationRecord();
        return [
            'comment' => [
                'id' => $this->buzzComment->getId(),
                'text' => $this->buzzComment->getText(),
                'numOfLikes' => $this->buzzComment->getNumOfLikes(),
                'liked' => $this->buzzComment->getDecorator()->isAuthEmployeeLiked(),
                'createdDate' => $this->buzzComment->getDecorator()->getCreatedDate(),
                'createdTime' => $this->buzzComment->getDecorator()->getCreatedTime(),
            ],
            'share' => [
                'id' => $this->buzzComment->getShare()->getId(),
            ],
            'employee' => [
                'empNumber' => $empNumber,
                'lastName' => $this->buzzComment->getEmployee()->getLastName(),
                'firstName' => $this->buzzComment->getEmployee()->getFirstName(),
                'middleName' => $this->buzzComment->getEmployee()->getMiddleName(),
                'employeeId' => $this->buzzComment->getEmployee()->getEmployeeId(),
                'terminationId' => $terminationRecord instanceof EmployeeTerminationRecord
                    ? $terminationRecord->getId() : null,
            ],
            'permission' => [
                'canUpdate' => $this->getBuzzService()->canUpdateBuzzComment($empNumber),
                'canDelete' => $this->getBuzzService()->canDeleteBuzzComment($empNumber),
            ],
        ];
    }
}
