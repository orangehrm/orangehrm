<?
/*
*OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures 
* all the essential functionalities required for any enterprise. 
* Copyright (C) 2006 hSenid Software International Pvt. Ltd, http://www.hsenid.com
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
//xajax headers
require_once ROOT_PATH . '/lib/confs/sysConf.php';
require_once ROOT_PATH . '/lib/controllers/EmpViewController.php';
//require_once ROOT_PATH . '/lib/logs/LogWriter.php';

	$sysConst = new sysConf(); 
	$locRights=$_SESSION['localRights'];

	$arrMStat = $this->popArr['arrMStat'];

function populateStates($value) {
	
	$emp_view_controller = new EmpViewController();
	$provlist = $emp_view_controller->xajaxObjCall($value,'EMP','province');
	
	$objResponse = new xajaxResponse();
	$xajaxFiller = new xajaxElementFiller();
	$response = $xajaxFiller->cmbFiller($objResponse,$provlist,1,'frmEmp','cmbProvince');
	$response->addAssign('status','innerHTML','');
	
return $response->getXML();
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
	$response = $xajaxFiller->cmbFiller($objResponse,$empstatlist,0,'frmEmp','cmbType');
	$response->addAssign('status','innerHTML','');
		
return $response->getXML();
}

/*function assignedPassPortFill() {
 
	$view_controller = new EmpViewController();
	$rset = $view_controller -> xajaxObjCall($_GET['id'],'PASSPORT','assigned');
		$outStr = 'Number :'.count($rset);
	/*$outStr = '';
	
	$outStr = "<table width='550' align='center' border='0' class='tabForm'>";
	$outStr .="<tr>";
     $outStr .=   	"<td width='50'>&nbsp;</td>";
	$outStr .=	 "<td><strong>Passport/Visa</strong></td>";
	$outStr .=	 "<td><strong>Number</strong></td>";
	$outStr .=	 "<td><strong>Country</strong></td>";
	$outStr .=	 "<td><strong>Issued Date</strong></td>";
	$outStr .=	 "<td><strong>Expiry Date</strong></td>";
	$outStr .="</tr> ";
					
	
    for($c=0;$rset && $c < count($rset); $c++)
        {
        $outStr .= '<tr>';
        $outStr .= "<td><input type='checkbox' class='checkbox' name='chkpassportdel[]' value='" . $rset[$c][1] ."'></td>";
            if($rset[$c][6]==1)
            	$fname="Passport";
            else
            	$fname="Visa";

        $outStr .= "<td><a href='#' onmousedown='xajax_viewPassport(<?=$rset[$c][1]?>)' ><?=$fname?></a></td>";
        $outStr .= '<td>' . $rset[$c][2] .'</td>';
        $outStr .= '<td>' . $rset[$c][9] .'</td>';
        	$dtPrint = explode(" ",$rset[$c][3]);
        $outStr .= '<td>' . $dtPrint[0] .'</td>';
        	$dtPrint = explode(" ",$rset[$c][4]);
        $outStr .= '<td>' . $dtPrint[0] .'</td>';
        $outStr .='</tr>';
        }
        
       //$logw = new LogWriter();
		//$logw->writeLogDB($outStr);
        return $outStr;
}


function addPassport($arrValues) {
		
	$logw = new LogWriter();
	//$logw->writeLogDB(count($arrValues));
	
	$ext_passport= new EXTRACTOR_EmpPassPort();
	$filledObj = $ext_passport->parseData($arrValues);
	
	$statArr['passportSTAT'] = 'ADD';
	$view_controller = new EmpViewController();
	$view_controller->assignEmpFormData($statArr,$filledObj,'ADD');
		
	$xajaxResponse = new xajaxResponse();
	
	$view_controller = new EmpViewController();
	$newPPSeqNo = $view_controller -> xajaxObjCall($this->getArr['id'],'PASSPORT','newseqno');
	
	//$xajaxResponse->addAssign('txtPPSeqNo','value',$newPPSeqNo);
	$xajaxResponse->addScript("document.frmEmp.txtPPSeqNo.value=$newPPSeqNo;");
	$xajaxResponse->addScript("document.frmEmp.txtPPNo.value='';");
	$xajaxResponse->addScript("document.frmEmp.txtPPIssDat.value='';");
	$xajaxResponse->addScript("document.frmEmp.txtPPExpDat.value='';");
	$xajaxResponse->addScript("document.frmEmp.txtComments.value='';");
	$xajaxResponse->addScript("document.frmEmp.PPType.value='';");
	$xajaxResponse->addScript("document.frmEmp.txtI9status.value='';");
	$xajaxResponse->addScript("document.frmEmp.txtI9ReviewDat.value='';");
	$xajaxResponse->addScript("document.frmEmp.cmbPPCountry.options[0].selected=true;");
	
	$mapStr = assignedPassPortFill();
	
	$xajaxResponse->addScript("document.frmEmp.tablePassport.innerHTML='$mapStr';");
	
	return $xajaxResponse->getXML();
}

function viewPassport($seqNo) {

	$arrValue = array($this->getArr['id'], $seqNo);
	$logw = new LogWriter();
	//$logw->writeLogDB(count($arrValue));
	$logw->writeLogDB($seqNo);
	
	//$logw->writeLogDB($this->getArr['id']);
	//$logw->writeLogDB($seqNo);
	$view_controller = new EmpViewController();
	$editArr = $view_controller -> xajaxObjCall($arrValue,'PASSPORT','filter');

	$logw = new LogWriter();
	$logw->writeLogDB(count($editArr));
	$logw->writeLogDB($editArr[0][0]);
	$logw->writeLogDB($editArr[0][3]);
	$logw->writeLogDB($editArr[0][4]);
	$logw->writeLogDB($editArr[0][5]);
	$logw->writeLogDB($editArr[0][6]);
	
	$xajaxResponse = new xajaxResponse();
	$xajaxResponse->addAssign('txtPPSeqNo','value',$editArr[0][1]);
	//$xajaxResponse->addAssign('txtPPNo','value',$editArr[0][2]);
	$xajaxResponse->addScript("document.frmEmp.txtPPNo.value=$editArr[0][2];");
	$xajaxResponse->addScript("document.frmEmp.txtPPIssDat.value=$editArr[0][3];");
	$xajaxResponse->addScript("document.frmEmp.txtPPExpDat.value=$editArr[0][4];");
	$xajaxResponse->addScript("document.frmEmp.txtComments.value=$editArr[0][5];");
	//$xajaxResponse->addAssign('txtPPIssDat','value',$editArr[0][3]);
	//$xajaxResponse->addAssign('txtPPExpDat','value',$editArr[0][4]);
	//$xajaxResponse->addAssign('txtComments','value',$editArr[0][5]);
	$xajaxResponse->addAssign('PPType','value',$editArr[0][6]);
	$xajaxResponse->addAssign('txtI9status','value',$editArr[0][7]);
	$xajaxResponse->addAssign('txtI9ReviewDat','value',$editArr[0][8]);
	
		$list = $this->popArr['ppcntlist'];
		for($c=0;$list && count($list)>$c;$c++)
		  if($edit[0][9]==$nation[$c][0]) 
	$xajaxResponse->addScript("document.frmEmp.cmbPPCountry.options[$c].selected=true;");
	
	return $xajaxResponse->getXML();
}

function editPassport($arrValues) {
	
	
	$ext_passport= new EXTRACTOR_EmpPassPort();
	$filledObj = $ext_passport->parseData($arrValues);
	
	$statArr['passportSTAT'] = 'EDIT';
	$view_controller = new EmpViewController();
	$view_controller->assignEmpFormData($statArr,$filledObj,'EDIT');
	
	$mapStr = assignedPassPortFill();
	
	$xajaxResponse = new xajaxResponse();
	$xajaxResponse->addAssign('tablePassport','innerHTML',$mapStr);

	$view_controller = new EmpViewController();
	$newPPSeqNo = $view_controller -> xajaxObjCall($this->getArr['id'],'PASSPORT','newseqno');
		
	$xajaxResponse->addAssign('txtPPSeqNo','value',$newPPSeqNo);
	$xajaxResponse->addScript("document.frmEmp.txtPPNo.value='';");
	$xajaxResponse->addScript("document.frmEmp.txtPPIssDat.value='';");
	$xajaxResponse->addScript("document.frmEmp.txtPPExpDat.value='';");
	$xajaxResponse->addScript("document.frmEmp.txtComments.value='';");
	$xajaxResponse->addScript("document.frmEmp.PPType.value='';");
	$xajaxResponse->addScript("document.frmEmp.txtI9status.value='';");
	$xajaxResponse->addScript("document.frmEmp.txtI9ReviewDat.value='';");
	$xajaxResponse->addScript("document.frmEmp.cmbPPCountry.options[0].selected=true;");

	return $xajaxResponse->getXML();
}
*/

$objAjax = new xajax();
$objAjax->registerFunction('populateStates');
$objAjax->registerFunction('populateDistrict');
$objAjax->registerFunction('assEmpStat');
//$objAjax->registerFunction('viewPassport');
//$objAjax->registerFunction('editPassport');
//$objAjax->registerFunction('addPassport');
$objAjax->processRequests();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<? $objAjax->printJavascript(); ?>
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

	var cnt = document.frmEmp.txtEmpLastName;
	if(!alpha(cnt)) {
		alert("Field should be Alphabetic");
		cnt.focus();
		return;
	}

	var cnt = document.frmEmp.txtEmpFirstName;
	if(!alpha(cnt)) {
		alert("Field should be Alphabetic");
		cnt.focus();
		return;
	}
	
	var cnt = document.frmEmp.txtEmpMiddleName;
	if(!alpha(cnt)) {
		alert("Field should be Alphabetic");
		cnt.focus();
		return;
	}

	if(document.frmEmp.txtNICNo.value=='') {
		alert("Field Empty");

		MM_showHideLayers('hidebg','','hide','personal','','show','job','','hide','workstation','','hide','econtact','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','hide');

		document.frmEmp.txtNICNo.focus();
		return;
	}
	
	/*if(document.frmEmp.cmbSalGrd.value=='0') {
		alert("Field should be selected");

		MM_showHideLayers('hidebg','','hide','personal','','hide','job','','show','workstation','','hide','econtact','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','hide');

		document.frmEmp.cmbSalGrd.focus();
		return;
	}
*/	
		document.frmEmp.sqlState.value = "NewRecord";
		document.frmEmp.submit();		
	}			

	function goBack() {
		location.href ="./CentralController.php?reqcode=<?=$this->getArr['reqcode']?>&VIEW=MAIN";
		
	}

function mout() {
	if(document.frmEmp.EditMode.value=='1') 
		document.Edit.src='../../themes/beyondT/pictures/btn_save.jpg'; 
	else
		document.Edit.src='../../themes/beyondT/pictures/btn_edit.jpg'; 
}

function mover() {
	if(document.frmEmp.EditMode.value=='1') 
		document.Edit.src='../../themes/beyondT/pictures/btn_save_02.jpg'; 
	else
		document.Edit.src='../../themes/beyondT/pictures/btn_edit_02.jpg'; 
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
	document.Edit.src="../../themes/beyondT/pictures/btn_save.jpg";
	document.Edit.title="Save";
	document.frmEmp.EditMode.value='1';
}
	
function addUpdate() {

	var cnt = document.frmEmp.txtEmpLastName;
	if(!alpha(cnt)) {
		alert("Field should be Alphabetic");
		cnt.focus();
		return;
	}

	var cnt = document.frmEmp.txtEmpFirstName;
	if(!alpha(cnt)) {
		alert("Field should be Alphabetic");
		cnt.focus();
		return;
	}
	
	var cnt = document.frmEmp.txtEmpMiddleName;
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
	/*
	if(document.frmEmp.cmbSalGrd.value=='0') {
		alert("Field should be selected");

		MM_showHideLayers('hidebg','','hide','personal','','hide','job','','show','workstation','','hide','econtact','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','hide');

		document.frmEmp.cmbSalGrd.focus();
		return;
	}
	*/

		document.frmEmp.sqlState.value = "UpdateRecord";
		document.frmEmp.submit();		
	}			

