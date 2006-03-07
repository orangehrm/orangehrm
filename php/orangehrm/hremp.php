<?
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




session_start();
if(!isset($_SESSION['fname'])) { 

	header("Location: ./relogin.htm");
	exit();
}

define('OpenSourceEIM', dirname(__FILE__));
require_once OpenSourceEIM . '/lib/Models/hrfunct/EmpInfo.php';
require_once OpenSourceEIM . '/lib/Models/hrfunct/EmpPassPort.php';
require_once OpenSourceEIM . '/lib/Models/hrfunct/EmpBank.php';
require_once OpenSourceEIM . '/lib/Models/hrfunct/EmpTax.php';
require_once OpenSourceEIM . '/lib/Models/hrfunct/EmpAttach.php';
require_once OpenSourceEIM . '/lib/Confs/sysConf.php';
require_once OpenSourceEIM . '/lib/Exceptionhandling/ExceptionHandler.php';

	$sysConst = new sysConf(); 
	$locRights=$_SESSION['localRights'];

	$parent_empinfo = new EmpInfo();
	$tax = new EmpTax();
	$pport = new EmpPassPort();
	$bankacc = new EmpBank();
	$attachment = new EmpAttach();
	
	$lastRecord = $parent_empinfo ->getLastRecord();
	$arrTitle = array('Mr.','Mrs.','Ms.','Rev');
	$arrBGroup = array ( 'A  +','A  -','B  +','B  -','AB +','AB -','O  +','O  -' );
	$arrMStat = array ('Unmarried','Married','Divorced','Others');
	$arrEmpType = array( 'Permanent', 'Expatriate', 'Contract', 'Temporary' , 'Others');
	$arrTaxExempt = array ('Monthly Paye', 'Annual Payee', 'Payee Exempt', 'Expatriate');
	$arrFillStat = array('Married','Unmarried');
	
	if ((isset($_POST['sqlState'])) && ($_POST['sqlState'] == 'NewRecord')) {
	
		$parent_empinfo -> setEmpId(trim($_POST['txtEmpID']));
		$parent_empinfo -> setEmpTitle(($_POST['cmbEmpTitle']));
		$parent_empinfo -> setEmpCallName(trim($_POST['txtEmpCallName']));
		$parent_empinfo -> setEmpSurname(trim($_POST['txtEmpSurname']));
		$parent_empinfo -> setEmpMaidenName(trim($_POST['txtEmpMaidenName']));
		$parent_empinfo -> setEmpInitials(trim($_POST['txtEmpInitials']));
		$parent_empinfo -> setEmpNamByIni(trim($_POST['txtEmpNamByIni']));
		$parent_empinfo -> setEmpFullName(trim($_POST['txtEmpFullName']));
		$parent_empinfo -> setEmpOtherName(trim($_POST['txtEmpOtherName']));
		//personal
		$parent_empinfo -> setEmpNICNo(trim($_POST['txtNICNo']));
		$parent_empinfo -> setEmpNICDate(trim($_POST['txtNICDate']));
		$parent_empinfo -> setEmpDOB(trim($_POST['DOB']));
		$parent_empinfo -> setEmpBirthPlace(trim($_POST['txtBirthPlace']));
		$parent_empinfo -> setEmpGender(trim($_POST['optGender']));
		$parent_empinfo -> setEmpBloodGrp(($_POST['cmbBloodGrp']));
		$parent_empinfo -> setEmpNation(($_POST['cmbNation']));
		$parent_empinfo -> setEmpReligion(($_POST['cmbReligion']));
		$parent_empinfo -> setEmpMarital(($_POST['cmbMarital']));
		$parent_empinfo -> setEmpMarriedDate(trim($_POST['txtMarriedDate']));
		//job info
		$parent_empinfo -> setEmpDatJoin(trim($_POST['txtDatJoin']));
		$parent_empinfo -> setEmpConfirmFlag(($_POST['chkConfirmFlag']!=NULL)?'1':'0');
		$parent_empinfo -> setEmpResigDat(trim($_POST['txtResigDat']));
		$parent_empinfo -> setEmpRetireDat(trim($_POST['txtRetireDat']));
		$parent_empinfo -> setEmpSalGrd(($_POST['cmbSalGrd']));
		$parent_empinfo -> setEmpCorpTit(($_POST['cmbCorpTit']));
		$parent_empinfo -> setEmpDesig(($_POST['cmbDesig']));
		$parent_empinfo -> setEmpCostCode(($_POST['cmbCostCode']));
		$parent_empinfo -> setEmpWorkHours(trim($_POST['txtWorkHours']));
		$parent_empinfo -> setEmpJobPref(trim($_POST['txtJobPref']));
		//job stat
		$parent_empinfo -> setEmpType(($_POST['cmbType']));
		$parent_empinfo -> setEmpStatutory(($_POST['cmbStatutory']));
		$parent_empinfo -> setEmpCat(($_POST['cmbCat']));
		$parent_empinfo -> setEmpStartDat(trim($_POST['txtStartDat']));
		$parent_empinfo -> setEmpEndDat(trim($_POST['txtEndDat']));
		$parent_empinfo -> setEmpConToPermFlag(($_POST['chkConToPermFlag']!=null)?'1':'0');
		$parent_empinfo -> setEmpConToPermDat(trim($_POST['txtConToPermDat']));
		$parent_empinfo -> setEmpHRActivFlag(($_POST['chkHRActivFlag']!=null)?'1':'0');
		$parent_empinfo -> setEmpPayActivFlag(trim($_POST['txtPayActivFlag']!=null)?'1':'0');
		$parent_empinfo -> setEmpTimAttActivFlag(trim($_POST['chkTimeAttActivFlag']!=null)?'1':'0');
		//workstation	   
		$parent_empinfo -> setEmpLoc($_POST['cmbLocation']);
		$parent_empinfo -> setEmpPrefLoc($_POST['txtHiCode']);
		$parent_empinfo -> setEmpTaxCountry($_POST['cmbTaxCountry']);
		//tax
		if($_POST['skipTax']==0) {
		$parent_empinfo -> setEmpTaxExempt(($_POST['cmbTaxExempt']));
		$parent_empinfo -> setEmpTaxOnTaxFlag(($_POST['chkTaxOnTaxFlag']!=null)?'1':'0');
		$parent_empinfo -> setEmpTaxID(trim($_POST['txtTaxID']));
		$parent_empinfo -> setEmpEPFEligibleFlag(($_POST['chkEPFEligibleFlag']!=null)?'1':'0');
		$parent_empinfo -> setEmpEPFNo(trim($_POST['txtEPFNo']));
		$parent_empinfo -> setCFundCBFundFlag(trim($_POST['optCFundCBFundFlag']));
		$parent_empinfo -> setEPFEmployeePercen(trim($_POST['txtEPFEmployeePercen']));
		$parent_empinfo -> setEPFEmployerPercen(trim($_POST['txtEPFEmployerPercen']));
		$parent_empinfo -> setETFEligibleFlag(($_POST['chkETFEligibleFlag']!=null)?'1':'0');
		$parent_empinfo -> setEmpETFNo(trim($_POST['txtETFNo']));
		$parent_empinfo -> setETFEmployeePercen(trim($_POST['txtETFEmployeePercen']));
		$parent_empinfo -> setETFDat(trim($_POST['txtETFDat']));
		$parent_empinfo -> setMSPSEligibleFlag(($_POST['chkMSPSEligibleFlag']!=null)?'1':'0');
		$parent_empinfo -> setMSPSEmployeePercen(trim($_POST['txtMSPSEmployeePercen']));
		$parent_empinfo -> setMSPSEmployerPercen(trim($_POST['txtMSPSEmployerPercen']));
		}
		//contact
		$parent_empinfo -> setEmpPermHouseNo(trim($_POST['txtPermHouseNo']));
		$parent_empinfo -> setEmpPermStreet1(trim($_POST['txtPermStreet1']));
		$parent_empinfo -> setEmpPermStreet2(trim($_POST['txtPermStreet2']));
		$parent_empinfo -> setEmpPermCityTown(trim($_POST['txtPermCityTown']));
		$parent_empinfo -> setEmpPermPostCode(trim($_POST['txtPermPostCode']));
		$parent_empinfo -> setEmpPermTelephone(trim($_POST['txtPermTelep']));
		$parent_empinfo -> setEmpPermMobile(trim($_POST['txtPermMobile']));
		$parent_empinfo -> setEmpPermFax(trim($_POST['txtPermFax']));
		$parent_empinfo -> setEmpPermEmail(trim($_POST['txtPermEmail']));
		$parent_empinfo -> setEmpPermCountry(($_POST['cmbPermCountry']));
		$parent_empinfo -> setEmpPermProvince(($_POST['cmbPermProvince']));
		$parent_empinfo -> setEmpPermDistrict(($_POST['cmbPermDistrict']));
		$parent_empinfo -> setEmpPermElectorate(($_POST['cmbPermElectorate']));
		$message = $parent_empinfo ->addEmpMain($_POST['skipTax']);

		if(isset($_POST['taxFlag']) && $_POST['taxFlag']==1 && $_POST['skipTax']==1 && $_POST['cmbTaxID']!='0') {
			$tax->setEmpId($_POST['txtEmpID']);
			$tax->setTaxId($_POST['cmbTaxID']);
			$tax->setEmpFedStateFlag($_POST['optFedStateFlag']);
			if($_POST['optFedStateFlag']==1) {
				$tax->setEmpTaxFillStat($_POST['cmbFedTaxFillStat']);
				$tax->setEmpTaxState('');
				$tax->setEmpTaxAllowance($_POST['txtFedTaxAllowance']);
				$tax->setEmpTaxExtra($_POST['txtFedTaxExtra']);
			} else {
				$tax->setEmpTaxFillStat($_POST['cmbStateTaxFillStat']);
				$tax->setEmpTaxState($_POST['cmbStateTaxState']);
				$tax->setEmpTaxAllowance($_POST['txtStateTaxAllowance']);
				$tax->setEmpTaxExtra($_POST['txtStateTaxExtra']);
			}
			
			$tax->addEmpTax();
		}
		
		if(isset($_POST['passportFlag']) && $_POST['passportFlag']==1 && $_POST['txtPPNo']!='') {
			$pport->setEmpId($_POST['txtEmpID']);
			$pport->setEmpPPSeqNo(1);
			$pport->setEmpPPNo(trim($_POST['txtPPNo']));
			$pport->setEmpPPIssDat(trim($_POST['txtPPIssDat']));
			$pport->setEmpPPIssPlace(trim($_POST['txtPPIssPlace']));
			$pport->setEmpPPExpDat(trim($_POST['txtPPExpDat']));
			$pport->setEmpPPComment(trim($_POST['PPComment']));
			$pport->setEmpVisaType($_POST['txtVisaType']);
			$pport->setEmpPPType($_POST['PPType']);
			$pport->setEmpPPCountry($_POST['cmbPPCountry']);
			$pport->setEmpPPNoOfEntries(trim($_POST['txtPPNoOfEntries']));
			
			$pport->addEmpPP();
		}
		
		if(isset($_POST['bankFlag']) && $_POST['bankFlag']==1 && $_POST['cmbBranchCode']!='0') {
		   $bankacc->setEmpId($_POST['txtEmpID']);
		   $bankacc->setEmpBranchCode($_POST['cmbBranchCode']);
		   $bankacc->setEmpAccNo(trim($_POST['AccNo']));
		   $bankacc->setEmpAccType($_POST['optAccType']);
		   $bankacc->setEmpAmount($_POST['Amount']);
		   $bankacc->addEmpBank();
				
		}
		
		if(isset($_POST['attachmentFlag']) && $_POST['attachmentFlag']==1 && $_FILES['ufile']['size']>0) {
			//file info
			$fileName=$_FILES['ufile']['name'];
			$tmpName  = $_FILES['ufile']['tmp_name'];
			$fileSize = $_FILES['ufile']['size'];
			$fileType = $_FILES['ufile']['type'];

			//file read
			$fp = fopen($tmpName,'r');
			$contents = fread($fp,filesize($tmpName));
			$contents = addslashes($contents);
			fclose($fp);
			
			if(!get_magic_quotes_gpc())
				$fileName=addslashes($fileName);
				
		$attachment->setEmpId($_POST['txtEmpID']);
		$attachment->setEmpAttId(1);
		$attachment->setEmpAttDesc(trim($_POST['txtAttDesc']));
		$attachment->setEmpAttFilename($fileName);
		$attachment->setEmpAttSize($fileSize);
		$attachment->setEmpAttachment($contents);
		$attachment->setEmpAttType($fileType);
		$attachment->addEmpAtt();				
		}
		
	if ($message) { 
		
		$showMsg = "Addition%Successful!"; //If $message is 1 setting up the 
		
		$reqcode = $_GET['reqcode'];
		$pageID = $_POST['pageID'];
		header("Location: ./empview.php?message=$showMsg&reqcode=$reqcode&pageID=$pageID");
		
	} else {
		
		$showMsg = "Addition Unsuccessful!";
		
		$reqcode = $_GET['reqcode'];
		$pageID = $_GET['pageID'];
		header("Location: ./hremp.php?message=$showMsg&captureState=AddMode");
	}
	
} elseif ((isset($_POST['sqlState'])) && ($_POST['sqlState'] == 'UpdateRecord')) {

		$message=1;
		$res=1;
		
	if($_POST['main']=='1') {
		$parent_empinfo -> setEmpId(trim($_POST['txtEmpID']));
		$parent_empinfo -> setEmpTitle(($_POST['cmbEmpTitle']));
		$parent_empinfo -> setEmpCallName(trim($_POST['txtEmpCallName']));
		$parent_empinfo -> setEmpSurname(trim($_POST['txtEmpSurname']));
		$parent_empinfo -> setEmpMaidenName(trim($_POST['txtEmpMaidenName']));
		$parent_empinfo -> setEmpInitials(trim($_POST['txtEmpInitials']));
		$parent_empinfo -> setEmpNamByIni(trim($_POST['txtEmpNamByIni']));
		$parent_empinfo -> setEmpFullName(trim($_POST['txtEmpFullName']));
		$parent_empinfo -> setEmpOtherName(trim($_POST['txtEmpOtherName']));
		$res = $parent_empinfo ->updateEmpMain();
	}
		if($res==0)
			$message=0;
		//personal
	if($_POST['personalFlag']=='1') {
		$parent_empinfo -> setEmpId(trim($_POST['txtEmpID']));
		$parent_empinfo -> setEmpNICNo(trim($_POST['txtNICNo']));
		$parent_empinfo -> setEmpNICDate(trim($_POST['txtNICDate']));
		$parent_empinfo -> setEmpDOB(trim($_POST['DOB']));
		$parent_empinfo -> setEmpBirthPlace(trim($_POST['txtBirthPlace']));
		$parent_empinfo -> setEmpGender(trim($_POST['optGender']));
		$parent_empinfo -> setEmpBloodGrp(($_POST['cmbBloodGrp']));
		$parent_empinfo -> setEmpNation(($_POST['cmbNation']));
		$parent_empinfo -> setEmpReligion(($_POST['cmbReligion']));
		$parent_empinfo -> setEmpMarital(($_POST['cmbMarital']));
		$parent_empinfo -> setEmpMarriedDate(trim($_POST['txtMarriedDate']));
		$res=$parent_empinfo->updateEmpPers();
	}
		if($res==0)
			$message=0;
				
		//job info
	if($_POST['jobFlag']=='1') {
		$parent_empinfo -> setEmpId(trim($_POST['txtEmpID']));
		$parent_empinfo -> setEmpDatJoin(trim($_POST['txtDatJoin']));
		$parent_empinfo -> setEmpConfirmFlag(($_POST['chkConfirmFlag']!=NULL)?'1':'0');
		$parent_empinfo -> setEmpResigDat(trim($_POST['txtResigDat']));
		$parent_empinfo -> setEmpRetireDat(trim($_POST['txtRetireDat']));
		$parent_empinfo -> setEmpSalGrd(($_POST['cmbSalGrd']));
		$parent_empinfo -> setEmpCorpTit(($_POST['cmbCorpTit']));
		$parent_empinfo -> setEmpDesig(($_POST['cmbDesig']));
		$parent_empinfo -> setEmpCostCode(($_POST['cmbCostCode']));
		$parent_empinfo -> setEmpWorkHours(trim($_POST['txtWorkHours']));
		$parent_empinfo -> setEmpJobPref(trim($_POST['txtJobPref']));
		$res=$parent_empinfo->updateEmpJobInfo();

		if($res==0)
			$message=0;
		//job stat
		$parent_empinfo -> setEmpId(trim($_POST['txtEmpID']));
		$parent_empinfo -> setEmpType(($_POST['cmbType']));
		$parent_empinfo -> setEmpStatutory(($_POST['cmbStatutory']));
		$parent_empinfo -> setEmpCat(($_POST['cmbCat']));
		$parent_empinfo -> setEmpStartDat(trim($_POST['txtStartDat']));
		$parent_empinfo -> setEmpEndDat(trim($_POST['txtEndDat']));
		$parent_empinfo -> setEmpConToPermFlag(($_POST['chkConToPermFlag']!=null)?'1':'0');
		$parent_empinfo -> setEmpConToPermDat(trim($_POST['txtConToPermDat']));
		$parent_empinfo -> setEmpHRActivFlag(($_POST['chkHRActivFlag']!=null)?'1':'0');
		$parent_empinfo -> setEmpPayActivFlag(trim($_POST['txtPayActivFlag']!=null)?'1':'0');
		$parent_empinfo -> setEmpTimAttActivFlag(trim($_POST['chkTimeAttActivFlag']!=null)?'1':'0');
		$res=$parent_empinfo->updateEmpJobStat();
	}
		if($res==0)
			$message=0;
		//tax
	if($_POST['taxFlag']=='1') {
		$parent_empinfo -> setEmpId(trim($_POST['txtEmpID']));
		$parent_empinfo -> setEmpTaxExempt(($_POST['cmbTaxExempt']));
		$parent_empinfo -> setEmpTaxOnTaxFlag(($_POST['chkTaxOnTaxFlag']!=null)?'1':'0');
		$parent_empinfo -> setEmpTaxID(trim($_POST['txtTaxID']));
		$parent_empinfo -> setEmpEPFEligibleFlag(($_POST['chkEPFEligibleFlag']!=null)?'1':'0');
		$parent_empinfo -> setEmpEPFNo(trim($_POST['txtEPFNo']));
		$parent_empinfo -> setCFundCBFundFlag(trim($_POST['optCFundCBFundFlag']));
		$parent_empinfo -> setEPFEmployeePercen(trim($_POST['txtEPFEmployeePercen']));
		$parent_empinfo -> setEPFEmployerPercen(trim($_POST['txtEPFEmployerPercen']));
		$parent_empinfo -> setETFEligibleFlag(($_POST['chkETFEligibleFlag']!=null)?'1':'0');
		$parent_empinfo -> setEmpETFNo(trim($_POST['txtETFNo']));
		$parent_empinfo -> setETFEmployeePercen(trim($_POST['txtETFEmployeePercen']));
		$parent_empinfo -> setETFDat(trim($_POST['txtETFDat']));
		$parent_empinfo -> setMSPSEligibleFlag(($_POST['chkMSPSEligibleFlag']!=null)?'1':'0');
		$parent_empinfo -> setMSPSEmployeePercen(trim($_POST['txtMSPSEmployeePercen']));
		$parent_empinfo -> setMSPSEmployerPercen(trim($_POST['txtMSPSEmployerPercen']));
		$res=$parent_empinfo->updateEmpTax();
	}
		if($res==0)
			$message=0;

		$parent_empinfo -> setEmpLoc($_POST['cmbLocation']);
		$parent_empinfo -> setEmpPrefLoc($_POST['txtHiCode']);
		$parent_empinfo->updateEmpWrkStation();
		
			//contact
	if($_POST['contactFlag']=='1') {
		$parent_empinfo -> setEmpId(trim($_POST['txtEmpID']));
		$parent_empinfo -> setEmpPermHouseNo(trim($_POST['txtPermHouseNo']));
		$parent_empinfo -> setEmpPermStreet1(trim($_POST['txtPermStreet1']));
		$parent_empinfo -> setEmpPermStreet2(trim($_POST['txtPermStreet2']));
		$parent_empinfo -> setEmpPermCityTown(trim($_POST['txtPermCityTown']));
		$parent_empinfo -> setEmpPermPostCode(trim($_POST['txtPermPostCode']));
		$parent_empinfo -> setEmpPermTelephone(trim($_POST['txtPermTelep']));
		$parent_empinfo -> setEmpPermMobile(trim($_POST['txtPermMobile']));
		$parent_empinfo -> setEmpPermFax(trim($_POST['txtPermFax']));
		$parent_empinfo -> setEmpPermEmail(trim($_POST['txtPermEmail']));
		$parent_empinfo -> setEmpPermCountry(($_POST['cmbPermCountry']));
		$parent_empinfo -> setEmpPermProvince(($_POST['cmbPermProvince']));
		$parent_empinfo -> setEmpPermDistrict(($_POST['cmbPermDistrict']));
		$parent_empinfo -> setEmpPermElectorate(($_POST['cmbPermElectorate']));
		$res=$parent_empinfo->updateEmpPermRes();
	}
		if($res==0)
			$message=0;
	
	// Checking whether the $message Value returned is 1 or 0
	if ($message) { 
		
		$showMsg = "Updation%Successful!"; //If $message is 1 setting up the 
		
		$reqcode = $_GET['reqcode'];
		$pageID = $_POST['pageID'];
		header("Location: ./empview.php?message=$showMsg&reqcode=$reqcode&pageID=$pageID");
		
	} else {
		
		$showMsg = "Updation%Unsuccessful!";
		
		$reqcode = $_GET['reqcode'];
		$pageID = $_GET['pageid'];
		header("Location: ./hremp.php?message=$showMsg&captureState=updatemode");
	}

} elseif ((isset($_POST['sqlState'])) && ($_POST['sqlState'] == 'UpdateCountry')) {
	$parent_empinfo -> setEmpId(trim($_POST['txtEmpID']));
	$parent_empinfo->setEmpTaxCountry($_POST['cmbTaxCountry']);	
	$parent_empinfo->updateEmpTaxCountry();	
}
?>
<html>
<head>
<script language="JavaScript" type="text/JavaScript">
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

