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

namespace OrangeHRM\Leave\Service;

use DateInterval;
use DateTime;
use InvalidArgumentException;
use OrangeHRM\Core\Exception\ServiceException;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Core\Traits\Service\NormalizerServiceTrait;
use OrangeHRM\Entity\LeavePeriodHistory;
use OrangeHRM\Leave\Dao\LeavePeriodDao;
use OrangeHRM\Leave\Dto\LeavePeriod;
use OrangeHRM\Leave\Service\Model\LeavePeriodModel;
use OrangeHRM\Leave\Traits\Service\LeaveConfigServiceTrait;
use OrangeHRM\Leave\Traits\Service\LeaveEntitlementServiceTrait;

class LeavePeriodService
{
    use LeaveConfigServiceTrait;
    use LeaveEntitlementServiceTrait;
    use NormalizerServiceTrait;
    use DateTimeHelperTrait;

    public const LEAVE_PERIOD_STATUS_FORCED = 1;
    public const LEAVE_PERIOD_STATUS_NOT_FORCED = 2;
    public const LEAVE_PERIOD_STATUS_NOT_APPLICABLE = 3;

    private ?LeavePeriodDao $leavePeriodDao = null;
    private ?LeavePeriodHistory $currentLeavePeriodStartDateAndMonth = null;
    private ?array $leavePeriodList = null;
    private ?array $leavePeriodHistoryList = null;

    /**
     * @return LeavePeriodDao
     */
    public function getLeavePeriodDao(): LeavePeriodDao
    {
        if (!($this->leavePeriodDao instanceof LeavePeriodDao)) {
            $this->leavePeriodDao = new LeavePeriodDao();
        }
        return $this->leavePeriodDao;
    }

    /**
     * Returns the list of month names in year
     *
     * @return string[] Array of month names
     */
    public function getListOfMonths(): array
    {
        $monthNames = [];
        foreach ($this->getMonthNumberList() as $monthNumber) {
            $monthNames[] = date('F', mktime(0, 0, 0, $monthNumber, 1));
        }

        return $monthNames;
    }

    /**
     * @return int[]
     */
    public function getMonthNumberList(): array
    {
        return range(1, 12);
    }

    /**
     * Returns the array of dates that can have for the given month
     *
     * @param int $month Month to which the list of dates be created
     * @param bool $isLeapYear
     * @return int[] Array of dates that can fall in the given month
     */
    public function getListOfDates(int $month, bool $isLeapYear = false): array
    {
        switch ($month) {
            case 1:
            case 3:
            case 5:
            case 7:
            case 8:
            case 10:
            case 12:
                return range(1, 31);

            case 4:
            case 6:
            case 9:
            case 11:
                return range(1, 30);

            case 2:
                $lastDayOfFebruary = ($isLeapYear) ? 29 : 28;
                return range(1, $lastDayOfFebruary);

            default:
                throw new InvalidArgumentException('Invalid value passed for month in ' . __METHOD__);
        }
    }

    /**
     * Get the latest Leave period start date and month
     * @param bool $forceReload (if false, will use cached value from previous method call)
     * @return null|LeavePeriodHistory
     */
    public function getCurrentLeavePeriodStartDateAndMonth(bool $forceReload = false): ?LeavePeriodHistory
    {
        if ($forceReload || is_null($this->currentLeavePeriodStartDateAndMonth)) {
            $this->currentLeavePeriodStartDateAndMonth = $this->getLeavePeriodDao()
                ->getCurrentLeavePeriodStartDateAndMonth();
        }

        return $this->currentLeavePeriodStartDateAndMonth;
    }

    /**
     * @param bool $forceReload
     * @return LeavePeriodHistory[]
     */
    protected function _getLeavePeriodHistoryList(bool $forceReload = false): array
    {
        if ($forceReload || is_null($this->leavePeriodHistoryList)) {
            $this->leavePeriodHistoryList = $this->getLeavePeriodDao()->getLeavePeriodHistoryList();
        }
        return $this->leavePeriodHistoryList;
    }