<? if ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'updatemode')) { 	?>
function reLoad() {
	location.href ="<?=$_SERVER['PHP_SELF']?>?id=<?=$this->getArr['id']?>&capturemode=updatemode&reqcode=<?=$this->getArr['reqcode']?>";
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

          	case 1 : MM_showHideLayers('hidebg','','hide','personal','','show','job','','hide','workstation','','hide','econtact','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','hide'); break;
          	case 2 : MM_showHideLayers('hidebg','','hide','personal','','hide','job','','show','workstation','','hide','econtact','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','hide'); break;
          	case 3 : MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','show','econtact','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','hide'); break;
            case 4 : MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','hide','econtact','','show','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','hide'); break;
            case 5 : MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','hide','econtact','','hide','contact','','show','passport','','hide','bank','','hide','attachment','','hide','other','','hide'); break;
          	case 6 : MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','hide','econtact','','hide','contact','','hide','passport','','show','bank','','hide','attachment','','hide','other','','hide'); break;
          	case 7 : MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','hide','econtact','','hide','contact','','hide','passport','','hide','bank','','show','attachment','','hide','other','','hide'); break;
          	case 8 : MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','hide','econtact','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','show','other','','hide'); break;
            case 9 : MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','show','econtact','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','hide'); break;

		}
}

function qshowpane1() {
			 MM_showHideLayers('hidebg','','hide','personal','','show','job','','hide','workstation','','hide','econtact','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','hide');
}

function setUpdate(opt) {
	
		switch(eval(opt)) {
          	case 0 : document.frmEmp.main.value=1; break;
          	case 1 : document.frmEmp.personalFlag.value=1; break;
          	case 2 : document.frmEmp.jobFlag.value=1; break;
          	//case 3 : document.frmEmp.workstationFlag.value=1; break;
          	case 3 : document.frmEmp.dependentsFlag.value=1;
          			document.frmEmp.workstationFlag.value=1; break;
            case 4 : document.frmEmp.econtactFlag.value=1; break;
            case 5 : document.frmEmp.contactFlag.value=1; break;
            case 6 : document.frmEmp.passportFlag.value=1; break;
            case 7 : document.frmEmp.bankFlag.value=1; break;
            case 8 : document.frmEmp.attachmentFlag.value=1; break;
            case 9 : document.frmEmp.childrenFlag.value=1; break;
		}	
}

function hierChg(cnt) {
		document.frmEmp.txtHiCode.value=cnt.value;
		qCombo(3);
}


function dwPopup() {
        var popup=window.open('../../templates/hrfunct/download.php?id=<?=isset($this->getArr['id']) ? $this->getArr['id'] : '' ?>&ATTACH=<?=isset($this->getArr['ATTACH']) ? $this->getArr['ATTACH'] : '' ?>','Downloads');
        if(!popup.opener) popup.opener=self;
}	

function popPhotoHandler() {
	var popup=window.open('../../templates/hrfunct/photohandler.php?id=<?=isset($this->getArr['id']) ? $this->getArr['id'] : ''?>','Photo','height=250,width=250');
	if(!popup.opener) popup.opener=self;
}

function delAttach() {
	
	var check = false;
	with (document.frmEmp) {
		for (var i=0; i < elements.length; i++) {
			if ((elements[i].name == 'chkattdel[]') && (elements[i].checked == true)){
				check = true;
			}
		}
	}

	if(!check){
		alert('Select at least one Attachment to Delete')
		return;
	}
		
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

	var check = false;
	with (document.frmEmp) {
		for (var i=0; i < elements.length; i++) {
			if ((elements[i].name == 'chkbrchdel[]') && (elements[i].checked == true)){
				check = true;
			}
		}
	}

	if(!check){
		alert('Select at least one record to Delete')
		return;
	}
	
	document.frmEmp.brchSTAT.value="DEL";
	qCombo(7);
}

function addBranch() {

	if(document.frmEmp.cmbBank.value == '0') {
		alert('Field should be Selected');
		document.frmEmp.cmbBank.focus();
		return;
	}

	if(document.frmEmp.cmbBranchCode.value == '0') {
		alert('Field should be Selected');
		document.frmEmp.cmbBranchCode.focus();
		return;
	}
	
	if(document.frmEmp.AccNo.value == '') {
		alert('Field Empty');
		document.frmEmp.AccNo.focus();
		return;
	}
	
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

function deldependent() { 
	
	var check = false;
	with (document.frmEmp) {
		for (var i=0; i < elements.length; i++) {
			if ((elements[i].name == 'chkdependentdel[]') && (elements[i].checked == true)) {
				check = true;
			}
		}
	}

	if(!check) {
		alert('Select at least one record to Delete')
		return;
	}
	
	document.frmEmp.dependentSTAT.value="DEL";
	qCombo(3);
}

function adddependent() {
	document.frmEmp.dependentSTAT.value="ADD";
	qCombo(3);
}

function viewdependent(pSeq) {
	document.frmEmp.action=document.frmEmp.action + "&DpSEQ=" + pSeq ;
	document.frmEmp.pane.value=3;
	document.frmEmp.submit();
}

function editdependent() {
	document.frmEmp.dependentSTAT.value="EDIT";
	qCombo(3);
}

function delPassport() {
	
	var check = false;
	with (document.frmEmp) {
		for (var i=0; i < elements.length; i++) {
			if ((elements[i].name == 'chkpassportdel[]') && (elements[i].checked == true)) {
				check = true;
			}
		}
	}

	if(!check) {
		alert('Select at least one record to Delete')
		return;
	}
	
	document.frmEmp.passportSTAT.value="DEL";
	qCombo(6);
}

function addPassport() {
	
	if(document.frmEmp.txtPPNo.value == '') {
		alert('Field Empty');
		document.frmEmp.txtPPNo.focus();
		return;
	}

	if(document.frmEmp.txtPPIssDat.value == '') {
		alert('Field Empty');
		document.frmEmp.txtPPIssDat.focus();
		return;
	}
	
	if(document.frmEmp.txtPPExpDat.value == '') {
		alert('Field Empty');
		document.frmEmp.txtPPExpDat.focus();
		return;
	}
	
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
	
function delDependents() {
	
	var check = false;
	with (document.frmEmp) {
		for (var i=0; i < elements.length; i++) {
			if ((elements[i].name == 'chkdepdel[]') && (elements[i].checked == true)) {
				check = true;
			}
		}
	}

	if(!check) {
		alert('Select at least one record to Delete')
		return;
	}
	
	document.frmEmp.depSTAT.value="DEL";
	qCombo(3);
}

function addDependents() {
	
	if(document.frmEmp.txtDepName.value == '') {
		alert('Field Empty');
		document.frmEmp.txtDepName.focus();
		return;
	}

	if(document.frmEmp.txtRelShip.value == '') {
		alert('Field Empty');
		document.frmEmp.txtRelShip.focus();
		return;
	}
		
	document.frmEmp.depSTAT.value="ADD";
	qCombo(3);
}

function viewDependents(dSeq) {
	document.frmEmp.action=document.frmEmp.action + "&DSEQ=" + dSeq ;
	document.frmEmp.pane.value=3;
	document.frmEmp.submit();
}

function editDependents() {
	document.frmEmp.depSTAT.value="EDIT";
	qCombo(3);
}

function delChildren() {
	
	var check = false;
	with (document.frmEmp) {
		for (var i=0; i < elements.length; i++) {
			if ((elements[i].name == 'chkchidel[]') && (elements[i].checked == true)) {
				check = true;
			}
		}
	}

	if(!check) {
		alert('Select at least one record to Delete')
		return;
	}
	
	document.frmEmp.chiSTAT.value="DEL";
	qCombo(9);
}

function addChildren() {
	
	if(document.frmEmp.txtChiName.value == '') {
		alert('Field Empty');
		document.frmEmp.txtChiName.focus();
		return;
	}

	if(document.frmEmp.DOB.value == '') {
		alert('Field Empty');
		document.frmEmp.DOB.focus();
		return;
	}
		
	document.frmEmp.chiSTAT.value="ADD";
	qCombo(9);
}

function viewChildren(cSeq) {
	document.frmEmp.action=document.frmEmp.action + "&CHSEQ=" + cSeq ;
	document.frmEmp.pane.value=9;
	document.frmEmp.submit();
}

function editChildren() {
	document.frmEmp.chiSTAT.value="EDIT";
	qCombo(3);
}

function delEContact() {
	
	var check = false;
	with (document.frmEmp) {
		for (var i=0; i < elements.length; i++) {
			if ((elements[i].name == 'chkecontactdel[]') && (elements[i].checked == true)) {
				check = true;
			}
		}
	}

	if(!check) {
		alert('Select at least one record to Delete')
		return;
	}
	
	document.frmEmp.econtactSTAT.value="DEL";
	qCombo(4);
}

function addEContact() {
	
	if(document.frmEmp.txtEConName.value == '') {
		alert('Field Empty');
		document.frmEmp.txtEConName.focus();
		return;
	}

	if(document.frmEmp.txtEConRel.value == '') {
		alert('Field Empty');
		document.frmEmp.txtEConRel.focus();
		return;
	}

	if(document.frmEmp.txtEConHmTel.value == '') {
		alert('Field Empty');
		document.frmEmp.txtEConHmTel.focus();
		return;
	}
	
	document.frmEmp.econtactSTAT.value="ADD";
	qCombo(4);
}

function viewEContact(ecSeq) {
	document.frmEmp.action=document.frmEmp.action + "&ECSEQ=" + ecSeq ;
	document.frmEmp.pane.value=4;
	document.frmEmp.submit();
}

function editEContact() {
	document.frmEmp.econtactSTAT.value="EDIT";
	qCombo(4);
}



</script>

<link href="../../themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("../../themes/beyondT/css/style.css"); </style>

</head>
<body onload="<?=(isset($this->postArr['pane']) && $this->postArr['pane']!='')?'qshowpane();':''?>;qshowpane1()">
<table width='100%' cellpadding='0' cellspacing='0' border='0'>
  <tr>
    <td valign='top'>&nbsp; </td>
    <td width='100%'><h2 align="center"><?=$employeeinformation?></h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'>
    <b><div align="right" id="status"></div></b></td>
  </tr>
</table>

<?
		if ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'addmode')) {
?>

<form name="frmEmp" id="frmEmp" method="post" action="<?=$_SERVER['PHP_SELF']?>?reqcode=<?=$this->getArr['reqcode']?>&capturemode=<?=$this->getArr['capturemode']?>" enctype="multipart/form-data">
<input type="hidden" name="sqlState">
<input type="hidden" name="pane" value="<?=(isset($this->postArr['pane']) && $this->postArr['pane']!='') ? $this->postArr['pane'] : ''?>">


<input type="hidden" name="econtactFlag" value="<?=isset($this->postArr['econtactFlag'])? $this->postArr['econtactFlag'] : '0'?>">

<input type="hidden" name="dependentFlag" value="<?=isset($this->postArr['dependentFlag'])? $this->postArr['dependentFlag'] : '0'?>">

<input type="hidden" name="passportFlag" value="<?=isset($this->postArr['passportFlag'])? $this->postArr['passportFlag'] : '0'?>">
<input type="hidden" name="dependentsFlag" value="<?=isset($this->postArr['dependentsFlag'])? $this->postArr['dependentsFlag'] : '0'?>">
<input type="hidden" name="childrenFlag" value="<?=isset($this->postArr['childrenFlag'])? $this->postArr['childrenFlag'] : '0'?>">
<input type="hidden" name="bankFlag" value="<?=isset($this->postArr['bankFlag'])? $this->postArr['bankFlag'] : '0'?>">
<input type="hidden" name="attachmentFlag" value="<?=isset($this->postArr['attachmentFlag'])? $this->postArr['attachmentFlag'] : '0'?>">

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
				<td><?=$code?></td>
				<td><input type="hidden" name="txtEmpID" value=<?=$this->popArr['newID']?>><strong><?=$this->popArr['newID']?></strong></td>
			  </tr>
			  <tr> 
				<td><font color=#ff0000>*</font><?=$lastname?></td>
				<td> <input type="text" name="txtEmpLastName" <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($this->postArr['txtEmpLastName']))?$this->postArr['txtEmpLastName']:''?>"></td>
				<td>&nbsp;</td>
				<td><font color=#ff0000>*</font><?=$firstname?></td>
				<td> <input type="text" name="txtEmpFirstName" <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($this->postArr['txtEmpFirstName']))?$this->postArr['txtEmpFirstName']:''?>"></td>
			  </tr>
			  <tr> 
				<td><font color=#ff0000>*</font><?=$middlename?></td>
				<td> <input type="text" name="txtEmpMiddleName" <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($this->postArr['txtEmpMiddleName']))?$this->postArr['txtEmpMiddleName']:''?>"></td>
				<td>&nbsp;</td>
			  <td><?=$nickname?></td>
				<td> <input type="text" name="txtEmpNickName" <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($this->postArr['txtEmpNickName']))?$this->postArr['txtEmpNickName']:''?>"></td>
			  </tr>
			 <tr>
				<td><?=$photo?></td>
				<td> <input type="file" name='photofile' <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($this->postArr['photofile']))?$this->postArr['photofile']:''?>"></td>
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

    <table border="0" align="center" >
    <tr>
    <td><img title="Back" onmouseout="this.src='../../themes/beyondT/pictures/btn_back.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_back_02.jpg';"  src="../../themes/beyondT/pictures/btn_back.jpg" onclick="goBack();"></td>
    <td>
					<?	if($locRights['add']) { ?>
					        <img border="0" title="Save" onClick="addSave();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
					<? 	} else { ?>
					        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_save.jpg">
					<?	} ?>
    </td>
    <td>&nbsp;</td>
    <td><img onClick="document.frmEmp.reset();" onmouseout="this.src='../../themes/beyondT/pictures/btn_clear.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_clear_02.jpg';" src="../../themes/beyondT/pictures/btn_clear.jpg"></td>
    </tr>
    </table>


