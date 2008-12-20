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
$role = $records[0];
$employees = $records[1];
$pendingTimesheets = $records[2];
$pending = $records[3];

?>
<script type="text/javascript">
<!--
var initialAction = "<?php echo $_SERVER['PHP_SELF']; ?>?timecode=Time&action=";

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

	frmObj.action= initialAction+'View_Timesheet';
	frmObj.submit();
}

function viewTimesheet(id) {
	frmObj = document.getElementById("frmTimesheet");

	frmObj.action= initialAction+'View_Timesheet&id='+id;
	frmObj.submit();
}
-->
</script>
<div class="formpage">
    <div class="outerbox">
        <div class="mainHeading"><h2><?php echo $lang_Time_Select_Employee_Title;?></h2></div>
    
    <?php if (isset($_GET['message'])) {    
            $message =  $_GET['message'];
            $messageType = CommonFunctions::getCssClassForMessage($message);
            $message = 'lang_Time_Errors_' . $message;
    ?>
        <div class="messagebar">
            <span class="<?php echo $messageType; ?>"><?php echo (isset($$message)) ? $$message: ""; ?></span>
        </div>  
    <?php } ?>
 
<form name="frmEmp" id="frmTimesheet" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?timecode=Time&action=">
<table border="0" cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<th></th>
	    	<th></th>
	    	<th></th>
	    	<th></th>
	    	<th></th>
	    	<th></th>
	    	<th></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td></td>
			<td ><?php echo $lang_Leave_Common_EmployeeName; ?></td>
			<td></td>
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
		 		  	<option value="<?php echo $employee[0] ?>"><?php echo $employee[1]; ?></option>
		  			<?php 	}
		   				} ?>
				</select>
			</td>
		<?php } ?>
			<td></td>
			<td>
                <input type="submit" class="viewbutton" id="btnView" name="btnView" onclick="view(); return false;"
                    onmouseover="moverButton(this);" onmouseout="moutButton(this);"                          
                    value="<?php echo $lang_Common_View;?>" />                                              
            </td>
			<td></td>
			<td></td>
		</tr>
	</tbody>
	<tfoot>
	  	<tr>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
  	</tfoot>
</table>

<?php
	if ($pending) {
?>
<div class="subHeading"><h3><?php echo $lang_Time_Select_Employee_SubmittedTimesheetsPendingSupervisorApproval; ?></h3></div>
<table border="0" cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<th></th>
	    	<th></th>
	    	<th></th>
	    	<th></th>
	    	<th></th>
	    	<th></th>
			<th></th>
		</tr>
		<tr>
			<th></th>
			<th width="100px"><?php echo $lang_Leave_Common_EmployeeName; ?></th>
			<th></th>
			<th width="150px"><?php echo $lang_Time_Select_Employee_TimesheetPeriod; ?></th>
			<th></th>
			<th></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php if (is_array($employees)) {
		   		foreach ($employees as $employee) {
		   			if (is_array($pendingTimesheets[$employee[0]])) {
		   				foreach ($pendingTimesheets[$employee[0]] as $timesheet) {
		?>
		<tr>
			<td></td>
			<td><?php echo $employee[1];?></td>
			<td>&nbsp;</td>
			<td><?php echo preg_replace(array('/#date/'), array(LocaleUtil::getInstance()->formatDate($timesheet->getStartDate())), $lang_Time_Select_Employee_WeekStartingDate); ?></td>
			<td>
                <input type="submit" class="viewbutton" 
                       onclick="viewTimesheet(<?php echo $timesheet->getTimesheetId(); ?>); return false;"                
                    onmouseover="moverButton(this);" onmouseout="moutButton(this);"                          
                    value="<?php echo $lang_Common_View;?>" /> 
			</td>
			<td>&nbsp;</td>
			<td></td>
		</tr>
		<?php
		   				}
		   			}
		   		}
			}
		?>
	</tbody>
	<tfoot>
	  	<tr>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
  	</tfoot>
</table>
<?php
}
?>
</form>
</div>
<script type="text/javascript">
//<![CDATA[
    if (document.getElementById && document.createElement) {
        roundBorder('outerbox');                
    }
//]]>
</script>
</div>