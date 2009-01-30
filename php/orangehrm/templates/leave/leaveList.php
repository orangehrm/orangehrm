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

 $modifier = $modifier[0];

 if (isset($modifier) && ($modifier == "Taken")) {

 	$empInfo = $records[count($records)-1][0];
 	$employeeName = $empInfo[2].' '.$empInfo[1];

 	array_pop($records);

 	$records = $records[0];
 }

if ($modifier === "SUP") {
 	$employeeName = $records[0]->getEmployeeName();
	$lang_Title = preg_replace('/#employeeName/', $employeeName, $lang_Leave_Leave_Requestlist_Title1);
} else if ($modifier === "ADMIN") {
 	$employeeName = $records[0]->getEmployeeName();
	$lang_Title = preg_replace('/#employeeName/', $employeeName, $lang_Leave_Leave_Requestlist_Title2);
} else if ($modifier === "Taken") {
 $lang_Title = preg_replace(array('/#employeeName/', '/#dispYear/'), array($employeeName, $dispYear) , $lang_Leave_Leave_list_Title2);
} else if ($modifier === "MY") {
	$lang_Title = $lang_Leave_Leave_list_TitleMyLeaveList;
} else {
 $lang_Title = $lang_Leave_Leave_list_Title3;
}

 if ($modifier === "SUP") {
 	$action = "Leave_ChangeStatus";
 	$backLink = "Leave_FetchLeaveSupervisor";
 } else if ($modifier === "ADMIN") {
 	$action = "Leave_ChangeStatus";
 	$backLink = "Leave_FetchLeaveAdmin";
 } else {
 	$action = "Leave_CancelLeave";
 	$backLink = "Leave_FetchLeaveEmployee";
 }

?>

<script type="text/javascript">
//<![CDATA[
	function goBack () {
		<?php if ($modifier == "ADMIN") { ?>
			window.location = "?leavecode=Leave&action=Leave_FetchLeaveAdmin";
		<?php
	    } else if ($modifier == "Taken") {
		?>
			history.back();
		<?php } else { ?>
			window.location = "?leavecode=Leave&action=<?php echo $backLink; ?>";
		<?php } ?>
	}
//]]>
</script>
<br class="clear" /><br class="clear" />
<div class="outerbox">
<form id="frmCancelLeave" name="frmCancelLeave" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?leavecode=Leave&amp;action=<?php echo $action; ?>">
    <div class="mainHeading"><h2><?php echo $lang_Title; ?></h2></div>

    <?php if (isset($_GET['message']) && $_GET['message'] != 'xx') {
            $message =  $_GET['message'];
            $messageType = CommonFunctions::getCssClassForMessage($message);
            $messageStr = "lang_Leave_" . $message;
    ?>
        <div class="messagebar">
            <span class="<?php echo $messageType; ?>"><?php echo (isset($$messageStr)) ? $$messageStr: ''; ?></span>
        </div>
    <?php } ?>


    <div class="actionbar">
        <div class="actionbuttons">

<?php   if ((is_array($records)) && ($modifier !== "Taken")) { ?>

        <input type="image" name="Save" class="save" src="../../themes/beyondT/pictures/btn_save.gif"/>

            <input type="submit" class="savebutton" name="Save"
                onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                value="<?php echo $lang_Common_Save;?>" title="<?php echo $lang_Common_Save;?>"/>
        <?php
            }
        ?>
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
   			$statusArr = array($record->statusLeaveRejected => $lang_Leave_Common_Rejected, $record->statusLeaveCancelled => $lang_Leave_Common_Cancelled, $record->statusLeavePendingApproval => $lang_Leave_Common_PendingApproval, $record->statusLeaveApproved => $lang_Leave_Common_Approved, $record->statusLeaveTaken=> $lang_Leave_Common_Taken);
   			$suprevisorRespArr = array($record->statusLeaveRejected => $lang_Leave_Common_Rejected, $record->statusLeaveApproved => $lang_Leave_Common_Approved, $record->statusLeaveCancelled => $lang_Leave_Common_Cancelled);
   			$employeeRespArr = array($record->statusLeaveCancelled => $lang_Leave_Common_Cancelled);

			if ($modifier === "MY") {
  				$possibleStatusesArr = $employeeRespArr;
  			} else if ($modifier == "SUP" || $modifier == "ADMIN") {
		  		$possibleStatusesArr = $suprevisorRespArr;

		  		if ($record->getLeaveStatus() == Leave::LEAVE_STATUS_LEAVE_TAKEN) {
		  			$possibleStatusesArr = array(Leave::LEAVE_STATUS_LEAVE_CANCELLED => $lang_Leave_Common_Cancelled);
		  		}
			}

    		if (($record->getLeaveStatus() == $record->statusLeavePendingApproval) || ($record->getLeaveStatus() ==  $record->statusLeaveApproved) || (($record->getLeaveStatus() ==  $record->statusLeaveRejected) && ($modifier == "SUP" || $modifier == "ADMIN")) ||
    			(($record->getLeaveStatus() ==  Leave::LEAVE_STATUS_LEAVE_TAKEN) && ($modifier == "ADMIN"))) {
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
  						} else {
  							echo $lang_Leave_Closed;
  						}
  			?>
  				<input type="hidden" name="cmbStatus[]" value="<?php echo $record->getLeaveStatus(); ?>" />
  			<?php }?>
    	<?php
    		} else {
    			if (Weekends::isWeekend($record->getLeaveDate())) {
    			    echo $lang_Leave_Closed;
    			} else {
    				echo $statusArr[$record->getLeaveStatus()];
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
	    (($record->getLeaveStatus() ==  Leave::LEAVE_STATUS_LEAVE_REJECTED) && ($modifier == "SUP" || $modifier == "ADMIN")) ||
	    (($record->getLeaveStatus() ==  Leave::LEAVE_STATUS_LEAVE_TAKEN) && ($modifier == "ADMIN"))) { ?>


		<input type="text" name="txtComment[]" value="<?php echo $record->getLeaveComments(); ?>" />
		<input type="hidden" name="txtEmployeeId[]" value="<?php echo $record->getEmployeeId(); ?>" />
		<?php } else if (($modifier == "MY") || ($modifier == "Taken")) {
			echo $record->getLeaveComments(); ?>
		<input type="hidden" name="txtEmployeeId[]" value="<?php echo $record->getEmployeeId(); ?>" />
		<input type="hidden" name="txtComment[]" value="<?php echo $record->getLeaveComments(); ?>" />
		<?php } else {
			echo $record->getLeaveComments();
		?>
			<input type="hidden" name="txtEmployeeId[]" value="<?php echo $record->getEmployeeId(); ?>" />
			<input type="hidden" name="txtComment[]" value="<?php echo $record->getLeaveComments(); ?>" />
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
        if (document.getElementById && document.createElement) {
            roundBorder('outerbox');
        }
    -->
</script>