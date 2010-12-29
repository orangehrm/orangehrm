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

 if (isset($modifier[1])) {
 	$dispYear = $modifier[1];
 }

$token = "";
if(isset($records['token'])) {
   $token = $records['token'];
   unset($records['token']);
}
 
if(isset($modifier['token'])) {
 $token = $modifier['token'];
 unset($modifier['token']);
}

$actionFlag = "";
if(isset($modifier['actionFlag'])) {
   $actionFlag = $modifier['actionFlag'];
   unset($modifier['actionFlag']);
}
 if (isset($modifier[0]) && ($modifier[0] == "Taken")) {

 	$empInfo = $records[count($records)-1][0];
 	$employeeName = $empInfo[2].' '.$empInfo[1];

 	array_pop($records);

 	$records = $records[0];
 }

if ($modifier[0] === "SUP") {
 	$employeeName = $records[0]->getEmployeeName();
	$lang_Title = preg_replace('/#employeeName/', $employeeName, $lang_Leave_Leave_Requestlist_Title1);
} else if ($modifier[0] === "ADMIN") {
 	$employeeName = $records[0]->getEmployeeName();
	$lang_Title = preg_replace('/#employeeName/', $employeeName, $lang_Leave_Leave_Requestlist_Title2);
} else if ($modifier[0] === "Taken") {
 $lang_Title = preg_replace(array('/#employeeName/', '/#dispYear/'), array($employeeName, $dispYear) , $lang_Leave_Leave_list_Title2);
} else if ($modifier[0] === "MY") {
	$lang_Title = $lang_Leave_Leave_list_TitleMyLeaveList;
} else {
 $lang_Title = $lang_Leave_Leave_list_Title3;
}

$action = "Leave_ChangeStatus";

if ($modifier[0] === "SUP") {
	$backLink = "Leave_FetchLeaveSupervisor";
} else if ($modifier[0] === "ADMIN") {
	$backLink = "Leave_FetchLeaveAdmin";
} else {
	$backLink = "Leave_FetchLeaveEmployee";
}
?>

<script type="text/javascript">
//<![CDATA[
	function goBack () {
		<?php if ($modifier[0] == "ADMIN") { ?>
			window.location = "?leavecode=Leave&action=Leave_FetchLeaveAdmin";
		<?php
	    } else if ($modifier[0] == "Taken") {
		?>
		window.location = "?leavecode=Leave&action=Leave_Summary&id=<?php echo $_REQUEST['id'];?>";
		<?php } else { ?>
			window.location = "?leavecode=Leave&action=<?php echo $backLink; ?>";
		<?php } ?>
	}

	function validateLeaveList() {
		for (i = 0; i < noOfLeaveRecords; i++) {
			if ($('txtComment_' + i).value.length > <?php echo LeaveRequests::MAX_COMMENT_LENGTH ?>) {
				alert('<?php echo CommonFunctions::escapeForJavascript(sprintf($lang_Leave_LeaveCommentTooLong, LeaveRequests::MAX_COMMENT_LENGTH)); ?>');
				$('txtComment_' + i).focus();
				return false;
			}
		}
		return true;
	}
//]]>
</script>

<div class="outerbox">
<form id="frmCancelLeave" name="frmCancelLeave" method="post"
	action="<?php echo $_SERVER['PHP_SELF']; ?>?leavecode=Leave&amp;action=<?php echo $action; ?><?php if($actionFlag != "") {?>&actionFlag=<?php echo $actionFlag; }?>"
	onsubmit="return validateLeaveList()">
   <input type="hidden" value="<?php echo $token;?>" name="token"/>
    <div class="mainHeading"><h2><?php echo $lang_Title; ?></h2></div>

    <?php if (isset($_GET['message']) && $_GET['message'] != 'xx') {
            $message =  $_GET['message'];
            $messageType = CommonFunctions::getCssClassForMessage($message);
            $messageStr = "lang_Leave_" . $message;
    ?>
    	<?php if (isset($$messageStr)) { ?>
        <div class="messagebar">
            <span class="<?php echo $messageType; ?>"><?php echo $$messageStr; ?></span>
        </div>
        <?php } ?>
    <?php } ?>


    <div class="actionbar">
        <div class="actionbuttons">

