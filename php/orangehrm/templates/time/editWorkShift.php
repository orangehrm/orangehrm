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

$workshift = $records[0];
$assignedEmployees = $records[1];
$availableEmployees = $records[2];

?>
<style type="text/css">
@import url("../../themes/beyondT/css/octopus.css");

.roundbox {
	margin-top: 10px;
	margin-left: 10px;
	width:300px;
}

label {
	width: 80px;
}

.roundbox_content {
	padding:5px 5px 20px 5px;
}

input[type=checkbox] {
	background-color: transparent;
	margin: 0px;
	margin-top: 5px;
	margin-bottom: 5px;
	width: 12px;
	vertical-align: bottom;
}

#txtHoursPerDay {
	width: 2em;
}

#addPanel {
	display: block;
}
</style>
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script type="text/javascript" src="../../scripts/octopus.js"></script>

<script type="text/javascript">
var baseUrl = '?timecode=Time&action=';

function goBack() {
	location.href = "./CentralController.php?timecode=Time&action=View_Work_Shifts";
}

function upateShift() {
	err=false;
	msg='<?php echo $lang_Error_PleaseCorrectTheFollowing; ?>\n\n';

	if ($('txtShiftName').value.trim() == '') {
		err=true;
		msg+="\t- <?php echo $lang_Time_Error_SpecifyWorkShiftName; ?>\n";
	}

	if ($('txtHoursPerDay').value.trim() == '') {
		err=true;
		msg+="\t- <?php echo $lang_Time_Error_SpecifyHoursPerDay; ?>\n";
	} else if (0 >= $('txtHoursPerDay').value.trim()) {
		err=true;
		msg+="\t- <?php echo $lang_Time_Error_HoursPerDayShouldBePositive; ?>\n";
	}

	if (err) {
		alert(msg);

		return false;
	}

	$('frmEditWorkShift').action=baseUrl+'Edit_Work_Shift';
	$('frmEditWorkShift').submit();
}

</script>
<h2>
<?php echo $lang_Time_WorkShifts; ?>
<hr/>
</h2>
<div class="navigation">
	<img title="Back" onMouseOut="this.src='../../themes/beyondT/pictures/btn_back.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_back_02.jpg';"  src="../../themes/beyondT/pictures/btn_back.jpg" onClick="goBack();">
</div>
<div id="editPanel">
	<form name="frmEditWorkShift" id="frmEditWorkShift" method="post" action="?timecode=Time&action=">
		<div class="roundbox">
			<label for="txtShiftName"><span class="error">*</span> <?php echo $lang_Time_ShiftName; ?></label>
	        <input type="text" id="txtShiftName" name="txtShiftName" tabindex="1" value="<?php echo $workshift->getName(); ?>"/>
			<br/>
	        <label for="txtHoursPerDay"><span class="error">*</span> <?php echo $lang_Time_HoursPerDay; ?></label>
	        <input type="text" id="txtHoursPerDay" name="txtHoursPerDay" tabindex="2" size="3" value="<?php echo $workshift->getHoursPerDay(); ?>"/>
	        <br>
	        <label for="none">&nbsp;</label>
	        <input type="hidden" id="txtShiftId" name="txtShiftId" value="<?php echo $workshift->getWorkshiftId(); ?>"/>
	        <img onClick="upateShift();"
	             style="margin-top:10px;"
	             onMouseOut="this.src='../../themes/beyondT/pictures/btn_save.jpg';"
	             onMouseOver="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';"
	             src="../../themes/beyondT/pictures/btn_save.jpg">
	   	</div>
	</form>
</div>
<script type="text/javascript">
<!--
    if (document.getElementById && document.createElement) {
 			initOctopus();
	}
 -->
</script>
<?php
if (isset($_GET['message']) && !empty($_GET['message'])) {

	$expString  = $_GET['message'];
	$expString = explode ("_",$expString);
	$length = count($expString);

	$col_def=strtolower($expString[$length-1]);
	$expString='lang_Time_Errors_'.$_GET['message'];

	$message = isset($$expString) ? $$expString : $_GET['message'];
?>
	<font class="<?php echo $col_def?>" size="-1" face="Verdana, Arial, Helvetica, sans-serif">
<?php echo $message; ?>
	</font>
<?php }	?>
<br/>
<div id="listOfShifts" >
  <form id="frmListOfShifts" name="frmListOfShifts" method="post" action="?timecode=Time&action=">
	<table border="0" cellpadding="0" cellspacing="0">
		<thead>
			<tr>
				<th class="tableTopLeft"></th>
				<th class="tableTopMiddle"></th>
		    	<th class="tableTopMiddle"></th>
		    	<th class="tableTopMiddle"></th>
				<th class="tableTopRight"></th>
			</tr>
			<tr>
				<th class="tableMiddleLeft"></th>
		    	<th width="25px"></th>
		    	<th width="200px"><?php echo $lang_Time_ShiftName; ?></th>
		    	<th width="150px"><?php echo $lang_Time_HoursPerDay; ?></th>
				<th class="tableMiddleRight"></th>
			</tr>
			<tr>
				<th class="tableMiddleLeft"></th>
		    	<th class="tableMiddleMiddle"></th>
		    	<th class="tableMiddleMiddle"></th>
		    	<th class="tableMiddleMiddle"></th>
				<th class="tableMiddleRight"></th>
			</tr>
		</thead>
		<tbody>
		<?php
		if (false) {
			$i=0;
			foreach ($workshifts as $workshift) {
				if(!($i%2)) {
					$cssClass = 'odd';
			 	} else {
			 		$cssClass = 'even';
			 	}
			 	$i++;
		?>
			<tr>
				<td class="tableMiddleLeft"></td>
		    	<td class="<?php echo $cssClass; ?>"><input type="checkbox" id="deleteShift[]" name="deleteShift[]" value="<?php echo $workshift->getWorkshiftId(); ?>" /></td>
		    	<td class="<?php echo $cssClass; ?>"><a href="?timecode=Time&action=Edit_Work_Shift&id=<?php echo $workshift->getWorkshiftId(); ?>"><?php echo $workshift->getName(); ?></a></td>
		    	<td class="<?php echo $cssClass; ?>"><?php echo $workshift->getHoursPerDay(); ?></td>
				<td class="tableMiddleRight"></td>
			</tr>
		<?php
			}
		} else {
		?>
			<tr>
				<td class="tableMiddleLeft"></td>
				<td></td>
		    	<td colspan="2"><?php echo $lang_Error_NoRecordsFound; ?></td>
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
				<td class="tableBottomRight"></td>
			</tr>
	  	</tfoot>
	</table>
  </form>
</div>
