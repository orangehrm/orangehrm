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

use OrangeHRM\Entity\Candidate;
use OrangeHRM\Entity\CandidateHistory;
use OrangeHRM\Entity\Interview;
use OrangeHRM\Entity\InterviewAttachment;
use OrangeHRM\Entity\Vacancy;
use OrangeHRM\Recruitment\Dto\CandidateActionHistory;
use OrangeHRM\Recruitment\Traits\Service\CandidateServiceTrait;
use OrangeHRM\Recruitment\Traits\Service\RecruitmentAttachmentServiceTrait;

class InterviewerUserRole extends AbstractUserRole
{
    use CandidateServiceTrait;
    use RecruitmentAttachmentServiceTrait;

    /**
     * @inheritDoc
     */
    protected function getAccessibleIdsForEntity(string $entityType, array $requiredPermissions = []): array
    {
        switch ($entityType) {
            case Vacancy::class:
                return $this->getAccessibleVacancyIds($requiredPermissions);
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
    private function getAccessibleCandidateIds(array $requiredPermissions = []): array
    {
        return $this->getCandidateService()
            ->getCandidateDao()
            ->getCandidateListForInterviewer($this->getEmployeeNumber());
    }

    /**
     * @param array $requiredPermissions
     * @return int[]
     */
    private function getAccessibleInterviewIds(array $requiredPermissions = []): array
    {
        return $this->getCandidateService()
            ->getCandidateDao()
            ->getInterviewListForInterviewer($this->getEmployeeNumber());
    }

    /**
     * @param array $requiredPermission
     * @return int[]
     */
    private function getAccessibleInterviewAttachmentIds(array $requiredPermission = []): array
    {
        return $this->getRecruitmentAttachmentService()
            ->getRecruitmentAttachmentDao()
            ->getInterviewAttachmentListForInterviewer($this->getEmployeeNumber());
    }

    /**
     * @param array $requiredPermissions
     * @return int[]
     */
    private function getAccessibleCandidateHistoryIds(array $requiredPermissions = []): array
    {
        return $this->getCandidateService()
            ->getCandidateDao()
            ->getCandidateHistoryIdListForInterviewer($this->getEmployeeNumber());
    }

    /**
     * @param array $requiredPermissions
     * @return int[]
     */
    private function getAccessibleVacancyIds(array $requiredPermissions = []): array
    {
        return $this->getCandidateService()
            ->getCandidateDao()
            ->getVacancyIdListForInterviewer($this->getEmployeeNumber());
    }

    /**
     * @param array $requiredPermissions
     * @return int[]
     */
    private function getAccessibleCandidateActionHistoryIds(array $requiredPermissions = []): array
    {
        $candidateActionHistory = new CandidateActionHistory();
        return $candidateActionHistory->getAccessibleCandidateHistoryIdsForInterviewer();
    }
}