<?php   if ((is_array($records)) && ($modifier[0] !== "Taken")) { ?>

            <input type="submit" class="savebutton" name="Save"
                onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                value="<?php echo $lang_Common_Save;?>" />
           
        <?php
            }
        ?>
		<input type="button" class="savebutton"
                onclick="goBack();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                value="<?php echo $lang_Common_Back;?>" />
        </div>
        <div class="noresultsbar"><?php echo (!is_array($records)) ? $lang_Error_NoRecordsFound : '';?></div>
        <div class="pagingbar"></div>
        <br class="clear" />
    </div>
    <br class="clear" />

<table border="0" cellpadding="0" cellspacing="0" class="data-table">
  <thead>
	<tr>
    	<td width="155px"><?php echo $lang_Leave_Common_Date;?></td>
    	<td width="90px"><?php echo $lang_Leave_Common_LeaveType;?></td>
    	<td width="110px"><?php echo $lang_Leave_Common_Status;?></td>
    	<td width="130px"><?php echo $lang_Leave_Duration;?></td>
    	<td width="150px"><?php echo $lang_Leave_Common_Comments;?></td>
	</tr>
  </thead>
  <tbody>
<?php
	$j = 0;
	$idIndex = 0;
    if (is_array($records)) {
		foreach ($records as $record) {
			if(!($j%2)) {
				$cssClass = 'odd';
			 } else {
			 	$cssClass = 'even';
			 }
			 $j++;

?>
  	<input type="hidden" name="txtLeaveRequestId[]" id="txtLeaveRequestId[]" value="<?php echo $record->getLeaveRequestId(); ?>" />
	<input type="hidden" name="txtEmployeeName[]" id="txtEmployeeName[]" value="<?php echo $record->getEmployeeName(); ?>" />
	<input type="hidden" name="txtLeaveDate[]" id="txtLeaveDate[]" value="<?php echo $record->getLeaveDate();; ?>" />
	<input type="hidden" name="txtLeaveTypeName[]" id="txtLeaveTypeName[]" value="<?php echo $record->getLeaveTypeName(); ?>" />
	<input type="hidden" name="sltLeaveLength[]" id="sltLeaveLength[]" value="<?php echo $record->getLeaveLengthHours(); ?>" />
  <tr>
    <td class="<?php echo $cssClass; ?>"><?php echo LocaleUtil::getInstance()->formatDate($record->getLeaveDate()); ?></td>
    <td class="<?php echo $cssClass; ?>"><?php echo $record->getLeaveTypeName(); ?></td>
    <td class="<?php echo $cssClass; ?>"><?php
   			$statusArr = array($record->statusLeaveRejected => $lang_Leave_Common_Rejected, $record->statusLeaveCancelled => $lang_Leave_Common_Cancelled, $record->statusLeavePendingApproval => $lang_Leave_Common_PendingApproval, $record->statusLeaveApproved => $lang_Leave_Common_Approved, $record->statusLeaveTaken=> $lang_Leave_Common_Taken, $record->statusLeaveHoliday=> $lang_Leave_Holiday, $record->statusLeaveWeekend=> $lang_Leave_Common_Weekend);
   			$suprevisorRespArr = array($record->statusLeaveRejected => $lang_Leave_Common_Rejected, $record->statusLeaveApproved => $lang_Leave_Common_Approved, $record->statusLeaveCancelled => $lang_Leave_Common_Cancelled);
   			$employeeRespArr = array($record->statusLeaveCancelled => $lang_Leave_Common_Cancelled);
            $possibleStatusesArr = array();
            
			if ($modifier[0] === "MY") {
  				$possibleStatusesArr = $employeeRespArr;
  			} else if ($modifier[0] == "SUP" || $modifier[0] == "ADMIN") {
		  		$possibleStatusesArr = $suprevisorRespArr;

		  		if ($record->getLeaveStatus() == Leave::LEAVE_STATUS_LEAVE_TAKEN) {
		  			$possibleStatusesArr = array(Leave::LEAVE_STATUS_LEAVE_CANCELLED => $lang_Leave_Common_Cancelled);
		  		}
			}

    		if (($record->getLeaveStatus() == $record->statusLeavePendingApproval) || ($record->getLeaveStatus() ==  $record->statusLeaveApproved) || (($record->getLeaveStatus() ==  $record->statusLeaveRejected) && ($modifier[0] == "SUP" || $modifier[0] == "ADMIN")) ||
    			(($record->getLeaveStatus() ==  Leave::LEAVE_STATUS_LEAVE_TAKEN) && ($modifier[0] == "ADMIN"))) {
    	?>
    			<input type="hidden" name="id[]" value="<?php echo $record->getLeaveId(); ?>" />
    		<?php if (($record->getLeaveLengthHours() != null) && ($record->getLeaveLengthHours() != 0)) { ?>
    			<select name="cmbStatus[]">
  					<option value="<?php echo $record->getLeaveStatus();?>" selected="selected" ><?php echo $statusArr[$record->getLeaveStatus()]; ?></option>
  					<?php foreach($possibleStatusesArr as $key => $value) {
                                if ($key != $record->getLeaveStatus()) {
  					?>
  							<option value="<?php echo $key; ?>"><?php echo $value; ?></option>
  					<?php       }
                          }
  					?>
  				</select>
  			<?php } else {
  						$holiday = Holidays::getHolidayForDate($record->getLeaveDate());
  						if (!empty($holiday) && is_a($holiday, 'Holidays')) {
  							echo $holiday->getDescription();
  						} elseif ($record->getLeaveStatus() ==  Leave::LEAVE_STATUS_LEAVE_WEEKEND) {
                            echo $lang_Leave_Common_Weekend;
  						}
  			?>
  				<input type="hidden" name="cmbStatus[]" value="<?php echo $record->getLeaveStatus(); ?>" />
  			<?php }?>
    	<?php
    		} else {
    			if ($record->getLeaveStatus() ==  Leave::LEAVE_STATUS_LEAVE_WEEKEND) {
    			    echo $lang_Leave_Common_Weekend;
                } elseif($record->getLeaveStatus() == 0) {
                    echo $lang_Leave_Common_Cancelled;
                } else {
                    $holiday = new Holidays();
                    if($holiday->isHoliday($record->getLeaveDate())){
                        $holiday = Holidays::getHolidayForDate($record->getLeaveDate());
				if(isset($holiday)) {
                        		echo $holiday->getDescription();
				}
                    } else {
                    	if (isset($statusArr[$record->getLeaveStatus()])) {
                    		echo $statusArr[$record->getLeaveStatus()];
                    	}
                    }
    			}
    			?>
    			<input type="hidden" name="cmbStatus[]" value="<?php echo $record->getLeaveStatus(); ?>" />
    			<input type="hidden" name="id[]" value="<?php echo $record->getLeaveId(); ?>" />
    	<?php } ?>
    </td>
    <td class="<?php echo $cssClass; ?>"><?php
    		echo (($record->getLeaveLengthHours() == null) || ($record->getLeaveLengthHours() == 0))?"----":$record->getLeaveLengthHours();
    ?></td>
    <td class="<?php echo $cssClass; ?>">
		<?php if (($record->getLeaveStatus() == Leave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL) || ($record->getLeaveStatus() ==  Leave::LEAVE_STATUS_LEAVE_APPROVED) ||
	    (($record->getLeaveStatus() ==  Leave::LEAVE_STATUS_LEAVE_REJECTED) && ($modifier[0] == "SUP" || $modifier[0] == "ADMIN")) ||
	    (($record->getLeaveStatus() ==  Leave::LEAVE_STATUS_LEAVE_TAKEN) && ($modifier[0] == "ADMIN"))) { ?>


		<input type="text" name="txtComment[]" id="txtComment_<?php echo $idIndex++; ?>" value="<?php echo $record->getLeaveComments(); ?>" />
		<input type="hidden" name="txtEmployeeId[]" value="<?php echo $record->getEmployeeId(); ?>" />
		<?php } else if (($modifier[0] == "MY") || ($modifier[0] == "Taken")) {
			echo $record->getLeaveComments(); ?>
		<input type="hidden" name="txtEmployeeId[]" value="<?php echo $record->getEmployeeId(); ?>" />
		<input type="hidden" name="txtComment[]" id="txtComment_<?php echo $idIndex++; ?>" value="<?php echo $record->getLeaveComments(); ?>" />
		<?php } else {
			echo $record->getLeaveComments();
		?>
			<input type="hidden" name="txtEmployeeId[]" value="<?php echo $record->getEmployeeId(); ?>" />
			<input type="hidden" name="txtComment[]" id="txtComment_<?php echo $idIndex++; ?>" value="<?php echo $record->getLeaveComments(); ?>" />
		<?php } ?>
	</td>
  </tr>

<?php }
    }
?>
  </tbody>
</table>

</form>
</div>
<script type="text/javascript">
    <!--
    	noOfLeaveRecords = <?php echo $idIndex; ?>;

        if (document.getElementById && document.createElement) {
            roundBorder('outerbox');
        }
    -->
</script>
