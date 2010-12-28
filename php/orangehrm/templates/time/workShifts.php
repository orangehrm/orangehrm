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

$workshifts = $records[0];
$rights = $records['rights'];

$disabledAttribute = 'disabled="disabled"';
$token = $records['token'];
unset($records['token']);
?>

<script type="text/javascript" src="../../scripts/archive.js"></script>

<script type="text/javascript">
//<![CDATA[
var baseUrl = '?timecode=Time&action=';

function actionShowAdd() {
	$('addPanel').style.display = 'block';
	$('frmAddWorkShift').reset();
}

function cancelAddShift() {
	$('addPanel').style.display = 'none';
	$('frmAddWorkShift').reset();
}

function addShift() {
	err=false;
	msg='<?php echo $lang_Error_PleaseCorrectTheFollowing; ?>\n\n';

	if ($('txtShiftName').value.trim() == '') {
		err=true;
		msg+="\t- <?php echo $lang_Time_Error_SpecifyWorkShiftName; ?>\n";
	}

	var hoursPerDay = $('txtHoursPerDay').value.trim();
	if ( hoursPerDay == '') {
		err=true;
		msg+="\t- <?php echo $lang_Time_Error_SpecifyHoursPerDay; ?>\n";
	} else if (isNaN(hoursPerDay)) {
		err=true;
		msg+="\t- <?php echo $lang_Time_Error_HoursPerDayShouldBeANumericValue; ?>\n";
	} else if (0 >= hoursPerDay) {
		err=true;
		msg+="\t- <?php echo $lang_Time_Error_HoursPerDayShouldBePositiveNumber; ?>\n";
	} else if (hoursPerDay > 24) {
		err=true;
		msg+="\t- <?php echo $lang_Time_Error_HoursPerDayShouldBeLessThan24; ?>\n";
	}

	if (err) {
		alert(msg);

		return false;
	}

	$('frmAddWorkShift').action=baseUrl+'Add_Work_Shift';
	$('frmAddWorkShift').submit();
}

function actionDelete() {
	with (document.frmListOfShifts) {
		check=false;

		for (var i=0; elements.length>i; i++) {
			if ((elements[i].type == 'checkbox') && (elements[i].checked == true)) {
				check = true;
				break;
			}
		}

		if (check) {
			action=baseUrl+'Delete_Work_Shifts';
			submit();
		} else {
			alert("<?php echo $lang_Error_SelectAtLeastOneRecordToDelete; ?>");
		}
	}
}

function doHandleAll() {
	with (document.frmListOfShifts) {
		if(elements['allCheck'].checked == false){
			doUnCheckAll();
		}
		else if(elements['allCheck'].checked == true){
			doCheckAll();
		}
	}
}

function doCheckAll() {
	with (document.frmListOfShifts) {
		for (var i=0; i < elements.length; i++) {
			if (elements[i].type == 'checkbox') {
				elements[i].checked = true;
			}
		}
	}
}

function doUnCheckAll() {
	with (document.frmListOfShifts) {
		for (var i=0; i < elements.length; i++) {
			if (elements[i].type == 'checkbox') {
				elements[i].checked = false;
			}
		}
	}
}

