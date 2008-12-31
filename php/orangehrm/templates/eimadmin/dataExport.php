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

$exportTypes = $this->popArr['exportTypes'];
$pluginExportTypesFound = false;
$editLink = './CentralController.php?uniqcode=CEX&amp;VIEW=MAIN';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $lang_DataExport_Title; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script type="text/javascript">
//<![CDATA[
    function exportData() {

        if (!validate()) {
            return;
        }
        var url = "<?php echo $_SERVER['PHP_SELF']; ?>?uniqcode=CSE&download=1&cmbExportType=" + $('cmbExportType').value;

        var popup = window.open(url, 'Export');
        if(!popup.opener) popup.opener=self;

    }
    function validate() {
        var errors = new Array();
        var error = false;

        var exportType = $('cmbExportType');
        if (exportType.value == 0) {
            error = true;
            errors.push('<?php echo $lang_DataExport_ExportTypeNotSelected; ?>');
        }

        if (error) {
            errStr = "<?php echo $lang_Common_EncounteredTheFollowingProblems; ?>\n";
            for (i in errors) {
                errStr += " - "+errors[i]+"\n";
            }
            alert(errStr);
            return false;
        }

        return true;
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
            <div class="mainHeading"><h2><?php echo $lang_DataExport_Title;?></h2></div>
        
        <?php $message =  isset($this->getArr['msg']) ? $this->getArr['msg'] : (isset($this->getArr['message']) ? $this->getArr['message'] : null);
            if (isset($message)) {
                $messageType = CommonFunctions::getCssClassForMessage($message);
                $message = "lang_Common_" . $message;
        ?>
            <div class="messagebar">
                <span class="<?php echo $messageType; ?>"><?php echo (isset($$message)) ? $$message: ""; ?></span>
            </div>  
        <?php } ?>
     
            <form name="frmDataExport" id="frmDataExport" method="post" onsubmit="exportData(); return false;" action="">                    

                <input type="hidden" name="sqlState" value=""/>    
                <label for="cmbExportType"><?php echo $lang_DataExport_Type; ?> <span class="required">*</span></label>
                <select name="cmbExportType" id="cmbExportType" class="formSelect">
                    <option value="0">-- <?php echo $lang_Common_Select;?> --</option>
                <?php
                    foreach ($exportTypes as $key=>$exportType) {

                        /* mark export types defined in plugins. key is an int for user defined exports
                         and a class name for exports defined in plugin classes. */
                        if (!is_int($key)) {
                            $pluginExportTypesFound = true;
                            $mark = ' (+)';
                        } else {
                            $mark = '';
                        }
                        echo "<option value='" . $key . "' >" . $exportType . $mark . "</option>";
                    }
                ?>
                </select>
                <?php if ($pluginExportTypesFound) { ?>
                        <div class="fieldHint"><?php echo $lang_DataExport_PluginsAreMarked; ?></div>
                <?php } ?>
                <div class="fieldHint"><?php echo $lang_DataExport_CustomExportTypesCanBeManaged; ?>
                    <a href='<?php echo $editLink; ?>'><?php echo $lang_DataExport_ClickingHereLink;?></a></div>        
                <br class="clear"/>
                
                <div class="formbuttons">               
                    <input type="button" class="exportbutton" id="btnExport" 
                        onclick="exportData();" tabindex="2" 
                        onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                        title="<?php echo $lang_DataExport_Export?>" value="<?php echo $lang_DataExport_Export;?>" />
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