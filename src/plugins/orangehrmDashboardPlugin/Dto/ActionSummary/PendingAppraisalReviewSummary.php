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

namespace OrangeHRM\Dashboard\Dto\ActionSummary;

use OrangeHRM\Dashboard\Dto\ActionableReviewSearchFilterParams;
use OrangeHRM\Dashboard\Traits\Service\EmployeeActionSummaryServiceTrait;
use OrangeHRM\Entity\WorkflowStateMachine;

class PendingAppraisalReviewSummary implements ActionSummary
{
    use EmployeeActionSummaryServiceTrait;

    /**
     * @var ActionableReviewSearchFilterParams
     */
    private ActionableReviewSearchFilterParams $actionableReviewSearchFilterParams;

    /**
     * @param int $empNumber
     */
    public function __construct(int $empNumber)
    {
        $actionableReviewSearchFilterParams = new ActionableReviewSearchFilterParams();
        $actionableReviewSearchFilterParams->setReviewerEmpNumber($empNumber);
        $actionableReviewSearchFilterParams->setActionableStatuses(
            [
                WorkflowStateMachine::REVIEW_ACTIVATE,
                WorkflowStateMachine::REVIEW_IN_PROGRESS_SAVE
            ]
        );
        $this->actionableReviewSearchFilterParams = $actionableReviewSearchFilterParams;
    }

    /**
     * @inheritDoc
     */
    public function getGroupId(): int
    {
        return 3;
    }

    /**
     * @inheritDoc
     */
    public function getGroup(): string
    {
        return 'Pending Appraisal Reviews';
    }

    /**
     * @inheritDoc
     */
    public function getPendingActionCount(): int
    {
        return $this->getEmployeeActionSummaryService()
            ->getEmployeeActionSummaryDao()
            ->getPendingAppraisalReviewCount($this->actionableReviewSearchFilterParams);
    }
}
