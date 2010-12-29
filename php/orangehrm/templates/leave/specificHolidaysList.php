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

 if (isset($modifier[1])) {
 	$dispYear = $modifier[1];
 }

 $modifier = $modifier[0];
 $rights = $_SESSION['localRights'];

 if (isset($modifier) && ($modifier == "Taken")) {
 	$empInfo = $records[count($records)-1][0];
 	$employeeName = $empInfo[2].' '.$empInfo[1];

 	array_pop($records);

 	$records = $records[0];
 }

if ($modifier === "SUP") {
 $lang_Title = $lang_Leave_Leave_list_Title1;
} else if ($modifier === "Taken") {
 $lang_Title = preg_replace(array('/#employeeName/', '/#dispYear/'), array($employeeName, $dispYear) , $lang_Leave_Leave_list_Title2);
} else {
 $lang_Title = $lang_Leave_Leave_list_Title3;
}

?>
<script type="text/javascript">
//<![CDATA[

	function actionAdd() {
		document.frmDeleteHolidays.action = '?leavecode=Leave&action=Holiday_Specific_View_Add';
 		document.frmDeleteHolidays.submit();
	}

	function actionEdit() {
		document.frmDeleteHolidays.action = '?leavecode=Leave&action=Holiday_Specific_View_Edit';
 		document.frmDeleteHolidays.submit();
	}

	function actionDelete() {
		$check = 0;
		with (document.frmDeleteHolidays) {
			for (var i=0; i < elements.length; i++) {
				if ((elements[i].type == 'checkbox') && (elements[i].checked == true)){
					$check = 1;
				}
			}
		}

		if ( $check == 1 ){

			var res = confirm("<?php echo $lang_Error_DoYouWantToDelete; ?>");

			if(!res) return;

			document.frmDeleteHolidays.action = '?leavecode=Leave&action=Holiday_Specific_Delete';
 			document.frmDeleteHolidays.submit();
		}else{
			alert("<?php echo $lang_Error_SelectAtLeastOneRecordToDelete; ?>");
		}
	}


	function doHandleAll() {
		with (document.frmDeleteHolidays) {
			if(elements['allCheck'].checked == false){
				doUnCheckAll();
			}
			else if(elements['allCheck'].checked == true){
				doCheckAll();
			}
		}
	}


	function doCheckAll() {
		with (document.frmDeleteHolidays) {
			for (var i=0; i < elements.length; i++) {
				if (elements[i].type == 'checkbox') {
					elements[i].checked = true;
				}
			}
		}
	}


	function doUnCheckAll() {
		with (document.frmDeleteHolidays) {
			for (var i=0; i < elements.length; i++) {
				if (elements[i].type == 'checkbox') {
					elements[i].checked = false;
				}
			}
		}
	}

	function editRecord() {

 		document.DefineLeaveType.action = '?leavecode=Leave&action=Leave_Type_Edit';
 		document.DefineLeaveType.submit();
	}

	/**
	 * If at least one day is unchecked, main check box would be unchecked
	 */

	function unCheckMain() {
		noOfCheckboxes = 0;
		noOfCheckedCheckboxes = 0;

		with ($('frmDeleteHolidays')) {
			for (i = 0; i < elements.length; i++) {
				if (elements[i].type == 'checkbox' && elements[i].name != 'allCheck') {
					noOfCheckboxes++;
					if (elements[i].checked == true) {
						noOfCheckedCheckboxes++;
					}

				}
			}
		}

		$('allCheck').checked = (noOfCheckboxes == noOfCheckedCheckboxes);
	}

//]]>
</script>
<div class="outerbox">
<form id="frmDeleteHolidays" name="frmDeleteHolidays" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?leavecode=Leave&action=">
    <div class="mainHeading"><h2><?php echo $lang_Leave_Leave_Holiday_Specific_Title; ?></h2></div>

