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

require_once($lan->getLangPath("full.php"));

$locRights=$_SESSION['localRights'];

$formAction="{$_SERVER['PHP_SELF']}?uniqcode={$this->getArr['uniqcode']}";
$new = true;
$disabled = '';
$userId = '';
$userName = '';
$userEmpNumber = '';
$userStatus = '';
$userGroupId = '';
$adminUser = (isset($_GET['isAdmin']) && ($_GET['isAdmin'] == 'Yes')) ? 'Yes' : 'No';
$userEmpId = '';
$userEmpFirstName = '';

$employeeSearchList = $this->popArr['employeeSearchList'];
$token = $this->popArr['token'];

if ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'updatemode')) {
    $formAction="{$formAction}&amp;id={$this->getArr['id']}&amp;capturemode=updatemode";
    $new = false;
    $disabled = "disabled='disabled'";
    $editData = $this->popArr['editArr'];
    /* editData:
     * 0-> user Id,
     * 1-> user name
     * 2-> Employee number (left padded with 0's)
     * 3-> is admin user? 'Yes' or 'No'
     * 4-> date entered - (not set)
     * 5-> date modified - (not set)
     * 6-> modified by user id - (not set)
     * 7-> created by - (not set)
     * 8-> user status
     * 9-> user group id
     * 10-> employee first name
     * 11-> employee ID
     * 12-> employee last name
     * 13-> employee work email
     */
    $userId = $editData[0][0];
    $userName = $editData[0][1];
    $userEmpNumber = $editData[0][2];
    $adminUser = $editData[0][3]; //'Yes' or 'No'
    $userStatus = $editData[0][8];
    $userGroupId = $editData[0][9];
    $userEmpFirstName = $editData[0][10];
    $userEmpId = $editData[0][11];
}

$formAction .= "&amp;isAdmin={$adminUser}";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<script type="text/javascript" src="../../scripts/archive.js"></script>

