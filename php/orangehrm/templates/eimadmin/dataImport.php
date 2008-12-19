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

$importTypes = $this->popArr['importTypes'];
$pluginImportTypesFound = false;
$editLink = './CentralController.php?uniqcode=CIM&amp;VIEW=MAIN';
$formAction = $_SERVER['PHP_SELF'] . "?uniqcode=IMP&amp;upload=1";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $lang_DataImport_Title; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script type="text/javascript">
//<![CDATA[
    function importData() {
        if (validate()) {
            $('frmDataImport').submit();
        }
    }
    
    function validate() {
        var errors = new Array();
        var error = false;

        var importType = $('cmbImportType');
        if (importType.value == 0) {
            error = true;
            errors.push('<?php echo $lang_DataImport_ImportTypeNotSelected; ?>');
        }

        var fileName = $('importFile').value;
        fileName = trim(fileName);
        if (fileName == "") {
            error = true;
            errors.push('<?php echo $lang_DataImport_Error_PleaseSelectFile; ?>');
        }

        if (error) {
            errStr = "<?php echo $lang_Common_EncounteredTheFollowingProblems; ?>\n";
            for (i in errors) {
                errStr += " - "+errors[i]+"\n";
            }
            alert(errStr);
            return false;
        }
        $('sqlState').value = 'NewRecord';
        return true;
    }
//]]>
</script>
<script type="text/javascript" src="../../themes/<?php echo $styleSheet;?>/scripts/style.js"></script>
<link href="../../themes/<?php echo $styleSheet;?>/css/style.css" rel="stylesheet" type="text/css"/>
<!--[if lte IE 6]>
<link href="../../themes/<?php echo $styleSheet; ?>/css/IE6_style.css" rel="stylesheet" type="text/css"/>
<![endif]-->
</head>

<body>
    <div class="formpage">
        <div class="outerbox">
            <div class="mainHeading"><h2><?php echo $lang_DataImport_Title;?></h2></div>
        
        <?php $message =  isset($this->getArr['msg']) ? $this->getArr['msg'] : (isset($this->getArr['message']) ? $this->getArr['message'] : null);
            if (isset($message)) {
                $messageType = CommonFunctions::getCssClassForMessage($message);
                $message = "lang_Common_" . $message;
        ?>
            <div class="messagebar">
                <span class="<?php echo $messageType; ?>"><?php echo (isset($$message)) ? $$message: ""; ?></span>
            </div>  
        <?php } ?>
     
            <form enctype="multipart/form-data" id="frmDataImport" name="frmDataImport" method="post" 
                onsubmit="return validate();" action="<?php echo $formAction;?>">

                <input type="hidden" id="sqlState" name="sqlState" value=""/>    
                <label for="cmbImportType"><?php echo $lang_DataImport_Type; ?> <span class="required">*</span></label>
                <select name="cmbImportType" id="cmbImportType" class="formSelect" tabindex="1">
                    <option value="0">-- <?php echo $lang_Common_Select;?> --</option>
                <?php
                    foreach ($importTypes as $key=>$importType) {

                        /* mark import types defined in plugins. key is an int for user defined imports
                         and a class name for imports defined in plugin classes. */
                        if (!is_int($key)) {
                            $pluginImportTypesFound = true;
                            $mark = ' (+)';
                        } else {
                            $mark = '';
                        }
                        echo "<option value='" . $key . "' >" . $importType . $mark . "</option>";
                    }
                ?>
                </select>
                <?php if ($pluginImportTypesFound) { ?>
                        <div class="fieldHint"><?php echo $lang_DataImport_PluginsAreMarked; ?></div>
                <?php } ?>
                <div class="fieldHint"><?php echo $lang_DataImport_CustomImportTypesCanBeManaged; ?>
                    <a href='<?php echo $editLink; ?>'><?php echo $lang_DataImport_ClickingHereLink;?></a></div>        
                <br class="clear"/>

                
                <label for="importFile"><?php echo $lang_DataImport_CSVFile; ?> <span class="required">*</span></label>
                <input type="file" name="importFile" id="importFile" tabindex="2"/>
                
                
                <div class="formbuttons">               
                    <input type="button" class="exportbutton" id="btnImport" 
                        onclick="importData();" tabindex="3" 
                        onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                        title="<?php echo $lang_DataImport_Import;?>" value="<?php echo $lang_DataImport_Import;?>" />
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

