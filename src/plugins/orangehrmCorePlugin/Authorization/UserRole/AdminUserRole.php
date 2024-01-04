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

use OrangeHRM\Admin\Service\LocationService;
use OrangeHRM\Buzz\Traits\Service\BuzzServiceTrait;
use OrangeHRM\Dashboard\Traits\Service\QuickLaunchServiceTrait;
use OrangeHRM\Entity\Candidate;
use OrangeHRM\Entity\CandidateHistory;
use OrangeHRM\Entity\Customer;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Interview;
use OrangeHRM\Entity\InterviewAttachment;
use OrangeHRM\Entity\Location;
use OrangeHRM\Entity\PerformanceReview;
use OrangeHRM\Entity\PerformanceTracker;
use OrangeHRM\Entity\PerformanceTrackerLog;
use OrangeHRM\Entity\Project;
use OrangeHRM\Entity\User;
use OrangeHRM\Entity\UserRole;
use OrangeHRM\Entity\Vacancy;
use OrangeHRM\Entity\VacancyAttachment;
use OrangeHRM\Performance\Traits\Service\PerformanceReviewServiceTrait;
use OrangeHRM\Performance\Traits\Service\PerformanceTrackerLogServiceTrait;
use OrangeHRM\Performance\Traits\Service\PerformanceTrackerServiceTrait;
use OrangeHRM\Pim\Traits\Service\EmployeeServiceTrait;
use OrangeHRM\Recruitment\Dto\CandidateActionHistory;
use OrangeHRM\Recruitment\Traits\Service\CandidateServiceTrait;
use OrangeHRM\Recruitment\Traits\Service\RecruitmentAttachmentServiceTrait;
use OrangeHRM\Recruitment\Traits\Service\VacancyServiceTrait;
use OrangeHRM\Time\Traits\Service\CustomerServiceTrait;
use OrangeHRM\Time\Traits\Service\ProjectServiceTrait;

class AdminUserRole extends AbstractUserRole
{
    use EmployeeServiceTrait;
    use ProjectServiceTrait;
    use CustomerServiceTrait;
    use PerformanceTrackerServiceTrait;
    use PerformanceReviewServiceTrait;
    use PerformanceTrackerLogServiceTrait;
    use CandidateServiceTrait;
    use RecruitmentAttachmentServiceTrait;
    use VacancyServiceTrait;
    use QuickLaunchServiceTrait;
    use BuzzServiceTrait;

    protected ?LocationService $locationService = null;

    /**
     * @return LocationService
     */
    protected function getLocationService(): LocationService
    {
        if (!$this->locationService instanceof LocationService) {
            $this->locationService = new LocationService();
        }
        return $this->locationService;
    }

    /**
     * @inheritDoc
     */
    protected function getAccessibleIdsForEntity(string $entityType, array $requiredPermissions = []): array
    {
        switch ($entityType) {
            case Employee::class:
                return $this->getAccessibleEmployeeIds($requiredPermissions);
            case User::class:
                return $this->getAccessibleSystemUserIds($requiredPermissions);
            case UserRole::class:
                return $this->getAccessibleUserRoleIds($requiredPermissions);
            case Location::class:
                return $this->getAccessibleLocationIds($requiredPermissions);
            case Project::class:
                return $this->getAccessibleProjectIds($requiredPermissions);
            case Customer::class:
                return $this->getAccessibleCustomerIds($requiredPermissions);
            case Vacancy::class:
                return $this->getAccessibleVacancyIds($requiredPermissions);
            case VacancyAttachment::class:
                return $this->getAccessibleVacancyAttachmentIds();
            case PerformanceTracker::class:
                return $this->getAccessibleTrackerIds($requiredPermissions);
            case PerformanceReview::class:
                return $this->getAccessibleReviewIds($requiredPermissions);
            case PerformanceTrackerLog::class:
                return $this->getAccessibleTrackerLogIds($requiredPermissions);
            case Candidate::class:
                return $this->getAccessibleCandidateIds($requiredPermissions);
            case Interview::class:
                return $this->getAccessibleInterviewIds($requiredPermissions);
            case InterviewAttachment::class:
                return $this->getAccessibleInterviewAttachmentIds($requiredPermissions);
            case CandidateHistory::class:
                return $this->getAccessibleCandidateHistoryIds($requiredPermissions);
            case CandidateActionHistory::class:
                return $this->getAccessibleCandidateActionHistoryIds($requiredPermissions);
            default:
                return [];
        }
    }

    /**
     * @param array $requiredPermissions
     * @return int[]
     */
    protected function getAccessibleEmployeeIds(array $requiredPermissions = []): array
    {
        return $this->getEmployeeService()->getEmployeeDao()->getEmpNumberList(false);
    }

