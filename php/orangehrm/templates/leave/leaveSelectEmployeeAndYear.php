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
 
 $lan = new Language();
 
 require_once($lan->getLangPath("leave/leaveCommon.php")); 
 require_once($lan->getLangPath("leave/leaveSelectEmployee.php")); 
 
 if (isset($modifier)) {
 	switch ($modifier) {
 		case "summary" : $action = "?leavecode=Leave&action=Leave_Summary";
 						 break;
 		default 		: $action = "";
 						  break;
 	}
 }
 
 $years = $records[0];
 $employees = $records[1];
 
	if (isset($_GET['message'])) {
?>
<var><?php echo $_GET['message']; ?></var>
<?php } ?>
<h3><?php echo $lang_Title?><hr/></h3>
<script language="javascript">
	function validate() {
		errors = "";
		
		if (document.frmSelectEmployee.year.value == -1) {
			errors += "- Please select a Year\n";
		}
		if (document.frmSelectEmployee.id.value == -1) {
			errors += "- Please select an Employee \n";
		}
		
		if (errors != "") {
			errors = "Please correct the following\n\n"+errors+"\n";
			alert(errors);
		} else {
			document.frmSelectEmployee.submit();
		}
	}
</script>
<form method="post" name="frmSelectEmployee" action="<?php echo $action; ?>" onsubmit="validate(); return false;">
<table border="0" cellpadding="0" cellspacing="0">
  <tbody>
  	<tr>
		<th class="tableTopLeft"></th>	
    	<th class="tableTopMiddle"></th>
    	<th class="tableTopMiddle"></th>
    	<th class="tableTopMiddle"></th>
    	<th class="tableTopMiddle"></th>
    	<th class="tableTopMiddle"></th>
		<th class="tableTopRight"></th>	
	</tr>
	<tr>
		<th class="tableMiddleLeft"></th>	
    	<th width="70px" class="odd"><?php echo $lang_Year;?></th>
    	
    	<th width="130px" class="odd">
		    	  <select name="year">
		    	    <option value="-1"> - Select - </option>
		   <?php 		   		
		   		if (is_array($years)) {
		   			foreach ($years as $year) {
		  ?>
		 		  	<option value="<?php echo $year ?>"><?php echo $year ?></option>		
		  <?php 	}
		   		} 
		 ?>		   	
  	    		  </select>
   	    </th>
    	
    	<th width="180px" class="odd"><?php echo $lang_EmployeeName;?></th>
    	
    	<th width="150px" class="odd">
				<select name="id">
					<option value="-1"> - Select - </option>
					<?php 		   		
		   				if (is_array($employees)) {
		   						sort($employees);
		   					foreach ($employees as $employee) {
		  ?>
		 		  	<option value="<?php echo $employee[0] ?>"><?php echo $employee[1] ?></option>		
		  <?php 			}
		   				} 
		 ?>	
  	    		</select>
		</th>
    	<th width="100px" class="odd"><input type="image" name="btnView" src="../../themes/beyondT/pictures/btn_search.jpg" /></th>
		<th class="tableMiddleRight"></th>	
	</tr>
	<tr>
		<th class="tableMiddleLeft"></th>	
    	<th width="70px" class="odd"></th>
    	
    	<th width="130px" class="odd">
   	    </th>
    	
    	<th width="180px" class="odd"></th>
    	
    	<th width="150px" class="odd">
		</th>
    	<th width="100px" class="odd"></th>
		<th class="tableMiddleRight"></th>	
	</tr>
  </tbody>
  <tfoot>
  	<tr>
		<td class="tableBottomLeft"></td>
		<td class="tableBottomMiddle"></td>
		<td class="tableBottomMiddle"></td>
		<td class="tableBottomMiddle"></td>
		<td class="tableBottomMiddle"></td>
		<td class="tableBottomMiddle"></td>
		<td class="tableBottomRight"></td>
	</tr>
  </tfoot>
</table>
</form>