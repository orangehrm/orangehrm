<script language="JavaScript">
function welcomeSubmit() {
	document.frmInstall.actionResponse.value  = 'UPLOADOK';
	document.frmInstall.submit();
}
</script>
	<div id="content">
		<h2>Step 1: Restoring the database </h2> <? print_r($_SESSION);?>
   
      
		<p>Please upload the database backup file </p>
	  <p>
	    <input name="file" type="file" id="file" size="50" />
	  </p>
		<input class="button" type="button" value="Back" onclick="back();" >
		<input type="button" name="next" value="Next" onclick="welcomeSubmit();" id="next" tabindex="1">
</div>
		<h4 id="welcomeLink"><a href="http://www.orangehrm.com" target="_blank" tabindex="36">OrangeHRM.com</a></h4>
	 
