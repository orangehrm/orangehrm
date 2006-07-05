<?
session_start();

if(!isset($_SESSION['fname'])) { 

	header("Location: ../../relogin.htm");
	exit();
}

define('ROOT_PATH', $_SESSION['path']);
require_once ROOT_PATH . '/lib/exception/ExceptionHandler.php';
require_once ROOT_PATH . '/lib/common/Language.php';

require_once ROOT_PATH . '/lib/controllers/ViewController.php';
require_once ROOT_PATH . '/lib/controllers/MTViewController.php';
require_once ROOT_PATH . '/lib/controllers/EmpViewController.php';
require_once ROOT_PATH . '/lib/controllers/RepViewController.php';

require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_BankInfo.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_TaxInfo.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_CashBen.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_CostCenter.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_CountryInfo.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_CurrencyTypes.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_DwellingType.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_ElectorateInfo.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_EmployeeCat.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_EmployeeGroup.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_EmployeeTypes.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_ExtraCurrActCat.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_HierarchyDefInfo.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_JDCategory.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_Branches.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_Location.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_MembershipType.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_NationalityInfo.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_NonCashBen.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_CompHier.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_QualificationType.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_ReligionInfo.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_SatutoryInfo.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_RatingTypes.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_RatingGrade.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_RouteInfo.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_SalaryGrades.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_Skills.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_ExtraCurActInfo.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_MembershipInfo.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_JDKra.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_LanguageInfo.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_UniformType.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_ExtraCurActInfo.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_Qualifications.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_JDType.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_SubjectInfo.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_SubSkillInfo.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_CashBenSal.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_NonCashBenSal.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_CorpTit.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_Designations.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_ProvinceInfo.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_SalCurDet.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_DesQual.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_DesQuaSub.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_DesDis.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_DistrictInfo.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_EEOJobCat.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_Licenses.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_EthnicRace.php';

require_once ROOT_PATH . '/lib/extractor/maintenance/EXTRACTOR_Bugs.php';
require_once ROOT_PATH . '/lib/extractor/maintenance/EXTRACTOR_FileVersions.php';
require_once ROOT_PATH . '/lib/extractor/maintenance/EXTRACTOR_Users.php';
require_once ROOT_PATH . '/lib/extractor/maintenance/EXTRACTOR_DbVersions.php';
require_once ROOT_PATH . '/lib/extractor/maintenance/EXTRACTOR_Modules.php';
require_once ROOT_PATH . '/lib/extractor/maintenance/EXTRACTOR_UserGroups.php';
require_once ROOT_PATH . '/lib/extractor/maintenance/EXTRACTOR_Versions.php';
require_once ROOT_PATH . '/lib/extractor/maintenance/EXTRACTOR_Rights.php';
require_once ROOT_PATH . '/lib/extractor/maintenance/EXTRACTOR_ChangeUsers.php';

require_once ROOT_PATH . '/lib/extractor/hrfunct/EXTRACTOR_EmpInfo.php';
require_once ROOT_PATH . '/lib/extractor/hrfunct/EXTRACTOR_EmpLanguage.php';
require_once ROOT_PATH . '/lib/extractor/hrfunct/EXTRACTOR_EmpJobSpec.php';
require_once ROOT_PATH . '/lib/extractor/hrfunct/EXTRACTOR_EmpConExt.php';
require_once ROOT_PATH . '/lib/extractor/hrfunct/EXTRACTOR_EmpBasSalary.php';
require_once ROOT_PATH . '/lib/extractor/hrfunct/EXTRACTOR_EmpMembership.php';
require_once ROOT_PATH . '/lib/extractor/hrfunct/EXTRACTOR_EmpWorkExp.php';
require_once ROOT_PATH . '/lib/extractor/hrfunct/EXTRACTOR_EmpExCur.php';
require_once ROOT_PATH . '/lib/extractor/hrfunct/EXTRACTOR_EmpQualification.php';
require_once ROOT_PATH . '/lib/extractor/hrfunct/EXTRACTOR_EmpQualSub.php';
require_once ROOT_PATH . '/lib/extractor/hrfunct/EXTRACTOR_EmpCashBen.php';
require_once ROOT_PATH . '/lib/extractor/hrfunct/EXTRACTOR_EmpNonCashBen.php';
require_once ROOT_PATH . '/lib/extractor/hrfunct/EXTRACTOR_EmpTax.php';
require_once ROOT_PATH . '/lib/extractor/hrfunct/EXTRACTOR_EmpPassPort.php';
require_once ROOT_PATH . '/lib/extractor/hrfunct/EXTRACTOR_EmpBank.php';
require_once ROOT_PATH . '/lib/extractor/hrfunct/EXTRACTOR_EmpAttach.php';
require_once ROOT_PATH . '/lib/extractor/hrfunct/EXTRACTOR_EmpRepTo.php';
require_once ROOT_PATH . '/lib/extractor/hrfunct/EXTRACTOR_EmpEss.php';
require_once ROOT_PATH . '/lib/extractor/hrfunct/EXTRACTOR_EmpDependents.php';
require_once ROOT_PATH . '/lib/extractor/hrfunct/EXTRACTOR_EmpSkill.php';
require_once ROOT_PATH . '/lib/extractor/hrfunct/EXTRACTOR_EmpLicenses.php';
require_once ROOT_PATH . '/lib/extractor/hrfunct/EXTRACTOR_EmpDependents.php';
require_once ROOT_PATH . '/lib/extractor/hrfunct/EXTRACTOR_EmpChildren.php';
require_once ROOT_PATH . '/lib/extractor/hrfunct/EXTRACTOR_EmpEmergencyCon.php';

