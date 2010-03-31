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
 require_once ROOT_PATH . '/lib/models/eimadmin/Customer.php';
 require_once ROOT_PATH . '/lib/common/CommonFunctions.php';
 
 class EXTRACTOR_Customer {

	function EXTRACTOR_Customer() {

		$this->new_customer = new Customer();
	}

	function parseAddData($postArr) {
			$this->new_customer ->setCustomerName(CommonFunctions::escapeHtml(trim($postArr['txtName'])));
			$this->new_customer ->setCustomerDescription(trim(CommonFunctions::escapeHtml($postArr['txtDescription'])));

			return $this->new_customer;
	}

	function parseEditData($postArr) {
			$this->new_customer ->setCustomerId(trim($postArr['txtId']));
			$this->new_customer ->setCustomerName(CommonFunctions::escapeHtml(trim($postArr['txtName'])));
			$this->new_customer ->setCustomerDescription(CommonFunctions::escapeHtml(trim($postArr['txtDescription'])));

			return $this->new_customer;
	}
}
?>