<script language="JavaScript">
function login() {	
	document.frmInstall.actionResponse.value = 'LOGIN';
	document.frmInstall.submit();
}

function regInfo() {
	
	frm = document.frmInstall;
	if(frm.userName.value == '') {
		alert('Please fill the Name Field');
		frm.userName.focus();
		return;
	}
	
	if(frm.userEmail.value == '') {
		alert('Email Field Empty!');
		frm.userEmail.focus();
		return;
	}
	
document.frmInstall.actionResponse.value  = 'REGINFO';
document.frmInstall.submit();
document.frmInstall.btnRegister.disabled = true;
}
</script>
<link href="style.css" rel="stylesheet" type="text/css" />


<div id="content">
	<h2>Step 7: Registration</h2>

        <p>You have sucessfully installed OrangeHRM, please take a moment to register.</p>
        <p>By registering you will be kept Up to Date and receive information on OrangeHRM (releases, updates, etc.).</p>
        
   
        <? if(isset($reqAccept)) { 

		if($reqAccept) { ?>
	        <p>Registration information was collected, and Succesfully sent to OrangeHRM.com</p>
        <? } else { ?>
    	    <p class="error">Registration information was collected, but NOT sent to OrangeHRM.com, please click Retry to try again, or click Skip to proceed and login into OrangeHRM</p>
            <? } 
	} 
	
	if(!isset($reqAccept) || (!$reqAccept)) { ?>        
    <table cellpadding="0" cellspacing="0" border="0" class="table">
	  <tr>
	 	<th colspan="3" align="left">Details</th>
	  </tr>
      <tr>
        <td class="tdComponent_n">Name</td>
        <td class="tdValues_n"><input type="text" name="userName" tabindex="2" value="<?=isset($_POST['userName'])? $_POST['userName'] : ''?>"/></td>
      </tr>
      <tr>
        <td class="tdComponent_n">Email</td>
        <td class="tdValues_n"><input type="text" name="userEmail" tabindex="3" value="<?=isset($_POST['userEmail'])? $_POST['userEmail'] : ''?>"/></td>
      </tr>
      <tr>
        <td class="tdComponent_n">Comments</td>
        <td class="tdValues_n"><textarea name="userComments" tabindex="4"><?=isset($_POST['userComments'])? $_POST['userComments'] : ''?></textarea></td>
      </tr>
      <tr>
        <td class="tdComponent_n">Updates/Newsletter</td>
        <td class="tdValues_n"><input type="checkbox" name="chkUpdates" value="1" tabindex="5" <?=(isset($_POST['chkUpdates']) && ($_POST['chkUpdates'] == 1)) ? 'checked' : ''?> /></td>
      </tr>     
</table>
<? } ?>
	<br />
	
        <? if(!isset($reqAccept)) { ?>        
        <input name="button" type="button" onclick="login();" value="No thanks!" tabindex="7"/>
		<input name="btnRegister" type="button" onclick="regInfo();" value="Register" tabindex="6"/>
        <? } elseif($reqAccept) { ?>
        <input name="button" type="button" onclick="login();" value="Login to OrangeHRM" tabindex="8"/>
        <? } else { ?>        
        <input name="button" type="button" onclick="login();" value="Skip" tabindex="9"/>
        <input name="btnRegister" type="button" onclick="regInfo();" value="Retry" tabindex="1"/>
        <? } ?>
</div>
