<script language="JavaScript">
function welcomeSubmit() {
	document.frmInstall.actionResponse.value  = 'WELCOMEOK';
	document.frmInstall.submit();
}
</script>
	<div id="content">
		<h2>Welcome to the OrangeHRM ver 1.2 Setup Wizard</h2>
   
      
		<p>This installer creates the OrangeHRM database tables and sets the
        configuration files that you need to start. <br/>
		Click Next to Start the Wizard.</p>
        <input class="button" type="button" value="Back" onclick="back();" disabled="disabled">
		<input type="button" name="next" value="Next" onclick="welcomeSubmit();" id="next" tabindex="1">
     </div>
		<h4 id="welcomeLink"><a href="http://www.orangehrm.com" target="_blank" tabindex="36">OrangeHRM.com</a></h4>
	 
