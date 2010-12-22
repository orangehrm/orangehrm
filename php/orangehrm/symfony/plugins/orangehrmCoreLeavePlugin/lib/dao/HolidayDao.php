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
class HolidayDao extends BaseDao
{

    /**
     * Add and Update Holiday
     * @param Holiday $holiday
     * @return boolean
     */

    public function saveHoliday(Holiday $holiday)
    {

        try
        {

            if ($holiday->getHolidayId() == '')
            {
                // genarate new ID for the Holiday Object
                $idGenService = new IDGeneratorService();
                $idGenService->setEntity($holiday);
                $holiday->setHolidayId( $idGenService->getNextID());
            }

            $holiday->save();
            return $holiday;

        } catch ( Exception $e )
        {
            throw new DaoException ( $e->getMessage () );
        }

    }

    /**
     * Read Holiday by given holiday id
     * @param $holidayId
     * @return Holiday
     */
    public function readHoliday($holidayId)
    {
        try
        {
            $holiday = Doctrine::getTable('Holiday')
                    ->find ($holidayId);

            return $holiday;
            
        } catch (Exception $e)
        {
            throw new DaoException ($e->getMessage());
        }
    }

    /**
     * Read Holiday by given Date
     * @param $date
     * @return Holiday
     */
    public function readHolidayByDate($date)
    {
        try
        {

            $q = Doctrine_Query::create()
                    ->from("Holiday")
                    ->where("date = ? OR (recurring=1 AND MONTH(date)=? AND DAY(date)=?)",array($date,date('m',strtotime($date)),date('d',strtotime($date))));
					
            
				
			$result = $q->fetchOne();
            
            return $result;

        } catch ( Exception $e )
        {
            throw new DaoException ( $e->getMessage () );
        }
    }

    /**
     * Delete Holiday by given holiday id
     * @param $holidayId
     * @return none
     */
    public function deleteHoliday($holiday)
    {
        try
        {
            $q = Doctrine_Query::create ()
                    ->delete ( 'Holiday' )
                    ->whereIn ( 'holiday_id', $holiday );
            $holidayDeleted = $q->execute ();
            if($holidayDeleted > 0)
            {
                return true ;
            }
            return false;


        } catch ( Exception $e )
        {
            throw new DaoException ( $e->getMessage () );
        }
    }

    /**
     * Get Holiday List
     * @return Holiday Collection
     */
    public function getHolidayList( $year=null, $offset=0,$limit=10)
    {

        try
        {
            if(!isset($year))
            {
                $year = date("Y");
            }
            $q = Doctrine_Query::create()
                    ->select('*')
                    ->addSelect("IF( h.recurring=1 && YEAR(h.date) <= $year, DATE_FORMAT(h.date, '$year-%m-%d'), h.date ) fdate")
                    ->from('Holiday h')
                    ->where('h.recurring = ? OR h.date >=?', array(1, "$year-01-01"))
                    ->orderBy('fdate ASC');

            $q->offset($offset)->limit($limit);
            $holidayList = $q->execute();
            return  $holidayList ;

        }catch( Exception $e)
        {
            throw new DaoException ( $e->getMessage () );
        }
    }

    /**
     * Get Full Holiday List
     * @return Holiday Collection
     */
    public function getFullHolidayList()
    {

        try
        {

            $q = Doctrine_Query::create()
                    ->select('*')
                    ->from('Holiday')
                    ->orderBy('date ASC');

            $holidayList = $q->execute();
            return  $holidayList ;

        }catch( Exception $e)
        {
            throw new DaoException ( $e->getMessage () );
        }
    }

}
