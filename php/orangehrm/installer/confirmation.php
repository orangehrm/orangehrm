<script language="JavaScript">
function confirm() {
	document.frmInstall.actionResponse.value  = 'CONFIRMED';
	document.frmInstall.submit();
}

function cancel() {
	document.frmInstall.actionResponse.value  = 'CANCEL';
	document.frmInstall.submit();
}
</script>

  <table cellspacing="0" align="center">
    <tr>
      <th width="400">Step 5: Confirmation</th>
    </tr>

    <tr>
      <td colspan="2" width="600">
        <p>All information required for OrangeHRM installation collected in the earlier
         steps are given below. On confirmation the installer will create the database, 
         database users, Conf file, etc.</p>
         
         <p><font color="Red"><?=isset($error) ? $error : ''?></font></p>

        <table cellpadding="0" cellspacing="0" border="0" width="100%">
		
		<tr>
			<td>Host Name</td>
			<td><?=$_SESSION['dbInfo']['dbHostName']?></td>
		</tr>
		<tr>
			<td>Database Host Port</td>
			<td><?=$_SESSION['dbInfo']['dbHostPort']?></td>
		</tr>
		<tr>
			<td>Database Name</td>
			<td><?=$_SESSION['dbInfo']['dbName']?></td>
		</tr>
		<tr>
			<td>Priviledged Database User-name</td>
			<td><?=$_SESSION['dbInfo']['dbUserName']?></td>
		</tr>
<? if(isset($_SESSION['dbInfo']['dbOHRMUserName'])) { ?>
		<tr>
			<td>OrangeHRM Database User-name</td>
			<td><?=$_SESSION['dbInfo']['dbOHRMUserName']?></td>
		</tr>
<? } ?>		
		<tr>
			<td>OrangeHRM Admin User Name</td>
			<td><?=$_SESSION['defUser']['AdminUserName']?></td>
		</tr>
		</table>
        <table cellspacing="0" cellpadding="0" border="0">
          <tr>
            <td><input class="button" type="button" value="Install" onclick="confirm();"></td>
            <td><input class="button" type="button" value="Cancel Install" onclick="cancel();"></td>
          </tr>
        </table>
