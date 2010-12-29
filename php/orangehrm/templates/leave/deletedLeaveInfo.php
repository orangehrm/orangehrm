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
 */
?>
<script language="javascript">

	function toggleDeletedLeaveTypeInfo() {
		oLayer = document.getElementById("deletedLeaveTypeInfoLayer");
		oLink = document.getElementById("deletedLeaveTypeMoreInfoLink");

		if (oLayer.style.display == 'none') {
			oLayer.style.display = 'block';
		} else {
			oLayer.style.display = 'none';
		}
	}

</script>

<div id="notice"><span class="error">*</span>&nbsp;<?php echo $lang_Leave_Summary_Deleted_Types_Shown;?>
<a href="javascript:toggleDeletedLeaveTypeInfo();" id="deletedLeaveTypeMoreInfoLink">?</a></div>
<div id="deletedLeaveTypeInfoLayer" style="display:none;">
<?php echo $lang_Leave_Summary_Deleted_Types_MoreInfo; ?>
</div>