    /**
     * @param array $requiredPermissions
     * @return int[]
     */
    protected function getAccessibleLocationIds(array $requiredPermissions = []): array
    {
        return $this->getLocationService()->getLocationDao()->getLocationsIdList();
    }

    /**
     * @param array $requiredPermissions
     * @return int[]
     */
    protected function getAccessibleSystemUserIds(array $requiredPermissions = []): array
    {
        return $this->getUserService()
            ->geUserDao()
            ->getSystemUserIdList();
    }

    /**
     * @param array $requiredPermissions
     * @return int[]
     */
    protected function getAccessibleUserRoleIds(array $requiredPermissions = []): array
    {
        $userRoles = $this->getUserService()
            ->geUserDao()
            ->getAssignableUserRoles();

        $ids = [];

        foreach ($userRoles as $role) {
            $ids[] = $role->getId();
        }

        return $ids;
    }

    /**
     * @param array $entities
     * @return Employee[]
     */
    public function getEmployeesWithRole(array $entities = []): array
    {
        return $this->getUserService()->getEmployeesByUserRole($this->roleName);
    }

    /**
     * @param array $requiredPermissions
     * @return int[]
     */
    protected function getAccessibleProjectIds(array $requiredPermissions = []): array
    {
        /**
         * @return int[]
         */
        return $this->getProjectService()
            ->getProjectDao()
            ->getProjectIdList();
    }

    /**
     * @param array $requiredPermissions
     * @return int[]
     */
    protected function getAccessibleCustomerIds(array $requiredPermissions): array
    {
        return $this->getCustomerService()
            ->getCustomerDao()
            ->getCustomerIdList();
    }

    /**
     * @param array $requiredPermissions
     * @return int[]
     */
    protected function getAccessibleVacancyIds(array $requiredPermissions = []): array
    {
        return $this->getVacancyService()->getVacancyDao()->getVacancyIdList();
    }

    /**
     * @param array $requiredPermissions
     * @return int[]
     */
    protected function getAccessibleTrackerIds(array $requiredPermissions = []): array
    {
        return $this->getPerformanceTrackerService()
            ->getPerformanceTrackerDao()
            ->getPerformanceTrackerIdList();
    }

    /**
     * @param array $requiredPermissions
     * @return int[]
     */
    protected function getAccessibleReviewIds(array $requiredPermissions = []): array
    {
        return $this->getPerformanceReviewService()
            ->getPerformanceReviewDao()
            ->getReviewIdList();
    }

    /**
     * @param array $requiredPermissions
     * @return int[]
     */
    protected function getAccessibleTrackerLogIds(array $requiredPermissions = []): array
    {
        return $this->getPerformanceTrackerLogService()
            ->getPerformanceTrackerLogDao()
            ->getPerformanceTrackerLogsIdList();
    }

    /**
     * @param array $requiredPermissions
     * @return int[]
     */
    protected function getAccessibleCandidateIds(array $requiredPermissions = []): array
    {
        return $this->getCandidateService()
            ->getCandidateDao()
            ->getCandidateIdList();
    }

    /**
     * @param array $requiredPermissions
     * @return int[]
     */
    protected function getAccessibleInterviewIds(array $requiredPermissions = []): array
    {
        return $this->getCandidateService()
            ->getCandidateDao()
            ->getInterviewIdList();
    }

    /**
     * @param array $requiredPermissions
     * @return int[]
     */
    protected function getAccessibleInterviewAttachmentIds(array $requiredPermissions = []): array
    {
        return $this->getRecruitmentAttachmentService()
            ->getRecruitmentAttachmentDao()
            ->getInterviewAttachmentIdList();
    }

    /**
     * @param array $requiredPermissions
     * @return int[]
     */
    private function getAccessibleVacancyAttachmentIds(array $requiredPermissions = []): array
    {
        return $this->getRecruitmentAttachmentService()
            ->getRecruitmentAttachmentDao()
            ->getVacancyAttachmentIdList();
    }

    /**
     * @param array $requiredPermissions
     * @return int[]
     */
    private function getAccessibleCandidateHistoryIds(array $requiredPermissions = []): array
    {
        return $this->getCandidateService()
            ->getCandidateDao()
            ->getCandidateHistoryIdList();
    }

    /**
     * @param array $requiredPermissions
     * @return int[]
     */
    private function getAccessibleCandidateActionHistoryIds(array $requiredPermissions = []): array
    {
        $candidateActionHistory = new CandidateActionHistory();
        return $candidateActionHistory->getAccessibleCandidateActionHistoryIds();
    }

    /**
     * @inheritDoc
     */
    public function getAccessibleQuickLaunchList(array $requiredPermissions): array
    {
        return $this->getQuickLaunchService()
            ->getQuickLaunchDao()
            ->getQuickLaunchList();
    }
}
