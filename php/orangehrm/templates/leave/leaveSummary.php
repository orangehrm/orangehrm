<?php
/**
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
 $empInfo = null;
 if (isset($records[count($records)-1][0])) {
 	$empInfo = $records[count($records)-1][0];
 }

 array_pop($records);

 $auth = $modifier[1];
 $dispYear = $modifier[2];

 $modifier = $modifier[0];

 $lan = new Language();

 if ($modifier === 'edit') {
 	$btnImage = '../../themes/beyondT/pictures/btn_save.jpg';
 	$btnImageMO = '../../themes/beyondT/pictures/btn_save_02.jpg';
 	$frmAction = '?leavecode=Leave&action=Leave_Quota_Save';
 } else {
 	$btnImage = '../../themes/beyondT/pictures/btn_edit.jpg';
 	$btnImageMO = '../../themes/beyondT/pictures/btn_edit_02.jpg';
 	$frmAction = '?leavecode=Leave&action=Leave_Edit_Summary';
 }

 $backLink = "./CentralController.php?leavecode=Leave&action=Leave_Select_Employee_Leave_Summary";

 require_once($lan->getLangPath("full.php"));

 if ($empInfo[0] == $_SESSION['empID']) {
 	$lang_Title = preg_replace(array('/#dispYear/'), array($dispYear), $lang_Leave_Leave_Summary_EMP_Title);
 } else {
 	if (isset($_REQUEST['id']) && ($_REQUEST['id'] != 0)) {
 		$employeeName = $empInfo[2].' '.$empInfo[1];
 	} else {
 		$employeeName = $lang_Leave_Common_AllEmployees;
 	}
 	$lang_Title = preg_replace(array('/#employeeName/', '/#dispYear/'), array($employeeName, $dispYear), $lang_Leave_Leave_Summary_SUP_Title);
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

	function goBack() {
	<?php if ($modifier === 'edit') { ?>
		document.frmSummary.reset();
		actForm();
	<?php } else { ?>
		location.href = '<?php echo $backLink; ?>';
	<?php } ?>
	}

<?php	if ($auth === 'admin') { ?>

	function actTakenLeave() {
		document.frmSummary.action = '?leavecode=Leave&action=Leave_List_Taken';
		document.frmSummary.submit();
	}


<?php	} ?>

</script>
<h2><?php echo $lang_Title; ?><hr/></h2>
<?php
	if (!is_array($records[0])) {
?>
	<img title="Back" onMouseOut="this.src='../../themes/beyondT/pictures/btn_back.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_back_02.jpg';"  src="../../themes/beyondT/pictures/btn_back.jpg" onClick="goBack();">
	<h5><?php echo $lang_Error_NoRecordsFound; ?></h5>
<?php
	} else {
		if ($auth === 'admin') {
?>
	<form method="post" onsubmit="actForm(); return false;" name="frmSummary" id="frmSummary">
		<input type="hidden" name="id" value="<?php echo isset($_REQUEST['id'])?$_REQUEST['id']:LeaveQuota::LEAVEQUOTA_CRITERIA_ALL; ?>"/>
		<input type="hidden" name="leaveTypeId" value="<?php echo isset($_REQUEST['leaveTypeId'])?$_REQUEST['leaveTypeId']:LeaveQuota::LEAVEQUOTA_CRITERIA_ALL; ?>" />
		<input type="hidden" name="year" value="<?php echo isset($_REQUEST['year'])?$_REQUEST['year']:date('Y'); ?>" />
		<input type="hidden" name="searchBy" value="<?php echo isset($_REQUEST['searchBy'])?$_REQUEST['searchBy']:"employee"; ?>"/>
	<p class="controls">
		<img title="Back" onMouseOut="this.src='../../themes/beyondT/pictures/btn_back.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_back_02.jpg';"  src="../../themes/beyondT/pictures/btn_back.jpg" onClick="goBack();">
		<input type="image" name="btnAct" src="<?php echo $btnImage; ?>" onMouseOut="this.src='<?php echo $btnImage; ?>';" onMouseOver="this.src='<?php echo $btnImageMO; ?>';">
	<?php if (isset($_REQUEST['id']) && ($_REQUEST['id'] != LeaveQuota::LEAVEQUOTA_CRITERIA_ALL)) {?>
		<a href="javascript:actTakenLeave()"><?php echo $lang_Leave_Common_ListOfTakenLeave; ?></a>
	<?php } ?>
	</p>
<?php
		}
?>
<table border="0" cellpadding="0" cellspacing="0">
  <thead>
  	<tr>
		<th class="tableTopLeft"></th>
		<?php if ((isset($_REQUEST['id']) && empty($_REQUEST['id'])) && (!isset($_SESSION['empID']) || (isset($_SESSION['empID']) && ($empInfo[0] != $_SESSION['empID'])))) { ?>
    	<th class="tableTopMiddle"></th>
    	<?php } ?>
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
		<?php if ((isset($_REQUEST['id']) && empty($_REQUEST['id'])) && (!isset($_SESSION['empID']) || (isset($_SESSION['empID']) && ($empInfo[0] != $_SESSION['empID'])))) { ?>
		<th width="180px" class="tableMiddleMiddle"><?php echo $lang_Leave_Common_EmployeeName;?></th>
		<?php } ?>
    	<th width="180px" class="tableMiddleMiddle"><?php echo $lang_Leave_Common_LeaveType;?></th>
    	<?php if ($auth === 'admin') { ?>
    	<th width="180px" class="tableMiddleMiddle"><?php echo $lang_Leave_Common_LeaveEntitled;?></th>
    	<?php } ?>
    	<th width="180px" class="tableMiddleMiddle"><?php echo $lang_Leave_Common_LeaveTaken;?></th>
    	<th width="180px" class="tableMiddleMiddle"><?php echo $lang_Leave_Common_LeaveAvailable;?></th>
		<th class="tableMiddleRight"></th>
	</tr>
  </thead>
  <tbody>
<?php
	$leaveTypeObj = new LeaveType();
	$j = 0;
	if (is_array($records[0]))
		foreach ($records[0] as $recordX) {
		  foreach ($recordX as $record) {
			if(!($j%2)) {
				$cssClass = 'odd';
			 } else {
			 	$cssClass = 'even';
			 }
			 $j++;
?>
  <tr>
  	<td class="tableMiddleLeft"></td>
   	<?php if ((isset($_REQUEST['id']) && empty($_REQUEST['id'])) && (!isset($_SESSION['empID']) || (isset($_SESSION['empID']) && ($empInfo[0] != $_SESSION['empID'])))) { ?>
  	<td class="<?php echo $cssClass; ?>"><?php echo $record['employee_name'] ?></td>
  	<?php } ?>
    <td class="<?php echo $cssClass; ?>"><?php echo $record['leave_type_name'] ?></td>
    <?php if (($auth === 'admin') && ($modifier === 'display')) { ?>
    <td class="<?php echo $cssClass; ?>"><?php echo $record['no_of_days_allotted']; ?></td>
    <?php } else if (($auth === 'admin') && ($modifier === 'edit')) {

    				$readOnly = "readonly";
    				if ($record['available_flag'] == $leaveTypeObj->availableStatusFlag) {
    					$readOnly = "";
    				}
    ?>
    <td class="<?php echo $cssClass; ?>">
    <input type="hidden" name="txtLeaveTypeId[]" value="<?php echo $record['leave_type_id']; ?>"/>
     <input type="hidden" name="txtEmployeeId[]" value="<?php echo $record['emp_number']; ?>"/>

    <input type="text" name="txtLeaveEntitled[]" value="<?php echo $record['no_of_days_allotted']; ?>" size="3" <?php echo $readOnly; ?>/></td>
    <?php } ?>
    <td class="<?php echo $cssClass; ?>"><?php echo $record['leave_taken']; ?></td>
    <td class="<?php echo $cssClass; ?>"><?php echo $record['leave_available']; ?></td>
	<td class="tableMiddleRight"></td>
  </tr>
<?php 	  }
		}
?>
  </tbody>
  <tfoot>
  	<tr>
		<td class="tableBottomLeft"></td>
		<?php if ((isset($_REQUEST['id']) && empty($_REQUEST['id'])) && (!isset($_SESSION['empID']) || (isset($_SESSION['empID']) && ($empInfo[0] != $_SESSION['empID'])))) { ?>
		<td class="tableBottomMiddle"></td>
		<?php } ?>
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