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

require_once ROOT_PATH.'/lib/common/calendar.php';
require_once ROOT_PATH . '/lib/extractor/time/EXTRACTOR_AttendanceRecord.php';


$_SESSION['moduleType'] = 'timeMod';
require_once ROOT_PATH . '/plugins/PlugInFactoryException.php';
require_once ROOT_PATH . '/plugins/PlugInFactory.php';
// Check csv plugin available
$PlugInObj = PlugInFactory::factory("CSVREPORT");
if(is_object($PlugInObj) && $PlugInObj->checkAuthorizeLoginUser(authorize::AUTHORIZE_ROLE_ADMIN) && $PlugInObj->checkAuthorizeModule( $_SESSION['moduleType'])){
    $csvExportRepotsPluginAvailable = true;
}


if (isset($records['recordsArr'])) {
	$recordsArr = $records['recordsArr'];

}

if ($records['reportView'] == 'detailed' && isset($records['recordsArr'])) {
	$extractor = new EXTRACTOR_AttendanceRecord($records['userTimeZoneOffset'], $records['serverTimeZoneOffset']);
}

if (isset($records['message'])) {

	if ($records['message'] == 'update-success') {
		$records['message'] = $lang_Time_Attendance_ReportSavingSuccess;
	} elseif ($records['message'] == 'update-failure') {
		$records['message'] = $lang_Time_Attendance_ReportSavingFailure;
	} elseif($records['message'] == 'overlapping-failure') {
		$records['message'] = $lang_Time_Attendance_Overlapping;
	} elseif($records['message'] == 'nochange-failure') {
		$records['message'] = $lang_Time_Attendance_ReportNoChange;
	}

}

?>

<script type="text/javascript">
//<![CDATA[
function exportData() { 
	var from = $('txtFromDate').value;
	var to = $('txtToDate').value;	
    var reportView = document.frmGenerateAttendanceReport.optReportView.options[document.frmGenerateAttendanceReport.optReportView.selectedIndex].value;
    markEmpNumber($('txtEmployeeSearch').value);
    var hdnEmpNo = $('hdnEmpNo').value;
    if(validateSearchCriteria()) {
	    var url = "../../plugins/PluginController.php?route=CSVPluginController/exportAttendanceData/&path=<?php echo addslashes(ROOT_PATH)?>&txtFromDate="+from+"&txtToDate="+to+"&optReportView="+reportView+"&hdnEmpNo="+hdnEmpNo;
	    window.location = url;
    }
}


<?php if (isset($records['recordsArr'])) {

$count = count($recordsArr);

?>


	dateTimeFormat = YAHOO.OrangeHRM.calendar.format+" "+YAHOO.OrangeHRM.time.format;

	function validate() {

		errFlag = false;
		var i=0;
		count = <?php echo $count; ?>;

		for (i=0;i<count;i++) {

			var inTime = strToTime($("txtNewInDate-"+i).value+" "+$("txtNewInTime-"+i).value, dateTimeFormat);
			var outTime = strToTime($("txtNewOutDate-"+i).value+" "+$("txtNewOutTime-"+i).value, dateTimeFormat);

			if (!inTime || !outTime) {
				alert("<?php echo $lang_Time_Errors_InvalidDateOrTime; ?>");
				errFlag = true;
			}

			if (inTime >= outTime) {
				alert("<?php echo $lang_Time_Attendance_InvalidOutTime; ?>");
				errFlag = true;
			}

			maxInTimestamp = strToTime($("txtNewInDate-"+i).value+" 24:00", dateTimeFormat);
			maxOutTimestamp = strToTime($("txtNewOutDate-"+i).value+" 24:00", dateTimeFormat);

			if (inTime >= maxInTimestamp || outTime >= maxOutTimestamp) {
				alert("<?php echo $lang_Time_Errors_InvalidMaxTime; ?>");
				errFlag = true;
			}

		}

		return !errFlag;

	}

	function submitForm() {
		if (validate()) {
			$("frmSaveAttendanceReport").submit();
		}
	}

<?php } ?>


