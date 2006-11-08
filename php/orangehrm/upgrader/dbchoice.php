<script language="JavaScript">
function welcomeSubmit() {
	document.frmInstall.actionResponse.value  = 'DBCHOICEOK';
	document.frmInstall.submit();
}
</script>
	<div id="content">
		<h2>Step 3: Selecting the Database Methord </h2>
   
      
		<p>Please select one of above methods</p>
		<p>
		  <input name="radiobutton" type="radio" value="radiobutton" />
		  Create New Database <br />
		  <input name="radiobutton" type="radio" value="radiobutton" /> 
		  Use Existing Database
</p>
		<input class="button" type="button" value="Back" onclick="back();" >
		<input type="button" name="next" value="Next" onclick="welcomeSubmit();" id="next" tabindex="1">
</div>
		<h4 id="welcomeLink"><a href="http://www.orangehrm.com" target="_blank" tabindex="36">OrangeHRM.com</a></h4>
	 
