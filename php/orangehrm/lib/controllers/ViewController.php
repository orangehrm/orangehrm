<?php
/*
* OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
* all the essential functionalities required for any enterprise.
* Copyright (C) 2006 hSenid Software International Pvt. Ltd, http://www.hsenid.com
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

require_once ROOT_PATH . '/lib/exception/ExceptionHandler.php';

require_once ROOT_PATH . '/lib/models/eimadmin/Location.php';
require_once ROOT_PATH . '/lib/models/eimadmin/SalaryGrades.php';
require_once ROOT_PATH . '/lib/models/eimadmin/CurrencyTypes.php';
require_once ROOT_PATH . '/lib/models/eimadmin/SalCurDet.php';
require_once ROOT_PATH . '/lib/models/eimadmin/CompStruct.php';
require_once ROOT_PATH . '/lib/models/eimadmin/Education.php';
require_once ROOT_PATH . '/lib/models/eimadmin/Skills.php';;
require_once ROOT_PATH . '/lib/models/eimadmin/MembershipType.php';
require_once ROOT_PATH . '/lib/models/eimadmin/NationalityInfo.php';
require_once ROOT_PATH . '/lib/models/eimadmin/LanguageInfo.php';
require_once ROOT_PATH . '/lib/models/eimadmin/MembershipInfo.php';
require_once ROOT_PATH . '/lib/models/eimadmin/EEOJobCat.php';
require_once ROOT_PATH . '/lib/models/eimadmin/Licenses.php';
require_once ROOT_PATH . '/lib/models/eimadmin/EthnicRace.php';
require_once ROOT_PATH . '/lib/models/eimadmin/CountryInfo.php';
require_once ROOT_PATH . '/lib/models/eimadmin/ProvinceInfo.php';
require_once ROOT_PATH . '/lib/models/eimadmin/DistrictInfo.php';
require_once ROOT_PATH . '/lib/models/eimadmin/JobTitle.php';
require_once ROOT_PATH . '/lib/models/eimadmin/JobTitEmpStat.php';
require_once ROOT_PATH . '/lib/models/eimadmin/EmployStat.php';
require_once ROOT_PATH . '/lib/models/eimadmin/GenInfo.php';
require_once ROOT_PATH . '/lib/models/eimadmin/EmailConfiguration.php';
require_once ROOT_PATH . '/lib/models/eimadmin/EmailNotificationConfiguration.php';

require_once ROOT_PATH . '/lib/common/FormCreator.php';

require_once ROOT_PATH . '/lib/models/maintenance/UserGroups.php';
require_once ROOT_PATH . '/lib/models/maintenance/Users.php';
require_once ROOT_PATH . '/lib/models/maintenance/Rights.php';

class ViewController {

	var $indexCode;
	var $message;
	var $pageID;
	var $headingInfo;


	function ViewController() {
		if(!isset($_SESSION)) {
			header("Location: ../../login.htm");
			exit();
		}
	}

	function xajaxObjCall($value,$uniqcode,$cntrl) {

		switch ($uniqcode) {

			case 'SCD' :
							if($cntrl == 'unAssCurrency') {

								$salcurdet = new SalCurDet();
								return $salcurdet->getUnAssSalCurDet($value);
							}

							if($cntrl == 'currencyEdit') {

								$currtype = new CurrencyTypes();
								return $currtype->filterCurrencyTypes($value);
							}

							break;

			case 'LOC' :
							if($cntrl == 'province') {

								$province = new ProvinceInfo();
								return $province->getProvinceCodes($value);
							}

							if($cntrl == 'district') {

								$district = new DistrictInfo();
								return $district->getDistrictCodes($value);
							}

							if($cntrl == 'addLocation') {

								$location = new Location();
								return $location->addLocation();
							}

							if($cntrl == 'getLocCodes') {

								$location = new Location();
								return $location->getLocCodes();
							}
							break;

			case 'DIS' :    if($cntrl == 'province') {

								$provinfo = new ProvinceInfo();
								return $provinfo->getProvinceCodes($value);
							}
							break;

			case 'CST' :
							if($cntrl == 'edit') {
								$compstruct = new CompStruct();
								return $compstruct->filterCompStruct($value);
							}

							if($cntrl == 'assigned') {

								$level = $value[0];
								$val = $value[1];

								$comphier = new CompHierachy();
								return $comphier->getAssCompHier($level,$val);
							}

							if($cntrl == 'unAssigned') {

								$level = $value[0];
								$val = $value[1];

								$compstruct = new CompStruct();
								return $compstruct->getUnAssCompStruct($val,$level);
							}

							break;

			case 'JOB' :
							if($cntrl == 'assigned') {

								$jobtit_empstat = new JobTitEmpStat();

								return $jobtit_empstat->getAssEmpStat($value);
							}

							if($cntrl == 'unAssigned') {

								$jobtit_empstat = new JobTitEmpStat();
								return $jobtit_empstat->getUnAssEmpStat($value);
							}

							if($cntrl == 'editEmpStat') {

								$empstat = new EmploymentStatus();
								return $empstat->filterEmpStat($value);
							}

							break;
		}
	}

	function viewList($getArr,$postArr) {

		if (!isset($getArr['sortField']))
			$getArr['sortField']=0;

		$sortOrderFld='sortOrder'.$getArr['sortField'];

		if (!isset($getArr[$sortOrderFld]))
			$getArr[$sortOrderFld]='ASC';

		$esp = (isset($getArr['isAdmin']) && ($getArr['isAdmin'] == 'Yes')) ? true : false;

		switch ($getArr['uniqcode']) {

			case 'CST' :
			case 'GEN' :
						$this->reDirect($getArr);
						break;
			case 'EMX' :
						$this->reDirect($getArr);
						break;
			case 'ENS' :
						$this->reDirect($getArr);
						break;
			default:
						$form_creator = new FormCreator($getArr,$postArr);
						$form_creator ->formPath ='/view.php';

						if ((isset($getArr['uniqcode'])) && ($getArr['uniqcode'] != '')) {
							$form_creator ->popArr['headinginfo'] = $this ->getHeadingInfo(trim($getArr['uniqcode']));
						}

						$form_creator ->popArr['currentPage'] = $currentPage =(isset($postArr['pageNO'])) ? (int)$postArr['pageNO'] : 1;

						if (isset($postArr['captureState'])&& ($postArr['captureState']=="SearchMode"))
					    {
							$choice=$postArr['loc_code'];
						    $strName=trim($postArr['loc_name']);
						    $form_creator ->popArr['message'] = $this ->  getInfo(trim($getArr['uniqcode']),$currentPage,$strName,$choice, $getArr['sortField'], $getArr[$sortOrderFld], $esp);
					    } else  {

							$form_creator ->popArr['message'] = $this ->  getInfo(trim($getArr['uniqcode']),$currentPage, '', -1, $getArr['sortField'], $getArr[$sortOrderFld],  $esp);
					    }
				   		if (isset($postArr['captureState'])&& ($postArr['captureState']=="SearchMode"))
							$form_creator ->popArr['temp'] = $this ->  countList(trim($getArr['uniqcode']), $strName, $choice, $getArr['sortField'], $getArr[$sortOrderFld]);
						else
							$form_creator ->popArr['temp'] = $this ->  countList(trim($getArr['uniqcode']), '', -1, $getArr['sortField'], $getArr[$sortOrderFld]);

						$form_creator->display();
						break;
		}
	}

    function delParser($indexCode,$arrList) {
        $this->indexCode=$indexCode;

        switch ($this->indexCode)  {

        	case 'EST' :

        		$this->empstat = new EmploymentStatus();
        		$res = $this->empstat->delEmpStat($arrList);
        		break;

        	case 'JEM' :

        		$this->jobtit_empstat = new JobTitEmpStat();
        		$res = $this->jobtit_empstat->delJobTitEmpStat($arrList);
        		break;

        	case 'JOB' :

        		$this->jobtit = new JobTitle();
        		$res = $this->jobtit ->delJobTitles($arrList);
        		break;

        	case 'CST' :

	            $this->compstruct = new CompStruct();
	            $res = $this->compstruct->delCompStruct($arrList);
	            break;

        	case 'LOC' :

	            $this->location = new Location();
	            $res = $this->location->delLocation($arrList);
	            break;

			case 'SKI' :

				$this-> skills = new Skills();
				$res = $this-> skills -> delSkills($arrList);
				break;

			case 'ETH' :

				$this-> ethnicrace = new EthnicRace();
				$res = $this-> ethnicrace -> delEthnicRace($arrList);
				break;

			case 'MEM' :

				$this-> membershiptype = new MembershipType();
				$res = $this-> membershiptype -> delMembershipType($arrList);
				break;

		case 'NAT' :

			$this-> nationalityinfo = new NationalityInfo();
			$res = $this-> nationalityinfo -> delNationalityInfo($arrList);
			break;

		case 'LAN':

			$this-> languageinfo = new LanguageInfo();
			$res = $this-> languageinfo -> delLanguageInfo($arrList);
			break;

		case 'MME':

			$this-> membershipinformation = new MembershipInfo();
			$res = $this-> membershipinformation -> delMembershipInfo($arrList);
			break;

		case 'SGR':

			$this-> salarygrade = new SalaryGrades();
			$res = $this-> salarygrade -> delSalaryGrades($arrList);
			break;

    	case 'EDU':

			$this-> qual = new Education();
			$res = $this-> qual -> delEducation($arrList);
			break;

		case 'EEC':

			$this-> EEOJobCat = new EEOJobCat();
			$res = $this-> EEOJobCat -> delEEOJobCat($arrList);
			break;

        case 'LIC':

			$this-> Licenses = new Licenses();
			$res = $this-> Licenses -> delLicenses($arrList);
			break;

		case 'USR':

			$this-> users = new Users();
			$res = $this-> users -> delUsers($arrList);
			break;

		case 'USG':

			$this-> usergroups = new UserGroups();
			$res = $this-> usergroups -> delUserGroups($arrList);
			break;

        }

		if ($res) {
			$_GET['message']='DELETE_SUCCESS';
		} else {
			$_GET['message']='DELETE_FAILURE';
		}
    }

	function selectIndexId($pageNO,$schStr,$mode, $sortField = 0, $sortOrder = 'ASC', $esp = false) {

		switch ($this->indexCode) {

		case 'EST' :

			$this->empstat = new EmploymentStatus();
			$message = $this->empstat->getListofEmpStat($pageNO,$schStr,$mode, $sortField, $sortOrder);
			return $message;

		case 'JOB' :

			$this->jobtit = new JobTitle();
			$message = $this->jobtit->getListofJobTitles($pageNO,$schStr,$mode, $sortField, $sortOrder);
			return $message;

		case 'LOC' :

			$this-> location = new Location();
			$message = $this-> location -> getListofLocations($pageNO,$schStr,$mode, $sortField, $sortOrder);
			return $message;

		case 'SKI' :

			$this-> skills = new Skills();
			$message = $this-> skills -> getListofSkills($pageNO,$schStr,$mode, $sortField, $sortOrder);
			return $message;

		case 'ETH' :

			$this-> ethnicrace = new EthnicRace();
			$message = $this-> ethnicrace -> getListofEthnicRace($pageNO,$schStr,$mode, $sortField, $sortOrder);
			return $message;

		case 'MEM' :

			$this-> membershiptype = new MembershipType();
			$message = $this-> membershiptype -> getListofMembershipType($pageNO,$schStr,$mode, $sortField, $sortOrder);
			return $message;

		case 'NAT' :

			$this-> nationalityinfo = new NationalityInfo();
			$message = $this-> nationalityinfo -> getListofNationalityInfo($pageNO,$schStr,$mode, $sortField, $sortOrder);
			return $message;

		case 'LAN' :

			$this-> languageinfo = new LanguageInfo();
			$message = $this-> languageinfo -> getListofLanguageInfo($pageNO,$schStr,$mode, $sortField, $sortOrder);

			return $message;

		case 'MME' :

			$this-> membershipinformation = new MembershipInfo();
			$message = $this-> membershipinformation -> getListofMembershipInfo($pageNO,$schStr,$mode, $sortField, $sortOrder);

			return $message;

		case 'SGR' :

			$this-> salarygrade = new SalaryGrades();
			$message = $this-> salarygrade -> getListofSalaryGrades($pageNO,$schStr,$mode, $sortField, $sortOrder);

			return $message;

    	case 'EDU' :

			$this-> qual = new Education();
			$message = $this-> qual -> getListofEducation($pageNO,$schStr,$mode, $sortField, $sortOrder);

			return $message;

    	case 'BBS' :

			$this-> salgrd = new SalaryGrades();
			$message = $this-> salgrd -> getListofCashBenefits($pageNO,$schStr,$mode);

			return $message;

    	case 'NBS' :

			$this-> salgrd = new SalaryGrades();
			$message = $this-> salgrd -> getListofNonCashBenefits($pageNO,$schStr,$mode);

			return $message;

		case 'EEC' :

			$this-> eeojobcat = new EEOJobCat();
			$message = $this-> eeojobcat -> getListofEEOJobCat($pageNO,$schStr,$mode, $sortField, $sortOrder);

			return $message;

		case 'LIC' :

			$this-> licenses = new Licenses();
			$message = $this-> licenses -> getListofLicenses($pageNO,$schStr,$mode, $sortField, $sortOrder);

			return $message;

		case 'USG' :

			$this-> userGroups = new UserGroups();
			$message = $this->userGroups-> getListOfUserGroups($pageNO,$schStr,$mode, $sortField, $sortOrder);

			return $message;

		case 'USR' :

			$this-> user = new Users();
			$message = $this->user-> getListOfUsers($pageNO,$schStr,$mode, $sortField, $sortOrder, $esp);

			return $message;
		}
	}

	function getHeadingInfo($indexCode) {

		$this->indexCode = $indexCode;

		switch ($this->indexCode) {

		case 'LOC' :

			$this->headingInfo = array ('Location ID','Location Name',1,'Locations','Deletion might affect Company Hierarchy');
			return $this->headingInfo;

		case 'COS' :

			$this->headingInfo = array ('Cost CenterID','Cost Center Name',1,'Cost Centers','Deletion might affect Employee Information');
			return $this->headingInfo;

		case 'CUR' :

			$this->headingInfo = array ('Currency Type ID','Currency Name',1,'Currency Types','Deletion might affect Salary Currency Detail');
			return $this->headingInfo;

		case 'CHI' :

			$this->headingInfo = array ('Company Hierarchy ID','Company Hierarchy Name',1,'Company Hierarchy','Deletion might affect Employee Information');
			return $this->headingInfo;

		case 'JDC' :

			$this->headingInfo = array ('JDCatergory ID','JDCatergory Name',1,'JD Catergory','Deletion might affect JD Type,Designation Description, Job Specifiction');
			return $this->headingInfo;

		case 'JDT' :

			$this->headingInfo = array ('JDType ID','JDType Name',1, 'JD Type','Deletion might affect JDCategory, Designation Description');
			return $this->headingInfo;

		case 'QLF' :

			$this->headingInfo = array ('Qualification Type ID','Qualification Name',1,'Qualification Type','Deletion might affect Qualifications, Designation Qualification, Employee Qualification');
			return $this->headingInfo;

		case 'RTM' :

			$this->headingInfo = array ('Rating Method ID','Rating Method Name',1,'Rating Method','Deletion might affect Qualification, Languages');
			return $this->headingInfo;

		case 'CTT' :

			$this->headingInfo = array ('Corporate TitleID','Corporate Title Name',1,'Corporate Title','Deletion might affect Employee Information, Designations');
			return $this->headingInfo;

		case 'SKI' :

			$this->headingInfo = array ('Skill ID','Skill Name',1,'Skills','Deletion might affect JDKRA');
			return $this->headingInfo;

		case 'ETH' :

			$this->headingInfo = array ('Ethnic Race ID','Ethnic Race Name',1,'Ethnic Races','Deletion might affect Employee');
			return $this->headingInfo;

		case 'EXC' :

			$this->headingInfo = array ('Extra Curricular Activity Category ID','Extra Curricular Activity Category Name',1,'Extra Curricular Activity Category','Deletion might affect Ex. Curr. Activities, Employee Ex. Curr. Activities');
			return $this->headingInfo;

		case 'MEM' :

			$this->headingInfo = array ('Membership Type ID','Membership Type Name',1,'Membership Type','Deletion might affect Employee Memberships, Memberships');
			return $this->headingInfo;

		case 'UNI' :

			$this->headingInfo = array ('Uniform Type ID','Unifrorm Type Name',1,'Uniform Type','Deletion might affect Employee Information');
			return $this->headingInfo;

		case 'SAT' :

			$this->headingInfo = array ('Statutory ID','Statutory Name',1,'Statutory Status','Deletion might affect Employee Information');
			return $this->headingInfo;

		case 'EMC' :

			$this->headingInfo = array ('Employee Category ID','Employee Category Name',1,'Employee Category','Deletion might affect Employee Information');
			return $this->headingInfo;

		case 'EMG' :

			$this->headingInfo = array ('Employee Group ID','Employee Group Name',1,'Employee Group','Deletion might affect Employee Information');
			return $this->headingInfo;

		case 'RTE' :

			$this->headingInfo = array ('Route ID','Route Name',1,'Routes','Deletion might affect Employee Information');
			return $this->headingInfo;

		case 'DWT' :

			$this->headingInfo = array ('Dwelling TypeID','Dwelling Type Name',1,'Dwelling Types','Deletion might affect Employee Information');
			return $this->headingInfo;

		case 'NAT' :

			$this->headingInfo = array ('Nationality ID','Nationality Name',1,'Nationality','Deletion might affect Employee Information');
			return $this->headingInfo;

		case 'RLG' :

			$this->headingInfo = array ('Religion ID','Religion Name',1,'Religions','Deletion might affect Employee Information');
			return $this->headingInfo;

		case 'COU' :

			$this->headingInfo = array ('Country ID','Country Name',1,'Country','Deletion might affect Employee Information');
			return $this->headingInfo;

		case 'DEF' :

			$this->headingInfo = array ('Hierarchy Definitiion ID','Hierarchy Definition Name',1,'Hierarchy Definition','Deletion might affect Company Hierachy!');
			return $this->headingInfo;

		case 'TAX' :

			$this->headingInfo = array ('Tax Info ID','Tax Name',1,'Tax','Deletion might affect Employee Information');
			return $this->headingInfo;

		case 'PRO' :

			$this->headingInfo = array ('State ID','State Name',1, 'State','Deletion might affect Employee Information');
			return $this->headingInfo;

		case 'DIS' :

			$this->headingInfo = array ('County ID','County Name',1,'County','Deletion might affect Employee Information');
			return $this->headingInfo;

		case 'ELE' :

			$this->headingInfo = array ('Electorate ID','Electorate Name',1,'Electorate','Deletion might affect Employee Information');
			return $this->headingInfo;

		case 'BNK' :

			$this->headingInfo = array ('Bank ID','Bank Name',1,'Banks','Deletion might affect Employee Banks, Branches');
			return $this->headingInfo;

		/*case 'LAN' :

			$this->headingInfo = array ('Language ID','Language Name',1,'Languages','Deletion might affect Employee Language');
			return $this->headingInfo;*/

		case 'MME' :

			$this->headingInfo = array ('Membership ID','Membership Name',1,'Membership','Deletion might affect Employee Membership');
			return $this->headingInfo;

		case 'SSK' :

			$this->headingInfo = array ('Sub Skill ID','Sub Skill Name',1,'Sub Skill','');
			return $this->headingInfo;

		case 'EXA' :

			$this->headingInfo = array ('Extra Curricular Activities ID','Extra Curricular Activities Name',1,'Extra Curricular Activities','Deletion might affect Employee Ex. Curr. Activities');
			return $this->headingInfo;

		case 'SGR' :

			$this->headingInfo = array ('Salary Grade ID','Salary Grade Name',1,'Salary Grades','Deletion might affect Employee Information, Corporate Titles');
			return $this->headingInfo;

		case 'DSG' :

			$this->headingInfo = array ('Designation ID','Designation Name',1,'Designations','Deletion might affect Employee Information, Corporate Titles');
			return $this->headingInfo;

		case 'DDI' :

			$this->headingInfo = array ('Designation ID','Designation Name',2,'Designation Description','');
			return $this->headingInfo;

		case 'DQA' :

			$this->headingInfo = array ('Designation ID','Designation Name',2,'Designation Qualification','');
			return $this->headingInfo;

		case 'JDK' :

			$this->headingInfo = array ('JDKRA ID','JDKRA Description',1,'JD Key Result Area','Deletion might affect Designation Description');
			return $this->headingInfo;

		case 'EDU' :

			$this->headingInfo = array ('Education ID','Education',1,'Education','Deletion might affect Education');
			return $this->headingInfo;

		case 'BCH' :

			$this->headingInfo = array ('Branch ID','Branch Name',1,'Branches','Deletion might affect Employee Bank');
			return $this->headingInfo;

		case 'CCB' :

			$this->headingInfo = array ('Cash Benefit ID','Cash Benefit Name',1,'Cash Benefits','Deletion might affect Employee Benefits, Benefits Assigned to Salary Grades');
			return $this->headingInfo;

		case 'NCB' :

			$this->headingInfo = array ('Non Cash Benefit ID','Non Cash Benefit Name',1,'Non Cash Benefits','Deletion might affect Employee Benefits, Benefits Assigned to Salary Grades');
			return $this->headingInfo;

		case 'BBS' :

			$this->headingInfo = array ('Salary Grade ID','Salary Grade Name',2,'Cash Benefits Assigned to Salary Grade','');
			return $this->headingInfo;

		case 'NBS' :

			$this->headingInfo = array ('Salary Grade Non Cash BenefitID','Benefit Name',2,'Non Cash Benefits Assigned to Salary Grade','');
			return $this->headingInfo;

		case 'ETY' :

			$this->headingInfo = array ('Employee TypeID','Employee Type Name',1,'Employee Types','Deletion might affect Employee Information');
			return $this->headingInfo;

		case 'SBJ' :

			$this->headingInfo = array ('Subject ID','Subject Name',1,'Subjects','Deletion might affect Employee Qualification, Designation Qualification');
			return $this->headingInfo;

		case 'EEC' :

			$this->headingInfo = array ('EEO Job Category ID','EEO Job Category Name',1,'EEO Job Category','Deletion might affect Employee Information');
			return $this->headingInfo;

		case 'LIC' :

			$this->headingInfo = array ('Licenses ID','Licenses Description',1,'Licenses','Deletion might affect Employee Information');
			return $this->headingInfo;
        }
	}

	function getInfo($indexCode,$pageNO,$schStr='',$schField=-1, $sortField=0, $sortOrder='ASC', $esp = false) {

		$this->indexCode = $indexCode;

		return $this->selectIndexId($pageNO,$schStr,$schField, $sortField, $sortOrder, $esp);
	}


	function countList($index, $schStr='',$mode=-1, $esp=false) {

	   $this->indexCode=$index;

	   switch ($this->indexCode) {

		case 'EST' :

			$this->empstat = new EmploymentStatus();
			$message = $this->empstat->countEmpStat($schStr,$mode);
			return $message;

		case 'JOB' :

			$this->jobtit = new JobTitle();
			$message = $this->jobtit->countJobTitles($schStr,$mode);
			return $message;

		case 'LOC' :

			$this-> location = new Location();
			$message = $this-> location -> countLocations($schStr,$mode);
			return $message;

		case 'COS' :

			$this-> costcenter = new CostCenter();
			$message = $this-> costcenter -> countCostCenters($schStr,$mode);
			return $message;

		case 'CUR' :

			$this-> currencytype = new CurrencyTypes();
			$message = $this-> currencytype -> countCurrencyTypes($schStr,$mode);
			return $message;

		case 'CHI' :

			$this-> comphier = new CompHierachy();
			$message = $this-> comphier -> countCompHierachy($schStr,$mode);
			return $message;

		case 'CTT' :

			$this-> corptit = new CorpTitle();
			$message = $this-> corptit -> countCorpTitles($schStr,$mode);
			return $message;

		case 'JDC' :

			$this-> jdcategory = new JDCategory();
			$message = $this-> jdcategory -> countJDCategorys($schStr,$mode);
			return $message;

		case 'JDT' :

			$this-> jdtype = new JDType();
			$message = $this-> jdtype -> countJDTypes($schStr,$mode);
			return $message;


		case 'QLF' :

			$this-> qualtype = new QualificationType();
			$message = $this-> qualtype -> countQualificationTypes($schStr,$mode);
			return $message;

		case 'RTM' :

			$this-> ratingmethods = new RatingTypes();
			$message = $this-> ratingmethods -> countRatingTypes($schStr,$mode);
			return $message;

		case 'SKI' :

			$this-> skills = new Skills();
			$message = $this-> skills -> countSkills($schStr,$mode);
			return $message;

		case 'ETH' :

			$this-> ethnicrace = new EthnicRace();
			$message = $this-> ethnicrace -> countEthnicRace($schStr,$mode);
			return $message;

		case 'EXC' :

			$this-> extracurract = new ExtraCurrActCat();
			$message = $this-> extracurract -> countExtraCurrActCat($schStr,$mode);
			return $message;

		case 'MEM' :

			$this-> membershiptype = new MembershipType();
			$message = $this-> membershiptype -> countMembershipType($schStr,$mode);
			return $message;

		case 'UNI' :

			$this-> uniformtypes = new UniformType();
			$message = $this-> uniformtypes -> countUniformType($schStr,$mode);
			return $message;

		case 'SAT' :

			$this-> satutoryinfo = new SatutoryInfo();
			$message = $this-> satutoryinfo -> countSatutoryInfo($schStr,$mode);
			return $message;

		case 'EMC' :

			$this-> employeecat = new EmployeeCat();
			$message = $this-> employeecat -> countEmployeeCat($schStr,$mode);
			return $message;

		case 'EMG' :

			$this-> employeegroup = new EmployeeGroup();
			$message = $this-> employeegroup -> countEmployeeGroup($schStr,$mode);
			return $message;

		case 'RTE' :

			$this-> routeinformation = new RouteInfo();
			$message = $this-> routeinformation -> countRouteInfo($schStr,$mode);
			return $message;

		case 'DWT' :

			$this-> routeinformation = new DwellingType();
			$message = $this-> routeinformation -> countDwellingType($schStr,$mode);
			return $message;

		case 'NAT' :

			$this-> nationalityinfo = new NationalityInfo();
			$message = $this-> nationalityinfo -> countNationalityInfo($schStr,$mode);
			return $message;

		case 'RLG' :

			$this-> religioninfo = new ReligionInfo();
			$message = $this-> religioninfo -> countReligionInfo($schStr,$mode);
			return $message;

		case 'COU' :

			$this-> countryinfo = new CountryInfo();
			$message = $this-> countryinfo -> countCountryInfo($schStr,$mode);
			return $message;

		case 'DEF' :

			$this-> hierachinfo = new HierarchyDefInfo();
			$message = $this-> hierachinfo -> countHierarchyDefInfo($schStr,$mode);
			return $message;

		case 'TAX' :

			$this-> taxinfo = new TaxInfo();
			$message = $this-> taxinfo -> countTaxInfo($schStr,$mode);
			return $message;

		case 'PRO' :

			$this-> provinceinfo = new ProvinceInfo();
			$message = $this-> provinceinfo -> countProvinceInfo($schStr,$mode);

			return $message;

		case 'DIS' :

			$this-> districtinfo = new DistrictInfo();
			$message = $this-> districtinfo -> countDistrictInfo($schStr,$mode);

			return $message;

		case 'ELE' :

			$this-> electorateinfo = new ElectorateInfo();
			$message = $this-> electorateinfo -> countElectorateInfo($schStr,$mode);

			return $message;

		case 'BNK' :

			$this-> bankinfo = new BankInfo();
			$message = $this-> bankinfo -> countBankInfo($schStr,$mode);

			return $message;

		case 'LAN' :

			$this-> languageinfo = new LanguageInfo();
			$message = $this-> languageinfo -> countLanguageInfo($schStr,$mode);

			return $message;

		case 'MME' :

			$this-> membershipinformation = new MembershipInfo();
			$message = $this-> membershipinformation -> countMembershipInfo($schStr,$mode);

			return $message;

		case 'SSK' :

			$this-> subskillinformation = new SubSkillInfo();
			$message = $this-> subskillinformation -> countSubSkillInfo($schStr,$mode);

			return $message;

		case 'EXA' :

			$this-> extracurractinfo = new ExtraCurActInfo();
			$message = $this-> extracurractinfo -> countExtraCurActInfo($schStr,$mode);

			return $message;

		case 'SGR' :

			$this-> salarygrade = new SalaryGrades();
			$message = $this-> salarygrade -> countSalaryGrades($schStr,$mode);

			return $message;

		case 'DSG' :

			$this-> designation = new Designations();
			$message = $this-> designation -> countDesignations($schStr,$mode);

			return $message;

		case 'DDI' :

			$this-> designation = new Designations();
			$message = $this-> designation -> countDesignations($schStr,$mode);

			return $message;

		case 'DQA' :

			$this-> designation = new Designations();
			$message = $this-> designation -> countDesignationsDes($schStr,$mode);

			return $message;

    	case 'JDK' :

			$this-> jdkra = new JDKra();
			$message = $this-> jdkra -> countJDKra($schStr,$mode);

			return $message;

    	case 'EDU' :

			$this-> edu = new Education();
			$message = $this-> edu -> countEducation($schStr,$mode);

			return $message;

    	case 'BCH' :

			$this-> brch = new Branches();
			$message = $this-> brch -> countBranches($schStr,$mode);

			return $message;

    	case 'CCB' :

			$this-> cashben = new CashBen();
			$message = $this-> cashben -> countCashBenefits($schStr,$mode);

			return $message;

    	case 'NCB' :

			$this-> noncashben = new NonCashBen();
			$message = $this-> noncashben -> countNonCashBenefits($schStr,$mode);

			return $message;

    	case 'BBS' :

			$this-> cashben = new CashBen();
			$message = $this-> cashben -> countCashBenefits($schStr,$mode);

			return $message;

    	case 'NBS' :

			$this-> noncashben = new NonCashBen();
			$message = $this-> noncashben -> countNonCashBenefits($schStr,$mode);

			return $message;

    	case 'ETY' :

			$this-> emptyp = new EmployeeType();
			$message = $this-> emptyp -> countEmployeeTypes($schStr,$mode);

			return $message;

		case 'SBJ' :

			$this-> subjectinfo = new SubjectInfo();
			$message = $this-> subjectinfo -> countSubjectInfo($schStr,$mode);

			return $message;

		case 'EEC' :

			$this-> eeojobcat = new EEOJobCat();
			$message = $this-> eeojobcat -> countEEOJobCat($schStr,$mode);

			return $message;

		case 'LIC' :

			$this-> licenses = new Licenses();
			$message = $this-> licenses -> countLicenses($schStr,$mode);

			return $message;

		case 'USG' :

			$this-> usergroups = new UserGroups();
			$message = $this-> usergroups -> countUserGroups($schStr,$mode);

			return $message;

		case 'USR' :

			$this-> users = new Users();
			$message = $this-> users -> countUsers($schStr,$mode, $esp);

			return $message;
		}
	}


	function addData($index,$object,$noRedirect = false) {

			switch ($index) {

				case 'EST'  :		$empstat = new EmploymentStatus();
									$empstat = $object;
									$res = $empstat ->addEmpStat();
									break;

				case 'JEM'  :		$jobtit_empstat = new JobTitEmpStat();
									$jobtit_empstat = $object;
									$res = $jobtit_empstat ->addJobTitEmpStat();
									break;

				case 'JOB'  :		$jobtit = new JobTitle();
									$jobtit = $object;
									$id = $jobtit->getJobId();
									$res = $jobtit ->addJobTitles();
									break;

				case 'CST'  :
									$compstruct = new CompStruct();
									$compstruct = $object;
									$res = $compstruct -> addCompStruct();
									break;

				case 'BNK'  :		$bankinfo = new BankInfo();
									$bankinfo = $object;
									$res = $bankinfo -> addBankInfo();
									break;

				case 'TAX'  :		$taxinfo = new TaxInfo();
									$taxinfo = $object;
									$res = $taxinfo -> addTaxInfo();
									break;

				case 'CCB'  :		$cashben = new CashBen();
									$cashben = $object;
									$res = $cashben -> addCashBenefits();
									break;

				case 'COS'  :		$cost = new CostCenter();
									$cost = $object;
									$res = $cost -> addCostCenter();
									break;

				case 'COU'  :		$country = new CountryInfo();
									$country = $object;
									$res = $country -> addCountryInfo();
									break;

				case 'CUR'  :		$currency = new CurrencyTypes();
									$currency = $object;
									$res = $currency -> addCurrencyTypes();
									break;

				case 'DWT'  :		$dwinfo = new DwellingType();
									$dwinfo = $object;
									$res = $dwinfo -> addDwellingType();
									break;

				case 'ELE'  :		$eleinfo = new ElectorateInfo();
									$eleinfo = $object;
									$res = $eleinfo -> addElectorateInfo();
									break;

				case 'EMC'  :		$empcatinfo = new EmployeeCat();
									$empcatinfo = $object;
									$res = $empcatinfo -> addEmployeeCat();
									break;

				case 'EMG'  :		$empgrpinfo = new EmployeeGroup();
									$empgrpinfo = $object;
									$res = $empgrpinfo -> addEmployeeGroup();
									break;

				case 'ETY'  :		$emptype = new EmployeeType();
									$emptype = $object;
									$res = $emptype -> addEmployeeType();
									break;

				case 'EXC'  :		$exccat = new ExtraCurrActCat();
									$exccat = $object;
									$res = $exccat -> addExtraCurrActCat();
									break;

				case 'DEF'  :		$hicode = new HierarchyDefInfo();
									$hicode = $object;
									$res = $hicode -> addHierarchyDefInfo();
									break;

				case 'JDC'  :		$jdcat = new JDCategory();
									$jdcat = $object;
									$res = $jdcat -> addJDCategory();
									break;

				case 'LOC'  :		$loc = new Location();
									$loc = $object;
									$res = $loc -> addLocation();
									break;

				case 'MEM'  :		$memtype = new MembershipType();
									$memtype = $object;
									$res = $memtype -> addMembershipType();
									break;

				case 'NAT'  :		$natinfo = new NationalityInfo();
									$natinfo = $object;
									$res = $natinfo -> addNationalityInfo();
									break;

				case 'NCB'  :		$noncashben = new NonCashBen();
									$noncashben = $object;
									$res = $noncashben -> addNonCashBenefits();
									break;

				case 'QLF'  :		$qualtype = new QualificationType();
									$qualtype = $object;
									$res = $qualtype -> addQualificationType();
									break;

				case 'RTM'  :		$rattype = new RatingTypes();
									$rattype = $object;
									$id = $rattype ->getRatingId();
									$res = $rattype -> addRatingTypes();
									break;

				case 'RTE'  :		$rouinfo = new RouteInfo();
									$rouinfo = $object;
									$res = $rouinfo -> addRouteInfo();
									break;

				case 'SGR'  :		$salgread = new SalaryGrades();
									$salgread = $object;
									$id = $salgread->getSalGrdId();
									$res = $salgread -> addSalaryGrades();
									break;

				case 'SKI'  :		$skill = new Skills();
									$skill = $object;
									$res = $skill -> addSkills();
									break;

				case 'ETH'  :		$ethnicrace = new EthnicRace();
									$ethnicrace = $object;
									$res = $ethnicrace -> addEthnicRace();
									break;

				case 'EXA'  :		$extcainfo = new ExtraCurActInfo();
									$extcainfo = $object;
									$res = $extcainfo -> addExtraCurActInfo();
									break;

				case 'MME'  :		$membinfo = new MembershipInfo();
									$membinfo = $object;
									$res = $membinfo -> addMembershipInfo();
									break;

				case 'JDK'  :		$jdkr = new JDKra();
									$jdkr  = $object;
									$res = $jdkr  -> addJDKra();
									break;

				case 'LAN'  :		$laninfo = new LanguageInfo();
									$laninfo  = $object;
									$res = $laninfo -> addLanguageInfo();
									break;

				case 'RLG'  :		$relinfo = new ReligionInfo();
									$relinfo = $object;
									$res = $relinfo -> addReligionInfo();
									break;

				case 'SAT'  :		$satinfo = new SatutoryInfo();
									$satinfo = $object;
									$res = $satinfo -> addSatutoryInfo();
									break;

				case 'UNI'  :		$unitype = new UniformType();
									$unitype = $object;
									$res = $unitype -> addUniformType();
									break;

				case 'EXA'  :		$excurac = new ExtraCurActInfo();
									$excurac = $object;
									$res = $excurac -> addExtraCurActInfo();
									break;

				case 'EDU'  :		$edu = new Education();
									$edu = $object;
									$res = $edu -> addEducation();
									break;

				case 'SSK'  :		$subsk = new SubSkillInfo();
									$subsk = $object;
									$res = $subsk -> addSubSkillInfo();
									break;

				case 'JDT'  :		$jdt = new JDType();
									$jdt = $object;
									$res = $jdt -> addJDType();
									break;

				case 'SBJ'  :		$subin = new SubjectInfo();
									$subin = $object;
									$res = $subin -> addSubjectInfo();
									break;

				case 'BCH'  :		$brch = new Branches();
									$brch = $object;
									$res = $brch -> addBranches();
									break;

				case 'CHI'  :		$cmphier = new CompHierachy();
									$cmphier = $object;
									$res = $cmphier -> addCompHierachy();
									break;

				case 'CTT'  :		$corptit = new CorpTitle();
									$corptit = $object;
									$res = $corptit -> addCorpTitles();
									break;

				case 'DSG'  :		$desig = new Designations();
									$desig = $object;
									$res = $desig -> addDesignations();
									break;

				case 'PRO'  :		$prov = new ProvinceInfo();
									$prov = $object;
									$res = $prov -> addProvinceInfo();
									break;

				case 'DIS'  :		$disinfo = new DistrictInfo();
									$disinfo = $object;
									$res = $disinfo -> addDistrictInfo();
									break;

				case 'EEC'  :		$eeojobcat = new EEOJobCat();
									$eeojobcat = $object;
									$res = $eeojobcat -> addEEOJobCat();
									break;

				case 'LIC'  :		$licenses = new Licenses();
									$licenses = $object;
									$res = $licenses -> addLicenses();
									break;
				case 'USR'  :		$users = new Users();
									$users = $object;
									$res = $users -> addUsers();
									break;

				case 'USG'  :		$usergroups = new UserGroups();
									$usergroups = $object;
									$id = $usergroups ->getUserGroupID();
									$res = $usergroups -> addUserGroups();
									break;
			}

			// Checking whether the $message Value returned is 1 or 0
			if ($res) {

				switch($index) {

					case 'SGR' :
								$backtype = isset($_POST['backtype']) ? $_POST['backtype'] : '';
								$refcapturemode = isset($_POST['refcapturemode']) ? $_POST['refcapturemode'] : '';
								$refid = isset($_POST['refid']) ? $_POST['refid'] : '';

								header("Location: ./CentralController.php?uniqcode=SGR&id=$id&capturemode=updatemode&backtype=$backtype&refcapturemode=$refcapturemode&refid=$refid");
								break;

					case 'RTM' :
								header("Location: ./CentralController.php?uniqcode=RTG&id=$id");
								break;

					case 'CST' :
								header("Location: ./CentralController.php?uniqcode=CST&VIEW=MAIN");
								break;


					case 'USG' : header("Location: ./CentralController.php?uniqcode=UGR&id=$id");
								break;

					case 'JEM' : break;

					case 'JOB' :
								header("Location: ./CentralController.php?uniqcode=JOB&id=$id&capturemode=updatemode");
								break;

					case 'EST' :

					case 'LOC' :
								if($noRedirect)
									break;
					case 'CUR' :
								if($noRedirect)
									break;

					default:
								$showMsg = "ADD_SUCCESS"; //If $message is 1 setting up the

								$uniqcode = $index;

								$esp = isset($_GET['isAdmin'])? ('&isAdmin='.$_GET['isAdmin']) : '';

								header("Location: ./CentralController.php?message=$showMsg&uniqcode=$uniqcode&VIEW=MAIN$esp");
				}

			} else {

				$showMsg = "ADD_FAILURE";

				$uniqcode = $index;
				header("Location: ./CentralController.php?msg=$showMsg&capturemode=addmode&uniqcode=$uniqcode");
			}
		}

	function addDesDisData($objectArr) {

		$desdis = new DesDescription();

			for($c=0;count($objectArr)>$c;$c++) {
				$desdis = $objectArr[$c];
				$desdis -> addJDKPI();
			}

	}

	function updateDesDisData($object) {
		$desdis = new DesDescription();

		$desdis = $object;
		$desdis -> updateJDKPI();
	}

	function delDesDisData($postArr,$getArr) {

			$desdis = new DesDescription();

		      $arr[0]=$postArr['chkdel'];
		      $size = count($arr[0]);

		      for($c=0 ; $size > $c ; $c++)
		          if($arr[0][$c]!=NULL)
		             $arr[1][$c]=$getArr['id'];

		      $desdis -> delJDKPI($arr);
    }

	function updateData($index,$id,$object,$noRedirect = false) {

			switch ($index) {

				case 'EST'  :		$empstat = new EmploymentStatus();
									$empstat = $object;
									$res = $empstat -> updateEmpStat();
									break;

				case 'JOB'  :		$jobtit = new JobTitle();
									$jobtit = $object;
									$res = $jobtit -> updateJobTitles();
									break;

				case 'CST'  :		$compstruct = new CompStruct();
									$compstruct = $object;
									$res = $compstruct -> updateCompStruct();
									break;

				case 'BNK'  :		$bankinfo = new BankInfo();
									$bankinfo = $object;
									$res = $bankinfo -> updateBankInfo();
									break;

				case 'TAX'  :		$taxinfo = new TaxInfo();
									$taxinfo = $object;
									$res = $taxinfo -> updateTaxInfo();
									break;

				case 'CCB'  :		$cashben = new CashBen();
									$cashben = $object;
									$res = $cashben -> updateCashBenefits();
									break;

				case 'COS'  :		$cost = new CostCenter();
									$cost = $object;
									$res = $cost -> updateCostCenter();
									break;

				case 'COU'  :		$country = new CountryInfo();
									$country = $object;
									$res = $country -> updateCountryInfo();
									break;

				case 'CUR'  :		$currency = new CurrencyTypes();
									$currency = $object;
									$res = $currency -> updateCurrencyTypes();
									break;

				case 'DWT'  :		$dwinfo = new DwellingType();
									$dwinfo = $object;
									$res = $dwinfo -> updateDwellingType();
									break;

				case 'ELE'  :		$eleinfo = new ElectorateInfo();
									$eleinfo = $object;
									$res = $eleinfo -> updateElectorateInfo();
									break;

				case 'EMC'  :		$empcatinfo = new EmployeeCat();
									$empcatinfo = $object;
									$res = $empcatinfo -> updateEmployeeCat();
									break;

				case 'EMG'  :		$empgrpinfo = new EmployeeGroup();
									$empgrpinfo = $object;
									$res = $empgrpinfo -> updateEmployeeGroup();
									break;

				case 'ETY'  :		$emptype = new EmployeeType();
									$emptype = $object;
									$res = $emptype -> updateEmployeeType();
									break;

				case 'EXC'  :		$exccat = new ExtraCurrActCat();
									$exccat = $object;
									$res = $exccat -> updateExtraCurrActCat();
									break;

				case 'DEF'  :		$hicode = new HierarchyDefInfo();
									$hicode = $object;
									$res = $hicode -> updateHierarchyDefInfo();
									break;

				case 'JDC'  :		$jdcat = new JDCategory();
									$jdcat = $object;
									$res = $jdcat -> updateJDCategory();
									break;

				case 'LOC'  :		$loc = new Location();
									$loc = $object;
									$res = $loc -> updateLocation();
									break;

				case 'MEM'  :		$memtype = new MembershipType();
									$memtype = $object;
									$res = $memtype -> updateMembershipType();
									break;

				case 'NAT'  :		$natinfo = new NationalityInfo();
									$natinfo = $object;
									$res = $natinfo -> updateNationalityInfo();
									break;

				case 'NCB'  :		$noncashben = new NonCashBen();
									$noncashben = $object;
									$res = $noncashben -> updateNonCashBenefits();
									break;

				case 'QLF'  :		$qualtype = new QualificationType();
									$qualtype = $object;
									$res = $qualtype -> updateQualificationType();
									break;

				case 'RTM'  :		$rattype = new RatingTypes();
									$rattype = $object;
									$res = $rattype -> updateRatingTypes();
									break;

				case 'RTE'  :		$rouinfo = new RouteInfo();
									$rouinfo = $object;
									$res = $rouinfo -> updateRouteInfo();
									break;

				case 'SGR'  :		$salgread = new SalaryGrades();
									$salgread = $object;
									$res = $salgread -> updateSalaryGrades();
									break;

				case 'SKI'  :		$skill = new Skills();
									$skill = $object;
									$res = $skill -> updateSkills();
									break;

				case 'ETH'  :		$ethnicrace = new EthnicRace();
									$ethnicrace = $object;
									$res = $ethnicrace -> updateEthnicRace();
									break;

				case 'EXA'  :		$extcainfo = new ExtraCurActInfo();
									$extcainfo = $object;
									$res = $extcainfo -> updateExtraCurActInfo();
									break;

				case 'MME'  :		$membinfo = new MembershipInfo();
									$membinfo = $object;
									$res = $membinfo -> updateMembershipInfo();
									break;

				case 'JDK'  :		$jdkr = new JDKra();
									$jdkr = $object;
									$res = $jdkr -> updateJDKra();
									break;

				case 'LAN'  :		$laninfo = new LanguageInfo();
									$laninfo  = $object;
									$res = $laninfo -> updateLanguageInfo();
									break;

				case 'RLG'  :		$relinfo = new ReligionInfo();
									$relinfo = $object;
									$res = $relinfo -> updateReligionInfo();
									break;

				case 'SAT'  :		$satinfo = new SatutoryInfo();
									$satinfo = $object;
									$res = $satinfo -> updateSatutoryInfo();
									break;

				case 'UNI'  :		$unitype = new UniformType();
									$unitype = $object;
									$res = $unitype -> updateUniformType();
									break;

				case 'EXA'  :		$excurac = new ExtraCurActInfo();
									$excurac = $object;
									$res = $excurac -> updateExtraCurActInfo();
									break;

				case 'EDU'  :		$edu = new Education();
									$edu = $object;
									$res = $edu -> updateEducation();
									break;

				case 'JDT'  :		$jdt = new JDType();
									$jdt = $object;
									$res = $jdt -> updateJDType();
									break;

				case 'SBJ'  :		$subin = new SubjectInfo();
									$subin = $object;
									$res = $subin -> updateSubjectInfo();
									break;

				case 'SSK'  :		$subsk = new SubSkillInfo();
									$subsk = $object;
									$res = $subsk -> updateSubSkillInfo();
									break;

				case 'BCH'  :		$brch = new Branches();
									$brch = $object;
									$res = $brch -> updateBranches();
									break;

				case 'CHI'  :		$cmphier = new CompHierachy();
									$cmphier = $object;
									$res = $cmphier -> updateCompHierachy();
									break;

				case 'CTT'  :		$corptit = new CorpTitle();
									$corptit = $object;
									$res = $corptit -> updateCorpTitles();
									break;

				case 'DSG'  :		$desig = new Designations();
									$desig = $object;
									$res = $desig -> updateDesignations();
									break;

				case 'PRO'  :		$prov = new ProvinceInfo();
									$prov = $object;
									$res = $prov -> updateProvinceInfo();
									break;

				case 'DIS'  :		$disinfo = new DistrictInfo();
									$disinfo = $object;
									$res = $disinfo -> updateDistrictInfo();
									break;

				case 'EEC'  :		$eeojobcat = new EEOJobCat();
									$eeojobcat = $object;
									$res = $eeojobcat -> updateEEOJobCat();
									break;


				case 'LIC'  :		$licenses = new Licenses();
									$licenses = $object;
									$res = $licenses -> updateLicenses();
									break;

				case 'USR'  :		$users = new Users();
									$users = $object;
									$res = $users -> updateUsers();
									break;

				case 'USG'  :		$usergroups = new UserGroups();
									$usergroups = $object;
									$res = $usergroups -> updateUserGroups();
									break;

				case 'EMX'  :		$emailConfig = new EmailConfiguration();
									$emailConfig = $object;
									$res = $emailConfig->reWriteConf();
									break;
				case 'ENS'  :		$emailNotifacationConfigs = new EmailNotificationConfiguration($_SESSION['user']);
									$emailNotifacationConfigs = $object;
									$res = true;
									foreach ($emailNotifacationConfigs as $emailNotifacationConfig) {
										$resp = $emailNotifacationConfig->updateNotificationStatus();
										if (!$resp) {
											$res = $resp;
										}
									}
									break;
			}

			// Checking whether the $message Value returned is 1 or 0
			if ($res) {

				switch ($index) {

					case 'CST' : break;

					case 'SGR' : $showMsg = "UPDATE_SUCCESS";

								 header("Location:".$_POST['referer']."&message=$showMsg");

					case 'EST' :
					case 'CUR' :
								if($noRedirect)
									break;

					default : 	$showMsg = "UPDATE_SUCCESS"; //If $message is 1 setting up the

								$uniqcode = $index;

								$esp = isset($_GET['isAdmin'])? ('&isAdmin='.$_GET['isAdmin']) : '';

								header("Location: ./CentralController.php?message=$showMsg&uniqcode=$uniqcode&VIEW=MAIN$esp");
				}

			} else {

				$showMsg = "UPDATE_FAILURE";

				$uniqcode = $index;

				$esp = isset($_GET['isAdmin'])? ('&isAdmin='.$_GET['isAdmin']) : '';
				echo mysql_error();

				header("Location: ./CentralController.php?msg=$showMsg&id=$id&capturemode=updatemode&uniqcode=$uniqcode$esp");
			}
	}


	function assignData($index,$object,$action) {

			switch ($index) {

				case 'GEN'  :		$geninfo = new GenInfo();
									$geninfo = $object;
									$geninfo->updateGenInfo();

									break;

				case 'JEM'  :		$jobtit_empstat = new JobTitEmpStat();
									$jobtit_empstat = $object;
									$jobtit_empstat->addJobTitEmpStat();

									break;

				case 'CST'  :		$comphier = new CompHierachy();
									$comphier = $object;
									if($action == 'ADD')
										$comphier->addCompHierachy();

									break;

				case 'BBS'  :		$cashben = new CashBenSalary();
									$cashben = $object;
									if($action == 'ADD')
										$cashben->addCashBenefits();
									elseif($action == 'EDIT')
										$cashben->updateCashBenefits();
									break;

				case 'NBS'  :		$noncashben = new NonCashBenSalary();
									$noncashben = $object;
									if($action == 'ADD')
										$noncashben->addCashBenefits();
									elseif($action == 'EDIT')
										$noncashben->updateCashBenefits();
									break;

				case 'DQA'  :		$desqual = new DesQualification();
									$desqual = $object;
									if($action == 'ADD')
										$desqual->addJDQual();
									elseif($action == 'EDIT')
										$desqual->updateJDQual();
									break;

				case 'DQS'  :		$desqual = new DesQualSubject();
									$desqualsub = $object;
									if($action == 'ADD')
										$desqualsub->addQuaSub();
									elseif($action == 'EDIT')
										$desqualsub->updateQuaSub();
									break;

				case 'SGR'  :		$salcud = new SalCurDet();
									$salcud = $object;
									if($action == 'ADD')
										$salcud->addSalCurDet();
									elseif($action == 'EDIT')
										$salcud->updateSalCurDet();
									break;

				case 'RTG'  :		$ratgrd = new RatingGrade();
									$ratgrd = $object;
									if($action == 'ADD') {
										$ratgrd->setRatGrdId($ratgrd->getLastRecord());
										$ratgrd->addRatGrd();
									} elseif($action == 'EDIT')
										$ratgrd->updateRatGrd();
									break;

				case 'UGR'  :		$ugrights = new Rights();
									$ugrights = $object;
									if($action == 'ADD')
										$ugrights->addRights();
									elseif($action == 'EDIT')
										$ugrights->updateRights();
									break;
			}
	}

	function deleteData($index, $object) {

		switch ($index) {
			case 'CST': $compstruct = new CompStruct();
						$compstruct = $object;
						$res = $compstruct -> deleteCompStruct();
						break;
		}
	}

	function delAssignData($index,$postArr,$getArr) {

			switch ($index) {

				case 'BBS'  :		$cashbensal = new CashBenSalary();
								      $arr[0]=$postArr['chkdel'];
								      $size = count($arr[0]);
								      for($c=0 ; $size > $c ; $c++)
								          if($arr[0][$c]!=NULL)
								             $arr[1][$c]=$getArr['id'];

								      $cashbensal -> delCashBenefits($arr);
								      break;

				case 'NBS'  :		$noncashbensal = new NonCashBenSalary();
								      $arr[0]=$postArr['chkdel'];
								      $size = count($arr[0]);
								      for($c=0 ; $size > $c ; $c++)
								          if($arr[0][$c]!=NULL)
								             $arr[1][$c]=$getArr['id'];

								      $noncashbensal -> delCashBenefits($arr);
								      break;

				case 'DQA'	:		$desqual = new DesQualification();
									$arr[1]=$postArr['chkdel'];
      								$size = count($arr[1]);
								    for($c=0 ; $size > $c ; $c++)
								          if($arr[1][$c]!=NULL)
								             $arr[0][$c]=$getArr['id'];

								    $desqual -> delJDQual($arr);
								    break;

				case 'DQS' :		$desqualsub = new DesQualSubject();

									$arr[1]=$postArr['chkdel'];
									$size = count($arr[1]);
									for($c=0 ; $size > $c ; $c++)
									    if($arr[1][$c]!=NULL)
									      {
									      $arr[0][$c]=$getArr['id'];
									      $arr[2][$c]=$getArr['QUA'];
									      }

									$desqualsub -> delQuaSub($arr);
									break;

			   case 'SGR'	:		$salcurdet = new SalCurDet();
									$arr[1]=$postArr['chkdel'];
      								$size = count($arr[1]);
								    for($c=0 ; $size > $c ; $c++)
								          if($arr[1][$c]!=NULL)
								             $arr[0][$c]=$getArr['id'];

								    $salcurdet -> delSalCurDet($arr);
								    break;

			   case 'RTG'	:		$ratgrd = new RatingGrade();
								    $arr[0]=$postArr['chkdel'];
								    for($c=0;count($arr[0])>$c;$c++)
								        if($arr[0][$c]!=NULL)
								           $arr[1][$c]=$getArr['id'];

								    $ratgrd -> delRatGrd($arr);
								    break;

				case 'UGR'  :
									$urights = new Rights();

								    $urights->clearRights($getArr['id']);
								    break;
			}
	}

	function reDirect($getArr,$object = null) {

		$form_creator = new FormCreator($getArr);

		switch ($getArr['uniqcode']) {

			case 'BNK' :	$form_creator ->formPath = '/templates/eimadmin/bankinformation.php';
							$bankinfo = new BankInfo();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $bankinfo->getLastRecord();
							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $bankinfo->filterBankInfo($getArr['id']);
							}

							break;

			case 'BCH' :	$form_creator ->formPath = '/templates/eimadmin/branches.php';
							$brch = new Branches();
							$bank = new BankInfo();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $brch ->getLastRecord();
								$form_creator ->popArr['bankcode'] = $bank ->getBankCodes();
							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $brch ->filterBranches($getArr['id']);
								$form_creator ->popArr['bankcode'] = $bank ->getBankCodes();
							}

							break;

			case 'TAX' :	$form_creator ->formPath = '/templates/eimadmin/taxinformation.php';
							$taxinfo = new TaxInfo();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $taxinfo->getLastRecord();
							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $taxinfo->filterTaxInfo($getArr['id']);
							}

							break;

			case 'CCB' :	$form_creator ->formPath = '/templates/eimadmin/cashben.php';
							$cashben = new CashBen();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $cashben->getLastRecord();
							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $cashben->filterCashBenefits($getArr['id']);
							}

							break;

			case 'BBS' :	$form_creator ->formPath = '/templates/eimadmin/cashbensal.php';
							$salgrd = new SalaryGrades();
							$cashbensal = new CashBenSalary();
							$cashben = new CashBen();

							$form_creator ->popArr['salDet'] = $salgrd -> filterSalaryGrades($getArr['id']);
							$form_creator ->popArr['benListAll'] = $cashben->getAllCashBenefits();

							if(isset($getArr['editID'])) {
								    $arr[0]=$getArr['editID'];
    								$arr[1]=$getArr['id'];


								$form_creator -> popArr['editArr'] = $cashbensal->filterCashBenefits($arr);
							} else {
								$form_creator ->popArr['benListUnAss'] = $cashben->getCashBenCodes($getArr['id']);
							}

							$form_creator ->popArr['benListAss'] = $cashbensal ->getAssCashBenefits($getArr['id']);
							break;

			case 'COS' :	$form_creator ->formPath = '/templates/eimadmin/costcenters.php';
							$cost = new CostCenter();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $cost->getLastRecord();
							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $cost->filterCostCenter($getArr['id']);
							}

							break;

			case 'COU' :	$form_creator ->formPath = '/templates/eimadmin/countryinformation.php';
							$country = new CountryInfo();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $country ->getLastRecord();
							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $country ->filterCountryInfo($getArr['id']);
							}

							break;

			case 'CUR' :	$form_creator ->formPath = '/templates/eimadmin/currencytypes.php';
							$currency = new CurrencyTypes();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $currency ->getLastRecord();
							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $currency ->filterCurrencyTypes($getArr['id']);
							}

							break;

			case 'DWT' :	$form_creator ->formPath = '/templates/eimadmin/dwellinginformation.php';
							$dwinfo = new DwellingType();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $dwinfo ->getLastRecord();
							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $dwinfo->filterDwellingType($getArr['id']);
							}

							break;

			case 'ELE' :	$form_creator ->formPath = '/templates/eimadmin/electorateinformation.php';
							$eleinfo = new ElectorateInfo();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $eleinfo ->getLastRecord();
							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $eleinfo->filterElectorateInfo($getArr['id']);
							}

							break;

			case 'EMC' :	$form_creator ->formPath = '/templates/eimadmin/empcatinfo.php';
							$empcatinfo = new EmployeeCat();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $empcatinfo ->getLastRecord();
							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $empcatinfo ->filterEmployeeCat($getArr['id']);
							}

							break;

			case 'EMG' :	$form_creator ->formPath = '/templates/eimadmin/employeegroups.php';
							$empgrpinfo = new EmployeeGroup();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $empgrpinfo ->getLastRecord();
							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $empgrpinfo ->filterEmployeeGroup($getArr['id']);
							}

							break;

			case 'ETY' :	$form_creator ->formPath = '/templates/eimadmin/emptypes.php';
							$emptype = new EmployeeType();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $emptype ->getLastRecord();
							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $emptype ->filterEmployeeType($getArr['id']);
							}

							break;

			case 'EXC' :	$form_creator ->formPath = '/templates/eimadmin/extracurractcat.php';
							$exccat = new ExtraCurrActCat();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $exccat ->getLastRecord();
							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $exccat ->filterExtraCurrActCat($getArr['id']);
							}

							break;

			case 'DEF' :	$form_creator ->formPath = '/templates/eimadmin/hierarchydef.php';
							$hidef = new HierarchyDefInfo();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $hidef ->getLastRecord();
							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $hidef ->filterHierarchyDefInfo($getArr['id']);
							}

							break;

			case 'CHI' :	$form_creator ->formPath = '/templates/eimadmin/comphier.php';
							$cmphier = new CompHierachy();
							$hidef = new HierarchyDefInfo();
							$loc = new Location();
							$empinfo = new EmpInfo();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $cmphier ->getLastRecord();
								$form_creator ->popArr['hiercodes'] = $hidef->getHierachyCodes();
								$form_creator ->popArr['empcodes'] = $empinfo->getEmpCodes();
								$form_creator ->popArr['deflev'] = $cmphier->getHierachyDef();
								$form_creator ->popArr['loccodes'] = $loc->getLocCodes();

							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $cmphier ->filterCompHierachy($getArr['id']);
								$form_creator ->popArr['hiercodes'] = $cmphier->getHierachyCodes();
								$form_creator ->popArr['empcodes'] = $empinfo->getEmpCodes();
								$form_creator ->popArr['deflev'] = $cmphier->getHierachyDef();
								$form_creator ->popArr['loccodes'] = $loc->getLocCodes();
							}

							break;

			case 'LOC' :	$form_creator ->formPath = '/templates/eimadmin/locations.php';
							$loc = new Location();
							$country = new CountryInfo();

							$form_creator->popArr['cntlist'] = $country->getCountryCodes();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $loc ->getLastRecord();
							} elseif($getArr['capturemode'] == 'updatemode') {

								$province = new ProvinceInfo();
								$district = new DistrictInfo();

								$form_creator ->popArr['editArr'] = $edit = $loc ->filterLocation($getArr['id']);
								$form_creator ->popArr['provlist'] = $province->getProvinceCodes($edit[0][2]);
								$form_creator ->popArr['districtlist'] = $district->getDistrictCodes($edit[0][3]);
							}

							break;

			case 'JDC' :	$form_creator ->formPath = '/templates/eimadmin/jdcategory.php';
							$jdcat = new JDCategory();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $jdcat ->getLastRecord();
							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $jdcat ->filterJDCategory($getArr['id']);
							}

							break;

			case 'MEM' :	$form_creator ->formPath = '/templates/eimadmin/membershiptypes.php';
							$memtype = new MembershipType();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $memtype ->getLastRecord();
							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $memtype ->filterMembershipType($getArr['id']);
							}

							break;
			case 'EMX' :	$form_creator ->formPath = '/templates/eimadmin/emailConfiguration.php';
							$emailConfigObj = new EmailConfiguration();
							$form_creator ->popArr['editArr'] = $emailConfigObj;
							break;
			case 'ENS' :	$form_creator->formPath = '/templates/eimadmin/emailNotificationConfiguration.php';
							$emailNotificationConfObj = new EmailNotificationConfiguration($_SESSION['user']);
							$form_creator ->popArr['editArr'] =$emailNotificationConfObj->fetchNotifcationStatus();
							break;
			case 'NAT' :	$form_creator ->formPath = '/templates/eimadmin/nationalityinformation.php';
							$natinfo = new NationalityInfo();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $natinfo ->getLastRecord();
							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $natinfo ->filterNationalityInfo($getArr['id']);
							}

							break;

			case 'NCB' :	$form_creator ->formPath = '/templates/eimadmin/noncashben.php';
							$noncashben = new NonCashBen();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $noncashben ->getLastRecord();
							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $noncashben ->filterNonCashBenefits($getArr['id']);
							}

							break;

			case 'QLF' :	$form_creator ->formPath = '/templates/eimadmin/qualificationtypes.php';
							$qualtype = new QualificationType();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $qualtype ->getLastRecord();
							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $qualtype ->filterQualificationType($getArr['id']);
							}

							break;

			case 'RTM' :	$form_creator ->formPath = '/templates/eimadmin/ratingmethods.php';
							$rattype = new RatingTypes();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $rattype ->getLastRecord();
							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $rattype ->filterRatingTypes($getArr['id']);
							}

							break;

			case 'RTE' :	$form_creator ->formPath = '/templates/eimadmin/routeinformation.php';
							$rouinfo = new RouteInfo();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $rouinfo ->getLastRecord();
							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $rouinfo  ->filterRouteInfo($getArr['id']);
							}

							break;

			case 'SGR' :	$form_creator ->formPath = '/templates/eimadmin/salarygrades.php';
							$salgrade = new SalaryGrades();
							$salcurdet = new SalCurDet();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $salgrade ->getLastRecord();
							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $salgrade ->filterSalaryGrades($getArr['id']);
								$form_creator ->popArr['assCurrency'] = $salcurdet->getAssSalCurDet($getArr['id']);

								if(!isset($getArr['editID'])) {
									$form_creator ->popArr['unAssCurrency'] = $salcurdet->getUnAssSalCurDet($getArr['id']);
								} else {
								    $arr[0]=$getArr['id'];
								    $arr[1]=$getArr['editID'];

									$form_creator ->popArr['editAssCurrency'] = $salcurdet->filterSalCurDet($arr);
								}
							}

							break;

			case 'SKI' :	$form_creator ->formPath = '/templates/eimadmin/skills.php';
							$skill = new Skills();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $skill ->getLastRecord();
							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $skill ->filterSkills($getArr['id']);
							}

							break;

			case 'ETH' :	$form_creator ->formPath = '/templates/eimadmin/ethnicrace.php';
							$ethnicrace = new EthnicRace();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $ethnicrace ->getLastRecord();
							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $ethnicrace ->filterEthnicRace($getArr['id']);

							}

							break;

			case 'EXA' :	$form_creator ->formPath = '/templates/eimadmin/extracurractinfo.php';
							$extcainfo = new ExtraCurActInfo();
							$excurac   = new ExtraCurrActCat();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $extcainfo  ->getLastRecord();
								$form_creator ->popArr['getResultSet'] = $extcainfo->getExtraCatCodes();
							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] =$extcainfo  ->filterExtraCurActInfo($getArr['id']);
								$form_creator ->popArr['getResultSet'] = $excurac->getExtraCatCodes();
							}

							break;

			case 'MME' :	$form_creator ->formPath = '/templates/eimadmin/membershipinformation.php';
							$meminfo = new MembershipInfo();
							$memtype = new  MembershipType();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $meminfo   ->getLastRecord();
								$form_creator ->popArr['getResultSet'] = $memtype ->getMembershipTypeCodes();
							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] =$meminfo   ->filterMembershipInfo($getArr['id']);
								$form_creator ->popArr['getResultSet'] = $memtype ->getMembershipTypeCodes();
							}

							break;

			case 'JDK' :	$form_creator ->formPath = '/templates/eimadmin/jdkra.php';
							$jdkr = new JDKra();
							$skill = new Skills();
							$jdtyp = new JDType();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $jdkr  ->getLastRecord();
								$form_creator ->popArr['jdktyplist'] = $jdtyp ->getJDTypeCodes();
								$form_creator ->popArr['sktyplist'] = $skill ->getSkillCodes();
							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] =$jdkr   ->filterJDKra($getArr['id']);
								$form_creator ->popArr['jdktyplist'] =$jdtyp ->getJDTypeCodes();
								$form_creator ->popArr['sktyplist'] =$skill ->getSkillCodes();
							}

							break;

			case 'LAN' :	$form_creator ->formPath = '/templates/eimadmin/languageinformation.php';
							$laninfo = new LanguageInfo();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $laninfo   ->getLastRecord();

							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] =$laninfo  ->filterLanguageInfo($getArr['id']);

							}

							break;

			case 'RLG' :	$form_creator ->formPath = '/templates/eimadmin/religioninformation.php';
							$relinfo = new ReligionInfo();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $relinfo ->getLastRecord();
							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $relinfo ->filterReligionInfo($getArr['id']);
							}

							break;

			case 'SAT' :	$form_creator ->formPath = '/templates/eimadmin/satutoryinfo.php';
							$satinfo = new SatutoryInfo();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $satinfo ->getLastRecord();
							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $satinfo ->filterSatutoryInfo($getArr['id']);
							}

							break;

			case 'UNI' :	$form_creator ->formPath = '/templates/eimadmin/uniformtypes.php';
							$unitype = new UniformType();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $unitype ->getLastRecord();
							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $unitype ->filterUniformType($getArr['id']);
							}

							break;

			case 'EXA' :	$form_creator ->formPath = '/templates/eimadmin/extracurractinfo.php';
							$excurac = new ExtraCurActInfo();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $excurac ->getLastRecord();
								$form_creator ->popArr['getResultSet'] = $excurac->getExtraCatCodes();
							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $excurac ->filterExtraCurActInfo($getArr['id']);
								$form_creator ->popArr['getResultSet'] = $excurac->getExtraCatCodes();
							}

							break;

		case 'EDU' :	$form_creator ->formPath = '/templates/eimadmin/qualifications.php';
							$edu = new Education();
							//$qualtype = new QualificationType();
							//$rattype = new RatingTypes();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $edu ->getLastRecord();
								//$form_creator ->popArr['qTypes'] = $qualtype->getQualTypes();
								//$form_creator ->popArr['ratings'] =$rattype ->getRatings();
							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $edu ->filterEducation($getArr['id']);
								//$form_creator ->popArr['qTypes'] = $qualtype->getQualTypes();
								//$form_creator ->popArr['ratings'] = $rattype->getRatings();
							}

							break;

			case 'JDT' :	$form_creator ->formPath = '/templates/eimadmin/jdtypes.php';
							$jdtyp = new JDType();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $jdtyp ->getLastRecord();
								$form_creator ->popArr['jdcatcodes']=$jdtyp ->getJDTypeCodes();

							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $jdtyp ->filterJDType($getArr['id']);
								$form_creator ->popArr['jdcatcodes']=$jdtyp ->getJDTypeCodes();

							}

							break;

			case 'SBJ' :	$form_creator ->formPath = '/templates/eimadmin/subjectinformation.php';
							$subin = new SubjectInfo();
							$qualifi = new Qualifications();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $subin ->getLastRecord();
								$form_creator ->popArr['getResultSet'] = $qualifi ->getQualifiCodes();


							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $subin ->filterSubjectInfo($getArr['id']);
								$form_creator ->popArr['getResultSet'] = $qualifi->getQualifiCodes();


							}

							break;

			case 'SSK' :	$form_creator ->formPath = '/templates/eimadmin/subskillinformation.php';
							$subsk = new SubSkillInfo();
							$skill = new Skills();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $subsk ->getLastRecord();
								$form_creator ->popArr['getResultSet'] = $skill->getSkillCodes();

				    		} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $subsk ->filterSubSkillInfo($getArr['id']);
								$form_creator ->popArr['getResultSet'] = $skill->getSkillCodes();
							}

							break;

			case 'CTT' :	$form_creator ->formPath = '/templates/eimadmin/corptit.php';
							$corptit = new CorpTitle();
							$salgrd  = new SalaryGrades();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $corptit ->getLastRecord();
								$form_creator ->popArr['corptitles'] = $corptit->getCorpTitles();
								$form_creator ->popArr['salgrds'] = $corptit->getSalGrades();
							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $corptit ->filterCorpTitles($getArr['id']);
								$form_creator ->popArr['corptitles'] = $corptit->getCorpTitles();
								$form_creator ->popArr['salgrds'] = $salgrd->getSalGrades();
							}

							break;

			case 'DSG' :	$form_creator ->formPath = '/templates/eimadmin/designations.php';
							$desig = new Designations();
							$corptit = new CorpTitle();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $desig ->getLastRecord();
								$form_creator ->popArr['corptitles'] = $corptit->getCorpTitles();
								$form_creator ->popArr['des'] = $desig->getDes();
							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $desig ->filterDesignations($getArr['id']);
								$form_creator ->popArr['corptitles'] = $corptit->getCorpTitles();
								$form_creator ->popArr['des'] = $desig->getDes();
							}

							break;

			case 'PRO' :	$form_creator ->formPath = '/templates/eimadmin/provinceinformation.php';
							$prov = new ProvinceInfo();
							$country = new CountryInfo();

							 $form_creator ->popArr['countrylist'] = $country->getCountryCodes();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $prov ->getLastRecord();
							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $prov ->filterProvinceInfo($getArr['id']);
							}

							break;

			case 'DIS' :	$form_creator ->formPath = '/templates/eimadmin/districtinformation.php';
							$disinfo = new DistrictInfo();
							$prov = new ProvinceInfo();
							$country = new CountryInfo();

							$form_creator ->popArr['countrylist'] = $country->getCountryCodes();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $disinfo ->getLastRecord();

								if($object != null) {
									$form_creator -> popArr['txtDistrictDesc'] = $object -> txtDistrictDesc;
									$form_creator -> popArr['cmbCountry'] = $object -> cmbCountry;
									$form_creator -> popArr['selProvince'] = $object -> selProvince;
								    $form_creator -> popArr['provlist'] = $prov->getProvinceCodes($object -> cmbCountry);

								}

							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $editArr = $disinfo ->filterDistrictInfo($getArr['id']);

								if($object != null) {
									$form_creator -> popArr['txtDistrictDesc'] = $object -> txtDistrictDesc;
									$form_creator -> popArr['cmbCountry'] = $object -> cmbCountry;
									$form_creator -> popArr['selProvince'] = $object -> selProvince;
								    $form_creator -> popArr['provlist'] = $prov->getProvinceCodes($object -> cmbCountry);
								} else {
								    $temp = $prov -> filterGetProvinceCodeInfo($editArr[0][2]);
								    $form_creator->popArr['selcountry'] = $temp[0][2];
								    $form_creator->popArr['provlist'] = $prov->getProvinceCodes($temp[0][2]);
								}
							}

							break;


			case 'NBS' :	$form_creator ->formPath = '/templates/eimadmin/noncashbensal.php';
							$salgrd = new SalaryGrades();
							$noncashbensal = new NonCashBenSalary();
							$noncashben = new NonCashBen();

							$form_creator ->popArr['salDet'] = $salgrd -> filterSalaryGrades($getArr['id']);
							$form_creator ->popArr['benListAll'] = $noncashben->getAllNonCashBenefits();

							if(isset($getArr['editID'])) {
								    $arr[0]=$getArr['editID'];
								    $arr[1]=$getArr['id'];

								$form_creator ->popArr['editArr'] = $noncashben->filterNonCashBenefits($arr);
							} else {
								$form_creator ->popArr['benListUnAss'] = $noncashben->getNonCashBenCodes($getArr['id']);
							}

							$form_creator ->popArr['benListAss'] = $noncashbensal ->getAssCashBenefits($getArr['id']);
							break;

			case 'DDI' :	$form_creator ->formPath = '/templates/eimadmin/desdis.php';
							$desig = new Designations();
							$desdis = new DesDescription();

							$form_creator ->popArr['desDet'] = $desig -> getDes();
							$form_creator ->popArr['desDisAss'] = $desdis ->getAssigned($getArr['id']);

							if(isset($getArr['editID'])) {
									$arr[1]=$getArr['id'];
									$arr[0]=$getArr['editID'];

								$form_creator -> popArr['editArr'] = $desdis -> filterJDKPI($arr);
							}

							if(isset($getArr['addForm']) && $getArr['addForm']=='ADD')
								$form_creator ->popArr['desDisUnAss'] = $desdis ->getGrouping($getArr['id']);

							break;

			case 'DQA' :	$form_creator ->formPath = '/templates/eimadmin/desqua.php';
							$desig = new Designations();
							$desqual = new DesQualification();

							$form_creator ->popArr['desDet'] = $desig -> getDes();
							$form_creator ->popArr['qualListAll'] = $desqual->getAllQualifications();

							if(isset($getArr['editID'])) {
							    $arr[0]=$getArr['id'];
							    $arr[1]=$getArr['editID'];

								$form_creator -> popArr['editArr'] = $desqual->filterJDQual($arr);
							} else {
								$form_creator ->popArr['qualListUnAss']	= $desqual->getQual($getArr['id']);
							}

							$form_creator ->popArr['qualListAss'] = $desqual ->getAssJDQual($getArr['id']);
							break;

			case 'DQS' :	$form_creator ->formPath = '/templates/eimadmin/desquasub.php';
							$desig = new Designations();
							$desqualsub = new DesQualSubject();
							$qualifi = new Qualifications();

							$form_creator ->popArr['desDet'] = $desig -> filterDesignations($getArr['id']);
							$form_creator ->popArr['qualDet'] = $qualifi -> getQualCodes();
							$form_creator->popArr['gradlist'] = $desqualsub ->getRatGrds($getArr['QUA']);
							$form_creator->popArr['subDet'] = $desqualsub -> getSubjectCodes();

							if(isset($getArr['editID'])) {
								    $arr[0]=$getArr['id'];
								    $arr[1]=$getArr['editID'];
								    $arr[2]=$getArr['QUA'];

								$form_creator->popArr['editArr'] = $desqualsub -> filterQuaSub($arr);

							} else {
								$form_creator->popArr['sublist'] = $desqualsub->getSubjects($getArr['id'],$getArr['QUA']);
							}

							$form_creator->popArr['rset'] = $desqualsub ->getAssQuaSub($getArr['id']);
							break;

			case 'RTG' :	$form_creator ->formPath = '/templates/eimadmin/ratinggrade.php';
								$rattyp = new RatingTypes();
								$form_creator->popArr['ratDet'] = $rattyp ->filterRatingTypes($getArr['id']);

							    $ratgrd = new RatingGrade();


							if(isset($getArr['editID'])) {
								    $arr[0]=$getArr['editID'];
								    $arr[1]=$getArr['id'];
								    $form_creator->popArr['editArr'] = $ratgrd->filterRatGrd($arr);
							} else {
								$form_creator ->popArr['newID'] = $ratgrd->getLastRecord();
							}

							$form_creator->popArr['ratGrdAss'] = $ratgrd ->getAssRatGrd($getArr['id']);
							break;


			case 'EEC' :	$form_creator ->formPath = '/templates/eimadmin/eeojobcat.php';
							$eeojobcat = new EEOJobCat();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $eeojobcat->getLastRecord();
							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $eeojobcat->filterEEOJobCat($getArr['id']);
							}

							break;

			case 'LIC' :	$form_creator ->formPath = '/templates/eimadmin/licenses.php';
							$licenses = new Licenses();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $licenses->getLastRecord();
							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $licenses->filterLicenses($getArr['id']);
							}

							break;

			case 'CST' :	$form_creator->formPath = '/templates/eimadmin/compstruct.php';
							/*
							$hierachinfo = new HierarchyDefInfo();
							$comphier = new CompHierachy();
							$compstruct = new CompStruct();

							$form_creator->popArr['hierlist'] = $hierachinfo -> getHierarchyDef();
							$form_creator->popArr['compstruct'] = $comphier ->getAssCompHier(1,'');
							$form_creator->popArr['unAssCompStruct'] = $compstruct->getUnAssCompStruct('',1);
							*/

							$locations = new Location();
							$countries = new CountryInfo();

							$form_creator->popArr['locations'] = $locations->getLocCodes();
							$form_creator->popArr['countries'] = $countries->getCountryCodes ();

							break;

			case 'JOB' :	$form_creator->formPath = '/templates/eimadmin/jobtitle.php';

							$jobtitle = new JobTitle();
							$paygrade = new SalaryGrades();
							$jobtit_empstat = new JobTitEmpStat();

							$form_creator ->popArr['paygrade'] = $paygrade->getSalGrades();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $jobtitle->getLastRecord();
							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $jobtitle->filterJobTitles($getArr['id']);
								$form_creator ->popArr['assEmploymentStat'] = $jobtit_empstat->getAssEmpStat($getArr['id']);
								$form_creator ->popArr['unAssEmploymentStat'] = $jobtit_empstat->getUnAssEmpStat($getArr['id']);
							}

							break;

			case 'EST' :	$form_creator->formPath = '/templates/eimadmin/empstat.php';

							$empstat = new EmploymentStatus();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $empstat->getLastRecord();
							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $empstat->filterEmpStat($getArr['id']);
							}

							break;

			case 'GEN' :	$form_creator->formPath = '/templates/eimadmin/geninfo.php';

							$geninfo = new GenInfo();
							$country = new CountryInfo();
							$province = new ProvinceInfo();
							$district = new DistrictInfo();

							$tabArr = $geninfo->filterGenInfo();
							$genInfoKeys = explode('|',$tabArr[0][0]);
							$genInfoValues = explode('|',$tabArr[0][1]);

							for($c=0; $genInfoKeys && count($genInfoKeys)>$c; $c++) {
								$editArr[$genInfoKeys[$c]] = $genInfoValues[$c];
							}

							$form_creator->popArr['editArr'] = $editArr;
							$form_creator->popArr['cntlist'] = $country->getCountryCodes();

							if(isset($editArr['COUNTRY']))
								$form_creator ->popArr['provlist'] = $province->getProvinceCodes($editArr['COUNTRY']);

							if(isset($editArr['STATE']))
								$form_creator ->popArr['districtlist'] = $district->getDistrictCodes($editArr['STATE']);

							break;

			case 'USR' :	$form_creator ->formPath = '/templates/maintenance/users.php';
							$user= new Users();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $user->getLastRecord();
								$form_creator ->popArr['emplist'] = $user->getEmployeeCodes();
								$form_creator ->popArr['uglist'] = $user->getUserGroupCodes();
							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $user->filterUsers($getArr['id']);
								$form_creator ->popArr['emplist'] = $user->getEmployeeCodes();
								$form_creator ->popArr['uglist'] = $user->getUserGroupCodes();
								//$form_creator ->popArr['editArr'] = $user->filterChangeUsers($getArr['id']);
							}
							break;
			case 'USG'  :	$form_creator ->formPath = '/templates/maintenance/usergroups.php';
							$usrgrp = new UserGroups();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $usrgrp->getLastRecord();

							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $usrgrp->filterUserGroups($getArr['id']);

							}

							break;

			case 'UGR' :	$form_creator ->formPath = '/templates/maintenance/ugrights.php';

							$urights = new Rights();
							$usergroup = new UserGroups();

							$form_creator->popArr['ugDet'] = $usergroup->filterUserGroups($getArr['id']);
							$form_creator->popArr['modlist'] = $urights->getAllModules();

							if(isset($getArr['editID'])) {
								    $arr[0]=$getArr['id'];
								    $arr[1]=$getArr['editID'];
								$form_creator->popArr['editArr'] = $urights->filterRights($arr);

							} else {
								$form_creator->popArr['modlistUnAss'] = $urights->getModuleCodes($getArr['id']);
							}

							$form_creator->popArr['modlistAss'] = $urights->getAssRights($getArr['id']);

							break;

				}

		$form_creator->display();

	}

}
?>
