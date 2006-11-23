<?php
/*
OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures 
all the essential functionalities required for any enterprise. 
Copyright (C) 2006 hSenid Software International Pvt. Ltd, http://www.hsenid.com

OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
the GNU General Public License as published by the Free Software Foundation; either
version 2 of the License, or (at your option) any later version.

OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program;
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
Boston, MA  02110-1301, USA
*/
//xajax headers
require_once ROOT_PATH . '/lib/confs/sysConf.php';
require_once ROOT_PATH . '/lib/controllers/EmpViewController.php';

	$sysConst = new sysConf(); 
	$locRights=$_SESSION['localRights'];
	
	$arrMStat = $this->popArr['arrMStat'];

function populateStates($value) {
	
	$view_controller = new ViewController();
	$provlist = $view_controller->xajaxObjCall($value,'LOC','province');
	
	$objResponse = new xajaxResponse();
	$xajaxFiller = new xajaxElementFiller();
	if ($provlist) {
		$objResponse->addAssign('lrState','innerHTML','<select name="txtState" id="txtState"><option value="0">--- Select ---</option></select>');
		$objResponse = $xajaxFiller->cmbFillerById($objResponse,$provlist,1,'frmGenInfo.lrState','txtState');
		
	} else {
		$objResponse->addAssign('lrState','innerHTML','<input type="text" name="txtState" id="txtState" value="">');
	}
	$objResponse->addAssign('status','innerHTML','');
	
return $objResponse->getXML();
}


function populateDistrict($value) {
	
	$emp_view_controller = new EmpViewController();
	$dislist = $emp_view_controller->xajaxObjCall($value,'EMP','district');
		
	$objResponse = new xajaxResponse();
	$xajaxFiller = new xajaxElementFiller();
	$response = $xajaxFiller->cmbFiller($objResponse,$dislist,1,'frmEmp','cmbCity');
	$response->addAssign('status','innerHTML','');
	
return $response->getXML();
}

function assEmpStat($value) {

	$view_controller = new ViewController();
	$empstatlist = $view_controller->xajaxObjCall($value,'JOB','assigned');
		
	$objResponse = new xajaxResponse();
	$xajaxFiller = new xajaxElementFiller();
	$response = $xajaxFiller->cmbFiller($objResponse,$empstatlist,0,'frmEmp','cmbType',3);
	$response->addAssign('status','innerHTML','');
		
return $response->getXML();
}

function getUnAssMemberships($mtype) {
	
	$emp_view_controller = new EmpViewController();
	
	$value[0] = $_GET['id'];
	$value[1] = $mtype;
	
	$unAssMembership = $emp_view_controller->xajaxObjCall($value,'MEM','unAssMembership');
	
	$response = new xajaxResponse();
	$xajaxFiller = new xajaxElementFiller();
	$response = $xajaxFiller->cmbFiller($response,$unAssMembership,0,'frmEmp','cmbMemCode',3);
	$response->addAssign('status','innerHTML','');
	
return $response->getXML();
}

function getMinMaxCurrency($value, $salGrd) {

	$emp_view_controller = new EmpViewController();
	
	$temp[0] = $salGrd;
	$temp[1] = $_GET['id'];
	
	$currlist = $emp_view_controller->xajaxObjCall($temp,'BAS','currency');
		
	for($c=0; $c < count($currlist);$c++)
		if($currlist[$c][2] == $value) 
			break;
			
	$response = new xajaxResponse();
	
	if ($value === '0') {
		$response->addAssign('txtMinCurrency','value', '');
		$response->addAssign('divMinCurrency','innerHTML', '-N/A-');
		$response->addAssign('txtMaxCurrency','value', '');
		$response->addAssign('divMaxCurrency','innerHTML', '-N/A-');
	
	} else {
		$response->addAssign('txtMinCurrency','value',$currlist[$c][3]);
		$response->addAssign('divMinCurrency','innerHTML',$currlist[$c][3]);
		$response->addAssign('txtMaxCurrency','value',$currlist[$c][5]);
		$response->addAssign('divMaxCurrency','innerHTML',$currlist[$c][5]);
	}
return $response->getXML();
}

$objAjax = new xajax();
$objAjax->registerFunction('populateStates');
$objAjax->registerFunction('populateDistrict');
$objAjax->registerFunction('assEmpStat');
$objAjax->registerFunction('getUnAssMemberships');
$objAjax->registerFunction('getMinMaxCurrency');
//$objAjax->registerFunction('viewPassport');
//$objAjax->registerFunction('editPassport');
//$objAjax->registerFunction('addPassport');
$objAjax->processRequests();
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php  $objAjax->printJavascript(); 
	require_once ROOT_PATH . '/scripts/archive.js'; ?>
	
<script language="JavaScript">
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}

