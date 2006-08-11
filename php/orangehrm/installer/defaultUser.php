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
	
document.frmInstall.actionResponse.value  = 'DEFUSERINFO';
document.frmInstall.submit();
}
</script>

<table cellspacing="0" cellpadding="0" border="0" align="center">
<tr>
    <th width="400">Step 4: Admin User Creation</th>
</tr>
<tr>
    <td colspan="2" width="600">	
        <p>After OrangeHRM is configured you will need an Adminstrator Account to Login into OrangeHRM. 
        Please fill in how you want to login as Adminstrator</p>
    </td>
</tr>
<table width="100%" cellpadding="0" cellpadding="0" border="0">
<tr><th colspan="3" align="left">Admin User Creation</td></tr>
<tr>
	<td>OrangeHRM Admin User Name</td>
	<td><input type="text" name="OHRMAdminUserName" value="Admin"></td>
</tr>
<tr>
	<td>OrangeHRM Admin User Password</td>
	<td><input type="password" name="OHRMAdminPassword" value=""></td>
</tr>
<tr>
	<td></td>
	<td><input type="button" value="OK" onclick="submitDefUserInfo()"></td>
</tr>
</table>
</table>