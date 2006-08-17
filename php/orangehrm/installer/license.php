<?
$license_file_name = ROOT_PATH . "/license/LICENSE.TXT";
$fh = fopen( $license_file_name, 'r' ) or die( "License file not found!" );
$license_file = fread( $fh, filesize( $license_file_name ) );
fclose( $fh );
?>
<script language="JavaScript">
function licenseAccept() {
	document.frmInstall.actionResponse.value  = 'LICENSEOK';
	document.frmInstall.submit();
}
</script>

	<div id="content">
	
  		<h2>Step 1: License Acceptance</h2>
		
		<p>Please read the license and click Accept to continue. </p>
    	<textarea cols="80" rows="20" readonly><?=$license_file?></textarea><br /><br />
    
    	<input class="button" type="button" value="Back" onclick="cancel();">
		<input type="button" onClick='licenseAccept();' value="I Accept">

	</div>