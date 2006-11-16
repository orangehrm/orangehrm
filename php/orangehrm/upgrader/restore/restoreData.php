<script language="JavaScript">
function uploadFileSubmit() {
	document.frmInstall.actionResponse.value  = 'UPLOADOK';
	document.frmInstall.submit();
}
</script>
	<div id="content">
		<h2>Restoring the database </h2>
   
<?php if(isset($error)) { ?>
	<font color="Red">
	    <?php if($error == 'WRONGDBINFO') {
	    		echo "Wrong DB Information";
	       } elseif ($error == 'WRONGDBVER') {
	       	 	echo "You need atleast MySQL 4.1.x, Detected MySQL ver " . $mysqlHost;
	       } elseif ($error == 'DBEXISTS') {
	       	 	echo "Database (" . $_SESSION['dbInfo']['dbName'] . ") already exists";
	       } elseif ($error == 'DBUSEREXISTS') {
	       	 	echo "Database User (" . $_SESSION['dbInfo']['dbOHRMUserName'] . ") already exists";
	       } ?>
    </font>
<?php } ?>
		<p>Please upload the database backup file </p>
	  <p>
	    <input name="file" type="file" id="file" size="50" />
	  </p>
		<input class="button" type="button" value="Back" onclick="back();" >
		<input type="button" name="next" value="Next" onclick="uploadFileSubmit();" id="next" tabindex="1">
</div>