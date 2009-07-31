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

$baseURL = "{$_SERVER['PHP_SELF']}?recruitcode={$_GET['recruitcode']}";
$historyURL = $baseURL . '&id=' . $application->getId() .  '&action=ViewHistory';

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

$applicationId = $application->getId();
$resumeName = $application->getResumeName();
$formAction = 'CentralController.php?recruitcode=Application&action=replaceResume';

if (!empty($resumeName)) {
	$resumeUrl = 'CentralController.php?recruitcode=Application&action=downloadResume&id='.$applicationId;
	$resumeDeleteUrl = 'CentralController.php?recruitcode=Application&action=deleteResume&id='.$applicationId;
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
        location.href = "<?php echo "{$baseURL}&action=List"; ?>";
    }

    function handleResumeForm() {

        var resumeForm = document.getElementById('resumeForm')

	    if (resumeForm.className == 'show') {
	        resumeForm.className = 'hide';
	        resumeForm.style.display = 'none';
	    } else {
	        resumeForm.className = 'show';
	        resumeForm.style.display = 'block';
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

    .txtName,.txtValue,.txtBox {
        display: block;  /* block float the labels to left column, set a width */
        float: left;
        margin: 8px 0px 2px 0px; /* set top margin same as form input - textarea etc. elements */
    }

    .txtName {
        text-align: left;
        width: 150px;
        padding-left: 10px;
        font-weight: bold;
    }

    .txtValue {
        width: 300px;
    }

    .txtName, .txtBox {
        margin-left: 10x;
        padding-left: 4px;
        padding-right: 4px;
    }

    .txtBox {
        width: 300px;
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

    .successMsg {
        display: block;
        text-align: center;
        color:#347C17;
    }

    .errorMsg {
        display: block;
        text-align: center;
        color:#E41B17;
    }

    -->
</style>
</head>
<body>
    <div class="formpage">
        <div class="navigation">
	    	<input type="button" class="backbutton"
		        onclick="goBack();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
		        value="<?php echo $lang_Common_Back;?>" />
        </div>

        <?php

        if (!empty($records['message'])) {

        	//ToDo: Replace with a proper universal message style

        	if ($records['message'] == 'Resume deleted') {
        	    $msgStyle = 'successMsg';
        	    $message = $lang_Recruit_Resume_DeletionSucceeded;
        	} elseif ($records['message'] == 'Resume not deleted') {
        		$msgStyle = 'errorMsg';
        		$message = $lang_Recruit_Resume_DeletionFailed;
        	} elseif ($records['message'] == 'Resume replaced') {
        		$msgStyle = 'successMsg';
        		$message = $lang_Recruit_Resume_ReplaceSucceeded;
			} elseif ($records['message'] == 'Resume not replaced') {
				$msgStyle = 'errorMsg';
				$message = $lang_Recruit_Resume_ReplaceFailed;
			} elseif ($records['message'] == 'size-error') {
				$msgStyle = 'errorMsg';
				$message = $lang_Recruit_ApplyFailure_UploadSizeError;
			} elseif ($records['message'] == 'type-error') {
				$msgStyle = 'errorMsg';
				$message = $lang_Recruit_ApplyFailure_UploadTypeError;
        	}

        	echo "<div class=\"$msgStyle\">$message</div>";

        }

        ?>

        <div class="outerbox" style="width:600px;">
            <div class="mainHeading"><h2><?php echo $lang_Recruit_JobApplicationDetails_Heading;?></h2></div>

        <?php $message =  isset($_GET['message']) ? $_GET['message'] : null;
            if (isset($message)) {
                $messageType = CommonFunctions::getCssClassForMessage($message);
                $message = "lang_Common_" . $message;
        ?>
            <div class="messagebar">
                <span class="<?php echo $messageType; ?>"><?php echo (isset($$message)) ? $$message: ''; ?></span>
            </div>
        <?php } ?>

        <div class="txtName"><?php echo $lang_Recruit_JobApplicationHistory_DateApplied; ?></div>
        <div class="txtValue"><?php echo LocaleUtil::getInstance()->formatDate($application->getAppliedDateTime()); ?></div><br/>

  		<div class="txtName"><?php echo $lang_Recruit_ApplicationForm_Position; ?></div>
  		<div class="txtValue"><?php echo CommonFunctions::escapeHtml($application->getJobTitleName()); ?></div><br/>

    	<div class="txtName"><?php echo $lang_Recruit_ApplicationForm_FirstName; ?></div>
    	<div class="txtValue"><?php echo CommonFunctions::escapeHtml($application->getFirstName()); ?></div><br />

        <div class="txtName"><?php echo $lang_Recruit_ApplicationForm_MiddleName; ?></div>
        <div class="txtValue"><?php echo CommonFunctions::escapeHtml($application->getMiddleName()); ?></div><br/>

        <div class="txtName"><?php echo $lang_Recruit_ApplicationForm_LastName; ?></div>
        <div class="txtValue"><?php echo CommonFunctions::escapeHtml($application->getLastName()); ?></div><br/>

        <div class="txtName"><?php echo $lang_Recruit_ApplicationForm_Street1; ?></div>
        <div class="txtValue"><?php echo CommonFunctions::escapeHtml($application->getStreet1()); ?></div><br/>

        <div class="txtName"><?php echo $lang_Recruit_ApplicationForm_Street2; ?></div>
        <div class="txtValue"><?php echo CommonFunctions::escapeHtml($application->getStreet2()); ?></div><br/>

        <div class="txtName"><?php echo $lang_Recruit_ApplicationForm_City; ?></div>
        <div class="txtValue"><?php echo CommonFunctions::escapeHtml($application->getCity()); ?></div><br/>

        <div class="txtName"><?php echo $lang_Recruit_ApplicationForm_Country; ?></div>
        <div class="txtValue"><?php echo CommonFunctions::escapeHtml($application->getCountry()); ?></div><br/>

        <div class="txtName"><?php echo $lang_Recruit_ApplicationForm_StateProvince; ?></div>
        <div class="txtValue"><?php echo CommonFunctions::escapeHtml($application->getProvince()); ?></div><br/>

        <div class="txtName"><?php echo $lang_Recruit_ApplicationForm_Zip; ?></div>
        <div class="txtValue"><?php echo CommonFunctions::escapeHtml($application->getZip()); ?></div><br/>

        <div class="txtName"><?php echo $lang_Recruit_ApplicationForm_Phone; ?></div>
        <div class="txtValue"><?php echo CommonFunctions::escapeHtml($application->getPhone()); ?></div><br/>

        <div class="txtName"><?php echo $lang_Recruit_ApplicationForm_Mobile; ?></div>
        <div class="txtValue"><?php echo CommonFunctions::escapeHtml($application->getMobile()); ?></div><br/>
        <div class="txtName"><?php echo $lang_Recruit_ApplicationForm_Email; ?></div>
        <div class="txtValue"><?php echo CommonFunctions::escapeHtml($application->getEmail()); ?></div><br/>
        <div class="txtName"><?php echo $lang_Recruit_ApplicationForm_Qualifications; ?></div>
        <div class="txtBox" style="margin-left:-2px">
        	<pre style="font-family: Arial, Helvetica, sans-serif; line-height:10px"><?php
        		echo nl2br(wordwrap(trim(CommonFunctions::escapeHtml($application->getQualifications())), 75));
        	?></pre>
        </div>
        <br/>

		<div class="txtName"><?php echo $lang_Recruit_ApplicationForm_Resume; ?></div>

        <?php
        if (!empty($resumeName)) {
        ?>

		<div class="txtValue">
		<a href="<?php echo $resumeUrl; ?>"><?php echo $lang_Recruit_ApplicationForm_ResumeDownload; ?></a>
		( <a href="<?php echo $resumeDeleteUrl; ?>"><?php echo $lang_Common_Delete; ?></a> |
		  <a href="#" onClick="handleResumeForm()"><?php echo $lang_Common_Replace; ?></a>)
		</div><br/>

        <?php } else { ?>

		<div class="txtValue">
		 <a href="#" onClick="handleResumeForm()"><?php echo $lang_Common_Add; ?></a>
		</div><br/>

        <?php } ?>

		<!-- Resume form: begins-->
		<?php /* Names of form elements should comply with expected names at EXTRACTOR_JobApplication */ ?>
		<div id="resumeForm" class="hide" style="display:none">
		<form name="frmResume" id="frmResume" method="post" action="<?php echo $formAction;?>" enctype="multipart/form-data">
		<input type="hidden" name="txtId" value="<?php echo $applicationId; ?>" />
		<input type="hidden" name="txtFirstName" value="<?php echo $application->getFirstName(); ?>" />
		<input type="hidden" name="txtLastName" value="<?php echo $application->getLastName(); ?>" />
		<input type="file" id="txtResume" name="txtResume" />
		<input type="submit" name="submit" class="savebutton" value="<?php echo $lang_Common_Save; ?>" /><br/>
		<?php echo $lang_Recruit_ApplicationForm_ResumeDescription; ?>
		</form>
		</div>
		<!-- Resume form: ends-->

        <br />

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
        </div><br/>
        <div class="txtName"><?php echo $lang_Recruit_JobApplicationDetails_Actions; ?></div><div class="txtValue">
            <?php
                $authManager = new RecruitmentAuthManager();
                $authorize = new authorize($_SESSION['empID'], $_SESSION['isAdmin']);
                $actions = $authManager->getAllowedActions($authorize, $application);

                foreach ($actions as $action) {
                    $resourceName = 'lang_Recruit_JobApplicationAction_' . $action;
                    $actionName = isset($$resourceName) ? $$resourceName : $action;
                    $actionURL = $baseURL . '&action=Confirm' . $action . '&id=' . $applicationId;
            ?>
                <a href="<?php echo $actionURL; ?>" style="white-space:nowrap;">
                    <?php echo $actionName;?>
                </a>&nbsp;&nbsp;
            <?php
                }
            ?>
            </div><br /><br />

    </div>
    <script type="text/javascript">
    //<![CDATA[
        if (document.getElementById && document.createElement) {
            roundBorder('outerbox');
        }
    //]]>
    </script>
    </div>
</body>
</html>
