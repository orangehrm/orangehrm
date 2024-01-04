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

namespace OrangeHRM\Entity\Decorator;

use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\PerformanceTracker;
use OrangeHRM\Entity\PerformanceTrackerLog;
use OrangeHRM\Entity\User;

class PerformanceTrackerLogDecorator
{
    use DateTimeHelperTrait;
    use EntityManagerHelperTrait;

    protected PerformanceTrackerLog $performanceTrackerLog;

    /**
     * @param PerformanceTrackerLog $performanceTrackerLog
     */
    public function __construct(PerformanceTrackerLog $performanceTrackerLog)
    {
        $this->performanceTrackerLog = $performanceTrackerLog;
    }

    /**
     * @return PerformanceTrackerLog
     */
    protected function getPerformanceTrackerLog(): PerformanceTrackerLog
    {
        return $this->performanceTrackerLog;
    }

    /**
     * @return string|null
     */
    public function getAddedDate(): ?string
    {
        return $this->getDateTimeHelper()->formatDate($this->getPerformanceTrackerLog()->getAddedDate());
    }

    /**
     * @return string|null
     */
    public function getModifiedDate(): ?string
    {
        return $this->getDateTimeHelper()->formatDate($this->getPerformanceTrackerLog()->getModifiedDate());
    }

    /**
     * @param int $empNumber
     * @return void
     */
    public function setReviewerByEmpNumber(int $empNumber): void
    {
        $employee = $this->getReference(Employee::class, $empNumber);
        $this->getPerformanceTrackerLog()->setEmployee($employee);
    }

    /**
     * @param int $userId
     * @return void
     */
    public function setUserByUserId(int $userId): void
    {
        $user = $this->getReference(User::class, $userId);
        $this->getPerformanceTrackerLog()->setUser($user);
    }

    /**
     * @param int $trackerId
     * @return void
     */
    public function setPerformanceTrackerById(int $trackerId): void
    {
        $tracker = $this->getReference(PerformanceTracker::class, $trackerId);
        $this->getPerformanceTrackerLog()->setPerformanceTracker($tracker);
    }
}
