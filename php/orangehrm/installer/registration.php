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
	var messages = '';
	if(frm.userName.value == '') {
		messages += "\n" + ' - Enter last name ';
	}
	if(frm.company.value == '') {
		messages += "\n" + ' - Enter company name';  
    }

	var reg = /^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/;

	if(frm.userEmail.value == '') {
		messages += "\n" + ' - Enter e-mail address';
	} else if (!reg.test(frm.userEmail.value)) {
		messages += "\n" + ' - Invalid e-mail address';
	}

	if (messages != '') {
        alert('Please correct the following error(s)' + messages);
        return;
    }

document.frmInstall.actionResponse.value  = 'REGINFO';
document.frmInstall.submit();
document.frmInstall.btnRegister.disabled = true;
}
</script>
<link href="style.css" rel="stylesheet" type="text/css" />

<style>
        ul.registration li { 
        	color:#dc8701; 
        	height: 11px;
        }
        ul.registration li span { 
            color:black;           
        }
        
        .registration {           
            	
        }
        .wrapper {
        	display: block;
        }
        
        .wrapper_content_div {
        	float: left;
            margin: 5px 30px 0px 0px; 
        }
		.clear {
			clear:both;
		}
}
</style>

<div style="display: block;" class="wrapper">
	<h2>Step 7: Registration</h2>
	 <p>You have sucessfully installed OrangeHRM, please take a moment to register.</p>
	<div class="wrapper" style="width: 900px;">
    <div class="wrapper_content_div">
        <h3>Benefits of Registration</h3>
	        <ul class="registration">
	            <li><span> Upgrades to new releases</span></li>
	            <li><span>Receive patches for bug fixes</span></li>
	            <li><span>Notification of new software updates</span></li>
	            <li><span>Prioritize your support queries</span></li>
	            <li><span>Receive OrangeHRM newsletter and other useful updates</span></li>
	        </ul>
    </div>


        <?php if(isset($reqAccept)) {

		if($reqAccept) { ?>
	        <p>Registration information was collected, and Succesfully sent to OrangeHRM.com</p>
        <?php } else { ?>
    	    <p class="error">Registration information was collected, but NOT sent to OrangeHRM.com, please click Retry to try again, or click Skip to proceed and login into OrangeHRM</p>
            <?php }
	}

	if(!isset($reqAccept) || (!$reqAccept)) { ?>
	<div class="wrapper_content_div">
	<h3>Detail</h3>
    <table cellpadding="0" cellspacing="0" border="0">
      <tr>
        <td class="tdComponent_n">First name</td>
        <td class="tdValues_n"><input type="text" name="firstName" tabindex="1" value="<?php echo isset($_POST['firstName'])? $_POST['firstName'] : ''?>"/></td>
      </tr>
      <tr>
        <td class="tdComponent_n">Last name <span class="required">*</span></td>
        <td class="tdValues_n"><input type="text" name="userName" tabindex="2" value="<?php echo isset($_POST['userName'])? $_POST['userName'] : ''?>"/></td>
      </tr>
      <tr>
        <td class="tdComponent_n">Company<span class="required">*</span></td>
        <td class="tdValues_n"><input type="text" name="company" tabindex="3" value="<?php echo isset($_POST['company'])? $_POST['company'] : ''?>"/></td>
      </tr>
      <tr>
      <tr>
      <tr>
        <td class="tdComponent_n">Email<span class="required">*</span></td>
        <td class="tdValues_n"><input type="text" name="userEmail" tabindex="4" value="<?php echo isset($_POST['userEmail'])? $_POST['userEmail'] : ''?>"/></td>
      </tr>
      <tr>
        <td class="tdComponent_n">Telephone</td>
        <td class="tdValues_n"><input type="text" name="userTp" tabindex="5" value="<?php echo isset($_POST['userTp'])? $_POST['userTp'] : ''?>"/></td>
      </tr>
      <tr>
        <td class="tdComponent_n">Comments</td>
        <td class="tdValues_n"><textarea name="userComments" tabindex="6"><?php echo isset($_POST['userComments'])? $_POST['userComments'] : ''?></textarea></td>
      </tr>
      <tr>
        <td class="tdComponent_n">Updates/Newsletter</td>
        <td class="tdValues_n"><input type="checkbox" name="chkUpdates" value="1" tabindex="7" <?php echo (isset($_POST['chkUpdates']) && ($_POST['chkUpdates'] == 1)) ? 'checked' : ''?> /></td>
      </tr>
      <tr>
        <td>
           
       
        </td>
      </tr>
      <tr>
        <td colspan="2" style="padding-top: 10px;">
            <span class="required"> * </span>Required Fields      
        </td>
      </tr>
</table>
</div>
</div>
<br style="clear: both"/>
<?php }  ?>
  <div style="margin-left: 490px; padding-top: 20px;">
<?php   if(!isset($reqAccept)) { ?>

        <div>
	        <input name="button" type="button" onclick="noREG();" value="No thanks!" tabindex="9"/>
	        <input name="btnRegister" type="button" onclick="regInfo();" value="Register" tabindex="8"/>
        </div>
        <?php } elseif($reqAccept) {?>
        <input name="button" type="button" onclick="login();" value="Login to OrangeHRM" tabindex="10"/>
        <?php } else { ?>
        <input name="button" type="button" onclick="noREG();" value="Skip" tabindex="11"/>
        <input name="btnRegister" type="button" onclick="regInfo();" value="Retry" tabindex="1"/>
        <?php } 
?>
 </div>    
</div>
<br class="clear"/>
