<script language="JavaScript">
function welcomeSubmit() {
	document.frmInstall.actionResponse.value  = 'WELCOMEOK';
	document.frmInstall.submit();
}
</script>
	<div id="content">
		<h2>Welcome to the OrangeHRM Web Upgrader Wizard</h2>
         
		<p>This upgrader upgrades your exsisting OrangeHRM 1.2 database to run OrangeHRM 2.0 and set the configuration files that you need to start using OrangeHRM 2.0.</p>
		
        <p><b>N.B.</b><br/>If you don't have OrangeHRM 1.2 running already you should install OrangeHRM 2.0, please click <a href="../install.php">here</a> to do so.</p>
        <p>
	  Click <b>[Next]</b> to Start the Wizard.</p>
        <input class="button" type="button" value="Back" onclick="back();" disabled="disabled">
		<input type="button" name="next" value="Next" onclick="welcomeSubmit();" id="next" tabindex="1">
</div>
		<h4 id="welcomeLink"><a href="http://www.orangehrm.com" target="_blank" tabindex="36">OrangeHRM.com</a></h4>
	 
