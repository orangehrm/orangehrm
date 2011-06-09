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



$locRights=$_SESSION['localRights'];

if ($_SESSION['userGroup'] == $this->popArr['ugDet'][0][0]) {
	$locRights=array('add'=> false , 'edit'=> false , 'delete'=> false, 'view'=> false);
}
$token = $this->popArr['token'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script type="text/javascript">
//<![CDATA[

function addEXT() {

	if ($('cmbModuleID').value == '0') {
		alert("<?php echo $lang_Admin_Users_Errors_ModuleShouldBeSelected; ?>");
		$('cmbModuleID').focus();
		return;
	}

	if (!$('chkView').checked) {
		alert("<?php echo $lang_Admin_Users_Errors_ViewShouldBeSelected; ?>");
		return;
	}

	$('STAT').value = "ADD";
	$('frmURights').submit();
}

function editEXT()

{

	var frm=document.frmURights;
	if((!frm.chkView.checked) && (frm.chkAdd.checked || frm.chkEdit.checked || frm.chkDelete.checked)) {
		alert("<?php echo $lang_Admin_Users_Errors_ViewShouldBeSelected; ?>");
		return
	}

  document.frmURights.STAT.value="EDIT";
  document.frmURights.submit();
}

	function goBack() {

		location.href = "./CentralController.php?capturemode=updatemode&uniqcode=USG&id=<?php echo $this->getArr['id']?>";
	}

function delEXT() {

	if(confirm("<?php echo $lang_Admin_Users_Errors_DoYouWantToClearRights; ?>!"))

    document.frmURights.STAT.value="DEL";
    document.frmURights.submit();
}

function edit() {
    var editBtn = $('editBtn');

	if (editBtn.title == '<?php echo $lang_Common_Save; ?>') {
		editEXT();
		return;
	}

    var frm = $('frmURights');

    for (var i=0; i < frm.elements.length; i++) {
        frm.elements[i].disabled = false;
    }

    editBtn.value="<?php echo $lang_Common_Save; ?>";
    editBtn.title="<?php echo $lang_Common_Save; ?>";
    editBtn.className = "savebutton";
}

//]]>
</script>
<script type="text/javascript" src="../../themes/<?php echo $styleSheet;?>/scripts/style.js"></script>
<link href="../../themes/<?php echo $styleSheet;?>/css/style.css" rel="stylesheet" type="text/css"/>
<!--[if lte IE 6]>
<link href="../../themes/<?php echo $styleSheet; ?>/css/IE6_style.css" rel="stylesheet" type="text/css"/>
<![endif]-->
<!--[if IE]>
<link href="../../themes/<?php echo $styleSheet; ?>/css/IE_style.css" rel="stylesheet" type="text/css"/>
<![endif]-->
</head>
<body>
<div class="formpage">
    <div class="navigation">
    	<input type="button" class="savebutton"
	        onclick="goBack();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
	        value="<?php echo $lang_Common_Back;?>" />
    </div>

<form name="frmURights" id="frmURights" method="post" action="<?php echo $_SERVER['PHP_SELF']?>?id=<?php echo $this->getArr['id']?>&amp;uniqcode=<?php echo $this->getArr['uniqcode']?>">
   <input type="hidden" value="<?php echo $token;?>" name="token" />
    <div class="outerbox">
        <div class="mainHeading"><h2><?php echo $lang_Admin_Users_RightsAssignedToUserGroups;?></h2></div>

        <?php
        $ugDet = $this ->popArr['ugDet'];
        ?>
        <input type="hidden" name="STAT" id="STAT" value=""/>
        <span class="formLabel"><?php echo $lang_Admin_Users_UserGroupId; ?></span>
        <span class="formValue"><?php echo $ugDet[0][0];?></span>
        <input type="hidden" id="txtUserGroupID" name="txtUserGroupID" value="<?php echo $ugDet[0][0]?>"/>
        <br class="clear"/>
        <span class="formLabel"><?php echo $lang_Admin_Users_UserGroup; ?></span>
        <span class="formValue"><?php echo $ugDet[0][1];?></span>
        <br class="clear"/>
    </div>

<?php if($ugDet[0][0] == $_SESSION['userGroup']) { ?>
    <div class="outerbox">
        <div class="notice"><?php echo $lang_Admin_Users_Errors_SameGroup; ?></div>
    </div>
<?php } elseif(isset($this->popArr['editArr'])) {
    $edit = $this->popArr['editArr'];
?>
    <div class="outerbox">
        <label for="cmbModuleID"><?php echo $lang_Admin_Users_Module; ?></label>
        <input type="hidden" id="cmbModuleID" name="cmbModuleID" value="<?php echo $edit[0][1]?>"/>
        <span class="formValue">
<?php
			$modlist = $this->popArr['modlist'];
			for($c=0;count($modlist)>$c;$c++) {
			    if($modlist[$c][0]==$edit[0][1]) {
			       echo  $modlist[$c][1];
                }
            }
?>
        </span>
        <br class="clear"/>
        <label for="chkAdd"><?php echo $lang_Admin_Users_add; ?></label>
        <input type="checkbox" disabled="disabled" <?php echo $edit[0][2]==1 ? 'checked' : ''?>
            name="chkAdd" id="chkAdd" value="1" class="formCheckboxWide"/>

        <label for="chkEdit"><?php echo $lang_Admin_Users_edit; ?></label>
        <input type="checkbox" disabled="disabled" <?php echo $edit[0][3]==1 ? 'checked' : ''?>
            name="chkEdit" id="chkEdit" value="1" class="formCheckboxWide"/>
        <br class="clear"/>

        <label for="chkDelete"><?php echo $lang_Admin_Users_delete; ?></label>
        <input type="checkbox" disabled="disabled" <?php echo $edit[0][4]==1 ? 'checked' : ''?>
            name="chkDelete" id="chkDelete" value="1" class="formCheckboxWide"/>

        <label for="chkView"><?php echo $lang_Admin_Users_view; ?></label>
        <input type="checkbox" disabled="disabled" <?php echo $edit[0][5]==1 ? 'checked' : ''?>
            name="chkView" id="chkView" value="1" class="formCheckboxWide"/>
        <br class="clear"/>

        <div class="formbuttons">
<?php if($locRights['edit']) { ?>
                    <input type="button" class="editbutton" id="editBtn"
                        onclick="edit();" tabindex="5" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                        value="<?php echo $lang_Common_Edit;?>" title="<?php echo $lang_Common_Edit;?>" />
<?php } ?>
        </div>
    </div>

<?php } else {
        $disabled = $locRights['add'] ? '':'disabled="disabled"';
    ?>
    <div class="outerbox">
        <label for="cmbModuleID"><?php echo $lang_Admin_Users_Module; ?></label>
		<select name="cmbModuleID" id="cmbModuleID" <?php echo $disabled; ?> class="formSelect">
            <option value="0">--<?php echo $lang_Admin_Users_SelectModule;?>--</option>
<?php
		$modlist = $this->popArr['modlistUnAss'];
        $excludedModules = array("MOD009", "MOD005", "MOD002");
		for($c=0;$modlist && count($modlist)>$c;$c++) {
         if(!in_array($modlist[$c][0], $excludedModules)) {
            echo "<option value='". $modlist[$c][0] . "'>". $modlist[$c][1] . "</option>";
         }
        }
?>
		</select>

        <br class="clear"/>
        <label for="chkAdd"><?php echo $lang_Admin_Users_add; ?></label>
        <input type="checkbox" <?php echo $disabled;?>
            name="chkAdd" id="chkAdd" value="1" class="formCheckboxWide"/>

        <label for="chkEdit"><?php echo $lang_Admin_Users_edit; ?></label>
        <input type="checkbox" <?php echo $disabled;?>
            name="chkEdit" id="chkEdit" value="1" class="formCheckboxWide"/>
        <br class="clear"/>

        <label for="chkDelete"><?php echo $lang_Admin_Users_delete; ?></label>
        <input type="checkbox" <?php echo $disabled;?>
            name="chkDelete" id="chkDelete" value="1" class="formCheckboxWide"/>

        <label for="chkView"><?php echo $lang_Admin_Users_view; ?></label>
        <input type="checkbox" <?php echo $disabled;?>
            name="chkView" id="chkView" value="1" class="formCheckboxWide"/>
        <br class="clear"/>

        <div class="formbuttons">
<?php if($locRights['add']) { ?>
        <input type="button" class="savebutton" id="saveBtn"
            onclick="addEXT();" tabindex="5" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
            value="<?php echo $lang_Common_Save;?>" title="<?php echo $lang_Common_Save;?>" />
<?php } ?>
        </div>
    </div>
<?php } ?>

    <div class="outerbox">
       <div class="subHeading"><h3><?php echo $lang_Admin_Users_AssignedRights; ?></h3></div>

<table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
        <tr>
			 <td><strong><?php echo $lang_Admin_Users_Module; ?></strong></td>
			 <td><strong><?php echo $lang_Admin_Users_add; ?></strong></td>
			 <td><strong><?php echo $lang_Admin_Users_edit; ?></strong></td>
			 <td><strong><?php echo $lang_Admin_Users_delete; ?></strong></td>
			 <td><strong><?php echo $lang_Admin_Users_view; ?></strong></td>
		</tr>
<?php
$rset = $this->popArr['modlistAss'];
$modlist = $this->popArr['modlist'];

    for($c=0; $rset && $c < count($rset); $c++)
        {
        echo '<tr>';
			for($a=0;count($modlist)>$a;$a++)
			    if($modlist[$a][0]==$rset[$c][1])
		            echo "<td><a href='" .$_SERVER['PHP_SELF']. "?id=" . $this->getArr['id']. "&amp;editID=" . $rset[$c][1] . "&amp;uniqcode=" .$this->getArr['uniqcode']. "'>" . $modlist[$a][1] . "</a></td>";
            echo '<td>' . (($rset[$c][2]==1) ? 'Yes' : 'No') .'</td>';
            echo '<td>' . (($rset[$c][3]==1) ? 'Yes' : 'No') .'</td>';
            echo '<td>' . (($rset[$c][4]==1) ? 'Yes' : 'No') .'</td>';
            echo '<td>' . (($rset[$c][5]==1) ? 'Yes' : 'No') .'</td>';
        echo '</tr>';
        }

?>
</table>
        <div class="formbuttons">
<?php if($locRights['delete']) { ?>
        <input type="button" class="delbutton" id="delBtn"
            onclick="delEXT();" tabindex="5" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
            value="<?php echo $lang_Common_Reset;?>" title="<?php echo $lang_Common_Reset;?>" />

<?php } ?>
        </div>
    </div>
</form>
<script type="text/javascript">
//<![CDATA[
    if (document.getElementById && document.createElement) {
        roundBorder('outerbox');
    }
//]]>
</script>
</div>
</body>
</html>
