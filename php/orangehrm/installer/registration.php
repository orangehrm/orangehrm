<script language="JavaScript">

function regInfo() {
	
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
	
document.frmInstall.actionResponse.value  = 'REGINFO';
document.frmInstall.submit();
}
</script>

<table cellspacing="0" cellpadding="0" border="0" align="center">
<tr>
    <th width="400">Step 6: Registration</th>
</tr>
<tr>
    <td colspan="2" width="600">	
        <p>You have sucessfully installed OrangeHRM, please take a moment to register.</p>
        <p>Registration allows you to receive upto date information on OrangeHRM</p>
    </td>
</tr>
<table width="100%" cellpadding="0" cellpadding="0" border="0">
<tr>
	<td>Do you want to register</td>
	<td><input type="checkbox" name="chkRegister" onclick="dis" value="1"></td>
</tr>
<tr>
	<td>Name</td>
	<td><input type="text" disabled name="userName" ></td>
</tr>
<tr>
	<td>Email</td>
	<td><input type="text" disabled name="txtEmail"></td>
</tr>
<tr>
	<td>Comments</td>
	<td><textarea disabled name="userComments"></textarea></td>
</tr>
<tr>
	<td>Updates/Newsletter</td>
	<td><input type="checkbox" disabled name="chkUpdates" value="1"></td>
</tr>
<tr>
	<td></td>
	<td><input type="button" value="OK" onclick="regInfo()"></td>
</tr>
</table>
</table>