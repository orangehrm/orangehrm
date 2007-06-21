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
function optionSubmit() {
	obj = document.getElementById('option1');

	if (!(obj && obj.checked)) {
		obj = document.getElementById('option2');
	}

	if (obj) {
		document.frmInstall.actionResponse.value  = obj.value;
		document.frmInstall.submit();
	} else {
		alert('Please select one of the options before proceeding');
	}
}
</script>
<div id="content">
	<h2>Options </h2>


	<p>Select one of the options and click <b>[Next]</b> to continue.</p>
	<p>
		<label>
		  	<input name="option" id="option1" type="radio" value="LOCCONF" checked="checked" tabindex="1"/>
		 	Upgrade Exsisting Database
		 </label>
		 <br/>
		 <label>
		  	<input name="option" id="option2" type="radio" value="DBCONF" tabindex="2"/>
		  	Create New Database
		  </label>
	</p>
	<input class="button" type="button" value="Back" onclick="back();" tabindex="4">
	<input type="button" name="next" value="Next" onclick="optionSubmit();" id="next" tabindex="3">
</div>