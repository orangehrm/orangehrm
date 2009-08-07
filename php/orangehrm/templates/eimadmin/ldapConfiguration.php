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

if(LdapDetails::LDAP_TYPE=='Open LDAP'){

	$ldapPortEx = '(Ex: 389)';
	$ldapDomainNameDisc = $lang_LDAP_Suffix;
	$ldapDomainNameEx = '(Ex: u=orangehrm,dc=orangehrm,dc=com)';
}
elseif(LdapDetails::LDAP_TYPE=='Windows AD'){

	$ldapPortEx = '(Ex: 3128)';
	$ldapDomainNameDisc = $lang_LDAP_Domain_Name;
	$ldapDomainNameEx = '(Ex: orangehrm.com)';
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title><?php echo str_replace('#ldapType', LdapDetails::LDAP_TYPE=="Open LDAP"?"LDAP":LdapDetails::LDAP_TYPE, $lang_LDAP_Configuration) ; ?></title>
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

function edit() {
	var Edit = $("btnEdit");

	if(Edit.value == '<?php echo $lang_Common_Save; ?>') {
		validate();
		return;
	}

	var frm = document.frmLDAPConfig;
	for (var i = 0; i < frm.elements.length; i++) {
		frm.elements[i].disabled = false;
	}

	var resetButton = $("btnReset");
	resetButton.disabled = false;

	Edit.value = "<?php $lang_Common_Save; ?>";
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

function resetForm() {
	$('frmLDAPConfig').reset();
}


</script>
<body>
<h2><?php echo str_replace('#ldapType', LdapDetails::LDAP_TYPE=="Open LDAP"?"LDAP":LdapDetails::LDAP_TYPE, $lang_LDAP_Configuration) ; ?><hr/></h2>
<?php
if (!extension_loaded("ldap")){ //To check whether the pluging is installed or not

	echo("<span class='Error'>$lang_LDAP__Error_Extension_Disabled</span>");
}
else{
?>
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
		<br>(Ex: 192.0.2.201)<br><br>
		</td>
        <td width="25">&nbsp;</td>
		<td valign="top"><?php echo $lang_LDAP_Port; ?></td>
		<td width="25">&nbsp;</td>
		<td valign="top"><input type="text" disabled name="txtLDAPPort" id="txtLDAPPort" value="<?php echo $editArr->getLdapPort();?>"/>
		<br><?php echo $ldapPortEx; ?><br><br>
		</td>
		<td class="tableMiddleRight"></td>
      </tr>

	  <tr>
        <td class="tableMiddleLeft"></td>
        <td valign="top"><span class="error">*</span> <?php echo $ldapDomainNameDisc; ?></td>
        <td width="25">&nbsp;</td>
        <td valign="top"><input type="text" disabled name="txtLDAPDomainName" id="txtLDAPDomainName" value="<?php echo $editArr->getLdapDomainName();?>" />
		<br><?php echo $ldapDomainNameEx;?><br><br>
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
			        <input type="button" class="editbutton" id="btnEdit" value="<?php echo $lang_Common_Edit; ?>" name="Edit"
			        	onmouseout="moutButton(this);" onmouseover="moverButton(this);"
			        	onclick="edit(); return false;">
<?php			} else { ?>
			        <input type="button" class="editbutton" id="btnEdit" disabled="disabled" value="<?php echo $lang_Common_Edit; ?>" />
<?php			}  ?>
<input type="button" class="resetbutton" id="btnReset" disabled="disabled" value="<?php $lang_Common_Reset; ?>"
	onmouseout="moutButton(this);" onmouseover="moverButton(this);"
	onclick="resetForm(); return false;" />
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
<?php
}
?>
</body>
</html>
