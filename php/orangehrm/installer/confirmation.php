<script language="JavaScript">
function confirm() {
	document.frmInstall.actionResponse.value  = 'CONFIRMED';
	document.frmInstall.submit();
}
</script>
<link href="style.css" rel="stylesheet" type="text/css" />


  <div id="content">
	<h2>Step 5: Confirmation</h2>
    
        <p>All information required for OrangeHRM installation collected in the earlier
         steps are given below. On confirmation the installer will create the database, 
         database users, configuration file, etc.<br />
		 Click <b>[Install]</b> to continue.
		 </p>
         
         <p><font color="Red"><?php echo isset($error) ? $error : ''?></font></p>

        <table cellpadding="0" cellspacing="0" border="0" class="table">
		<tr>
			<th colspan="3" align="left" class="th">Details</th>
		</tr>
		<tr>
			<td class="tdComponent">Host Name</td>
			<td class="tdValues"><?php echo $_SESSION['dbInfo']['dbHostName']?></td>
		</tr>
		<tr>
			<td class="tdComponent">Database Host Port</td>
			<td class="tdValues"><?php echo $_SESSION['dbInfo']['dbHostPort']?></td>
		</tr>
		<tr>
			<td class="tdComponent">Database Name</td>
			<td class="tdValues"><?php echo $_SESSION['dbInfo']['dbName']?></td>
		</tr>
		<tr>
			<td class="tdComponent">Priviledged Database User-name</td>
			<td class="tdValues"><?php echo $_SESSION['dbInfo']['dbUserName']?></td>
		</tr>
<?php if(isset($_SESSION['dbInfo']['dbOHRMUserName'])) { ?>
		<tr>
			<td class="tdComponent">OrangeHRM Database User-name</td>
			<td class="tdValues"><?php echo $_SESSION['dbInfo']['dbOHRMUserName']?></td>
		</tr>
<?php } ?>		
		<tr>
			<td class="tdComponent">OrangeHRM Admin User Name</td>
			<td class="tdValues"><?php echo $_SESSION['defUser']['AdminUserName']?></td>
		</tr>
</table>
		<br />
		<input class="button" type="button" value="Back" onclick="back();" tabindex="3"/>
		<input class="button" type="button" value="Cancel Install" onclick="cancel();" tabindex="2"/>
        <input class="button" type="button" value="Install" onclick="confirm();" tabindex="1"/>
  </div>   