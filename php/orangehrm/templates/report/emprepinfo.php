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
require_once ROOT_PATH . '/lib/exception/ExceptionHandler.php';
require_once ROOT_PATH . '/lib/confs/sysConf.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpInfo.php';

	$sysConst = new sysConf();
	$locRights=$_SESSION['localRights'];

	$arrAgeSim = $this-> popArr['arrAgeSim'];
	$arrEmpType= $this-> popArr['arrEmpType'];

	$empInfoObj = new EmpInfo();

$headingInfo = array ("$lang_emprepinfo_heading : $lang_Common_New", "$lang_emprepinfo_heading : $lang_Common_Edit");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title></title>

<script language="JavaScript">
function alpha(txt)
{
var flag=true;
var i,code;

if(txt.value=="")
   return false;

for(i=0;txt.value.length>i;i++)
	{
	code=txt.value.charCodeAt(i);
    if((code>=65 && code<=122) || code==32 || code==46)
	   flag=true;
	else
	   {
	   flag=false;
	   break;
	   }
	}
return flag;
}

function numeric(txt)
{
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


function goBack() {
	location.href = "./CentralController.php?repcode=<?php echo $this->getArr['repcode']?>&VIEW=MAIN";
	}

function returnEmpDetail(){
		var popup=window.open('../../templates/hrfunct/emppop.php?reqcode=REP&REPORT=REPORT','Employees','height=450,width=400');
        if(!popup.opener) popup.opener=self;
}


function addCat() {
document.frmEmpRepTo.sqlState.value="OWN";
document.frmEmpRepTo.submit();
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
function edit()
{
	if(document.Edit.title=='Save') {
		addUpdate();
		return;
	}

	var frm=document.frmEmpRepTo;

	frm.txtRepName.disabled = false;

	for (var i=0; i < frm.elements.length; i++)
		if(frm.elements[i].type == 'checkbox') {
			frm.elements[i].disabled = false ;
		}

	chkboxCriteriaEnable();
	document.Edit.src="../../themes/beyondT/pictures/btn_save.jpg";
	document.Edit.title="Save";
}

function addUpdate() {


	if(!chkboxCheck()) {
		alert('<?php echo $lang_rep_SelectAtLeastOneCriteriaAndOneField; ?>');
		return;
	}

	if(!validation())
		return;

	parent.scroll(0,0);
	document.frmEmpRepTo.sqlState.value = "UpdateRecord";
	document.frmEmpRepTo.submit();
}


function chkboxCriteriaEnable() {

		with (document.frmEmpRepTo) {
			for (var i=0; i < elements.length; i++) {
				if (elements[i].name == 'chkcriteria[]') {

					 switch(elements[i].id) {

					 	case 'EMPNO'      : document.frmEmpRepTo.empPop.disabled = !elements[i].checked;
					 						document.frmEmpRepTo.cmbId.disabled = !elements[i].checked;
				 							if(!elements[i].checked){
						 						document.frmEmpRepTo.txtRepEmpID.value='';
						 						document.frmEmpRepTo.cmbRepEmpID.value='';
						 						document.frmEmpRepTo.cmbId.options[0].selected = true;
				 							}
				 							disableEmployeeId();
				 							break;
					 	case 'AgeGroup'   :
											document.frmEmpRepTo.cmbAgeCode.disabled= !elements[i].checked;

											disableAgeField();

											if(!elements[i].checked){
					 							document.frmEmpRepTo.cmbAgeCode.options[0].selected = true;
					 							document.frmEmpRepTo.txtEmpAge1.value='';
					 							document.frmEmpRepTo.txtEmpAge2.value='';

					 						} break;
					 	case 'PayGrade'   : document.frmEmpRepTo.cmbSalGrd.disabled= !elements[i].checked;
					 						if(!elements[i].checked){
					 						document.frmEmpRepTo.cmbSalGrd.options[0].selected=true;
					 						} break;
					 	case 'QualType'   : document.frmEmpRepTo.TypeCode.disabled = !elements[i].checked;
				 							if(!elements[i].checked){
					 						document.frmEmpRepTo.TypeCode.options[0].selected=true;

				 							} break;
					 	case 'EmpType'    : document.frmEmpRepTo.cmbEmpType.disabled= !elements[i].checked;
					 						if(!elements[i].checked){
					 						document.frmEmpRepTo.cmbEmpType.options[0].selected=true;
					 						} break;
					 	case 'SerPeriod'  : document.frmEmpRepTo.cmbSerPerCode.disabled= !elements[i].checked;
											disableSerPeriodField()

					 						if(!elements[i].checked){
					 						document.frmEmpRepTo.cmbSerPerCode.options[0].selected = true;
					 						document.frmEmpRepTo.Service1.value='';
					 						document.frmEmpRepTo.Service2.value='';

											document.frmEmpRepTo.Service1.disabled = false;
					 						document.frmEmpRepTo.Service2.disabled = false;
					 						} break;
					 	case 'JobTitle': document.frmEmpRepTo.cmbDesig.disabled= !elements[i].checked;
					 						if(!elements[i].checked){
					 						document.frmEmpRepTo.cmbDesig.options[0].selected = true;
					 						} break;
					 	case 'Language': document.frmEmpRepTo.cmbLanguage.disabled= !elements[i].checked;
					 					 if(!elements[i].checked){
					 						document.frmEmpRepTo.cmbLanguage.options[0].selected = true;
					 					 }
					 					 break;
					 	case 'Skill'   : document.frmEmpRepTo.cmbSkill.disabled= !elements[i].checked;
					 					 if(!elements[i].checked){
					 						document.frmEmpRepTo.cmbSkill.options[0].selected = true;
					 					 }
					 					 break;
					 }
				}
			}
        }
}

function chkboxCheck() {
        var check = 0;
		with (document.frmEmpRepTo) {
			for (var i=0; i < elements.length; i++) {
				if ((elements[i].name == 'chkcriteria[]') && (elements[i].checked == true)){
					check = 1;
				}
			}
        }

        if(check==0)
			return false;


      var check = 0;
		with (document.frmEmpRepTo) {
			for (var i=0; i < elements.length; i++) {
				if ((elements[i].name == 'checkfield[]') && (elements[i].checked == true)){
					check = 1;
				}
			}
        }

        if(check==0)
        	return false;

	return true;
}


function disableAgeField() {
	if(document.frmEmpRepTo.cmbAgeCode.value == "0") {
		document.frmEmpRepTo.txtEmpAge1.disabled = true;
		document.frmEmpRepTo.txtEmpAge2.disabled = true;
		document.frmEmpRepTo.txtEmpAge1.style.visibility = "hidden";
		document.frmEmpRepTo.txtEmpAge2.style.visibility = "hidden";
		return;
	} else if(document.frmEmpRepTo.cmbAgeCode.value=="range") {
		document.frmEmpRepTo.txtEmpAge1.disabled = false;
		document.frmEmpRepTo.txtEmpAge2.disabled = false;
		document.frmEmpRepTo.txtEmpAge1.style.visibility = "visible";
		document.frmEmpRepTo.txtEmpAge2.style.visibility = "visible";
		return;
	} else if(document.frmEmpRepTo.cmbAgeCode.value=='<' || document.frmEmpRepTo.cmbAgeCode.value=='>') {
		document.frmEmpRepTo.txtEmpAge1.disabled = false;
		document.frmEmpRepTo.txtEmpAge2.disabled = true;
		document.frmEmpRepTo.txtEmpAge1.style.visibility = "visible";
		document.frmEmpRepTo.txtEmpAge2.style.visibility = "hidden";
		document.frmEmpRepTo.txtEmpAge2.value='';
		return;
	}
}

function disableEmployeeId() {
	if(document.frmEmpRepTo.cmbId.value == "0") {
		document.frmEmpRepTo.empPop.style.visibility = "hidden";
		document.frmEmpRepTo.cmbRepEmpID.style.visibility = "hidden";
		document.frmEmpRepTo.txtRepEmpID.value='';
		document.frmEmpRepTo.cmbRepEmpID.value='';
	} else if(document.frmEmpRepTo.cmbId.value == "1") {
		document.frmEmpRepTo.empPop.style.visibility = "visible";
		document.frmEmpRepTo.cmbRepEmpID.style.visibility = "visible";
	}
}

function disableSerPeriodField() {
	if(document.frmEmpRepTo.cmbSerPerCode.value=="0") {
		document.frmEmpRepTo.Service1.disabled = true;
		document.frmEmpRepTo.Service2.disabled = true;
		document.frmEmpRepTo.Service1.style.visibility = "hidden";
		document.frmEmpRepTo.Service2.style.visibility = "hidden";
		return;
	} else if(document.frmEmpRepTo.cmbSerPerCode.value=="range") {
		document.frmEmpRepTo.Service1.disabled = false;
		document.frmEmpRepTo.Service2.disabled = false;
		document.frmEmpRepTo.Service1.style.visibility = "visible";
		document.frmEmpRepTo.Service2.style.visibility = "visible";
		return;
	} else if(document.frmEmpRepTo.cmbSerPerCode.value=='<' || document.frmEmpRepTo.cmbSerPerCode.value=='>') {
		document.frmEmpRepTo.Service1.disabled = false;
		document.frmEmpRepTo.Service2.disabled = true;
		document.frmEmpRepTo.Service1.style.visibility = "visible";
		document.frmEmpRepTo.Service2.style.visibility = "hidden";
		document.frmEmpRepTo.Service2.value='';
		return;
	}
}



 function addEXT() {

 	if(!chkboxCheck()) {
		alert('<?php echo $lang_rep_SelectAtLeastOneCriteriaAndOneField; ?>');
		return;
	}

	if(!validation())
		return;

	parent.scroll(0,0);
	document.frmEmpRepTo.sqlState.value="NewRecord";
	document.frmEmpRepTo.submit();
}

 function validation() {

 		if(document.frmEmpRepTo.txtRepName.value=='') {
 			alert('<?php echo $lang_rep_ReportNameEmpty; ?>');
			document.frmEmpRepTo.txtRepName.focus();
			return false;
 		}

		with (document.frmEmpRepTo) {
			for (var i=0; i < elements.length; i++) {
				if (elements[i].name == 'chkcriteria[]') {

					 switch(elements[i].id) {
					 	case 'EMPNO'      :
					 						if((document.frmEmpRepTo.cmbId.value == 1) && (elements[i].checked && document.frmEmpRepTo.txtRepEmpID.value=='')) {
												alert("<?php echo $lang_Error_FieldShouldBeSelected; ?>");
												document.frmEmpRepTo.txtRepEmpID.focus();
												return false;
											}
											break;
						case 'AgeGroup'   :

											if(elements[i].checked && document.frmEmpRepTo.cmbAgeCode.value=="0") {
												alert("<?php echo $lang_rep_SelectTheComparison; ?>");
												document.frmEmpRepTo.cmbAgeCode.focus();
												return false;
											} else if(elements[i].checked && document.frmEmpRepTo.cmbAgeCode.value=='range') {

												if(!numeric(document.frmEmpRepTo.txtEmpAge1)) {
													alert("<?php echo $lang_rep_AgeShouldBeNumeric; ?>");
													document.frmEmpRepTo.txtEmpAge1.focus();
													return false;
												}

												if(!numeric(document.frmEmpRepTo.txtEmpAge2)) {
													alert("<?php echo $lang_rep_AgeShouldBeNumeric; ?>");
													document.frmEmpRepTo.txtEmpAge2.focus();
													return false;
												}

												if(eval(document.frmEmpRepTo.txtEmpAge1.value) > eval(document.frmEmpRepTo.txtEmpAge2.value)) {
													alert("<?php echo $lang_rep_InvalidAgeRange; ?>");
													document.frmEmpRepTo.txtEmpAge2.focus();
													return flase;
												}
											} else if(elements[i].checked && document.frmEmpRepTo.cmbAgeCode.value=='<' || document.frmEmpRepTo.cmbAgeCode.value=='>') {

												if(!numeric(document.frmEmpRepTo.txtEmpAge1)) {
													alert("<?php echo $lang_rep_AgeShouldBeNumeric; ?>");
													document.frmEmpRepTo.txtEmpAge1.focus();
													return false;
												}
											}
											break;

						case 'PayGrade'   :
					 						if(elements[i].checked && document.frmEmpRepTo.cmbSalGrd.value=="0") {
												alert("<?php echo $lang_rep_FieldNotSelected; ?>");
												document.frmEmpRepTo.cmbSalGrd.focus();
												return false;
											}
											break;

						case 'QualType'   :
											if(elements[i].checked && document.frmEmpRepTo.TypeCode.value=="0") {
												alert("<?php echo $lang_Common_FieldEmpty; ?>");
												document.frmEmpRepTo.TypeCode.focus();
												return false;
											}

											break;

						case 'Emptype'    :
											if(elements[i].checked && document.frmEmpRepTo.cmbEmpType.value=="0") {
												alert("<?php echo $lang_Common_FieldEmpty; ?>");
												document.frmEmpRepTo.cmbEmpType.focus();
												return false;
											}
											break;

						case 'SerPeriod'  :
											if(elements[i].checked && document.frmEmpRepTo.cmbSerPerCode.value=="0") {
												alert("<?php echo $lang_rep_SelectTheComparison; ?>");
												document.frmEmpRepTo.cmbSerPerCode.focus();
												return false;
											} else if(elements[i].checked && document.frmEmpRepTo.cmbSerPerCode.value=='range') {

												if(!numeric(document.frmEmpRepTo.Service1)) {
													alert("<?php echo $lang_rep_DateShouldBeNumeric; ?>");
													document.frmEmpRepTo.Service1.focus();
													return false;
												}

												if(!numeric(document.frmEmpRepTo.Service2)) {
													alert("<?php echo $lang_rep_DateShouldBeNumeric; ?>");
													document.frmEmpRepTo.Service2.focus();
													return false;
												}

												if(eval(document.frmEmpRepTo.Service1.value) > eval(document.frmEmpRepTo.Service2.value)) {
													alert("<?php echo $lang_rep_InvalidAgeRange; ?>");
													document.frmEmpRepTo.Service2.focus();
													return false;
												}


											} else if(elements[i].checked && document.frmEmpRepTo.cmbSerPerCode.value=='<' || document.frmEmpRepTo.cmbSerPerCode.value=='>') {

												if(!numeric(document.frmEmpRepTo.Service1)) {
													alert("<?php echo $lang_rep_AgeShouldBeNumeric; ?>");
													document.frmEmpRepTo.Service1.focus();
													return false;
												}
											}
											break;

						case 'JobTitle':	if(elements[i].checked && document.frmEmpRepTo.cmbDesig.value=='0') {
												alert("<?php echo $lang_Common_FieldEmpty; ?>");
												document.frmEmpRepTo.cmbDesig.focus();
												return false;
											}
											break;

						case 'Language':	if(elements[i].checked && document.frmEmpRepTo.cmbLanguage.value=='0') {
												alert("<?php echo $lang_Common_FieldEmpty; ?>");
												document.frmEmpRepTo.cmbLanguage.focus();
												return false;
											}
											break;
						case 'Skill'   :	if(elements[i].checked && document.frmEmpRepTo.cmbSkill.value=='0') {
												alert("<?php echo $lang_Common_FieldEmpty; ?>");
												document.frmEmpRepTo.cmbSkill.focus();
												return false;
											}
											break;
					 }
				}
			}
		}

 return true;
 }

</script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<link href="../../themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("../../themes/beyondT/css/style1.css"); </style>
</head>
<?php if ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'addmode')) { ?>
<body>
<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
  <tr>
    <td valign='top'>&nbsp; </td>
    <td width='100%'><h2><?php echo $headingInfo[0]?></h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>
</table>
<table border="0" >
  <tr>
  <td valign="middle" height="35"><img title="Back" onMouseOut="this.src='../../themes/beyondT/pictures/btn_back.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_back_02.jpg';"  src="../../themes/beyondT/pictures/btn_back.jpg" onClick="goBack();"></td>
  </tr>
</table>

<p>
<p>
<table border="0">
<form name="frmEmpRepTo" method="post" action="<?php echo $_SERVER['PHP_SELF']?>?repcode=<?php echo $this->getArr['repcode']?>&capturemode=addmode">
<input type="hidden" name="sqlState">


<tr><td>
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
 					  <td><?php echo $lang_repview_ReportName; ?></td>
						<td ><input type="text"  name="txtRepName" value="<?php echo (isset($this->postArr['txtRepName'])  ? $this->postArr['txtRepName'] : '') ?>"  ></td>
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
</td> </tr>
<tr>
    <td valign="bottom" height="35"><h4><?php echo $lang_rep_SelectionCriteria; ?></h4></td>
  </tr>
  <tr><td>
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
                       <td><input type='checkbox' class='checkbox'  name='chkcriteria[]' id='EMPNO' value='EMPNO' onClick="chkboxCriteriaEnable()" <?php echo  (isset($this->postArr['chkcriteria']) && in_array('EMPNO', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> ></td>
                      <td valign="top"><?php echo $lang_rep_Employee; ?></td>
                      <td align="left">
	                      <select name="cmbId" onchange="disableEmployeeId();" disabled>
						  	<option value="0"><?php echo $lang_Leave_Common_AllEmployees; ?></option>
							<option value="1"><?php echo $lang_Leave_Common_Select; ?> --></option>
						  </select>
					  </td>
                      <td align="left" valign="top"><input type="text" style="visibility:hidden;" readonly name="cmbRepEmpID" value="<?php echo isset($this->postArr['txtRepEmpID']) ? $this->postArr['txtRepEmpID'] : ''?>" ><input type="hidden"  readonly name="txtRepEmpID" value="<?php echo isset($this->postArr['txtRepEmpID']) ? $this->postArr['txtRepEmpID'] : ''?>" ></td>
                      <td align="left"><input class="button" type="button" style="visibility:hidden;" name="empPop" value=".." onClick="returnEmpDetail();" <?php echo  (isset($this->postArr['chkcriteria']) && in_array('EMPNO', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?>></td>
  					</tr>
					<tr>
					 <td><input type='checkbox' class='checkbox' name='chkcriteria[]' id='AgeGroup' value="AGE" onClick="chkboxCriteriaEnable()" <?php echo  (isset($this->postArr['chkcriteria']) && in_array('AGE', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> ></td>
  					 <td valign="top"><?php echo $lang_rep_AgeGroup; ?></td>
					 <td align="left" valign="top"> <select   name="cmbAgeCode" onChange="disableAgeField();" <?php echo  (isset($this->postArr['chkcriteria']) && in_array('AGE', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?> class="cmb" >
 					  <option value="0">--<?php echo $lang_rep_SelectComparison; ?>--</option>
<?php
							$keys   = array_keys($arrAgeSim);
							$values = array_values($arrAgeSim);

							for($c=0;count($arrAgeSim)>$c;$c++)
								if(isset($this->postArr['cmbAgeCode']) && $this->postArr['cmbAgeCode']==$values[$c])
									echo "<option selected value='" .$values[$c]. "'>" .$keys[$c]. "</option>";
								else
									echo "<option value='" .$values[$c]. "'>" .$keys[$c]. "</option>";
?>
					  </select>
				    </td>
					<td><input type="text" <?php echo isset($this->postArr['txtEmpAge1']) ? $this->postArr['txtEmpAge1'] : 'style="visibility:hidden;" disabled'?>  name='txtEmpAge1' value="<?php echo isset($this->postArr['txtEmpAge1']) ? $this->postArr['txtEmpAge1'] : ''?>" /></td>
					<td><input type="text" <?php echo isset($this->postArr['txtEmpAge2']) ? $this->postArr['txtEmpAge2'] : 'style="visibility:hidden;"disabled'?> name='txtEmpAge2' value="<?php echo isset($this->postArr['txtEmpAge2']) ? $this->postArr['txtEmpAge2'] : ''?>" /></td>

					</tr>




				<tr>
				  <td><input type='checkbox' class='checkbox' name='chkcriteria[]' id='PayGrade' value="PAYGRD" onClick="chkboxCriteriaEnable()" <?php echo  (isset($this->postArr['chkcriteria']) && in_array('PAYGRD', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> ></td>
				  <td><?php echo $lang_rep_PayGrade; ?></td>
			      <td><select  name="cmbSalGrd" <?php echo  (isset($this->postArr['chkcriteria']) && in_array('PAYGRD', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?> class="cmb" >
			  		<option value="0">--<?php echo $lang_rep_SelectPayGrade; ?>--</option>
<?php					$grdlist = $this->popArr['grdlist'];
					for($c=0;$grdlist && count($grdlist)>$c;$c++)
						if(isset($this->postArr['cmbSalGrd']) && $this->postArr['cmbSalGrd']==$grdlist[$c][0])
							echo "<option selected value='" .$grdlist[$c][0]. "'>" .$grdlist[$c][1]. "</option>";
						else
							echo "<option value='" .$grdlist[$c][0]. "'>" .$grdlist[$c][1]. "</option>";
?>
			  </select></td>
					</tr>


   					<tr>
					<td><input type='checkbox' class='checkbox' name='chkcriteria[]' id='QualType' value="QUL" onClick="chkboxCriteriaEnable()" <?php echo  (isset($this->postArr['chkcriteria']) && in_array('QUL', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> ></td>
				    <td><?php echo $lang_rep_Education; ?></td>
    				  <td>
					  <select name="TypeCode"  <?php echo  (isset($this->postArr['chkcriteria']) && in_array('QUL', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?> class="cmb" >
					  <option value=0>--<?php echo $lang_rep_SelectEducation; ?>--</option>
<?php
						$edulist=$this -> popArr['edulist'];
						for($c=0;$edulist && count($edulist)>$c;$c++)
							if(isset($this->postArr['TypeCode']) && $this->postArr['TypeCode']==$edulist[$c][0])
							   echo "<option selected value=" . $edulist[$c][0] . ">" . $edulist[$c][2].', '.$edulist[$c][1] . "</option>";
							else
							   echo "<option value=" . $edulist[$c][0] . ">" .$edulist[$c][2].', '. $edulist[$c][1] . "</option>";
?>
					  </select></td>
						<td valign="middle"></td>
						<td align="left" valign="middle"></td>
					</tr>


<tr>
					  <td><input type='checkbox' class='checkbox' name='chkcriteria[]' id='EmpType' value="EMPSTATUS" onClick="chkboxCriteriaEnable()" <?php echo  (isset($this->postArr['chkcriteria']) && in_array('EMPSTATUS', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> ></td>
					  <td valign="top"><?php echo $lang_rep_EmploymentStatus; ?></td>
					  	<td><select name="cmbEmpType" <?php echo  (isset($this->postArr['chkcriteria']) && in_array('EMPSTATUS', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?> class="cmb" >
			  		    <option value="0">--<?php echo $lang_rep_SelectEmploymentType; ?>--</option>
<?php
					//if(isset($this->postArr['cmbEmpType'])) {
						$arrEmpType=$this-> popArr['arrEmpType'];
						for($c=0;$arrEmpType && count($arrEmpType)>$c;$c++)
						    echo "<option value=" . $arrEmpType[$c][0] . ">" . $arrEmpType[$c][1] . "</option>";
						//}
?>
			         		</select>
						</td>
					</tr>



					<tr>
					   <td><input type='checkbox' class='checkbox' name='chkcriteria[]' id='SerPeriod' value="SERPIR" onClick="chkboxCriteriaEnable()" <?php echo  (isset($this->postArr['chkcriteria']) && in_array('SERPIR', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> ></td>
					  <td valign="top"><?php echo $lang_rep_ServicePeriod; ?></td>
					    <td align="left" valign="middle"> <select  name="cmbSerPerCode" onChange="disableSerPeriodField()" <?php echo  (isset($this->postArr['chkcriteria']) && in_array('SERPIR', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?> class="cmb" >
					     <option value="0">--<?php echo $lang_rep_SelectComparison; ?>--</option>
<?php
							$keys   = array_keys($arrAgeSim);
							$values = array_values($arrAgeSim);

							for($c=0;count($arrAgeSim)>$c;$c++)
								if(isset($this->postArr['cmbSerPerCode']) && $this->postArr['cmbSerPerCode']==$values[$c])
									echo "<option selected value='" .$values[$c]. "'>" .$keys[$c]. "</option>";
								else
									echo "<option value='" .$values[$c]. "'>" .$keys[$c]. "</option>";

							?>
					     </select>
					  	</td>
				        <td><input type="text" <?php echo isset($this->postArr['Service1']) ? $this->postArr['Service1'] : 'style="visibility:hidden;" disabled'?> name="Service1" value="<?php echo isset($this->postArr['Service1']) ? $this->postArr['Service1'] : ''?>" ></td>
                        <td><input type="text" <?php echo isset($this->postArr['Service2']) ? $this->postArr['Service2'] : 'style="visibility:hidden;" disabled'?> name="Service2" value="<?php echo isset($this->postArr['Service2']) ? $this->postArr['Service2'] : ''?>" ></td>
					</tr>


   					<tr>
					 <td><input type='checkbox' class='checkbox' name='chkcriteria[]' id='JobTitle' value="JOBTITLE" onClick="chkboxCriteriaEnable()" <?php echo  (isset($this->postArr['chkcriteria']) && in_array('JOBTITLE', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> ></td>
					 <td><?php echo $lang_rep_JobTitle; ?></td>
					  <td><select  name="cmbDesig" <?php echo (isset($this->postArr['chkcriteria']) && in_array('JOBTITLE', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?> class="cmb" >
					  		<option value="0">--<?php echo $lang_rep_SelectJobTitle; ?>--</option>
<?php
							$deslist = $this->popArr['deslist'];
							for($c=0;$deslist && count($deslist)>$c;$c++)
								if(isset($this->postArr['cmbDesig']) && $this->postArr['cmbDesig']==$deslist[$c][0])
									echo "<option selected value='" .$deslist[$c][0]. "'>" .$deslist[$c][1]. "</option>";
								else
									echo "<option value='" .$deslist[$c][0]. "'>" .$deslist[$c][1]. "</option>";
?>
					  </select></td>
					</tr>
					<tr>
					 <td><input type='checkbox' class='checkbox' name='chkcriteria[]' id='Language' value="LANGUAGE" onClick="chkboxCriteriaEnable()" <?php echo  (isset($this->postArr['chkcriteria']) && in_array('LANGUAGE', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> ></td>
					 <td><?php echo $lang_rep_Language; ?></td>
					  <td><select  name="cmbLanguage" <?php echo (isset($this->postArr['chkcriteria']) && in_array('LANGUAGE', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?> class="cmb" >
					  		<option value="0">--<?php echo $lang_rep_SelectLanguage; ?>--</option>
							<?php
							$deslist = $this->popArr['languageList'];
							for($c=0;$deslist && count($deslist)>$c;$c++)
								if(isset($this->postArr['cmbLanguage']) && $this->postArr['cmbLanguage']==$deslist[$c][0])
									echo "<option selected value='" .$deslist[$c][0]. "'>" .$deslist[$c][1]. "</option>";
								else
									echo "<option value='" .$deslist[$c][0]. "'>" .$deslist[$c][1]. "</option>";
							?>
					  </select></td>
					</tr>

					<tr>
					 <td><input type='checkbox' class='checkbox' name='chkcriteria[]' id='Skill' value="SKILL" onClick="chkboxCriteriaEnable()" <?php echo  (isset($this->postArr['chkcriteria']) && in_array('SKILL', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> ></td>
					 <td><?php echo $lang_rep_Skill; ?></td>
					  <td><select  name="cmbSkill" <?php echo (isset($this->postArr['chkcriteria']) && in_array('SKILL', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?> class="cmb" >
					  		<option value="0">--<?php echo $lang_rep_SelectSkill; ?>--</option>
							<?php
							$deslist = $this->popArr['skillList'];
							for($c=0;$deslist && count($deslist)>$c;$c++)
								if(isset($this->postArr['cmbSkill']) && $this->postArr['cmbSkill']==$deslist[$c][0])
									echo "<option selected value='" .$deslist[$c][0]. "'>" .$deslist[$c][1]. "</option>";
								else
									echo "<option value='" .$deslist[$c][0]. "'>" .$deslist[$c][1]. "</option>";
							?>
					  </select></td>
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
                <tr><td>&nbsp;</td></tr>
              </table>
</td> </tr>
<tr>
	<td> <img border="0" title="Save" onClick="addEXT();" onMouseOut="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg"></td>
</tr>
                <tr><td>&nbsp;</td></tr>
  <tr>
    <td height="15"><h4><?php echo $lang_rep_Field; ?></h4></td>
  </tr>
<tr><td>
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
                      	 <td><input type='checkbox' checked class='checkbox' name='checkfield[]' value='EMPNO'></td>
						 <td><?php echo $lang_rep_EmpNo; ?></td>
					</tr>

					<tr>
                      	 <td><input type='checkbox' checked  class='checkbox' name='checkfield[]' value='EMPFIRSTNAME'></td>
						 <td><?php echo $lang_rep_FirstName; ?></td>
					</tr>

					<tr>
                      	 <td><input type='checkbox' checked class='checkbox' name='checkfield[]' value='EMPLASTNAME'></td>
						 <td><?php echo $lang_rep_LastName; ?></td>
					</tr>

					<tr>
                      	 <td><input type='checkbox' checked class='checkbox' name='checkfield[]' value='ADDRESS1'></td>
						 <td><?php echo $lang_rep_Address; ?></td>
					</tr>

					<tr>
                      	 <td><input type='checkbox' checked class='checkbox' name='checkfield[]' value='TELENO'></td>
						 <td><?php echo $lang_rep_TelNo; ?></td>
					</tr>

					<tr>
                      	 <td><input type='checkbox' checked class='checkbox' name='checkfield[]' value='AGE'></td>
						 <td><?php echo $lang_rep_DateOfBirth; ?></td>
					</tr>

					<tr>
                      	 <td><input type='checkbox' checked class='checkbox' name='checkfield[]' value='REPORTTO'></td>
						 <td><?php echo $lang_rep_ReportTo; ?></td>
					</tr>

					<tr>
                      	 <td><input type='checkbox' checked class='checkbox' name='checkfield[]' value='REPORTINGMETHOD'></td>
						 <td><?php echo $lang_rep_ReportingMethod; ?></td>
					</tr>

					<tr>
                      	 <td><input type='checkbox' checked class='checkbox' name='checkfield[]' value='JOBTITLE'></td>
						 <td><?php echo $lang_rep_JobTitle; ?></td>
					</tr>

					<tr>
                      	 <td><input type='checkbox' checked class='checkbox' name='checkfield[]' value='SERPIR'></td>
						 <td><?php echo $lang_rep_JoinedDate; ?></td>
					</tr>

					<tr>
                      	 <td><input type='checkbox' checked class='checkbox' name='checkfield[]' value='SUBDIVISION'></td>
						 <td><?php echo $lang_rep_SubDivision; ?></td>
					</tr>

					<tr>
                      	 <td><input type='checkbox' checked class='checkbox' name='checkfield[]' value='QUL'></td>
						 <td><?php echo $lang_rep_Qualification; ?></td>
					</tr>

					<tr>
                      	 <td><input type='checkbox' checked  class='checkbox' name='checkfield[]' value='YEAROFPASSING'></td>
						 <td><?php echo $lang_rep_YearOfPassing; ?></td>
					</tr>

					<tr>
                      	 <td><input type='checkbox' checked class='checkbox' name='checkfield[]' value='EMPSTATUS'></td>
						 <td><?php echo $lang_rep_EmploymentStatus; ?></td>
					</tr>

					<tr>
                      	 <td><input type='checkbox' checked class='checkbox' name='checkfield[]' value='PAYGRD'></td>
						 <td><?php echo $lang_rep_PayGrade; ?></td>
					</tr>

					<tr>
                      	 <td><input type='checkbox' checked class='checkbox' name='checkfield[]' value='LANGUAGES'></td>
						 <td><?php echo $lang_rep_Languages; ?></td>
					</tr>

					<tr>
                      	 <td><input type='checkbox' checked class='checkbox' name='checkfield[]' value='SKILLS'></td>
						 <td><?php echo $lang_rep_Skills; ?></td>
					</tr>

					<tr>
                      	 <td><input type='checkbox' checked class='checkbox' name='checkfield[]' value='CONTRACT'></td>
						 <td><?php echo $lang_rep_Contract; ?></td>
					</tr>

					<tr>
                      	 <td><input type='checkbox' checked class='checkbox' name='checkfield[]' value='WORKEXPERIENCE'></td>
						 <td><?php echo $lang_rep_WorkExperience; ?></td>
					</tr>

                  </table></td>
                  <td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                </tr>
                <tr>
                  <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
                  <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
                  <td><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="16" border="0" alt=""></td>
                </tr>
                <tr><td>&nbsp;</td></tr>
            	<tr><td>&nbsp;</td></tr>
              </table>
</td></tr>
</table>
</form>
</body>
<?php } else if ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'updatemode')) {  ?>
<body>
<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
  <tr>
    <td valign='top'>&nbsp; </td>
    <td width='100%'><h2><?php echo $headingInfo[1]?></h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>
</table>
<table border="0" >
  <tr>
  <td valign="middle" height="35"><img title="Back" onMouseOut="this.src='../../themes/beyondT/pictures/btn_back.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_back_02.jpg';"  src="../../themes/beyondT/pictures/btn_back.jpg" onClick="goBack();"></td>
  </tr>
</table>

<p>
<p>
<table border="0">
<form name="frmEmpRepTo" method="post" action="<?php echo $_SERVER['PHP_SELF']?>?repcode=<?php echo $this->getArr['repcode']?>&id=<?php echo $this->getArr['id']?>&capturemode=updatemode">
<input type="hidden" name="sqlState">

<?php $edit = $this->popArr['editArr']; ?>
<tr><td>
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
                      <td><?php echo $lang_repview_ReportID; ?></td>
    				  <td ><strong><?php echo $edit[0][0]?></strong><input type="hidden" name="txtRepID" value="<?php echo $edit[0][0]?>"></td>

    				 </tr>
    				 <tr>
 					  <td><?php echo $lang_repview_ReportName; ?></td>
					  <td ><input type="text" disabled name="txtRepName" value="<?php echo isset($this->post['txtRepName']) ? $this->post['txtRepName'] : $edit[0][1]?>"></td>
					</tr>
    				 <tr>
 					  <td><b><a href="<?php echo $_SERVER['PHP_SELF']?>?id=<?php echo $edit[0][0]?>&repcode=RUG"><?php echo $lang_rep_AssignUserGroups; ?></a></b></td>
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
</td> </tr>
<tr>
    <td valign="bottom" height="35"><h4><?php echo $lang_rep_SelectionCriteria; ?></h4></td>
  </tr>
  <tr><td>
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

                  <?php $editCriteriaChk= $this->popArr['editCriteriaChk'];?>
                  <?php $criteriaData=$this->popArr['editCriteriaData'];?>
                    <tr>
                       <td><input <?php echo isset($_POST['txtRepName']) ? '' : 'disabled'?> type='checkbox' <?php echo in_array('EMPNO',$editCriteriaChk) ? 'checked' : ''?> class='checkbox'  name='chkcriteria[]' id='EMPNO' value='EMPNO' onClick="chkboxCriteriaEnable()" <?php echo  (isset($this->postArr['chkcriteria']) && in_array('EMPNO', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> ></td>
                      <td valign="top"><?php echo $lang_rep_Employee; ?></td>
                      <?php
                      	$empId = isset($criteriaData['EMPNO'][0]) ? $criteriaData['EMPNO'][0] : false;

                      	if ($empId) {
                      		 $empId = $empInfoObj->fetchEmployeeId($empId);
                      		 if (!$empId) {
                      		 	$empId = $criteriaData['EMPNO'][0];
                      		 }
                      	} else {
                      		$empId = "";
                      	}
                      ?>
                      <td align="left">
	                      <select name="cmbId" onchange="disableEmployeeId();" disabled >
						  	<option value="0"><?php echo $lang_Leave_Common_AllEmployees;?></option>
							<option value="1" <?php echo ($empId == "")?"":"selected='selected'"; ?> ><?php echo $lang_Leave_Common_Select;?> --></option>
						  </select>
					  </td>
                      <td align="left" valign="top"><input type="text" <?php echo ($empId == "")?'style="visibility:hidden;"':''; ?> readonly name="cmbRepEmpID" value="<?php echo $empId;?>" ><input type="hidden" readonly name="txtRepEmpID" value="<?php echo isset($criteriaData['EMPNO'][0]) ? $criteriaData['EMPNO'][0] : ''?>"   ></td>
                      <td align="left"><input class="button" type="button" <?php echo ($empId == "")?'style="visibility:hidden;"':''; ?> name="empPop" value=".." onClick="returnEmpDetail();" <?php echo  (isset($this->postArr['chkcriteria']) && in_array('EMPNO', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?>></td>
  					</tr>



					<tr>
					 <td><input <?php echo isset($_POST['txtRepName']) ? '' : 'disabled'?> type='checkbox' <?php echo in_array('AGE',$editCriteriaChk) ? 'checked' : ''?> type='checkbox' class='checkbox' name='chkcriteria[]' id='AgeGroup' value="AGE" onclick="chkboxCriteriaEnable()" <?php echo  (isset($this->postArr['chkcriteria']) && in_array('AGE', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> ></td>
  					 <td valign="top"><?php echo $lang_rep_AgeGroup; ?></td>
					 <td align="left" valign="top"> <select name="cmbAgeCode" onChange="disableAgeField()" <?php echo  (isset($this->postArr['chkcriteria']) && in_array('AGE', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?> class="cmb" >
 					  <option value="0">--<?php echo $lang_rep_SelectComparison; ?>--</option>
<?php
							$keys   = array_keys($arrAgeSim);
							$values = array_values($arrAgeSim);

							for($c=0;count($arrAgeSim)>$c;$c++)
								if(isset($this->postArr['cmbAgeCode'])) {
									if($this->postArr['cmbAgeCode']==$values[$c])
										echo "<option selected value='" .$values[$c]. "'>" .$keys[$c]. "</option>";
									else
										echo "<option value='" .$values[$c]. "'>" .$keys[$c]. "</option>";
								} else {
									if(isset($criteriaData['AGE']) && $criteriaData['AGE'][0]==$values[$c])
										echo "<option selected value='" .$values[$c]. "'>" .$keys[$c]. "</option>";
									else
										echo "<option value='" .$values[$c]. "'>" .$keys[$c]. "</option>";
								}
?>
					  </select>
				    </td>
					<td> <input type="text" <?php echo isset($criteriaData['AGE'][1]) ? '' : 'style="visibility: hidden;"'?> disabled name='txtEmpAge1' value="<?php echo isset($criteriaData['AGE'][1]) ? $criteriaData['AGE'][1] : ''?>" ></td>
					<td> <input type="text" <?php echo isset($criteriaData['AGE'][2]) ? '' : 'style="visibility: hidden;"'?> disabled name='txtEmpAge2' value="<?php echo isset($criteriaData['AGE'][2]) ? $criteriaData['AGE'][2] : ''?>" ></td>

					</tr>

					<tr>
				  <td><input <?php echo isset($_POST['txtRepName']) ? '' : 'disabled'?> type='checkbox' <?php echo in_array('PAYGRD',$editCriteriaChk) ? 'checked' : ''?> type='checkbox' class='checkbox' name='chkcriteria[]' id='PayGrade' value="PAYGRD" onclick="chkboxCriteriaEnable()" <?php echo  (isset($this->postArr['chkcriteria']) && in_array('PAYGRD', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> ></td>
				  <td><?php echo $lang_rep_PayGrade; ?></td>
			      <td><select  name="cmbSalGrd"   <?php echo  (isset($this->postArr['chkcriteria']) && in_array('PAYGRD', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?> class="cmb" >
		<option value="0">--<?php echo $lang_rep_SelectPayGrade; ?>--</option>
<?php					$grdlist = $this->popArr['grdlist'];
					for($c=0;$grdlist && count($grdlist)>$c;$c++)
						if(isset($this->postArr['cmbSalGrd'])) {
							if($this->postArr['cmbSalGrd']==$grdlist[$c][0])
								echo "<option selected value='" .$grdlist[$c][0]. "'>" .$grdlist[$c][1]. "</option>";
							else
								echo "<option value='" .$grdlist[$c][0]. "'>" .$grdlist[$c][1]. "</option>";
						} else {
							if(isset($criteriaData['PAYGRD']) && $criteriaData['PAYGRD'][0]==$grdlist[$c][0])
								echo "<option selected value='" .$grdlist[$c][0]. "'>" .$grdlist[$c][1]. "</option>";
							else
								echo "<option value='" .$grdlist[$c][0]. "'>" .$grdlist[$c][1]. "</option>";
						}
?>
			  </select></td>
					</tr>


   					<tr>
					<td><input <?php echo isset($_POST['txtRepName']) ? '' : 'disabled'?> type='checkbox' <?php echo in_array('QUL',$editCriteriaChk) ? 'checked' : ''?> type='checkbox' class='checkbox' name='chkcriteria[]' id='QualType' value="QUL" onclick="chkboxCriteriaEnable()" <?php echo  (isset($this->postArr['chkcriteria']) && in_array('QUL', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> ></td>
				    <td><?php echo $lang_rep_Education; ?></td>
    				  <td>
					  <select name="TypeCode"  <?php echo  (isset($this->postArr['chkcriteria']) && in_array('QUL', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?> class="cmb" >
					  <option value=0>--<?php echo $lang_rep_SelectEducation; ?>--</option>
<?php
						$edulist=$this -> popArr['edulist'];
						for($c=0;$edulist && count($edulist)>$c;$c++)
							if(isset($criteriaData['QUL']) && $criteriaData['QUL'][0]==$edulist[$c][0])
							   echo "<option selected value=" . $edulist[$c][0] . ">" . $edulist[$c][2].', '.$edulist[$c][1] . "</option>";
							else
							   echo "<option value=" . $edulist[$c][0] . ">" .$edulist[$c][2].', '. $edulist[$c][1] . "</option>";
?>
					  </select></td>
						<td valign="middle"></td>
						<td align="left" valign="middle"></td>
					</tr>


<tr>

					  <td><input <?php echo isset($_POST['txtRepName']) ? '' : 'disabled'?> type='checkbox' <?php echo in_array('EMPSTATUS',$editCriteriaChk) ? 'checked' : ''?> type='checkbox' class='checkbox' name='chkcriteria[]' id='EmpType' value="EMPSTATUS" onclick="chkboxCriteriaEnable()" <?php echo  (isset($this->postArr['chkcriteria']) && in_array('EMPSTATUS', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> ></td>
					  <td valign="top"><?php echo $lang_rep_EmploymentStatus; ?></td>
					  	<td><select name="cmbEmpType"  <?php echo  (isset($this->postArr['chkcriteria']) && in_array('EMPSTATUS', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?> class="cmb" >
			  		<option value="0">--<?php echo $lang_rep_SelectEmploymentType; ?>--</option>
<?php							$arrEmpType = $this->popArr['arrEmpType'];
								for($c=0;count($arrEmpType)>$c;$c++)
								if(isset($this->postArr['cmbEmpType'])){
									if($this->postArr['cmbEmpType']==$arrEmpType[$c][0])
										echo "<option selected value='".$arrEmpType[$c][0]."'>" .$arrEmpType[$c][1]. "</option>";
									else
										echo "<option value='".$arrEmpType[$c][0]."'>" .$arrEmpType[$c][1]. "</option>";
								} else {
									if(isset($criteriaData['EMPSTATUS']) && $criteriaData['EMPSTATUS'][0]==$arrEmpType[$c][0])
										echo "<option selected value='".$arrEmpType[$c][0]."'>" .$arrEmpType[$c][1]. "</option>";
									else
										echo "<option value='".$arrEmpType[$c][0]."'>" .$arrEmpType[$c][1]. "</option>";
								}
?>
			         		</select>
						</td>
					</tr>



					<tr>
					   <td><input <?php echo isset($_POST['txtRepName']) ? '' : 'disabled'?> type='checkbox' <?php echo in_array('SERPIR',$editCriteriaChk) ? 'checked' : ''?> type='checkbox' class='checkbox' name='chkcriteria[]' id='SerPeriod' value="SERPIR" onclick="chkboxCriteriaEnable()" <?php echo  (isset($this->postArr['chkcriteria']) && in_array('SERPIR', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> ></td>
					  <td valign="top"><?php echo $lang_rep_ServicePeriod; ?></td>
					    <td align="left" valign="middle"> <select  name="cmbSerPerCode" onChange="disableSerPeriodField()"  <?php echo  (isset($this->postArr['chkcriteria']) && in_array('SERPIR', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?>  class="cmb" >
					     <option value="0">--<?php echo $lang_rep_SelectComparison; ?>--</option>
<?php
							$keys   = array_keys($arrAgeSim);
							$values = array_values($arrAgeSim);

							for($c=0;count($arrAgeSim)>$c;$c++)
								if(isset($this->postArr['cmbSerPerCode'])){
									 if($this->postArr['cmbSerPerCode']==$values[$c])
										echo "<option selected value='" .$values[$c]. "'>" .$keys[$c]. "</option>";
									else
										echo "<option value='" .$values[$c]. "'>" .$keys[$c]. "</option>";
								} else {
									  if(isset($criteriaData['SERPIR'][0]) && ($criteriaData['SERPIR'][0]==$values[$c]))
										echo "<option selected value='" .$values[$c]. "'>" .$keys[$c]. "</option>";
									else
										echo "<option value='" .$values[$c]. "'>" .$keys[$c]. "</option>";
								}
							?>
					     </select>
					  	</td>
				        <td><input type="text" <?php echo isset($criteriaData['SERPIR'][1]) ? '' : 'style="visibility: hidden;"'?> disabled name="Service1" value="<?php echo isset($criteriaData['SERPIR'][1]) ? $criteriaData['SERPIR'][1] : ''?>" ></td>
                        <td><input type="text" <?php echo isset($criteriaData['SERPIR'][2]) ? '' : 'style="visibility: hidden;"'?> disabled name="Service2" value="<?php echo isset($criteriaData['SERPIR'][2]) ? $criteriaData['SERPIR'][2] : ''?>" ></td>
					</tr>

   					<tr>
					 <td><input <?php echo isset($_POST['txtRepName']) ? '' : 'disabled'?> type='checkbox' <?php echo in_array('JOBTITLE',$editCriteriaChk) ? 'checked' : ''?> type='checkbox' class='checkbox' name='chkcriteria[]' id='JobTitle' value="JOBTITLE" onclick="chkboxCriteriaEnable()" <?php echo  (isset($this->postArr['chkcriteria']) && in_array('JOBTITLE', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> ></td>

					 <td><?php echo $lang_rep_JobTitle; ?></td>
					  <td><select  name="cmbDesig"  <?php echo (isset($this->postArr['chkcriteria']) && in_array('JOBTITLE', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?> class="cmb" >
				  		<option value="0">---<?php echo $lang_rep_SelectJobTitle; ?>---</option>
						<?php
							$deslist = $this->popArr['deslist'];
							for($c=0;$deslist && count($deslist)>$c;$c++)
								if(isset($this->postArr['cmbDesig'])) {
									if($this->postArr['cmbDesig']==$deslist[$c][0])

											echo "<option selected value='" .$deslist[$c][0]. "'>" .$deslist[$c][1]. "</option>";
										else
											echo "<option value='" .$deslist[$c][0]. "'>" .$deslist[$c][1]. "</option>";
								} else {
									 if(isset($criteriaData['JOBTITLE']) && $criteriaData['JOBTITLE'][0]==$deslist[$c][0])

											echo "<option selected value='" .$deslist[$c][0]. "'>" .$deslist[$c][1]. "</option>";
										else
											echo "<option value='" .$deslist[$c][0]. "'>" .$deslist[$c][1]. "</option>";
								}
						?>
					  </select></td>
					</tr>

					<tr>
					 <td><input <?php echo isset($_POST['txtRepName']) ? '' : 'disabled'?> type='checkbox' <?php echo in_array('LANGUAGE',$editCriteriaChk) ? 'checked' : ''?> type='checkbox' class='checkbox' name='chkcriteria[]' id='Language' value="LANGUAGE" onclick="chkboxCriteriaEnable()" <?php echo  (isset($this->postArr['chkcriteria']) && in_array('LANGUAGE', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> ></td>

					 <td><?php echo $lang_rep_Language; ?></td>
					  <td><select  name="cmbLanguage"  <?php echo (isset($this->postArr['chkcriteria']) && in_array('LANGUAGE', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?> class="cmb" >
				  		<option value="0">---<?php echo $lang_rep_SelectLanguage; ?>---</option>
						<?php
							$deslist = $this->popArr['languageList'];
							for($c=0;$deslist && count($deslist)>$c;$c++)
								if(isset($this->postArr['cmbLanguage'])) {
									if($this->postArr['cmbLanguage']==$deslist[$c][0])

											echo "<option selected value='" .$deslist[$c][0]. "'>" .$deslist[$c][1]. "</option>";
										else
											echo "<option value='" .$deslist[$c][0]. "'>" .$deslist[$c][1]. "</option>";
								} else {
									 if(isset($criteriaData['LANGUAGE']) && $criteriaData['LANGUAGE'][0]==$deslist[$c][0])

											echo "<option selected value='" .$deslist[$c][0]. "'>" .$deslist[$c][1]. "</option>";
										else
											echo "<option value='" .$deslist[$c][0]. "'>" .$deslist[$c][1]. "</option>";
								}
						?>
					  </select></td>
					</tr>

					<tr>
					 <td><input <?php echo isset($_POST['txtRepName']) ? '' : 'disabled'?> type='checkbox' <?php echo in_array('SKILL',$editCriteriaChk) ? 'checked' : ''?> type='checkbox' class='checkbox' name='chkcriteria[]' id='Skill' value="SKILL" onclick="chkboxCriteriaEnable()" <?php echo  (isset($this->postArr['chkcriteria']) && in_array('SKILL', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> ></td>

					 <td><?php echo $lang_rep_Skill; ?></td>
					  <td><select  name="cmbSkill"  <?php echo (isset($this->postArr['chkcriteria']) && in_array('LANGUAGE', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?> class="cmb" >
				  		<option value="0">---<?php echo $lang_rep_SelectSkill; ?>---</option>
						<?php
							$deslist = $this->popArr['skillList'];
							for($c=0;$deslist && count($deslist)>$c;$c++)
								if(isset($this->postArr['cmbSkill'])) {
									if($this->postArr['cmbSkill']==$deslist[$c][0])

											echo "<option selected value='" .$deslist[$c][0]. "'>" .$deslist[$c][1]. "</option>";
										else
											echo "<option value='" .$deslist[$c][0]. "'>" .$deslist[$c][1]. "</option>";
								} else {
									 if(isset($criteriaData['SKILL']) && $criteriaData['SKILL'][0]==$deslist[$c][0])

											echo "<option selected value='" .$deslist[$c][0]. "'>" .$deslist[$c][1]. "</option>";
										else
											echo "<option value='" .$deslist[$c][0]. "'>" .$deslist[$c][1]. "</option>";
								}
						?>
					  </select></td>
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
                <tr><td>&nbsp;</td></tr>
              </table>
</td> </tr>
<tr>
	<td><img src="../../themes/beyondT/pictures/btn_edit.jpg" title="Edit" onMouseOut="mout();" onMouseOver="mover();" name="Edit" onClick="edit();"></td>
</tr>
                <tr><td>&nbsp;</td></tr>
  <tr>
    <td height="15"><h4><?php echo $lang_rep_Field;?></h4></td>
  </tr>
<tr><td>
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
                  <?php $fieldArr= $this->popArr['fieldList'];?>

                    <tr>
                      	 <td><input disabled type='checkbox' <?php echo in_array('EMPNO',$fieldArr) ? 'checked': ''?>  class='checkbox' name='checkfield[]' value='EMPNO'></td>
						 <td><?php echo $lang_rep_EmpNo; ?></td>
					</tr>

					<tr>
                      	 <td><input disabled type='checkbox' <?php echo in_array('EMPFIRSTNAME',$fieldArr) ? 'checked': ''?>  class='checkbox' name='checkfield[]' value='EMPFIRSTNAME'></td>
						 <td><?php echo $lang_rep_FirstName; ?></td>
					</tr>

					<tr>
                      	 <td><input disabled type='checkbox' <?php echo in_array('EMPLASTNAME',$fieldArr) ? 'checked': ''?>  class='checkbox' name='checkfield[]' value='EMPLASTNAME'></td>
						 <td><?php echo $lang_rep_LastName; ?></td>
					</tr>

					<tr>
                      	 <td><input disabled type='checkbox' <?php echo in_array('ADDRESS1',$fieldArr) ? 'checked': ''?>  class='checkbox' name='checkfield[]' value='ADDRESS1'></td>
						 <td><?php echo $lang_rep_Address; ?></td>
					</tr>



					<tr>
                      	 <td><input disabled type='checkbox' <?php echo in_array('TELENO',$fieldArr) ? 'checked': ''?>  class='checkbox' name='checkfield[]' value='TELENO'></td>
						 <td><?php echo $lang_rep_TelNo; ?></td>
					</tr>

					<tr>
                      	 <td><input disabled type='checkbox' <?php echo in_array('AGE',$fieldArr) ? 'checked': ''?>  class='checkbox' name='checkfield[]' value='AGE'></td>
						 <td><?php echo $lang_rep_DateOfBirth; ?></td>
					</tr>

					<tr>
                      	 <td><input disabled type='checkbox' <?php echo in_array('REPORTTO',$fieldArr) ? 'checked': ''?> class='checkbox' name='checkfield[]' value='REPORTTO'></td>
						 <td><?php echo $lang_rep_ReportTo; ?></td>
					</tr>

					<tr>
                      	 <td><input disabled type='checkbox' <?php echo in_array('REPORTINGMETHOD',$fieldArr) ? 'checked': ''?> class='checkbox' name='checkfield[]' value='REPORTINGMETHOD'></td>
						 <td><?php echo $lang_rep_ReportingMethod; ?></td>
					</tr>

					<tr>
                      	 <td><input disabled type='checkbox' <?php echo in_array('JOBTITLE',$fieldArr) ? 'checked': ''?>  class='checkbox' name='checkfield[]' value='JOBTITLE'></td>
						 <td><?php echo $lang_rep_JobTitle; ?></td>
					</tr>

					<tr>
                      	 <td><input disabled type='checkbox' <?php echo in_array('SERPIR',$fieldArr) ? 'checked': ''?>  class='checkbox' name='checkfield[]' value='SERPIR'></td>
						 <td><?php echo $lang_rep_JoinDate; ?></td>
					</tr>

					<tr>
                      	 <td><input disabled type='checkbox' <?php echo in_array('SUBDIVISION',$fieldArr) ? 'checked': ''?> class='checkbox' name='checkfield[]' value='SUBDIVISION'></td>
						 <td><?php echo $lang_rep_SubDivision; ?></td>
					</tr>

					<tr>
                      	 <td><input disabled type='checkbox' <?php echo in_array('QUL',$fieldArr) ? 'checked': ''?>  class='checkbox' name='checkfield[]' value='QUL'></td>
						 <td><?php echo $lang_rep_Qualification; ?></td>
					</tr>

					<tr>
                      	 <td><input disabled type='checkbox' <?php echo in_array('YEAROFPASSING',$fieldArr) ? 'checked': ''?>  class='checkbox' name='checkfield[]' value='YEAROFPASSING'></td>
						 <td><?php echo $lang_rep_YearOfPassing; ?></td>
					</tr>

					<tr>
                      	 <td><input disabled type='checkbox' <?php echo in_array('EMPSTATUS',$fieldArr) ? 'checked': ''?>  class='checkbox' name='checkfield[]' value='EMPSTATUS'></td>
						 <td><?php echo $lang_rep_EmployeeStates; ?></td>
					</tr>

					<tr>
                      	 <td><input disabled type='checkbox' <?php echo in_array('PAYGRD',$fieldArr) ? 'checked': ''?>  class='checkbox' name='checkfield[]' value='PAYGRD'></td>
						 <td><?php echo $lang_rep_PayGrade; ?></td>
					</tr>

					<tr>
                      	 <td><input disabled type='checkbox' <?php echo in_array('LANGUAGES',$fieldArr) ? 'checked': ''?>  class='checkbox' name='checkfield[]' value='LANGUAGES'></td>
						 <td><?php echo $lang_rep_Languages; ?></td>
					</tr>

					<tr>
                      	 <td><input disabled type='checkbox' <?php echo in_array('SKILLS',$fieldArr) ? 'checked': ''?>  class='checkbox' name='checkfield[]' value='SKILLS'></td>
						 <td><?php echo $lang_rep_Skills; ?></td>
					</tr>

					<tr>
                      	 <td><input disabled type='checkbox' <?php echo in_array('CONTRACT',$fieldArr) ? 'checked': ''?> class='checkbox' name='checkfield[]' value='CONTRACT'></td>
						 <td><?php echo $lang_rep_Contract; ?></td>
					</tr>

					<tr>
                      	 <td><input disabled type='checkbox' <?php echo in_array('WORKEXPERIENCE',$fieldArr) ? 'checked': ''?> class='checkbox' name='checkfield[]' value='WORKEXPERIENCE'></td>
						 <td><?php echo $lang_rep_WorkExperience; ?></td>
					</tr>

                  </table></td>
                  <td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                </tr>
                <tr>
                  <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
                  <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
                  <td><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="16" border="0" alt=""></td>
                </tr>
                <tr><td>&nbsp;</td></tr>
            	<tr><td>&nbsp;</td></tr>
              </table>
</td></tr>
</table>
</form>
</body>
<?php } ?>
</html>
