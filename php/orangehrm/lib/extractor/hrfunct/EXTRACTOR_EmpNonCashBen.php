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

require_once ROOT_PATH . '/lib/models/hrfunct/EmpNonCashBen.php';
require_once ROOT_PATH . '/lib/models/eimadmin/NonCashBen.php';

class EXTRACTOR_EmpNonCashBen {
	
	function EXTRACTOR_EmpNonCashBen() {

		$this->empnoncashben = new EmpNonCashBen();
		$this->noncashben = new NonCashBen();
	}
	
	function parseData($postArr) {	
		
		if($postArr['STAT'] == 'ADD' || $postArr['STAT'] == 'ADDOTH') {
		   $arr = $postArr['chkdel'];
		   $benlist = $this->noncashben->getBenCodes();
		   
		   for($c=0;count($arr)>$c;$c++)
		       if($arr[$c]!=NULL) {
			       	$this->empnoncashben->setEmpId($postArr['txtEmpID']);
			       	$this->empnoncashben->setEmpBenCode($arr[$c]);
			       	$object[$c] = $this->empnoncashben;
		       }
			return $object;
			
		} elseif($postArr['STAT'] == 'EDIT') {

			    $this->empnoncashben->setEmpId($postArr['txtEmpID']);
				$this->empnoncashben->setEmpBenCode($postArr['cmbBenCode']);
				$this->empnoncashben->setEmpBenIssDat($postArr['txtBenIssDat']);
				$this->empnoncashben->setEmpBenQty(trim($postArr['txtBenQty']));
				$this->empnoncashben->setEmpBenComment(trim($postArr['txtBenComment']));
				$this->empnoncashben->setEmpBenItmReturnableFlag(isset($postArr['chkBenItmReturnableFlag'])?'1':'0');
				$this->empnoncashben->setEmpBenItmRetDat(trim($postArr['txtBenItmRetDat']));
				$this->empnoncashben->setEmpBenItmRetFlag(isset($postArr['chkBenItmRetFlag'])?'1':'0');
			
			return $this->empnoncashben;
		}
	}
}
?>