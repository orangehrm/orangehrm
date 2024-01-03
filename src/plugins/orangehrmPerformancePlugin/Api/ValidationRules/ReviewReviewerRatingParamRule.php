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

        foreach ($ratings as $rating) {
            if (count(array_keys($rating)) != 3 || !(isset($rating[SupervisorEvaluationAPI::PARAMETER_KPI_ID]))) {
                return false;
            }
            $kpiIdsForReviewId = $this->getPerformanceReviewService()->getPerformanceReviewDao()
                ->getKpiIdsForReviewId($this->reviewId);

            $kpiId = $rating[SupervisorEvaluationAPI::PARAMETER_KPI_ID];
            if (!(is_numeric($kpiId) && ($kpiId > 0))
                || !in_array($kpiId, $kpiIdsForReviewId)
            ) {
                return false;
            }

            $kpi = $this->getKpiService()->getKpiDao()->getKpiById($kpiId);
            $userRating = $rating[SupervisorEvaluationAPI::PARAMETER_RATING];

            if ($userRating === null || trim($userRating) === '') {
                continue;
            }

            $userRating = intval($userRating);

            if ($userRating < $kpi->getMinRating() || $userRating > $kpi->getMaxRating()) {
                return false;
            }
        }

        return true;
    }
}
