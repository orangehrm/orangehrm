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

while (true) {
	require_once ROOT_PATH . '/lib/extractor/benefits/EXTRACTOR_HspPayPeriod.php';
	require_once ROOT_PATH . '/lib/extractor/benefits/EXTRACTOR_Hsp.php';
	require_once ROOT_PATH . '/lib/extractor/benefits/EXTRACTOR_HspPaymentRequest.php';
	require_once ROOT_PATH . '/lib/extractor/benefits/EXTRACTOR_DefineHsp.php';
	require_once ROOT_PATH . '/lib/extractor/benefits/EXTRACTOR_HspSummary.php';

	switch ($_GET['action']) {
		case 'Benefits_Schedule_Select_Year': BenefitsController::selectYear('Schedule');
											  break;
		case 'List_Benefits_Schedule'		: BenefitsController::listPayPeriods($_GET['year']);
											  break;
		case 'View_Add_Pay_Period'			: if (isset($_GET['year'])) {
											  	BenefitsController::viewAddPayPeriod($_GET['year']);
											  } else {
											  	BenefitsController::viewAddPayPeriod();
											  }
											  break;
		case 'View_Edit_Pay_Period'			: BenefitsController::viewEditPayPeriod($_GET['year'], $_GET['id']);
											  break;
		case 'Delete_Pay_Period'			: $payPeriods = EXTRACTOR_HspPayPeriod::parseDeleteData($_POST);
											  BenefitsController::deletePayPeriods($payPeriods, $_GET['year']);
											  break;
		case 'Add_Pay_Period'				: $payPeriod = EXTRACTOR_HspPayPeriod::parseAddData($_POST);
											  BenefitsController::addPayPeriod($payPeriod);
		case 'Edit_Pay_Period'				: $payPeriod = EXTRACTOR_HspPayPeriod::parseEditData($_POST);
											  BenefitsController::editPayPeriod($payPeriod);
											  break;
		case 'Hsp_Summary_Select_Year'		: BenefitsController::selectYear('Hsp_Summary');
											  break;
		case 'Hsp_Summary_Select_Year_Employee' : BenefitsController::selectYear('Hsp_Summary_Employee');
											  break;
		case 'Hsp_Summary_Select_Year_Employee_Admin' : BenefitsController::selectYearAndEmployee('Hsp_Summary_Employee');
											  break;
		case 'Hsp_Summary'					: BenefitsController::viewHspSummary($_GET['year']);
											  break;
		case 'Hsp_Summary_Employee'			: if (!isset($_GET['employeeId']) && isset($_GET['id'])) {
												$_GET['employeeId']=$_GET['id'];
											  }
											  BenefitsController::viewHspSummary($_GET['year'], $_GET['employeeId']);
											  break;
		case 'Save_Hsp_Summary'				: $hspArr = EXTRACTOR_HspSummary::parseHspSaveData($_POST);
                                    $screenParam = array('benefitcode' => $_GET['benefitcode'], 'action' => 'Hsp_Summary');
                                    $tokenGenerator = CSRFTokenGenerator::getInstance();
                                    $tokenGenerator->setKeyGenerationInput($screenParam);
                                    $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                                    
                                   if($token == $_POST['token']) {
                                      if (isset($_GET['empId'])) {
                                          BenefitsController::saveHspSummary($hspArr, $_GET['year'], $_GET['empId']);
                                      } else {
                                          BenefitsController::saveHspSummary($hspArr, $_GET['year']);
                                      }
                                   }
											  break;
		case 'Search_Hsp_Summary'			:
                                      if ((!isset($_POST['txtEmployeeSearchName']) || empty($_POST['txtEmployeeSearchName'])) && isset($_POST['year'])) {
                                         BenefitsController::viewHspSummary($_POST['year']);
                                      } elseif (isset($_POST['txtEmployeeSearchName']) && isset($_POST['year'])) {
                                          $empId = EXTRACTOR_HspSummary::parseSearchData($_POST);
                                          if ($empId == "") {
                                             BenefitsController::viewHspSummary($_POST['year'], "leftNull");
                                          } else {
                                            BenefitsController::searchHspSummary($empId, $_POST['year']);
                                          }
                                      } elseif (isset($_GET['empId']) && isset($_GET['year'])) {
                                          BenefitsController::searchHspSummary($_GET['empId'], $_GET['year']);
                                      }
											  break;
		case 'Hsp_Not_Defined'				: BenefitsController::HspNotDefined();
											  break;
		case 'Save_Hsp_Values_Employee'		: $hspArr = EXTRACTOR_Hsp::parseSaveData($_POST);
											  BenefitsController::saveHspValues($hspArr, $_GET['year'], true);
											  break;
		case 'Halt_Hsp_Plan'				: BenefitsController::haltHspPlan($_GET['id'], $_GET['year']);
											  break;
		case 'Halt_Hsp_Plan_Employee'		: BenefitsController::haltHspPlan($_GET['id'], $_GET['year'], true);
											  break;
		case 'Hsp_Request_Add_View'			: BenefitsController::addHspRequestView();
											  break;
		case 'Hsp_Request_Add'				: $hspRequest = EXTRACTOR_HspPaymentRequest::parseSaveData($_POST);
											  BenefitsController::saveHspRequest($hspRequest);
											  break;
		case 'List_Hsp_Due'					: BenefitsController::listPendingHspRequest();
											  break;
		case 'View_Edit_Hsp_Request'		: BenefitsController::viewHspRequestView($_GET['id'], true);
											  break;
		case 'Delete_Request'				: BenefitsController::deleteHspRequest($_GET['id']);
											  break;
		case 'Deny_Request'					: BenefitsController::denyHspRequest($_GET['id']);
											  break;
		case 'Hsp_Request_Save'				: $hspRequest = EXTRACTOR_HspPaymentRequest::parseSaveData($_POST);
											  BenefitsController::payHspRequest($hspRequest);
											  break;
		case 'Hsp_Expenditures_Select_Year_And_Employee' : BenefitsController::selectYearAndEmployee('Expenditure');
											  			   break;
		case 'Hsp_Expenditures'				: BenefitsController::listHspExpenditures($_GET['year'], $_GET['employeeId']);
											  break;
		case 'Hsp_Used_Select_Year'			: BenefitsController::selectYearAndEmployee('Used_Select_Year');
											  			   break;
		case 'Hsp_Used'				: BenefitsController::listHspUsed($_GET['year'], $_GET['employeeId']);
											  break;
		case 'View_Hsp_Request'				: BenefitsController::viewHspRequestView($_GET['id']);
											  break;
		case 'View_Hsp_Request'				: BenefitsController::viewHspRequestView($_GET['id']);
											  break;
		case 'Terminate_Hsp_Plan'			: BenefitsController::terminateEmployment($_GET['id'], $_GET['year'], $_GET['enddate']);
											  break;
		case 'print'						: BenefitsController::printHspSummary();
											  break;
		case 'Define_Health_Savings_Plans'	: BenefitsController::defineHsp();
											  break;
		case 'Save_Health_Savings_Plans'	: 
                                    $screenParam = array('benefitcode' => $_GET['benefitcode'], 'action' => 'Define_Health_Savings_Plans');
                                    $tokenGenerator = CSRFTokenGenerator::getInstance();
                                    $tokenGenerator->setKeyGenerationInput($screenParam);
                                    $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
                                    $saveHsp = false;
                                    if($token == $_POST['token']) {
                                       $saveHsp = EXTRACTOR_DefineHsp::parseSaveDataHsp($_POST['HspType']);
                                    }
                                    BenefitsController::checkHspState($saveHsp);
                                    break;
	}
	break;
}
 ?>
