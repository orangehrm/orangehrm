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
 
 $lan = new Language();
 
 require_once($lan->getLangPath("leave/leaveCommon.php")); 
 require_once($lan->getLangPath("leave/leaveList.php")); 
 
 if ($modifier === "SUP") {
 	$action = "Leave_ChangeStatus";
 } else {
 	$action = "Leave_CancelLeave";
 }
 
 if (isset($_GET['message'])) {
?>
<var><?php echo $_GET['message']; ?></var>
<?php } ?>
<h3><?php echo $lang_Title?><hr/></h3>
<?php 
	if (!is_array($records)) { 
?>
	<h5>No records found!</h5>
<?php
	}
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
		<th class="tableTopRight"></th>	
	</tr>
	<tr>
		<th class="tableMiddleLeft"></th>	
    	<th width="75px" class="tableMiddleMiddle"><?php echo $lang_Date;?></th>
    	<?php if ($modifier == "SUP") { ?>
    	<th width="150px" class="tableMiddleMiddle"><?php echo $lang_EmployeeName;?></th>
    	<?php } ?>
    	<th width="90px" class="tableMiddleMiddle"><?php echo $lang_LeaveType;?></th>
    	<th width="150px" class="tableMiddleMiddle"><?php echo $lang_Status;?></th>
    	<th width="100px" class="tableMiddleMiddle"><?php echo $lang_Length;?></th>
    	<th width="150px" class="tableMiddleMiddle"><?php echo $lang_Comments;?></th>
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
?> 
  <tr>
  	<td class="tableMiddleLeft"></td>
    <td class="<?php echo $cssClass; ?>"><?php echo $record->getLeaveDate(); ?></td>
    <?php if ($modifier == "SUP") { ?>
    <td class="<?php echo $cssClass; ?>"><?php echo $record->getEmployeeName(); ?></td>
    <?php } ?>
    <td class="<?php echo $cssClass; ?>"><?php echo $record->getLeaveTypeName(); ?></td>
    <td class="<?php echo $cssClass; ?>"><?php 
   			$statusArr = array($record->statusLeaveRejected => $lang_Rejected, $record->statusLeaveCancelled => $lang_Cancelled, $record->statusLeavePendingApproval => $lang_PendingApproval, $record->statusLeaveApproved => $lang_Approved, $record->statusLeaveTaken=> $lang_Taken);
   			$suprevisorRespArr = array($record->statusLeaveRejected => $lang_Rejected, $record->statusLeaveApproved => $lang_Approved);
   			$employeeRespArr = array($record->statusLeaveCancelled => $lang_Cancelled);
   			//sort($statusArr);
   			    		
    		if (($record->getLeaveStatus() == $record->statusLeavePendingApproval) || ($record->getLeaveStatus() ==  $record->statusLeaveApproved) || (($record->getLeaveStatus() ==  $record->statusLeaveRejected) && ($modifier == "SUP"))) {
    	?>
    			<input type="hidden" name="id[]" value="<?php echo $record->getLeaveId(); ?>" />
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
    			case $record->lengthFullDay :	$leaveLength = $lang_FullDay;
    											break; 
    			case $record->lengthHalfDay:	$leaveLength = $lang_HalfDay;
    											break; 	
    		}
    		
    		echo $leaveLength;			
    ?></td>
    <td class="<?php echo $cssClass; ?>">	
	<?php if ($modifier == null) { 
			echo $record->getLeaveComments(); ?>
		<input type="hidden" name="txtComment[]" value="<?php echo $record->getLeaveComments(); ?>" />			
	<?php } else if ($modifier == "SUP") { ?>
		<input type="text" name="txtComment[]" value="<?php echo $record->getLeaveComments(); ?>" />
		<input type="hidden" name="txtEmployeeId[]" value="<?php echo $record->getEmployeeId(); ?>" />		
		<?php } ?>	</td>
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
		<td class="tableBottomRight"></td>
	</tr>
  </tfoot>
</table>
<p id="controls">
<input type="image" name="Save" class="save" src="../../themes/beyondT/pictures/btn_save.jpg"/>
</p>
</form>