require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_JobTitle.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_JobTitEmpStat.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_EmpStat.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_GenInfo.php';

require_once ROOT_PATH . '/lib/extractor/report/EXTRACTOR_EmpReport.php';
//require_once ROOT_PATH . '/lib/extractor/report/EXTRACTOR_ReportGenerator.php';
require_once ROOT_PATH . '/lib/extractor/report/EXTRACTOR_EmpRepUserGroups.php';

if(isset($_GET['uniqcode'])) {
	$moduletype = 'admin';
} elseif (isset($_GET['reqcode'])) {
	$moduletype = 'hr';
} elseif (isset($_GET['mtcode'])) {
	$moduletype = 'mt';
} elseif (isset($_GET['repcode'])) {
	$moduletype = 'rep';
}

//authentication
require_once ROOT_PATH . '/lib/models/maintenance/Rights.php';
require_once ROOT_PATH . '/lib/models/maintenance/UserGroups.php';

$rights = new Rights();

$locRights = array('add'=> false , 'edit'=> false , 'delete'=> false, 'view'=> false);

define('Admin', 'MOD001');
define('PIM', 'MOD002');
define('MT', 'MOD003');
	
switch ($moduletype) {   
	case 'admin'	:	$locRights = $rights->getRights($_SESSION['userGroup'],Admin); break;
	case 'hr'		:	$locRights = $rights->getRights($_SESSION['userGroup'],PIM); break;
	case 'mt'		:	$locRights = $rights->getRights($_SESSION['userGroup'],MT); break;
}

if(!is_array($locRights) && $locRights == false)
	$locRights = array('add'=> false , 'edit'=> false , 'delete'=> false, 'view'=> false);

if($_SESSION['isAdmin'] != 'Yes')	
	$locRights = array('add'=> false , 'edit'=> false , 'delete'=> false, 'view'=> true);
	
$ugroup = new UserGroups();
$ugDet = $ugroup ->filterUserGroups($_SESSION['userGroup']);

$locRights['repDef'] = $ugDet[0][2] == '1' ? true : false;

$_SESSION['localRights'] = $locRights;