<table width="600" align="center" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><table border="0" cellpadding="0" cellspacing="0" width="600">
        <tr>
          <td><img name="tabs_r1_c1" src="../../themes/beyondT/pictures/tabs_r1_c1.jpg" width="28" height="71" border="0" alt=""></td>

          <td><a href="#" onClick="MM_showHideLayers('hidebg','','hide','personal','','show','job','','hide','workstation','','hide','econtact','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','hide')">

		  	  <img name="tabs_r1_c2" src="../../themes/beyondT/pictures/tabs_r1_c2.jpg" width="62" height="71" border="0" alt=""></a></td>

          <td><a href="#" onClick="MM_showHideLayers('hidebg','','hide','personal','','hide','job','','show','workstation','','hide','econtact','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','hide')">

		      <img name="tabs_r1_c3" src="../../themes/beyondT/pictures/tabs_r1_c3.jpg" width="56" height="71" border="0" alt=""></a></td>

         <td><a href="#" onClick="MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','show','econtact','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','hide')">

		      <img name="tabs_r1_c4" src="../../themes/beyondT/pictures/tabs_r1_c4.jpg" width="62" height="71" border="0" alt=""></a></td>

          <td><a href="#" onClick="MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','hide','econtact','','hide','contact','','show','passport','','hide','bank','','hide','attachment','','hide','other','','hide')">

		      <img name="tabs_r1_c6" src="../../themes/beyondT/pictures/tabs_r1_c6.jpg" width="59" height="71" border="0" alt=""></a></td>
		      
        <td><a href="#" onClick="MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','hide','econtact','','show','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','hide')">

		      <img name="tabs_r1_c5" src="../../themes/beyondT/pictures/tabs_r1_c5.jpg" width="60" height="71" border="0" alt=""></a></td>

            <td><a href="#" onClick="MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','hide','econtact','','hide','contact','','hide','passport','','show','bank','','hide','attachment','','hide','other','','hide')">

		  	  <img name="tabs_r1_c7" src="../../themes/beyondT/pictures/tabs_r1_c7.jpg" width="61" height="71" border="0" alt=""></a></td>

<!--      <td><a href="#" onClick="MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','hide','econtact','','hide','contact','','hide','passport','','hide','bank','','show','attachment','','hide','other','','hide')">

		      <img name="tabs_r1_c8" src="../../themes/beyondT/pictures/tabs_r1_c8.jpg" width="60" height="71" border="0" alt=""></a></td> -->

          <td><a href="#" onClick="MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','hide','econtact','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','show','other','','hide')">

		      <img name="tabs_r1_c9" src="../../themes/beyondT/pictures/tabs_r1_c9.jpg" width="60" height="71" border="0" alt=""></a></td>

<!--          <td><a href="#" onClick="MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','hide','econtact','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','show')">


		      <img name="tabs_r1_c10" src="../../themes/beyondT/pictures/tabs_r1_c10.jpg" width="54" height="71" border="0" alt=""></a></td>-->
          <td><img name="tabs_r1_c11" src="../../themes/beyondT/pictures/tabs_r1_c11.jpg" width="38" height="71" border="0" alt=""></td>
        </tr>
        <tr>
          <td colspan="11"><img name="tabs_r2_c1" src="../../themes/beyondT/pictures/tabs_r2_c1.jpg" width="600" height="17" border="0" alt=""></td>
          <td><img src="../../images/spacer.gif" width="1" height="17" border="0" alt=""></td>
        </tr>
        <tr>
          <td colspan="11"><img name="tabs_r3_c1" src="../../themes/beyondT/pictures/tabs_r3_c1.jpg" width="600" height="27" border="0" alt=""></td>
          <td><img src="../../images/spacer.gif" width="1" height="27" border="0" alt=""></td>
        </tr>
    </table></td>
  </tr>

  <tr>
    <td align="center"><div id="personal" style="position:absolute; z-index:2; width: 540px; visibility: hidden; left: 100px; top: 360px;">
	  <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><table height="200" border="0" cellpadding="0" cellspacing="0">
<tr>
					<td><font color=#ff0000>*</font><?=$ssnno?></td>
					<td><input type="text" name="txtNICNo" <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($this->postArr['txtNICNo']))?$this->postArr['txtNICNo']:''?>"></td>
					<td width="50">&nbsp;</td>
					<td><?=$nationality?></td>
					<td><select <?=$locRights['add'] ? '':'disabled'?> name="cmbNation">
						<option value="0"><?=$selectnatio?></option>
<?
					$nation = $this->popArr['nation'];
					 for($c=0;$nation && $c < count($nation);$c++)
						            echo '<option value=' . $nation[$c][0] . '>' . $nation[$c][1] .'</option>';
?>					
					</select></td>
				</tr>
				<tr>
				<td><?=$sinno?></td>
					<td><input type="text" name="txtSINNo" <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($this->postArr['txtSINNo']))?$this->postArr['txtSINNo']:''?>"></td>
					<td width="50">&nbsp;</td>
				<td><?=$dateofbirth?></td>
				<td><input type="text" name="DOB" readonly value=<?=(isset($this->postArr['DOB']))?$this->postArr['DOB']:''?>>&nbsp;<input type="button" <?=$locRights['add'] ? '':'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.DOB);return false;"></td>
				</tr>
				<tr>
				<td><?=$otherid?></td>
				<td><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtOtherID" value="<?=(isset($this->postArr['txtOtherID']))?$this->postArr['txtOtherID']:''?>"></td>
				<td>&nbsp;</td>
				<td><?=$maritalstatus?></td>
				<td><select <?=$locRights['add'] ? '':'disabled'?> name="cmbMarital">
					<option><?=$selmarital?></option>
<?					
					for($c=0;count($arrMStat)>$c;$c++)
						if(isset($this->postArr['cmbMarital']) && $this->postArr['cmbMarital']==$arrMStat[$c])
						    echo "<option selected>" .$arrMStat[$c]."</option>";
						else 
						    echo "<option>" .$arrMStat[$c]."</option>";
?>
				</select></td>
				</tr>
				<tr>
				<td><?=$smoker?></td>
			  <td><input type="checkbox" <?=$locRights['add'] ? '':'disabled'?> name="chkSmokeFlag" <?=(isset($this->postArr['chkSmokeFlag']) && $this->postArr['chkSmokeFlag']=='1'?'checked':'')?> value="1"></td>
			  <td width="50">&nbsp;</td>
				<td><?=$gender?></td>
				<td valign="middle">Male<input <?=$locRights['add'] ? '':'disabled'?> type="radio" name="optGender" value="1" checked>		Female<input <?=$locRights['add'] ? '':'disabled'?> type="radio" name="optGender" value="2" <?=(isset($this->postArr['optGender']) && isset($this->postArr['optGender'])==2)?'checked':''?>></td>
				</tr>
				<tr>
				<td><?=$dlicenno?></td>
				<td><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtLicenNo" value="<?=(isset($this->postArr['txtLicenNo']))?$this->postArr['txtLicenNo']:''?>"></td>
				<td>&nbsp;</td>
				<td><?=$licexpdate?></td>
				<td><input type="text" readonly name="txtLicExpDate" value=<?=(isset($this->postArr['txtLicExpDate']))?$this->postArr['txtLicExpDate']:''?>>&nbsp;<input type="button" <?=$locRights['add'] ? '':'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtLicExpDate);return false;"></td>
				</tr> 
				<tr>
				<td><?=$militaryservice?></td>
				<td><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtMilitarySer" value="<?=(isset($this->postArr['txtMilitarySer']))?$this->postArr['txtMilitarySer']:''?>"></td>
				<td>&nbsp;</td>
				<td><?=$ethnicrace?></td>
					<td><select <?=$locRights['add'] ? '':'disabled'?> name="cmbEthnicRace">
						<option value="0"><?=$selethnicrace?></option>
<?  			    	$ethRace = $this->popArr['ethRace'];
						      for($c=0;$ethRace && $c < count($ethRace);$c++)
						            echo '<option value=' . $ethRace[$c][0] . '>' . $ethRace[$c][1] .'</option>';
						    ?>			
					</select></td>
				</tr>
				</table></td>
          <td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
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
	<div id="job" style="position:absolute; z-index:2; width: 540px; visibility: hidden; left: 100px; top: 360px;">
	  <table border="0" cellpadding="0" cellspacing="0" >
        <tr>
          <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><table height="150" border="0" cellpadding="0" cellspacing="0">
<tr>
			   <td><?=$jobtitle?></td>
			  <td><select name="cmbJobTitle" <?=$locRights['add'] ? '':'disabled'?> onchange="document.getElementById('status').innerHTML = 'Please Wait....'; xajax_assEmpStat(this.value);">
			  		<option value="0">---Select <?=$jobtitle?>---</option>
			  		<? $jobtit = $this->popArr['jobtit'];
			  			for ($c=0; $jobtit && count($jobtit)>$c ; $c++) {
			  				echo "<option value='" . $jobtit[$c][0] . "'>" .$jobtit[$c][1]. "</option>";
			  			} ?>
			  </select> </td>
			  <td width="50">&nbsp;</td>
			  <td><?=$empstatus?></td>
			  <td><select <?=$locRights['add'] ? '':'disabled'?> name="cmbType">
			  		<option value="0"><?=$selempstat?></option>
<?					for($c=0;count($arrEmpType)>$c;$c++)
						if(isset($this->postArr['cmbType']) && $this->postArr['cmbType']==$arrEmpType[$c])
							echo "<option selected>" .$arrEmpType[$c]. "</option>";
						else
							echo "<option>" .$arrEmpType[$c]. "</option>";