    /**
     * Get Generated Leave Period List
     * @param null $toDate
     * @param false $forceReload
     * @return LeavePeriod[]
     */
    public function getGeneratedLeavePeriodList($toDate = null, bool $forceReload = false): array
    {
        $leavePeriodList = [];
        $leavePeriodHistoryList = $this->_getLeavePeriodHistoryList($forceReload);

        if (empty($leavePeriodHistoryList)) {
            throw new ServiceException('Leave Period Start Date Is Not Defined.');
        }

        if ($forceReload || empty($this->leavePeriodList)) {
            $endDate = ($toDate != null) ? new DateTime($toDate) : $this->getDateTimeHelper()->getNow();
            //If To Date is not specified return leave type till next leave period
            if (is_null($toDate)) {
                $endDate->add(new DateInterval('P1Y'));
            }

            $firstCreatedDate = $leavePeriodHistoryList[0]->getCreatedAt();
            $startDate = new DateTime(
                $firstCreatedDate->format('Y') . '-' .
                $leavePeriodHistoryList[0]->getStartMonth() . '-' .
                $leavePeriodHistoryList[0]->getStartDay()
            );
            if ($firstCreatedDate < $startDate) {
                $startDate->sub(new DateInterval('P1Y'));
            }
            $tempDate = $startDate;
            $i = 0;
            while ($tempDate <= $endDate) {
                $projectedStartDate = ($i == 0) ? $tempDate : new DateTime(
                    date('Y-m-d', strtotime($tempDate->format('Y-m-d') . '+1 day'))
                );
                $projectedEndDate = new DateTime(
                    date('Y-m-d', strtotime($projectedStartDate->format('Y-m-d') . ' +1 year -1 day'))
                );

                foreach ($leavePeriodHistoryList as $leavePeriodHistory) {
                    $createdDate = $leavePeriodHistory->getCreatedAt();

                    if (($projectedStartDate < $createdDate) && ($createdDate < $projectedEndDate)) {
                        $newStartDate = new DateTime(
                            $createdDate->format('Y') . '-' .
                            $leavePeriodHistory->getStartMonth() . '-' . $leavePeriodHistory->getStartDay()
                        );
                        if ($createdDate < $newStartDate) {
                            $newStartDate->sub(new DateInterval('P1Y'));
                        }
                        $projectedEndDate = $newStartDate->add(DateInterval::createFromDateString('+1 year -1 day'));
                    }
                }

                $tempDate = $projectedEndDate;

                $leavePeriodList[] = new LeavePeriod($projectedStartDate, $projectedEndDate);
                $i++;
            }
            $this->leavePeriodList = $leavePeriodList;
        }
        return $this->leavePeriodList;
    }

    /**
     * @param DateTime $currentDate
     * @param bool $forceReload
     * @return LeavePeriod|null
     */
    public function getCurrentLeavePeriodByDate(DateTime $currentDate, bool $forceReload = false): ?LeavePeriod
    {
        $matchLeavePeriod = null;
        $this->leavePeriodList = $this->getGeneratedLeavePeriodList(null, $forceReload);
        foreach ($this->leavePeriodList as $leavePeriod) {
            if (($leavePeriod->getStartDate() <= $currentDate) && ($currentDate <= $leavePeriod->getEndDate())) {
                $matchLeavePeriod = $leavePeriod;
                break;
            }
        }
        return $matchLeavePeriod;
    }

    /**
     * @param bool $forceReload
     * @return LeavePeriod|null
     */
    public function getCurrentLeavePeriod(bool $forceReload = false): ?LeavePeriod
    {
        $leavePeriodDefined = $this->getLeaveConfigService()->isLeavePeriodDefined();
        return $leavePeriodDefined ? $this->getCurrentLeavePeriodByDate(
            $this->getDateTimeHelper()->getNow(),
            $forceReload
        ) : null;
    }

    /**
     * @return array|null
     */
    public function getNormalizedCurrentLeavePeriod(): ?array
    {
        $currentLeavePeriod = $this->getCurrentLeavePeriod();
        return $currentLeavePeriod ?
            $this->getNormalizerService()->normalize(
                LeavePeriodModel::class,
                $currentLeavePeriod
            ) : null;
    }

    /**
     * @return DateTime|null
     */
    public function getMaxAllowedLeavePeriodEndDate(): ?DateTime
    {
        if (!$this->getLeaveConfigService()->isLeavePeriodDefined()) {
            return null;
        }
        $leavePeriods = $this->getGeneratedLeavePeriodList();
        return end($leavePeriods)->getEndDate();
    }
}
