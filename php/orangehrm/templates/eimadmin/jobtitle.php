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
	//$parent::
	$objResponse->addAssign('layerEmpStat','style','visibility:hidden;');
	$objResponse->addAssign('buttonLayer','innerHTML',"<input type='button' value='".$GLOBALS['lang_Common_Save']."' onClick='addFormData();'>");
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

	$objResponse->addAssign('buttonLayer','innerHTML',"<input type='button' value='".$GLOBALS['lang_Common_Save']."' onClick='editFormData();'>");
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

	$objResponse->addAssign('buttonLayer','innerHTML','');
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

	$objResponse->addAssign('buttonLayer','innerHTML','');
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
  setcookie('cmbPayGrade', 'null', time()-3600, '/');
  setcookie('txtJobTitleID', 'null', time()-3600, '/');

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>

<head>

<title></title>
<?php $objAjax->printJavascript(); ?>
<script language="JavaScript">



	function addSave() {



		if(document.frmJobTitle.txtJobTitleName.value == '') {

			alert ('<?php echo $lang_jobtitle_NameShouldBeSpecified; ?>');

			document.frmJobTitle.txtJobTitleName.focus();

			return;

			}



		if(document.frmJobTitle.txtJobTitleDesc.value == '') {

			alert ('<?php echo $lang_jobtitle_DescriptionShouldBeSpecified; ?>');

			document.frmJobTitle.txtJobTitleDesc.focus();

			return;

			}



		if(document.frmJobTitle.cmbPayGrade.value == '0') {

			alert ('<?php echo $lang_jobtitle_PayGradeNotSelected; ?>');

			document.frmJobTitle.cmbPayGrade.focus();

			return;

			}



		document.frmJobTitle.sqlState.value = "NewRecord";

		document.frmJobTitle.submit();

	}



function goBack() {



		location.href = "./CentralController.php?uniqcode=<?php echo $this->getArr['uniqcode']?>&VIEW=MAIN";

	}



function mout() {

	if(document.Edit.title=='Save')

		document.Edit.src='../../themes/beyondT/pictures/btn_save.jpg';

	else

		document.Edit.src='../../themes/beyondT/pictures/btn_edit.jpg';

}



function mover() {

	if(document.Edit.title=='Save')

		document.Edit.src='../../themes/beyondT/pictures/btn_save_02.jpg';

	else

		document.Edit.src='../../themes/beyondT/pictures/btn_edit_02.jpg';

}



