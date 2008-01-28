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

$importStatus = $this->popArr['importStatus'];
$backLink = './CentralController.php?uniqcode=IMP';

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title><?php echo $lang_DataImportStatus_Title; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="../../themes/<?php echo $styleSheet;?>/css/style.css" rel="stylesheet" type="text/css">
<link href="../../themes/<?php echo $styleSheet;?>/css/leave.css" rel="stylesheet" type="text/css" />
<style type="text/css">
@import url("../../themes/<?php echo $styleSheet;?>/css/style.css");
</style>
<script language="javascript">
	function goBack() {
		location.href = '<?php echo $backLink; ?>';
	}
</script>
</head>
<body>
<h2><?php echo $lang_DataImportStatus_Title; ?><hr/></h2>
	<img title="Back" onMouseOut="this.src='../../themes/beyondT/pictures/btn_back.gif';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_back_02.gif';"  src="../../themes/beyondT/pictures/btn_back.gif" onClick="goBack();">
	<br/>
<?php
	if ($importStatus->getNumFailed() == 0) {
		$style = "success";
		if ($importStatus->getNumImported() == 0) {
			// No failures, nothing to import
			$message = $lang_DataImportStatus_NothingImported;
		} else {
			// import success
			$message = $lang_DataImportStatus_ImportSuccess;
		}
	} else {
		$style = "error";
		if ($importStatus->getNumImported() == 0) {
			// all failures
			$message = $lang_DataImportStatus_ImportFailed;
		} else {
			// some successes, some failures
			$message = $lang_DataImportStatus_ImportSomeFailed;
		}
	}
?>
<div class="message">
	<font class="<?php echo $style;?>" size="-1" face="Verdana, Arial, Helvetica, sans-serif">
		<?php echo $message; ?>
	</font>
</div>

<!-- Import status summary -->
<h3><?php echo $lang_DataImportStatus_Summary; ?></h3>
<table border="0" cellpadding="0" cellspacing="0" >
	<tr>
        <td><?php echo $lang_DataImportStatus_NumImported; ?></td><td width="5"></td>
        <td><?php echo $importStatus->getNumImported();?></td>
	</tr>
	<tr>
        <td><?php echo $lang_DataImportStatus_NumFailed; ?></td><td width="5"></td>
        <td><?php echo $importStatus->getNumFailed();?></td>
	</tr>
	<tr>
        <td><?php echo $lang_DataImportStatus_NumSkipped; ?></td><td width="5"></td>
        <td><?php echo $importStatus->getNumSkipped();?></td>
	</tr>
</table>

<?php
if ($importStatus->getNumFailed() > 0) {
?>
<!-- Details of failed rows -->
<h3><?php echo $lang_DataImportStatus_Details; ?></h3>
<table border="0" cellpadding="0" cellspacing="0" >
	<thead>
      <tr>
        <th class="tableTopLeft"></th>
        <th class="tableTopMiddle"></th>
        <th class="tableTopMiddle"></th>
        <th class="tableTopMiddle"></th>
        <th class="tableTopRight"></th>
      </tr>
    </thead>
    <tbody>
		<tr>
			<th class="tableMiddleLeft"></th>
			<th width="40px" class="tableMiddleMiddle"><?php echo $lang_DataImportStatus_Heading_Row; ?></th>
			<th width="150px" class="tableMiddleMiddle"><?php echo $lang_DataImportStatus_Heading_Error; ?></th>
			<th width="250px" class="tableMiddleMiddle"><?php echo $lang_DataImportStatus_Heading_Comments; ?></th>
			<th class="tableMiddleRight"></th>
		</tr>

<?php
	$odd = false;
	foreach($importStatus->getImportResults() as $row=>$result) {
 		$cssClass = ($odd) ? 'even' : 'odd';
 		$odd = !$odd;
 		$resource = 'lang_DataImportStatus_Error_' . $result->getStatus();;
 		$msg = isset($$resource) ? $$resource : $result->getStatus();
?>
	<tr>
		<td class="tableMiddleLeft"></td>
		<td class="<?php echo $cssClass;?>"><?php echo ($row + 1);?></td>
		<td class="<?php echo $cssClass;?>"><?php echo $msg;?></td>
		<td class="<?php echo $cssClass;?>"><?php echo $result->getComments();?></td>
		<td class="tableMiddleRight"></td>
	</tr>
<?php
	}
?>
    </tbody>
    <tfoot>
      <tr>
        <td class="tableBottomLeft"></td>
        <td class="tableBottomMiddle"></td>
        <td class="tableBottomMiddle"></td>
        <td class="tableBottomMiddle"></td>
        <td class="tableBottomRight"></td>
      </tr>
    </tfoot>
</table>
<?php
}
?>
</body>
</html>
