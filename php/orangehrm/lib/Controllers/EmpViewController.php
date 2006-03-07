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

require_once OpenSourceEIM . '/lib/Models/hrfunct/EmpInfo.php';
require_once OpenSourceEIM . '/lib/Models/hrfunct/EmpBank.php';
require_once OpenSourceEIM . '/lib/Models/hrfunct/EmpBasSalary.php';
require_once OpenSourceEIM . '/lib/Models/hrfunct/EmpCashBen.php';
require_once OpenSourceEIM . '/lib/Models/hrfunct/EmpConExt.php';
require_once OpenSourceEIM . '/lib/Models/hrfunct/EmpExCur.php';
require_once OpenSourceEIM . '/lib/Models/hrfunct/EmpJobSpec.php';
require_once OpenSourceEIM . '/lib/Models/hrfunct/EmpLang.php';
require_once OpenSourceEIM . '/lib/Models/hrfunct/EmpMembership.php';
require_once OpenSourceEIM . '/lib/Models/hrfunct/EmpNonCashBen.php';
require_once OpenSourceEIM . '/lib/Models/hrfunct/EmpPassPort.php';
require_once OpenSourceEIM . '/lib/Models/hrfunct/EmpQual.php';
require_once OpenSourceEIM . '/lib/Models/hrfunct/EmpQualSub.php';
require_once OpenSourceEIM . '/lib/Models/hrfunct/EmpRepTo.php';
require_once OpenSourceEIM . '/lib/Models/hrfunct/EmpWorkExp.php';

class EmpViewController {

	var $indexCode;
	var $message;
	var $pageID;
	var $headingInfo;
	
		
	function EmpViewController() {
		
	}
	
    function delParser($indexCode,$arrList) {
        $this->indexCode=$indexCode;

        if(($this->indexCode) == 'EMP') {

            $this->employee = new EmpInfo();
            $this->employee->delEmployee($arrList);
			
		}
	} 

