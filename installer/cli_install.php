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
 *
 */
session_start();
define('REAL_ROOT_PATH', dirname(__FILE__));
define('ROOT_PATH',dirname( dirname(__FILE__) ));

require_once(REAL_ROOT_PATH.'/utils/installUtil.php');
require_once(REAL_ROOT_PATH.'/DetailsHandler.php');
require_once(REAL_ROOT_PATH.'/BasicConfigurations.php');

include_once('OrangeHrmRegistration.php');
$ohrmRegistration = new OrangeHrmRegistration();

include_once ('SystemConfiguration.php');
$systemConfiguration = new SystemConfiguration();


function setValueToLogFile($filePath, $content) {		
	file_put_contents($filePath, $content , FILE_APPEND | LOCK_EX);
}

function clearLogFile($filePath) {
	file_put_contents($filePath, '' );
}

function deleteFile($file)
{
if(is_file($file)){
    if (!unlink($file))
    {
	  $messages = getMessages();
      $messages->displayMessage(Messages::SEPERATOR."Error deleting $file");
    }
  }
}

function getMessages(){
    if (!isset($messageList)) {
        $messageList = new Messages();
    }
    return $messageList;
}
function getDetailsHandler(){
    if (!isset($detailsHandler)) {
        $detailsHandler = new DetailsHandler();
    }
    return $detailsHandler;
}
function getBasicConfigurations(){
    if (!isset($basicConfigurations)) {
        $basicConfigurations = new BasicConfigurations();
    }
    return $basicConfigurations;
}
error_reporting(0);
$detailsHandler = getDetailsHandler();
$messages = getMessages();
$basicConfigurations = getBasicConfigurations();


$licenseAgreementFromFile = parse_ini_file(REAL_ROOT_PATH."/config.ini");

if($licenseAgreementFromFile['License'] != 'y'){

$licenseFilePath=ROOT_PATH."/license/LICENSE.TXT";
echo "For continue installation need to accept orangehrm license agreement. It is available in '$licenseFilePath'. Read it carefully and if you agree type word 'yes'. : ";
$handle = fopen ("php://stdin","r");
$acceptAggrement = fgets($handle);
$acceptAggrement = (trim($acceptAggrement) == 'yes') ? true :  false;
}
else{
  $acceptAggrement =true;
  echo "Agreed to license from config file\n";
}



