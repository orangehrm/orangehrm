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
 
 require_once($lan->getLangPath("leave/leaveList.php")); 
 if (isset($_GET['message'])) {
?>
<var><?php echo $_GET['message']; ?></var>
<?php } ?>
<h3><?php echo $lang_Title?></h3>
<?php 
	if (!is_array($records)) { 
?>
	<h5>No records found!</h5>
<?php
	}
?>
<form id="frmCancelLeave" name="frmCancelLeave" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?leavecode=Leave&action=Leave_CancelLeave">
<table border="1" cellpadding="2" cellspacing="0">
  <thead>
  	<tr>
    	<th><?php echo $lang_Date;?></th>
    	<th><?php echo $lang_LeaveType;?></th>
    	<th><?php echo $lang_Status;?></th>
    	<th><?php echo $lang_Length;?></th>
    	<th><?php echo $lang_Comments;?></th>
	</tr>
  </thead>
  <tbody>
<?php
	if (is_array($records))
		foreach ($records as $record) {
?> 
  <tr>
    <td><?php echo $record->getLeaveDate(); ?></td>
    <td><?php echo $record->getLeaveTypeName(); ?></td>
    <td><?php 
   			$statusArr = array($record->statusLeaveCancelled => $lang_Cancelled, $record->statusLeavePendingApproval => $lang_PendingApproval, $record->statusLeaveApproved => $lang_Approved, $record->statusLeaveTaken=> $lang_Taken);
   			
   			//sort($statusArr);
   			    		
    		if (($record->getLeaveStatus() == 1) || ($record->getLeaveStatus() == 2)) {
    	?>
    			<input type="hidden" name="id[]" value="<?php echo $record->getLeaveId(); ?>" />
    			<select name="cmbStatus[]">
  					<option value="<?php echo $record->getLeaveStatus();?>" selected="selected" ><?php echo $statusArr[$record->getLeaveStatus()]; ?></option>
  					<option value="0">Cancel</option>
  				</select>
    	<?php		
    		} else {
    			echo $statusArr[$record->getLeaveStatus()];
    		}
    		
    		?></td>
    <td><?php echo $record->getLeaveLength(); ?></td>
    <td><?php echo $record->getLeaveComments(); ?></td>
  </tr>

<?php 	
		}
?>	
  </tbody>
</table>

<input type="submit" name="Save" value="Save" />
</form>