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
<script type="text/javascript">
//<![CDATA[
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
		var popup=window.open('../../templates/hrfunct/emppop.php?reqcode=REP&LEAVE=SUMMARY','Employees','height=450,width=400,scrollbars=1');
        if(!popup.opener) popup.opener=self;
		popup.focus();
	}
//]]>    
</script>
<div class="formpage">
    <div class="outerbox">
        <div class="mainHeading"><h2><?php echo ${"lang_Benefits_$heading"}; ?></h2></div>
  
        <form method="post" name="frmSelectEmployee" id="frmSelectEmployee" action="?benefitcode=Benefits&amp;action=<?php echo $action; ?>" onsubmit="validate(); return false;">
            <input type="hidden" name="searchBy" value="leaveType"/>
            <label for="year"><?php echo $lang_Benefits_Common_Year;?></label>
            <select name="year" id="year" class="formSelect" style="width:8em;">
                <option value="-1"> - <?php echo $lang_Benefits_Common_Select;?> - </option>
        		   <?php if (is_array($years)) {
        		   			foreach ($years as $year) { ?>
        		<option value="<?php echo $year ?>"><?php echo $year ?></option>
        		  <?php 	}
        		   		}
        		 ?>
            </select>
            <br class="clear"/>
            
            <label for="cmbEmpID"><?php echo $lang_Benefits_Common_EmployeeName;?></label>
            <input type="text" name="cmbEmpID" id="cmbEmpID" disabled="disabled" class="formInputText"/>
            <input type="hidden" name="id" id="id" value="-1" />
            <input type="button" value="..." onclick="returnEmpDetail();" class="empPopupButton"/>
            <br class="clear"/>
            <div class="formbuttons">                
                <input type="submit" class="viewbutton" id="btnView" name="btnView"
                    onmouseover="moverButton(this);" onmouseout="moutButton(this);"                          
                    value="<?php echo $lang_Common_View;?>" />                    
            </div>
        </form>
    </div>
    <script type="text/javascript">
        <!--
            if (document.getElementById && document.createElement) {
                roundBorder('outerbox');                
            }
        -->
    </script>
</div>