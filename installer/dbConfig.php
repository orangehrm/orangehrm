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

function dosubmit(){
    document.frmInstall.submit();
}

function disableFields() {

	if(document.frmInstall.chkSameUser.checked) {
		document.frmInstall.dbOHRMUserName.disabled = true;
		document.frmInstall.dbOHRMPassword.disabled = true;
	} else {
		document.frmInstall.dbOHRMUserName.disabled = false;
		document.frmInstall.dbOHRMPassword.disabled = false;
	}

}

function changePortField() {
	portModifier = document.getElementById('dbHostPortModifier');
	port = document.getElementById('dbHostPort');

	if (portModifier.value == "port") {
		port.maxLength = '5';
		port.size = '4';
		port.value = "3306";
	} else if (portModifier.value == "socket") {
		port.maxLength = '256';
		port.size = 40;
		port.value = "socket:/var/lib/mysql/mysql.sock";
	}
}

function submitDBInfo() {

	frm = document.frmInstall;
	if(frm.dbHostName.value == '') {
		alert('DB Hostname left Empty!');
		frm.dbHostName.focus();
		return;
	}

	if(frm.dbHostPort.value == '') {
		alert('DB Host Port left Empty!');
		frm.dbHostPort.focus();
		return;
	}

	if(frm.dbName.value == '') {
		alert('DB Name left Empty!');
		frm.dbName.focus();
		return;
	}

<?php if ($_SESSION['cMethod'] == 'new') { ?>

	if(frm.dbUserName.value == '') {
		alert('DB User-name left Empty');
		frm.dbUserName.focus();
		return;
	}

	if(!document.frmInstall.chkSameUser.checked && frm.dbOHRMUserName.value == '') {
		alert('OrangeHRM DB User-name left Empty');
		frm.dbOHRMUserName.focus();
		return;
	}

<?php } ?>

document.frmInstall.actionResponse.value  = 'DBINFO';
document.frmInstall.submit();
}

</script>

<link href="style.css" rel="stylesheet" type="text/css" />

<div id="content">
	<h2>Step 2: Database Configuration</h2>

<?php if(isset($error)) { ?>
	<font color="Red">
	    <?php if($error == 'WRONGDBINFO') {
	    		$msg = '';
	    		if(isset($_SESSION['mysqlErrNo']) && $_SESSION['mysqlErrNo'] == '1045') {

					if (isset($_SESSION['errorMsg'])) {
						$msg = $_SESSION['errorMsg'] . '. ';
					}

					$msg .= 'Please Check Privileged Database Username and Password Correct.';

	    		}else if(isset($_SESSION['mysqlErrNo']) && $_SESSION['mysqlErrNo'] == '2003'){
	    			if (isset($_SESSION['errorMsg'])) {
						$msg = $_SESSION['errorMsg'] . '. ';
					}

					$msg .= 'Please Make Sure MySQL Server Is Up And Running.';
	    		} else {
	    			$msg = "Unable to Connect to MySQL server. Please check MySQL server is running and DB Information given are correct";
	    		}
	    		echo $msg;

	       } elseif ($error == 'WRONGDBVER') {
	       	 	echo "You need at least MySQL 4.1.x, Detected MySQL ver " . $mysqlHost;
	       } elseif ($error == 'DBEXISTS') {
	       	 	echo "Database (" . $_SESSION['dbInfo']['dbName'] . ") already exists";
	       } elseif ($error == 'DBUSEREXISTS') {
	       	 	echo "Database User (" . $_SESSION['dbInfo']['dbOHRMUserName'] . ") already exists";
	       } ?>
    </font>
<?php } ?>

        <p>Please enter your database configuration information below. If you are
        unsure of what to fill in, we suggest that you use the default values.</p>

 <table cellpadding="0" cellspacing="0" border="0" class="table">
	<tr>
		<th colspan="3" align="left">Database Configuration    
               </th>
                
	</tr>

 <tr>
        <td class="tdComponent">Database to Use</td>
        <td class="tdValues"> <select name="cMethod" onchange="dosubmit();">

                <?php
                    $selectEx = "";
                    $selectDB = "";
                    
                    if ($_SESSION['cMethod'] == 'existing') {
                        $selectEx = "selected";
                    } else {
                        $selectDB = "selected";
                    }
                ?>
                                        <option value="existing" <?php echo $selectEx; ?>>Existing Empty Database</option>
                                        <option value="new" <?php echo $selectDB; ?>>New Database</option>
                              </select>
        </td>
</tr>
<tr>
	<td class="tdComponent">Database Host Name</td>
	<td class="tdValues"><input type="text" name="dbHostName" value="<?php echo  isset($_SESSION['dbInfo']['dbHostName']) ? $_SESSION['dbInfo']['dbHostName'] : 'localhost'?>" tabindex="1" ></td>