<?php if ($records['reportType'] == 'Emp') { // Emp report data: Begins ?>

	function markEmpNumber(empName) {
		empNoField = document.getElementById("hdnEmpNo");
		empFullName = document.getElementById("hdnEmpName");
		for(i in employees) {
			if (employees[i].toLowerCase() == empName.toLowerCase()) {
				empNoField.value = ids[i];
				empFullName.value = employees[i];
				return;
			} else {
				empNoField.value = '';
			}
		}
	}

    var employees = new Array();
    var ids = new Array();

	<?php
	$employees = $records['empList'];
	for ($i=0;$i<count($employees);$i++) {
		echo "employees[" . $i . "] = '" . CommonFunctions::escapeForJavascript($employees[$i][1] . " " . $employees[$i][2]) . "';\n";
		echo "ids[" . $i . "] = \"" . $employees[$i][0] . "\";\n";
	}
	echo "employees[" . ++$i . "] = 'All';\n";
	echo "ids[" . $i . "] = \"-1\";\n";
	?>

	function showAutoSuggestTip(obj) {
			
		if (obj.value == '<?php echo $lang_Common_TypeHereForHints; ?>') {
			obj.value = '';
			obj.style.color = '#000000';
		}
	}

<?php }  ?>

<?php if ($records['reportView'] == 'summary') { ?>
	function showDetailedReport(dateVal, employeeId, name) {
		document.frmShowDetailedReport.txtFromDate.value = dateVal;
		document.frmShowDetailedReport.txtToDate.value = dateVal;
		document.frmShowDetailedReport.hdnEmployeeId.value = employeeId;
		document.frmShowDetailedReport.hdnEmpName.value = name;
		document.frmShowDetailedReport.submit();
	}
<?php } ?>

<?php if (isset($records['hdnFromSummary'])) { ?>

	function backToSummary() {
		var frm = document.frmGenerateAttendanceReport;
		frm.optReportView.value = 'summary';
		frm.txtFromDate.value = '<?php echo $records['orgFromDate']; ?>';
		frm.txtToDate.value = '<?php echo $records['orgToDate']; ?>';
		frm.submit();
	}

<?php } ?>

	function nextPage() {
		i=document.frmGenerateAttendanceReport.pageNo.value;
		i++;
		document.frmGenerateAttendanceReport.pageNo.value=i;
		document.frmGenerateAttendanceReport.hdnFromPaging.value = 'Yes';
		document.frmGenerateAttendanceReport.submit();
	}
	function prevPage() {
		var i=document.frmGenerateAttendanceReport.pageNo.value;
		i--;
		document.frmGenerateAttendanceReport.pageNo.value=i;
		document.frmGenerateAttendanceReport.hdnFromPaging.value = 'Yes';
		document.frmGenerateAttendanceReport.submit();
	}
	function chgPage(pNo) {
		document.frmGenerateAttendanceReport.pageNo.value=pNo;
		document.frmGenerateAttendanceReport.hdnFromPaging.value = 'Yes';
		document.frmGenerateAttendanceReport.submit();
	}

	function validateSearchCriteria() {
		errors = new Array();
		employeeName = $('txtEmployeeSearch').value;

		if (employeeName == '' || employeeName == '<?php echo $lang_Common_TypeHereForHints; ?>') {
			errors.push('<?php echo CommonFunctions::escapeForJavascript($lang_Error_PleaseSelectAnEmployee); ?>');
		}

		dateFormat = YAHOO.OrangeHRM.calendar.format;
		fromDateTimestamp = strToTime($('txtFromDate').value, dateFormat);
		toDateTimestamp = strToTime($('txtToDate').value, dateFormat);
		if (!fromDateTimestamp || !toDateTimestamp) {
			errors.push('<?php echo CommonFunctions::escapeForJavascript($lang_Time_Attendance_EnterValidDates); ?>');
		} else {
			if (fromDateTimestamp >= toDateTimestamp) {
				errors.push('<?php echo CommonFunctions::escapeForJavascript($lang_Time_Attendance_EnterValidDateRange); ?>');
			}
		}

		if (errors.length > 0) {
			message = '<?php echo CommonFunctions::escapeForJavascript($lang_Error_PleaseCorrectTheFollowing); ?>' + "\n";
			for (i = 0; i < errors.length; i++) {
				message += ' - ' + errors[i] + "\n";
			}

			alert(message);
			return false;
		}
		return true;
	}

//]]>
</script>

<style type="text/css">
#detailed-table td {
	width: 135px;
}
#detailed-table input {
	width: 110px;
	text-align: center;
}

.note-td {
	text-align: left;
}

#paging {
   text-align:right;
   margin-right: 10px;
}

