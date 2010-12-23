<?php
/*
 *
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
 *
*/
class HolidayService extends BaseService
{
    // Holiday Data Access Object
    private $HolidayDao ;

    /**
     * Get the Holiday Data Access Object
     * @return HolidayDao
     */
    public function getHolidayDao()
    {
        return $this->HolidayDao;
    }

    /**
     * Set Holiday Data Access Object
     * @param HolidayDao $HolidayDao
     * @return void
     */
    public function setHolidayDao( HolidayDao $HolidayDao)
    {
        $this->HolidayDao = $HolidayDao;
    }

    /**
     * Add, Update Holidays
     * @param Holiday $holiday
     * @return boolean
     */
    public function saveHoliday( Holiday $holiday)
    {

        return $this->getHolidayDao()->saveHoliday( $holiday );

    }

    /**
     * Delete Holiday
     * @param int $holidayId
     * @return boolean
     */
    public function deleteHoliday( $holidayId )
    {

        return $this->getHolidayDao()->deleteHoliday( $holidayId );

    }

    /**
     * Read Holiday by given holidayId
     * @param int $holidayId
     * @return Holiday $Holiday
     */
    public function readHoliday($holidayId)
    {

        $holiday = $this->getHolidayDao()->readHoliday($holidayId);

        if(!$holiday instanceof Holiday)
        {
            $holiday = new Holiday();

        }

        return $holiday;

    }

    /**
     * Read Holiday by given Date
     * @param int $holidayId
     * @return Holiday $holiday
     */
    public function readHolidayByDate($date)
    {

        $holiday = $this->getHolidayDao()->readHolidayByDate($date);

        if(!$holiday instanceof Holiday)
        {
            $holiday = new Holiday();

        }
        
        return $holiday;

    }

    /**
     * Get Holiday list
     * @param int $offset
     * @param int $limit
     * @return Holidays $holidayList
     */
    public function getHolidayList($year = null,$offset=0,$limit=50)
    {
        $holidayList = $this->getHolidayDao()->getHolidayList($year,$offset, $limit);
        return $holidayList;

    }

    /**
     * Search Holidays within a given leave period
     * @param String $startDate
     * @param String $endDate
     * @return Holidays
     */
    public function searchHolidays($startDate = null, $endDate = null) {
        return $this->getHolidayDao()->searchHolidays($startDate, $endDate);
    }

    /**
     * check whether the given date is a holiday
     *
     * @param date $day
     * @return boolean
     * 
     */
    public function isHoliday($day)
    {

        $holiday =  $this->getHolidayDao()->readHolidayByDate($day);

        if($holiday != null && $holiday->getLength() == WorkWeek::WORKWEEK_LENGTH_FULL_DAY) {
           return true;
        }
        
        return false;

    }

    /**
     * Findout whether day is a half day
     * @param Date $day
     * @returns boolean
     * @throws LeaveServiceException
     */
    public function isHalfDay($day) {

        $holiday =  $this->getHolidayDao()->readHolidayByDate($day);

        if($holiday != null && $holiday->getLength() >= WorkWeek::WORKWEEK_LENGTH_HALF_DAY && $holiday->getLength() < WorkShift::DEFAULT_WORK_SHIFT_LENGTH) {
            return true;
        }

        return false;

    }
   /**
     * check whether the given date is a holiday
     *
     * @param date $day
     * @return boolean
     * 
     */
    public function isHalfdayHoliday($day)
    {

        $holiday =  $this->getHolidayDao()->readHolidayByDate($day);

        if($holiday != null && $holiday->getLength() == Holiday::HOLIDAY_HALF_DAY_LENGTH) {
            return true;
        } else {
            return false;
        }

    }
    

    /**
     * Get Holiday full holiday list
     * @return Holidays $holidayList
     */
    public function getFullHolidayList()
    {

        $holidayList = $this->getHolidayDao()->getFullHolidayList();
        return $holidayList;

    }


}
