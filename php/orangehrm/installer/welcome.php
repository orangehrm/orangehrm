<script language="JavaScript">
function welcomeSubmit() {
	document.frmInstall.actionResponse.value  = 'WELCOMEOK';
	document.frmInstall.submit();
}
</script>

<table cellspacing="0" cellpadding="0" border="0" align="center" class="shell">
    <tr>
      <th width="400">Welcome to the OrangeHRM ver 1.2 Setup Wizard</th>

      <th width="200" height="30" style="text-align: right;"><a href="http://www.orangehrm.com" target=
      "_blank">OrangeHRM.com</a></th>
    </tr>

    <tr>
        <p>This installer creates the OrangeHRM database tables and sets the
        configuration files that you need to start. </p>
        <p><input type="button" name="next" value="Next" onclick="welcomeSubmit();"></p>
      </td>
    </tr>