.hasPunch {
	color : gray;
}
</style>

<div class="outerbox">

<!-- Message box: Begins -->
<?php if (isset($records['noReports']) && $records['noReports']) { ?>
    <div class="messagebar">
        <span class="<?php echo 'FAILURE'; ?>"><?php echo $lang_Time_Attendance_NoReports; ?></span>
    </div>
<?php } ?>
<!-- Message box: Ends -->

<form id="frmGenerateAttendanceReport" name="frmGenerateAttendanceReport" method="post"
    action="?timecode=Time&amp;action=Generate_Attendance_Report" <?php if ($records['reportType'] == 'Emp') { ?>
    onsubmit="markEmpNumber(this.txtEmployeeSearch.value); return validateSearchCriteria()"<?php } ?>>

    <div class="mainHeading"><h2><?php echo $lang_Time_Heading_Attendance_Report.($records['empName'] != ''?': '.$records['empName']:''); ?></h2></div>
    <input type="hidden" name="hdnReportType" value="<?php echo $records['reportType']; ?>" />
    <input type="hidden" name="hdnEmpNo" id="hdnEmpNo" value="<?php echo $records['empId']; ?>" />
    <input type="hidden" name="hdnEmpName" id="hdnEmpName" value="<?php echo $records['empName']; ?>" />
	<input type="hidden" name="hdnFromPaging" id="hdnFromPaging" value="No" />

    <div class="searchbox">
        <?php if ($records['reportType'] == 'Emp') {  ?>

       <label for="txtEmployeeSearchName"><?php echo $lang_Leave_Common_EmployeeName; ?></label>
        <div class="yui-skin-sam" style="float:left;margin-right:10px">
            <div id="employeeSearchAC" style="width:135px">
                  <input  id="txtEmployeeSearch" type="text" name="txtEmployeeSearchName"
                    type="text" value="<?php echo ($records['empName'] != '' ? CommonFunctions::escapeHtml($records['empName']):"All"); ?>" style="color:#999999;width:135px"/>
                  <div id="employeeSearchACContainer"></div>
            </div>
        </div>


        <?php } ?>

        <label for="txtFromDate"><?php echo $lang_Leave_Common_FromDate;?></label>
        <input type="text" name="txtFromDate" id="txtFromDate" size="10" value="<?php echo $records['fromDate']; ?>" />
        <input type="button" value="  " class="calendarBtn" />

        <label for="txtToDate"><?php echo $lang_Leave_Common_ToDate;?></label>
        <input type="text" name="txtToDate" id="txtToDate" size="10" value="<?php echo $records['toDate']; ?>" />
        <input type="button" value="  " class="calendarBtn" />


        <label for="loc_name"><?php echo $lang_Time_ReportType?></label>
        <select name="optReportView">
            <option value="summary"><?php echo $lang_Time_Option_Summary; ?></option>
            <option value="detailed" <?php echo (isset($records['reportView']) && $records['reportView'] == 'detailed')?'selected':''; ?>>
            <?php echo $lang_time_Option_Detailed; ?></option>
        </select>      
        <input type="hidden" name="pageNo" value="<?php echo (isset($records['pageNo']))?$records['pageNo']:'1'; ?>">

        <input type="submit" class="punchbutton"
            class="punchbutton" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
            value="<?php echo $lang_Time_Button_Generate;?>" />
        
        
        <?php  if(isset($csvExportRepotsPluginAvailable))  {   ?>
                    <!--
                        this pece of code added to compatible the time module with attendance data exporter
                    -->
                    <input type="button" name="btnExportData" value="Export to CSV" class="extralongbtn"
                       onclick="exportData(); return false;"
                       onmouseover="moverButton(this);"
                       onmouseout="moutButton(this)" />
      <?php  } ?>
        <br class="clear"/>
    </div>

</form>

</div> <!-- End of outerbox -->

<br class="clear" />





