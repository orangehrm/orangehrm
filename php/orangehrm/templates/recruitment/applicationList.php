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

$baseURL = "{$_SERVER['PHP_SELF']}?recruitcode={$_GET['recruitcode']}";
$detailsURL = $baseURL . '&action=ViewDetails';
$historyURL = $baseURL . '&action=ViewHistory';

$applications = $records['applications'];

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

?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script type="text/javascript" src="../../scripts/octopus.js"></script>
<script>
</script>
<link href="../../themes/<?php echo $styleSheet;?>/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("../../themes/<?php echo $styleSheet;?>/css/style.css"); </style>

<style type="text/css">
    <!--
    .roundbox {
        margin-top: 10px;
        margin-left: 0px;
        width:550px;
    }

    .roundbox_content {
        padding:15px 15px 15px 15px;
    }
    .eventDate {
        font-style: italic;
    }
    -->
</style>
</head>
<body>
	<p>
		<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
			<tr>
		  		<td width='100%'>
		  			<h2><?php echo $lang_Recruit_JobApplicationList_Heading; ?></h2>
		  		</td>
	  			<td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
	  		</tr>
		</table>
	</p>
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
    <?php if (count($applications) == 0) { ?>
        <?php echo $lang_Recruit_JobApplicationList_NoApplications;?>
    <?php } else { ?>
    <table width="520" class="simpleList" >
        <thead>
            <tr>
            <th width><?php echo $lang_Recruit_JobApplicationList_Name; ?></th>
            <th><?php echo $lang_Recruit_JobApplicationList_PositionApplied; ?></th>
            <th><?php echo $lang_Recruit_HiringManager; ?></th>
            <th><?php echo $lang_Recruit_VacancyStatus; ?></th>
            <th><?php echo $lang_Recruit_JobApplicationList_Actions; ?></th>
            <th></th>
            </tr>
        </thead>
        <?php
            $odd = false;
            foreach ($applications as $app) {
                $cssClass = ($odd) ? 'even' : 'odd';
                $odd = !$odd;

                $applicantName = $app->getFirstName() . ' ' . $app->getLastName();
                $applicationId = $app->getId();
                $status = $statusList[$app->getStatus()];

                $statusDate = '';

                $latestEvent = $app->getLatestEvent();
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
        ?>
        <tr>
            <td class="<?php echo $cssClass?>"><?php echo CommonFunctions::escapeHtml($applicantName); ?></td>
            <td class="<?php echo $cssClass?>"><?php echo CommonFunctions::escapeHtml($app->getJobTitleName()); ?></td>
            <td class="<?php echo $cssClass?>"><?php echo CommonFunctions::escapeHtml($app->getHiringManagerName()); ?></td>
            <td class="<?php echo $cssClass?>"><?php echo $status; ?>
            <?php if (!empty($statusDate)) { ?>
                <div class="eventDate">(<?php echo $statusDate; ?>)</div>
            <?php } ?>
            </td>
            <td class="<?php echo $cssClass?>">
            <?php
                $actions = $app->getPossibleActions();

                foreach ($actions as $action) {
                    $resourceName = 'lang_Recruit_JobApplicationAction_' . $action;
                    $actionName = isset($$resourceName) ? $$resourceName : $action;
                    $actionURL = $baseURL . '&action=' . $action . '&id=' . $applicationId;
            ?>
                <a href="<?php echo $actionURL; ?>" style="white-space:nowrap;">
                    <?php echo $actionName;?>
                </a><br />
            <?php
                }
            ?>
            </td>
            <td class="<?php echo $cssClass?>">
                <a href="<?php echo $historyURL. '&id=' . $applicationId; ?>" style="white-space:nowrap;">
                    <?php echo $lang_Recruit_JobApplicationList_EventHistory; ?></a><br />
                <a href="<?php echo $detailsURL. '&id=' . $applicationId; ?>"><?php echo $lang_Recruit_JobApplicationList_Details; ?></a>
            </td>
        </tr>
        <?php
            }
        ?>
    </table>
    <?php } ?>
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
