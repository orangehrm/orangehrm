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
require_once ROOT_PATH . '/lib/common/LocaleUtil.php';

$sysConst = new sysConf();
$locRights = $_SESSION['localRights'];

$arrAgeSim = $this->popArr['arrAgeSim'];
$arrEmpType = $this->popArr['arrEmpType'];
$arrSerPer = $this->popArr['arrSerPer'];
$arrJoiDat = $this->popArr['arrJoiDat'];

if (isset($this->getArr['msg'])) {
	$msg = $this->getArr['msg'];
	$displayMsg = "lang_rep_Error_{$msg}";
	$displayMsg = $$displayMsg;
}

$empInfoObj = new EmpInfo();

$formAction = $_SERVER['PHP_SELF']. '?repcode=' . $this->getArr['repcode'];

if ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'updatemode')) {
    $heading = "$lang_emprepinfo_heading : $lang_Common_Edit";
    $new = false;
    $formAction .= '&amp;id=' . $this->getArr['id'] . '&amp;capturemode=updatemode';
    $edit = $this->popArr['editArr'];
    $reportId = $edit[0][0];
    $reportName = $edit[0][1];
    $editCriteriaChk= $this->popArr['editCriteriaChk'];
    $criteriaData=$this->popArr['editCriteriaData'];
    $disabled = 'disabled="disabled"';

} else {
    $heading = "$lang_emprepinfo_heading : $lang_Common_Edit";
    $new = true;
    $formAction .= '&amp;capturemode=addmode';
    $heading = "$lang_emprepinfo_heading : $lang_Common_New";
    $reportId = '';
    $reportName = '';
    $disabled = '';
}

// Post values
$reportName = isset($this->postArr['txtRepName'])  ? CommonFunctions::escapeHtml($this->postArr['txtRepName']) : $reportName;

// TODO: This file has to be simplified (eg: combine add/update part) and cleaned up.

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script src="../../scripts/time.js"></script>
<?php include ROOT_PATH."/lib/common/calendar.php"; ?>
<style type="text/css">

div.reportName {
	margin: 4px 4px 4px 14px;
	padding-bottom: 16px;
}

div.selectionCriteria {
	margin: 4px 4px 4px 14px;
}

</style>

<script>
//<![CDATA[

	function validDate(txt) {
		txt = txt.trim();
		if ((txt == '') || !YAHOO.OrangeHRM.calendar.parseDate(txt)) {
			return false;
		} else {
			return true;
		}

	}


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
		var popup=window.open('../../templates/hrfunct/emppop.php?reqcode=REP&REPORT=REPORT','Employees','height=450,width=400,scrollbars=1');
        if(!popup.opener) popup.opener=self;
}


function addCat() {
    document.frmEmpRepTo.sqlState.value="OWN";
    document.frmEmpRepTo.submit();
}

function edit() {

    var editBtn = $('editBtn');
	if(editBtn.title=='<?php echo $lang_Common_Save;?>') {
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
	editBtn.className="savebutton";
    editBtn.value='<?php echo $lang_Common_Save;?>';
	editBtn.title='<?php echo $lang_Common_Save;?>';
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
					 						document.frmEmpRepTo.Service1.value='Type in Years';
					 						document.frmEmpRepTo.Service2.value='Type in Years';
					 						document.frmEmpRepTo.Service1.style.visibility = "hidden";
					 						document.frmEmpRepTo.Service2.style.visibility = "hidden";

											document.frmEmpRepTo.Service1.disabled = false;
					 						document.frmEmpRepTo.Service2.disabled = false;
					 						} break;

					 	case 'JoinedDate'  : document.frmEmpRepTo.cmbJoiDatCode.disabled= !elements[i].checked;
											disableJoiDatField()

					 						if(!elements[i].checked){
					 						document.frmEmpRepTo.cmbJoiDatCode.options[0].selected = true;
					 						document.frmEmpRepTo.Join1.value='';
					 						document.frmEmpRepTo.Join2.value='';
					 						document.frmEmpRepTo.Join1.style.visibility = "hidden";
					 						document.frmEmpRepTo.Join2.style.visibility = "hidden";
					 						document.frmEmpRepTo.Join1Button.style.visibility = "hidden";
					 						document.frmEmpRepTo.Join2Button.style.visibility = "hidden";

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

	} else if(document.frmEmpRepTo.cmbAgeCode.value=="range") {
		document.frmEmpRepTo.txtEmpAge1.disabled = false;
		document.frmEmpRepTo.txtEmpAge2.disabled = false;
		document.frmEmpRepTo.txtEmpAge1.style.visibility = "visible";
		document.frmEmpRepTo.txtEmpAge2.style.visibility = "visible";

	} else if(document.frmEmpRepTo.cmbAgeCode.value=='<' || document.frmEmpRepTo.cmbAgeCode.value=='>') {
		document.frmEmpRepTo.txtEmpAge1.disabled = false;
		document.frmEmpRepTo.txtEmpAge2.disabled = true;
		document.frmEmpRepTo.txtEmpAge1.style.visibility = "visible";
		document.frmEmpRepTo.txtEmpAge2.style.visibility = "hidden";
		document.frmEmpRepTo.txtEmpAge2.value='';

	}
    document.frmEmpRepTo.txtEmpAge1.style.width = "4em";
    document.frmEmpRepTo.txtEmpAge2.style.width = "4em";

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
	} else if(document.frmEmpRepTo.cmbSerPerCode.value=="range") {
		document.frmEmpRepTo.Service1.disabled = false;
		document.frmEmpRepTo.Service2.disabled = false;
		document.frmEmpRepTo.Service1.style.visibility = "visible";
		document.frmEmpRepTo.Service2.style.visibility = "visible";
	} else if(document.frmEmpRepTo.cmbSerPerCode.value=='<' || document.frmEmpRepTo.cmbSerPerCode.value=='>') {
		document.frmEmpRepTo.Service1.disabled = false;
		document.frmEmpRepTo.Service2.disabled = true;
		document.frmEmpRepTo.Service1.style.visibility = "visible";
		document.frmEmpRepTo.Service2.style.visibility = "hidden";
		document.frmEmpRepTo.Service2.value='Type in Years';
	}
    document.frmEmpRepTo.Service1.style.width = "10em";
    document.frmEmpRepTo.Service2.style.width = "10em"

}