<?php if ($records['reportView'] == 'summary' && !empty($recordsArr)) { // Summary Table Begins ?>

<div class="outerbox" style="width:400px;text-align:center;">

<!-- Paging: Begins -->
<?php if (isset($records['recordsCount']) && $records['recordsCount'] > 50) {

echo '<div id="paging">';

$commonFunc = new CommonFunctions();
$pageStr = $commonFunc->printPageLinks($records['recordsCount'], $records['pageNo'], 50);
$pageStr = preg_replace(array('/#first/', '/#previous/', '/#next/', '/#last/'), array($lang_empview_first, $lang_empview_previous, $lang_empview_next, $lang_empview_last), $pageStr);

echo $pageStr;

echo '</div>';

} ?>
<!-- Paging: Ends -->

<table border="0" cellpadding="0" cellspacing="0" class="data-table" id="summary-table">

  <thead>
	<tr>
	    <th><?php echo $lang_empview_employeename ?></th>
    	<th><?php echo $lang_Common_Date; ?></th>
        <th><?php echo $lang_Time_Timesheet_Duration; ?></th>
    </tr>
  </thead>

  <tbody>

<?php 

	$i = 0;
	foreach ($recordsArr as $attendanceRow) {

		$className="odd";
		if (($i%2) == 0) {
			$className="even";
		}
		
		$hasPunchedStyleClass = "";
		if($attendanceRow->duration == 0) {
			$hasPunchedStyleClass = "hasPunch";
		}

?>
    <tr class="<?php echo $className; ?>">
    <?php if($attendanceRow->employeeName == null) {?>
    			 <td class="<?php echo $hasPunchedStyleClass;?>" ><?php echo $records['empName'];?></td>
    <?php } else { ?>
    			 <td class="<?php echo $hasPunchedStyleClass;?>" ><?php echo $attendanceRow->employeeName;?></td>
    <?php }?>
       
        <td class="<?php echo $hasPunchedStyleClass;?>"><?php echo $attendanceRow->inTime; ?></td>
        <td class="<?php echo $hasPunchedStyleClass;?>">
        <?php
        $durationParts = explode(".",$attendanceRow->duration);
        $duration =sprintf("%02d:%02d", $durationParts [0], $durationParts [1]);
        if ($attendanceRow->duration > 0) {        	
        	echo "<a href=\"javascript:showDetailedReport('{$attendanceRow->inTime}',{$attendanceRow->employeeId},'".addcslashes($attendanceRow->employeeName,"'")."')\" class='".$hasPunchedStyleClass."'  style=\"text-decoration:underline\">".str_replace(".",":",$duration)."</a>";
        } else {        	
        	echo str_replace(".",":",$duration);
        }

        ?>
        </td>
    </tr>

<?php $i++;
	 } ?>

 </tbody>
</table>

<form action="?timecode=Time&action=Summary_Attendance_Report" method="post" name="frmShowDetailedReport" id="frmShowDetailedReport" />
<input type="hidden" name="hdnEmployeeId" value="<?php echo $records['empId']; ?>" />
<input type="hidden" name="txtFromDate" value="" />
<input type="hidden" name="txtToDate" value="" />
<input type="hidden" name="orgFromDate" value="<?php echo $records['fromDate']; ?>" />
<input type="hidden" name="orgToDate" value="<?php echo $records['toDate']; ?>" />
<input type="hidden" name="hdnReportType" value="<?php echo $records['reportType']; ?>" />
<input type="hidden" name="optReportView" value="detailed" />
<input type="hidden" name="hdnEmpName" id="hdnEmpName" value="<?php echo $records['empName']; ?>" />
<input type="hidden" name="callbackSummery" id="callbackSummery" value="<?php echo $records['empId'];?>" />
<input type="hidden" name="hdnFromSummary" value="yes" />
</form>

</div> <!-- End of outerbox -->

<?php } // Summary Table Ends ?>