function addSave() {

	var cnt = document.frmEmp.txtEmpCallName;
	if(!alpha(cnt)) {
		alert("Field should be Alphabetic");
		cnt.focus();
		return;
	}

	var cnt = document.frmEmp.txtEmpSurname;
	if(!alpha(cnt)) {
		alert("Field should be Alphabetic");
		cnt.focus();
		return;
	}
	
	var cnt = document.frmEmp.txtEmpFullName;
	if(!alpha(cnt)) {
		alert("Field should be Alphabetic");
		cnt.focus();
		return;
	}

	if(document.frmEmp.txtNICNo.value=='') {
		alert("Field Empty");
		MM_showHideLayers('hidebg','','hide','personal','','show','job','','hide','workstation','','hide','tax','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','hide');
		document.frmEmp.txtNICNo.focus();
		return;
	}
	
	if(document.frmEmp.cmbSalGrd.value=='0') {
		alert("Field should be selected");
		MM_showHideLayers('hidebg','','hide','personal','','hide','job','','show','workstation','','hide','tax','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','hide');
		document.frmEmp.cmbSalGrd.focus();
		return;
	}
	
	if(document.frmEmp.cmbCorpTit.value=='0') {
		alert("Field should be selected");
		MM_showHideLayers('hidebg','','hide','personal','','hide','job','','show','workstation','','hide','tax','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','hide');
		document.frmEmp.cmbCorpTit.focus();
		return;
	}

	if(document.frmEmp.cmbDesig.value=='0') {
		alert("Field should be selected");
		MM_showHideLayers('hidebg','','hide','personal','','hide','job','','show','workstation','','hide','tax','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','hide');
		document.frmEmp.cmbDesig.focus();
		return;
	}

	if(document.frmEmp.cmbType.value=='0') {
		alert("Field should be selected");
		MM_showHideLayers('hidebg','','hide','personal','','hide','job','','show','workstation','','hide','tax','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','hide');
		document.frmEmp.cmbType.focus();
		return;
	}

		document.frmEmp.sqlState.value = "NewRecord";
		document.frmEmp.submit();		
	}			

	function goBack() {
		location.href = "empview.php?reqcode=<?=$_GET['reqcode']?>";
	}

function mout() {
	if(document.frmEmp.EditMode.value=='1') 
		document.Edit.src='./themes/beyondT/pictures/btn_save.jpg'; 
	else
		document.Edit.src='./themes/beyondT/pictures/btn_edit.jpg'; 
}

function mover() {
	if(document.frmEmp.EditMode.value=='1') 
		document.Edit.src='./themes/beyondT/pictures/btn_save_02.jpg'; 
	else
		document.Edit.src='./themes/beyondT/pictures/btn_edit_02.jpg'; 
}
	
function edit()
{
	if(document.frmEmp.EditMode.value=='1') {
		addUpdate();
		return;
	}
	
	var frm=document.frmEmp;
//  alert(frm.elements.length);
	for (var i=0; i < frm.elements.length; i++)
		frm.elements[i].disabled = false;
	document.Edit.src="./themes/beyondT/pictures/btn_save.jpg";
	document.Edit.title="Save";
	document.frmEmp.EditMode.value='1';
}
	
function addUpdate() {

	var cnt = document.frmEmp.txtEmpCallName;
	if(!alpha(cnt)) {
		alert("Field should be Alphabetic");
		cnt.focus();
		return;
	}

	var cnt = document.frmEmp.txtEmpSurname;
	if(!alpha(cnt)) {
		alert("Field should be Alphabetic");
		cnt.focus();
		return;
	}
	
	var cnt = document.frmEmp.txtEmpFullName;
	if(!alpha(cnt)) {
		alert("Field should be Alphabetic");
		cnt.focus();
		return;
	}

	if(document.frmEmp.txtNICNo.value=='') {
		alert("Field Empty");
		document.frmEmp.txtNICNo.focus();
		return;
	}
	
	if(document.frmEmp.cmbSalGrd.value=='0') {
		alert("Field should be selected");
		MM_showHideLayers('hidebg','','hide','personal','','hide','job','','show','workstation','','hide','tax','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','hide');
		document.frmEmp.cmbSalGrd.focus();
		return;
	}
	
	if(document.frmEmp.cmbCorpTit.value=='0') {
		alert("Field should be selected");
		MM_showHideLayers('hidebg','','hide','personal','','hide','job','','show','workstation','','hide','tax','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','hide');
		document.frmEmp.cmbCorpTit.focus();
		return;
	}

	if(document.frmEmp.cmbDesig.value=='0') {
		alert("Field should be selected");
		MM_showHideLayers('hidebg','','hide','personal','','hide','job','','show','workstation','','hide','tax','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','hide');
		document.frmEmp.cmbDesig.focus();
		return;
	}

	if(document.frmEmp.cmbType.value=='0') {
		alert("Field should be selected");
		MM_showHideLayers('hidebg','','hide','personal','','hide','job','','show','workstation','','hide','tax','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','hide');
		document.frmEmp.cmbType.focus();
		return;
	}
	
		document.frmEmp.sqlState.value = "UpdateRecord";
		document.frmEmp.submit();		
	}			

<? if ((isset($_GET['capturemode'])) && ($_GET['capturemode'] == 'updatemode')) { 	?>
function reLoad() {
	location.href ="hremp.php?id=<?=$_GET['id']?>&capturemode=updatemode&reqcode=<?=$_GET['reqcode']?>&pageID=<?=$_GET['pageID']?>";
}
 <? } ?>
 
function qCombo(lblPane) {

	document.frmEmp.pane.value=lblPane;
	document.frmEmp.submit();
}

function chgPane(lblPane) {

	document.frmEmp.pane.value=lblPane;
}

function qshowpane() {
	var opt=eval(document.frmEmp.pane.value);
		switch(opt) {
          	case 1 : MM_showHideLayers('hidebg','','hide','personal','','show','job','','hide','workstation','','hide','tax','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','hide'); break;
          	case 2 : MM_showHideLayers('hidebg','','hide','personal','','hide','job','','show','workstation','','hide','tax','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','hide'); break;
          	case 3 : MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','show','tax','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','hide'); break;
            case 4 : MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','hide','tax','','show','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','hide'); break;
            case 5 : MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','hide','tax','','hide','contact','','show','passport','','hide','bank','','hide','attachment','','hide','other','','hide'); break;
          	case 6 : MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','hide','tax','','hide','contact','','hide','passport','','show','bank','','hide','attachment','','hide','other','','hide'); break;
          	case 7 : MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','hide','tax','','hide','contact','','hide','passport','','hide','bank','','show','attachment','','hide','other','','hide'); break;
          	case 8 : MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','hide','tax','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','show','other','','hide'); break;
            case 9 : MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','hide','tax','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','show'); break;
		}
}

function setUpdate(opt) {
	
		switch(eval(opt)) {
          	case 0 : document.frmEmp.main.value=1; break;
          	case 1 : document.frmEmp.personalFlag.value=1; break;
          	case 2 : document.frmEmp.jobFlag.value=1; break;
          	case 3 : document.frmEmp.workstationFlag.value=1; break;
            case 4 : document.frmEmp.taxFlag.value=1; break;
            case 5 : document.frmEmp.contactFlag.value=1; break;
            case 6 : document.frmEmp.passportFlag.value=1; break;
            case 7 : document.frmEmp.bankFlag.value=1; break;
            case 8 : document.frmEmp.attachmentFlag.value=1; break;
		}	
}

function hierChg(cnt) {
		document.frmEmp.txtHiCode.value=cnt.value;
		qCombo(3);
}

function taxCountry() {

			if(document.frmEmp.cmbTaxCountry.options[document.frmEmp.cmbTaxCountry.selectedIndex].text!='Sri Lanka')
				document.frmEmp.skipTax.value="1";
			else
				document.frmEmp.skipTax.value="0";

			qCombo(4);
}

function updateTaxCountry() {
		document.frmEmp.sqlState.value = "UpdateCountry";
		qCombo(4);
}

function setFed() {
	
			document.frmEmp.cmbFedTaxFillStat.disabled = false;
			document.frmEmp.txtFedTaxAllowance.disabled = false;
			document.frmEmp.txtFedTaxExtra.disabled = false;
			document.frmEmp.cmbStateTaxFillStat.disabled = true;
			document.frmEmp.cmbStateTaxState.disabled = true;
			document.frmEmp.txtStateTaxAllowance.disabled = true;
			document.frmEmp.txtStateTaxExtra.disabled = true;
}

function setState() {
	
			document.frmEmp.cmbFedTaxFillStat.disabled = true;
			document.frmEmp.txtFedTaxAllowance.disabled = true;
			document.frmEmp.txtFedTaxExtra.disabled = true;
			document.frmEmp.cmbStateTaxFillStat.disabled = false;
			document.frmEmp.cmbStateTaxState.disabled = false;
			document.frmEmp.txtStateTaxAllowance.disabled = false;
			document.frmEmp.txtStateTaxExtra.disabled = false;
}

function dwPopup() {
        var popup=window.open('download.php?id=<?=isset($_GET['id']) ? $_GET['id'] : '' ?>&ATTACH=<?=isset($_GET['ATTACH']) ? $_GET['ATTACH'] : '' ?>','Downloads');
        if(!popup.opener) popup.opener=self;
}	

function delAttach() {
	document.frmEmp.attSTAT.value="DEL";
	qCombo(8);
}
function addAttach() {
	document.frmEmp.attSTAT.value="ADD";
	qCombo(8);
}

function viewAttach(att) {
	document.frmEmp.action=document.frmEmp.action + "&ATTACH=" + att;
	document.frmEmp.pane.value=8;
	document.frmEmp.submit();
}

function editAttach() {
	document.frmEmp.attSTAT.value="EDIT";
	qCombo(8);
}

function delBranch() {
	document.frmEmp.brchSTAT.value="DEL";
	qCombo(7);
}

function addBranch() {
	document.frmEmp.brchSTAT.value="ADD";
	qCombo(7);
}

function viewBranch(brch) {
	document.frmEmp.action=document.frmEmp.action + "&BRCH=" + brch ;
	document.frmEmp.pane.value=7;
	document.frmEmp.submit();
}

function editBranch() {
	document.frmEmp.brchSTAT.value="EDIT";
	qCombo(7);
}

function delPassport() {
	document.frmEmp.passportSTAT.value="DEL";
	qCombo(6);
}

function addPassport() {
	document.frmEmp.passportSTAT.value="ADD";
	qCombo(6);
}

function viewPassport(pSeq) {
	document.frmEmp.action=document.frmEmp.action + "&PPSEQ=" + pSeq ;
	document.frmEmp.pane.value=6;
	document.frmEmp.submit();
}

function editPassport() {
	document.frmEmp.passportSTAT.value="EDIT";
	qCombo(6);
}
	

function delTax() {
	document.frmEmp.taxSTAT.value="DEL";
	qCombo(4);
}

function addTax() {
	document.frmEmp.taxSTAT.value="ADD";
	document.frmEmp.pane.value=4;
	document.frmEmp.submit();
}

function viewTax(taxID,fdFlag) {
	document.frmEmp.action=document.frmEmp.action + "&TAXID=" + taxID + "&FEDST=" + fdFlag;
	document.frmEmp.pane.value=4;
	document.frmEmp.submit();
}

function editTax()
{
		document.frmEmp.taxSTAT.value="EDIT";
		document.frmEmp.pane.value=4;
		document.frmEmp.submit();
}


</script>

<link href="./themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("./themes/beyondT/css/style.css"); </style>

</head>
<body onload="<?=(isset($_POST['pane']) && $_POST['pane']!='')?'qshowpane();':''?>">
<table width='100%' cellpadding='0' cellspacing='0' border='0'>
  <tr>
    <td valign='top'>&nbsp; </td>
    <td width='100%'><h2 align="center">Employee Information</h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>
</table>

<?
	if ((isset($_GET['capturemode'])) && ($_GET['capturemode'] == 'addmode')) {
?>

<form name="frmEmp" method="post" action="./hremp.php?pageID=<?=$_GET['pageID']?>&reqcode=<?=$_GET['reqcode']?>&capturemode=<?=$_GET['capturemode']?>" enctype="multipart/form-data">
<input type="hidden" name="sqlState">
<input type="hidden" name="pane" value="<?=(isset($_POST['pane']) && $_POST['pane']!='') ? $_POST['pane'] : ''?>">

<input type="hidden" name="taxFlag" value="<?=isset($_POST['taxFlag'])? $_POST['taxFlag'] : '0'?>">
<input type="hidden" name="passportFlag" value="<?=isset($_POST['passportFlag'])? $_POST['passportFlag'] : '0'?>">
<input type="hidden" name="bankFlag" value="<?=isset($_POST['bankFlag'])? $_POST['bankFlag'] : '0'?>">
<input type="hidden" name="attachmentFlag" value="<?=isset($_POST['attachmentFlag'])? $_POST['attachmentFlag'] : '0'?>">

<table width="550" align="center" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="13"><img name="table_r1_c1" src="themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="339" background="themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td width="13"><img name="table_r1_c3" src="themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="11"><img src="themes/beyondT/pictures/spacer.gif" width="1" height="12" border="0" alt=""></td>
                </tr>
                <tr>
                  <td background="themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
			  <tr> 
				<td>Code</td>
				<td> <input type="text" readonly="true" name="txtEmpID" value=<?=$lastRecord=$parent_empinfo->getLastRecord(); ?> ></td>
			  </tr>
			  <tr>
				<td>Title</td>
				<td> <select <?=$locRights['add'] ? '':'disabled'?> name="cmbEmpTitle">
				<?      
					for($c=0;$c < count($arrTitle);$c++)
						if(isset($_POST['cmbEmpTitle']) && $_POST['cmbEmpTitle']==$arrTitle[$c])
							echo '<option selected value=' . $arrTitle[$c] . '>' . $arrTitle[$c] .'</option>';
						else 
							echo '<option value=' . $arrTitle[$c] . '>' . $arrTitle[$c] .'</option>';
				?>
				</select></td>
			  </tr>
			  <tr> 
				<td>Calling Name</td>
				<td> <input type="text" name="txtEmpCallName" <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($_POST['txtEmpCallName']))?$_POST['txtEmpCallName']:''?>"></td>
				<td>&nbsp;</td>
				<td>Surname</td>
				<td> <input type="text" name="txtEmpSurname" <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($_POST['txtEmpSurname']))?$_POST['txtEmpSurname']:''?>"></td>
			  </tr>
			  <tr> 
				<td>Maiden Name</td>
				<td> <input type="text" name="txtEmpMaidenName" <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($_POST['txtEmpMaidenName']))?$_POST['txtEmpMaidenName']:''?>"></td>
				<td>&nbsp;</td>
			  <td>Initials</td>
				<td> <input type="text" name="txtEmpInitials" <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($_POST['txtEmpInitials']))?$_POST['txtEmpInitials']:''?>"></td>
			  </tr>
			  <tr>
				<td>Names By Initials</td>
				<td> <input type="text" name='txtEmpNamByIni' <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($_POST['txtEmpNamByIni']))?$_POST['txtEmpNamByIni']:''?>"></td>
			  <td>&nbsp;</td>
				<td>Full Name</td>
				<td> <input type="text" name='txtEmpFullName' <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($_POST['txtEmpFullName']))?$_POST['txtEmpFullName']:''?>"></td>
			  </tr>
				<tr>
				<td>Other Names</td>
				<td> <input type="text" name='txtEmpOtherName' <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($_POST['txtEmpOtherName']))?$_POST['txtEmpOtherName']:''?>"></td>
			  </tr>
                  </table></td>
                  <td background="themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                </tr>
                <tr>
                  <td><img name="table_r3_c1" src="themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
                  <td background="themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img name="table_r3_c3" src="themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
                  <td><img src="themes/beyondT/pictures/spacer.gif" width="1" height="16" border="0" alt=""></td>
                </tr>
              </table>

    <table border="0" align="center" >
    <tr>
    <td><img title="Back" onmouseout="this.src='./themes/beyondT/pictures/btn_back.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_back_02.jpg';"  src="./themes/beyondT/pictures/btn_back.jpg" onclick="goBack();"></td>
    <td>
					<?	if($locRights['add']) { ?>
					        <img border="0" title="Save" onClick="addSave();" onmouseout="this.src='./themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_save_02.jpg';" src="./themes/beyondT/pictures/btn_save.jpg">
					<? 	} else { ?>
					        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="./themes/beyondT/pictures/btn_save.jpg">
					<?	} ?>
    </td>
    <td>&nbsp;</td>
    <td><img onClick="document.frmEmp.reset();" onmouseout="this.src='./themes/beyondT/pictures/btn_clear.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_clear_02.jpg';" src="./themes/beyondT/pictures/btn_clear.jpg"></td>
    </tr>
    </table>


<table width="600" align="center" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><table border="0" cellpadding="0" cellspacing="0" width="600">
        <tr>
          <td><img name="tabs_r1_c1" src="themes/beyondT/pictures/tabs_r1_c1.jpg" width="28" height="71" border="0" alt=""></td>
          <td><a href="#" onClick="MM_showHideLayers('hidebg','','hide','personal','','show','job','','hide','workstation','','hide','tax','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','hide')">
		  	  <img name="tabs_r1_c2" src="themes/beyondT/pictures/tabs_r1_c2.jpg" width="62" height="71" border="0" alt=""></a></td>
          <td><a href="#" onClick="MM_showHideLayers('hidebg','','hide','personal','','hide','job','','show','workstation','','hide','tax','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','hide')">
		      <img name="tabs_r1_c3" src="themes/beyondT/pictures/tabs_r1_c3.jpg" width="56" height="71" border="0" alt=""></a></td>
         <td><a href="#" onClick="MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','show','tax','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','hide')">
		      <img name="tabs_r1_c4" src="themes/beyondT/pictures/tabs_r1_c4.jpg" width="62" height="71" border="0" alt=""></a></td>
          <td><a href="#" onClick="MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','hide','tax','','show','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','hide')">
		      <img name="tabs_r1_c5" src="themes/beyondT/pictures/tabs_r1_c5.jpg" width="60" height="71" border="0" alt=""></a></td>
          <td><a href="#" onClick="MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','hide','tax','','hide','contact','','show','passport','','hide','bank','','hide','attachment','','hide','other','','hide')">
		      <img name="tabs_r1_c6" src="themes/beyondT/pictures/tabs_r1_c6.jpg" width="59" height="71" border="0" alt=""></a></td>
          <td><a href="#" onClick="MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','hide','tax','','hide','contact','','hide','passport','','show','bank','','hide','attachment','','hide','other','','hide')">
		  	  <img name="tabs_r1_c7" src="themes/beyondT/pictures/tabs_r1_c7.jpg" width="61" height="71" border="0" alt=""></a></td>
          <td><a href="#" onClick="MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','hide','tax','','hide','contact','','hide','passport','','hide','bank','','show','attachment','','hide','other','','hide')">
		      <img name="tabs_r1_c8" src="themes/beyondT/pictures/tabs_r1_c8.jpg" width="60" height="71" border="0" alt=""></a></td>
          <td><a href="#" onClick="MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','hide','tax','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','show','other','','hide')">
		      <img name="tabs_r1_c9" src="themes/beyondT/pictures/tabs_r1_c9.jpg" width="60" height="71" border="0" alt=""></a></td>
