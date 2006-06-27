<?
/*
// OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures 
// all the essential functionalities required for any enterprise. 
// Copyright (C) 2006 hSenid Software International Pvt. Ltd, http://www.hsenid.com

// OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
// the GNU General Public License as published by the Free Software Foundation; either
// version 2 of the License, or (at your option) any later version.

// OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
// without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
// See the GNU General Public License for more details.

// You should have received a copy of the GNU General Public License along with this program;
// if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
// Boston, MA  02110-1301, USA
*/

require_once ROOT_PATH . '/lib/models/eimadmin/CurrencyTypes.php';

class EXTRACTOR_CurrencyTypes {
	
	function EXTRACTOR_CurrencyTypes() {

		$this->parent_currencytype = new CurrencyTypes();
	}

	function parseAddData($postArr) {	
			
			$this->parent_currencytype -> setCurrencyId($this->parent_currencytype->getLastRecord());
			$this->parent_currencytype -> setCurrencyDescription(trim($postArr['txtCurrencyTypeDesc']));
			
			return $this->parent_currencytype;
	}

	function parseEditData($postArr) {	
			
			$this->parent_currencytype -> setCurrencyId(trim($postArr['txtCurrencyTypeID']));
			$this->parent_currencytype -> setCurrencyDescription(trim($postArr['txtCurrencyTypeDesc']));
		
			return $this->parent_currencytype;
	}
	
}
?>
