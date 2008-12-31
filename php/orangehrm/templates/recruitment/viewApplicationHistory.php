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
$events = $application->getEvents();

$baseURL = "{$_SERVER['PHP_SELF']}?recruitcode={$_GET['recruitcode']}";
$editEventURL = $baseURL . '&action=EditEvent';


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

$eventStatusList = array(
    JobApplicationEvent::STATUS_INTERVIEW_SCHEDULED => $lang_Recruit_JobApplicationHistory_StatusInterviewScheduled,
    JobApplicationEvent::STATUS_INTERVIEW_FINISHED => $lang_Recruit_JobApplicationHistory_StatusFinished
);

$eventTitles = array(
    JobApplicationEvent::EVENT_REJECT => $lang_Recruit_JobApplicationHistory_Rejected,
    JobApplicationEvent::EVENT_SCHEDULE_FIRST_INTERVIEW => $lang_Recruit_JobApplicationHistory_FirstInterview,
    JobApplicationEvent::EVENT_SCHEDULE_SECOND_INTERVIEW => $lang_Recruit_JobApplicationHistory_SecondInterview,
    JobApplicationEvent::EVENT_OFFER_JOB => $lang_Recruit_JobApplicationHistory_OfferedJob,
    JobApplicationEvent::EVENT_MARK_OFFER_DECLINED => $lang_Recruit_JobApplicationHistory_OfferMarkedAsDeclined,
    JobApplicationEvent::EVENT_SEEK_APPROVAL => $lang_Recruit_JobApplicationHistory_SeekApproval,
    JobApplicationEvent::EVENT_APPROVE => $lang_Recruit_JobApplicationHistory_Approved,
);

$picDir = "../../themes/{$styleSheet}/pictures/";
$iconDir = "../../themes/{$styleSheet}/icons/";

