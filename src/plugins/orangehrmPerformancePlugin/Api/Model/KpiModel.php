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

namespace OrangeHRM\Performance\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\ModelTrait;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Entity\Kpi;
use OrangeHRM\Performance\Traits\Service\KpiServiceTrait;

/**
 * @OA\Schema(
 *     schema="Performance-KpiModel",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="title", type="string"),
 *     @OA\Property(
 *         property="jobTitle",
 *         type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="name", type="string"),
 *         @OA\Property(property="deleted", type="boolean"),
 *     ),
 *     @OA\Property(property="minRating", type="integer"),
 *     @OA\Property(property="maxRating", type="integer"),
 *     @OA\Property(property="isDefault", type="boolean"),
 *     @OA\Property(property="deletable", type="boolean")
 * )
 */
class KpiModel implements Normalizable
{
    use ModelTrait {
        ModelTrait::toArray as entityToArray;
    }
    use KpiServiceTrait;

    public function __construct(Kpi $kpi)
    {
        $this->setEntity($kpi);
        $this->setFilters(
            [
                'id',
                'title',
                ['getJobTitle', 'getId'],
                ['getJobTitle', 'getJobTitleName'],
                ['getJobTitle', 'isDeleted'],
                'minRating',
                'maxRating',
                ['isDefaultKpi'],
            ]
        );
        $this->setAttributeNames(
            [
                'id',
                'title',
                ['jobTitle', 'id'],
                ['jobTitle', 'name'],
                ['jobTitle', 'deleted'],
                'minRating',
                'maxRating',
                'isDefault',
            ]
        );
    }

    public function toArray(): array
    {
        $deletable = $this->getKpiService()->getKpiDao()->isKpiDeletable(
            $this->getEntity()->getId()
        );
        $result = $this->entityToArray();
        $result['deletable'] = $deletable;
        return $result;
    }
}
