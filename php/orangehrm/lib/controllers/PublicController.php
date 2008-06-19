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

/**
 * Controller for pages that should be accessible to general public - i.e. Users who are not logged in.
 * Initially created for displaying Job Applications.
 *
 * Does not validate session or login details since pages should be accessible by anyone.
 *
 */

ob_start();
session_start();
set_magic_quotes_runtime(0); // Turning off magic quotes runtime
define('ROOT_PATH', dirname(__FILE__) . '/../../');
$wpath = explode('/lib/controllers/PublicController.php', $_SERVER['REQUEST_URI']);
$_SESSION['WPATH']= $wpath[0];

require_once ROOT_PATH . '/lib/exception/ExceptionHandler.php';
require_once ROOT_PATH . '/lib/common/Language.php';
require_once ROOT_PATH . '/lib/common/LocaleUtil.php';

require_once ROOT_PATH . '/lib/controllers/RecruitmentController.php';

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
} elseif (isset($_GET['recruitcode'])) {
	$moduletype = 'recruitMod';
}

switch ($moduletype) {

	case 'admin' 	: break;
	case 'hr'		: break;
	case 'mt'		: break;
	case 'rep' 		: break;
	case 'leave'	: break;
	case 'timeMod'	: break;

	case 'recruitMod' :
					$recruitController = new RecruitmentController();
					switch ($_GET['recruitcode']) {

						case 'ApplicantViewJobs':
							$recruitController->showVacanciesToApplicant();
							break;

						case 'ApplicantViewApplication':
							$recruitController->showJobApplication($_GET['id']);
							break;

						case 'ApplicantApply':
							$recruitController->applyForJob();
							break;

					}
}
ob_end_flush();  ?>