<!--          <td><a href="#" onClick="MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','hide','tax','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','show')">
		      <img name="tabs_r1_c10" src="themes/beyondT/pictures/tabs_r1_c10.jpg" width="54" height="71" border="0" alt=""></a></td>-->
          <td><img name="tabs_r1_c11" src="themes/beyondT/pictures/tabs_r1_c11.jpg" width="38" height="71" border="0" alt=""></td>
        </tr>
        <tr>
          <td colspan="11"><img name="tabs_r2_c1" src="themes/beyondT/pictures/tabs_r2_c1.jpg" width="600" height="17" border="0" alt=""></td>
          <td><img src="images/spacer.gif" width="1" height="17" border="0" alt=""></td>
        </tr>
        <tr>
          <td colspan="11"><img name="tabs_r3_c1" src="themes/beyondT/pictures/tabs_r3_c1.jpg" width="600" height="27" border="0" alt=""></td>
          <td><img src="images/spacer.gif" width="1" height="27" border="0" alt=""></td>
        </tr>
    </table></td>
  </tr>
  <tr>
    <td align="center"><div id="personal" style="position:absolute; z-index:2; width: 540px; visibility: hidden; left: 100px; top: 360px;">
	  <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="13"><img name="table_r1_c1" src="themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><table height="200" border="0" cellpadding="0" cellspacing="0">
<tr>
					<td>SSN No:</td>
					<td><input type="text" name="txtNICNo" <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($_POST['txtNICNo']))?$_POST['txtNICNo']:''?>"></td>
					<td width="50">&nbsp;</td>
					<td>Nationality</td>
					<td><select <?=$locRights['add'] ? '':'disabled'?> name="cmbNation">
						<option value="0">--Select Nationality--</option>
<?
					$nation=$parent_empinfo ->getNationCodes();
					for($c=0;$nation && count($nation)>$c;$c++)
						if(isset($_POST['cmbNation']) && $_POST['cmbNation']==$nation[$c][0])
						    echo "<option selected value='" . $nation[$c][0] . "'>" .$nation[$c][1]. "</option>";
						else
						    echo "<option value='" . $nation[$c][0] . "'>" .$nation[$c][1]. "</option>";
?>					
					</select></td>
				</tr>
				<tr>
				<td>SSN Issued Date</td>
				<td><input type="text" name="txtNICDate" readonly value=<?=(isset($_POST['txtNICDate']))?$_POST['txtNICDate']:''?>>&nbsp;<input type="button" <?=$locRights['add'] ? '':'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtNICDate);return false;"></td>
				<td>&nbsp;</td>
				<td>Religion</td>
				<td><select <?=$locRights['add'] ? '':'disabled'?> name="cmbReligion">
						<option value="0">----Select Religion----</option>
<?
					$rel=$parent_empinfo ->getReligionCodes();
					for($c=0;$rel && count($rel)>$c;$c++)
						if(isset($_POST['cmbReligion']) && $_POST['cmbReligion']==$rel[$c][0])
						    echo "<option selected value='" . $rel[$c][0] . "'>" .$rel[$c][1]. "</option>";
					    else
						    echo "<option value='" . $rel[$c][0] . "'>" .$rel[$c][1]. "</option>";
?>					
				</select></td>
				
				</tr>
				<tr>
				<td>Date of Birth</td>
				<td><input type="text" name="DOB" readonly value=<?=(isset($_POST['DOB']))?$_POST['DOB']:''?>>&nbsp;<input type="button" <?=$locRights['add'] ? '':'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.DOB);return false;"></td>
				<td>&nbsp;</td>
				<td>Blood Group</td>
				<td><select <?=$locRights['add'] ? '':'disabled'?> name="cmbBloodGrp">
<?
					for($c=0;count($arrBGroup)>$c;$c++)
						if(isset($_POST['cmbBloodGrp']) && $_POST['cmbBloodGrp']==$arrBGroup[$c])
							echo "<option selected>" .$arrBGroup[$c]. "</option>";
						else 
							echo "<option>" .$arrBGroup[$c]. "</option>";
?>
				</select></td>
				</tr>
				<tr>
				<td>Place of Birth</td>
				<td><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtBirthPlace" value="<?=(isset($_POST['txtBirthPlace']))?$_POST['txtBirthPlace']:''?>"></td>
				<td>&nbsp;</td>
				<td>Marital Status</td>
				<td><select <?=$locRights['add'] ? '':'disabled'?> name="cmbMarital">
					<option>--Select--</option>
<?					
					for($c=0;count($arrMStat)>$c;$c++)
						if(isset($_POST['cmbMarital']) && $_POST['cmbMarital']==$arrMStat[$c])
						    echo "<option selected>" .$arrMStat[$c]."</option>";
						else 
						    echo "<option>" .$arrMStat[$c]."</option>";
?>
				</select></td>
				</tr>
				<tr>
				<td>Gender</td>
				<td valign="middle">Male<input <?=$locRights['add'] ? '':'disabled'?> type="radio" name="optGender" value="1" checked>		Female<input <?=$locRights['add'] ? '':'disabled'?> type="radio" name="optGender" value="2" <?=(isset($_POST['optGender']) && isset($_POST['optGender'])==2)?'checked':''?>></td>
				<td>&nbsp;</td>
				<td>Married Date</td>
				<td><input type="text" readonly name="txtMarriedDate" value=<?=(isset($_POST['txtMarriedDate']))?$_POST['txtMarriedDate']:''?>>&nbsp;<input type="button" <?=$locRights['add'] ? '':'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtMarriedDate);return false;"></td>
				</tr> 
				</table></td>
          <td background="themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
    </div>
	<div id="job" style="position:absolute; z-index:2; width: 540px; visibility: hidden; left: 100px; top: 360px;">
	  <table border="0" cellpadding="0" cellspacing="0" >
        <tr>
          <td width="13"><img name="table_r1_c1" src="themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><table height="300" border="0" cellpadding="0" cellspacing="0">
<tr>
			  <td>Date Joined</td>
			  <td><input type="text" name="txtDatJoin" readonly value=<?=(isset($_POST['txtDatJoin']))?$_POST['txtDatJoin']:''?>>&nbsp;<input type="button" <?=$locRights['add'] ? '':'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtDatJoin);return false;"></td>
			  <td width="50">&nbsp;</td>
			  <td>Employment Type</td>
			  <td><select <?=$locRights['add'] ? '':'disabled'?> name="cmbType">
			  		<option value="0">---Select Empl. Type---</option>
<?					for($c=0;count($arrEmpType)>$c;$c++)
						if(isset($_POST['cmbType']) && $_POST['cmbType']==$arrEmpType[$c])
							echo "<option selected>" .$arrEmpType[$c]. "</option>";
						else
							echo "<option>" .$arrEmpType[$c]. "</option>";
?>			        
			  </select></td>
              </tr>
			  <tr>
			  <td>Confirmed</td>
			  <td><input type="checkbox" <?=$locRights['add'] ? '':'disabled'?> name="chkConfirmFlag" <?=(isset($_POST['chkConfirmFlag']) && $_POST['chkConfirmFlag']=='1'?'checked':'')?> value="1"></td>
			  <td width="50">&nbsp;</td>
			  <td>Statutory Classification</td>
			  <td><select <?=$locRights['add'] ? '':'disabled'?> name="cmbStatutory">
			  		<option value="0">--Select Stat. Classification--</option>
<?					$stat=$parent_empinfo ->getStatCodes();
						for($c=0;$stat && count($stat)>$c;$c++)
							if(isset($_POST['cmbStatutory']) && $_POST['cmbStatutory']==$stat[$c][0])
							    echo "<option selected value='".$stat[$c][0]. "'>" . $stat[$c][1] ."</option>";
							else
							    echo "<option value='".$stat[$c][0]. "'>" . $stat[$c][1] ."</option>";
?>			  
			  </select></td>
			  </tr>
			  <tr>
			  <td>Resignation Date</td>
			  <td><input type="text" name="txtResigDat" readonly value=<?=(isset($_POST['txtResigDat']))?$_POST['txtResigDat']:''?>>&nbsp;<input type="button" <?=$locRights['add'] ? '':'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtResigDat);return false;"></td>
			  <td width="50">&nbsp;</td>
			  <td>Employment Category</td>
			  <td><select <?=$locRights['add'] ? '':'disabled'?> name="cmbCat">
			  		<option value="0">---Select Empl. Category---</option>
<?					$catlist=$parent_empinfo ->getCatCodes();
					for($c=0;$catlist && count($catlist)>$c;$c++)
						if(isset($_POST['cmbCat']) && $_POST['cmbCat']==$catlist[$c][0])
						  	echo "<option selected value='" . $catlist[$c][0] ."'>" .$catlist[$c][1]. "</option>";
						else
						  	echo "<option value='" . $catlist[$c][0] ."'>" .$catlist[$c][1]. "</option>";
?>			  
			  </select></td>
			  </tr>
			  <tr>
			  <td>Retire Date</td>
			  <td><input type="text" name="txtRetireDat" readonly value=<?=(isset($_POST['txtRetireDat']))?$_POST['txtRetireDat']:''?>>&nbsp;<input type="button" <?=$locRights['add'] ? '':'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtRetireDat);return false;"></td>
			  <td width="50">&nbsp;</td>
			  <td>Start Date</td>
			  <td><input type="text" name="txtStartDat" readonly value=<?=(isset($_POST['txtStartDat']))?$_POST['txtStartDat']:''?>>&nbsp;<input type="button" <?=$locRights['add'] ? '':'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtStartDat);return false;"></td>
			  </tr>
			  <tr>
 			  <td>Salary Grade</td>
			  <td><select onchange="document.frmEmp.cmbDesig.options[0].selected=true,qCombo(2);" <?=$locRights['add'] ? '':'disabled'?> name="cmbSalGrd">
			  		<option value="0">--Select Salary Grade--</option>
<?					$grdlist=$parent_empinfo ->getSalGrdCodes();
					for($c=0;$grdlist && count($grdlist)>$c;$c++)
						if(isset($_POST['cmbSalGrd']) && $_POST['cmbSalGrd']==$grdlist[$c][0])
							echo "<option selected value='" .$grdlist[$c][0]. "'>" .$grdlist[$c][1]. "</option>";
						else
							echo "<option value='" .$grdlist[$c][0]. "'>" .$grdlist[$c][1]. "</option>";
?>			  
			  </select></td>
			  <td width="50">&nbsp;</td>
			  <td>End Date</td>
			  <td><input type="text" name="txtEndDat" readonly value=<?=(isset($_POST['txtEndDate']))?$_POST['txtEndDate']:''?>>&nbsp;<input type="button" <?=$locRights['add'] ? '':'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtEndDat);return false;"></td>
			  </tr>
			  <tr>
			  <td>Corporate Title</td>
			  <td><select onchange="qCombo(2)" <?=$locRights['add'] ? '':'disabled'?> name="cmbCorpTit">
			  		<option value="0">--Select Corporate Title--</option>
<?				if(isset($_POST['cmbSalGrd'])) {
					$ctlist=$parent_empinfo ->getCorpTitles($_POST['cmbSalGrd']);
					for($c=0;$ctlist && count($ctlist)>$c;$c++)
						if(isset($_POST['cmbCorpTit']) && $_POST['cmbCorpTit']==$ctlist[$c][1])
							echo "<option selected value='" .$ctlist[$c][1]. "'>" .$ctlist[$c][2]. "</option>";
						else
							echo "<option value='" .$ctlist[$c][1]. "'>" .$ctlist[$c][2]. "</option>";
				}
?>			  
			  </select></td>
			  <td width="50">&nbsp;</td>
			  <td>Contract to Permanent</td>
			  <td><input type="checkbox" <?=$locRights['add'] ? '':'disabled'?> name="chkConToPermFlag" <?=(isset($_POST['chkConToPermFlag']) && $_POST['chkConToPermFlag']=='1')?'checked':''?> value="1"></td>
			  </tr>
			  <tr>
			  <td>Designation</td>
			  <td><select <?=$locRights['add'] ? '':'disabled'?> name="cmbDesig">
			  		<option value="0">---Select Designation---</option>
<?					if(isset($_POST['cmbCorpTit'])) {
					$deslist=$parent_empinfo ->getDes($_POST['cmbCorpTit']);
					for($c=0;$deslist && count($deslist)>$c;$c++)
						if(isset($_POST['cmbDesig']) && $_POST['cmbDesig']==$deslist[$c][1])
							echo "<option selected value='" .$deslist[$c][1]. "'>" .$deslist[$c][2]. "</option>";
						else
							echo "<option value='" .$deslist[$c][1]. "'>" .$deslist[$c][2]. "</option>";
					}
?>			  
			  </select></td>
			  <td width="50">&nbsp;</td>
			  <td>Contract to Permanent Date</td>
			  <td><input type="text" name="txtConToPermDat" value=<?=(isset($_POST['txtConToPermDat']))?$_POST['txtConToPermDat']:''?>>&nbsp;<input type="button" <?=$locRights['add'] ? '':'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtConToPermDat);return false;"></td>
			  </tr>
			  <tr>
			  <td>Costing Centre</td>
			  <td><select <?=$locRights['add'] ? '':'disabled'?> name="cmbCostCode">
			  		<option value="0">--Select Costing Centre--</option>
<?					$costlist=$parent_empinfo ->getCostCodes();
					for($c=0;$costlist && count($costlist)>$c;$c++)
						if(isset($_POST['cmbCostCode']) && $_POST['cmbCostCode']==$costlist[$c][0])
							echo "<option selected value='" .$costlist[$c][0]. "'>" .$costlist[$c][1]. "</option>";
						else
							echo "<option value='" .$costlist[$c][0]. "'>" .$costlist[$c][1]. "</option>";
?>			  
			  </select></td>
			  <td width="50">&nbsp;</td>
			  <td>HR Active</td>
			  <td><input type="checkbox" <?=$locRights['add'] ? '':'disabled'?> name="chkHRActivFlag" <?=(isset($_POST['chkHRActivFlag']) && $_POST['chkHRActivFlag']=='1'?'checked':'')?> value="1"></td>
			  </tr>
			  <tr>
			  <td>Work Hours</td>
			  <td><input type="text" name="txtWorkHours" <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($_POST['txtWorkHours']))?$_POST['txtWorkHours']:''?>"></td>
			  <td width="50">&nbsp;</td>
			  <td>Payroll Activ</td>
			  <td><input type="checkbox" <?=$locRights['add'] ? '':'disabled'?> name="txtPayActivFlag" <?=(isset($_POST['txtPayActivFlag']) && $_POST['txtPayActivFlag']=='1'?'checked':'')?> value="1"></td>
			  </tr>
			  <tr>
			  <td>Job Preference</td>
			  <td><input type="text" name="txtJobPref" <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($_POST['txtJobPref']))?$_POST['txtJobPref']:''?>"></td>
			  <td width="50">&nbsp;</td>
			  <td>Time &amp; Attendance Active</td>
			  <td><input type="checkbox" <?=$locRights['add'] ? '':'disabled'?> name="chkTimeAttActivFlag" <?=(isset($_POST['chkTimeAttActivFlag']) && $_POST['chkTimeAttActivFlag']=='1'?'checked':'')?> value="1"></td>
			  </tr>          </table></td>
          <td background="themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
    </div>
	<div id="workstation" style="position:absolute; z-index:2; width: 540px; visibility: hidden; left: 100px; top: 360px;">
	  <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="13"><img name="table_r1_c1" src="themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
<?
$hierarchy=$parent_empinfo->getHierarchyDef();
if($hierarchy)
   $noFields=count($hierarchy);
else
	$noFields=0;
		
?>        
          <td background="themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><table height="<?=30+$noFields*25?>" border="0" cellpadding="0" cellspacing="0">
				<tr>
				<td>Location</td>
				<td><select <?=$locRights['add'] ? '':'disabled'?> name="cmbLocation">
						<option value="0">--Select Location--</option>
<?						$loc=$parent_empinfo->getLocations();
						for($c=0;$loc && count($loc)>$c;$c++)
						    if(isset($_POST['cmbLocation']) && $_POST['cmbLocation']==$loc[$c][0])
							    echo "<option selected value'" . $loc[$c][0] . "'>" .$loc[$c][1] . "</option>";
							else							    
							    echo "<option value'" . $loc[$c][0] . "'>" .$loc[$c][1] . "</option>";
							    
		for($c=0;$noFields>$c;$c++) { 
			if($c==0) {
				$res=$parent_empinfo->getCompHier($hierarchy[$c][0],0);
			} elseif (isset($_POST["hierarchy".($c-1)])) {
				$arr[0]=$hierarchy[$c][0];
				$arr[1]=$_POST["hierarchy".($c-1)];
				$res=$parent_empinfo->getCompHier($arr,1);
			}
			
	?>
          <tr>
                <td><?=$hierarchy[$c][1]?><td>
                <td><select onchange="hierChg(this);" <?=$locRights['add'] ? '':'disabled'?> name="hierarchy<?=$c?>">
                		<option value="0">--Select--</option>
<?
			if($res) {
					for($a=0;count($res)>$a;$a++)
					    if(isset($_POST["hierarchy".$c]) && $res[$a][2]==$_POST["hierarchy".$c])
				   			echo "<option selected value='" .$res[$a][2] . "'>"  .$res[$a][3] . "</option>";
				   		else
				   			echo "<option value='" .$res[$a][2] . "'>"  .$res[$a][3] . "</option>";


           } ?>
          </td></tr> 
	<?	} ?>
<input type="hidden" name="txtHiCode" value="<?=isset($_POST['txtHiCode']) ? $_POST['txtHiCode'] : '0'?>">
          </table></td>
          <td background="themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
    </div>
	<div id="tax" style="position:absolute; z-index:2; width: 540px; visibility: hidden; left: 100px; top: 360px;">
	  <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="13"><img name="table_r1_c1" src="themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><table onclick='setUpdate(4)' onkeypress='setUpdate(4)' height="250" border="0" cellpadding="0" cellspacing="0">
          <tr>
          <td valign="top">Select Country</td>
          <td valign="top"><select onChange="taxCountry()" <?=$locRights['add'] ? '':'disabled'?> name="cmbTaxCountry">
          		<option value="0">--Select--</option>
<?				$list=$tax->getCountryCodes();
					for($c=0;$list && count($list)>$c;$c++)
					if(isset($_POST['cmbTaxCountry']) && $_POST['cmbTaxCountry']==$list[$c][0])
					    echo "<option selected value='". $list[$c][0]."'>" . $list[$c][1]. "</option>";
					else
					    echo "<option value='". $list[$c][0]."'>" . $list[$c][1]. "</option>";
?>          	
				</select></td>
				</tr>
			<input type="hidden" name="skipTax" value="<?=isset($_POST['skipTax']) ? $_POST['skipTax'] : ''?>">

