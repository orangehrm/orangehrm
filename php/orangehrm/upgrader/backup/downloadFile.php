<script language="JavaScript">
function downloadSubmit() {
	obj = document.getElementById('downloaded');
	
	if ((obj) && (obj.checked)) {
		document.frmInstall.actionResponse.value  = 'DOWNLOADOK';
		document.frmInstall.submit();
	} else {
		alert('If you downloaded the backup file select Downloaded and click Next.');
	}
}
</script>
<div id="content">
	<h2>Backup Data</h2>   
	<?php if (isset($_SESSION['error'])) { ?>
    <p><?php echo $_SESSION['error']; ?></p>
    <?php } ?>
	<p>Please save the backup file that will start downloading in few seconds. If the download doesn't start automatically click <a href="download.php">here</a>.</p>
	<p>To continue select <b>Downloaded</b> and click <b>[Next]</b> to continue.</p>
	<p><label>
	  <input type="checkbox" id="downloaded" name="downloaded" value="1" tabindex="1"/>
    Downloaded</label></p>
	<p>
		<input class="button" type="button" value="Back" onclick="back();" tabindex="4">
		<input type="button" name="next" value="Next" onclick="downloadSubmit();" id="next" tabindex="3">
	</p>
</div>
<meta http-equiv="refresh" content="2;URL=backup/download.php" />