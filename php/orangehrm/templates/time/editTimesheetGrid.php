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
 *
 */

$grid = $records['grid'];
$gridCount = count($grid);
$projectsList = $records['projectsList'];
$projectsCount = count($projectsList);
$startDateStamp = $records['startDateStamp'];
$endDateStamp = $records['endDateStamp'];

if (isset($records['message'])) {

	$message = '';
	if ($records['message'] == 'update-success') {
		$message = $lang_Time_Errors_UPDATE_SUCCESS;
	} elseif ($records['message'] == 'update-failure') {
	    $message = $lang_Time_Errors_UPDATE_FAILURE;
	} elseif ($records['message'] == 'no-changes') {
	    $message = $lang_Time_Attendance_ReportNoChange;
	}

}

function compareConcatenatedName($a, $b){
    return strcmp($a["name"], $b["name"]);
}

//sort the array by customer name - project name
if (is_array($projectsList)) {
	usort($projectsList , "compareConcatenatedName");
}

?>

<style type="text/css">

.tableTopLeft {
    background: none;
}
.tableTopMiddle {
    background: none;
}
.tableTopRight {
    background: none;
}
.tableMiddleLeft {
    background: none;
}
.tableMiddleRight {
    background: none;
}
.tableBottomLeft {
    background: none;
}
.tableBottomMiddle {
    background: none;
}
.tableBottomRight {
    background: none;
}

td {
    text-align:center;
}

.durationTd {
    width:70px;
}

.selectTd {
    width:120px;
}

#frmTimegrid input[type=text] {
    border: 1px solid #888888;
    width: 50px;
}

#frmTimegrid select {
    width: 120px;
}

</style>

<div class="outerbox" style="width:980px">

<!-- Message box: Begins -->
<?php if (isset($records['message'])) { ?>
    <div class="messagebar">
        <span class="<?php echo $records['messageType']; ?>"><?php echo $message; ?></span>
    </div>
<?php } ?>
<!-- Message box: Ends -->

<div class="mainHeading">
<h2><?php echo $lang_Time_Timesheet_EditTimesheetForWeekStarting.' '.date('Y-m-d', $startDateStamp); ?></h2>
</div>

<form id="frmTimegrid" name="frmTimegrid" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?timecode=Time&action=Update_Timeesheet_Grid">
<table border="0" cellpadding="0" cellspacing="0" width="100%" id="tblTimegrid">
	<thead>

		<tr>
			<th class="tableTopLeft"></th>
	    	<th class="tableTopMiddle" width="120px"></th>
	    	<th class="tableTopMiddle" width="120px"></th>

<?php for ($i=$startDateStamp; $i<=$endDateStamp; $i=strtotime("+1 day", $i)) { ?>
			<th class="tableTopMiddle"></th>
<?php } ?>

			<th class="tableTopRight"></th>
		</tr>

		<tr>
			<th class="tableMiddleLeft"></th>
			<th class="tableMiddleMiddle">Project</th>
			<th class="tableMiddleMiddle">Activity</th>

<?php
	$datesCount = 0;
	for ($i=$startDateStamp; $i<=$endDateStamp; $i=strtotime("+1 day", $i)) { ?>
			<th class="tableMiddleMiddle">
			<?php echo date('l ' . LocaleUtil::getInstance()->getDateFormat(), $i); ?>
			<input type="hidden" name="hdnReportedDate-<?php echo $datesCount; ?>"
			value="<?php echo date('Y-m-d', $i); ?>" />
			</th>
<?php
	$datesCount++;
	}
?>

			<th class="tableMiddleRight"></th>

		</tr>

	</thead>

	<tbody>