</tr>
<tr>
	<td class="tdComponent">Database Host <select name="dbHostPortModifier" id="dbHostPortModifier" onchange="changePortField();">
										    <option value="port" <?php echo (isset($_SESSION['dbInfo']['dbHostPortModifier']) && ($_SESSION['dbInfo']['dbHostPortModifier'] != 'port'))?'':'selected'; ?> >Port</option>
										    <option value="socket" <?php echo (isset($_SESSION['dbInfo']['dbHostPortModifier']) && ($_SESSION['dbInfo']['dbHostPortModifier'] == 'socket'))?'selected':''; ?> >Socket</option>
										  </select>
    </td>
	<td class="tdValues">
		<input type="text" id="dbHostPort" name="dbHostPort"
				maxlength="<?php echo (isset($_SESSION['dbInfo']['dbHostPortModifier']) && ($_SESSION['dbInfo']['dbHostPortModifier'] == 'socket'))?'256':'5'; ?>"
				size="<?php echo (isset($_SESSION['dbInfo']['dbHostPortModifier']) && ($_SESSION['dbInfo']['dbHostPortModifier'] == 'socket'))?'40':'4'; ?>"
				value="<?php echo  isset($_SESSION['dbInfo']['dbHostPort']) ? $_SESSION['dbInfo']['dbHostPort'] : '3306'?>"
				tabindex="2" />
	</td>
</tr>
<tr>
	<td class="tdComponent">Database Name</td>
	<td class="tdValues"><input type="text" name="dbName" value="<?php echo  isset($_SESSION['dbInfo']['dbName']) ? $_SESSION['dbInfo']['dbName'] : 'orangehrm_mysql'?>" tabindex="3"></td>
</tr>
<?php if ($_SESSION['cMethod'] == 'new') { // Couldn't use JavaScript since IE didn't support 'table-row' display property in CSS ?>
<tr>
	<td class="tdComponent">Privileged Database Username</td>
	<td class="tdValues"><input type="text" name="dbUserName" value="<?php echo  isset($_SESSION['dbInfo']['dbUserName']) ? $_SESSION['dbInfo']['dbUserName'] : 'root'?>" tabindex="4"> *</td>
</tr>
<tr>
	<td class="tdComponent">Privileged Database User Password</td>
	<td class="tdValues"><input type="password" name="dbPassword" value="<?php echo  isset($_SESSION['dbInfo']['dbPassword']) ? $_SESSION['dbInfo']['dbPassword'] : ''?>" tabindex="5" > *</td>
</tr>
<tr>
	<td class="tdComponent">Use the same Database User for OrangeHRM</td>
	<td class="tdValues"><input type="checkbox" onclick="disableFields()" <?php echo isset($_POST['chkSameUser']) ? 'checked' : '' ?> name="chkSameUser" value="1" tabindex="6"></td>
</tr>
<?php  } ?>
<tr>
	<td class="tdComponent">OrangeHRM Database Username</td>
	<td class="tdValues"><input type="text" name="dbOHRMUserName" <?php echo isset($_POST['chkSameUser']) ? 'disabled' : '' ?> value="<?php echo  isset($_SESSION['dbInfo']['dbOHRMUserName']) ? $_SESSION['dbInfo']['dbOHRMUserName'] : 'orangehrm'?>" tabindex="7"> #</td>
</tr>
<tr>
	<td class="tdComponent">OrangeHRM Database User Password</td>
	<td class="tdValues"><input type="password" name="dbOHRMPassword" <?php echo isset($_POST['chkSameUser']) ? 'disabled' : '' ?> value="<?php echo  isset($_SESSION['dbInfo']['dbOHRMPassword']) ? $_SESSION['dbInfo']['dbOHRMPassword'] : ''?>" tabindex="8"> #</td>
</tr>
<tr>
	<td class="tdComponent">Enable Data Encryption</td>
	<td class="tdValues"><input type="checkbox" name="chkEncryption" tabindex="9"></td>
</tr>
</table>

<br />

<!--<input type="hidden" name="cMethod" value="<?php //echo $_SESSION['cMethod'] == 'existing'?'new':'existing'; ?>" />-->

<br />
<input type="hidden" id="dbCreateMethod" name="dbCreateMethod" value="<?php echo $_SESSION['cMethod'] == 'existing'?'existing':'new'; ?>" />
<input class="button" type="button" value="Back" onclick="back();" tabindex="11"/>
<input type="button" value="Next" onclick="submitDBInfo()" tabindex="10"/>
<br /><br />

<div id="pDescription">
<font size="1">* Privileged Database User should have the rights to create databases, create tables, insert data into table, alter table structure and to create database users.</font>
</div>
<div id="oDescription">
<font size="1"># OrangeHRM database user should have the rights to insert data into table, update data in a table, delete data in a table.</font>
</div>

</div>
