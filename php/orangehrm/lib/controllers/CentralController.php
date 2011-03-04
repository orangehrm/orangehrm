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


ob_start();

session_start();

$_SESSION['posted'] = false;

if(!isset($_SESSION['fname'])) {

	header("Location: ../../login.php");
	exit();
}

set_magic_quotes_runtime(0); // Turning off magic quotes runtime

define('ROOT_PATH', $_SESSION['path']);
define("SALT", '$2a$'.str_pad($_SESSION['empID'].session_id(), 24, session_id()).'$');

require_once ROOT_PATH . '/lib/exception/ExceptionHandler.php';
require_once ROOT_PATH . '/lib/common/Language.php';
require_once ROOT_PATH . '/lib/common/LocaleUtil.php';
require_once ROOT_PATH . '/lib/common/CommonFunctions.php';

require_once ROOT_PATH . '/lib/controllers/ViewController.php';
require_once ROOT_PATH . '/lib/controllers/MTViewController.php';
require_once ROOT_PATH . '/lib/controllers/EmpViewController.php';
require_once ROOT_PATH . '/lib/controllers/RepViewController.php';
require_once ROOT_PATH . '/lib/controllers/TimeController.php';
require_once ROOT_PATH . '/lib/controllers/BenefitsController.php';
require_once ROOT_PATH . '/lib/controllers/RecruitmentController.php';

require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_JobTitle.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_JobSpec.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_JobTitEmpStat.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_EmpStat.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_GenInfo.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_CompStruct.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_Location.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_CompProperty.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_MembershipType.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_MembershipInfo.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_NationalityInfo.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_SalaryGrades.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_Skills.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_LanguageInfo.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_Qualifications.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_SalCurDet.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_EEOJobCat.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_Licenses.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_EthnicRace.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_EmailConfiguration.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_EmailNotificationConfiguration.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_Customer.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_Projects.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_ProjectAdmin.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_ProjectActivity.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_CustomFields.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_CustomExport.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_CustomImport.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_CSVImport.php';

require_once ROOT_PATH . '/lib/extractor/maintenance/EXTRACTOR_Bugs.php';
require_once ROOT_PATH . '/lib/extractor/maintenance/EXTRACTOR_Users.php';
require_once ROOT_PATH . '/lib/extractor/maintenance/EXTRACTOR_Modules.php';
require_once ROOT_PATH . '/lib/extractor/maintenance/EXTRACTOR_UserGroups.php';
require_once ROOT_PATH . '/lib/extractor/maintenance/EXTRACTOR_Rights.php';
require_once ROOT_PATH . '/lib/extractor/maintenance/EXTRACTOR_ChangeUsers.php';

require_once ROOT_PATH . '/lib/extractor/hrfunct/EXTRACTOR_EmpInfo.php';
require_once ROOT_PATH . '/lib/extractor/hrfunct/EXTRACTOR_EmpLanguage.php';
require_once ROOT_PATH . '/lib/extractor/hrfunct/EXTRACTOR_EmpConExt.php';
require_once ROOT_PATH . '/lib/extractor/hrfunct/EXTRACTOR_EmpJobHistory.php';
require_once ROOT_PATH . '/lib/extractor/hrfunct/EXTRACTOR_EmpBasSalary.php';
require_once ROOT_PATH . '/lib/extractor/hrfunct/EXTRACTOR_EmpEducation.php';
require_once ROOT_PATH . '/lib/extractor/hrfunct/EXTRACTOR_EmpMembership.php';
require_once ROOT_PATH . '/lib/extractor/hrfunct/EXTRACTOR_EmpWorkExp.php';
require_once ROOT_PATH . '/lib/extractor/hrfunct/EXTRACTOR_EmpQualification.php';
require_once ROOT_PATH . '/lib/extractor/hrfunct/EXTRACTOR_EmpPassPort.php';

require_once ROOT_PATH . '/lib/extractor/hrfunct/EXTRACTOR_EmpAttach.php';
require_once ROOT_PATH . '/lib/extractor/hrfunct/EXTRACTOR_EmpRepTo.php';
require_once ROOT_PATH . '/lib/extractor/hrfunct/EXTRACTOR_EmpDependents.php';
require_once ROOT_PATH . '/lib/extractor/hrfunct/EXTRACTOR_EmpSkill.php';
require_once ROOT_PATH . '/lib/extractor/hrfunct/EXTRACTOR_EmpLicenses.php';
require_once ROOT_PATH . '/lib/extractor/hrfunct/EXTRACTOR_EmpDependents.php';
require_once ROOT_PATH . '/lib/extractor/hrfunct/EXTRACTOR_EmpChildren.php';
require_once ROOT_PATH . '/lib/extractor/hrfunct/EXTRACTOR_EmpEmergencyCon.php';
require_once ROOT_PATH . '/lib/extractor/hrfunct/EXTRACTOR_EmpDirectDebit.php';

require_once ROOT_PATH . '/lib/extractor/report/EXTRACTOR_EmpReport.php';
require_once ROOT_PATH . '/lib/extractor/report/EXTRACTOR_EmpRepUserGroups.php';

require_once ROOT_PATH . '/lib/extractor/time/EXTRACTOR_Timesheet.php';
require_once ROOT_PATH . '/lib/extractor/time/EXTRACTOR_TimeEvent.php';
require_once ROOT_PATH . '/lib/extractor/time/EXTRACTOR_TimesheetSubmissionPeriod.php';
require_once ROOT_PATH . '/lib/extractor/time/EXTRACTOR_Workshift.php';
if ($_SESSION['ldap'] == "enabled") {
	require_once ROOT_PATH . '/plugins/ldap/EXTRACTOR_LdapDetails.php';
}

//leave modules extractorss go here

if(isset($_GET['uniqcode'])) {
	$moduletype = 'admin';
} elseif (isset($_GET['reqcode'])) {
	$moduletype = 'hr';
} elseif (isset($_GET['mtcode'])) {
	$moduletype = 'mt';
} elseif (isset($_GET['repcode'])) {
	$moduletype = 'rep';
} elseif (isset($_GET['leavecode'])) {
	$moduletype = 'leave';
} elseif (isset($_GET['timecode'])) {
	$moduletype = 'timeMod';
} elseif (isset($_GET['benefitcode'])) {
	$moduletype = 'benefits';
} elseif (isset($_GET['recruitcode'])) {
	$moduletype = 'recruitMod';
}
//authentication
require_once ROOT_PATH . '/lib/models/maintenance/Rights.php';
require_once ROOT_PATH . '/lib/models/maintenance/UserGroups.php';

$rights = new Rights();

$locRights = array('add'=> false , 'edit'=> false , 'delete'=> false, 'view'=> false);

define('Admin', 'MOD001');
define('PIM', 'MOD002');
define('MT', 'MOD003');
define('REP', 'MOD004');
define('LEAVE', 'MOD005');
define('TIMEMOD', 'MOD006');
define('RECRUITMOD', 'MOD008');

/* Help page session variables: Begin */

if ($_SESSION['isAdmin'] == 'Yes') {
    $_SESSION['hp-role'] = 'Admin';
} elseif ($_SESSION['isSupervisor']) {
    $_SESSION['hp-role'] = 'Sup';
} else {
    $_SESSION['hp-role'] = 'ESS';
}

