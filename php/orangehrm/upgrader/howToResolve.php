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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Upgrade to PHP5</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>
<style type="text/css">
#amp, #manual {
	display:none
}

body {
	border: #CCCCCC 5px solid;
	height: 315px;
}

#close {
	position:absolute;
	top: 10px;
	right: 10px;
}
</style>
<body>
<a href="javascript:close()" id="close">[Close]</a>
<!-- Main Content -->
<div id="main">
<h4>Upgrade to PHP5 to use OrangeHRM 2.2</h4>
<p>You have PHP4 installed. OrangeHRM 2.2 requires PHP5. You will have to upgrade to PHP5.</p>
<ul>
	<li>If you are using EasyPHP (AMP Stack), please click <a href="javascript:ampDet()">here</a> for datails</li>
	<li>If you configured Apache, MySQL and PHP manually, please click <a href="javascript:mannualDet()">here</a> for datails</li>
</ul>
</div>

<!-- For EasyPHP users -->
<div id="amp">
<h4>Upgrade to an AMP Stack with PHP5</h4>
<p>You will have to uninstall EasyPHP and install XAMPP for Windows. Click <a href="http://www.apachefriends.org/en/xampp-windows.html#641" target="_blank">here</a> to download.</p>
<p>If you didn't save the OrangeHRM database backup that was given in "Backup Data" screen, please go back and save the file that downloads.</p>
<p>Please note that you will lose all files stored in the folder where EasyPHP is installed and any data in the MySQL server that came with EasyPHP. Therefore please remember to backup any other applications running on EasyPHP.</p>

<a href="javascript:hide('amp')">[Back]</a>
</div>

<!-- Mannual users -->
<div id="manual">
<h4>Upgrade to PHP5 manually</h4>
<p>You will have to download the latest release of PHP from <a href="http://www.php.net/downloads.php" target="_blank">php.net</a> . Follow the instructions you get and install PHP5 and Click <b>[Re-check]</b> in the System Check screen to continue</p>

<a href="javascript:hide('manual')">[Back]</a>
</div>

<script type="text/javascript" language="javascript">
ampDet = function () {
	document.getElementById('main').style.display = 'none';
	document.getElementById('amp').style.display = 'block';
}

mannualDet = function () {
	document.getElementById('main').style.display = 'none';
	document.getElementById('manual').style.display = 'block';
}

hide = function (id) {
	document.getElementById('main').style.display = 'block';
	document.getElementById(id).style.display = 'none';
}
</script>
</body>
</html>
