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
 * Kpi Dao Test class 
 *
 * @author Samantha Jayasinghe
 */
require_once 'PHPUnit/Framework.php';

class KpiDaoTest extends PHPUnit_Framework_TestCase{
	
	private $testCases;
	private $kpiDao ;
	
	/**
     * PHPUnit setup function
     */
    public function setup() {

       $configuration 		= ProjectConfiguration::getApplicationConfiguration('orangehrm', 'test', true);       
       $this->testCases 	= sfYaml::load(sfConfig::get('sf_test_dir') . '/fixtures/performance/kpi.yml');
       $this->kpiDao		=	new KpiDao();
	   
    }
    
	/**
     * Test Save Kpi function
     *
     */
    public function testSaveKpi()
    {
    	
    	foreach ($this->testCases['Kpi'] as $testCase) {
			$kpi	=	new DefineKpi();
			$kpi->setJobtitlecode ($this->testCases['JobTitle']['jobtitlecode']);
			$kpi->setDesc ( $testCase['desc']);
			$kpi->setMin ( $testCase['min'] );	
			$kpi->setMax ( $testCase['max'] );
			$kpi->setDefault ( $testCase['default'] );
			$kpi->setIsactive ( $testCase['isactive'] );
			
			$kpi 	= $this->kpiDao->saveKpi( $kpi );
			$result	=	($kpi instanceof DefineKpi)?true:false;
			$this->assertTrue($result);
			
    	}
    	  
    }
    
}