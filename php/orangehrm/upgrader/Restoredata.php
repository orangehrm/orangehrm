<script language="JavaScript">
function uploadFileSubmit() {
	document.frmInstall.actionResponse.value  = 'UPLOADOK';
	document.frmInstall.submit();
}
</script>
	<div id="content">
		<h2>Restoring the database </h2>
   
      
		<p>Please upload the database backup file </p>
	  <p>
	    <input name="file" type="file" id="file" size="50" />
	  </p>
		<input class="button" type="button" value="Back" onclick="back();" >
		<input type="button" name="next" value="Next" onclick="uploadFileSubmit();" id="next" tabindex="1">
</div>