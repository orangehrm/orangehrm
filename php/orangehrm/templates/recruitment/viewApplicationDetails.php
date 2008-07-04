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

</script>

    <link href="../../themes/<?php echo $styleSheet;?>/css/style.css" rel="stylesheet" type="text/css">
    <style type="text/css">@import url("../../themes/<?php echo $styleSheet;?>/css/style.css"); </style>

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
    -->
</style>
</head>
<body>
	<p><h2 class="moduleTitle"><?php echo $lang_Recruit_JobApplicationDetails_Heading; ?></h2></p>
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
        <div class="txtName"><?php echo $lang_Recruit_JobApplicationHistory_DateApplied; ?></div>
        <div class="txtValue"><?php echo LocaleUtil::getInstance()->formatDate($application->getAppliedDateTime()); ?></div><br/>
  		<div class="txtName"><?php echo $lang_Recruit_ApplicationForm_Position; ?></div><div class="txtValue"><?php echo CommonFunctions::escapeHtml($application->getJobTitleName()); ?></div><br/>
    	<div class="txtName"><?php echo $lang_Recruit_ApplicationForm_FirstName; ?></div><div class="txtValue"><?php echo CommonFunctions::escapeHtml($application->getFirstName()); ?></div><br />
        <div class="txtName"><?php echo $lang_Recruit_ApplicationForm_MiddleName; ?></div><div class="txtValue"><?php echo CommonFunctions::escapeHtml($application->getMiddleName()); ?></div><br/>
        <div class="txtName"><?php echo $lang_Recruit_ApplicationForm_LastName; ?></div><div class="txtValue"><?php echo CommonFunctions::escapeHtml($application->getLastName()); ?></div><br/>
        <div class="txtName"><?php echo $lang_Recruit_ApplicationForm_Street1; ?></div><div class="txtValue"><?php echo CommonFunctions::escapeHtml($application->getStreet1()); ?></div><br/>
        <div class="txtName"><?php echo $lang_Recruit_ApplicationForm_Street2; ?></div><div class="txtValue"><?php echo CommonFunctions::escapeHtml($application->getStreet2()); ?></div><br/>
        <div class="txtName"><?php echo $lang_Recruit_ApplicationForm_City; ?></div><div class="txtValue"><?php echo CommonFunctions::escapeHtml($application->getCity()); ?></div><br/>
        <div class="txtName"><?php echo $lang_Recruit_ApplicationForm_Country; ?></div><div class="txtValue"><?php echo CommonFunctions::escapeHtml($application->getCountry()); ?></div><br/>
        <div class="txtName"><?php echo $lang_Recruit_ApplicationForm_StateProvince; ?></div><div class="txtValue"><?php echo CommonFunctions::escapeHtml($application->getProvince()); ?></div><br/>
        <div class="txtName"><?php echo $lang_Recruit_ApplicationForm_Zip; ?></div><div class="txtValue"><?php echo CommonFunctions::escapeHtml($application->getZip()); ?></div><br/>
        <div class="txtName"><?php echo $lang_Recruit_ApplicationForm_Phone; ?></div><div class="txtValue"><?php echo CommonFunctions::escapeHtml($application->getPhone()); ?></div><br/>
        <div class="txtName"><?php echo $lang_Recruit_ApplicationForm_Mobile; ?></div><div class="txtValue"><?php echo CommonFunctions::escapeHtml($application->getMobile()); ?></div><br/>
        <div class="txtName"><?php echo $lang_Recruit_ApplicationForm_Email; ?></div><div class="txtValue"><?php echo CommonFunctions::escapeHtml($application->getEmail()); ?></div><br/>
        <div class="txtName"><?php echo $lang_Recruit_ApplicationForm_Qualifications; ?></div><div class="txtBox"><?php echo CommonFunctions::escapeHtml($application->getQualifications()); ?></div><br/>
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
                $applicationId = $application->getId();

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
        <!--
        	if (document.getElementById && document.createElement) {
   	 			initOctopus();
			}
        -->
    </script>

</body>
</html>
