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
    public function testSaveKpi(){
    	
    	foreach ($this->testCases['Kpi'] as $key=>$testCase) {
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
			
			$this->testCases['Kpi'][$key]["id"] =  $kpi->getId();
    	}

    	file_put_contents(sfConfig::get('sf_test_dir') . '/fixtures/performance/kpi.yml',sfYaml::dump($this->testCases));
    }

	/**
     * Verify fix for bug: 3006775.
     * Tests that any doctrine validator exceptions are thrown so that the action classes can handle them,
     * instead of catching them and throwing generic dao exceptions.
     *
     */
    public function testSaveKpiValidation(){

    	foreach ($this->testCases['Kpi'] as $key=>$testCase) {
			$kpi = new DefineKpi();
			$kpi->setJobtitlecode("JOB001 '+%7C%7C+'ACUtwoACU'");
			$kpi->setDesc($testCase['desc']);
			$kpi->setMin($testCase['min']);
			$kpi->setMax($testCase['max']);
			$kpi->setDefault($testCase['default']);
			$kpi->setIsactive($testCase['isactive']);

            // This save should fail because job title code is too long for the job title field.
            // Should throw a Doctrine_Validator_Exception

            try {
			    $kpi = $this->kpiDao->saveKpi($kpi);
                $this->fail("Validation exception expected.");
            } catch (Doctrine_Validator_Exception $e) {
                // expected
            } catch (Exception $e) {
                // Should not throw other exception
                $this->fail("Validation exception expected. Should not throw other exception");
            }
    	}
    }

	/**
	 * Test Read Kpi
	 * @return unknown_type
	 */
	public function testReadKpi(){
		foreach ($this->testCases['Kpi'] as $key=>$testCase) {
			$result	=	$this->kpiDao->readKpi( $testCase['id']);
			$this->assertTrue($result instanceof DefineKpi);
		}
		
	}
	
	/**
	 * Test Get default Kpi rating
	 */
	public function testGetKpiDefaultRate(  ){
		$result	=	$this->kpiDao->getKpiDefaultRate( );
		$this->assertTrue($result instanceof DefineKpi);
		
	}
	
	/**
	 * Test over ride default kpi 
	 */
	public function testOverRideKpiDefaultRate(  ){
		foreach ($this->testCases['Kpi'] as $key=>$testCase) {
			$kpi	=	$this->kpiDao->readKpi( $testCase['id']);
			$result	=	$this->kpiDao->overRideKpiDefaultRate($kpi);
			$this->assertTrue($result);
		}
		
		
	}
	
	/**
	 * Test Get default Kpi rating
	 */
	public function testGetKpiForJobTitle(  ){
		$kpiList	=	$this->kpiDao->getKpiForJobTitle( $this->testCases['JobTitle']['jobtitlecode']);
		foreach( $kpiList as $result){
			$this->assertTrue($result instanceof DefineKpi);
		}
	}
	
	/**
	 * Test delete kpi for job title
	 */
	public function testDeleteKpiForJobTitle(  ){
		$result	=	$this->kpiDao->deleteKpiForJobTitle( $this->testCases['JobTitle']['jobtitlecode']);
		$this->assertTrue($result);
	}
	
	/**
	 * Test delete Kpi
	 */
	public function testDeleteKpi(  ){
		$deleteList	=	array();
		foreach ($this->testCases['Kpi'] as $key=>$testCase) {
			array_push($deleteList,$testCase['id']);
			unset($this->testCases['Kpi'][$key]["id"]);
		}
		$result = $this->kpiDao->deleteKpi( $deleteList );
		$this->assertTrue($result);
		
		file_put_contents(sfConfig::get('sf_test_dir') . '/fixtures/performance/kpi.yml',sfYaml::dump($this->testCases));
	}
    
}