<?		if(isset($_POST['skipTax']) && $_POST['skipTax']=='1') {
?>
				<tr>
				<td>Tax</td>
          <td><select name="cmbTaxID">
          		<option value="0">----Select Tax-----</option>
<?				$list=$tax->getTaxCodes();
					for($c=0;$list && count($list)>$c;$c++)
					if(isset($_POST['cmbTaxID']) && $_POST['cmbTaxID']==$list[$c][0])
					    echo "<option selected value='" . $list[$c][0] . "'>" . $list[$c][1]. "</option>";
					else
					    echo "<option value='" . $list[$c][0] . "'>" . $list[$c][1]. "</option>";
?>          	
				</select></td>
				</tr>

          <tr>
          <td>Federal<input type="radio" checked onclick="setFed();" name="optFedStateFlag" value="1"></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>State<input type="radio" onclick="setState();" name="optFedStateFlag" <?=(isset($_POST['optFedStateFlag']) && $_POST['optFedStateFlag']!=1)?'checked':''?> value="2"></td>
          <td>&nbsp;</td>
          </tr>
          <tr>
          <td>Filing Status</td>
          <td><select name="cmbFedTaxFillStat">
			 		<option>--Select Filing Status--</option>
<?			for($c=0;count($arrFillStat)>$c;$c++)
				if(isset($_POST['cmbFedTaxFillStat']) && $_POST['cmbFedTaxFillStat']==$arrFillStat[$c])
          		   echo "<option selected>" .$arrFillStat[$c] . "</option>";
          		else
          		   echo "<option>" .$arrFillStat[$c] . "</option>";
?>          		
          </select></td>
          <td>&nbsp;</td>
          <td>Filing Status</td>
          <td><select disabled name="cmbStateTaxFillStat">
			 		<option>--Select Filing Status--</option>
<?			for($c=0;count($arrFillStat)>$c;$c++)
				if(isset($_POST['cmbStateTaxFillStat']) && $_POST['cmbStateTaxFillStat']==$arrFillStat[$c])
          		   echo "<option selected>" .$arrFillStat[$c] . "</option>";
          		else
          		   echo "<option>" .$arrFillStat[$c] . "</option>";
?>          		
          </select></td>
          </tr>
          <tr>
          <td>Allowances</td>
          <td><input type="text" name="txtFedTaxAllowance" value="<?=isset($_POST['txtFedTaxAllowance'])?$_POST['txtFedTaxAllowance']:''?>"></td>
          <td>&nbsp;</td>
          <td>Taxed State</td>
          <td><select disabled name="cmbStateTaxState">
			 		<option value="0">--Select State--</option>
<?			if(isset($_POST['cmbTaxCountry'])) {
				$plist=$tax->getProvinceCodes($_POST['cmbTaxCountry']);
				for($c=0;$plist && count($plist)>$c;$c++)
					if(isset($_POST['cmbStateTaxState']) && $_POST['cmbStateTaxState']==$plist[$c][1])
					    echo "<option selected value='" . $plist[$c][1]. "'>" . $plist[$c][2]. "</option>";
					else
					    echo "<option value='" . $plist[$c][1]. "'>" . $plist[$c][2]. "</option>";
				}
?>
			</select></td>
			</tr>
			<tr>
			<td>Extra</td>
			<td><input type="text" name="txtFedTaxExtra" value="<?=isset($_POST['txtFedTaxExtra'])?$_POST['txtFedTaxExtra']:''?>"></td>
			<td>&nbsp;</td>
			<td>Allowances</td>
            <td><input type="text" name="txtStateTaxAllowance" disabled value="<?=isset($_POST['txtStateTaxAllowance'])?$_POST['txtStateTaxAllowance']:''?>"></td>
            </tr>
            <tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>Extra</td>
			<td><input type="text" name="txtStateTaxExtra" disabled value="<?=isset($_POST['txtStateTaxExtra'])?$_POST['txtStateTaxExtra']:''?>"></td>
			</tr>
<? } elseif(isset($_POST['skipTax']) && $_POST['skipTax']=='0')  { ?>            

            <tr>
			  	<td>Tax Exempt</td>
				<td><select name="cmbTaxExempt">
			  		<option value="0">--Select Exemption Type--</option>
<?				
				for($c=0;count($arrTaxExempt)>$c;$c++)
					if(isset($_POST['cmbTaxExempt']) && $_POST['cmbTaxExempt']==$arrTaxExempt[$c])
					    echo "<option selected>" .$arrTaxExempt[$c] . "</option>";
					else
					    echo "<option>" .$arrTaxExempt[$c] . "</option>";
?>
				</select></td>
				<td width="50">&nbsp;</td>
				<td>ETF Eligibility</td>
				<td><input type="checkbox" value="1" <?=(isset($_POST['chkETFEligibleFlag']) && $_POST['chkETFEligibleFlag']=='1'?'checked':'')?> name="chkETFEligibleFlag"></td>
              </tr>
			  <tr>
			  <td>Tax on Tax</td>
			  <td><input type="checkbox" value="1" <?=(isset($_POST['chkTaxOnTaxFlag']) && $_POST['chkTaxOnTaxFlag']=='1'?'checked':'')?> name="chkTaxOnTaxFlag"></td>
			  <td width="50">&nbsp;</td>
			  <td>ETF No.</td>
			  <td><input type="text" name="txtETFNo" value="<?=(isset($_POST['txtETFNo']))?$_POST['txtETFNo']:''?>"></td>
			  </tr>
			  <tr>
			  <td>Tax ID</td>
			  <td><input type="text" name="txtTaxID" value="<?=(isset($_POST['txtTaxID']))?$_POST['txtTaxID']:''?>"></td>
			  <td width="50">&nbsp;</td>
			  <td>Employee %</td>
			  <td><input type="text" name="txtETFEmployeePercen" value="<?=(isset($_POST['txtETFEmployeePercen']))?$_POST['txtETFEmployeePercen']:''?>"></td>
			  </tr>
			  <tr>
			  <td>EPF Eligible</td>
			  <td><input type="checkbox" value="1" <?=(isset($_POST['chkEPFEligibleFlag']) && $_POST['chkEPFEligibleFlag']=='1'?'checked':'')?> name="chkEPFEligibleFlag"></td>
			  <td width="50">&nbsp;</td>
			  <td>ETF Date</td>
			  <td><input type="text" name="txtETFDat" readonly value=<?=(isset($_POST['txtETFDat']))?$_POST['txtETFDat']:''?>>&nbsp;<input type="button" <?=$locRights['add'] ? '':'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtETFDat);return false;"></td>
			  </tr>
			  <tr>
			  <td>EPF No.</td>
			  <td><input type="text" name="txtEPFNo" value="<?=(isset($_POST['txtEPFNo']))?$_POST['txtEPFNo']:''?>"></td>
			  <td width="50">&nbsp;</td>
			  <td>MSPS</td>
			  <td><input type="checkbox" name="chkMSPSEligibleFlag" <?=(isset($_POST['chkMSPSEligibleFlag']) && $_POST['chkMSPSEligibleFlag']=='1'?'checked':'')?> value="1"></td>
			  </tr>
			  <tr>
			  <td>Company EPF fund</td><td><input type="radio" name="optCFundCBFundFlag" checked value="1"></td>
			  <td width="50">&nbsp;</td>
			  <tr>
			  <td>Central Bank EPF fund</td><td><input type="radio" name="optCFundCBFundFlag" <?=(isset($_POST['optCFundCBFundFlag']) && isset($_POST['optCFundCBFundFlag'])!=1)?'checked':''?> value="0"></td>
			  <td width="50">&nbsp;</td>
			  <td>Employee %</td>
			  <td><input type="text" name="txtMSPSEmployeePercen" value="<?=(isset($_POST['txtMSPSEmployeePercen']))?$_POST['txtMSPSEmployeePercen']:''?>"></td>
			  </tr>
			  <tr>
			  <td>Employee %</td>
			  <td><input type="text" name="txtEPFEmployeePercen" value="<?=(isset($_POST['txtEPFEmployeePercen']))?$_POST['txtEPFEmployeePercen']:''?>"></td>
			  <td width="50">&nbsp;</td>
			  <td>Employer %</td>
			  <td><input type="text" name="txtMSPSEmployerPercen" value="<?=(isset($_POST['txtMSPSEmployerPercen']))?$_POST['txtMSPSEmployerPercen']:''?>"></td>
			  </tr>
			  <tr>
			  <td>Employer %</td>
			  <td><input type="text" name="txtEPFEmployerPercen" value="<?=(isset($_POST['txtEPFEmployerPercen']))?$_POST['txtEPFEmployerPercen']:''?>"></td>
			  </tr>
<? } ?>
          </table></td>
          <td background="themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
    </div>
	<div id="contact" style="position:absolute; z-index:2; width: 540px; visibility: hidden; left: 100px; top: 360px;">
	  <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="13"><img name="table_r1_c1" src="themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><table height="250" border="0" cellpadding="0" cellspacing="0">
<tr>
			  <td>House No.</td>
			  <td><input type="text" name="txtPermHouseNo" <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($_POST['txtPermHouseNo']))?$_POST['txtPermHouseNo']:''?>"></td>
			  <td width="50">&nbsp;</td>
			  <td>Fax</td>
			  <td><input type="text" name="txtPermFax" <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($_POST['txtPermFax']))?$_POST['txtPermFax']:''?>"></td>
             </tr>
			 <tr>
			 <td>Street 1</td>
			 <td><input type="text" name="txtPermStreet1" <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($_POST['txtPermStreet1']))?$_POST['txtPermStreet1']:''?>"></td>
			 <td width="50">&nbsp;</td>
			 <td>Email</td>
			 <td><input type="text" name="txtPermEmail" <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($_POST['txtPermEmail']))?$_POST['txtPermEmail']:''?>"></td>
			 </tr>
			 <tr>
			 <td>Street 2</td>
			 <td><input type="text" name="txtPermStreet2" <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($_POST['txtPermStreet2']))?$_POST['txtPermStreet2']:''?>"></td>
			 <td width="50">&nbsp;</td>
			 <td>Country</td>
			 <td><select onChange="document.frmEmp.cmbPermDistrict.options[0].selected=true,qCombo(5);" <?=$locRights['add'] ? '':'disabled'?> name="cmbPermCountry">
			 		<option value="0">----Select Country----</option>
<?				$list=$parent_empinfo->getCountryCodes();
				for($c=0;$list && count($list)>$c;$c++)
					if(isset($_POST['cmbPermCountry']) && $_POST['cmbPermCountry']==$list[$c][0])
					    echo "<option selected value='" . $list[$c][0] . "'>" . $list[$c][1]. "</option>";
					else
					    echo "<option value='" . $list[$c][0] . "'>" . $list[$c][1]. "</option>";
?>			 
			 </select></td>
			 </tr>
			 <tr>
			 <td>City/Town</td>
			 <td><input type="text" name="txtPermCityTown" <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($_POST['txtPermCityTown']))?$_POST['txtPermCityTown']:''?>"></td>
			 <td width="50">&nbsp;</td>
			 <td>State</td>
			 <td><select onChange="qCombo(5)" <?=$locRights['add'] ? '':'disabled'?> name="cmbPermProvince">
			 		<option value="0">--Select State--</option>
<?			if(isset($_POST['cmbPermCountry'])) {
				$plist=$parent_empinfo->getProvinceCodes($_POST['cmbPermCountry']);
				for($c=0;$plist && count($plist)>$c;$c++)
					if(isset($_POST['cmbPermProvince']) && $_POST['cmbPermProvince']==$plist[$c][1])
					    echo "<option selected value='" . $plist[$c][1]. "'>" . $plist[$c][2]. "</option>";
					else
					    echo "<option value='" . $plist[$c][1]. "'>" . $plist[$c][2]. "</option>";
				}
?>
			 </select></td>
			 </tr>
			 <tr>
			 <td>ZIP Code</td>
			 <td><input type="text" name="txtPermPostCode" <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($_POST['txtPermPostCode']))?$_POST['txtPermPostCode']:''?>"></td>
			 <td width="50">&nbsp;</td>
			 <td>County</td>
			 <td><select <?=$locRights['add'] ? '':'disabled'?> name="cmbPermDistrict">
			 		<option value="0">---Select County---</option>
<?			if(isset($_POST['cmbPermProvince'])) {
				$dlist=$parent_empinfo->getDistrictCodes($_POST['cmbPermProvince']);
				for($c=0;$dlist && count($dlist)>$c;$c++)
					if(isset($_POST['cmbPermDistrict']) && $_POST['cmbPermDistrict']==$dlist[$c][1])
					    echo "<option selected value='" . $dlist[$c][1]. "'>" . $dlist[$c][2]. "</option>";
					else
					    echo "<option value='" . $dlist[$c][1]. "'>" . $dlist[$c][2]. "</option>";
				}
?>
			 </select></td>
			 </tr>
			 <tr>
			 <td>Telephone</td>
			 <td><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtPermTelep" value="<?=(isset($_POST['txtPermTelep']))?$_POST['txtPermTelep']:''?>"></td>
			 <td width="50">&nbsp;</td>
			 <td>Electorate</td>
			 <td><select <?=$locRights['add'] ? '':'disabled'?> name="cmbPermElectorate">
			 		<option value="0">---Select Electorate---</option>
<?
			$elelist=$parent_empinfo->getElectorateCodes();
			for($c=0;$elelist && count($elelist)>$c;$c++)
				if(isset($_POST['cmbPermElectorate']) && $_POST['cmbPermElectorate']==$elelist[$c][0])
				    echo "<option selected value='" . $elelist[$c][0] . "'>" .$elelist[$c][1]. "</option>";
				else
				    echo "<option value='" . $elelist[$c][0] . "'>" .$elelist[$c][1]. "</option>";
?>
			 
			 </select></td>
			 </tr>
			 <tr>
			 <td>Mobile</td>
			 <td><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtPermMobile" value="<?=(isset($_POST['txtPermMobile']))?$_POST['txtPermMobile']:''?>"></td>
			 </tr>
			 </table></td>
          <td background="themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
    </div>
	<div id="passport" style="position:absolute; z-index:2; width: 540px; visibility: hidden; left: 100px; top: 360px;">
	  <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="13"><img name="table_r1_c1" src="themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><table onclick="setUpdate(6)" onkeypress="setUpdate(6)" height="200" border="0" cellpadding="0" cellspacing="0">
				<tr>
			  <td>Passport <input type="radio" checked <?=$locRights['add'] ? '':'disabled'?> name="PPType" value="1"></td><td> Visa <input type="radio" <?=$locRights['add'] ? '':'disabled'?> <?=(isset($_POST['PPType']) && $_POST['PPType']!='1') ? 'checked':''?> name="PPType" value="2"></td>
			  <td width="50">&nbsp;</td>
		  	  <td>Issued Place</td>
		  	  <td><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtPPIssPlace" value="<?=isset($_POST['txtPPIssPlace']) ? $_POST['txtPPIssPlace'] : ''?>"></td>
              </tr>
              <tr>
                <td>Passport/Visa No</td>
                <td><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtPPNo" value="<?=isset($_POST['txtPPNo']) ? $_POST['txtPPNo'] : ''?>"></td>
                <td width="50">&nbsp;</td>
                <td>Issued Date</td>
                <td><input type="text" readonly name="txtPPIssDat" value=<?=isset($_POST['txtPPIssDat']) ? $_POST['txtPPIssDat'] : ''?>>&nbsp;<input type="button" <?=$locRights['add'] ? '':'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtPPIssDat);return false;"></td>
              </tr>
              <tr>
                <td>Passport/Visa Type</td>
                <td><input name="txtVisaType" <?=$locRights['add'] ? '':'disabled'?> type="text" value="<?=isset($_POST['txtVisaType']) ? $_POST['txtVisaType'] : ''?>">
                <td width="50">&nbsp;</td>
                <td>Date of Expiry</td>
                <td><input type="text" readonly name="txtPPExpDat" value=<?=isset($_POST['txtPPExpDat']) ? $_POST['txtPPExpDat'] : ''?>>&nbsp;<input type="button" <?=$locRights['add'] ? '':'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtPPExpDat);return false;"></td>
              </tr>
              <tr>
                <td>Country</td>
                <td><select <?=$locRights['add'] ? '':'disabled'?> name="cmbPPCountry">
                		<option value="0">-Select Countr-</option>
<?				$list=$parent_empinfo->getCountryCodes();
				for($c=0;$list && count($list)>$c;$c++)
					if(isset($_POST['cmbPPCountry']) && $_POST['cmbPPCountry']==$list[$c][0])
					    echo "<option selected value='" . $list[$c][0] . "'>" . $list[$c][1]. "</option>";
					else
					    echo "<option value='" . $list[$c][0] . "'>" . $list[$c][1]. "</option>";
?>			 
				</td>
				<td width="50">&nbsp;</td>
				<td>Comments</td>
				<td><textarea <?=$locRights['add'] ? '':'disabled'?> name="PPComment"><?=isset($_POST['PPComment']) ? $_POST['PPComment'] : ''?></textarea></td>
				<tr>
				  <td>No of Entries</td>
				  <td><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtPPNoOfEntries" <?=isset($_POST['txtPPNoOfEntries']) ? $_POST['txtPPNoOfEntries'] : ''?>></td>
				  <td width="50">&nbsp;</td>
				  <td width="50">&nbsp;</td>
				</tr>
          </table></td>
          <td background="themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
    </div>
	<div id="bank" style="position:absolute; z-index:2; width: 540px; visibility: hidden; left: 100px; top: 360px;">
	  <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="13"><img name="table_r1_c1" src="themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><table onclick="setUpdate(7)" onkeypress="setUpdate(7)" height="200" border="0" cellpadding="0" cellspacing="0">
          <tr>
				<td>Bank Name</td>
				<td><select onchange="qCombo(7)" <?=$locRights['add'] ? '':'disabled'?> name="cmbBank">
				<option value="0">---Select Bank---</option>
<?				$banklist=$bankacc->getBankCodes();
				for($c=0;$banklist && $c<count($banklist);$c++)
					if(isset($_POST['cmbBank']) && $_POST['cmbBank']==$banklist[$c][0])
					   echo "<option selected value='" . $banklist[$c][0]. "'>" . $banklist[$c][1]. "</option>";
					else
					   echo "<option value='" . $banklist[$c][0]. "'>" . $banklist[$c][1]. "</option>";
?>				
				</td>
				<td width="50">&nbsp;</td>
				<td>Account Type</td>
				<td><input type="radio" <?=$locRights['add'] ? '':'disabled'?> name="optAccType" checked value="1">Current</td><td><input type="radio" <?=(isset($_POST['optAccType']) && $_POST['optAccType']!='1') ? 'checked':''?> name="optAccType" <?=$locRights['add'] ? '':'disabled'?> value="2">Savings</td>
              </tr>
              <tr>
                <td>Branch Name</td>
                <td><select <?=$locRights['add'] ? '':'disabled'?> name="cmbBranchCode">
				<option value="0">---Select Branch---</option>
<?				if(isset($_POST['cmbBank'])) {
					$brchlist=$bankacc->getBranchCodes($_POST['cmbBank']);
					for($c=0;$brchlist && count($brchlist)>$c;$c++)
					    if($_POST['cmbBranchCode']==$brchlist[$c][1])
					   		echo "<option selected value='" . $brchlist[$c][1]. "'>" . $brchlist[$c][2]. "</option>";
					   	else
					   		echo "<option value='" . $brchlist[$c][1]. "'>" . $brchlist[$c][2]. "</option>";
					}
?>                
				</td>
				<td width="50">&nbsp;</td>
				<td>Amount</td>
				<td><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="Amount" value="<?=isset($_POST['Amount'])?$_POST['Amount']:''?>"></td>
			<tr>
				<td>Account No</td>
				<td><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="AccNo" value="<?=isset($_POST['AccNo'])?$_POST['AccNo']:''?>"></td>
				<td width="50">&nbsp;</td>
			</tr>
          </table></td>
          <td background="themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
    </div>
	<div id="attachment" style="position:absolute; z-index:2; width: 540px; visibility: hidden; left: 100px; top: 360px;">
	  <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="13"><img name="table_r1_c1" src="themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><table onclick="setUpdate(8)" onkeypress="setUpdate(8)" width="352" height="200" border="0" cellpadding="0" cellspacing="0">
              <tr>
				<td>Path</td>
				<td><input type="file" name="ufile"></td>
              </tr>
              <tr>
              	<td>Description</td>
              	<td><textarea name="txtAttDesc"></textarea></td>
              </tr>
          </table></td>
          <td background="themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
    </div>
	<div id="other" style="position:absolute; z-index:2; width: 540px; visibility: hidden; left: 100px; top: 360px;">
	  <table border="0" cellpadding="0" cellspacing="0" width="550">
        <tr>
          <td width="13"><img name="table_r1_c1" src="themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><table width="352" height="200" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td height="18"><img name="table_r3_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt="">Other</td>
              </tr>
          </table></td>
          <td background="themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
    </div>
	</td>
  </tr>
</table>
            </td>
          </tr>
    </table>
    </form>
<iframe width=174 height=189 name="gToday:normal:agenda.js" id="gToday:normal:agenda.js" src="scripts/ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; top:-500px; left:-500px;">
</iframe>
</body>
</html>