//]]>
</script>
<div id="addPanel" class="outerbox" style="width:350px;display:none;">
<div class="mainHeading"><h2><?php echo $lang_Time_WorkShift_Add;?></h2></div>
	<form name="frmAddWorkShift" id="frmAddWorkShift" method="post" action="?timecode=Time&amp;action=">
      <input type="hidden" name="token" value="<?php echo $token;?>" />
		<div class="roundbox">
			<label for="txtShiftName"><?php echo $lang_Time_ShiftName; ?><span class="required">*</span></label>
	        <input type="text" id="txtShiftName" name="txtShiftName" tabindex="1" class="formInputText"/>
            <br class="clear"/>
	        <label for="txtHoursPerDay"> <?php echo $lang_Time_HoursPerDay; ?><span class="required">*</span></label>
	        <input type="text" id="txtHoursPerDay" name="txtHoursPerDay" tabindex="2" size="3" class="formInputText"
                style="width:30px;"/>
            <br class="clear"/>
	        <label for="none">&nbsp;</label>
	        <input type="hidden" id="none" name="none"/>
            <div class="formbuttons">
            	<?php $disabled = ($rights['add']) ? '' : $disabledAttribute; ?>
                <input type="button" class="addbutton" id="addBtn" <?php echo $disabled; ?>
                    onclick="addShift();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                    value="<?php echo $lang_Common_Add;?>" />
                <input type="button" class="cancelbutton" onclick="cancelAddShift();" <?php echo $disabled; ?>
                    onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                     value="<?php echo $lang_Common_Cancel;?>" />
            </div>
            <br class="clear"/>
	   	</div>
	</form>
</div>


<div class="formpage">
    <div class="outerbox">
        <div class="mainHeading"><h2><?php echo $lang_Time_WorkShifts;?></h2></div>

    <?php
        if (isset($_GET['message']) && !empty($_GET['message'])) {
            $message  = $_GET['message'];
            $messageType = CommonFunctions::getCssClassForMessage($message);
            $message = "lang_Time_Errors_" . $message;
    ?>
        <div class="messagebar">
            <span class="<?php echo $messageType; ?>"><?php echo (isset($$message)) ? $$message: CommonFunctions::escapeHtml($_GET['message']); ?></span>
        </div>
    <?php } ?>

   <div class="actionbar">
        <div class="actionbuttons">
        	<?php $disabled = ($rights['add']) ? '' : $disabledAttribute; ?>
            <input type="button" class="addbutton" <?php echo $disabled; ?>
                onclick="actionShowAdd();"
                name="btnAdd" id="btnAdd"
                onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                value="<?php echo $lang_Common_Add;?>" />
			<?php $disabled = ($rights['delete']) ? '' : $disabledAttribute; ?>
                <input type="button" class="delbutton" <?php echo $disabled; ?>
                    name="btnDel" id="btnDel" alt="Delete"
                    onclick="actionDelete();"
                    onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                    value="<?php echo $lang_Common_Delete;?>" />
        </div>
        <div class="noresultsbar"><?php echo (count($workshifts) == 0) ? $lang_Error_NoRecordsFound : '';?></div>
        <div class="pagingbar"></div>
    <br class="clear" />
    </div>
    <br class="clear" />
  <form id="frmListOfShifts" name="frmListOfShifts" method="post" action="?timecode=Time&amp;action=">
	<table border="0" cellpadding="0" cellspacing="0" class="data-table">
		<thead>
			<tr>
		    	<td width="25"><input type="checkbox" class="checkbox" name="allCheck" value="" onclick="doHandleAll();" />
		    	</td>
		    	<td width="200"><?php echo $lang_Time_ShiftName; ?></td>
		    	<td width="150"><?php echo $lang_Time_HoursPerDay; ?></td>
			</tr>
		</thead>
		<tbody>
		<?php
		if (count($workshifts) > 0) {
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
		    	<td class="<?php echo $cssClass; ?>"><input type="checkbox" id="deleteShift_<?php echo $i;?>" name="deleteShift[]" value="<?php echo $workshift->getWorkshiftId(); ?>" /></td>
		    	<td class="<?php echo $cssClass; ?>"><a href="?timecode=Time&amp;action=View_Edit_Work_Shift&amp;id=<?php echo $workshift->getWorkshiftId(); ?>"><?php echo $workshift->getName(); ?></a></td>
		    	<td class="<?php echo $cssClass; ?>"><?php echo $workshift->getHoursPerDay(); ?></td>
			</tr>
		<?php
			}
		}
		?>
		</tbody>
	</table>
  </form>
  </div>
</div>

<script type="text/javascript">
//<![CDATA[
    if (document.getElementById && document.createElement) {
        roundBorder('outerbox');
    }
//]]>
</script>