<?php if ($records['reportView'] == 'detailed' && isset($recordsArr)) { // Detailed Table Begins ?>

<div class="outerbox" style="text-align:center;">

<!-- Message box: Begins -->
<?php if (isset($records['message'])) { ?>
    <div class="messagebar">
        <span class="<?php echo $records['messageType']; ?>"><?php echo $records['message']; ?></span>
    </div>
<?php } ?>
<!-- Message box: Ends -->


<!-- Paging: Begins -->
<?php if (isset($records['recordsCount']) && $records['recordsCount'] > 50) {

echo '<div id="paging">';

$commonFunc = new CommonFunctions();
$pageStr = $commonFunc->printPageLinks($records['recordsCount'], $records['pageNo'], 50);
$pageStr = preg_replace(array('/#first/', '/#previous/', '/#next/', '/#last/'), array($lang_empview_first, $lang_empview_previous, $lang_empview_next, $lang_empview_last), $pageStr);

echo $pageStr;

echo '</div>';

} ?>
<!-- Paging: Ends -->

<?php if(isset($records['editMode'])) { ?>
<form id="frmSaveAttendanceReport" name="frmSaveAttendanceReport" method="post" action="?timecode=Time&action=Save_Attendance_Report">
<?php } ?>

<table border="0" cellpadding="0" cellspacing="0" class="data-table" id="detailed-table">
  <thead>
	<tr>
	    <th><?php echo $lang_Common_EmployeeName ?></th>
    	<th><?php echo $lang_Time_In.' '.$lang_Common_Date; ?></th>
        <th><?php echo $lang_Time_In.' '.$lang_Common_Time; ?></th>
    	<th><?php echo $lang_Time_In.' '.$lang_Common_Note; ?></th>
    	<th><?php echo $lang_Time_Out.' '.$lang_Common_Date; ?></th>
    	<th><?php echo $lang_Time_Out.' '.$lang_Common_Time; ?></th>
    	<th><?php echo $lang_Time_Out.' '.$lang_Common_Note; ?></th>
    	<th><?php echo $lang_Time_Timesheet_Duration ?></th>
    	<?php if ($records['editMode']) { ?>
    	<th><?php echo $lang_Common_Delete; ?></th>
    	<?php } ?>
	</tr>
  </thead>
  <tbody>

	<?php

	for ($i=0; $i<$count; $i++) { // Records array: Begins

	$id = $recordsArr[$i]->getAttendanceId();
	$inDate = $recordsArr[$i]->getInDate();
	$inTime = $recordsArr[$i]->getInTime();
	$inNote = htmlentities($recordsArr[$i]->getInNote());
	$outDate = $recordsArr[$i]->getOutDate();
	$outTime = $recordsArr[$i]->getOutTime();
	$outNote = htmlentities($recordsArr[$i]->getOutNote());
	$timestampDiff = $recordsArr[$i]->getTimestampDiff();

	$className="odd";
	if (($i%2) == 0) {
		$className="even";
	}
	
	if ($records['editMode']) {

	?>

    <tr class="<?php echo $className;?>">
        <td><?php echo $recordsArr[$i]->getEmployeeName()?></td>
        <td>
        <input type="hidden" name="hdnAttendanceId-<?php echo $i; ?>" value="<?php echo $id; ?>" />
        <input type="text" name="txtNewInDate-<?php echo $i; ?>" id="txtNewInDate-<?php echo $i; ?>" size="10" value="<?php echo $inDate; ?>" />
        <input type="hidden" name="hdnOldInDate-<?php echo $i; ?>" value="<?php echo $inDate;?>" />
        </td>
        <td>
        <input type="text" name="txtNewInTime-<?php echo $i; ?>" id="txtNewInTime-<?php echo $i; ?>" value="<?php echo $inTime; ?>" />
        <input type="hidden" name="hdnOldInTime-<?php echo $i; ?>" value="<?php echo $inTime; ?>" />
        </td>
        <td class="note-td">
        <input type="text" name="txtNewInNote-<?php echo $i; ?>" id="txtNewInNote-<?php echo $i; ?>" value="<?php echo $inNote; ?>" />
        <input type="hidden" name="hdnOldInNote-<?php echo $i; ?>" value="<?php echo $inNote; ?>" />
        </td>
        <td>
        <input type="text" name="txtNewOutDate-<?php echo $i; ?>" id="txtNewOutDate-<?php echo $i; ?>" value="<?php echo $outDate; ?>" />
        <input type="hidden" name="hdnOldOutDate-<?php echo $i; ?>" value="<?php echo $outDate; ?>" />
        </td>
        <td>
        <input type="text" name="txtNewOutTime-<?php echo $i; ?>" id="txtNewOutTime-<?php echo $i; ?>" value="<?php echo $outTime; ?>" />
        <input type="hidden" name="hdnOldOutTime-<?php echo $i; ?>" value="<?php echo $outTime; ?>" />
        </td>
        <td class="note-td">
        <input type="text" name="txtNewOutNote-<?php echo $i; ?>" id="txtNewOutNote-<?php echo $i; ?>" value="<?php echo $outNote; ?>" />
        <input type="hidden" name="hdnOldOutNote-<?php echo $i; ?>" value="<?php echo $outNote; ?>" />
        </td>
        <td>
        <input type="text" name="txtDuration-<?php echo $i; ?>" id="txtDuration-<?php echo $i; ?>" value="<?php echo $recordsArr[$i]->getDuration(); ?>" read />
        <input type="hidden" name="hdnDuration-<?php echo $i; ?>" value="<?php echo $recordsArr[$i]->getDuration(); ?>" />
        </td>
        <td>
        <input type="checkbox" name="chkDeleteStatus-<?php echo $i; ?>" id="chkDeleteStatus-<?php echo $i; ?>" />
        <input type="hidden" name="hdnTimestampDiff-<?php echo $i; ?>" id="hdnTimestampDiff-<?php echo $i; ?>" value="<?php echo $timestampDiff; ?>" />
        </td>
    </tr>

    <?php } else { // If editing is not allowed ?>

    <tr class="<?php echo $className;?>">
    	<td><?php echo $recordsArr[$i]->getEmployeeName()?></td>
        <td><?php echo $inDate ;?> </td>
        <td><?php echo $inTime ;?></td>
        <td class="note-td"><?php echo $recordsArr[$i]->getInNote() ;?></td>
        <td><?php echo $outDate ;?></td>
        <td><?php echo $outTime ;?></td>
        <td class="note-td"><?php echo $recordsArr[$i]->getOutNote() ;?></td>
        <td><?php echo $recordsArr[$i]->getDuration(); ?></td>       
    </tr>

	<?php } } // Records array: Ends ?>

  </tbody>
</table>

<br class="clear" />

<?php if($records['editMode']) { ?>
<input type="hidden" name="hdnEmployeeId" value="<?php echo $records['empId']; ?>" />
<input type="hidden" name="txtFromDate" value="<?php echo $records['fromDate']; ?>" />
<input type="hidden" name="txtToDate" value="<?php echo $records['toDate']; ?>" />
<input type="hidden" name="hdnReportType" value="<?php echo $records['reportType']; ?>" />
<input type="hidden" name="optReportView" value="<?php echo $records['reportView']; ?>" />
<input type="hidden" name="recordsCount" value="<?php echo $count; ?>" />
<input type="hidden" name="hdnEmpName" id="hdnEmpName" value="<?php echo $records['empName']; ?>" />
<?php if (isset($records['hdnFromSummary'])) { ?>
<input type="hidden" name="orgFromDate" value="<?php echo $records['orgFromDate']; ?>" />
<input type="hidden" name="orgToDate" value="<?php echo $records['orgToDate']; ?>" />
<input type="hidden" name="hdnFromSummary" value="yes" />
<input type="button" value="Back" onclick="backToSummary()" class="punchbutton" />
<?php } ?>
<input type="button" name="btnSave" value="<?php echo $lang_Common_Save; ?>" onclick="submitForm()"
class="punchbutton" onmouseover="moverButton(this);" onmouseout="moutButton(this);" />
</form>
<?php } ?>

<br class="clear" />


</div> <!-- End of outerbox -->

<?php } // Detailed Table Ends ?>

