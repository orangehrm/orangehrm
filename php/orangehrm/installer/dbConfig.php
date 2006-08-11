<script language="JavaScript">

function disableFields() {
	
	if(document.frmInstall.chkSameUser.checked) {
		document.frmInstall.dbOHRMUserName.disabled = false;
		document.frmInstall.dbOHRMPassword.disabled = false;
	} else {
		document.frmInstall.dbOHRMUserName.disabled = true;
		document.frmInstall.dbOHRMPassword.disabled = true;
		//document.frmInstall.dbOHRMUserName.value = '';
		//document.frmInstall.dbOHRMPassword.value = '';
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
	
	if(frm.dbUserName.value == '') {
		alert('DB User-name left Empty');
		frm.dbUserName.focus();
		return;
	}

	if(document.frmInstall.chkSameUser.checked && frm.dbOHRMUserName.value == '') {
		alert('OrangeHRM DB User-name left Empty');
		frm.dbOHRMUserName.focus();
		return;
	}
document.frmInstall.actionResponse.value  = 'DBINFO';
document.frmInstall.submit();
}
</script>

<table cellspacing="0" cellpadding="0" border="0" align="center">
<tr>
    <th width="400">Step 3: Database Configuration</th>
</tr>
<? if(isset($dbConnectError)) { ?>
<tr>
    <td colspan="2" width="600"><font color="Red">
	    <? if($dbConnectError == 'WRONGDBINFO') {
	    		echo "Wrong DB Information";
	       } elseif ($dbConnectError == 'WRONGDBVER') {
	       	 	echo "You need atleast MySQL 4.1.x, Detected MySQL ver " . $mysqlHost;
	       } elseif ($dbConnectError == 'DBEXISTS') {
	       	 	echo "Database (" . $_SESSION['dbInfo']['dbName'] . ") already exists";
	       } elseif ($dbConnectError == 'DBUSEREXISTS') {
	       	 	echo "Database User (" . $_SESSION['dbInfo']['dbOHRMUserName'] . ") already exists";
	       } ?>
    </font></td>
</tr>
<? } ?>
<tr>
    <td colspan="2" width="600">	
        <p>Please enter your database configuration information below. If you are
        unsure of what to fill in, we suggest that you use the default values.</p>
    </td>
</tr>
<table width="100%" cellpadding="0" cellpadding="0" border="0">
<tr><th colspan="3" align="left">Database Configuration</td></tr>
<tr>
	<td>Database Host Name</td>
	<td><input type="text" name="dbHostName" value="<?= isset($_SESSION['dbInfo']['dbHostName']) ? $_SESSION['dbInfo']['dbHostName'] : 'localhost'?>"></td>
</tr>
<tr>
	<td>Database Host Port</td>
	<td><input type="text" maxlength="4" size="4" name="dbHostPort" value="<?= isset($_SESSION['dbInfo']['dbHostPort']) ? $_SESSION['dbInfo']['dbHostPort'] : '3306'?>"></td>
</tr>
<tr>
	<td>Database Name</td>
	<td><input type="text" name="dbName" value="<?= isset($_SESSION['dbInfo']['dbName']) ? $_SESSION['dbInfo']['dbName'] : 'hr_mysql'?>"></td>
</tr>
<tr>
	<td>Priviledged Database User-name</td>
	<td><input type="text" name="dbUserName" value="<?= isset($_SESSION['dbInfo']['dbUserName']) ? $_SESSION['dbInfo']['dbUserName'] : 'root'?>"></td>
</tr>
<tr>
	<td>Priviledged Database User-Password</td>
	<td><input type="password" name="dbPassword" value="<?= isset($_SESSION['dbInfo']['dbPassword']) ? $_SESSION['dbInfo']['dbPassword'] : ''?>"></td>
</tr>
<tr>
	<td>Use the same Database User for OrangeHRM</td>
	<td><input type="checkbox" onclick="disableFields()" <?=isset($_POST['chkSameUser']) ? 'checked' : '' ?> name="chkSameUser" value="1"></td>
</tr>
<tr>
	<td>OrangeHRM Database User-name</td>
	<td><input type="text" name="dbOHRMUserName" <?=isset($_POST['chkSameUser']) ? '' : 'disabled' ?> value="<?= isset($_SESSION['dbInfo']['dbOHRMUserName']) ? $_SESSION['dbInfo']['dbOHRMUserName'] : 'orangehrm'?>"></td>
</tr>
<tr>
	<td>OrangeHRM Database User-Password</td>
	<td><input type="password" name="dbOHRMPassword" <?=isset($_POST['chkSameUser']) ? '' : 'disabled' ?> value="<?= isset($_SESSION['dbInfo']['dbOHRMPassword']) ? $_SESSION['dbInfo']['dbOHRMPassword'] : ''?>"></td>
</tr>
<tr>
	<td></td>
	<td><input type="button" value="OK" onclick="submitDBInfo()"></td>
</tr>
</table>
</table>