MM_reloadPage(true);

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_showHideLayers() { //v6.0
  var z,i,p,v,obj,args=MM_showHideLayers.arguments;
  for (i=0; i<(args.length-2); i+=3) if ((obj=MM_findObj(args[i]))!=null) { v=args[i+2];
    if (obj.style) { obj=obj.style; z=(v=='show')?3:(v=='hide')?2:2; v=(v=='show')?'visible':(v=='hide')?'hidden':v; }
    obj.visibility=v; obj.zIndex=z; }
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function alpha(txt)
{
	var flag=true;
    var i,code;

    if(txt.value=='')
    	return false;

	for(i=0;txt.value.length>i;i++)
	{
		code=txt.value.charCodeAt(i);
    
		if (code>=48 && code<=57) {
			flag=false;
			break;
		} else {
	       flag=true;
		}
	   
	}
	
  return flag;
}

function numeric(txt) {
var flag=true;
var i,code;

if(txt.value=="")
   return false;

for(i=0;txt.value.length>i;i++)	{
	code=txt.value.charCodeAt(i);
    if(code>=48 && code<=57)
	   flag=true;
	else
	   {
	   flag=false;
	   break;
	   }
	}
return flag;
}

function addEmpMain() {

	var cnt = document.frmEmp.txtEmpLastName;
	if(!(cnt.value == '') && !alpha(cnt) && !confirm('Last Name contains numbers. Do you want to continue?')) {		
		cnt.focus();
		return;
	}  if (cnt.value == '') {
		alert('Last Name Empty!');
		cnt.focus();
		return;
	}

	var cnt = document.frmEmp.txtEmpFirstName;
	if(!(cnt.value == '') && !alpha(cnt) && !confirm('First Name contains numbers. Do you want to continue?')) {		
		cnt.focus();
		return;
	} else if (cnt.value == '') {
		alert('First Name Empty!');
		cnt.focus();
		return;
	}
	
	var cnt = document.frmEmp.txtEmpMiddleName;
	if(!(cnt.value == '') && !alpha(cnt) && !confirm('Middle Name contains numbers. Do you want to continue?')) {		
		cnt.focus();
		return;
	} else if ((cnt.value == '') && !confirm('Middle Name Empty. Do you want to continue?')) {
		cnt.focus();
		return;
	}

	document.frmEmp.sqlState.value = "NewRecord";
	document.frmEmp.submit();		
}

	function goBack() {

		location.href ="./CentralController.php?reqcode=<?php echo $this->getArr['reqcode']?>&VIEW=MAIN";
	}

function mout() {
	var Edit = document.getElementById("btnEdit");
	if(document.frmEmp.EditMode.value=='1') 
		Edit.src='../../themes/beyondT/pictures/btn_save.jpg'; 
	else
		Edit.src='../../themes/beyondT/pictures/btn_edit.jpg'; 
}

function mover() {
	var Edit = document.getElementById("btnEdit");
	if(document.frmEmp.EditMode.value=='1') 
		Edit.src='../../themes/beyondT/pictures/btn_save_02.jpg'; 
	else
		Edit.src='../../themes/beyondT/pictures/btn_edit_02.jpg'; 
}
	
function editEmpMain() {
	
	var lockedEl = Array(100);
	var lockEmpCont = false;

	var Edit = document.getElementById("btnEdit");

	if(document.frmEmp.EditMode.value=='1') {
		updateEmpMain();
		return;
	}
	
	var frm=document.frmEmp;
	
	for (var i=0; i < frm.elements.length; i++) {				
		<?php if (isset($_SESSION['isAdmin']) && ($_SESSION['isAdmin'] == 'Yes')) { ?>
		
		frm.elements[i].disabled=false;
		
		<?php } else if (isset($_GET['reqcode']) && ($_GET['reqcode'] === "ESS")) { ?>
		enableArr = new Array(	'txtEmpFirstName',
								'txtEmpMiddleName',
								'txtEmpLastName',
								'txtEmpNickName',
								'cmbCountry', 
								'txtEConName', 
								"btnBrowser", 
								"chkSmokeFlag",
								"txtMilitarySer",
								"cmbNation",
								"cmbMarital",
								"cmbEthnicRace",
								"btnLicExpDate",
								"txtLicExpDate",
								"btnDOB",
								"DOB",
								"txtState",
								"cmbCity",
								"txtHmTelep",
								"txtWorkTelep",
								"txtOtherEmail",
								"txtStreet1",
								"txtStreet2",
								"txtzipCode",
								"txtMobile",
								"txtWorkEmail",
								"txtEConRel",
								"txtEConHmTel",
								"txtEConMobile",
								"txtEConWorkTel",
								"txtEConName");
								
		for (j=0; j<enableArr.length; j++) {
			frm[enableArr[j]].disabled = false;
		}
		/*
		if (frm.elements[i].name == 'txtEmpLastName')
			lockEmpCont=false;
		if (frm.elements[i].name == 'cmbCountry')
			lockEmpCont=false;
		if (frm.elements[i].name == 'txtEConName')
			lockEmpCont=false;
		if (frm.elements[i].name == 'dependentSTAT')
			lockEmpCont=true;		
		
		frm.elements[i].disabled=lockEmpCont;	
			
		if (frm.elements[i].name == 'txtEmpNickName')
			lockEmpCont=true;
		if (frm.elements[i].name == 'txtOtherEmail')
			lockEmpCont=true;		*/
			
		if (frm.elements[i].type == "hidden")
			frm.elements[i].disabled=false;	
			
			
		/*	
		if (frm.elements[i].name == "btnBrowser")
			frm.elements[i].disabled=false;	
			
		if (frm.elements[i].name == "chkSmokeFlag")
			frm.elements[i].disabled=false;
			
		if (frm.elements[i].name == "txtMilitarySer")
			frm.elements[i].disabled=false;
			
		if (frm.elements[i].name == "cmbNation")
			frm.elements[i].disabled=false;
		
		if (frm.elements[i].name == "cmbMarital")
			frm.elements[i].disabled=false;
			
		if (frm.elements[i].name == "cmbEthnicRace")
			frm.elements[i].disabled=false;
		
		if ((frm.elements[i].name == "btnLicExpDate") || (frm.elements[i].name == "txtLicExpDate"))
			frm.elements[i].disabled=false;
			
		if ((frm.elements[i].name == "btnDOB") || (frm.elements[i].name == "DOB"))
			frm.elements[i].disabled=false;*/		
		
		<?php } ?>		
	}
		
	document.getElementById("btnClear").disabled = false;
	Edit.src="../../themes/beyondT/pictures/btn_save.jpg";
	Edit.title="Save";
	document.frmEmp.EditMode.value='1';
}
	
function updateEmpMain() {
	//alert('hi');
	var cnt = document.frmEmp.txtEmpLastName;
	if(!(cnt.value == '') && !alpha(cnt) && !confirm('Last Name contains numbers. Do you want to continue?')) {		
		cnt.focus();
		return;
	}  if (cnt.value == '') {
		alert('Last Name Empty!');
		cnt.focus();
		return;
	}

	var cnt = document.frmEmp.txtEmpFirstName;
	if(!(cnt.value == '') && !alpha(cnt) && !confirm('First Name contains numbers. Do you want to continue?')) {		
		cnt.focus();
		return;
	} else if (cnt.value == '') {
		alert('First Name Empty!');
		cnt.focus();
		return;
	}
	
	var cnt = document.frmEmp.txtEmpMiddleName;
	
	if((document.frmEmp.main.value == 1) && !(cnt.value == '') && !alpha(cnt) && !confirm('Middle Name contains numbers. Do you want to continue?')) {		
		cnt.focus();
		return;
	} else if ((document.frmEmp.main.value == 1) && (cnt.value == '') && !confirm('Middle Name Empty. Do you want to continue?')) {
		cnt.focus();
		return;
	}
	
	document.getElementById("cmbProvince").value=document.getElementById("txtState").value;
	document.frmEmp.sqlState.value = "UpdateRecord";
	document.frmEmp.submit();		
}	

function hideLoad() {
	document.getElementById("status").innerHTML = '';		
}

<?php if ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'updatemode')) { 	?>
		function reLoad() {
			location.href ="<?php echo $_SERVER['PHP_SELF']?>?id=<?php echo $this->getArr['id']?>&capturemode=updatemode&reqcode=<?php echo $this->getArr['reqcode']?>";
		}
<?php } ?>

 function qCombo(lblPane) {

	document.frmEmp.pane.value=lblPane;
	document.frmEmp.submit();
}