function disableJoiDatField() {
	if(document.frmEmpRepTo.cmbJoiDatCode.value=="0") {
		document.frmEmpRepTo.Join1.disabled = true;
		document.frmEmpRepTo.Join2.disabled = true;
		document.frmEmpRepTo.Join1.style.visibility = "hidden";
		document.frmEmpRepTo.Join2.style.visibility = "hidden";
		document.frmEmpRepTo.Join1Button.style.visibility = "hidden";
		document.frmEmpRepTo.Join2Button.style.visibility = "hidden";

	} else if(document.frmEmpRepTo.cmbJoiDatCode.value=="range") {
		document.frmEmpRepTo.Join1.disabled = false;
		document.frmEmpRepTo.Join2.disabled = false;
		document.frmEmpRepTo.Join1Button.disabled = false;
		document.frmEmpRepTo.Join2Button.disabled = false;
		document.frmEmpRepTo.Join1.style.visibility = "visible";
		document.frmEmpRepTo.Join2.style.visibility = "visible";
		document.frmEmpRepTo.Join1Button.style.visibility = "visible";
		document.frmEmpRepTo.Join2Button.style.visibility = "visible";

	} else if(document.frmEmpRepTo.cmbJoiDatCode.value=='<' || document.frmEmpRepTo.cmbJoiDatCode.value=='>') {
		document.frmEmpRepTo.Join1.disabled = false;
		document.frmEmpRepTo.Join2.disabled = true;
		document.frmEmpRepTo.Join1Button.disabled = false;
		document.frmEmpRepTo.Join1.style.visibility = "visible";
		document.frmEmpRepTo.Join2.style.visibility = "hidden";
		document.frmEmpRepTo.Join2.value='';
		document.frmEmpRepTo.Join1Button.style.visibility = "visible";
		document.frmEmpRepTo.Join2Button.style.visibility = "hidden";

	}
    document.frmEmpRepTo.Join1.style.width = "100px";
    document.frmEmpRepTo.Join2.style.width = "100px";
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

function resetForm() {
	form = $('frmEmpRepTo');
	form.reset();
	chkboxCriteriaEnable();
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
											}

											else if(elements[i].checked && document.frmEmpRepTo.cmbSerPerCode.value=='range') {

												if(!numeric(document.frmEmpRepTo.Service1)) {
													alert("<?php echo $lang_rep_ValueShouldBeinYears; ?>");
													document.frmEmpRepTo.Service1.focus();
													return false;
												}

												if(!numeric(document.frmEmpRepTo.Service2)) {
													alert("<?php echo $lang_rep_ValueShouldBeinYears; ?>");
													document.frmEmpRepTo.Service2.focus();
													return false;
												}

												if(eval(document.frmEmpRepTo.Service1.value) > eval(document.frmEmpRepTo.Service2.value)) {
													alert("<?php echo $lang_rep_InvalidRange; ?>");
													document.frmEmpRepTo.Service2.focus();
													return false;
												}


											} else if(elements[i].checked && document.frmEmpRepTo.cmbSerPerCode.value=='<' || document.frmEmpRepTo.cmbSerPerCode.value=='>') {

												if(!numeric(document.frmEmpRepTo.Service1)) {
													alert("<?php echo $lang_rep_ValueShouldBeinYears; ?>");
													document.frmEmpRepTo.Service1.focus();
													return false;
												}
											}


											break;

						// Joined Date

						case 'JoinedDate'  :
											if(elements[i].checked && document.frmEmpRepTo.cmbJoiDatCode.value=="0") {
												alert("<?php echo $lang_rep_SelectTheComparison; ?>");
												document.frmEmpRepTo.cmbJoiDatCode.focus();
												return false;
											}

											else if(elements[i].checked && document.frmEmpRepTo.cmbJoiDatCode.value=='range') {

												var join1 = document.frmEmpRepTo.Join1;
												var join2 = document.frmEmpRepTo.Join2;

												if(!validDate(join1.value)) {
													alert("<?php echo $lang_Error_InvalidDate; ?>");
													join1.focus();
													return false;
												}

												if(!validDate(join2.value)) {
													alert("<?php echo $lang_Error_InvalidDate; ?>");
													join2.focus();
													return false;
												}

												var format = YAHOO.OrangeHRM.calendar.format;
												if(strToDate(join1.value, format) > strToDate(join2.value, format)) {
													alert("<?php echo $lang_Leave_Common_InvalidDateRange; ?>");
													join2.focus();
													return false;
												}


											} else if(elements[i].checked && document.frmEmpRepTo.cmbJoiDatCode.value=='<' || document.frmEmpRepTo.cmbJoiDatCode.value=='>') {
												var join1 = document.frmEmpRepTo.Join1;
												if(!validDate(join1.value)) {
													alert("<?php echo $lang_Error_InvalidDate; ?>");
													join1.focus();
													return false;
												}
											}



											break;



		// Joined Date Ends

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

	YAHOO.OrangeHRM.container.init();

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
<?php if ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'addmode')) { ?>
<body>
    <div class="formpage2col">
        <div class="navigation">
            <input type="button" class="backbutton"
				onclick="goBack();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
				value="<?php echo $lang_Common_Back;?>" />
        </div>
        <div class="outerbox">
            <div class="mainHeading"><h2><?php echo $heading;?></h2></div>

        <?php if (isset($displayMsg)) { ?>
            <div class="messagebar">
                <span class=""><?php echo $displayMsg; ?></span>
            </div>
        <?php } ?>


<form name="frmEmpRepTo" id="frmEmpRepTo" method="post" action="<?php echo $formAction;?>">
    <input type="hidden" name="sqlState"/>
    <input type="hidden" name="token" value="<?php echo $this->popArr['token'];?>" />
<label for="txtRepName"><?php echo $lang_repview_ReportName; ?></label>
<div class="reportName">
	<input type="text" <?php echo $disabled;?> name="txtRepName" id="txtRepName" value="<?php echo $reportName; ?>"
	    class="formInputText"/>
</div>
<br class="clear"/>
<div class="subHeading"><h3><?php echo $lang_rep_SelectionCriteria; ?></h3></div>
	<div class="selectionCriteria">
		<input type='checkbox' name='chkcriteria[]' id='EMPNO' value='EMPNO' onclick="chkboxCriteriaEnable()" <?php echo  (isset($this->postArr['chkcriteria']) && in_array('EMPNO', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?>
		    class="formCheckbox"/>

		<label for="EMPNO"><?php echo $lang_rep_Employee; ?></label>
		<select name="cmbId" onchange="disableEmployeeId();" disabled="disabled" class="formSelect">
		    <option value="0"><?php echo $lang_Leave_Common_AllEmployees; ?></option>
		    <option value="1"><?php echo $lang_Leave_Common_Select; ?> --></option>
		</select>
		<input type="text" style="visibility:hidden;" readonly="readonly" name="cmbRepEmpID" value="<?php echo isset($this->postArr['txtRepEmpID']) ? $this->postArr['txtRepEmpID'] : ''?>" class="formInputText"/>
		<input type="hidden"  readonly="readonly" name="txtRepEmpID" value="<?php echo isset($this->postArr['txtRepEmpID']) ? $this->postArr['txtRepEmpID'] : ''?>" />
		<input class="empPopupButton" type="button" style="visibility:hidden;" name="empPop" value=".." onClick="returnEmpDetail();"
		    <?php echo  (isset($this->postArr['chkcriteria']) && in_array('EMPNO', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?>>
		<br class="clear"/>

		<input type='checkbox' class='formCheckbox' name='chkcriteria[]' id='AgeGroup' value="AGE" onClick="chkboxCriteriaEnable()"
		    <?php echo  (isset($this->postArr['chkcriteria']) && in_array('AGE', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> ></td>
		<label for="AgeGroup"><?php echo $lang_rep_AgeGroup; ?></label>
		<select   name="cmbAgeCode" onChange="disableAgeField();" <?php echo  (isset($this->postArr['chkcriteria']) && in_array('AGE', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?>
		    class="formSelect" >
		    <option value="0">--<?php echo $lang_rep_SelectComparison; ?>--</option>
		<?php
		$keys = array_keys($arrAgeSim);
		$values = array_values($arrAgeSim);

		for ($c = 0; count($arrAgeSim) > $c; $c++)
			if (isset ($this->postArr['cmbAgeCode']) && $this->postArr['cmbAgeCode'] == $values[$c])
				echo "<option selected value='" . $values[$c] . "'>" . $keys[$c] . "</option>";
			else
				echo "<option value='" . $values[$c] . "'>" . $keys[$c] . "</option>";
		?>
		</select>
		<input type="text" <?php echo isset($this->postArr['txtEmpAge1']) ? $this->postArr['txtEmpAge1'] : 'style="visibility:hidden;" disabled'?>
		    name='txtEmpAge1' value="<?php echo isset($this->postArr['txtEmpAge1']) ? $this->postArr['txtEmpAge1'] : ''?>"
		    class="formInputText" style="width:4em;"/>
		<input type="text" <?php echo isset($this->postArr['txtEmpAge2']) ? $this->postArr['txtEmpAge2'] : 'style="visibility:hidden;"disabled'?>
		    name='txtEmpAge2' value="<?php echo isset($this->postArr['txtEmpAge2']) ? $this->postArr['txtEmpAge2'] : ''?>"
		    class="formInputText" style="width:4em;"/>
		<br class="clear"/>

		<input type='checkbox' class='formCheckbox' name='chkcriteria[]' id='PayGrade' value="PAYGRD" onClick="chkboxCriteriaEnable()"
		    <?php echo  (isset($this->postArr['chkcriteria']) && in_array('PAYGRD', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> >
		<label for="PayGrade"><?php echo $lang_rep_PayGrade; ?></label>
		<select  name="cmbSalGrd" <?php echo  (isset($this->postArr['chkcriteria']) && in_array('PAYGRD', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?> class="formSelect" >
		    <option value="0">--<?php echo $lang_rep_SelectPayGrade; ?>--</option>
		<?php

		$grdlist = $this->popArr['grdlist'];
		for ($c = 0; $grdlist && count($grdlist) > $c; $c++)
			if (isset ($this->postArr['cmbSalGrd']) && $this->postArr['cmbSalGrd'] == $grdlist[$c][0])
				echo "<option selected value='" . $grdlist[$c][0] . "'>" . $grdlist[$c][1] . "</option>";
			else
				echo "<option value='" . $grdlist[$c][0] . "'>" . $grdlist[$c][1] . "</option>";
		?>
		</select>
		<br class="clear"/>

		<input type='checkbox' class='formCheckbox' name='chkcriteria[]' id='QualType' value="QUL" onClick="chkboxCriteriaEnable()"
		    <?php echo  (isset($this->postArr['chkcriteria']) && in_array('QUL', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> />
		<label for="QualType"><?php echo $lang_rep_Education; ?></label>
		<select name="TypeCode"  <?php echo  (isset($this->postArr['chkcriteria']) && in_array('QUL', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?> class="formSelect" >
		    <option value=0>--<?php echo $lang_rep_SelectEducation; ?>--</option>
		<?php

		$edulist = $this->popArr['edulist'];
		for ($c = 0; $edulist && count($edulist) > $c; $c++)
			if (isset ($this->postArr['TypeCode']) && $this->postArr['TypeCode'] == $edulist[$c][0])
				echo "<option selected value=" . $edulist[$c][0] . ">" . $edulist[$c][2] . ', ' . $edulist[$c][1] . "</option>";
			else
				echo "<option value=" . $edulist[$c][0] . ">" . $edulist[$c][2] . ', ' . $edulist[$c][1] . "</option>";
		?>
		</select>
		<br class="clear"/>

		<input type='checkbox' class='formCheckbox' name='chkcriteria[]' id='EmpType' value="EMPSTATUS" onClick="chkboxCriteriaEnable()"
		    <?php echo  (isset($this->postArr['chkcriteria']) && in_array('EMPSTATUS', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> ></td>
		<label for="EmpType"><?php echo $lang_rep_EmploymentStatus; ?></label>
		<select name="cmbEmpType" <?php echo  (isset($this->postArr['chkcriteria']) && in_array('EMPSTATUS', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?> class="formSelect" >
		    <option value="0">--<?php echo $lang_rep_SelectEmploymentType; ?>--</option>
		<?php

		//if(isset($this->postArr['cmbEmpType'])) {
		$arrEmpType = $this->popArr['arrEmpType'];
		for ($c = 0; $arrEmpType && count($arrEmpType) > $c; $c++)
			echo "<option value=" . $arrEmpType[$c][0] . ">" . $arrEmpType[$c][1] . "</option>";
		//}
		?>
		</select>
		<br class="clear"/>

		<?php //Service Period ?>
		<input type='checkbox' class='formCheckbox' name='chkcriteria[]' id='SerPeriod' value="SERPIR" onClick="chkboxCriteriaEnable()"
		    <?php echo  (isset($this->postArr['chkcriteria']) && in_array('SERPIR', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> >
		<label for="SerPeriod"><?php echo $lang_rep_ServicePeriod; ?></label>
		<select  name="cmbSerPerCode" onChange="disableSerPeriodField()" <?php echo  (isset($this->postArr['chkcriteria']) && in_array('SERPIR', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?>
		        class="formSelect" >
		    <option value="0">--<?php echo $lang_rep_SelectComparison; ?>--</option>
		<?php

		$keys = array_keys($arrSerPer);
		$values = array_values($arrSerPer);

		for ($c = 0; count($arrSerPer) > $c; $c++)
			if (isset ($this->postArr['cmbSerPerCode']) && $this->postArr['cmbSerPerCode'] == $values[$c])
				echo "<option selected value='" . $values[$c] . "'>" . $keys[$c] . "</option>";
			else
				echo "<option value='" . $values[$c] . "'>" . $keys[$c] . "</option>";
		?>
		</select>
		<input type="text" <?php echo isset($this->postArr['Service1']) ? $this->postArr['Service1'] : 'style="visibility:hidden;" disabled'?>
		    name="Service1" id="Service1" value="<?php echo isset($this->postArr['Service1']) ? $this->postArr['Service1'] : 'Type in Years'?>"
		    class="formInputText" style="width:4em;"/>
		<input type="text" <?php echo isset($this->postArr['Service2']) ? $this->postArr['Service2'] : 'style="visibility:hidden;" disabled'?>
		    name="Service2" id="Service2" value="<?php echo isset($this->postArr['Service2']) ? $this->postArr['Service2'] : 'Type in Years'?>"
		    class="formInputText" style="width:4em;"/>
		<?php //Service Period  ?>
		<br class="clear"/>

		<?php //Joined-Date ?>
		<input type='checkbox' class='formCheckbox' name='chkcriteria[]' id='JoinedDate' value="JOIDAT" onClick="chkboxCriteriaEnable()"
		    <?php echo  (isset($this->postArr['chkcriteria']) && in_array('JOIDAT', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> >
		<label for="JoinedDate"><?php echo $lang_rep_JoinedDate; ?></label>
		<select  name="cmbJoiDatCode" onChange="disableJoiDatField()" <?php echo  (isset($this->postArr['chkcriteria']) && in_array('JOIDAT', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?>
		        class="formSelect" >
		    <option value="0">--<?php echo $lang_rep_SelectComparison; ?>--</option>
		<?php

		$keys = array_keys($arrJoiDat);
		$values = array_values($arrJoiDat);

		for ($c = 0; count($arrJoiDat) > $c; $c++)
			if (isset ($this->postArr['cmbJoiDatCode']) && $this->postArr['cmbJoiDatCode'] == $values[$c])
				echo "<option selected value='" . $values[$c] . "'>" . $keys[$c] . "</option>";
			else
				echo "<option value='" . $values[$c] . "'>" . $keys[$c] . "</option>";
		?>
		</select>
		<input type="text" <?php echo isset($this->postArr['Join1']) ? "" : 'style="visibility:hidden;" disabled'?>
		    name="Join1" id="Join1" value="<?php echo isset($this->postArr['Join1']) ? LocaleUtil::getInstance()->formatDate($this->postArr['Join1']) : ''?>"
		    class="formDateInput" style="width:100px"/>
		<input type="button" class="calendarBtn" value="  " <?php echo isset($this->postArr['Join1']) ? $this->postArr['Join1'] : 'style="visibility:hidden;" disabled'?>
		    name="Join1Button"/>
		<input type="text" <?php echo isset($this->postArr['Join2']) ? "" : 'style="visibility:hidden;" disabled'?>
		    name="Join2" id="Join2" value="<?php echo isset($this->postArr['Join2']) ? LocaleUtil::getInstance()->formatDate($this->postArr['Join2']) : ''?>"
		    class="formDateInput" style="width:100px"/>
		<input type="button" class="calendarBtn" value="  " <?php echo isset($this->postArr['Join2']) ? $this->postArr['Join2'] : 'style="visibility:hidden;" disabled'?>
		    name="Join2Button"/>
		<?php //Joined-Date-Ends  ?>
		<br class="clear"/>

		<input type='checkbox' class='formCheckbox' name='chkcriteria[]' id='JobTitle' value="JOBTITLE" onClick="chkboxCriteriaEnable()"
		    <?php echo  (isset($this->postArr['chkcriteria']) && in_array('JOBTITLE', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> />
		<label for="JobTitle"><?php echo $lang_rep_JobTitle; ?></label>
		<select  name="cmbDesig" <?php echo (isset($this->postArr['chkcriteria']) && in_array('JOBTITLE', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?> class="formSelect" >
		    <option value="0">--<?php echo $lang_rep_SelectJobTitle; ?>--</option>
		<?php

		$deslist = $this->popArr['deslist'];
		for ($c = 0; $deslist && count($deslist) > $c; $c++)
			if (isset ($this->postArr['cmbDesig']) && $this->postArr['cmbDesig'] == $deslist[$c][0])
				echo "<option selected value='" . $deslist[$c][0] . "'>" . $deslist[$c][1] . "</option>";
			else
				echo "<option value='" . $deslist[$c][0] . "'>" . $deslist[$c][1] . "</option>";
		?>
		</select>
		<br class="clear"/>

		<input type='checkbox' class='formCheckbox' name='chkcriteria[]' id='Language' value="LANGUAGE" onClick="chkboxCriteriaEnable()"
		    <?php echo  (isset($this->postArr['chkcriteria']) && in_array('LANGUAGE', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> >
		<label for="Language"><?php echo $lang_rep_Language; ?></label>
		<select name="cmbLanguage" <?php echo (isset($this->postArr['chkcriteria']) && in_array('LANGUAGE', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?> class="formSelect" >
		    <option value="0">--<?php echo $lang_rep_SelectLanguage; ?>--</option>
		<?php

		$deslist = $this->popArr['languageList'];
		for ($c = 0; $deslist && count($deslist) > $c; $c++)
			if (isset ($this->postArr['cmbLanguage']) && $this->postArr['cmbLanguage'] == $deslist[$c][0])
				echo "<option selected value='" . $deslist[$c][0] . "'>" . $deslist[$c][1] . "</option>";
			else
				echo "<option value='" . $deslist[$c][0] . "'>" . $deslist[$c][1] . "</option>";
		?>
		</select>
		<br class="clear"/>

		<input type='checkbox' class='formCheckbox' name='chkcriteria[]' id='Skill' value="SKILL" onClick="chkboxCriteriaEnable()"
		    <?php echo  (isset($this->postArr['chkcriteria']) && in_array('SKILL', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> />
		<label for="Skill"><?php echo $lang_rep_Skill; ?></label>

		<select  name="cmbSkill" <?php echo (isset($this->postArr['chkcriteria']) && in_array('SKILL', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?> class="formSelect" >
		    <option value="0">--<?php echo $lang_rep_SelectSkill; ?>--</option>
		<?php

		$deslist = $this->popArr['skillList'];
		for ($c = 0; $deslist && count($deslist) > $c; $c++)
			if (isset ($this->postArr['cmbSkill']) && $this->postArr['cmbSkill'] == $deslist[$c][0])
				echo "<option selected value='" . $deslist[$c][0] . "'>" . $deslist[$c][1] . "</option>";
			else
				echo "<option value='" . $deslist[$c][0] . "'>" . $deslist[$c][1] . "</option>";
		?>
		</select>
		<br class="clear"/>
	</div>
<div class="formbuttons">
    <input type="button" class="savebutton"
        onclick="addEXT();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
        value="<?php echo $new ? $lang_Common_Save : $lang_Common_Edit;?>" />
     <input type="button" class="plainbtn" value="<?php echo $lang_Common_Reset; ?>"
		onmouseover="moverButton(this)" onmouseout="moutButton(this)"
		onclick="resetForm()"/>
</div>

<div class="subHeading"><h3><?php echo $lang_rep_Field; ?></h3></div>

<?php
    $addFieldList = array('EMPNO'=>$lang_rep_EmpNo,
        'EMPFIRSTNAME'=> $lang_rep_FirstName,
        'EMPLASTNAME' => $lang_rep_LastName,
        'ADDRESS1' => $lang_rep_Address,
        'TELENO' => $lang_rep_TelNo,
        'AGE' => $lang_rep_DateOfBirth,
        'REPORTTO' => $lang_rep_ReportTo,
        'REPORTINGMETHOD' => $lang_rep_ReportingMethod,
        'JOBTITLE' => $lang_rep_JobTitle,
        'SERPIR' => $lang_rep_JoinedDate,
        'SUBDIVISION' => $lang_rep_SubDivision,
        'QUL' => $lang_rep_Qualification,
        'YEAROFPASSING' => $lang_rep_YearOfPassing,
        'EMPSTATUS' => $lang_rep_EmploymentStatus,
        'PAYGRD' => $lang_rep_PayGrade,
        'LANGUAGES' => $lang_rep_Languages,
        'SKILLS' =>  $lang_rep_Skills,
        'CONTRACT' => $lang_rep_Contract,
        'WORKEXPERIENCE' => $lang_rep_WorkExperience);

    foreach ($addFieldList as $field=>$label) {
        $fieldId = 'FIELD_' . $field;
?>
    <label for='<?php echo $fieldId;?>'><?php echo $label;?></label>
    <input type='checkbox' checked='checked' class='formCheckbox' name='checkfield[]'
        value='<?php echo $field;?>' id='<?php echo $fieldId;?>'/>
    <br class="clear"/>
<?php

    }
?>
</form>
</div>
</div>
<div id="cal1Container" style="position:absolute;" ></div>
<script type="text/javascript">
//<![CDATA[
    if (document.getElementById && document.createElement) {
        roundBorder('outerbox');
    }
//]]>
</script>
</body>
<?php } else if ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'updatemode')) {  ?>
<body>
    <div class="formpage2col">
        <div class="navigation">
            <input type="button" class="backbutton"
				onclick="goBack();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
				value="<?php echo $lang_Common_Back;?>" />
        </div>
        <div class="outerbox">
            <div class="mainHeading"><h2><?php echo $heading;?></h2></div>

        <?php if (isset($displayMsg)) { ?>
            <div class="messagebar">
                <span class=""><?php echo $displayMsg; ?></span>
            </div>
        <?php } ?>
<form name="frmEmpRepTo" id="frmEmpRepTo" method="post" action="<?php echo $formAction;?>">
    <input type="hidden" name="sqlState"/>
    <input type="hidden" name="token" value="<?php echo $this->popArr['token'];?>" />
    <span class="formLabel"><?php echo $lang_repview_ReportID; ?></span>
    <span class="formValue"><?php echo $reportId; ?></span>
    <input type="hidden" name="txtRepID" value="<?php echo $reportId;?>"/>
    <br class="clear"/>

    <label for="txtRepName"><?php echo $lang_repview_ReportName; ?></label>
    <input type="text" <?php echo $disabled;?> name="txtRepName" id="txtRepName" value="<?php echo $reportName; ?>"
        class="formInputText"/>
    <br class="clear"/>

    <div class="notice" style="padding-left:10px;"><a href="<?php echo $_SERVER['PHP_SELF'] . "?id={$reportId}&amp;repcode=RUG";?>" ><?php echo $lang_rep_AssignUserGroups; ?></a></div>
    <br class="clear"/>

<div class="subHeading"><h3><?php echo $lang_rep_SelectionCriteria; ?></h3></div>
<div class="selectionCriteria">
	<input <?php echo isset($_POST['txtRepName']) ? '' : 'disabled'?> type='checkbox' <?php echo in_array('EMPNO',$editCriteriaChk) ? 'checked' : ''?>
	    name='chkcriteria[]' id='EMPNO' value='EMPNO' onclick="chkboxCriteriaEnable()"
	    <?php echo  (isset($this->postArr['chkcriteria']) && in_array('EMPNO', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?>
	    class="formCheckbox"/>

	<label for="EMPNO"><?php echo $lang_rep_Employee; ?></label>
	<?php

	$empId = isset ($criteriaData['EMPNO'][0]) ? $criteriaData['EMPNO'][0] : false;
	
	
	if ($empId) {
		$empId = $empInfoObj->fetchEmployeeId($empId);
		$employeeName = $empInfoObj->getFullName($empId, true);
		if (!$empId) {
			$empId = $criteriaData['EMPNO'][0];
		}
	} else {
		$empId = "";
		$employeeName = "";
	}
	?>
	  <select name="cmbId" onchange="disableEmployeeId();" disabled="disabled" class="formSelect">
	  	<option value="0"><?php echo $lang_Leave_Common_AllEmployees;?></option>
		<option value="1" <?php echo ($empId == "")?"":"selected='selected'"; ?> ><?php echo $lang_Leave_Common_Select;?> --></option>
	  </select>

	<input type="text" <?php echo ($empId == "")?'style="visibility:hidden;"':''; ?> readonly="readonly" name="cmbRepEmpID" value="<?php echo $employeeName;?>" class="formInputText" />
	<input type="hidden" readonly="readonly" name="txtRepEmpID" value="<?php echo isset($criteriaData['EMPNO'][0]) ? $criteriaData['EMPNO'][0] : ''?>"   />
	<input class="empPopupButton" type="button" <?php echo ($empId == "")?'style="visibility:hidden;"':''; ?> name="empPop" value=".."
	    onClick="returnEmpDetail();" <?php echo  (isset($this->postArr['chkcriteria']) && in_array('EMPNO', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?>/>
	<br class="clear"/>

	<input <?php echo isset($_POST['txtRepName']) ? '' : 'disabled'?> type='checkbox' <?php echo in_array('AGE',$editCriteriaChk) ? 'checked' : ''?>
	    type='checkbox' class='formCheckbox' name='chkcriteria[]' id='AgeGroup' value="AGE" onclick="chkboxCriteriaEnable()"
	        <?php echo  (isset($this->postArr['chkcriteria']) && in_array('AGE', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> ></td>
	<label for="AgeGroup"><?php echo $lang_rep_AgeGroup; ?></label>

	<select name="cmbAgeCode" onChange="disableAgeField()" <?php echo  (isset($this->postArr['chkcriteria']) && in_array('AGE', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?>
	    class="formSelect" >
	<option value="0">--<?php echo $lang_rep_SelectComparison; ?>--</option>
	<?php

	$keys = array_keys($arrAgeSim);
	$values = array_values($arrAgeSim);

	for ($c = 0; count($arrAgeSim) > $c; $c++)
		if (isset ($this->postArr['cmbAgeCode'])) {
			if ($this->postArr['cmbAgeCode'] == $values[$c])
				echo "<option selected value='" . $values[$c] . "'>" . $keys[$c] . "</option>";
			else
				echo "<option value='" . $values[$c] . "'>" . $keys[$c] . "</option>";
		} else {
			if (isset ($criteriaData['AGE']) && $criteriaData['AGE'][0] == $values[$c])
				echo "<option selected value='" . $values[$c] . "'>" . $keys[$c] . "</option>";
			else
				echo "<option value='" . $values[$c] . "'>" . $keys[$c] . "</option>";
		}
	?>
	</select>
	<input type="text" <?php echo isset($criteriaData['AGE'][1]) ? '' : 'style="visibility: hidden;"'?> disabled name='txtEmpAge1'
	    value="<?php echo isset($criteriaData['AGE'][1]) ? $criteriaData['AGE'][1] : ''?>"
	    class="formInputText" style="width:100px;"/>
	<input type="text" <?php echo isset($criteriaData['AGE'][2]) ? '' : 'style="visibility: hidden;"'?> disabled name='txtEmpAge2'
	    value="<?php echo isset($criteriaData['AGE'][2]) ? $criteriaData['AGE'][2] : ''?>"
	    class="formInputText" style="width:100px;"/>
	<br class="clear"/>

	<input <?php echo isset($_POST['txtRepName']) ? '' : 'disabled'?> type='checkbox' <?php echo in_array('PAYGRD',$editCriteriaChk) ? 'checked' : ''?>
	    type='checkbox' class='formCheckbox' name='chkcriteria[]' id='PayGrade' value="PAYGRD" onclick="chkboxCriteriaEnable()"
	    <?php echo  (isset($this->postArr['chkcriteria']) && in_array('PAYGRD', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> />
	<label for="PayGrade"><?php echo $lang_rep_PayGrade; ?></label>
	<select  name="cmbSalGrd"   <?php echo  (isset($this->postArr['chkcriteria']) && in_array('PAYGRD', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?> class="formSelect" >
	    <option value="0">--<?php echo $lang_rep_SelectPayGrade; ?>--</option>
	<?php

	$grdlist = $this->popArr['grdlist'];
	for ($c = 0; $grdlist && count($grdlist) > $c; $c++)
		if (isset ($this->postArr['cmbSalGrd'])) {
			if ($this->postArr['cmbSalGrd'] == $grdlist[$c][0])
				echo "<option selected value='" . $grdlist[$c][0] . "'>" . $grdlist[$c][1] . "</option>";
			else
				echo "<option value='" . $grdlist[$c][0] . "'>" . $grdlist[$c][1] . "</option>";
		} else {
			if (isset ($criteriaData['PAYGRD']) && $criteriaData['PAYGRD'][0] == $grdlist[$c][0])
				echo "<option selected value='" . $grdlist[$c][0] . "'>" . $grdlist[$c][1] . "</option>";
			else
				echo "<option value='" . $grdlist[$c][0] . "'>" . $grdlist[$c][1] . "</option>";
		}
	?>
	</select>
	<br class="clear"/>

	<input <?php echo isset($_POST['txtRepName']) ? '' : 'disabled'?> type='checkbox' <?php echo in_array('QUL',$editCriteriaChk) ? 'checked' : ''?>
	    type='checkbox' class='formCheckbox' name='chkcriteria[]' id='QualType' value="QUL" onclick="chkboxCriteriaEnable()"
	    <?php echo  (isset($this->postArr['chkcriteria']) && in_array('QUL', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> />
	<label for="QualType"><?php echo $lang_rep_Education; ?></label>
	<select name="TypeCode"  <?php echo  (isset($this->postArr['chkcriteria']) && in_array('QUL', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?> class="formSelect" >
	    <option value=0>--<?php echo $lang_rep_SelectEducation; ?>--</option>
	<?php

	$edulist = $this->popArr['edulist'];
	for ($c = 0; $edulist && count($edulist) > $c; $c++)
		if (isset ($criteriaData['QUL']) && $criteriaData['QUL'][0] == $edulist[$c][0])
			echo "<option selected value=" . $edulist[$c][0] . ">" . $edulist[$c][2] . ', ' . $edulist[$c][1] . "</option>";
		else
			echo "<option value=" . $edulist[$c][0] . ">" . $edulist[$c][2] . ', ' . $edulist[$c][1] . "</option>";
	?>
	</select>
	<br class="clear"/>

	<input <?php echo isset($_POST['txtRepName']) ? '' : 'disabled'?> type='checkbox' <?php echo in_array('EMPSTATUS',$editCriteriaChk) ? 'checked' : ''?>
	    type='checkbox' class='formCheckbox' name='chkcriteria[]' id='EmpType' value="EMPSTATUS" onclick="chkboxCriteriaEnable()"
	    <?php echo  (isset($this->postArr['chkcriteria']) && in_array('EMPSTATUS', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> />
	<label for="EmpType"><?php echo $lang_rep_EmploymentStatus; ?></label>
	<select name="cmbEmpType"  <?php echo  (isset($this->postArr['chkcriteria']) && in_array('EMPSTATUS', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?> class="formSelect" >
	    <option value="0">--<?php echo $lang_rep_SelectEmploymentType; ?>--</option>
	<?php

	$arrEmpType = $this->popArr['arrEmpType'];
	for ($c = 0; count($arrEmpType) > $c; $c++)
		if (isset ($this->postArr['cmbEmpType'])) {
			if ($this->postArr['cmbEmpType'] == $arrEmpType[$c][0])
				echo "<option selected value='" . $arrEmpType[$c][0] . "'>" . $arrEmpType[$c][1] . "</option>";
			else
				echo "<option value='" . $arrEmpType[$c][0] . "'>" . $arrEmpType[$c][1] . "</option>";
		} else {
			if (isset ($criteriaData['EMPSTATUS']) && $criteriaData['EMPSTATUS'][0] == $arrEmpType[$c][0])
				echo "<option selected value='" . $arrEmpType[$c][0] . "'>" . $arrEmpType[$c][1] . "</option>";
			else
				echo "<option value='" . $arrEmpType[$c][0] . "'>" . $arrEmpType[$c][1] . "</option>";
		}
	?>
	</select>
	<br class="clear"/>

	<?php // Service Period ?>
	<input <?php echo isset($_POST['txtRepName']) ? '' : 'disabled'?> type='checkbox'
	    <?php echo in_array('SERPIR',$editCriteriaChk) ? 'checked' : ''?> type='checkbox' class='formCheckbox'
	    name='chkcriteria[]' id='SerPeriod' value="SERPIR" onclick="chkboxCriteriaEnable()"
	    <?php echo  (isset($this->postArr['chkcriteria']) && in_array('SERPIR', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> />
	<label for="SerPeriod"><?php echo $lang_rep_ServicePeriod; ?></label>
	<select  name="cmbSerPerCode" onChange="disableSerPeriodField()"
	        <?php echo  (isset($this->postArr['chkcriteria']) && in_array('SERPIR', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?>  class="formSelect" >
	    <option value="0">--<?php echo $lang_rep_SelectComparison; ?>--</option>
	<?php

	$keys = array_keys($arrSerPer);
	$values = array_values($arrSerPer);

	for ($c = 0; count($arrAgeSim) > $c; $c++)
		if (isset ($this->postArr['cmbSerPerCode'])) {
			if ($this->postArr['cmbSerPerCode'] == $values[$c])
				echo "<option selected value='" . $values[$c] . "'>" . $keys[$c] . "</option>";
			else
				echo "<option value='" . $values[$c] . "'>" . $keys[$c] . "</option>";
		} else {
			if (isset ($criteriaData['SERPIR'][0]) && ($criteriaData['SERPIR'][0] == $values[$c]))
				echo "<option selected value='" . $values[$c] . "'>" . $keys[$c] . "</option>";
			else
				echo "<option value='" . $values[$c] . "'>" . $keys[$c] . "</option>";
		}
	?>
	</select>
	<input type="text" <?php echo isset($criteriaData['SERPIR'][1]) ? '' : 'style="visibility: hidden;"'?> disabled
	    name="Service1" value="<?php echo isset($criteriaData['SERPIR'][1]) ? $criteriaData['SERPIR'][1] : 'Type in Years'?>" id="Service1"
	    class="formInputText" style="width:4em;"/>
	<input type="text" <?php echo isset($criteriaData['SERPIR'][2]) ? '' : 'style="visibility: hidden;"'?> disabled
	    name="Service2" value="<?php echo isset($criteriaData['SERPIR'][2]) ? $criteriaData['SERPIR'][2] : 'Type in Years'?>"  id="Service2"
	    class="formInputText" style="width:4em;"/>
	<?php // Service Period Ends ?>
	<br class="clear"/>

	<?php // Joined Date ?>
	<input <?php echo isset($_POST['txtRepName']) ? '' : 'disabled'?> type='checkbox' <?php echo in_array('JOIDAT',$editCriteriaChk) ? 'checked' : ''?>
	    type='checkbox' class='formCheckbox' name='chkcriteria[]' id='JoinedDate' value="JOIDAT" onclick="chkboxCriteriaEnable()"
	    <?php echo  (isset($this->postArr['chkcriteria']) && in_array('JOIDAT', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> >
	<label for="JoinedDate"><?php echo $lang_rep_JoinedDate; ?></label>
	<select  name="cmbJoiDatCode" onChange="disableJoiDatField()"
	        <?php echo  (isset($this->postArr['chkcriteria']) && in_array('JOIDAT', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?>  class="formSelect" >
	    <option value="0">--<?php echo $lang_rep_SelectComparison; ?>--</option>
	<?php

	$keys = array_keys($arrJoiDat);
	$values = array_values($arrJoiDat);

	for ($c = 0; count($arrAgeSim) > $c; $c++)
		if (isset ($this->postArr['cmbJoiDatCode'])) {
			if ($this->postArr['cmbJoiDatCode'] == $values[$c])
				echo "<option selected value='" . $values[$c] . "'>" . $keys[$c] . "</option>";
			else
				echo "<option value='" . $values[$c] . "'>" . $keys[$c] . "</option>";
		} else {
			if (isset ($criteriaData['JOIDAT'][0]) && ($criteriaData['JOIDAT'][0] == $values[$c]))
				echo "<option selected value='" . $values[$c] . "'>" . $keys[$c] . "</option>";
			else
				echo "<option value='" . $values[$c] . "'>" . $keys[$c] . "</option>";
		}
	?>
	</select>
	<input type="text" <?php echo isset($criteriaData['JOIDAT'][1]) ? '' : 'style="visibility: hidden;"'?> disabled
	    name="Join1" value="<?php echo isset($criteriaData['JOIDAT'][1]) ? LocaleUtil::getInstance()->formatDate($criteriaData['JOIDAT'][1]) : ''?>" id="Join1"
	    class="formDateInput"/>
	<input type="button" class="calendarBtn" value="  " <?php echo isset($criteriaData['JOIDAT'][1]) ? '' : 'style="visibility: hidden;"'?> name="Join1Button"/>
	<input type="text" <?php echo isset($criteriaData['JOIDAT'][2]) ? '' : 'style="visibility: hidden;"'?> disabled name="Join2"
	    value="<?php echo isset($criteriaData['JOIDAT'][2]) ? LocaleUtil::getInstance()->formatDate($criteriaData['JOIDAT'][2]) : ''?>"  id="Join2"
	    class="formDateInput"/>
	<input type="button" class="calendarBtn" value="  " <?php echo isset($criteriaData['JOIDAT'][2]) ? '' : 'style="visibility: hidden;"'?> name="Join2Button"/>
	<?php // Joined Date Ends ?>
	<br class="clear"/>

	<input <?php echo isset($_POST['txtRepName']) ? '' : 'disabled'?> type='checkbox' <?php echo in_array('JOBTITLE',$editCriteriaChk) ? 'checked' : ''?>
	    type='checkbox' class='formCheckbox' name='chkcriteria[]' id='JobTitle' value="JOBTITLE" onclick="chkboxCriteriaEnable()"
	    <?php echo  (isset($this->postArr['chkcriteria']) && in_array('JOBTITLE', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> />
	<label for="JobTitle"><?php echo $lang_rep_JobTitle; ?></label>
	<select  name="cmbDesig"  <?php echo (isset($this->postArr['chkcriteria']) && in_array('JOBTITLE', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?> class="formSelect" >
	    <option value="0">---<?php echo $lang_rep_SelectJobTitle; ?>---</option>
	<?php

	$deslist = $this->popArr['deslist'];
	for ($c = 0; $deslist && count($deslist) > $c; $c++)
		if (isset ($this->postArr['cmbDesig'])) {
			if ($this->postArr['cmbDesig'] == $deslist[$c][0])
				echo "<option selected value='" . $deslist[$c][0] . "'>" . $deslist[$c][1] . "</option>";
			else
				echo "<option value='" . $deslist[$c][0] . "'>" . $deslist[$c][1] . "</option>";
		} else {
			if (isset ($criteriaData['JOBTITLE']) && $criteriaData['JOBTITLE'][0] == $deslist[$c][0])
				echo "<option selected value='" . $deslist[$c][0] . "'>" . $deslist[$c][1] . "</option>";
			else
				echo "<option value='" . $deslist[$c][0] . "'>" . $deslist[$c][1] . "</option>";
		}
	?>
	</select>
	<br class="clear"/>

	<input <?php echo isset($_POST['txtRepName']) ? '' : 'disabled'?> type='checkbox' <?php echo in_array('LANGUAGE',$editCriteriaChk) ? 'checked' : ''?>
	    type='checkbox' class='formCheckbox' name='chkcriteria[]' id='Language' value="LANGUAGE" onclick="chkboxCriteriaEnable()"
	    <?php echo  (isset($this->postArr['chkcriteria']) && in_array('LANGUAGE', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> >
	<label for="Language"><?php echo $lang_rep_Language; ?></label>
	<select  name="cmbLanguage"  <?php echo (isset($this->postArr['chkcriteria']) && in_array('LANGUAGE', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?> class="formSelect" >
	    <option value="0">---<?php echo $lang_rep_SelectLanguage; ?>---</option>
							<?php

	$deslist = $this->popArr['languageList'];
	for ($c = 0; $deslist && count($deslist) > $c; $c++)
		if (isset ($this->postArr['cmbLanguage'])) {
			if ($this->postArr['cmbLanguage'] == $deslist[$c][0])
				echo "<option selected value='" . $deslist[$c][0] . "'>" . $deslist[$c][1] . "</option>";
			else
				echo "<option value='" . $deslist[$c][0] . "'>" . $deslist[$c][1] . "</option>";
		} else {
			if (isset ($criteriaData['LANGUAGE']) && $criteriaData['LANGUAGE'][0] == $deslist[$c][0])
				echo "<option selected value='" . $deslist[$c][0] . "'>" . $deslist[$c][1] . "</option>";
			else
				echo "<option value='" . $deslist[$c][0] . "'>" . $deslist[$c][1] . "</option>";
		}
	?>
	</select>
	<br class="clear"/>

	<input <?php echo isset($_POST['txtRepName']) ? '' : 'disabled'?> type='checkbox' <?php echo in_array('SKILL',$editCriteriaChk) ? 'checked' : ''?>
	    type='checkbox' class='formCheckbox' name='chkcriteria[]' id='Skill' value="SKILL" onclick="chkboxCriteriaEnable()"
	    <?php echo  (isset($this->postArr['chkcriteria']) && in_array('SKILL', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> ></td>
	<label for="Skill"><?php echo $lang_rep_Skill; ?></label>
	<select  name="cmbSkill"  <?php echo (isset($this->postArr['chkcriteria']) && in_array('LANGUAGE', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?> class="formSelect" >
	    <option value="0">---<?php echo $lang_rep_SelectSkill; ?>---</option>
	<?php

	$deslist = $this->popArr['skillList'];
	for ($c = 0; $deslist && count($deslist) > $c; $c++)
		if (isset ($this->postArr['cmbSkill'])) {
			if ($this->postArr['cmbSkill'] == $deslist[$c][0])
				echo "<option selected value='" . $deslist[$c][0] . "'>" . $deslist[$c][1] . "</option>";
			else
				echo "<option value='" . $deslist[$c][0] . "'>" . $deslist[$c][1] . "</option>";
		} else {
			if (isset ($criteriaData['SKILL']) && $criteriaData['SKILL'][0] == $deslist[$c][0])
				echo "<option selected value='" . $deslist[$c][0] . "'>" . $deslist[$c][1] . "</option>";
			else
				echo "<option value='" . $deslist[$c][0] . "'>" . $deslist[$c][1] . "</option>";
		}
	?>
	</select>
</div>
<br class="clear"/>

<div class="formbuttons">
    <input type="button" class="editbutton" id="editBtn"
        onclick="edit();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
        value="<?php echo $lang_Common_Edit;?>" title="<?php echo $lang_Common_Edit;?>"/>
<input type="button" class="plainbtn" value="<?php echo $lang_Common_Reset; ?>"
		onmouseover="moverButton(this)" onmouseout="moutButton(this)"
		onclick="resetForm()"/>
</div>

<div class="subHeading"><h3><?php echo $lang_rep_Field; ?></h3></div>

<?php $fieldArr= $this->popArr['fieldList'];

    $addFieldList = array('EMPNO'=>$lang_rep_EmpNo,
        'EMPFIRSTNAME'=> $lang_rep_FirstName,
        'EMPLASTNAME' => $lang_rep_LastName,
        'ADDRESS1' => $lang_rep_Address,
        'TELENO' => $lang_rep_TelNo,
        'AGE' => $lang_rep_DateOfBirth,
        'REPORTTO' => $lang_rep_ReportTo,
        'REPORTINGMETHOD' => $lang_rep_ReportingMethod,
        'JOBTITLE' => $lang_rep_JobTitle,
        'SERPIR' => $lang_rep_JoinedDate,
        'SUBDIVISION' => $lang_rep_SubDivision,
        'QUL' => $lang_rep_Qualification,
        'YEAROFPASSING' => $lang_rep_YearOfPassing,
        'EMPSTATUS' => $lang_rep_EmploymentStatus,
        'PAYGRD' => $lang_rep_PayGrade,
        'LANGUAGES' => $lang_rep_Languages,
        'SKILLS' =>  $lang_rep_Skills,
        'CONTRACT' => $lang_rep_Contract,
        'WORKEXPERIENCE' => $lang_rep_WorkExperience);

    foreach ($addFieldList as $field=>$label) {
        $fieldId = 'FIELD_' . $field;
        $checked = in_array($field, $fieldArr) ? 'checked="checked"': '';
?>
    <label for='<?php echo $fieldId;?>'><?php echo $label;?></label>
    <input type='checkbox' <?php echo $checked;?> disabled="disabled" class='formCheckbox' name='checkfield[]'
        value='<?php echo $field;?>' id='<?php echo $fieldId;?>'/>
    <br class="clear"/>
<?php

    }
?>

</form>
&nbsp;
</div>
</div>
<div id="cal1Container" style="position:absolute;" ></div>
<script type="text/javascript">
//<![CDATA[
    if (document.getElementById && document.createElement) {
        roundBorder('outerbox');
    }
//]]>
</script>
</body>
<?php } ?>
</html>
