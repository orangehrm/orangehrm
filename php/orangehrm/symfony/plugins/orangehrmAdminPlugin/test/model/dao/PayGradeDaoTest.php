<?php

/**
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
 */
require_once sfConfig::get('sf_test_dir') . '/util/TestDataService.php';

/**
 *  @group Admin
 */
class PayGradeDaoTest extends PHPUnit_Framework_TestCase {
	
	private $payGradeDao;
	protected $fixture;

	/**
	 * Set up method
	 */
	protected function setUp() {

		$this->payGradeDao = new PayGradeDao();
		$this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmAdminPlugin/test/fixtures/PayGradeDao.yml';
		TestDataService::populate($this->fixture);
	}
	
	public function testGetPayGradeList(){
		$result = $this->payGradeDao->getPayGradeList();
		$this->assertEquals(count($result), 3);
	}
	
	public function testGetPayGradeById(){
		$result = $this->payGradeDao->getPayGradeById(1);
		$this->assertEquals($result->getName(), 'Pay Grade 1');
	}
	
	public function testGetCurrencyListByPayGradeId(){
		$result = $this->payGradeDao->getCurrencyListByPayGradeId(1);
		$this->assertEquals(count($result), 2);
	}
	
	public function testGetCurrencyByCurrencyIdAndPayGradeId(){
		$result = $this->payGradeDao->getCurrencyByCurrencyIdAndPayGradeId('USD', 1);
		$this->assertEquals($result->getMinSalary(), 5000);
	}
}

?>
