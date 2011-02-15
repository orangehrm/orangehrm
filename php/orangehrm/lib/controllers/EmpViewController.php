<?php
/*
// OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
// all the essential functionalities required for any enterprise.
// Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com

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

require_once ROOT_PATH . '/lib/models/hrfunct/EmpInfo.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpBasSalary.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpConExt.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpEducation.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpLang.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpMembership.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpPassPort.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpQual.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpRepTo.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpWorkExp.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpAttach.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpDependents.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpSkill.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpLicenses.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpChildren.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpEmergencyCon.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpDirectDebit.php';
require_once ROOT_PATH . '/lib/models/eimadmin/PayPeriod.php';
require_once ROOT_PATH . '/lib/models/eimadmin/CustomFields.php';
require_once ROOT_PATH . '/lib/models/eimadmin/Location.php';
require_once ROOT_PATH . '/lib/models/hrfunct/LocationHistory.php';
require_once ROOT_PATH . '/lib/models/hrfunct/JobTitleHistory.php';
require_once ROOT_PATH . '/lib/models/hrfunct/SubDivisionHistory.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpLocation.php';
require_once ROOT_PATH . '/lib/models/eimadmin/EmployStat.php';

require_once ROOT_PATH . '/lib/common/FormCreator.php';
require_once ROOT_PATH . '/lib/models/benefits/HspSummary.php';
require_once ROOT_PATH . '/lib/common/Config.php';
require_once ROOT_PATH . '/lib/common/authorize.php';
require_once ROOT_PATH . '/lib/utils/CSRFTokenGenerator.php';

class EmpViewController {

	var $indexCode;
	var $message;
	var $pageID;
	var $headingInfo;


	function EmpViewController() {
	}

	function viewList($getArr,$postArr) {

		$form_creator = new FormCreator($getArr,$postArr);
		$form_creator ->formPath = '/empview.php';

		if (isset($getArr['reqcode'])) {
 			$form_creator ->popArr['headinginfo'] = $this ->getHeadingInfo(trim($getArr['reqcode']));
    	}

		if (isset($postArr['pageNO'])) {
			$currentPage = (int) $postArr['pageNO'];
		} elseif (isset($getArr['pageNO'])) {
			$currentPage = (int) $getArr['pageNO'];
		} else {
			$currentPage = 1;
		}

    	$form_creator ->popArr['currentPage'] = $currentPage;

		$sortField = 4;
		$sortOrder = 'ASC';
 		if (isset($getArr['sortField']) && isset($getArr['sortOrder'.$getArr['sortField']])) {
			$sortField = $getArr['sortField'];
			$sortOrder = $getArr['sortOrder'.$getArr['sortField']];
 		}

		if (isset($postArr['captureState'])&& ($postArr['captureState']=="SearchMode")) {
		    $choice=$postArr['loc_code'];
    		$strName=trim($postArr['loc_name']);
    		$form_creator ->popArr['emplist'] = $this->getInfo(trim($getArr['reqcode']),$currentPage,$strName,$choice, $sortField, $sortOrder);
    	} else {
    		$form_creator ->popArr['emplist'] = $this->getInfo(trim($getArr['reqcode']),$currentPage, '', -1, $sortField, $sortOrder);
		}

   		if (isset($postArr['captureState'])&& ($postArr['captureState']=="SearchMode"))
			$form_creator ->popArr['temp'] = $this->countList(trim($getArr['reqcode']),$strName,$choice, $sortField, $sortOrder);
		else
			$form_creator ->popArr['temp'] = $this->countList(trim($getArr['reqcode']), '', -1, $sortField, $sortOrder);

		$form_creator->display();
	}


	function delParser($indexCode,$arrList) {
        $this->indexCode=$indexCode;

        if(($this->indexCode) == 'EMP') {

            $this->employee = new EmpInfo();
            $res=$this->employee->delEmployee($arrList);

		}

		if(($this->indexCode) == 'JSP') {

            $this->empjob = new EmpJobSpec();
            $res=$this->empjob->delEmpJobSpec($arrList);

		}

		if ($res === false) {
			$_GET['message']='DELETE_FAILURE';
		} else {
			$_GET['message']='DELETE_SUCCESS';
		}
	}

	function xajaxObjCall($value,$reqcode,$cntrl) {

		switch ($reqcode) {

			case 'MEM' :
							if($cntrl == 'unAssMembership') {

								$memshipinfo = new MembershipInfo();
								return $memshipinfo ->getUnAssMembershipCodes($value[0], $value[1]);
							}
							break;

			case 'BAS' :
							if($cntrl == 'currency') {

								$empbassal = new EmpBasSalary();
								$salGrd = $value[0];
								$employee = $value[1];
								return $empbassal->getUnAssCurrCodes($salGrd,$employee);
							}
							break;
			case 'BAS_FOR_PIM' :
							if($cntrl == 'currency') {

								$empbassal = new EmpBasSalary();
								$salGrd = $value[0];
								$employee = $value[1];
								return $empbassal->getCurrCodes($salGrd);
							}
							break;

			case 'EMP' :
							if($cntrl == 'province') {

								$province = new ProvinceInfo();
								return $province->getProvinceCodes($value);
							}

							if($cntrl == 'district') {

								$district = new DistrictInfo();
								return $district->getDistrictCodes($value);
							}
							break;

			case 'PASSPORT' :

							if($cntrl == 'assigned') {

								$pport = new EmpPassPort();
								return $pport ->getAssEmpPP($value);
							}

							if($cntrl == 'newseqno') {

								$pport = new EmpPassPort();
								return $pport ->getLastRecord($value);
							}

							if($cntrl == 'filter') {

								$pport = new EmpPassPort();
								return $pport ->filterEmpPP($value);
							}

							break;
		}
	}

	function getInfo($indexCode,$pageNO,$schStr='',$mode=0, $sortField=4, $sortOrder='ASC') {

		$this->indexCode = $indexCode;

		if (($this->indexCode) == 'EMP') {

			/* Filter by supervisor */
			$supervisorId = null;
			if ($_SESSION['isSupervisor']) {
				$supervisorId = $_SESSION['empID'];
			}

			$this->employee = new EmpInfo();
			$message = $this->employee->getListofEmployee($pageNO,$schStr,$mode, $sortField, $sortOrder, $supervisorId);
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

		} else if(($this->indexCode) == 'DEP') {

			$this->empDep = new EmpDependents();
			$message = $this->empDep->getListofEmpDep($pageNO,$schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'CHI') {

			$this->empChi = new EmpChildren();
			$message = $this->empChi->getListofEmpChi($pageNO,$schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'EMC') {

			$this->empecon = new EmpEmergencyCon();
			$message = $this->empecon->getListofEmpEC($pageNO,$schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'QUA') {

			$this->empQual = new EmpQualification();
			$message = $this->empQual->getListofEmpQual($pageNO,$schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'REP') {

			$this->empinfo = new EmpInfo();
			$message = $this->empinfo->getListofEmpRepTo($pageNO,$schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'EXP') {

			$this->empWrkExp = new EmpWorkExp();
			$message = $this->empWrkExp->getListofEmpWorkExp($pageNO,$schStr,$mode);
			return $message;


		} else if(($this->indexCode) == 'DPN') {

			$this->empDp = new EmpDependents();
			$message = $this->empDp->getListofEmpDp($pageNO,$schStr,$mode);
			return $message;


		} else if(($this->indexCode) == 'SKI') {

			$this->empSkill = new EmpSkill();
			$message = $this->empSkill->getListofEmpSkill($pageNO,$schStr,$mode);
			return $message;
		}

		else if(($this->indexCode) == 'LIC') {

			$this->emplicen = new EmpLicenses();
			$message = $this->emplicen->getListofEmpLicenses($pageNO,$schStr,$mode);
			return $message;
		}
	}

	function countList($indexCode,$schStr='',$mode=0) {

		$this->indexCode = $indexCode;

		if (($this->indexCode) == 'EMP') {

			$this-> empinfo = new EmpInfo();

			$message = 0;

			if ($_SESSION['isAdmin'] == 'Yes') {
				$message = $this->empinfo->countEmployee($schStr,$mode);
			} elseif ($_SESSION['isSupervisor'] == 'Yes') {
				$supervisorID = $_SESSION['empID'];
				$message = $this->empinfo->countSubordinates($supervisorID);
			}

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

		} else if(($this->indexCode) == 'DEP') {

			$this->empDep = new EmpDependents();
			$message = $this->empDep->countEmpDep($schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'QUA') {

			$this->empQual = new EmpQualification();
			$message = $this->empQual->countEmpQual($schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'REP') {

			$this->empinfo = new EmpInfo();
			$message = $this->empinfo->countEmpRepTo($schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'EXP') {

			$this->empWrkExp = new EmpWorkExp();
			$message = $this->empWrkExp->countEmpWorkExp($schStr,$mode);
			return $message;



		} else if(($this->indexCode) == 'SKI') {

			$this->empSkill = new EmpSkill();
			$message = $this->empSkill->countEmpSkill($schStr,$mode);
			return $message;

		}

		 else if(($this->indexCode) == 'LIC') {

			$this->emplicen = new EmpLicenses();
			$message = $this->emplicen->countEmpLicenses($schStr,$mode);
			return $message;
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

		} else if(($this->indexCode) == 'SKI') {

			$this->headingInfo = array ('Skills' ,1);
			return $this->headingInfo;

		} else if(($this->indexCode) == 'LIC') {

			$this->headingInfo = array ('Licenses' ,1);
			return $this->headingInfo;
		}


	}

	function getUnAssigned($indexCode,$pageNO,$schStr='',$mode=-1, $sortField = 0, $sortOrder = 'ASC') {

		$this->indexCode = $indexCode;

		if(($this->indexCode) == 'BNK') {

			$this->empbank = new EmpBank();
			$message = $this->empbank->getUnAssEmployee($pageNO,$schStr,$mode, $sortField, $sortOrder);
			return $message;

		} else if(($this->indexCode) == 'SAL') {

			$this->empinfo= new EmpInfo();
			$message = $this->empinfo->getBasSalUnAssEmployee($pageNO,$schStr,$mode, $sortField, $sortOrder);
			return $message;

		} else if(($this->indexCode) == 'CBN') {

			$this->empinfo= new EmpInfo();
			$message = $this->empinfo->getCashBenUnAssEmployee($pageNO,$schStr,$mode, $sortField, $sortOrder);
			return $message;

		} else if(($this->indexCode) == 'NBN') {

			$this->empinfo= new EmpInfo();
			$message = $this->empinfo->getNonCashUnAssEmployee($pageNO,$schStr,$mode, $sortField, $sortOrder);
			return $message;

		} else if(($this->indexCode) == 'CXT') {

			$this->empinfo = new EmpInfo();
			$message = $this->empinfo->getUnAssEmployee($pageNO,$schStr,$mode, $sortField, $sortOrder);
			return $message;

		} else if(($this->indexCode) == 'EXC') {

			$this->empinfo = new EmpInfo();
			$message = $this->empinfo->getUnAssEmployeeExCur($pageNO,$schStr,$mode, $sortField, $sortOrder);
			return $message;

		} else if(($this->indexCode) == 'JSP') {

			$this->empinfo= new EmpInfo();
			$message = $this->empinfo->getJobSpecUnAssEmployee($pageNO,$schStr,$mode, $sortField, $sortOrder);
			return $message;


		} else if(($this->indexCode) == 'LAN') {

			$this->empinfo= new EmpInfo();
			$message = $this->empinfo->getLangUnAssEmployee($pageNO,$schStr,$mode, $sortField, $sortOrder);
			return $message;

		} else if(($this->indexCode) == 'MEM') {

			$this->empinfo = new EmpInfo();
			$message = $this->empinfo->getUnAssEmployeeMemShip($pageNO,$schStr,$mode, $sortField, $sortOrder);
			return $message;

		} else if(($this->indexCode) == 'PAS') {

			$this->empPP = new EmpPassPort();
			$message = $this->empPP->getUnAssEmployee($pageNO,$schStr,$mode, $sortField, $sortOrder);
			return $message;

		} else if(($this->indexCode) == 'DEP') {

			$this->empDep = new EmpDependents();
			$message = $this->empDep->getUnAssEmployee($pageNO,$schStr,$mode, $sortField, $sortOrder);
			return $message;

		} else if(($this->indexCode) == 'QUA') {

			$this->empinfo= new EmpInfo();
			$message = $this->empinfo->getQulUnAssEmployee($pageNO,$schStr,$mode, $sortField, $sortOrder);
			return $message;

		} else if(($this->indexCode) == 'REP') {

			$this->empinfo = new EmpInfo();
			$message = $this->empinfo->getUnAssEmployeeRepTo($pageNO,$schStr,$mode, $sortField, $sortOrder);
			return $message;

		} else if(($this->indexCode) == 'EXP') {

			$this->empinfo = new EmpInfo();
			$message = $this->empinfo->getUnAssEmployeeWorkExp($pageNO,$schStr,$mode, $sortField, $sortOrder);
			return $message;

		} else if(($this->indexCode) == 'SKI') {

			$this->empinfo = new EmpInfo();
			$message = $this->empinfo->getSkillUnAssEmployee($pageNO,$schStr,$mode, $sortField, $sortOrder);
			return $message;

		} else if(($this->indexCode) == 'LIC') {

			$this->empinfo = new EmpInfo();
			$message = $this->empinfo->getLicensesUnAssEmployee($pageNO,$schStr,$mode, $sortField, $sortOrder);
			return $message;

		}
	}

	function countUnAssigned($indexCode,$schStr='',$mode=0) {

		$this->indexCode = $indexCode;

		if(($this->indexCode) == 'BNK') {

			$this->empbank = new EmpBank();
			$message = $this->empbank->countUnAssEmployee($schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'SAL') {

			$this->empinfo = new EmpInfo();
			$message = $this->empinfo->countBasSalUnAssEmployee($schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'CBN') {

			$this->empinfo = new EmpInfo();
			$message = $this->empinfo->countCashBenUnAssEmployee($schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'NBN') {

			$this->empinfo = new EmpInfo();
			$message = $this->empinfo->countNonCashUnAssEmployee($schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'CXT') {

			$this->empinfo = new EmpInfo();
			$message = $this->empinfo->countUnAssEmployee($schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'EXC') {

			$this->empinfo = new EmpInfo();
			$message = $this->empinfo -> countUnAssEmployeeExCur($schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'JSP') {

			$this->empinfo = new EmpInfo();
			$message = $this->empinfo->countJobSpecUnAssEmployee($schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'LAN') {

			$this->empinfo = new EmpInfo();
			$message = $this->empinfo->countLangUnAssEmployee($schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'MEM') {

			$this->empinfo = new EmpInfo();
			$message = $this->empinfo->countUnAssEmployeeMemShip($schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'PAS') {

			$this->empPP = new EmpPassPort();
			$message = $this->empPP->countUnAssEmployee($schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'DEP') {

			$this->empDep = new EmpDependets();
			$message = $this->empDep->countUnAssEmployee($schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'QUA') {

			$this->empinfo = new EmpInfo();
			$message = $this->empinfo->countQulUnAssEmployee($schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'REP') {

			$this->empinfo = new EmpInfo();
			$message = $this->empinfo->countUnAssEmployeeRepTo($schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'EXP') {

			$this->empinfo = new EmpInfo();
			$message = $this->empinfo->countUnAssEmployeeWorkExp($schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'SKI') {

			$this->empinfo = new EmpInfo();
			$message = $this->empinfo->countSkillUnAssEmployee($schStr,$mode);
			return $message;

		} else if(($this->indexCode) == 'LIC') {

			$this->empinfo = new EmpInfo();
			$message = $this->empinfo->countLicensesUnAssEmployee($schStr,$mode);
			return $message;

		}

	}

	function assignEmpFormData($postArr,$object,$action) {

		if(isset($postArr['econtactSTAT']) && ($postArr['econtactSTAT'] == 'ADD' || $postArr['econtactSTAT'] == 'EDIT')) {

			$empecon = new EmpEmergencyCon();
			$empecon = $object;
			if($action == 'ADD')
				$empecon->addEmpEC();
			elseif($action == 'EDIT')
				$empecon->updateEmpEC();

		return;
		}

		if(isset($postArr['passportSTAT']) && ($postArr['passportSTAT'] == 'ADD' || $postArr['passportSTAT'] == 'EDIT')) {
			$emppassport = new EmpPassPort();
			$emppassport = $object;
			if($action == 'ADD')
				$emppassport->addEmpPP();
			elseif($action == 'EDIT')
				$emppassport->updateEmpPP();

		return;
		}

		if(isset($postArr['directDebitSTAT']) && ($postArr['directDebitSTAT'] == 'ADD' || $postArr['directDebitSTAT'] == 'EDIT')) {
			$directDebit = $object;
			if($action == 'ADD') {
				$directDebit->add();
			} elseif ($action == 'EDIT') {
				$directDebit->update();
			}

			return;
		}

		if(isset($postArr['langSTAT']) && ($postArr['langSTAT'] == 'ADD' || $postArr['langSTAT'] == 'EDIT')) {
			$emplanguage = new EmpLanguage();
			$emplanguage = $object;
			if($action == 'ADD')
				$emplanguage->addEmpLang();
			elseif($action == 'EDIT')
				$emplanguage->updateEmpLang();

		return;
		}

		if(isset($postArr['licenseSTAT']) && ($postArr['licenseSTAT'] == 'ADD' || $postArr['licenseSTAT'] == 'EDIT')) {
			$emplicense = new EmpLicenses();
			$emplicense = $object;
			if($action == 'ADD')
				$emplicense->addEmpLicenses();
			elseif($action == 'EDIT')
				$emplicense->updateEmpLicenses();

		return;
		}

		if(isset($postArr['membershipSTAT']) && ($postArr['membershipSTAT'] == 'ADD' || $postArr['membershipSTAT'] == 'EDIT')) {
			$empmembership = new EmpMembership();
			$empmembership = $object;
			if($action == 'ADD')
				$empmembership->addEmpMembership();
			elseif($action == 'EDIT')
				$empmembership->updateEmpMembership();

		return;
		}

		if(isset($postArr['paymentSTAT']) && ($postArr['paymentSTAT'] == 'ADD' || $postArr['paymentSTAT'] == 'EDIT')) {
			$emppayment = new EmpBasSalary();
			$emppayment = $object;
			if($action == 'ADD')
				$emppayment->addEmpBasSal();
			elseif($action == 'EDIT')
				if(is_array($object)){
					$emppayment = new EmpBasSalary();
					$emppayment->updateEmpBasSal($object);
				}else{
					$emppayment->updateEmpBasSal();
				}

		return;
		}

		if(isset($postArr['educationSTAT']) && ($postArr['educationSTAT'] == 'ADD' || $postArr['educationSTAT'] == 'EDIT')) {
			$empeducation = new EmpEducation();
			$empeducation = $object;
			if($action == 'ADD')
				$empeducation->addEmpEducation();
			elseif($action == 'EDIT')
				$empeducation->updateEmpEducation();

		return;
		}

		if(isset($postArr['wrkexpSTAT']) && ($postArr['wrkexpSTAT'] == 'ADD' || $postArr['wrkexpSTAT'] == 'EDIT')) {
			$empwrkexp = new EmpWorkExp();
			$empwrkexp = $object;
			if($action == 'ADD')
				$empwrkexp->addEmpWorkExp();
			elseif($action == 'EDIT')
				$empwrkexp->updateEmpWorkExp();

		return;
		}

		if(isset($postArr['skillSTAT']) && ($postArr['skillSTAT'] == 'ADD' || $postArr['skillSTAT'] == 'EDIT')) {
			$empskill = new EmpSkill();
			$empskill = $object;
			if($action == 'ADD')
				$empskill->addEmpSkill();
			elseif($action == 'EDIT')
				$empskill->updateEmpSkill();

		return;
		}

		if(isset($postArr['reporttoSTAT']) && ($postArr['reporttoSTAT'] == 'ADD' || $postArr['reporttoSTAT'] == 'EDIT')) {
			$emprepto = new EmpRepTo();
			$emprepto = $object;
			if($action == 'ADD') {
				if ($emprepto->getEmpSupId() == $emprepto->getEmpSubId()) {
					$_GET['message']="SELF_SUPERVISOR_FAILURE";
					return false;
				}

				$emprepto->addEmpRepTo();
			} else if($action == 'EDIT') {

				if(isset($_GET['reporterChanged']) && $_GET['reporterChanged']== 'changed'){
					$emprepto->updateEmpRepTo($_POST['txtSupEmpID'],$_POST['txtSubEmpID'],$_POST['oldRepMethod'],$_POST['cmbRepMethod'], "reporterChanged" );
				}
				else{
					$emprepto->updateEmpRepTo($_POST['txtSupEmpID'],$_POST['txtSubEmpID'],$_POST['oldRepMethod'],$_POST['cmbRepMethod']);
				}
			}

		return;
		}

		if(isset($postArr['dependentSTAT']) && ($postArr['dependentSTAT'] == 'ADD' || $postArr['dependentSTAT'] == 'EDIT')) {
			$empdep = new EmpDependents();
			$empdep = $object;
			if($action == 'ADD') {
				$empdep->addEmpDep();
			} elseif($action == 'EDIT')
				$empdep->updateEmpDep();

		return;
		}

		if(isset($postArr['conextSTAT']) && ($postArr['conextSTAT'] == 'ADD' || $postArr['conextSTAT'] == 'EDIT')) {
			$empconext = new EmpConExt();
			$empconext = $object;
			if($action == 'ADD')
				$empconext -> addConExt();
			elseif($action == 'EDIT')
				$empconext -> updateConExt();

		return;
		}

        if (isset($postArr['empjobHistorySTAT']) && ($postArr['empjobHistorySTAT'] == 'ADD' || $postArr['empjobHistorySTAT'] == 'EDIT')) {
            if ($action == 'ADD') {
                $object->save();
            } else if (($action == 'EDIT') && is_array($object)) {
                foreach ($object as $historyItem) {
                    $historyItem->save();
                }
            }
            return;
        }

		if(isset($postArr['childrenSTAT']) && ($postArr['childrenSTAT'] == 'ADD' || $postArr['childrenSTAT'] == 'EDIT')) {
			$empchi = new EmpChildren();
			$empchi = $object;
			if($action == 'ADD')
				$empchi->addEmpChi();
			elseif($action == 'EDIT')
				$empchi->updateEmpChi();

		return;
		}

		if(isset($postArr['brchSTAT']) && ($postArr['brchSTAT'] == 'ADD' || $postArr['brchSTAT'] == 'EDIT')) {
			$empbank = new EmpBank();
			$empbank = $object;
			if($action == 'ADD')
				$empbank->addEmpBank();
			elseif($action == 'EDIT')
				$empbank->updateEmpBank();

		return;
		}

		if(isset($postArr['attSTAT']) && ($postArr['attSTAT'] == 'ADD' || $postArr['attSTAT'] == 'EDIT')) {

			if($object == null){
				return;
			}

			if (isset($_FILES['ufile']) && ($_FILES['ufile']['size']>0)) {
				$empatt = new EmpAttach();
				$empatt = $object;
				if($action == 'ADD')
					$empatt->addEmpAtt();
			} else {
				$empatt = new EmpAttach();
				$empatt = $object;
				$empatt->updateEmpAtt();
			}
		return;
		}
	}

	function delEmpFormData($getArr,$postArr) {

		if(isset($postArr['econtactSTAT']) && $postArr['econtactSTAT'] =='DEL') {

			$econ = new EmpEmergencyCon();

		    $arr[1]=$postArr['chkecontactdel'];
			for($c=0;count($arr[1])>$c;$c++)
				if($arr[1][$c]!=NULL)
				   $arr[0][$c]=$getArr['id'];

			$econ->delEmpEC($arr);

		}

		if(isset($postArr['passportSTAT']) && $postArr['passportSTAT'] =='DEL') {

			$passport = new EmpPassPort();

			$arr[1]=$postArr['chkpassportdel'];
			for($c=0;count($arr[1])>$c;$c++)
				if($arr[1][$c]!=NULL)
				   $arr[0][$c]=$getArr['id'];

			$passport->delEmpPP($arr);
		}

		if(isset($postArr['directDebitSTAT']) && $postArr['directDebitSTAT'] =='DEL') {

			$directDebit = new EmpDirectDebit();
			$directDebit->delete($getArr['id'], $postArr['chkdebitdel']);
		}

		if(isset($postArr['wrkexpSTAT']) && $postArr['wrkexpSTAT'] =='DEL') {

			$empwrkexp = new EmpWorkExp();

			$arr[1]=$postArr['chkwrkexpdel'];
			for($c=0;count($arr[1])>$c;$c++)
				if($arr[1][$c]!=NULL)
				   $arr[0][$c]=$getArr['id'];

			$empwrkexp->delEmpWorkExp($arr);
		}

		if(isset($postArr['licenseSTAT']) && $postArr['licenseSTAT'] == 'DEL') {

			$license = new EmpLicenses();

			$arr[1]=$postArr['chklicdel'];
			for($c=0;count($arr[1])>$c;$c++)
				if($arr[1][$c]!=NULL)
				   $arr[0][$c]=$getArr['id'];

			$license->delEmpLicenses($arr);
		}

		if(isset($postArr['skillSTAT']) && $postArr['skillSTAT'] == 'DEL') {

			$empskill = new EmpSkill();

			$arr[1]=$postArr['chkskilldel'];
			for($c=0;count($arr[1])>$c;$c++)
				if($arr[1][$c]!=NULL)
				   $arr[0][$c]=$getArr['id'];

			$empskill->delEmpSkill($arr);
		}

		if(isset($postArr['educationSTAT']) && $postArr['educationSTAT'] == 'DEL') {

			$empeducation = new EmpEducation();

			$arr[1]=$postArr['chkedudel'];
			for($c=0;count($arr[1])>$c;$c++)
				if($arr[1][$c]!=NULL)
				   $arr[0][$c]=$getArr['id'];

			$empeducation->delEmpEducation($arr);
		}

		if(isset($postArr['membershipSTAT']) && $postArr['membershipSTAT'] == 'DEL') {

		 	    $empmem = new EmpMembership();
				$arr=$postArr['chkmemdel'];

   				for($c=0;count($arr)>$c;$c++) {
   		 			$frg=explode("|",$arr[$c]);
		 			$arrpass[1][$c]=$frg[0];
					$arrpass[2][$c]=$frg[1];
   				}

   				for($c=0;count($arr)>$c;$c++)
          			if($arr[$c]!=NULL)
	     				$arrpass[0][$c]=$getArr['id'];

   				$empmem->delEmpMembership($arrpass);

		}

		if(isset($postArr['paymentSTAT']) && $postArr['paymentSTAT'] == 'DEL') {

		 	    $emppayment = new EmpBasSalary();
				$arr=$postArr['chkpaydel'];

   				for($c=0;count($arr)>$c;$c++) {
   		 			$frg=explode("|",$arr[$c]);
		 			$arrpass[1][$c]=$frg[0];
					$arrpass[2][$c]=$frg[1];
   				}

   				for($c=0;count($arr)>$c;$c++)
          			if($arr[$c]!=NULL)
	     				$arrpass[0][$c]=$getArr['id'];

   				$emppayment->delEmpBasSal($arrpass);
		}

		if(isset($postArr['reporttoSTAT']) && $postArr['reporttoSTAT'] == 'DEL') {

  			$emprepto = new EmpRepTo();

 			if($postArr['delSupSub']=='sup') {
				$arr=$postArr['chksupdel'];

 				for($c=0;count($arr)>$c;$c++) {
   		 			$frg=explode("|",$arr[$c]);
		 			$arrpass[0][$c]=$frg[0];
					$arrpass[2][$c]=$frg[1];
   	   			}

   				for($c=0;count($arr)>$c;$c++)
          			if($arr[$c]!=NULL)
	     				$arrpass[1][$c]=$getArr['id'];

   			} elseif ($postArr['delSupSub']=='sub') {

   				$arr=$postArr['chksubdel'];

   				for($c=0;count($arr)>$c;$c++) {
   			   		$frg=explode("|",$arr[$c]);
		 	   		$arrpass[1][$c]=$frg[0];
			   		$arrpass[2][$c]=$frg[1];
   	   			}

   				for($c=0;count($arr)>$c;$c++)
          			if($arr[$c]!=NULL)
	     				$arrpass[0][$c]=$getArr['id'];
   			}

			$emprepto->delEmpRepTo($arrpass);
		}

		if(isset($postArr['langSTAT']) && $postArr['langSTAT'] =='DEL') {

		    $emplan = new EmpLanguage();
			$arr=$postArr['chklangdel'];

   			for($c=0;count($arr)>$c;$c++) {
   		  		$frg=explode("|",$arr[$c]);
		   		$arrpass[1][$c]=$frg[0];
		   		$arrpass[2][$c]=$frg[1];
   	   		}

   			for($c=0;count($arr)>$c;$c++)
        		if($arr[$c]!=NULL)
	    			$arrpass[0][$c]=$getArr['id'];

   			$emplan->delEmpLang($arrpass);
		}

		if(isset($postArr['dependentSTAT']) && $postArr['dependentSTAT'] =='DEL') {

			$dep = new EmpDependents();

			$arr[1]=$postArr['chkdepdel'];
			for($c=0;count($arr[1])>$c;$c++)
				if($arr[1][$c]!=NULL)
				   $arr[0][$c]=$getArr['id'];

			$dep->delEmpDep($arr);
		}

		if(isset($postArr['conextSTAT']) && $postArr['conextSTAT'] =='DEL') {

			$conext = new EmpConExt();

			$arr[1]=$postArr['chkconextdel'];
			for($c=0;count($arr[1])>$c;$c++)
				if($arr[1][$c]!=NULL)
				   $arr[0][$c]=$getArr['id'];

			$conext -> delConExt($arr);
		}

         if (isset ($postArr['empjobHistorySTAT']) && $postArr['empjobHistorySTAT'] == 'DEL') {

             // Job title history
             if (isset($postArr['chkjobtitHistory'])) {
                $jobTitleHistory = new JobTitleHistory();
                $jobTitleHistory->delete($postArr['chkjobtitHistory']);
             }

             // Sub division history
             if (isset($postArr['chksubdivisionHistory'])) {
                $empDivisionHistory = new SubDivisionHistory();
                $empDivisionHistory->delete($postArr['chksubdivisionHistory']);
             }

             // Location history
             if (isset($postArr['chklocationHistory'])) {
                $locationHistory = new LocationHistory();
                $locationHistory->delete($postArr['chklocationHistory']);
             }
         }

		if(isset($postArr['childrenSTAT']) && $postArr['childrenSTAT'] =='DEL') {

			$chi = new EmpChildren();

			$arr[1]=$postArr['chkchidel'];
			for($c=0;count($arr[1])>$c;$c++)
				if($arr[1][$c]!=NULL)
				   $arr[0][$c]=$getArr['id'];

			$chi->delEmpChi($arr);
		}

		if(isset($postArr['brchSTAT']) && $postArr['brchSTAT'] =='DEL') {

			$bankacc = new EmpBank();

			$arr[1]=$postArr['chkbrchdel'];
			for($c=0;count($arr[1])>$c;$c++)
				if($arr[1][$c]!=NULL)
					$arr[0][$c]=$getArr['id'];
			$bankacc->delEmpBank($arr);
		}

		if(isset($postArr['attSTAT']) && $postArr['attSTAT'] =='DEL') {

			$attachment = new EmpAttach();

			$arr[1]=$postArr['chkattdel'];
			for($c=0;count($arr[1])>$c;$c++)
				if($arr[1][$c]!=NULL)
					$arr[0][$c]=$getArr['id'];
			$attachment->delEmpAtt($arr);
		}
	}

	function reDirect($getArr,$postArr,$object = null) {

		$form_creator = new FormCreator($getArr,$postArr);

		switch ($getArr['reqcode']) {

			case 'JSP' :	$form_creator ->formPath = '/templates/hrfunct/hrempjobspec.php';
							 $ejobsp = new EmpJobSpec();
							 $empinfo = new EmpInfo();
							 $jdcat = new JDCategory();

							$form_creator->popArr['empdet'] = $empinfo->filterEmpMain($getArr['id']);
							$form_creator->popArr['catlist']= $jdcat->getJDCatCodes();

							if(isset($getArr['editID'])) {
							    $arr[0]=$getArr['id'];
							    $arr[1]=$getArr['editID'];

							    $form_creator->popArr['editArr']=$ejobsp->filterEmpJobSpec($arr);

							}
								else
							{
							     $form_creator ->popArr['unasscatlist']  =$jdcat->getUnAssJDCatCodes($getArr['id']);
							}
							     $form_creator ->popArr['rset'] = $ejobsp ->getAssEmpJobSpec($getArr['id']);
							     //$form_creator ->popArr['catlist']=$ejobsp->getJDCatCodes();


							break;

		   case 'CXT' :	  $form_creator ->formPath = '/templates/hrfunct/hrempconext.php';
							 $empconext = new EmpConExt();
							 $empinfo = new EmpInfo();


							$form_creator->popArr['empdet'] = $empinfo->filterEmpMain($getArr['id']);
							$form_creator->popArr['empstat'] = $empinfo->filterEmpJobStat($getArr['id']);

							if(isset($getArr['editID']))
								{
	   								$arr[0]=$getArr['id'];
    								$arr[1]=$getArr['editID'];

    							$form_creator->popArr['editArr'] =$empconext->filterConExt($arr);
    							$form_creator->popArr['lastrec']=$empconext->getLastRecord($getArr['id']);
								}
								else
								{
								$form_creator->popArr['lastrec']=$empconext->getLastRecord($getArr['id']);
								}
								$form_creator->popArr['rset']=$empconext ->getAssConExt($getArr['id']);

							break;

			case 'SAL' :
							$form_creator ->formPath = '/templates/hrfunct/hrempbassal.php';
							    	$empinfo = new EmpInfo();
							    	$empbassal = new EmpBasSalary();
							    	$currTyp = new CurrencyTypes();

							$form_creator->popArr['einfo'] = $empinfo->filterEmpMain($getArr['id']);
							$form_creator->popArr['empdet'] = $empdet =$empinfo->filterEmpJobInfo($getArr['id']);
							$salGrd = $empdet[0][5];

							if(isset($getArr['editID1'])) {
   								$arr[0]=$getArr['id'];
    							$arr[1]=$getArr['editID1'];
   								$arr[2]=$getArr['editID2'];

		   					    $form_creator->popArr['editArr'] = $edit = $empbassal->filterEmpBasSal($arr);
		   					    $form_creator->popArr['currlist'] = $empbassal-> getCurrCodes($edit[0][1]);
							} else {
								$form_creator->popArr['curlist'] = $empbassal->getUnAssCurrCodes($salGrd,$getArr['id']);

								if($object != null) {
									$form_creator->popArr['cmbCurrCode'] = $object->cmbCurrCode;
								}
							}

							$form_creator->popArr['rset'] = $empbassal->getAssEmpBasSal($getArr['id']);
						    $form_creator->popArr['currAlllist'] = $currTyp->getAllCurrencyCodes();

							break;

			case 'LAN' :	$form_creator ->formPath = '/templates/hrfunct/hremplan.php';
							$empinf = new EmpInfo();
							$emplan = new EmpLanguage();
							$laninfo = new LanguageInfo();

							$form_creator ->popArr['empDet'] = $empinf ->filterEmpMain($getArr['id']);
							$form_creator ->popArr['lanlist'] = $lanlist = $laninfo ->getLang();

							if(isset($getArr['editID1'])) {
								    $arr[0]=$getArr['id'];
    								$arr[1]=$getArr['editID1'];
    								$arr[2]=$getArr['editID2'];

								$form_creator -> popArr['editArr'] = $emplan ->filterEmpLang($arr);

								for($c=0;count($lanlist)>$c;$c++)
									if($getArr['editID1']==$lanlist[$c][0])
							  			$ratSel=$lanlist[$c][2];


							} else {
								if($object != null) {
									$form_creator->popArr['cmbLanCode'] = $object->cmbLanCode;
									for($c=0;$lanlist && count($lanlist)>$c;$c++)
									    if($lanlist[$c][0]==$object->cmbLanCode) {
										       $ratSel = $lanlist[$c][2];
									    }

								$form_creator -> popArr['ratSel']= $ratSel;

								}
								$form_creator ->popArr['lanlist']   = $laninfo ->getLang();

							}

							$form_creator ->popArr['rsets']    = $emplan ->getAssEmpLang($getArr['id']);
							break;

			case 'MEM' :	$form_creator ->formPath = '/templates/hrfunct/hrempmemship.php';
							$empinf = new EmpInfo();
							$empmem = new EmpMembership();
							$memtype = new MembershipType();
							$memshipinfo = new MembershipInfo();


							$form_creator ->popArr['empDet'] = $empinf ->filterEmpMain($getArr['id']);
							$form_creator ->popArr['typlist'] = $typlist = $memtype ->getMembershipTypeCodes();

							if(isset($getArr['editID1'])) {
								    $arr[0]=$getArr['id'];
    								$arr[1]=$getArr['editID1'];
    								$arr[2]=$getArr['editID2'];

								$form_creator -> popArr['editArr'] = $edit = $empmem ->filterEmpMembership($arr);

								$form_creator -> popArr['mship'] = $memshipinfo ->getMembershipCodes($edit[0][2]);
							} else {
								if($object != null) {
									$form_creator->popArr['cmbMemTypeCode'] = $object->cmbMemTypeCode;

								    $form_creator->popArr['mship'] = $memshipinfo ->getUnAssMembershipCodes($getArr['id'], $object->cmbMemTypeCode);

								}

								$form_creator ->popArr['typlist'] = $typlist = $memtype ->getMembershipTypeCodes();
							}

							$form_creator ->popArr['rset']    = $empmem->getAssEmpMembership($getArr['id']);
							$form_creator ->popArr['mshipAll']   = $memshipinfo ->getAllMembershipCodes();

							break;

			case 'EXC' :
							$form_creator ->formPath = '/templates/hrfunct/hrempextcur.php';

							$empinfo   = new EmpInfo();
							$empextcur = new EmpExCur();
							$extracurr = new ExtraCurrActCat();
							$extrainfo = new ExtraCurActInfo();

							$form_creator->popArr['empdet'] = $empinfo->filterEmpMain($getArr['id']);
							$form_creator-> popArr['catlist'] = $extracurr->getExCurCatCodes();

							if(isset($getArr['editID']))
							{
    							$arr[0]=$getArr['id'];
    							$arr[1]=$getArr['editID'];

    							$form_creator-> popArr['editArr'] = $edit = $empextcur->filterEmpExCur($arr);
    							$form_creator-> popArr['typelist']= $extrainfo->getExCurTypeCodes($edit[0][2]);
							} else {
								$form_creator-> popArr['lastrec'] = $empextcur->getLastRecord($getArr['id']);


								if($object != null){
									$form_creator->popArr['cmbExtCurCat'] = $catcode = $object->cmbExtCurCat;
									$form_creator-> popArr['typeunasslist'] = $extrainfo->getUnAssExCurTypeCodes($getArr['id'],$catcode);
								}

							}

							$form_creator-> popArr['rset']  =  $empextcur ->getAssExCur($getArr['id']);
							$form_creator-> popArr['allcatlist'] = $extracurr->getExCurCatCodes();
							$form_creator-> popArr['alltypelist'] = $extrainfo->getAllExtraCurActInfo();

							break;

			case 'EXP' :
							$form_creator ->formPath = '/templates/hrfunct/hrempwrkexp.php';

							$empinfo  = new EmpInfo();
							$empworkex= new EmpWorkExp();
							$countryinfo = new CountryInfo();
							$proinfo = new ProvinceInfo();

							$form_creator->popArr['empDet'] = $empinfo->filterEmpMain($getArr['id']);

							if($object != null) {
									$form_creator->popArr['txtEmpExpCompany'] =  $object->txtEmpExpCompany;
									$form_creator->popArr['txtEmpExpToDat'] =  $object->txtEmpExpToDat;
									$form_creator->popArr['txtEmpExpDesOnLev'] =  $object->txtEmpExpDesOnLev;
									$form_creator->popArr['txtEmpExpYears'] =  $object->txtEmpExpYears;
									$form_creator->popArr['chkEmpExpWorkRelFlag'] =  $object->chkEmpExpWorkRelFlag;
									$form_creator->popArr['txtEmpExpMonths'] =  $object->txtEmpExpMonths;
									$form_creator->popArr['txtEmpExpAdd1'] =  $object->txtEmpExpAdd1;
									$form_creator->popArr['txtEmpExpAcc'] =  $object->txtEmpExpAcc;
									$form_creator->popArr['txtEmpExpAdd2'] =  $object->txtEmpExpAdd2;
							}

							if(isset($getArr['editID']))
							{
    							$arr[0]=$getArr['id'];
    							$arr[1]=$getArr['editID'];

    							$form_creator-> popArr['editArr'] = $edit =  $empworkex->filterEmpWorkExp($arr);
    							$form_creator-> popArr['list']  = $countryinfo->getCountryCodes();
    							if($object != null) {
									$form_creator->popArr['txtEmpExpAdd2'] = $pli =  $object->txtEmpExpAdd2;
									$form_creator->popArr['txtEmpExpConPers'] =  $object->txtEmpExpConPers;
									$form_creator->popArr['txtEmpExpAdd3'] =  $object->txtEmpExpAdd3;

									$form_creator->popArr['txtEmpExpTelep'] =  $object->txtEmpExpTelep;
									$form_creator->popArr['txtEmpResLev'] =  $object->txtEmpResLev;
									$form_creator->popArr['txtEmpExpAchmnt'] =  $object->txtEmpExpAchmnt;
									$form_creator->popArr['txtEmpExpFromDat'] =  $object->txtEmpExpFromDat;
									$form_creator->popArr['txtEmpExpEmail'] =  $object->txtEmpExpEmail;
    								$form_creator-> popArr['plist']=  $empinfo->getProvinceCodes( $pli);

    							} else
    								$form_creator-> popArr['plist1']=  $proinfo->getProvinceCodes($edit[0][4]);


							} else {

								$form_creator->popArr['newID'] = $empworkex ->getLastRecord($getArr['id']);
								$form_creator-> popArr['list'] = $countryinfo->getCountryCodes();

								if($object != null) {
									$form_creator->popArr['txtEmpExpCompany']  = $object->txtEmpExpCompany;
									$form_creator->popArr['txtEmpExpToDat']  = $object->txtEmpExpToDat;
									$form_creator->popArr['txtEmpExpDesOnLev']  = $object->txtEmpExpDesOnLev;
									$form_creator->popArr['txtEmpExpYears']  = $object->txtEmpExpYears;
									$form_creator->popArr['chkEmpExpWorkRelFlag']  = $object->chkEmpExpWorkRelFlag;
									$form_creator->popArr['txtEmpExpMonths'] = $object->txtEmpExpMonths;
									$form_creator->popArr['txtEmpExpAdd1'] =  $object->txtEmpExpAdd1;
									$form_creator->popArr['txtEmpExpAcc']  = $object->txtEmpExpAcc;

									$form_creator->popArr['txtEmpExpTelep'] =  $object->txtEmpExpTelep;
									$form_creator->popArr['txtEmpResLev'] =  $object->txtEmpResLev;
									$form_creator->popArr['txtEmpExpAchmnt'] =  $object->txtEmpExpAchmnt;
									$form_creator->popArr['txtEmpExpFromDat'] =  $object->txtEmpExpFromDat;
									$form_creator->popArr['txtEmpExpEmail'] =  $object->txtEmpExpEmail;
									$form_creator->popArr['txtEmpExpAdd2'] = $plis =  $object->txtEmpExpAdd2;
									$form_creator->popArr['txtEmpExpAdd3'] =  $object->txtEmpExpAdd3;
									$form_creator->popArr['txtEmpExpConPers'] =  $object->txtEmpExpConPers;
									$form_creator-> popArr['plist2'] =  $empinfo->getProvinceCodes($plis);
								}
							}

							$form_creator-> popArr['rset']  =  $empworkex ->getAssEmpWorkExp($getArr['id']);

							break;


			case 'QUA' :
							$form_creator ->formPath = '/templates/hrfunct/hrempqual.php';

							$empinfo = new EmpInfo();
							$empqual = new EmpQualification();
							$qualtyp = new QualificationType();
							$qual = new Qualifications();

							$form_creator-> popArr['statlist'] = array('First Class','Second Class Upr.','Second Class Lwr.');

							$form_creator-> popArr['empdet'] = $empinfo->filterEmpMain($getArr['id']);
							$form_creator-> popArr['typlist'] = $qualtyp->getQualificationTypeCodes();

							if(isset($getArr['editID']))
								{
    							$arr[0]=$getArr['editID'];
    							$arr[1]=$getArr['id'];

    							$form_creator-> popArr['editArr']  = $empqual->filterEmpQual($arr);
    							$form_creator-> popArr['quallist'] = $qual->getAllQualifications();

							} else {

								if($object != null){
								$form_creator-> popArr['TypeCode'] 	  =   $object -> TypeCode;
								$form_creator-> popArr['mship']       =   $qual->getUnAssQualifications($getArr['id'],$object -> TypeCode);
									}
								}
								$form_creator-> popArr['rset']         =  $empqual->getAssEmpQual($getArr['id']);
								$form_creator-> popArr['allquallist']  =  $qual->getAllQualifications();

							break;

			case 'QAS' :
							$form_creator ->formPath = '/templates/hrfunct/hrempqualsub.php';

							$empinfo = new EmpInfo();
							$qual = new Qualifications();
							$empqualsub = new EmpQualSubject();
							$subinfo = new SubjectInfo();

							$form_creator-> popArr['empdet'] = $empinfo->filterEmpMain($getArr['id']);
							$form_creator-> popArr['qualDet'] = $qual->filterQualifications($getArr['QUA']);

							$form_creator->popArr['sublistAll'] = $subinfo->getSubjectCodes($getArr['QUA']);
							$form_creator->popArr['ratlist'] = $empqualsub->getRatGrds($getArr['QUA']);

							if(isset($getArr['editID'])) {
								$arr[0] = $getArr['id'];
								$arr[1] = $getArr['QUA'];
								$arr[2] = $getArr['editID'];

								$form_creator->popArr['editArr'] = $empqualsub ->filterEmpQualSub($arr);
							} else {
								$form_creator->popArr['sublistUnAss'] = $subinfo ->getUnAssSubjects($getArr['id'],$getArr['QUA']);
							}

							$arr[0] = $getArr['id'];
							$arr[1] = $getArr['QUA'];
							$form_creator->popArr['sublistAss'] = $empqualsub ->getAssEmpQualSub($arr);
							break;

			case 'CBN' :
							$form_creator ->formPath = '/templates/hrfunct/hrempcashben.php';

							$empinfo = new EmpInfo();
							$empcashben = new EmpCashBen();
							$cashben = new CashBen();

							$form_creator->popArr['empdet'] = $empinfo->filterEmpMain($getArr['id']);
							$form_creator->popArr['bfilter'] = array('Salary Point' => 1,'Designation' => 2,'Employee' => 3);
							$form_creator->popArr['benlist'] = $cashben->getBenCodes();

							if(isset($getArr['editID'])) {
								    $arr[0]=$getArr['id'];
								    $arr[1]=$getArr['editID'];

								    $form_creator->popArr['editArr'] = $empcashben->filterEmpCashBen($arr);
							}

							$form_creator->popArr['cashbenAss'] = $empcashben->getAssEmpCashBen($getArr['id']);
							$form_creator->popArr['cashbenUnAss'] = $empcashben->getUnAssEmpCashBen($getArr['id']);

							if(isset($getArr['OBEN']))
								$form_creator->popArr['cashbenOther'] = $cashben->getOthEmpCashBen($getArr['id']);
							break;

			case 'NBN' :
							$form_creator ->formPath = '/templates/hrfunct/hrempnoncashben.php';

							$empinfo = new EmpInfo();
							$empnoncashben = new EmpNonCashBen();
							$noncashben = new NonCashBen();

							$form_creator->popArr['empdet'] = $empinfo->filterEmpMain($getArr['id']);
							$form_creator->popArr['bfilter'] = array('Salary Point' => 1,'Designation' => 2,'Employee' => 3);
							$form_creator->popArr['benlist'] = $noncashben->getBenCodes();

							if(isset($getArr['editID'])) {
								$arr[0]=$getArr['id'];
								$arr[1]=$getArr['editID'];
								    $form_creator->popArr['editArr'] = $empnoncashben->filterEmpNonCashBen($arr);
							}

							$form_creator->popArr['cashbenAss'] = $empnoncashben->getAssEmpNonCashBen($getArr['id']);
							$form_creator->popArr['cashbenUnAss'] = $empnoncashben->getUnAssEmpNonCashBen($getArr['id']);

							if(isset($getArr['OBEN']))
								$form_creator->popArr['cashbenOther'] = $noncashben->getOthEmpNonCashBen($getArr['id']);

							break;

			case 'ESS' :
			case 'EMP' :	$form_creator->formPath = '/templates/hrfunct/hrEmpMain.php';     //hremp.php';

							$form_creator->popArr['arrMStat'] = array ('Unmarried','Married','Divorced','Others');

							$empinfo = new EmpInfo();
							$pport = new EmpPassPort();
							$ddebit = new EmpDirectDebit();
							$dep = new EmpDependents();
							$econ = new EmpEmergencyCon();
							$chi = new EmpChildren();
							$attachment = new EmpAttach();
							$countryinfo = new CountryInfo();
							$porinfo = new ProvinceInfo();
							$nationinfo = new NationalityInfo();
							$location = new models_eimadmin_Location();
							$distric = new DistrictInfo();
							$jobtit = new JobTitle();

							$depen = new EmpDependents();

							$ethnicrace = new EthnicRace();
							$eeojobcat  = new EEOJobCat();
							$emplan = new EmpLanguage();
							$laninfo = new LanguageInfo();
							$emplicen = new EmpLicenses();
							$licen = new Licenses();
							$empmem = new EmpMembership();
							$memtype = new MembershipType();
							$memshipinfo = new MembershipInfo();
							$emprepto = new EmpRepTo();
							$empskill = new EmpSkill();
							$skill = new Skills();
							$empworkex= new EmpWorkExp();
							$empeducation = new EmpEducation();
							$education = new Education();
							$empTax = new EmpTax();

								$form_creator ->popArr['nation'] = $nationinfo ->getNationCodes();
								$form_creator->popArr['loc'] = $location->getLocCodes();
								$form_creator->popArr['cntlist'] = $countryinfo->getCountryCodes();
								$form_creator->popArr['ethRace'] = $ethnicrace->getEthnicRaceCodes();
								$form_creator->popArr['eeojobcat'] = $eeojobcat->getEEOJobCatCodes();
								$form_creator->popArr['jobtit'] = $jobtit ->getJobTit();
								$form_creator->popArr['hier'] = isset($res) ? $res : false;

								$form_creator->popArr['cntlist'] = $countrylist = $countryinfo->getCountryCodes();
								$form_creator->popArr['ppcntlist'] = $countrylist;
								$form_creator->popArr['customFieldList'] = CustomFields::getCustomFieldList();
                                $form_creator->popArr['leavePeriodCount'] = $empinfo->getLeavePeriodsCount();
                                
							if($getArr['capturemode'] == 'addmode') {

                        //we introduce token for the form here
                        $tokenGenerator = CSRFTokenGenerator::getInstance();
                        $tokenGenerator->setKeyGenerationInput($_GET);
                        $token = $tokenGenerator->getCSRFToken(array_keys($_GET));
                        $form_creator->popArr['empToken']   = $token;

								$form_creator ->popArr['newID']     = $empinfo->getLastId();

								if($object != null) {

									$form_creator->popArr['ctlist'] = $corptit ->getCorpTitlesEmpInfo($object->cmbSalGrd);
									$form_creator->popArr['deslist'] = $des ->getDesEmpInfo($object->cmbCorpTit);
									$form_creator->popArr['plist'] = $porinfo->getProvinceCodes($object->cmbTaxCountry);
									$form_creator->popArr['resplist'] = $porinfo->getProvinceCodes($object->cmbPermCountry);
									$form_creator->popArr['resdlist'] = $distric->getDistrictCodes($object->cmbPermProvince);

									if(isset($postArr['cmbJobTitle']))
										$form_creator ->popArr['empstatlist'] = $view_controller->xajaxObjCall($postArr['cmbJobTitle'],'JOB','allEmpStat');

									$form_creator->popArr['ethRace'] = $ethnicrace->getEthnicRaceCodes($object->cmbEthnicRace);
								}

							} elseif($getArr['capturemode'] == 'updatemode') {

                        //we introduce token for the form here
                        $screenParam = array('id' => $_GET['id'], 'capturemode' => $_GET['capturemode'], 'reqcode' => $_GET['reqcode']);
                        $tokenGenerator = CSRFTokenGenerator::getInstance();
                        $tokenGenerator->setKeyGenerationInput($screenParam);
                        $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                        $form_creator->popArr['empToken']   = $token;

								$form_creator ->popArr['editTaxInfo'] = $empTax->getEmployeeTaxInfo($getArr['id']);
								$form_creator ->popArr['usStateList'] = $porinfo->getProvinceCodes('US');
								$form_creator->popArr['empDDAss'] = $ddebit->getEmployeeDirectDebit($getArr['id']);
								if(isset($getArr['DDSEQ'])) {
									$form_creator->popArr['editDDForm'] = $ddebit->getDirectDebit($getArr['id'], $getArr['DDSEQ']);
								}

								$form_creator ->popArr['editMainArr'] = $empinfo ->filterEmpMain($getArr['id']);
								$form_creator ->popArr['editPersArr'] = $empinfo->filterEmpPers($getArr['id']);
								$form_creator ->popArr['editJobInfoArr'] = $empJobInfo = $empinfo->filterEmpJobInfo($getArr['id']);
								$form_creator ->popArr['editCustomInfoArr'] = $empCustomInfo = $empinfo->filterEmpCustomInfo($getArr['id']);

								$view_controller = new ViewController();
								$form_creator ->popArr['empstatlist'] = $view_controller->xajaxObjCall($empJobInfo[0][2],'JOB','allEmpStat');

                                $empJobTitle = $empJobInfo[0][2];
								if (isset($postArr['cmbJobTitle'])) {
									$form_creator ->popArr['empstatlist'] = $view_controller->xajaxObjCall($postArr['cmbJobTitle'],'JOB','allEmpStat');
                                    $empJobTitle = $postArr['cmbJobTitle'];
								}

                                if (CommonFunctions::isValidId($empJobTitle, 'JOB')) {
                                    $empJobSpec = $view_controller->getJobSpecForJob($empJobTitle);
                                } else {
                                    $empJobSpec = null;
                                }
                                $form_creator ->popArr['jobSpec'] = $empJobSpec;

                                $jobTitleHistory = new JobTitleHistory();
                                $form_creator->popArr['jobTitleHistory'] = $jobTitleHistory->getHistory($getArr['id']);
                                $empDivisionHistory = new SubDivisionHistory();
                                $form_creator->popArr['subDivisionHistory'] = $empDivisionHistory->getHistory($getArr['id']);

                                $locationHistory = new LocationHistory();
                                $form_creator->popArr['locationHistory'] = $locationHistory->getHistory($getArr['id']);

                                $form_creator ->popArr['assignedlocationList'] = EmpLocation::getEmpLocations($getArr['id']);
                                $form_creator ->popArr['availablelocationList'] = EmpLocation::getUnassignedLocations($getArr['id']);

								$form_creator->popArr['editPermResArr'] = $edit = $editPermRes = $empinfo->filterEmpContact($getArr['id']);
								$form_creator->popArr['provlist'] = $porinfo ->getProvinceCodes($edit[0][4]);
								$form_creator->popArr['citylist'] = $distric ->getDistrictCodes($edit[0][5]);
								$form_creator->popArr['empPPAss'] = $pport ->getAssEmpPP($getArr['id']);
								$form_creator->popArr['empDepAss'] = $dep ->getAssEmpDep($getArr['id']);
								$form_creator->popArr['empECAss'] = $econ ->getAssEmpEC($getArr['id']);
								$form_creator->popArr['empChiAss'] = $chi ->getAssEmpChi($getArr['id']);
								$form_creator->popArr['empAttAss'] = $attachment -> getAssEmpAtt($getArr['id']);

								$form_creator->popArr['empDpAss'] = $depen ->getAssEmpDep($getArr['id']);

								$form_creator->popArr['newPPID'] = $pport->getLastRecord($getArr['id']);
								if(isset($getArr['PPSEQ'])) {
									$arr[0]=$getArr['id'];
									$arr[1]=$getArr['PPSEQ'];
									$form_creator->popArr['editPPForm'] = $pport->filterEmpPP($arr);
								}

								$form_creator->popArr['newECID'] = $econ->getLastRecord($getArr['id']);
								if(isset($getArr['ECSEQ'])) {
									$arr[0]=$getArr['id'];
									$arr[1]=$getArr['ECSEQ'];
									$form_creator->popArr['editECForm'] = $econ->filterEmpEC($arr);
								}

								$form_creator->popArr['newDepID'] = $dep->getLastRecord($getArr['id']);
								if(isset($getArr['depSEQ'])) {
									$arr[0]=$getArr['id'];
									$arr[1]=$getArr['depSEQ'];
									$form_creator->popArr['editDepForm'] = $dep->filterEmpDep($arr);
								}


								$form_creator->popArr['newCID'] = $chi->getLastRecord($getArr['id']);
								if(isset($getArr['CHSEQ'])) {
									$arr[0]=$getArr['id'];
									$arr[1]=$getArr['CHSEQ'];
									$form_creator->popArr['editChiForm'] = $chi->filterEmpChi($arr);
								}

								if(isset($getArr['ATTACH'])) {
									$arr[0]=$getArr['id'];
									$arr[1]=$getArr['ATTACH'];
									$form_creator->popArr['editAttForm'] = $attachment->filterEmpAtt($arr);
								}

							$form_creator->popArr['lantype'] = array ( 'Writing'=> 1 , 'Speaking'=>2 , 'Reading'=>3 );
							$form_creator->popArr['grdcodes'] = array( 'Poor'=> 1 ,'Basic'=>2 , 'Good'=>3 ,'Mother Tongue'=>4);

							$form_creator ->popArr['lanlist'] = $lanlist = $laninfo ->getLang();

							if(isset($getArr['lanSEQ'])) {

								$arr[0]=$getArr['id'];
    							$arr[1]=$getArr['lanSEQ'];
    							$arr[2]=$getArr['lanFLU'];

								$form_creator -> popArr['editLanArr'] = $emplan ->filterEmpLang($arr);
							}

							$form_creator ->popArr['rsetLang'] = $emplan ->getAssEmpLang($getArr['id']);


							$form_creator ->popArr['allLicenlist'] =  $licenlist = $licen ->getLicensesCodes();
							$form_creator ->popArr['unassLicenlist']   = $licen ->getUnAssLicensesCodes($getArr['id']);

							if(isset($getArr['LIC'])) {
								    $arr[0]=$getArr['id'];
    								$arr[1]=$getArr['LIC'];

								$form_creator -> popArr['editLicenseArr'] = $emplicen ->filterEmpLicenses($arr);

							}

							$form_creator ->popArr['rsetLicense'] = $emplicen ->getAssEmpLicenses($getArr['id']);

							$form_creator ->popArr['typlist'] = $typlist = $memtype ->getMembershipTypeCodes();

							if(isset($getArr['MEM'])) {
								    $arr[0]=$getArr['id'];
    								$arr[1]=$getArr['MEM'];
    								$arr[2]=$getArr['MTP'];

								$form_creator -> popArr['editMembershipArr'] = $edit = $empmem ->filterEmpMembership($arr);
								$form_creator -> popArr['mship'] = $memshipinfo ->getMembershipCodes($edit[0][2]);
							}

							$form_creator ->popArr['rsetMembership']    = $empmem->getAssEmpMembership($getArr['id']);
							$form_creator ->popArr['mshipAll']   = $memshipinfo ->getAllMembershipCodes();

					    	$empbassal = new EmpBasSalary();
					    	$currTyp = new CurrencyTypes();
					    	$jobtit = new JobTitle();
					    	$salgradelist = new SalaryGrades();

					    	$form_creator->popArr['salgradelist'] = $salgradelist->getSalGrades(true);
							$empdet = $empinfo->filterEmpJobInfo($getArr['id']);
							$jt = $jobtit->filterJobTitles($empdet[0][2]);

							$form_creator->popArr['payPeriodList'] = PayPeriod::getPayPeriodList();

							$form_creator->popArr['salGrd'] = $salGrd = $empbassal->getEmpSalGrade($getArr['id']);

							if(isset($getArr['PAY'])) {
   								$arr[0]=$getArr['id'];
    							$arr[1]=$getArr['PAY'];
   								$arr[2]=$getArr['CUR'];

		   					    $form_creator->popArr['editPaymentArr'] = $edit = $empbassal->filterEmpBasSal($arr);
		   					    $form_creator->popArr['salGrd'] = $salGrd = $edit[0][1];
		   					    $form_creator->popArr['currlist'] = $empbassal-> getCurrCodes($edit[0][1]);

							}

                            $form_creator->popArr['salGrdCurrList'] = $empbassal->getAllCurrCodesForSalaryGrade($salGrd);
							$form_creator->popArr['unAssCurrList'] = $empbassal->getUnAssCurrCodes($salGrd,$getArr['id']);
//assigned basic salary
							$form_creator->popArr['rsetPayment'] = $empbassal->getAssEmpBasSal($getArr['id']);
						    $form_creator->popArr['currAlllist'] = $currTyp->getAllCurrencyCodes();


						    $form_creator->popArr['suprset'] = $emprepto->getEmpSup($getArr['id']);
							$form_creator->popArr['empname'] = $empinfo ->getEmpInfo();
							$form_creator->popArr['subrset'] = $emprepto->getEmpSub($getArr['id']);

							if(isset($getArr['editIDSup'])){
								$arr[0]=$getArr['editIDSup'];
								$arr[1]=$getArr['id'];
								$arr[2]=$getArr['RepMethod'];

							    $form_creator->popArr['editIDSupInfo'] = $emprepto->filterEmpRepTo($arr);
							 }

							if(isset($getArr['editIDSub'])){
								$arr[0]=$getArr['id'];
								$arr[1]=$getArr['editIDSub'];
								$arr[2]=$getArr['RepMethod'];

							    $form_creator->popArr['editIDSubInfo'] = $emprepto->filterEmpRepTo($arr);
							}


							$form_creator ->popArr['allSkilllist'] =  $skill ->getSkillCodes();

							$form_creator ->popArr['uskilllist']   = $skill ->getUnAssSkillCodes($getArr['id']);

							if(isset($getArr['SKILL'])) {
								    $arr[0]=$getArr['id'];
    								$arr[1]=$getArr['SKILL'];

								$form_creator -> popArr['editSkillArr'] = $empskill ->filterEmpSkill($arr);
							}

							$form_creator ->popArr['rsetSkill']    = $empskill ->getAssEmpSkill($getArr['id']);

							$form_creator->popArr['newWrkExpID'] = $empworkex ->getLastRecord($getArr['id']);

							if(isset($getArr['WRKEXP'])) {

    							$arr[0]=$getArr['id'];
    							$arr[1]=$getArr['WRKEXP'];

    							$form_creator-> popArr['editWrkExpArr'] = $edit =  $empworkex->filterEmpWorkExp($arr);

							}


							$form_creator-> popArr['rsetWrkExp']  =  $empworkex ->getAssEmpWorkExp($getArr['id']);


							$form_creator->popArr['allEduCodes'] = $education->getListofEducation(0,'',0);

							$form_creator->popArr['unAssEduCodes'] = $empeducation->getUnAssEduCodes($getArr['id']);

							if (isset($getArr['EDU'])) {
								$arr[0] = $getArr['id'];
								$arr[1] = $getArr['EDU'];

								$form_creator->popArr['editEducationArr'] = $empeducation->filterEmpEducation($arr);
							}

							$form_creator->popArr['rsetEducation'] = $empeducation->getAssEmpEducation($getArr['id']);

								if($object != null) {

									$form_creator->popArr['ctlist'] = $corptit ->getCorpTitlesEmpInfo($object->cmbSalGrd);
									$form_creator->popArr['deslist'] = $des ->getDesEmpInfo($object->cmbCorpTit);
									$form_creator->popArr['resplist'] = $porinfo->getProvinceCodes($object->cmbPermCountry);
									$form_creator->popArr['resdlist'] = $distric->getDistrictCodes($object->cmbPermProvince);
									$form_creator->popArr['brchlist'] = $brch->getUnAssBranchCodes($getArr['id'],$object->cmbBank);

									$form_creator->popArr['ctlist'] = $empinfo ->getCorpTitles($object->cmbSalGrd);
									$form_creator->popArr['deslist'] = $empinfo ->getDes($object->cmbCorpTit);
									$form_creator->popArr['resplist'] = $empinfo->getProvinceCodes($object->cmbPermCountry);
									$form_creator->popArr['resdlist'] = $empinfo->getDistrictCodes($object->cmbPermProvince);
									$form_creator->popArr['brchlist'] = $brch->getUnAssBranchCodes($getArr['id'],$object->cmbBank);


								} else {
									$form_creator->popArr['resplist'] = $porinfo->getProvinceCodes($editPermRes[0][10]);
									$form_creator->popArr['resdlist'] = $distric->getDistrictCodes($editPermRes[0][11]);
								}

							$empconext = new EmpConExt();

							$form_creator->popArr['newConExtID'] = $empconext->getLastRecord($getArr['id']);

							if(isset($getArr['CONEXT'])) {
	   							$arr[0]=$getArr['id'];
    							$arr[1]=$getArr['CONEXT'];

    							$form_creator->popArr['editConExtArr'] = $empconext->filterConExt($arr);
							}

							$form_creator->popArr['rsetConExt'] = $empconext ->getAssConExt($getArr['id']);
							}

							$form_creator->popArr['currentPage'] = (isset($getArr['currentPage'])) ? $getArr['currentPage'] : 1;
							if (isset($getArr['reqcode']) && $getArr['reqcode'] === 'ESS') {
					    		$form_creator ->popArr['showBackButton'] = false;
					    	} else {
					    		$form_creator ->popArr['showBackButton'] = true;
					    	}

							break;

			case 'REP' :
							$form_creator ->formPath = '/templates/hrfunct/hremprepto.php';

								$empinfo  = new EmpInfo();
								$emprepto = new EmpRepTo();


							$form_creator->popArr['empdet']  = $empinfo->filterEmpMain($getArr['id']);

							$form_creator->popArr['suprset'] = $emprepto->getEmpSup($getArr['id']);
							$form_creator->popArr['empname'] = $empinfo ->getEmpInfo();
							$form_creator->popArr['subrset'] = $emprepto->getEmpSub($getArr['id']);

							if(isset($getArr['editIDSup'])){
								$arr[0]=$getArr['editIDSup'];
								$arr[1]=$getArr['id'];
								$arr[2]=$getArr['repmethod'];

								    $form_creator->popArr['editIDSupInfo'] = $emprepto->filterEmpRepTo($arr);
							 }

							if(isset($getArr['editIDSub'])){
								$arr[0]=$getArr['id'];
								$arr[1]=$getArr['editIDSub'];
								$arr[2]=$getArr['repmethod'];

								    $form_creator->popArr['editIDSubInfo'] = $emprepto->filterEmpRepTo($arr);
							}
							break;

			case 'SKI' :	$form_creator ->formPath = '/templates/hrfunct/hrempskill.php';
							$empinf = new EmpInfo();
							$empskill = new EmpSkill();
							$skill = new Skills();


							$form_creator ->popArr['empDet'] = $empinf ->filterEmpMain($getArr['id']);
							$form_creator ->popArr['allSkilllist'] =  $skill ->getSkillCodes();


							if(isset($getArr['editID1'])) {
								    $arr[0]=$getArr['id'];
    								$arr[1]=$getArr['editID1'];
    								$arr[2]=$getArr['editID2'];

								$form_creator -> popArr['editArr'] = $empskill ->filterEmpSkill($arr);

							} else {

								$form_creator ->popArr['uskilllist']   = $skill ->getUnAssSkillCodes($getArr['id']);
							}

							$form_creator ->popArr['rsets']    = $empskill ->getAssEmpSkill($getArr['id']);
							break;

			case 'LIC' :	$form_creator ->formPath = '/templates/hrfunct/hremplicenses.php';
							$empinf = new EmpInfo();
							$emplicen = new EmpLicenses();
							$licen = new Licenses();


							$form_creator ->popArr['empDet'] = $empinf ->filterEmpMain($getArr['id']);
							$form_creator ->popArr['allLicenlist'] =  $licenlist = $licen ->getLicensesCodes();

							$form_creator ->popArr['unassLicenlist']   = $licen ->getUnAssLicensesCodes($getArr['id']);

							if(isset($getArr['editID1'])) {
								    $arr[0]=$getArr['id'];
    								$arr[1]=$getArr['editID1'];
    								$arr[2]=$getArr['editID2'];

								$form_creator -> popArr['editArr'] = $emplicen ->filterEmpLicenses($arr);

							};

							$form_creator ->popArr['rsets']    = $emplicen ->getAssEmpLicenses($getArr['id']);
							break;

					}
			$form_creator->display();
	}

	function updateCountryData($object) {
		$empinfo = new EmpInfo();

		$empinfo = $object;
		$empinfo -> updateEmpTaxCountry();
	}

	function addData($postArr,$index,$object) {

			switch ($index) {

				case 'EMP'  :  $empinfo = new EmpInfo();
									$empinfo = $object['EmpInfo'];

                           //we add this lines to prevent application from CSRF attack
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($_GET);
                           $token = $tokenGenerator->getCSRFToken(array_keys($_GET));
                           if($token == $postArr["empToken"]) {
                              $res = $empinfo -> addEmpMain();
                          
                              /* Get padded empID since most classes expect the empId to
                               * be left padded with zeros.
                               */
                              $id = $empinfo->getPaddedEmpId();

                              if(isset($object['EmpPhoto']) && $res) {
                                 $empphoto = $object['EmpPhoto'];
                                           $empphoto -> setEmpId($empinfo->getEmpId());
                                 $empphoto -> addEmpPic();
                              }
                              $tokenGenerator->clearToken(array_keys($_GET));
                           } else {
                              $res = false;
                           }
							break;
			}

			if($res) {

				// For adding a new record to `hs_hr_hsp_summary` table.
				if (HspSummary::recordsExist(date('Y'))) {
					HspSummary::saveInitialSummaryForOneEmployee($empinfo->getEmpId());
				}

				$showMsg = "Addition%Successful!"; //If $message is 1 setting up the

				$reqcode = $index;
				header("Location: ./CentralController.php?reqcode=EMP&id=$id&capturemode=updatemode");
			} else {

				$errCode = mysql_errno();

				switch ($errCode) {
					case 1062 :
						$showMsg = "DUPLICATE_EMPCODE_FAILURE";
						break;

					default :
						$showMsg = "ADD_FAILURE";
						break;
				}

				$reqcode = $index;
				header("Location: ./CentralController.php?message=$showMsg&capturemode=addmode&reqcode=$reqcode"); //if Adding Data to DB not succeed set msg with showMsg
			}
	}

	function updateData($index,$id,$object) {

			switch ($index) {

				case 'EMP'  :		$empinfo = new EmpInfo();
                           //we add this lines to prevent application from CSRF attack
                           $screenParam = array('id' => $_GET['id'], 'capturemode' => $_GET['capturemode'], 'reqcode' => $_GET['reqcode']);
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));

                           if(($token == $_POST['empToken'])) {
                              if(isset($object['EmpMain'])) {
                                 $empinfo = $object['EmpMain'];
                                 $res = $empinfo -> updateEmpMain();
                                 $message = "";
                                 if (!$res) {
                                    $message = "UPDATE_FAILURE";
                                 }
                                 $id = $empinfo -> getEmpId();
                              }

                              if(isset($object['EmpPers'])) {
                                 $empinfo = $object['EmpPers'];
                                 $empinfo -> updateEmpPers();
                              }

                              if(isset($object['EmpJobInfo'])) {
                                 $empinfo = $object['EmpJobInfo'];
                                 /*
                                  * Check if employment status is not terminated and if so
                                  * empty the fields terminated date and terminated reason
                                  */
                                  if ($empinfo->getEmpStatus() != EmploymentStatus::EMPLOYMENT_STATUS_ID_TERMINATED) {
                                    $empinfo->setEmpTerminatedDate('null');
                                    $empinfo->setEmpTerminationReason('');
                                  }
                                 $empinfo -> updateEmpJobInfo();
                              }

                              if(isset($object['EmpCustomInfo'])) {
                                 $empinfo = $object['EmpCustomInfo'];
                                 $empinfo -> updateEmpCustomInfo();
                              }

                              /*if(isset($object['EmpJobStat'])) {
                                 $empinfo = $object['EmpJobStat'];
                                 $empinfo -> updateEmpJobStat();
                              }*/

                              if(isset($object['EmpTaxInfo'])) {
                                 $empinfo = $object['EmpTaxInfo'];
                                 $empinfo->updateEmpTax();
                              }

                              /*if(isset($object['EmpWrkStation'])) {
                                 $empinfo = $object['EmpWrkStation'];
                                 $empinfo -> updateEmpWrkStation();
                              }
                              */
                              if(isset($object['EmpPermRes'])) {
                                 $empinfo = $object['EmpPermRes'];
                                 $empinfo -> updateEmpContact();
                              }
                           }
									break;

				case 'ESS'  :		$empinfo = new EmpInfo();

									if(isset($object['EmpMain'])) {
										$empinfo = $object['EmpMain'];
										$res = $empinfo -> updateEmpMain();
										$message = "";
										if (!$res) {
											$message = "UPDATE_FAILURE";
										}
									}

									if(isset($object['EmpPers'])) {
										$empinfo = $object['EmpPers'];
										$empinfo -> updateEmpPers();
									}


									if(isset($object['EmpPermRes'])) {
										$empinfo = $object['EmpPermRes'];
										$empinfo -> updateEmpContact();
									}
									break;

			}

		if (!empty($message)) {
			$message="&message=$message";
		} else {
			$message="";
		}

		if($index=='ESS') {
			$empid=$_SESSION['empID'];
			header("Location: ./CentralController.php?reqcode=ESS&id=$empid&capturemode=updatemode$message");
		} else {
			$reqcode = $index;
			$pane = (isset($_POST['pane']))?$_POST['pane']:$_GET['pane'];
			header("Location: ./CentralController.php?reqcode=$reqcode&id=$id&capturemode=updatemode&pane=$pane$message");
		}
	}

	function assignData($index,$object,$action) {

			switch ($index) {

				case 'JSP'  :		$empjob = new EmpJobSpec();
									$empjob = $object;
									if($action == 'ADD')
										$empjob->addEmpJobSpec();
									elseif($action == 'EDIT')
										$empjob->updateEmpJobSpec();
									break;

				case 'CXT'  :		$empconex = new EmpConExt();
									$empconex = $object;
									if($action == 'ADD')
										$empconex->addConExt();
									elseif($action == 'EDIT')
										$empconex->updateConExt();
									break;

				case 'SAL'  :		$empbassal = new EmpBasSalary();
									$empbassal = $object;
									if($action == 'ADD')
										$empbassal->addEmpBasSal();
									elseif($action == 'EDIT')
										$empbassal->updateEmpBasSal();
									break;

				case 'LAN'  :		$emplan = new EmpLanguage();
									$emplan = $object;
									if($action == 'ADD')
										$emplan ->addEmpLang();
									elseif($action == 'EDIT')
										$emplan ->updateEmpLang();
									break;

				case 'MEM'  :		$empmem = new EmpMembership();
									$empmem = $object;
									if($action == 'ADD')
										$empmem ->addEmpMembership();
									elseif($action == 'EDIT')
										$empmem ->updateEmpMembership();
									break;

				case 'EXP'  :		$empworkex = new EmpWorkExp();
									$empworkex = $object;
									if($action == 'ADD')
										$empworkex ->addEmpWorkExp();
									elseif($action == 'EDIT') {
										$empworkex ->updateEmpWorkExp();
										$id = $empworkex->getEmpId();
										header("Location: ./CentralController.php?reqcode=EXP&id=$id");
									}
									break;

				case 'EXC'  :		$empextcur = new EmpExCur();
									$empextcur = $object;
									if($action == 'ADD')
										$empextcur->addEmpExCur();
									elseif($action == 'EDIT')
										$empextcur->updateEmpExCur();
									break;

				case 'QUA'  :		$empqual = new EmpQualification();
									$empqual = $object;
									if($action == 'ADD')
										$empqual->addEmpQual();
									elseif($action == 'EDIT')
										$empqual->updateEmpQual();
									break;

				case 'QAS'  :		$empqualsub = new EmpQualSubject();
									$empqualsub = $object;
									if($action == 'ADD')
										$empqualsub->addEmpQualSub();
									elseif($action == 'EDIT')
										$empqualsub->updateEmpQualSub();
									break;

				case 'CBN'	:		$empcashben = new EmpCashBen();

									if($action == 'ADD') {
										for($c=0;count($object)>$c;$c++){
											$empcashben = $object[$c];
											$empcashben -> addUnAssEmpCashBen();
										}
									} elseif ($action == 'ADDOTH'){
										for($c=0;count($object)>$c;$c++){
											$empcashben = $object[$c];
											$empcashben -> addEmpCashBen();
										}
									} elseif ($action == 'EDIT') {
											$empcashben = $object;
											$empcashben -> updateEmpCashBen();
									}

									break;

				case 'NBN'	:		$empnoncashben = new EmpNonCashBen();

									if($action == 'ADD') {
										for($c=0;count($object)>$c;$c++){
											$empnoncashben = $object[$c];
											$empnoncashben -> addUnAssEmpNonCashBen();
										}
									} elseif ($action == 'ADDOTH'){
										for($c=0;count($object)>$c;$c++){
											$empnoncashben = $object[$c];
											$empnoncashben -> addEmpNonCashBen();
										}
									} elseif ($action == 'EDIT') {
											$empnoncashben = $object;
											$empnoncashben -> updateEmpNonCashBen();
									}
									break;

			  	case 'REP'  :		$emprepto = new EmpRepTo();
									$emprepto = $object;
									if($action == 'ADD')
										$emprepto->addEmpRepTo();
									elseif($action == 'EDIT')
										$emprepto->updateEmpRepTo($_POST['txtSupEmpID'],$_POST['txtSubEmpID'],$_POST['oldRepMethod'],$_POST['cmbRepMethod']);
									break;

				case 'SKI'  :		$empskill = new EmpSkill();
									$empskill = $object;
									if($action == 'ADD')
										$empskill ->addEmpSkill();
									elseif($action == 'EDIT')
										$empskill ->updateEmpSkill();
									break;

				case 'LIC'  :		$empLicen = new EmpLicenses();
									$empLicen = $object;
									if($action == 'ADD')
										$empLicen ->addEmpLicenses();
									elseif($action == 'EDIT')
										$empLicen ->updateEmpLicenses();
									break;
			}
		}

		function delAssignData($index,$postArr,$getArr) {

			switch ($index) {

				case 'JSP'  :		$empjob = new EmpJobSpec();
								      $arr[1]=$postArr['chkdel'];
								         for($c=0;count($arr[1])>$c;$c++)
      										 if($arr[1][$c]!=NULL)
	      										$arr[0][$c]=$getArr['id'];

								     $empjob -> delEmpJobSpec($arr);
								      break;

			    case 'CXT'   : 		$empconex = new EmpConExt();
									$arr[1]=$postArr['chkdel'];
  										 for($c=0;count($arr[1])>$c;$c++)
       										if($arr[1][$c]!=NULL)
	      										$arr[0][$c]=$getArr['id'];


	      								$empconex->delConExt($arr);
										break;

				case 'SAL'   : 	    $empbassal = new EmpBasSalary();
									$arr=$postArr['chkdel'];

   										for($c=0;count($arr)>$c;$c++) {
   											$frg=explode("|",$arr[$c]);
											$arrpass[1][$c]=$frg[0];
											$arrpass[2][$c]=$frg[1];
   																		}

   										for($c=0;count($arr)>$c;$c++)
       										if($arr[$c]!=NULL)
	      										$arrpass[0][$c]=$getArr['id'];

   										$empbassal->delEmpBasSal($arrpass);
			   							break;

				case 'LAN'   : 	    $emplan = new EmpLanguage();
									$arr=$postArr['chkdel'];

   										for($c=0;count($arr)>$c;$c++) {
   		 					 	    		$frg=explode("|",$arr[$c]);
		 					 	    		$arrpass[1][$c]=$frg[0];
				 			 	    		$arrpass[2][$c]=$frg[1];
   	   						 			}

   										for($c=0;count($arr)>$c;$c++)
          					 				if($arr[$c]!=NULL)
	     					 				$arrpass[0][$c]=$getArr['id'];

   										$emplan->delEmpLang($arrpass);
			   							break;

				case 'MEM'   : 	    $empmem = new EmpMembership();
									$arr=$postArr['chkdel'];

   										for($c=0;count($arr)>$c;$c++) {
   		 					 	    		$frg=explode("|",$arr[$c]);
		 					 	    		$arrpass[1][$c]=$frg[0];
				 			 	    		$arrpass[2][$c]=$frg[1];
   	   						 			}

   										for($c=0;count($arr)>$c;$c++)
          					 				if($arr[$c]!=NULL)
	     					 				$arrpass[0][$c]=$getArr['id'];

   										$empmem->delEmpMembership($arrpass);
			   							break;

				case 'EXP'  :		$empworkex = new EmpWorkExp();
									 $arr[1]=$_POST['chkdel'];
  								 	for($c=0;count($arr[1])>$c;$c++)
      							 		if($arr[1][$c]!=NULL)
	     								 $arr[0][$c]=$getArr['id'];

  									 $empworkex->delEmpWorkExp($arr);
			   							break;

				case 'EXC'  :		$empextcur = new EmpExCur();

  									 $arr[1]=$postArr['chkdel'];

  									 for($c=0;count($arr[1])>$c;$c++)
       										if($arr[1][$c]!=NULL)
	     									 $arr[0][$c]=$getArr['id'];

   									$empextcur->delEmpExCur($arr);
   										break;

   				case 'QUA'	:    	$empqual = new EmpQualification();

   									$arr[1]=$postArr['chkdel'];

   									for($c=0;count($arr[1])>$c;$c++)
       										if($arr[1][$c]!=null)
          									 $arr[0][$c]=$getArr['id'];

   									$empqual->delEmpQual($arr);
   										break;

   				case 'QAS'	:    	$empqualsub = new EmpQualSubject();

   									$arr[2]=$postArr['chkdel'];

   									for($c=0;count($arr[2])>$c;$c++)
       										if($arr[2][$c]!=null) {
	          									 $arr[0][$c]=$getArr['id'];
	          									 $arr[1][$c]=$getArr['QUA'];
       										}
   									$empqualsub->delEmpQualSub($arr);
   										break;

   				case 'CBN'	:    	$empcashben = new EmpCashBen();
									$arrpass[1]=$postArr['chkdel'];

									for($c=0;count($arrpass[1])>$c;$c++)
									     if($arrpass[1][$c]!=NULL)
									       $arrpass[0][$c]=$getArr['id'];

									$empcashben->delEmpCashBen($arrpass);
   									break;

   				case 'NBN'	:    	$empnoncashben = new EmpNonCashBen();
									$arrpass[1]=$postArr['chkdel'];

									for($c=0;count($arrpass[1])>$c;$c++)
									     if($arrpass[1][$c]!=NULL)
									       $arrpass[0][$c]=$getArr['id'];

									$empnoncashben->delEmpNonCashBen($arrpass);
   									break;

   				case 'REP'	:    	$emprepto = new EmpRepTo();

   									if($postArr['delSupSub']=='sup'){
									$arr=$postArr['chksupdel'];

   										for($c=0;count($arr)>$c;$c++) {
   		 					 	    		$frg=explode("|",$arr[$c]);
		 					 	    		$arrpass[0][$c]=$frg[0];
				 			 	    		$arrpass[2][$c]=$frg[1];
   	   						 			}

   										for($c=0;count($arr)>$c;$c++)
          					 				if($arr[$c]!=NULL)
	     					 				$arrpass[1][$c]=$getArr['id'];

   									}
   									elseif ($postArr['delSupSub']=='sub')
   									{
   									$arr=$postArr['chksubdel'];

   										for($c=0;count($arr)>$c;$c++) {
   		 					 	    		$frg=explode("|",$arr[$c]);
		 					 	    		$arrpass[1][$c]=$frg[0];
				 			 	    		$arrpass[2][$c]=$frg[1];
   	   						 			}

   										for($c=0;count($arr)>$c;$c++)
          					 				if($arr[$c]!=NULL)
	     					 				$arrpass[0][$c]=$getArr['id'];
   									}
									$emprepto->delEmpRepTo($arrpass);

   									break;

   					case 'SKI'   :  $empskill = new EmpSkill();
									$arr=$postArr['chkdel'];

   										for($c=0;count($arr)>$c;$c++) {
   		 					 	    		$frg=explode("|",$arr[$c]);
		 					 	    		$arrpass[1][$c]=$frg[0];
				 			 	    		$arrpass[2][$c]=$frg[1];
   	   						 			}

   										for($c=0;count($arr)>$c;$c++)
          					 				if($arr[$c]!=NULL)
	     					 				$arrpass[0][$c]=$getArr['id'];

   										$empskill->delEmpSkill($arrpass);
			   							break;

			   		case 'LIC'   :  $empLicen = new EmpLicenses();
									$arr=$postArr['chkdel'];

   										for($c=0;count($arr)>$c;$c++) {
   		 					 	    		$frg=explode("|",$arr[$c]);
		 					 	    		$arrpass[1][$c]=$frg[0];
				 			 	    		$arrpass[2][$c]=$frg[1];
   	   						 			}

   										for($c=0;count($arr)>$c;$c++)
          					 				if($arr[$c]!=NULL)
	     					 				$arrpass[0][$c]=$getArr['id'];

   										$empLicen->delEmpLicenses($arrpass);
			   							break;
			}
  	   }


       /**
        * Assign given location to given employee
        *
        * @param int $empNumber Employee number
        * @param string $locationCode Location code to assign
        *
        * @return boolean true if successfully assigned, false otherwise
        */
       public function assignLocation($empNumber, $locationCode) {

            $result = false;
            $auth = new authorize($_SESSION['empID'], $_SESSION['isAdmin']);

            /* Only allow admins and supervisors of the given employee to assign locations */
            if ($auth->isAdmin() || ($auth->isSupervisor() && $auth->isTheSupervisor($empNumber))) {
                $empLocation = new EmpLocation($empNumber, $locationCode);

                try {
                    $empLocation->save();
                    $result = true;
                    $history = new LocationHistory();
                    $history->updateHistory($empNumber, $locationCode);
                } catch (EmpLocationException $e) {

                }
            }

            return $result;
       }

       /**
        * Remove given location from employee
        *
        * @param int $empNumber Employee number
        * @param string $locationCode Location code to remove
        *
        * @return boolean true if successfully assigned, false otherwise
        */
       public function removeLocation($empNumber, $locationCode) {

            $result = false;
            $auth = new authorize($_SESSION['empID'], $_SESSION['isAdmin']);

            /* Only allow admins and supervisors of the given employee to assign locations */
            if ($auth->isAdmin() || ($auth->isSupervisor() && $auth->isTheSupervisor($empNumber))) {
                $empLocation = new EmpLocation($empNumber, $locationCode);

                try {
                    $empLocation->delete();
                    $result = true;
                    $history = new LocationHistory();
                    $history->updateHistory($empNumber, $locationCode, true);
                } catch (EmpLocationException $e) {

                }
            }

            return $result;
       }

       public function cleanCustomFieldData($params) {
	       	$eventOccurance = array_shift($params);
	       	$customFieldIds = $params[0];

			if ($eventOccurance === 'custom_field_delete') {
				$empInfo = new EmpInfo();
				$empInfo->cleanCustomFieldData($customFieldIds);
			}
       }
}
?>