switch ($moduletype) {

    case 'admin'	:	$_SESSION['hp-module'] = 'Admin';
    					$_SESSION['hp-action'] = $_GET['uniqcode'];
    					if ($_GET['uniqcode'] == 'USR' && $_GET['isAdmin'] == 'Yes') { // Used for the help of creating users: Admin > Users
    					    $_SESSION['hp-userType'] = 'Admin';
    					} elseif ($_GET['uniqcode'] == 'USR' && $_GET['isAdmin'] == 'No') {
    					    $_SESSION['hp-userType'] = 'ESS';
    					}
    					break;

    case 'hr'		:	$_SESSION['hp-module'] = 'PIM';
    					$_SESSION['hp-action'] = isset($_POST['pane'])?$_POST['pane']:'';
    					break;

    case 'leave'	:	$_SESSION['hp-module'] = 'Leave';
    					$_SESSION['hp-action'] = $_GET['action'];
    					break;

}

/* Help page session variables: End */

switch ($moduletype) {
	case 'admin'	:	$locRights = $rights->getRights($_SESSION['userGroup'],Admin); break;
	case 'hr'		:	$locRights = $rights->getRights($_SESSION['userGroup'],PIM); break;
	case 'rep'		:	$locRights = $rights->getRights($_SESSION['userGroup'],REP); break;
	case 'leave'	:	$locRights = $rights->getRights($_SESSION['userGroup'],LEAVE); break;
	case 'timeMod'	:	$locRights = $rights->getRights($_SESSION['userGroup'],TIMEMOD); break;
	case 'benefits' : include ROOT_PATH.'/lib/controllers/Benefits.inc.php'; break;
	case 'recruitMod' :	$locRights = $rights->getRights($_SESSION['userGroup'],RECRUITMOD); break;
}

if(!is_array($locRights) && $locRights == false)
	$locRights = array('add'=> false , 'edit'=> false , 'delete'=> false, 'view'=> false);

/* TODO: This seems to give view permission to ESS users to admin module */
if($_SESSION['isAdmin'] != 'Yes')
	$locRights = array('add'=> false , 'edit'=> false , 'delete'=> false, 'view'=> true);

if (isset($_GET['reqcode']) && 	($_GET['reqcode'] === "ESS") && (isset($_GET['id']) && ($_GET['id'] !== $_SESSION['empID']))) {
	trigger_error("Authorization Failed: You are not allowed to view this page", E_USER_ERROR);
}

/*
 * Allow Project admins to view project activity page.
 */
if ($_SESSION['isProjectAdmin'] && ($moduletype == 'admin') && ($_GET['uniqcode'] == 'PAC')) {
	$locRights = array('add'=> true , 'edit'=> true , 'delete'=> true, 'view'=> true);
}

/*
 * Allow Supervisors to view/edit company property page
 */
if ($_SESSION['isSupervisor'] && ($moduletype == 'admin') && ($_GET['uniqcode'] == 'TCP') ) {
    $locRights = array('add'=> true , 'edit'=> true , 'delete'=> true, 'view'=> true);
}

$ugroup = new UserGroups();
$ugDet = $ugroup ->filterUserGroups($_SESSION['userGroup']);

$locRights['repDef'] = ($ugDet !== null && $ugDet[0][2] == '1') ? true : false;

$_SESSION['localRights'] = $locRights;