if(!$acceptAggrement){
	$messages->displayMessage("Need accept license agreement to continue!");
}
//else if(posix_getuid() != 0){
//	$messages->displayMessage(Messages::SUPER_USER_NEED);
//}
else if (is_file(ROOT_PATH . '/lib/confs/Conf.php')) {
        exit ("\nThis system already installed.\n");
}else{ 
        ($argv[1]<1) ? $detailsHandler->checkDetailsValidation() : setConfiguration($argv,$detailsHandler);
	if(!($basicConfigurations->isFailBasicConfigurations()))
	{
//		shell_exec("chmod -R 777 ".ROOT_PATH);
//		shell_exec("exit");
		include "ApplicationSetupUtility.php";	
	
		$logFilePath = ApplicationSetupUtility::getErrorLogPath();
		writeLogs("OrangeHRM Installation Log\n", $logFilePath);
		writeLogs("DB Creation - Starting", $logFilePath);
		ApplicationSetupUtility::createDB();
		if (!isset($_SESSION['dbError']) && !isset($_SESSION['error'])) {
			$messages->displayMessage("Please wait...");
			$_SESSION['INSTALLING'] = 1;
			$messages->displayMessage("Db Creating ...");
			writeLogs("DB Creation - Done", $logFilePath);

			$_SESSION['defUser']['organizationName'] = $detailsHandler->getOrganizationName();
			$_SESSION['defUser']['adminEmployeeFirstName'] = $detailsHandler->getAdminEmployeeFirstName();
			$_SESSION['defUser']['adminEmployeeLastName'] = $detailsHandler->getAdminEmployeeLastName();
			$_SESSION['defUser']['organizationEmailAddress'] = $detailsHandler->getOrganizationEmailAddress();
			$_SESSION['defUser']['contactNumber'] = $detailsHandler->getContactNumber();
			$_SESSION['defUser']['AdminUserName'] = $detailsHandler->getAdminUserName();
			$_SESSION['defUser']['AdminPassword'] = $detailsHandler->getAdminPassword();
			$_SESSION['defUser']['randomNumber'] = rand(1,100);
			$_SESSION['defUser']['type'] = 0;

			$_SESSION['dbHostName'] = $detailsHandler->getHost();
			$_SESSION['dbUserName'] = $detailsHandler->getOrangehrmDatabaseUser();
			$_SESSION['dbPassword'] = $detailsHandler->getOrangehrmDatabasePassword();
			$_SESSION['dbName'] = $detailsHandler->getDatabaseName();
			$_SESSION['dbHostPort'] = $detailsHandler->getPort();

			$ohrmRegistration->sendRegistrationData();
			
			$controlval = 0;
			for ($i=0; $i < $_SESSION['INSTALLING']-$controlval; $i++){
		
				ApplicationSetupUtility::install();

				if($_SESSION['INSTALLING']==2) $messages->displayMessage("Fill Data Phase 1 - No Errors...");
				if($_SESSION['INSTALLING']==3) $messages->displayMessage("Fill Data Phase 2 - No Errors...");
				if($_SESSION['INSTALLING']==4) $messages->displayMessage("Create DB user - No Errors...");
				if($_SESSION['INSTALLING']==5) $messages->displayMessage("Create OrangeHRM user - No Errors...");
				if($_SESSION['INSTALLING']==6) $messages->displayMessage("Write Conf - No Errors...");
				if($_SESSION['INSTALLING']==7) $messages->displayMessage("Install Plugins  - No Errors...");
			}
		}

		$error = false;
		if (isset($_SESSION['dbError'])) {
			$messages->displayMessage($_SESSION['dbError']);
			writeLogs($_SESSION['dbError'], $logFilePath);
			$error = true;
		} elseif (isset($_SESSION['error'])) {
			$messages->displayMessage($_SESSION['error']);
			writeLogs($_SESSION['error'], $logFilePath);
			$error = true;
		}


		$logfileName = "logInsatall.log";
		clearLogFile($logfileName);
		setValueToLogFile($logfileName,date("Y-m-d H:i:s "));

		$result = shell_exec(__DIR__  . "/cli_common_commands.sh 2>> ". $logfileName); // Composer install and symfony commands

		if(!isset($result) || trim($result)==='' || $error){
			$messages->displayMessage("Error(s) found. Error log file will display below.");

			$logFile = fopen("logInsatall.log", "r") or die("Unable to open file log file in cli!");
			echo fread($logFile,filesize("logInsatall.log"));
			fclose($logFile);

			include(ROOT_PATH . '/installer/cleanUp.php');

			deleteFile(ROOT_PATH . '/lib/confs/cryptokeys/key.ohrm');
			deleteFile(ROOT_PATH . '/lib/confs/Conf.php');
			$file = ROOT_PATH . '/symfony/config/databases.yml';
			if(is_file($file)){
				shell_exec("sudo rm  ". $file);
			}
		} else {
			$messages->displayMessage("Please wait...");
			$messages->displayMessage("Result - " . $result);
			$messages->displayMessage("Installation successfully completed...");
			sendInstallationStatusAsSuccess();
			setValueToLogFile($logfileName, "Installation successfully completed.\n");
			require_once(ROOT_PATH.'/install.php');
		}
	}
	else{
	 	$messages->displayMessage(Messages::INTERUPT_MESSAGE); 
        }
}

function isUserFillFromBash($value){
  return $value == "-N"? null:$value;
}

function setConfiguration($argv,$detailsHandler){
		$dbHostName = isUserFillFromBash($argv[2]);
		$dbHostPortID = isUserFillFromBash($argv[3]);
		$dbName = isUserFillFromBash($argv[4]);

		$adminUserName = isUserFillFromBash($argv[5]);
	        $adminPassword = "";

		$dbOHRMUserName = isUserFillFromBash($argv[6]);
		$dbOHRMPassword = "";

		$dbUserName = isUserFillFromBash($argv[7]);
		$dbPassword = ""; 	

		$databaseRootPassword = '';

		$encryption = isUserFillFromBash($argv[8]); //"true = Active"/"Failed"
		$dbCreateMethod = isUserFillFromBash($argv[9]); //existing/new
		$sameOhrmUser  = isUserFillFromBash($argv[10]);

		$companyName  = isUserFillFromBash($argv[11]);

		$adminEmployeeFirstName = "";
		$adminEmployeeLastName = "";

		$detailsHandler->setConfigurationFromParameter($dbHostName, $dbHostPortID, $dbName, $adminUserName, $adminPassword, $dbOHRMUserName, $dbOHRMPassword, $dbUserName, $dbPassword, $databaseRootPassword, $encryption, $dbCreateMethod, $sameOhrmUser, $companyName, $adminEmployeeFirstName, $adminEmployeeLastName);
}

function sendInstallationStatusAsSuccess() {
    $_SESSION['defUser']['type'] = 3;
    $ohrmRegistration = new OrangeHrmRegistration();
    $ohrmRegistration->sendRegistrationData();
}

/**
 * Write logs when running cli-installer
 * @param $message
 * @param $filePath
 */
function writeLogs($message, $filePath) {
    $log = date("r") . " {$message}\n";
    error_log($log, 3, $filePath);
}

?>

