<?php
$uploadLimit=ini_get('upload_max_filesize');

if (!is_int($uploadLimit)) {
	$intUploadLimit=substr($uploadLimit, 0, -1);
	$shortHandSize=strtoupper(substr($uploadLimit, -1, 1));

	switch ($shortHandSize) {
		case 'Y' : $intUploadLimit *= 1024; // Yotta
        case 'Z' : $intUploadLimit *= 1024; // Zetta
        case 'E' : $intUploadLimit *= 1024; // Exa
        case 'P' : $intUploadLimit *= 1024; // Peta
        case 'T' : $intUploadLimit *= 1024; // Tera
        case 'G' : $intUploadLimit *= 1024; // Giga
        case 'M' : $intUploadLimit *= 1024; // Mega
        case 'K' : $intUploadLimit *= 1024; // kilo
		default:
			  $intUploadLimit*=1;
	}
} else {
	$intUploadLimit=$uploadLimit;
}

$intUploadLimit-=100;

?>
<script language="JavaScript">
function uploadFileSubmit() {
	if (document.getElementById("file").value == "") {
		alert("Please select the backup file");
		return false;
	}

	document.frmInstall.actionResponse.value  = 'UPLOADOK';
	document.frmInstall.submit();
}

function enableNext() {
	if (document.getElementById("file").value == "") {
		document.getElementById("next").disabled=true;
		return false;
	}

	document.getElementById("next").disabled=false;

	return true;
}
</script>
	<div id="content">
		<h2>Restoring the database </h2>

<?php if(isset($error)) { ?>
	<font color="Red">
	    <?php if($error == 'WRONGDBINFO') {
	    		echo "Wrong DB Information";
	       } elseif ($error == 'WRONGDBVER') {
	       	 	echo "You need at least MySQL 4.1.x, Detected MySQL ver " . $mysqlHost;
	       } elseif ($error == 'DBEXISTS') {
	       	 	echo "Database (" . $_SESSION['dbInfo']['dbName'] . ") already exists";
	       } elseif ($error == 'DBUSEREXISTS') {
	       	 	echo "Database User (" . $_SESSION['dbInfo']['dbOHRMUserName'] . ") already exists";
	       } else {
	       		echo $error;
	       }?>
    </font>
<?php } ?>
		<p>Please upload the database backup file. Maximum size <?php echo $uploadLimit; ?>.</p>
	  <p>
	    <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $intUploadLimit; ?>" />
	    <input name="file" id="file" type="file" id="file" size="50" onchange="enableNext();" />
	  </p>
		<input class="button" type="button" value="Back" onclick="back();" >
		<input type="button" name="next" id="next" value="Next" onclick="uploadFileSubmit();" id="next" tabindex="1" disabled>
</div>