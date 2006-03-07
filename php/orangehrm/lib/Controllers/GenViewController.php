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

require_once OpenSourceEIM . '/lib/Models/eimadmin/DesDis.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/DesQual.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/CashBenSal.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/NonCashBenSal.php';

class GenViewController {

	var $indexCode;
	var $message;
	var $pageID;
	var $headingInfo;
	
		
	function GenViewController() {
		
	}
	

	function selectIndexId($pageNO,$schStr,$mode) {
					
	
		if (($this->indexCode) == 'DDI') {

			$this-> designation = new DesDescription();
			$message = $this-> designation -> getUnAssDesignations($pageNO,$schStr,$mode);

			return $message;

		}  else if (($this->indexCode) == 'DQA') {

			$this-> designation = new DesQualification();
			$message = $this-> designation -> getUnAssDesignations($pageNO,$schStr,$mode);

			return $message;

		}  else if (($this->indexCode) == 'BBS') {

			$this-> salgrade = new CashBenSalary();
			$message = $this-> salgrade -> getUnAssCashBenefits($pageNO,$schStr,$mode);

			return $message;

		}  else if (($this->indexCode) == 'NBS') {

			$this-> noncashbensal = new NonCashBenSalary();
			$message = $this-> noncashbensal -> getUnAssCashBenefits($pageNO,$schStr,$mode);

			return $message;
		}
	}
	
	
	function getPageID($indexCode) {
	
		$this->indexCode = $indexCode;	
							

		if (($this->indexCode) == 'DDI') {

			$this->pageID = './desdis';
			return $this->pageID;

		} else if (($this->indexCode) == 'DQA') {

			$this->pageID = './desqua';
			return $this->pageID;

		} else if (($this->indexCode) == 'BBS') {

			$this->pageID = './cashbensal';
			return $this->pageID;

		} else if (($this->indexCode) == 'NBS') {

			$this->pageID = './noncashbensal';
			return $this->pageID;
		}
	} 
	
	
	function getHeadingInfo($indexCode) {
		
		$this->indexCode = $indexCode;					
		
		if (($this->indexCode) == 'DDI') {

			$this->headingInfo = array ('Designation ID','Designation Name',2,'Designation Description');
			return $this->headingInfo;

		} else if (($this->indexCode) == 'DQA') {

			$this->headingInfo = array ('Designation ID','Designation Name',2,'Designation Qualification');
			return $this->headingInfo;

		} else if (($this->indexCode) == 'BBS') {

			$this->headingInfo = array ('Salary Grade ID','Salary Grade Name',2,'Cash Benefits Assigned to Salary Grade');
			return $this->headingInfo;

		} else if (($this->indexCode) == 'NBS') {

			$this->headingInfo = array ('Salary Grade ID','Salary Grade Name',2,'Non Cash Benefits Assigned to Salary Grade');
			return $this->headingInfo;
		}
	}
	
	
	
	function getInfo($indexCode,$pageNO,$schStr='',$mode=0) {
	
		$this->indexCode = $indexCode;
		return $this->selectIndexId($pageNO,$schStr,$mode);
	
	}
	
	
	function getPageName($indexCode) {
		
		$this->indexCode = $indexCode;
		return $this->getPageID();
		
	}
	
	function countList($index,$schStr='',$mode=0) {
		
		$this->indexCode=$index;
					

		if (($this->indexCode) == 'DDI') {

			$this-> designation = new DesDescription();
			$message = $this-> designation -> countUnAssDesignations($schStr,$mode);

			return $message;

		}  else if (($this->indexCode) == 'DQA') {

			$this-> designation = new DesQualification();
			$message = $this-> designation ->countUnAssDesignations($schStr,$mode);

			return $message;

		}  else if (($this->indexCode) == 'BBS') {

			$this-> salgrade = new CashBenSalary();
			$message = $this-> salgrade -> countUnAssCashBenefits($schStr,$mode);

			return $message;

		}  else if (($this->indexCode) == 'NBS') {

			$this-> salgrade = new NonCashBenSalary();
			$message = $this-> salgrade -> countUnAssCashBenefits($schStr,$mode);

			return $message;
		}
	}
	
}

?>