?>			        
			  </select></td>
              </tr>
			  <tr>
			  <td><?=$eeocategory?> </td>
			  <td><select <?=$locRights['add'] ? '':'disabled'?> name="cmbEEOCat">
			  		<option value="0"><?=$seleeocat?></option>
<?  			    	$eeojobcat = $this->popArr['eeojobcat'];
						      for($c=0;$eeojobcat && $c < count($eeojobcat);$c++)
						            echo '<option value=' . $eeojobcat[$c][0] . '>' . $eeojobcat[$c][1] .'</option>';
						    ?>			
					</select></td>
			   <td width="50">&nbsp;</td>
			  <td><?=$location?></td>
				<td><select <?=$locRights['add'] ? '':'disabled'?> name="cmbLocation">
						<option value="0"><?=$selectlocation?></option>
<?						$loc = $this->popArr['loc'];
						for($c=0;$loc && count($loc)>$c;$c++)
						    echo '<option value=' . $loc[$c][0] . '>' . $loc[$c][1] .'</option>';
							    
?>			  
			  </select></td>
			  </tr>
			  <tr>
			  <td><?=$joindate?></td>
				<td><input type="text" readonly name="txtJoinedDate" value=<?=(isset($this->postArr['txtJoinedDate']))?$this->postArr['txtJoinedDate']:''?>>&nbsp;<input type="button" <?=$locRights['add'] ? '':'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtJoinedDate);return false;"></td>
			  </tr>
			  </table></td>
          <td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
    </div>
   
   
	<div id="workstation" style="position:absolute; z-index:2; width: 540px; visibility: hidden; left: 100px; top: 360px;">
	  <table><tr><td><table border="0" cellpadding="0" cellspacing="0" >
        <tr>
          <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><table height="150" border="0" cellpadding="0" cellspacing="0" onclick="setUpdate(3)" onkeypress="setUpdate(3)">
         <th><h3><?=$dependents?></h3></th>
			<tr>	  
			 <td><?=$name?></td>
			  <td><input type="text" name="txtDepName" <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($this->postArr['txtDepName']))?$this->postArr['txtDepName']:''?>"></td>
			</tr>	
			<tr>	  
			 <td><?=$relationship?></td>
			  <td><input type="text" name="txtRelShip" <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($this->postArr['txtRelShip']))?$this->postArr['txtRelShip']:''?>"></td>
			</tr>			  
			  </table></td>
			   <td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table></td>
      <td><table border="0" cellpadding="0" cellspacing="0" >
        <tr>
          <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><table height="150" border="0" cellpadding="0" cellspacing="0" onclick="setUpdate(9)" onkeypress="setUpdate(9)">
          <th><h3><?=$children?></h3></th>
		<tr>
	   	<td><?=$name?></td>
			  <td><input type="text" name="txtChiName" <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($this->postArr['txtChiName']))?$this->postArr['txtChiName']:''?>"></td>
			  </tr>
		<tr>
		<td><?=$dateofbirth?></td>
			<td><input type="text" readonly name="ChiDOB" value=<?=(isset($this->postArr['ChiDOB']))?$this->postArr['ChiDOB']:''?>>&nbsp;<input type="button" <?=$locRights['add'] ? '':'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.ChiDOB);return false;"></td>
				</tr> 
			  </table></td>
			   <td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table></td></tr></table>
    </div>
    
    
	<div id="econtact" style="position:absolute; z-index:2; width: 540px; visibility: hidden; left: 100px; top: 360px;">
	  <table border="0" cellpadding="0" cellspacing="0" >
        <tr>
          <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><table height="150" border="0" cellpadding="0" cellspacing="0" onclick="setUpdate(4)" onkeypress="setUpdate(4)">
         	<tr>	  
			 <td><?=$name?></td>
			  <td><input type="text" name="txtEConName" <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($this->postArr['txtEConName']))?$this->postArr['txtEConName']:''?>"></td>
			<td width="50">&nbsp;</td>  
			 <td><?=$relationship?></td>
			  <td><input type="text" name="txtEConRel" <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($this->postArr['txtEConRel']))?$this->postArr['txtEConRel']:''?>"></td>
			</tr>
			<tr>
			 <td><?=$hmtele?></td>
			 <td><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtEConHmTel" value="<?=(isset($this->postArr['txtEConHmTel']))?$this->postArr['txtEConHmTel']:''?>"></td>
			 <td width="50">&nbsp;</td>
			<td><?=$mobile?></td>
			 <td><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtEConMobile" value="<?=(isset($this->postArr['txtEConMobile']))?$this->postArr['txtEConMobile']:''?>"></td>
			 </tr>
			 <tr>
			<td><?=$worktele?></td>
			 <td><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtEConWorkTel" value="<?=(isset($this->postArr['txtEConWorkTel']))?$this->postArr['txtEConWorkTel']:''?>"></td>
			</tr>			  
			  </table></td>
			   <td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
    </div>
    
      
	<div id="contact" style="position:absolute; z-index:2; width: 540px; visibility: hidden; left: 100px; top: 360px;">
	  <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><table height="250" border="0" cellpadding="0" cellspacing="0">
<tr>
		<td><?=$country?></td>
			 <td><select <?=$locRights['add'] ? '':'disabled'?> name="cmbCountry" onchange="document.getElementById('status').innerHTML = 'Please Wait....'; xajax_populateStates(this.value);">
						  		<option value="0"><?=$selectcountry?></option>
<?
								$cntlist = $this->popArr['cntlist'];
								for($c=0;$cntlist && count($cntlist)>$c;$c++) { 
									echo "<option value='" .$cntlist[$c][0] . "'>" . $cntlist[$c][1] . '</option>';
								}
					?>	 
			 </select></td>
			   <td width="50">&nbsp;</td>
			   <td><?=$street1?></td>
			 <td><input type="text" name="txtStreet1" <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($this->postArr['txtStreet1']))?$this->postArr['txtStreet1']:''?>"></td>
			 </tr>
			 <tr>
			 <td><?=$state?></td>
			  <td><select <?=$locRights['add'] ? '':'disabled'?> name="cmbProvince" onchange="document.getElementById('status').innerHTML = 'Please Wait....'; xajax_populateDistrict(this.value);">
						  		<option value="0"><?=$selstate?></option>
						  </select></td>
			  <td width="50">&nbsp;</td>
			  <td><?=$street2?></td>
			 <td><input type="text" name="txtStreet2" <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($this->postArr['txtStreet2']))?$this->postArr['txtStreet2']:''?>"></td>
			  </tr>
			 <tr>
			 <td><?=$city?></td>
			 <td><select <?=$locRights['add'] ? '':'disabled'?> name="cmbCity">
					<option value="0"><?=$selcity?></option>
				</select></td>
			<td width="50">&nbsp;</td>
			 <td><?=$zipcode?></td>
			 <td><input type="text" name="txtzipCode" <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($this->postArr['txtzipCode']))?$this->postArr['txtzipCode']:''?>"></td>
			 </tr>
			 <tr>
			 <td><?=$hmtele?></td>
			 <td><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtHmTelep" value="<?=(isset($this->postArr['txtHmTelep']))?$this->postArr['txtHmTelep']:''?>"></td>
			 <td width="50">&nbsp;</td>
			<td><?=$mobile?></td>
			 <td><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtMobile" value="<?=(isset($this->postArr['txtMobile']))?$this->postArr['txtMobile']:''?>"></td>
			 </tr>
			 <tr>
			<td><?=$worktele?></td>
			 <td><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtWorkTelep" value="<?=(isset($this->postArr['txtWorkTelep']))?$this->postArr['txtWorkTelep']:''?>"></td>
			 <td width="50">&nbsp;</td>
			 <td><?=$workemail?></td>
			 <td><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtWorkEmail" value="<?=(isset($this->postArr['txtWorkEmail']))?$this->postArr['txtWorkEmail']:''?>"></td>
			 </tr>
			 <tr>
			<td><?=$otheremail?></td>
			 <td><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtOtherEmail" value="<?=(isset($this->postArr['txtOtherEmail']))?$this->postArr['txtOtherEmail']:''?>"></td>
			 </tr>
			 
			 </table></td>
          <td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
    </div>
    
       
	<div id="passport" style="position:absolute; z-index:2; width: 540px; visibility: hidden; left: 100px; top: 360px;">
	  <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><table onclick="setUpdate(6)" onkeypress="setUpdate(6)" height="200" border="0" cellpadding="0" cellspacing="0">
				<tr>
			 <td><?=$passport?> <input type="radio" checked <?=$locRights['add'] ? '':'disabled'?> name="PPType" value="1"></td><td><?=$visa?><input type="radio" <?=$locRights['add'] ? '':'disabled'?> <?=(isset($this->postArr['PPType']) && $this->postArr['PPType']!='1') ? 'checked':''?> name="PPType" value="2"></td>
			  <td width="50">&nbsp;</td>
		  	  <td><?=$citizenship?></td>
                <td><select <?=$locRights['add'] ? '':'disabled'?> name="cmbPPCountry">
                		<option value="0"><?=$selectcountry?></option>
<?				$list = $this->popArr['ppcntlist'];
				for($c=0;$list && count($list)>$c;$c++)
					if(isset($this->postArr['cmbPPCountry']) && $this->postArr['cmbPPCountry']==$list[$c][0])
					    echo "<option selected value='" . $list[$c][0] . "'>" . $list[$c][1]. "</option>";
					else
					    echo "<option value='" . $list[$c][0] . "'>" . $list[$c][1]. "</option>";
?>			 
				</td>
				</tr>
              <tr>
              <td><?=$passvisano?></td>
                <td><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtPPNo" value="<?=isset($this->postArr['txtPPNo']) ? $this->postArr['txtPPNo'] : ''?>"></td>
               <td width="50">&nbsp;</td>
                <td><?=$issueddate?></td>
                <td><input type="text" readonly name="txtPPIssDat" value=<?=isset($this->postArr['txtPPIssDat']) ? $this->postArr['txtPPIssDat'] : ''?>>&nbsp;<input type="button" <?=$locRights['add'] ? '':'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtPPIssDat);return false;"></td>
              </tr>
              <tr>
              <td><?=$i9status?></td>
                <td><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtI9status" value="<?=isset($this->postArr['txtI9status']) ? $this->postArr['txtI9status'] : ''?>"></td>
                <td width="50">&nbsp;</td>
                <td><?=$dateofexp?></td>
                <td><input type="text" readonly name="txtPPExpDat" value=<?=isset($this->postArr['txtPPExpDat']) ? $this->postArr['txtPPExpDat'] : ''?>>&nbsp;<input type="button" <?=$locRights['add'] ? '':'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtPPExpDat);return false;"></td>
              </tr>
               <tr>
                <td><?=$i9reviewdate?></td>
                <td><input type="text" readonly name="txtI9ReviewDat" value=<?=isset($this->postArr['txtI9ReviewDat']) ? $this->postArr['txtI9ReviewDat'] : ''?>>&nbsp;<input type="button" <?=$locRights['add'] ? '':'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtI9ReviewDat);return false;"></td>
                <td width="50">&nbsp;</td>
      			<td><?=$comments?></td>
                <td><textarea name="txtComments"<?=$locRights['add'] ? '':'disabled'?> value="<?=isset($this->postArr['txtComments']) ? $this->postArr['txtComments'] : ''?>"></textarea></td>
               </tr>
          </table></td>
          <td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
    </div>
    
        
	<div id="bank" style="position:absolute; z-index:2; width: 540px; visibility: hidden; left: 100px; top: 360px;">
	  
    </div>
	<div id="attachment" style="position:absolute; z-index:2; width: 540px; visibility: hidden; left: 100px; top: 360px;">
	  <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><table onclick="setUpdate(8)" onkeypress="setUpdate(8)" width="352" height="200" border="0" cellpadding="0" cellspacing="0">
              <tr>
				<td><?=$path?></td>
				<td><input type="file" name="ufile"></td>
              </tr>
              <tr>
              	<td><?=$description?></td>
              	<td><textarea name="txtAttDesc"></textarea></td>
              </tr>
          </table></td>
          <td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
    </div>
    
          
	<div id="other" style="position:absolute; z-index:2; width: 540px; visibility: hidden; left: 100px; top: 360px;">
	  no
    </div>
	</td>
  </tr>
