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

@session_start();

if(!isset($_SESSION['fname'])) {
	header("Location: ../../login.php");
	exit();
}

if (!defined('ROOT_PATH')) {
	define("ROOT_PATH",$_SESSION['path']);
}

require_once ROOT_PATH . '/lib/models/hrfunct/EmpPhoto.php';
require_once ROOT_PATH . '/lib/extractor/hrfunct/EXTRACTOR_EmpPhoto.php';
require_once ROOT_PATH . '/lib/common/Language.php';
require_once ROOT_PATH . '/lib/common/CommonFunctions.php';
require_once ROOT_PATH . '/language/default/lang_default_full.php';

$lan = new Language();

require_once($lan->getLangPath("full.php"));

$photo = new EmpPicture();
$employeeId = isset($_GET['id']) ? $_GET['id'] : '001';
$edit = $photo->filterEmpPic($employeeId);

$styleSheet = CommonFunctions::getTheme();

if(isset($_GET['action']) && $_GET['action'] == 'VIEW') {

	if($edit) {
		header("Content-length: " .$edit[0][4]);
		header("Content-type: " .$edit[0][3]);
		echo $edit[0][1];
		exit();

	} else {
		// TODO: Use the current theme instead of hard-coded 'beyondT'; Use the $styleSheet variable
		$tmpName = ROOT_PATH . '/themes/beyondT/pictures/default_employee_image.gif';
		$fp = fopen($tmpName,'r');
		$contents = fread($fp,filesize($tmpName));
		fclose($fp);

		header("Content-type: image/gif");
		echo $contents;
		exit();
	}
}

$object = new EmpPicture();
$message = null;

if(isset($_POST['STAT']) && $_POST['STAT'] == 'ADD') {
	$extractor = new EXTRACTOR_EmpPhoto();
	$object = $extractor->parseData();

	if($object != null) {
		$object->setEmpId($_GET['id']);
		$object->addEmpPic();
	} else {
		$message = "FAILURE";
	}
}

if(isset($_POST['STAT']) && $_POST['STAT'] == 'EDIT') {
	$extractor = new EXTRACTOR_EmpPhoto();
	$object = $extractor->parseData();

	if($object != null) {
		$object->setEmpId($_GET['id']);
		$object->updateEmpPic();
	} else {
		$message = "FAILURE";
	}
}

if(isset($_POST['STAT']) && $_POST['STAT'] == 'DELETE') {
	$object = new EmpPicture();
	$object->delEmpPic(array(array($_GET['id'])));
}

$imagePath = "http://localhost:8080/orangehrm/templates/hrfunct/photohandler.php?id={$employeeId}";
?>

<script type="text/javaScript"><!--//--><![CDATA[//><!--
function addPic() {
	$('actionStatus').value = 'ADD';
	_setUploadFormAttributes();
	$('frmEmp').submit();
}

function updatePic() {
	$('actionStatus').value = 'EDIT';
	_setUploadFormAttributes();
	$('frmEmp').submit();
}

function deletePic() {

	if (!confirm('<?php echo $lang_hremp_AreYouSureYouWantToDeleteThePhotograph; ?>?')) {
		return false;
	}

	$('actionStatus').value = 'DELETE';
	_setUploadFormAttributes();
	$('frmEmp').submit();
}

function _setUploadFormAttributes() {
	$('frmEmp').encoding = 'multipart/form-data';
	$('frmEmp').action = '<?php echo $imagePath; ?>';
}
function windowClose() {
	opener.document.frmEmp.submit();
	window.close();
}

<?php if (isset($message) && ($message == "FAILURE")) { ?>
	alert('<?php echo $lang_Error_UploadFailed; ?>');
<?php } else if (isset($_POST['STAT'])) { ?>
	windowClose();
<?php } ?>

//--><!]]></script>
<style type="text/css">
	#currentImage {
	    padding: 2px;
	    margin: 14px 4px 14px 8px;
	    border: 1px solid #FAD163;
	}

	#imageSizeRule {
		width:200px;
	}

</style>


<div class="addPane" >
	<input type="hidden" name="STAT" id="actionStatus" />
	<input type="hidden" name="MAX_FILE_SIZE" value="1000000" />

	<span>
		<img id="currentImage" style="width:100px; height:120px;" alt="Employee Photo"
			src="<?php echo "{$imagePath}&action=VIEW"; ?>" />
	</span>
	<br />
	<label for="photofile"><?php echo $lang_hremp_SelectAPhoto; ?></label>
	<input type="file" name="photofile" id="photofile" class="formFileInput" accept="image/gif,image/jpeg,image/png" />
	<label for="photofile" id="imageSizeRule">[<?php echo $lang_hremp_PhotoMaxSize;?>] [<?php echo $lang_hremp_PhotoDimensions;?>]</label>
	<br />
	&nbsp;
	<div class="formbuttons">
	<?php if($edit) { ?>
		<input type="button" value="<?php echo $lang_Common_Save; ?>" class="savebutton"
			onmouseout="moutButton(this)" onmouseover="moverButton(this)"
			onclick="updatePic();" />
		<input type="button" value="<?php echo $lang_Common_Delete; ?>" class="delbutton"
			onmouseout="moutButton(this)" onmouseover="moverButton(this)"
			onclick="deletePic();" />
	<?php } else { ?>
		<input type="button" value="<?php echo $lang_Common_Save; ?>" class="savebutton"
			onmouseout="moutButton(this)" onmouseover="moverButton(this)"
			onclick="addPic();" />
	<?php } ?>
	</div>
</div>