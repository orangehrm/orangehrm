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
<table border="0" cellpadding="0" cellspacing="0">
  <thead>
  	<tr>
		<th class="tableTopLeft"></th>	
    	<th class="tableTopMiddle"></th>
    	<th class="tableTopMiddle"></th>
    	<th class="tableTopMiddle"></th>
    	<th class="tableTopMiddle"></th>
    	<th class="tableTopMiddle"></th>
		<th class="tableTopRight"></th>	
	</tr>
	<tr>
		<th class="tableMiddleLeft"></th>	
    	<th class="tableMiddleMiddle"><?php echo $lang_Date;?></th>
    	<th class="tableMiddleMiddle"><?php echo $lang_LeaveType;?></th>
    	<th class="tableMiddleMiddle"><?php echo $lang_Status;?></th>
    	<th class="tableMiddleMiddle"><?php echo $lang_Length;?></th>
    	<th class="tableMiddleMiddle"><?php echo $lang_Comments;?></th>
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
    <td width="100px" class="<?php echo $cssClass; ?>"><?php echo $record->getLeaveDate(); ?></td>
    <td width="100px" class="<?php echo $cssClass; ?>"><?php echo $record->getLeaveTypeName(); ?></td>
    <td width="200px" class="<?php echo $cssClass; ?>"><?php 
   			$statusArr = array($record->statusLeaveRejected => $lang_Rejected, $record->statusLeaveCancelled => $lang_Cancelled, $record->statusLeavePendingApproval => $lang_PendingApproval, $record->statusLeaveApproved => $lang_Approved, $record->statusLeaveTaken=> $lang_Taken);
   			
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
    <td width="200px" class="<?php echo $cssClass; ?>"><?php 
    		$leaveLength = null;
    		switch ($record->getLeaveLength()) { 
    			case $record->lenthFullDay :	$leaveLength = $lang_FullDay;
    											break; 
    			case $record->lengthHalfDay:	$leaveLength = $lang_HalfDay;
    											break; 	
    		}
    		
    		echo $leaveLength;			
    ?></td>
    <td width="200px" class="<?php echo $cssClass; ?>"><?php echo $record->getLeaveComments(); ?></td>
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