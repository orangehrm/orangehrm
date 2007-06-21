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
?>
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
	<p>Please save the backup file that will start downloading in few seconds. If the download doesn't start automatically click <a href="backup/download.php">here</a>.</p>
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