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

if(! defined('ROOT_PATH'))
	define('ROOT_PATH', '/var/www/html/orangehrm-2.3');

require_once ROOT_PATH . '/lib/common/TemplateMerger.php';
require_once ROOT_PATH . '/lib/common/authorize.php';
require_once ROOT_PATH . '/lib/models/benefits/HspPayPeriod.php';
require_once ROOT_PATH . '/lib/models/benefits/HspSummary.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpInfo.php';
require_once ROOT_PATH . '/lib/models/benefits/mail/HspMailNotification.php';
require_once ROOT_PATH . '/lib/models/benefits/HspPaymentRequest.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpInfo.php';
require_once ROOT_PATH . '/lib/models/benefits/DefineHsp.php';
require_once ROOT_PATH . '/lib/logger/Logger.php';
require_once ROOT_PATH . '/lib/logs/LogFileWriter.php';
require_once ROOT_PATH . '/lib/models/eimadmin/EmailNotificationConfiguration.php';
require_once ROOT_PATH . '/lib/utils/CSRFTokenGenerator.php';

class BenefitsController {
	private $authorizeObj;

	public function __construct() {
		$this->authorizeObj = new authorize($_SESSION['empID'], $_SESSION['isAdmin']);
	}

	public function __distruct() {

	}

	public static function redirect($message=null, $url = null) {

		if (isset($url)) {
			$mes = "";
			if (isset($message)) {
				$mes = "&message=";
			}
			$url=array($url.$mes);
			$id="";
		} else if (isset($message)) {
			preg_replace('/[&|?]+id=[A-Za-z0-9]*/', "", $_SERVER['HTTP_REFERER']);

			if (preg_match('/&/', $_SERVER['HTTP_REFERER']) > 0) {
				$message = "&message=".$message;
				$url = preg_split('/(&||\?)message=[A-Za-z0-9]*/', $_SERVER['HTTP_REFERER']);
			} else {
				$message = "?message=".$message;
			}

			if (isset($_REQUEST['id']) && !empty($_REQUEST['id']) && !is_array($_REQUEST['id'])) {
				$id = "&id=".$_REQUEST['id'];
			} else {
				$id="";
			}
		} else {
			if (isset($_REQUEST['id']) && !empty($_REQUEST['id']) && (preg_match('/&/', $_SERVER['HTTP_REFERER']) > 0)) {
				$id = "&id=".$_REQUEST['id'];
			} else if (preg_match('/&/', $_SERVER['HTTP_REFERER']) == 0){
				$id = "?id=".$_REQUEST['id'];
			} else {
				$id="";
			}
		}

		header("Location: ".$url[0].$message.$id);
		exit(0);
	}

	public static function selectYear($action) {
		switch ($action) {
			case 'Schedule' : $heading = 'PayrollSchedule';
							  $action = 'List_Benefits_Schedule';
							  break;
			case 'Hsp_Summary' : $heading = 'EmployeeHspSummary';
								 $action = 'Hsp_Summary';
							     break;
			case 'Hsp_Summary_Employee' : $heading = 'PersonalHspSummary';
										  $action = 'Hsp_Summary_Employee&amp;employeeId='.$_SESSION['empID'];
									      break;
		}

		$years = HspPayPeriod::getYears();

		$path = "/templates/benefits/selectYear.php";
		$tmpOb[0]=$heading;
		$tmpOb[1]=$years;
		$tmpOb[2]=$action;

		$template = new TemplateMerger($tmpOb, $path);

		$template->display($action);
	}

	public static function selectYearAndEmployee($action) {
		switch ($action) {
			case 'Expenditure' : if ($_SESSION['isAdmin'] == 'No') {
								     die('You are not authorized to view this page');
								 }
			
								 $heading = 'HealthSavingsPlanUsedList';
							     $action = 'Hsp_Expenditures';
							     break;
			case 'Hsp_Summary' : $heading = 'EmployeeHspSummary';
								 $action = 'Hsp_Summary';
							     break;
			case 'Hsp_Summary_Employee' : $heading = 'PersonalHspSummary';
										  $action = 'Hsp_Summary_Employee&amp;employeeId='.$_SESSION['empID'];
									      break;
			case 'Used_Select_Year' : if ($_SESSION['isAdmin'] == 'No') {
								      	die('You are not authorized to view this page');
								 	  }
								 	  
								 	  $heading = 'HspUsed';
							  		  $action = 'Hsp_Used';
							          break;
		}

		$years = HspPayPeriod::getYears();

		$path = "/templates/benefits/selectEmployeeAndYear.php";

		$tmpOb[0]=$heading;
		$tmpOb[1]=$years;
		$tmpOb[2]=$action;

		$template = new TemplateMerger($tmpOb, $path);

		$template->display($action);
	}

	public static function listPayPeriods($year) {
      $screenParam = array('benefitcode' => $_GET['benefitcode'], 'action' => $_GET['action'], 'year' => $year);
      $tokenGenerator = CSRFTokenGenerator::getInstance();
      $tokenGenerator->setKeyGenerationInput($screenParam);
      $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
      
		$payPeriods = HspPayPeriod::listPayPeriods($year);

		$path = "/templates/benefits/listPayPeriods.php";

		$tmpOb[0] = $payPeriods;
		$tmpOb[1] = $year;
		$tmpOb[2] = new authorize($_SESSION['empID'], $_SESSION['isAdmin']);
      $tmpOb['token'] = $token;

		$template = new TemplateMerger($tmpOb, $path);

		$template->display();
	}

