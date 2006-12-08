<?php
/*
OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures 
all the essential functionalities required for any enterprise. 
Copyright (C) 2006 hSenid Software International Pvt. Ltd, http://www.hsenid.com

OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
the GNU General Public License as published by the Free Software Foundation; either
version 2 of the License, or (at your option) any later version.

OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program;
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
Boston, MA  02110-1301, USA
*/

define('ROOT_PATH', dirname(__FILE__));

session_start();

$wpath = explode('/login.php', $_SERVER['REQUEST_URI']);			
$_SESSION['WPATH']= $wpath[0];

require_once ROOT_PATH . '/lib/models/eimadmin/Login.php';


if ((isset($_POST['actionID'])) && $_POST['actionID'] == 'chkAuthentication') {

	$login = new Login();
	
	$rset=$login->filterUser(trim($_POST['txtUserName']));
	
	if (md5($_POST['txtPassword']) == $rset[0][1]) {
		if($rset[0][5]=='Enabled') {			
			$_SESSION['user']=$rset[0][3];
			$_SESSION['userGroup']=$rset[0][4];
			$_SESSION['isAdmin']=$rset[0][7];
			$_SESSION['empID']=$rset[0][6];
			
			$_SESSION['fname']=$rset[0][2];
			
			$wpath = explode('/login.php', $_SERVER['REQUEST_URI']);			
			$_SESSION['WPATH']= $wpath[0];
			
			setcookie('Loggedin', 'True', 0, '/');
			
			header("Location: ./index.php");
		} else $InvalidLogin=2;
	} else {
		$InvalidLogin=1;
	} 
}

?>
<html>
<head>
<title>OrangeHRM - New Level of HR Management</title>
<link href="favicon.ico" rel="icon" type="image/gif"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script>
	
	function submitForm() {
		
		if(document.loginForm.txtUserName.value == "") {
				alert("User Name not Given!");
				return false;
		   }
		   
		if(document.loginForm.txtPassword.value == "") {
				alert("Password not Given!");
				return false;
		   }
		   
		document.loginForm.actionID.value = "chkAuthentication";
		document.loginForm.submit();
	}