<div id="cal1Container" style="position:absolute;" ></div>
<script type="text/javascript">
//<![CDATA[

    if (document.getElementById && document.createElement) {
        roundBorder('outerbox');
    }

<?php if ($records['reportType'] == 'Emp') { // Emp report data: Begins ?>

	YAHOO.OrangeHRM.autocomplete.ACJSArray = new function() {
	   	// Instantiate first JS Array DataSource
	   	this.oACDS = new YAHOO.widget.DS_JSArray(employees);

	   	// Instantiate AutoComplete for txtEmployeeSearch
	   	this.oAutoComp = new YAHOO.widget.AutoComplete('txtEmployeeSearch','employeeSearchACContainer', this.oACDS);
	   	this.oAutoComp.prehighlightClassName = "yui-ac-prehighlight";
	   	this.oAutoComp.typeAhead = false;
	   	this.oAutoComp.useShadow = true;
	   	this.oAutoComp.minQueryLength = 1;
	   	this.oAutoComp.textboxFocusEvent.subscribe(function(){
	   	    var sInputValue = YAHOO.util.Dom.get('txtEmployeeSearch').value;
	   	    if(sInputValue.length === 0) {
	   	        var oSelf = this;
	   	        setTimeout(function(){oSelf.sendQuery(sInputValue);},0);
	   	    }
	   	});
	};

<?php } // Emp report data: Ends ?>

//]]>
</script>
