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

namespace OrangeHRM\Dashboard\Dao;

use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\Entity\Module;
use OrangeHRM\Leave\Traits\Service\LeaveConfigServiceTrait;

class QuickLaunchDao extends BaseDao
{
    use ConfigServiceTrait;
    use LeaveConfigServiceTrait;

    public const ASSIGN_LEAVE = 'leave.assign_leave';
    public const LEAVE_LIST = 'leave.leave_list';
    public const APPLY_LEAVE = 'leave.apply_leave';
    public const MY_LEAVE = 'leave.my_leave';
    public const EMPLOYEE_TIMESHEET = 'time.employee_timesheet';
    public const MY_TIMESHEET = 'time.my_timesheet';

    private const LEAVE_ADMIN_SHORTCUTS = [self::ASSIGN_LEAVE, self::LEAVE_LIST, self::APPLY_LEAVE, self::MY_LEAVE];
    private const LEAVE_ESS_SHORTCUTS = [self::APPLY_LEAVE, self::MY_LEAVE];
    private const TIME_ADMIN_SHORTCUTS = [self::EMPLOYEE_TIMESHEET, self::MY_TIMESHEET];
    private const TIME_ESS_SHORTCUTS = [self::MY_TIMESHEET];

    private const LEAVE_MODULE = 'leave';
    private const TIME_MODULE = 'time';

    /**
     * @return string[]
     */
    public function getQuickLaunchList(): array
    {
        $shortcuts = [];
        $modules = $this->getActiveQuickLaunchModuleList();

        if (in_array(self::LEAVE_MODULE, $modules)) {
            $shortcuts = array_merge($shortcuts, self::LEAVE_ADMIN_SHORTCUTS);
        }
        if (in_array(self::TIME_MODULE, $modules)) {
            $shortcuts = array_merge($shortcuts, self::TIME_ADMIN_SHORTCUTS);
        }

        return $shortcuts;
    }

    /**
     * @return string[]
     */
    public function getQuickLaunchListForESS(): array
    {
        $shortcuts = [];
        $modules = $this->getActiveQuickLaunchModuleList();

        if (in_array(self::LEAVE_MODULE, $modules)) {
            $shortcuts = array_merge($shortcuts, self::LEAVE_ESS_SHORTCUTS);
        }
        if (in_array(self::TIME_MODULE, $modules)) {
            $shortcuts = array_merge($shortcuts, self::TIME_ESS_SHORTCUTS);
        }

        return $shortcuts;
    }

    /**
     * @return string[]
     */
    private function getActiveQuickLaunchModuleList(): array
    {
        $active = [];

        $qb = $this->createQueryBuilder(Module::class, 'module');
        $qb->select('module.name');
        $qb->andWhere($qb->expr()->eq('module.status', ':status'))
            ->setParameter('status', true);
        $qb->andWhere($qb->expr()->orX(
            $qb->expr()->eq('module.name', ':leaveModule'),
            $qb->expr()->eq('module.name', ':timeModule')
        ))
            ->setParameter('leaveModule', self::LEAVE_MODULE)
            ->setParameter('timeModule', self::TIME_MODULE);

        $modules = $qb->getQuery()->execute();

        foreach ($modules as $module) {
            if ($module['name'] === self::LEAVE_MODULE && $this->getLeaveConfigService()->isLeavePeriodDefined()) {
                $active[] = self::LEAVE_MODULE;
            }
            if ($module['name'] === self::TIME_MODULE && $this->getConfigService()->isTimesheetPeriodDefined()) {
                $active[] = self::TIME_MODULE;
            }
        }

        return $active;
    }
}
