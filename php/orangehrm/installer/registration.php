<script language="JavaScript">
function login() {
	
	document.frmInstall.actionResponse.value = 'LOGIN';
	document.frmInstall.submit();
	return;
}

function regInfo() {
	
	if(!document.frmInstall.chkRegister.checked) {
		document.frmInstall.actionResponse.value  = 'LOGIN';
		document.frmInstall.submit();
		return;
	}

	frm = document.frmInstall;
	if(frm.userName.value == '') {
		alert('Please fill the Name Field');
		frm.userName.focus();
		return;
	}
	
	if(frm.txtEmail.value == '') {
		alert('Email Fiel Empty!');
		frm.txtEmail.focus();
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
        <p>Registration allows you to receive upto date information on OrangeHRM(releases, updates, etc.)</p>
        
    </td>
</tr>
<? if(isset($reqAccept)) { ?>
<tr>
    <td colspan="2" width="600">	
    <? if($reqAccept) { ?>
	        <p>Registration information was collected, and Succesfully sent to OrangeHRM.com</p>
    <? } else { ?>
    	    <p>Registration information was collected, and NOT sent to OrangeHRM.com, click Retry to try again, or proceed to login into OrangeHRM</p>
    <? } ?>
    </td>
</tr>
<? } ?>
<table width="100%" cellpadding="0" cellpadding="0" border="0">
<tr>
	<td>Do you want to register</td>
	<td><input type="checkbox" name="chkRegister" value="1"></td>
</tr>
<tr>
	<td>Name</td>
	<td><input type="text" name="userName" ></td>
</tr>
<tr>
	<td>Email</td>
	<td><input type="text" name="userEmail"></td>
</tr>
<tr>
	<td>Comments</td>
	<td><textarea name="userComments"></textarea></td>
</tr>
<tr>
	<td>Updates/Newsletter</td>
	<td><input type="checkbox" name="chkUpdates" value="1"></td>
</tr>
<tr>
	<td></td>
<? if(!isset($reqAccept)) { ?>
	<td><input type="button" value="OK" onclick="regInfo()"></td>
<? } elseif($reqAccept) { ?>
	<td><input type="button" value="Login to OrangeHRM" onclick="login()"></td>
<? } else { ?>
	<td><input type="button" value="Re-send" onclick="regInfo()"></td>
	<td><input type="button" value="Skip" onclick="login()"></td>
<? } ?>
</tr>
</table>
</table>