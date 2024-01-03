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

namespace OrangeHRM\Claim\Api\Model;

use OpenApi\Annotations as OA;
use OrangeHRM\Core\Api\V2\Serializer\ModelTrait;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Entity\ClaimExpense;

/**
 * @OA\Schema(
 *     schema="Claim-ExpenseModel",
 *     type="object",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *     ),
 *     @OA\Property(
 *         property="claimRequest",
 *         type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="referenceId", type="integer"),
 *     ),
 *     @OA\Property(
 *         property="expenseType",
 *         type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="name", type="string"),
 *         @OA\Property(property="status", type="boolean"),
 *         @OA\Property(property="isDeleted", type="boolean"),
 *     ),
 *     @OA\Property(
 *         property="amount",
 *         type="float",
 *     ),
 *     @OA\Property(
 *         property="note",
 *         type="string",
 *     ),
 * )
 */
class ClaimExpenseModel implements Normalizable
{
    use ModelTrait;

    public function __construct(ClaimExpense $claimExpense)
    {
        $this->setEntity($claimExpense);
        $this->setFilters(
            [
                'id',
                ['getExpenseType', 'getId'],
                ['getExpenseType', 'getName'],
                ['getExpenseType', 'getStatus'],
                ['getExpenseType', 'isDeleted'],
                'amount',
                'note',
                ['getDecorator', 'getDate']
            ]
        );
        $this->setAttributeNames(
            [
                'id',
                ['expenseType', 'id'],
                ['expenseType', 'name'],
                ['expenseType', 'status'],
                ['expenseType', 'isDeleted'],
                'amount',
                'note',
                'date'
            ]
        );
    }
}
