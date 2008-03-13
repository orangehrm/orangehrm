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

require_once($lan->getLangPath("full.php"));

$locRights=$_SESSION['localRights'];

$editArr = $this->popArr['editArr'];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title><?php echo $lang_LDAP_Configuration; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="../../themes/<?php echo $styleSheet;?>/css/style.css" rel="stylesheet" type="text/css">
<link href="../../themes/<?php echo $styleSheet;?>/css/leave.css" rel="stylesheet" type="text/css" />
<style type="text/css">
@import url("../../themes/<?php echo $styleSheet;?>/css/style.css"); .style1 {color: #FF0000}

.hide {
	display:none;
}

.show {
	display: table-row;
}
</style>
</head>
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script type="text/javascript" >

function $(id) {
	return document.getElementById(id);
}

function mout() {
	var Edit = $("btnEdit");

	if(Edit.title=='Save')
		Edit.src='../../themes/beyondT/pictures/btn_save.gif';
	else
		Edit.src='../../themes/beyondT/pictures/btn_edit.gif';
}

function mover() {
	var Edit = $("btnEdit");

	if(Edit.title=='Save')
		Edit.src='../../themes/beyondT/pictures/btn_save_02.gif';
	else
		Edit.src='../../themes/beyondT/pictures/btn_edit_02.gif';
}

function edit()
{
	var Edit = $("btnEdit");

	if(Edit.title=='Save') {
		validate();
		return;
	}

	var frm = document.frmLDAPConfig;
	for (var i=0; i < frm.elements.length; i++) {
		frm.elements[i].disabled = false;
	}
	var clear = $("btnClear");
	clear.disabled = false;
	Edit.src="../../themes/beyondT/pictures/btn_save.gif";
	Edit.title="Save";
}

	function validate() {
		var errors = new Array();
		var error = false;
		var server 	= $("txtLDAPServer");
		var port 	= $("txtLDAPPort");
		var domain 	= $("txtLDAPDomainName");

		if (server.value == '') {
			error = true;
			errors.push('<?php echo $lang_LDAP_Error_Server_Empty; ?>');
		}

		if (domain.value == '') {
			error = true;
			errors.push('<?php echo $lang_LDAP_Error_Domain_Empty; ?>');
		}

		if (port.value != '') {
			if ( !numbers(port) || ((port.value <= 0) || (port.value > 65535))) {
				error = true;
				errors.push('<?php echo $lang_LDAP_Invalid_Port; ?>');
			}
		}

		if (error) {
			errStr = "<?php echo $lang_Common_EncounteredTheFollowingProblems; ?>\n";
			for (i in errors) {
				errStr += " - "+errors[i]+"\n";
			}
			alert(errStr);
			return false;

		} else  {
			$('sqlState').value = 'UpdateRecord';
			$('frmLDAPConfig').submit();
			return true;
		}
	}

function clearAll() {

	var server 	= $("txtLDAPServer");
	var port 	= $("txtLDAPPort");
	var domain 	= $("txtLDAPDomainName");
	var status 	= $("cbLDAPEnable");

	server.value 	= "";
	port.value 		= "";
	domain.value 	= "";
	status.checked 	= false;

}


</script>
<body>
<h2><?php echo $lang_LDAP_Configuration; ?><hr/></h2>
<form id="frmLDAPConfig" name="frmLDAPConfig" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?uniqcode=LDAP&id=1">
<input type="hidden" name="sqlState" id="sqlState" />
<font face="Verdana, Arial, Helvetica, sans-serif">
<?php
if (isset($_GET['message']) && $_GET['message'] == "UPDATE_SUCCESS") {
	echo "<font color=\"#009966\">" . $lang_Common_UPDATE_SUCCESS . "</font>";
} elseif (isset($_GET['message']) && $_GET['message'] == "UPDATE_FAILURE") {
	echo "<font color=\"#ff3366\">" . $lang_Common_UPDATE_FAILURE . "</font>";
}
?>
</font>
  <table border="0" cellpadding="0" cellspacing="0">
    <thead>
      <tr>
        <th class="tableTopLeft"></th>
        <th class="tableTopMiddle"></th>
        <th class="tableTopMiddle"></th>
        <th class="tableTopMiddle"></th>
        <th class="tableTopMiddle"></th>
		<th class="tableTopMiddle"></th>
		<th class="tableTopMiddle"></th>
		<th class="tableTopMiddle"></th>

        <th class="tableTopRight"></th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="tableMiddleLeft"></td>
        <td valign="top"><span class="error">*</span> <?php echo $lang_LDAP_Server; ?></td>
        <td width="25">&nbsp;</td>
        <td valign="top"><input type="text" disabled name="txtLDAPServer" id="txtLDAPServer" value="<?php echo $editArr->getLdapServer();?>"/>
		<br>(Ex: comp1)<br><br>
		</td>
        <td width="25">&nbsp;</td>
		<td valign="top"><?php echo $lang_LDAP_Port; ?></td>
		<td width="25">&nbsp;</td>
		<td valign="top"><input type="text" disabled name="txtLDAPPort" id="txtLDAPPort" value="<?php echo $editArr->getLdapPort();?>"/>
		<br>(Ex: 3128)<br><br>		
		</td>
		<td class="tableMiddleRight"></td>
      </tr>

	  <tr>
        <td class="tableMiddleLeft"></td>
        <td valign="top"><span class="error">*</span> <?php echo $lang_LDAP_Domain_Name; ?></td>
        <td width="25">&nbsp;</td>
        <td valign="top"><input type="text" disabled name="txtLDAPDomainName" id="txtLDAPDomainName" value="<?php echo $editArr->getLdapDomainName();?>" />
		<br>(Ex: orangehrm.com)<br><br>
		</td>
        <td width="25">&nbsp;</td>
		<td valign="top"><?php echo $lang_LDAP_Enable; ?></td>
		<td width="25">&nbsp;</td>
		<td valign="top"><input type="checkbox" disabled name="cbLDAPEnable" id="cbLDAPEnable" value="" <?php if ($editArr->getLdapStatus() == "enabled") { echo "checked=\"checked\"";} ?>/></td>

        <td class="tableMiddleRight"></td>
      </tr>

	  <tr>
        <td class="tableMiddleLeft"></td>
        <td colspan="7" align="center">

<?php			if($locRights['edit']) { ?>
			        <input type="image" class="button1" id="btnEdit" src="../../themes/beyondT/pictures/btn_edit.gif" title="Edit" onMouseOut="mout();" onMouseOver="mover();" name="Edit" onClick="edit(); return false;">
<?php			} else { ?>
			        <input type="image" class="button1" id="btnEdit" src="../../themes/beyondT/pictures/btn_edit.gif" onClick="alert('<?php echo $lang_Common_AccessDenied;?>'); return false;">
<?php			}  ?>
<input type="image" class="button1" id="btnClear" disabled src="../../themes/beyondT/icons/reset.gif" onMouseOut="this.src='../../themes/beyondT/icons/reset.gif';" onMouseOver="this.src='../../themes/beyondT/icons/reset_o.gif';" onClick="clearAll(); return false;" />
          </td>
        <td class="tableMiddleRight"></td>
      </tr>
    </tbody>
    <tfoot>
      <tr>
        <td class="tableBottomLeft"></td>
        <td class="tableBottomMiddle"></td>
        <td class="tableBottomMiddle"></td>
		<td class="tableBottomMiddle"></td>
        <td class="tableBottomMiddle"></td>
        <td class="tableBottomMiddle"></td>
        <td class="tableBottomMiddle"></td>
		<td class="tableBottomMiddle"></td>

        <td class="tableBottomRight"></td>
      </tr>
    </tfoot>
  </table>
<span id="notice"><?php echo preg_replace('/#star/', '<span class="error">*</span>', $lang_Commn_RequiredFieldMark); ?>.</span>
</form>
</body>
</html>
