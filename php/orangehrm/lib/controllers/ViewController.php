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

require_once ROOT_PATH . '/lib/exception/ExceptionHandler.php';

require_once ROOT_PATH . '/lib/common/EventMediator.php';

require_once ROOT_PATH . '/lib/models/eimadmin/Location.php';
require_once ROOT_PATH . '/lib/models/eimadmin/SalaryGrades.php';
require_once ROOT_PATH . '/lib/models/eimadmin/CurrencyTypes.php';
require_once ROOT_PATH . '/lib/models/eimadmin/SalCurDet.php';
require_once ROOT_PATH . '/lib/models/eimadmin/CompStruct.php';
require_once ROOT_PATH . '/lib/models/eimadmin/CompProperty.php';
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
require_once ROOT_PATH . '/lib/models/eimadmin/JobSpec.php';

require_once ROOT_PATH . '/lib/models/eimadmin/Customer.php';
require_once ROOT_PATH . '/lib/models/eimadmin/Projects.php';
require_once ROOT_PATH . '/lib/models/eimadmin/ProjectAdmin.php';
require_once ROOT_PATH . '/lib/models/eimadmin/ProjectAdminGateway.php';
require_once ROOT_PATH . '/lib/models/eimadmin/ProjectActivity.php';
require_once ROOT_PATH . '/lib/models/eimadmin/CustomFields.php';
require_once ROOT_PATH . '/lib/models/eimadmin/CustomExport.php';
require_once ROOT_PATH . '/lib/models/eimadmin/CustomImport.php';

require_once ROOT_PATH . '/lib/models/eimadmin/CSVExport.php';
require_once ROOT_PATH . '/lib/models/eimadmin/CSVImport.php';

require_once ROOT_PATH . '/lib/common/FormCreator.php';

require_once ROOT_PATH . '/lib/models/maintenance/UserGroups.php';
require_once ROOT_PATH . '/lib/models/maintenance/Users.php';
require_once ROOT_PATH . '/lib/models/maintenance/Rights.php';

if ($_SESSION['ldap'] == "enabled") {
	require_once ROOT_PATH . '/plugins/ldap/LdapDetails.php';
}

class ViewController {

	var $indexCode;
	var $message;
	var $pageID;


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

								$location = new models_eimadmin_Location();
								return $location->addLocation();
							}

							if($cntrl == 'getLocCodes') {

								$location = new models_eimadmin_Location();
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

							if($cntrl == 'allEmpStat') {

								$jobtit_empstat = new JobTitEmpStat();
								return $jobtit_empstat->getAllEmpStats($value);
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
            case 'TCP' :
			case 'GEN' :
						$this->reDirect($getArr);
						break;
			case 'EMX' :$this->reDirect($getArr);
						break;
			case 'CSE' :
						$this->reDirect($getArr);
						break;
			case 'IMP' :
			case 'IMPAJAX' :
						$this->reDirect($getArr);
						break;
			case 'ENS' :
						$this->reDirect($getArr);
						break;
			case 'PAC' :
						$this->reDirect($getArr);
						break;
			case 'LDAP':
						$this->reDirect($getArr);
						break;
			default:

                  $screenParam = array('uniqcode' => $_GET['uniqcode'], 'VIEW' => $_GET['VIEW']);
                  $tokenGenerator = CSRFTokenGenerator::getInstance();
                  $tokenGenerator->setKeyGenerationInput($screenParam);
                  $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                  
						$form_creator = new FormCreator($getArr,$postArr);
						$form_creator ->formPath ='/view.php';

						$form_creator ->popArr['currentPage'] = $currentPage =(isset($postArr['pageNO'])) ? (int)$postArr['pageNO'] : 1;

						if (isset($postArr['captureState'])&& ($postArr['captureState']=="SearchMode"))
					    {

							$choice=$postArr['loc_code'];
						    $strName=trim($postArr['loc_name']);
						    $form_creator ->popArr['message'] = $this->getInfo(trim($getArr['uniqcode']),$currentPage,$strName,$choice, $getArr['sortField'], $getArr[$sortOrderFld], $esp);
					    } else  {

							$form_creator ->popArr['message'] = $this->getInfo(trim($getArr['uniqcode']),$currentPage, '', -1, $getArr['sortField'], $getArr[$sortOrderFld],  $esp);
					    }

				   		if (isset($postArr['captureState'])&& ($postArr['captureState']=="SearchMode")) {
							$form_creator ->popArr['temp'] = $this->countList(trim($getArr['uniqcode']), $strName, $choice, $esp);
						} else {
							$form_creator ->popArr['temp'] = $this->countList(trim($getArr['uniqcode']), '', -1, $esp);
						}
                  $form_creator ->popArr['token'] = $token;
						$form_creator->display();

						break;
		}
	}

    function delParser($indexCode,$arrList) {
         $screenParam = array('uniqcode' => $_GET['uniqcode'], 'VIEW' => 'MAIN');
         $tokenGenerator = CSRFTokenGenerator::getInstance();
         $tokenGenerator->setKeyGenerationInput($screenParam);
         $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
        $this->indexCode=$indexCode;

        switch ($this->indexCode)  {

        	case 'EST' :

        		$this->empstat = new EmploymentStatus();
            $res = false;
            if($token == $_POST['token']) {
               $res = $this->empstat->delEmpStat($arrList);
            }
        		break;

        	case 'JEM' :

        		$this->jobtit_empstat = new JobTitEmpStat();
        		$res = $this->jobtit_empstat->delJobTitEmpStat($arrList);
        		break;

        	case 'JOB' :

        		$this->jobtit = new JobTitle();
        		$res = $this->jobtit->delJobTitles($arrList);
        		if ($res) {
        		    $empInfo = new EmpInfo();
        		    $empInfo->resetJobTitile($arrList[0]);
        		}
            $res = false;
            if($token == $_POST['token']) {
               $res = $this->jobtit ->delJobTitles($arrList);
            }
        		break;

        	case 'SPC' :

				if (isset($arrList[0])) {
					try {
                  $res = false;
                  if($token == $_POST['token']) {
                     $res = JobSpec::delete($arrList[0]);
                  }
					} catch (JobSpecException $e) {
						$res = false;
					}
				}
        		break;

        	case 'CST' :

	            $this->compstruct = new CompStruct();
	            $res = $this->compstruct->delCompStruct($arrList);
	            break;

        	case 'LOC' :
	            $this->location = new models_eimadmin_Location();
               $res = false;
               if($token == $_POST['token']) {
                  $res = $this->location->delLocation($arrList);
               }
	            break;

			case 'SKI' :
               $this-> skills = new Skills();
               $res = false;
               if($token == $_POST['token']) {
                  $res = $this-> skills -> delSkills($arrList);
               }
				break;

			case 'ETH' :

				$this-> ethnicrace = new EthnicRace();
            $res = false;
            if($token == $_POST['token']) {
               $res = $this-> ethnicrace -> delEthnicRace($arrList);
            }
				break;

			case 'MEM' :
				$this-> membershiptype = new MembershipType();
            $res = false;
            if($token == $_POST['token']) {
               $res = $this-> membershiptype -> delMembershipType($arrList);
            }
				break;

		case 'NAT' :

			$this-> nationalityinfo = new NationalityInfo();
         $res = false;
         if($token == $_POST['token']) {
            $res = $this-> nationalityinfo -> delNationalityInfo($arrList);
         }
			break;

		case 'LAN':
         $res = false;
			$this-> languageinfo = new LanguageInfo();
         if($token == $_POST['token']) {
            $res = $this-> languageinfo -> delLanguageInfo($arrList);
         }

			break;

		case 'MME':
			$this-> membershipinformation = new MembershipInfo();
         $res = false;
         if($token == $_POST['token']) {
            $res = $this-> membershipinformation -> delMembershipInfo($arrList);
         }
			break;

		case 'SGR':

			$this-> salarygrade = new SalaryGrades();
         $res = false;
         if($token == $_POST['token']) {
            $res = $this-> salarygrade -> delSalaryGrades($arrList);
         }
			break;

    	case 'EDU':
         $screenParam = array('uniqcode' => $_GET['uniqcode'], 'VIEW' => 'MAIN');
         $tokenGenerator = CSRFTokenGenerator::getInstance();
         $tokenGenerator->setKeyGenerationInput($screenParam);
         $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
         $res = false;
         if($token == $_POST['token']) {
            $this-> qual = new Education();
            $res = $this-> qual -> delEducation($arrList);
         }
			break;

		case 'EEC':

			$this-> EEOJobCat = new EEOJobCat();
         $res = false;
         if($token == $_POST['token']) {
            $res = $this-> EEOJobCat -> delEEOJobCat($arrList);
         }
			break;

        case 'LIC':
         $screenParam = array('uniqcode' => $_GET['uniqcode'], 'VIEW' => 'MAIN');
         $tokenGenerator = CSRFTokenGenerator::getInstance();
         $tokenGenerator->setKeyGenerationInput($screenParam);
         $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                     
			$this-> Licenses = new Licenses();
         $res = false;
         if($token == $_POST['token']) {
            $res = $this-> Licenses -> delLicenses($arrList);
         }
			break;

		case 'CUS':

			$this-> customers = new Customer();
         $res = false;
         if($token == $_POST['token']) {
            $res = $this->customers->deletewrapperCustomer($arrList);
         }
			break;

		case 'CTM':		$this->customFields = new CustomFields();
						if (isset($arrList[0])) {
							try {
                        $res = false;
                        if($token == $_POST['token']) {
                           $this->customFields->deleteFields($arrList[0]);
                           $res = true;
                        }
							    if (is_array($arrList) && count($arrList) > 0) {
						            $mediator = EventMediator::instance();
						            $mediator->notify(EventMediator::POST_CUSTOM_FIELD_DELETE_EVENT, array('customFieldIds' => $arrList[0]));
								}
								
							} catch (CustomFieldsException $e) {
								$res = false;
							}
						} else {
							$res = false;
						}
						break;
		case 'CEX':		if (isset($arrList[0])) {
							try {
                        $res = false;
                        if($token == $_POST['token']) {
                           CustomExport::deleteExports($arrList[0]);
                           $res = true;
                        }
							} catch (CustomExportException $e) {
								$res = false;
							}
						} else {
							$res = false;
						}
						break;

		case 'CIM':		if (isset($arrList[0])) {
							try {
                        $res = false;
                        if($token == $_POST['token']) {
                           CustomImport::deleteImports($arrList[0]);
                           $res = true;
                        }
							} catch (CustomImportException $e) {
								$res = false;
							}
						} else {
							$res = false;
						}
						break;

		case 'PRJ':

			$this-> Projects = new Projects();
         $res = false;
         if($token == $_POST['token']) {
            $res = $this->Projects->deletewrapperProjects($arrList) ;
         }
			break;

		case 'PAD': // Project admins

			$gw = new ProjectAdminGateway();
			$projectId = $_GET['id'];
			$res = $gw->removeAdmins($projectId, $arrList[0]) ;
			break;

		case 'PAC': // Project activity

			$authorizeObj = new authorize($_SESSION['empID'], $_SESSION['isAdmin']);
			$projectId = $_GET['projectId'];

			if ($authorizeObj->isAdmin() || $authorizeObj->isProjectAdminOf($projectId)) {
				$res = ProjectActivity::deleteActivities($arrList[0], $projectId) ;
			}
			break;

		case 'USR':

			$this-> users = new Users();
         $res = false;
          if($token == $_POST['token']) {
            $res = $this-> users -> delUsers($arrList);
          }
			
			break;

		case 'USG':

			$this-> usergroups = new UserGroups();
         $res = false;
         if($token == $_POST['token']) {
            $res = $this-> usergroups -> delUserGroups($arrList);
         }
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

		case 'SPC' :
			return JobSpec::getListForView($pageNO,$schStr,$mode, $sortField, $sortOrder);

		case 'LOC' :

			$this-> location = new models_eimadmin_Location();
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

		case 'CUS' :

			$this-> customer = new Customer();
			$message = $this->customer->getListofCustomers($pageNO, $schStr, $mode, $sortField, $sortOrder);

			return $message;

		case 'CTM' :

			$this-> customFields = new CustomFields();
			$message = $this->customFields->getCustomerFieldListForView($pageNO, $schStr, $mode, $sortField, $sortOrder);

			return $message;

		case 'CEX' :

			return CustomExport::getCustomExportListForView($pageNO, $schStr, $mode, $sortField, $sortOrder);

		case 'CIM' :

			return CustomImport::getCustomImportListForView($pageNO, $schStr, $mode, $sortField, $sortOrder);

		case 'PRJ' :

			$this-> projects = new Projects();
			$message = $this->projects->getListOfProjectsStr($pageNO, $schStr, $mode, $sortField, $sortOrder);

			return $message;

		case 'USR' :

			$this-> user = new Users();
			$message = $this->user-> getListOfUsers($pageNO,$schStr,$mode, $sortField, $sortOrder, $esp);

			return $message;
		}
	}
