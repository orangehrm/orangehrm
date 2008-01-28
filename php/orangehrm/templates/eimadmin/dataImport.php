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
$editLink = './CentralController.php?uniqcode=CIM&VIEW=MAIN';

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title><?php echo $lang_DataImport_Title; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="../../themes/<?php echo $styleSheet;?>/css/style.css" rel="stylesheet" type="text/css">
<link href="../../themes/<?php echo $styleSheet;?>/css/leave.css" rel="stylesheet" type="text/css" />
<style type="text/css">
@import url("../../themes/<?php echo $styleSheet;?>/css/style.css");

</style>
</head>
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script type="text/javascript" >

	function importData() {

		if (validate()) {
			$('sqlState').value = 'NewRecord';
			return true;
		}
		return false;

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

		return true;
	}

</script>
<body>
<h2><?php echo $lang_DataImport_Title; ?><hr/></h2>
<?php $message =  isset($this->getArr['msg']) ? $this->getArr['msg'] : (isset($this->getArr['message']) ? $this->getArr['message'] : null);
	if (isset($message)) {
		$col_def = CommonFunctions::getCssClassForMessage($message);
		$message = "lang_Common_" . $message;
?>
<div class="message">
	<font class="<?php echo $col_def?>" size="-1" face="Verdana, Arial, Helvetica, sans-serif">
		<?php echo (isset($$message)) ? $$message: ""; ?>
	</font>
</div>
<?php }	?>

<form enctype="multipart/form-data" id="frmDataImport" name="frmDataImport" method="post" onsubmit="return importData();"
	action="<?php echo $_SERVER['PHP_SELF']; ?>?uniqcode=IMP&upload=1">
<input type="hidden" name="sqlState" id="sqlState" value=""/>
  <table border="0" cellpadding="0" cellspacing="0" >
    <thead>
      <tr>
        <th class="tableTopLeft"></th>
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
        <td><span class="error">*</span>&nbsp;<?php echo $lang_DataImport_Type; ?></td>
        <td width="5px">&nbsp;</td>
        <td width="50px"><select name="cmbImportType" id="cmbImportType">
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
        </td>
        <td width="5px"></td>
        <td width="25px"></td>
        <td class="tableMiddleRight"></td>
      </tr>
      <tr>
        <td class="tableMiddleLeft"></td>
        <td><span class="error">*</span>&nbsp;<?php echo $lang_DataImport_CSVFile; ?></td>
        <td width="5px">&nbsp;</td>
        <td width="50px"><input type="file" name="importFile" id="importFile"/>
        </td>
        <td width="5px"></td>
        <td width="25px"></td>
        <td class="tableMiddleRight"></td>
      </tr>
	  <tr>
        <td class="tableMiddleLeft"></td>
        <td></td>
        <td width="5px">&nbsp;</td>
        <td width="25px">
	        <input type="submit" class="button" id="btnImport" value="<?php echo $lang_DataImport_Import?>"
	        	title="<?php echo $lang_DataImport_Import?>" name="btnImport" />
	    </td>
	    <td colspan="2"></td>
        <td class="tableMiddleRight"></td>
      </tr>
	  <tr>
        <td class="tableMiddleLeft"></td>
	    <td colspan="5"></td>
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
        <td class="tableBottomRight"></td>
      </tr>
    </tfoot>
  </table>
</form>
<?php if ($pluginImportTypesFound) { ?>
	<span id="notice"><?php echo $lang_DataImport_PluginsAreMarked; ?><br /></span>
<?php } ?>
<span id="notice"><?php echo $lang_DataImport_CustomImportTypesCanBeManaged; ?><a href='<?php echo $editLink; ?>'><?php echo $lang_DataImport_ClickingHereLink;?></a></span>

</body>
</html>
