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
 * Service Class for Performance Review
 *
 * @author orange
 */
class PerformanceKpiService extends BaseService {
	
	/**
	 * Get XML String from Kpi List
	 * @param $kpiList
	 * @return String
	 */
	public function getXmlFromKpi( $kpiList )
	{
		$xmlString	=	'';
		
		$performanceKpiList	=	$this->getKpiToPerformanceKpi( $kpiList );
		$xmlString			=	$this->getXml( $performanceKpiList );
		return $xmlString;
		
	}
	
	/**
	 * Get XML from Performance Kpi
	 * @param $performanceKpiList
	 * @return unknown_type
	 */
	public function getXml( $performanceKpiList)
	{
		try {
			$xmlStr = '
			<xml>
			</xml>';
	
	 
			$xml = simplexml_load_string($xmlStr);
			
			$kpis	=	$xml->addChild('kpis');
			
			foreach( $performanceKpiList as $performanceKpi){
				$xmlKpi	=	$kpis->addChild('kpi');
				$xmlKpi->addChild('id',$performanceKpi->getId());
				$xmlKpi->addChild('desc',$performanceKpi->getKpi());
				$xmlKpi->addChild('min',$performanceKpi->getMinRate());
				$xmlKpi->addChild('max',$performanceKpi->getMaxRate());
				$xmlKpi->addChild('rate',($performanceKpi->getRate()=='')?' ':$performanceKpi->getRate());
				$xmlKpi->addChild('comment',($performanceKpi->getComment()=='')?' ':$performanceKpi->getComment());
			}
			return $xml->asXML();
		}catch (Exception $e) {
			    throw new PerformanceServiceException($e->getMessage());
		}	  
	}
	
	/**
	 * Get Performance List from XML
	 * @param $xmlString
	 * @return unknown_type
	 */
	public function getPerformanceKpiList( $xmlString )
	{
		try {
			$performanceKpiList	=	array();
			
			$xml = simplexml_load_string($xmlString);
			foreach( $xml->kpis->kpi	as $kpi){
				$performanceKpi	=	new PerformanceKpi();
				$performanceKpi->setId((int)$kpi->id);
				$performanceKpi->setKpi((string)$kpi->desc);
				$performanceKpi->setMinRate((string)$kpi->min);
				$performanceKpi->setMaxRate((string)$kpi->max);
				$performanceKpi->setRate((string)$kpi->rate);
				$performanceKpi->setComment((string)$kpi->comment);
				array_push($performanceKpiList,$performanceKpi);
			}
			return $performanceKpiList;
		}catch (Exception $e) {
			throw new PerformanceServiceException($e->getMessage());
		}	  
		
	}
	
	/**
	 * Get Performance Kpi 
	 * @return unknown_type
	 */
	private function getKpiToPerformanceKpi( $kpiList)
	{
		try {
			
			$performanceKpiList	=	array();
			foreach ($kpiList as $kpi) {
				$performanceKpi	=	new PerformanceKpi();
				$performanceKpi->setId( $kpi->getId());
		    	$performanceKpi->setKpi( $kpi->getDesc());
		    	$performanceKpi->setMinRate( $kpi->getMin());
		    	$performanceKpi->setMaxRate( $kpi->getMax());
		    	array_push($performanceKpiList,$performanceKpi);
			}
			return $performanceKpiList;
		} catch (Exception $e) {
		    throw new PerformanceServiceException($e->getMessage());
		}	    
	}


}