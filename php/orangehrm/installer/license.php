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

  <table cellspacing="0" cellpadding="0" border="0" align="center" class="shell">
    <tr>
      <th width="400">Step 2: License Acceptance</th>
    </tr>

    <tr>
      <td width="600"><textarea cols="80" rows="20" readonly><?=$license_file?></textarea></td>
    </tr>
    <tr>
      <td align="center">
        <input type="button" onClick='licenseAccept();' value="I Accept">
      </td>
    </tr>
  </table>
