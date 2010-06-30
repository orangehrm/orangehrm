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
$application = $records['application'];
$action = $records['action'];
$applicationId = $application->getId();

$resourceName = 'lang_Recruit_JobApplicationAction_' . $action;
$actionName = isset($$resourceName) ? $$resourceName : $action;
$actionButtonClass = ($action == JobApplication::ACTION_MARK_OFFER_DECLINED) ? 'extralongbtn' : 'plainbtn';

$confirmMsgRes = 'lang_Recruit_JobApplicationConfirm_Confirm' . $action;
$confirmMsg = isset($$confirmMsgRes) ? $$confirmMsgRes : $confirmMsgRes;
$confirmDescRes = 'lang_Recruit_JobApplicationConfirm_Confirm' . $action . 'Desc';
$confirmDesc = isset($$confirmDescRes) ? $$confirmDescRes : $confirmDescRes;

$baseURL = "{$_SERVER['PHP_SELF']}?recruitcode={$_GET['recruitcode']}";
$actionURL = $baseURL . '&amp;action=' . $action;

$statusList = array(
    JobApplication::STATUS_SUBMITTED => $lang_Recruit_JobApplicationStatus_Submitted,
    JobApplication::STATUS_FIRST_INTERVIEW_SCHEDULED => $lang_Recruit_JobApplicationStatus_FirstInterview,
    JobApplication::STATUS_SECOND_INTERVIEW_SCHEDULED => $lang_Recruit_JobApplicationStatus_SecondInterview,
    JobApplication::STATUS_JOB_OFFERED => $lang_Recruit_JobApplicationStatus_JobOffered,
    JobApplication::STATUS_OFFER_DECLINED => $lang_Recruit_JobApplicationStatus_OfferDeclined,
    JobApplication::STATUS_PENDING_APPROVAL => $lang_Recruit_JobApplicationStatus_PendingApproval,
    JobApplication::STATUS_HIRED => $lang_Recruit_JobApplicationStatus_Hired,
    JobApplication::STATUS_REJECTED => $lang_Recruit_JobApplicationStatus_Rejected
    );


$picDir = "../../themes/{$styleSheet}/pictures/";
$iconDir = "../../themes/{$styleSheet}/icons/";

$backImg = $picDir . 'btn_back.gif';
$backImgPressed = $picDir . 'btn_back_02.gif';