$backImg = $picDir . 'btn_back.gif';
$backImgPressed = $picDir . 'btn_back_02.gif';

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

    function addEditMode(btn) {
        if (!('editMode' in btn)) {
            btn.editMode = false;
        }
    }

    function edit(btn, formId) {

        addEditMode(btn);
        form = $(formId);

        if(btn.editMode) {
            form.submit();
        } else {
            btn.editMode = true;
            form.txtNotes.disabled = false;
            if ('cmbStatus' in form) {
                form.cmbStatus.disabled = false;
            }
            btn.value = '<?php echo $lang_Common_Save;?>';
            btn.className = 'savebutton';
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

    label, .txtName,.txtValue,.txtBox, .eventTitle {
        display: block;  /* block float the labels to left column, set a width */
        float: left;
        margin: 3px 0px 2px 0px; /* set top margin same as form input - textarea etc. elements */
    }

    .txtName, .eventTitle {
        text-align: left;
        width: 100px;
        padding-left: 10px;
    }

    .txtValue {
        width: auto;
    }

    .txtName, .txtBox, .eventTitle {
        margin-left: 10x;
        padding-left: 4px;
        padding-right: 4px;
    }

    .col2 {
        text-align: right;
        padding-right: 10px;
    }

    label {
        text-align: left;
        width: 100px;
        padding-left: 4px;
        padding-right: 4px;
    }

    textarea {
        width: 300px;
        height: 50px;
        padding-right: 0px;
        color: #444444;
    }

    .eventTitle {
        width: 95%;
        background-color: #EEEEEE;
        border-style: solid;
        border-width: 0px 0px 1px 0px;
        border-color: #888888;
    }

    .txtBox {
        width: 100px;
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
    -->
</style>
</head>
<body>
    <?php
        $applicantName = CommonFunctions::escapeHtml($application->getFirstName() . ' ' . $application->getLastName());
        $jobTitleName = CommonFunctions::escapeHtml($application->getJobTitleName());
        $heading = $lang_Recruit_JobApplicationHistory_EventHistory . ' - ' . $applicantName . ' (' 
            . $lang_Recruit_JobApplicationHistory_ApplicationForThePositionOf . ' ' . $jobTitleName . ')';
    ?>
    <div class="formpage">
        <div class="navigation">
            <a href="#" class="backbutton" title="<?php echo $lang_Common_Back;?>" onclick="goBack();">
                <span><?php echo $lang_Common_Back;?></span>
            </a>
        </div>
        <div class="outerbox">
            <div class="mainHeading"><h2><?php echo $heading;?></h2></div>
        
        <?php $message =  isset($_GET['message']) ? $_GET['message'] : null;
            if (isset($message)) {
                $messageType = CommonFunctions::getCssClassForMessage($message);
                $message = "lang_Common_" . $message;
        ?>
            <div class="messagebar">
                <span class="<?php echo $messageType; ?>"><?php echo (isset($$message)) ? $$message: ""; ?></span>
            </div>  
        <?php } ?>
    
  		<div class="txtName"><?php echo $lang_Recruit_JobApplicationHistory_DateApplied; ?></div>
        <div class="txtValue"><?php echo LocaleUtil::getInstance()->formatDate($application->getAppliedDateTime()); ?></div><br />
        <div class="txtName"><?php echo $lang_Recruit_JobApplicationDetails_Status; ?></div>
        <div class="txtValue" style="white-space:nowrap;">
            <?php echo $statusList[$application->getStatus()]; ?></div><br/>

        <?php
            $authManager = new RecruitmentAuthManager();
            $auth = new authorize($_SESSION['empID'], $_SESSION['isAdmin']);
            $role = $authManager->getRoleForApplication($auth, $application);
            $eventCount = 0;
            foreach ($events as $event) {

                $allowEdit = $authManager->isAllowedToEditEvent($auth, $event);
                $allowStatusChange = $authManager->isAllowedToChangeEventStatus($auth, $event);

                $title = $eventTitles[$event->getEventType()];

                if (($event->getEventType() == JobApplicationEvent::EVENT_SCHEDULE_FIRST_INTERVIEW) ||
                        ($event->getEventType() == JobApplicationEvent::EVENT_SCHEDULE_SECOND_INTERVIEW)) {

                    $showEventDate = true;
                    $evenDateLabel = $lang_Recruit_JobApplicationHistory_InterviewTime;
                    $showStatus = true;
                    $showOwner = true;
                    $creatorLabel = $lang_Recruit_JobApplicationHistory_ScheduledBy;
                } else {
                    $eventDateLabel = '';
                    $showEventDate = false;
                    $showStatus = false;
                    $showOwner = false;
                    $creatorLabel = $lang_Recruit_JobApplicationHistory_By;
                }

                $eventCount++;

                $createdBy = $event->getCreatorName();
                $createdDate = LocaleUtil::getInstance()->formatDateTime($event->getCreatedTime());
                $eventDate = LocaleUtil::getInstance()->formatDateTime($event->getEventTime());
                $owner = CommonFunctions::escapeHtml($event->getOwnerName());
                $notes = CommonFunctions::escapeHtml($event->getNotes());

                $formId = 'frmEvent' . $event->getId();
        ?>
        <div class="eventTitle"><?php echo $title; ?></div></br>
        <form id="<?php echo $formId; ?>" name="<?php echo $formId; ?>"
            method="post" action="<?php echo $editEventURL;?>">

            <input type="hidden" id="txId" name="txtId" value="<?php echo $event->getId();?>"/>
            <div class="txtName"><?php echo $creatorLabel; ?></div>
            <div class="txtValue"><?php echo $createdBy; ?></div>
            <div class="txtName col2" ><?php echo $lang_Recruit_JobApplicationHistory_At; ?></div>
            <div class="txtValue"><?php echo $createdDate; ?></div>
            <br/>

<?php if ($showEventDate) { ?>
            <div class="txtName"><?php echo $evenDateLabel; ?></div>
            <div class="txtValue"><?php echo $eventDate; ?></div><br/>
<?php } ?>
<?php if ($showOwner) { ?>
            <div class="txtName"><?php echo $lang_Recruit_JobApplicationHistory_Interviewer; ?></div>
            <div class="txtValue"><?php echo $owner; ?></div><br/>
<?php } ?>
<?php if ($showStatus) { ?>
            <div class="txtName"><?php echo $lang_Recruit_JobApplicationHistory_Status; ?></div>
            <?php if ($allowStatusChange) { ?>
                <select name="cmbStatus" disabled="true" >
                    <?php
                    foreach ($eventStatusList as $status=>$statusName) {
                        $selected = ($status == $event->getStatus()) ? 'selected' : '';
                        echo "<option $selected value=". $status . ">" . $statusName . "</option>";
                    }
                    ?>
                </select>
            <?php } else { ?>
                <div class="txtValue">
                <?php
                    if (isset($eventStatusList[$event->getStatus()])) {
                        echo $eventStatusList[$event->getStatus()];
                    }
                ?>
                </div>
            <?php } ?>
            <br />
<?php } ?>
            <label for="txtNotes"><?php echo $lang_Recruit_JobApplicationHistory_Notes; ?></label>
            <textarea name="txtNotes" disabled="true"><?php echo $notes; ?></textarea>
            <?php if ($allowEdit) { ?>
            <input type="button" class="editbutton" id="editBtn" 
                onclick="edit(this, '<?php echo $formId; ?>');" onmouseover="moverButton(this);" onmouseout="moutButton(this);"                          
                value="<?php echo $lang_Common_Edit;?>" />                
            <br/><br/>
            <?php } ?>
        </form>

        <?php
            }
            if ($eventCount == 0) {
                echo $lang_Recruit_JobApplicationHistory_NoEvents;
            }
        ?>
  </div>
    <script type="text/javascript">
        <!--
            if (document.getElementById && document.createElement) {
                roundBorder('outerbox');                
            }
        -->
    </script>
</div>
</body>
</html>