</script>
<link href="themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
body {
	background-color: #FFFFFF;
}
.bodyTXT {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 11px;
	color: #666666;
}
.style2 {color: #339900}
.loginTXT {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 11px;
	color: #666666;
	height: 19px;
	vertical-align: middle;
	padding-top:0;
}
-->
</style></head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<noscript>
	<strong><font color='Red' style="padding-left:15px; text-decoration:blink;">You need a JavaScript enabled Browser. Ex. <a href="http://www.mozilla.com/firefox/" target="_blank" style="text-decoration:none;">Mozilla Firefox</a></font>
	</strong>
</noscript>
<?php if (isset($_COOKIE['Loggedin']) && isset($_SERVER['HTTP_REFERER'])) { ?>
	<strong><font color='Red' style="padding-left:15px;">Your session expired because you were inactive. Please re-login.</font>
	</strong>
<?php } ?>

<!-- ImageReady Slices (orange_new.psd) -->
<table id="Table_01" width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="43%" align="right"><img src="themes/beyondT/pictures/orange_new_02.gif" width="266" height="67"></td>
    <td width="57%" align="center">&nbsp;</td>
  </tr>
</table>
<table id="Table_01" width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="10%" align="center" bgcolor="#E77817"><table id="Table_01" width="874" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td width="25"><img src="themes/beyondT/pictures/orange_new_05.gif" width="25" height="14" alt=""></td>
        <td width="72"><img src="themes/beyondT/pictures/orange_new_06.gif" width="72" height="14" alt=""></td>
        <td colspan="2"><img src="themes/beyondT/pictures/orange_new_07.gif" width="107" height="14" alt=""></td>
        <td colspan="5"><img src="themes/beyondT/pictures/orange_new_08.gif" width="610" height="14" alt=""></td>
        <td width="403"><img src="themes/beyondT/pictures/orange_new_09.gif" width="49" height="14" alt=""></td>
        <td width="52"><img src="themes/beyondT/pictures/orange_new_10.gif" width="10" height="14" alt=""></td>
      </tr>
    </table></td>
  </tr>
</table>
  <form name="loginForm" method="post" action="./login.php" onSubmit="submitForm(); return false;">
	<input type="hidden" name="actionID">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="20%"><img src="themes/beyondT/pictures/spacer.gif" width="5" height="5" alt=""></td>
    <td width="60%"><table id="Table_01" width="717" height="379" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td rowspan="6"><img src="themes/beyondT/pictures/orange_newMain_01.gif" width="5" height="338" alt=""></td>
        <td rowspan="5" valign="top"><img src="themes/beyondT/pictures/orange_new_13.jpg" width="167" height="180">
          <table width="100%"  border="0" cellspacing="0" cellpadding="3">
            <tr>
              <td width="45%">&nbsp;</td>
              <td width="55%">&nbsp;</td>
            </tr>
            <tr>
              <td align="right" class="bodyTXT">Login Name : </td>
              <td>
<?php		if(isset($_POST['txtUserName'])) {?>
              <input name="txtUserName" type="text" class="loginTXT" size="10" value="<?php echo $_POST['txtUserName']?>">
<?php		} else { ?>
              <input name="txtUserName" type="text" class="loginTXT" size="10" >
<?php		} ?>
              </td>
            </tr>
            <tr>
              <td align="right" class="bodyTXT">Password : </td>
              <td><input name="txtPassword" type="password" class="loginTXT" size="10"></td>
            </tr>
            <tr>
			<td height="40" valign="bottom" align="center"><input type="Submit" name="Submit" value="Login" class="button" > </td>
            <td align="center" valign="bottom"><input type="reset" name="clear" value="Clear" class="button"></td>
            </tr>
            <tr>
             	<td></td>
<?php
			if(isset($InvalidLogin)) {
			   switch ($InvalidLogin) {
			   	
			   		case 1 : 	echo "<td align='center'><strong><font color='Red'>Invalid Login</font></strong></td>";
			   					break;
			   		case 2 : 	echo "<td align='center'><strong><font color='Red'>User Disabled</font></strong></td>";
			   					break;					
			   }
			} else
		        echo "<td>&nbsp; </td>";
?>           
            </tr>
          </table></td>
          </form>
        <td colspan="2" rowspan="3"><img src="themes/beyondT/pictures/orange_new_14.jpg" width="94" height="116"></td>
        <td colspan="2"><img src="themes/beyondT/pictures/orange_newMain_04.gif" width="451" height="29" alt=""></td>
      </tr>
      <tr>
        <td colspan="2"><img src="themes/beyondT/pictures/orange_newMain_05.gif" width="451" height="46" alt=""></td>
      </tr>
      <tr>
        <td colspan="2"><img src="themes/beyondT/pictures/orange_newMain_06.gif" width="451" height="41" alt=""></td>
      </tr>
      <tr>
        <td><img src="themes/beyondT/pictures/orange_new_19.gif" width="23" height="22"></td>
        <td colspan="3"><img src="themes/beyondT/pictures/orange_newMain_08.gif" width="522" height="22" alt=""></td>
      </tr>
      <tr>
        <td><img src="themes/beyondT/pictures/orange_newMain_09.gif" width="23" height="169" alt=""></td>
        <td colspan="3" valign="top"><table width="80%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="bodyTXT"><!--<strong>Orange<span class="style2">HRM</span></strong> comes as a comprehensive solution for the efficient management and development of your Human Resource. It will assist you in the complex and strategic process of managing this crucial resource of your enterprise. Based on modular architecture, it facilitates a vastrange of HR activities, with features that reflect the main HR management activities. It comes as a web-enabled application and considering the available flexibility, OrangeHRM is a perfect platform for reengineering your HR processes and achieving a new level of HR Management.-->
            <font color="#6C7E89" size="2" face="Tahoma">Orange</font><font size="2" face="Tahoma" color="#FF9933">HRM</font></b><font color="#6C7E89" size="3" face="tahoma" style="line-height: 18px; font-size: 11.8px; font-family: tahoma;"> comes as a comprehensive solution for the efficient management and development of your Human Resource. It will assist you in the complex and strategic process of managing this crucial resource of your enterprise. Based on modular architecture, it facilitates a vastrange of HR activities, with features that reflect the main HR management activities. It comes as a web-enabled application and considering the available flexibility, OrangeHRM is a perfect platform for reengineering your HR processes and achieving a new level of HR Management.</font></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td valign="bottom"><img src="themes/beyondT/pictures/orange_new_13_2.jpg" width="167" height="25"></td>
        <td colspan="4"><img src="themes/beyondT/pictures/orange_newMain_11.gif" width="545" height="31" alt=""></td>
      </tr>
      <tr>
        <td colspan="5"><img src="themes/beyondT/pictures/orange_newMain_12.gif" width="657" height="40" alt=""></td>
        <td><img src="themes/beyondT/pictures/orange_newMain_13.gif" width="60" height="40" alt=""></td>
      </tr>
      <tr>
        <td><img src="themes/beyondT/pictures/spacer.gif" width="5" height="1" alt=""></td>
        <td><img src="themes/beyondT/pictures/spacer.gif" width="167" height="1" alt=""></td>
        <td><img src="themes/beyondT/pictures/spacer.gif" width="23" height="1" alt=""></td>
        <td><img src="themes/beyondT/pictures/spacer.gif" width="71" height="1" alt=""></td>
        <td><img src="themes/beyondT/pictures/spacer.gif" width="391" height="1" alt=""></td>
        <td><img src="themes/beyondT/pictures/spacer.gif" width="60" height="1" alt=""></td>
      </tr>
    </table></td>
    <td width="20%" valign="top">&nbsp;</td>
  </tr>
</table>
<!-- End ImageReady Slices -->
<table width="100%">
<tr>
<td align="center"><a href="http://www.orangehrm.com" target="_blank">OrangeHRM</a> ver 2.0.3 &copy; OrangeHRM Inc. 2005 - 2006 All rights reserved.</td>
</tr>
</table>

</body>
</html>
