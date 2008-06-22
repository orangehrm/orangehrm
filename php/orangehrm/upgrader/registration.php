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
function login() {
	document.frmInstall.actionResponse.value = 'LOGIN';
	document.frmInstall.submit();
}

function noREG() {
	document.frmInstall.actionResponse.value = 'NOREG';
	document.frmInstall.submit();
}


function regInfo() {

	frm = document.frmInstall;
	if(frm.userName.value == '') {
		alert('Please fill the Name Field');
		frm.userName.focus();
		return;
	}

	var reg = /^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/;

	if(frm.userEmail.value == '') {
		alert('Email Field Empty!');
		frm.userEmail.focus();
		return;
	} else if (!reg.test(frm.userEmail.value)) {
		alert('Invalid E-mail Address!');
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

        <p>You have sucessfully upgraded to OrangeHRM 2.3-beta.3, please take a moment to register.</p>
        <p>By registering you will be kept Up to Date and receive information on OrangeHRM (releases, updates, etc.).</p>


        <?php if(isset($reqAccept)) {

		if($reqAccept) { ?>
	        <p>Registration information was collected, and Succesfully sent to OrangeHRM.com</p>
        <?php } else { ?>
    	    <p class="error">Registration information was collected, but NOT sent to OrangeHRM.com, please click Retry to try again, or click Skip to proceed and login into OrangeHRM</p>
            <?php }
	}

	if(!isset($reqAccept) || (!$reqAccept)) { ?>
    <table cellpadding="0" cellspacing="0" border="0" class="table">
	  <tr>
	 	<th colspan="3" align="left">Details</th>
	  </tr>
      <tr>
        <td class="tdComponent_n">Name</td>
        <td class="tdValues_n"><input type="text" name="userName" tabindex="2" value="<?php echo isset($_POST['userName'])? $_POST['userName'] : ''?>"/></td>
      </tr>
      <tr>
        <td class="tdComponent_n">Email</td>
        <td class="tdValues_n"><input type="text" name="userEmail" tabindex="3" value="<?php echo isset($_POST['userEmail'])? $_POST['userEmail'] : ''?>"/></td>
      </tr>
      <tr>
        <td class="tdComponent_n">Comments</td>
        <td class="tdValues_n"><textarea name="userComments" tabindex="4"><?php echo isset($_POST['userComments'])? $_POST['userComments'] : ''?></textarea></td>
      </tr>
      <tr>
        <td class="tdComponent_n">Updates/Newsletter</td>
        <td class="tdValues_n"><input type="checkbox" name="chkUpdates" value="1" tabindex="5" <?php echo (isset($_POST['chkUpdates']) && ($_POST['chkUpdates'] == 1)) ? 'checked' : ''?> /></td>
      </tr>
</table>
<?php } ?>
	<br />

        <?php if(!isset($reqAccept)) { ?>
        <input name="button" type="button" onclick="noREG();" value="No thanks!" tabindex="7"/>
		<input name="btnRegister" type="button" onclick="regInfo();" value="Register" tabindex="6"/>
        <?php } elseif($reqAccept) { ?>
        <input name="button" type="button" onclick="login();" value="Login to OrangeHRM" tabindex="8"/>
        <?php } else { ?>
        <input name="button" type="button" onclick="noREG();" value="Skip" tabindex="9"/>
        <input name="btnRegister" type="button" onclick="regInfo();" value="Retry" tabindex="1"/>
        <?php } ?>
</div>