</table>
            </td>
          </tr>
    </table>
    </form>
<iframe width=174 height=189 name="gToday:normal:agenda.js" id="gToday:normal:agenda.js" src="../../scripts/ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; top:-500px; left:-500px;">
</iframe>
</body>
</html>

<? } elseif ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'updatemode')) { 
	$edit = $this->popArr['editMainArr'];
?>

<form name="frmEmp" id="frmEmp" method="post" action="<?=$_SERVER['PHP_SELF']?>?id=<?=$this->getArr['id']?>&reqcode=<?=$this->getArr['reqcode']?>&capturemode=<?=$this->getArr['capturemode']?>" enctype="multipart/form-data">
<input type="hidden" name="sqlState">
<input type="hidden" name="pane" value="<?=(isset($this->postArr['pane']) && $this->postArr['pane']!='')?$this->postArr['pane']:''?>">

<input type="hidden" name="main" value="<?=isset($this->postArr['main'])? $this->postArr['main'] : '0'?>">
<input type="hidden" name="personalFlag" value="<?=isset($this->postArr['personalFlag'])? $this->postArr['personalFlag'] : '0'?>">
<input type="hidden" name="jobFlag" value="<?=isset($this->postArr['jobFlag'])? $this->postArr['jobFlag'] : '0'?>">

<input type="hidden" name="workstationFlag" value="<?=isset($this->postArr['workstationFlag'])? $this->postArr['workstationFlag'] : '0'?>">
<input type="hidden" name="childrenFlag" value="<?=isset($this->postArr['childrenFlag'])? $this->postArr['childrenFlag'] : '0'?>">
<input type="hidden" name="econtactFlag" value="<?=isset($this->postArr['econtactFlag'])? $this->postArr['econtactFlag'] : '0'?>">

<input type="hidden" name="dependentFlag" value="<?=isset($this->postArr['dependentFlag'])? $this->postArr['dependentFlag'] : '0'?>">

<input type="hidden" name="contactFlag" value="<?=isset($this->postArr['contactFlag'])? $this->postArr['contactFlag'] : '0'?>">
<input type="hidden" name="attSTAT" value="">
<input type="hidden" name="EditMode" value="<?=isset($this->postArr['EditMode'])? $this->postArr['EditMode'] : '0'?>">
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
                  <td><table onclick="setUpdate(0)" onkeypress="setUpdate(0)" width="100%" border="0" cellpadding="5" cellspacing="0" class="">
			  <tr> 
				<td><?=$code?></td>
				<td><strong><input type="hidden" name="txtEmpID" value="<?=$this->getArr['id']?>"><?=$this->getArr['id']?></strong></td>
			  </tr>
			  <tr> 
				<td><?=$lastname?></td>
				<td> <input type="text" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="txtEmpLastName" value="<?=(isset($this->postArr['txtEmpLastName']))?$this->postArr['txtEmpLastName']:$edit[0][1]?>"></td>
				<td>&nbsp;</td>
				<td><?=$firstname?></td>
				<td> <input type="text" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="txtEmpFirstName" value="<?=(isset($this->postArr['txtEmpFirstName']))?$this->postArr['txtEmpFirstName']:$edit[0][2]?>"></td>
			  </tr>
			  <tr> 
				<td><?=$middlename?></td>
				<td> <input type="text" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="txtEmpMiddleName" value="<?=(isset($this->postArr['txtEmpMiddleName']))?$this->postArr['txtEmpMiddleName']:$edit[0][4]?>"></td>
				<td>&nbsp;</td>
			  <td><?=$nickname?></td>
				<td> <input type="text" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="txtEmpNickName" value="<?=(isset($this->postArr['txtEmpNickName']))?$this->postArr['txtEmpNickName']:$edit[0][3]?>"></td>
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
                  <td width="339" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="11"><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="12" border="0" alt=""></td>
                </tr>
                <tr>
                  <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
                    <tr>
                    <td width="100%" align="center"><img width="100" height="120" src="../../templates/hrfunct/photohandler.php?id=<?=$this->getArr['id']?>&action=VIEW"></td>
                    </tr>
                    <tr>
                    <td width="100%" align="center"><input type="button" value="Browse" onclick="popPhotoHandler()"></td>
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
    <td><img title="Back" onmouseout="this.src='../../themes/beyondT/pictures/btn_back.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_back_02.jpg';"  src="../../themes/beyondT/pictures/btn_back.jpg" onclick="goBack();"></td>
    <td>
<?			if($locRights['edit']) { ?>
			        <img src="<?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '../../themes/beyondT/pictures/btn_save.jpg' : '../../themes/beyondT/pictures/btn_edit.jpg'?>" title="Edit" onmouseout="mout();" onmouseover="mover();" name="Edit" onClick="edit();">
<?			} else { ?>
			        <img src="../../themes/beyondT/pictures/btn_edit.jpg" onClick="alert('<?=$sysConst->accessDenied?>');">
<?			}  ?>
    </td>
    <td><img src="../../themes/beyondT/pictures/btn_clear.jpg" onmouseout="this.src='../../themes/beyondT/pictures/btn_clear.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_clear_02.jpg';" onClick="reLoad();" ></td>
    </tr>
    </table>


<table width="600" align="center" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><table border="0" cellpadding="0" cellspacing="0" width="600">
        <tr>
          <td><img name="tabs_r1_c1" src="../../themes/beyondT/pictures/tabs_r1_c1.jpg" width="28" height="71" border="0" alt=""></td>

          <td><a href="#" onClick="MM_showHideLayers('hidebg','','hide','personal','','show','job','','hide','workstation','','hide','econtact','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','hide')">

		  	  <img name="tabs_r1_c2" src="../../themes/beyondT/pictures/tabs_r1_c2.jpg" width="62" height="71" border="0" alt=""></a></td>

          <td><a href="#" onClick="MM_showHideLayers('hidebg','','hide','personal','','hide','job','','show','workstation','','hide','econtact','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','hide')">

		      <img name="tabs_r1_c3" src="../../themes/beyondT/pictures/tabs_r1_c3.jpg" width="56" height="71" border="0" alt=""></a></td>

         <td><a href="#" onClick="MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','show','econtact','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','hide')">

		      <img name="tabs_r1_c4" src="../../themes/beyondT/pictures/tabs_r1_c4.jpg" width="62" height="71" border="0" alt=""></a></td> 

          <td><a href="#" onClick="MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','hide','econtact','','hide','contact','','show','passport','','hide','bank','','hide','attachment','','hide','other','','hide')">

		      <img name="tabs_r1_c6" src="../../themes/beyondT/pictures/tabs_r1_c6.jpg" width="59" height="71" border="0" alt=""></a></td>

		 <td><a href="#" onClick="MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','hide','econtact','','show','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','hide')">

		      <img name="tabs_r1_c5" src="../../themes/beyondT/pictures/tabs_r1_c5.jpg" width="60" height="71" border="0" alt=""></a></td>


          <td><a href="#" onClick="MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','hide','econtact','','hide','contact','','hide','passport','','show','bank','','hide','attachment','','hide','other','','hide')">

		  	  <img name="tabs_r1_c7" src="../../themes/beyondT/pictures/tabs_r1_c7.jpg" width="61" height="71" border="0" alt=""></a></td>

<!--          <td><a href="#" onClick="MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','hide','econtact','','hide','contact','','hide','passport','','hide','bank','','show','attachment','','hide','other','','hide')">

		      <img name="tabs_r1_c8" src="../../themes/beyondT/pictures/tabs_r1_c8.jpg" width="60" height="71" border="0" alt=""></a></td> -->

          <td><a href="#" onClick="MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','hide','econtact','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','show','other','','hide')">

		      <img name="tabs_r1_c9" src="../../themes/beyondT/pictures/tabs_r1_c9.jpg" width="60" height="71" border="0" alt=""></a></td>

<!--          <td><a href="#" onClick="MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','hide','econtact','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','show')">

		      <img name="tabs_r1_c10" src="../../themes/beyondT/pictures/tabs_r1_c10.jpg" width="54" height="71" border="0" alt=""></a></td>-->
          <td><img name="tabs_r1_c11" src="../../themes/beyondT/pictures/tabs_r1_c11.jpg" width="38" height="71" border="0" alt=""></td>
        </tr>
        <tr>
          <td colspan="11"><img name="tabs_r2_c1" src="../../themes/beyondT/pictures/tabs_r2_c1.jpg" width="600" height="17" border="0" alt=""></td>
          <td><img src="../../images/spacer.gif" width="1" height="17" border="0" alt=""></td>
        </tr>
        <tr>
          <td colspan="11"><img name="tabs_r3_c1" src="../../themes/beyondT/pictures/tabs_r3_c1.jpg" width="600" height="27" border="0" alt=""></td>
          <td><img src="../../images/spacer.gif" width="1" height="27" border="0" alt=""></td>
        </tr>
               
    </table></td>
   
    
   </tr>
 

  <tr>
  	
    <td align="center">
    <div id="personal" style="position:absolute; z-index:2; width: 540px; visibility: hidden; left: 100px; top: 360px;">
	  <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><table onclick="setUpdate(1)" onkeypress="setUpdate(1)" height="200" border="0" cellpadding="0" cellspacing="0">
<?
		  $edit = $this->popArr['editPersArr'];
?>

          <tr>
					<td><font color=#ff0000>*</font><?=$ssnno?></td>
					<td><input type="text" name="txtNICNo" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> value="<?=(isset($this->postArr['txtNICNo']))?$this->postArr['txtNICNo']:$edit[0][7]?>"></td>
					<td width="50">&nbsp;</td>
					<td><?=$nationality?></td>
					<td><select <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="cmbNation">
						<option value="0"><?=$selectnatio?></option>
<?
					$nation = $this->popArr['nation'];
					for($c=0;$nation && count($nation)>$c;$c++)
						if(isset($this->postArr['cmbNation'])) {
							if($this->postArr['cmbNation']==$nation[$c][0])
							    echo "<option selected value='" . $nation[$c][0] . "'>" .$nation[$c][1]. "</option>";
							else
							    echo "<option value='" . $nation[$c][0] . "'>" .$nation[$c][1]. "</option>";
						} elseif($edit[0][4]==$nation[$c][0]) 
							    echo "<option selected value='" . $nation[$c][0] . "'>" .$nation[$c][1]. "</option>";
							else
							    echo "<option value='" . $nation[$c][0] . "'>" .$nation[$c][1]. "</option>";
						
?>					
					</select></td>
				</tr>
				<tr>
				<td><?=$sinno?></td>
					<td><input type="text" name="txtSINNo" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> value="<?=(isset($this->postArr['txtSINNo']))?$this->postArr['txtSINNo']:$edit[0][8]?>"></td>
					<td width="50">&nbsp;</td>
				<td><?=$dateofbirth?></td>
				<td><input type="text" readonly name="DOB" value=<?=(isset($this->postArr['DOB']))?$this->postArr['DOB']:$edit[0][3]?>>&nbsp;<input type="button" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.DOB);return false;"></td>
				</tr>
				<tr>
				<td><?=$otherid?></td>
				<td><input type="text" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="txtOtherID" value="<?=(isset($this->postArr['txtOtherID']))?$this->postArr['txtOtherID']:$edit[0][9]?>"></td>
				<td>&nbsp;</td>
				<td><?=$maritalstatus?></td>
				<td><select <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="cmbMarital">
					<option value="0"><?=$selmarital?></option>