function edit() {

	if(document.Edit.title=='Save') {

		addUpdate();

		return;

	}



	var frm=document.frmJobTitle;


	for (var i=0; i < frm.elements.length; i++)

		frm.elements[i].disabled = false;

	frm.txtEmpStatDesc.disabled=true;

	document.Edit.src="../../themes/beyondT/pictures/btn_save.jpg";

	document.Edit.title="Save";

}



	function addUpdate() {

		if(document.frmJobTitle.txtJobTitleName.value == '') {

			alert ('<?php echo $lang_jobtitle_NameShouldBeSpecified; ?>');

			document.frmJobTitle.txtJobTitleName.focus();

			return;

			}



		if(document.frmJobTitle.txtJobTitleDesc.value == '') {

			alert ('<?php echo $lang_jobtitle_DescriptionShouldBeSpecified; ?>');

			document.frmJobTitle.txtJobTitleDesc.focus();

			return;

			}



		if(document.frmJobTitle.cmbPayGrade.value == '0') {

			alert ('<?php echo $lang_jobtitle_PayGradeNotSelected; ?>');

			document.frmJobTitle.cmbPayGrade.focus();

			return;

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



var flag=true;

var i,code;



if(txt.value=="")

   return false;



for(i=0;txt.value.length>i;i++)

	{

	code=txt.value.charCodeAt(i);

    if(code>=48 && code<=57 || code==46)

	   flag=true;

	else

	   {

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
	document.frmJobTitle.cmbPayGrade.value = 0;
}
</script>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<link href="../../themes/beyondT/css/style.css" rel="stylesheet" type="text/css">

<style type="text/css">@import url("../../themes/beyondT/css/style.css"); </style>

</head>

<body onLoad="<?php echo (isset($cookie) && isset($this->getArr['capturemode']) && ($this->getArr['capturemode'] == 'updatemode'))? 'edit();' : '' ?><?php echo isset($cookie) ? 'promptUseCookieValues();' : '' ?>">

<form id="gotoPayGrade" name="gotoPayGrade" action="../../lib/controllers/CentralController.php?uniqcode=SGR&capturemode=addmode" method="post">
<input type="hidden" name="referer" value="<?php echo $_SERVER['REQUEST_URI'];?>">
</form>

<?php if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'addmode') { ?>

<table width='100%' cellpadding='0' cellspacing='0' border='0'>

  <tr>

    <td valign='top'></td>

    <td width='100%'><h2><?php echo $lang_jobtitle_heading; ?></h2></td>

    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'><b><div id="status"></div></b></td>

  </tr>

</table>

<br>

<img title="Back" onMouseOut="this.src='../../themes/beyondT/pictures/btn_back.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_back_02.jpg';"  src="../../themes/beyondT/pictures/btn_back.jpg" onClick="goBack();">

<br>

		<form id="frmJobTitle" name="frmJobTitle" method="POST" action="<?php echo $_SERVER['PHP_SELF']?>?uniqcode=<?php echo $this->getArr['uniqcode']?>">

		<input type="hidden" name="sqlState">

           <table border="0" cellpadding="0" cellspacing="0">

                <tr>

                  <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>

                  <td width="339" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>

                  <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>

                  <td width="11"><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="12" border="0" alt=""></td>

                </tr>

                <tr>

                  <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>

                  <td><table width="100%" border="0" cellpadding="5" cellspacing="0" class="">

				                  <tr>
				                  		<td><input type="hidden" name="txtJobTitleID" id="txtJobTitleID" value=""></td>
				                  </tr>

				                  <tr>

				                  		<td><span class="error">*</span><?php echo $lang_jobtitle_jobtitname;?></td>

				                  		<td><input type="text" name="txtJobTitleName" id="txtJobTitleName" value="<?php echo isset($cookie['txtJobTitleName'])? $cookie['txtJobTitleName'] : ''?>"></td>

				                  </tr>

				                  <tr>

				                  		<td><span class="error">*</span><?php echo $lang_jobtitle_jobtitdesc;?></td>

				                  		<td><textarea name="txtJobTitleDesc" id="txtJobTitleDesc"><?php echo isset($cookie['txtJobTitleDesc']) ? $cookie['txtJobTitleDesc'] : ''?></textarea></td>

				                  </tr>

				                  <tr>

				                  		<td><?php echo $lang_jobtitle_jobtitcomments; ?></td>

				                  		<td><textarea name="txtJobTitleComments" id="txtJobTitleComments"><?php echo isset($cookie['txtJobTitleComments']) ? $cookie['txtJobTitleComments'] : ''?></textarea></td>

				                  </tr>

				                  <tr>

				                  		<td><span class="error">*</span> <?php echo $lang_hrEmpMain_paygrade; ?></td>

				                  		<td><select name="cmbPayGrade" id="cmbPayGrade">

				               				<option value='0'>--<?php echo $lang_Leave_Common_Select; ?>--</option>

				               			<?php $paygrade = $this->popArr['paygrade'];

				               				for($c=0;$paygrade && count($paygrade)>$c;$c++) {?>

				               					<option <?php echo (isset($cookie['cmbPayGrade']) && ($cookie['cmbPayGrade'] == $paygrade[$c][0])) ? 'selected' : '' ?> value="<?php echo $paygrade[$c][0]?>"><?php echo $paygrade[$c][1]?></option>

										<?php	} ?>



				                  		</select></td>

				                  		<td><input type="button" onClick="preserveData(); addSalaryGrade();" value="<?php echo $lang_jobtitle_addpaygrade; ?>" />

				                  		<input type="button" onClick="preserveData(); editSalaryGrade();" value="<?php echo $lang_jobtitle_editpaygrade; ?>"  /></td>

				                  </tr>

					  <tr><td></td><td align="right"><img onClick="addSave();" onMouseOut="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">

        				<img onClick="clearAll();" onMouseOut="this.src='../../themes/beyondT/pictures/btn_clear.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_clear_02.jpg';" src="../../themes/beyondT/pictures/btn_clear.jpg"></td></tr>

                  </table></td>

                  <td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>

                  <td><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>

                </tr>

                <tr>

                  <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>

                  <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>

                  <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>

                  <td><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="16" border="0" alt=""></td>

                </tr>

              </table>



</form>
<span id="notice"><?php echo preg_replace('/#star/', '<span class="error">*</span>', $lang_Commn_RequiredFieldMark); ?>.</span>
</body>

</html>



<?php } elseif (isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') {


?>

<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>

  <tr>

    <td valign='top'></td>

    <td width='100%'><h2><?php echo $lang_jobtitle_heading; ?></h2></td>

    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'><b><div id="status"></div></b></td>

  </tr>

</table>

           <table border="0" cellpadding="0" cellspacing="0">

                <tr>

                  <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>

                  <td width="500" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>

                  <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>

                  <td width="11"><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="12" border="0" alt=""></td>

                </tr>

                <tr>

                	<form id="frmJobTitle" name="frmJobTitle" method="POST" action="<?php echo $_SERVER['PHP_SELF']?>?id=<?php echo $this->getArr['id']?>&uniqcode=<?php echo $this->getArr['uniqcode']?>&capturemode=updatemode">

						<input type="hidden" name="sqlState">

						<br>

						<img title="Back" onMouseOut="this.src='../../themes/beyondT/pictures/btn_back.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_back_02.jpg';"  src="../../themes/beyondT/pictures/btn_back.jpg" onClick="goBack();">

						<br>

                  <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>

                  <td><table width="100%" border="0" cellpadding="5" cellspacing="0" class="">

				                  <tr>

				                  		<td><?php echo $lang_jobtitle_jobtitid; ?></td>

				                  		<td><strong><?php echo $editArr[0][0]?></strong></td>

				                  </tr>

				                  <tr><input type="hidden" name="txtJobTitleID" id="txtJobTitleID" value="<?php echo $editArr[0][0]?>">

				                  		<td><span class="error">*</span> <?php echo $lang_jobtitle_jobtitname;?></td>

				                  		<td><input type="text" disabled name="txtJobTitleName" id="txtJobTitleName" value="<?php echo isset($cookie['txtJobTitleName']) ? $cookie['txtJobTitleName'] : $editArr[0][1]?>"></td>

				                  </tr>

				                  <tr>

				                  		<td><span class="error">*</span> <?php echo $lang_jobtitle_jobtitdesc;?></td>

				                  		<td><textarea disabled name="txtJobTitleDesc" id="txtJobTitleDesc"><?php echo isset($cookie['txtJobTitleDesc']) ? $cookie['txtJobTitleDesc'] : $editArr[0][2]?></textarea></td>

				                  </tr>

				                  <tr>

				                  		<td><?php echo $lang_jobtitle_jobtitcomments; ?></td>

				                  		<td><textarea disabled name="txtJobTitleComments" id="txtJobTitleComments"><?php echo isset($cookie['txtJobTitleComments']) ? $cookie['txtJobTitleComments'] : $editArr[0][3]?></textarea></td>

				                  </tr>

				                  <tr>

				                  		<td><span class="error">*</span><?php echo $lang_hrEmpMain_paygrade; ?></td>

				                  		<td><table border="0">

				                  			<tr><td width="100">

				                  		<select disabled name="cmbPayGrade" id="cmbPayGrade">

				               				<option value='0'>--<?php echo $lang_Leave_Common_Select; ?>--</option>

				               			<?php $paygrade = $this->popArr['paygrade'];

				               				for($c=0;$paygrade && count($paygrade)>$c;$c++)

				               					if ((isset($cookie['cmbPayGrade']) && ($cookie['cmbPayGrade'] == $paygrade[$c][0])) || ((!isset($cookie['cmbPayGrade'])) && ($paygrade[$c][0] == $editArr[0][4])))

					               					echo "<option selected value='" .$paygrade[$c][0]. "'>" .$paygrade[$c][1]. "</option>";

					               				else

					               					echo "<option value='" .$paygrade[$c][0]. "'>" .$paygrade[$c][1]. "</option>";

				               			?>

				                  		</select></td>

				                  		<td><input type="button" onClick="preserveData(); addSalaryGrade();" value="<?php echo $lang_jobtitle_addpaygrade; ?>" disabled="disabled"/>

				                  		<input type="button" onClick="preserveData(); editSalaryGrade();" value="<?php echo $lang_jobtitle_editpaygrade; ?>"  disabled="disabled" /></td>

				                  		</tr></table></td>

				                  </tr>

				                  <tr>

										<td valign="top">
										<span class="success">#</span> <?php echo $lang_jobtitle_empstat; ?><br>

										</td>

										<td><table border="0">

										<tr><td width="100">

										<select disabled size="3" name="cmbAssEmploymentStatus" style="width:125px;">

				               			<?php $assEmploymentStat = $this->popArr['assEmploymentStat'];

				               				for($c=0;$assEmploymentStat && count($assEmploymentStat)>$c;$c++)

					               				echo "<option value='" .$assEmploymentStat[$c][0]. "'>" .$assEmploymentStat[$c][1]. "</option>";

										?>

										</select></td>

										<td align="center" width="100"><input type="button" disabled name="butAssEmploymentStatus" onClick="assignEmploymentStatus();" value="< <?php echo $lang_compstruct_add; ?>" style="width:80%"><br><br><input type="button" disabled name="butUnAssEmploymentStatus" onClick="unAssignEmploymentStatus();" value="<?php echo $lang_Leave_Common_Remove; ?> >" style="width:80%"></td>

										<td><select disabled size="3" name="cmbUnAssEmploymentStatus" style="width:125px;">

				               			<?php $unAssEmploymentStat = $this->popArr['unAssEmploymentStat'];

				               				for($c=0;$unAssEmploymentStat && count($unAssEmploymentStat)>$c;$c++)

					               				echo "<option value='" .$unAssEmploymentStat[$c][0]. "'>" .$unAssEmploymentStat[$c][1]. "</option>";

										?>

										</select></td></tr>

										</table>

										</td>

								</tr>

								<tr>

									<td><!--<a href="../../lib/controllers/CentralController.php?uniqcode=EST&capturemode=addmode"><?php echo $lang_jobtitle_addempstat?></a><br>

				                  		<a href="javascript:editEmpStat();"><?php echo $lang_jobtitle_editempstat?></a>-->

									<input type="button" disabled value="<?php echo $lang_jobtitle_addempstat; ?>" onClick="xajax_showAddEmpStatForm();"><br><br>

									<input type="button" disabled value="<?php echo $lang_jobtitle_editempstat; ?>" onClick="showEditForm();">

									</td>

									<td>

						  <!-- form fits here -->

	<div id="layerEmpStat" name="layerEmpStat" style="visibility:hidden;">

	<table border='0' cellpadding='0' cellspacing='0'>

    <tr><td width='13'><img name='table_r1_c1' src='../../themes/beyondT/pictures/table_r1_c1.gif' width='13' height='12' border='0' alt=''></td>

    <td width='220' background='../../themes/beyondT/pictures/table_r1_c2.gif'><img name='table_r1_c2' src='../../themes/beyondT/pictures/spacer.gif' width='1' height='1' border='0' alt=''></td>

    <td width='13'><img name='table_r1_c3' src='../../themes/beyondT/pictures/table_r1_c3.gif' width='13' height='12' border='0' alt=''></td>

    <td width='11'><img src='../../themes/beyondT/pictures/spacer.gif' width='1' height='12' border='0' alt=''></td></tr>

    <tr><td background='../../themes/beyondT/pictures/table_r2_c1.gif'><img name='table_r2_c1' src='../../themes/beyondT/pictures/spacer.gif' width='1' height='1' border='0' alt=''></td>

    <td><table width='100%' border='0' cellpadding='5' cellspacing='0' class=''>

	<tr>

		<td><?php echo $lang_jobtitle_empstat; ?></td>

		<td><input type="hidden" name="txtEmpStatID"><input type="text" name="txtEmpStatDesc" disabled></td>

	</tr>

	<tr>

		<td></td>

		<td align="right"><div id='buttonLayer'></div></td>

	</tr>

    </table></td><td background='../../themes/beyondT/pictures/table_r2_c3.gif'><img name='table_r2_c3' src='../../themes/beyondT/pictures/spacer.gif' width='1' height='1' border='0' alt=''></td>

    <td><img src='../../themes/beyondT/pictures/spacer.gif' width='1' height='1' border='0' alt=''></td></tr>

    <tr><td><img name='table_r3_c1' src='../../themes/beyondT/pictures/table_r3_c1.gif' width='13' height='16' border='0' alt=''></td>

    <td background='../../themes/beyondT/pictures/table_r3_c2.gif'><img name='table_r3_c2' src='../../themes/beyondT/pictures/spacer.gif' width='1' height='1' border='0' alt=''></td>

    <td><img name='table_r3_c3' src='../../themes/beyondT/pictures/table_r3_c3.gif' width='13' height='16' border='0' alt=''></td>

    <td><img src='../../themes/beyondT/pictures/spacer.gif' width='1' height='16' border='0' alt=''></td></tr></table>

	</div>

						  <!-- form ends here -->

									</td>

								</tr>

					  <tr><td></td><td align="right">

<?php			if($locRights['edit']) { ?>

			        <img src="../../themes/beyondT/pictures/btn_edit.jpg" title="Edit" onMouseOut="mout();" onMouseOver="mover();" name="Edit" onClick="edit();">

<?php			} else { ?>

			        <img src="../../themes/beyondT/pictures/btn_edit.jpg" onClick="alert('<?php echo $lang_Common_AccessDenied;?>');">

<?php			}  ?>

					  <img src="../../themes/beyondT/pictures/btn_clear.jpg" onMouseOut="this.src='../../themes/beyondT/pictures/btn_clear.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_clear_02.jpg';" onClick="" >

</td>

					  </tr>

                  </table></td>

                  <td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>

                  <td><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>

                </tr>

                <tr>

                  <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>

                  <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>

                  <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>

                  <td><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="16" border="0" alt=""></td>

                </tr>

              </table>

</form>
<span id="notice">
<?php echo preg_replace('/#star/', '<span class="error">*</span>', $lang_Commn_RequiredFieldMark); ?>.</span>
<br>
<span id="notice">
<span class="success">#</span> = <?php echo $lang_jobtitle_emstatExpl; ?>
</span>
</body>

</html>

<?php } ?>
