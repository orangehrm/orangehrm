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

$confirmMsgRes = 'lang_Recruit_JobApplicationConfirm_Confirm' . $action;
$confirmMsg = isset($$confirmMsgRes) ? $$confirmMsgRes : $confirmMsgRes;
$confirmDescRes = 'lang_Recruit_JobApplicationConfirm_Confirm' . $action . 'Desc';
$confirmDesc = isset($$confirmDescRes) ? $$confirmDescRes : $confirmDescRes;

$baseURL = "{$_SERVER['PHP_SELF']}?recruitcode={$_GET['recruitcode']}";
$actionURL = $baseURL . '&action=' . $action;

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

?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script type="text/javascript" src="../../scripts/octopus.js"></script>
<script>

    function goBack() {
        location.href = "<?php echo "{$baseURL}&action=List"; ?>";
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

</script>

    <link href="../../themes/<?php echo $styleSheet;?>/css/style.css" rel="stylesheet" type="text/css">
    <style type="text/css">@import url("../../themes/<?php echo $styleSheet;?>/css/style.css"); </style>

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

    label {

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

    .roundbox {
        margin-top: 10px;
        margin-left: 15px;
        margin-right: auto;
        width: 500px;
    }

    body {
    	margin-top: 10px;
        margin-left: auto;
        margin-right: auto;
        width: 780px;
    }

    .roundbox_content {
        padding:5px;
    }

	.hidden {
		display: none;
	}

	.display-block {
		display: block;
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
    .buttonSec {
        padding-left: 15px;
        padding-bottom: 15px;
    }
    -->
</style>
</head>
<body>
	<p><h2 class="moduleTitle"><?php echo $lang_Recruit_JobApplicationConfirm_Heading . $actionName; ?></h2></p>
  	<div id="navigation" style="margin:0;">
  		<img title="<?php echo $lang_Common_Back;?>" onMouseOut="this.src='<?php echo $backImg; ?>';"
  			 onMouseOver="this.src='<?php echo $backImgPressed;?>';" src="<?php echo $backImg;?>"
  			 onClick="goBack();">
	</div>
    <?php $message =  isset($_GET['message']) ? $_GET['message'] : null;
    	if (isset($message)) {
			$col_def = CommonFunctions::getCssClassForMessage($message);
			$message = "lang_Common_" . $message;
	?>
	<div class="message">
		<font class="<?php echo $col_def?>" size="-1" face="Verdana, Arial, Helvetica, sans-serif">
			<?php echo (isset($$message)) ? $$message: ""; ?>
		</font>
	</div>
	<?php }	?>
  <div class="roundbox">
    <form id="frmConfirm" name="frmConfirm" onsubmit="return validate();" method="post" action="<?php echo $actionURL; ?>" >
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

        <label class="txtName" for="txtNotes"><span class="error">*</span><?php echo $lang_Recruit_JobApplication_Schedule_Notes; ?></label>
        <textarea id="txtNotes" name="txtNotes" tabindex="1"></textarea><br/>

        <div class="buttonSec">
            <input type="submit" id="actionBtn" value="<?php echo $actionName;?>"/>
            <input type="button" id="cancelBtn" onClick="goBack();" value="<?php echo $lang_Leave_Common_Cancel;?>" />
        </div>
       </form>
    </div>
    <script type="text/javascript">
        <!--
        	if (document.getElementById && document.createElement) {
   	 			initOctopus();
			}
        -->
    </script>
    <div id="notice"><?php echo preg_replace('/#star/', '<span class="error">*</span>', $lang_Commn_RequiredFieldMark); ?>.</div>
</body>
</html>