<?					
					for($c=0;count($arrMStat)>$c;$c++)
						if(isset($this->postArr['cmbMarital'])) {
						 	if($this->postArr['cmbMarital']==$arrMStat[$c])
						    echo "<option selected>" .$arrMStat[$c]."</option>";
						else 
						    echo "<option>" .$arrMStat[$c]."</option>";
						} elseif($edit[0][6]==$arrMStat[$c])
								    echo "<option selected>" .$arrMStat[$c]."</option>";
								else 
								    echo "<option>" .$arrMStat[$c]."</option>";
?>
				</select></td>
				</tr>
				<tr>
				<td><?=$smoker?></td>
			  <td> 
<?
			  if(isset($this->postArr['chkSmokeFlag'])) { ?>
			  <input type="checkbox" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="chkSmokeFlag" <?=$this->postArr['chkSmokeFlag']=='1'?'checked':''?> value="1">
<?			 } else { ?> 
			  <input type="checkbox" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="chkSmokeFlag" <?=$edit[0][1]==1?'checked':''?> value="1">
<? } ?>			  
			  </td>
				<td>&nbsp;</td>
				<td><?=$gender?></td>
				<td valign="middle">Male<input <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> type="radio" name="optGender" value="1" checked>		
<?				if(isset($this->postArr['optGender'])) { ?>
				Female<input type="radio" name="optGender" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> value="2" <?=($this->postArr['optGender']==2)?'checked':''?>></td>
<?				} else {  ?>
				Female<input type="radio" name="optGender" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> value="2" <?=($edit[0][5]==2)?'checked':''?>></td>
<? } ?>				
				</tr>
				<tr>
				<td><?=$dlicenno?></td>
				<td><input type="text" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="txtLicenNo" value="<?=(isset($this->postArr['txtLicenNo']))?$this->postArr['txtLicenNo']:$edit[0][10]?>"></td>
				<td>&nbsp;</td>
				<td><?=$licexpdate?></td>
				<td><input type="text" name="txtLicExpDate" readonly value=<?=(isset($this->postArr['txtLicExpDate']))?$this->postArr['txtLicExpDate']:$edit[0][11]?>>&nbsp;<input type="button" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtLicExpDate);return false;"></td>
				</tr> 
				<tr>
				<td><?=$militaryservice?></td>
				<td><input type="text" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="txtMilitarySer" value="<?=(isset($this->postArr['txtMilitarySer']))?$this->postArr['txtMilitarySer']:$edit[0][12]?>"></td>
				<td>&nbsp;</td>
				<td><?=$ethnicrace?></td>
					<td><select <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="cmbEthnicRace">
						<option value="0"><?=$selethnicrace?></option>
<?
					$ethRace = $this->popArr['ethRace'];
					for($c=0;$nation && count($ethRace)>$c;$c++)
						if(isset($this->postArr['cmbEthnicRace'])) {
							if($this->postArr['cmbEthnicRace']==$ethRace[$c][0])
							    echo "<option selected value='" . $ethRace[$c][0] . "'>" .$ethRace[$c][1]. "</option>";
							else
							    echo "<option value='" . $ethRace[$c][0] . "'>" .$ethRace[$c][1]. "</option>";
						} elseif($edit[0][2]==$ethRace[$c][0]) 
							    echo "<option selected value='" . $ethRace[$c][0] . "'>" .$ethRace[$c][1]. "</option>";
							else
							    echo "<option value='" . $ethRace[$c][0] . "'>" .$ethRace[$c][1]. "</option>";
						
?>					
					</select></td>
				</tr>
				</table></td>
          <td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
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
		<div id="job" style="position:absolute; z-index:2; width: 540px; visibility: hidden; left: 100px; top: 360px;">
	     <table border="0" cellpadding="0" cellspacing="0">
     <tr>
          <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><table onclick="setUpdate(2)" onkeypress="setUpdate(2)" height="150" border="0" cellpadding="0" cellspacing="0">

    
<?
		  $edit1 = $this->popArr['editJobInfoArr'];
		 // $edit2 = $this->popArr['editJobStatArr'];
?>
<tr>
		<?/*	  <td><?=$jobtitle?></td>
			  <td><input type="text" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="txtJobTitle" value="<?=(isset($this->postArr['txtJobTitle'])) ? $this->postArr['txtJobTitle']:$edit1[0][2]?>"></td>
			*/?>  
			   <td><?=$jobtitle?></td>
			  <td><select name="cmbJobTitle" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> onchange="document.getElementById('status').innerHTML = 'Please Wait....'; xajax_assEmpStat(this.value);">
			  		<option value="0">---Select <?=$jobtitle?>---</option>
			  		<? $jobtit = $this->popArr['jobtit'];
			  			for ($c=0; $jobtit && count($jobtit)>$c ; $c++) 
			  				if($edit1[0][2] == $jobtit[$c][0])
				  				echo "<option selected value='" . $jobtit[$c][0] . "'>" .$jobtit[$c][1]. "</option>";
				  			else
				  				echo "<option value='" . $jobtit[$c][0] . "'>" .$jobtit[$c][1]. "</option>";
			  			 ?>

			  			
			  <td width="50">&nbsp;</td>
			  <td><?=$empstatus?></td>
			  <td><select <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="cmbType">
			  		<option value="0"><?=$selempstat?></option>
<?					for($c=0;count($arrEmpType)>$c;$c++)
						if(isset($this->postArr['cmbType'])) {
							if($this->postArr['cmbType']==$arrEmpType[$c])
									echo "<option selected>" .$arrEmpType[$c]. "</option>";
								else
									echo "<option>" .$arrEmpType[$c]. "</option>";
						} elseif($edit1[0][1]==$arrEmpType[$c])
									echo "<option selected>" .$arrEmpType[$c]. "</option>";
								else
									echo "<option>" .$arrEmpType[$c]. "</option>";
?>			        
			  </select></td>
              </tr>
			  <tr>
			  <td><?=$eeocategory?></td>
			  <td><select <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="cmbEEOCat">
			  		<option value="0"><?=$seleeocat?></option>
<?				  		$eeojobcat = $this->popArr['eeojobcat'];
				for($c=0;$eeojobcat && count($eeojobcat)>$c;$c++)
							if(isset($this->postArr['cmbEEOCat'])) {
							   if($this->postArr['cmbEEOCat']==$eeojobcat[$c][0])
								    echo "<option selected value='".$eeojobcat[$c][0]. "'>" . $eeojobcat[$c][1] ."</option>";
								else
								    echo "<option value='".$eeojobcat[$c][0]. "'>" . $eeojobcat[$c][1] ."</option>";
							} elseif($edit1[0][3]==$eeojobcat[$c][0])
								    echo "<option selected value='".$eeojobcat[$c][0]. "'>" . $eeojobcat[$c][1] ."</option>";
								else
								    echo "<option value='".$eeojobcat[$c][0]. "'>" . $eeojobcat[$c][1] ."</option>";
?>			 
			  </select></td>
			  
			  <td width="50">&nbsp;</td>
			  <td><?=$location?></td>
			  <td><select <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="cmbLocation">
			  		<option value="0"><?=$selectlocation?></option>
<?					$loc = $this->popArr['loc'];
						for($c=0;$loc && count($loc)>$c;$c++)
							if(isset($this->postArr['cmbLocation'])) {
							   if($this->postArr['cmbLocation']==$loc[$c][0])
								    echo "<option selected value='".$loc[$c][0]. "'>" . $loc[$c][1] ."</option>";
								else
								    echo "<option value='".$loc[$c][0]. "'>" . $loc[$c][1] ."</option>";
							} elseif($edit1[0][4]==$loc[$c][0])
								    echo "<option selected value='".$loc[$c][0]. "'>" . $loc[$c][1] ."</option>";
								else
								    echo "<option value='".$loc[$c][0]. "'>" . $loc[$c][1] ."</option>";
?>			  
			  </select></td>
			  </tr>
			  <tr>
			  <td><?=$joindate?></td>
				<td><input type="text" readonly name="txtJoinedDate" value=<?=(isset($this->postArr['txtJoinedDate']))?$this->postArr['txtJoinedDate']:$edit1[0][5]?>>&nbsp;<input type="button" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtJoinedDate);return false;"></td>
		
				
			  </tr>
			  </table></td>
          <td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
     
    </div>
    
  
	<div id="workstation" style="position:absolute; z-index:2; width: 540px; visibility: hidden; left: 100px; top: 360px;">
	  <table><tr><td><table border="0" cellpadding="0" cellspacing="0">

        <tr>
          <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>

        <tr>
          <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><table height="150" border="0" cellpadding="0" cellspacing="0">
          
            <input type="hidden" name="depSTAT" value="">
<?
		if(!isset($this->getArr['DSEQ'])) {
?>
          
              <input type="hidden" name="txtDSeqNo" value="<?=$this->popArr['newDID']?>">
			   <th><h3><?=$dependents?></h3></th>          

              <tr>

                <td><?=$name?></td>
                <td><input name="txtDepName" <?=$locRights['add'] ? '':'disabled'?> type="text">
                </tr>
                <tr>
                <td><?=$relationship?></td>
                <td><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtRelShip"></td>
              </tr>
              				
				  <td>
<?	if($locRights['add']) { ?>
        <img border="0" title="Save" onClick="addDependents();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_save.jpg">
<?	} ?>
				  </td>
				</tr>
				<tr>
				<td>
<?	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delDependents();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} ?>
				</td>
				</tr>
<!--<div id="tablePassport">	-->
				<table width="275" align="center" border="0" class="tabForm">
				 <tr>
                      	<td width="50">&nbsp;</td>
						 <td><strong><?=$name?></strong></td>
						 <td><strong><?=$relationship?></strong></td>
				</tr> 
					
					<?
	$rset = $this->popArr['empDepAss'];
		
    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkdepdel[]' value='" . $rset[$c][1] ."'></td>";
			
            ?> <td><a href="#" onmousedown="viewDependents(<?=$rset[$c][1]?>)" ><?=$rset[$c][2]?></a></td> <?
            echo '<td>' . $rset[$c][3] .'</td>';
            echo '</tr>';
        }?>

	<?} elseif(isset($this->getArr['DSEQ'])) {
		$edit = $this->popArr['editDepForm'];
?>

          
              <input type="hidden" name="txtDSeqNo" value="<?=$edit[0][1]?>">
			 <th><h3><?=$dependents?></h3></th>	 
              <tr>
                <td><?=$name?></td>
                <td><input type="text" name="txtDepName" <?=$locRights['edit'] ? '':'disabled'?> value="<?=$edit[0][2]?>"></td>
               </tr>
              <tr>
                <td><?=$relationship?></td>
                <td><input name="txtRelShip" type="text" <?=$locRights['edit'] ? '':'disabled'?> value="<?=$edit[0][3]?>">
               </tr>
              
				  
				  <td>
					<?	if($locRights['edit']) { ?>
					        <img border="0" title="Save" onClick="editDependents();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
					<? 	} else { ?>
					        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_save.jpg">
					<?	} ?>
				  </td>
				</tr>
				<tr>
				<td>
<?	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delDependents();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} ?>
				</td>
				</tr>
				
				<table width="275" align="center" border="0" class="tabForm">
				 <tr>
                      	<td width="50">&nbsp;</td>
						 <td><strong><?=$name?></strong></td>
						 <td><strong><?=$relationship?></strong></td>
				</tr>
<?
	$rset = $this->popArr['empDepAss'];

    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkdepdel[]' value='" . $rset[$c][1] ."'></td>";
			
            ?> <td><a href="#" onmousedown="viewDependents(<?=$rset[$c][1]?>)" ><?=$rset[$c][2]?></a></td> <?
            echo '<td>' . $rset[$c][3] .'</td>';
           
        echo '</tr>';
        }

 } ?>
				



          </table></td>

          <td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table></td><td>
     <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><table height="150" border="0" cellpadding="0" cellspacing="0">
           <input type="hidden" name="chiSTAT" value="">
