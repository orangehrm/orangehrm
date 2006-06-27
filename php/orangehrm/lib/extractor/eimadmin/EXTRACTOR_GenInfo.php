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

require_once ROOT_PATH . '/lib/models/eimadmin/GenInfo.php';

class EXTRACTOR_GenInfo {
	
	function EXTRACTOR_GenInfo() {

		$this->parent_geninfo = new GenInfo();
	}

	function parseData($postArr) {	
			
		$arrGenInfo = array('COMPANY'	=> 'txtCompanyName',
							'COUNTRY'	=> 'cmbCountry',
							'STREET1'	=> 'txtStreet1',
							'STREET2'	=> 'txtStreet2',
							'STATE'		=> 'cmbState',
							'CITY'		=> 'cmbCity',
							'ZIP'		=> 'txtZIP',
							'PHONE'		=> 'txtPhone',
							'FAX'		=> 'txtFax',
							'TAX'		=> 'txtTaxID',
							'NAICS'		=> 'txtNAICS',
							'COMMENTS'	=> 'txtComments');
							
		$genInfoKeys = implode('|',array_keys($arrGenInfo));
			
		$this->parent_geninfo -> setGenInfoKeys($genInfoKeys);
		
		$arrGenInfoUI = array_values($arrGenInfo);
		for($c=0; count($arrGenInfo) > $c; $c++) {
			$arrGenInfoValues[$c] = $postArr[$arrGenInfoUI[$c]];
		}
		
		$genInfoValues = implode('|',$arrGenInfoValues);
		
		$this->parent_geninfo -> setGenInfoValues($genInfoValues);
	
		return $this->parent_geninfo;
	}
			
}
?>
