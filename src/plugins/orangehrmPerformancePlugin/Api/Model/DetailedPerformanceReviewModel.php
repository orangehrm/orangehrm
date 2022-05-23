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
 */

namespace OrangeHRM\Performance\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Core\Traits\Service\NormalizerServiceTrait;
use OrangeHRM\Entity\PerformanceReview;
use OrangeHRM\Performance\Traits\Service\PerformanceReviewServiceTrait;

class DetailedPerformanceReviewModel implements Normalizable
{
    use NormalizerServiceTrait;
    use PerformanceReviewServiceTrait;

    private PerformanceReview $performanceReview;

    /**
     * @param PerformanceReview $performanceReview
     */
    public function __construct(PerformanceReview $performanceReview)
    {
        $this->performanceReview = $performanceReview;
    }

    public function toArray(): array
    {
        $selfReviewStatus = $this->getPerformanceReviewService()->getPerformanceReviewDao()
            ->getPerformanceSelfReviewStatus($this->performanceReview);
        return [
            'id' => $this->performanceReview->getId(),
            'jobTitle' => [
                'id' => $this->performanceReview->getJobTitle()->getId(),
                'name' => $this->performanceReview->getJobTitle()->getJobTitleName(),
                'deleted' => $this->performanceReview->getJobTitle()->isDeleted(),
            ],
            'subunit' => [
                'id' => $this->performanceReview->getSubunit()->getId(),
                'name' => $this->performanceReview->getSubunit()->getName(),
            ],
            'reviewPeriodStart' => $this->performanceReview->getDecorator()->getReviewPeriodStart(),
            'reviewPeriodEnd' => $this->performanceReview->getDecorator()->getReviewPeriodEnd(),
            'dueDate' => $this->performanceReview->getDecorator()->getDueDate(),
            'overallStatus' => [
                'statusId' => $this->performanceReview->getStatusId(),
                'statusName' => $this->performanceReview->getDecorator()->getStatusName(),
            ],
            'selfReviewStatus' => $selfReviewStatus,
        ];
    }
}
