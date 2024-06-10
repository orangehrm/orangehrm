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

namespace OrangeHRM\Leave\Dao;

use DateTime;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\Holiday;
use OrangeHRM\Leave\Dto\HolidaySearchFilterParams;
use OrangeHRM\ORM\Paginator;

class HolidayDao extends BaseDao
{
    /**
     * Add and Update Holiday
     * @param Holiday $holiday
     * @return Holiday
     */
    public function saveHoliday(Holiday $holiday): Holiday
    {
        $this->persist($holiday);
        return $holiday;
    }

    /**
     * @param int $holidayId
     * @return Holiday|null
     */
    public function getHolidayById(int $holidayId): ?Holiday
    {
        return $this->getRepository(Holiday::class)->find($holidayId);
    }

    /**
     * @param int[] $ids
     * @return int[]
     */
    public function getExistingHolidayIds(array $ids): array
    {
        $qb = $this->createQueryBuilder(Holiday::class, 'holiday');

        $qb->select('holiday.id')
            ->andWhere($qb->expr()->in('holiday.id', ':ids'))
            ->setParameter('ids', $ids);

        return $qb->getQuery()->getSingleColumnResult();
    }

    /**
     * @param HolidaySearchFilterParams $holidaySearchFilterParams
     * @return Holiday[]
     */
    public function searchHolidays(HolidaySearchFilterParams $holidaySearchFilterParams): array
    {
        return $this->getSearchHolidaysPaginator($holidaySearchFilterParams)->getQuery()->execute();
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

    /**
     * @param array $toDeleteIds
     * @return int
     */
    public function deleteHolidays(array $toDeleteIds): int
    {
        $q = $this->createQueryBuilder(Holiday::class, 'holiday');
        $q->delete()
            ->andWhere($q->expr()->in('holiday.id', ':ids'))
            ->setParameter('ids', $toDeleteIds);
        return $q->getQuery()->execute();
    }
}