function chgPane(lblPane) {

	document.frmEmp.pane.value=lblPane;
}

function qshowpane() {
	
	var opt=eval(document.frmEmp.pane.value);
	displayLayer(opt);
}

function displayLayer(panelNo) {
	
	switch(panelNo) {
          	case 1 : MM_showHideLayers('hidebg','','hide','personal','','show','job','','hide','dependents','','hide','contacts','','hide','emgcontacts','','hide','attachments','','hide','cash-benefits','','hide','noncash-benefits','','hide','education','','hide','immigration','','hide','languages','','hide','licenses','','hide','memberships','','hide','payments','','hide','report-to','','hide','skills','','hide','work-experiance','','hide'); break; //personal
          	case 2 : MM_showHideLayers('hidebg','','hide','personal','','hide','job','','show','dependents','','hide','contacts','','hide','emgcontacts','','hide','attachments','','hide','cash-benefits','','hide','noncash-benefits','','hide','education','','hide','immigration','','hide','languages','','hide','licenses','','hide','memberships','','hide','payments','','hide','report-to','','hide','skills','','hide','work-experiance','','hide'); break; //job
          	case 3 : MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','dependents','','show','contacts','','hide','emgcontacts','','hide','attachments','','hide','cash-benefits','','hide','noncash-benefits','','hide','education','','hide','immigration','','hide','languages','','hide','licenses','','hide','memberships','','hide','payments','','hide','report-to','','hide','skills','','hide','work-experiance','','hide'); break; //dependents
          	case 4 : MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','dependents','','hide','contacts','','show','emgcontacts','','hide','attachments','','hide','cash-benefits','','hide','noncash-benefits','','hide','education','','hide','immigration','','hide','languages','','hide','licenses','','hide','memberships','','hide','payments','','hide','report-to','','hide','skills','','hide','work-experiance','','hide'); break; //contacts
          	case 5 : MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','dependents','','hide','contacts','','hide','emgcontacts','','show','attachments','','hide','cash-benefits','','hide','noncash-benefits','','hide','education','','hide','immigration','','hide','languages','','hide','licenses','','hide','memberships','','hide','payments','','hide','report-to','','hide','skills','','hide','work-experiance','','hide'); break; //emg-contacts
          	case 6 : MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','dependents','','hide','contacts','','hide','emgcontacts','','hide','attachments','','show','cash-benefits','','hide','noncash-benefits','','hide','education','','hide','immigration','','hide','languages','','hide','licenses','','hide','memberships','','hide','payments','','hide','report-to','','hide','skills','','hide','work-experiance','','hide'); break; //attachements
          	case 7 : MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','dependents','','hide','contacts','','hide','emgcontacts','','hide','attachments','','hide','cash-benefits','','show','noncash-benefits','','hide','education','','hide','immigration','','hide','languages','','hide','licenses','','hide','memberships','','hide','payments','','hide','report-to','','hide','skills','','hide','work-experiance','','hide'); break; //cash-benefits
          	case 8 : MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','dependents','','hide','contacts','','hide','emgcontacts','','hide','attachments','','hide','cash-benefits','','hide','noncash-benefits','','show','education','','hide','immigration','','hide','languages','','hide','licenses','','hide','memberships','','hide','payments','','hide','report-to','','hide','skills','','hide','work-experiance','','hide'); break; //noncash-benefits 
          	case 9 : MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','dependents','','hide','contacts','','hide','emgcontacts','','hide','attachments','','hide','cash-benefits','','hide','noncash-benefits','','hide','education','','show','immigration','','hide','languages','','hide','licenses','','hide','memberships','','hide','payments','','hide','report-to','','hide','skills','','hide','work-experiance','','hide'); break; //education
          	case 10 : MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','dependents','','hide','contacts','','hide','emgcontacts','','hide','attachments','','hide','cash-benefits','','hide','noncash-benefits','','hide','education','','hide','immigration','','show','languages','','hide','licenses','','hide','memberships','','hide','payments','','hide','report-to','','hide','skills','','hide','work-experiance','','hide'); break; //immigration
          	case 11 : MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','dependents','','hide','contacts','','hide','emgcontacts','','hide','attachments','','hide','cash-benefits','','hide','noncash-benefits','','hide','education','','hide','immigration','','hide','languages','','show','licenses','','hide','memberships','','hide','payments','','hide','report-to','','hide','skills','','hide','work-experiance','','hide'); break; //languages
          	case 12 : MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','dependents','','hide','contacts','','hide','emgcontacts','','hide','attachments','','hide','cash-benefits','','hide','noncash-benefits','','hide','education','','hide','immigration','','hide','languages','','hide','licenses','','show','memberships','','hide','payments','','hide','report-to','','hide','skills','','hide','work-experiance','','hide'); break; //licenses
          	case 13 : MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','dependents','','hide','contacts','','hide','emgcontacts','','hide','attachments','','hide','cash-benefits','','hide','noncash-benefits','','hide','education','','hide','immigration','','hide','languages','','hide','licenses','','hide','memberships','','show','payments','','hide','report-to','','hide','skills','','hide','work-experiance','','hide'); break; //memberships
          	case 14 : MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','dependents','','hide','contacts','','hide','emgcontacts','','hide','attachments','','hide','cash-benefits','','hide','noncash-benefits','','hide','education','','hide','immigration','','hide','languages','','hide','licenses','','hide','memberships','','hide','payments','','show','report-to','','hide','skills','','hide','work-experiance','','hide'); break; //payments
          	case 15 : MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','dependents','','hide','contacts','','hide','emgcontacts','','hide','attachments','','hide','cash-benefits','','hide','noncash-benefits','','hide','education','','hide','immigration','','hide','languages','','hide','licenses','','hide','memberships','','hide','payments','','hide','report-to','','show','skills','','hide','work-experiance','','hide'); break; //report-to
          	case 16 : MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','dependents','','hide','contacts','','hide','emgcontacts','','hide','attachments','','hide','cash-benefits','','hide','noncash-benefits','','hide','education','','hide','immigration','','hide','languages','','hide','licenses','','hide','memberships','','hide','payments','','hide','report-to','','hide','skills','','show','work-experiance','','hide'); break; //skills
          	case 17 : MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','dependents','','hide','contacts','','hide','emgcontacts','','hide','attachments','','hide','cash-benefits','','hide','noncash-benefits','','hide','education','','hide','immigration','','hide','languages','','hide','licenses','','hide','memberships','','hide','payments','','hide','report-to','','hide','skills','','hide','work-experiance','','show'); break; //work-experiance
	}
}

