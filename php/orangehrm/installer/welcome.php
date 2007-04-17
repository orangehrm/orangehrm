<script language="JavaScript">
function welcomeSubmit() {
	document.frmInstall.actionResponse.value  = 'WELCOMEOK';
	document.frmInstall.submit();
}
</script>
	<div id="content">
		<h2>Welcome to the OrangeHRM ver 2.2 Setup Wizard</h2>
   
      
		<p>This installer creates the OrangeHRM database tables and sets the
        configuration files that you need to start.</p>
        <p><b>N.B.</b><br/>If you have OrangeHRM 1.2 or OrangeHRM 2.0 already running and would like to upgrade to OrangeHRM 2.2, please click <a href="../upgrade.php">here</a>.</p>
        <p>
		Click <b>[Next]</b> to Start the Wizard.</p>
        <input class="button" type="button" value="Back" onclick="back();" disabled="disabled">
		<input type="button" name="next" value="Next" onclick="welcomeSubmit();" id="next" tabindex="1">
     </div>
		<h4 id="welcomeLink"><a href="http://www.orangehrm.com" target="_blank" tabindex="36">OrangeHRM.com</a></h4>
	 
