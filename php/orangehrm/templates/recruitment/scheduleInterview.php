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
$formAction = $baseURL . '&action=Save' . $_GET['action'];

$managers = $records['managers'];
$application = $records['application'];
$num = $records['interview'];
$locRights=$_SESSION['localRights'];

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
<?php include ROOT_PATH."/lib/common/calendar.php"; ?>
<script>

    var dateTimeFormat = YAHOO.OrangeHRM.calendar.format + " " + YAHOO.OrangeHRM.time.format;
    var firstInterviewDate = false;

<?php
    if ($num == 2) {
        $event = $application->getEventOfType(JobApplicationEvent::EVENT_SCHEDULE_FIRST_INTERVIEW);
        if ($event) {
            $eventTime = $event->getEventTime();
            if (!empty($eventTime)) {
                echo "\tfirstInterviewDate = '" . LocaleUtil::getInstance()->formatDateTime($eventTime) . "';";
           }
        }
    }
?>


    function goBack() {
        location.href = "<?php echo $baseURL; ?>&action=List";
    }

    /**
     * Check if second interview date is after the first interview date
     */
    function secondInterviewAfterFirst() {

        if (firstInterviewDate) {

            timeVal = strToTime(firstInterviewDate, dateTimeFormat);
            if (timeVal) {
                newTime = $('txtDate').value.trim() + " " + $('txtTime').value.trim();
                newTimeVal = strToTime(newTime, dateTimeFormat);

                if (newTimeVal && newTimeVal < timeVal) {
                    return true;
                }
            }
        }
        return false;
    }

	function validate() {

		err = false;
		msg = '<?php echo $lang_Error_PleaseCorrectTheFollowing; ?>\n\n';

        if ($('txtDate').value.trim() == '') {
            err = true;
            msg += "\t- <?php echo $lang_Recruit_JobApplication_PleaseSpecifyDate; ?>\n";
        } else {
            dateVal = strToDate($("txtDate").value, YAHOO.OrangeHRM.calendar.format);
            if (!dateVal) {
                err = true;
                msg += "\t- <?php echo $lang_Recruit_JobApplication_PleaseSpecifyValidDate; ?>" + YAHOO.OrangeHRM.calendar.format + "\n";
            }
        }

        timeVal = $('txtTime').value.trim();
        if (timeVal == '') {
            err = true;
            msg += "\t- <?php echo $lang_Recruit_JobApplication_PleaseSpecifyTime; ?>\n";
        } else {
            dateNow = formatDate(new Date(), YAHOO.OrangeHRM.calendar.format);
            timeVal = strToTime(dateNow + " " + timeVal, dateTimeFormat);
            if (!timeVal) {
                err = true;
                msg += "\t- <?php echo $lang_Recruit_JobApplication_PleaseSpecifyValidTime; ?>" + YAHOO.OrangeHRM.time.format + "\n";
            }
        }

        if (!err) {
            if (secondInterviewAfterFirst()) {
                err = true;
                msg += "\t- <?php echo $lang_Recruit_JobApplication_SecondInterviewShouldBeAfterFirst; ?>(" + firstInterviewDate + ")\n";
            }
        }

		errors = new Array();
        if ($('cmbInterviewer').value == -1) {
			err = true;
			msg += "\t- <?php echo $lang_Recruit_JobApplication_PleaseSpecifyInterviewer; ?>\n";
        }

		if (err) {
			alert(msg);
			return false;
		} else {
			return true;
		}
	}

    function save() {

		if (validate()) {
        	$('frmInterview').submit();
		} else {
			return false;
		}
    }

	function reset() {
		$('frmInterview').reset();
	}
