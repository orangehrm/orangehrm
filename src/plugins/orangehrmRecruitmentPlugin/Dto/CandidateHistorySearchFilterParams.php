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

namespace OrangeHRM\Recruitment\Dto;

use OrangeHRM\Core\Dto\FilterParams;
use OrangeHRM\ORM\ListSorter;

class CandidateHistorySearchFilterParams extends FilterParams
{
    public const ALLOWED_SORT_FIELDS = [
        'candidateHistory.performedDate',
    ];

    /**
     * @var array
     */
    private array $actionIds;

    /**
     * @var int
     */
    protected int $candidateId;

    public function __construct()
    {
        $this->setSortField('candidateHistory.performedDate');
        $this->setSortOrder(ListSorter::DESCENDING);
    }

    /**
     * @return int
     */
    public function getCandidateId(): int
    {
        return $this->candidateId;
    }

    /**
     * @param int $candidateId
     */
    public function setCandidateId(int $candidateId): void
    {
        $this->candidateId = $candidateId;
    }

    /**
     * @return array
     */
    public function getActionIds(): array
    {
        return $this->actionIds;
    }

    /**
     * @param array $actionIds
     */
    public function setActionIds(array $actionIds): void
    {
        $this->actionIds = $actionIds;
    }
}
