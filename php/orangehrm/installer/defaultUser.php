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
<link href="style.css" rel="stylesheet" type="text/css" />


<div id="content">
	<h2>Step 4: Admin User Creation</h2>

        <p>After OrangeHRM is configured you will need an Adminstrator Account to Login into OrangeHRM. 
        Please fill in how you want to login as Adminstrator</p>
    
<table cellpadding="0" cellspacing="0" border="0" class="table">
<tr><th colspan="3" align="left">Admin User Creation</th></tr>
<tr>
	<td class="tdComponent_n">OrangeHRM Admin User Name</td>
	<td class="tdValues_n"><input type="text" name="OHRMAdminUserName" value="Admin"></td>
</tr>
<tr>
	<td class="tdComponent_n">OrangeHRM Admin User Password</td>
	<td class="tdValues_n"><input type="password" name="OHRMAdminPassword" value=""></td>
</tr>

</table><br />
<input class="button" type="button" value="Back" onclick="back();">
<input type="button" value="Next" onclick="submitDefUserInfo()">
</div>