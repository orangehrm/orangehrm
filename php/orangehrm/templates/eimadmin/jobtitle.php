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

require_once ROOT_PATH . '/lib/controllers/ViewController.php';
require_once ROOT_PATH . '/lib/confs/sysConf.php';
require_once($lan->getLangPath("full.php"));

$GLOBALS['lang_Common_Select'] = $lang_Common_Select;
$GLOBALS['lang_Common_Save'] = $lang_Common_Save;

function assignEmploymentStatus($valArr) {

	$view_controller = new ViewController();
	$ext_jobtitempstat = new EXTRACTOR_JobTitEmpStat();
	$filledObj = $ext_jobtitempstat->parseAddData($valArr);
	$view_controller->addData('JEM',$filledObj);

	$assList = $view_controller->xajaxObjCall($valArr['txtJobTitleID'],'JOB','assigned');
	$unAssList = $view_controller->xajaxObjCall($valArr['txtJobTitleID'],'JOB','unAssigned');

	$objResponse = new xajaxResponse();
	$xajaxFiller = new xajaxElementFiller();
	$xajaxFiller->setDefaultOptionName($GLOBALS['lang_Common_Select']);
	$objResponse = $xajaxFiller->cmbFiller($objResponse,$assList,0,'frmJobTitle','cmbAssEmploymentStatus',0);
	$objResponse = $xajaxFiller->cmbFiller($objResponse,$unAssList,0,'frmJobTitle','cmbUnAssEmploymentStatus',0);
	$objResponse->addAssign('status','innerHTML','');

return $objResponse->getXML();
}



function unAssignEmploymentStatus($jobtit,$empstat) {

	$delArr[0][0] = $jobtit;
	$delArr[1][0] = $empstat;

	$view_controller = new ViewController();
	$view_controller ->delParser('JEM',$delArr);

	$view_controller = new ViewController();
	$assList = $view_controller->xajaxObjCall($jobtit,'JOB','assigned');
	$unAssList = $view_controller->xajaxObjCall($jobtit,'JOB','unAssigned');

	$objResponse = new xajaxResponse();
	$xajaxFiller = new xajaxElementFiller();
	$xajaxFiller->setDefaultOptionName($GLOBALS['lang_Common_Select']);
	$objResponse = $xajaxFiller->cmbFiller($objResponse,$assList,0,'frmJobTitle','cmbAssEmploymentStatus',0);
	$objResponse = $xajaxFiller->cmbFiller($objResponse,$unAssList,0,'frmJobTitle','cmbUnAssEmploymentStatus',0);

	$objResponse->addAssign('status','innerHTML','');

return $objResponse->getXML();
}



function showAddEmpStatForm() {

    $objResponse = new xajaxResponse();
	$objResponse->addScript("document.frmJobTitle.txtEmpStatDesc.disabled = false;");
	$objResponse->addScript("document.frmJobTitle.txtEmpStatDesc.focus();");
	$objResponse->addScript("document.getElementById('layerEmpStat').style.visibility='visible';");
    $objResponse->addScript("document.getElementById('btnEmpStat').onclick=addFormData;");
	$objResponse->addAssign('status','innerHTML','');

return $objResponse->getXML();
}

function showEditEmpStatForm($estatCode) {

	$view_controller = new ViewController();
	$editArr = $view_controller->xajaxObjCall($estatCode,'JOB','editEmpStat');

	$objResponse = new xajaxResponse();
	$objResponse->addScript("document.frmJobTitle.txtEmpStatDesc.disabled = false;");
	$objResponse->addScript("document.frmJobTitle.txtEmpStatID.value = '" .$editArr[0][0]."';");
	$objResponse->addScript("document.frmJobTitle.txtEmpStatDesc.value = '" .$editArr[0][1]."';");
	$objResponse->addScript("document.frmJobTitle.txtEmpStatDesc.focus();");
	$objResponse->addScript("document.frmJobTitle.txtEmpStatDesc.selectAll();");
	$objResponse->addScript("document.getElementById('layerEmpStat').style.visibility='visible';");

    $objResponse->addScript("document.getElementById('btnEmpStat').onclick=editFormData;");
	$objResponse->addAssign('status','innerHTML','');

return $objResponse->getXML();
}



