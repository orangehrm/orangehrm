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

session_start();

$role = $_SESSION['hp-role'];
$module = isset($_SESSION['hp-module'])?$_SESSION['hp-module']:'';
$action = isset($_SESSION['hp-action'])?$_SESSION['hp-action']:'';
$userType = isset($_SESSION['hp-userType'])?$_SESSION['hp-userType']:''; // Used for the help of creating users: Admin > Users

?>

<html>
<head>
<title>Help</title>
</head>
<body>
<form action="http://www.orangehrm.com/help/" name="frmHelp" method="post">
<input type="hidden" name="role" value="<?php echo $role;?>" />
<input type="hidden" name="module" value="<?php echo $module;?>" />
<input type="hidden" name="action" value="<?php echo $action;?>" />
<input type="hidden" name="userType" value="<?php echo $userType;?>" />
</form>
<script type="text/javascript">
document.frmHelp.submit();
</script>
</body>
</html>