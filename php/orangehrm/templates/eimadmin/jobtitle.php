<?
/*
// OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures 
// all the essential functionalities required for any enterprise. 
// Copyright (C) 2006 hSenid Software International Pvt. Ltd, http://www.hsenid.com

// OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
// the GNU General Public License as published by the Free Software Foundation; either
// version 2 of the License, or (at your option) any later version.

// OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
// without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
// See the GNU General Public License for more details.

// You should have received a copy of the GNU General Public License along with this program;
// if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
// Boston, MA  02110-1301, USA
*/

require_once ROOT_PATH . '/lib/controllers/ViewController.php';
require_once ROOT_PATH . '/lib/confs/sysConf.php';

function assignEmploymentStatus($valArr) {
	
	$view_controller = new ViewController();
	$ext_jobtitempstat = new EXTRACTOR_JobTitEmpStat();
	$filledObj = $ext_jobtitempstat->parseAddData($valArr);
	$view_controller->addData('JEM',$filledObj);

	$assList = $view_controller->xajaxObjCall($valArr['txtJobTitleID'],'JOB','assigned');
	$unAssList = $view_controller->xajaxObjCall($valArr['txtJobTitleID'],'JOB','unAssigned');
	
	$objResponse = new xajaxResponse();
	$xajaxFiller = new xajaxElementFiller();
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
	$objResponse = $xajaxFiller->cmbFiller($objResponse,$assList,0,'frmJobTitle','cmbAssEmploymentStatus',0);
	$objResponse = $xajaxFiller->cmbFiller($objResponse,$unAssList,0,'frmJobTitle','cmbUnAssEmploymentStatus',0);
	$objResponse->addAssign('status','innerHTML','');

	return $objResponse->getXML();
}

function showAddEmpStatForm() {
	    
    $objResponse = new xajaxResponse();
	$objResponse->addScript("document.frmJobTitle.txtEmpStatDesc.disabled = false;");
	$objResponse->addScript("document.frmJobTitle.txtEmpStatDesc.focus();");

	$objResponse->addAssign('buttonLayer','innerHTML',"<input type='button' value='Save' onClick='addFormData();'>");
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
	
	$objResponse->addAssign('buttonLayer','innerHTML',"<input type='button' value='Save' onClick='editFormData();'>");
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
	$objResponse = $xajaxFiller->cmbFiller($objResponse,$unAssEmpStat,0,'frmJobTitle','cmbUnAssEmploymentStatus',0);
	$objResponse->addScript("document.frmJobTitle.txtEmpStatDesc.value = '';");
	$objResponse->addScript("document.frmJobTitle.txtEmpStatDesc.disabled = true;");
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
	$objResponse = $xajaxFiller->cmbFiller($objResponse,$unAssEmpStat,0,'frmJobTitle','cmbUnAssEmploymentStatus',0);
	$objResponse->addScript("document.frmJobTitle.txtEmpStatID.value = '';");
	$objResponse->addScript("document.frmJobTitle.txtEmpStatDesc.value = '';");
	$objResponse->addScript("document.frmJobTitle.txtEmpStatDesc.disabled = true;");
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
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Untitled Document</title>
<? $objAjax->printJavascript(); ?>

<script language="JavaScript">

	function addSave() {
		
		if(document.frmJobTitle.txtJobTitleName.value == '') {
			alert ("Name Field Empty!");
			document.frmJobTitle.txtJobTitleName.focus();
			return;
			}
			
		if(document.frmJobTitle.txtJobTitleDesc.value == '') {
			alert ("Description Empty!");
			document.frmJobTitle.txtJobTitleDesc.focus();
			return;
			}
			
		if(document.frmJobTitle.cmbPayGrade.value == '0') {
			alert ("Field not Selected!");
			document.frmJobTitle.cmbPayGrade.focus();
			return;
			}
		
		document.frmJobTitle.sqlState.value = "NewRecord";
		document.frmJobTitle.submit();		
	}

function goBack() {

		location.href = "./CentralController.php?uniqcode=<?=$this->getArr['uniqcode']?>&VIEW=MAIN";
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
//  alert(frm.elements.length);
	for (var i=0; i < frm.elements.length; i++)
		frm.elements[i].disabled = false;
	document.Edit.src="../../themes/beyondT/pictures/btn_save.jpg";
	document.Edit.title="Save";
}

	function addUpdate() {
		if(document.frmJobTitle.txtJobTitleName.value == '') {
			alert ("Name Field Empty!");
			document.frmJobTitle.txtJobTitleName.focus();
			return;
			}
			
		if(document.frmJobTitle.txtJobTitleDesc.value == '') {
			alert ("Description Empty!");
			document.frmJobTitle.txtJobTitleDesc.focus();
			return;
			}
			
		if(document.frmJobTitle.cmbPayGrade.value == '0') {
			alert ("Field not Selected!");
			document.frmJobTitle.cmbPayGrade.focus();
			return;
			}
		
		document.frmJobTitle.sqlState.value = "UpdateRecord";
		document.frmJobTitle.submit();		
	}

function assignEmploymentStatus() {

	if(document.frmJobTitle.cmbUnAssEmploymentStatus.selectedIndex == -1) {
		alert('No Selection!');
		return;
	}

	document.getElementById('status').innerHTML = 'Please Wait....'; 

	xajax_assignEmploymentStatus(xajax.getFormValues('frmJobTitle'));
}

function unAssignEmploymentStatus() {
	
	if(document.frmJobTitle.cmbAssEmploymentStatus.selectedIndex == -1) {
		alert('No Selection!');
		return;
	}
	
	document.getElementById('status').innerHTML = 'Please Wait....'; 
	
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
		alert('Please Select!');
		document.frmJobTitle.cmbPayGrade.focus();
		return;
	}
	
	location.href = '../../lib/controllers/CentralController.php?uniqcode=SGR&id=' + paygrade + '&capturemode=updatemode';
}