<?
		if(!isset($this->getArr['CHSEQ'])) {
?>
          
              <input type="hidden" name="txtCSeqNo" value="<?=$this->popArr['newCID']?>">
			   <th><h3><?=$children?></h3></th>          
              <tr>
                <td><?=$name?></td>
                <td><input name="txtChiName" <?=$locRights['add'] ? '':'disabled'?> type="text">
                </tr>
                <tr>
                <td><?=$dateofbirth?></td>
				<td><input type="text" readonly name="ChiDOB">&nbsp;<input type="button" <?=$locRights['add'] ? '':'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.ChiDOB);return false;"></td>
            </tr>
              				
				  <td>
<?	if($locRights['add']) { ?>
        <img border="0" title="Save" onClick="addChildren();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_save.jpg">
<?	} ?>
				  </td>
				</tr>
				<tr>
				<td>
<?	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delChildren();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} ?>
				</td>
				</tr>
<!--<div id="tablePassport">	-->
				<table width="275" align="center" border="0" class="tabForm">
				 <tr>
                      	<td width="50">&nbsp;</td>
						 <td><strong><?=$name?></strong></td>
						 <td><strong><?=$dateofbirth?></strong></td>
				</tr> 
					
					<?
	$rset = $this->popArr['empChiAss'];
		
    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkchidel[]' value='" . $rset[$c][1] ."'></td>";
			
            ?> <td><a href="#" onmousedown="viewChildren(<?=$rset[$c][1]?>)" ><?=$rset[$c][2]?></a></td> <?
            echo '<td>' . $rset[$c][3] .'</td>';
            echo '</tr>';
        }?>

	<?} elseif(isset($this->getArr['CHSEQ'])) {
		$edit = $this->popArr['editChiForm'];
?>

          
              <input type="hidden" name="txtCSeqNo" value="<?=$edit[0][1]?>">
			 <th><h3><?=$children?><h3></th>	 
              <tr>
                <td><?=$name?></td>
                <td><input type="text" name="txtChiName" <?=$locRights['edit'] ? '':'disabled'?> value="<?=$edit[0][2]?>"></td>
               </tr>
              <tr>
                <td><?=$dateofbirth?></td>
                <td><input type="text" name="ChiDOB" readonly value=<?=$edit[0][3]?>>&nbsp;<input type="button" <?=$locRights['edit'] ? '':'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.ChiDOB);return false;"></td>
               </tr>
              			  
				  <td>
					<?	if($locRights['edit']) { ?>
					        <img border="0" title="Save" onClick="editChildren();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
					<? 	} else { ?>
					        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_save.jpg">
					<?	} ?>
				  </td>
				</tr>
				<tr>
				<td>
<?	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delChildren();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} ?>
				</td>
				</tr>
				
				<table width="275" align="center" border="0" class="tabForm">
				 <tr>
                      	<td width="50">&nbsp;</td>
						 <td><strong><?=$name?></strong></td>
						 <td><strong><?=$dateofbirth?></strong></td>
				</tr>
<?
	$rset = $this->popArr['empChiAss'];

    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkchidel[]' value='" . $rset[$c][1] ."'></td>";
			
            ?> <td><a href="#" onmousedown="viewChildren(<?=$rset[$c][1]?>)" ><?=$rset[$c][2]?></a></td> <?
            echo '<td>' . $rset[$c][3] .'</td>';
           
        echo '</tr>';
        }

 } ?>
          </table></td>
          <td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table></td>
</tr></table>
    </div>
    
        
	<div id="econtact" style="position:absolute; z-index:2; width: 540px; visibility: hidden; left: 100px; top: 360px;">
	  <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><table height="200" border="0" cellpadding="0" cellspacing="0">

          <input type="hidden" name="econtactSTAT" value="">
<?
		if(!isset($this->getArr['ECSEQ'])) {
?>
            <input type="hidden" name="txtECSeqNo" value="<?=$this->popArr['newECID']?>">
			 <tr>
			 <td><?=$name?></td>
			  <td><input name="txtEConName" <?=$locRights['add'] ? '':'disabled'?> type="text"></td>
			 <td width="50">&nbsp;</td>
			<td><?=$relationship?></td>
			 <td><input name="txtEConRel" <?=$locRights['add'] ? '':'disabled'?> type="text"></td>
			 </tr>
			 <tr>
			 <td><?=$hmtele?></td>
			 <td><input name="txtEConHmTel" <?=$locRights['add'] ? '':'disabled'?> type="text"></td>
			 <td width="50">&nbsp;</td>
			 <td><?=$mobile?></td>
			 <td><input name="txtEConMobile" <?=$locRights['add'] ? '':'disabled'?> type="text"></td>
			 </tr>
			 <tr>
			 <td><?=$worktele?></td>
			 <td><input name="txtEConWorkTel" <?=$locRights['add'] ? '':'disabled'?> type="text"></td>
			  </tr>
				  <td>
<?	if($locRights['add']) { ?>
        <img border="0" title="Save" onClick="addEContact();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_save.jpg">
<?	} ?>
				  </td>
				</tr>
				<tr>
				<td>
<?	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delEContact();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} ?>
				</td>
				</tr>
	
				<table width="550" align="center" border="0" class="tabForm">
				 <tr>
                      	<td width="50">&nbsp;</td>
						 <td><strong><?=$name?></strong></td>
						 <td><strong><?=$relationship?></strong></td>
						 <td><strong><?=$hmtele?></strong></td>
						 <td><strong><?=$mobile?></strong></td>
						 <td><strong><?=$worktele?></strong></td>
					</tr> 
					
					<?
	$rset = $this->popArr['empECAss'];

	for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkecontactdel[]' value='" . $rset[$c][1] ."'></td>";
			
            ?> <td><a href="javascript:viewEContact('<?=$rset[$c][1]?>')"><?=$rset[$c][2]?></a></td> <?
            echo '<td>' . $rset[$c][3] .'</td>';
            echo '<td>' . $rset[$c][4] .'</td>';
            echo '<td>' . $rset[$c][5] .'</td>';
            echo '<td>' . $rset[$c][6] .'</td>';
           
        echo '</tr>';
        }?>

	<?} elseif(isset($this->getArr['ECSEQ'])) {
		$edit = $this->popArr['editECForm'];
		
?>

          <tr>
              <input type="hidden" name="txtECSeqNo" value="<?=$edit[0][1]?>">
			  
			 <td><?=$name?></td>
			 <td><input type="text" name="txtEConName" value="<?=$edit[0][2]?>"></td>
			 <td width="50">&nbsp;</td>
			<td><?=$relationship?></td>
			 <td><input type="text" name="txtEConRel" value="<?=$edit[0][3]?>"></td>
			 </tr>
			 <tr>
			 <td><?=$hmtele?></td>
			 <td><input type="text"  name="txtEConHmTel" value="<?=$edit[0][4]?>"></td>
			 <td width="50">&nbsp;</td>
			 <td><?=$mobile?></td>
			 <td><input type="text" name="txtEConMobile" value="<?=$edit[0][5]?>"></td>
			 </tr>
			 <tr>
			 <td><?=$worktele?></td>
			 <td><input type="text" name="txtEConWorkTel" value="<?=$edit[0][6]?>"></td>
			 </tr>
			
				  
				  <td>
					<?	if($locRights['edit']) { ?>
					        <img border="0" title="Save" onClick="editEContact();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
					<? 	} else { ?>
					        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_save.jpg">
					<?	} ?>
				  </td>
				</tr>
				<tr>
				<td>
<?	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delEContact();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} ?>
				</td>
				</tr>
				
				<table width="550" align="center" border="0" class="tabForm">
				 <tr>
                      	<td width="50">&nbsp;</td>
						 <td><strong><?=$name?></strong></td>
						 <td><strong><?=$relationship?></strong></td>
						 <td><strong><?=$hmtele?></strong></td>
						 <td><strong><?=$mobile?></strong></td>
						 <td><strong><?=$worktele?></strong></td>
					</tr>
<?
	$rset = $this->popArr['empECAss'];
//$exception_handler = new ExceptionHandler();
	  	 	//$exception_handler->logW(count($rset).'hhh');
    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkecontactdel[]' value='" . $rset[$c][1] ."'></td>";
			
            ?> <td><a href="javascript:viewEContact('<?=$rset[$c][1]?>')"><?=$rset[$c][2]?></a></td> <?
            echo '<td>' . $rset[$c][3] .'</td>';
            echo '<td>' . $rset[$c][4] .'</td>';
            echo '<td>' . $rset[$c][5] .'</td>';
            echo '<td>' . $rset[$c][6] .'</td>';
            
        echo '</tr>';
        }

 } ?>
		</table></td>
          <td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
    </div>
  
    
	<div id="contact" style="position:absolute; z-index:2; width: 540px; visibility: hidden; left: 100px; top: 360px;">
	  <table border="0" cellpadding="0" cellspacing="0" height="100%">
        <tr>
          <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><table onclick="setUpdate(5)" onkeypress="setUpdate(5)" height="250" border="0" cellpadding="0" cellspacing="0">
<?
		$edit = $this->popArr['editPermResArr'];
?>
          <tr>
			  <td><?=$country?></td>
						  <td><select name="cmbCountry" disabled onchange="document.getElementById('status').innerHTML = 'Please Wait....'; xajax_populateStates(this.value);">
						  		<option value="0"><?=$selectcountry?></option>
					<?
								$cntlist = $this->popArr['cntlist'];
								for($c=0;$cntlist && count($cntlist)>$c;$c++)  
									if($edit[0][4]==$cntlist[$c][0])
										echo "<option selected value='" .$cntlist[$c][0] . "'>" . $cntlist[$c][1] . '</option>';
									else
										echo "<option value='" .$cntlist[$c][0] . "'>" . $cntlist[$c][1] . '</option>';
					?>
						  </select></td>
						  <td width="50">&nbsp;</td>
			  <td><?=$street1?></td>
			  <td><input type="text" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="txtStreet1" value="<?=(isset($this->postArr['txtStreet1']))?$this->postArr['txtStreet1']:$edit[0][1]?>"></td>
             </tr>
			 <tr>
			 <td><?=$state?></td>
						  <td><select name="cmbProvince" disabled onchange="document.getElementById('status').innerHTML = 'Please Wait....'; xajax_populateDistrict(this.value);">
						  		<option value="0"><?=$selstate?></option>
					<?
								$provlist = $this->popArr['provlist'];
								for($c=0;$provlist && count($provlist)>$c;$c++)  
									if($edit[0][5]==$provlist[$c][1])
										echo "<option selected value='" .$provlist[$c][1] . "'>" . $provlist[$c][2] . '</option>';
									else
										echo "<option value='" .$provlist[$c][1] . "'>" . $provlist[$c][2] . '</option>';
					?>
						  </select></td>
						  <td width="50">&nbsp;</td>
			 <td><?=$street2?></td>
			  <td><input type="text" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="txtStreet2" value="<?=(isset($this->postArr['txtStreet2']))?$this->postArr['txtStreet2']:$edit[0][2]?>"></td>
			 </tr>
			 <tr>
			 <td><?=$city?></td>
			 <td><select name="cmbCity" disabled >
			  <option value="0"><?=$selcity?></option>
<?
				$citylist = $this->popArr['citylist'];
				 for($c=0;$citylist && count($citylist)>$c;$c++)  
					if($edit[0][3]==$citylist[$c][1])
						echo "<option selected value='" .$citylist[$c][1] . "'>" . $citylist[$c][2] . '</option>';
						else
						echo "<option value='" .$citylist[$c][1] . "'>" . $citylist[$c][2] . '</option>';
