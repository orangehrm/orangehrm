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
use OrangeHRM\Entity\ClaimRequest;

class ClaimRequestSummaryModel implements Normalizable
{
    use ModelTrait;

    /**
     * @OA\Schema(
     *     schema="Claim-ClaimRequestSummaryModel",
     *     type="object",
     *     @OA\Property(property="id", type="integer"),
     *     @OA\Property(property="referenceId", type="integer")
     * )
     */
    public function __construct(ClaimRequest $claimRequest)
    {
        $this->setEntity($claimRequest);
        $this->setFilters(
            [
                'id',
                'referenceId'
            ]
        );
        $this->setAttributeNames(
            [
                'id',
                'referenceId'
            ]
        );
    }
}
