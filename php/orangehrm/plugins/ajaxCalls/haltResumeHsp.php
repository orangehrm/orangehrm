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

ob_start();

session_start();

if (!defined('ROOT_PATH'))
	define('ROOT_PATH', $_SESSION['path']);

require_once ROOT_PATH . '/lib/models/benefits/Hsp.php';
require_once ROOT_PATH . '/lib/models/benefits/mail/HspMailNotification.php';

try {
	$hspSummaryId 	= $_GET['hspSummaryId'];
	$newHspStatus   = $_GET['newHspStatus'];
	$empId		= $_GET['empId'];

	$hsp = new Hsp();
	$hsp->setEmployeeId($empId);
	$hsp->setSummaryId($hspSummaryId);
	$hsp->setHspPlanStatus($newHspStatus);

	$hspMailNotification = new HspMailNotification();

	if(Hsp::updateStatus($hspSummaryId, $newHspStatus)) {
		switch ($newHspStatus) {
			case Hsp::HSP_STATUS_HALTED :
				$hspMailNotification -> sendHspPlanHaltedByHRAdminNotification($hsp);
				break;
			case Hsp::HSP_STATUS_ACTIVE :
				break;
			case Hsp::HSP_STATUS_ESS_HALTED :
				$hspMailNotification -> sendHspPlanHaltedByHRAdminOnRequestNotification($hsp);
				break;
			case Hsp::HSP_STATUS_PENDING_HALT :
				//$hspMailNotification->sendHspPlanHaltRequestedByESSNotification($hsp);
				break;
		}
		echo 'done:'. $newHspStatus;
	} else {
		echo 'fail:Error while changing the new HSP status';
	}
} catch(Exception $e) {
	echo 'fail:Error while performing the requested action';
}

?>