$token = "";
if(isset($records['token'])) {
   $token = $records['token'];
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script type="text/javascript">
//<![CDATA[

    function goBack() {
        history.back();
    }

    function validate() {
        err = false;
        msg = '<?php echo $lang_Error_PleaseCorrectTheFollowing; ?>\n\n';

        errors = new Array();
        if ($('txtNotes').value.trim() == '') {
            err = true;
            msg += "\t- <?php echo $lang_Recruit_JobApplication_PleaseSpecifyNotes; ?>\n";
        }
        if (err) {
            alert(msg);
            return false;
        } else {
            return true;
        }
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
    <style type="text/css">
    <!--

    .txtName,.txtValue {
        display: block;  /* block float the labels to left column, set a width */
        float: left;
        margin: 5px 0px 2px 0px; /* set top margin same as form input - textarea etc. elements */
    }

    .txtName {
        text-align: left;
        width: 150px;
        padding-left: 10px;
    }

    .txtValue {
        width: 300px;
    }

    textarea {
        display: block;
        float: left;
        margin: 10px 0px 20px 0px;
        padding: 0px 4px 0px 4px;
        width: 300px;
        height: 100px;
    }

    br {
        clear: left;
    }

    .eventDate {
        font-style: italic;
    }
    .confirmMsg {
        padding-left: 15px;
        padding-top: 10px;
        font-weight: bold;
    }
    .confirmDesc {
        padding-left: 15px;
        font-style: italic;
        padding-bottom: 20px;
    }
    -->
</style>
</head>
<body>
    <div class="formpage">
        <div class="navigation">
	    	<input type="button" class="savebutton"
		        onclick="goBack();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
		        value="<?php echo $lang_Common_Back;?>" />
        </div>
        <div class="outerbox">
            <div class="mainHeading"><h2><?php echo $lang_Recruit_JobApplicationConfirm_Heading . $actionName;?></h2></div>

        <?php $message =  isset($_GET['message']) ? $_GET['message'] : null;
            if (isset($message)) {
                $messageType = CommonFunctions::getCssClassForMessage($message);
                $message = "lang_Common_" . $message;
        ?>
            <div class="messagebar">
                <span class="<?php echo $messageType; ?>"><?php echo (isset($$message)) ? $$message: ""; ?></span>
            </div>
        <?php } ?>

    <form id="frmConfirm" name="frmConfirm" onsubmit="return validate();" method="post" action="<?php echo $actionURL; ?>" >
       <input type="hidden" value="<?php echo $token;?>" name="token" />
       <div class="txtName"><?php echo $lang_Recruit_JobApplicationConfirm_ApplicantName; ?></div>
        <div class="txtValue">
            <?php echo CommonFunctions::escapeHtml($application->getFirstName() . ' ' . $application->getLastName());?>
        </div><br/>
  		<div class="txtName"><?php echo $lang_Recruit_JobApplicationConfirm_Position; ?></div>
        <div class="txtValue"><?php echo CommonFunctions::escapeHtml($application->getJobTitleName()); ?></div><br/>

        <div class="txtName"><?php echo $lang_Recruit_JobApplicationDetails_Status; ?></div>
        <div class="txtValue" style="white-space:nowrap;">
            <?php
                $status = $statusList[$application->getStatus()];
                $statusDate = '';

                $latestEvent = $application->getLatestEvent();
                if (!empty($latestEvent)) {
                    if (($latestEvent->getEventType() == JobApplicationEvent::EVENT_SCHEDULE_FIRST_INTERVIEW) ||
                        ($latestEvent->getEventType() == JobApplicationEvent::EVENT_SCHEDULE_SECOND_INTERVIEW)) {

                        if ($latestEvent->getStatus() != JobApplicationEvent::STATUS_INTERVIEW_FINISHED) {
                            $statusDate = LocaleUtil::getInstance()->formatDateTime($latestEvent->getEventTime());
                        } else {
                            $statusDate = $lang_Recruit_JobApplicationHistory_StatusFinished;
                        }
                    }
                }

                echo $status;
                if (!empty($statusDate)) {
            ?>
            <span class="eventDate">(<?php echo $statusDate; ?>)</span>
            <?php } ?>
        </div><br />

        <div class="confirmMsg"><?php echo $confirmMsg;?></div>
        <div class="confirmDesc"><?php echo $confirmDesc;?></div>
        <input type="hidden" id="appId" name="appId" value="<?php echo $applicationId; ?>"/>

        <label class="txtName" for="txtNotes"><?php echo $lang_Recruit_JobApplication_Schedule_Notes; ?><span class="error">*</span></label>
        <textarea id="txtNotes" name="txtNotes" tabindex="1" rows="6" cols="40"></textarea><br/>

        <div class="formbuttons">
            <input type="submit" class="<?php echo $actionButtonClass; ?>"  id="actionBtn" tabindex="2"
                onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                value="<?php echo $actionName;?>" />
            <input type="button" class="cancelbutton" onclick="goBack();" tabindex="3"
                onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                 value="<?php echo $lang_Leave_Common_Cancel;?>" />
        </div>
       </form>
    </div>
    <script type="text/javascript">
        <!--
            if (document.getElementById && document.createElement) {
                roundBorder('outerbox');
            }
        -->
    </script>
    <div class="requirednotice"><?php echo preg_replace('/#star/', '<span class="required">*</span>', $lang_Commn_RequiredFieldMark); ?>.</div>
</div>
</body>
</html>
