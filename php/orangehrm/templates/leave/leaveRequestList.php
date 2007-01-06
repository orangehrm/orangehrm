<?php
/*
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures 
 * all the essential functionalities required for any enterprise. 
 * Copyright (C) 2006 hSenid Software, http://www.hsenid.com
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

/*
 *	Including the language pack
 *
 **/
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
 $lan = new Language(); 
 
 require_once($lan->getLangPath("full.php")); 
 
if ($modifier === "SUP") {
 $lang_Title = $lang_Leave_Leave_list_Title1;
} else if ($modifier === "Taken") {
 $lang_Title = preg_replace(array('/#employeeName/', '/#dispYear/'), array($employeeName, $dispYear) , $lang_Leave_Leave_list_Title2);	
} else {
 $lang_Title = $lang_Leave_Leave_list_Title3;	
}
 
 if ($modifier === "SUP") {
 	$action = "Leave_ChangeStatus";
 } else {
 	$action = "Leave_CancelLeave";
 }
 
 if (isset($_GET['message'])) {
?>
<var><?php echo $_GET['message']; ?></var>
<?php } ?>
<h2><?php echo $lang_Title?><hr/></h2>
<?php 
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
    	<?php if ($modifier == "SUP") { ?>
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
    	<th width="175px" class="tableMiddleMiddle"><?php echo $lang_Leave_Common_Date;?></th>
    	<?php if ($modifier == "SUP") { ?>
    	<th width="140px" class="tableMiddleMiddle"><?php echo $lang_Leave_Common_EmployeeName;?></th>
    	<?php } ?>
    	<th width="50px" class="tableMiddleMiddle">No of Days</th>
    	<th width="90px" class="tableMiddleMiddle"><?php echo $lang_Leave_Common_LeaveType;?></th>
    	<th width="150px" class="tableMiddleMiddle"><?php echo $lang_Leave_Common_Status;?></th>
    	<th width="100px" class="tableMiddleMiddle">Leave Period</th>
    	<th width="150px" class="tableMiddleMiddle"><?php echo $lang_Leave_Common_Comments;?></th>
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
			 
			 $dateStr = $record->getLeaveFromDate();
			 
			 $toDate = $record->getLeaveToDate();			  

			 if (!empty($toDate)) {
			 	$dateStr .=	" -> ".$toDate;
			 }
			 
			 
?> 
  <tr>
  	<td class="tableMiddleLeft"></td>
    <td class="<?php echo $cssClass; ?>"><?php echo  $dateStr; ?></td>
    <?php if ($modifier == "SUP") { ?>
    <td class="<?php echo $cssClass; ?>"><?php echo $record->getEmployeeName(); ?></td>
    <?php } ?>
    <td class="<?php echo $cssClass; ?>"><?php echo $record->getNoDays(); ?></td>
    <td class="<?php echo $cssClass; ?>"><?php echo $record->getLeaveTypeName(); ?></td>
    <td class="<?php echo $cssClass; ?>"><?php 
   			$statusArr = array($record->statusLeaveRejected => $lang_Leave_Common_Rejected, $record->statusLeaveCancelled => $lang_Leave_Common_Cancelled, $record->statusLeavePendingApproval => $lang_Leave_Common_PendingApproval, $record->statusLeaveApproved => $lang_Leave_Common_Approved, $record->statusLeaveTaken=> $lang_Leave_Common_Taken, LeaveRequests::LEAVEREQUESTS_MULTIPLESTATUSES => "Status Differ");
   			$suprevisorRespArr = array($record->statusLeaveRejected => $lang_Leave_Common_Rejected, $record->statusLeaveApproved => $lang_Leave_Common_Approved);
   			$employeeRespArr = array($record->statusLeaveCancelled => $lang_Leave_Common_Cancelled);
   			    		
    		if (($record->getLeaveStatus() == $record->statusLeavePendingApproval) || ($record->getLeaveStatus() ==  $record->statusLeaveApproved) || (($record->getLeaveStatus() ==  $record->statusLeaveRejected) && ($modifier == "SUP"))) {
    	?>
    			<input type="hidden" name="id[]" value="<?php echo $record->getLeaveRequestId(); ?>" />
    			<select name="cmbStatus[]">
  					<option value="<?php echo $record->getLeaveStatus();?>" selected="selected" ><?php echo $statusArr[$record->getLeaveStatus()]; ?></option>
  					<?php if ($modifier == null) { 
  							foreach($employeeRespArr as $key => $value) {
  								if ($key != $record->getLeaveStatus()) {
  					?>
  							<option value="<?php echo $key; ?>"><?php echo $value; ?></option>
  					<?php 		}
  							}
  						} else if ($modifier == "SUP") { 
		  					foreach($suprevisorRespArr as $key => $value) {	
		  						if ($key != $record->getLeaveStatus()) {
  					?>
  							<option value="<?php echo $key; ?>"><?php echo $value; ?></option>
  					<?php 		}
		  					}
  						}
  					?>
  				</select>
    	<?php		
    		} else {
    			echo $statusArr[$record->getLeaveStatus()];
    		}
   			
    		
    		?></td>
    <td class="<?php echo $cssClass; ?>"><?php 
    		$leaveLength = null;
    		switch ($record->getLeaveLength()) { 
    			case $record->lengthFullDay 		 :	$leaveLength = $lang_Leave_Common_FullDay;
    													break; 
    			case $record->lengthHalfDayMorning	 :	$leaveLength = $lang_Leave_Common_HalfDayMorning;
    													break;
				case $record->lengthHalfDayAfternoon :	$leaveLength = $lang_Leave_Common_HalfDayAfternoon;
    													break;  
				case LeaveRequests::LEAVEREQUESTS_LEAVELENGTH_RANGE : $leaveLength = $lang_Leave_Common_Range;
    																  break;
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
	<?php } else if (($record->getLeaveStatus() == $record->statusLeavePendingApproval) || ($record->getLeaveStatus() ==  $record->statusLeaveApproved) || (($record->getLeaveStatus() ==  $record->statusLeaveRejected) && ($modifier == "SUP"))) { ?>
		<input type="text" <?php echo $inputType; ?> name="txtComment[]" value="<?php echo $record->getLeaveComments(); ?>" />
		<input type="hidden" name="txtEmployeeId[]" value="<?php echo $record->getEmployeeId(); ?>" />		
		<?php } else { ?>
		<input type="text" <?php echo $inputType; ?> name="txtComment[]" value="<?php echo $record->getLeaveComments(); ?>" />
		<?php } ?></td>
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
		<?php if ($modifier == "SUP") { ?>
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
<input type="image" name="Save" class="save" src="../../themes/beyondT/pictures/btn_save.jpg"/>
</p>
</form>
<?php   }
	 } ?>