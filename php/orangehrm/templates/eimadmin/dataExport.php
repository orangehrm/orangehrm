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
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title><?php echo $lang_DataExport_Title; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="../../themes/<?php echo $styleSheet;?>/css/style.css" rel="stylesheet" type="text/css">
<link href="../../themes/<?php echo $styleSheet;?>/css/leave.css" rel="stylesheet" type="text/css" />
<style type="text/css">
@import url("../../themes/<?php echo $styleSheet;?>/css/style.css");

</style>
</head>
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script type="text/javascript" >

	function exportData() {

		if (!validate()) {
			return;
		}
		var url = "<?php echo $_SERVER['PHP_SELF']; ?>?uniqcode=EXP&download=1&cmbExportType=" + $('cmbExportType').value;

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

</script>
<body>
<h2><?php echo $lang_DataExport_Title; ?><hr/></h2>
<form id="frmDataExport" name="frmDataExport" method="post" >
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
        <td><span class="error">*</span>&nbsp;<?php echo $lang_DataExport_Type; ?></td>
        <td width="5px">&nbsp;</td>
        <td width="50px"><select name="cmbExportType" id="cmbExportType">
        		<option value="0">-- <?php echo $lang_Common_Select;?> --</option>
        		<?php
        		    foreach ($exportTypes as $key=>$exportType) {
        		    	echo "<option value='" . $key . "' >" . $exportType . "</option>";
        		    }
        		?>
        	</select>
        </td>
        <td width="5px"></td>
        <td width="25px">
	        <input type="button" class="button" id="btnExport" value="<?php echo $lang_DataExport_Export?>"
	        	title="<?php echo $lang_DataExport_Export?>" name="btnExport" onclick="exportData();"/>
	    </td>
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
<span id="notice"><?php echo preg_replace('/#star/', '<span class="error">*</span>', $lang_Commn_RequiredFieldMark); ?>.</span>
</body>
</html>
