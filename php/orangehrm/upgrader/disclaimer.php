<?php
$license_file_name = ROOT_PATH . "/upgrader/DISCLAIMER.TXT";
$fh = fopen( $license_file_name, 'r' ) or die( "License file not found!" );
$license_file = fread( $fh, filesize( $license_file_name ) );
fclose( $fh );
?>
<script language="JavaScript">
function disclaimerAccept() {
	document.frmInstall.actionResponse.value = 'DISCLAIMEROK';
	document.frmInstall.submit();
}
</script>

	<div id="content">
	
  		<h2>Step 2: Disclaimer</h2>
		
		<p>Please read the disclaimer and click <b>[Accept]</b> to continue. </p>
    	<textarea cols="80" rows="20" readonly tabindex="1"><?php echo $license_file?></textarea><br /><br />
    
    	<input class="button" type="button" value="Back" onclick="back();" tabindex="3">
		<input type="button" onClick='disclaimerAccept();' value="Accept" tabindex="2">

	</div>