<script type="text/javascript">
//<![CDATA[

    var editMode = <?php echo $new ? 'true' : 'false'; ?>;

	var employeeSearchList = new Array();

	function showAutoSuggestTip(obj) {
		if (obj.value == '<?php echo $lang_Common_TypeHereForHints; ?>') {
			obj.value = '';
			obj.style.color = '#000000';
		}
	}

    function goBack() {
        location.href = "./CentralController.php?uniqcode=<?php echo $this->getArr['uniqcode']?>&VIEW=MAIN&isAdmin=<?php echo $adminUser; ?>";
    }

    function popEmpList() {
        var popup=window.open('../../templates/hrfunct/emppop.php?reqcode=REP&USR=USR','Employees','height=450,width=400,scrollbars=1');
        if(!popup.opener) popup.opener=self;
        popup.focus();
    }

    function addSave() {

    }

    function validate() {
        var err = false;
        var msg = '<?php echo $lang_Error_PleaseCorrectTheFollowing; ?>\n\n';
        var errors = new Array();

        var frm = document.frmUsers;
        var userName = trim($('txtUserName').value);

        // Run only if adding entry or user name changed.
        if (<?php echo ($new) ? 'true' : 'false'; ?> || userName != '<?php echo $userName;?>') {
            if (userName.length < 5 ) {
                err = true;
                msg += "- <?php echo $lang_Admin_Users_Errors_UsernameShouldBeAtleastFiveCharactersLong; ?>\n";
            }

            if (userName.search(/['\"\*\+\-]/) != -1) {
                err = true;
                msg += "- <?php echo $lang_Admin_Users_Errors_SpecialCharacters; ?>\n";
            }
        }

<?php if (($_SESSION['ldap'] != "enabled") && ($new || ($adminUser == 'No'))) {?>

        var password = trim($('txtUserPassword').value);
        var confirmPassword = trim($('txtUserConfirmPassword').value);

    <?php if (!$new) { ?>
        if (password != '') {
    <?php } ?>

        if(password.length < 4) {
            err = true;
            msg += "- <?php echo $lang_Admin_Users_Errors_PasswordShouldBeAtleastFourCharactersLong; ?>\n";
        }

        if(password != confirmPassword) {
            err = true;
            msg += "- <?php echo $lang_Admin_Users_ErrorsPasswordMismatch; ?>\n";
        }

    <?php if (!$new) { ?>
        }
    <?php } ?>

<?php } ?>

        if(!frm.chkUserIsAdmin && frm.cmbUserEmpID.value == '') {
            err = true;
            msg += "- <?php echo $lang_Admin_Users_Errors_EmployeeIdShouldBeDefined; ?>\n";
        }


        if(frm.chkUserIsAdmin && frm.cmbUserGroupID.value == '0') {
            err = true;
            msg += "- <?php echo $lang_Admin_Users_Errors_AdminUserGroupShouldBeSelected; ?>\n";
        }

        if (err) {
            alert(msg);
            return false;
        } else {
            return true;
        }
    }

    function reset() {
        $('frmUsers').reset();
    }

    function edit() {

    	for (i in employeeSearchList) {
    		if ($('txtUserEmpID').value == employeeSearchList[i][0]) {
    			$('cmbUserEmpID').value = employeeSearchList[i][2];
    			break;
    		}
    	}

<?php if($locRights['edit']) { ?>
        if (editMode) {
            if (validate()) {
                $('frmUsers').submit();
            }
            return;
        }
        editMode = true;
        var frm = $('frmUsers');

        for (var i=0; i < frm.elements.length; i++) {
            frm.elements[i].disabled = false;
        }
        $('editBtn').value="<?php echo $lang_Common_Save; ?>";
        $('editBtn').title="<?php echo $lang_Common_Save; ?>";
        $('editBtn').className = "savebutton";

<?php } else {?>
        alert('<?php echo $lang_Common_AccessDenied;?>');
<?php } ?>
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
</style>
<?php include ROOT_PATH."/lib/common/autocomplete.php"; ?>
</head>

<body class="yui-skin-sam">
    <div class="formpage2col" style="width:700px;">

        <div class="outerbox">
            <div class="mainHeading"><h2><?php echo $lang_view_Users; ?> : <?php echo ($adminUser == 'Yes') ? $lang_view_HRAdmin : $lang_view_ESS; ?> <?php echo $lang_view_Users; ?></h2></div>

        <?php $message =  isset($this->getArr['msg']) ? $this->getArr['msg'] : (isset($this->getArr['message']) ? $this->getArr['message'] : null);
            if (isset($message)) {
                $messageType = CommonFunctions::getCssClassForMessage($message);
                $message = "lang_Common_" . $message;
        ?>
            <div class="messagebar">
                <span class="<?php echo $messageType; ?>"><?php echo (isset($$message)) ? $$message: ""; ?></span>
            </div>
        <?php } ?>

            <?php $tabIndex = 1;?>
            <form name="frmUsers" id="frmUsers" method="post" onsubmit="return validate()" action="<?php echo $formAction;?>">
               <input type="hidden" value="<?php echo $token;?>" name="token" />
               <input type="hidden" name="sqlState" value="<?php echo $new ? 'NewRecord' : 'UpdateRecord'; ?>"/>
                <?php if (!$new) { ?>
                    <label for="txtUserID"><?php echo $lang_Commn_code; ?></label>
                    <input type="hidden" id="txtUserID" name="txtUserID" value="<?php echo $userId;?>"/>
                    <span class="formValue"><?php echo $userId;?></span><br class="clear"/>
                <?php } ?>

                <label for="txtUserName"><?php echo $lang_Admin_Users_UserName; ?><span class="required">*</span></label>
                <input type="text" id="txtUserName" name="txtUserName" tabindex="<?php echo $tabIndex++;?>"
                    class="formInputText" value="<?php echo $userName; ?>" <?php echo $disabled;?> />
                <br class="clear"/>

                <?php if ($new || ($adminUser == 'No')) { ?>
                    <label for="txtUserPassword"><?php echo $lang_Admin_Users_Password; ?><span class="required"><?php echo ($_SESSION['ldap'] == "enabled") ? '' : '*'; ?></span></label>
                    <input type="password" id="txtUserPassword" name="txtUserPassword" class="formInputText"
                        tabindex="<?php echo $tabIndex++;?>"/>

                    <label for="txtUserConfirmPassword"><?php echo $lang_Admin_Users_ConfirmPassword; ?><span class="required"><?php echo ($_SESSION['ldap'] == "enabled") ? '' : '*'; ?></span></label>
                    <input type="password" id="txtUserConfirmPassword" name="txtUserConfirmPassword"
                        class="formInputText" tabindex="<?php echo $tabIndex++;?>"/>
                    <br class="clear"/>
                <?php } ?>


                <label for="cmbUserStatus"><?php echo $lang_Admin_Users_Status; ?></label>
                <select id="cmbUserStatus" name="cmbUserStatus" <?php echo $disabled;?> class="formSelect"
                        tabindex="<?php echo $tabIndex++;?>">
                    <option value="Enabled"><?php echo $lang_Admin_Users_Enabled; ?></option>
                    <option <?php echo $userStatus == 'Disabled' ? 'selected="selected"' : ''?>
                        value="Disabled"><?php echo $lang_Admin_Users_Disabled; ?></option>
                </select>

                <input type="hidden" name="cmbUserEmpID" id="cmbUserEmpID" value="<?php echo $userEmpNumber;?>"/>
		<div>
		<label for="txtUserEmpID"><?php echo $lang_Admin_Users_Employee; ?><span class="required"><?php echo ($adminUser == 'No') ? '*' : '' ?></span></label>
<?php
  $empDispName = empty($userEmpId)  ? $userEmpNumber : $userEmpId;

  if ( !empty($userEmpFirstName) ) {
      $empDispName .= " - " . $userEmpFirstName;
  }

?>

		<div class="yui-ac" id="employeeSearchAC" style="float: left">
 	 		      <input name="txtUserEmpID" autocomplete="off" class="yui-ac-input" id="txtUserEmpID" type="text" 
 	 		      	value="<?php echo CommonFunctions::escapeForJavascript($empDispName); ?>" <?php echo $disabled; ?> 
 	 		      	tabindex="<?php echo $tabIndex++;?>" onfocus="showAutoSuggestTip(this)" style="color: #999999" />
 	 		      <div class="yui-ac-container" id="employeeSearchACContainer" style="top: 28px; left: 10px;">
 	 		        <div style="display: none; width: 159px; height: 0px; left: 100em" class="yui-ac-content">
 	 		          <div style="display: none;" class="yui-ac-hd"></div>
 	 		          <div class="yui-ac-bd">
 	 		            <ul>
 	 		              <li style="display: none;"></li>
 	 		              <li style="display: none;"></li>
 	 		              <li style="display: none;"></li>
 	 		              <li style="display: none;"></li>
 	 		              <li style="display: none;"></li>
 	 		              <li style="display: none;"></li>
 	 		              <li style="display: none;"></li>
 	 		              <li style="display: none;"></li>
 	 		              <li style="display: none;"></li>
 	 		              <li style="display: none;"></li>
 	 		            </ul>
 	 		          </div>
 	 		          <div style="display: none;" class="yui-ac-ft"></div>
 	 		        </div>
 	 		        <div style="width: 0pt; height: 0pt;" class="yui-ac-shadow"></div>
 	 	      </div>
    	</div>
    	</div>


                <br class="clear"/>

                <?php if ($adminUser == 'Yes') { ?>
                    <input type="hidden" name="chkUserIsAdmin" value="true"/>
                    <label for="cmbUserStatus"><?php echo $lang_Admin_Users_UserGroup; ?><span class="required">*</span></label>
                    <select id="cmbUserGroupID" name="cmbUserGroupID" <?php echo $disabled;?> class="formSelect"
                            tabindex="<?php echo $tabIndex++;?>">
                        <option value="0">--<?php echo $lang_Admin_Users_SelectUserGroup; ?>--</option>
                        <?php $userGroups = $this->popArr['uglist'];
                              if (!empty($userGroups)) {
                                  foreach ($userGroups as $userGroup) {
                                      $selected = ($userGroupId == $userGroup[0]) ? 'selected="selected"' : '';
                                      echo "<option {$selected} value='{$userGroup[0]}'>{$userGroup[1]}</option>";
                                  }
                              }
                        ?>
                    </select>
                    <br class="clear"/>
                <?php } else { ?>
                    <input type="hidden" id="cmbUserGroupID" name="cmbUserGroupID" value="0" />
                <?php } ?>

                <div class="formbuttons">
<?php if($locRights['edit']) { ?>
                    <input type="button" class="<?php echo $new ? 'savebutton': 'editbutton';?>" id="editBtn"
                        onclick="edit();" tabindex="<?php echo $tabIndex++;?>"
                        onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                        value="<?php echo $new ? $lang_Common_Save : $lang_Common_Edit;?>" />
                    <input type="button" class="clearbutton" onclick="reset();" tabindex="<?php echo $tabIndex++;?>"
                        onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                         value="<?php echo $lang_Common_Reset;?>" />
                    <input type="button" class="savebutton"
                                onclick="goBack();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                                value="<?php echo $lang_Common_Back;?>" />
<?php } ?>
                </div>
            </form>
        </div>
        <script type="text/javascript">
        //<![CDATA[
            if (document.getElementById && document.createElement) {
                roundBorder('outerbox');
            }

         <?php

			$i = 0;

			foreach ($employeeSearchList as $record) {

                foreach ($record as $pos => $item) {
                    $record[$pos] = CommonFunctions::escapeForJavascript($item);
                }
		?>
			employeeSearchList[<?php echo $i++; ?>] = new Array('<?php echo implode("', '", $record); ?>');
		<?php
			}
		?>

 	 	YAHOO.OrangeHRM.autocomplete.ACJSArray = new function() {

			// Instantiate second JS Array DataSource
		    this.oACDS = new YAHOO.widget.DS_JSArray(employeeSearchList);

		    // Instantiate second AutoComplete
		    this.oAutoComp = new YAHOO.widget.AutoComplete('txtUserEmpID','employeeSearchACContainer', this.oACDS);
		    this.oAutoComp.prehighlightClassName = "yui-ac-prehighlight";
		    this.oAutoComp.typeAhead = false;
		    this.oAutoComp.useShadow = true;
		    this.oAutoComp.forceSelection = true;
		    this.oAutoComp.formatResult = function(oResultItem, sQuery) {
		        var sMarkup = oResultItem[0] + "<br />" + oResultItem[1] .fontsize(-1).fontcolor('#999999')  + "&nbsp;";
		        return (sMarkup);
		    };

 	 	};
        //]]>
        </script>
        <div class="requirednotice"><?php echo preg_replace('/#star/', '<span class="required">*</span>', $lang_Commn_RequiredFieldMark); ?>.</div>
    </div>
</body>
</html>