switch ($moduletype) {

	case 'admin' 	:  // beg. admin module

					$authorizeObj = new authorize($_SESSION['empID'], $_SESSION['isAdmin']);

					if(!$authorizeObj->isActionPermitted($_GET['uniqcode'])){
						die('Your are not authorize to view this page');
					}

					$view_controller = new ViewController();

						if(isset($_POST['delState']) && $_POST['delState']=='DeleteMode' && $locRights['delete']) {
							    $arrList[0] = array();
                                if(isset($_POST['chkLocID'])) {
                                    $arrList[0] = $_POST['chkLocID'];
                                }
							    $view_controller->delParser(trim($_GET['uniqcode']),$arrList);
						}

						if(isset($_GET['VIEW']) && $_GET['VIEW'] == 'MAIN' && $locRights['view']) {
							$view_controller ->viewList($_GET,$_POST);
							break;
						}


					// choosing which extractor
					switch ($_GET['uniqcode']) {

						case 'GEN'	:
										if(isset($_POST['STAT'])) {
											$extractor = new EXTRACTOR_GenInfo();
										}

										break;

						case 'EST'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_EmployStat();
										}

										break;

						case 'JOB'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_JobTitle();
										}

										break;

						case 'SPC'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_JobSpec();
										}

										break;

						case 'BNK'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_BankInfo();
										}

										break;

						case 'TAX'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_TaxInfo();
										}

										break;

						case 'CCB'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_CashBen();
										}

										break;

						case 'COS'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_CostCenter();
										}

										break;

						case 'COU'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_CountryInfo();
										}

										break;

						case 'CUR'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_CurrencyTypes();
										}

										break;

						case 'DWT'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_DwellingType();
										}

										break;

						case 'ELE'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_ElectorateInfo();
										}

										break;

						case 'EMC'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_EmployeeCat();
										}

										break;

						case 'EMG'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_EmployeeGroup();
										}

										break;

						case 'ETY'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_EmployeeTypes();
										}

										break;

						case 'EXC'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_ExtraCurrActCat();
										}

										break;

						case 'DEF'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_HierarchyDefInfo();
										}

										break;

						case 'JDC'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_JDCategory();
										}

										break;

						case 'LOC'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_Location();
										}

										break;
                        case 'TCP'  :
                                        if(isset($_POST['sqlState'])) {
                                            $extractor = new EXTRACTOR_CompProperty();
                                        }
                                        break;

						case 'MEM'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_MembershipType();
										}

										break;

						case 'NAT'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_NationalityInfo();
										}

										break;

						case 'NCB'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_NonCashBen();
										}

										break;

						case 'QLF'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_QualificationType();
										}

										break;

						case 'RTM'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_RatingTypes();
										}

										break;

						case 'RTE'  :
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_RouteInfo();
										}

										break;

						case 'SGR'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_SalaryGrades();
										}

										if(isset($_POST['STAT'])) {
											$extractor = new EXTRACTOR_SalCurDet();
										}

										break;

						case 'SKI'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_Skills();
										}

										break;

						case 'EXA'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_ExtraCurActInfo();
										}

										break;

						case 'MME'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_MembershipInfo();
										}

										break;

						case 'JDK'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_JDKra();
										}

										break;

						case 'LAN'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_LanguageInfo();
										}

										break;

						case 'RLG'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_ReligionInfo();
										}

										break;

						case 'SAT'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_SatutoryInfo();
										}

										break;

						case 'UNI'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_UniformType();
										}

										break;

						case 'EXA'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_ExtraCurActInfo();
										}

										break;

						case 'EDU'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_education();
										}

										break;

						case 'JDT'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_JDType();
										}

										break;

						case 'SBJ'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_SubjectInfo();
										}

										break;

						case 'SSK'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_SubSkillInfo();
										}

										break;

						case 'BCH'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_Branches();
										}

										break;

						case 'CHI'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_CompHier();
										}

										break;

						case 'CTT'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_CorpTit();
										}

										break;

						case 'DSG'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_Designations();
										}

										break;

						case 'PRO'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_ProvinceInfo();
										}

										break;

						case 'ETH'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_EthnicRace();
										}

										break;

						case 'DIS'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_DistrictInfo();
										}

										break;

						case 'EEC'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_EEOJobCat();
										}

			 							break;

			 			case 'LIC'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_Licenses();
										}

			 							break;
			 			case 'CST'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_CompStruct();
										}

			 							break;

						case 'BBS'	:
										if(isset($_POST['STAT'])) {
											$extractor = new EXTRACTOR_CashBenSal();
										}

										break;

						case 'NBS'	:
										if(isset($_POST['STAT'])) {
											$extractor = new EXTRACTOR_NonCashBenSal();
										}

										break;

						case 'DDI'	:
										if(isset($_POST['KRA'])) {
											$extractor = new EXTRACTOR_DesDescription();
										}

										break;

						case 'DQA'	:
										if(isset($_POST['STAT'])) {
											$extractor = new EXTRACTOR_DesQualification();
										}

										break;

						case 'DQS'	:
										if(isset($_POST['STAT'])) {
											$extractor = new EXTRACTOR_DesQualSubject();
										}

										break;

						case 'RTG'	:
										if(isset($_POST['STAT'])) {
											$extractor = new EXTRACTOR_RatingGrade();
										}

			 							break;

			 			case 'CUS'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_Customer();
										}

										break;
			 			case 'CTM'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_CustomFields();
										}

										break;

			 			case 'CHD'	:   // fall through
			 			case 'CEX'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_CustomExport();
										}

										break;
			 			case 'CIM'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_CustomImport();
										}

										break;

			 			case 'IMP'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_CSVImport();
										}

										break;

					    case 'PRJ'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_Projects();
										}

										break;

					    case 'PAD'	:   // Project Admin
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_ProjectAdmin();
										}

										break;

					    case 'PAC'	:   // Project Activity
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_ProjectActivity();
										}

										break;

						case 'USR'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_Users();
										}
										break;

						case 'USG'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_UserGroups();
										}

			 							break;

						case 'UGR'	:
										if(isset($_POST['STAT'])) {
											$extractor = new EXTRACTOR_Rights();
										}
										break;
						case 'EMX'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_EmailConfiguration();
										}
										break;

						case 'ENS'	:
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_EmailNotificationConfiguration();
										}
										break;
						case 'LDAP'	:
										if(isset($_POST['sqlState']) && $_SESSION['ldap'] == "enabled") {
											$extractor = new EXTRACTOR_LdapDetails();
										}
										break;
					}

										if(isset($_POST['sqlState']) && $_POST['sqlState']=='delete' && $locRights['delete']) {
											$parsedObject = $extractor->parseDeleteData($_POST);
											$view_controller->deleteData($_GET['uniqcode'],$parsedObject);
										}
										if(isset($_POST['sqlState']) && $_POST['sqlState']=='NewRecord' && $locRights['add']) {
												$parsedObject = $extractor->parseAddData($_POST);
												$view_controller->addData($_GET['uniqcode'],$parsedObject);
												break;
										} elseif (isset($_POST['sqlState']) && $_POST['sqlState']=='UpdateRecord' && $locRights['edit']) {
												$parsedObject = $extractor->parseEditData($_POST);
												$view_controller->updateData($_GET['uniqcode'],$_GET['id'],$parsedObject);
												break;
										} elseif (($_GET['uniqcode'] == 'CSE') && isset($_GET['download'])) {
											$view_controller->exportCSV($_GET['cmbExportType']);
											break;
										} elseif ($_GET['uniqcode'] == 'IMPAJAX' && isset($_GET['file'])) {
											$view_controller->importData(base64_decode($_GET['file']), trim($_GET['importType']));
											break;
										}


										if(isset($_POST['KRA']) && $_POST['KRA']=='SEL' && $locRights['add']) {
												$objectArr = $extractor->parseAddData($_POST);
												$view_controller->addDesDisData($objectArr);
										} elseif (isset($_POST['KRA']) && $_POST['KRA']=='SAV' && $locRights['edit']) {
												$parsedObject = $extractor->parseEditData($_POST);
												$view_controller->updateDesDisData($parsedObject);
										} elseif(isset($_POST['KRA']) && $_POST['KRA'] == 'DEL' && $locRights['delete']) {
												$view_controller->delDesDisData($_POST,$_GET);
										}


										if(isset($_POST['STAT']) && (($_POST['STAT'] == 'ADD' && $locRights['add']) || ($_POST['STAT'] == 'EDIT' && $locRights['edit']))) {
												$parsedObject = $extractor->parseData($_POST);
												$view_controller->assignData($_GET['uniqcode'],$parsedObject,$_POST['STAT']);
										} elseif(isset($_POST['STAT']) && $_POST['STAT'] == 'DEL' && $locRights['delete']) {
												$view_controller->delAssignData($_GET['uniqcode'],$_POST,$_GET);
										}


						if(isset($_POST['STAT']) && $_POST['STAT'] == 'OWN') {
							$parsedObject = $extractor->parseData($_POST);
							$view_controller -> reDirect($_GET,$parsedObject);
							break;
						} elseif(isset($_POST['sqlState']) && isset($_POST['capturemode']) && $_GET['capturemode'] == 'addmode') {
							$extObject = $extractor->reloadData($_POST);
							$view_controller -> reDirect($_GET,$extObject);
							break;
						} elseif(isset($_POST['sqlState']) && isset($_POST['capturemode']) && $_GET['capturemode'] == 'updatemode') {
							$extObject = $extractor->reloadData($_POST);
							$view_controller -> reDirect($_GET,$extObject);
							break;
						} else {
							$view_controller -> reDirect($_GET);
							break;
						}


						// end of admin module
						break;

	case 'hr'		:


					$view_controller = new EmpViewController();

						if(isset($_POST['delState']) && $_POST['delState']=='DeleteMode' && $locRights['delete']) {

							    $arrList[0]=$_POST['chkLocID'];
							    $view_controller->delParser(trim($_GET['reqcode']),$arrList);
						}

						if(isset($_GET['VIEW']) && $_GET['VIEW'] == 'MAIN' && $locRights['view']) {

							if ($_SESSION['isAdmin'] == 'No' && !$_SESSION['isSupervisor']) {
							    die('You are not authorized to view this page');
							}

							$view_controller ->viewList($_GET,$_POST);
							break;
						}

					$authorize = new authorize($_SESSION['empID'], $_SESSION['isAdmin']);
					$supervisor = false;

					/* Set permission to the employee information view for non admins */
					if (isset ($_GET['reqcode']) && ($_GET['reqcode'] === "EMP") && (!$authorize->isAdmin())) {

						/* Supervisors can only access their subordinates */
						if ($authorize->isSupervisor()) {

							/* Don't allow if trying to view own details or trying to view details of non-subordinate
							 */
							if ((isset($_GET['id']) && ($_GET['id'] === $_SESSION['empID']))) {

								trigger_error("Authorization Failed: You are not allowed to view this page", E_USER_ERROR);
							}

							/* If we came here, this is a subordinate. Assign all rights */
							$locRights = array ('add' => true, 'edit' => true, 'delete' => true,'view' => true);
							$_SESSION['localRights'] = $locRights;
							$supervisor = true;

						} else if ($authorize->isESS()) {
                            if($_GET['id'] != $_SESSION['empID']) {
                                /* Deny access to ESS users. */
                                trigger_error("Authorization Failed: You are not allowed to view this page", E_USER_ERROR);
                            }
						}
					}

					// choosing which extractor
					if(isset($_POST['econtactSTAT']) && $_POST['econtactSTAT']!= '') {
						$extractorForm = new EXTRACTOR_EmpEmergencyCon();
					}

					if(isset($_POST['passportSTAT']) && $_POST['passportSTAT']!= '' && isset($_GET['reqcode']) && ($_GET['reqcode'] !== "ESS")) {
						$extractorForm = new EXTRACTOR_EmpPassPort();
					}

					if(isset($_POST['directDebitSTAT']) && $_POST['directDebitSTAT']!= '' && isset($_GET['reqcode']) && ($_GET['reqcode'] !== "ESS")) {
						$extractorForm = new EXTRACTOR_EmpDirectDebit();
					}

					if(isset($_POST['langSTAT']) && $_POST['langSTAT']!= '') {
						$extractorForm = new EXTRACTOR_EmpLanguage();
					}

					if(isset($_POST['licenseSTAT']) && $_POST['licenseSTAT']!= '') {
						$extractorForm = new EXTRACTOR_EmpLicenses();
					}

					if(isset($_POST['membershipSTAT']) && $_POST['membershipSTAT']!= '') {
						$extractorForm = new EXTRACTOR_EmpMembership();
					}

					if(isset($_POST['paymentSTAT']) && $_POST['paymentSTAT']!= '' && isset($_GET['reqcode']) && ($_GET['reqcode'] !== "ESS")) {
						$extractorForm = new EXTRACTOR_EmpBasSalary();
					}

					if(isset($_POST['educationSTAT']) && $_POST['educationSTAT']!= '') {
						$extractorForm = new EXTRACTOR_EmpEducation();
					}

					if(isset($_POST['wrkexpSTAT']) && $_POST['wrkexpSTAT']!= '' && isset($_GET['reqcode']) && ($_GET['reqcode'] !== "ESS")) {
						$extractorForm = new EXTRACTOR_EmpWorkExp();
					}

					if(isset($_POST['skillSTAT']) && $_POST['skillSTAT']!= '' && isset($_GET['reqcode']) && ($_GET['reqcode'] !== "ESS")) {
						$extractorForm = new EXTRACTOR_EmpSkill();
					}

					if(isset($_POST['reporttoSTAT']) && $_POST['reporttoSTAT']!= '' && isset($_GET['reqcode']) && ($_GET['reqcode'] !== "ESS")) {
						$extractorForm = new EXTRACTOR_EmpRepTo();
					}

					if(isset($_POST['brchSTAT']) && $_POST['brchSTAT']!= '' && isset($_GET['reqcode']) && ($_GET['reqcode'] !== "ESS")) {
						$extractorForm = new EXTRACTOR_EmpBank();
					}

					if(isset($_POST['attSTAT']) && $_POST['attSTAT']!= '' && isset($_GET['reqcode']) && ($_GET['reqcode'] !== "ESS")) {
						$extractorForm = new EXTRACTOR_EmpAttach();
					}

					if(isset($_POST['dependentSTAT']) && $_POST['dependentSTAT']!= '') {
						$extractorForm = new EXTRACTOR_EmpDependents();
					}

					if(isset($_POST['childrenSTAT']) && $_POST['childrenSTAT']!= '') {
						$extractorForm = new EXTRACTOR_EmpChildren();
					}

					if(isset($_POST['conextSTAT']) && $_POST['conextSTAT']!= '' && isset($_GET['reqcode']) && ($_GET['reqcode'] !== "ESS")) {
						$extractorForm = new EXTRACTOR_EmpConExt();
					}

                    if(isset($_POST['empjobHistorySTAT']) && $_POST['empjobHistorySTAT']!= '' && isset($_GET['reqcode']) && ($_GET['reqcode'] !== "ESS")) {
                        $extractorForm = new EXTRACTOR_EmpJobHistory();
                    }

					switch ($_GET['reqcode']) {
						case 'ESS'	:	if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_EmpInfo();
											$extractor->ESS();
										}
										break;
						case 'EMP'	:
										if(isset($_POST['sqlState'])) {

											$extractor = new EXTRACTOR_EmpInfo();
										}

										break;
					}

										if(isset($_POST['sqlState']) && $_POST['sqlState']=='NewRecord' && $locRights['add']) {

												$parsedObject = $extractor->parseAddData($_POST);
												$view_controller->addData($_POST,$_GET['reqcode'],$parsedObject);
												break;
										} elseif (isset($_POST['sqlState']) && $_POST['sqlState']=='UpdateRecord' && $locRights['edit']) {
												$parsedObject = $extractor->parseEditData($_POST);
												$view_controller->updateData($_GET['reqcode'],$_GET['id'],$parsedObject);
												break;
										} elseif (isset($_POST['sqlState']) && $_POST['sqlState']=='UpdateCountry' && $locRights['edit']) {
												$parsedObject = $extractor->parseCountryData($_POST);
												$view_controller->updateCountryData($parsedObject);
										}

										if (isset($_POST['sqlState']) && $_POST['sqlState']=='UpdateRecord' && $_GET['reqcode'] == 'ESS') {
												$parsedObject = $extractor->parseEditData($_POST);
												$view_controller->updateData($_GET['reqcode'],$_GET['id'],$parsedObject);
												break;
										}

										if(isset($_POST['STAT']) && ((($_POST['STAT'] == 'ADD' || $_POST['STAT'] == 'ADDOTH') && $locRights['add']) || ($_POST['STAT'] == 'EDIT' && $locRights['edit']))) {
											if (isset($_POST['imageChange']) && $_POST['imageChange'] == '1') {
												$object = new EmpPicture();
												$extractor = new EXTRACTOR_EmpPhoto();
												$message = null;
												$result = false;

												if (isset($_POST['STAT'])) {
													switch ($_POST['STAT']) {
														case 'ADD':
															$object = $extractor->parseData();
															if($object != null) {
																$object->setEmpId($_GET['id']);
																$result = $object->addEmpPic();
															} else {
																$message = "FAILURE";
															}
															break;
														case 'EDIT':
															$object = $extractor->parseData();
															if($object != null) {
																$object->setEmpId($_GET['id']);
																$result = $object->updateEmpPic();
															} else {
																$message = "FAILURE";
															}
															break;
													}
												}
											}

											$parsedObject = $extractor->parseData($_POST);
											$view_controller->assignData($_GET['reqcode'],$parsedObject,$_POST['STAT']);

										} elseif(isset($_POST['STAT']) && $_POST['STAT'] == 'DEL' && $locRights['delete']) {
											if (isset($_POST['imageChange']) && $_POST['imageChange'] == '1') {
												$object = new EmpPicture();
												$result = $object->delEmpPic(array(array($_GET['id'])));
											}
											$view_controller->delAssignData($_GET['reqcode'],$_POST,$_GET);
										}

										if(isset($_POST['educationSTAT']) && (($_POST['educationSTAT'] == 'ADD') || ($_POST['educationSTAT'] == 'EDIT'))) {
												$parsedObject = $extractorForm->parseData($_POST);
												$view_controller->assignEmpFormData($_POST,$parsedObject,$_POST['educationSTAT']);
										} elseif(isset($_POST['educationSTAT']) && $_POST['educationSTAT'] == 'DEL') {
												$view_controller->delEmpFormData($_GET,$_POST);
										}

										if(isset($_POST['wrkexpSTAT']) && (($_POST['wrkexpSTAT'] == 'ADD' && $locRights['add']) || ($_POST['wrkexpSTAT'] == 'EDIT' && $locRights['edit']))) {
												$parsedObject = $extractorForm->parseData($_POST);
												$view_controller->assignEmpFormData($_POST,$parsedObject,$_POST['wrkexpSTAT']);
										} elseif(isset($_POST['wrkexpSTAT']) && $_POST['wrkexpSTAT'] == 'DEL' && $locRights['delete']) {
												$view_controller->delEmpFormData($_GET,$_POST);
										}

										if(isset($_POST['skillSTAT']) && (($_POST['skillSTAT'] == 'ADD' && $locRights['add']) || ($_POST['skillSTAT'] == 'EDIT' && $locRights['edit']))) {
												$parsedObject = $extractorForm->parseData($_POST);
												$view_controller->assignEmpFormData($_POST,$parsedObject,$_POST['skillSTAT']);
										} elseif(isset($_POST['skillSTAT']) && $_POST['skillSTAT'] == 'DEL' && $locRights['delete']) {
												$view_controller->delEmpFormData($_GET,$_POST);
										}

										/* If supervisor mode, don't allow changes to payment details or report-to */
										if(!$supervisor) {

											if(isset($_POST['paymentSTAT']) && (($_POST['paymentSTAT'] == 'ADD' && $locRights['add']) || ($_POST['paymentSTAT'] == 'EDIT' && $locRights['edit']))) {
													$parsedObject = $extractorForm->parseData($_POST);
													$view_controller->assignEmpFormData($_POST,$parsedObject,$_POST['paymentSTAT']);
											} elseif(isset($_POST['paymentSTAT']) && $_POST['paymentSTAT'] == 'DEL' && $locRights['delete']) {
													$view_controller->delEmpFormData($_GET,$_POST);
											}

											if(isset($_POST['reporttoSTAT']) && (($_POST['reporttoSTAT'] == 'ADD' && $locRights['add']) || ($_POST['reporttoSTAT'] == 'EDIT' && $locRights['edit']))) {
													$parsedObject = $extractorForm->parseData($_POST);
													$view_controller->assignEmpFormData($_POST,$parsedObject,$_POST['reporttoSTAT']);
											} elseif(isset($_POST['reporttoSTAT']) && $_POST['reporttoSTAT'] == 'DEL' && $locRights['delete']) {
													$view_controller->delEmpFormData($_GET,$_POST);
											}
										}

										if(isset($_POST['econtactSTAT']) && (($_POST['econtactSTAT'] == 'ADD' && $locRights['add']) || ($_POST['econtactSTAT'] == 'EDIT' && $locRights['edit']) || ($_GET['reqcode'] === "ESS") && (($_POST['econtactSTAT'] == 'ADD') || ($_POST['econtactSTAT'] == 'EDIT')))) {
												$parsedObject = $extractorForm->parseData($_POST);
												$view_controller->assignEmpFormData($_POST,$parsedObject,$_POST['econtactSTAT']);
										} elseif(isset($_POST['econtactSTAT']) && $_POST['econtactSTAT'] == 'DEL' && ($locRights['delete'] || (isset($_GET['reqcode']) && ($_GET['reqcode'] === "ESS")))) {
												$view_controller->delEmpFormData($_GET,$_POST);
										}

										if(isset($_POST['passportSTAT']) && (($_POST['passportSTAT'] == 'ADD' && $locRights['add']) || ($_POST['passportSTAT'] == 'EDIT' && $locRights['edit']))) {
												$parsedObject = $extractorForm->parseData($_POST);
												$view_controller->assignEmpFormData($_POST,$parsedObject,$_POST['passportSTAT']);
										} elseif(isset($_POST['passportSTAT']) && $_POST['passportSTAT'] == 'DEL' && $locRights['delete']) {
												$view_controller->delEmpFormData($_GET,$_POST);
										}

										if(isset($_POST['directDebitSTAT']) && (($_POST['directDebitSTAT'] == 'ADD' && $locRights['add']) || ($_POST['directDebitSTAT'] == 'EDIT' && $locRights['edit']))) {
												$parsedObject = $extractorForm->parseData($_POST);
												$view_controller->assignEmpFormData($_POST,$parsedObject,$_POST['directDebitSTAT']);
										} elseif(isset($_POST['directDebitSTAT']) && $_POST['directDebitSTAT'] == 'DEL' && $locRights['delete']) {
												$view_controller->delEmpFormData($_GET,$_POST);
										}

										if(isset($_POST['langSTAT']) && (($_POST['langSTAT'] == 'ADD') || ($_POST['langSTAT'] == 'EDIT'))) {
												$parsedObject = $extractorForm->parseData($_POST);
												$view_controller->assignEmpFormData($_POST,$parsedObject,$_POST['langSTAT']);
										} elseif(isset($_POST['langSTAT']) && $_POST['langSTAT'] == 'DEL') {
												$view_controller->delEmpFormData($_GET,$_POST);
										}

										if(isset($_POST['licenseSTAT']) && (($_POST['licenseSTAT'] == 'ADD') || ($_POST['licenseSTAT'] == 'EDIT'))) {
												$parsedObject = $extractorForm->parseData($_POST);
												$view_controller->assignEmpFormData($_POST,$parsedObject,$_POST['licenseSTAT']);
										} elseif(isset($_POST['licenseSTAT']) && $_POST['licenseSTAT'] == 'DEL') {
												$view_controller->delEmpFormData($_GET,$_POST);
										}

										if(isset($_POST['membershipSTAT']) && (($_POST['membershipSTAT'] == 'ADD') || ($_POST['membershipSTAT'] == 'EDIT'))) {
												$parsedObject = $extractorForm->parseData($_POST);
												$view_controller->assignEmpFormData($_POST,$parsedObject,$_POST['membershipSTAT']);
										} elseif(isset($_POST['membershipSTAT']) && $_POST['membershipSTAT'] == 'DEL') {
												$view_controller->delEmpFormData($_GET,$_POST);
										}

										if(isset($_POST['dependentSTAT']) && (($_POST['dependentSTAT'] == 'ADD') || ($_POST['dependentSTAT'] == 'EDIT'))) {
												$parsedObject = $extractorForm->parseData($_POST);
												$view_controller->assignEmpFormData($_POST,$parsedObject,$_POST['dependentSTAT']);
										} elseif(isset($_POST['dependentSTAT']) && $_POST['dependentSTAT'] == 'DEL') {
												$view_controller->delEmpFormData($_GET,$_POST);
										}

										if(isset($_POST['childrenSTAT']) && (($_POST['childrenSTAT'] == 'ADD') || ($_POST['childrenSTAT'] == 'EDIT'))) {
												$parsedObject = $extractorForm->parseData($_POST);
												$view_controller->assignEmpFormData($_POST,$parsedObject,$_POST['childrenSTAT']);
										} elseif(isset($_POST['childrenSTAT']) && $_POST['childrenSTAT'] == 'DEL') {
												$view_controller->delEmpFormData($_GET,$_POST);
										}

										if(isset($_POST['conextSTAT']) && (($_POST['conextSTAT'] == 'ADD' && $locRights['add']) || ($_POST['conextSTAT'] == 'EDIT' && $locRights['edit']))) {
												$parsedObject = $extractorForm->parseData($_POST);
												$view_controller->assignEmpFormData($_POST,$parsedObject,$_POST['conextSTAT']);
										} elseif(isset($_POST['conextSTAT']) && $_POST['conextSTAT'] == 'DEL' && $locRights['delete']) {
												$view_controller->delEmpFormData($_GET,$_POST);
										}

                                        if(isset($_POST['empjobHistorySTAT'])) {
                                            if(($_POST['empjobHistorySTAT'] == 'ADD') && $locRights['add']) {
                                                $parsedObject = $extractorForm->parseAddData($_POST);
                                                $view_controller->assignEmpFormData($_POST,$parsedObject,$_POST['empjobHistorySTAT']);
                                            } elseif(($_POST['empjobHistorySTAT'] == 'EDIT') && $locRights['edit']) {
                                                $parsedObject = $extractorForm->parseEditData($_POST);
                                                $view_controller->assignEmpFormData($_POST,$parsedObject,$_POST['empjobHistorySTAT']);
                                            } elseif($_POST['empjobHistorySTAT'] == 'DEL' && $locRights['delete']) {
                                                $view_controller->delEmpFormData($_GET,$_POST);
                                            }
                                        }

										if(isset($_POST['brchSTAT']) && (($_POST['brchSTAT'] == 'ADD' && $locRights['add']) || ($_POST['brchSTAT'] == 'EDIT' && $locRights['edit']))) {
												$parsedObject = $extractorForm->parseData($_POST);
												$view_controller->assignEmpFormData($_POST,$parsedObject,$_POST['brchSTAT']);
										} elseif(isset($_POST['brchSTAT']) && $_POST['brchSTAT'] == 'DEL' && $locRights['delete']) {
												$view_controller->delEmpFormData($_GET,$_POST);
										}

										if(isset($_POST['attSTAT']) && (($_POST['attSTAT'] == 'ADD' && $locRights['add']) || ($_POST['attSTAT'] == 'EDIT' && $locRights['edit']))) {
												$parsedObject = $extractorForm->parseData($_POST);
												$view_controller->assignEmpFormData($_POST,$parsedObject,$_POST['attSTAT']);
										} elseif(isset($_POST['attSTAT']) && $_POST['attSTAT'] == 'DEL' && $locRights['delete']) {
												$view_controller->delEmpFormData($_GET,$_POST);
										}

										//handling failed Attachment upload
										if(isset($_POST['attSTAT']) && $_POST['attSTAT'] == 'ADD' && $locRights['add'] && $parsedObject === null) {
											$_GET['ATT_UPLOAD'] = 'FAILED';
										}


						$_POST['EditMode'] = 0;
						if((isset($_POST['STAT'])) && ($_POST['STAT'] == 'OWN')) {
							$extObject = $extractor->reloadData($_POST);
							$view_controller -> reDirect($_GET,$_POST,$extObject);
							break;
						} elseif(isset($_POST['sqlState']) && $_GET['capturemode'] == 'addmode') {
							$extObject = $extractor->reloadData($_POST);
							$view_controller -> reDirect($_GET,$_POST,$extObject);
							break;
						} elseif(isset($_POST['sqlState']) && $_GET['capturemode'] == 'updatemode' && $_POST['EditMode'] == '1') {
							$extObject = $extractor->reloadData($_POST);
							$view_controller -> reDirect($_GET,$_POST,$extObject);
							break;
						} else {
							$view_controller -> reDirect($_GET,$_POST);
							break;
						}
						//end of pim module


	case 'mt'		:  //beg of mt module

						$view_controller = new MTViewController();

							if(isset($_POST['delState']) && $_POST['delState']=='DeleteMode' && $locRights['delete']) {
							    $arrList[0]=$_POST['chkLocID'];
							    $view_controller->delParser(trim($_GET['mtcode']),$arrList);
							}

							if(isset($_GET['VIEW']) && $_GET['VIEW'] == 'MAIN' && ($locRights['view'] || $_GET['mtcode']=='BUG')) {
								$view_controller ->viewList($_GET,$_POST);
								break;
							}

						// choosing which extractor
						switch ($_GET['mtcode']) {

							case 'BUG'	:	if(isset($_POST['sqlState'])) {
												$extractor = new EXTRACTOR_Bugs();
											}
											break;

							case 'CPW'	:	if(isset($_POST['sqlState'])) {
												$extractor = new EXTRACTOR_ChangeUsers();
											}
											break;
						}



										if(isset($_POST['STAT']) && (($_POST['STAT'] == 'ADD' && $locRights['add']) || ($_POST['STAT'] == 'EDIT' && $locRights['edit']))) {
												$parsedObject = $extractor->parseData($_POST);
												$view_controller->assignData($_GET['mtcode'],$parsedObject,$_POST['STAT']);
										} elseif(isset($_POST['STAT']) && $_POST['STAT'] == 'DEL' && $locRights['delete']) {
												$view_controller->delAssignData($_GET['mtcode'],$_POST,$_GET);
										}

										if(isset($_POST['sqlState']) && $_POST['sqlState']=='NewRecord' && ($locRights['add'] || $_GET['mtcode']=='BUG')) {
												$parsedObject = $extractor->parseAddData($_POST);
												$view_controller->addData($_GET['mtcode'],$parsedObject);
												break;
										} elseif (isset($_POST['sqlState']) && ($_POST['sqlState']=='UpdateRecord') && ($locRights['edit'] || ($_GET['mtcode']=='BUG') || ($_GET['mtcode']=='CPW'))) {
												$parsedObject = $extractor->parseEditData($_POST);
												$view_controller->updateData($_GET['mtcode'],$_GET['id'],$parsedObject);
												break;
										} else {

												$view_controller -> reDirect($_GET);
												break;

										}

					//ending of mt module
						break;

	case 'rep' 	:  // beg. Reports module
						$view_controller = new RepViewController();

							if(isset($_POST['delState']) && $_POST['delState']=='DeleteMode' && $locRights['delete']) {
							    $arrList[0]=$_POST['chkLocID'];
							    $view_controller->delParser(trim($_GET['repcode']),$arrList);
							}

							if(isset($_GET['VIEW']) && $_GET['VIEW'] == 'MAIN') {
								$view_controller ->viewList($_GET,$_POST);
								break;
							}

						// choosing which extractor
						switch ($_GET['repcode']) {

							case 'EMPDEF'	:
											if(isset($_POST['sqlState'])) {

											$extractor = new EXTRACTOR_EmpReport();
											}
											break;

							case 'RUG'	:
											if(isset($_POST['USG'])) {
												$extractor = new EXTRACTOR_EmpRepUserGroups;
											}
											break;
						}

										if(isset($_POST['USG']) && $_POST['USG']=='SEL' && $locRights['add']) {
												$parsedObject = $extractor->parseAddData($_POST);
												$view_controller->addUserGroups($parsedObject);
										} elseif(isset($_POST['USG']) && $_POST['USG'] == 'DEL' && $locRights['delete']) {
												$view_controller->delUserGroups($_POST,$_GET);
										}

										if(isset($_POST['sqlState']) && $_POST['sqlState']=='NewRecord' && $locRights['add']) {
												$parsedObject = $extractor->parseAddData($_POST);
												$view_controller->addData($_GET['repcode'],$parsedObject);
												break;
										} elseif (isset($_POST['sqlState']) && $_POST['sqlState']=='UpdateRecord' && $locRights['edit']) {
												$parsedObject = $extractor->parseEditData($_POST);
												$view_controller->updateData($_GET['repcode'],$_GET['id'],$parsedObject);
												break;
										} elseif ((isset($_POST['sqlState'])) && ($_POST['sqlState'] == 'OWN' && $locRights['view'])) {
												$extObject = $extractor->reloadData($_POST);
												$view_controller -> reDirect($_GET,$_POST,$extObject);
												break;
										} else {
												$view_controller -> reDirect($_GET,$_POST);
												break;
										}

	case 'timeMod'	:

							switch ($_GET['timecode']) {
							case 'Time'	:	if (isset($_GET['action'])) {
												$timeController = new TimeController();
												$timesheetExtractor = new EXTRACTOR_Timesheet();
												$timeEventExtractor = new EXTRACTOR_TimeEvent();
												$timesheetSubmissionPeriodExtractor = new EXTRACTOR_TimesheetSubmissionPeriod();
												$workShiftExtractor = new EXTRACTOR_Workshift();

												$current=false;
												$useClientTime = false;
												$clientTime = null;
												$punchIn=false;
												$new=false;
												$return=null;

												switch ($_GET['action']) {
													case 'View_Current_Timesheet':	$current=true;

																					$clientTimezoneOffset = @$_GET['clientTimezoneOffset'];

																					settype($clientTimezoneOffset, 'integer');

																					$serverTimezoneOffset = ((int) date('Z')) / 60;
																					$timeZoneDiff = $clientTimezoneOffset - $serverTimezoneOffset;

																					if ($clientTimezoneOffset != $serverTimezoneOffset) {

																						$useClientTime = true;
																						$clientTimestamp = time() + $timeZoneDiff * 60;

																						$day = date('N', $clientTimestamp);

																						$objSubmissionPeriods = new TimesheetSubmissionPeriod();
						   																$timesheetSubmissionPeriods = $objSubmissionPeriods->fetchTimesheetSubmissionPeriods();
																						$timesheetSubmissionDay = $timesheetSubmissionPeriods[0]->getStartDay();
																						$dayDiff = $timesheetSubmissionDay - $day;

																						if ($dayDiff > 0) {
																							$dayDiff -= 7;
																						}

																						$sign = ($dayDiff < 0) ? "" : "+";

																						$clientStartDateTimestamp = strtotime("{$sign}{$dayDiff} day", (time() + $timeZoneDiff*60));

																						$clientStartDate = date('Y-m-d', $clientStartDateTimestamp);
																						$clientEndDate = date('Y-m-d', strtotime("+6 day", $clientStartDateTimestamp));

																					}

													case 'View_Timesheet' 		:
																					if ($current && $useClientTime) {
																						$timesheetPeriodId = $timesheetSubmissionPeriods[0]->getTimesheetPeriodId();
																						$obj = $timesheetExtractor->parseViewDataWithTimezoneDiff($clientStartDate, $clientEndDate, $timesheetPeriodId);
																					} else {
																						$obj = $timesheetExtractor->parseViewData($_POST);
																					}

																					if (isset($_GET['id'])) {
																						$obj->setTimesheetId($_GET['id']);
																					}
																					$timeController->setObjTime($obj);

                                                                     $timeController->viewTimesheet($current);

																					break;
													case 'View_Edit_Timesheet' :	$obj = $timesheetExtractor->parseViewData($_POST);
																					if (isset($_GET['id']) && !empty($_GET['id'])) {
																						$obj->setTimesheetId($_GET['id']);
																					}
																					if (isset($_GET['return']) && !empty($_GET['return'])) {
																						$return=$_GET['return'];
																					}
																					$timeController->setObjTime($obj);
																					$timeController->viewEditTimesheet($return);
																					break;
													case 'View_Detail_Timesheet' :	$obj = $timesheetExtractor->parseViewData($_POST);
																					if (isset($_GET['id']) && !empty($_GET['id'])) {
																						$obj->setTimesheetId($_GET['id']);
																					}
																					$timeController->setObjTime($obj);
																					$timeController->viewDetailedTimesheet();
																					break;
													case 'Edit_Timesheet'		:	$objs = $timeEventExtractor->parseEditData($_POST);
																					$timeController->setObjTime($objs);
																					$duplicateRows = $timeEventExtractor->getDetailedDuplicate();
																					$invalidDuration = $timeEventExtractor->getDetailedInvalidDuration();
																					$timeController->editTimesheet($_POST['nextAction'], $duplicateRows, $invalidDuration);
																					break;
													case 'Delete_Timesheet'		:	$objs = $timeEventExtractor->parseDeleteData($_POST);
																					$timeController->setObjTime($objs);
																					$timeController->deleteTimesheet($_POST['nextAction']);
																					break;


													/* Timegrid Controlers: Begin */

													case 'Edit_Timesheet_Grid'			:	$obj = $timesheetExtractor->parseViewData($_POST);
																							$timeController->setObjTime($obj);
																							$timeController->editTimesheetGrid();
																							break;
																							
													case 'Delete_Timesheet_Grid_Rows'	:
																							$obj = $timesheetExtractor->parseViewData($_POST);
																							$timeController->setObjTime($obj);
																							$timeController->deleteTimeGridRows();
																							break;

													case 'Update_Timeesheet_Grid'		:	$obj = $timesheetExtractor->parseViewData($_POST);
																							$timeController->setObjTime($obj);
																							$eventsList = $timeEventExtractor->parseEditTimegrid($_POST);
																							$timeController->updateTimegrid($eventsList);
																							break;

													case 'Timegrid_Fetch_Activities'	:	ob_clean();
																							echo $timeController->prepareProjectActivitiesResponse($_GET['projectId']);
																							break;
													/* Timegrid Controlers: End */

													case 'View_Work_Shifts'		:	$timeController->viewShifts();
																					break;
													case 'Add_Work_Shift'		:	$obj = $workShiftExtractor->parseAddData($_POST);
																					$timeController->setObjTime($obj);
																					$timeController->saveWorkShift();
																					break;
													case 'View_Edit_Work_Shift'	:   if (isset($_GET['id']) && !empty($_GET['id'])) {
																						$timeController->viewEditWorkShift($_GET['id']);
																					}
																					break;
													case 'Edit_Work_Shift'		:	$obj = $workShiftExtractor->parseEditData($_POST);
																					$timeController->setObjTime($obj);
																					$timeController->updateWorkShift();
																					break;
													case 'Delete_Work_Shifts'	:	$objs = $workShiftExtractor->parseDeleteData($_POST);
																					$timeController->setObjTime($objs);
																					$timeController->deleteWorkShifts();
																					break;
													case 'Submit_Timesheet'		:	$obj = $timesheetExtractor->parseChangeStatusData($_POST);
																					$timeController->setObjTime($obj);
																					$timeController->submitTimesheet();
																					break;
													case 'Cancel_Timesheet'		:	$obj = $timesheetExtractor->parseChangeStatusData($_POST);
																					$timeController->setObjTime($obj);
																					$timeController->cancelTimesheet();
																					break;
													case 'Approve_Timesheet'	:	$obj = $timesheetExtractor->parseChangeStatusData($_POST);
																					$timeController->setObjTime($obj);
																					$timeController->approveTimesheet();
																					break;
													case 'Reject_Timesheet'		:	$obj = $timesheetExtractor->parseChangeStatusData($_POST);
																					$timeController->setObjTime($obj);
																					$timeController->rejectTimesheet();
																					break;
													case 'View_Select_Employee'	:	$timeController->viewSelectEmployee();
																					break;
													case 'Fetch_Next_Timesheet'	:
																					$obj = $timesheetExtractor->parseViewData($_POST);
																					$timeController->setObjTime($obj);
																					$timeController->nextEmployeeTimesheet();
																					break;
													case 'Fetch_Prev_Timesheet'	:
																					$obj = $timesheetExtractor->parseViewData($_POST);
																					$timeController->setObjTime($obj);
																					$timeController->previousEmployeeTimesheet();
																					break;
													case 'Show_Punch_Time'		:	$timeController->showPunchTime();
																					break;
													case 'Punch_In'				:	$punchIn=true;
													case 'Punch_Out'			:	$obj = $timeEventExtractor->parsePunch($_POST, $punchIn);
																					$timeController->setObjTime($obj);
																					$timeController->punchTime($punchIn);
																					break;

													/* Attendance Controls: Begin */

													case 'Show_Punch_View'				:	$timeController->showPunchView();
																							break;

													case 'Save_Punch'					:	$timeController->savePunch();
																							break;

													case 'Show_My_Report'				:	$timeController->showAttendanceReportForm('My');
																							break;

													case 'Show_Employee_Report'			:	$timeController->showAttendanceReportForm('Emp');
																							break;

													case 'Generate_Attendance_Report'	:	$from = $_POST['txtFromDate'].' 00:00:00';
																							$to = $_POST['txtToDate'].' 23:59:59';

                                                                     $screenParam = array('timecode' => $_GET['timecode'], 'action' => 'Show_Employee_Report');
                                                                     $tokenGenerator = CSRFTokenGenerator::getInstance();
                                                                     $tokenGenerator->setKeyGenerationInput($screenParam);
                                                                     $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                                                                     if($token == $_POST['token']) {
                                                                        if ($_POST['optReportView'] == 'summary') {
                                                                           $timeController->generateAttendanceSummary($_POST['hdnEmpNo'], $from, $to, true);
                                                                        } elseif ($_POST['optReportView'] == 'detailed') {
                                                                           $timeController->generateAttendanceReport($_POST['hdnEmpNo'], $from, $to , null, null,true);
                                                                        }
                                                                     }
																							break;

													case 'Summary_Attendance_Report'	:	$from = $_POST['txtFromDate'].' 00:00:00';
																							$to = $_POST['txtFromDate'].' 23:59:59';
																							$timeController->generateAttendanceReport($_POST['hdnEmployeeId'], $from, $to);
																							break;

													case 'Save_Attendance_Report'		:	$timeController->saveAttendanceReport();
																							break;

													case 'Show_Attendance_Config'		:	$timeController->showAttendanceConfig();
																							break;

													case 'Save_Attendance_Config'		:	$timeController->saveAttendanceConfig();
																							break;

													/* Attendance Controls: End */

													case 'Time_Event_Home'		:	$timeController->timeEventHome();
																					break;
													case 'New_Time_Event_View'	:	$new=true;
													case 'Update_Event_View'	:	$timeController->timeEventEditView($new);
																					break;
													case 'Edit_Time_Event_View'	:	$timeController->timeEventHome();
																					break;
													case 'Time_Event_Save'		:	$obj = $timeEventExtractor->parseSingleEvent($_POST);
																					$timeController->setObjTime($obj);
																					$timeController->saveTimeEvent();
																					break;
													case 'Work_Week_Edit_View'	:	$timeController->defineWorkWeekView();
																					break;
													case 'Work_Week_Save'		:	$obj = $timesheetSubmissionPeriodExtractor->parseEditData($_POST);
																					$timeController->setObjTime($obj);
																					$timeController->saveWorkWeek();
																					break;
													case 'Employee_Report_Define':	$timeController->viewDefineEmployeeTimeReport();
																					break;
													case 'Employee_Report'		:	$obj = $timeEventExtractor->parseReportParams($_POST);
																					$timeController->setObjTime($obj);
                                                               $screenParam = array('timecode' => $_GET['timecode'], 'action' => 'Employee_Report_Define');
                                                               $tokenGenerator = CSRFTokenGenerator::getInstance();
                                                               $tokenGenerator->setKeyGenerationInput($screenParam);
                                                               $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                                                               if($token == $_POST['token']) {
                                                                  $timeController->viewEmployeeTimeReport();
                                                               }
																					break;
													case 'Project_Report_Define':	$timeController->viewDefineProjectReport();
																					break;
													case 'Project_Report'       :   $obj = $timeEventExtractor->parseProjectReportParams($_POST);
																					$timeController->setObjTime($obj);
                                                               $screenParam = array('timecode' => $_GET['timecode'], 'action' => 'Project_Report_Define');
                                                               $tokenGenerator = CSRFTokenGenerator::getInstance();
                                                               $tokenGenerator->setKeyGenerationInput($screenParam);
                                                               $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                                                               
                                                               if($token == $_POST['token']) {
                                                                  $timeController->viewProjectReport();
                                                               }
																					break;
													case 'Activity_Report'       :  $obj = $timeEventExtractor->parseActivityReportParams($_POST);
																					$timeController->setObjTime($obj);
																					$timeController->viewActivityReport();
																					break;
													case 'Select_Timesheets_View':
                                                               if (isset($_GET['cache'])) {
																						$_SESSION['posted'] = true;
																					}
																					$timeController->viewSelectTimesheet();
																					break;
													case 'Timesheet_Print_Preview' :$_SESSION['txtUserEmpID'] = $_POST['txtUserEmpID'];
																					$_SESSION['cmbUserEmpID'] = $_POST['cmbUserEmpID'];
																					$_SESSION['txtLocation'] = $_POST['txtLocation'];
																					$_SESSION['cmbLocation'] = $_POST['cmbLocation'];
																					$_SESSION['cmbRepEmpID'] = $_POST['cmbRepEmpID'];
																					$_SESSION['txtRepEmpID'] = $_POST['txtRepEmpID'];
																					$_SESSION['cmbEmploymentStatus'] = $_POST['cmbEmploymentStatus'];
																					$_SESSION['txtStartDate'] = $_POST['txtStartDate'];
																					$_SESSION['txtEndDate'] = $_POST['txtEndDate'];

																					$filterValues = array($_POST['cmbUserEmpID'],
																										  $_POST['txtLocation'],
																										  $_POST['txtRepEmpID'],
																										  $_POST['cmbEmploymentStatus']);

																					$obj = $timesheetExtractor->parseViewData($_POST);
																					$timeController->setObjTime($obj);
                                                               $screenParam = array('timecode' => $_GET['timecode'], 'action' => 'Select_Timesheets_View');
                                                               $tokenGenerator = CSRFTokenGenerator::getInstance();
                                                               $tokenGenerator->setKeyGenerationInput($screenParam);
                                                               $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));

                                                               if($token == $_POST['token']) {
                                                                  $timeController->viewTimesheetPrintPreview($filterValues);
                                                               }
																					break;
													case 'Print_Timesheet_Get_Page':$filterValues = array($_POST['txtEmpID'],
																										  $_POST['txtLocation'],
																										  $_POST['txtRepEmpID'],
																										  $_POST['txtEmploymentStatus']);
																					$obj = $timesheetExtractor->parseViewData($_POST);
																					$timeController->setObjTime($obj);
																					$timeController->viewTimesheelBulk($filterValues, $_GET['page']);
																					break;
													case 'Print'				   :$timeController->showPrint();
																					break;
													}
											} else {
												trigger_error("Invalid Action ".$_GET['action'], E_USER_NOTICE);
											}
											break;
							case 'benefits' : include ROOT_PATH.'/lib/controllers/Benefits.inc.php';
											  break;
							default		: trigger_error("Invalid Action ".$_GET['action'], E_USER_NOTICE);
						}
						break;

	case 'recruitMod'	:
						$recruitController = new RecruitmentController();
						$recruitController->handleRequest($_GET['recruitcode']);
						break;

}

@ob_end_flush();  ?>
