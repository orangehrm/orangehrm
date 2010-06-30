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
	// Language constants, defined here since we don't want the bug tracker page to be translated
	// since we can only handle english bug reports.
	// Move to the language files if we need to translate the bug tracker in the future.

        $lang_bugtracker_Title = "Report Bugs";
        $lang_bugtracker_FoundInRelease = "Found in Release";
        $lang_bugtracker_Category = "Category";
        $lang_bugtracker_None = "None";
        $lang_bugtracker_Category_Interface = "Interface";
        $lang_bugtracker_Category_PHP = "PHP";
        $lang_bugtracker_Category_Database = "Database";
        $lang_bugtracker_Category_LanguagePack = "Language Pack";
        $lang_bugtracker_Category_WebInstaller = "Web-Installer";
        $lang_bugtracker_Module = "Module";
        $lang_bugtracker_SelectModule = "Select Module";
        $lang_bugtracker_Priority = "Priority";
        $lang_bugtracker_Priority_Lowest = "Lowest";
        $lang_bugtracker_Priority_Medium = "Medium";
        $lang_bugtracker_Priority_Highest = "Highest";
        $lang_bugtracker_Summary = "Summary";
        $lang_bugtracker_YourEmail = "Your Email";
        $lang_bugtracker_Description = "Description";

        $lang_bugtracker_PleaseSelectABugCategory = "Please select a bug category";
        $lang_bugtracker_PleaseSelectAModule = "Please select a module";
        $lang_bugtracker_PleaseSpecifyBugSummary = "Please specify the bug summary";
        $lang_bugtracker_PleaseSpecifyBugDescription = "Please specify the bug description";
        $lang_bugtracker_EmailNotVaild = "The email entered is not valid";
        
        $token = $this->popArr['token'];

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $lang_bugtracker_Title; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script type="text/javascript">
//<![CDATA[

    function addSave() {

        if(document.frmBugs.category_id.value=='100') {
            alert('<?php echo $lang_bugtracker_PleaseSelectABugCategory; ?>');
            document.frmBugs.cmbSource.focus();
            return;
        }

        if(document.frmBugs.cmbModule.value=='0') {
            alert('<?php echo $lang_bugtracker_PleaseSelectAModule; ?>');
            document.frmBugs.cmbModulse.focus();
            return;
        }

        if (document.frmBugs.summary.value == '') {
            alert ('<?php echo $lang_bugtracker_PleaseSpecifyBugSummary; ?>');
            return false;
        }

        if (document.frmBugs.txtDescription.value == '') {
            alert ('<?php echo $lang_bugtracker_PleaseSpecifyBugDescription; ?>');
            return false;
        }

        // validate email if supplied
        var email = document.frmBugs.txtEmail.value;
        if (email != '') {
            if( !checkEmail(email) ){
                alert ('<?php echo $lang_bugtracker_EmailNotVaild; ?>');
                return false;
            }
        }

        document.frmBugs.sqlState.value = "NewRecord";
        document.frmBugs.submit();
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
        <div class="outerbox">
            <div class="mainHeading"><h2><?php echo $lang_bugtracker_Title;?></h2></div>

        <?php
            if (isset($this->getArr['message'])) {
                $expString  = $this->getArr['message'];
                $expString = str_replace ('%', ' ', $expString);
        ?>
            <div class="messagebar">
                <span class=""><?php echo $expString; ?></span>
            </div>
        <?php } ?>

    <form name="frmBugs" method="post" action="<?php echo $_SERVER['PHP_SELF']?>?mtcode=<?php echo $this->getArr['mtcode']?>">
        <input type="hidden" name="sqlState" value=""/>
			<input type="hidden" value="<?php echo $token;?>" name="token" />
            <span class="formLabel"><?php echo $lang_bugtracker_FoundInRelease; ?></span>
            <span class="formValue">2.6-beta.7</span><br class="clear"/>
            <input type="hidden" readonly="readonly" name="artifact_group_id" value="786061"/>

            <label for="category_id"><?php echo $lang_bugtracker_Category; ?><span class="required">*</span></label>
            <select id="category_id" name="category_id" tabindex="1" class="formSelect" >
                <option value="100"><?php echo $lang_bugtracker_None; ?></option>
                <option value="803416"><?php echo $lang_bugtracker_Category_Interface; ?></option>
                <option value="813016"><?php echo $lang_bugtracker_Category_PHP; ?></option>
                <option value="813015"><?php echo $lang_bugtracker_Category_Database; ?></option>
                <option value="864255"><?php echo $lang_bugtracker_Category_LanguagePack; ?></option>
                <option value="883366"><?php echo $lang_bugtracker_Category_WebInstaller; ?></option>
            </select><br class="clear" />

            <label for="cmbModule"><?php echo $lang_bugtracker_Module;?><span class="required">*</span></label>
            <select id="cmbModule" name="cmbModule" tabindex="2" class="formSelect">
                <option value="0">--<?php echo $lang_bugtracker_SelectModule;?>--</option>
                <?php  $module = $this->popArr['module'];
                for($c=0;$c < count($module);$c++)
                echo "<option>" . $module[$c][1] ."</option>";
                ?>
            </select><br class="clear"/>

            <label for="priority"><?php echo $lang_bugtracker_Priority = "Priority"?><span class="required">*</span></label>
            <select id="priority" name="priority" tabindex="3" class="formSelect">
                <option value="1">1 - <?php echo $lang_bugtracker_Priority_Lowest; ?></option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5" selected="selected">5 - <?php echo $lang_bugtracker_Priority_Medium; ?></option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9 - <?php echo $lang_bugtracker_Priority_Highest; ?></option>
            </select><br class="clear"/>

            <label for="summary"><?php echo $lang_bugtracker_Summary; ?><span class="required">*</span></label>
            <input type="text" class="formInputText" id="summary" name="summary" class="formInputText" tabindex="4"/>
            <br class="clear"/>

            <label for="txtEmail"><?php echo $lang_bugtracker_YourEmail; ?></label>
            <input type="text" id="txtEmail" name="txtEmail" tabindex="5" class="formInputText"
                value="<?php echo isset($_POST['txtEmail']) ? $_POST['txtEmail'] : ''?>"/>
            <br class="clear"/>

            <label for="txtDescription"><?php echo $lang_bugtracker_Description;?><span class="required">*</span></label>
            <textarea name='txtDescription' id="txtDescription" rows="3" cols="30" tabindex="6" class="formTextArea"></textarea><br class="clear" />

            <div class="formbuttons">
                <input type="button" class="savebutton" id="saveBtn"
                    onclick="addSave();" tabindex="7" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                    value="<?php echo $lang_Common_Save;?>" />
                <input type="button" class="clearbutton" onclick="document.frmBugs.reset();" tabindex="8"
                    onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                     value="<?php echo $lang_Common_Reset;?>" />
            </div>
    </form>
    </div>
    <div class="requirednotice"><?php echo preg_replace('/#star/', '<span class="required">*</span>', $lang_Commn_RequiredFieldMark); ?>.</div>
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
