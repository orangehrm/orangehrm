<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

namespace OrangeHRM\Leave\Dao;

use DateTime;
use Exception;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\LeavePeriodHistory;
use OrangeHRM\Leave\Traits\Service\LeaveConfigServiceTrait;
use OrangeHRM\Leave\Traits\Service\LeaveEntitlementServiceTrait;
use OrangeHRM\Leave\Traits\Service\LeavePeriodServiceTrait;
use OrangeHRM\ORM\ListSorter;

class LeavePeriodDao extends BaseDao
{
    use LeavePeriodServiceTrait;
    use LeaveEntitlementServiceTrait;
    use LeaveConfigServiceTrait;

    /**
     * @param LeavePeriodHistory $leavePeriodHistory
     * @return LeavePeriodHistory
     * @throws DaoException
     */
    public function saveLeavePeriodHistory(LeavePeriodHistory $leavePeriodHistory): LeavePeriodHistory
    {
        $this->beginTransaction();
        try {
            $currentLeavePeriod = $this->getCurrentLeavePeriodStartDateAndMonth();
            $strategy = $this->getLeaveEntitlementService()->getLeaveEntitlementStrategy();

            $this->persist($leavePeriodHistory);

            $isLeavePeriodDefined = $this->getLeaveConfigService()->isLeavePeriodDefined();
            $this->getLeaveConfigService()->setLeavePeriodDefined(true);

            if ($isLeavePeriodDefined && !empty($currentLeavePeriod)) {
                $leavePeriodForToday = $this->getLeavePeriodService()->getCurrentLeavePeriodByDate(
                    new DateTime(),
                    true
                );
                $oldStartMonth = $currentLeavePeriod->getStartMonth();
                $oldStartDay = $currentLeavePeriod->getStartDay();
                $newStartMonth = $leavePeriodHistory->getStartMonth();
                $newStartDay = $leavePeriodHistory->getStartDay();

                $strategy->handleLeavePeriodChange(
                    $leavePeriodForToday,
                    $oldStartMonth,
                    $oldStartDay,
                    $newStartMonth,
                    $newStartDay
                );
            }

            $this->commitTransaction();
        } catch (Exception $e) {
            $this->rollBackTransaction();
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }

        return $leavePeriodHistory;
    }

    /**
     * @return LeavePeriodHistory|null
     */
    public function getCurrentLeavePeriodStartDateAndMonth(): ?LeavePeriodHistory
    {
        $q = $this->createQueryBuilder(LeavePeriodHistory::class, 'leavePeriod');
        $q->addOrderBy('leavePeriod.createdAt', ListSorter::DESCENDING);
        $q->addOrderBy('leavePeriod.id', ListSorter::DESCENDING);

        return $this->fetchOne($q);
    }

    /**
     * @return LeavePeriodHistory[]
     */
    public function getLeavePeriodHistoryList(): array
    {
        $q = $this->createQueryBuilder(LeavePeriodHistory::class, 'leavePeriod');
        $q->addOrderBy('leavePeriod.createdAt');

        return $q->getQuery()->execute();
    }
}
