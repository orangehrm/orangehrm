<script language="JavaScript">
function login() {
	
	document.frmInstall.actionResponse.value = 'LOGIN';
	document.frmInstall.submit();
	return;
}

function regInfo() {
	
	frm = document.frmInstall;
	if(frm.userName.value == '') {
		alert('Please fill the Name Field');
		frm.userName.focus();
		return;
	}
	
	if(frm.txtEmail.value == '') {
		alert('Email Field Empty!');
		frm.txtEmail.focus();
		return;
	}
	
document.frmInstall.actionResponse.value  = 'REGINFO';
document.frmInstall.submit();
}
</script>
<link href="style.css" rel="stylesheet" type="text/css" />


<div id="content">
	<h2>Step 7: Registration</h2>

        <p>You have sucessfully installed OrangeHRM, please take a moment to register.</p>
        <p>Registration allows you to receive upto date information on OrangeHRM(releases, updates, etc.)</p>
        
   
<? if(isset($reqAccept)) { 

		if($reqAccept) { ?>
	        <p>Registration information was collected, and Succesfully sent to OrangeHRM.com</p>
        <? } else { ?>
    	    <p>Registration information was collected, and NOT sent to OrangeHRM.com, click Retry to try again, or proceed to login into OrangeHRM</p>
    <? } 
	} ?>
    <table cellpadding="0" cellspacing="0" border="0" class="table">
	  <tr>
	 	<th colspan="3" align="left">Details</th>
	  </tr>
      <tr>
        <td class="tdComponent_n">Name</td>
        <td class="tdValues_n"><input type="text" name="userName" /></td>
      </tr>
      <tr>
        <td class="tdComponent_n">Email</td>
        <td class="tdValues_n"><input type="text" name="userEmail" /></td>
      </tr>
      <tr>
        <td class="tdComponent_n">Comments</td>
        <td class="tdValues_n"><textarea name="userComments"></textarea></td>
      </tr>
      <tr>
        <td class="tdComponent_n">Updates/Newsletter</td>
        <td class="tdValues_n"><input type="checkbox" name="chkUpdates" value="1" /></td>
      </tr>     
</table>
	<br />
	
        <? if(!isset($reqAccept)) { ?>
        <input name="button" type="button" onclick="regInfo()" value="Register" />
        <input name="button" type="button" onclick="login()" value="No thanks!" />
        <? } elseif($reqAccept) { ?>
        <input name="button" type="button" onclick="login()" value="Login to OrangeHRM" />
        <? } else { ?>
        <input name="button" type="button" onclick="regInfo()" value="Re-send" />
        <input name="button" type="button" onclick="login()" value="Skip" />
        <? } ?>
         
