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

?>

<html>
<head>
<title><?php echo $lang_bugtracker_Title; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script type="text/javascript" src="../../scripts/octopus.js"></script>
<script>

    function goBack() {
        location.href = "./CentralController.php?mtcode=<?php echo $this->getArr['mtcode']?>&VIEW=MAIN";
    }

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

</script>

    <link href="../../themes/<?php echo $styleSheet;?>/css/style.css" rel="stylesheet" type="text/css">
    <style type="text/css">@import url("../../themes/<?php echo $styleSheet;?>/css/style.css"); </style>

    <style type="text/css">
    <!--

    label,select,input,textarea {
        display: block;  /* block float the labels to left column, set a width */
        width: 150px;
        float: left;
        margin: 10px 0px 2px 0px; /* set top margin same as form input - textarea etc. elements */
    }

    /* this is needed because otherwise, hidden fields break the alignment of the other fields */
    input[type=hidden] {
        display: none;
        border: none;
        background-color: red;
    }

    label {
        text-align: left;
        width: 75px;
        padding-left: 10px;
    }

    select,input,textarea {
        margin-left: 10px;
    }

    input,textarea {
        padding-left: 4px;
        padding-right: 4px;
    }

    textarea {
        width: 250px;
    }

    form {
        min-width: 550px;
        max-width: 600px;
    }

    br {
        clear: left;
    }

    .version_label {
        display: block;
        float: left;
        width: 150px;
        font-weight: bold;
        margin-left: 10px;
        margin-top: 10px;
    }

    .roundbox {
        margin-top: 50px;
        margin-left: 0px;
    }

    .roundbox_content {
        padding:15px;
    }
    -->
    </style>

    </head>

    <body>
    <h2><?php echo $lang_bugtracker_Title; ?></h2>

    <form name="frmBugs" method="post" action="<?php echo $_SERVER['PHP_SELF']?>?mtcode=<?php echo $this->getArr['mtcode']?>">
        <input type="hidden" name="sqlState" value="">

        <font color="red" face="Verdana, Arial, Helvetica, sans-serif">
            <?php
            if (isset($this->getArr['message'])) {
                $expString  = $this->getArr['message'];
                $expString = explode ("%",$expString);
                $length = sizeof($expString);
                for ($x=0; $x < $length; $x++) {
                    echo " " . $expString[$x];
                }
            }
            ?>

        </font>


        <div class="roundbox">

            <label for="dummy1"><?php echo $lang_bugtracker_FoundInRelease; ?></label><div class="version_label">2.3-alpha.6</div></br>
            <input type="hidden" readonly name="artifact_group_id" value="786061">

            <label for="category_id"><span class="error">*</span><?php echo $lang_bugtracker_Category; ?></label>
            <select id="category_id" name="category_id" tabindex="1">
                <option VALUE="100"><?php echo $lang_bugtracker_None; ?></OPTION>
                <option VALUE="803416"><?php echo $lang_bugtracker_Category_Interface; ?></OPTION>
                <OPTION VALUE="813016"><?php echo $lang_bugtracker_Category_PHP; ?></OPTION>
                <OPTION VALUE="813015"><?php echo $lang_bugtracker_Category_Database; ?></OPTION>
                <OPTION VALUE="864255"><?php echo $lang_bugtracker_Category_LanguagePack; ?></OPTION>
                <OPTION VALUE="883366"><?php echo $lang_bugtracker_Category_WebInstaller; ?></OPTION>
            </select><br>

            <label for="cmbModule"><span class="error">*</span><?php echo $lang_bugtracker_Module;?></label>
            <select id="cmbModule" name="cmbModule" tabindex="2">
                <option value="0">--<?php echo $lang_bugtracker_SelectModule;?>--</option>
                <?php  $module = $this->popArr['module'];
                for($c=0;$c < count($module);$c++)
                echo "<option>" . $module[$c][1] ."</option>";
                ?>
            </select><br>

            <label for="priority"><span class="error">*</span><?php echo $lang_bugtracker_Priority = "Priority"?></label>
            <select id="priority" name="priority" tabindex="3">
                <option value="1">1 - <?php echo $lang_bugtracker_Priority_Lowest; ?></option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5" selected="selected">5 - <?php echo $lang_bugtracker_Priority_Medium; ?></option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9 - <?php echo $lang_bugtracker_Priority_Highest; ?></option>
            </select><br>

            <label for="summary"><span class="error">*</span><?php echo $lang_bugtracker_Summary; ?></label>
            <input type="text" id="summary" name="summary" tabindex="4">

            <div style="float:right">
                <label for="txtEmail"><?php echo $lang_bugtracker_YourEmail; ?></label>
                <input type="text" id="txtEmail" name="txtEmail" tabindex="5"
                    value="<?php echo isset($_POST['txtEmail']) ? $_POST['txtEmail'] : ''?>">
            </div><br>

            <label for="txtDescription"><span class="error">*</span><?php echo $lang_bugtracker_Description;?></label>
            <textarea name='txtDescription' id="txtDescription" rows="3" cols="30" tabindex="6"></textarea><br>

            <div align="center" >
            <img onClick="addSave();" onMouseOut="this.src='../../themes/beyondT/pictures/btn_save.gif';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_save_02.gif';" src="../../themes/beyondT/pictures/btn_save.gif">

            <img onClick="document.frmBugs.reset();" onMouseOut="this.src='../../themes/beyondT/pictures/btn_clear.gif';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_clear_02.gif';" src="../../themes/beyondT/pictures/btn_clear.gif">

            </div>
        </div>
        <script type="text/javascript">
        <!--
        	if (document.getElementById && document.createElement) {
   	 			initOctopus();
			}
        -->
        </script>
    </form>
	<span id="notice"><?php echo preg_replace('/#star/', '<span class="error">*</span>', $lang_Commn_RequiredFieldMark); ?>.</span>
</body>
</html>