/*
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

			$this->headingInfo = array ('Hierarchy Definitiion ID','Hierarchy Definition Name',1,'Hierarchy Definition','Deletion might affect Company Hierarchy!');
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

		//case 'LAN' :

		//	$this->headingInfo = array ('Language ID','Language Name',1,'Languages','Deletion might affect Employee Language');
		//	return $this->headingInfo;

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

*/
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

		case 'SPC' :

			return JobSpec::getCount($schStr,$mode);

		case 'LOC' :

			$this-> location = new models_eimadmin_Location();
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

		case 'CUS' :

			$this-> custormers = new Customer();
			$message = $this->custormers->countcustomerID($schStr,$mode);

			return $message;

		case 'CTM' :

			$customerFields = new CustomFields();
			$list = $customerFields->getCustomFieldList();
			$message = count($list);

			return $message;

		case 'CEX' :

			$list = CustomExport::getCustomExportList();
			return count($list);

		case 'CIM' :

			$list = CustomImport::getCustomImportList();
			return count($list);

		case 'PRJ' :

			$this-> projects = new Projects();
			$message = $this-> projects -> countprojectID($schStr,$mode);

			return $message;

		case 'USR' :

			$this-> users = new Users();
			$message = $this-> users -> countUsers($schStr,$mode, $esp);

			return $message;
		}
	}

   
	function addData($index,$object,$noRedirect = false) {
		try {
			switch ($index) {

				case 'EST'  :	
                           $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => 'addmode');
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                           
                           $empstat = new EmploymentStatus();
									$empstat = $object;
                           $res = false;
                           if($token == $_POST['token']) {
                              $res = $empstat ->addEmpStat();
                           }
									break;

				case 'JEM'  :		$jobtit_empstat = new JobTitEmpStat();
									$jobtit_empstat = $object;
									$res = $jobtit_empstat ->addJobTitEmpStat();
									break;

				case 'JOB'  :	
                           $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => 'addmode');
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                           
                           $jobtit = new JobTitle();
									$jobtit = $object;
                           $res = false;
                           if($token == $_POST['token']) {
                              $res = $jobtit ->addJobTitles();
                           }
									$id = $jobtit->getJobId();
									break;

				case 'SPC'  :		$jobSpec = $object;
                           $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => 'addmode');
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                           $res = false;
									try {
                              if($token == $_POST['token']) {
                                 $res = $jobSpec->save();
                              }
									} catch(JobSpecException $e) {
										$res = false;
									}
									break;

				case 'CST'  :
                           $screenParam = array('uniqcode' => $_GET['uniqcode'], 'VIEW' => 'MAIN');
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));

									$compstruct = new CompStruct();
									$compstruct = $object;
                           $res = false;
                           if($token == $_POST['token']) {
                              $res = $compstruct -> addCompStruct();
                           }
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

				case 'LOC'  :	$loc = new models_eimadmin_Location();
                           $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => 'addmode');
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                           $res = false;
                           if($token == $_POST['token']) {
                              $loc = $object;
                              $res = $loc -> addLocation();
                           }
									break;

          case 'TCP'  :
                              $screenParam = array('uniqcode' => $_GET['uniqcode'], 'action' => 'add');
                              $tokenGenerator = CSRFTokenGenerator::getInstance();
                              $tokenGenerator->setKeyGenerationInput($screenParam);
                              $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                              $res = false;
                              if($token == $_POST['token']) {
                                 $tcp = new CompProperty();
                                 $tcp = $object;
                                 $res = $tcp -> addProperty();
                              }
                              break;

				case 'MEM'  :
                              $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => 'addmode');
                              $tokenGenerator = CSRFTokenGenerator::getInstance();
                              $tokenGenerator->setKeyGenerationInput($screenParam);
                              $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                           $memtype = new MembershipType();
									$memtype = $object;
                           $res = false;
                           if($token == $_POST['token']) {
                              $res = $memtype -> addMembershipType();
                           }
									break;

				case 'NAT'  :		$natinfo = new NationalityInfo();
									$natinfo = $object;
                           $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => 'addmode');
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                           $res = false;
                           if($token == $_POST['token']) {
                              $res = $natinfo -> addNationalityInfo();
                           }
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
                           $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => 'addmode');
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                           
									try {
                              $res = false;
                              if($token == $_POST['token']) {
                                 $salgread -> addSalaryGrades();
                                 $res = true;
                              }
									} catch (SalaryGradesException $e) {
										if ($e->getCode() == SalaryGradesException::DUPLICATE_SALARY_GRADE){
											$showMsg = "DUPLICATE_NAME_FAILURE";
										} elseif ($e->getCode() == SalaryGradesException::UNKNOWN_EXCEPTION) {
											$showMsg = "ADD_FAILURE";
										}
										$res = false;
									}
									$id = $salgread->getSalGrdId();
									break;

				case 'SKI'  :		$skill = new Skills();
                           $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => 'addmode');
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));

									$skill = $object;
                           $res = false;
                           if($token == $_POST['token']) {
                              $res = $skill -> addSkills();
                           }
									break;

				case 'ETH'  :		$ethnicrace = new EthnicRace();
                           $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => 'addmode');
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                           
									$ethnicrace = $object;
                           $res = false;
                           if($token == $_POST['token']) {
                              $res = $ethnicrace -> addEthnicRace();
                           }
									break;

				case 'EXA'  :		$extcainfo = new ExtraCurActInfo();
									$extcainfo = $object;
									$res = $extcainfo -> addExtraCurActInfo();
									break;

				case 'MME'  :		$membinfo = new MembershipInfo();
									$membinfo = $object;
                           $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => 'addmode');
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                           $res = false;
                           if($token == $_POST['token']) {
                              $res = $membinfo -> addMembershipInfo();
                           }
									break;

				case 'JDK'  :		$jdkr = new JDKra();
									$jdkr  = $object;
									$res = $jdkr  -> addJDKra();
									break;

				case 'LAN'  :		$laninfo = new LanguageInfo();
									$laninfo  = $object;
                           $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => 'addmode');
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                           $res = false;
                           if($token == $_POST['token']) {
                              $res = $laninfo -> addLanguageInfo();
                           }
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

				case 'EDU'  :
                           $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => 'addmode');
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));

                           $edu = new Education();
									$edu = $object;
                           $res = false;
                           if($token == $_POST['token']) {
                              $res = $edu -> addEducation();
                           }
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

				case 'DIS'  :		$disinfo = new DistrictInfo();
									$disinfo = $object;
									$res = $disinfo -> addDistrictInfo();
									break;

				case 'EEC'  :		$eeojobcat = new EEOJobCat();
                           $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => 'addmode');
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                     
									$eeojobcat = $object;
                           $res = false;
                           if($token == $_POST['token']) {
                              $res = $eeojobcat -> addEEOJobCat();
                           }
									break;

				case 'LIC'  :	
                           $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => 'addmode');
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                           $licenses = new Licenses();
									$licenses = $object;
                           $res = false;
                           if($token == $_POST['token']) {
                              $res = $licenses -> addLicenses();
                           }
									break;


				case 'CUS'  :		$customer = new Customer();
									$customer = $object;
                           $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => 'addmode');
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                           $res = false;
                           if($token == $_POST['token']) {
                              $res= $customer->addCustomer();
                           }
									$id= $customer->getCustomerId();

									break;

				case 'CTM'  :
                           $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => 'addmode');
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                           $res = false;
                           $customField = $object;
                           
									try {
                              if($token == $_POST['token']) {
                                 $customField->addCustomField();
                                 $res = true;
                              }
									} catch (CustomFieldsException $e) {
										$res = false;
									}
									$id= $customField->getFieldNumber();

									break;

				case 'CEX'  :		$customExport = $object;
                           $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => 'addmode');
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                           
									try {
                              $res = false;
                              if($token == $_POST['token']) {
                                 $customExport->save();
                                 $res = true;
                              }
									} catch (CustomExportException $e) {
										if ($e->getCode() == CustomExportException::DUPLICATE_EXPORT_NAME){
											$showMsg = "DUPLICATE_NAME_FAILURE";
										}
										$res = false;
									}
									$id = $customExport->getId();
									break;

				case 'CIM'  :		$customImport = $object;
                           $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => 'addmode');
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                     
									try {
                              $res = false;
                              if($token == $_POST['token']) {
                                 $customImport->save();
                                 $res = true;
                              }
									} catch (CustomImportException $e) {
										if ($e->getCode() == CustomImportException::DUPLICATE_IMPORT_NAME){
											$showMsg = "DUPLICATE_NAME_FAILURE";
										}
										if ($e->getCode() == CustomImportException::COMPULSARY_FIELDS_NOT_ASSIGNED){
											$showMsg = "COMPULSARY_FIELDS_NOT_ASSIGNED_FAILURE";
										}
										$res = false;
									}
									$id = $customImport->getId();
									break;
				case 'IMP'  :
									$authorizeObj = new authorize($_SESSION['empID'], $_SESSION['isAdmin']);
									if ($authorizeObj->isAdmin()) {
										$csvImport = $object;

										try {
											$res = $csvImport->handleUpload();
										} catch (CSVImportException $e) {
											if ($e->getCode() == CSVImportException::IMPORT_DATA_NOT_RECEIVED) {
												$showMsg = "IMPORT_FAILURE";
											}
											$res = false;
										} catch (CSVSplitterException $e) {
                                            if (($e->getCode() == CSVSplitterException::DIRECTORY_NOT_WRITABLE)||($e->getCode() == CSVSplitterException::FILES_NOT_WRITABLE)) {
                                                $showMsg = "IMPORT_TEMPDIR_NOT_WRITABLE_FAILURE";
                                                $res = false;
                                            }

                                            if (($e->getCode() == CSVSplitterException::SOURCE_FILE_NOT_FOUND) || ($e->getCode() == CSVSplitterException::SOURCE_FILE_NOT_READABLE)){
                                                $showMsg = "IMPORT_FILE_PERMISSION_ERROR";
                                                $res = false;
                                            }

                                            if (($e->getCode() == CSVSplitterException::SOURCE_FILE_EMPTY)){
                                                $showMsg = "IMPORT_FILE_EMPTY_ERROR";
                                                $res = false;
                                            }

                                        }
									}
									break;

				case 'PAD'  :		$projectAdmin = $object;
									$id = $projectAdmin->getProjectId();
									$gw = new ProjectAdminGateway();
									$res = $gw->addAdmin($id, $projectAdmin->getEmpNumber());

									break;

				case 'PRJ'  :	
                     $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => 'addmode');
                     $tokenGenerator = CSRFTokenGenerator::getInstance();
                     $tokenGenerator->setKeyGenerationInput($screenParam);
                     $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                     
                           $project = $object;
                           $res = false;
                           if($token == $_POST['token']) {
                              $res= $project->addProject();
                           }
									$id= $project->getProjectId();
									break;

				case 'PAC'  :		$projectActivity = $object;
									$id = $projectActivity->getProjectId();
									$name = $projectActivity->getName();
									$res = true;
									$activities = ProjectActivity::getActivitiesWithName($id, $name);

									if (empty($activities)) {
										try {
											$projectActivity->save();
										} catch (ProjectActivityException $e) {
											$res = false;
										}
									}

									break;

				case 'USR'  :	
                           $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => 'addmode', 'isAdmin' => $_GET['isAdmin']);
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                           
                           $users = new Users();
									$users = $object;
                           $res = false;
                           if($token == $_POST['token']) {
                              $res = $users -> addUsers();
                           }
									break;

				case 'USG'  :	
                           $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => 'addmode');
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                           
                           $usergroups = new UserGroups();
									$usergroups = $object;
                           $res = false;
                           if($token == $_POST['token']) {
                              $res = $usergroups -> addUserGroups();
                           }
									$id = $usergroups ->getUserGroupID();
									break;


			}
		} catch (Exception $e) {
			$res = false;
			if ($e->getCode() == 1) {
				$showMsg="DUPLICATE_NAME_FAILURE";
			}
		}
			// Checking whether the $message Value returned is 1 or 0
			if ($res) {

				switch($index) {

					case 'IMP' :
								$this->reDirect($_GET, $res);
								break;
					case 'CEX' : // Go to CSV heading Define page
								 header("Location: ./CentralController.php?uniqcode=CHD&id=$id");
								 break;

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

					case 'PAD' : // Project admin page. Fall through to PRJ case below.

					case 'PRJ' :
								header("Location: ./CentralController.php?message=ADD_SUCCESS&uniqcode=PRJ&id=$id&capturemode=updatemode");
								break;
					case 'PAC' :
								header("Location: ./CentralController.php?message=ADD_SUCCESS&uniqcode=PAC&projectId=$id");
								break;

					case 'EST' :

					case 'LOC' :
								if($noRedirect)
									break;
                    case 'TCP' :
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

				$esp = isset($_GET['isAdmin'])? ('&isAdmin='.$_GET['isAdmin']) : '';

				if (!isset($showMsg) || empty($showMsg)) {
					$showMsg = "DUP_USERS_FAILURE";
				}

				$uniqcode = $index;
				header("Location: ./CentralController.php?msg=$showMsg&capturemode=addmode&uniqcode={$uniqcode}{$esp}");
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
		try {
			$extraParams = "";

			switch ($index) {

				case 'EST'  :	
                           $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => 'updatemode', 'id' => $_GET['id']);
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                           
                           $empstat = new EmploymentStatus();
									$empstat = $object;
                           $res = false;
                           if($token == $_POST['token']) {
                              $res = $empstat -> updateEmpStat();
                           }
									break;

				case 'JOB'  :	
                           $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => 'updatemode', 'id' => $_GET['id']);
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));

                           $jobtit = new JobTitle();
									$jobtit = $object;
                           $res = false;
                           if($token == $_POST['token']) {
                              $res = $jobtit -> updateJobTitles();
                           }
									break;

				case 'SPC'  :		$jobSpec = $object;
                           $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => 'updatemode', 'id' => $_GET['id']);
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                           
									try {
                              $res = false;
                              if($token == $_POST['token']) {
                                 $res = $jobSpec->save();
                              }
									} catch(JobSpecException $e) {
										$res = false;
									}
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

				case 'LOC'  :
                           $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => 'updatemode');
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                           $loc = new models_eimadmin_Location();
									$loc = $object;
                           $res =  false;
                           if($token == $_POST['token']) {
                              $res = $loc -> updateLocation();
                           }
									break;

				case 'MEM'  :
                           $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => 'updatemode', 'id' => $_GET['id']);
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                           $memtype = new MembershipType();
									$memtype = $object;
                           $res = false;
                           if($token == $_POST['token']) {
                              $res = $memtype -> updateMembershipType();
                           }
									break;

				case 'NAT'  :		$natinfo = new NationalityInfo();
									$natinfo = $object;
                           $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => 'updatemode', 'id' => $_GET['id']);
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                           $res = false;
                           if($token == $_POST['token']) {
                              $res = $natinfo -> updateNationalityInfo();
                           }
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
                           $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => 'updatemode');
                           if(isset($_GET['id'])) {
                              $screenParam['id'] = $_GET['id'];
                           }
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                           $res = false;
                           if($token == $_POST['token']) {
                              $res = $salgread -> updateSalaryGrades();
                           }
									break;

				case 'SKI'  :		$skill = new Skills();
                           $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => $_GET['capturemode']);
                           if(isset($_GET['id'])) {
                              $screenParam['id'] = $_GET['id'];
                           }
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
									$skill = $object;
                           $res = false;
                           if($token == $_POST['token']) {
                              $res = $skill -> updateSkills();
                           }
									break;

				case 'ETH'  :		$ethnicrace = new EthnicRace();
									$ethnicrace = $object;
                           $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => $_GET['capturemode']);
                           if(isset($_GET['id'])) {
                              $screenParam['id'] = $_GET['id'];
                           }
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                           $res = false;
                           if($token == $_POST['token']) {
                              $res = $ethnicrace -> updateEthnicRace();
                           }
									break;

				case 'EXA'  :		$extcainfo = new ExtraCurActInfo();
									$extcainfo = $object;
									$res = $extcainfo -> updateExtraCurActInfo();
									break;

				case 'MME'  :		$membinfo = new MembershipInfo();
                           $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => $_GET['capturemode']);
                           if(isset($_GET['id'])) {
                              $screenParam['id'] = $_GET['id'];
                           }
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
									$membinfo = $object;
                           $res = false;
                           if($token == $_POST['token']) {
                              $res = $membinfo -> updateMembershipInfo();
                           }
									break;

				case 'JDK'  :		$jdkr = new JDKra();
									$jdkr = $object;
									$res = $jdkr -> updateJDKra();
									break;

				case 'LAN'  :		$laninfo = new LanguageInfo();
									$laninfo  = $object;

                           $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => $_GET['capturemode']);
                           if(isset($_GET['id'])) {
                              $screenParam['id'] = $_GET['id'];
                           }
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                           $res = false;
                           if($token == $_POST['token']) {
                              $res = $laninfo -> updateLanguageInfo();
                           }
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

				case 'EDU'  :	
                           $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => $_GET['capturemode'], 'id' => $_GET['id']);
                           $edu = new Education();
									$edu = $object;
                           $res = false;
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                           if($token == $_POST['token']) {
                              $res = $edu -> updateEducation();
                           }
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

				case 'DIS'  :		$disinfo = new DistrictInfo();
									$disinfo = $object;
									$res = $disinfo -> updateDistrictInfo();
									break;

				case 'EEC'  :	
                           $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => 'updatemode', 'id' => $_GET['id']);
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                           
                           $eeojobcat = new EEOJobCat();
									$eeojobcat = $object;
                           $res = false;
                           if($token == $_POST['token']) {
                              $res = $eeojobcat -> updateEEOJobCat();
                           }
									break;


				case 'LIC'  :	
                           $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => $_GET['capturemode'], 'id' => $_GET['id']);
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                           
                           $licenses = new Licenses();
									$licenses = $object;
                           $res = false;
                           if($token == $_POST['token']) {
                              $res = $licenses -> updateLicenses();
                           }
									break;

				case 'CUS'  :
                           $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => $_GET['capturemode'], 'id' => $_GET['id']);
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                           
                           $customers = new Customer();
									$customers = $object;
                           $res = false;
                           if($token == $_POST['token']) {
                              $res = $customers->updateCustomer();
                           }
									break;

				case 'CTM'  :		$customField = $object;
                           $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => 'updatemode', 'id' => $_GET['id']);
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                           $res = false;
                           
									try {
                              if($token == $_POST['token']) {
                                 $customField->updateCustomField();
                                 $res = true;
                              }
									} catch (CustomFieldsException $e) {
										$res = false;
									}
									break;

				case 'CHD'  :	    // Fall through to 'CEX' below

				case 'CEX'  :
                           $screenParam = array('uniqcode' => $index, 'id' => $_GET['id']);
                           if(isset($_GET['capturemode'])) {
                              $screenParam['capturemode'] = 'updatemode';
                           }
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));

                           $customExport = $object;
									try {
                              $customExport->save();
                              $res = true;
									} catch (CustomExportException $e) {
										if ($e->getCode() == CustomExportException::DUPLICATE_EXPORT_NAME){
											$showMsg = "DUPLICATE_NAME_FAILURE";
										}
										$res = false;
									}
									break;

				case 'CIM'  :		$customImport = $object;
                           $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => 'updatemode');
                           if(isset($_GET['id'])) {
                              $screenParam['id'] = $_GET['id'];
                           }

                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                           
									try {
                              $res = false;
                              if($token == $_POST['token']) {
                                 $customImport->save();
                                 $res = true;
                              }
									} catch (CustomImportException $e) {
										if ($e->getCode() == CustomImportException::DUPLICATE_IMPORT_NAME){
											$showMsg = "DUPLICATE_NAME_FAILURE";
										}
										if ($e->getCode() == CustomImportException::COMPULSARY_FIELDS_NOT_ASSIGNED){
											$showMsg = "COMPULSARY_FIELDS_NOT_ASSIGNED_FAILURE";
										}
										$res = false;
									}
									break;

				case 'PRJ'  :
                           $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => $_GET['capturemode'], 'id' => $_GET['id']);
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                           
                           $projects = new Projects();
									$projects = $object;
                           $res = false;
                           if($token == $_POST['token']) {
                              $res = $projects->updateProject();
                           }
									break;

                case 'TCP'  : 
                           $screenParam = array('uniqcode' => $_GET['uniqcode'], 'VIEW' => 'MAIN');
                           if(isset($_GET['capturemode'])) {
                              unset($screenParam['VIEW']);
                              $screenParam['action'] = "edit";
                           }
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                           $res = false;
                           if($token == $_POST['token']) {
                              $property = new CompProperty();
                              $property = $object;

                              if($property->getEditPropFlag()==false)
                                  $res = $property->editPropertyList();
                              else
                                  $res = $property->editProperty($id);
                           }
									break;

				case 'PAC'  :		$projectActivity = $object;
									$authorizeObj = new authorize($_SESSION['empID'], $_SESSION['isAdmin']);
									$projectId = $projectActivity->getProjectId();

									$res = true;
									if ($authorizeObj->isAdmin() || $authorizeObj->isProjectAdminOf($projectId)) {
										try {
											$projectActivity->save();
											$extraParams = "&projectId={$projectId}";
										} catch (ProjectActivityException $e) {
											$res = false;
										}
									}

									break;

				case 'USR'  :		$users = new Users();
									$users = $object;

                           $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => $_GET['capturemode'], 'isAdmin' => $_GET['isAdmin']);
                           if(isset($_GET['id'])) {
                              $screenParam['id'] = $_GET['id'];
                           }
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                           $res = false;
                           if($token == $_POST['token']) {
                              $res = $users -> updateUsers();
                           }
									break;

				case 'USG'  :  $usergroups = new UserGroups();
									$usergroups = $object;
                           
                           $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => $_GET['capturemode']);
                           if(isset($_GET['id'])) {
                              $screenParam['id'] = $_GET['id'];
                           }
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));

                           $res = false;
                           if($token == $_POST['token']) {
                              $res = $usergroups->updateUserGroups();
                           }
									break;

				case 'EMX'  :	
                           $screenParam = array('uniqcode' => $_GET['uniqcode'], 'VIEW' => 'MAIN');
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                           $res = false;
                           if($token == $_POST['token']) {
                              $emailConfig = new EmailConfiguration();
                              $emailConfig = $object;
                              $res = (bool) $emailConfig->reWriteConf();
                              $testEmail = $emailConfig->getTestEmail();
                              $testEmailType = $emailConfig->getTestEmailType();
                           }
									break;
				case 'ENS'  :
                           $screenParam = array('uniqcode' => $_GET['uniqcode'], 'VIEW' => 'MAIN');
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));

                           $emailNotifacationConfigs = new EmailNotificationConfiguration($_SESSION['user']);
									$emailNotifacationConfigs = $object;
									$res = true;
									foreach ($emailNotifacationConfigs as $emailNotifacationConfig) {
                              $resp = false;
                              if($token == $_POST['token']) {
                                 $resp = $emailNotifacationConfig->updateNotificationStatus();
                              }
										if (!$resp) {
											$res = $resp;
										}
									}
									break;

				case 'LDAP'	:		if ($_SESSION['ldap'] == "enabled") {
										$ldapDetails = new LdapDetails();
										$ldapDetails = $object;
										$res = $ldapDetails->updateLdapDetails();
									}
									break;
			}
		} catch (Exception $e) {
			if ($e->getCode() == 1) {
				$res=false;
				$showMsg="DUPLICATE_NAME_FAILURE";
			}
		}
			// Checking whether the $message Value returned is 1 or 0
			if ($res) {

				switch ($index) {

					case 'CEX' : // Go to CSV heading Define page
								 header("Location: ./CentralController.php?uniqcode=CHD&id=$id");
								 break;

					case 'CHD' : $showMsg = "UPDATE_SUCCESS";
								 $esp = isset($_GET['isAdmin'])? ('&isAdmin='.$_GET['isAdmin']) : '';
								 header("Location: ./CentralController.php?message=$showMsg&uniqcode=CEX&VIEW=MAIN$esp{$extraParams}");
								 break;

					case 'CST' : break;

					case 'EMX' :
								$emailConfig = new EmailConfiguration();
								$showMsg = "";

								/*
								 * $testEmail and $testEmail should be initialized when writing the mailConfig file
								 * Getting the value for $testEmail should not be from the newlt created EmailConfiguration
								 * object, since test mail value is not written to the physical file
								 */
								if (isset($testEmail) && !empty($testEmail) && isset($testEmailType) && !empty($testEmailType)) {
									$emailConfig->setTestEmail($testEmail);
									$emailConfig->setTestEmailType($testEmailType);
									if ($emailConfig->sendTestEmail()) {
								 		$showMsg = "TEST_EMAIL_SUCCESS";
								 	} else {
								 		$showMsg = "TEST_EMAIL_FAILIURE";
								 	}
								}

								header("Location:./CentralController.php?uniqcode=EMX&VIEW=MAIN&msg=$showMsg");
								break;

					case 'SGR' :
								$showMsg = "UPDATE_SUCCESS";

								header("Location:".$_POST['referer']."&msg=$showMsg");
								break;

					case 'EST' :
					case 'CUR' :
								if($noRedirect) {
									break;
								}

					default : 	$showMsg = "UPDATE_SUCCESS"; //If $message is 1 setting up the

								$pageNoQueryString = isset($_REQUEST['pageNo']) ? '&pageNo=' . $_REQUEST['pageNo'] : '';

								$uniqcode = $index;

								$esp = isset($_GET['isAdmin'])? ('&isAdmin='.$_GET['isAdmin']) : '';

								header("Location: ./CentralController.php?message=$showMsg&uniqcode=$uniqcode&VIEW=MAIN$esp{$extraParams}$pageNoQueryString");
				}

			} else {
				if (!isset($showMsg) || empty($showMsg)) {
					$showMsg = "UPDATE_FAILURE";
				}

				$uniqcode = $index;

				$esp = isset($_GET['isAdmin'])? ('&isAdmin='.$_GET['isAdmin']) : '';

				header("Location: ./CentralController.php?msg=$showMsg&id=$id&capturemode=updatemode&uniqcode=$uniqcode$esp{$extraParams}");
			}
	}


	/**
	 * Run CSV Export
	 *
	 * @param string $exportType The export type
	 */
	public function exportCSV($exportType) {

		$authorizeObj = new authorize($_SESSION['empID'], $_SESSION['isAdmin']);

		if ($authorizeObj->isAdmin()) {
			$csvExport = new CSVExport();
			$csvExport->exportData($exportType);
		}
	}

	public function importData($fileName, $importType) {
		$authorizeObj = new authorize($_SESSION['empID'], $_SESSION['isAdmin']);

		if ($authorizeObj->isAdmin()) {
			$csvImport = new CSVImport();
			$csvImport->setImportType($importType);

			try {
				$res = $csvImport->importData($fileName);
			} catch (CSVImportException $e) {
				if ($e->getCode() == CSVImportException::IMPORT_DATA_NOT_RECEIVED) {
					$showMsg = "IMPORT_FAILURE";
				}
				$res = false;
			}

			if ($res != false) {
				CSVSplitter::deleteTempFile($fileName);

				$response[] = $res->getNumImported();
				$response[] = $res->getNumFailed();
				$response[] = $res->getNumSkipped();

				$results = $res->getImportResults();
				$failures = array();

				$i = 1;

				foreach ($results as $result) {
					$status = $result->getStatus();
					if ($status != CSVImport::IMPORTED && $status != CSVImport::SKIPPED_HEADER) {
						$failures[] = array($i, $status, $result->getComments());
					}

					if ($status != CSVImport::SKIPPED_HEADER) {
						$i++;
					}
				}

				if (count($failures) > 0) {
					$response[] = $failures;
				}

				AjaxCalls::sendResponse($response, false, AjaxCalls::NON_XML_MULTI_LEVEL_MODE);

			} else {

			}

		}

	}

	function assignData($index,$object,$action) {

			switch ($index) {

				case 'GEN'  :
                           $screenParam = array('uniqcode' => $index, 'VIEW' => 'MAIN');
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                           if($token == $_POST['token']) {
                              $geninfo = new GenInfo();
                              $geninfo = $object;
                              $geninfo->updateGenInfo();
                           }
									break;

				case 'JEM'  :		$jobtit_empstat = new JobTitEmpStat();
									$jobtit_empstat = $object;
									$jobtit_empstat->addJobTitEmpStat();

									break;

				case 'CST'  :  $comphier = new CompHierachy();
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
                           $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => 'updatemode');
                           if(isset($_GET['id'])) {
                              $screenParam['id'] = $_GET['id'];
                           }
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                           
									$salcud = $object;
                           if($token == $_POST['token']) {
                              if($action == 'ADD')
                                 $salcud->addSalCurDet();
                              elseif($action == 'EDIT')
                                 $salcud->updateSalCurDet();
                           }
									break;

				case 'RTG'  :		$ratgrd = new RatingGrade();
									$ratgrd = $object;
									if($action == 'ADD') {
										$ratgrd->setRatGrdId($ratgrd->getLastRecord());
										$ratgrd->addRatGrd();
									} elseif($action == 'EDIT')
										$ratgrd->updateRatGrd();
									break;

				case 'UGR'  :	
                           $screenParam = array('uniqcode' => $_GET['uniqcode'], 'id' => $_GET['id']);
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                           
                           $ugrights = new Rights();
									$ugrights = $object;
                           if($token == $_POST['token']) {
                              if($action == 'ADD')
                                 $ugrights->addRights();
                              elseif($action == 'EDIT')
                                 $ugrights->updateRights();
                           }
									break;
			}
	}

	function deleteData($index, $object) {

		switch ($index) {
			case 'CST': $compstruct = new CompStruct();
						$compstruct = $object;
						$res = $compstruct -> deleteCompStruct();
						break;
            case 'TCP':
                  $screenParam = array('uniqcode' => $_GET['uniqcode'], 'VIEW' => 'MAIN');
                  $tokenGenerator = CSRFTokenGenerator::getInstance();
                  $tokenGenerator->setKeyGenerationInput($screenParam);
                  $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                  
               $compProperty = new CompProperty();
               $compProperty = $object;
               $res = false;
               if($token == $_POST['token']) {
                  $res = $compProperty->deleteProperties();
               }

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
                           $screenParam = array('uniqcode' => $_GET['uniqcode'], 'id' => $_GET['id']);
                           $tokenGenerator = CSRFTokenGenerator::getInstance();
                           $tokenGenerator->setKeyGenerationInput($screenParam);
                           $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                           if($token == $_POST['token']) {
                              $urights->clearRights($getArr['id']);
                           }
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
							} elseif($getArr['captuIMPremode'] == 'updatemode') {
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

							if($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $country ->filterCountryInfo($getArr['id']);
							}

							break;

			case 'CUR' :	$form_creator ->formPath = '/templates/eimadmin/currencytypes.php';
							$currency = new CurrencyTypes();

							if($getArr['capturemode'] == 'updatemode') {
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
							} elseif($getArr['capturemIMPode'] == 'updatemode') {
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
							$loc = new models_eimadmin_Location();
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
         
                        $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => $_GET['capturemode']);
                        $tokenGenerator = CSRFTokenGenerator::getInstance();
                        $tokenGenerator->setKeyGenerationInput($screenParam);
                        $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
							$loc = new models_eimadmin_Location();
							$country = new CountryInfo();

							$form_creator->popArr['cntlist'] = $country->getCountryCodes();
                     $form_creator->popArr['token'] = $token;

							if($getArr['capturemode'] == 'updatemode') {

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
                     $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => $_GET['capturemode']);
                     if(isset($_GET['id'])) {
                        $screenParam['id'] = $_GET['id'];
                     }
                     $tokenGenerator = CSRFTokenGenerator::getInstance();
                     $tokenGenerator->setKeyGenerationInput($screenParam);
                     $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                     
							if($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $memtype ->filterMembershipType($getArr['id']);
							}
                     $form_creator ->popArr['token'] = $token;
							break;
			case 'EMX' ://print_r($_GET);die();
                     $screenParam = array('uniqcode' => $_GET['uniqcode'], 'VIEW' => 'MAIN');
                     $tokenGenerator = CSRFTokenGenerator::getInstance();
                     $tokenGenerator->setKeyGenerationInput($screenParam);
                     $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                     
                     $form_creator ->formPath = '/templates/eimadmin/emailConfiguration.php';
							$emailConfigObj = new EmailConfiguration();
							$form_creator ->popArr['editArr'] = $emailConfigObj;
                     $form_creator ->popArr['token'] = $token;
							break;
			case 'CSE' :	$form_creator ->formPath = '/templates/eimadmin/dataExport.php';
							$csvExport = new CSVExport();
							$form_creator ->popArr['exportTypes'] = $csvExport->getDefinedExportTypes();
							break;
			case 'IMP' :    if (isset($getArr['upload']) && $getArr['upload'] == 1) {
								$form_creator ->formPath = '/templates/eimadmin/dataUploadStatus.php';
								$form_creator ->popArr['uploadStatus'] = $object;
								$form_creator ->popArr['recordLimit'] = CSVSplitter::getRecordLimit();
								$form_creator ->popArr['delimiterLevels'] = AjaxCalls::getDelimiterLevelsArray(3);

							} else {
								$form_creator ->formPath = '/templates/eimadmin/dataImport.php';
								$csvImport = new CSVImport();
								$form_creator ->popArr['importTypes'] = $csvImport->getDefinedImportTypes();
							}
							break;

			case 'ENS' :
                     $screenParam = array('uniqcode' => $_GET['uniqcode'], 'VIEW' => 'MAIN');
                     $tokenGenerator = CSRFTokenGenerator::getInstance();
                     $tokenGenerator->setKeyGenerationInput($screenParam);
                     $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));

                     $form_creator->formPath = '/templates/eimadmin/emailNotificationConfiguration.php';
							$emailNotificationConfObj = new EmailNotificationConfiguration($_SESSION['user']);
							$form_creator ->popArr['editArr'] =$emailNotificationConfObj->fetchNotifcationStatus();
                     $form_creator ->popArr['token'] = $token;
							break;
			case 'NAT' :	$form_creator ->formPath = '/templates/eimadmin/nationalityinformation.php';
							$natinfo = new NationalityInfo();
                     $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => $_GET['capturemode']);
                     if(isset($_GET['id'])) {
                        $screenParam['id'] = $_GET['id'];
                     }
                     $tokenGenerator = CSRFTokenGenerator::getInstance();
                     $tokenGenerator->setKeyGenerationInput($screenParam);
                     $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
							if($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $natinfo ->filterNationalityInfo($getArr['id']);
							}
                     $form_creator ->popArr['token'] = $token;
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
                     $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => $_GET['capturemode']);
                     if(isset($_GET['id'])) {
                        $screenParam['id'] = $_GET['id'];
                     }
                     $tokenGenerator = CSRFTokenGenerator::getInstance();
                     $tokenGenerator->setKeyGenerationInput($screenParam);
                     $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                     
							$salgrade = new SalaryGrades();
							$salcurdet = new SalCurDet();
                     $form_creator ->popArr['token'] = $token;
                     
							if($getArr['capturemode'] == 'updatemode') {
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
                     
                     $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => $_GET['capturemode']);
                     if(isset($_GET['id'])) {
                        $screenParam['id'] = $_GET['id'];
                     }
                     $tokenGenerator = CSRFTokenGenerator::getInstance();
                     $tokenGenerator->setKeyGenerationInput($screenParam);
                     $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
							$skill = new Skills();

							if($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $skill ->filterSkills($getArr['id']);
							}
                     $form_creator->popArr['token'] = $token;
							break;

			case 'ETH' :	$form_creator ->formPath = '/templates/eimadmin/ethnicrace.php';
							$ethnicrace = new EthnicRace();
                     $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => $_GET['capturemode']);
                     if(isset($_GET['id'])) {
                        $screenParam['id'] = $_GET['id'];
                     }
                     $tokenGenerator = CSRFTokenGenerator::getInstance();
                     $tokenGenerator->setKeyGenerationInput($screenParam);
                     $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
							if($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $ethnicrace ->filterEthnicRace($getArr['id']);
							}
                     $form_creator ->popArr['token'] = $token;
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
                     $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => $_GET['capturemode']);
                     if(isset($_GET['id'])) {
                        $screenParam['id'] = $_GET['id'];
                     }
                     $tokenGenerator = CSRFTokenGenerator::getInstance();
                     $tokenGenerator->setKeyGenerationInput($screenParam);
                     $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                     $form_creator ->popArr['token'] = $token;

							if($getArr['capturemode'] == 'addmode') {
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
                     $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => $_GET['capturemode']);
                     if(isset($_GET['id'])) {
                        $screenParam['id'] = $_GET['id'];
                     }
                     $tokenGenerator = CSRFTokenGenerator::getInstance();
                     $tokenGenerator->setKeyGenerationInput($screenParam);
                     $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                     $form_creator ->popArr['token'] = $token;
							if($getArr['capturemode'] == 'updatemode') {
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
      
                     $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => $_GET['capturemode']);
                     if(isset($_GET['id'])) {
                        $screenParam['id'] = $_GET['id'];
                     }
                     $tokenGenerator = CSRFTokenGenerator::getInstance();
                     $tokenGenerator->setKeyGenerationInput($screenParam);
                     $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                     $form_creator ->popArr['token'] = $token;
							$edu = new Education();
							if($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $edu ->filterEducation($getArr['id']);
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

			case 'DIS' :	$form_creator ->formPath = '/templates/eimadmin/districtinformation.php';
							$disinfo = new DistrictInfo();
							$prov = new ProvinceInfo();
							$country = new CountryInfo();

							$form_creator ->popArr['countrylist'] = $country->getCountryCodes();

							if($getArr['capturemode'] == 'addmode') {

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
                     $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => $_GET['capturemode']);

                     //this is for add modify screens
                     if(isset($_GET['id'])) {
                        $screenParam['id'] = $_GET['id'];
                     }
                     $tokenGenerator = CSRFTokenGenerator::getInstance();
                     $tokenGenerator->setKeyGenerationInput($screenParam);
                     $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));

							if($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $eeojobcat->filterEEOJobCat($getArr['id']);
							}
                     $form_creator ->popArr['token'] = $token;
							break;

			case 'LIC' :	$form_creator ->formPath = '/templates/eimadmin/licenses.php';
							$licenses = new Licenses();

                     $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => $_GET['capturemode']);
                     if(isset($_GET['id'])) {
                        $screenParam['id'] = $_GET['id'];
                     }
                     $tokenGenerator = CSRFTokenGenerator::getInstance();
                     $tokenGenerator->setKeyGenerationInput($screenParam);
                     $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));

							if($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $licenses->filterLicenses($getArr['id']);
							}
                     $form_creator ->popArr['token'] = $token;
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
                     $screenParam = array('uniqcode' => $_GET['uniqcode'], 'VIEW' => 'MAIN');
                     $tokenGenerator = CSRFTokenGenerator::getInstance();
                     $tokenGenerator->setKeyGenerationInput($screenParam);
                     $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                     
							$locations = new models_eimadmin_Location();
							$countries = new CountryInfo();

                     $form_creator->popArr['token']     = $token;
							$form_creator->popArr['locations'] = $locations->getLocCodes();
							$form_creator->popArr['countries'] = $countries->getCountryCodes ();

							break;

            case 'TCP' : $form_creator->formPath = '/templates/eimadmin/companyProperty.php';
                     $screenParam = array('uniqcode' => $_GET['uniqcode'], 'VIEW' => 'MAIN');

                     //this is for add modify screens
                     if(isset($_GET['action'])) {
                        $screenParam['action'] = $_GET['action'];
                        unset($screenParam['VIEW']);
                     } 
                     $tokenGenerator = CSRFTokenGenerator::getInstance();
                     $tokenGenerator->setKeyGenerationInput($screenParam);
                     $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));

            				$authorizeObj = new authorize($_SESSION['empID'], $_SESSION['isAdmin']);

                            if (!isset($_GET['action'])) {
                                $empInfo = new EmpInfo();
                                if ($authorizeObj->isAdmin()) {
                                	$form_creator->popArr['emplist'] = $empInfo->getListofEmployee();
                                } else {
                                	$empReportTo = new EmpRepTo();
									$form_creator->popArr['emplist'] = $empReportTo->getEmpSubDetails($_SESSION['empID']);
									/* An element of the value from $empInfo->getListofEmployee() will contain
									 * an array with 6 elements. Values from $empReportTo->getEmpSubDetails() will
									 * only contain 3 elements. But there won't be a problem because only first 3 values
									 * will be used in the template
									 */
                                }
                            }

                            $compProp = new CompProperty();

                            $propertyCount = count($compProp->getPropertyList());
                            $pageNo = 1;

                            if (isset($_REQUEST['pageNo'])) {
                                $pageNo = $_REQUEST['pageNo'];
                            } else {
                                $pageNo = ceil($propertyCount/10);
                            }

							if ($authorizeObj->isAdmin()) {
	                            $form_creator->popArr['properties'] = $compProp->getPropertyList($pageNo);
							} else {
								$subordinateList = array();
								foreach ($form_creator->popArr['emplist'] as $subordinate) {
									$subordinateList[] = $subordinate[0];
								}
	                            $form_creator->popArr['properties'] = $compProp->getPropertyList($pageNo, $subordinateList, true);

							}
							$form_creator->popArr['allProperties'] = $compProp->getPropertyList();
                            $form_creator->popArr['pageNo'] = $pageNo;
                            $form_creator->popArr['recordCount'] = $propertyCount;
                            $form_creator->popArr['authObj'] = $authorizeObj;
                            $form_creator->popArr['token'] = $token;
                            break;

			case 'JOB' :	$form_creator->formPath = '/templates/eimadmin/jobtitle.php';
                     $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => $_GET['capturemode']);
                     if(isset($_GET['id'])) {
                        $screenParam['id'] = $_GET['id'];
                     }
                     $tokenGenerator = CSRFTokenGenerator::getInstance();
                     $tokenGenerator->setKeyGenerationInput($screenParam);
                     $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                     
							$jobtitle = new JobTitle();
							$paygrade = new SalaryGrades();
							$jobtit_empstat = new JobTitEmpStat();

							$form_creator ->popArr['paygrade'] = $paygrade->getSalGrades(true);
                            $form_creator->popArr['jobSpecList'] = JobSpec::getAll();

							if($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $jobtitle->filterJobTitles($getArr['id']);
								$form_creator ->popArr['assEmploymentStat'] = $jobtit_empstat->getAssEmpStat($getArr['id']);
								$form_creator ->popArr['unAssEmploymentStat'] = $jobtit_empstat->getUnAssEmpStat($getArr['id']);
							}
                     $form_creator ->popArr['token'] = $token;
							break;

			case 'SPC' :	$form_creator->formPath = '/templates/eimadmin/jobSpec.php';
                     $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => $_GET['capturemode']);
                     if(isset($_GET['id'])) {
                        $screenParam['id'] = $_GET['id'];
                     }
                     $tokenGenerator = CSRFTokenGenerator::getInstance();
                     $tokenGenerator->setKeyGenerationInput($screenParam);
                     $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                     
							// Here we fetch all job specs for easier validation of duplicate names
							// Assuming it's unlikely that very large number of job specs will be defined.
							$form_creator->popArr['jobSpecList'] = JobSpec::getAll();
							if($getArr['capturemode'] == 'updatemode') {
								$form_creator->popArr['jobSpec'] = JobSpec::getJobSpec($getArr['id']);
							} else {
								$form_creator->popArr['jobSpec'] = new JobSpec();
							}
                     $form_creator->popArr['token'] = $token;
							break;

			case 'EST' :	$form_creator->formPath = '/templates/eimadmin/empstat.php';
                     $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => $_GET['capturemode']);
                     if(isset($_GET['id'])) {
                        $screenParam['id'] = $_GET['id'];
                     }
                     $tokenGenerator = CSRFTokenGenerator::getInstance();
                     $tokenGenerator->setKeyGenerationInput($screenParam);
                     $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                     
							$empstat = new EmploymentStatus();

							if($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $empstat->filterEmpStat($getArr['id']);
							}
                     $form_creator ->popArr['token'] = $token;
							break;

			case 'GEN' :	$form_creator->formPath = '/templates/eimadmin/geninfo.php';

                     $screenParam = array('uniqcode' => $_GET['uniqcode'], 'VIEW' => 'MAIN');
                     $tokenGenerator = CSRFTokenGenerator::getInstance();
                     $tokenGenerator->setKeyGenerationInput($screenParam);
                     $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                     
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
                     $form_creator->popArr['token'] = $token;
                     
							if(isset($editArr['COUNTRY']))
								$form_creator ->popArr['provlist'] = $province->getProvinceCodes($editArr['COUNTRY']);

							if(isset($editArr['STATE']))
								$form_creator ->popArr['districtlist'] = $district->getDistrictCodes($editArr['STATE']);


                            //Finding the numer of  employees
                            $empInfo = new EmpInfo();
                            $form_creator->popArr['empcount']= $empInfo->countEmployee();
							break;

			case 'CUS' :	$form_creator->formPath = '/templates/eimadmin/customer.php';
							$customer = new Customer();
                     $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => $_GET['capturemode']);
                     if(isset($_GET['id'])) {
                        $screenParam['id'] = $_GET['id'];
                     }
                     $tokenGenerator = CSRFTokenGenerator::getInstance();
                     $tokenGenerator->setKeyGenerationInput($screenParam);
                     $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                     
							if($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $customer->fetchCustomer($getArr['id']);
							} else {
							    $form_creator ->popArr['editArr'] = new Customer();
							}
                     $form_creator ->popArr['token'] = $token;
							break;

			case 'CTM' :	$form_creator->formPath = '/templates/eimadmin/customFields.php';
                     $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => $_GET['capturemode']);
                     if(isset($_GET['id'])) {
                        $screenParam['id'] = $_GET['id'];
                     }
                     $tokenGenerator = CSRFTokenGenerator::getInstance();
                     $tokenGenerator->setKeyGenerationInput($screenParam);
                     $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                     
							$form_creator ->popArr['available'] = CustomFields::getAvailableFieldNumbers();
							if ($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = CustomFields::getCustomField($getArr['id']);
							} else {
							    $form_creator ->popArr['editArr'] = new CustomFields();
							}
                     $form_creator ->popArr['token'] = $token;
							break;

			case 'CEX' :	$form_creator->formPath = '/templates/eimadmin/customExportDefine.php';
                     $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => $_GET['capturemode']);
                     
                     if(isset($_GET['id'])) {
                        $screenParam['id'] = $_GET['id'];
                     }
                     $tokenGenerator = CSRFTokenGenerator::getInstance();
                     $tokenGenerator->setKeyGenerationInput($screenParam);
                     $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));

							$form_creator ->popArr['customExportList'] = CustomExport::getCustomExportList();
							if($getArr['capturemode'] == 'updatemode') {
								$customExport = CustomExport::getCustomExport($getArr['id']);

								$form_creator ->popArr['headings'] = $customExport->getHeadings();
								$form_creator ->popArr['available'] = $customExport->getAvailableFields();
								$form_creator ->popArr['assigned'] = $customExport->getAssignedFields();
								$form_creator ->popArr['exportName'] = $customExport->getName();
								$form_creator ->popArr['id'] = $customExport->getId();
							} else {
								$form_creator ->popArr['headings'] = array();
								$form_creator ->popArr['available'] = CustomExport::getAllFields();
								$form_creator ->popArr['assigned'] = array();
								$form_creator ->popArr['exportName'] = null;
								$form_creator ->popArr['id'] = null;
							}
                     $form_creator ->popArr['token'] = $token;
							break;

			case 'CIM' :	$form_creator->formPath = '/templates/eimadmin/customImportDefine.php';
                     $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => $_GET['capturemode']);
                     if(isset($_GET['id'])) {
                        $screenParam['id'] = $_GET['id'];
                     }
                     
                     $tokenGenerator = CSRFTokenGenerator::getInstance();
                     $tokenGenerator->setKeyGenerationInput($screenParam);
                     $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                     $form_creator ->popArr['token'] = $token;
                     
							$form_creator ->popArr['customImportList'] = CustomImport::getCustomImportList();
							if($getArr['capturemode'] == 'updatemode') {
								$customImport = CustomImport::getCustomImport($getArr['id']);

								$form_creator ->popArr['has_heading'] = $customImport->getContainsHeader();
								$form_creator ->popArr['available'] = $customImport->getAvailableFields();
								$form_creator ->popArr['assigned'] = $customImport->getAssignedFields();
								$form_creator ->popArr['importName'] = $customImport->getName();
								$form_creator ->popArr['id'] = $customImport->getId();
								$form_creator ->popArr['compulsary_fields'] = CustomImport::getCompulsaryFields();
							} else {
								$customImport = new CustomImport();

								// Assign compulsary fields
								$compulsary = CustomImport::getCompulsaryFields();
								$customImport->setAssignedFields($compulsary);

								$form_creator ->popArr['has_heading'] = true;
								$form_creator ->popArr['available'] = $customImport->getAvailableFields();
								$form_creator ->popArr['assigned'] = $compulsary;
								$form_creator ->popArr['importName'] = null;
								$form_creator ->popArr['id'] = null;
								$form_creator ->popArr['compulsary_fields'] = $compulsary;
							}
							break;

			case 'CHD' :	$form_creator->formPath = '/templates/eimadmin/customExportHeadingDefine.php';
                     $screenParam = array('uniqcode' => $_GET['uniqcode'], 'id' => $_GET['id']);
                     $tokenGenerator = CSRFTokenGenerator::getInstance();
                     $tokenGenerator->setKeyGenerationInput($screenParam);
                     $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));

							$customExport = CustomExport::getCustomExport($getArr['id']);

							$headings = $customExport->getHeadings();
							$form_creator ->popArr['headings'] = empty($headings) ? $customExport->getAssignedFields() : $headings;
							$form_creator ->popArr['assigned'] = $customExport->getAssignedFields();
							$form_creator ->popArr['exportName'] = $customExport->getName();
							$form_creator ->popArr['id'] = $customExport->getId();
                     $form_creator ->popArr['token'] = $token;
							break;

			case 'PAD' :    // Project Admin. Fall through to PRJ case below.
							$form_creator->getArr['uniqcode'] = "PRJ";

			case 'PRJ' : 	$form_creator->formPath = '/templates/eimadmin/project.php';
                     $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => $_GET['capturemode']);
                     if(isset($_GET['id'])) {
                        $screenParam['id'] = $_GET['id'];
                     }

                     $tokenGenerator = CSRFTokenGenerator::getInstance();
                     $tokenGenerator->setKeyGenerationInput($screenParam);
                     $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));

							$project = new Projects();
							$customer = new Customer ();
							$form_creator->popArr['cusid'] = $customer->fetchCustomers();
							$form_creator ->popArr['employeeList'] = EmpInfo::getEmployeeMainDetails();

 							if($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $project->fetchProject($getArr['id']) ;
								$gw = new ProjectAdminGateway();
								$form_creator ->popArr['admins'] = $gw->getAdmins($getArr['id']);
							}
                     $form_creator ->popArr['token'] = $token;
							break;

			case 'PAC' : 	$form_creator->formPath = '/templates/eimadmin/projectActivity.php';

							/* If a HR admin, show all projects. Otherwise only show projects for which
							 * user is an admin
							 */
							$authorizeObj = new authorize($_SESSION['empID'], $_SESSION['isAdmin']);
							if ($authorizeObj->isAdmin()) {

								$projects = new Projects();

								/* Filter only not deleted projects */
								$projects->setDeleted(Projects::PROJECT_NOT_DELETED);
								$projectList = $projects->fetchProjects();
							} else if ($authorizeObj->isProjectAdmin()) {

								$gw = new ProjectAdminGateway();
								$projectList = $gw->getProjectsForAdmin($_SESSION['empID']);
							}

							/* If projectId given, get activities for that project else load first
							 * project in list
							 */
							$projectId = null;
							$activities = array();
							if (!empty($projectList)) {

								if (isset($getArr['projectId']) && ($authorizeObj->isAdmin() ||
										$authorizeObj->isProjectAdminOf($getArr['projectId']))) {
									$projectId = $getArr['projectId'];
								} else {
									$projectId = $projectList[0]->getProjectId();
								}
								$activities = ProjectActivity::getActivityList($projectId);
							}

							$form_creator->popArr['projects'] = $projectList;
							$form_creator->popArr['projectId'] = $projectId;
							$form_creator->popArr['activities'] = $activities;
							break;

			case 'USR' :	$form_creator ->formPath = '/templates/maintenance/users.php';
							$user= new Users();
                     $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => $_GET['capturemode'], 'isAdmin' => $_GET['isAdmin']);
                     if(isset($_GET['id'])) {
                        $screenParam['id'] = $_GET['id'];
                     }
                     $tokenGenerator = CSRFTokenGenerator::getInstance();
                     $tokenGenerator->setKeyGenerationInput($screenParam);
                     $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                     
							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['emplist'] = $user->getEmployeeCodes();
								$form_creator ->popArr['uglist'] = $user->getUserGroupCodes();
							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $user->filterUsers($getArr['id']);
								$form_creator ->popArr['emplist'] = $user->getEmployeeCodes();
								$form_creator ->popArr['uglist'] = $user->getUserGroupCodes();
								//$form_creator ->popArr['editArr'] = $user->filterChangeUsers($getArr['id']);
							}

                            $form_creator->popArr['employeeSearchList'] = EmpInfo::getEmployeeSearchList();
                     $form_creator->popArr['token'] = $token;
							break;
			case 'USG'  :	$form_creator ->formPath = '/templates/maintenance/usergroups.php';
							$usrgrp = new UserGroups();
                     $screenParam = array('uniqcode' => $_GET['uniqcode'], 'capturemode' => $_GET['capturemode']);
                     if(isset($_GET['id'])) {
                        $screenParam['id'] = $_GET['id'];
                     }
                     $tokenGenerator = CSRFTokenGenerator::getInstance();
                     $tokenGenerator->setKeyGenerationInput($screenParam);
                     $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                     
							if($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $usrgrp->filterUserGroups($getArr['id']);
							}
                     $form_creator ->popArr['token'] = $token;
							break;

			case 'UGR' :	$form_creator ->formPath = '/templates/maintenance/ugrights.php';
                     $screenParam = array('uniqcode' => $_GET['uniqcode'], 'id' => $_GET['id']);
                     $tokenGenerator = CSRFTokenGenerator::getInstance();
                     $tokenGenerator->setKeyGenerationInput($screenParam);
                     $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                     
							$urights = new Rights();
							$usergroup = new UserGroups();

							$form_creator->popArr['ugDet'] = $usergroup->filterUserGroups($getArr['id']);
							$form_creator->popArr['modlist'] = $urights->getAllModules();
                     $form_creator->popArr['token'] = $token;

							if(isset($getArr['editID'])) {
								    $arr[0]=$getArr['id'];
								    $arr[1]=$getArr['editID'];
								$form_creator->popArr['editArr'] = $urights->filterRights($arr);

							} else {
								$form_creator->popArr['modlistUnAss'] = $urights->getModuleCodes($getArr['id']);
							}

							$form_creator->popArr['modlistAss'] = $urights->getAssRights($getArr['id']);
							break;

			case 'LDAP' :	if ($_SESSION['ldap'] == "enabled") {
								$form_creator ->formPath = '/templates/eimadmin/ldapConfiguration.php';
								$ldapConfigObj = new LdapDetails();
								$form_creator ->popArr['editArr'] = $ldapConfigObj->retrieveLdapDetails();
							}
							break;

				}
		$form_creator->display();

	}

    /**
     * Get the job spec for the given job title
     * @param String $jobTitleCode The job title code
     * @return JobSpec JobSpec object or null if no job spec assigned for given job title
     */
    public function getJobSpecForJob($jobTitleCode) {

        $jobSpec = null;

        if (CommonFunctions::isValidId($jobTitleCode, 'JOB')) {

            $jobTitle = new JobTitle();
            $jobTitles = $jobTitle->filterJobTitles($jobTitleCode);
            if (is_array($jobTitles) && (count($jobTitles) == 1)) {
                $jobSpecId = $jobTitles[0][5];

                try {
                    $jobSpec = JobSpec::getJobSpec($jobSpecId);
                } catch (JobSpecException $ex) {
                    // ignore, we will be returning null
                }
            }
        }
        return $jobSpec;
    }
}
?>