?>
						  </select></td>			 
			<td width="50">&nbsp;</td>
			<td><?=$zipcode?></td>
			 <td><input type="text" name="txtzipCode" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> value="<?=(isset($this->postArr['txtzipCode']))?$this->postArr['txtzipCode']:$edit[0][6]?>"></td>
			 </tr>
			 <tr>
			 <td><?=$hmtele?></td>
			 <td><input type="text" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="txtHmTelep" value="<?=(isset($this->postArr['txtHmTelep']))?$this->postArr['txtHmTelep']:$edit[0][7]?>"></td>
			 <td width="50">&nbsp;</td>
			<td><?=$mobile?></td>
			 <td><input type="text" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="txtMobile" value="<?=(isset($this->postArr['txtMobile']))?$this->postArr['txtMobile']:$edit[0][8]?>"></td>
			 </tr>
			 <tr>
			 <td><?=$worktele?></td>
			 <td><input type="text" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="txtWorkTelep" value="<?=(isset($this->postArr['txtWorkTelep']))?$this->postArr['txtWorkTelep']:$edit[0][9]?>"></td>
			 <td width="50">&nbsp;</td>
			 <td><?=$workemail?></td>
			 <td><input type="text" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="txtWorkEmail" value="<?=(isset($this->postArr['txtWorkEmail']))?$this->postArr['txtWorkEmail']:$edit[0][10]?>"></td>
			 </tr>
			 <tr>
			 <td><?=$otheremail?></td>
			 <td><input type="text" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="txtOtherEmail" value="<?=(isset($this->postArr['txtOtherEmail']))?$this->postArr['txtOtherEmail']:$edit[0][11]?>"></td>
			 </tr>
			
			 </table></td>
          <td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
    </div>
    
        
	<div id="passport" style="position:absolute; z-index:2; width: 540px; visibility: hidden; left: 100px; top: 360px;">
	  <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><table height="200" border="0" cellpadding="0" cellspacing="0">

          <input type="hidden" name="passportSTAT" value="">
<?
		if(!isset($this->getArr['PPSEQ'])) {
?>
          <tr>
              <input type="hidden" name="txtPPSeqNo" value="<?=$this->popArr['newPPID']?>">
			  <td><?=$passport?> <input type="radio" <?=$locRights['add'] ? '':'disabled'?> checked name="PPType" value="1"></td><td><?=$visa?><input type="radio" <?=$locRights['add'] ? '':'disabled'?> name="PPType" value="2"></td>
			  <td width="50">&nbsp;</td>
		  	  <td><?=$citizenship?></td>
                <td><select <?=$locRights['add'] ? '':'disabled'?> name="cmbPPCountry">
                		<option value="0"><?=$selectcountry?></option>
<?				$list = $this->popArr['ppcntlist'];
				for($c=0;$list && count($list)>$c;$c++)
				    echo "<option value='" . $list[$c][0] . "'>" . $list[$c][1]. "</option>";
?>			 
				</td> 
		    </tr>
              <tr>
                <td><?=$passvisano?></td>
                <td><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtPPNo"></td>
                <td width="50">&nbsp;</td>
                <td><?=$issueddate?></td>
                <td><input type="text" readonly name="txtPPIssDat">&nbsp;<input type="button" <?=$locRights['add'] ? '':'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtPPIssDat);return false;"></td>
              </tr>
              <tr>
                <td><?=$i9status?></td>
                <td><input name="txtI9status" <?=$locRights['add'] ? '':'disabled'?> type="text">
                <td width="50">&nbsp;</td>
                <td><?=$dateofexp?></td>
                <td><input type="text" readonly name="txtPPExpDat">&nbsp;<input type="button" <?=$locRights['add'] ? '':'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtPPExpDat);return false;"></td>
              </tr>
              <tr>
               <td><?=$i9reviewdate?></td>
                <td><input type="text" readonly name="txtI9ReviewDat">&nbsp;<input type="button" <?=$locRights['add'] ? '':'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtI9ReviewDat);return false;"></td>
				<td width="50">&nbsp;</td>
				<td><?=$comments?></td>
				<td><textarea <?=$locRights['add'] ? '':'disabled'?> name="txtComments"></textarea></td>
				</tr>
				
				  <td>
<?	if($locRights['add']) { ?>
        <img border="0" title="Save" onClick="addPassport();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_save.jpg">
<?	} ?>
				  </td>
				</tr>
				<tr>
				<td>
<?	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delPassport();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} ?>
				</td>
				</tr>
<div id="tablePassport">	
				<table width="550" align="center" border="0" class="tabForm">
				 <tr>
                      	<td width="50">&nbsp;</td>
						 <td><strong><?=$passport?>/<?=$visa?></strong></td>
						 <td><strong><?=$passvisano?></strong></td>
						 <td><strong><?=$citizenship?></strong></td>
						 <td><strong><?=$issueddate?></strong></td>
						 <td><strong><?=$dateofexp?></strong></td>
					</tr> 
					
					<?
	$rset = $this->popArr['empPPAss'];
		
    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkpassportdel[]' value='" . $rset[$c][1] ."'></td>";
			if($rset[$c][6]==1)
            	$fname="Passport";
            else
            	$fname="Visa";

            ?> <td><a href="#" onmousedown="viewPassport(<?=$rset[$c][1]?>)" ><?=$fname?></a></td> <?
            echo '<td>' . $rset[$c][2] .'</td>';
            echo '<td>' . $rset[$c][9] .'</td>';
            $dtPrint = explode(" ",$rset[$c][3]);
            echo '<td>' . $dtPrint[0] .'</td>';
            $dtPrint = explode(" ",$rset[$c][4]);
            echo '<td>' . $dtPrint[0] .'</td>';
        echo '</tr>';
        }?>
</div>
	<?} elseif(isset($this->getArr['PPSEQ'])) {
		$edit = $this->popArr['editPPForm'];
?>

          <tr>
              <input type="hidden" name="txtPPSeqNo" value="<?=$edit[0][1]?>">
			  <td><?=$passport?> <input type="radio" checked <?=$locRights['edit'] ? '':'disabled'?> name="PPType" value="1"></td><td><?=$visa?><input type="radio" <?=$locRights['edit'] ? '':'disabled'?> name="PPType" <?=($edit[0][6]=='2')?'checked':''?> value="2"></td>
			  <td width="50">&nbsp;</td>
		  	 <td><?=$citizenship?></td>
                <td><select <?=$locRights['edit'] ? '':'disabled'?> name="cmbPPCountry">
                <option value="0"><?=$selectcountry?></option>
<?				$list = $this->popArr['ppcntlist'];
				for($c=0;count($list)>$c;$c++)
					if($edit[0][9]==$list[$c][0])
					    echo "<option selected value='" . $list[$c][0] . "'>" . $list[$c][1]. "</option>";
					else
					    echo "<option value='" . $list[$c][0] . "'>" . $list[$c][1]. "</option>";
?>			 
				</td>            
			  </tr>
              <tr>
                <td><?=$passvisano?></td>
                <td><input type="text" name="txtPPNo" <?=$locRights['edit'] ? '':'disabled'?> value="<?=$edit[0][2]?>"></td>
                <td width="50">&nbsp;</td>
                <td><?=$issueddate?></td>
                <td><input type="text" name="txtPPIssDat" readonly value=<?=$edit[0][3]?>>&nbsp;<input type="button" <?=$locRights['edit'] ? '':'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtPPIssDat);return false;"></td>
              </tr>
              <tr>
                <td><?=$i9status?></td>
                <td><input name="txtI9status" type="text" <?=$locRights['edit'] ? '':'disabled'?> value="<?=$edit[0][7]?>">
                <td width="50">&nbsp;</td>
                <td><?=$dateofexp?></td>
                <td><input type="text" name="txtPPExpDat" readonly value=<?=$edit[0][4]?>>&nbsp;<input type="button" <?=$locRights['add'] ? '':'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtPPExpDat);return false;"></td>
              </tr>
              <tr>
               <td><?=$i9reviewdate?></td>
                <td><input type="text" name="txtI9ReviewDat" readonly value=<?=$edit[0][8]?>>&nbsp;<input type="button" <?=$locRights['add'] ? '':'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtI9ReviewDat);return false;"></td>
				<td width="50">&nbsp;</td>
				<td><?=$comments?></td>
				<td><textarea <?=$locRights['edit'] ? '':'disabled'?> name="txtComments"><?=$edit[0][5]?></textarea></td>
				</tr>
				  
				  <td>
					<?	if($locRights['edit']) { ?>
					        <img border="0" title="Save" onClick="editPassport();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
					<? 	} else { ?>
					        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_save.jpg">
					<?	} ?>
				  </td>
				</tr>
				<tr>
				<td>
<?	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delPassport();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} ?>
				</td>
				</tr>
				
				<table width="550" align="center" border="0" class="tabForm">
				 <tr>
                      	<td width="50">&nbsp;</td>
						 <td><strong><?=$passport?>/<?=$visa?></strong></td>
						 <td><strong><?=$passvisano?></strong></td>
						 <td><strong><?=$citizenship?></strong></td>
						 <td><strong><?=$issueddate?></strong></td>
						 <td><strong><?=$dateofexp?></strong></td>
					</tr>
<?
	$rset = $this->popArr['empPPAss'];

    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkpassportdel[]' value='" . $rset[$c][1] ."'></td>";
			if($rset[$c][6]==1)
            	$fname="Passport";
            else
            	$fname="Visa";

            ?> <td><a href="#" onmousedown="viewPassport(<?=$rset[$c][1]?>)" ><?=$fname?></a></td> <?
            echo '<td>' . $rset[$c][2] .'</td>';
            echo '<td>' . $rset[$c][9] .'</td>';
            $dtPrint = explode(" ",$rset[$c][3]);
            echo '<td>' . $dtPrint[0] .'</td>';
            $dtPrint = explode(" ",$rset[$c][4]);
            echo '<td>' . $dtPrint[0] .'</td>';
        echo '</tr>';
        }

 } ?>
		</table></td>
          <td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
    </div>
   
	<div id="bank" style="position:absolute; z-index:2; width: 540px; visibility: hidden; left: 100px; top: 360px;">
	  
    </div>
	<div id="attachment" style="position:absolute; z-index:2; width: 540px; visibility: hidden; left: 100px; top: 360px;">
	  <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><table width="352" height="200" border="0" cellpadding="0" cellspacing="0">
		
<?		if(!isset($this->getArr['ATTACH'])) { ?>
          <tr>
				<td><?=$path?></td>
				<td><input type="file" name="ufile" ></td>
              </tr>
              <tr>
              	<td><?=$description?></td>
              	<td><textarea name="txtAttDesc"></textarea></td>
              </tr>
				<tr>
				<td>
<?	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delAttach();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} ?>
				</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>
<?	if($locRights['add']) { ?>
        <img border="0" title="Save" onClick="addAttach();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_save.jpg">
<?	} ?>
				</td>
				</tr>
			<table border="0" width="450" align="center" class="tabForm">
			
			    <tr>
                      	<td></td>
						 <td><strong><?=$filename?></strong></td>
						 <td><strong><?=$size?></strong></td>
						 <td><strong><?=$type?></strong></td>
					</tr>
<?
	$rset = $this->popArr['empAttAss'] ;

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
              
<?		} elseif(isset($this->getArr['ATTACH'])) {
		$edit = $this->popArr['editAttForm'];
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
        <img title="Delete" onclick="delAttach();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} ?>
				</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>
<?	if($locRights['edit']) { ?>
        <img border="0" title="Save" onClick="editAttach();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_save.jpg">
<?	} ?>
				</td>
				</tr>
			<table border="0" width="450" align="center" class="tabForm">
			
			    <tr>
                      	<td></td>
						 <td><strong><?=$filename?></strong></td>
						 <td><strong><?=$size?></strong></td>
						 <td><strong><?=$type?></strong></td>
					</tr>
<?
	$rset = $this->popArr['empAttAss'] ;

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
          <td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
    </div>
   
       	
	<div id="other" style="position:absolute; z-index:2; width: 540px; visibility: hidden; left: 100px; top: 360px;">
	  <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><table width="352" height="200" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td height="18"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt="">Other</td>
              </tr>
          </table></td>
          <td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
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
</table>
            </td>
          </tr>
    </table>
    
       </form>
<iframe width=174 height=189 name="gToday:normal:agenda.js" id="gToday:normal:agenda.js" src="../../scripts/ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; top:-500px; left:-500px;">
</iframe>
</body>
</html>

<? } ?>