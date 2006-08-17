<script language="JavaScript">

function disableFields() {
	
	if(document.frmInstall.chkSameUser.checked) {
		document.frmInstall.dbOHRMUserName.disabled = true;
		document.frmInstall.dbOHRMPassword.disabled = true;			
	} else {
		document.frmInstall.dbOHRMUserName.disabled = false;
		document.frmInstall.dbOHRMPassword.disabled = false;	
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
<link href="style.css" rel="stylesheet" type="text/css" />

<div id="content">
	<h2>Step 3: Database Configuration</h2>

<? if(isset($dbConnectError)) { ?>
	<font color="Red">
	    <? if($dbConnectError == 'WRONGDBINFO') {
	    		echo "Wrong DB Information";
	       } elseif ($dbConnectError == 'WRONGDBVER') {
	       	 	echo "You need atleast MySQL 4.1.x, Detected MySQL ver " . $mysqlHost;
	       } elseif ($dbConnectError == 'DBEXISTS') {
	       	 	echo "Database (" . $_SESSION['dbInfo']['dbName'] . ") already exists";
	       } elseif ($dbConnectError == 'DBUSEREXISTS') {
	       	 	echo "Database User (" . $_SESSION['dbInfo']['dbOHRMUserName'] . ") already exists";
	       } ?>
    </font>
<? } ?>

        <p>Please enter your database configuration information below. If you are
        unsure of what to fill in, we suggest that you use the default values.</p>
   
 <table cellpadding="0" cellspacing="0" border="0" class="table">
	<tr>
		<th colspan="3" align="left">Database Configuration</td>
	</tr>
<tr>
	<td class="tdComponent_n">Database Host Name</td>
	<td class="tdValues_n"><input type="text" name="dbHostName" value="<?= isset($_SESSION['dbInfo']['dbHostName']) ? $_SESSION['dbInfo']['dbHostName'] : 'localhost'?>" tabindex="1" ></td>
</tr>
<tr>
	<td class="tdComponent_n">Database Host Port</td>
	<td class="tdValues_n"><input type="text" maxlength="4" size="4" name="dbHostPort" value="<?= isset($_SESSION['dbInfo']['dbHostPort']) ? $_SESSION['dbInfo']['dbHostPort'] : '3306'?>" tabindex="2" ></td>
</tr>
<tr>
	<td class="tdComponent_n">Database Name</td>
	<td class="tdValues_n"><input type="text" name="dbName" value="<?= isset($_SESSION['dbInfo']['dbName']) ? $_SESSION['dbInfo']['dbName'] : 'hr_mysql'?>" tabindex="3"></td>
</tr>
<tr>
	<td class="tdComponent_n">Priviledged Database Username</td>
	<td class="tdValues_n"><input type="text" name="dbUserName" value="<?= isset($_SESSION['dbInfo']['dbUserName']) ? $_SESSION['dbInfo']['dbUserName'] : 'root'?>" tabindex="4"></td>
</tr>
<tr>
	<td class="tdComponent_n">Priviledged Database User Password</td>
	<td class="tdValues_n"><input type="password" name="dbPassword" value="<?= isset($_SESSION['dbInfo']['dbPassword']) ? $_SESSION['dbInfo']['dbPassword'] : ''?>" tabindex="5" ></td>
</tr>
<tr>
	<td class="tdComponent_n">Use the same Database User for OrangeHRM</td>
	<td class="tdValues_n"><input type="checkbox" onclick="disableFields()" <?=isset($_POST['chkSameUser']) ? 'checked' : '' ?> name="chkSameUser" value="1" tabindex="6"></td>
</tr>
<tr>
	<td class="tdComponent_n">OrangeHRM Database Username</td>
	<td class="tdValues_n"><input type="text" name="dbOHRMUserName" <?=isset($_POST['chkSameUser']) ? 'disabled' : '' ?> value="<?= isset($_SESSION['dbInfo']['dbOHRMUserName']) ? $_SESSION['dbInfo']['dbOHRMUserName'] : 'orangehrm'?>" tabindex="7"></td>
</tr>
<tr>
	<td class="tdComponent_n">OrangeHRM Database User Password</td>
	<td class="tdValues_n"><input type="password" name="dbOHRMPassword" <?=isset($_POST['chkSameUser']) ? 'disabled' : '' ?> value="<?= isset($_SESSION['dbInfo']['dbOHRMPassword']) ? $_SESSION['dbInfo']['dbOHRMPassword'] : ''?>" tabindex="8"></td>
</tr>

</table>
<br />
<input class="button" type="button" value="Back" onclick="back();" tabindex="10"/>
<input type="button" value="Next" onclick="submitDBInfo()" tabindex="9"/>
</div>
