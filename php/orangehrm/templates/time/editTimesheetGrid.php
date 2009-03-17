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

input[type=text] {
    border: 1px solid #888888;
}

td {
    text-align:center;
}

</style>

<div class="outerbox" style="width:980px">

<!-- Message box: Begins -->
<?php if (isset($records['message'])) { ?>
    <div class="messagebar">
        <span class="<?php echo $records['messageType']; ?>"><?php echo $records['message']; ?></span>
    </div>
<?php } ?>
<!-- Message box: Ends -->

<div class="mainHeading">
<h2><?php echo $lang_Time_Timesheet_EditTimesheetForWeekStarting.' '.date('Y-m-d', $startDateStamp); ?></h2>
</div>    
    
<form id="frmTimesheet" name="frmTimesheet" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?timecode=Time&action=Update_Timeesheet_Grid">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<thead>

		<tr>
			<th class="tableTopLeft"></th>
	    	<th class="tableTopMiddle" width="120px"></th>
	    	<th class="tableTopMiddle" width="120px"></th>

<?php for ($i=$startDateStamp; $i<=$endDateStamp; $i=strtotime("+1 day", $i)) { ?>
			<th width="80px" class="tableTopMiddle"></th>
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
			<th width="80px" class="tableMiddleMiddle">
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
			
			<td >
				<select id="cmbProject-<?php echo $k; ?>" name="cmbProject-<?php echo $k; ?>" onchange="fetchActivities(this.value, this.id)">
				
<?php for ($j=0; $j<$projectsCount; $j++) { // Project list : Begins ?>
				<option value="<?php echo $projectsList[$j]['id']; ?>" 
				<?php echo ($projectsList[$j]['id']==$projectId?'selected':''); ?>>
				<?php echo $projectsList[$j]['name']; ?>
				</option>
<?php } // Project list : Ends ?>
				
				</select>
			</td>
			
			<td>
				<select id="cmbActivity-<?php echo $k; ?>" name="cmbActivity-<?php echo $k; ?>">

<?php for ($j=0; $j<$activityCount; $j++) { ?>
				<option value="<?php echo $activityList[$j]->getId(); ?>" 
				<?php echo ($activityList[$j]->getId()==$activityId?'selected':''); ?>>
				<?php echo $activityList[$j]->getName(); ?>
				</option>
<?php } ?>

				</select>
			</td>
				
<?php 
	$dCount = 0; // $datesCount is defined at <th> and is used in EXTRACTOR_TimeEvent. Therefore use $dCount to avoid conflicts
	for ($i=$startDateStamp; $i<=$endDateStamp; $i=strtotime("+1 day", $i)) { ?>
			<td width="70px">
				<input type="text" name="txtDuration-<?php echo $k.'-'.$dCount; // Format: txtDuration-0-0 (RowCount-DatesCount) ?>" 
				value="<?php echo (isset($value[$i])?$value[$i]['duration']:''); ?>" 
				size="5" maxlength="5" />
				
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
			
			<td >
				<select id="cmbProject-0" name="cmbProject-0" onchange="fetchActivities(this.value, this.id)">
				<option value="-1">-- <?php echo $lang_Leave_Common_Select;?> --</option>
				
<?php for ($i=0; $i<$projectsCount; $i++) { // Project list : Begins ?>
				<option value="<?php echo $projectsList[$i]['id']; ?>"><?php echo $projectsList[$i]['name']; ?></option>
<?php } // Project list : Ends ?>
				
				</select>
			</td>
			
			<td>
				<select id="cmbActivity-0" name="cmbActivity-0">
				<option value="-1">-- <?php echo $lang_Time_Timesheet_SelectProject;?> --</option>
				</select>
			</td>
				
<?php 
	$dCount = 0;
	for ($i=$startDateStamp; $i<=$endDateStamp; $i=strtotime("+1 day", $i)) { ?>
			<td width="70px">
				<input type="text" name="txtDuration-1-<?php echo $dCount; ?>" size="5" maxlength="5" />
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

<input type="hidden" name="hdnGridCount" value="<?php echo $gridCount; ?>" />
<input type="hidden" name="hdnDatesCount" value="<?php echo $datesCount; ?>" />

<?php /* Hidden data: Ends */ ?>

<div class="formbuttons">

<input type="button" class="updatebutton"  
        onclick="actionUpdate(); return false;"
        onmouseover="moverButton(this);" onmouseout="moutButton(this);"
        name="btnUpdate" id="btnUpdate"                              
        value="Add Row" />         
<input type="button" class="resetbutton"  
        onclick="actionUpdate(); return false;"
        onmouseover="moverButton(this);" onmouseout="moutButton(this);"
        name="btnReset" id="btnReset"                              
        value="Save" />         
</div>

</p>

</form>

</div>

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
		
			var combo = document.getElementById('cmbActivity-'+rowId);	
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
			
			    combo.options[0] = new Option('<?php echo $lang_Time_Timesheet_NoProjects;?>', '-1');
			    
			}
	
		}
	
	}
	
	function trimResponse(value) {
	    return value.replace(/^\s+|\s+$/g,"");
	}
	
	/* Populate project activities: Ends */
	
	
	function actionUpdate() {
		document.frmTimesheet.submit();
	}
	
	
	
	currFocus = $("cmbProject-0");
	currFocus.focus();
	if (document.getElementById && document.createElement) {
	    roundBorder('outerbox');                
	}
	
	//]]>
</script>