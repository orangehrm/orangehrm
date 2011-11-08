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

class CustomerService extends BaseService {
	
	private $customerDao;

	/**
	 * Construct
	 */
	public function __construct() {
		$this->customerDao = new CustomerDao();
	}

	/**
	 *
	 * @return <type>
	 */
	public function getCustomerDao() {
		return $this->customerDao;
	}

	/**
	 *
	 * @param UbCoursesDao $UbCoursesDao 
	 */
	public function setCustomerDao(CustomerDao $customerDao) {
		$this->customerDao = $customerDao;
	}
	
	/**
	 *
	 * @return <type> 
	 */
	public function getCustomerList($noOfRecords, $offset, $sortField, $sortOrder, $activeOnly){
		return $this->customerDao->getCustomerList($noOfRecords, $offset, $sortField, $sortOrder, $activeOnly);
	}

	/**
	 *
	 * @return <type>
	 */
	public function getCustomerCount($activeOnly){
		return $this->customerDao->getCustomerCount($activeOnly);
	}
	
	public function getCustomerById($customerId){
		return $this->customerDao->getCustomerById($customerId);
	}
	
	public function deleteCustomer($customerId){
		return $this->customerDao->deleteCustomer($customerId);
	}
	
	public function getAllCustomers($activeOnly){
		return $this->customerDao->getAllCustomers();
	}
	
	public function hasCustomerGotTimesheetItems($customerId){
		return $this->customerDao->hasCustomerGotTimesheetItems($customerId);
	}
	
	
}

?>
