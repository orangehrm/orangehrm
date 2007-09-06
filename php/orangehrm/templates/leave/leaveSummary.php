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

 $empInfo = null;
 if (isset($records[count($records)-1][0])) {
 	$empInfo = $records[count($records)-1][0];
 }

 array_pop($records);

 $deletedLeaveTypesFound = false;
 $auth = $modifier[1];
 $dispYear = $modifier[2];

 $copyQuota = $modifier[3];


 $modifier = $modifier[0];

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

 /* Add sort parameters to form action url */
 $sortBy =  isset($_REQUEST['sortField'])?$_REQUEST['sortField']:null;

 $sortOrder = null;
 if ($sortBy != null) {

 	$sortParam = "sortOrder" . $sortBy;
 	if (isset($_REQUEST[$sortParam])) {
 		$sortOrder =  $_REQUEST[$sortParam];
 	}
 }

 if ($sortBy != null && $sortOrder != null) {
 	$frmAction .= "&sortField=${sortBy}&sortOrder${sortBy}=${sortOrder}";
 }


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

	function getCurSortOrder($colNum) {

		$curSortOrder = null;

		$varName = "sortOrder${colNum}";
		if (isset($_REQUEST[$varName])) {
			$curSortOrder = $_REQUEST[$varName];
		}
		return $curSortOrder;
	}

	function getNextSortOrder($colNum) {

		$curSortOrder = getCurSortOrder($colNum);

		if ($curSortOrder == 'ASC') {
			$nextSortOrder = "DESC";
		} else {
			$nextSortOrder = "ASC";
		}
		return $nextSortOrder;
	}


	function getNextSortOrderInWords($colNum) {

		$curSortOrder = getCurSortOrder($colNum);

		if ($curSortOrder == 'ASC') {
			return "lang_Common_Sort_DESC";
		} else {
			return "lang_Common_Sort_ASC";
		}
	}

	function getSortURL($colNum) {

		$sortOrder = getNextSortOrder($colNum);
		$url = "./CentralController.php?leavecode=Leave&action=Leave_Summary&sortField=${colNum}&sortOrder${colNum}=${sortOrder}";
		return $url;
	}

	function getSortIcon($colNum) {

		$imgName = getCurSortOrder($colNum);
		if ($imgName == null) {
			$imgName = 'null';
		}
		return "../../themes/beyondT/icons/" . $imgName . ".png";
	}
?>
<?php include ROOT_PATH."/lib/common/yui.php"; ?>
<script language="javascript">

	function init() {
	  oLinkNewTimeEvent = new YAHOO.widget.Button("linkTakenLeave");
	}

	YAHOO.util.Event.addListener(window, "load", init);

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

	function sort(sortUrl) {
		document.frmSummary.action = sortUrl;
		document.frmSummary.submit();
	}