<?php if ($gridCount > 0) { ?>
<?php
$k = 0;

foreach ($grid as $key => $value) { // Grid iteration: Begins

	$projectId = $value['projectId'];
	$activityId = $value['activityId'];
	$activityList = $value['activityList'];
	$activityCount = count($activityList);
?>
		<tr id="row-<?php echo $k; ?>">

			<td class="tableMiddleLeft"></td>

			<td class="selectTd" id="cmbProject-<?php echo $k; ?>-td">
				<?php
				$tags = '';
				if ($value['projectObj']->getDeleted()) {
					$tags ='style="display:none" disabled="disabled"';
				?>
					<span id="cmbProject-<?php echo $k; ?>-span">
						<a href="javascript: changeDeletedProject('cmbProject-<?php echo $k; ?>')"><?php echo $value['projectObj']->getProjectName(); ?></a>
						<span class="error">*</span>
						<input type="hidden" name="cmbProject-<?php echo $k; ?>" value="<?php echo $projectId; ?>" />
					</span>
				<?php } ?>
				<select id="cmbProject-<?php echo $k; ?>" name="cmbProject-<?php echo $k; ?>" <?php echo $tags; ?>
				id="cmbProject-<?php echo $k; ?>" onchange="fetchActivities(this.value, this.id)">
				<?php if ($value['projectObj']->getDeleted()) { ?>
				<option value="-1">-- <?php echo $lang_Time_Timesheet_SelectProject; ?> --</option>
				<?php } ?>
				<?php for ($j=0; $j<$projectsCount; $j++) { // Project list : Begins ?>
				<option value="<?php echo $projectsList[$j]['id']; ?>"
				<?php echo ($projectsList[$j]['id'] == $projectId) ? 'selected="selected"' : ''; ?>>
				<?php echo $projectsList[$j]['name']; ?>
				</option>
				<?php } // Project list : Ends ?>
				</select>
			</td>

			<td class="selectTd" id="cmbActivity-<?php echo $k; ?>-td">
				<?php
					$tags = '';
					if ($value['isActivityDeleted'] || $value['projectObj']->getDeleted()) {
					$tags ='style="display:none" disabled="disabled"';
				?>
					<span id="cmbActivity-<?php echo $k; ?>-span">
						<a href="javascript: changeDeletedActivity('cmbActivity-<?php echo $k; ?>')"><?php echo $value['activityName']; ?></a>
						<span class="error">*</span>
						<input type="hidden" name="cmbActivity-<?php echo $k; ?>" value="<?php echo $activityId; ?>" />
					</span>
				<?php } ?>
				<select id="cmbActivity-<?php echo $k; ?>" name="cmbActivity-<?php echo $k; ?>" <?php echo $tags; ?>
				id="cmbActivity-<?php echo $k; ?>">
				<?php if ($value['isActivityDeleted'] || $value['projectObj']->getDeleted()) { ?>
				<option value="-1">-- <?php echo $lang_Time_Timesheet_SelectProjectFirst; ?> --</option>
				<?php } ?>
<?php for ($j=0; $j<$activityCount; $j++) { ?>
				<option value="<?php echo $activityList[$j]->getId(); ?>"
				<?php echo (($activityList[$j]->getId() == $activityId) && !($value['isActivityDeleted'] || $value['projectObj']->getDeleted())) ? ' selected="selected"' : ''; ?>>
				<?php echo $activityList[$j]->getName(); ?>
				</option>
<?php } ?>

				</select>
			</td>
<?php
	$dCount = 0; // $datesCount is defined at <th> and is used in EXTRACTOR_TimeEvent. Therefore use $dCount to avoid conflicts
	for ($i=$startDateStamp; $i<=$endDateStamp; $i=strtotime("+1 day", $i)) { ?>
			<td calss="durationTd">
				<input type="text" name="txtDuration-<?php echo $k.'-'.$dCount; // Format: txtDuration-0-0 (RowCount-DatesCount) ?>"
				value="<?php echo (isset($value[$i])?$value[$i]['duration']:''); ?>" id="txtDuration-<?php echo $k.'-'.$dCount; ?>"
				maxlength="5" />

				<?php if(isset($value[$i])) { ?>
				<input type="hidden" name="hdnTimeEventId-<?php echo $k.'-'.$dCount; ?>"
				value="<?php echo $value[$i]['eventId']; ?>" />
				<input type="hidden" name="hdnDuration-<?php echo $k.'-'.$dCount; ?>"
				value="<?php echo $value[$i]['duration']; ?>" />
				<?php } ?>
			</td>
<?php
	$dCount++;
	}
?>

			<td class="tableMiddleRight"></td>

		<input type="hidden" name="hdnProject-<?php echo $k; ?>" value="<?php echo $projectId; ?>" />
		<input type="hidden" name="hdnActivity-<?php echo $k; ?>" value="<?php echo $activityId; ?>" />

		</tr>

<?php

	$k++;

} // Grid iteration: Ends ?>

<?php } else { // If Grid count is Zero ?>

		<tr id="row-0">

			<td class="tableMiddleLeft"></td>

			<td class="selectTd">
				<select id="cmbProject-0" name="cmbProject-0" id="cmbProject-0"
				onchange="fetchActivities(this.value, this.id)">
				<option value="-1">-- <?php echo $lang_Time_Timesheet_SelectProject;?> --</option>

<?php for ($i=0; $i<$projectsCount; $i++) { // Project list : Begins ?>
				<option value="<?php echo $projectsList[$i]['id']; ?>"><?php echo $projectsList[$i]['name']; ?></option>
<?php } // Project list : Ends ?>

				</select>
			</td>

			<td class="selectTd">
				<select style = "width:150px" id="cmbActivity-0" name="cmbActivity-0" id="cmbActivity-0">
				<option value="-1">-- <?php echo $lang_Time_Timesheet_SelectProjectFirst;?> --</option>
				</select>
			</td>

<?php
	$dCount = 0;
	for ($i=$startDateStamp; $i<=$endDateStamp; $i=strtotime("+1 day", $i)) { ?>
			<td class="durationTd">
				<input type="text" name="txtDuration-0-<?php echo $dCount; ?>"
				id="txtDuration-0-<?php echo $dCount; ?>" maxlength="5" />
			</td>
<?php
	$dCount++;
	}
?>

			<td class="tableMiddleRight"></td>

		</tr>

<?php } // Grid count checking ends ?>

	</tbody>

	<tfoot>

	  	<tr>
			<td class="tableBottomLeft"></td>
			<td class="tableBottomMiddle"></td>
			<td class="tableBottomMiddle"></td>

