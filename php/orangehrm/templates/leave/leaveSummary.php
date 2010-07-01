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
$_SESSION['moduleType'] = 'leave';
require_once ROOT_PATH . '/plugins/PlugInFactoryException.php';
require_once ROOT_PATH . '/plugins/PlugInFactory.php';
//Check leave-csv plugin available
$csvLeaveExportRepotsPluginAvailable = false;
$PlugInObj = PlugInFactory::factory("LEAVEREPORT");
if(is_object($PlugInObj) && $PlugInObj->checkAuthorizeLoginUser(authorize::AUTHORIZE_ROLE_ADMIN) && $PlugInObj->checkAuthorizeModule( $_SESSION['moduleType'])){
	$csvLeaveExportRepotsPluginAvailable = true;
} 
//echo '<pre>';print_r($records);
$empInfo = null;
if (isset($records['empDetails'])) {
	$empInfo = $records['empDetails'];
}

$rights = $_SESSION['localRights'];

$currentPage = $records['pageNo'] ;
$allRecords =   $records['leaveCount'];
$token = $records['token'];
$deletedLeaveTypesFound = false;
$auth = $modifier[1];
$dispYear = $modifier[2];

$copyQuota = $modifier[3];

$broughtForward = $modifier[4];


 $modifier = $modifier[0];

 if ($modifier === 'edit') {
    $btnClass = 'savebutton';
    $btnTitle = $lang_Common_Save;
    $resetDisabled = '';
 	$btnImage = '../../themes/beyondT/pictures/btn_save.gif';
 	$btnImageMO = '../../themes/beyondT/pictures/btn_save_02.gif';
 	$frmAction = '?leavecode=Leave&action=Leave_Quota_Save';
 } else {
    $btnClass = 'editbutton';
    $btnTitle = $lang_Common_Edit;
    $resetDisabled = 'disabled="disabled"';
 	$btnImage = '../../themes/beyondT/pictures/btn_edit.gif';
 	$btnImageMO = '../../themes/beyondT/pictures/btn_edit_02.gif';
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

 if (isset($empInfo[0]) && $empInfo[0] == $_SESSION['empID']) {
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

<style type="text/css">
<!--

.leaveQuotaLabel {
	padding-left: 2px;
	color: #FF0000;
	font-size: 11px;
}

.leaveQuotaBox {
	border: solid 1px #000000;
	padding: 2px;
	width: 40px;
}


-->
</style>

<script type="text/javascript">
//<![CDATA[

	function init() {
	  oLinkNewTimeEvent = new YAHOO.widget.Button("linkTakenLeave");
	}

	function nextPage() {
		i=document.frmSummary.pageNO.value;
		i++;
		document.frmSummary.pageNO.value=i;
		document.frmSummary.submit();
	}

	function prevPage() {
		var i=document.frmSummary.pageNO.value;
		i--;
		document.frmSummary.pageNO.value=i;
		document.frmSummary.submit();
	}

	function chgPage(pNO) {
		document.frmSummary.pageNO.value=pNO;
		document.frmSummary.submit();
	}

	function validateLeaveQuotaAmount(strValue) {
		if (isNaN(strValue)) {
			return "<?php echo $lang_Leave_Summary_Error_NonNumericValue; ?>";
		} else {
			amount = new Number(strValue);
			if (amount < 0 || amount > 365) {
				return "<?php echo $lang_Leave_Summary_Error_InvalidValue; ?>";
			}
		}

		return '';
	}

	function markFields(obj, msg) {
		if (msg != '') {
			obj.style.backgroundColor = '#FFCCCC';
		} else {
			obj.style.backgroundColor = '#FFFFFF';
		}

		labelIndex = obj.getAttribute('id');
		document.getElementById('leaveQuotaLabel_' + labelIndex).innerHTML = msg;
	}

	function validateLeaveSummary() {

		isValid = true;

		with (document.frmSummary) {
			for (i in elements) {
				if (elements[i] && elements[i].type == 'text') {
					msg = validateLeaveQuotaAmount(elements[i].value);
					markFields(elements[i], msg);

					if (msg != '') {
						isValid = false;
					}
				}
			}
		}

		return isValid;

	}

	function validateIndividualLeaveQuota(obj) {
		msg = validateLeaveQuotaAmount(obj.value);
		markFields(obj, msg);
	}

	YAHOO.util.Event.addListener(window, "load", init);

	function actForm() {
		
		if (validateLeaveSummary()) {

			document.frmSummary.action = '<?php echo $frmAction; ?>';
			document.frmSummary.submit();
			return true;
		} else {
			alert("<?php echo $lang_Leave_Summary_Error_CorrectLeaveSummary; ?>");
			return false;
		}
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

	function actCopyLeaveBroughtForward() {
		window.location = '?leavecode=Leave&action=Leave_Brought_Forward_Copy_Last_Year&currYear=<?php echo $dispYear; ?>';
	}

	
<?php	} ?>

<?php	if ($auth === 'supervisor') { ?>

	function actTakenLeave() {
		document.frmSummary.action = '?leavecode=Leave&action=Leave_List_Taken';
		document.frmSummary.submit();
	}

<?php }  ?>

<?php if (($auth === 'admin' ) || ($auth === 'supervisor')) {
	
?>
function exportSummaryData(pdfData) {
	
	//exportStatus = true;
   // var url = "../../plugins/leave-csv/LeaveReportController.php?path=<?php echo addslashes(ROOT_PATH) ?>&userId=<?php echo $_SESSION['empID'];?>&repType=LeaveSummaryRep&moduleType=<?php echo  $_SESSION['moduleType'] ?>&obj=<?php  echo   base64_encode(serialize($PlugInObj))?>";
   var userId = document.getElementById('id').value ;
    var yearVal = document.getElementById('year').value ;
	var leaveTypeId = document.getElementById('leaveTypeId').value ;
	var searchBy = document.getElementById('searchBy').value ;
	//exportStatus = true;

    var url = "../../plugins/leave-csv/LeaveReportController.php?path=<?php echo addslashes(ROOT_PATH) ?>&userId="+userId+"&year="+yearVal+"&leaveType="+leaveTypeId+"&searchBy="+searchBy+"&printPdf="+pdfData+"&pdfName=Leave-Summary"+"&repType=LeaveSummaryRep&moduleType=<?php echo  $_SESSION['moduleType'] ?>&obj=<?php  echo   base64_encode(serialize($PlugInObj))?>";
	
	window.location = url;


}

<?php

}?>
//]]>
</script>

<?php if (($auth === 'admin' ) || ($auth === 'supervisor')) { ?>
<div class="navigation">
	<input type="button" class="backbutton" onclick="goBack();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
	value="<?php echo $lang_Common_Back;?>" />
</div>
<?php } ?>

<div class="outerbox">
<div class="mainHeading"><h2><?php echo $lang_Title; ?></h2></div>
<?php if (isset($_GET['message']) && $_GET['message'] != 'xx') {

	$expString  = $_GET['message'];
	$expString = explode ("_",$expString);
	$length = count($expString);

	$col_def=strtolower($expString[$length-1]);

	$expString='lang_Leave_'.$_GET['message'];
	if (isset($$expString)) {
?>
    <div class="messagebar">
        <span class="<?php echo $col_def; ?>"><?php echo $$expString; ?></span>
    </div>
<?php
	}
}

	if (!is_array($records['leaveSummary'])) {
?>
	<h5><?php echo $lang_Error_NoRecordsFound; ?></h5>
<?php
	} else {
?>
	<form method="post" onsubmit="return actForm(); return false;" name="frmSummary" id="frmSummary">
		<input type="hidden" id = "id" name="id" value="<?php echo isset($_REQUEST['id'])?$_REQUEST['id']:LeaveQuota::LEAVEQUOTA_CRITERIA_ALL; ?>"/>
		<input type="hidden" id="leaveTypeId" name="leaveTypeId" value="<?php echo isset($_REQUEST['leaveTypeId'])?$_REQUEST['leaveTypeId']:LeaveQuota::LEAVEQUOTA_CRITERIA_ALL; ?>" />
		<input type="hidden" id="year" name="year" value="<?php echo isset($_REQUEST['year'])?$_REQUEST['year']:date('Y'); ?>" />
		<input type="hidden" id="searchBy" name="searchBy" value="<?php echo isset($_REQUEST['searchBy'])?$_REQUEST['searchBy']:"employee"; ?>"/>
		<input type="hidden" name="pageNO" value="<?php echo $currentPage ?>" />
      <input type="hidden" name="token" value="<?php echo $token; ?>" />
    <div class="actionbar">
        <div class="actionbuttons">
    <?php
        if ($auth === 'admin' ) {
    ?>
          <input type="button" class="<?php echo $btnClass;?>" id="editBtn" onclick="actForm();" <?php echo ($rights['edit']) ? '' : 'disabled="disabled"'; ?>
                onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                value="<?php echo $btnTitle;?>" />
		   <input type="reset" class="resetbutton" <?php echo $resetDisabled; ?> value="<?php echo $lang_Common_Reset; ?>" />
		   
		   

    <?php if (isset($_REQUEST['id']) && ($_REQUEST['id'] != LeaveQuota::LEAVEQUOTA_CRITERIA_ALL)) {?>
        <a href="javascript:actTakenLeave()"><?php echo $lang_Leave_Common_ListOfTakenLeave; ?></a>
    <?php } ?>
    <?php if ($copyQuota) { ?>
        <a href="javascript:actCopyLeaveQuota()"><?php echo $lang_Leave_CopyLeaveQuotaFromLastYear; ?></a>
    <?php } if (!$copyQuota && $broughtForward) { ?>
        <a href="javascript:actCopyLeaveBroughtForward()"><?php echo $lang_Leave_CopyLeaveBroughtForwardFromLastYear; ?></a>
    <?php } ?>
<?php
        }else if($auth === 'supervisor'){
?>

        <?php if (isset($_REQUEST['id']) && ($_REQUEST['id'] != LeaveQuota::LEAVEQUOTA_CRITERIA_ALL)) {?>
        <a href="javascript:actTakenLeave()"><?php echo $lang_Leave_Common_ListOfTakenLeave; ?></a>
    <?php } ?>

<?php
        }
        if(($auth === 'supervisor' || $auth === 'admin') && $csvLeaveExportRepotsPluginAvailable) {
        ?>
        <!--
						The value/label of the following button is hardcoded because it is shown
						only if the plugin is installed and the label should come from the plugin
						and not from the language files-->
	<input type="button" name="btnExportData" value="Export To CSV" class="plainbtn" onclick="exportSummaryData(0); return false;"
	onmouseover="moverButton(this);" onmouseout="moutButton(this)" />
    <input type="button" name="btnExportPDFData" value="Export To PDF" class="plainbtn" onclick="exportSummaryData(1); return false;"
	onmouseover="moverButton(this);" onmouseout="moutButton(this)" />
<?php 	
        }
?>
        </div>
        <div class="noresultsbar"></div>
        <div class="pagingbar">
<?php
		$commonFunc = new CommonFunctions();
		$pageStr = $commonFunc->printPageLinks($allRecords , $currentPage);
		$pageStr = preg_replace(array('/#first/', '/#previous/', '/#next/', '/#last/'), array($lang_empview_first, $lang_empview_previous, $lang_empview_next, $lang_empview_last), $pageStr);
		echo $pageStr;
?>
        </div>
    </div>
    <br class="clear"/>

<table border="0" cellpadding="0" cellspacing="0" class="data-table">
  <thead>
	<tr>
<?php 

if (
	(isset($_REQUEST['id']) && empty($_REQUEST['id'])) && 
	(
		$_SESSION['isAdmin'] == 'Yes' ||
		(
			isset($_SESSION['empID']) && 
			((!empty($empInfo)) && $empInfo[0] != $_SESSION['empID'])
		)
	)
) { ?>

			<?php $col = 1; ?>
			<td>
				<?php if ($modifier === 'edit') {
					      echo $lang_Leave_Common_EmployeeName;
				      } else { ?>
				<a href="#" onclick="sort('<?php echo getSortURL($col); ?>')"
				   title="<?php $word = getNextSortOrderInWords($col); echo $$word; ?>" class="sortBy"><?php echo $lang_Leave_Common_EmployeeName;?></a>
				<img src="<?php echo getSortIcon($col); ?>" width="8" height="10" border="0" alt="" style="vertical-align: bottom">
				<?php } ?>
			</td>
		<?php } ?>

		<?php $col = 2; ?>
    	<td>
				<?php if ($modifier === 'edit') {
						  echo $lang_Leave_Common_LeaveType;
				      } else { ?>
			<a href="#" onclick="sort('<?php echo getSortURL($col); ?>')"
			   title="<?php $word = getNextSortOrderInWords($col); echo $$word; ?>" class="sortBy"><?php echo $lang_Leave_Common_LeaveType;?></a>
			<img src="<?php echo getSortIcon($col); ?>" width="8" height="10" border="0" alt="" style="vertical-align: bottom">
				<?php } ?>
		</td>

    	<?php if ($auth === 'admin') { ?>

			<?php $col = 3; ?>
			<td>
				<?php if ($modifier === 'edit') {
						  echo "$lang_Leave_Common_LeaveEntitled ($lang_Common_Days)";
				      } else { ?>
				<a href="#" onclick="sort('<?php echo getSortURL($col); ?>')"
				   title="<?php $word = getNextSortOrderInWords($col); echo $$word; ?>" class="sortBy"><?php echo "$lang_Leave_Common_LeaveEntitled ($lang_Common_Days)";?></a>
					<img src="<?php echo getSortIcon($col); ?>" width="8" height="10" border="0" alt="" style="vertical-align: bottom">
				<?php } ?>
			</td>
    	<?php } ?>

		<?php $col = 4; ?>
		<td>
				<?php if ($modifier === 'edit') {
					      echo "$lang_Leave_Common_LeaveTaken ($lang_Common_Days)";
				      } else { ?>
			<a href="#" onclick="sort('<?php echo getSortURL($col); ?>')"
			   title="<?php $word = getNextSortOrderInWords($col); echo $$word; ?>" class="sortBy"><?php echo "$lang_Leave_Common_LeaveTaken ($lang_Common_Days)";?></a>
				<img src="<?php echo getSortIcon($col); ?>" width="8" height="10" border="0" alt="" style="vertical-align: bottom">
				<?php } ?>
		</td>

		<?php $col = 5; ?>
		<td>
				<?php if ($modifier === 'edit') {
					      echo "$lang_Leave_Common_LeaveScheduled ($lang_Common_Days)";
				      } else { ?>
			<a href="#" onclick="sort('<?php echo getSortURL($col); ?>')"
			   title="<?php $word = getNextSortOrderInWords($col); echo $$word; ?>" class="sortBy"><?php echo "$lang_Leave_Common_LeaveScheduled ($lang_Common_Days)";?></a>
				<img src="<?php echo getSortIcon($col); ?>" width="8" height="10" border="0" alt="" style="vertical-align: bottom">
				<?php } ?>
		</td>

		<?php $col = 6; ?>
		<td>
				<?php if ($modifier === 'edit') {
						  echo "$lang_Leave_Common_LeaveRemaining ($lang_Common_Days)";
				      } else { ?>
			<a href="#" onclick="sort('<?php echo getSortURL($col); ?>')"
			   title="<?php $word = getNextSortOrderInWords($col); echo $$word; ?>" class="sortBy"><?php echo "$lang_Leave_Common_LeaveRemaining ($lang_Common_Days)";?></a>
			<img src="<?php echo getSortIcon($col); ?>" width="8" height="10" border="0" alt="" style="vertical-align: bottom">
				<?php } ?>
		</td>
	</tr>
  </thead>
  <tbody>
<?php
	$leaveTypeObj = new LeaveType();
	$j = 0;
	if (is_array($records['leaveSummary'])) {
		  foreach ($records['leaveSummary'] as $record) {
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
  
<?php 
if (
	(isset($_REQUEST['id']) && empty($_REQUEST['id'])) && 
	(
		$_SESSION['isAdmin'] == 'Yes' ||
		(
			isset($_SESSION['empID']) && 
			(!empty($empInfo) && $empInfo[0] != $_SESSION['empID'])
		)
	)
) { 
?>
  	
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

				$readOnly = ($deletedLeaveType) ? 'readonly="readonly"' : '';
    ?>
    <td class="<?php echo $cssClass; ?>">
    <input type="hidden" name="txtLeaveTypeId[]" value="<?php echo $record['leave_type_id']; ?>"/>
    <input type="hidden" name="txtEmployeeId[]" value="<?php echo $record['emp_number']; ?>"/>
    <input
    	type="text"
    	name="txtLeaveEntitled[]"
    	class="leaveQuotaBox"
    	id="<?php echo $j; ?>"
    	value="<?php echo number_format(round($record['no_of_days_allotted'], 2), 2); ?>"
    	onblur="validateIndividualLeaveQuota(this)"
    	<?php echo $readOnly; ?> />
    <span id="leaveQuotaLabel_<?php echo $j; ?>" class="leaveQuotaLabel"></span>
    </td>
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

    <td class="<?php echo $cssClass; ?>"><?php if (!empty($record['leave_available'])) {
												    echo number_format(round($record['leave_available'], 2), 2);
    										   } ?></td>
  </tr>
<?php 	  }
	}
?>
  </tbody>
</table>
</form>

<?php
	if ($deletedLeaveTypesFound) {
		include ROOT_PATH . "/templates/leave/deletedLeaveInfo.php";
	}
}
?>
</div>
<script type="text/javascript">
    <!--
        if (document.getElementById && document.createElement) {
            roundBorder('outerbox');
        }
    -->
</script>
