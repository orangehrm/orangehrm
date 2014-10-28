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
 * @group Admin
 */
class PayGradeServiceTest extends PHPUnit_Framework_TestCase {
	
	private $payGradeService;
	protected $fixture;

	/**
	 * Set up method
	 */
	protected function setUp() {

		$this->payGradeService = new PayGradeService();
		$this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmAdminPlugin/test/fixtures/PayGradeDao.yml';
		TestDataService::populate($this->fixture);
	}
	
	public function testGetPayGradeList() {

		$payGradeList = TestDataService::loadObjectList('PayGrade', $this->fixture, 'PayGrade');

		$payGradeDao = $this->getMock('PayGradeDao');
		$payGradeDao->expects($this->once())
			->method('getPayGradeList')
			->will($this->returnValue($payGradeList));

		$this->payGradeService->setPayGradeDao($payGradeDao);

		$result = $this->payGradeService->getPayGradeList();
		$this->assertEquals($result, $payGradeList);
	}
	
	public function testGetPayGradeById() {

		$payGradeList = TestDataService::loadObjectList('PayGrade', $this->fixture, 'PayGrade');

		$payGradeDao = $this->getMock('PayGradeDao');
		$payGradeDao->expects($this->once())
			->method('getPayGradeById')
			->with(1)
			->will($this->returnValue($payGradeList[0]));

		$this->payGradeService->setPayGradeDao($payGradeDao);

		$result = $this->payGradeService->getPayGradeById(1);
		$this->assertEquals($result, $payGradeList[0]);
	}
	
	public function testGetCurrencyListByPayGradeId() {

		$payGradeCurrencyList = TestDataService::loadObjectList('PayGradeCurrency', $this->fixture, 'PayGradeCurrency');
		$payGradeCurrencyList = array($payGradeCurrencyList[0], $payGradeCurrencyList[1]);

		$payGradeDao = $this->getMock('PayGradeDao');
		$payGradeDao->expects($this->once())
			->method('getCurrencyListByPayGradeId')
			->with(1)
			->will($this->returnValue($payGradeCurrencyList));

		$this->payGradeService->setPayGradeDao($payGradeDao);

		$result = $this->payGradeService->getCurrencyListByPayGradeId(1);
		$this->assertEquals($result, $payGradeCurrencyList);
	}
	
	public function testGetCurrencyByCurrencyIdAndPayGradeId() {

		$payGradeCurrencyList = TestDataService::loadObjectList('PayGradeCurrency', $this->fixture, 'PayGradeCurrency');

		$payGradeDao = $this->getMock('PayGradeDao');
		$payGradeDao->expects($this->once())
			->method('getCurrencyByCurrencyIdAndPayGradeId')
			->with('USD', 1)
			->will($this->returnValue($payGradeCurrencyList[0]));

		$this->payGradeService->setPayGradeDao($payGradeDao);

		$result = $this->payGradeService->getCurrencyByCurrencyIdAndPayGradeId('USD', 1);
		$this->assertEquals($result, $payGradeCurrencyList[0]);
	}
}
