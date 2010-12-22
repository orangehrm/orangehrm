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
class WorkWeekDao extends BaseDao
{

    /**
     * Add and Update WorkWeek
     * @param Weekends $dayOff
     * @return boolean
     */
    public function saveWorkWeek( WorkWeek $workWeek)
    {
        try
        {
            $workWeek->save();
            return $workWeek;
        } catch ( Exception $e )
        {
            throw new DaoException ( $e->getMessage () );
        }
    }

    /**
     * Read WorkWeek
     * @param $day
     * @return WorkWeek
     */
    public function readWorkWeek($day)
    {
        try
        {
            $workWeek = Doctrine::getTable ( 'WorkWeek' )
                    ->find ( $day );
            return $workWeek;
        } catch ( Exception $e )
        {
            throw new DaoException ( $e->getMessage () );
        }
    }

    /**
     * Delete WorkWeek
     * @param $day
     * @return boolean
     */
    public function deleteWorkWeek($day)
    {
        try
        {
            $q = Doctrine_Query::create ()
                    ->delete ( 'WorkWeek' )
                    ->whereIn ( 'day', $day );
            $holidayDeleted = $q->execute ();

            if($holidayDeleted > 0){
                return true;
            }
            return false;

        } catch ( Exception $e )
        {
            throw new DaoException ( $e->getMessage () );
        }
    }

    /**
     * Get WorkWeek List
     * @return WorkWeek Collection
     */
    public function getWorkWeekList( $offset=0,$limit=10)
    {
        try
        {
            $q = Doctrine_Query::create()
                    ->from('WorkWeek')
                    ->orderBy('day');

            $q->offset($offset)->limit($limit);

            $WorkWeekList = $q->execute();
            return  $WorkWeekList ;

        }catch( Exception $e)
        {
            throw new DaoException ( $e->getMessage () );
        }
    }
    
    /**
	 * Check whether the given date is a weekend.
	 * @param date $date
	 * @return bool true on success and false on failiure
	 */
    public function isWeekend( $day,$fullDay = true){
        try
        {
            $dayNumber = date('N', strtotime($day));
           
        	$q = Doctrine_Query::create()
                    ->from('WorkWeek')
                    ->where('day=?',$dayNumber);
			
            
            $workWeek = $q->fetchOne();
			if($fullDay){
				
	            if($workWeek->getLength()==WorkWeek::WORKWEEK_LENGTH_WEEKEND)
                  return true;
	            else
	            	return false;
			}else{
				if($workWeek->getLength()==WorkWeek::WORKWEEK_LENGTH_HALF_DAY)
	            	return true;
	            else
	            	return false;
			}

        }catch( Exception $e)
        {
            throw new DaoException ( $e->getMessage () );
        }
    }
    
   

}