function showEditForm() {
	
	empstat = document.frmJobTitle.cmbUnAssEmploymentStatus.value;
	
	if(document.frmJobTitle.cmbUnAssEmploymentStatus.selectedIndex == -1) {
		alert('Please Select!');
		document.frmJobTitle.cmbUnAssEmploymentStatus.focus();
		return;
	}
	
	xajax_showEditEmpStatForm(document.frmJobTitle.cmbUnAssEmploymentStatus.value);
}

function addFormData() {
	
	if(document.frmJobTitle.txtEmpStatDesc.value == '') {
		alert("Empty Field!");
		document.frmJobTitle.txtEmpStatDesc.focus();
		return;
	}

	document.getElementById('status').innerHTML = 'Please Wait....'; 
	xajax_addExt(xajax.getFormValues('frmJobTitle'));
}
	
function editFormData() {
	
	if(document.frmJobTitle.txtEmpStatDesc.value == '') {
		alert("Empty Field!");
		document.frmJobTitle.txtEmpStatDesc.focus();
		return;
	}

	document.getElementById('status').innerHTML = 'Please Wait....'; 
	xajax_editExt(xajax.getFormValues('frmJobTitle'));
}

</script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("../../themes/beyondT/css/style.css"); </style>
</head>
<body>

<? if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'addmode') { ?>
<table width='100%' cellpadding='0' cellspacing='0' border='0'>
  <tr>
    <td valign='top'></td>
    <td width='100%'><h2><?=$heading?></h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'><b><div id="status"></div></b></td>
  </tr>
</table>
<br>
<img title="Back" onmouseout="this.src='../../themes/beyondT/pictures/btn_back.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_back_02.jpg';"  src="../../themes/beyondT/pictures/btn_back.jpg" onclick="goBack();">
<br>
		<form id="frmJobTitle" name="frmJobTitle" method="POST" action="<?=$_SERVER['PHP_SELF']?>?uniqcode=<?=$this->getArr['uniqcode']?>">
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
				                  		<td><?=$jobtitid?></td>
				                  		<td><strong><?=$this->popArr['newID']?></strong></td>
				                  </tr>
				                  <tr>
				                  		<td><?=$jobtitname?></td>
				                  		<td><input type="text" name="txtJobTitleName"></td>
				                  </tr>
				                  <tr>
				                  		<td><?=$jobtitdesc?></td>
				                  		<td><textarea name="txtJobTitleDesc"></textarea></td>
				                  </tr>
				                  <tr>
				                  		<td><?=$jobtitcomments?></td>
				                  		<td><textarea name="txtJobTitleComments"></textarea></td>
				                  </tr>
				                  <tr>
				                  		<td><?=$pgrade?></td>
				                  		<td><select name="cmbPayGrade">
				               				<option value='0'>---Select---</option>
				               			<? $paygrade = $this->popArr['paygrade'];
				               				for($c=0;$paygrade && count($paygrade)>$c;$c++) 
				               					echo "<option value='" .$paygrade[$c][0]. "'>" .$paygrade[$c][1]. "</option>";
				               			?>	
				                  		</select></td>
				                  		<td><a href="../../lib/controllers/CentralController.php?uniqcode=SGR&capturemode=addmode"><?=$addpaygrade?></a><br>
				                  		<a href="javascript:editForeign(document.frmJobTitle.cmbPayGrade.value);"><?=$editpaygrade?></a></td>
				                  </tr>
					  <tr><td></td><td align="right"><img onClick="addSave();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
        				<img onClick="clearAll();" onmouseout="this.src='../../themes/beyondT/pictures/btn_clear.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_clear_02.jpg';" src="../../themes/beyondT/pictures/btn_clear.jpg"></td></tr>
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
</body>
</html>

<? } elseif (isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { 
	
	$editArr = $this->popArr['editArr']; 
?>
<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
  <tr>
    <td valign='top'></td>
    <td width='100%'><h2>Job Title</h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'><b><div id="status"></div></b></td>
  </tr>
</table>
           <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="339" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="11"><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="12" border="0" alt=""></td>
                </tr>
                <tr>
                	<form id="frmJobTitle" name="frmJobTitle" method="POST" action="<?=$_SERVER['PHP_SELF']?>?id=<?=$this->getArr['id']?>&uniqcode=<?=$this->getArr['uniqcode']?>&capturemode=updatemode">
						<input type="hidden" name="sqlState">
						<br>
						<img title="Back" onmouseout="this.src='../../themes/beyondT/pictures/btn_back.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_back_02.jpg';"  src="../../themes/beyondT/pictures/btn_back.jpg" onclick="goBack();">
						<br>
                  <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
				                  <tr>
				                  		<td><?=$jobtitid?></td>
				                  		<td><strong><?=$editArr[0][0]?></strong></td>
				                  </tr>
				                  <tr><input type="hidden" name="txtJobTitleID" value="<?=$editArr[0][0]?>">
				                  		<td><?=$jobtitname?></td>
				                  		<td><input type="text" disabled name="txtJobTitleName" value="<?=$editArr[0][1]?>"></td>
				                  </tr>
				                  <tr>
				                  		<td><?=$jobtitdesc?></td>
				                  		<td><textarea disabled name="txtJobTitleDesc"><?=$editArr[0][2]?></textarea></td>
				                  </tr>
				                  <tr>
				                  		<td><?=$jobtitcomments?></td>
				                  		<td><textarea disabled name="txtJobTitleComments"><?=$editArr[0][3]?></textarea></td>
				                  </tr>
				                  <tr>
				                  		<td><?=$pgrade?></td>
				                  		<td><table border="0">
				                  			<tr><td width="100">
				                  		<select disabled name="cmbPayGrade">
				               				<option value='0'>---Select---</option>
				               			<? $paygrade = $this->popArr['paygrade'];
				               				for($c=0;$paygrade && count($paygrade)>$c;$c++) 
				               					if($paygrade[$c][0] == $editArr[0][4])
					               					echo "<option selected value='" .$paygrade[$c][0]. "'>" .$paygrade[$c][1]. "</option>";
					               				else
					               					echo "<option value='" .$paygrade[$c][0]. "'>" .$paygrade[$c][1]. "</option>";
				               			?>	
				                  		</select></td>
				                  		<td><a href="../../lib/controllers/CentralController.php?uniqcode=SGR&capturemode=addmode"><?=$addpaygrade?></a><br>
				                  		<a href="javascript:editPayGrade(document.frmJobTitle.cmbPayGrade.value);"><?=$editpaygrade?></a></td>
				                  		</tr></table></td>
				                  </tr>
				                  <tr>
										<td><?=$emstat?></td>
										<td><table border="0">
										<tr><td width="100">
										<select disabled size="3" name="cmbAssEmploymentStatus">
				               			<? $assEmploymentStat = $this->popArr['assEmploymentStat'];
				               				for($c=0;$assEmploymentStat && count($assEmploymentStat)>$c;$c++) 
					               				echo "<option value='" .$assEmploymentStat[$c][0]. "'>" .$assEmploymentStat[$c][1]. "</option>";
										?>
										</select></td>
										<td align="center" width="100"><input type="button" disabled name="butAssEmploymentStatus" onclick="assignEmploymentStatus();" value="< Add"><br><br><input type="button" disabled name="butUnAssEmploymentStatus" onclick="unAssignEmploymentStatus();" value="Remove >"></td>
										<td><select disabled size="3" name="cmbUnAssEmploymentStatus">
				               			<? $unAssEmploymentStat = $this->popArr['unAssEmploymentStat'];
				               				for($c=0;$unAssEmploymentStat && count($unAssEmploymentStat)>$c;$c++) 
					               				echo "<option value='" .$unAssEmploymentStat[$c][0]. "'>" .$unAssEmploymentStat[$c][1]. "</option>";
										?>
										</select></td></tr>
										</table>
										</td>
								</tr>
								<tr>
									<td><!--<a href="../../lib/controllers/CentralController.php?uniqcode=EST&capturemode=addmode"><?=$addempstat?></a><br>
				                  		<a href="javascript:editEmpStat();"><?=$editempstat?></a>-->
									<input type="button" disabled value="<?=$addempstat?>" onclick="xajax_showAddEmpStatForm();"><br><br>
									<input type="button" disabled value="<?=$editempstat?>" onclick="showEditForm();">
									</td>
									<td>
						  <!-- form fits here -->
	<table border='0' cellpadding='0' cellspacing='0'>
    <tr><td width='13'><img name='table_r1_c1' src='../../themes/beyondT/pictures/table_r1_c1.gif' width='13' height='12' border='0' alt=''></td>
    <td width='220' background='../../themes/beyondT/pictures/table_r1_c2.gif'><img name='table_r1_c2' src='../../themes/beyondT/pictures/spacer.gif' width='1' height='1' border='0' alt=''></td>
    <td width='13'><img name='table_r1_c3' src='../../themes/beyondT/pictures/table_r1_c3.gif' width='13' height='12' border='0' alt=''></td>
    <td width='11'><img src='../../themes/beyondT/pictures/spacer.gif' width='1' height='12' border='0' alt=''></td></tr>
    <tr><td background='../../themes/beyondT/pictures/table_r2_c1.gif'><img name='table_r2_c1' src='../../themes/beyondT/pictures/spacer.gif' width='1' height='1' border='0' alt=''></td>
    <td><table width='100%' border='0' cellpadding='5' cellspacing='0' class=''>
	<tr>
		<td><?=$emstat?></td>
		<td><input type="hidden" name="txtEmpStatID"><input type="text" name="txtEmpStatDesc"></td>
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
						  <!-- form ends here -->
									</td>
								</tr>
					  <tr><td></td><td align="right" width="100%">
<?			if($locRights['edit']) { ?>
			        <img src="../../themes/beyondT/pictures/btn_edit.jpg" title="Edit" onmouseout="mout();" onmouseover="mover();" name="Edit" onClick="edit();">
<?			} else { ?>
			        <img src="../../themes/beyondT/pictures/btn_edit.jpg" onClick="alert('<?=$sysConst->accessDenied?>');">
<?			}  ?>
					  <img src="../../themes/beyondT/pictures/btn_clear.jpg" onmouseout="this.src='../../themes/beyondT/pictures/btn_clear.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_clear_02.jpg';" onClick="clearAll();" >
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
</body>
</html>
<? } ?>