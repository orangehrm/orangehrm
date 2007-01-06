<?php
/*
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
 if (isset($modifier[1])) {
 	$dispYear = $modifier[1];
 }
 
 $modifier = $modifier[0];
 
 if (isset($modifier) && ($modifier == "Taken")) {
 	$empInfo = $records[count($records)-1][0]; 
 	$employeeName = $empInfo[2].' '.$empInfo[1];
 	
 	array_pop($records);
 	
 	$records = $records[0];
 } 
 $lan = new Language(); 
 
 require_once($lan->getLangPath("full.php")); 
 
if ($modifier === "SUP") {
 $lang_Title = $lang_Leave_Leave_list_Title1;
} else if ($modifier === "Taken") {
 $lang_Title = preg_replace(array('/#employeeName/', '/#dispYear/'), array($employeeName, $dispYear) , $lang_Leave_Leave_list_Title2);	
} else {
 $lang_Title = $lang_Leave_Leave_list_Title3;	
}
 
 if (isset($_GET['message'])) {
?>
<var><?php echo $_GET['message']; ?></var>
<?php } ?>
<h2><?php echo $lang_Leave_Leave_Holiday_Specific_Title; ?><hr/></h2>
<?php 
	if (!is_array($records)) { 
?>
	<h5><?php echo $lang_Error_NoRecordsFound; ?></h5>
<?php
	}
?>
<script>			
	
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
	
	
</script>
<form id="frmDeleteHolidays" name="frmDeleteHolidays" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?leavecode=Leave&action=">
<p class="navigation">
  
	  <input type="image" onmouseout="this.src='../../themes/beyondT/pictures/btn_add.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_add_02.jpg';" src="../../themes/beyondT/pictures/btn_add.jpg" name="btnAdd" id="btnAdd" onclick="actionAdd(); return false;"/>	
      <input type="image" onclick="actionDelete(); return false;" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg" name="btnDel" id="btnDel"/>
</p>
<table border="0" cellpadding="0" cellspacing="0">
  <thead>
  	<tr>
		<th class="tableTopLeft"></th>	
    	<th class="tableTopMiddle"></th>    	
    	<th class="tableTopMiddle"></th>
    	<th class="tableTopMiddle"></th>
    	<th class="tableTopMiddle"></th>
		<th class="tableTopRight"></th>	
	</tr>
	<tr>
		<th class="tableMiddleLeft"></th>	
		<th width="30px" class="tableMiddleMiddle"><input type="checkbox" name='allCheck' value='' onclick="doHandleAll();" /></th>
    	<th class="tableMiddleMiddle"><?php echo $lang_Leave_Common_NameOfHoliday;?></th>    	
    	<th width="90px" class="tableMiddleMiddle"><?php echo $lang_Leave_Common_Date;?></th>
    	<th width="135px" class="tableMiddleMiddle"><?php echo $lang_Leave_Common_Length;?></th>
		<th class="tableMiddleRight"></th>	
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
  	<td class="tableMiddleLeft"></td>
  	<td class="<?php echo $cssClass; ?>"><input type="checkbox" name="deletHoliday[]" value="<?php echo $record->getHolidayId(); ?>"/></th>
    <td class="<?php echo $cssClass; ?>" style="padding-right: 20px;"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?leavecode=Leave&action=Holiday_Specific_View_Edit&id=<?php echo $record->getHolidayId(); ?>"><?php echo $record->getDescription(); ?></a></td>   
    <td class="<?php echo $cssClass; ?>"><?php echo $record->getDate(); ?></td>    
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
		<td class="tableBottomMiddle"></td>
		<td class="tableBottomRight"></td>
	</tr>
  </tfoot>
</table>
<?php 	if ($modifier !== "Taken") { ?>

</form>
<?php   }
	  ?>