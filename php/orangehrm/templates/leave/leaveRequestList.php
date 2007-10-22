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
if ($modifier === "ADMIN") {
	$lang_Title = $lang_Leave_Leave_list_Title4;
} else if ($modifier === "SUP") {
	$lang_Title = $lang_Leave_Leave_list_Title1;
} else if ($modifier === "Taken") {
	$lang_Title = preg_replace(array('/#employeeName/', '/#dispYear/'), array($employeeName, $dispYear) , $lang_Leave_Leave_list_Title2);
} else {
	$lang_Title = $lang_Leave_Leave_list_Title3;
}

 if ($modifier === "SUP" || $modifier === "ADMIN") {
 	$action = "Leave_Request_ChangeStatus";
 	$detailAction = "Leave_FetchDetailsSupervisor";
 } else {
 	$action = "Leave_Request_CancelLeave";
 	$detailAction = "Leave_FetchDetailsEmployee";
 }
?>
<h2><?php echo $lang_Title?><hr/></h2>
<?php if (isset($_GET['message']) && $_GET['message'] != 'xx') {

	$expString  = $_GET['message'];
	$expString = explode ("_",$expString);
	$length = count($expString);

	$col_def=strtolower($expString[$length-1]);

	$expString='lang_Leave_'.$_GET['message'];
	if (isset($$expString)) {
?>
	<font class="<?php echo $col_def?>" size="-1" face="Verdana, Arial, Helvetica, sans-serif">
<?php echo $$expString; ?>
	</font>
<?php
	}
}
	if (!is_array($records)) {
?>
	<h5><?php echo $lang_Error_NoRecordsFound; ?></h5>
<?php
	} else {
?>
<form id="frmCancelLeave" name="frmCancelLeave" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?leavecode=Leave&action=<?php echo $action; ?>">

<table border="0" cellpadding="0" cellspacing="0">
  <thead>
  	<tr>
		<th class="tableTopLeft"></th>
    	<th class="tableTopMiddle"></th>
    	<?php if ($modifier == "SUP" || $modifier == "ADMIN") { ?>
    	<th class="tableTopMiddle"></th>
    	<?php } ?>
    	<th class="tableTopMiddle"></th>
    	<th class="tableTopMiddle"></th>
    	<th class="tableTopMiddle"></th>
    	<th class="tableTopMiddle"></th>
    	<th class="tableTopMiddle"></th>
		<th class="tableTopRight"></th>
	</tr>
	<tr>
		<th class="tableMiddleLeft"></th>
    	<th width="100px" class="tableMiddleMiddle"><?php echo $lang_Leave_Common_Date;?></th>
    	<?php if ($modifier == "SUP" || $modifier == "ADMIN") { ?>
    	<th width="200px" class="tableMiddleMiddle"><?php echo $lang_Leave_Common_EmployeeName;?></th>
    	<?php } ?>
    	<th width="50px" class="tableMiddleMiddle"><?php echo $lang_Leave_NoOfDays;?></th>
    	<th width="90px" class="tableMiddleMiddle"><?php echo $lang_Leave_Common_LeaveType;?></th>
    	<th width="100px" class="tableMiddleMiddle"><?php echo $lang_Leave_Common_Status;?></th>
    	<th width="100px" class="tableMiddleMiddle"><?php echo $lang_Leave_Period;?></th>
    	<th width="100px" class="tableMiddleMiddle"><?php echo $lang_Leave_Common_Comments;?></th>
		<th class="tableMiddleRight"></th>
	</tr>
  </thead>
  <tbody>
<?php
	$j = 0;
	if (is_array($records))
		foreach ($records as $record) {
			if(!($j%2)) {
				$cssClass = 'odd';
			 } else {
			 	$cssClass = 'even';
			 }
			 $j++;

			 $dateStr = LocaleUtil::getInstance()->formatDate($record->getLeaveFromDate());
			 $toDate = LocaleUtil::getInstance()->formatDate($record->getLeaveToDate());

			 if (!empty($toDate)) {
			 	$dateStr .=	" -> ".$toDate;
			 }
?>
  <tr>
  	<td class="tableMiddleLeft"></td>
    <td class="<?php echo $cssClass; ?>"><a href="?leavecode=Leave&action=<?php echo ($modifier == "ADMIN")?"Leave_FetchDetailsAdmin":$detailAction; ?>&id=<?php echo $record->getLeaveRequestId(); ?>&digest=<?php echo md5($record->getLeaveRequestId().SALT); ?>"><?php echo $dateStr; ?></a></td>
    <?php if ($modifier == "SUP" || $modifier == "ADMIN") { ?>
    <td class="<?php echo $cssClass; ?>"><?php echo $record->getEmployeeName(); ?></td>
    <?php } ?>
    <td class="<?php echo $cssClass; ?>"><?php echo round($record->getNoDays(),2); ?></td>
    <td class="<?php echo $cssClass; ?>"><?php echo $record->getLeaveTypeName(); ?></td>
    <td class="<?php echo $cssClass; ?>"><?php
   			$statusArr = array($record->statusLeaveRejected => $lang_Leave_Common_Rejected, $record->statusLeaveCancelled => $lang_Leave_Common_Cancelled, $record->statusLeavePendingApproval => $lang_Leave_Common_PendingApproval, $record->statusLeaveApproved => $lang_Leave_Common_Approved, $record->statusLeaveTaken=> $lang_Leave_Common_Taken, LeaveRequests::LEAVEREQUESTS_MULTIPLESTATUSES => $lang_Leave_Common_StatusDiffer);
   			$suprevisorRespArr = array($record->statusLeaveRejected => $lang_Leave_Common_Rejected, $record->statusLeaveApproved => $lang_Leave_Common_Approved, $record->statusLeaveCancelled => $lang_Leave_Common_Cancelled);
   			$employeeRespArr = array($record->statusLeaveCancelled => $lang_Leave_Common_Cancelled);

    		if (($record->getLeaveStatus() == $record->statusLeavePendingApproval) || ($record->getLeaveStatus() ==  $record->statusLeaveApproved) || (($record->getLeaveStatus() ==  $record->statusLeaveRejected) && ($modifier == "SUP" || $modifier == "ADMIN"))) {
    	?>
    			<input type="hidden" name="id[]" value="<?php echo $record->getLeaveRequestId(); ?>" />
    			<?php if (($record->getLeaveLengthHours() != null) || ($record->getLeaveLengthHours() != 0)) { ?>
    			<select name="cmbStatus[]">
  					<option value="<?php echo $record->getLeaveStatus();?>" selected="selected" ><?php echo $statusArr[$record->getLeaveStatus()]; ?></option>
  					<?php if ($modifier == null) {
  							foreach($employeeRespArr as $key => $value) {
  								if ($key != $record->getLeaveStatus()) {
  					?>
  							<option value="<?php echo $key; ?>"><?php echo $value; ?></option>
  					<?php 		}
  							}
  						} else if ($modifier == "SUP" || $modifier == "ADMIN") {
		  					foreach($suprevisorRespArr as $key => $value) {
		  						if ($key != $record->getLeaveStatus()) {
  					?>
  							<option value="<?php echo $key; ?>"><?php echo $value; ?></option>
  					<?php 		}
		  					}
  						}
  					?>
  				</select>
  				<?php } else { ?>
  					<?php echo $lang_Leave_Holiday; ?> <input type="hidden" name="cmbStatus[]" value="<?php echo $record->getLeaveStatus(); ?>" />
  				<?php }?>
    	<?php
    		} else if ($record->getLeaveStatus() != null) {
     			echo $statusArr[$record->getLeaveStatus()];
    		}


    		?></td>
    <td class="<?php echo $cssClass; ?>"><?php
    		$leaveLength = null;
    		if ($record->getLeaveLengthHours() == LeaveRequests::LEAVEREQUESTS_LEAVELENGTH_RANGE) {
    			$leaveLength = $lang_Leave_Common_Range;
    		} else if (($record->getStartTime() != null) && ($record->getEndTime() != null)) {
    			$leaveLength = "{$record->getStartTime()} - {$record->getEndTime()}";
    		} else if ($record->getLeaveLengthHours() != null) {
    			$leaveLength = "{$record->getLeaveLengthHours()} {$lang_Common_Hours}";
    		} else {
    			$leaveLength = '----';
    		}

    		echo $leaveLength;
    ?></td>
    <td class="<?php echo $cssClass; ?>">
	<?php
		if ($record->getCommentsDiffer()) {
			$inputType = "readonly";
		} else {
			$inputType = "";
		}

		if (($modifier != null) && ($modifier == "Taken")) {
			echo $record->getLeaveComments(); ?>
		<input type="hidden" <?php echo $inputType; ?> name="txtComment[]" value="<?php echo $record->getLeaveComments(); ?>" />
	<?php } else if (($record->getLeaveStatus() == $record->statusLeavePendingApproval) || ($record->getLeaveStatus() ==  $record->statusLeaveApproved) || (($record->getLeaveStatus() ==  $record->statusLeaveRejected) && ($modifier == "SUP" || $modifier == "ADMIN"))) { ?>
		<input type="text" <?php echo $inputType; ?> name="txtComment[]" value="<?php echo $record->getLeaveComments(); ?>" />
		<input type="hidden" name="txtEmployeeId[]" value="<?php echo $record->getEmployeeId(); ?>" />
		<?php } else if (($record->getLeaveStatus() == $record->statusLeavePendingApproval) || ($record->getLeaveStatus() ==  $record->statusLeaveApproved)) { ?>
		<input type="text" <?php echo $inputType; ?> name="txtComment[]" value="<?php echo $record->getLeaveComments(); ?>" />
		<?php } else {
			echo $record->getLeaveComments();
			} ?></td>
	<td class="tableMiddleRight"></td>
  </tr>

<?php
		}
?>
  </tbody>
  <tfoot>
  	<tr>
		<td class="tableBottomLeft"></td>
		<td class="tableBottomMiddle"></td>
		<?php if ($modifier == "SUP" || $modifier == "ADMIN") { ?>
    	<td class="tableBottomMiddle"></td>
    	<?php } ?>
		<td class="tableBottomMiddle"></td>
		<td class="tableBottomMiddle"></td>
		<td class="tableBottomMiddle"></td>
		<td class="tableBottomMiddle"></td>
		<td class="tableBottomMiddle"></td>
		<td class="tableBottomRight"></td>
	</tr>
  </tfoot>
</table>
<?php 	if ($modifier !== "Taken") { ?>
<p id="controls">
<input type="image" name="Save" class="save" src="../../themes/beyondT/pictures/btn_save.gif"/>
</p>
</form>
<?php   }
	 } ?>