function addExt($arrElements) {

	$view_controller = new ViewController();
	$ext_empstat = new EXTRACTOR_EmployStat();

	$objEmpStat = $ext_empstat->parseAddData($arrElements);
	$view_controller -> addData('EST',$objEmpStat,true);

	$view_controller = new ViewController();
	$unAssEmpStat = $view_controller->xajaxObjCall($arrElements['txtJobTitleID'],'JOB','unAssigned');

	$objResponse = new xajaxResponse();
	$xajaxFiller = new xajaxElementFiller();
	$xajaxFiller->setDefaultOptionName($GLOBALS['lang_Common_Select']);
	$objResponse = $xajaxFiller->cmbFiller($objResponse,$unAssEmpStat,0,'frmJobTitle','cmbUnAssEmploymentStatus',0);
	$objResponse->addScript("document.frmJobTitle.txtEmpStatDesc.value = '';");
	$objResponse->addScript("document.frmJobTitle.txtEmpStatDesc.disabled = true;");
	$objResponse->addScript("document.getElementById('layerEmpStat').style.visibility='hidden';");

	$objResponse->addAssign('status','innerHTML','');

return $objResponse->getXML();
}

function editExt($arrElements) {

	$view_controller = new ViewController();
	$ext_empstat = new EXTRACTOR_EmployStat();

	$objEmpStat = $ext_empstat -> parseEditData($arrElements);
	$view_controller->updateData('EST',$arrElements['txtEmpStatID'],$objEmpStat,true);

	$view_controller = new ViewController();
	$unAssEmpStat = $view_controller->xajaxObjCall($arrElements['txtJobTitleID'],'JOB','unAssigned');

	$objResponse = new xajaxResponse();
	$xajaxFiller = new xajaxElementFiller();
	$xajaxFiller->setDefaultOptionName($GLOBALS['lang_Common_Select']);
	$objResponse = $xajaxFiller->cmbFiller($objResponse,$unAssEmpStat,0,'frmJobTitle','cmbUnAssEmploymentStatus',0);
	$objResponse->addScript("document.frmJobTitle.txtEmpStatID.value = '';");
	$objResponse->addScript("document.frmJobTitle.txtEmpStatDesc.value = '';");
	$objResponse->addScript("document.frmJobTitle.txtEmpStatDesc.disabled = true;");
	$objResponse->addScript("document.getElementById('layerEmpStat').style.visibility='hidden';");

	$objResponse->addAssign('status','innerHTML','');

return $objResponse->getXML();
}

	$objAjax = new xajax();
	$objAjax->registerFunction('assignEmploymentStatus');
	$objAjax->registerFunction('unAssignEmploymentStatus');
	$objAjax->registerFunction('showAddEmpStatForm');
	$objAjax->registerFunction('showEditEmpStatForm');
	$objAjax->registerFunction('addExt');
	$objAjax->registerFunction('editExt');
	$objAjax->processRequests();

	$sysConst = new sysConf();

	$locRights=$_SESSION['localRights'];
	$cookie = $_COOKIE;

  if (isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') {

	$editArr = $this->popArr['editArr'];

	if (!isset($_COOKIE['txtJobTitleID']) || (isset($_COOKIE['txtJobTitleID']) && ($_COOKIE['txtJobTitleID'] != $editArr[0][0]))) {
		unset($cookie);
	}

  }

  if (isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'addmode') {

	if (!isset($_COOKIE['txtJobTitleID']) || (isset($_COOKIE['txtJobTitleID']) && ($_COOKIE['txtJobTitleID'] != ''))) {
		unset($cookie);
	}

  }

  setcookie('txtJobTitleName', 'null', time()-3600, '/');
  setcookie('txtJobTitleDesc', 'null', time()-3600, '/');
  setcookie('txtJobTitleComments', 'null', time()-3600, '/');
  setcookie('cmbJobSpecId', 'null', time()-3600, '/');
  setcookie('cmbPayGrade', 'null', time()-3600, '/');
  setcookie('txtJobTitleID', 'null', time()-3600, '/');

  $themeDir = '../../themes/' . $styleSheet;
   $token = $this->popArr['token'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<?php $objAjax->printJavascript(); ?>
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script type="text/javascript"><!--
//<![CDATA[

	function addSave() {
		if(document.frmJobTitle.txtJobTitleName.value == '') {
			alert ('<?php echo $lang_jobtitle_NameShouldBeSpecified; ?>');
			document.frmJobTitle.txtJobTitleName.focus();
			return false;
		}

		if(isEmpty(document.frmJobTitle.txtJobTitleDesc.value)) { // isEmpty() is defined in scripts/archive.js
			alert ('<?php echo $lang_jobtitle_DescriptionShouldBeSpecified; ?>');
			document.frmJobTitle.txtJobTitleDesc.focus();
			return false;
		}

		document.frmJobTitle.sqlState.value = "NewRecord";

		document.frmJobTitle.submit();
	}

	function goBack() {
		location.href = "./CentralController.php?uniqcode=<?php echo $this->getArr['uniqcode']?>&VIEW=MAIN";
	}

	function edit() {
        var editBtn = $('editBtn');

		if(editBtn.title=='<?php echo $lang_Common_Save;?>') {
			addUpdate();
			return;
		}

		var frm=document.frmJobTitle;

		for (var i=0; i < frm.elements.length; i++) {
			frm.elements[i].disabled = false;
		}

		frm.txtEmpStatDesc.disabled=true;

		editBtn.className = "savebutton";
		editBtn.title = "<?php echo $lang_Common_Save;?>";
        editBtn.value = "<?php echo $lang_Common_Save;?>";
	}

	function addUpdate() {
		if(document.frmJobTitle.txtJobTitleName.value == '') {
			alert ('<?php echo $lang_jobtitle_NameShouldBeSpecified; ?>');
			document.frmJobTitle.txtJobTitleName.focus();

			return false;
		}

		if(isEmpty(document.frmJobTitle.txtJobTitleDesc.value)) { // isEmpty() is defined in scripts/archive.js
			alert ('<?php echo $lang_jobtitle_DescriptionShouldBeSpecified; ?>');
			document.frmJobTitle.txtJobTitleDesc.focus();

			return false;
		}
		
		document.frmJobTitle.sqlState.value = "UpdateRecord";

		document.frmJobTitle.submit();
	}



	function assignEmploymentStatus() {
		if(document.frmJobTitle.cmbUnAssEmploymentStatus.selectedIndex == -1) {
			alert('<?php echo $lang_jobtitle_NoSelection; ?>');
			return;
		}

		document.getElementById('status').innerHTML = '<?php echo $lang_Commn_PleaseWait;?>....';

		xajax_assignEmploymentStatus(xajax.getFormValues('frmJobTitle'));
	}

	function unAssignEmploymentStatus() {
		if(document.frmJobTitle.cmbAssEmploymentStatus.selectedIndex == -1) {
			alert('<?php echo $lang_jobtitle_NoSelection; ?>');
			return;
		}

		document.getElementById('status').innerHTML = '<?php echo $lang_Commn_PleaseWait;?>....';

		xajax_unAssignEmploymentStatus(document.frmJobTitle.txtJobTitleID.value, document.frmJobTitle.cmbAssEmploymentStatus.value);
	}

	function numeric(txt) {
		var flag = true;
		var i, code;

		if (txt.value=="") {
		   return false;
		 }

		for (i=0;txt.value.length>i;i++) {
			code=txt.value.charCodeAt(i);

			if(code>=48 && code<=57 || code==46) {
			   flag=true;
			} else {
			   flag=false;
			   break;
			}
		}

		return flag;
	}



	function editPayGrade() {
		paygrade = document.frmJobTitle.cmbPayGrade.value;

		if(paygrade == '0') {
			alert('<?php echo $lang_jobtitle_PayGradeNotSelected; ?>');
			document.frmJobTitle.cmbPayGrade.focus();
			return;
		}

		document.gotoPayGrade.action = '../../lib/controllers/CentralController.php?uniqcode=SGR&id=' + paygrade + '&capturemode=updatemode';

		document.gotoPayGrade.submit();

	}

	function showEditForm() {
		empstat = document.frmJobTitle.cmbUnAssEmploymentStatus.value;

		if(document.frmJobTitle.cmbUnAssEmploymentStatus.selectedIndex == -1) {
			alert('<?php echo $lang_jobtitle_PleaseSelectEmploymentStatus; ?>');
			document.frmJobTitle.cmbUnAssEmploymentStatus.focus();

			return;
		}

		xajax_showEditEmpStatForm(document.frmJobTitle.cmbUnAssEmploymentStatus.value);
	}

	function addFormData() {
		if(document.frmJobTitle.txtEmpStatDesc.value == '') {
			alert('<?php echo $lang_jobtitle_EnterEmploymentStatus; ?>');
			document.frmJobTitle.txtEmpStatDesc.focus();

			return;
		}

		document.getElementById('status').innerHTML = '<?php echo $lang_Commn_PleaseWait;?>....';

		xajax_addExt(xajax.getFormValues('frmJobTitle'));
	}

	function editFormData() {
		if(document.frmJobTitle.txtEmpStatDesc.value == '') {
			alert('<?php echo $lang_jobtitle_EnterEmploymentStatus; ?>');
			document.frmJobTitle.txtEmpStatDesc.focus();

			return;
		}

		document.getElementById('status').innerHTML = '<?php echo $lang_Commn_PleaseWait;?>....';

		xajax_editExt(xajax.getFormValues('frmJobTitle'));
	}

	function preserveData() {
		if (!(document.getElementById('txtJobTitleName').disabled)) {
			id="txtJobTitleID";
			writeCookie(id,document.getElementById('txtJobTitleID').value);

			id="txtJobTitleName";
			writeCookie(id,document.getElementById('txtJobTitleName').value);

			id="txtJobTitleDesc";
			writeCookie(id,document.getElementById('txtJobTitleDesc').value);

			id="txtJobTitleComments";
			writeCookie(id,document.getElementById('txtJobTitleComments').value);

			id="cmbJobSpecId";
			writeCookie(id, document.getElementById(id).value);

			id="cmbPayGrade";
			writeCookie(id,document.getElementById('cmbPayGrade').value);
		}
	}

	function writeCookie(name, value, expire) {
		if (!expire) {
			expire = 3600000;
		}

		var date = new Date();
		date.setTime(date.getTime()+expire);
		var expires = date.toGMTString();

		document.cookie = name+"="+value+"; expires="+expires+"; path=/";
	}

	function promptUseCookieValues() {
		if (!confirm('<?php echo $lang_jobtitle_ShowingSavedValues . "\\n" . $lang_Error_DoYouWantToContinue; ?>')) {
			history.go();
		}
	}

	function addSalaryGrade() {
		document.gotoPayGrade.action =  '../../lib/controllers/CentralController.php?uniqcode=SGR&capturemode=addmode';

		document.gotoPayGrade.submit();
	}

	function editSalaryGrade() {
		editPayGrade(document.frmJobTitle.cmbPayGrade.value);
	}

	function clearAll() {
		document.frmJobTitle.txtJobTitleName.value = '';
		document.frmJobTitle.txtJobTitleDesc.value = '';
		document.frmJobTitle.txtJobTitleComments.value = '';
		document.frmJobTitle.cmbJobSpecId.value = -1;
		document.frmJobTitle.cmbPayGrade.value = 0;
	}
//]]>
--></script>
<script type="text/javascript" src="../../themes/<?php echo $styleSheet;?>/scripts/style.js"></script>
<link href="../../themes/<?php echo $styleSheet;?>/css/style.css" rel="stylesheet" type="text/css"/>
<!--[if lte IE 6]>
<link href="../../themes/<?php echo $styleSheet; ?>/css/IE6_style.css" rel="stylesheet" type="text/css"/>
<![endif]-->
<!--[if IE]>
<link href="../../themes/<?php echo $styleSheet; ?>/css/IE_style.css" rel="stylesheet" type="text/css"/>
<![endif]-->

<style type="text/css">

    .roundbox {
        margin-top: 10px;
        margin-left: 0px;
        width: 625px;
    }

    .roundbox_content {
        padding:15px;
    }

	.controlLabel {
		width: 135px;
		float: left;
		padding-right: 10px;
		padding-left: 15px;
	}

	.controlContainer {
		padding-top: 4px;
		padding-bottom: 4px;
		vertical-align: top;
	}

</style>

</head>

<body onload="<?php echo (isset($cookie) && isset($this->getArr['capturemode']) && ($this->getArr['capturemode'] == 'updatemode'))? 'edit();' : '' ?><?php echo isset($cookie) ? 'promptUseCookieValues();' : '' ?>">

<form id="gotoPayGrade" name="gotoPayGrade" action="../../lib/controllers/CentralController.php?uniqcode=SGR&amp;capturemode=addmode" method="post">
	<input type="hidden" name="referer" value="<?php echo $_SERVER['REQUEST_URI'];?>" />
</form>
<div id="status" style="width: 20%; text-align: right; position: absolute; right: 0px; top: 5px;"></div>

<div class="formpage2col">
    <div class="navigation">
    	<input type="button" class="savebutton"
	        onclick="goBack();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
	        value="<?php echo $lang_Common_Back;?>" />
    </div>
    <div class="outerbox">
        <div class="mainHeading"><h2><?php echo $lang_jobtitle_heading;?></h2></div>

<?php if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'addmode') { ?>

		<form id="frmJobTitle" name="frmJobTitle" method="POST" action="<?php echo $_SERVER['PHP_SELF']?>?uniqcode=<?php echo $this->getArr['uniqcode']?>" onsubmit="return addSave()">
      <input type="hidden" name="token" value="<?php echo $token;?>" />
		<input type="hidden" name="sqlState" />
		<input type="hidden" name="txtJobTitleID" id="txtJobTitleID" value="" />

    	<label for="txtJobTitleName" class="controlLabel"><?php echo $lang_jobtitle_jobtitname;?><span class="required">*</span></label>
    	<input type="text" name="txtJobTitleName" id="txtJobTitleName" value="<?php echo isset($cookie['txtJobTitleName'])? $cookie['txtJobTitleName'] : ''?>"
            class="formInputText"/>
        <br class="clear"/>

		<label for="txtJobTitleDesc" class="controlLabel"><?php echo $lang_jobtitle_jobtitdesc;?><span class="required">*</span></label>
		<textarea name="txtJobTitleDesc" id="txtJobTitleDesc" class="formTextArea"><?php echo isset($cookie['txtJobTitleDesc']) ? $cookie['txtJobTitleDesc'] : ''?></textarea>
        <br class="clear"/>

		<label for="txtJobTitleComments" class="controlLabel"><?php echo $lang_jobtitle_jobtitcomments; ?></label>
		<textarea name="txtJobTitleComments" id="txtJobTitleComments" class="formTextArea"><?php echo isset($cookie['txtJobTitleComments']) ? $cookie['txtJobTitleComments'] : ''?></textarea>
        <br class="clear"/>

		<label for="cmbJobSpecId" class="controlLabel"><?php echo $lang_jobtitle_jobspec; ?></label>
		<select name="cmbJobSpecId" id="cmbJobSpecId" style="width: 150px;" class="formSelect">
			<option value='-1'>--<?php echo $lang_Leave_Common_Select; ?>--</option>
			<?php
				$jobSpecs = $this->popArr['jobSpecList'];
				$selectedSpecId = isset($cookie['cmbJobSpecId']) ? $cookie['cmbJobSpecId'] : null;

				foreach($jobSpecs as $jobSpec) {
					$selected = ($selectedSpecId == $jobSpec->getId()) ? 'selected' : '';
			?>
					<option <?php echo $selected; ?> value="<?php echo $jobSpec->getId();?>"> <?php echo $jobSpec->getName();?></option>
			<?php   } ?>
		</select>
        <br class="clear"/>

		<label for="cmbPayGrade" class="controlLabel"><?php echo $lang_hrEmpMain_paygrade; ?></label>
		<select name="cmbPayGrade" id="cmbPayGrade" style="width: 150px;" class="formSelect">
			<option value='0'>--<?php echo $lang_Leave_Common_Select; ?>--</option>
			<?php
				$paygrade = $this->popArr['paygrade'];

				for($c=0;$paygrade && count($paygrade)>$c;$c++) { ?>
					<option <?php echo (isset($cookie['cmbPayGrade']) && ($cookie['cmbPayGrade'] == $paygrade[$c][0])) ? 'selected' : '' ?> value="<?php echo $paygrade[$c][0]?>">
						<?php echo $paygrade[$c][1]?>
					</option>
			<?php	} ?>
		</select>
		<div style="padding: 10px 0 2px 10px;">
            &nbsp;
            <input type="button" class="longbtn"
                onclick="preserveData(); addSalaryGrade();"
                onmouseover="this.className='longbtn longbtnhov'" onmouseout="this.className='longbtn'"
                value="<?php echo $lang_jobtitle_addpaygrade;?>" />
            &nbsp;
            <input type="button" class="longbtn"
                onclick="preserveData(); editSalaryGrade();"
                onmouseover="this.className='longbtn longbtnhov'" onmouseout="this.className='longbtn'"
                value="<?php echo $lang_jobtitle_editpaygrade;?>" />
		</div>
        <br class="clear"/>

        <div class="formbuttons">

            <input type="button" class="savebutton"
                onclick="addSave();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                value="<?php echo $lang_Common_Save;?>" />
            <input type="button" class="clearbutton" onclick="clearAll();"
                onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                 value="<?php echo $lang_Common_Reset;?>" />
        </div>

	</form>


<?php } elseif (isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>

	<form id="frmJobTitle" name="frmJobTitle" method="POST" action="<?php echo $_SERVER['PHP_SELF']?>?id=<?php echo $this->getArr['id']?>&uniqcode=<?php echo $this->getArr['uniqcode']?>&capturemode=updatemode" onsubmit="return addUpdate()">
      <input type="hidden" name="token" value="<?php echo $token;?>" />
		<input type="hidden" name="sqlState" />
		<input type="hidden" name="txtJobTitleID" id="txtJobTitleID" value="<?php echo $editArr[0][0]?>" />
		<label class="formLabel"><?php echo $lang_jobtitle_jobtitid; ?></label>
		<span class="formValue"><?php echo $editArr[0][0]?></span>
        <br class="clear"/>

		<label for="txtJobTitleName"><?php echo $lang_jobtitle_jobtitname;?><span class="required">*</span></label>
		<input type="text" class="formInputText" disabled="disabled" name="txtJobTitleName" id="txtJobTitleName"
            value="<?php echo isset($cookie['txtJobTitleName']) ? $cookie['txtJobTitleName'] : $editArr[0][1]?>" />
        <br class="clear"/>

		<label for="txtJobTitleDesc"><?php echo $lang_jobtitle_jobtitdesc;?><span class="required">*</span></label>
        <textarea disabled="disabled" name="txtJobTitleDesc" id="txtJobTitleDesc" cols="30" class="formTextArea"
            rows="3"><?php echo isset($cookie['txtJobTitleDesc']) ? $cookie['txtJobTitleDesc'] : $editArr[0][2]?></textarea>
        <br class="clear"/>

		<label for="txtJobTitleComments"><?php echo $lang_jobtitle_jobtitcomments; ?></label>
		<textarea disabled="disabled" name="txtJobTitleComments" id="txtJobTitleComments" rows="3" class="formTextArea"
            cols="30"><?php echo isset($cookie['txtJobTitleComments']) ? $cookie['txtJobTitleComments'] : $editArr[0][3]?></textarea>
        <br class="clear"/>


		<label for="cmbJobSpecId"><?php echo $lang_jobtitle_jobspec; ?></label>
		<select disabled="disabled" name="cmbJobSpecId" id="cmbJobSpecId" class="formSelect">
        	<option value='-1'>--<?php echo $lang_Leave_Common_Select; ?>--</option>
            <?php
				$jobSpecs = $this->popArr['jobSpecList'];
				$selectedSpecId = isset($cookie['cmbJobSpecId']) ? $cookie['cmbJobSpecId'] : $editArr[0][5];

				foreach($jobSpecs as $jobSpec) {
					$selected = ($selectedSpecId == $jobSpec->getId()) ? 'selected' : '';
			?>
					<option <?php echo $selected; ?> value="<?php echo $jobSpec->getId();?>"> <?php echo $jobSpec->getName();?></option>
			<?php   } ?>
		</select>
		<br class="clear"/>

		<label for="cmbPayGrade"><?php echo $lang_hrEmpMain_paygrade; ?></label>
		<select disabled="disabled" name="cmbPayGrade" id="cmbPayGrade" class="formSelect">
			<option value='0'>--<?php echo $lang_Leave_Common_Select; ?>--</option>
			<?php
				$paygrade = $this->popArr['paygrade'];

			    for($c=0;$paygrade && count($paygrade)>$c;$c++)
			    	if ((isset($cookie['cmbPayGrade']) && ($cookie['cmbPayGrade'] == $paygrade[$c][0])) || ((!isset($cookie['cmbPayGrade'])) && ($paygrade[$c][0] == $editArr[0][4]))) {
						echo "<option selected value='" .$paygrade[$c][0]. "'>" .$paygrade[$c][1]. "</option>";
					} else {
						echo "<option value='" .$paygrade[$c][0]. "'>" .$paygrade[$c][1]. "</option>";
					}
				?>
		</select>

        <input type="button" class="longbtn"
            onclick="preserveData(); addSalaryGrade();" value="<?php echo $lang_jobtitle_addpaygrade; ?>"
            disabled="disabled" style="margin:10px 2px 0 10px;"
            onmouseover="moverButton(this);" onmouseout="moutButton(this);"/>
        <input type="button" class="longbtn" onclick="preserveData(); editSalaryGrade();"
            value="<?php echo $lang_jobtitle_editpaygrade; ?>"  disabled="disabled" style="margin:10px 2px 0 5px;"
            onmouseover="moverButton(this);" onmouseout="moutButton(this);"/>

        <br class="clear"/>


		<label for="cmbAssEmploymentStatus"><?php echo $lang_jobtitle_empstat; ?><span class="success">#</span></label>
		<select disabled="disabled" size="3" name="cmbAssEmploymentStatus" id="cmbAssEmploymentStatus"
            class="formSelect" style="width:150px; height: 50px;">
		<?php
			$assEmploymentStat = $this->popArr['assEmploymentStat'];

			for($c=0;$assEmploymentStat && count($assEmploymentStat)>$c;$c++) {
				echo "<option value='" .$assEmploymentStat[$c][0]. "'>" .$assEmploymentStat[$c][1]. "</option>";
			}
		?>
		</select>

		<div style="margin: 10px 10px 0 10px; float: left;">
            <input type="button" disabled="disabled" name="butAssEmploymentStatus"
                onclick="assignEmploymentStatus();" value="&lt; <?php echo $lang_compstruct_add; ?>"
                style="width: 100px;" class="plainbtn" onmouseover="moverButton(this);" onmouseout="moutButton(this);" />
			<br />
            <input type="button" disabled="disabled" name="butUnAssEmploymentStatus"
                onclick="unAssignEmploymentStatus();" value="<?php echo $lang_Leave_Common_Remove; ?> &gt;"
                style="width: 100px;margin-top:10px;" class="plainbtn" onmouseover="moverButton(this);" onmouseout="moutButton(this);" />
		</div>

		<select disabled="disabled" size="3" name="cmbUnAssEmploymentStatus" id="cmbUnAssEmploymentStatus"
            class="formSelect" style="width:150px; height: 50px;">
		<?php
			$unAssEmploymentStat = $this->popArr['unAssEmploymentStat'];

		    for($c=0;$unAssEmploymentStat && count($unAssEmploymentStat)>$c;$c++) {
				echo "<option value='" .$unAssEmploymentStat[$c][0]. "'>" .$unAssEmploymentStat[$c][1]. "</option>";
			}
		?>
		</select>
		<br class="clear"/>

		<div class="controlContainer" style="padding-top: 20px; padding-left: 10px;">
            <input type="button" disabled="disabled" name="butUnAssEmploymentStatus"
                value="<?php echo $lang_jobtitle_addempstat; ?>" onclick="xajax_showAddEmpStatForm();"
                class="extralongbtn" onmouseover="moverButton(this);" onmouseout="moutButton(this);" />
			<br /><br />
            <input type="button" disabled="disabled" name="butUnAssEmploymentStatus"
                value="<?php echo $lang_jobtitle_editempstat; ?>" onclick="showEditForm();"
                class="extralongbtn" onmouseover="moverButton(this);" onmouseout="moutButton(this);" />
		</div>
		<div id="layerEmpStat" style="visibility: hidden;">
			<input type="hidden" name="txtEmpStatID" />
			<label for="txtEmpStatDesc"><?php echo $lang_jobtitle_empstat; ?></label>
			<input type="text" name="txtEmpStatDesc" id="txtEmpStatDesc" class="formInputText" disabled="disabled" style="width: 200px" />
            <input type="button" class="savebutton" id="btnEmpStat"
                onmouseover="moverButton(this);" onmouseout="moutButton(this);" style="margin:10px 0 0 5px;"
                value="<?php echo $lang_Common_Save;?>" onclick="addFormData();" >
		</div>

        <div class="formbuttons">
<?php if($locRights['edit']) { ?>
            <input type="button" class="editbutton" id="editBtn"
                onclick="edit();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                title="<?php echo $lang_Common_Edit;?>"
                value="<?php echo $lang_Common_Edit;?>" />

            <input type="button" class="clearbutton" onclick="$('frmJobTitle').reset();"
                onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                 value="<?php echo $lang_Common_Reset;?>" />
<?php } ?>
        </div>

	</form>

<div style="padding-top: 10px;">

</div>

<?php } ?>
</div>
<div class="requirednotice"><?php echo preg_replace('/#star/', '<span class="required">*</span>', $lang_Commn_RequiredFieldMark); ?>.</div>
<br />
<div class="requirednotice"><span class="success">#</span> = <?php echo $lang_jobtitle_emstatExpl; ?></div>
<script type="text/javascript">
//<![CDATA[
    if (document.getElementById && document.createElement) {
        roundBorder('outerbox');
    }
//]]>
</script>

</body>
</html>
