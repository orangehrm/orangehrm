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

namespace OrangeHRM\Core\Authorization\UserRole;

use OrangeHRM\Buzz\Traits\Service\BuzzServiceTrait;
use OrangeHRM\Dashboard\Traits\Service\QuickLaunchServiceTrait;
use OrangeHRM\Entity\PerformanceReview;
use OrangeHRM\Performance\Traits\Service\PerformanceReviewServiceTrait;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Entity\PerformanceTracker;
use OrangeHRM\Entity\PerformanceTrackerLog;
use OrangeHRM\Performance\Traits\Service\PerformanceTrackerLogServiceTrait;
use OrangeHRM\Performance\Traits\Service\PerformanceTrackerServiceTrait;

class EssUserRole extends AbstractUserRole
{
    use AuthUserTrait;
    use PerformanceTrackerServiceTrait;
    use PerformanceTrackerLogServiceTrait;
    use PerformanceReviewServiceTrait;
    use QuickLaunchServiceTrait;
    use BuzzServiceTrait;

    public const ALLOWED_REVIEW_STATUSES = 'allowed_review_statuses';

    /**
     * @inheritDoc
     */
    protected function getAccessibleIdsForEntity(string $entityType, array $requiredPermissions = []): array
    {
        switch ($entityType) {
            case PerformanceReview::class:
                return $this->getAccessibleReviewIds($requiredPermissions);
            case PerformanceTracker::class:
                return $this->getAccessiblePerformanceTrackerIdsForESS($requiredPermissions);
            case PerformanceTrackerLog::class:
                return $this->getAccessiblePerformanceTrackerLogIdsForESS($requiredPermissions);
            default:
                return [];
        }
    }

    /**
     * @return int[]
     */
    protected function getAccessibleReviewIds(array $requiredPermissions = []): array
    {
        $allowedStatuses = [];
        if (isset($requiredPermissions[self::ALLOWED_REVIEW_STATUSES]) &&
            is_array($requiredPermissions[self::ALLOWED_REVIEW_STATUSES])
        ) {
            $allowedStatuses = $requiredPermissions[self::ALLOWED_REVIEW_STATUSES];
        }

        $empNumber = $this->getEmployeeNumber();
        return $this->getPerformanceReviewService()
            ->getPerformanceReviewDao()
            ->getSelfReviewIds($empNumber, $allowedStatuses);
    }

    /**
     * @param array $requiredPermissions
     * @return int[]
     */
    protected function getAccessiblePerformanceTrackerIdsForESS(array $requiredPermissions = []): array
    {
        return $this->getPerformanceTrackerService()
            ->getPerformanceTrackerDao()
            ->getTrackerIdsByEmpNumber($this->getAuthUser()->getEmpNumber());
    }

    /**
     * @param array $requiredPermissions
     * @return int[]
     */
    protected function getAccessiblePerformanceTrackerLogIdsForESS(array $requiredPermissions = []): array
    {
        return $this->getPerformanceTrackerLogService()
            ->getPerformanceTrackerLogDao()
            ->getPerformanceTrackerLogIdsByUserId($this->getAuthUser()->getUserId());
    }

    /**
     * @inheritDoc
     */
    public function getAccessibleQuickLaunchList(array $requiredPermissions): array
    {
        return $this->getQuickLaunchService()
            ->getQuickLaunchDao()
            ->getQuickLaunchListForESS();
    }
}