<? } elseif ((isset($_GET['capturemode'])) && ($_GET['capturemode'] == 'updatemode')) { 
	$edit = $parent_empinfo ->filterEmpMain($_GET['id']);
?>

<form name="frmEmp" method="post" action="./hremp.php?pageID=<?=$_GET['pageID']?>&id=<?=$_GET['id']?>&reqcode=<?=$_GET['reqcode']?>&capturemode=<?=$_GET['capturemode']?>" enctype="multipart/form-data">
<input type="hidden" name="sqlState">
<input type="hidden" name="pane" value="<?=(isset($_POST['pane']) && $_POST['pane']!='')?$_POST['pane']:''?>">

<input type="hidden" name="main" value="<?=isset($_POST['main'])? $_POST['main'] : '0'?>">
<input type="hidden" name="personalFlag" value="<?=isset($_POST['personalFlag'])? $_POST['personalFlag'] : '0'?>">
<input type="hidden" name="jobFlag" value="<?=isset($_POST['jobFlag'])? $_POST['jobFlag'] : '0'?>">
<input type="hidden" name="workstationFlag" value="<?=isset($_POST['workstationFlag'])? $_POST['workstationFlag'] : '0'?>">
<input type="hidden" name="taxFlag" value="<?=isset($_POST['taxFlag'])? $_POST['taxFlag'] : '0'?>">
<input type="hidden" name="contactFlag" value="<?=isset($_POST['contactFlag'])? $_POST['contactFlag'] : '0'?>">
<input type="hidden" name="attSTAT" value="">
<input type="hidden" name="EditMode" value="<?=isset($_POST['EditMode'])? $_POST['EditMode'] : '0'?>">

			<table width="550" align="center" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="13"><img name="table_r1_c1" src="themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="339" background="themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td width="13"><img name="table_r1_c3" src="themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="11"><img src="themes/beyondT/pictures/spacer.gif" width="1" height="12" border="0" alt=""></td>
                </tr>
                <tr>
                  <td background="themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><table onclick="setUpdate(0)" onkeypress="setUpdate(0)" width="100%" border="0" cellpadding="5" cellspacing="0" class="">
			  <tr> 
				<td>Code</td>
				<td> <input type="text" readonly="true" name="txtEmpID" value="<?=$_GET['id']?>"></td>
			  </tr>
			  <tr>
				<td>Title</td>
				<td> <select <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> name="cmbEmpTitle">
				<?      
					for($c=0;$c < count($arrTitle);$c++)
						if($edit[0][1]==$arrTitle[$c])
							echo '<option selected value=' . $arrTitle[$c] . '>' . $arrTitle[$c] .'</option>';
						else
							echo '<option value=' . $arrTitle[$c] . '>' . $arrTitle[$c] .'</option>';
				?>
				</select></td>
			  </tr>
			  <tr> 
				<td>Calling Name</td>
				<td> <input type="text" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> name="txtEmpCallName" value="<?=(isset($_POST['txtEmpCallName']))?$_POST['txtEmpCallName']:$edit[0][2]?>"></td>
				<td>&nbsp;</td>
				<td>Surname</td>
				<td> <input type="text" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> name="txtEmpSurname" value="<?=(isset($_POST['txtEmpSurname']))?$_POST['txtEmpSurname']:$edit[0][3]?>"></td>
			  </tr>
			  <tr> 
				<td>Maiden Name</td>
				<td> <input type="text" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> name="txtEmpMaidenName" value="<?=(isset($_POST['txtEmpMaidenName']))?$_POST['txtEmpMaidenName']:$edit[0][4]?>"></td>
				<td>&nbsp;</td>
			  <td>Initials</td>
				<td> <input type="text" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> name="txtEmpInitials" value="<?=(isset($_POST['txtEmpInitials']))?$_POST['txtEmpInitials']:$edit[0][5]?>"></td>
			  </tr>
			  <tr>
				<td>Names By Initials</td>
				<td> <input type="text" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> name='txtEmpNamByIni' value="<?=(isset($_POST['txtEmpNamByIni']))?$_POST['txtEmpNamByIni']:$edit[0][6]?>"></td>
			  <td>&nbsp;</td>
				<td>Full Name</td>
				<td> <input type="text" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> name='txtEmpFullName' value="<?=(isset($_POST['txtEmpFullName']))?$_POST['txtEmpFullName']:$edit[0][7]?>"></td>
			  </tr>
				<tr>
				<td>Other Names</td>
				<td><input type="text" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> name='txtEmpOtherName' value="<?=(isset($_POST['txtEmpOtherName']))?$_POST['txtEmpOtherName']:$edit[0][8]?>"></td>
			  </tr>
                  </table></td>
                  <td background="themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                </tr>
                <tr>
                  <td><img name="table_r3_c1" src="themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
                  <td background="themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img name="table_r3_c3" src="themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
                  <td><img src="themes/beyondT/pictures/spacer.gif" width="1" height="16" border="0" alt=""></td>
                </tr>
              </table>

    <table border="0" align="center" >
    <tr>
    <td><img title="Back" onmouseout="this.src='./themes/beyondT/pictures/btn_back.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_back_02.jpg';"  src="./themes/beyondT/pictures/btn_back.jpg" onclick="goBack();"></td>
    <td>
<?			if($locRights['edit']) { ?>
			        <img src="<?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? './themes/beyondT/pictures/btn_save.jpg' : './themes/beyondT/pictures/btn_edit.jpg'?>" title="Edit" onmouseout="mout();" onmouseover="mover();" name="Edit" onClick="edit();">
<?			} else { ?>
			        <img src="./themes/beyondT/pictures/btn_edit.jpg" onClick="alert('<?=$sysConst->accessDenied?>');">
<?			}  ?>
    </td>
    <td><img src="./themes/beyondT/pictures/btn_clear.jpg" onmouseout="this.src='./themes/beyondT/pictures/btn_clear.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_clear_02.jpg';" onClick="reLoad();" ></td>
    </tr>
    </table>


<table width="600" align="center" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><table border="0" cellpadding="0" cellspacing="0" width="600">
        <tr>
          <td><img name="tabs_r1_c1" src="themes/beyondT/pictures/tabs_r1_c1.jpg" width="28" height="71" border="0" alt=""></td>
          <td><a href="#" onClick="MM_showHideLayers('hidebg','','hide','personal','','show','job','','hide','workstation','','hide','tax','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','hide')">
		  	  <img name="tabs_r1_c2" src="themes/beyondT/pictures/tabs_r1_c2.jpg" width="62" height="71" border="0" alt=""></a></td>
          <td><a href="#" onClick="MM_showHideLayers('hidebg','','hide','personal','','hide','job','','show','workstation','','hide','tax','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','hide')">
		      <img name="tabs_r1_c3" src="themes/beyondT/pictures/tabs_r1_c3.jpg" width="56" height="71" border="0" alt=""></a></td>
         <td><a href="#" onClick="MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','show','tax','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','hide')">
		      <img name="tabs_r1_c4" src="themes/beyondT/pictures/tabs_r1_c4.jpg" width="62" height="71" border="0" alt=""></a></td> 
          <td><a href="#" onClick="MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','hide','tax','','show','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','hide')">
		      <img name="tabs_r1_c5" src="themes/beyondT/pictures/tabs_r1_c5.jpg" width="60" height="71" border="0" alt=""></a></td>
          <td><a href="#" onClick="MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','hide','tax','','hide','contact','','show','passport','','hide','bank','','hide','attachment','','hide','other','','hide')">
		      <img name="tabs_r1_c6" src="themes/beyondT/pictures/tabs_r1_c6.jpg" width="59" height="71" border="0" alt=""></a></td>
          <td><a href="#" onClick="MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','hide','tax','','hide','contact','','hide','passport','','show','bank','','hide','attachment','','hide','other','','hide')">
		  	  <img name="tabs_r1_c7" src="themes/beyondT/pictures/tabs_r1_c7.jpg" width="61" height="71" border="0" alt=""></a></td>
          <td><a href="#" onClick="MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','hide','tax','','hide','contact','','hide','passport','','hide','bank','','show','attachment','','hide','other','','hide')">
		      <img name="tabs_r1_c8" src="themes/beyondT/pictures/tabs_r1_c8.jpg" width="60" height="71" border="0" alt=""></a></td>
          <td><a href="#" onClick="MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','hide','tax','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','show','other','','hide')">
		      <img name="tabs_r1_c9" src="themes/beyondT/pictures/tabs_r1_c9.jpg" width="60" height="71" border="0" alt=""></a></td>
<!--          <td><a href="#" onClick="MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','hide','tax','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','show')">
		      <img name="tabs_r1_c10" src="themes/beyondT/pictures/tabs_r1_c10.jpg" width="54" height="71" border="0" alt=""></a></td>-->
          <td><img name="tabs_r1_c11" src="themes/beyondT/pictures/tabs_r1_c11.jpg" width="38" height="71" border="0" alt=""></td>
        </tr>
        <tr>
          <td colspan="11"><img name="tabs_r2_c1" src="themes/beyondT/pictures/tabs_r2_c1.jpg" width="600" height="17" border="0" alt=""></td>
          <td><img src="images/spacer.gif" width="1" height="17" border="0" alt=""></td>
        </tr>
        <tr>
          <td colspan="11"><img name="tabs_r3_c1" src="themes/beyondT/pictures/tabs_r3_c1.jpg" width="600" height="27" border="0" alt=""></td>
          <td><img src="images/spacer.gif" width="1" height="27" border="0" alt=""></td>
        </tr>
    </table></td>
  </tr>
  <tr>
    <td align="center">
    <div id="personal" style="position:absolute; z-index:2; width: 540px; visibility: hidden; left: 100px; top: 360px;">
	  <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="13"><img name="table_r1_c1" src="themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><table onclick="setUpdate(1)" onkeypress="setUpdate(1)" height="200" border="0" cellpadding="0" cellspacing="0">
<?
		  $edit=$parent_empinfo->filterEmpPers($_GET['id']);
?>

          <tr>
					<td>SSN:</td>
					<td><input type="text" name="txtNICNo" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> value="<?=(isset($_POST['txtNICNo']))?$_POST['txtNICNo']:$edit[0][1]?>"></td>
					<td width="50">&nbsp;</td>
					<td>Nationality</td>
					<td><select <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> name="cmbNation">
						<option value="0">--Select Nationality--</option>
<?
					$nation=$parent_empinfo ->getNationCodes();
					for($c=0;$nation && count($nation)>$c;$c++)
						if(isset($_POST['cmbNation'])) {
							if($_POST['cmbNation']==$nation[$c][0])
							    echo "<option selected value='" . $nation[$c][0] . "'>" .$nation[$c][1]. "</option>";
							else
							    echo "<option value='" . $nation[$c][0] . "'>" .$nation[$c][1]. "</option>";
						} elseif($edit[0][7]==$nation[$c][0]) 
							    echo "<option selected value='" . $nation[$c][0] . "'>" .$nation[$c][1]. "</option>";
							else
							    echo "<option value='" . $nation[$c][0] . "'>" .$nation[$c][1]. "</option>";
						
?>					
					</select></td>
				</tr>
				<tr>
				<td>SSN Issued Date</td>
				<td><input type="text" name="txtNICDate" readonly value=<?=(isset($_POST['txtNICDate']))?$_POST['txtNICDate']:$edit[0][2]?>>&nbsp;<input type="button" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtNICDate);return false;"></td>
				<td>&nbsp;</td>
				<td>Religion</td>
				<td><select <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> name="cmbReligion">
						<option value="0">---Select Religion---</option>
<?
					$rel=$parent_empinfo ->getReligionCodes();
					for($c=0;$rel && count($rel)>$c;$c++)
						if(isset($_POST['cmbReligion'])) {
							if($_POST['cmbReligion']==$rel[$c][0])
						    echo "<option selected value='" . $rel[$c][0] . "'>" .$rel[$c][1]. "</option>";
					    else
						    echo "<option value='" . $rel[$c][0] . "'>" .$rel[$c][1]. "</option>";
						} elseif($edit[0][8]==$rel[$c][0])
								    echo "<option selected value='" . $rel[$c][0] . "'>" .$rel[$c][1]. "</option>";
							    else
								    echo "<option value='" . $rel[$c][0] . "'>" .$rel[$c][1]. "</option>";
?>					
				</select></td>
				
				</tr>
				<tr>
				<td>Date of Birth</td>
				<td><input type="text" readonly name="DOB" value=<?=(isset($_POST['DOB']))?$_POST['DOB']:$edit[0][3]?>>&nbsp;<input type="button" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.DOB);return false;"></td>
				<td>&nbsp;</td>
				<td>Blood Group</td>
				<td><select <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> name="cmbBloodGrp">
<?
					for($c=0;count($arrBGroup)>$c;$c++)
						if(isset($_POST['cmbBloodGrp'])) {
						   if($_POST['cmbBloodGrp']==$arrBGroup[$c])
							echo "<option selected>" .$arrBGroup[$c]. "</option>";
						else 
							echo "<option>" .$arrBGroup[$c]. "</option>";
						} elseif($edit[0][6]==$arrBGroup[$c])
									echo "<option selected>" .$arrBGroup[$c]. "</option>";
								else 
									echo "<option>" .$arrBGroup[$c]. "</option>";
?>
				</select></td>
				</tr>
				<tr>
				<td>Place of Birth</td>
				<td><input type="text" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> name="txtBirthPlace" value="<?=(isset($_POST['txtBirthPlace']))?$_POST['txtBirthPlace']:$edit[0][4]?>"></td>
				<td>&nbsp;</td>
				<td>Marital Status</td>
				<td><select <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> name="cmbMarital">
					<option value="0">--Select--</option>
<?					
					for($c=0;count($arrMStat)>$c;$c++)
						if(isset($_POST['cmbMarital'])) {
						 	if($_POST['cmbMarital']==$arrMStat[$c])
						    echo "<option selected>" .$arrMStat[$c]."</option>";
						else 
						    echo "<option>" .$arrMStat[$c]."</option>";
						} elseif($edit[0][9]==$arrMStat[$c])
								    echo "<option selected>" .$arrMStat[$c]."</option>";
								else 
								    echo "<option>" .$arrMStat[$c]."</option>";
?>
				</select></td>
				</tr>
				<tr>
				<td>Gender</td>
				<td valign="middle">Male<input <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> type="radio" name="optGender" value="1" checked>		
<?				if(isset($_POST['optGender'])) { ?>
				Female<input type="radio" name="optGender" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> value="2" <?=($_POST['optGender']==2)?'checked':''?>></td>
<?				} else {  ?>
				Female<input type="radio" name="optGender" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> value="2" <?=($edit[0][5]==2)?'checked':''?>></td>
<? } ?>				
				<td>&nbsp;</td>
				<td>Married Date</td>
				<td><input type="text" name="txtMarriedDate" readonly value=<?=(isset($_POST['txtMarriedDate']))?$_POST['txtMarriedDate']:$edit[0][10]?>>&nbsp;<input type="button" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtMarriedDate);return false;"></td>
				</tr> 
				</table></td>
          <td background="themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
    </div>
		<div id="job" style="position:absolute; z-index:2; width: 540px; visibility: hidden; left: 100px; top: 360px;">
	  <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="13"><img name="table_r1_c1" src="themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><table onclick="setUpdate(2)" onkeypress="setUpdate(2)" height="350" border="0" cellpadding="0" cellspacing="0">
<?
		  $edit1=$parent_empinfo->filterEmpJobInfo($_GET['id']);
		  $edit2=$parent_empinfo->filterEmpJobStat($_GET['id']);
?>
<tr>
			  <td>Date Joined</td>
			  <td><input type="text" readonly name="txtDatJoin" value=<?=(isset($_POST['txtDatJoin']))?$_POST['txtDatJoin']:$edit1[0][1]?>>&nbsp;<input type="button" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtDatJoin);return false;"></td>
			  <td width="50">&nbsp;</td>
			  <td>Employment Type</td>
			  <td><select <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> name="cmbType">
			  		<option value="0">--Select Empl. Type--</option>
<?					for($c=0;count($arrEmpType)>$c;$c++)
						if(isset($_POST['cmbType'])) {
							if($_POST['cmbType']==$arrEmpType[$c])
									echo "<option selected>" .$arrEmpType[$c]. "</option>";
								else
									echo "<option>" .$arrEmpType[$c]. "</option>";
						} elseif($edit2[0][1]==$arrEmpType[$c])
									echo "<option selected>" .$arrEmpType[$c]. "</option>";
								else
									echo "<option>" .$arrEmpType[$c]. "</option>";
?>			        
			  </select></td>
              </tr>
			  <tr>
			  <td>Confirmed</td>
			  <td> 
<?
			  if(isset($_POST['chkConfirmFlag'])) { ?>
			  <input type="checkbox" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> name="chkConfirmFlag" <?=$_POST['chkConfirmFlag']=='1'?'checked':''?> value="1">
<?			 } else { ?> 
			  <input type="checkbox" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> name="chkConfirmFlag" <?=$edit1[0][2]==1?'checked':''?> value="1">
<? } ?>			  
			  </td>
			  <td width="50">&nbsp;</td>
			  <td>Statutory Classification</td>
			  <td><select <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> name="cmbStatutory">
			  		<option value="0">-Select Stat.Class.--</option>
<?					$stat=$parent_empinfo ->getStatCodes();
						for($c=0;$stat && count($stat)>$c;$c++)
							if(isset($_POST['cmbStatutory'])) {
							   if($_POST['cmbStatutory']==$stat[$c][0])
								    echo "<option selected value='".$stat[$c][0]. "'>" . $stat[$c][1] ."</option>";
								else
								    echo "<option value='".$stat[$c][0]. "'>" . $stat[$c][1] ."</option>";
							} elseif($edit2[0][2]==$stat[$c][0])
								    echo "<option selected value='".$stat[$c][0]. "'>" . $stat[$c][1] ."</option>";
								else
								    echo "<option value='".$stat[$c][0]. "'>" . $stat[$c][1] ."</option>";
?>			  
			  </select></td>
			  </tr>
			  <tr>
			  <td>Resignation Date</td>
			  <td><input type="text" name="txtResigDat" readonly value=<?=(isset($_POST['txtResigDat']))?$_POST['txtResigDat']:$edit1[0][3]?>>&nbsp;<input type="button" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtResigDat);return false;"></td>
			  <td width="50">&nbsp;</td>
			  <td>Employment Category</td>
			  <td><select <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> name="cmbCat">
			  		<option value="0">--Select Empl. Cat.--</option>
<?					$catlist=$parent_empinfo ->getCatCodes();
					for($c=0;$catlist && count($catlist)>$c;$c++)
						if(isset($_POST['cmbCat'])) {
						 	if($_POST['cmbCat']==$catlist[$c][0])
							  	echo "<option selected value='" . $catlist[$c][0] ."'>" .$catlist[$c][1]. "</option>";
							else
							  	echo "<option value='" . $catlist[$c][0] ."'>" .$catlist[$c][1]. "</option>";
						} elseif($edit2[0][3]==$catlist[$c][0])
							  	echo "<option selected value='" . $catlist[$c][0] ."'>" .$catlist[$c][1]. "</option>";
							else
							  	echo "<option value='" . $catlist[$c][0] ."'>" .$catlist[$c][1]. "</option>";
?>			  
			  </select></td>
			  </tr>
			  <tr>
			  <td>Retire Date</td>
			  <td><input type="text" name="txtRetireDat" readonly value=<?=(isset($_POST['txtRetireDat']))?$_POST['txtRetireDat']:$edit1[0][4]?>>&nbsp;<input type="button" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtRetireDat);return false;"></td>
			  <td width="50">&nbsp;</td>
			  <td>Start Date</td>
			  <td><input type="text" name="txtStartDat" readonly value=<?=(isset($_POST['txtStartDat']))?$_POST['txtStartDat']:$edit2[0][4]?>>&nbsp;<input type="button" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtStartDat);return false;"></td>
			  </tr>
			  <tr>
 			  <td>Salary Grade</td>
			  <td><select <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> onchange="document.frmEmp.cmbDesig.options[0].selected=true,qCombo(2)" name="cmbSalGrd">
						<option value="0">-Select Sal. Grade-</option>
			  <?					$grdlist=$parent_empinfo ->getSalGrdCodes();
					for($c=0;$grdlist && count($grdlist)>$c;$c++)
						if(isset($_POST['cmbSalGrd'])) {
							 if($_POST['cmbSalGrd']==$grdlist[$c][0])
							echo "<option selected value='" .$grdlist[$c][0]. "'>" .$grdlist[$c][1]. "</option>";
						else
							echo "<option value='" .$grdlist[$c][0]. "'>" .$grdlist[$c][1]. "</option>";
						} elseif($edit1[0][5]==$grdlist[$c][0])
									echo "<option selected value='" .$grdlist[$c][0]. "'>" .$grdlist[$c][1]. "</option>";
								else
									echo "<option value='" .$grdlist[$c][0]. "'>" .$grdlist[$c][1]. "</option>";

						?>			  
			  </select></td>
			  <td width="50">&nbsp;</td>
			  <td>End Date</td>
			  <td><input type="text" readonly name="txtEndDat" value=<?=(isset($_POST['txtEndDate']))?$_POST['txtEndDate']:$edit2[0][5]?>>&nbsp;<input type="button" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtEndDat);return false;"></td>
			  </tr>
			  <tr>
			  <td>Corporate Title</td>
			  <td><select <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> onchange="qCombo(2)" name="cmbCorpTit">
			  		<option value="0">--Select Corp. Title--</option>
<?				if(isset($_POST['cmbSalGrd'])) {
					$ctlist=$parent_empinfo ->getCorpTitles($_POST['cmbSalGrd']);
					for($c=0;$ctlist && count($ctlist)>$c;$c++)
						if(isset($_POST['cmbCorpTit'])) { 
							if($_POST['cmbCorpTit']==$ctlist[$c][1])
							echo "<option selected value='" .$ctlist[$c][1]. "'>" .$ctlist[$c][2]. "</option>";
						else
							echo "<option value='" .$ctlist[$c][1]. "'>" .$ctlist[$c][2]. "</option>";
						} elseif($edit1[0][6]==$ctlist[$c][1])
							echo "<option selected value='" .$ctlist[$c][1]. "'>" .$ctlist[$c][2]. "</option>";
						else
							echo "<option value='" .$ctlist[$c][1]. "'>" .$ctlist[$c][2]. "</option>";
				} else {
					$ctlist=$parent_empinfo ->getCorpTitles($edit[0][5]);
					for($c=0;$ctlist && count($ctlist)>$c;$c++)
						if($edit1[0][6]==$ctlist[$c][1])
							echo "<option selected value='" .$ctlist[$c][1]. "'>" .$ctlist[$c][2]. "</option>";
						else
							echo "<option value='" .$ctlist[$c][1]. "'>" .$ctlist[$c][2]. "</option>";
				}
?>			  
			  </select></td>
			  <td width="50">&nbsp;</td>
			  <td>Contract to Permanent</td>
			  <td>
<?
			if(isset($_POST['chkConToPermFlag'])) { ?>			  
			  <input type="checkbox" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> name="chkConToPermFlag" <?=($_POST['chkConToPermFlag']=='1')?'checked':''?> value="1">
<?			} else { ?>
			  <input type="checkbox" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> name="chkConToPermFlag" <?=($edit2[0][6]=='1')?'checked':''?> value="1">
<? } ?>			
			  </td>
			  </tr>
			  <tr>
			  <td>Designation</td>
			  <td><select <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> name="cmbDesig">
			  			<option value="0">---Select Designation---</option>
