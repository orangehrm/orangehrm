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
 * @author Samantha Jayasinghe
 */
class KpiService extends BaseService {
	
	private $kpiDao ;
	
	
	public function getKpiDao(){
		return $this->kpiDao;	
	}
	
	public function setKpiDao( KpiDao $kpiDao){
		$this->kpiDao	=	$kpiDao ;
	}
	
	/**
     * 
     * Get Kpi for Job Title
     * @return Kpi List
     */
    public function getKpiForJobTitle( $jobTitleId ){
    	try{
	    		    
			$kpiList = $this->getKpiDao()->getKpiForJobTitle( $jobTitleId  );
			
			return $kpiList;
			   
        }catch( Exception $e){
            throw new PerformanceServiceException($e->getMessage());
        }
    }
    
    /**
     * Get KPI List
     * @return unknown_type
     */
    public function getKpiList( $offset=0,$limit=10){
    	try{	
			$kpiList = $this->getKpiDao()->getKpiList($offset,$limit);
			return  $kpiList ;
			
        }catch( Exception $e){
            throw new PerformanceServiceException($e->getMessage());
        }
    }
    
    /**
     * Get KPI count
     * @return unknown_type
     */
    public function getCountKpiList( ){
    	try{
	    	return $this->getKpiDao()->getCountKpiList();
			
        }catch( Exception $e){
            throw new PerformanceServiceException($e->getMessage());
        }
    }
    
	/**
	 * Save Kpi
	 * @return None
	 */
	public function saveKpi(DefineKpi $Kpi) {
		try {
			$this->getKpiDao()->saveKpi( $Kpi );
			if($Kpi->getDefault() == 1)
				$this->getKpiDao()->overRideKpiDefaultRate($Kpi);
			return $Kpi;
        } catch ( Doctrine_Validator_Exception $e ) {
            // propagate validator exceptions
            throw $e;            
		} catch ( Exception $e ) {
			throw new PerformanceServiceException ( $e->getMessage () );
		}
	}
	
	/**
	 * Read Kpi
	 * @param $defineKpiId
	 * @return Array
	 */
	public function readKpi($defineKpiId) {
		try {
			return $this->getKpiDao()->readKpi( $defineKpiId );
		} catch ( Exception $e ) {
			throw new PerformanceServiceException ( $e->getMessage () );
		}
	}
	
	/**
	 * Delete Kpi
	 * @param $DefineKpiList
	 * @return none
	 */
	public function deleteKpi($DefineKpiList) {
		try {
			$this->getKpiDao()->deleteKpi( $DefineKpiList );
			
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
			
			$defaultRate = $this->getKpiDao()->getKpiDefaultRate();
			return $defaultRate;
			
		} catch ( Exception $e ) {
			throw new PerformanceServiceException ( $e->getMessage () );
		}
		
	}
	
	
	/**
	 * overrides kpi default rating scale
	 * 
	 * @return Array
	 */
	private function overRideKpiDefaultRate( DefineKpi $Kpi) {
		try {
			
			$this->getKpiDao()->overRideKpiDefaultRate($Kpi);
			
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
			$this->getKpiDao()->deleteKpiForJobTitle( $toJobTitle);
			
			$kpiList	=	$this->getKpiForJobTitle( $fromJobTitle );
			
			foreach( $kpiList as $fromKpi){
				$kpi	=	new DefineKpi ( );
				$kpi->setJobtitlecode ( $toJobTitle );
				$kpi->setDesc ( $fromKpi->getDesc() );
				$kpi->setMin ( $fromKpi->getMin() );
				$kpi->setMax ( $fromKpi->getMax() );
				$kpi->setDefault ( 0 );
				$kpi->setIsactive ( 1 );
				$this->saveKpi($kpi);
			}
			return true ;
		}catch ( Exception $e ) {
			throw new PerformanceServiceException ( $e->getMessage () );
		}
	}
}