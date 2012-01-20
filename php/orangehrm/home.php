<?php
/** * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures * all the essential functionalities required for any enterprise. * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com * * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of * the GNU General Public License as published by the Free Software Foundation; either * version 2 of the License, or (at your option) any later version. * * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. * See the GNU General Public License for more details. * * You should have received a copy of the GNU General Public License along with this program; * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, * Boston, MA  02110-1301, USA */
define('ROOT_PATH', dirname(__FILE__));require_once ROOT_PATH . '/lib/common/CommonFunctions.php';

session_start();
$styleSheet = CommonFunctions::getTheme();
?>

<html>
<head>
<title>OrangeHRM - New Level of HR Management</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="themes/<?php echo $styleSheet;?>/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
.bodyTXT {	font-family: Arial, Helvetica, sans-serif;
	font-size: 11px;
	color: #666666;
}
.style2 {color: #339900}
-->
</style>
</head>
<body>
<!-- <body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0"> -->
<!-- ImageReady Slices (orange_new.psd) -->

<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="20%"><img src="themes/<?php echo $styleSheet;?>/pictures/spacer.gif" width="5" height="5" alt=""></td>
    <td width="60%"><table id="Table_01" width="717" height="379" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td rowspan="6"><img src="themes/<?php echo $styleSheet;?>/pictures/orange_newMain_01.gif" width="5" height="338" alt=""></td>
        <td rowspan="6"><img src="themes/<?php echo $styleSheet;?>/pictures/orange_newMain_02.gif" width="167" height="338" alt=""></td>
        <td colspan="2" rowspan="3"><img src="themes/<?php echo $styleSheet;?>/pictures/orange_newMain_03.gif" width="94" height="116" alt=""></td>
        <td colspan="2"><img src="themes/<?php echo $styleSheet;?>/pictures/orange_newMain_04.gif" width="451" height="29" alt=""></td>
      </tr>
      <tr>
        <td colspan="2"><img src="themes/<?php echo $styleSheet;?>/pictures/orange_newMain_05.gif" width="451" height="46" alt=""></td>
      </tr>
      <tr>
        <td colspan="2"><img src="themes/<?php echo $styleSheet;?>/pictures/orange_newMain_06.gif" width="451" height="41" alt=""></td>
      </tr>
      <tr>
        <td><img src="themes/<?php echo $styleSheet;?>/pictures/orange_newMain_07.gif" width="23" height="22" alt=""></td>
        <td colspan="3"><img src="themes/<?php echo $styleSheet;?>/pictures/orange_newMain_08.gif" width="522" height="22" alt=""></td>
      </tr>
      <tr>
        <td><img src="themes/<?php echo $styleSheet;?>/pictures/orange_newMain_09.gif" width="23" height="169" alt=""></td>
        <td colspan="3" valign="top"><table width="80%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="bodyTXT"><!--<strong>Orange<span class="style2">HRM</span></strong> comes as a comprehensive solution for the efficient management and development of your Human Resource. It will assist you in the complex and strategic process of managing this crucial resource of your enterprise. Based on modular architecture, it facilitates a vast range of HR activities, with features that reflect the main HR management activities. It comes as a web-enabled application and considering the available flexibility, OrangeHRM is a perfect platform for reengineering your HR processes and achieving a new level of HR Management.-->
            <font color="#6C7E89" size="2" face="Tahoma">Orange</font><font size="2" face="Tahoma" color="#FF9933">HRM</font></b><font color="#6C7E89" size="3" face="tahoma" style="line-height: 18px; font-size: 11.8px; font-family: tahoma;"> comes as a comprehensive solution for the efficient management and development of your Human Resource. It will assist you in the complex and strategic process of managing this crucial resource of your enterprise. Based on modular architecture, it facilitates a vast range of HR activities, with features that reflect the main HR management activities. It comes as a web-enabled application and considering the available flexibility, OrangeHRM is a perfect platform for reengineering your HR processes and achieving a new level of HR Management.</font></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td colspan="4"><img src="themes/<?php echo $styleSheet;?>/pictures/orange_newMain_11.gif" width="545" height="31" alt=""></td>
      </tr>
      <tr>
        <td colspan="5"><img src="themes/<?php echo $styleSheet;?>/pictures/orange_newMain_12.gif" width="657" height="40" alt=""></td>
        <td><img src="themes/<?php echo $styleSheet;?>/pictures/orange_newMain_13.gif" width="60" height="40" alt=""></td>
      </tr>
      <tr>
        <td><img src="themes/<?php echo $styleSheet;?>/pictures/spacer.gif" width="5" height="1" alt=""></td>
        <td><img src="themes/<?php echo $styleSheet;?>/pictures/spacer.gif" width="167" height="1" alt=""></td>
        <td><img src="themes/<?php echo $styleSheet;?>/pictures/spacer.gif" width="23" height="1" alt=""></td>
        <td><img src="themes/<?php echo $styleSheet;?>/pictures/spacer.gif" width="71" height="1" alt=""></td>
        <td><img src="themes/<?php echo $styleSheet;?>/pictures/spacer.gif" width="391" height="1" alt=""></td>
        <td><img src="themes/<?php echo $styleSheet;?>/pictures/spacer.gif" width="60" height="1" alt=""></td>
      </tr>
    </table></td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<!-- End ImageReady Slices -->
</body>
</html>