	function getInfo($indexCode,$pageNO,$schStr='',$mode=0) {

		$this->indexCode = $indexCode;
					
		if (($this->indexCode) == 'EMP') {
		
			$this-> employee = new EmpInfo();
			$message = $this-> employee -> getListofEmployee($pageNO,$schStr,$mode);
			return $message;
			
		} else if(($this->indexCode) == 'BNK') {
		
			$this->empBank= new EmpBank();
			$message = $this->empBank->getListofEmpBank($pageNO,$schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'SAL') {
		
			$this->empBasSal= new EmpBasSalary();
			$message = $this->empBasSal->getListofEmpBasSal($pageNO,$schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'CBN') {
		
			$this->empCashBen= new EmpCashBen();
			$message = $this->empCashBen->getListofEmpCashBen($pageNO,$schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'NBN') {
		
			$this->empNonCashBen = new EmpNonCashBen();
			$message = $this->empNonCashBen->getListofEmpNonCashBen($pageNO,$schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'CXT') {
		
			$this->empConExt = new EmpConExt();
			$message = $this->empConExt->getListofConExt($pageNO,$schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'EXC') {
		
			$this->empExCur = new EmpExCur();
			$message = $this->empExCur->getListofEmpExCur($pageNO,$schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'JSP') {
		
			$this->empJobSpec = new EmpJobSpec();
			$message = $this->empJobSpec->getListofEmpJobSpec($pageNO,$schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'LAN') {
		
			$this->empLan = new EmpLanguage();
			$message = $this->empLan->getListofEmpLang($pageNO,$schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'MEM') {
		
			$this->empMship = new EmpMembership();
			$message = $this->empMship->getListofEmpMembership($pageNO,$schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'PAS') {
		
			$this->empPP = new EmpPassPort();
			$message = $this->empPP->getListofEmpPP($pageNO,$schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'QUA') {
		
			$this->empQual = new EmpQualification();
			$message = $this->empQual->getListofEmpQual($pageNO,$schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'REP') {
		
			$this->empRepTo = new EmpRepTo();
			$message = $this->empRepTo->getListofEmpRepTo($pageNO,$schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'EXP') {
		
			$this->empWrkExp = new EmpWorkExp();
			$message = $this->empWrkExp->getListofEmpWorkExp($pageNO,$schStr,$mode);
			return $message;

		}
	}
	
	function countList($indexCode,$schStr='',$mode=0) {

		$this->indexCode = $indexCode;
					
		if (($this->indexCode) == 'EMP') {
		
			$this-> employee = new EmpInfo();
			$message = $this-> employee -> countEmployee($schStr,$mode);
			return $message;
			
		} else if(($this->indexCode) == 'BNK') {
		
			$this->empBank= new EmpBank();
			$message = $this->empBank->countEmpBank($schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'SAL') {
		
			$this->empBasSal= new EmpBasSalary();
			$message = $this->empBasSal->countEmpBasSal($schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'CBN') {
		
			$this->empCashBen= new EmpCashBen();
			$message = $this->empCashBen->countEmpCashBen($schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'NBN') {
		
			$this->empNonCashBen = new EmpNonCashBen();
			$message = $this->empNonCashBen->countEmpNonCashBen($schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'CXT') {
		
			$this->empConExt = new EmpConExt();
			$message = $this->empConExt->countConExt($schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'EXC') {
		
			$this->empExCur = new EmpExCur();
			$message = $this->empExCur->countEmpExCur($schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'JSP') {
		
			$this->empJobSpec = new EmpJobSpec();
			$message = $this->empJobSpec->countEmpJobSpec($schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'LAN') {
		
			$this->empLan = new EmpLanguage();
			$message = $this->empLan->countEmpLang($schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'MEM') {
		
			$this->empMship = new EmpMembership();
			$message = $this->empMship->countEmpMembership($schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'PAS') {
		
			$this->empPP = new EmpPassPort();
			$message = $this->empPP->countEmpPP($schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'QUA') {
		
			$this->empQual = new EmpQualification();
			$message = $this->empQual->countEmpQual($schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'REP') {
		
			$this->empRepTo = new EmpRepTo();
			$message = $this->empRepTo->countEmpRepTo($schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'EXP') {
		
			$this->empWrkExp = new EmpWorkExp();
			$message = $this->empWrkExp->countEmpWorkExp($schStr,$mode);
			return $message;

		}
		
	}
	
	function getPageID($indexCode) {
	
		$this->indexCode = $indexCode;	
							
		if (($this->indexCode) == 'EMP') {
		
			$this->pageID = './hremp';
			return $this->pageID;

		} else if (($this->indexCode) == 'CXT') {

			$this->pageID = './hrempconext';
			return $this->pageID;

		} else if (($this->indexCode) == 'EXC') {

			$this->pageID = './hrempextcur';
			return $this->pageID;

		} else if (($this->indexCode) == 'LAN') {

			$this->pageID = './hremplan';
			return $this->pageID;

		} else if (($this->indexCode) == 'EXP') {

			$this->pageID = './hrempwrkexp';
			return $this->pageID;

		} else if (($this->indexCode) == 'JSP') {

			$this->pageID = './hrempjobspec';
			return $this->pageID;

		} else if (($this->indexCode) == 'MEM') {

			$this->pageID = './hrempmemship';
			return $this->pageID;

		} else if (($this->indexCode) == 'SAL') {

			$this->pageID = './hrempbassal';
			return $this->pageID;

		} else if (($this->indexCode) == 'CBN') {

			$this->pageID = './hrempcashben';
			return $this->pageID;

		} else if (($this->indexCode) == 'NBN') {

			$this->pageID = './hrempnoncashben';
			return $this->pageID;

		} else if (($this->indexCode) == 'QUA') {

			$this->pageID = './hrempqual';
			return $this->pageID;

		} 	
	} 
	
	
	function getHeadingInfo($indexCode) {
		
		$this->indexCode = $indexCode;					
		
		if (($this->indexCode) == 'EMP') {
		
			$this->headingInfo = array ('Employee Information',1);
			return $this->headingInfo;

		} else if(($this->indexCode) == 'SAL') {

			$this->headingInfo = array ('Remuneration',1);
			return $this->headingInfo;

		} else if(($this->indexCode) == 'CBN') {

			$this->headingInfo = array ('Cash Benefits Assigned to Employee',1);
			return $this->headingInfo;

		} else if(($this->indexCode) == 'NBN') {

			$this->headingInfo = array ('Non Cash Benefits Assigned to Employee' ,1);
			return $this->headingInfo;

		} else if(($this->indexCode) == 'CXT') {

			$this->headingInfo = array ('Contract Extends' ,1);
			return $this->headingInfo;

		} else if(($this->indexCode) == 'EXC') {

			$this->headingInfo = array ('Extra Curricular Activities' ,1);
			return $this->headingInfo;

		} else if(($this->indexCode) == 'JSP') {

			$this->headingInfo = array ('Job Profile' ,1);
			return $this->headingInfo;

		} else if(($this->indexCode) == 'LAN') {

			$this->headingInfo = array ('Language' ,1);
			return $this->headingInfo;

		} else if(($this->indexCode) == 'MEM') {

			$this->headingInfo = array ('Memberships' ,1);
			return $this->headingInfo;


		} else if(($this->indexCode) == 'QUA') {

			$this->headingInfo = array ('Qualification' ,1);
			return $this->headingInfo;

		} else if(($this->indexCode) == 'REP') {

			$this->headingInfo = array ('Report To' ,1);
			return $this->headingInfo;

		} else if(($this->indexCode) == 'EXP') {

			$this->headingInfo = array ('Work Experience' ,1);
			return $this->headingInfo;
		} 
	}

	function getUnAssigned($indexCode,$pageNO,$schStr='',$mode=0) {

		$this->indexCode = $indexCode;
					
		if(($this->indexCode) == 'BNK') {
		
			$this->empBank= new EmpBank();
			$message = $this->empBank->getUnAssEmployee($pageNO,$schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'SAL') {
		
			$this->empBasSal= new EmpBasSalary();
			$message = $this->empBasSal->getUnAssEmployee($pageNO,$schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'CBN') {
		
			$this->empCashBen= new EmpCashBen();
			$message = $this->empCashBen->getUnAssEmployee($pageNO,$schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'NBN') {
		
			$this->empNonCashBen = new EmpNonCashBen();
			$message = $this->empNonCashBen->getUnAssEmployee($pageNO,$schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'CXT') {
		
			$this->empConExt = new EmpConExt();
			$message = $this->empConExt->getUnAssEmployee($pageNO,$schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'EXC') {
		
			$this->empExCur = new EmpExCur();
			$message = $this->empExCur->getUnAssEmployee($pageNO,$schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'JSP') {
		
			$this->empJobSpec = new EmpJobSpec();
			$message = $this->empJobSpec->getUnAssEmployee($pageNO,$schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'LAN') {
		
			$this->empLan = new EmpLanguage();
			$message = $this->empLan->getUnAssEmployee($pageNO,$schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'MEM') {
		
			$this->empMship = new EmpMembership();
			$message = $this->empMship->getUnAssEmployee($pageNO,$schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'PAS') {
		
			$this->empPP = new EmpPassPort();
			$message = $this->empPP->getUnAssEmployee($pageNO,$schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'QUA') {
		
			$this->empQual = new EmpQualification();
			$message = $this->empQual->getUnAssEmployee($pageNO,$schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'REP') {
		
			$this->empRepTo = new EmpRepTo();
			$message = $this->empRepTo->getUnAssEmployee($pageNO,$schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'EXP') {
		
			$this->empWrkExp = new EmpWorkExp();
			$message = $this->empWrkExp->getUnAssEmployee($pageNO,$schStr,$mode);
			return $message;

		}
	}

	function countUnAssigned($indexCode,$schStr='',$mode=0) {

		$this->indexCode = $indexCode;
					
		if(($this->indexCode) == 'BNK') {
		
			$this->empBank= new EmpBank();
			$message = $this->empBank->countUnAssEmployee($schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'SAL') {
		
			$this->empBasSal= new EmpBasSalary();
			$message = $this->empBasSal->countUnAssEmployee($schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'CBN') {
		
			$this->empCashBen= new EmpCashBen();
			$message = $this->empCashBen->countUnAssEmployee($schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'NBN') {
		
			$this->empNonCashBen = new EmpNonCashBen();
			$message = $this->empNonCashBen->countUnAssEmployee($schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'CXT') {
		
			$this->empConExt = new EmpConExt();
			$message = $this->empConExt->countUnAssEmployee($schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'EXC') {
		
			$this->empExCur = new EmpExCur();
			$message = $this->empExCur->countUnAssEmployee($schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'JSP') {
		
			$this->empJobSpec = new EmpJobSpec();
			$message = $this->empJobSpec->countUnAssEmployee($schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'LAN') {
		
			$this->empLan = new EmpLanguage();
			$message = $this->empLan->countUnAssEmployee($schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'MEM') {
		
			$this->empMship = new EmpMembership();
			$message = $this->empMship->countUnAssEmployee($schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'PAS') {
		
			$this->empPP = new EmpPassPort();
			$message = $this->empPP->countUnAssEmployee($schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'QUA') {
		
			$this->empQual = new EmpQualification();
			$message = $this->empQual->countUnAssEmployee($schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'REP') {
		
			$this->empRepTo = new EmpRepTo();
			$message = $this->empRepTo->countUnAssEmployee($schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'EXP') {
		
			$this->empWrkExp = new EmpWorkExp();
			$message = $this->empWrkExp->countUnAssEmployee($schStr,$mode);
			return $message;

		}
		
	}
	
}

?>
