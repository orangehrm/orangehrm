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
							EmailNotificationConfiguration::EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_LEAVE_REJECTED => $lang_Admin_ENS_LeaveRejections,
							EmailNotificationConfiguration::EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_JOB_APPLIED => $lang_Admin_ENS_JobApplications,
                            EmailNotificationConfiguration::EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_SEEK_HIRE_APPROVAL => $lang_Admin_ENS_SeekHireApproval,
                            EmailNotificationConfiguration::EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_HIRE_TASKS => $lang_Admin_ENS_HiringTasks,
                            EmailNotificationConfiguration::EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_HIRE_APPROVED => $lang_Admin_ENS_HiringApproved,
EmailNotificationConfiguration::EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_HSP => $lang_Admin_ENS_HspNotifications);

$statusArr = null;

$notificationEmail = "";
if (isset($editArr) && is_array($editArr)) {
	$notificationObj = current($editArr);
	$notificationEmail = $notificationObj->getEmail();

	foreach ($editArr as $notificationObj) {
		$notificationObjs[$notificationObj->getNotifcationTypeId()] = $notificationObj;
	}
}

$formAction = $_SERVER['PHP_SELF'] . "?uniqcode=ENS&amp;capturemode=updatemode&amp;id=1";
$token = $this->popArr['token'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $lang_Admin_SubscribeToMailNotifications; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script type="text/javascript">
//<![CDATA[

    var editMode = false;

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


    function validate() {
        var err = false;
        var msg = '<?php echo $lang_Error_PleaseCorrectTheFollowing; ?>\n\n';

        var mailRegExp = /^(([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)*\.[a-zA-Z])$/;
        obj = $('txtMailAddress');

        if (!checkEmail(obj.value)) {
            err = true;
            msg += "\t- <?php echo $lang_Error_InvalidEmail; ?>\n";
        }

        if (err) {
            alert(msg);
            return false;
        } else {
            return true;
        }
    }

    function reset() {
        $('mailSubscription').reset();
    }

    function edit() {

<?php if($locRights['edit']) { ?>
        if (editMode) {
            if (validate()) {
                $('mailSubscription').submit();
            }
            return;
        }
        editMode = true;
        var frm = $('mailSubscription');

        for (var i=0; i < frm.elements.length; i++) {
            frm.elements[i].disabled = false;
        }
        $('editBtn').value="<?php echo $lang_Common_Save; ?>";
        $('editBtn').title="<?php echo $lang_Common_Save; ?>";
        $('editBtn').className = "savebutton";

<?php } else {?>
        alert('<?php echo $lang_Common_AccessDenied;?>');
<?php } ?>
    }

//]]>
</script>
<script type="text/javascript" src="../../themes/<?php echo $styleSheet;?>/scripts/style.js"></script>
<link href="../../themes/<?php echo $styleSheet;?>/css/style.css" rel="stylesheet" type="text/css"/>
<!--[if lte IE 6]>
<link href="../../themes/<?php echo $styleSheet; ?>/css/IE6_style.css" rel="stylesheet" type="text/css"/>
<![endif]-->
<!--[if IE]>
<link href="../../themes/<?php echo $styleSheet; ?>/css/IE_style.css" rel="stylesheet" type="text/css"/>
<![endif]-->
</head>
<body>
   <div class="formpageNarrow">
        <div class="outerbox">
            <div class="mainHeading"><h2><?php echo $lang_Admin_SubscribeToMailNotifications;?></h2></div>

        <?php $message =  isset($this->getArr['msg']) ? $this->getArr['msg'] : (isset($this->getArr['message']) ? $this->getArr['message'] : null);
            if (isset($message)) {
                $messageType = CommonFunctions::getCssClassForMessage($message);
                $message = "lang_Common_" . $message;
        ?>
            <div class="messagebar">
                <span class="<?php echo $messageType; ?>"><?php echo (isset($$message)) ? $$message: ""; ?></span>
            </div>
        <?php } ?>

            <form name="mailSubscription" id="mailSubscription" method="post" onsubmit="return validate()" action="<?php echo $formAction;?>">
               <input type="hidden" name="token" value="<?php echo $token;?>" />
                <input type="hidden" name="sqlState" value="UpdateRecord"/>
                <label for="txtMailAddress"><?php echo $lang_Commn_Email; ?><span class="required">*</span></label>
                <input type="text" name="txtMailAddress" id="txtMailAddress" class="formInputText"
                    value="<?php echo $notificationEmail; ?>" disabled="disabled"/>
                <br class="clear"/>

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
            <label for="txtSkillName"><?php echo $notificationName; ?></label>
            <input type="hidden" name="notificationMessageId[<?php echo $i; ?>]" value="<?php echo $notificationType; ?>" />
            <input type="hidden" name="notificationMessageStatus[<?php echo $i; ?>]" value="0" />
            <input type="checkbox" <?php echo $checked; ?>  name="notificationMessageStatus[<?php echo $i; ?>]"
                value="1" class="formCheckbox" disabled="disabled"/>
            <br class="clear"/>
      <?php
        $i++;
        }
      ?>


                <div class="formbuttons">
<?php if($locRights['edit']) { ?>
                    <input type="button" class="editbutton" id="editBtn"
                        onclick="edit();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                        value="<?php echo $lang_Common_Edit;?>" />
                    <input type="button" class="clearbutton" onclick="reset();" tabindex="3"
                        onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                         value="<?php echo $lang_Common_Reset;?>" />
<?php } ?>
                </div>
            </form>
        </div>
        <script type="text/javascript">
        //<![CDATA[
            if (document.getElementById && document.createElement) {
                roundBorder('outerbox');
            }
        //]]>
        </script>
        <div class="requirednotice"><?php echo preg_replace('/#star/', '<span class="required">*</span>', $lang_Commn_RequiredFieldMark); ?>.</div>
    </div>
</body>
</html>
