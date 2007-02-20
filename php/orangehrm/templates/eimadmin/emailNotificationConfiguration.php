<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 hSenid Software, http://www.hsenid.com
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
require_once($lan->getLangPath("full.php"));

$locRights=$_SESSION['localRights'];

$editArr = $this->popArr['editArr'];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>E-mail Cofiguration</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="../../themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<link href="../../themes/beyondT/css/leave.css" rel="stylesheet" type="text/css" />
<?php require_once ROOT_PATH . '/scripts/octopus.js'; ?>
<style type="text/css">
@import url("../../themes/beyondT/css/style.css");

.style1 {color: #FF0000}

.hide {
	display:none;
}

.show {
	display: table-row;
}

.roundbox {
	margin-top: 50px;
	margin-left: 0px;
}

.roundbox_content {
	padding:15px;
}

</style>
</head>
<body>
<h2><?php echo $subscribeToMailNotifications; ?> <hr/></h2>
<div class="roundbox">
  <table border="0">
    <tbody>
      <tr>
        <td><input type="checkbox" checked="checked" name="notification['approve']" value="1" /></td>
		<td>Leave Approval</td>
      </tr>
    </tbody>
  </table>
</div>
</body>
</html>
