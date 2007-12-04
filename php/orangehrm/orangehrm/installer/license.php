<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 *
 */


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

		<p>Please read the license and click <b>[I Accept]</b> to continue. </p>
    	<textarea cols="80" rows="20" readonly tabindex="1"><?php echo $license_file?></textarea><br /><br />

    	<input class="button" type="button" value="Back" onclick="cancel();" tabindex="3">
		<input type="button" onClick='licenseAccept();' value="I Accept" tabindex="2">

	</div>