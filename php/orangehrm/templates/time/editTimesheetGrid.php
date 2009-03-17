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

<div class="mainHeading">
<h2><?php echo $lang_Time_Timesheet_EditTimesheetForWeekStarting.' '.date('Y-m-d', $startDateStamp); ?></h2>
</div>    
    
<form id="frmTimesheet" name="frmTimesheet" method="post" action="/orangehrm/lib/controllers/CentralController.php?timecode=Time&id=4&action=">
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

<?php for ($i=$startDateStamp; $i<=$endDateStamp; $i=strtotime("+1 day", $i)) { ?>
			<th width="80px" class="tableMiddleMiddle"><?php echo date('l ' . LocaleUtil::getInstance()->getDateFormat(), $i); ?></th>
<?php } ?>

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
	
	
?>

		<tr id="row-<?php echo $k; ?>">
		
			<td class="tableMiddleLeft"></td>
			
			<td >
				<select id="cmbProject-<?php echo $k; ?>" name="cmbProject-<?php echo $k; ?>" onchange="fetchActivities(this.value, this.id)">
				<option value="-1">-- <?php echo $lang_Leave_Common_Select;?> --</option>
				
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
				<option value="-1">-- <?php echo $lang_Time_Timesheet_SelectProject;?> --</option>
				</select>
			</td>
				
<?php for ($i=$startDateStamp; $i<=$endDateStamp; $i=strtotime("+1 day", $i)) { ?>
			<td width="70px">
				<input type="text" name="txtDuration-<?php echo $k; ?>" 
				value="<?php echo (isset($value[$i])?$value[$i]['duration']:''); ?>" 
				size="5" maxlength="5" />
				<input type="hidden" name="hdnTimeEventId-<?php echo $k; ?>" 
				value="<?php echo (isset($value[$i])?$value[$i]['eventId']:''); ?>" />
			</td>
<?php } ?>
				
			<td class="tableMiddleRight"></td>
			
		</tr>

<?php 

	$k++;

} // Grid iteration: Ends ?>




			
<?php } else { // If Grid count is Zero ?>
	







		<tr id="row-1">
		
			<td class="tableMiddleLeft"></td>
			
			<td >
				<select id="cmbProject-1" name="cmbProject-1" onchange="fetchActivities(this.value, this.id)">
				<option value="-1">-- <?php echo $lang_Leave_Common_Select;?> --</option>
				
<?php for ($i=0; $i<$projectsCount; $i++) { // Project list : Begins ?>
				<option value="<?php echo $projectsList[$i]['id']; ?>"><?php echo $projectsList[$i]['name']; ?></option>
<?php } // Project list : Ends ?>
				
				</select>
			</td>
			
			<td>
				<select id="cmbActivity-1" name="cmbActivity-1">
				<option value="-1">-- <?php echo $lang_Time_Timesheet_SelectProject;?> --</option>
				</select>
			</td>
				
<?php for ($i=$startDateStamp; $i<=$endDateStamp; $i=strtotime("+1 day", $i)) { ?>
			<td width="70px">
				<input type="text" name="txtDuration" size="5" maxlength="5" />
			</td>
<?php } ?>
				
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
<input type="hidden" name="txtTimesheetId" value="4" />
<input type="hidden" name="txtEmployeeId" value="1" />
<input type="hidden" name="nextAction" value="View_Timesheet" />
<div class="formbuttons">
<input type="button" class="updatebutton"  
        onclick="actionUpdate(); return false;"
        onmouseover="moverButton(this);" onmouseout="moutButton(this);"
        name="btnUpdate" id="btnUpdate"                              
        value="Add Row" />         
<input type="button" class="resetbutton"  
        onclick="actionReset(); return false;"
        onmouseover="moverButton(this);" onmouseout="moutButton(this);"
        name="btnReset" id="btnReset"                              
        value="Save" />         
</p>

</form>
</div>
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
	
	
	
	
	
	
	totRows = 0;
	currFocus = $("cmbProject-1");
	currFocus.focus();
	if (document.getElementById && document.createElement) {
	    roundBorder('outerbox');                
	}
	
	//]]>
</script>