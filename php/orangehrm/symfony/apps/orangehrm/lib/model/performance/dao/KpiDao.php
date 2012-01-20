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

/**
 * Kpi Dao class 
 *
 * @author Samantha Jayasinghe
 */
class KpiDao extends BaseDao {
	
	
	/**
	 * Save Kpi
	 * 
	 * @param DefineKpi $Kpi
	 * @return DefineKpi
	 */
	public function saveKpi(DefineKpi $Kpi) {
		try {
			if( $Kpi->getId() == ''){
				$idGenService = new IDGeneratorService ( );
				$idGenService->setEntity ( $Kpi );
				$Kpi->setId ( $idGenService->getNextID () );
			}
			$Kpi->save ();
			
			return $Kpi;
        } catch ( Doctrine_Validator_Exception $e ) {
            // propagate validator exceptions
            throw $e;
		} catch ( Exception $e ) {
			throw new DaoException ( $e->getMessage () );
		}
	}
	
	/**
	 * Read kpi 
	 * @param $defineKpiId
	 * @return DefineKpi Array
	 */
	public function readKpi($defineKpiId){
		try {
			$defineKpis = Doctrine::getTable ( 'DefineKpi' )
			->find ( $defineKpiId );
			return $defineKpis;
		} catch ( Exception $e ) {
			throw new DaoException ( $e->getMessage () );
		}
	}
	
	/**
	 * Delete Kpi
	 * @param $DefineKpiList
	 * @return none
	 */
	public function deleteKpi($DefineKpiList) {
		try {
			$q = Doctrine_Query::create ()
			->delete ( 'DefineKpi' )
			->whereIn ( 'id', $DefineKpiList );
			$numDeleted = $q->execute ();
			
			return true ;
			
		} catch ( Exception $e ) {
			throw new DaoException ( $e->getMessage () );
		}
	}
	
    /**
     * Get KPI List
     * @return unknown_type
     */
    public function getKpiList( $offset=0,$limit=10){
    	try{
	    	$q = Doctrine_Query::create()
			    ->from('DefineKpi kpi')
			    ->orderBy('kpi.jobtitlecode');
			
			$q->offset($offset)->limit($limit);
			
			$kpiList = $q->execute();  
			return  $kpiList ;
			
        }catch( Exception $e){
            throw new DaoException ( $e->getMessage () );
        }
    }
    
    /**
     * Get KPI count list
     * @return unknown_type
     */
    public function getCountKpiList( ){
    	try{
	    	$count = Doctrine_Query::create()
			    		->from('DefineKpi kpi')
			    		->count();
			
			return  $count ;
			
        }catch( Exception $e){
            throw new DaoException ( $e->getMessage () );
        }
    }
    
	/**
	 * Get Kpi default rating scale
	 * 
	 * @return Int
	 */
	public function getKpiDefaultRate() {
		
		$defaultRate	=	array();
		try {
			$q = Doctrine_Query::create ()
			->select ( 'kpi.rate_min, kpi.rate_max' )
			->from ( "DefineKpi kpi" )
			->where ( "kpi.rate_default = 1" );
			
			$defaultRate = $q->fetchOne();
			
			return $defaultRate;
			
		} catch ( Exception $e ) {
			throw new DaoException ( $e->getMessage () );
		}
		
		
	}
	
	/**
	 * overrides kpi default rating scale
	 * 
	 * @return boolean
	 */
	public function overRideKpiDefaultRate( DefineKpi $Kpi) {
		try {
			
				$q = Doctrine_Query::create ()
				->update ( 'DefineKpi' )
				->set ( 'DefineKpi.default', '0' )
				->whereNotIn('DefineKpi.id',array($Kpi->getId()));
				$q->execute ();
			
			return true ;
			
		} catch ( Exception $e ) {
			throw new DaoException ( $e->getMessage () );
		}
	}
	
	/**
	 * Delete Kpi for job title
	 * 
	 * @return boolean
	 */
	public function deleteKpiForJobTitle( $toJobTitleCode ){
		try{
	    	
        	$q = Doctrine_Query::create ()
			->delete ( 'DefineKpi' )
			->where ( "jobtitlecode='$toJobTitleCode'"  );
			$numDeleted = $q->execute ();
			
			return true;
			   
        }catch( Exception $e){
            throw new DaoException($e->getMessage());
        }
	}
	
	/**
     * Get Kpi for Job Title
     * 
     * @param int $jobTitleId
     * @return DefineKpi KpiList
     */
    public function getKpiForJobTitle( $jobTitleId ){
    	try{
	    	
        	$q = Doctrine_Query::create( )
				    ->from('DefineKpi kpi') 
				    ->where("kpi.job_title_code='".$jobTitleId."' AND kpi.is_active = '1'" );
				    
			$kpiList = $q->execute();
			
			return $kpiList;
			   
        }catch( Exception $e){
            throw new DaoException($e->getMessage());
        }
    }
    

}