switch ($moduletype) {
	
	case 'admin' 	:  // beg. admin module
	
					$view_controller = new ViewController();


						if(isset($_POST['delState']) && $_POST['delState']=='DeleteMode' && $locRights['delete']) {
							    $arrList[0]=$_POST['chkLocID'];
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
					}

										if(isset($_POST['sqlState']) && $_POST['sqlState']=='NewRecord' && $locRights['add']) {
												$parsedObject = $extractor->parseAddData($_POST);
												$view_controller->addData($_GET['uniqcode'],$parsedObject);
												break;
										} elseif (isset($_POST['sqlState']) && $_POST['sqlState']=='UpdateRecord' && $locRights['edit']) {
												$parsedObject = $extractor->parseEditData($_POST);
												$view_controller->updateData($_GET['uniqcode'],$_GET['id'],$parsedObject);
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
						} elseif(isset($_POST['sqlState']) && $_GET['capturemode'] == 'addmode') {
							$extObject = $extractor->reloadData($_POST);
							$view_controller -> reDirect($_GET,$extObject);
							break;
						} elseif(isset($_POST['sqlState']) && $_GET['capturemode'] == 'updatemode') {
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
							$view_controller ->viewList($_GET,$_POST);
							break;
						} 
						
					// choosing which extractor

					if(isset($_POST['econtactSTAT']) && $_POST['econtactSTAT']!= '') {
						$extractorForm = new EXTRACTOR_EmpEmergencyCon();
					}

					if(isset($_POST['passportSTAT']) && $_POST['passportSTAT']!= '') {
						$extractorForm = new EXTRACTOR_EmpPassPort();
					}

					if(isset($_POST['brchSTAT']) && $_POST['brchSTAT']!= '') {
						$extractorForm = new EXTRACTOR_EmpBank();
					}

					if(isset($_POST['attSTAT']) && $_POST['attSTAT']!= '') {
						$extractorForm = new EXTRACTOR_EmpAttach();
					}

					
					if(isset($_POST['depSTAT']) && $_POST['depSTAT']!= '') {
						$extractorForm = new EXTRACTOR_EmpDependents();
					}

					if(isset($_POST['chiSTAT']) && $_POST['chiSTAT']!= '') {
						$extractorForm = new EXTRACTOR_EmpChildren();
					}

					
					if(isset($_POST['dependentSTAT']) && $_POST['dependentSTAT']!= '') {
						$extractorForm = new EXTRACTOR_EmpDependents();
					}


					switch ($_GET['reqcode']) {	
							
							case 'EMP'	:	
										if(isset($_POST['sqlState'])) {
											
											$extractor = new EXTRACTOR_EmpInfo();
										}
									
										break;
										
							case 'ESS'	:	
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_EmpEss();
										}
										
										break;	
							
							case 'LAN'	:	
										if(isset($_POST['STAT'])) {
											$extractor = new EXTRACTOR_EmpLanguage();
										}
									
										break;	
							
							case 'JSP'	:	
										if(isset($_POST['STAT'])) {
											$extractor = new EXTRACTOR_EmpJobSpec();
										}
										
										break;
										
										
							case 'CXT'	:	
										if(isset($_POST['STAT'])) {
											$extractor = new EXTRACTOR_EmpConExt();
										}
										
										break;	
										
							case 'SAL'	:	
										if(isset($_POST['STAT'])) {
											$extractor = new EXTRACTOR_EmpBasSalary();
										}
										
										break;
										
							case 'MEM'	:	
										if(isset($_POST['STAT'])) {
											$extractor = new EXTRACTOR_EmpMembership();
										}
										
										break;
										
							case 'EXP'	:	
										if(isset($_POST['STAT'])) {
											$extractor = new EXTRACTOR_EmpWorkExp();
										}
										
										break;								
										
							case 'EXC'	:	
										if(isset($_POST['STAT'])) {
											$extractor = new EXTRACTOR_EmpExCur();
										}
										
										break;	
										
							case 'QUA'	:	
										if(isset($_POST['STAT'])) {
											$extractor = new EXTRACTOR_EmpQualification();
										}
										
										break;	
											
							case 'QAS'	:	
										if(isset($_POST['STAT'])) {
											$extractor = new EXTRACTOR_EmpQualSub();
										}
										
										break;	

							case 'CBN'	:	
										if(isset($_POST['STAT'])) {
											$extractor = new EXTRACTOR_EmpCashBen();
										}
										
										break;	
										
							case 'NBN'	:	
										if(isset($_POST['STAT'])) {
											$extractor = new EXTRACTOR_EmpNonCashBen();
										}
										
										break;
										
							case 'REP'	:	
										if(isset($_POST['STAT'])) {
											$extractor = new EXTRACTOR_EmpRepTo();
										}
										
										break;
										
							case 'SKI'	:	
										if(isset($_POST['STAT'])) {
											$extractor = new EXTRACTOR_EmpSkill();
										}
										
										break;
										
							case 'LIC'	:	
										if(isset($_POST['STAT'])) {
											$extractor = new EXTRACTOR_EmpLicenses();
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
												$parsedObject = $extractor->parseData($_POST);
												$view_controller->assignData($_GET['reqcode'],$parsedObject,$_POST['STAT']);
										} elseif(isset($_POST['STAT']) && $_POST['STAT'] == 'DEL' && $locRights['delete']) {
												$view_controller->delAssignData($_GET['reqcode'],$_POST,$_GET);
										}
										
										if(isset($_POST['econtactSTAT']) && (($_POST['econtactSTAT'] == 'ADD' && $locRights['add']) || ($_POST['econtactSTAT'] == 'EDIT' && $locRights['edit']))) {
												$parsedObject = $extractorForm->parseData($_POST);
												$view_controller->assignEmpFormData($_POST,$parsedObject,$_POST['econtactSTAT']);
										} elseif(isset($_POST['econtactSTAT']) && $_POST['econtactSTAT'] == 'DEL' && $locRights['delete']) {
												$view_controller->delEmpFormData($_GET,$_POST);
										}
										
										if(isset($_POST['passportSTAT']) && (($_POST['passportSTAT'] == 'ADD' && $locRights['add']) || ($_POST['passportSTAT'] == 'EDIT' && $locRights['edit']))) {
												$parsedObject = $extractorForm->parseData($_POST);
												$view_controller->assignEmpFormData($_POST,$parsedObject,$_POST['passportSTAT']);
										} elseif(isset($_POST['passportSTAT']) && $_POST['passportSTAT'] == 'DEL' && $locRights['delete']) {
												$view_controller->delEmpFormData($_GET,$_POST);
										}
										

										if(isset($_POST['depSTAT']) && (($_POST['depSTAT'] == 'ADD' && $locRights['add']) || ($_POST['depSTAT'] == 'EDIT' && $locRights['edit']))) {
												$parsedObject = $extractorForm->parseData($_POST);
												$view_controller->assignEmpFormData($_POST,$parsedObject,$_POST['depSTAT']);
										} elseif(isset($_POST['depSTAT']) && $_POST['depSTAT'] == 'DEL' && $locRights['delete']) {
												$view_controller->delEmpFormData($_GET,$_POST);
										}
										
										if(isset($_POST['chiSTAT']) && (($_POST['chiSTAT'] == 'ADD' && $locRights['add']) || ($_POST['chiSTAT'] == 'EDIT' && $locRights['edit']))) {
												$parsedObject = $extractorForm->parseData($_POST);
												$view_controller->assignEmpFormData($_POST,$parsedObject,$_POST['chiSTAT']);
										} elseif(isset($_POST['chiSTAT']) && $_POST['chiSTAT'] == 'DEL' && $locRights['delete']) {
												$view_controller->delEmpFormData($_GET,$_POST);
										}
										

										if(isset($_POST['dependentSTAT']) && (($_POST['dependentSTAT'] == 'ADD' && $locRights['add']) || ($_POST['dependentSTAT'] == 'EDIT' && $locRights['edit']))) {
												$parsedObject = $extractorForm->parseData($_POST);
												$view_controller->assignEmpFormData($_POST,$parsedObject,$_POST['dependentSTAT']);
										} elseif(isset($_POST['dependentSTAT']) && $_POST['dependentSTAT'] == 'DEL' && $locRights['delete']) {
												$view_controller->delEmpFormData($_GET,$_POST);
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
						
							case 'BUG'	:	
											if(isset($_POST['sqlState'])) {
												$extractor = new EXTRACTOR_Bugs();
											}
										
											break;
											
							case 'DVR'	:	
											if(isset($_POST['sqlState'])) {
												$extractor = new EXTRACTOR_DbVersions();
											}
										
											break;
											
							case 'MOD'	:	
											if(isset($_POST['sqlState'])) {
												$extractor = new EXTRACTOR_Modules();
											}
										
											break;
											
							case 'USG'	:	
											if(isset($_POST['sqlState'])) {
												$extractor = new EXTRACTOR_UserGroups();
											}
										
											break;
											
							 case 'VER'	:	
											if(isset($_POST['sqlState'])) {
												$extractor = new EXTRACTOR_Versions();
											}
										
											break;				
						
						
						case 'FVR'	:	
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_FileVersions();
										}

										break;	
										
						case 'USR'	:	
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_Users();
										}

										break;
										
						case 'CPW'	:	
										if(isset($_POST['sqlState'])) {
											$extractor = new EXTRACTOR_ChangeUsers();
										}

										break;	
										
						case 'UGR'	:	
										if(isset($_POST['STAT'])) {
											$extractor = new EXTRACTOR_Rights();
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
										} elseif (isset($_POST['sqlState']) && $_POST['sqlState']=='UpdateRecord' && ($locRights['edit'] || $_GET['mtcode']=='BUG')) {
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
						
							if(isset($_POST['delState']) && $_POST['delState']=='DeleteMode' && $locRights['repDef']) {
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
					
									/*	if(isset($_POST['STAT']) && $_POST['STAT']=='REP') {
												$parsedObject = $extractor->parseData($_POST);
												$view_controller->assignData($_GET['repcode'],$parsedObject);
												break;
										}*/ 

										if(isset($_POST['USG']) && $_POST['USG']=='SEL' && $locRights['repDef']) {
												$objectArr = $extractor->parseAddData($_POST);
												$view_controller->addUserGroups($objectArr);
										} elseif(isset($_POST['USG']) && $_POST['USG'] == 'DEL' && $locRights['repDef']) {
												$view_controller->delUserGroups($_POST,$_GET);
										}
										
										if(isset($_POST['sqlState']) && $_POST['sqlState']=='NewRecord' && $locRights['repDef']) {
												$parsedObject = $extractor->parseAddData($_POST);
												$view_controller->addData($_GET['repcode'],$parsedObject);
												break;
										} elseif (isset($_POST['sqlState']) && $_POST['sqlState']=='UpdateRecord' && $locRights['repDef']) {
												$parsedObject = $extractor->parseEditData($_POST);
												$view_controller->updateData($_GET['repcode'],$_GET['id'],$parsedObject);
												break;
										} elseif ((isset($_POST['sqlState'])) && ($_POST['sqlState'] == 'OWN' && $locRights['repDef'])) {
												$extObject = $extractor->reloadData($_POST);
												$view_controller -> reDirect($_GET,$_POST,$extObject);
												break;
										} else {
												$view_controller -> reDirect($_GET,$_POST);
												break;
										}
	
						
}