<?					if(isset($_POST['cmbCorpTit'])) {
					$deslist=$parent_empinfo ->getDes($_POST['cmbCorpTit']);
					for($c=0;$deslist && count($deslist)>$c;$c++)
						if(isset($_POST['cmbDesig'])) {
						   if($_POST['cmbDesig']==$deslist[$c][1])
								echo "<option selected value='" .$deslist[$c][1]. "'>" .$deslist[$c][2]. "</option>";
							else
								echo "<option value='" .$deslist[$c][1]. "'>" .$deslist[$c][2]. "</option>";
						} elseif($edit1[0][7]==$deslist[$c][1])
									echo "<option selected value='" .$deslist[$c][1]. "'>" .$deslist[$c][2]. "</option>";
								else
									echo "<option value='" .$deslist[$c][1]. "'>" .$deslist[$c][2]. "</option>";
					} else {
						$deslist=$parent_empinfo ->getDes($edit1[0][6]);
						for($c=0;$deslist && count($deslist)>$c;$c++)
							if($edit1[0][7]==$deslist[$c][1])
									echo "<option selected value='" .$deslist[$c][1]. "'>" .$deslist[$c][2]. "</option>";
								else
									echo "<option value='" .$deslist[$c][1]. "'>" .$deslist[$c][2]. "</option>";
					}
?>			  
			  </select></td>
			  <td width="50">&nbsp;</td>
			  <td>Contract to Permanent Date</td>
			  <td><input type="text" readonly name="txtConToPermDat" value=<?=(isset($_POST['txtConToPermDat']))?$_POST['txtConToPermDat']:$edit2[0][7]?>>&nbsp;<input type="button" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtConToPermDat);return false;"></td>
			  </tr>
			  <tr>
			  <td>Costing Centre</td>
			  <td><select <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> name="cmbCostCode">
			  		<option value="0">--Select Cost Centre--</option>
<?					$costlist=$parent_empinfo ->getCostCodes();
					for($c=0;$costlist && count($costlist)>$c;$c++)
						if(isset($_POST['cmbCostCode'])) {
							if($_POST['cmbCostCode']==$costlist[$c][0])
							echo "<option selected value='" .$costlist[$c][0]. "'>" .$costlist[$c][1]. "</option>";
						else
							echo "<option value='" .$costlist[$c][0]. "'>" .$costlist[$c][1]. "</option>";
						} elseif($edit1[0][8]==$costlist[$c][0])
									echo "<option selected value='" .$costlist[$c][0]. "'>" .$costlist[$c][1]. "</option>";
								else
									echo "<option value='" .$costlist[$c][0]. "'>" .$costlist[$c][1]. "</option>";
?>			  
			  </select></td>
			  <td width="50">&nbsp;</td>
			  <td>HR Active</td>
			  <td>
<?			if(isset($_POST['chkHRActivFlag'])) { ?>
			  <input type="checkbox" name="chkHRActivFlag" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> <?=($_POST['chkHRActivFlag']=='1'?'checked':'')?> value="1">
<?			} else { ?>
			  <input type="checkbox" name="chkHRActivFlag" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> <?=($edit2[0][8]=='1'?'checked':'')?> value="1">
<?			} ?>
			  </td>
			  </tr>
			  <tr>
			  <td>Work Hours</td>
			  <td><input type="text" name="txtWorkHours" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> value="<?=(isset($_POST['txtWorkHours']))?$_POST['txtWorkHours']:$edit1[0][9]?>"></td>
			  <td width="50">&nbsp;</td>
			  <td>Payroll Activ</td>
			  <td>
<?			if(isset($_POST['txtPayActivFlag'])) { ?>
			  <input type="checkbox" name="txtPayActivFlag" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> <?=($_POST['txtPayActivFlag']=='1'?'checked':'')?> value="1">
<?			} else { ?>
			  <input type="checkbox" name="txtPayActivFlag" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> <?=($edit2[0][9]=='1'?'checked':'')?> value="1">
<?			} ?>

			  </td>
			  </tr>
			  <tr>
			  <td>Job Preference</td>
			  <td><input type="text" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> name="txtJobPref" value="<?=(isset($_POST['txtJobPref']))?$_POST['txtJobPref']:$edit1[0][10]?>"></td>
			  <td width="50">&nbsp;</td>
			  <td>Time &amp; Attendance Active</td>
			  <td>
<?			if(isset($_POST['chkTimeAttActivFlag'])) { ?>
			  <input type="checkbox" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> name="chkTimeAttActivFlag" <?=($_POST['chkTimeAttActivFlag']=='1'?'checked':'')?> value="1">
<?			} else { ?>
			  <input type="checkbox" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> name="chkTimeAttActivFlag" <?=($edit2[0][10]=='1'?'checked':'')?> value="1">
<?			} ?>			  
			  </td>
			  </tr>          </table></td>
          <td background="themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
    </div>
	<div id="workstation" style="position:absolute; z-index:2; width: 540px; visibility: hidden; left: 100px; top: 360px;">
	  <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="13"><img name="table_r1_c1" src="themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
<?
$edit=$parent_empinfo->filterEmpWrkStaion($_GET['id']);
$hierarchy=$parent_empinfo->getHierarchyDef();
if($hierarchy)
   $noFields=count($hierarchy);
else
	$noFields=0;
		
?>        
        <tr>
          <td background="themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><table onclick="setUpdate(3)" onkeypress="setUpdate(3)" height="<?=30+$noFields*25?>" border="0" cellpadding="0" cellspacing="0">
              <tr>
				<td>Location</td>
				<td><select <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> name="cmbLocation">
						<option value="0">--Select Location--</option>
<?						$loc=$parent_empinfo->getLocations();
						for($c=0;$loc && count($loc)>$c;$c++)
						    if(isset($_POST['cmbLocation'])) {
							    if( $_POST['cmbLocation']==$loc[$c][0])
								    echo "<option selected value'" . $loc[$c][0] . "'>" .$loc[$c][1] . "</option>";
								else							    
								    echo "<option value'" . $loc[$c][0] . "'>" .$loc[$c][1] . "</option>";
						    } else {
							    if($edit[0][9]==$loc[$c][0])
								    echo "<option selected value'" . $loc[$c][0] . "'>" .$loc[$c][1] . "</option>";
								else							    
								    echo "<option value'" . $loc[$c][0] . "'>" .$loc[$c][1] . "</option>";
						    }
							    
		for($c=0;$noFields>$c;$c++) { 
			if($c==0) {
				$res=$parent_empinfo->getCompHier($hierarchy[$c][0],0);
			} elseif (isset($_POST["hierarchy".($c-1)])) {
				$arr[0]=$hierarchy[$c][0];
				$arr[1]=$_POST["hierarchy".($c-1)];
				$res=$parent_empinfo->getCompHier($arr,1);
			} 
			
	?>
          <tr>
                <td><?=$hierarchy[$c][1]?><td>
                <td><select onchange="hierChg(this);" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> name="hierarchy<?=$c?>">
                		<option value="0">--Select--</option>
<?
			if($res) {
					for($a=0;$res && count($res)>$a;$a++)
					    if(isset($_POST["hierarchy".$c]) && $res[$a][2]==$_POST["hierarchy".$c])
				   			echo "<option selected value='" .$res[$a][2] . "'>"  .$res[$a][3] . "</option>";
				   		else
				   			echo "<option value='" .$res[$a][2] . "'>"  .$res[$a][3] . "</option>";


            } ?>
          </td></tr> 
	<?	} ?>
<input type="hidden" name="txtHiCode" value="<?=isset($_POST['txtHiCode']) ? $_POST['txtHiCode'] : ($edit[0][10] == '' ? '0' : $edit[0][10])?>">

              </tr>
          </table></td>
          <td background="themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
    </div>
	<div id="tax" style="position:absolute; z-index:2; width: 540px; visibility: hidden; left: 100px; top: 360px;">
	  <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="13"><img name="table_r1_c1" src="themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
<? $edit=$parent_empinfo->filterEmpTax($_GET['id']); 
	$clist=$parent_empinfo->getCountryCodes();
	
	for($c=0;count($clist)>$c;$c++)
	   if($clist[$c][0]==$edit[0][16]) {
	   	  if($clist[$c][1]=='Sri Lanka') {
	   	     $showTax=0;
	   	     break;
	   	  }
	   	$showTax=1;
	   	$taxCountry=$edit[0][16];
	   	break;
	   }
	     
?>
          <td><table <?=(isset($showTax) && $showTax==0)?"onclick='setUpdate(4)' onkeypress='setUpdate(4)'":''?>  height="250" border="0" cellpadding="0" cellspacing="0">


	             <tr>
          <td valign="top">Select Country</td>
          <td valign="top"><select onChange="updateTaxCountry()" name="cmbTaxCountry">
          		<option value="0">---Select---</option>
<?				$list=$tax->getCountryCodes();
					for($c=0;$list && count($list)>$c;$c++)
					if(isset($_POST['cmbTaxCountry'])) {
						if($_POST['cmbTaxCountry']==$list[$c][0])
						    echo "<option selected value='". $list[$c][0]."'>" . $list[$c][1]. "</option>";
						else
						    echo "<option value='". $list[$c][0]."'>" . $list[$c][1]. "</option>";
					} elseif($list[$c][0]==$edit[0][16])
						    echo "<option selected value='". $list[$c][0]."'>" . $list[$c][1]. "</option>";
						else
						    echo "<option value='". $list[$c][0]."'>" . $list[$c][1]. "</option>";
					
?>          	

				</select></td>
				</tr>

<?		if(isset($showTax) && $showTax=='1') {
	
		if(isset($_POST['taxSTAT']) && $_POST['taxSTAT']=='ADD') {
			$tax->setEmpId($_POST['txtEmpID']);
			$tax->setTaxId($_POST['cmbTaxID']);
			$tax->setEmpFedStateFlag($_POST['optFedStateFlag']);
			if($_POST['optFedStateFlag']==1) {
				$tax->setEmpTaxFillStat($_POST['cmbFedTaxFillStat']);
				$tax->setEmpTaxState(null);
				$tax->setEmpTaxAllowance($_POST['txtFedTaxAllowance']);
				$tax->setEmpTaxExtra($_POST['txtFedTaxExtra']);
			} else {
				$tax->setEmpTaxFillStat($_POST['cmbStateTaxFillStat']);
				$tax->setEmpTaxState($_POST['cmbStateTaxState']);
				$tax->setEmpTaxAllowance($_POST['txtStateTaxAllowance']);
				$tax->setEmpTaxExtra($_POST['txtStateTaxExtra']);
			}
			
			$tax->addEmpTax();
		}
		
		if(isset($_POST['taxSTAT']) && $_POST['taxSTAT']=='EDIT') {
			$tax->setEmpId($_POST['txtEmpID']);
			$tax->setTaxId($_POST['cmbTaxID']);
			$tax->setEmpFedStateFlag($_POST['optFedStateFlag']);
			if($_POST['optFedStateFlag']==1) {
				$tax->setEmpTaxFillStat($_POST['cmbFedTaxFillStat']);
				$tax->setEmpTaxState('');
				$tax->setEmpTaxAllowance($_POST['txtFedTaxAllowance']);
				$tax->setEmpTaxExtra($_POST['txtFedTaxExtra']);
			} else {
				$tax->setEmpTaxFillStat($_POST['cmbStateTaxFillStat']);
				$tax->setEmpTaxState($_POST['cmbStateTaxState']);
				$tax->setEmpTaxAllowance($_POST['txtStateTaxAllowance']);
				$tax->setEmpTaxExtra($_POST['txtStateTaxExtra']);
			}
			
			$tax->updateEmpTax();
		}
		
		if(isset($_POST['taxSTAT']) && $_POST['taxSTAT']=='DEL')
		   {
		   $arr=$_POST['chktaxdel'];
		   
		   for($c=0;count($arr)>$c;$c++) {
		   		$frg=explode("|",$arr[$c]);
				$arrpass[1][$c]=$frg[0];
				$arrpass[2][$c]=$frg[1];
		   		}
		
		   for($c=0;count($arr)>$c;$c++)
		       if($arr[$c]!=NULL)
			      $arrpass[0][$c]=$_GET['id'];
				  
		   $tax->delEmpTax($arrpass);
		   }
		
?>
			<input type="hidden" name="taxSTAT" value="">

<?			if(!isset($_GET['TAXID'])) { ?>
				<tr>
				<td>Tax</td>
          <td><select <?=$locRights['add'] ? '':'disabled'?> name="cmbTaxID">
          			<option value="0">--Select Tax--</option>
<?				$list=$tax->getTaxCodes();
					for($c=0;$list && count($list)>$c;$c++)
					    echo "<option value='" . $list[$c][0] . "'>" . $list[$c][1]. "</option>";
?>          	
				</select></td>
				</tr>

          <tr>
          <td>Federal<input type="radio" checked <?=$locRights['add'] ? '':'disabled'?> onclick="setFed();" name="optFedStateFlag" value="1"></td>
          <td>&nbsp;</td>
          <td width="75">&nbsp;</td>
          <td>&nbsp;</td>
          <td>State<input type="radio" onclick="setState();" <?=$locRights['add'] ? '':'disabled'?> name="optFedStateFlag"  value="2"></td>
          <td>&nbsp;</td>
          </tr>
          <tr>
          <td>Filing Status</td>
          <td><select <?=$locRights['add'] ? '':'disabled'?> name="cmbFedTaxFillStat">
			 		<option>--Select--</option>
<?			for($c=0;count($arrFillStat)>$c;$c++)
          		   echo "<option>" .$arrFillStat[$c] . "</option>";
?>          		
          </select></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>Filing Status</td>
          <td><select disabled name="cmbStateTaxFillStat">
			 		<option>--Select--</option>
<?			for($c=0;count($arrFillStat)>$c;$c++)
          		   echo "<option>" .$arrFillStat[$c] . "</option>";
?>          		
          </select></td>
          </tr>
          <tr>
          <td>Allowances</td>
          <td><input type="text" name="txtFedTaxAllowance" <?=$locRights['add'] ? '':'disabled'?> ></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>Taxed State</td>
          <td><select disabled name="cmbStateTaxState">
			 		<option value="0">--Select State--</option>
<?				$plist=$tax->getProvinceCodes($taxCountry);
				for($c=0;$plist && count($plist)>$c;$c++)
					    echo "<option value='" . $plist[$c][1]. "'>" . $plist[$c][2]. "</option>";
				
?>
			</select></td>
			</tr>
			<tr>
			<td>Extra</td>
			<td><input type="text" name="txtFedTaxExtra" <?=$locRights['add'] ? '':'disabled'?> ></td>
          <td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>Allowances</td>
            <td><input type="text" name="txtStateTaxAllowance" disabled ></td>
            </tr>
            <tr>
			<td>&nbsp;</td>
          <td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>Extra</td>
			<td><input type="text" name="txtStateTaxExtra" disabled ></td>
			</tr>
			<tr>
			<td>
<?	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delTax();" onmouseout="this.src='./themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_delete_02.jpg';" src="./themes/beyondT/pictures/btn_delete.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="./themes/beyondT/pictures/btn_delete.jpg">
<? 	} ?>

			</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>
					<?	if($locRights['add']) { ?>
					        <img border="0" title="Save" onClick="addTax();" onmouseout="this.src='./themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_save_02.jpg';" src="./themes/beyondT/pictures/btn_save.jpg">
					<? 	} else { ?>
					        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="./themes/beyondT/pictures/btn_save.jpg">
					<?	} ?>
			</td>
			</tr>

				<table width="550" align="center" border="0" class="tabForm">
				 <tr>
                      	<td width="50">&nbsp;</td>
						 <td><strong>Federal/State</strong></td>
						 <td><strong>Tax</strong></td>
						 <td><strong>Fill Status</strong></td>
						 <td><strong>Allowance</strong></td>
						 <td><strong>Extra</strong></td>
					</tr>
<?
$rset = $tax ->getAssEmpTax($_GET['id']);

    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chktaxdel[]' value='" . $rset[$c][1] ."|". $rset[$c][2] ."'></td>";
			if($rset[$c][2]==1)
            	$fname="Federal";
            else
            	$fname="State";

            ?> <td><a href="#" onmousedown="viewTax('<?=$rset[$c][1]?>',<?=$rset[$c][2]?>)" ><?=$fname?></a></td> <?
				$list=$tax->getTaxCodes();
					for($a=0;$list && count($list)>$a;$a++)
						if($rset[$c][1]==$list[$a][0])
						  $fname=$list[$a][1];

            echo '<td>' . $fname .'</td>';
            echo '<td>' . $rset[$c][3] .'</td>';
            echo '<td>' . $rset[$c][4] .'</td>';
            echo '<td>' . $rset[$c][5] .'</td>';
        echo '</tr>';
        }

} elseif(isset($_GET['TAXID'])) {
	
	$arr[0]=$_GET['id'];
	$arr[1]=$_GET['TAXID'];
	$arr[2]=$_GET['FEDST'];
	$edit=$tax->filterEmpTax($arr);
?>
				<tr>
				<td>Tax</td>
          <td><input type="hidden" name="cmbTaxID" value="<?=$edit[0][1]?>"><strong>
<?				$list=$tax->getTaxCodes();
					for($c=0;$list && count($list)>$c;$c++)
					   if($edit[0][1]==$list[$c][0])
					    echo $list[$c][1];
?>          	
				</strong></td>
				</tr>
		<input type="hidden" name="optFedStateFlag" value="<?=$edit[0][2]?>">
          <tr>
          <td>Tax/State</td>
          <td><strong><?=$edit[0][2]==1 ? 'Federal':'State'?></strong></td>
          </tr>
          <tr>
<? if($edit[0][2]==1) { ?>
          <td>Filing Status</td>
          <td><select <?=$locRights['edit'] ? '' : 'disabled'?> name="cmbFedTaxFillStat">
			 		<option>--Select--</option>
<?			for($c=0;$edit[0][2]==1 && count($arrFillStat)>$c;$c++)
				if($edit[0][3]==$arrFillStat[$c])
          		   echo "<option selected>" .$arrFillStat[$c] . "</option>";
          		else
          		   echo "<option>" .$arrFillStat[$c] . "</option>";
?>          		
          </select></td>
<? } 
 
if($edit[0][2]==2) { ?>

 		  <td>Filing Status</td>
          <td><select <?=$locRights['edit'] ? '' : 'disabled'?> name="cmbStateTaxFillStat">
			 		<option>--Select--</option>
<?			for($c=0;$edit[0][2]==2 && count($arrFillStat)>$c;$c++)
				if($edit[0][3]==$arrFillStat[$c])
          		   echo "<option selected>" .$arrFillStat[$c] . "</option>";
          		else
          		   echo "<option>" .$arrFillStat[$c] . "</option>";
?>          		
          </select></td>
<? } ?>          
          </tr>
          <tr>
<? if($edit[0][2]==1) { ?>
          <td>Allowances</td>
          <td><input type="text" <?=$locRights['edit'] ? '' : 'disabled'?> name="txtFedTaxAllowance" value="<?=$edit[0][2]==1 ? $edit[0][4] : ''?>"></td>
<? } 
 
if($edit[0][2]==2) { ?>

          <td>Taxed State</td>
          <td><select <?=$locRights['edit'] ? '' : 'disabled'?> name="cmbStateTaxState">
			 		<option value="0">-Select State-</option>
<?			$plist=$tax->getProvinceCodes($taxCountry);
				for($c=0;$edit[0][2]==2 && $plist && count($plist)>$c;$c++)
					if($edit[0][6]==$plist[$c][1])
					    echo "<option selected value='" . $plist[$c][1]. "'>" . $plist[$c][2]. "</option>";
					else
					    echo "<option value='" . $plist[$c][1]. "'>" . $plist[$c][2]. "</option>";
				}
?>
			</select></td>

			</tr>
			<tr>
<? if($edit[0][2]==1) { ?>
			<td>Extra</td>
			<td><input type="text" <?=$locRights['edit'] ? '' : 'disabled'?> name="txtFedTaxExtra" value="<?=$edit[0][2]==1 ? $edit[0][5] : '' ?>"></td>
<? }
if($edit[0][2]==2) { ?>
			
			<td>Allowances</td>
            <td><input type="text" name="txtStateTaxAllowance" <?=$locRights['edit'] ? '' : 'disabled'?> value="<?=$edit[0][2]==2 ? $edit[0][4] : '' ?>"></td>
            </tr>
            <tr>
			<td>Extra</td>
			<td><input type="text" name="txtStateTaxExtra" <?=$locRights['edit'] ? '' : 'disabled'?> value="<?=$edit[0][2]==2 ? $edit[0][5] : '' ?>"></td>
<? } ?>			
			</tr>
			<tr>
			<td>
					<?	if($locRights['edit']) { ?>
					        <img border="0" title="Save" onClick="editTax();" onmouseout="this.src='./themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_save_02.jpg';" src="./themes/beyondT/pictures/btn_save.jpg">
					<? 	} else { ?>
					        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="./themes/beyondT/pictures/btn_save.jpg">
					<?	} ?>
			</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>
<?	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delTax();" onmouseout="this.src='./themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_delete_02.jpg';" src="./themes/beyondT/pictures/btn_delete.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="./themes/beyondT/pictures/btn_delete.jpg">
<? 	} ?>
			</td>
			</tr>

				<table width="550" align="center" border="0" class="tabForm">
				 <tr>
                      	<td width="50">&nbsp;</td>
						 <td><strong>Federal/State</strong></td>
						 <td><strong>Tax</strong></td>
						 <td><strong>Fill Status</strong></td>
						 <td><strong>Allowance</strong></td>
						 <td><strong>Extra</strong></td>
					</tr>