function setUpdate(opt) {
		//alert(opt);
		switch(eval(opt)) {
          	case 0 : document.frmEmp.main.value=1; break;
          	case 1 : document.frmEmp.personalFlag.value=1; break;
          	case 2 : document.frmEmp.jobFlag.value=1; break;
            case 4 : document.frmEmp.contactFlag.value=1; break;		
		}
		document.frmEmp.pane.value = opt;			
}


function popPhotoHandler() {
	var popup=window.open('../../templates/hrfunct/photohandler.php?id=<?php echo isset($this->getArr['id']) ? $this->getArr['id'] : ''?>','Photo','height=250,width=250');
	if(!popup.opener) popup.opener=self;
	popup.focus()
}

function resetAdd(panel) {
	document.frmEmp.action = document.frmEmp.action;
	document.frmEmp.pane.value = panel;
	document.frmEmp.submit();
}
</script>

<link href="../../themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("../../themes/beyondT/css/style.css"); </style>
<style type="text/css">@import url("./hrEmpMain.css"); </style>
<style type="text/css">
<!--
.mnuPIM {
/*Remove all spacings from the list items*/
	margin: 0;
	padding: 0;
	cursor: default;
	list-style-type: none;
	display: inline;
}


.mnuPIM td{
	display: table-cell;
	position: relative;
	padding: 2px 6px;
	background-position: center top;
	background-repeat: no-repeat;
}


/*** Menu colors (customizable) ***/

.mnuPIM a{
	text-decoration: none;
	display: block;	
	vertical-align: bottom;
	padding-top: 40px;
	/*height:50px;*/
	width:52px;	
	text-align:center;
}

.mnuPIM td{	
	height: 50px;
	width: 52px;
	vertical-align: top;
	text-align:center;	
}

#jobLink {
	background-image: url(../../themes/beyondT/icons/job.jpg);	
}

#personalLink {
	background-image: url(../../themes/beyondT/icons/personal.jpg);
}

#dependantsLink {
	background-image: url(../../themes/beyondT/icons/dependants.jpg);
}

#contactLink {
	background-image: url(../../themes/beyondT/icons/contact.jpg);
}

#emergency_contactLink {
	background-image: url(../../themes/beyondT/icons/emergency_contact.jpg);
}

#attachmentsLink {
	background-image: url(../../themes/beyondT/icons/attachments.jpg);
}

#cash_benefitsLink {
	background-image: url(../../themes/beyondT/icons/cash_benefits.jpg);
}

#educationLink {
	background-image: url(../../themes/beyondT/icons/education.jpg);
}

#immigrationLink {
	background-image: url(../../themes/beyondT/icons/immigration.jpg);
}

#languagesLink {
	background-image: url(../../themes/beyondT/icons/languages.jpg);
}

