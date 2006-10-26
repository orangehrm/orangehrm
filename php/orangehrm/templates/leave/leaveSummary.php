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
 $empInfo = $records[count($records)-1][0];
 
 array_pop($records);
 
 $auth = $modifier[1];
 $modifier = $modifier[0];

 $lan = new Language();
 
 if ($modifier === 'edit') {
 	$btnImage = '../../themes/beyondT/pictures/btn_save.jpg';
 	$frmAction = '?leavecode=Leave&action=Leave_Quota_Save';
 } else {
 	$btnImage = '../../themes/beyondT/pictures/btn_edit.jpg';
 	$frmAction = '?leavecode=Leave&action=Leave_Edit_Summary';
 }
 
 require_once($lan->getLangPath("leave/leaveCommon.php"));
 
 if ($empInfo[0] === $_SESSION['empID']) {
 	require_once($lan->getLangPath("leave/leaveSummaryEmployee.php"));
 } else {
 	require_once($lan->getLangPath("leave/leaveSummarySupervisor.php"));
 }
 
 if (isset($_GET['message'])) {
?>
<var><?php echo $_GET['message']; ?></var>
<?php } ?>
<script language="javascript">
	function actForm() {
		document.frmSummary.action = '<?php echo $frmAction; ?>';
		document.frmSummary.submit();
	}
</script>
<h3><?php echo $lang_Title.date('Y'); ?><hr/></h3>

<?php 
	if (!is_array($records)) { 
?>
	<h5>No records found!</h5>
<?php
	} else {
		if ($auth === 'admin') {
?>
	<form method="post" onsubmit="actForm(); return false;" name="frmSummary">
		<input type="hidden" name="id" value="<?php echo $empInfo[0]; ?>"/>
	<p class="controls">
		<input type="image" name="btnAct" src="<?php echo $btnImage; ?>" >
	</p>
<?php
		}
?>
<table border="0" cellpadding="0" cellspacing="0">
  <thead>
  	<tr>
		<th class="tableTopLeft"></th>    	
    	<th class="tableTopMiddle"></th>
    	<?php if ($auth === 'admin') { ?>
    	<th class="tableTopMiddle"></th>
    	<?php } ?>    	
    	<th class="tableTopMiddle"></th>
    	<th class="tableTopMiddle"></th>
		<th class="tableTopRight"></th>	
	</tr>
	<tr>
		<th class="tableMiddleLeft"></th>    	
    	<th width="180px" class="tableMiddleMiddle"><?php echo $lang_LeaveType;?></th>
    	<?php if ($auth === 'admin') { ?>
    	<th width="180px" class="tableMiddleMiddle"><?php echo $lang_LeaveEntitled;?></th>
    	<?php } ?>
    	<th width="180px" class="tableMiddleMiddle"><?php echo $lang_LeaveTaken;?></th>    	
    	<th width="180px" class="tableMiddleMiddle"><?php echo $lang_LeaveAvailable;?></th>
		<th class="tableMiddleRight"></th>	
	</tr>
  </thead>
  <tbody>
<?php
	$j = 0;	
	if (is_array($records[0]))
		foreach ($records[0] as $record) {
			if(!($j%2)) { 
				$cssClass = 'odd';
			 } else {
			 	$cssClass = 'even';
			 }
			 $j++;
?> 
  <tr>
  	<td class="tableMiddleLeft"></td>
    <td class="<?php echo $cssClass; ?>"><?php echo $record->getLeaveTypeName(); ?></td>
    <?php if (($auth === 'admin') && ($modifier === 'display')) { ?>
    <td class="<?php echo $cssClass; ?>"><?php echo $record->getNoOfDaysAllotted(); ?></td>    
    <?php } else if (($auth === 'admin') && ($modifier === 'edit')) {?>    
    <td class="<?php echo $cssClass; ?>">
    <input type="hidden" name="txtLeaveTypeId[]" value="<?php echo $record->getLeaveTypeId(); ?>"/>
       
    <input type="text" name="txtLeaveEntitled[]" value="<?php echo $record->getNoOfDaysAllotted(); ?>" size="3"/></td>
    <?php } ?>    
    <td class="<?php echo $cssClass; ?>"><?php echo $record->getLeaveTaken(); ?></td>    
    <td class="<?php echo $cssClass; ?>"><?php echo $record->getLeaveAvailable(); ?></td>
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
		<?php if ($auth === 'admin') { ?>
    	<th class="tableBottomMiddle"></th>
    	<?php } ?>
		<td class="tableBottomMiddle"></td>
		<td class="tableBottomMiddle"></td>
		<td class="tableBottomRight"></td>
	</tr>
  </tfoot>
</table>
<?php if ($auth === 'admin') { ?>
	</form>
<?php 
	 }
} 
?>