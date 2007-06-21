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
?>
<script language="JavaScript">

function submitDefUserInfo() {

	frm = document.frmInstall;
	if(frm.OHRMAdminUserName.value.length < 5) {
		alert('OrangeHRM Admin User-name should be at least 5 char. long!');
		frm.OHRMAdminUserName.focus();
		return;
	}

	if(frm.OHRMAdminPassword.value == '') {
		alert('OrangeHRM Admin Password left Empty!');
		frm.OHRMAdminPassword.focus();
		return;
	}

	if(frm.OHRMAdminPassword.value != frm.OHRMAdminPasswordConfirm.value) {
		alert('OrangeHRM Admin Password and Confirm OrangeHRM Admin Password don\'t match!');
		frm.OHRMAdminPassword.focus();
		return;
	}

document.frmInstall.actionResponse.value  = 'DEFUSERINFO';
document.frmInstall.submit();
}
</script>
<link href="style.css" rel="stylesheet" type="text/css" />


<div id="content">
	<h2>Step 4: Admin User Creation</h2>

        <p>After OrangeHRM is configured you will need an Administrator Account to Login into OrangeHRM. <br />
        Please fill in the Username and User Password for the Administrator login. </p>

        <table cellpadding="0" cellspacing="0" border="0" class="table">
<tr><th colspan="3" align="left">Admin User Creation</th></tr>
<tr>
	<td class="tdComponent_n">OrangeHRM Admin Username</td>
	<td class="tdValues_n"><input type="text" name="OHRMAdminUserName" value="Admin" tabindex="1"/></td>
</tr>
<tr>
	<td class="tdComponent_n">OrangeHRM Admin User Password</td>
	<td class="tdValues_n"><input type="password" name="OHRMAdminPassword" value="" tabindex="2"/></td>
</tr>
<tr>
	<td class="tdComponent_n">Confirm OrangeHRM Admin User Password</td>
	<td class="tdValues_n"><input type="password" name="OHRMAdminPasswordConfirm" value="" tabindex="3"/></td>
</tr>

</table><br />
<input class="button" type="button" value="Back" onclick="back();" tabindex="5"/>
<input type="button" value="Next" onclick="submitDefUserInfo()" tabindex="4"/>
</div>