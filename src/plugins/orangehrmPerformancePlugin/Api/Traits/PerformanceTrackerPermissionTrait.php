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

namespace OrangeHRM\Performance\Api\Traits;

use OrangeHRM\Core\Authorization\Dto\ResourcePermission;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\PerformanceTrackerLog;

trait PerformanceTrackerPermissionTrait
{
    use UserRoleManagerTrait;

    /**
     * @param PerformanceTrackerLog $performanceTrackerLog
     * @return ResourcePermission
     */
    protected function getTrackerLogPermission(PerformanceTrackerLog $performanceTrackerLog): ResourcePermission
    {
        $self = $this->getUserRoleManagerHelper()
            ->isSelfByEmpNumber($performanceTrackerLog->getEmployee()->getEmpNumber());
        return $this->getUserRoleManager()->getDataGroupPermissions(
            'performance_tracker_log',
            [],
            [],
            $self,
            []
        );
    }
}
