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
 * Description of DefineKpiService
 *
 * @author orange
 */
class DefineKpiService extends BaseService {
	

	
	/**
     * 
     * Search Kpi
     * @return unknown_type
     */
    public function getKpiForJobTitle( $jobTitleId )
    {
    	try
        {
	    	
        	$q = Doctrine_Query::create( )
				    ->from('DefineKpi kpi') 
				    ->where("kpi.job_title_code='".$jobTitleId."' AND kpi.is_active = '1'" );
				    
			$kpiList = $q->execute();
			
			return $kpiList;
			   
        }catch( Exception $e)
        {
            throw new PerformanceServiceException($e->getMessage());
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
            throw new AdminServiceException($e->getMessage());
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
            throw new AdminServiceException($e->getMessage());
        }
    }
    
	/**
	 * Save Define Kpi
	 * @return None
	 */
	public function saveDefineKpi(DefineKpi $Kpi) {
		try {
			if( $Kpi->getId() == ''){
				$idGenService = new IDGeneratorService ( );
				$idGenService->setEntity ( $Kpi );
				$Kpi->setId ( $idGenService->getNextID () );
			}
			$Kpi->save ();
			if($Kpi->getDefault() == 1)
				$this->overRideDefineKpiDefaultRate($Kpi);
			return $Kpi;
		} catch ( Exception $e ) {
			throw new PerformanceServiceException ( $e->getMessage () );
		}
	}
	
	/**
	 * Get Define Kpi details to edit
	 * @param $defineKpiId
	 * @return Array
	 */
	public function readDefineKpi($defineKpiId) {
		try {
			$defineKpis = Doctrine::getTable ( 'DefineKpi' )
			->find ( $defineKpiId );
			return $defineKpis;
		} catch ( Exception $e ) {
			throw new PerformanceServiceException ( $e->getMessage () );
		}
	}
	

	
	/**
	 * Delete Define Kpi
	 * @param $DefineKpiList
	 * @return none
	 */
	public function deleteDefineKpi($DefineKpiList) {
		try {
			$q = Doctrine_Query::create ()
			->delete ( 'DefineKpi' )
			->whereIn ( 'id', $DefineKpiList );
			$numDeleted = $q->execute ();
			
			return true ;
			
		} catch ( Exception $e ) {
			throw new PerformanceServiceException ( $e->getMessage () );
		}
	}
	

	
	/**
	 * Get Kpi default rating scale
	 * 
	 * @return Array
	 */
	public function getKpiDefaultRate() {
		
		$defaultRate	=	array();
		try {
			$q = Doctrine_Query::create ()
			->select ( 'kpi.rate_min, kpi.rate_max' )
			->from ( "DefineKpi kpi" )
			->where ( "kpi.rate_default = 1" );
			
			$defaultRate = $q->fetchOne();
			
		} catch ( Exception $e ) {
			throw new PerformanceServiceException ( $e->getMessage () );
		}
		
		return $defaultRate;
	}
	
	/**
	 * overrides kpi default rating scale
	 * 
	 * @return Array
	 */
	private function overRideDefineKpiDefaultRate( DefineKpi $Kpi) {
		try {
			
				$q = Doctrine_Query::create ()
				->update ( 'DefineKpi' )
				->set ( 'DefineKpi.default', '0' )
				->whereNotIn('DefineKpi.id',array($Kpi->getId()));
				$q->execute ();
			
			return true ;
			
		} catch ( Exception $e ) {
			throw new PerformanceServiceException ( $e->getMessage () );
		}
		
		
	}
	
	/**
	 * Save all defined kpi's from selected Job Title
	 * @param $jobTitleId
	 * @param $copiedKpis
	 * @return None
	 */
	public function copyKpi( $toJobTitle , $fromJobTitle)
	{
		try {
			$q = Doctrine_Query::create ()
			->delete ( 'DefineKpi' )
			->where ( "jobtitlecode='$toJobTitle'"  );
			$numDeleted = $q->execute ();
			
			$kpiList	=	$this->getKpiForJobTitle( $fromJobTitle );
			
			foreach( $kpiList as $fromKpi){
				$kpi	=	new DefineKpi ( );
				$kpi->setJobtitlecode ( $toJobTitle );
				$kpi->setDesc ( $fromKpi->getDesc() );
				$kpi->setMin ( $fromKpi->getMin() );
				$kpi->setMax ( $fromKpi->getMax() );
				$kpi->setDefault ( 0 );
				$kpi->setIsactive ( 1 );
				$this->saveDefineKpi($kpi);
			}
			return true ;
		}catch ( Exception $e ) {
			throw new PerformanceServiceException ( $e->getMessage () );
		}
	}
	

}