<?
$rset = $tax ->getAssEmpTax($_GET['id']);

    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chktaxdel[]' value='" . $rset[$c][1] ."|". $rset[$c][2] ."'></td>";
			if($rset[$c][2]==1)
            	$fname="Federal";
            else
            	$fname="State";

            ?> <td><a href="#" onmousedown="viewTax('<?=$rset[$c][1]?>',<?=$rset[$c][2]?>)" ><?=$fname?></a></td> <?
				$list=$tax->getTaxCodes();
					for($a=0;count($list)>$a;$a++)
						if($rset[$c][1]==$list[$a][0])
						  $fname=$list[$a][1];

            echo '<td>' . $fname .'</td>';
            echo '<td>' . $rset[$c][3] .'</td>';
            echo '<td>' . $rset[$c][4] .'</td>';
            echo '<td>' . $rset[$c][5] .'</td>';
        echo '</tr>';
        }			
 } ?>
 
 
 <? } elseif(isset($showTax) && $showTax=='0') {

           
?>
			  	<td>Tax Exempt</td>
				<td><select <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> name="cmbTaxExempt">
			  		<option value="0">--Select--</option>
<?				
				for($c=0;count($arrTaxExempt)>$c;$c++)
					if(isset($_POST['cmbTaxExempt'])) {
						if($_POST['cmbTaxExempt']==$arrTaxExempt[$c])
						    echo "<option selected>" .$arrTaxExempt[$c] . "</option>";
						else
						    echo "<option>" .$arrTaxExempt[$c] . "</option>";
					} elseif ($edit[0][1]==$arrTaxExempt[$c])
						    echo "<option selected>" .$arrTaxExempt[$c] . "</option>";
						else
						    echo "<option>" .$arrTaxExempt[$c] . "</option>";
?>
				</select></td>
				<td width="50">&nbsp;</td>
				<td>ETF Eligibility</td>
				<td>
<?				if(isset($_POST['chkETFEligibleFlag'])) { ?>
				<input type="checkbox" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> value="1" <?=($_POST['chkETFEligibleFlag']=='1'?'checked':'')?> name="chkETFEligibleFlag">
<?				} else { ?>
				<input type="checkbox" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> value="1" <?=($edit[0][9]=='1'?'checked':'')?> name="chkETFEligibleFlag">
<?				} ?>
				</td>
              </tr>
			  <tr>
			  <td>Tax on Tax</td>
			  <td>
<?			if(isset($_POST['chkTaxOnTaxFlag'])) { ?>
			  <input type="checkbox" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> value="1" <?=($_POST['chkTaxOnTaxFlag']=='1'?'checked':'')?> name="chkTaxOnTaxFlag">
<?			} else { ?>
			  <input type="checkbox" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> value="1" <?=($edit[0][2]=='1'?'checked':'')?> name="chkTaxOnTaxFlag">
<?			} ?>
			  
			  </td>
			  <td width="50">&nbsp;</td>
			  <td>ETF No.</td>
			  <td><input type="text" name="txtETFNo" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> value="<?=(isset($_POST['txtETFNo']))?$_POST['txtETFNo']:$edit[0][10]?>"></td>
			  </tr>
			  <tr>
			  <td>Tax ID</td>
			  <td><input type="text" name="txtTaxID" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> value="<?=(isset($_POST['txtTaxID']))?$_POST['txtTaxID']:$edit[0][3]?>"></td>
			  <td width="50">&nbsp;</td>
			  <td>Employee %</td>
			  <td><input type="text" name="txtETFEmployeePercen" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> value="<?=(isset($_POST['txtETFEmployeePercen']))?$_POST['txtETFEmployeePercen']:$edit[0][11]?>"></td>
			  </tr>
			  <tr>
			  <td>EPF Eligible</td>
			  <td>
<?			  if(isset($_POST['chkEPFEligibleFlag'])) { ?>
			  <input type="checkbox" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> value="1" <?=($_POST['chkEPFEligibleFlag']=='1'?'checked':'')?> name="chkEPFEligibleFlag">
<?			} else { ?>
			  <input type="checkbox" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> value="1" <?=($edit[0][4]=='1'?'checked':'')?> name="chkEPFEligibleFlag">
<?			} ?>  
			  </td>
			  <td width="50">&nbsp;</td>
			  <td>ETF Date</td>
			  <td><input type="text" readonly name="txtETFDat" value=<?=(isset($_POST['txtETFDat']))?$_POST['txtETFDat']:$edit[0][12]?>>&nbsp;<input type="button" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtETFDat);return false;"></td>
			  </tr>
			  <tr>
			  <td>EPF No.</td>
			  <td><input type="text" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> name="txtEPFNo" value="<?=(isset($_POST['txtEPFNo']))?$_POST['txtEPFNo']:$edit[0][5]?>"></td>
			  <td width="50">&nbsp;</td>
			  <td>MSPS</td>
			  <td>
<? 			if(isset($_POST['chkMSPSEligibleFlag'])) { ?>
			  <input type="checkbox" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> name="chkMSPSEligibleFlag" <?=($_POST['chkMSPSEligibleFlag']=='1'?'checked':'')?> value="1">
<?			} else { ?>
			  <input type="checkbox" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> name="chkMSPSEligibleFlag" <?=$edit[0][13]=='1'?'checked':''?> value="1">
<?			} ?>
			  </td>
			  </tr>
			  <tr>
			  <td>Company EPF fund</td><td><input type="radio" name="optCFundCBFundFlag" checked value="1"></td>
			  <td width="50">&nbsp;</td>
			  <tr>
			  <td>
<?			  if(isset($_POST['optCFundCBFundFlag'])) { ?>
			  Central Bank EPF fund</td><td><input type="radio" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> name="optCFundCBFundFlag" <?=($_POST['optCFundCBFundFlag']!=1)?'checked':''?> value="0">
<?			} else { ?>
			  Central Bank EPF fund</td><td><input type="radio" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> name="optCFundCBFundFlag" <?=($edit[0][6]!=1)?'checked':''?> value="0">
<?			} ?>
			  </td>
			  <td width="50">&nbsp;</td>
			  <td>Employee %</td>
			  <td><input type="text" name="txtMSPSEmployeePercen" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> value="<?=(isset($_POST['txtMSPSEmployeePercen']))?$_POST['txtMSPSEmployeePercen']:$edit[0][14]?>"></td>
			  </tr>
			  <tr>
			  <td>Employee %</td>
			  <td><input type="text" name="txtEPFEmployeePercen" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> value="<?=(isset($_POST['txtEPFEmployeePercen']))?$_POST['txtEPFEmployeePercen']:$edit[0][7]?>"></td>
			  <td width="50">&nbsp;</td>
			  <td>Employer %</td>
			  <td><input type="text" name="txtMSPSEmployerPercen" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> value="<?=(isset($_POST['txtMSPSEmployerPercen']))?$_POST['txtMSPSEmployerPercen']:$edit[0][15]?>"></td>
			  </tr>
			  <tr>
			  <td>Employer %</td>
			  <td><input type="text" name="txtEPFEmployerPercen" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> value="<?=(isset($_POST['txtEPFEmployerPercen']))?$_POST['txtEPFEmployerPercen']:$edit[0][8]?>"></td>
			  </tr>
<? } ?>
          </table></td>
          <td background="themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
    </div>
	<div id="contact" style="position:absolute; z-index:2; width: 540px; visibility: hidden; left: 100px; top: 360px;">
	  <table border="0" cellpadding="0" cellspacing="0" height="100%">
        <tr>
          <td width="13"><img name="table_r1_c1" src="themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><table onclick="setUpdate(5)" onkeypress="setUpdate(5)" height="350" border="0" cellpadding="0" cellspacing="0">
<?
		$edit=$parent_empinfo->filterEmpPermRes($_GET['id']);
?>
          <tr>
			  <td>House No.</td>
			  <td><input type="text" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> name="txtPermHouseNo" value="<?=(isset($_POST['txtPermHouseNo']))?$_POST['txtPermHouseNo']:$edit[0][1]?>"></td>
			  <td width="50">&nbsp;</td>
			  <td>Fax</td>
			  <td><input type="text" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> name="txtPermFax" value="<?=(isset($_POST['txtPermFax']))?$_POST['txtPermFax']:$edit[0][8]?>"></td>
             </tr>
			 <tr>
			 <td>Street 1</td>
			 <td><input type="text" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> name="txtPermStreet1" value="<?=(isset($_POST['txtPermStreet1']))?$_POST['txtPermStreet1']:$edit[0][2]?>"></td>
			 <td width="50">&nbsp;</td>
			 <td>Email</td>
			 <td><input type="text" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> name="txtPermEmail" value="<?=(isset($_POST['txtPermEmail']))?$_POST['txtPermEmail']:$edit[0][9]?>"></td>
			 </tr>
			 <tr>
			 <td>Street 2</td>
			 <td><input type="text" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> name="txtPermStreet2" value="<?=(isset($_POST['txtPermStreet2']))?$_POST['txtPermStreet2']:$edit[0][3]?>"></td>
			 <td width="50">&nbsp;</td>
			 <td>Country</td>
			 <td><select <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> onChange="document.frmEmp.cmbPermDistrict.options[0].selected=true,qCombo(5);" name="cmbPermCountry">
			 		<option value="0">-Select Country--</option>
<?				$list=$parent_empinfo->getCountryCodes();
				for($c=0;$list && count($list)>$c;$c++)
					if(isset($_POST['cmbPermCountry'])) {
						if($_POST['cmbPermCountry']==$list[$c][0])
					    echo "<option selected value='" . $list[$c][0] . "'>" . $list[$c][1]. "</option>";
					else
					    echo "<option value='" . $list[$c][0] . "'>" . $list[$c][1]. "</option>";
					} elseif($edit[0][10]==$list[$c][0])
						echo "<option selected value='" . $list[$c][0] . "'>" . $list[$c][1]. "</option>";
					else
					    echo "<option value='" . $list[$c][0] . "'>" . $list[$c][1]. "</option>";					
?>			 
			 </select></td>
			 </tr>
			 <tr>
			 <td>City/Town</td>
			 <td><input type="text" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> name="txtPermCityTown" value="<?=(isset($_POST['txtPermCityTown']))?$_POST['txtPermCityTown']:$edit[0][4]?>"></td>
			 <td width="50">&nbsp;</td>
			 <td>State</td>
			 <td><select onChange="qCombo(5)" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> name="cmbPermProvince">
			 		<option value="0">-Select State-</option>
<?			if(isset($_POST['cmbPermCountry'])) {
				$plist=$parent_empinfo->getProvinceCodes($_POST['cmbPermCountry']);
				for($c=0;$plist && count($plist)>$c;$c++)
					if(isset($_POST['cmbPermProvince'])) {
						if($_POST['cmbPermProvince']==$plist[$c][1])
						    echo "<option selected value='" . $plist[$c][1]. "'>" . $plist[$c][2]. "</option>";
						else
						    echo "<option value='" . $plist[$c][1]. "'>" . $plist[$c][2]. "</option>";
					} elseif ($edit[0][11]==$plist[$c][1]) 
						    echo "<option selected value='" . $plist[$c][1]. "'>" . $plist[$c][2]. "</option>";
						else
						    echo "<option value='" . $plist[$c][1]. "'>" . $plist[$c][2]. "</option>";
				} else {
						$plist=$parent_empinfo->getProvinceCodes($edit[0][10]);
						for($c=0;$plist && count($plist)>$c;$c++)
						if ($edit[0][11]==$plist[$c][1]) 
						    echo "<option selected value='" . $plist[$c][1]. "'>" . $plist[$c][2]. "</option>";
						else
						    echo "<option value='" . $plist[$c][1]. "'>" . $plist[$c][2]. "</option>";
				}
?>
			 </select></td>
			 </tr>
			 <tr>
			 <td>Post Code</td>
			 <td><input type="text" name="txtPermPostCode" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> value="<?=(isset($_POST['txtPermPostCode']))?$_POST['txtPermPostCode']:$edit[0][5]?>"></td>
			 <td width="50">&nbsp;</td>
			 <td>County</td>
			 <td><select <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> name="cmbPermDistrict">
			 		<option value="0">-Select County-</option>
<?			if(isset($_POST['cmbPermProvince'])) {
				$dlist=$parent_empinfo->getDistrictCodes($_POST['cmbPermProvince']);
				for($c=0;$dlist && count($dlist)>$c;$c++)
					if(isset($_POST['cmbPermDistrict'])) {
						if($_POST['cmbPermDistrict']==$dlist[$c][1])
							    echo "<option selected value='" . $dlist[$c][1]. "'>" . $dlist[$c][2]. "</option>";
							else
							    echo "<option value='" . $dlist[$c][1]. "'>" . $dlist[$c][2]. "</option>";
					} elseif($edit[0][12]==$dlist[$c][1])	
							    echo "<option selected value='" . $dlist[$c][1]. "'>" . $dlist[$c][2]. "</option>";
							else
							    echo "<option value='" . $dlist[$c][1]. "'>" . $dlist[$c][2]. "</option>";
			} else {
				
						$dlist=$parent_empinfo->getDistrictCodes($edit[0][11]);
						for($c=0;$dlist && count($dlist)>$c;$c++)
						if($edit[0][12]==$dlist[$c][1])	
							    echo "<option selected value='" . $dlist[$c][1]. "'>" . $dlist[$c][2]. "</option>";
							else
							    echo "<option value='" . $dlist[$c][1]. "'>" . $dlist[$c][2]. "</option>";
			}
				
?>
			 </select></td>
			 </tr>
			 <tr>
			 <td>Telephone</td>
			 <td><input type="text" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> name="txtPermTelep" value="<?=(isset($_POST['txtPermTelep']))?$_POST['txtPermTelep']:$edit[0][6]?>"></td>
			 <td width="50">&nbsp;</td>
			 <td>Electorate</td>
			 <td><select <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> name="cmbPermElectorate">
			 		<option value="0">-Select Electorate-</option>
<?
			$elelist=$parent_empinfo->getElectorateCodes();
			for($c=0;$elelist && count($elelist)>$c;$c++)
				if(isset($_POST['cmbPermElectorate'])) {
					if($_POST['cmbPermElectorate']==$elelist[$c][0])
					    echo "<option selected value='" . $elelist[$c][0] . "'>" .$elelist[$c][1]. "</option>";
					else
					    echo "<option value='" . $elelist[$c][0] . "'>" .$elelist[$c][1]. "</option>";
				} elseif($edit[0][13]==$elelist[$c][0])
					    echo "<option selected value='" . $elelist[$c][0] . "'>" .$elelist[$c][1]. "</option>";
					else
					    echo "<option value='" . $elelist[$c][0] . "'>" .$elelist[$c][1]. "</option>";
?>
			 
			 </select></td>
			 </tr>
			 <tr>
			 <td>Mobile</td>
			 <td><input type="text" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> name="txtPermMobile" value="<?=(isset($_POST['txtPermMobile']))?$_POST['txtPermMobile']:$edit[0][7]?>"></td>
			 </tr>
			 </table></td>
          <td background="themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
    </div>
	<div id="passport" style="position:absolute; z-index:2; width: 540px; visibility: hidden; left: 100px; top: 360px;">
	  <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="13"><img name="table_r1_c1" src="themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><table height="200" border="0" cellpadding="0" cellspacing="0">
<?
			if(isset($_POST['passportSTAT']) && $_POST['passportSTAT']=='ADD') {
			$pport->setEmpId($_POST['txtEmpID']);
			$pport->setEmpPPSeqNo(trim($_POST['txtPPSeqNo']));
			$pport->setEmpPPNo(trim($_POST['txtPPNo']));
			$pport->setEmpPPIssDat(trim($_POST['txtPPIssDat']));
			$pport->setEmpPPIssPlace(trim($_POST['txtPPIssPlace']));
			$pport->setEmpPPExpDat(trim($_POST['txtPPExpDat']));
			$pport->setEmpPPComment(trim($_POST['PPComment']));
			$pport->setEmpVisaType($_POST['txtVisaType']);
			$pport->setEmpPPType($_POST['PPType']);
			$pport->setEmpPPCountry($_POST['cmbPPCountry']);
			$pport->setEmpPPNoOfEntries(trim($_POST['txtPPNoOfEntries']));
			$pport->addEmpPP();
			}

			if(isset($_POST['passportSTAT']) && $_POST['passportSTAT']=='EDIT') {
			$pport->setEmpId($_POST['txtEmpID']);
			$pport->setEmpPPSeqNo(trim($_POST['txtPPSeqNo']));
			$pport->setEmpPPNo(trim($_POST['txtPPNo']));
			$pport->setEmpPPIssDat(trim($_POST['txtPPIssDat']));
			$pport->setEmpPPIssPlace(trim($_POST['txtPPIssPlace']));
			$pport->setEmpPPExpDat(trim($_POST['txtPPExpDat']));
			$pport->setEmpPPComment(trim($_POST['PPComment']));
			$pport->setEmpVisaType($_POST['txtVisaType']);
			$pport->setEmpPPType($_POST['PPType']);
			$pport->setEmpPPCountry($_POST['cmbPPCountry']);
			$pport->setEmpPPNoOfEntries(trim($_POST['txtPPNoOfEntries']));
			$pport->updateEmpPP();
			}

			if(isset($_POST['passportSTAT']) && $_POST['passportSTAT']=='DEL') {

				$arr[1]=$_POST['chkpassportdel'];
			   for($c=0;count($arr[1])>$c;$c++)
			       if($arr[1][$c]!=NULL)
				      $arr[0][$c]=$_GET['id'];
			$pport->delEmpPP($arr);
			}

?>
          <input type="hidden" name="passportSTAT" value="">
<?
		if(!isset($_GET['PPSEQ'])) {
?>
          <tr>
              <input type="hidden" name="txtPPSeqNo" value="<?=$pport->getLastRecord($_GET['id'])?>">
			  <td>Passport <input type="radio" <?=$locRights['add'] ? '':'disabled'?> checked name="PPType" value="1"></td><td> Visa <input type="radio" <?=$locRights['add'] ? '':'disabled'?> name="PPType" value="2"></td>
			  <td width="50">&nbsp;</td>
		  	  <td>Issued Place</td>
		  	  <td><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtPPIssPlace"></td>
              </tr>
              <tr>
                <td>Passport/Visa No</td>
                <td><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtPPNo"></td>
                <td width="50">&nbsp;</td>
                <td>Issued Date</td>
                <td><input type="text" readonly name="txtPPIssDat">&nbsp;<input type="button" <?=$locRights['add'] ? '':'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtPPIssDat);return false;"></td>
              </tr>
              <tr>
                <td>Passport/Visa Type</td>
                <td><input name="txtVisaType" <?=$locRights['add'] ? '':'disabled'?> type="text">
                <td width="50">&nbsp;</td>
                <td>Date of Expiry</td>
                <td><input type="text" readonly name="txtPPExpDat">&nbsp;<input type="button" <?=$locRights['add'] ? '':'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtPPExpDat);return false;"></td>
              </tr>
              <tr>
                <td>Country</td>
                <td><select <?=$locRights['add'] ? '':'disabled'?> name="cmbPPCountry">
                		<option value="0">-Select Country-</option>
<?				$list=$parent_empinfo->getCountryCodes();
				for($c=0;$list && count($list)>$c;$c++)
				    echo "<option value='" . $list[$c][0] . "'>" . $list[$c][1]. "</option>";
?>			 
				</td>
				<td width="50">&nbsp;</td>
				<td>Comments</td>
				<td><textarea <?=$locRights['add'] ? '':'disabled'?> name="PPComment"></textarea></td>
				<tr>
				  <td>No of Entries</td>
				  <td><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtPPNoOfEntries"></td>
				  <td width="50">&nbsp;</td>
				  <td width="50">&nbsp;</td>
				  <td>
<?	if($locRights['add']) { ?>
        <img border="0" title="Save" onClick="addPassport();" onmouseout="this.src='./themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_save_02.jpg';" src="./themes/beyondT/pictures/btn_save.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="./themes/beyondT/pictures/btn_save.jpg">
<?	} ?>
				  </td>
				</tr>
				<tr>
				<td>
<?	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delPassport();" onmouseout="this.src='./themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_delete_02.jpg';" src="./themes/beyondT/pictures/btn_delete.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="./themes/beyondT/pictures/btn_delete.jpg">
<? 	} ?>
				</td>
				</tr>
				
				<table width="550" align="center" border="0" class="tabForm">
				 <tr>
                      	<td width="50">&nbsp;</td>
						 <td><strong>Passport/Visa</strong></td>
						 <td><strong>Number</strong></td>
						 <td><strong>Country</strong></td>
						 <td><strong>Issued Date</strong></td>
						 <td><strong>Expiry Date</strong></td>
					</tr>
