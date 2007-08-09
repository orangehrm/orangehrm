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
function nextPage() {
	document.frmInstall.actionResponse.value  = 'NOTESOK';
	document.frmInstall.submit();
}
</script>
<link href="style.css" rel="stylesheet" type="text/css" />


<div id="content">

	<h2>Upgrade Notes</h2>
	<p>
	Please read the notes below and click <b>[Next]</b> to continue.
	</p>
<?php
	if (isset($_SESSION['UPGRADE_NOTES'])) {
		$upgradeNotes = $_SESSION['UPGRADE_NOTES'];
		foreach	($upgradeNotes as $note) {
?>
	<p><?php echo $note;?></p>
<?php
		}
	}

?>

	<br />
	<input class="button" type="button" value="Next" onclick="nextPage();" tabindex="2">
</div>
