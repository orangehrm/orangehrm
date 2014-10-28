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

class PayGradeService extends BaseService {
	
	private $payGradeDao;

	/**
	 * Construct
	 */
	public function __construct() {
		$this->payGradeDao = new PayGradeDao();
	}

	/**
	 *
	 * @return <type>
	 */
	public function getPayGradeDao() {
		return $this->payGradeDao;
	}

	/**
	 *
	 * @param CustomerDao $customerDao 
	 */
	public function setPayGradeDao(PayGradeDao $payGradeDao) {
		$this->payGradeDao = $payGradeDao;
	}
	
	public function getPayGradeById($payGradeId){
		return $this->payGradeDao->getPayGradeById($payGradeId);
	}
	
	public function getPayGradeList($sortField='name', $sortOrder='ASC'){
		return $this->payGradeDao->getPayGradeList($sortField, $sortOrder);
	}
	
	public function getCurrencyListByPayGradeId($payGradeId){
		return $this->payGradeDao->getCurrencyListByPayGradeId($payGradeId);
	}
	
	public function getCurrencyByCurrencyIdAndPayGradeId($currencyId, $payGradeId){
		return $this->payGradeDao->getCurrencyByCurrencyIdAndPayGradeId($currencyId, $payGradeId);
	}

}

?>