#licenseLink {
	background-image: url(../../themes/beyondT/icons/license.jpg);
}

#membershipLink {
	background-image: url(../../themes/beyondT/icons/membership.jpg);
}

#non_cash_benefitsLink {
	background-image: url(../../themes/beyondT/icons/non_cash_benefits.jpg);
}

#paymentLink {
	background-image: url(../../themes/beyondT/icons/payment.jpg);
}

#report-toLink {
	background-image: url(../../themes/beyondT/icons/report-to.jpg);
}

#skillsLink {
	background-image: url(../../themes/beyondT/icons/skills.jpg);
}

#work_experienceLink {
	background-image: url(../../themes/beyondT/icons/work_experience.jpg);
}
-->
</style>

<body onLoad="hideLoad();">
<?php
 if (!isset($this->getArr['pane'])) {
 	$this->getArr['pane'] = 1;
 };
 if (!isset($this->postArr['pane'])) {
 	$this->postArr['pane'] = $this->getArr['pane'];
 };
 ?>
<table width='100%' cellpadding='0' cellspacing='0' border='0'>
  <tr>
    <td valign='top'>&nbsp; </td>
    <td width='100%'><h2 align="center"><?php echo $employeeinformation?></h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'>
    <b><div align="right" id="status"><img src="../../themes/beyondT/icons/loading.gif" width="20" height="20" style="vertical-align:bottom;"/> <span style="vertical-align:text-top">Loading Page...</span></div></b></td>
  </tr>
</table>

<?php	if ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'addmode')) { ?>
<form name="frmEmp" id="frmEmp" method="post" action="<?php echo $_SERVER['PHP_SELF']?>?reqcode=<?php echo $this->getArr['reqcode']?>&capturemode=<?php echo $this->getArr['capturemode']?>" enctype="multipart/form-data">
<?php
	} elseif ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'updatemode')) {
	$edit = $this->popArr['editMainArr'];
?>
<form name="frmEmp" id="frmEmp" method="post" action="<?php echo $_SERVER['PHP_SELF']?>?id=<?php echo $this->getArr['id']?>&reqcode=<?php echo $this->getArr['reqcode']?>&capturemode=<?php echo $this->getArr['capturemode']?>" enctype="multipart/form-data">
<?php } ?>

<input type="hidden" name="sqlState">
<input type="hidden" name="pane" value="<?php echo (isset($this->postArr['pane']) && $this->postArr['pane']!='')?$this->postArr['pane']:''?>">

<input type="hidden" name="main" value="<?php echo isset($this->postArr['main'])? $this->postArr['main'] : '0'?>">
<input type="hidden" name="personalFlag" value="<?php echo isset($this->postArr['personalFlag'])? $this->postArr['personalFlag'] : '0'?>">
<input type="hidden" name="jobFlag" value="<?php echo isset($this->postArr['jobFlag'])? $this->postArr['jobFlag'] : '0'?>">

<input type="hidden" name="dependentFlag" value="<?php echo isset($this->postArr['dependentFlag'])? $this->postArr['dependentFlag'] : '0'?>">
<input type="hidden" name="childrenFlag" value="<?php echo isset($this->postArr['childrenFlag'])? $this->postArr['childrenFlag'] : '0'?>">
<input type="hidden" name="contactFlag" value="<?php echo isset($this->postArr['contactFlag'])? $this->postArr['contactFlag'] : '0'?>">
<input type="hidden" name="econtactFlag" value="<?php echo isset($this->postArr['econtactFlag'])? $this->postArr['econtactFlag'] : '0'?>">
<input type="hidden" name="cash-benefitsFlag" value="<?php echo isset($this->postArr['cash-benefitsFlag'])? $this->postArr['cash-benefitsFlag'] : '0'?>">
<input type="hidden" name="noncash-benefitsFlag" value="<?php echo isset($this->postArr['noncash-benefitsFlag'])? $this->postArr['noncash-benefitsFlag'] : '0'?>">
<input type="hidden" name="educationFlag" value="<?php echo isset($this->postArr['educationFlag'])? $this->postArr['educationFlag'] : '0'?>">
<input type="hidden" name="immigrationFlag" value="<?php echo isset($this->postArr['immigrationFlag'])? $this->postArr['immigrationFlag'] : '0'?>">
<input type="hidden" name="languageFlag" value="<?php echo isset($this->postArr['languageFlag'])? $this->postArr['languageFlag'] : '0'?>">
<input type="hidden" name="licenseFlag" value="<?php echo isset($this->postArr['licenseFlag'])? $this->postArr['licenseFlag'] : '0'?>">
<input type="hidden" name="membershipFlag" value="<?php echo isset($this->postArr['membershipFlag'])? $this->postArr['membershipFlag'] : '0'?>">
<input type="hidden" name="paymentFlag" value="<?php echo isset($this->postArr['paymentFlag'])? $this->postArr['paymentFlag'] : '0'?>">
<input type="hidden" name="report-toFlag" value="<?php echo isset($this->postArr['report-toFlag'])? $this->postArr['report-toFlag'] : '0'?>">
<input type="hidden" name="skillsFlag" value="<?php echo isset($this->postArr['skillsFlag'])? $this->postArr['skillsFlag'] : '0'?>">
<input type="hidden" name="work-experianceFlag" value="<?php echo isset($this->postArr['work-experianceFlag'])? $this->postArr['work-experianceFlag'] : '0'?>">
<input type="hidden" name="attSTAT" value="">
<input type="hidden" name="EditMode" value="<?php echo isset($this->postArr['EditMode'])? $this->postArr['EditMode'] : '0'?>">

