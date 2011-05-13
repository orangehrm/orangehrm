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
function welcomeSubmit() {
	document.frmInstall.actionResponse.value  = 'WELCOMEOK';
	document.frmInstall.submit();
}
</script>
	<div id="content">
		<h2>Welcome to the OrangeHRM ver 2.6.4 Setup Wizard</h2>


		<p>This installer creates the OrangeHRM database tables and sets the
        configuration files that you need to start.</p>
        <p>
		Click <b>[Next]</b> to Start the Wizard.</p>
        <input class="button" type="button" value="Back" onclick="back();" disabled="disabled">
		<input type="button" name="next" value="Next" onclick="welcomeSubmit();" id="next" tabindex="1">
     </div>
		<h4 id="welcomeLink"><a href="http://www.orangehrm.com" target="_blank" tabindex="36">OrangeHRM.com</a></h4>