	public static function viewAddPayPeriod($year=null) {
      $screenParam = array('benefitcode' => $_GET['benefitcode'], 'action' => $_GET['action']);
      $tokenGenerator = CSRFTokenGenerator::getInstance();
      $tokenGenerator->setKeyGenerationInput($screenParam);
      $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));

		if ($_SESSION['isAdmin'] == 'No') {
		    die('You are not authorized to view this page');
		}
		
		$path = "/templates/benefits/addPayPeriods.php";

		if (isset($year)) {
			$tmpOb[0] = $year;
		} else {
			$tmpOb[0] = date('Y');
		}
      $tmpOb['token'] = $token;
		$template = new TemplateMerger($tmpOb, $path);

		$template->display();
	}

	public static function viewEditPayPeriod($year, $id) {
		$path = "/templates/benefits/editPayPeriods.php";
      $screenParam = array('benefitcode' => $_GET['benefitcode'], 'action' => $_GET['action'], 'id' => $_GET['id']);
      $tokenGenerator = CSRFTokenGenerator::getInstance();
      $tokenGenerator->setKeyGenerationInput($screenParam);
      $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));

		$tmpOb[0] = $year;
		$tmpOb[1] = HspPayPeriod::getPayPeriod($id);
      $tmpOb['token'] = $token;
		$template = new TemplateMerger($tmpOb, $path);

		$template->display();
	}

	public static function addPayPeriod($payPeriod) {
		$msg = 'UPDATE_SUCCESS';
      $screenParam = array('benefitcode' => $_GET['benefitcode'], 'action' => 'View_Add_Pay_Period');
      $tokenGenerator = CSRFTokenGenerator::getInstance();
      $tokenGenerator->setKeyGenerationInput($screenParam);
      $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
      
		try {
         $msg = 'UNKNOWN_ERROR_FAILURE';
         if($token == $_POST['token']) {
            $res = $payPeriod->add();
            $msg = 'UPDATE_SUCCESS';
         }
		} catch (Exception $e) {
			$msg = 'UNKNOWN_ERROR_FAILURE';
		}

		self::redirect($msg, '?benefitcode=Benefits&action=List_Benefits_Schedule&year='.date('Y', strtotime($payPeriod->getCheckDate())));
	}

	public static function editPayPeriod($payPeriod) {
		$msg = 'UPDATE_SUCCESS';

      $screenParam = array('benefitcode' => $_GET['benefitcode'], 'action' => 'View_Edit_Pay_Period', 'id' => $_POST['txtPayPeriodId']);
      $tokenGenerator = CSRFTokenGenerator::getInstance();
      $tokenGenerator->setKeyGenerationInput($screenParam);
      $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
      
		try {
         $msg = 'UNKNOWN_ERROR_FAILURE';
         if($token == $_POST['token']) {
            $res = $payPeriod->update();
            $msg = 'UPDATE_SUCCESS';
         }
		} catch (Exception $e) {
			$msg = 'UNKNOWN_ERROR_FAILURE';
		}

		self::redirect($msg, '?benefitcode=Benefits&action=List_Benefits_Schedule&year='.date('Y', strtotime($payPeriod->getCheckDate())));
	}

	public static function deletePayPeriods($payPeriods, $year) {
		$msg = 'DELETE_SUCCESS';
      $screenParam = array('benefitcode' => $_GET['benefitcode'], 'action' => 'List_Benefits_Schedule', 'year' => $year);
      $tokenGenerator = CSRFTokenGenerator::getInstance();
      $tokenGenerator->setKeyGenerationInput($screenParam);
      $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
      
		try {
         if($token == $_POST['token']) {
            if (count($payPeriods) > 0) {
               for ($i=0; $i<count($payPeriods); $i++) {
                  $payPeriods[$i]->delete();
               }
            } else {
               $msg = 'NO_PAY_PERIODS';
            }
         } else {
            $msg = 'DELETE_FAILURE';
         }
		} catch (PayPeriodException $e) {
			switch ($e->getCode()) {
				case PayPeriodException::INVALID_ID : $msg = 'INVALID_ID_FAILURE';
													  break;
				case PayPeriodException::INVALID_ROW_COUNT : $msg = 'DELETE_FAILURE';
													  		 break;
				default : $msg = 'UNKNOWN_ERROR_FAILURE';
						  break;
			}
		}

		self::redirect($msg, '?benefitcode=Benefits&action=List_Benefits_Schedule&year='.$year);
	}


	public static function viewHspSummary($year, $employeeId=null, $saveSuccess=null) {
		$authorizeObj = new authorize($_SESSION['empID'], $_SESSION['isAdmin']);
      $screenParam = array('benefitcode' => $_GET['benefitcode'], 'action' => $_GET['action']);
      $tokenGenerator = CSRFTokenGenerator::getInstance();
      $tokenGenerator->setKeyGenerationInput($screenParam);
      $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));

		if (!$authorizeObj->isAdmin()) { // Check whether the Admin
			if ($employeeId != $_SESSION['empID']) {
				//$error['notAdmin'] = "You are not authorised";
				self::searchHspSummary($_SESSION['empID'], $year);
				exit(0);
			}
		}

		if (Config::getHspCurrentPlan() == 0) { // Check whether the HSP plan has been defined
		    $error['hspPlanNotDefined'] = true;
          $error['token'] = $token;
		}

		if ($employeeId == "leftNull") {
		    $error['nonExistedEmployeeSearch'] = true;
          $error['token'] = $token;
		}

		if (isset($error)) { // If errors found
		    $path = "/templates/benefits/hspSummary.php";
		    $template = new TemplateMerger($error, $path);
		    $template->setError(true);
		    $template->display();
		} else { // If no errors found, fetching summary is tried.
			try { // Saving initial summary. Exception is thrown if `hs_hr_employee` table is empty
				switch (Config::getHspCurrentPlan()) {
					case 1:
						self::_saveInitialRecords($year, 1);
						break;
					case 2:
						self::_saveInitialRecords($year, 2);
						break;
					case 3:
						self::_saveInitialRecords($year, 3);
						break;
					case 4:
						self::_saveInitialRecords($year, 1);
						self::_saveInitialRecords($year, 3);
						break;
					case 5:
						self::_saveInitialRecords($year, 2);
						self::_saveInitialRecords($year, 3);
						break;
					case 6:
						self::_saveInitialRecords($year, 1);
						self::_saveInitialRecords($year, 2);
						break;
				}

				// Setting the Page No
				if (isset($_POST['pageNo'])) {
			    	$pageNo = $_POST['pageNo'];
				} else {
			    	$pageNo = 1;
				}

				// Setting records that are used in /templates/benefits/hspSummary.php
				$tmpOb[0]="hspSummary";
				$tmpOb[1]=HspSummary::fetchHspSummary($year, $pageNo);
				$tmpOb[2]=$year;
				$tmpOb[3]=$pageNo;
				$tmpOb[4]=HspSummary::recordsCount($year, Config::getHspCurrentPlan());
            $tmpOb['token'] = $token;
				if (isset($saveSuccess)) {
					$tmpOb[5] = $saveSuccess;
				} else {
				    $tmpOb[5] = null;
				}
				$tmpOb[6]=EmpInfo::getEmployeeMainDetails();
				$tmpOb[7]=HspSummary::getYears();


			} catch(Exception $e) {
					$error['noEmployeeRecords'] = true;
               $error['token'] = $token;
			}

			// Setting template paths
			if (isset($_GET['printPdf']) && $_GET['printPdf'] == 1) {
				$tmpOb[1]=HspSummary::fetchHspSummary($year, -1);
				if ($_GET['pdfName'] == "All-Employees-HSP-Summary") {
					$path = "/plugins/printBenefits/pdfHspSummary.php";
				} elseif ($_GET['pdfName'] == "Personal-HSP-Summary") {
					if (!isset($employeeId)) {
						$empId = $_GET['empId'];
					}

					$tmpOb[1]=HspSummary::fetchPersonalHspSummary($year, $empId);
					$path = "/plugins/printBenefits/pdfPersonalHspSummary.php";
				}
			} else {
				$path = "/templates/benefits/hspSummary.php";
			}

			if (isset($employeeId) && !isset($_GET['printPdf'])) {
				$path = "/templates/benefits/personalHspSummary.php";
			}


			if (isset($_GET['printPdf']) && $_GET['printPdf'] == 1) {
				$template = new TemplateMerger($tmpOb, $path, 'pdfHeader.php', 'pdfFooter.php');
			} else {
				if (isset($error)) {
				    $template = new TemplateMerger($error, $path);
				    $template->setError(true);
				} else {
				    $template = new TemplateMerger($tmpOb, $path);
				}

			}

			$template->display();

		} // If no errors found, fetching summary is tried. Code ends.

	}

	/**
	 * Used when HSP plan has not been set and ESS user tries to view his/her Personal HSP Summary
	 */

	public static function HspNotDefined() {
	    $path = "/templates/benefits/hspSummary.php";
	    $error['hspNotDefined'] = true;
	    $template = new TemplateMerger($error, $path);
		$template->setError(true);
	    $template->display();
	}


	/**
	 * Used in viewHspSummary()
	 */

	private static function _saveInitialRecords($year, $hspPlanId) {

		if (!HspSummary::recordsExist($year, $hspPlanId)) {
			HspSummary::saveInitialSummary($year, $hspPlanId);
		}

	}

	public static function saveHspSummary($summaryObjArr, $year, $empId=null) {

		for ($i = 0; $i < count($summaryObjArr); $i++) {

			try {
				$log = Logger::getInstance();

				$exsisting = HspSummary::fetchHspSummary($year, 1, $summaryObjArr[$i]->getEmployeeId());

				$mssg = $summaryObjArr[$i]->isHspValueChangedByAdmin($exsisting[0]);

				if ($mssg != false) {
					$log->info($mssg);
				}

			} catch (Exception $e) {}

		}

		$saveSuccess = HspSummary::saveHspSummary($summaryObjArr);

		if (isset($empId)) {
			self::searchHspSummary($empId, $year, $saveSuccess);
		} else {
		    self::viewHspSummary($year, null, $saveSuccess);
		}

	}

	/**
	 * For searching HSP Summary for an employee
	 */

	public static function searchHspSummary($empId, $year, $saveSuccess=null) {

		$errorFlag = false;
      $screenParam = array('benefitcode' => $_GET['benefitcode'], 'action' => 'Hsp_Summary');
      $tokenGenerator = CSRFTokenGenerator::getInstance();
      $tokenGenerator->setKeyGenerationInput($screenParam);
      $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
      
		//Checking whether records exist for $year
		try {
			if (!HspSummary::recordsExist($year)) {
				switch (Config::getHspCurrentPlan()) {
					case 1:
						self::_saveInitialRecords($year, 1);
						break;
					case 2:
						self::_saveInitialRecords($year, 2);
						break;
					case 3:
						self::_saveInitialRecords($year, 3);
						break;
					case 4:
						self::_saveInitialRecords($year, 1);
						self::_saveInitialRecords($year, 3);
						break;
					case 5:
						self::_saveInitialRecords($year, 2);
						self::_saveInitialRecords($year, 3);
						break;
					case 6:
						self::_saveInitialRecords($year, 1);
						self::_saveInitialRecords($year, 2);
						break;
				}
			}
		} catch (HspSummaryException $e) {
		    if ($e->getCode() == HspSummaryException::HSP_PLAN_NOT_DEFINED) {
		        $errorFlag = true;
		    }
		}

		// Setting template path
		$path = "/templates/benefits/hspSummary.php";

		if ($errorFlag) {

			$error['hspNotDefinedESS'] = true;
         $error['token'] = $token;
			$template = new TemplateMerger($error, $path);
			$template->setError(true);

		} else {

			// Setting the Page No
			if (isset($_POST['pageNo'])) {
		    	$pageNo = $_POST['pageNo'];
			} else {
		    	$pageNo = 1;
			}

			// Setting records that are used in /templates/benefits/hspSummary.php
			$tmpOb[0]="searchHspSummary";
			$tmpOb[1]=HspSummary::fetchHspSummary($year, 1, $empId);
			$tmpOb[2]=$year;
			$tmpOb[3]=$pageNo;
			$tmpOb[4]=HspSummary::recordsCount($year, Config::getHspCurrentPlan());
			if (isset($saveSuccess)) {
				$tmpOb[5] = $saveSuccess;
			} else {
			    $tmpOb[5] = null;
			}
			$tmpOb[6]=EmpInfo::getEmployeeMainDetails();
			$tmpOb[7]=HspSummary::getYears();
         $tmpOb['token'] = $token;
			$template = new TemplateMerger($tmpOb, $path);

		}

		// Displaying
		$template->display();

	}


	public static function saveHspValues($hspArr, $year, $employee = false) {
		$msg = 'SAVE_SUCCESS';

		try {
			if (count($hspArr) > 0) {
				for ($i=0; $i<count($hspArr); $i++) {
					// Check if sensitive data is changed by admin and write to log
					try {
						$log = Logger::getInstance();
						$exsisting = Hsp::getHsp($hspArr[$i]->getId());
						$mssg = $hspArr[$i]->isHspValueChangedByAdmin($exsisting);
						if ($mssg != false) {
							$log->info($mssg);
						}

					}catch(Exception $e) {

					}

					$hspArr[$i]->setBenefitYear($year. "-1-1");
					$hspArr[$i]->save();

					$hspSummary = new HspSummary();
					$employeeId = $hspArr[$i] -> getEmployeeId();
				}
			} else {
				$msg = 'SAVE_FAILURE';
			}

			if($employee) {
				$backAction = "Hsp_Summary_Employee&id={$_GET['employeeId']}";
			}else {
				$backAction = "Hsp_Summary";
			}

		} catch (HspException $e) {
			switch ($e->getCode()) {
				case HspException::INVALID_ROW_COUNT : $msg = 'SAVE_FAILURE';
													  		 break;
				default : $msg = 'UNKNOWN_ERROR_FAILURE';
						  break;
			}
		}

		self::redirect($msg, '?benefitcode=Benefits&action='.$backAction.'&year='.$year);
	}

	public static function haltHspPlan($id, $year, $employee=false) {
		try {
			$hsp = Hsp::getHsp($id);
		}catch(Exception $e) {
			$hsp = new Hsp();
		}

		$backAction = 'Hsp_Summary';
		if ($employee) {
			$backAction = 'Hsp_Summary_Employee';

			if ($hsp->getEmployeeId() != $_SESSION['empID'] && $_SESSION['isAdmin'] != 'Yes' ) {
				self::redirect('UNAUTHORIZED_FAILURE', '?benefitcode=Benefits&action='.$backAction.'&year='.$year.'&employeeId='.$hsp->getEmployeeId());
			}
		}

		if (($hsp->getHalted() == 1) || ($hsp->getTerminated() == 1)) {
			self::redirect('HALT_FAILURE', '?benefitcode=Benefits&action='.$backAction.'&year='.$year.'&employeeId='.$hsp->getEmployeeId());
		}

		$hsp->setHalted(1);
		$hsp->setHaltedDate(date('Y-m-d'));

		$hspSummary = new HspSummary();
		$empId = $hsp -> getEmployeeId();

		$totalUsed = $hspRecordArr[0]['total_used'];
		$totalAccrued = $hspRecordArr[0]['total_acrued'];

		if($totalUsed < $totalAccrued) {
			$hsp -> setHspValue($totalAccrued);
		}else {
			$hsp -> setHspValue($totalUsed);
		}

		try {
			$hsp->save();
		}catch(Exception $e) {

		}
		try {
			$hspMailNotification = new HspMailNotification();

			if($_SESSION['isAdmin'] == 'Yes'){
				$hspMailNotification -> sendHspPlanHaltedByHRAdminNotification($hsp);
			}else {
				$hspMailNotification -> sendHspPlanHaltedByESSNotification($hsp);
			}
		}catch(Exception $e) {

		}

		$msg = 'HALT_SUCCESS';

		self::redirect($msg, '?benefitcode=Benefits&action='.$backAction.'&year='.$year.'&employeeId='.$hsp->getEmployeeId());
	}

	public static function saveTerminateEmployment($id, $year, $terminationDate, $isTerminated, $employee = false) {
		$hsp = Hsp::getHsp($id);

		$backAction = 'Hsp_Summary';
		if ($employee) {
			$backAction = 'Hsp_Summary_Employee';
		}

		if ($hsp->getTerminated() == 1) {
			self::redirect('TERMINATE_FAILURE', '?benefitcode=Benefits&action='.$backAction.'&year='.$year);
		}
		$log = Logger::getInstance();
//$log->debug('Terminated :' . $isTerminated);
		if($isTerminated) {
//$log->debug('Term before :');
			$empId = $hsp->getEmployeeId();
			Hsp::terminateEmployment($empId, $terminationDate);
//$log->debug('Term after :');
		}else {
			self::redirect('TERMINATE_FAILURE', '?benefitcode=Benefits&action='.$backAction.'&year='.$year);
		}

//		$employee = new EmpInfo();
//		$employeeJob = $employee->filterEmpJobInfo($hsp->getEmployeeId());

//		$employee->setEmpId($hsp->getEmployeeId());
//		$employee->setEmpStatus('EST000');
//		$employee->setEmpJobTitle(empty($employeeJob[0][2])?0:$employeeJob[0][2]);
//		$employee->setEmpEEOCat(empty($employeeJob[0][3])?0:$employeeJob[0][3]);
//		$employee->setEmpLocation(empty($employeeJob[0][6])?'':$employeeJob[0][6]);

//		$employee->updateEmpJobInfo();

		$msg = 'TERMINATE_SUCCESS';

		self::redirect($msg, '?benefitcode=Benefits&action='.$backAction.'&year='.$year);
	}

	public static function terminateEmployment($id, $year, $terminationDate) {
		$hsp = Hsp::getHsp($id);

		self::redirect(null, './CentralController.php?id=003&capturemode=updatemode&reqcode=EMP&hspid=' . $id . '&enddate=' . $terminationDate . '&id=' . $hsp->getEmployeeId() . '&year=' . $year);
	}

	public static function addHspRequestView() {
		$year = date('Y');

		$authorizeObj = new authorize($_SESSION['empID'], $_SESSION['isAdmin']);


		$path = "/templates/benefits/editHspPaymentRequest.php";
		$header = "requestHeader.php";

		$empID = $_SESSION['empID'];
		$empName = self::_getEmployeeName($empID);

		$hspPaymentRequest = new HspPaymentRequest();
		$children = $hspPaymentRequest -> fetchChildren($empID);
		$dependent = $hspPaymentRequest -> fetchDependants($empID);

		$planId = DefineHsp::fetchHsp();
		$planName = DefineHsp::getHspPlanName($planId);

		if (strstr($planName, '+'))
			$plans = explode('+', $planName);
		else
			$plans = $planName;

		$tmpOb[0]=$year;
		$tmpOb[1]=null;
		$tmpOb[2]=0;
		$tmpOb[3]=null;
		$tmpOb[4]=$authorizeObj;
		$tmpOb[5]=$_SESSION['empID'];
		$tmpOb[6]=$empName;
		$tmpOb[7]=$dependent;
		$tmpOb[8]=$children;
		$tmpOb[9]=$plans;

		$template = new TemplateMerger($tmpOb, $path, $header);

		$template->display();
	}

	public static function saveHspRequest($hspReqest) {
		try {
			$empId = $hspReqest->getEmployeeId();
			$year = date('Y', strtotime($hspReqest->getDateIncurred()));
			$amount = $hspReqest->getExpenseAmount();
			$hspId = $hspReqest->getHspId();

			switch ($hspId) {
				case 1 :
					if ($year != date('Y')) {
						throw new HspPaymentRequestException('Requests for the previous year are not allowed under the current health savings plan', HspPaymentRequestException::INVALID_DATE_PREVIOUS_YEAR);
					}

					$personalHspSummary = HspSummary::fetchHspSummary($year, 1, $empId);

					if (is_null($personalHspSummary))
						throw new HspPaymentRequestException('HSP Summary details not defined by HR Admins', HspPaymentRequestException::NO_HSP);

					$amountLimit = $personalHspSummary[0]->getTotalAccrued() - $personalHspSummary[0]->getTotalUsed();
					break;
				case 2 :
					if ($year != date('Y')) {
						throw new HspPaymentRequestException('Requests for the previous year are not allowed under the current health savings plan', HspPaymentRequestException::INVALID_DATE_PREVIOUS_YEAR);
					}

					$personalHspSummary = HspSummary::fetchHspSummary($year, 1, $empId);

					if (is_null($personalHspSummary))
						throw new HspPaymentRequestException('HSP Summary details not defined by HR Admins', HspPaymentRequestException::NO_HSP);

					if (count($personalHspSummary) == 2) {
						$index = ($personalHspSummary[0]->getHspPlanName() == 'HRA') ? 0 : 1;
					} else {
						$index = 0;
					}
					$amountLimit = $personalHspSummary[$index]->getTotalAccrued() - $personalHspSummary[$index]->getTotalUsed();
					break;
				case 3 :
					$reqError = BenefitsController::_validateFSARequest($year);
					if (is_null($reqError)) {
						$personalHspSummary = HspSummary::fetchHspSummary($year, 1, $empId);
						if (is_null($personalHspSummary))
							throw new HspPaymentRequestException('HSP Summary details not defined by HR Admins', HspPaymentRequestException::NO_HSP);

						$index = (count($personalHspSummary) == 2) ? 1 : 0;

						if ($year == date('Y') - 1) {
							$amountLimit = HspSummary::_fetchLastYearFsaBalance($empId, $year);

						} else {
							$amountLimit = $personalHspSummary[$index]->getAnnualLimit() - $personalHspSummary[$index]->getTotalUsed();
						}
					} else {
						throw $reqError;
					}
					break;
			}

			if ($amount > $amountLimit) {
				throw new HspPaymentRequestException('Request amount cannot exceed the annual limit', HspPaymentRequestException::EXCEED_LIMIT);
			}

			$msg = 'SAVE_SUCCESS';

			$hspReqest->addHspRequest();

			$server = $_SERVER['HTTP_HOST'];
			$path = str_replace(__FILE__, '', $_SERVER['REQUEST_URI']);
			$link = 'http://'. $server . $path .'&benefitcode=Benefits&action=View_Edit_Hsp_Request&id=' . $hspReqest->getId();

			/* Informing HR Admin: Begins */

			$notificationObj = new EmailNotificationConfiguration();
			$mailAddress = $notificationObj->fetchMailNotifications(EmailNotificationConfiguration::EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_HSP);

			if (isset($mailAddress)) {
				$hspMailNotification = new HspMailNotification();
				$hspMailNotification -> sendHspPaymentRequestNotifications($hspReqest, $link);
			}

			/* Informing HR Admin: Ends */

		} catch (HspPaymentRequestException $e) {
			switch ($e->getCode()) {
				case HspPaymentRequestException::INVALID_ROW_COUNT :
					$msg = 'SAVE_FAILURE';
					break;
				case HspPaymentRequestException::HSP_TERMINATED :
					$msg = 'SAVE_TERMINATED_FAILURE';
					break;
				case HspPaymentRequestException::HSP_NOT_ENOUGH_BALANCE_REMAINING :
					$msg = 'SAVE_LOWBALANCE_FAILURE';
					break;
				case HspPaymentRequestException::EXCEED_LIMIT :
					$msg = 'SAVE_REQUEST_LIMIT_EXCEED_FAILURE';
					break;
				case HspPaymentRequestException::INVALID_YEAR :
					$msg = 'SAVE_REQUEST_INVALID_YEAR_FAILURE';
					break;
				case HspPaymentRequestException::INVALID_DATE :
					$msg = 'SAVE_REQUEST_INVALID_DATE_FAILURE';
					break;
				case HspPaymentRequestException::NO_HSP :
					$msg = 'SAVE_REQUEST_NO_HSP_SUMMARY_DEFINED_FAILURE';
					break;
				case HspPaymentRequestException::INVALID_DATE_PREVIOUS_YEAR :
					$msg = 'SAVE_REQUEST_INVALID_DATE_PREVIOUS_YEAR_FAILURE';
					break;
				default :
					$msg = 'UNKNOWN_ERROR_FAILURE';
					 break;
			}
		}

		self::redirect($msg, '?benefitcode=Benefits&action=Hsp_Request_Add_View');
	}

	private static function _validateFSARequest($year) {
		if ($year == date('Y')) {
			return null;
		} elseif ($year + 1 == date('Y')) {
			if (date('m-d') <= '03-15') {
				return null;
			} else {
				return new HspPaymentRequestException('Requests for the previous year should be made before 15th of March this year', HspPaymentRequestException::INVALID_DATE);
			}
		} else {
			throw new HspPaymentRequestException('Requests are allowed only for this year and the previous year', HspPaymentRequestException::INVALID_YEAR);
		}
	}

	public static function payHspRequest($hspReqest) {
		try {
			// Check if sensitive data is changed by admin and write to log
			try {
				$log = Logger::getInstance();
				$exsistingRequest = $hspReqest->getHspRequest($hspReqest->getId());
				$mssg = $exsistingRequest->isDataChangedByAdmin($hspReqest);
				if ($mssg != false) {
					$log->info($mssg);
				}

			} catch (Exception $e) {

			}

			$hspReqestTemp = $hspReqest->getHspRequest($hspReqest->getId());
			$hspSummary    = new HspSummary();
			$empId 	       = $hspReqestTemp->getEmployeeId();
			$year 	       = date('Y', strtotime($hspReqestTemp->getDateIncurred()));

			$hspReqestTemp->setDatePaid($hspReqest->getDatePaid());
			$hspRecordArr = array();

			$amount = $hspReqest->getExpenseAmount();
			$hspId  = $hspReqestTemp->getHspId();

			switch ($hspId) {
				case 1 :
					$personalHspSummary = HspSummary::fetchHspSummary($year, 1, $empId);
					$amountLimit = $personalHspSummary[0]->getTotalAccrued() - $personalHspSummary[0]->getTotalUsed();
					break;
				case 2 :
					$personalHspSummary = HspSummary::fetchHspSummary($year, 1, $empId);
					if (count($personalHspSummary) == 2) {
						$index = ($personalHspSummary[0]->getHspPlanName() == 'HRA') ? 0 : 1;
					} else {
						$index = 0;
					}
					$amountLimit = $personalHspSummary[$index]->getTotalAccrued() - $personalHspSummary[$index]->getTotalUsed();
					break;
				case 3 :
					$personalHspSummary = HspSummary::fetchHspSummary($year, 1, $empId);
					$index = (count($personalHspSummary) == 2) ? 1 : 0;

					$amountLimit = $personalHspSummary[$index]->getAnnualLimit() - $personalHspSummary[$index]->getTotalUsed();
					break;
			}

			if ($amount > $amountLimit) {
				throw new HspPaymentRequestException('Request amount cannot exceed the annual limit', HspPaymentRequestException::EXCEED_LIMIT);
			}

			$server = $_SERVER['HTTP_HOST'];
			$path = str_replace(__FILE__, '', $_SERVER['REQUEST_URI']);
			$link = 'http://'. $server . $path .'&benefitcode=Benefits&action=View_Edit_Hsp_Request&id=' . $hspReqest->getId();

			//$log->debug("BC before ter :" . $terminated);
			//$log->debug("BC before hsp :" . $hspValue);
			//$log->debug("BC before total :" . $totalUsed);

			$msg = 'SAVE_SUCCESS';
			$hspReqest->payHspRequest();

			// For updating Total Used in HSP Summary
			Hsp::updateUsedPerPayment($year, $hspReqestTemp->getHspId(), $empId, $hspReqest->getExpenseAmount());

			$hspMailNotification = new HspMailNotification();
			$hspMailNotification -> sendHspPaymentAcceptNotification($hspReqestTemp, $link);

		} catch (HspPaymentRequestException $e) {
			switch ($e->getCode()) {
				case HspPaymentRequestException::INVALID_ROW_COUNT :
					$msg = 'SAVE_FAILURE';
				  	break;
				case HspPaymentRequestException::EXCEED_LIMIT :
					$msg = 'SAVE_REQUEST_LIMIT_EXCEED_FAILURE';
					break;
				default :
					$msg = 'UNKNOWN_ERROR_FAILURE';
					break;
			}
		}

		$_SESSION['paid'] = "Yes";
		$id = $_GET['id'];

		self::redirect($msg, "?benefitcode=Benefits&action=View_Hsp_Request&id=$id");
	}

	public static function deleteHspRequest($id) {
		$hspReqest = HspPaymentRequest::getHspRequest($id);

		try {
			$msg = 'DELETE_SUCCESS';
			$hspMailNotification = new HspMailNotification();
			$hspMailNotification -> sendHspPaymentRequestDeleteNotification($hspReqest);
			$hspReqest->deleteHspRequest();
		} catch (HspPaymentRequestException $e) {
			switch ($e->getCode()) {
				case HspException::INVALID_ROW_COUNT : $msg = 'DELETE_FAILURE';
													  		 break;
				default : $msg = 'UNKNOWN_ERROR_FAILURE';
						  break;
			}
		}

		self::redirect($msg, '?benefitcode=Benefits&action=List_Hsp_Due');
	}

	public static function denyHspRequest($id) {
		$hspReqest = HspPaymentRequest::getHspRequest($id);

		try {
			$msg = 'DENY_SUCCESS';
			$hspReqest->denyHspRequest();
			$hspMailNotification = new HspMailNotification();
			$hspMailNotification -> sendHspPaymentDenyNotification($hspReqest);
		} catch (HspPaymentRequestException $e) {
			switch ($e->getCode()) {
				case HspException::INVALID_ROW_COUNT : $msg = 'DENY_FAILURE';
													  		 break;
				default : $msg = 'UNKNOWN_ERROR_FAILURE';
						  break;
			}
		}

		self::redirect($msg, '?benefitcode=Benefits&action=List_Hsp_Due');
	}

	public static function listPendingHspRequest() {

		$authorizeObj = new authorize($_SESSION['empID'], $_SESSION['isAdmin']);

		if (!$authorizeObj->isAdmin()) {
			self::redirect('UNAUTHORIZED_FAILURE', '?benefitcode=Benefits&action=Hsp_Summary_Select_Year_Employee');
		}

		if (isset($_GET['printPdf']) && $_GET['printPdf'] == 1) {
			if ($_GET['pdfName'] == "HSP-Payments-Due") {
				$path = "/plugins/printBenefits/pdfHspPaymentsDue.php";
			}
		} else {
			$path = "/templates/benefits/listPendingHspPaymentRequest.php";
		}

		$requests = HspPaymentRequest::listUnPaidHspRequests();

		$tmpOb[0]=$requests;

		if (isset($_GET['printPdf']) && $_GET['printPdf'] == 1) {
			$template = new TemplateMerger($tmpOb, $path, 'pdfHeader.php', 'pdfFooter.php');
		} else {
			$template = new TemplateMerger($tmpOb, $path);
		}

		$template->display();
	}

	public static function listHspExpenditures($year, $employeeId) {
		$authorizeObj = new authorize($_SESSION['empID'], $_SESSION['isAdmin']);

		if (isset($_GET['printPdf']) && $_GET['printPdf'] == 1) {
			if ($_GET['pdfName'] == "HSP-Expenditures") {
				$path = "/plugins/printBenefits/pdfHspExpenditures.php";
			}
		} else {
			$path = "/templates/benefits/listPaidHspPaymentRequest.php";
		}

		$requests = HspPaymentRequest::listEmployeeHspRequests($year, $employeeId, true);

		$empFullName = self::_getEmployeeName($employeeId);

		$tmpOb[0]=$requests;
		$tmpOb[1][0]=$empFullName;

		if (isset($_GET['printPdf']) && $_GET['printPdf'] == 1) {
			$template = new TemplateMerger($tmpOb, $path, 'pdfHeader.php', 'pdfFooter.php');
		} else {
			$template = new TemplateMerger($tmpOb, $path);
		}

		$template->display();
	}

	public static function listHspUsed($year, $employeeId) {
		$authorizeObj = new authorize($_SESSION['empID'], $_SESSION['isAdmin']);

		if (isset($_GET['printPdf']) && $_GET['printPdf'] == 1) {
			if ($_GET['pdfName'] == "HSP-Used") {
				$path = "/plugins/printBenefits/pdfHspUsed.php";
			}
		} else {
			$path = "/templates/benefits/listHspPaymentRequestsUsed.php";
		}

		$requests = HspPaymentRequest::listEmployeeHspRequestsPaid($year, $employeeId);
		$empFullName = self::_getEmployeeName($employeeId);

		$tmpOb[0]=$requests;
		$tmpOb[1][0]= $empFullName;

		if (isset($_GET['printPdf']) && $_GET['printPdf'] == 1) {
			$template = new TemplateMerger($tmpOb, $path, 'pdfHeader.php', 'pdfFooter.php');
		} else {
			$template = new TemplateMerger($tmpOb, $path);
		}

		$template->display();
	}


	public static function viewHspRequestView($id, $edit=false) {
		$year = date('Y');
		$authorizeObj = new authorize($_SESSION['empID'], $_SESSION['isAdmin']);


		$path = "/templates/benefits/editHspPaymentRequest.php";

		$request = HspPaymentRequest::getHspRequest($id);

		$empID = $_SESSION['empID'];

                $planId = $request->getHspId();
                $planName = DefineHsp::getHspPlanName($planId);

		$tmpOb[0]=$year;
		$tmpOb[1]=null;
		if ($edit) {
			$tmpOb[2]=1;
		} else {
			$tmpOb[2]=2;
		}
		$tmpOb[3]=$request;
		$tmpOb[4]=$authorizeObj;
		$tmpOb[5]=$_SESSION['empID'];
		$tmpOb[6]=$id;
		$tmpOb[9]=$planName;

		$template = new TemplateMerger($tmpOb, $path);

		$template->display();
	}

	private static function _getEmployeeName($empID) {
		$empInfo = new EmpInfo();
		$empDetail = $empInfo->filterEmpMain($empID);

		$empName = '';
		if(isset($empDetail[0][2])) {
			$empName = $empDetail[0][2];
		}
		if(isset($empDetail[0][1])) {
			$empName .= " " . $empDetail[0][1];
		}

		return $empName;
	}

	public static function printHspSummary() {
		$path = "/plugins/printBenefits/printPopHspSummary.php";

		$template = new TemplateMerger(null, $path);
		$template->display();
	}

	public static function checkHspState($hspReqest) {
		if($hspReqest==true){
			$log = Logger::getInstance();
			$log->info("HSP Type changed to {$_POST['HspType']} by {$_SESSION['fname']} [{$_SESSION['user']}]");
			$msg = 'SAVE_SUCCESS';
		}
		else{
			$msg = 'SAVE_FAILURE';
		}
		//$msg = 'SAVE_SUCCESS';
		self::redirect($msg, '?benefitcode=Benefits&action=Define_Health_Savings_Plans');
	}


	public static function defineHsp(){
      $screenParam = array('benefitcode' => $_GET['benefitcode'], 'action' => $_GET['action']);
      $tokenGenerator = CSRFTokenGenerator::getInstance();
      $tokenGenerator->setKeyGenerationInput($screenParam);
      $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
      
		if ($_SESSION['isAdmin'] == 'No') {
		    die('You are not authorized to view this page');
		}
		
		$path = "/templates/benefits/defineHsp.php";
      $param = array('token' => $token);
		$template = new TemplateMerger($param, $path);
		$template->display();
	}
}
 ?>
