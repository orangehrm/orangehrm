<?php
/*
OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
all the essential functionalities required for any enterprise.
Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com

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
$locRights=$_SESSION['localRights'];

$report = $this->popArr['report'];
$assignedGroups = $this->popArr['repUsgAss'];
$userGroupList = $this->popArr['usgAll'];

$reportId = $report[0][0];
$reportName = $report[0][1];
$formAction = $_SERVER['PHP_SELF'] . "?repcode=" . $this->getArr['repcode'] . "&amp;id=" . $this->getArr['id'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script type="text/javascript">
//<![CDATA[

function assignGroup() {
	if (document.frmUSG.cmbUserGroup.value == "0") {
		alert("<?php echo $lang_rep_NoGroupSelected;?>")
		return;
	}
    document.frmUSG.USG.value="SEL";
    document.frmUSG.submit();
}

function goBack() {
	location.href =  "./CentralController.php?id=<?php echo $this->getArr['id']?>&repcode=EMPDEF&capturemode=updatemode";
}

function unassignGroup() {
    var check = 0;
	with (document.frmRepUserGroup) {
		for (var i=0; i < elements.length; i++) {
			if ((elements[i].type == 'checkbox') && (elements[i].checked == true)){
				check = 1;
			}
		}
    }

    if(check==0) {
        alert("<?php echo $lang_rep_SelectAtLeaseOneUserGroupToDelete; ?>");
        return;
    }

    document.frmRepUserGroup.USG.value="DEL";
    document.frmRepUserGroup.submit();
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
            <input type="button" class="backbutton"
				onclick="goBack();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
				value="<?php echo $lang_Common_Back;?>" />
        </div>
        <div class="outerbox">
            <div class="mainHeading"><h2><?php echo "$lang_rep_ReportDefinition : $lang_rep_AssignUserGroups"; ?></h2></div>

            <span class="formLabel"><?php echo $lang_repview_ReportID; ?></span>
            <span class="formValue"><?php echo $reportId;?></span>
            <br class="clear"/>
            <span class="formLabel"><?php echo $lang_repview_ReportName; ?></span>
            <span class="formValue"><?php echo $reportName;?></span>
            <br class="clear"/>


<?php if($locRights['add']) { ?>
            <form name="frmUSG" method="post" action="<?php echo $formAction;?>" >
               <input type="hidden" value="<?php echo $this->popArr['token'];?>" name="token" />
                <input type="hidden" name="USG" value=""/>
                <input type="hidden" name="txtRepID" value="<?php echo $this->getArr['id']?>"/>
                <input type="hidden" name="dummy"/>

                <label for="cmbUserGroup"><?php echo $lang_rep_UserGroup;?></label>
                <select name="cmbUserGroup" id="cmbUserGroup" class="formSelect">
                	<option value="0">-- <?php echo $lang_rep_SelectUserGroup;?> --</option>
            <?php
                        $unassignedGroups = $this->popArr['usgUnAss'];
                        if (!empty($unassignedGroups)) {
                            foreach ($unassignedGroups as $group) {
                                $groupId = $group[0];
                                $groupName = htmlspecialchars($group[1]);
                                echo "<option value=\"{$groupId}\">{$groupName}</option>";
                            }
                        }
            ?>
                </select>
                <div class="formbuttons">
                    <input type="button" class="assignbutton" id="assignBtn" style="margin-left:5px;"
                        onclick="assignGroup();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                        value="<?php echo $lang_Common_Assign;?>" title="<?php echo $lang_Common_Assign;?>"/>
                </div>
            </form>
<?php } ?>

<?php
?>
            <form name="frmRepUserGroup" method="post" action="<?php echo $formAction;?>">
            	<input type="hidden" name="USG" value=""/>
               <input type="hidden" value="<?php echo $this->popArr['token'];?>" name="token" />
                <div class="subHeading"><h3><?php echo $lang_rep_AssignedUserGroups; ?></h3></div>
                <div class="actionbar">
                    <div class="actionbuttons">
                    <?php if($locRights['delete']) { ?>
                        <input type="button" class="delbutton" onclick="unassignGroup();"
                            onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                            value="<?php echo $lang_Common_Delete;?>" />
                    <?php } ?>
                    </div>
                    <div class="noresultsbar"><?php echo empty($assignedGroups) ? $lang_rep_NoUserGroupsAssigned : '';?></div>
                    <div class="pagingbar"></div>
                    <br class="clear" />
                </div>
                <br class="clear" />

                <table width="250" class="simpleList" >
                    <thead>
                        <tr>
<?php if($locRights['delete']) { ?>
                            <th></th>
<?php } ?>
                        <th class="listViewThS1"><?php echo $lang_rep_UserGroup; ?></th>
                        </tr>
                    </thead>
                    <tbody>
<?php if (!empty($assignedGroups)) { ?>
                        <?php
                            $odd = false;
                            foreach ($assignedGroups as $group) {
                                $cssClass = ($odd) ? 'even' : 'odd';
                                $odd = !$odd;
                        ?>
                        <tr>
                        <?php   if($locRights['delete']) { ?>
                                    <td class="<?php echo $cssClass?>">
                                        <input type='checkbox' class='checkbox' name='chkdel[]'
                                            value='<?php echo $group[1];?>'/>
                                    </td>
                        <?php } ?>

                        <?php
                                for ($a = 0; count($userGroupList) > $a; $a++) {
                                    if($userGroupList[$a][0] == $group[1]) {
                        ?>
                                        <td class="<?php echo $cssClass?>"><?php echo $userGroupList[$a][1];?></td>
                        <?php
                                    }
                                }
                        ?>
                        </tr>

                        <?php
                            }
                        ?>
            <?php } ?>
                    </tbody>
                </table>
                <br class="clear"/>

            </form>

        </div>
    </div>
<script type="text/javascript">
//<![CDATA[
    if (document.getElementById && document.createElement) {
        roundBorder('outerbox');
    }
//]]>
</script>
</body>
</html>