<?php for ($i=$startDateStamp; $i<=$endDateStamp; $i=strtotime("+1 day", $i)) { ?>
			<td class="tableBottomMiddle">
			</td>
<?php } ?>

			<td class="tableBottomRight"></td>
		</tr>
  	</tfoot>
</table>

<p id="controls">

<?php
/* Hidden data: Begins
 *
 * Some values prefix 'txt' instead of 'hdn' to comply with Extractors
 *
 * */

?>

<input type="hidden" name="txtEmployeeId" value="<?php echo $records['employeeId']; ?>" />
<input type="hidden" name="txtTimesheetId" value="<?php echo $records['timesheetId']; ?>" />
<input type="hidden" name="txtStartDate" value="<?php echo date('Y-m-d', $startDateStamp); ?>" />
<input type="hidden" name="txtEndDate" value="<?php echo date('Y-m-d', $endDateStamp); ?>" />

<input type="hidden" name="hdnGridCount" id="hdnGridCount" value="<?php echo ($gridCount==0?1:$gridCount); ?>" />
<input type="hidden" name="hdnDatesCount" id="hdnDatesCount" value="<?php echo $datesCount; ?>" />

<?php /* Hidden data: Ends */ ?>

<div class="formbuttons">

<input type="button" class="updatebutton"
        onclick="backToTimesheetView(); return false;"
        onmouseover="moverButton(this);" onmouseout="moutButton(this);"
        name="btnBack" id="btnBack"
        value="Back" />
