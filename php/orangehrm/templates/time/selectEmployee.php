<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 hSenid Software International Pvt. Ltd, http://www.hsenid.com
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
$role = $records[0];
$employees = $records[1];

?>
<script type="text/javascript">
<!--
function returnEmpDetail(){
		var popup=window.open('../../templates/hrfunct/emppop.php?reqcode=REP','Employees','height=450,width=400');
        if(!popup.opener) popup.opener=self;
		popup.focus();
}

function view() {
	empIdObj = document.getElementById("txtRepEmpID");
	if ((empIdObj.value == 0) || (empIdObj.value == '')) {
		alert('<?php echo $lang_Error_PleaseSelectAnEmployee; ?>');
		return false;
	}
	frmObj = document.getElementById("frmTimesheet");

	frmObj.action+='View_Timesheet';
	frmObj.submit();
}
-->
</script>
<h2><?php echo $lang_Time_Select_Employee_Title; ?>
	<hr>
</h2>
<?php if (isset($_GET['message'])) {

		$expString  = $_GET['message'];
		$expString = explode ("_",$expString);
		$length = count($expString);

		$col_def=strtolower($expString[$length-1]);

		$expString='lang_Time_Errors_'.$_GET['message'];
?>
		<font class="<?php echo $col_def?>" size="-1" face="Verdana, Arial, Helvetica, sans-serif">
<?php echo $$expString; ?>
		</font>
<?php }	?>
<form name="frmEmp" id="frmTimesheet" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?timecode=Time&action=">
<table border="0" cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<th class="tableTopLeft"></th>
	    	<th class="tableTopMiddle"></th>
	    	<th class="tableTopMiddle"></th>
	    	<th class="tableTopMiddle"></th>
	    	<th class="tableTopMiddle"></th>
	    	<th class="tableTopMiddle"></th>
	    	<th class="tableTopMiddle"></th>
			<th class="tableTopRight"></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td ><?php echo $lang_Leave_Common_EmployeeName; ?></td>
			<td>&nbsp;</td>
		<?php if ($role == authorize::AUTHORIZE_ROLE_ADMIN) { ?>
			<td ><input type="text" name="cmbRepEmpID" id="cmbRepEmpID" disabled />
				<input type="hidden" name="txtRepEmpID" id="txtRepEmpID" />
				<input type="button" value="..." onclick="returnEmpDetail();" />
			</td>
		<?php } else if ($role == authorize::AUTHORIZE_ROLE_SUPERVISOR) { ?>
			<td >
				<select name="txtRepEmpID" id="txtRepEmpID">
					<option value="-1">-<?php echo $lang_Leave_Common_Select;?>-</option>
					<?php if (is_array($employees)) {
		   					foreach ($employees as $employee) {
		  			?>
		 		  	<option value="<?php echo $employee[2] ?>"><?php echo $employee[1] ?></option>
		  			<?php 	}
		   				} ?>
				</select>
			</td>
		<?php } ?>
			<td>&nbsp;</td>
			<td><input type="image" name="btnView" onclick="view(); return false;" src="../../themes/beyondT/icons/view.jpg" onmouseover="this.src='../../themes/beyondT/icons/view_o.jpg';" onmouseout="this.src='../../themes/beyondT/icons/view.jpg';" /></td>
			<td>&nbsp;</td>
			<td class="tableMiddleRight"></tdh>
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
			<td class="tableBottomMiddle"></td>
			<td class="tableBottomRight"></td>
		</tr>
  	</tfoot>
</form>