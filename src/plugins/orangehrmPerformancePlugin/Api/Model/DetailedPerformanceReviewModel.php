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
use OrangeHRM\Entity\PerformanceReview;
use OrangeHRM\Performance\Traits\Service\PerformanceReviewServiceTrait;

/**
 * @OA\Schema(
 *     schema="Performance-DetailedPerformanceReviewModel",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(
 *         property="jobTitle",
 *         type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="name", type="string"),
 *         @OA\Property(property="deleted", type="boolean"),
 *     ),
 *     @OA\Property(
 *         property="subunit",
 *         type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="name", type="string"),
 *     ),
 *     @OA\Property(property="reviewPeriodStart", type="number"),
 *     @OA\Property(property="reviewPeriodEnd", type="number"),
 *     @OA\Property(property="dueDate", type="number"),
 *     @OA\Property(
 *         property="overallStatus",
 *         type="object",
 *         @OA\Property(property="statusId", type="integer"),
 *         @OA\Property(property="statusName", type="string"),
 *     )
 * )
 */
class DetailedPerformanceReviewModel implements Normalizable
{
    use ModelTrait {
        ModelTrait::toArray as entityToArray;
    }
    use PerformanceReviewServiceTrait;

    /**
     * @param PerformanceReview $performanceReview
     */
    public function __construct(PerformanceReview $performanceReview)
    {
        $this->setEntity($performanceReview);
        $this->setFilters([
            'id',
            ['getJobTitle', 'getId'],
            ['getJobTitle', 'getJobTitleName'],
            ['getJobTitle', 'isDeleted'],
            ['getSubunit', 'getId'],
            ['getSubunit', 'getName'],
            ['getDecorator', 'getReviewPeriodStart'],
            ['getDecorator', 'getReviewPeriodEnd'],
            ['getDecorator', 'getDueDate'],
            'statusId',
            ['getDecorator', 'getStatusName'],
        ]);
        $this->setAttributeNames([
            'id',
            ['jobTitle', 'id'],
            ['jobTitle', 'name'],
            ['jobTitle', 'deleted'],
            ['subunit', 'id'],
            ['subunit', 'name'],
            'reviewPeriodStart',
            'reviewPeriodEnd',
            'dueDate',
            ['overallStatus', 'statusId'],
            ['overallStatus', 'statusName'],
        ]);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $selfReviewStatus = $this->getPerformanceReviewService()->getPerformanceReviewDao()
            ->getPerformanceSelfReviewStatus($this->getEntity());
        $result = $this->entityToArray();
        $result['selfReviewStatus'] = $selfReviewStatus;
        return $result;
    }
}