<?php	if ($auth === 'admin') { ?>

	function actTakenLeave() {
		document.frmSummary.action = '?leavecode=Leave&action=Leave_List_Taken';
		document.frmSummary.submit();
	}

	function actCopyLeaveQuota() {
		window.location = '?leavecode=Leave&action=Leave_Quota_Copy_Last_Year&currYear=<?php echo $dispYear; ?>';
	}

<?php	} ?>
</script>
<h2><?php echo $lang_Title; ?><hr/></h2>
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

	if (!is_array($records[0])) {
?>
	<img title="Back" onMouseOut="this.src='../../themes/beyondT/pictures/btn_back.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_back_02.jpg';"  src="../../themes/beyondT/pictures/btn_back.jpg" onClick="goBack();">
	<h5><?php echo $lang_Error_NoRecordsFound; ?></h5>
<?php
	} else {
?>
	<form method="post" onsubmit="actForm(); return false;" name="frmSummary" id="frmSummary">
		<input type="hidden" name="id" value="<?php echo isset($_REQUEST['id'])?$_REQUEST['id']:LeaveQuota::LEAVEQUOTA_CRITERIA_ALL; ?>"/>
		<input type="hidden" name="leaveTypeId" value="<?php echo isset($_REQUEST['leaveTypeId'])?$_REQUEST['leaveTypeId']:LeaveQuota::LEAVEQUOTA_CRITERIA_ALL; ?>" />
		<input type="hidden" name="year" value="<?php echo isset($_REQUEST['year'])?$_REQUEST['year']:date('Y'); ?>" />
		<input type="hidden" name="searchBy" value="<?php echo isset($_REQUEST['searchBy'])?$_REQUEST['searchBy']:"employee"; ?>"/>

	<?php
		if ($auth === 'admin') {
	?>

	<p class="controls">
		<img title="Back" onMouseOut="this.src='../../themes/beyondT/pictures/btn_back.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_back_02.jpg';"  src="../../themes/beyondT/pictures/btn_back.jpg" onClick="goBack();">
		<input type="image" name="btnAct" src="<?php echo $btnImage; ?>" onMouseOut="this.src='<?php echo $btnImage; ?>';" onMouseOver="this.src='<?php echo $btnImageMO; ?>';" />
	<?php if (isset($_REQUEST['id']) && ($_REQUEST['id'] != LeaveQuota::LEAVEQUOTA_CRITERIA_ALL)) {?>
		<a href="javascript:actTakenLeave()"><?php echo $lang_Leave_Common_ListOfTakenLeave; ?></a>
	<?php } ?>
	<?php if ($copyQuota) { ?>
		<a href="javascript:actCopyLeaveQuota()"><?php echo $lang_Leave_CopyLeaveQuotaFromLastYear; ?></a>
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
		<th class="tableTopMiddle"></th>
		<th class="tableTopRight"></th>
	</tr>
	<tr>
		<th class="tableMiddleLeft"></th>

		<?php if ((isset($_REQUEST['id']) && empty($_REQUEST['id'])) && (!isset($_SESSION['empID']) || (isset($_SESSION['empID']) && ($empInfo[0] != $_SESSION['empID'])))) { ?>

			<?php $col = 1; ?>
			<th width="180px" class="tableMiddleMiddle">
				<?php if ($modifier === 'edit') {
					      echo $lang_Leave_Common_EmployeeName;
				      } else { ?>
				<a href="#" onclick="sort('<?php echo getSortURL($col); ?>')"
				   title="<?php $word = getNextSortOrderInWords($col); echo $$word; ?>" class="sortBy"><?php echo $lang_Leave_Common_EmployeeName;?></a>
				<img src="<?php echo getSortIcon($col); ?>" width="8" height="10" border="0" alt="" style="vertical-align: bottom">
				<?php } ?>
			</th>
		<?php } ?>

		<?php $col = 2; ?>
    	<th width="180px" class="tableMiddleMiddle">
				<?php if ($modifier === 'edit') {
						  echo $lang_Leave_Common_LeaveType;
				      } else { ?>
			<a href="#" onclick="sort('<?php echo getSortURL($col); ?>')"
			   title="<?php $word = getNextSortOrderInWords($col); echo $$word; ?>" class="sortBy"><?php echo $lang_Leave_Common_LeaveType;?></a>
			<img src="<?php echo getSortIcon($col); ?>" width="8" height="10" border="0" alt="" style="vertical-align: bottom">
				<?php } ?>
		</th>

    	<?php if ($auth === 'admin') { ?>

			<?php $col = 3; ?>
			<th width="180px" class="tableMiddleMiddle">
				<?php if ($modifier === 'edit') {
						  echo $lang_Leave_Common_LeaveEntitled;
				      } else { ?>
				<a href="#" onclick="sort('<?php echo getSortURL($col); ?>')"
				   title="<?php $word = getNextSortOrderInWords($col); echo $$word; ?>" class="sortBy"><?php echo $lang_Leave_Common_LeaveEntitled;?></a>
					<img src="<?php echo getSortIcon($col); ?>" width="8" height="10" border="0" alt="" style="vertical-align: bottom">
				<?php } ?>
			</th>
    	<?php } ?>

		<?php $col = 4; ?>
		<th width="180px" class="tableMiddleMiddle">
				<?php if ($modifier === 'edit') {
					      echo $lang_Leave_Common_LeaveTaken;
				      } else { ?>
			<a href="#" onclick="sort('<?php echo getSortURL($col); ?>')"
			   title="<?php $word = getNextSortOrderInWords($col); echo $$word; ?>" class="sortBy"><?php echo $lang_Leave_Common_LeaveTaken;?></a>
				<img src="<?php echo getSortIcon($col); ?>" width="8" height="10" border="0" alt="" style="vertical-align: bottom">
				<?php } ?>
		</th>

		<?php $col = 5; ?>
		<th width="180px" class="tableMiddleMiddle">
				<?php if ($modifier === 'edit') {
					      echo $lang_Leave_Common_LeaveScheduled;
				      } else { ?>
			<a href="#" onclick="sort('<?php echo getSortURL($col); ?>')"
			   title="<?php $word = getNextSortOrderInWords($col); echo $$word; ?>" class="sortBy"><?php echo $lang_Leave_Common_LeaveScheduled;?></a>
				<img src="<?php echo getSortIcon($col); ?>" width="8" height="10" border="0" alt="" style="vertical-align: bottom">
				<?php } ?>
		</th>

		<?php $col = 6; ?>
		<th width="180px" class="tableMiddleMiddle">
				<?php if ($modifier === 'edit') {
						  echo $lang_Leave_Common_LeaveAvailable;
				      } else { ?>
			<a href="#" onclick="sort('<?php echo getSortURL($col); ?>')"
			   title="<?php $word = getNextSortOrderInWords($col); echo $$word; ?>" class="sortBy"><?php echo $lang_Leave_Common_LeaveAvailable;?></a>
			<img src="<?php echo getSortIcon($col); ?>" width="8" height="10" border="0" alt="" style="vertical-align: bottom">
				<?php } ?>
		</th>
		<th class="tableMiddleRight"></th>
	</tr>
  </thead>
  <tbody>
<?php
	$leaveTypeObj = new LeaveType();
	$j = 0;
	if (is_array($records[0])) {
		  foreach ($records[0] as $record) {
			$cssClass = (!($j%2)) ? 'odd' : 'even';
			$j++;

			if ($record['available_flag'] == $leaveTypeObj->availableStatusFlag) {
				$deletedLeaveType = false;
			} else {
				$deletedLeaveTypesFound = true;
				$deletedLeaveType = true;
			}
?>
  <tr>
  	<td class="tableMiddleLeft"></td>
   	<?php if ((isset($_REQUEST['id']) && empty($_REQUEST['id'])) && (!isset($_SESSION['empID']) || (isset($_SESSION['empID']) && ($empInfo[0] != $_SESSION['empID'])))) { ?>
  	<td class="<?php echo $cssClass; ?>"><?php echo $record['employee_name'] ?></td>
  	<?php } ?>
    <td class="<?php echo $cssClass; ?>">
    <?php echo $record['leave_type_name'];
          if ($deletedLeaveType) {
          	echo '<span class="error">*</span>';
          }
    ?></td>
    <?php if (($auth === 'admin') && ($modifier === 'display')) { ?>
    <td class="<?php echo $cssClass; ?>"><?php echo number_format(round($record['no_of_days_allotted'], 2), 2); ?></td>
    <?php } else if (($auth === 'admin') && ($modifier === 'edit')) {

				$readOnly = ($deletedLeaveType) ? "readonly" : "";
    ?>
    <td class="<?php echo $cssClass; ?>">
    <input type="hidden" name="txtLeaveTypeId[]" value="<?php echo $record['leave_type_id']; ?>"/>
    <input type="hidden" name="txtEmployeeId[]" value="<?php echo $record['emp_number']; ?>"/>

    <input type="text" name="txtLeaveEntitled[]" value="<?php echo number_format(round($record['no_of_days_allotted'], 2), 2); ?>" size="3" <?php echo $readOnly; ?>/></td>
    <?php } ?>
		<td class="<?php echo $cssClass; ?>"><?php if (!empty($record['leave_taken'])) {
													  echo number_format(round($record['leave_taken'], 2), 2);
												   } else {
												      echo "0.00";
												   } ?></td>
		<td class="<?php echo $cssClass; ?>"><?php if (!empty($record['leave_scheduled'])) {
													  echo number_format(round($record['leave_scheduled'], 2), 2);
												   } else {
												      echo "0.00";
												   } ?></td>

    <td class="<?php echo $cssClass; ?>"><?php if (!empty($record['leave_available']) && $record['leave_available'] >= 0) {
												    echo number_format(round($record['leave_available'], 2), 2);
    										   } else {
    										   		echo "0.00";
    										   } ?></td>
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
		<td class="tableBottomMiddle"></td>
		<td class="tableBottomRight"></td>
	</tr>
  </tfoot>
</table>
</form>

<?php
	if ($deletedLeaveTypesFound) {
		include ROOT_PATH . "/templates/leave/deletedLeaveInfo.php";
	}
}
?>