<?php
 if (isset($_GET['message']) &&!empty($_GET['message'])) {
?>
    <div class="messagebar">
        <span><?php echo CommonFunctions::escapeHtml($_GET['message']); ?></span>
    </div>
<?php } ?>

    <div class="actionbar">
        <div class="actionbuttons">
        	<?php $disabled = ($rights['add']) ? '' : 'disabled="disabled"'; ?>
            <input type="button" class="addbutton" <?php echo $disabled; ?>
                name="btnAdd" id="btnAdd" onclick="actionAdd(); return false;"
                onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                value="<?php echo $lang_Common_Add;?>" />

              <?php /* Show delete button only if records are available: Begins */
              if (count($records) > 0) {
              ?>
              	<?php $disabled = ($rights['delete']) ? '' : 'disabled="disabled"'; ?>
                <input type="button" class="delbutton" onclick="actionDelete(); return false;"
                    name="btnDel" id="btnDel" <?php echo $disabled; ?>
                    onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                    value="<?php echo $lang_Common_Delete;?>" />
              <?php /* Show delete button only if records are available: Ends */
              }
              ?>
        </div>
        <div class="noresultsbar"><?php echo (!is_array($records)) ? $lang_Error_NoRecordsFound : '';?></div>
        <div class="pagingbar"></div>
    <br class="clear" />
    </div>
    <br class="clear" />


<?php /* Show table only if records are available: Begins */
if (count($records) > 0) {
?>
<table border="0" cellpadding="0" cellspacing="0" class="data-table">
  <thead>
	<tr>
		<td width="50px"><input type="checkbox" name="allCheck" id="allCheck" value="" onclick="doHandleAll();" /></td>
    	<td scope="col"><?php echo $lang_Leave_Common_NameOfHoliday;?></td>
    	<td scope="col"><?php echo $lang_Leave_Common_Date;?></td>
    	<td scope="col"><?php echo $lang_Leave_Common_Length;?></td>
    	<td scope="col"><?php echo $lang_Leave_Common_Recurring;?></td>
	</tr>
  </thead>
  <tbody>
<?php
	$j = 0;
	if (is_array($records))
		foreach ($records as $record) {
			if(!($j%2)) {
				$cssClass = 'odd';
			 } else {
			 	$cssClass = 'even';
			 }
			 $j++;

?>
  <tr>
  	<td class="<?php echo $cssClass; ?>">
  		<input type="checkbox" name="deletHoliday[]" value="<?php echo $record->getHolidayId(); ?>"
  			onchange="unCheckMain();" onclick="unCheckMain();" />
  	</td>
    <td class="<?php echo $cssClass; ?>" style="padding-right: 20px;"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?leavecode=Leave&action=Holiday_Specific_View_Edit&id=<?php echo $record->getHolidayId(); ?>"><?php echo $record->getDescription(); ?></a></td>
    <td class="<?php echo $cssClass; ?>"><?php echo LocaleUtil::getInstance()->formatDate($record->getDate()); ?></td>
    <td class="<?php echo $cssClass; ?>"><?php
    		$leaveLength = null;
    		switch ($record->getLength()) {
    			case Leave::LEAVE_LENGTH_FULL_DAY 	 		:	$leaveLength = $lang_Leave_Common_FullDay;
    													break;
    			case Leave::LEAVE_LENGTH_HALF_DAY	:	$leaveLength = $lang_Leave_Common_HalfDay;
    													break;
    		}

    		echo $leaveLength;
    ?></td>
    <td class="<?php echo $cssClass; ?>"><?php echo ($record->getRecurring() == Holidays::HOLIDAYS_RECURRING) ? $lang_Common_Yes: $lang_Common_No;?>
    </td>
  </tr>

<?php
		}
?>
  </tbody>
</table>
<?php /* Show table only if records are available: Ends */
}
?>
<?php 	if ($modifier !== "Taken") { ?>

</form>
<?php   }
	  ?>
</div>
<script type="text/javascript">
//<![CDATA[
    if (document.getElementById && document.createElement) {
        roundBorder('outerbox');
    }
//]]>
</script>