<input type="button" class="updatebutton"
        onclick="addRow(); return false;"
        onmouseover="moverButton(this);" onmouseout="moutButton(this);"
        name="btnAddRow" id="btnAddRow"
        value="Add Row" />
<input type="button" class="longbtn"
        onclick="removeRow(); return false;"
        onmouseover="moverButton(this);" onmouseout="moutButton(this);"
        name="btnRemoveRow" id="btnRemoveRow"
        value="Remove Row" />
<input type="button" class="savebutton"
        onclick="actionUpdate(); return false;"
        onmouseover="moverButton(this);" onmouseout="moutButton(this);"
        name="btnSave" id="btnSave"
        value="Save" />
<input type="button" class="resetbutton"
		onclick="resetTimesheetGrid()"
		onmouseover="moverButton(this);" onmouseout="moutButton(this);"
		value="<?php echo $lang_Common_Reset; ?>" />
</div>

</form>

</div>

<div class="requirednotice">
	<?php echo sprintf($lang_Time_Timesheet_DeletedProjectsAndActivitiesNotice, '<span class="error">*</span>'); ?>
</div>

<form method="post" id="resetForm" action="<?php echo $_SERVER['PHP_SELF']; ?>?timecode=Time&action=Edit_Timesheet_Grid" style="display:none">
	<input type="hidden" name="txtTimesheetId" value="<?php echo $records['timesheetId']; ?>" />
	<input type="hidden" name="txtEmployeeId" value="<?php echo $records['employeeId']; ?>" />
	<input type="hidden" name="txtTimesheetPeriodId" value="<?php echo $records['timesheetPeriodId']; ?>" />
	<input type="hidden" name="txtStartDate" value="<?php echo date('Y-m-d', $records['startDateStamp']); ?>" />
	<input type="hidden" name="txtEndDate" value="<?php echo date('Y-m-d', $records['endDateStamp']); ?>" />
</form>

