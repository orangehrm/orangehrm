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
$languageId = '';
$languageName = '';

if ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'updatemode')) {
    $formAction="{$formAction}&amp;id={$this->getArr['id']}&amp;capturemode=updatemode";
    $new = false;
    $disabled = "disabled='disabled'";
    $editData = $this->popArr['editArr'];
    $languageId = $editData[0][0];
    $languageName = $editData[0][1];
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

    var editMode = <?php echo $new ? 'true' : 'false'; ?>;

    function goBack() {
        location.href = "./CentralController.php?uniqcode=<?php echo $this->getArr['uniqcode']?>&VIEW=MAIN";
    }

    function validate() {
        var err = false;
        var msg = '<?php echo $lang_Error_PleaseCorrectTheFollowing; ?>\n\n';
        var errors = new Array();

        var desc = trim($('txtLanguageInfoDesc').value);

        if (desc == '') {
            err = true;
            msg += "\t- <?php echo $lang_Error_InvalidDescription; ?>\n";
        } else if (!alpha($('txtLanguageInfoDesc'))) {
            err = true;
            msg += "\t- <?php echo $lang_hremplan_NameContainsNonChars; ?>\n";
        }

        if (err) {
            alert(msg);
            return false;
        } else {
            return true;
        }
    }

    function reset() {
        $('frmLanguageInformation').reset();
    }

    function edit() {

<?php if($locRights['edit']) { ?>
        if (editMode) {
            if (validate()) {
                $('frmLanguageInformation').submit();
            }
            return;
        }
        editMode = true;
        var frm = $('frmLanguageInformation');

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
</head>

<body>
    <div class="formpage">
        <div class="navigation">
            <input type="button" class="savebutton"
            onclick="goBack();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
            value="<?php echo $lang_Common_Back;?>" />
        </div>
        <div class="outerbox">
            <div class="mainHeading"><h2><?php echo "$lang_Menu_Admin_Skills : $lang_Menu_Admin_Skills_Languages";?></h2></div>

        <?php $message =  isset($this->getArr['msg']) ? $this->getArr['msg'] : (isset($this->getArr['message']) ? $this->getArr['message'] : null);
            if (isset($message)) {
                $messageType = CommonFunctions::getCssClassForMessage($message);
                $message = "lang_Common_" . $message;
        ?>
            <div class="messagebar">
                <span class="<?php echo $messageType; ?>"><?php echo (isset($$message)) ? $$message: ""; ?></span>
            </div>
        <?php } ?>

            <form name="frmLanguageInformation" id="frmLanguageInformation" method="post" onsubmit="return validate()" action="<?php echo $formAction;?>">
               <input type="hidden" name="token" value="<?php echo $token;?>" />
                <input type="hidden" name="sqlState" value="<?php echo $new ? 'NewRecord' : 'UpdateRecord'; ?>"/>
                <?php if (!$new) { ?>
                    <label for="txtLanguageInfoId"><?php echo $lang_Commn_code; ?></label>
                    <input type="hidden" id="txtLanguageInfoId" name="txtLanguageInfoId" value="<?php echo $languageId;?>"/>
                    <span class="formValue"><?php echo $languageId;?></span><br class="clear"/>
                <?php } ?>

                <label for="txtLanguageInfoDesc"><?php echo $lang_Commn_name; ?><span class="required">*</span></label>
                <input type="text" id="txtLanguageInfoDesc" name="txtLanguageInfoDesc" tabindex="1"
                    class="formInputText" <?php echo $disabled;?> value="<?php echo $languageName; ?>" />
                <br class="clear"/>

                <div class="formbuttons">
<?php if($locRights['edit']) { ?>
                    <input type="button" class="<?php echo $new ? 'savebutton': 'editbutton';?>" id="editBtn"
                        onclick="edit();" tabindex="2" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                        value="<?php echo $new ? $lang_Common_Save : $lang_Common_Edit;?>" />
                    <input type="button" class="clearbutton" onclick="reset();" tabindex="3"
                        onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                         value="<?php echo $lang_Common_Reset;?>" />
<?php } ?>
                </div>
            </form>
        </div>
        <script type="text/javascript">
        //<![CDATA[
            if (document.getElementById && document.createElement) {
                roundBorder('outerbox');
            }
        //]]>
        </script>
        <div class="requirednotice"><?php echo preg_replace('/#star/', '<span class="required">*</span>', $lang_Commn_RequiredFieldMark); ?>.</div>
    </div>
</body>
</html>
