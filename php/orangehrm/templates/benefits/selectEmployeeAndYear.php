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

$heading = $records[0];
$years = $records[1];
$action = $records[2];
?>
<h2><?php echo ${"lang_Benefits_$heading"}; ?><hr/></h2>
<script type="text/javascript">
	function validate() {
		err = false;
		errors = "<?php echo $lang_Error_PleaseCorrectTheFollowing; ?>\n\n";

		if (document.frmSelectEmployee.year.value == -1) {
			errors += "-  <?php echo $lang_Error_PleaseSelectAYear; ?>\n";
			err = true;
		}
		if (document.frmSelectEmployee.id.value == -1) {
			errors += "-  <?php echo $lang_Error_PleaseSelectAnEmployee; ?>\n";
			err = true;
		}

		if (err) {
			errors = errors+"\n";
			alert(errors);
		} else {
			window.location = $('frmSelectEmployee').action+'&year='+$('year').value+'&employeeId='+$('id').value;
		}
	}

	function returnEmpDetail(){
		var popup=window.open('../../templates/hrfunct/emppop.php?reqcode=REP&LEAVE=SUMMARY','Employees','height=450,width=400');
        if(!popup.opener) popup.opener=self;
		popup.focus();
	}
</script>
<form method="post" name="frmSelectEmployee" id="frmSelectEmployee" action="?benefitcode=Benefits&amp;action=<?php echo $action; ?>" onsubmit="validate(); return false;">
<input type="hidden" name="searchBy" value="leaveType"/>
<table border="0" cellpadding="2" cellspacing="0">
  <tbody>
  	<tr>
		<td class="tableTopLeft"></td>
    	<td class="tableTopMiddle"></td>
    	<td class="tableTopMiddle"></td>
    	<td class="tableTopMiddle"></td>
		<td class="tableTopRight"></td>
	</tr>
	<tr>
		<td class="tableMiddleLeft"></td>
    	<td width="70px" class="odd"><?php echo $lang_Benefits_Common_Year;?></td>

    	<td width="130px" class="odd">
		    	  <select name="year" id="year">
		    	    <option value="-1"> - <?php echo $lang_Benefits_Common_Select;?> - </option>
		   <?php
		   		if (is_array($years)) {
		   			foreach ($years as $year) {
		  ?>
		 		  	<option value="<?php echo $year ?>"><?php echo $year ?></option>
		  <?php 	}
		   		}
		 ?>
  	    		  </select>
   	    </td>
		<td></td>
   	    <td class="tableMiddleRight"></td>
   	</tr>
   	<?php
   	/**
   	 * This table row is for the spacing
   	 */
   	?>
   	<tr>
		<td class="tableMiddleLeft"></td>
		<td width="90px" class="odd"></td>
		<td></td>
		<td></td>
		<td class="tableMiddleRight"></td>
	</tr>
	<tr>
		<td class="tableMiddleLeft"></td>
    	<td width="180px" class="odd"><?php echo $lang_Benefits_Common_EmployeeName;?></td>
    	<td width="180px" class="odd">
    		<input type="text" name="cmbEmpID" id="cmbEmpID" disabled />
			<input type="hidden" name="id" id="id" value="-1" />
			<input type="button" value="..." onclick="returnEmpDetail();" />
		</td>
		<td></td>
		<td class="tableMiddleRight"></td>
	</tr>
	<?php
   	/**
   	 * This table row is for the spacing
   	 */
   	?>
	<tr>
		<td class="tableMiddleLeft"></td>
		<td width="90px" class="odd"></td>
		<td></td>
		<td></td>
		<td class="tableMiddleRight"></td>
	</tr>
	<tr>
		<td class="tableMiddleLeft"></td>
		<td></td>
		<td width="100px" class="odd"><input type="image" name="btnView" onclick="view('employee');" src="../../themes/beyondT/icons/view.gif" onmouseover="this.src='../../themes/beyondT/icons/view_o.gif';" onmouseout="this.src='../../themes/beyondT/icons/view.gif';" /></td>
		<td></td>
    	<td class="tableMiddleRight"></td>
	</tr>

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