<script type="text/javascript">
	//<![CDATA[

	/* Populate project activities: Begins */

	var xmlHttp = null;

	function fetchActivities(projectId, rowId) {

		try { // Firefox, Opera 8.0+, Safari
	  		xmlHttp=new XMLHttpRequest();
	  	}
		catch(e) { // Internet Explorer

	  		try {
	    		xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
	    	}
	  		catch(e) {

	    		try {
	      			xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
	      		}
	    		catch(e) {
	      			alert("Your browser does not support AJAX!");
	      			return false;
	      		}
	    	}
	  	}

	  	var rowIdArr = rowId.split("-");

		xmlHttp.onreadystatechange = function() { populateActivities(rowIdArr[1]); };

		xmlHttp.open("GET", "<?php echo $_SERVER['PHP_SELF']; ?>?timecode=Time&action=Timegrid_Fetch_Activities&projectId="+projectId, true);
		xmlHttp.send(null);

	}

	function populateActivities(rowId){

		if(xmlHttp.readyState == 4){

			var combo = $('cmbActivity-'+rowId);
			combo.options.length = 0;
			var response = trimResponse(xmlHttp.responseText);

			if (response.length > 0) {

				var items = response.split(";");
				var count = items.length;

				for (var i=0;i<count;i++){

					var values = items[i].split("%");
					combo.options[i] = new Option(values[0],values[1]);

				}

			} else {

			    combo.options[0] = new Option('<?php echo $lang_Time_Timesheet_SelectProjectFirst;?>', '-1');

			}

		}

	}

	function trimResponse(value) {
	    return value.replace(/^\s+|\s+$/g,"");
	}

	/* Populate project activities: Ends */

	/* Going back to Timesheet view */

	function backToTimesheetView() {
		document.frmTimegrid.action = '<?php echo $_SERVER['PHP_SELF']; ?>?timecode=Time&action=View_Timesheet';
		document.frmTimegrid.submit();
	}

	/* Submitting Timegrid */

	function actionUpdate() {
		if (validateTimegrid()) {
			document.frmTimegrid.submit();
		}
	}

	/* Adding a row to grid: Begins */

	function addRow() {

		var tbody = $('tblTimegrid').getElementsByTagName('tbody')[0];
		var rowNo = tbody.rows.length;
		row = document.createElement('tr');

		/* Adding left most td */
		leftCell = document.createElement('td');
		leftCell.className = 'tableMiddleLeft';
		row.appendChild(leftCell);

		/* Adding projects select box */
		projectCell = document.createElement('td');

		selectName = 'cmbProject-'+ rowNo;

		projectSelect = '<select name="' + selectName + '" id="' + selectName + '"';
		projectSelect += 'onchange="fetchActivities($(\'' + selectName + '\').value, \'' + selectName + '\');"';
		projectSelect += '>';

		projectSelect += '<option value="-1">-- <?php echo $lang_Time_Timesheet_SelectProject;?> --</option>';
		<?php
		for ($i=0; $i<$projectsCount; $i++) {
		?>
		projectSelect += '<option value="<?php echo $projectsList[$i]['id']; ?>"><?php echo CommonFunctions::escapeForJavascript($projectsList[$i]['name']);?></option>';
		<?php
		}
		?>

		projectSelect += '</select>';

		projectCell.innerHTML = projectSelect;
		row.appendChild(projectCell);

		/* Adding activities select box */
		activityCell = document.createElement('td');
		var activitySelect = document.createElement('select');
		activitySelect.style.width= "150px";
		activitySelect.name = 'cmbActivity-'+ rowNo;
		activitySelect.id = 'cmbActivity-'+ rowNo;
		activitySelect.options[0] = new Option('-- <?php echo $lang_Time_Timesheet_SelectProjectFirst;?> --', '-1');
		activityCell.appendChild(activitySelect);
		row.appendChild(activityCell);

		/* Adding duration input boxes */

		<?php
		for ($i=0; $i<$datesCount; $i++) {
		?>

		durationCell = document.createElement('td');
		var durationInput = document.createElement('input');
		durationInput.type = 'text';
  		durationInput.name = 'txtDuration-' + rowNo + '-' + <?php echo $i; ?>;
  		durationInput.id = 'txtDuration-' + rowNo + '-' + <?php echo $i; ?>;
  		durationInput.maxLength = 5;
		durationCell.appendChild(durationInput);
		row.appendChild(durationCell);
		<?php
		}
		?>

		/* Adding right most td */
		rightCell = document.createElement('td');
		rightCell.className = 'tableMiddleRight';
		row.appendChild(rightCell);

		tbody.appendChild(row);

		/* Incrementing grid count (Grid count is used in EXTRACTOR_TimeEvent) */

		var gridCount =	$('hdnGridCount');
		gridCount.value = parseInt(gridCount.value) + 1;

	}

	/* Adding a row to grid: Ends */

	/* Removing a row from grid */

	function removeRow() {

		var tbody = $('tblTimegrid').getElementsByTagName('tbody')[0];
		var rowNo = tbody.rows.length;
		var gCount = <?php echo ($gridCount==0?1:$gridCount); // Can only remove rows added before saving ?>;

		if (rowNo > gCount) {

		    tbody.deleteRow(rowNo-1);

			/* Decrementing grid count (Grid count is used in EXTRACTOR_TimeEvent) */

			var gridCount =	$('hdnGridCount');
			gridCount.value = parseInt(gridCount.value) - 1;

		}

	}

	function changeDeletedProject(id) {
		$(id + '-td').removeChild($(id + '-span'));
		$(id).style.display = 'block';
		$(id).disabled = false;

		activitySelectBoxId = id.replace('Project', 'Activity');
		if ($(activitySelectBoxId).disabled) {
			changeDeletedProject(activitySelectBoxId);
		}
	}

	function changeDeletedActivity(id) {
		$(id + '-td').removeChild($(id + '-span'));
		$(id).style.display = 'block';
		$(id).disabled = false;

		projectSelectBoxId = id.replace('Activity', 'Project');
		if ($(projectSelectBoxId).disabled) {
			changeDeletedProject(projectSelectBoxId);
		}
	}

	function resetTimesheetGrid() {
		$('resetForm').submit();
	}

	/* Validating timegrid: Begins */

	function validateTimegrid() {

	    var gridCount =	$('hdnGridCount').value;
	    var datesCount = $('hdnDatesCount').value;

		/* Checking durations entered */

	    var pattern = /^\d+.?\d*$/;
	    var durationFlag = true;

	    for (var i=0; i<gridCount; i++) {

	        for (var j=0; j<datesCount; j++) {

	            var durationId = 'txtDuration-'+i+'-'+j;
	            var duration = $(durationId).value;

	            if (duration != '' && duration.match(pattern)==null) {
					durationFlag = false;
	            } else if (duration > 24) {
	                durationFlag = false;
	            }

	        }

	    }

	    if (!durationFlag) {
	        alert('<?php echo $lang_Time_Errors_INVALID_DURATION_FAILURE; ?>');
	        return false;
	    }

	    /* Checking for duplicate rows */

		var duplicateFlag = true;
	    var activities = new Array();
	    var duplicates = new Array();

	    for (var i=0; i<gridCount; i++) {

	    	var projectId = $('cmbProject-'+i).value;
	    	var activityId = $('cmbActivity-'+i).value;

	    	if (projectId > -1 && activityId > -1) { // Checking whether projectId and activityId are not negative

	    		var value = projectId+'-'+activityId;

		    	if (activities.length > 0) {

		    	    for (var j=0; j<i; j++) {

		    	        if (activities[j] == value) {
		    	        	duplicates[duplicates.length] = value;
		    	        } else {
		    	            activities[activities.length] = value;
		    	        }

		    	    }

		    	} else {
		    	    activities[activities.length] = value;
		    	}

	    	}

	    }

		if (duplicates.length > 0) {
		    alert('<?php echo $lang_Time_Errors_DUPLICATE_ROWS; ?>');
		    return false;
		}

		/* For checking all empty rows */

		var emptyProjectFlag = true;
		var emptyActivityFlag = true;

		for (var i=0; i<gridCount; i++) {

			var projectId = $('cmbProject-'+i).value;
			if (!($('cmbProject-'+i).disabled) && projectId == -1) {
			    emptyProjectFlag = false;
			}

			var activityId = $('cmbActivity-'+i).value;
			if (!($('cmbActivity-'+i).disabled) && activityId == -1) {
			    emptyActivityFlag = false;
			}

		}

		if (!emptyProjectFlag) {
		    alert('<?php echo $lang_Time_Errors_NO_PROJECT_SELECTED; ?>');
		    return false;
		}

		if (!emptyActivityFlag) {
		    alert('<?php echo $lang_Time_Errors_NO_ACTIVITY_SELECTED; ?>');
		    return false;
		}

		/* Checking whether day's total duration is more than 24 hours */

		for (var i=0; i<datesCount; i++) {

			var dayTotal = 0;

		    for (var j=0; j<gridCount; j++) {

		        dayTotal = dayTotal + Number($('txtDuration-'+j+'-'+i).value);

		    }

		    if (dayTotal > 24) {
		        alert('<?php echo $lang_Time_Errors_MaxTotalDuration; ?>');
		        return false;
		    }

		}

		return true;

	}

	/* Validating timegrid: Ends */

	/* Making table corners round */

	currFocus = $("cmbProject-0");
	currFocus.focus();
	if (document.getElementById && document.createElement) {
	    roundBorder('outerbox');
	}

	//]]>
</script>
