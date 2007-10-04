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
<script type="text/javascript" src="<?php echo $_SESSION['WPATH']; ?>/scripts/archive.js"></script>
<script type="text/javascript" src="<?php echo $_SESSION['WPATH']; ?>/scripts/yui/yahoo/yahoo-min.js"></script>
<script type="text/javascript" src="<?php echo $_SESSION['WPATH']; ?>/scripts/yui/event/event-min.js"></script>
<script type="text/javascript">
function init() {
	parent.popAndPrint();

	printPage(window);
}

YAHOO.util.Event.addListener(window, "load", init);
</script>
<style type="text/css">
table {
	border: 0.1em solid #CCCCCC;
}
.tableTopLeft, .tableTopMiddle, .tableTopRight, .tableMiddleLeft,
.tableMiddleRight, .tableBottomLeft, .tableBottomMiddle, .tableBottomRight {
	background: none;
}
#branding {
	text-align: right;
	margin-top: 20px;
	font-size: 0.5em;
}
</style>
<div id="printArea"></div>
<div id="branding">
Powered by:<br/>
<img src="../../themes/beyondT/pictures/orangehrm_tiny.png" alt="OrangeHRM" />
</div>