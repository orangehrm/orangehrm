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
require_once($lan->getLangPath("full.php"));

$locRights=$_SESSION['localRights'];

$editArr = $this->popArr['editArr'];

$allNotifications = array(	EmailNotificationConfiguration::EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_LEAVE_PENDING_APPROVAL => $lang_Admin_ENS_LeaveApplications,
							EmailNotificationConfiguration::EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_LEAVE_APPROVED => $lang_Admin_ENS_LeaveApprovals,
							EmailNotificationConfiguration::EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_LEAVE_CANCELLED => $lang_Admin_ENS_LeaveCancellations,
							EmailNotificationConfiguration::EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_LEAVE_REJECTED => $lang_Admin_ENS_LeaveRejections);

$statusArr = null;

$notificationEmail = "";
if (isset($editArr) && is_array($editArr)) {
	$notificationObj = current($editArr);
	$notificationEmail = $notificationObj->getEmail();

	foreach ($editArr as $notificationObj) {
		$notificationObjs[$notificationObj->getNotifcationTypeId()] = $notificationObj;
	}
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>E-mail Cofiguration</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="../../themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<link href="../../themes/beyondT/css/leave.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../../scripts/archive.js"></script>
<?php require_once ROOT_PATH . '/scripts/octopus.js'; ?>
<script language="JavaScript" type="text/javascript">
  function validate() {
		error = false;
		mailRegExp = /^(([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)*\.[a-zA-Z])$/;
		obj = $('txtMailAddress');

		if (!checkEmail(obj.value)) {
			error = true;
			alert('<?php echo $lang_Error_InvalidEmail; ?>')
		}

		if (!error) {
			$('sqlState').value = 'UpdateRecord';
			$('mailSubscription').submit();
		}
	}

	function $(id) {
		return document.getElementById(id);
	}
</script>

<style type="text/css">
@import url("../../themes/beyondT/css/style.css");

.style1 {color: #FF0000}

.hide {
	display:none;
}

.show {
	display: table-row;
}

.roundbox {
	margin-top: 50px;
	margin-left: 0px;
	padding: 0 10px;

}

.roundbox_content {
	padding:15px;
}

input[type=checkbox] {
	border:none;
}
</style>
</head>
<body>
<h2><?php echo $lang_Admin_SubscribeToMailNotifications; ?><hr/></h2>
<form name="mailSubscription" action="<?php echo $_SERVER['PHP_SELF']; ?>?uniqcode=ENS&capturemode=updatemode&id=1" method="post" onsubmit="validate(); return false;" >
<input type="hidden" name="sqlState" id="sqlState" />
  <table border="0" cellpadding="0" cellspacing="0">
  	<thead>
      <tr>
        <th class="tableTopLeft"></th>
        <th class="tableTopMiddle"></th>
        <th class="tableTopRight"></th>
      </tr>
    </thead>
    <tbody>
    <tr>
    	<td class="tableMiddleLeft"></td>
        <td>
        	<label><?php echo $lang_Commn_Email; ?>
  				<input type="text" name="txtMailAddress" id="txtMailAddress" value="<?php echo $notificationEmail; ?>"/>
  			</label>
  		</td>
  		<td class="tableMiddleRight"></td>
      </tr>
      <?php
      	$i=0;
      	foreach ($allNotifications as $notificationType=>$notificationName) {
      		$checked = "checked='checked'";
      		if (isset($notificationObjs[$notificationType])) {
      			$notificationStatus = $notificationObjs[$notificationType]->getNotificationStatus();

      			if ($notificationStatus == 0) {
      				$checked = "";
      			}
      		}
      ?>
      <tr>
      	<td class="tableMiddleLeft"></td>
        <td>
        	<label>
			<input type="hidden" name="notificationMessageId[<?php echo $i; ?>]" value="<?php echo $notificationType; ?>" />
			<input type="hidden" name="notificationMessageStatus[<?php echo $i; ?>]" value="0" />
			<input type="checkbox" <?php echo $checked; ?>  name="notificationMessageStatus[<?php echo $i; ?>]" value="1" />
				<?php echo $notificationName; ?></label>
		</td>
      	<td class="tableMiddleRight"></td>
      </tr>
      <?php
      	$i++;
      	}
      ?>
    </tbody>
    <tfoot>
      <tr>
        <td class="tableBottomLeft"></td>
        <td class="tableBottomMiddle"></td>
        <td class="tableBottomRight"></td>
      </tr>
    </tfoot>
  </table>
<input type="image" name="btnAct" src="../../themes/beyondT/pictures/btn_save.jpg" onMouseOut="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';"
style="border:none;"/>
</form>
</body>
</html>