<?php if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'addmode') { ?>

<table width="550" align="center" border="0" cellpadding="0" cellspacing="0">
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
			  
				<td><?php echo $code?></td>
				<td><input type="hidden" name="txtEmpID" value=<?php echo $this->popArr['newID']?>><strong><?php echo $this->popArr['newID']?></strong></td>
			  </tr>
			  <tr> 
				<td><font color=#ff0000>*</font> <?php echo $lastname?></td>
				<td> <input type="text" name="txtEmpLastName" <?php echo $locRights['add'] ? '':'disabled'?> value="<?php echo (isset($this->postArr['txtEmpLastName']))?$this->postArr['txtEmpLastName']:''?>"></td>
				<td>&nbsp;</td>
				<td><font color=#ff0000>*</font> <?php echo $firstname?></td>
				<td> <input type="text" name="txtEmpFirstName" <?php echo $locRights['add'] ? '':'disabled'?> value="<?php echo (isset($this->postArr['txtEmpFirstName']))?$this->postArr['txtEmpFirstName']:''?>"></td>
			  </tr>
			  <tr> 
				<td><font color=#ff0000>*</font> <?php echo $middlename?></td>
				<td> <input type="text" name="txtEmpMiddleName" <?php echo $locRights['add'] ? '':'disabled'?> value="<?php echo (isset($this->postArr['txtEmpMiddleName']))?$this->postArr['txtEmpMiddleName']:''?>"></td>
				<td>&nbsp;</td>
			  <td><?php echo $nickname?></td>
				<td> <input type="text" name="txtEmpNickName" <?php echo $locRights['add'] ? '':'disabled'?> value="<?php echo (isset($this->postArr['txtEmpNickName']))?$this->postArr['txtEmpNickName']:''?>"></td>
			  </tr>
			 <tr>
				<td><?php echo $photo?></td>
				<td><input type="file" name='photofile' <?php echo $locRights['add'] ? '':'disabled'?> value="<?php echo (isset($this->postArr['photofile']))?$this->postArr['photofile']:''?>"></td>
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
             	<p align="center">Fields marked with an asterisk <font color=#ff0000>*</font> are required.</p>
              
    <table border="0" align="center" >
                <tr>
                </tr>
    <tr>
    <td><?php if($_GET['reqcode'] !== "ESS") {?><img title="Back" onMouseOut="this.src='../../themes/beyondT/pictures/btn_back.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_back_02.jpg';"  src="../../themes/beyondT/pictures/btn_back.jpg" onClick="goBack();"><?php }?></td>
    <td>
					<?php	if (($locRights['add']) || ($_GET['reqcode'] === "ESS")) { ?>
					        <input type="image" class="button1" id="btnEdit" border="0" title="Save" onClick="addEmpMain(); return false;" onMouseOut="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
					<?php 	} else { ?>
					        <input type="image" class="button1" id="btnEdit" onClick="alert('<?php echo $sysConst->accessDenied?>'); return false;" src="../../themes/beyondT/pictures/btn_save.jpg">

					<?php	} ?>
    </td>
    <td>&nbsp;</td>
    <td><input type="image" class="button1" id="btnClear" onClick="document.frmEmp.reset(); return false;" onMouseOut="this.src='../../themes/beyondT/pictures/btn_clear.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_clear_02.jpg';" src="../../themes/beyondT/pictures/btn_clear.jpg"></td>
    </tr>
    </table>
    
<?php } elseif(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>

<table width="100%">
<tr>
<td>
			<table width="550" align="center" border="0" cellpadding="0" cellspacing="0"><tr><td><br>&nbsp;</td></tr>
                <tr>
                  <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="339" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="11"><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="12" border="0" alt=""></td>
                </tr>
                <tr>
                  <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><table onClick="setUpdate(0)" onKeyPress="setUpdate(0)" width="100%" border="0" cellpadding="5" cellspacing="0" class="">
			  <tr> 
				<td><?php echo $code?></td>
				<td><strong><input type="hidden" name="txtEmpID" value="<?php echo $this->getArr['id']?>"><?php echo $this->getArr['id']?></strong></td>
			  </tr>
			  <tr> 
				<td><font color=#ff0000>*</font> <?php echo $lastname?></td>
				<td> <input type="text" <?php echo (isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="txtEmpLastName" value="<?php echo (isset($this->postArr['txtEmpLastName']))?$this->postArr['txtEmpLastName']:$edit[0][1]?>"></td>
				<td>&nbsp;</td>
				<td><font color=#ff0000>*</font> <?php echo $firstname?></td>
				<td><input type="text" <?php echo (isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="txtEmpFirstName" value="<?php echo (isset($this->postArr['txtEmpFirstName']))?$this->postArr['txtEmpFirstName']:$edit[0][2]?>"></td>
			  </tr>
			  <tr> 
				<td><font color=#ff0000>*</font> <?php echo $middlename?></td>
				<td> <input type="text" <?php echo (isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="txtEmpMiddleName" value="<?php echo (isset($this->postArr['txtEmpMiddleName']))?$this->postArr['txtEmpMiddleName']:$edit[0][3]?>"></td>
				<td>&nbsp;</td>
			  <td><?php echo $nickname?></td>
				<td> <input type="text" <?php echo (isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="txtEmpNickName" value="<?php echo (isset($this->postArr['txtEmpNickName']))?$this->postArr['txtEmpNickName']:$edit[0][4]?>"></td>
			  </tr><tr><td><br>&nbsp;</td></tr>
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
</td>
<td>
      <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="200" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="11"><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="12" border="0" alt=""></td>
                </tr>
                <tr>
                  <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
                    <tr>
                    <td width="100%" align="center"><img width="100" height="120" src="../../templates/hrfunct/photohandler.php?id=<?php echo $this->getArr['id']?>&action=VIEW"></td>
                    </tr>
                    <tr>
                    <td width="100%" align="center"><input type="button" value="Browse" name="btnBrowser" onClick="popPhotoHandler()"></td>
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
</td>              
</tr>
</table>
    <table border="0" align="center" >
    <tr>
    <td><?php if($_GET['reqcode'] !== "ESS") {?>      <img title="Back" onMouseOut="this.src='../../themes/beyondT/pictures/btn_back.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_back_02.jpg';"  src="../../themes/beyondT/pictures/btn_back.jpg" onClick="goBack();">
      <?php }?></td>
    <td>
<?php			if (($locRights['edit']) || ($_GET['reqcode'] === "ESS")) { ?>
			        <input type="image" class="button1" id="btnEdit" src="<?php echo (isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '../../themes/beyondT/pictures/btn_save.jpg' : '../../themes/beyondT/pictures/btn_edit.jpg'?>" title="Edit" onMouseOut="mout();" onMouseOver="mover();" name="EditMain" onClick="editEmpMain(); return false;">
<?php			} else { ?>
			        <input type="image" class="button1" id="btnEdit" src="../../themes/beyondT/pictures/btn_edit.jpg" onClick="alert('<?php echo $sysConst->accessDenied?>');  return false;">
<?php			}  ?>
    </td>
    <td><input type="image" class="button1" id="btnClear" disabled src="../../themes/beyondT/pictures/btn_clear.jpg" onMouseOut="this.src='../../themes/beyondT/pictures/btn_clear.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_clear_02.jpg';" onClick="reLoad();  return false;" ></td>
    </tr>
    </table>
<br>


    
	<table border="0">
		<tr>
			<td>
			<table border="0" align="center" cellpadding="0" cellspacing="0">
				<tr class="mnuPIM">
					<td id="personalLink"><a href="javascript:displayLayer(1)">Personal</a></td>
					<td id="contactLink"><a href="javascript:displayLayer(4)">Contact</a></td>
					<td id="emergency_contactLink"><a href="javascript:displayLayer(5)">Emergency Contact(s)</a></td>
					<td id="dependantsLink"><a href="javascript:displayLayer(3)">Dependents</a></td>
					<td id="immigrationLink"><a href="javascript:displayLayer(10)">Immigration</a></td>
					
					<td id="jobLink"><a href="javascript:displayLayer(2)">Job</a></td>
					<td id="paymentLink"><a href="javascript:displayLayer(14)">Payments</a></td>
					<td id="report-toLink"><a href="javascript:displayLayer(15)">Report-to</a></td>
					
					<td id="work_experienceLink"><a href="javascript:displayLayer(17)">Work experience</a></td>
					<td id="educationLink"><a href="javascript:displayLayer(9)">Education</a></td>
					<td id="skillsLink"><a href="javascript:displayLayer(16)">Skills</a></td>
					<td id="languagesLink"><a href="javascript:displayLayer(11)">Languages</a></td>
					<td id="licenseLink"><a href="javascript:displayLayer(12)">License</a></td>
					
					<td id="membershipLink"><a href="javascript:displayLayer(13)">Membership</a></td>
					<td id="attachmentsLink"><a href="javascript:displayLayer(6)">Attachments</a></td>
					<!--<td id="cash_benefitsLink"><a href="javascript:displayLayer(7)">Cash Benefits</a></td>
					<td id="non_cash_benefitsLink"><a href="javascript:displayLayer(8)">Non cash benefits</a></td>	-->		
				</tr>
			</table>
			</td>
		</tr>
		<tr>
    		<td align="center">
    		
    <div id="personal" style="position:absolute; z-index:3; width: 540px; visibility: <?php echo (isset($this->postArr['pane']) && $this->postArr['pane'] != '1') ? 'hidden' : 'visible'?>; left: 200px; top: 360px;">
	  <table  border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td>
          
          <?php require(ROOT_PATH . "/templates/hrfunct/hremppers.php"); ?>
          
			</td><td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
      
      <table border="0" align="center" >
    <tr>
    <td>Fields marked with an asterisk <font color=#ff0000>*</font> are required.</td> 
    </tr>
    </table>	
    </div>

    <div id="job" style="position:absolute; z-index:2; width: 540px; visibility: <?php echo (isset($this->postArr['pane']) && $this->postArr['pane'] == '2') ? 'visible' : 'hidden'?>; left: 200px; top: 360px;">
	  <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td>
          
          <?php require(ROOT_PATH . "/templates/hrfunct/hrempjob.php"); require(ROOT_PATH . "/templates/hrfunct/hrempconext.php"); ?>
          
			</td><td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
    </div>
    <div id="dependents" style="position:absolute; z-index:2; width: 540px; visibility: <?php echo (isset($this->postArr['pane']) && $this->postArr['pane'] == '3') ? 'visible' : 'hidden'?>; left: 200px; top: 360px;">
    <table border="0" align="center">
     <tr><td>
	  <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td>
          
          <?php require(ROOT_PATH . "/templates/hrfunct/hrempdependent.php"); ?>
          
			</td><td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
      </td>
     <td>
	  <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td>
          
          <?php require(ROOT_PATH . "/templates/hrfunct/hrempchildren.php"); ?>
          
			</td><td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
      </td></tr>
      </table>
    </div>
    <div id="contacts" style="position:absolute; z-index:2; width: 540px; visibility: <?php echo (isset($this->postArr['pane']) && $this->postArr['pane'] == '4') ? 'visible' : 'hidden'?>; left: 200px; top: 360px;">
	  <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td>
          
          <?php require(ROOT_PATH . "/templates/hrfunct/hrempcontact.php"); ?>
          
			</td><td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
    </div>
    <div id="emgcontacts" style="position:absolute; z-index:2; width: 540px; visibility: <?php echo (isset($this->postArr['pane']) && $this->postArr['pane'] == '5') ? 'visible' : 'hidden'?>; left: 200px; top: 360px;">
	  <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td>
          
          <?php require(ROOT_PATH . "/templates/hrfunct/hrempemgcontact.php"); ?>
          
			</td><td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
    </div>
    <div id="attachments" style="position:absolute; z-index:3; width: 540px; visibility: <?php echo (isset($this->postArr['pane']) && $this->postArr['pane'] == '6') ? 'visible' : 'hidden'?>; left: 200px; top: 360px;">
	  <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td>
          
          <?php require(ROOT_PATH . "/templates/hrfunct/hrempattachment.php"); ?>
          
			</td><td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
    </div>
    <div id="cash-benefits" style="position:absolute; z-index:2; width: 540px; visibility: <?php echo (isset($this->postArr['pane']) && $this->postArr['pane'] == '7') ? 'visible' : 'hidden'?>; left: 200px; top: 360px;">
	  <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td>
 			Cash Benefits         
          <?php //require(ROOT_PATH . "/templates/hrfunct/EmpCashBenefits.php"); ?>
          
			</td><td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
    </div>
    <div id="noncash-benefits" style="position:absolute; z-index:2; width: 540px; visibility: <?php echo (isset($this->postArr['pane']) && $this->postArr['pane'] == '8') ? 'visible' : 'hidden'?>; left: 200px; top: 360px;">
	  <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td>
          Non-cash benefits
          <?php //require(ROOT_PATH . "/templates/hrfunct/EmpNonCashBenefits.php"); ?>
          
			</td><td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
    </div>
    <div id="education" style="position:absolute; z-index:2; width: 540px; visibility: <?php echo (isset($this->postArr['pane']) && $this->postArr['pane'] == '9') ? 'visible' : 'hidden'?>; left: 200px; top: 360px;">
	  <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td>
          
          <?php require(ROOT_PATH . "/templates/hrfunct/hrempeducation.php"); ?>
          
			</td><td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
    </div>
    <div id="immigration" style="position:absolute; z-index:2; width: 540px; visibility: <?php echo (isset($this->postArr['pane']) && $this->postArr['pane'] == '10') ? 'visible' : 'hidden'?>; left: 200px; top: 360px;">
	  <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td>
          
          <?php require(ROOT_PATH . "/templates/hrfunct/hrempimmigration.php"); ?>
          
			</td><td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
    </div>
    <div id="languages" style="position:absolute; z-index:2; width: 540px; visibility: <?php echo (isset($this->postArr['pane']) && $this->postArr['pane'] == '11') ? 'visible' : 'hidden'?>; left: 200px; top: 360px;">
	  <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td>
          
          <?php require(ROOT_PATH . "/templates/hrfunct/hremplanguage.php"); ?>
          
			</td><td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
    </div>
    <div id="licenses" style="position:absolute; z-index:2; width: 540px; visibility: <?php echo (isset($this->postArr['pane']) && $this->postArr['pane'] == '12') ? 'visible' : 'hidden'?>; left: 200px; top: 360px;">
	  <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td>
          
          <?php require(ROOT_PATH . "/templates/hrfunct/hremplicenses.php"); ?>
          
			</td><td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
    </div>
    <div id="memberships" style="position:absolute; z-index:2; width: 540px; visibility: <?php echo (isset($this->postArr['pane']) && $this->postArr['pane'] == '13') ? 'visible' : 'hidden'?>; left: 200px; top: 360px;">
	  <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td>
          
          <?php require(ROOT_PATH . "/templates/hrfunct/hrempmembership.php"); ?>
          
			</td><td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
    </div>
    <div id="payments" style="position:absolute; z-index:2; width: 540px; visibility: <?php echo (isset($this->postArr['pane']) && $this->postArr['pane'] == '14') ? 'visible' : 'hidden'?>; left: 200px; top: 360px;">
	  <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td>
          
          <?php require(ROOT_PATH . "/templates/hrfunct/hremppayment.php"); ?>
          
			</td><td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
    </div>
    <div id="report-to" style="position:absolute; z-index:2; width: 540px; visibility: <?php echo (isset($this->postArr['pane']) && $this->postArr['pane'] == '15') ? 'visible' : 'hidden'?>; left: 200px; top: 360px;">
	  <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td>
          
          <?php require(ROOT_PATH . "/templates/hrfunct/hrempreportto.php"); ?>
          
			</td><td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
    </div>
    <div id="skills" style="position:absolute; z-index:2; width: 540px; visibility: <?php echo (isset($this->postArr['pane']) && $this->postArr['pane'] == '16') ? 'visible' : 'hidden'?>; left: 200px; top: 360px;">
	  <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td>
          
          <?php require(ROOT_PATH . "/templates/hrfunct/hrempskill.php"); ?>
          
			</td><td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
    </div>
    <div id="work-experiance" style="position:absolute; z-index:2; width: 540px; visibility: <?php echo (isset($this->postArr['pane']) && $this->postArr['pane'] == '17') ? 'visible' : 'hidden'?>; left: 200px; top: 360px;">
	  <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td>
          
          <?php require(ROOT_PATH . "/templates/hrfunct/hrempwrkexp.php"); ?>
          
			</td><td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
    </div>
			
			</td>
		</tr>
	<table>  

<?php } ?>		
	
		</form>
		<iframe width=174 height=189 name="gToday:normal:agenda.js" id="gToday:normal:agenda.js" src="../../scripts/ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; top:-500px; left:-500px;"></iframe>
	</body>
</html>
