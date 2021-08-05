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
use OrangeHRM\Entity\Holiday;
use OrangeHRM\Leave\Dto\HolidaySearchFilterParams;
use OrangeHRM\ORM\Paginator;

class HolidayDao extends BaseDao
{
    /**
     * Add and Update Holiday
     * @param Holiday $holiday
     * @return Holiday
     * @throws DaoException
     */
    public function saveHoliday(Holiday $holiday): Holiday
    {
        try {
            $this->persist($holiday);
            return $holiday;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param int $holidayId
     * @return Holiday|null
     * @throws DaoException
     */
    public function getHolidayById(int $holidayId): ?Holiday
    {
        try {
            return $this->getRepository(Holiday::class)->find($holidayId);
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param HolidaySearchFilterParams $holidaySearchFilterParams
     * @return Holiday[]
     * @throws DaoException
     */
    public function searchHolidays(HolidaySearchFilterParams $holidaySearchFilterParams): array
    {
        try {
            return $this->getSearchHolidaysPaginator($holidaySearchFilterParams)->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @param HolidaySearchFilterParams $holidaySearchFilterParams
     * @return Paginator
     */
    private function getSearchHolidaysPaginator(
        HolidaySearchFilterParams $holidaySearchFilterParams
    ): Paginator {
        $q = $this->createQueryBuilder(Holiday::class, 'holiday');
        $this->setSortingParams($q, $holidaySearchFilterParams);
        $q->andWhere($q->expr()->between('holiday.date', ':fromDate', ':toDate'))
            ->setParameter('fromDate', $holidaySearchFilterParams->getFromDate())
            ->setParameter('toDate', $holidaySearchFilterParams->getToDate());
        if ($holidaySearchFilterParams->isExcludeRecurring()) {
            $q->andWhere('holiday.recurring = :recurring')
                ->setParameter('recurring', false);
        } else {
            $q->orWhere('holiday.recurring = :recurring')
                ->setParameter('recurring', true);
        }

        return $this->getPaginator($q);
    }

    /**
     * @param DateTime $date
     * @return Holiday|null
     */
    public function getHolidayByDate(DateTime $date): ?Holiday
    {
        $q = $this->createQueryBuilder(Holiday::class, 'holiday');
        $q->andWhere($q->expr()->eq($q->expr()->substring('holiday.date', 6), ':datePortion'))
            ->setParameter('datePortion', $date->format('m') . '-' . $date->format('d'));
        $q->andWhere('holiday.recurring = :recurring')
            ->setParameter('recurring', true);
        $q->orWhere('holiday.date = :date')
            ->setParameter('date', $date);

        return $this->fetchOne($q);
    }
}
