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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<script type="text/javascript" src="../../scripts/archive.js"></script>
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
    .eventDate {
        font-style: italic;
    }
    
    table.simpleList {
        margin: 4px 4px 4px 4px;
    }
    -->
</style>
</head>
<body>
    <div class="formpage2col">
        <div class="outerbox">
            <div class="mainHeading"><h2><?php echo $lang_Recruit_JobApplicationList_Heading;?></h2></div>
        
        <?php $message =  isset($_GET['message']) ? $_GET['message'] : null;
            if (isset($message)) {
                $messageType = CommonFunctions::getCssClassForMessage($message);
                $message = "lang_Common_" . $message;
        ?>
            <div class="messagebar">
                <span class="<?php echo $messageType; ?>"><?php echo (isset($$message)) ? $$message: ""; ?></span>
            </div>  
        <?php } ?>



    <?php if (count($applications) == 0) { ?>
        <?php echo $lang_Recruit_JobApplicationList_NoApplications;?>
    <?php } else { ?>
    <table class="simpleList">
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
            $authManager = new RecruitmentAuthManager();
            $authorize = new authorize($_SESSION['empID'], $_SESSION['isAdmin']);

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
                $actions = $authManager->getAllowedActions($authorize, $app);

                foreach ($actions as $action) {
                    $resourceName = 'lang_Recruit_JobApplicationAction_' . $action;
                    $actionName = isset($$resourceName) ? $$resourceName : $action;
                    $actionURL = $baseURL . '&action=Confirm' . $action . '&id=' . $applicationId;
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
    //<![CDATA[
        if (document.getElementById && document.createElement) {
            roundBorder('outerbox');                
        }
    //]]>
    </script>
</div>  
</body>
</html>
