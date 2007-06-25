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
function confLocationSubmit() {
	document.frmInstall.actionResponse.value  = 'LOCCONFOK';
	document.frmInstall.submit();
}
</script>
<div id="content">
	<h2>OrangeHRM 1.2/2.0/2.1 </h2>
	<?php if (isset($_SESSION['error'])) { ?>
    <p><font color="Red"><?php echo $_SESSION['error']; ?></font></p>
    <?php } ?>
	<p>Please enter the location of the previous installation of OrangeHRM  and Click <b>[Next]</b> to continue.</p>
<table cellpadding="0" cellspacing="0" border="0" class="table">
	<tr>
		<th colspan="3" align="left">Database Configuration</td>
	</tr>
<tr>
	<td class="tdComponent">Location of previous Installation of OrangeHRM</td>
	<td class="tdValues"><input type="text" name="locationOhrm" value="<?php echo  isset($_SESSION['dbInfo']['locationOhrm']) ? $_SESSION['dbInfo']['locationOhrm'] : 'orangehrm'?>" tabindex="1" ></td>
</tr>
</table>
<p>
	<input class="button" type="button" value="Back" onclick="back();" tabindex="4">
	<input type="button" name="next" value="Next" onclick="confLocationSubmit();" id="next" tabindex="3">
</p>
</div>
