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

/* For logging PHP errors */
include_once('../../lib/confs/log_settings.php');

ob_start();

session_start();

$_SESSION['posted'] = false;

if(!isset($_SESSION['fname'])) {

	header("Location: ../../symfony/web/index.php/auth/login");
	exit();
}

set_magic_quotes_runtime(0); // Turning off magic quotes runtime

define('ROOT_PATH', $_SESSION['path']);
define("SALT", '$2a$'.str_pad($_SESSION['empID'].session_id(), 24, session_id()).'$');

require_once ROOT_PATH . '/lib/exception/ExceptionHandler.php';
require_once ROOT_PATH . '/lib/common/Language.php';
require_once ROOT_PATH . '/lib/common/LocaleUtil.php';
require_once ROOT_PATH . '/lib/common/CommonFunctions.php';

require_once ROOT_PATH . '/lib/controllers/BenefitsController.php';

/** Clean Get variables that are used in page */
$varsToClean = array('uniqcode', 'repcode', 'reqcode', 'mtcode', 'repcode', 
    'leavecode', 'timecode', 'benefitcode', 'recruitcode', 'VIEW', 'action',
    'menu_no_top', 'isAdmin', 'pageNo', 'id', 'capturemode');

foreach ($varsToClean as $var) {
    if (isset($_GET[$var])) {
        $_GET[$var] = CommonFunctions::cleanAlphaNumericIdField($_GET[$var]);
    }
}

/** Clean $_SERVER['PHP_SELF'] */
$selfUrl = $_SERVER['PHP_SELF'];

$urlPos = stripos($selfUrl, 'CentralController.php');
if ( $urlPos !== FALSE) {
    $_SERVER['PHP_SELF'] = substr($selfUrl, 0, $urlPos + strlen('CentralController.php'));
}

//leave modules extractorss go here

if (isset($_GET['benefitcode'])) {
	$moduletype = 'benefits';
}

define('Admin', 'MOD001');
define('PIM', 'MOD002');
define('MT', 'MOD003');
define('REP', 'MOD004');
define('LEAVE', 'MOD005');
define('TIMEMOD', 'MOD006');
define('RECRUITMOD', 'MOD008');

if (isset($_GET['reqcode']) && 	($_GET['reqcode'] === "ESS") && (isset($_GET['id']) &&
   ($_GET['id'] != $_SESSION['empID']) && ($_SESSION['isAdmin'] != 'Yes' && !$_SESSION['isSupervisor']))) {
	trigger_error("Authorization Failed: You are not allowed to view this page", E_USER_ERROR);
}

/* Loading disabled modules: Begins */

require_once ROOT_PATH . '/lib/common/ModuleManager.php';

$disabledModules = array();

if (isset($_SESSION['admin.disabledModules'])) {
    
    $disabledModules = $_SESSION['admin.disabledModules'];
    
} else {
    
    $moduleManager = new ModuleManager();    
    $disabledModules = $moduleManager->getDisabledModuleList();
    $_SESSION['admin.disabledModules'] = $disabledModules;    
    
}

if (in_array('benefits', $disabledModules) && isset($_GET['benefitcode'])) {
    header("HTTP/1.0 404 Not Found");
    die;
}


/* Loading disabled modules: Ends */        

include ROOT_PATH.'/lib/controllers/Benefits.inc.php';


@ob_end_flush();  ?>
