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

namespace OrangeHRM\Performance\Api\ValidationRules;

use OrangeHRM\Core\Api\V2\Validator\Rules\AbstractRule;
use OrangeHRM\Performance\Api\SupervisorEvaluationAPI;
use OrangeHRM\Performance\Traits\Service\KpiServiceTrait;
use OrangeHRM\Performance\Traits\Service\PerformanceReviewServiceTrait;

class ReviewReviewerRatingParamRule extends AbstractRule
{
    use PerformanceReviewServiceTrait;
    use KpiServiceTrait;

    private int $reviewId;

    /**
     * @param int $reviewId
     */
    public function __construct(int $reviewId)
    {
        $this->reviewId = $reviewId;
    }

    /**
     * @inheritDoc
     */
    public function validate($ratings): bool
    {
        if (! is_array($ratings)) {
            return false;
        }

        /*$kpisForReview = $this->getPerformanceReviewService()->getPerformanceReviewDao()
            ->getKpiIdsForReviewId($this->reviewId);*/

        foreach ($ratings as $rating) {
            if (count(array_keys($rating)) != 3) {
                return false;
            }
            if (!(isset($rating[SupervisorEvaluationAPI::PARAMETER_KPI_ID]))) {
                return false;
            }

            $kpisForReview = $this->getPerformanceReviewService()->getPerformanceReviewDao()
                ->getKpiIdsForReviewId($this->reviewId);

            $kpiId = $rating[SupervisorEvaluationAPI::PARAMETER_KPI_ID];
            if (! (is_numeric($kpiId) && ($kpiId > 0))) {
                return false;
            }
            if (! in_array($rating[SupervisorEvaluationAPI::PARAMETER_KPI_ID], array_column($kpisForReview, 'id'))) {
                return false;
            }

            $kpi = $this->getKpiService()->getKpiDao()->getKpiById($kpiId);
            if (! ($kpi->getMinRating() <= $rating[SupervisorEvaluationAPI::PARAMETER_RATING] && $rating[SupervisorEvaluationAPI::PARAMETER_RATING] <= $kpi->getMaxRating())) {
                return false;
            }
        }

        return true;
    }
}
