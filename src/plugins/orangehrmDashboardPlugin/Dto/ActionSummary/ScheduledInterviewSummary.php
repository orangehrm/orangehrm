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

use OrangeHRM\Dashboard\Traits\Service\EmployeeActionSummaryServiceTrait;

class ScheduledInterviewSummary implements ActionSummary
{
    use EmployeeActionSummaryServiceTrait;

    /**
     * @var array
     */
    private array $accessibleCandidateIds;

    /**
     * @param array $accessibleCandidateIds
     */
    public function __construct(array $accessibleCandidateIds)
    {
        $this->accessibleCandidateIds = $accessibleCandidateIds;
    }

    /**
     * @return int
     */
    public function getGroupId(): int
    {
        return 5;
    }

    /**
     * @return string
     */
    public function getGroup(): string
    {
        return 'Candidates To Interview';
    }

    /**
     * @inheritDoc
     */
    public function getPendingActionCount(): int
    {
        return $this->getEmployeeActionSummaryService()
            ->getEmployeeActionSummaryDao()
            ->getActionableScheduledInterviewCount($this->accessibleCandidateIds);
    }
}