<?
$rset = $pport ->getAssEmpPP($_GET['id']);

    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkpassportdel[]' value='" . $rset[$c][1] ."'></td>";
			if($rset[$c][8]==1)
            	$fname="Passport";
            else
            	$fname="Visa";

            ?> <td><a href="#" onmousedown="viewPassport(<?=$rset[$c][1]?>)" ><?=$fname?></a></td> <?
            echo '<td>' . $rset[$c][2] .'</td>';
            echo '<td>' . $rset[$c][9] .'</td>';
            $dtPrint = explode(" ",$rset[$c][3]);
            echo '<td>' . $dtPrint .'</td>';
            $dtPrint = explode(" ",$rset[$c][5]);
            echo '<td>' . $dtPrint .'</td>';
        echo '</tr>';
        }

	} elseif(isset($_GET['PPSEQ'])) {
			
		$arr[0]=$_GET['id'];
		$arr[1]=$_GET['PPSEQ'];
		$edit=$pport->filterEmpPP($arr);
?>

          <tr>
              <input type="hidden" name="txtPPSeqNo" value="<?=$edit[0][1]?>">
			  <td>Passport <input type="radio" checked <?=$locRights['edit'] ? '':'disabled'?> name="PPType" value="1"></td><td> Visa <input type="radio" <?=$locRights['edit'] ? '':'disabled'?> name="PPType" <?=($edit[0][8]=='2')?'checked':''?> value="2"></td>
			  <td width="50">&nbsp;</td>
		  	  <td>Issued Place</td>
		  	  <td><input type="text" name="txtPPIssPlace" <?=$locRights['edit'] ? '':'disabled'?> value="<?=$edit[0][4]?>"></td>
              </tr>
              <tr>
                <td>Passport/Visa No</td>
                <td><input type="text" name="txtPPNo" <?=$locRights['edit'] ? '':'disabled'?> value="<?=$edit[0][2]?>"></td>
                <td width="50">&nbsp;</td>
                <td>Issued Date</td>
                <td><input type="text" name="txtPPIssDat" readonly value=<?=$edit[0][3]?>>&nbsp;<input type="button" <?=$locRights['edit'] ? '':'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtPPIssDat);return false;"></td>
              </tr>
              <tr>
                <td>Passport/Visa Type</td>
                <td><input name="txtVisaType" type="text" <?=$locRights['edit'] ? '':'disabled'?> value="<?=$edit[0][7]?>">
                <td width="50">&nbsp;</td>
                <td>Date of Expiry</td>
                <td><input type="text" name="txtPPExpDat" readonly value=<?=$edit[0][5]?>>&nbsp;<input type="button" <?=$locRights['add'] ? '':'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtPPExpDat);return false;"></td>
              </tr>
              <tr>
                <td>Country</td>
                <td><select <?=$locRights['edit'] ? '':'disabled'?> name="cmbPPCountry">
<?				$list=$parent_empinfo->getCountryCodes();
				for($c=0;count($list)>$c;$c++)
					if($edit[0][9]==$list[$c][0])
					    echo "<option selected value='" . $list[$c][0] . "'>" . $list[$c][1]. "</option>";
					else
					    echo "<option value='" . $list[$c][0] . "'>" . $list[$c][1]. "</option>";
?>			 
				</td>
				<td width="50">&nbsp;</td>
				<td>Comments</td>
				<td><textarea <?=$locRights['edit'] ? '':'disabled'?> name="PPComment"><?=$edit[0][6]?></textarea></td>
				<tr>
				  <td>No of Entries</td>
				  <td><input type="text" <?=$locRights['edit'] ? '':'disabled'?> name="txtPPNoOfEntries" value="<?=$edit[0][10]?>"></td>
				  <td width="50">&nbsp;</td>
				  <td width="50">&nbsp;</td>
				  <td>
					<?	if($locRights['edit']) { ?>
					        <img border="0" title="Save" onClick="editPassport();" onmouseout="this.src='./themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_save_02.jpg';" src="./themes/beyondT/pictures/btn_save.jpg">
					<? 	} else { ?>
					        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="./themes/beyondT/pictures/btn_save.jpg">
					<?	} ?>
				  </td>
				</tr>
				<tr>
				<td>
<?	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delPassport();" onmouseout="this.src='./themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_delete_02.jpg';" src="./themes/beyondT/pictures/btn_delete.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="./themes/beyondT/pictures/btn_delete.jpg">
<? 	} ?>
				</td>
				</tr>
				
				<table width="550" align="center" border="0" class="tabForm">
				 <tr>
                      	<td width="50">&nbsp;</td>
						 <td><strong>Passport/Visa</strong></td>
						 <td><strong>Number</strong></td>
						 <td><strong>Country</strong></td>
						 <td><strong>Issued Date</strong></td>
						 <td><strong>Expiry Date</strong></td>
					</tr>
<?
$rset = $pport ->getAssEmpPP($_GET['id']);

    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkpassportdel[]' value='" . $rset[$c][1] ."'></td>";
			if($rset[$c][8]==1)
            	$fname="Passport";
            else
            	$fname="Visa";

            ?> <td><a href="#" onmousedown="viewPassport(<?=$rset[$c][1]?>)" ><?=$fname?></a></td> <?
            echo '<td>' . $rset[$c][2] .'</td>';
            echo '<td>' . $rset[$c][9] .'</td>';
            $dtPrint = explode(" ",$rset[$c][3]);
            echo '<td>' . $dtPrint .'</td>';
            $dtPrint = explode(" ",$rset[$c][5]);
            echo '<td>' . $dtPrint .'</td>';
        echo '</tr>';
        }

 } ?>
		</table></td>
          <td background="themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
    </div>
	<div id="bank" style="position:absolute; z-index:2; width: 540px; visibility: hidden; left: 100px; top: 360px;">
	  <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="13"><img name="table_r1_c1" src="themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><table height="200" border="0" cellpadding="0" cellspacing="0">
<?
		if(isset($_POST['brchSTAT']) && $_POST['brchSTAT']=='ADD')  {
		   $bankacc->setEmpId($_POST['txtEmpID']);
		   $bankacc->setEmpBranchCode($_POST['cmbBranchCode']);
		   $bankacc->setEmpAccNo(trim($_POST['AccNo']));
		   $bankacc->setEmpAccType($_POST['optAccType']);
		   $bankacc->setEmpAmount($_POST['Amount']);
		   $bankacc->addEmpBank();
		}

		if(isset($_POST['brchSTAT']) && $_POST['brchSTAT']=='EDIT')  {
		   $bankacc->setEmpId($_POST['txtEmpID']);
		   $bankacc->setEmpBranchCode($_POST['cmbBranchCode']);
		   $bankacc->setEmpAccNo(trim($_POST['AccNo']));
		   $bankacc->setEmpAccType($_POST['optAccType']);
		   $bankacc->setEmpAmount($_POST['Amount']);
		   $bankacc->updateEmpBank();
		}

			if(isset($_POST['brchSTAT']) && $_POST['brchSTAT']=='DEL') {

				$arr[1]=$_POST['chkbrchdel'];
			   for($c=0;count($arr[1])>$c;$c++)
			       if($arr[1][$c]!=NULL)
				      $arr[0][$c]=$_GET['id'];
			$bankacc->delEmpBank($arr);
			}

?>
          <input type="hidden" name="brchSTAT" value="">
<?	if(!isset($_GET['BRCH'])) { ?>
          <tr>
				<td>Bank Name</td>
				<td><select <?=$locRights['add'] ? '':'disabled'?> onchange="qCombo(7)" name="cmbBank">
				<option value="0">--Select Bank--</option>
<?				$banklist=$bankacc->getBankCodes();
				for($c=0; $banklist && $c<count($banklist);$c++)
				   if(isset($_POST['cmbBank']) && $_POST['cmbBank']==$banklist[$c][0])
					   echo "<option selected value='" . $banklist[$c][0]. "'>" . $banklist[$c][1]. "</option>";
				   else
					   echo "<option value='" . $banklist[$c][0]. "'>" . $banklist[$c][1]. "</option>";
?>				
				</td>
				<td width="50">&nbsp;</td>
				<td>Account Type</td>
				<td><input type="radio" <?=$locRights['add'] ? '':'disabled'?> name="optAccType" checked value="1">Current</td><td><input type="radio" <?=$locRights['add'] ? '':'disabled'?> name="optAccType" value="2">Savings</td>
              </tr>
              <tr>
                <td>Branch Name</td>
                <td><select <?=$locRights['add'] ? '':'disabled'?> name="cmbBranchCode">
				<option value="0">--Select Branch--</option>
<?				if(isset($_POST['cmbBank'])) {
					$brchlist=$bankacc->getUnAssBranchCodes($_GET['id'],$_POST['cmbBank']);
					for($c=0;$brchlist && count($brchlist)>$c;$c++)
					   if($_POST['cmbBranchCode']==$brchlist[$c][0])
					   		echo "<option selected value='" . $brchlist[$c][0]. "'>" . $brchlist[$c][1]. "</option>";
					   else
					   		echo "<option value='" . $brchlist[$c][0]. "'>" . $brchlist[$c][1]. "</option>";
				   		
					}
?>                
				</td>
				<td width="50">&nbsp;</td>
				<td>Amount</td>
				<td><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="Amount"></td>
			<tr>
				<td>Account No</td>
				<td><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="AccNo"></td>
				<td width="50">&nbsp;</td>
				<td width="50">&nbsp;</td><td>
<?	if($locRights['add']) { ?>
        <img border="0" title="Save" onClick="addBranch();" onmouseout="this.src='./themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_save_02.jpg';" src="./themes/beyondT/pictures/btn_save.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="./themes/beyondT/pictures/btn_save.jpg">
<?	} ?>
				</td>
			</tr>
				<tr>
				<td>
<?	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delBranch();" onmouseout="this.src='./themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_delete_02.jpg';" src="./themes/beyondT/pictures/btn_delete.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="./themes/beyondT/pictures/btn_delete.jpg">
<? 	} ?>
				</td>
				</tr>
			<table border="0" width="450" align="center" class="tabForm">
			
			    <tr>
                      	<td></td>
						 <td><strong>Branch</strong></td>
						 <td><strong>Bank</strong></td>
						 <td><strong>Account No</strong></td>
						 <td><strong>Account Type</strong></td>
						 <td><strong>Amount</strong></td>
					</tr>
<?
$rset = $bankacc ->getAssEmpBank($_GET['id']);
$bankname='';
$fname='';
    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkbrchdel[]' value='" . $rset[$c][1] ."'></td>";
            $brchlist=$bankacc->getAllBranchCodes();
            for ($a=0;count($brchlist)>$a;$a++)
                 if($rset[$c][1]==$brchlist[$a][0]) {
                    $fname=$brchlist[$a][1];
                    $bankname=$brchlist[$a][3];
                    break;
                 }
            ?> <td><a href="#" onmousedown="viewBranch('<?=$rset[$c][1]?>')" ><?=$fname?></a></td> <?
            echo '<td>' . $bankname .'</td>';     
            echo '<td>' . $rset[$c][2] .'</td>';
			if($rset[$c][3]==1)
            	echo '<td>Current</td>';
            else
            	echo '<td>Savings</td>';
            echo '<td>' . $rset[$c][4] .'</td>';
        echo '</tr>';
        }

} elseif(isset($_GET['BRCH'])) {
	
		$arr[0]=$_GET['BRCH'];
		$arr[1]=$_GET['id'];
		$edit=$bankacc->filterEmpBank($arr);

        $brchlist=$bankacc->getAllBranchCodes();
        for ($a=0;count($brchlist)>$a;$a++)
             if($edit[0][0]==$brchlist[$a][0]) 
                 break;

		?>
          <tr>
				<td>Bank Name</td>
				<td><strong><?=$brchlist[$a][3]?></strong></td>
				<td width="50">&nbsp;</td>
				<td>Account Type</td>
				<td><input type="radio" name="optAccType" <?=$locRights['edit'] ? '':'disabled'?> checked value="1">Current</td>

<?				if($edit[0][3]==2) { ?>
					<td><input type="radio" <?=$locRights['edit'] ? '':'disabled'?> name="optAccType" checked value="2">Savings</td>
<?				} else { ?>
					<td><input type="radio" <?=$locRights['edit'] ? '':'disabled'?> name="optAccType" value="2">Savings</td>
<?				} ?>
              </tr>
              <tr>
                <td>Branch Name</td>
                <td><input type="hidden" name="cmbBranchCode" value="<?=$edit[0][0]?>">
                    <strong><?=$brchlist[$a][1]?></strong>
				</td>
				<td width="50">&nbsp;</td>
				<td>Amount</td>
				<td><input type="text" name="Amount" <?=$locRights['edit'] ? '':'disabled'?> value="<?=$edit[0][4]?>"></td>
			<tr>
				<td>Account No</td>
				<td><input type="text" <?=$locRights['edit'] ? '':'disabled'?> name="AccNo" value="<?=$edit[0][2]?>"></td>
				<td width="50">&nbsp;</td>
				<td width="50">&nbsp;</td>
				<td>
<?	if($locRights['edit']) { ?>
        <img border="0" title="Save" onClick="editBranch();" onmouseout="this.src='./themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_save_02.jpg';" src="./themes/beyondT/pictures/btn_save.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="./themes/beyondT/pictures/btn_save.jpg">
<?	} ?>
				</td>
			</tr>
				<tr>
				<td>
<?	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delBranch();" onmouseout="this.src='./themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_delete_02.jpg';" src="./themes/beyondT/pictures/btn_delete.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="./themes/beyondT/pictures/btn_delete.jpg">
<? 	} ?>
				</td>
				</tr>
			<table border="0" width="450" align="center" class="tabForm">
			
			    <tr>
                      	<td></td>
						 <td><strong>Branch</strong></td>
						 <td><strong>Bank</strong></td>
						 <td><strong>Account No</strong></td>
						 <td><strong>Account Type</strong></td>
						 <td><strong>Amount</strong></td>
					</tr>
<?
$rset = $bankacc ->getAssEmpBank($_GET['id']);
	$bankname='';
    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkbrchdel[]' value='" . $rset[$c][1] ."'></td>";
            $brchlist=$bankacc->getAllBranchCodes();
            for ($a=0;count($brchlist)>$a;$a++)
                 if($rset[$c][1]==$brchlist[$a][0]) {
                    $fname=$brchlist[$a][1];
                    $bankname=$brchlist[$a][3];
                    break;
                 }
            ?> <td><a href="#" onmousedown="viewBranch('<?=$rset[$c][1]?>')" ><?=$fname?></a></td> <?
            echo '<td>' . $bankname .'</td>';     
            echo '<td>' . $rset[$c][2] .'</td>';
			if($rset[$c][3]==1)
            	echo '<td>Current</td>';
            else
            	echo '<td>Savings</td>';
            echo '<td>' . $rset[$c][4] .'</td>';
        echo '</tr>';
        }
} ?>
			</table></td>
          <td background="themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
    </div>
	<div id="attachment" style="position:absolute; z-index:2; width: 540px; visibility: hidden; left: 100px; top: 360px;">
	  <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="13"><img name="table_r1_c1" src="themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><table width="352" height="200" border="0" cellpadding="0" cellspacing="0">
<?
			if(isset($_POST['attSTAT']) && $_POST['attSTAT']=='DEL') {

				$arr[1]=$_POST['chkattdel'];
			   for($c=0;count($arr[1])>$c;$c++)
			       if($arr[1][$c]!=NULL)
				      $arr[0][$c]=$_GET['id'];
			$attachment->delEmpAtt($arr);
			}

			if(isset($_POST['attSTAT']) && $_POST['attSTAT']=='ADD' && $_FILES['ufile']['size']>0) {
			//file info
			$fileName = $_FILES['ufile']['name'];
			$tmpName  = $_FILES['ufile']['tmp_name'];
			$fileSize = $_FILES['ufile']['size'];
			$fileType = $_FILES['ufile']['type'];
			$exception_handler = new ExceptionHandler();
	  	 	//$exception_handler->logW($tmpName);
	  	 	//$exception_handler->logW($fileName);

			//file read
			$fp = fopen($tmpName,'r');
			$contents = fread($fp,filesize($tmpName));
			$contents = addslashes($contents);
			fclose($fp);
			
			if(!get_magic_quotes_gpc())
				$fileName=addslashes($fileName);
				
		$attachment->setEmpId($_POST['txtEmpID']);
		$attachment->setEmpAttId($attachment->getLastRecord($_POST['txtEmpID']));
		$attachment->setEmpAttDesc(trim($_POST['txtAttDesc']));
		$attachment->setEmpAttFilename($fileName);
		$attachment->setEmpAttSize($fileSize);
		$attachment->setEmpAttachment($contents);
		$attachment->setEmpAttType($fileType);
		$attachment->addEmpAtt();				
		}
		
			if(isset($_POST['attSTAT']) && $_POST['attSTAT']=='EDIT' ) {
			//file info

				
		$attachment->setEmpId($_POST['txtEmpID']);
		$attachment->setEmpAttId($_POST['seqNO']);
		$attachment->setEmpAttDesc(trim($_POST['txtAttDesc']));
		$attachment->updateEmpAtt();				
		}

?>          
		
          <?		if(!isset($_GET['ATTACH'])) { ?>
          <tr>
				<td>Path</td>
				<td><input type="file" name="ufile" ></td>
              </tr>
              <tr>
              	<td>Description</td>
              	<td><textarea name="txtAttDesc"></textarea></td>
              </tr>
				<tr>
				<td>
<?	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delAttach();" onmouseout="this.src='./themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_delete_02.jpg';" src="./themes/beyondT/pictures/btn_delete.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="./themes/beyondT/pictures/btn_delete.jpg">
<? 	} ?>
				</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>
<?	if($locRights['add']) { ?>
        <img border="0" title="Save" onClick="addAttach();" onmouseout="this.src='./themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_save_02.jpg';" src="./themes/beyondT/pictures/btn_save.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="./themes/beyondT/pictures/btn_save.jpg">
<?	} ?>
				</td>
				</tr>
			<table border="0" width="450" align="center" class="tabForm">
			
			    <tr>
                      	<td></td>
						 <td><strong>File Name</strong></td>
						 <td><strong>Size</strong></td>
						 <td><strong>Type</strong></td>
					</tr>
<?
$rset = $attachment -> getAssEmpAtt($_GET['id']);

    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkattdel[]' value='" . $rset[$c][1] ."'></td>";
            ?> <td><a href="#" title="<?=$rset[$c][2]?>" onmousedown="viewAttach('<?=$rset[$c][1]?>')" ><?=$rset[$c][3]?></a></td> <?
            echo '<td>' . $rset[$c][4] .'byte(s)</td>';     
            echo '<td>' . $rset[$c][6] .'</td>';
        echo '</tr>';
        }
?>
              
<?		} elseif(isset($_GET['ATTACH'])) {
		$arr[0]=$_GET['id'];
		$arr[1]=$_GET['ATTACH'];
		$edit=$attachment->filterEmpAtt($arr);
?>
              <input type="hidden" name="seqNO" value="<?=$edit[0][1]?>">
              <tr>
              	<td>Description</td>
              	<td><textarea name="txtAttDesc"><?=$edit[0][2]?></textarea></td>
              </tr>
              <tr>
              	<td><input type="button" value="Show File" class="buton" onclick="dwPopup()"></td>
              </tr>
				<tr>
				<td>
<?	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delAttach();" onmouseout="this.src='./themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_delete_02.jpg';" src="./themes/beyondT/pictures/btn_delete.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="./themes/beyondT/pictures/btn_delete.jpg">
<? 	} ?>
				</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>
<?	if($locRights['edit']) { ?>
        <img border="0" title="Save" onClick="editAttach();" onmouseout="this.src='./themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_save_02.jpg';" src="./themes/beyondT/pictures/btn_save.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="./themes/beyondT/pictures/btn_save.jpg">
<?	} ?>
				</td>
				</tr>
			<table border="0" width="450" align="center" class="tabForm">
			
			    <tr>
                      	<td></td>
						 <td><strong>File Name</strong></td>
						 <td><strong>Size</strong></td>
						 <td><strong>Type</strong></td>
					</tr>
<?
$rset = $attachment -> getAssEmpAtt($_GET['id']);

    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkattdel[]' value='" . $rset[$c][1] ."'></td>";
            ?> <td><a href="#" title="<?=$rset[$c][2]?>" onmousedown="viewAttach('<?=$rset[$c][1]?>')" ><?=$rset[$c][3]?></a></td> <?
            echo '<td>' . $rset[$c][4] .'byte(s)</td>';     
            echo '<td>' . $rset[$c][6] .'</td>';
        echo '</tr>';
        }
?>


<? } ?>
              
          </table></td>
          <td background="themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
    </div>
	<div id="other" style="position:absolute; z-index:2; width: 540px; visibility: hidden; left: 100px; top: 360px;">
	  <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="13"><img name="table_r1_c1" src="themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><table width="352" height="200" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td height="18"><img name="table_r3_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt="">Other</td>
              </tr>
          </table></td>
          <td background="themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
    </div>
	</td>
  </tr>
</table>
            </td>
          </tr>
    </table>
    </form>
<iframe width=174 height=189 name="gToday:normal:agenda.js" id="gToday:normal:agenda.js" src="scripts/ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; top:-500px; left:-500px;">
</iframe>
</body>
</html>

<? } ?>