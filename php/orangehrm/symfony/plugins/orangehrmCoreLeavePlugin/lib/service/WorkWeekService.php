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
class WorkWeekService extends BaseService
{

    private $WorkWeekDao ;

    /**
     * Get the WorkWeek Service
     *
     * @return DayOffDao
     */
    public function getWorkWeekDao()
    {
        return $this->WorkWeekDao;
    }

    /**
     * Set the WorkWeek Service
     *
     * @param DayOffDao $DayOffDao
     * @return void
     */
    public function setWorkWeekDao( WorkWeekDao $workWeekDao)
    {
        $this->WorkWeekDao = $workWeekDao;
    }

    /**
     * Add, Update WorkWeek
     * @param DayOff $dayOff
     * @return boolean
     */
    public function saveWorkWeek( WorkWeek $workWeek)
    {

        return $this->getWorkWeekDao()->saveWorkWeek( $workWeek );

    }

    /**
     * Delete WorkWeek
     * @param Integer $day
     * @return boolean
     */
    public function deleteWorkWeek( $day )
    {

        return $this->getWorkWeekDao()->deleteWorkWeek( $day );

    }

    /**
     * Read WorkWeek by given day
     * @param $day
     * @return $workWeek DayOff
     */
    public function readWorkWeek($day)
    {

        $workWeek = $this->getWorkWeekDao()->readWorkWeek($day);

        if(!$workWeek instanceof WorkWeek)
        {
            $workWeek = new WorkWeek();
        }

        return $workWeek;

    }

    /**
     *
     * @param integer $offset
     * @param integer $limit
     * @return attay Array of WorkWeek Objects
     */
    public function getWorkWeekList($offset=0,$limit=10)
    {

        $workWeekList = $this->getWorkWeekDao()->getWorkWeekList($offset, $limit);
        return $workWeekList;

    }

    /**
     *
     * @param $day
     * @return boolean
     */
    public function isWeekend( $day,$fullDay )
    {

        return $this->getWorkWeekDao()->isWeekend( $day ,$fullDay );

    }

}