YAHOO.OrangeHRM.container.init();
</script>

    <link href="../../themes/<?php echo $styleSheet;?>/css/style.css" rel="stylesheet" type="text/css">
    <style type="text/css">@import url("../../themes/<?php echo $styleSheet;?>/css/style.css"); </style>

    <style type="text/css">
    <!--

    label,select,input,textarea {
        display: block;  /* block float the labels to left column, set a width */
        width: 150px;
        float: left;
        margin: 10px 0px 2px 0px; /* set top margin same as form input - textarea etc. elements */
    }
    input[type="checkbox"] {
		width: 15px;
		background-color: transparent;
		vertical-align: bottom;
    }

    /* this is needed because otherwise, hidden fields break the alignment of the other fields */
    input[type="hidden"] {
        display: none;
        border: none;
        background-color: red;
    }

    label {
        text-align: left;
        width: 110px;
        padding-left: 10px;
    }

    select,input,textarea {
        margin-left: 10px;
    }

    input,textarea {
        padding-left: 4px;
        padding-right: 4px;
    }

    textarea {
        width: 330px;
        height: 150px;
    }

    form {
        min-width: 550px;
        max-width: 600px;
    }

    br {
        clear: left;
    }

    .roundbox {
        margin-top: 10px;
        margin-left: 0px;
        width: 500px;
    }

    .roundbox_content {
        padding:15px;
    }

	.hidden {
		display: none;
	}

	.display-block {
		display: block;
	}

    .calendarBtn {
        width: auto;
        border-style: none !important;
        border: 0px !important;
    }

    #txtDate, #txtTime {
        width: 100px;
    }

	#nomanagers {
		font-style: italic;
		color: red;
        padding-left: 10px;
        width: 400px;
        border: 1px;
	}
    -->
</style>
</head>
<body>
	<p>
		<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
			<tr>
		  		<td width='100%'>
		  			<h2>

<?php
$applicantName = $application->getFirstName() . ' ' . $application->getLastName();
    if ($num == 1) {
        $heading = $lang_Recruit_JobApplication_ScheduleFirstInterview;
    } else {
        $heading = $lang_Recruit_JobApplication_ScheduleSecondInterview;
    }
    echo $heading . ' ' . $applicantName;
?>
                    </h2>
		  		</td>
	  			<td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
	  		</tr>
		</table>
	</p>
  	<div id="navigation" style="margin:0;">
  		<img title="Back" onMouseOut="this.src='<?php echo $backImg;?>';"
  			 onMouseOver="this.src='<?php echo $backImgPressed;?>'"
  			 src="<?php echo $backImg;?>" onClick="goBack();">
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

  <form name="frmInterview" id="frmInterview" method="post" action="<?php echo $formAction;?>">
		<input type="hidden" id="txtId" name="txtId" value="<?php echo $application->getId();?>"/><br/>

        <label for="txtDate"><span class="error">*</span> <?php echo $lang_Recruit_JobApplication_Schedule_Date; ?></label>
        <input type="text" id="txtDate" name="txtDate" value="" size="10" tabindex="1" />
        <input type="button" id="btnToDate" name="btnToDate" value="  " class="calendarBtn"/><br/>

        <label for="txtTime"><span class="error">*</span> <?php echo $lang_Recruit_JobApplication_Schedule_Time; ?></label>
        <input type="text" id="txtTime" name="txtTime" tabindex="2" /><br/>

		<label for="cmbInterviewer"><span class="error">*</span> <?php echo $lang_Recruit_JobApplication_Schedule_Interviewer; ?></label>
        <select id="cmbInterviewer" name="cmbInterviewer" tabindex="3">
	        <option value="-1">-- <?php echo $lang_Recruit_JobApplication_Select;?> --</option>
                <?php
                foreach ($managers as $manager) {

                	// Ugly, but this is how EmpInfo returns employees
                	$empNum = $manager[2];
                	$empName = CommonFunctions::escapeHtml($manager[1]);
	                echo "<option value=". $empNum . ">" . $empName . "</option>";
                }
                ?>
        </select><br/>
		<?php
				if (count($managers) == 0) {
		?>
			<div id="nomanagers">
				<?php echo $lang_Recruit_NoManagersNotice; ?>
			</div>
		<?php
				}
		?>
		<label for="txtNotes">&nbsp;<?php echo $lang_Recruit_JobApplication_Schedule_Notes; ?></label>
        <textarea id="txtNotes" name="txtNotes" tabindex="4"></textarea><br/>
		<br/><br/>

        <div align="left">
            <img onClick="save();" id="saveBtn"
            	onMouseOut="this.src='<?php echo $picDir;?>btn_save.gif';"
                onMouseOver="this.src='<?php echo $picDir;?>btn_save_02.gif';"
            	src="<?php echo $picDir;?>btn_save.gif">
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
    <div id="cal1Container" style="position:absolute;" ></div>
</body>
</html>
