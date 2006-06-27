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

require_once ROOT_PATH . '/lib/confs/sysConf.php';

	$sysConst = new sysConf(); 

	$arrTitle = $this->popArr['arrTitle'];
	$arrBGroup = $this->popArr['arrBGroup'];
	$arrMStat = $this->popArr['arrMStat'];
	$arrEmpType = $this->popArr['arrEmpType'];
	$arrTaxExempt = $this->popArr['arrTaxExempt'];
	$arrFillStat = $this->popArr['arrFillStat'];

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
	
		document.frmEmp.sqlState.value = "UpdateRecord";
		document.frmEmp.submit();		
	}			


function reLoad() {
	location.href ="<?=$_SERVER['PHP_SELF']?>?id=<?=$this->getArr['id']?>&reqcode=<?=$this->getArr['reqcode']?>";
}

 
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

function dwPopup() {
        var popup=window.open('../../templates/hrfunct/download.php?id=<?=isset($this->getArr['id']) ? $this->getArr['id'] : '' ?>&ATTACH=<?=isset($this->getArr['ATTACH']) ? $this->getArr['ATTACH'] : '' ?>','Downloads');
        if(!popup.opener) popup.opener=self;
}	

function viewAttach(att) {
	document.frmEmp.action=document.frmEmp.action + "&ATTACH=" + att;
	document.frmEmp.pane.value=8;
	document.frmEmp.submit();
}


function viewBranch(brch) {
	document.frmEmp.action=document.frmEmp.action + "&BRCH=" + brch ;
	document.frmEmp.pane.value=7;
	document.frmEmp.submit();
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

function viewTax(taxID,fdFlag) {
	document.frmEmp.action=document.frmEmp.action + "&TAXID=" + taxID + "&FEDST=" + fdFlag;
	document.frmEmp.pane.value=4;
	document.frmEmp.submit();
}

</script>

<link href="../../themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("../../themes/beyondT/css/style.css"); </style>

</head>
<body onload="<?=(isset($this->postArr['pane']) && $this->postArr['pane']!='')?'qshowpane();':''?>">
<table width='100%' cellpadding='0' cellspacing='0' border='0'>
  <tr>
    <td valign='top'>&nbsp; </td>
    <td width='100%'><h2 align="center">Employee Information</h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>
</table>
<form name="frmEmp" method="post" action="<?=$_SERVER['PHP_SELF']?>?id=<?=$this->getArr['id']?>&reqcode=<?=$this->getArr['reqcode']?>&capturemode=<?=$this->getArr['capturemode']?>">
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
<?
$edit = $this->popArr['editMainArr'];
?>
			<table width="550" align="center" border="0" cellpadding="0" cellspacing="0">
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
				<td>Code</td>
				<td><strong><input type="hidden" name="txtEmpID" value="<?=$this->getArr['id']?>"><?=$this->getArr['id']?></strong></td>
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
    <td>
			        <img src="<?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '../../themes/beyondT/pictures/btn_save.jpg' : '../../themes/beyondT/pictures/btn_edit.jpg'?>" title="Edit" onmouseout="mout();" onmouseover="mover();" name="Edit" onClick="edit();">
    </td>
    <td><img src="../../themes/beyondT/pictures/btn_clear.jpg" onmouseout="this.src='../../themes/beyondT/pictures/btn_clear.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_clear_02.jpg';" onClick="reLoad();" ></td>
    </tr>
    </table>


<table width="600" align="center" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><table border="0" cellpadding="0" cellspacing="0" width="600">
        <tr>
          <td><img name="tabs_r1_c1" src="../../themes/beyondT/pictures/tabs_r1_c1.jpg" width="28" height="71" border="0" alt=""></td>
          <td><a href="#" onClick="MM_showHideLayers('hidebg','','hide','personal','','show','job','','hide','workstation','','hide','tax','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','hide')">
		  	  <img name="tabs_r1_c2" src="../../themes/beyondT/pictures/tabs_r1_c2.jpg" width="62" height="71" border="0" alt=""></a></td>
          <td><a href="#" onClick="MM_showHideLayers('hidebg','','hide','personal','','hide','job','','show','workstation','','hide','tax','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','hide')">
		      <img name="tabs_r1_c3" src="../../themes/beyondT/pictures/tabs_r1_c3.jpg" width="56" height="71" border="0" alt=""></a></td>
<!--         <td><a href="#" onClick="MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','show','tax','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','hide')">
		      <img name="tabs_r1_c4" src="../../themes/beyondT/pictures/tabs_r1_c4.jpg" width="62" height="71" border="0" alt=""></a></td> -->
          <td><a href="#" onClick="MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','hide','tax','','show','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','hide')">
		      <img name="tabs_r1_c5" src="../../themes/beyondT/pictures/tabs_r1_c5.jpg" width="60" height="71" border="0" alt=""></a></td>
          <td><a href="#" onClick="MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','hide','tax','','hide','contact','','show','passport','','hide','bank','','hide','attachment','','hide','other','','hide')">
		      <img name="tabs_r1_c6" src="../../themes/beyondT/pictures/tabs_r1_c6.jpg" width="59" height="71" border="0" alt=""></a></td>
          <td><a href="#" onClick="MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','hide','tax','','hide','contact','','hide','passport','','show','bank','','hide','attachment','','hide','other','','hide')">
		  	  <img name="tabs_r1_c7" src="../../themes/beyondT/pictures/tabs_r1_c7.jpg" width="61" height="71" border="0" alt=""></a></td>
          <td><a href="#" onClick="MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','hide','tax','','hide','contact','','hide','passport','','hide','bank','','show','attachment','','hide','other','','hide')">
		      <img name="tabs_r1_c8" src="../../themes/beyondT/pictures/tabs_r1_c8.jpg" width="60" height="71" border="0" alt=""></a></td>
          <td><a href="#" onClick="MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','hide','tax','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','show','other','','hide')">
		      <img name="tabs_r1_c9" src="../../themes/beyondT/pictures/tabs_r1_c9.jpg" width="60" height="71" border="0" alt=""></a></td>
<!--          <td><a href="#" onClick="MM_showHideLayers('hidebg','','hide','personal','','hide','job','','hide','workstation','','hide','tax','','hide','contact','','hide','passport','','hide','bank','','hide','attachment','','hide','other','','show')">
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
					<td>SSN:</td>
					<td><input type="text" name="txtNICNo" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> value="<?=(isset($_POST['txtNICNo']))?$_POST['txtNICNo']:$edit[0][1]?>"></td>
					<td width="50">&nbsp;</td>
					<td>Nationality</td>
					<td><select <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'disabled'?> name="cmbNation">
						<option value="0">--Select Nationality--</option>
<?
					$nation = $this->popArr['nation'];
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
					$rel = $this->popArr['rel'];
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
					$keys = array_keys($arrBGroup);
					$values = array_values($arrBGroup);
					for($c=0;count($arrBGroup)>$c;$c++)
						if(isset($_POST['cmbBloodGrp'])) {
						   if($_POST['cmbBloodGrp']==$values[$c])
							echo "<option selected value='" .$values[$c]. "'>" .$keys[$c]. "</option>";
						else 
							echo "<option value='" .$values[$c]. "'>" .$keys[$c]. "</option>";
						} elseif($edit[0][6]==$values[$c])
									echo "<option selected value='" .$values[$c]. "'>" .$keys[$c]. "</option>";
								else 
									echo "<option value='" .$values[$c]. "'>" .$keys[$c]. "</option>";
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
          <td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
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
          <td><table onclick="setUpdate(2)" onkeypress="setUpdate(2)" height="350" border="0" cellpadding="0" cellspacing="0">
<?
		  $edit1 = $this->popArr['editJobInfoArr'];
		  $edit2 = $this->popArr['editJobStatArr'];
?>
<tr>
			  <td>Date Joined</td>
			  <?
			  $dtField = explode(" ",$edit1[0][1])
			  ?>
			  <td><strong><?=$dtField[0]?></strong></td>
			  <td width="50">&nbsp;</td>
			  <td>Employment Type</td>
			  <td>
			  		<strong>
<?					for($c=0;count($arrEmpType)>$c;$c++)
						if($edit2[0][1]==$arrEmpType[$c])
								echo $arrEmpType[$c];
								
?>			        
</strong>
			 </td>
              </tr>
			  <tr>
			  <td>Confirmed</td>
			  <td> 
			  <input type="checkbox" readonly name="chkConfirmFlag" <?=$edit1[0][2]==1?'checked':''?> value="1">
			  </td>
			  <td width="50">&nbsp;</td>
			  <td>Statutory Classification</td>
			  <td><strong>
<?					$stat = $this->popArr['stat'];
						for($c=0;$stat && count($stat)>$c;$c++)
							if($edit2[0][2]==$stat[$c][0])
								    echo  $stat[$c][1];
?>			  
			  </strong></td>
			  </tr>
			  <tr>
			  <td>Resignation Date</td>
			  <td> <?
			  $dtField = explode(" ",$edit1[0][3])
			  ?>
			  <strong><?=$dtField[0]?></strong>
			  <td width="50">&nbsp;</td>
			  <td>Employment Category</td>
			  <td><strong>
<?					$catlist = $this->popArr['catlist'];
					for($c=0;$catlist && count($catlist)>$c;$c++)
						if($edit2[0][3]==$catlist[$c][0])
							  	echo $catlist[$c][1];
?>			  
			  </strong></td>
			  </tr>
			  <tr>
			  <td>Retire Date</td>
			   <?
			  $dtField = explode(" ",$edit1[0][1])
			  ?>
			  <td><strong><?=$dtField[0]?></strong></td>
			  <td width="50">&nbsp;</td>
			  <td>Start Date</td>
			   <?
			  $dtField = explode(" ",$edit1[0][1])
			  ?>
			  <td><strong><?=$dtField[0]?></strong></td>
			  </tr>
			  <tr>
 			  <td>Salary Grade</td>
			  <td><strong>
			  <?	$grdlist = $this->popArr['grdlist'];
					for($c=0;$grdlist && count($grdlist)>$c;$c++)
						if($edit1[0][5]==$grdlist[$c][0])
									echo $grdlist[$c][1];

						?>			  
			 </strong></td>
			  <td width="50">&nbsp;</td>
			  <td>End Date</td>
			   <?
			  $dtField = explode(" ",$edit1[0][1])
			  ?>
			  <td><strong><?=$dtField[0]?></strong></td>
			  </tr>
			  <tr>
			  <td>Corporate Title</td>
			  <td><strong>
<?				
					$ctlist = $this->popArr['ctlist'];
					for($c=0;$ctlist && count($ctlist)>$c;$c++)
						if($edit1[0][6]==$ctlist[$c][1])
							echo $ctlist[$c][2];
				
?>			  
			  </strong></td>
			  <td width="50">&nbsp;</td>
			  <td>Contract to Permanent</td>
			  <td>

			
			  <input type="checkbox" readonly name="chkConToPermFlag" <?=($edit2[0][6]=='1')?'checked':''?> value="1">
			
			  </td>
			  </tr>
			  <tr>
			  <td>Designation</td>
			  <td> <strong>
<?					$deslist = $this->popArr['deslist'];
					for($c=0;$deslist && count($deslist)>$c;$c++)
						if($edit1[0][7]==$deslist[$c][1])
							echo $deslist[$c][2];
?>			  
			  </strong></td>
			  <td width="50">&nbsp;</td>
			  <td>Contract to Permanent Date</td>
			  <td> <?
			  $dtField = explode(" ",$edit1[0][3])
			  ?>
			  <strong><?=$dtField[0]?></strong></td>
			  </tr>
			  <tr>
			  <td>Costing Centre</td>
			  <td><strong>
<?					$costlist = $this->popArr['costlist'];
					for($c=0;$costlist && count($costlist)>$c;$c++)
						 if($edit1[0][8]==$costlist[$c][0])
									echo $costlist[$c][1];
								
?>			  
			  </strong></td>
			  <td width="50">&nbsp;</td>
			  <td>HR Active</td>
			  <td>

			  <input type="checkbox" name="chkHRActivFlag" readonly <?=($edit2[0][8]=='1'?'checked':'')?> value="1">

			  </td>
			  </tr>
			  <tr>
			  <td>Work Hours</td>
			  <td><strong><?=(isset($_POST['txtWorkHours']))?$_POST['txtWorkHours']:$edit1[0][9]?><strong></td>
			  <td width="50">&nbsp;</td>
			  <td>Payroll Activ</td>
			  <td>

			  <input type="checkbox" name="txtPayActivFlag" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'readonly'?> <?=($edit2[0][9]=='1'?'checked':'')?> value="1">
		

			  </td>
			  </tr>
			  <tr>
			  <td>Job Preference</td>
			  <td><strong><?=(isset($_POST['txtJobPref']))?$_POST['txtJobPref']:$edit1[0][10]?></strong></td>
			  <td width="50">&nbsp;</td>
			  <td>Time &amp; Attendance Active</td>
			  <td>
			
			  <input type="checkbox" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'readonly'?> name="chkTimeAttActivFlag" <?=($edit2[0][10]=='1'?'checked':'')?> value="1">
						  
			  </td>
			  </tr>          </table></td>
          <td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table>
    </div>
  <div id="tax" style="position:absolute; z-index:2; width: 540px; visibility: hidden; left: 100px; top: 360px;">
	  <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>

<? $edit=$this->popArr['editTaxArr']; 
	$clist = $this->popArr['taxcntlist'];
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
          <td><table onclick='setUpdate(4)' onkeypress='setUpdate(4)'":  height="250" border="0" cellpadding="0" cellspacing="0">


	             <tr>
          <td valign="top">Country</td>
          <td valign="top"><strong>
<?				$list = $this->popArr['taxcntlist'];
					for($c=0;$list && count($list)>$c;$c++)
					if($list[$c][0]==$edit[0][16])
						    echo  $list[$c][1];
											
?>          	

				</strong></td>
				</tr>

<?		if(isset($showTax) && $showTax=='1') {
	
?>
			<input type="hidden" name="taxSTAT" value="">

      

<? if(isset($this->getArr['TAXID'])) {
	
	$edit = $this->popArr['editTaxForm'];
?>
				<tr>
				<td>Tax</td>
          <td><input type="hidden" name="cmbTaxID" value="<?=$edit[0][1]?>"><strong>
<?				$list=$this->popArr['taxlist'];
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
          <td><strong>
			 		
<?			for($c=0;$edit[0][2]==1 && count($arrFillStat)>$c;$c++)
				if($edit[0][3]==$arrFillStat[$c])
          		   echo $arrFillStat[$c];
          		
?>          		
          </strong></td>
<? } 
 
if($edit[0][2]==2) { ?>

 		  <td>Filing Status</td>
          <td><select disabled name="cmbStateTaxFillStat">
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
          <td><input type="text" disabled name="txtFedTaxAllowance" value="<?=$edit[0][2]==1 ? $edit[0][4] : ''?>"></td>
<? } 
 
if($edit[0][2]==2) { ?>

          <td>Taxed State</td>
          <td><select disabled name="cmbStateTaxState">
			 		<option value="0">-Select State-</option>
<?			$plist = $this->popArr['plist'];
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
			<td><input type="text" disabled name="txtFedTaxExtra" value="<?=$edit[0][2]==1 ? $edit[0][5] : '' ?>"></td>
<? }
if($edit[0][2]==2) { ?>
			
			<td>Allowances</td>
            <td><input type="text" name="txtStateTaxAllowance" disabled value="<?=$edit[0][2]==2 ? $edit[0][4] : '' ?>"></td>
            </tr>
            <tr>
			<td>Extra</td>
			<td><input type="text" name="txtStateTaxExtra" disabled value="<?=$edit[0][2]==2 ? $edit[0][5] : '' ?>"></td>
<? } ?>			
			</tr>


				<table width="550" align="center" border="0" class="tabForm">
				 <tr>
                      	
						 <td><strong>Federal/State</strong></td>
						 <td><strong>Tax</strong></td>
						 <td><strong>Fill Status</strong></td>
						 <td><strong>Allowance</strong></td>
						 <td><strong>Extra</strong></td>
					</tr>
<?
$rset = $this ->popArr['empTaxAss'];
$list = $this->popArr['taxlist'];

    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            
			if($rset[$c][2]==1)
            	$fname="Federal";
            else
            	$fname="State";

            ?> <td><a href="#" onmousedown="viewTax('<?=$rset[$c][1]?>',<?=$rset[$c][2]?>)" ><?=$fname?></a></td> <?
					for($a=0;count($list)>$a;$a++)
						if($rset[$c][1]==$list[$a][0])
						  $fname=$list[$a][1];

            echo '<td>' . $fname .'</td>';
            echo '<td>' . $rset[$c][3] .'</td>';
            echo '<td>' . $rset[$c][4] .'</td>';
            echo '<td>' . $rset[$c][5] .'</td>';
        echo '</tr>';
        }			
 } else { ?>
	
		<table width="550" align="center" border="0" class="tabForm">
				 <tr>
                      	
						 <td><strong>Federal/State</strong></td>
						 <td><strong>Tax</strong></td>
						 <td><strong>Fill Status</strong></td>
						 <td><strong>Allowance</strong></td>
						 <td><strong>Extra</strong></td>
					</tr>
<?
$rset = $this ->popArr['empTaxAss'];
$list = $this->popArr['taxlist'];

    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            
			if($rset[$c][2]==1)
            	$fname="Federal";
            else
            	$fname="State";

            ?> <td><a href="#" onmousedown="viewTax('<?=$rset[$c][1]?>',<?=$rset[$c][2]?>)" ><?=$fname?></a></td> <?
					for($a=0;count($list)>$a;$a++)
						if($rset[$c][1]==$list[$a][0])
						  $fname=$list[$a][1];

            echo '<td>' . $fname .'</td>';
            echo '<td>' . $rset[$c][3] .'</td>';
            echo '<td>' . $rset[$c][4] .'</td>';
            echo '<td>' . $rset[$c][5] .'</td>';
        echo '</tr>';
        }	
	}  		
 
 
 } elseif(isset($showTax) && $showTax=='0') {

	$edit=$this->popArr['editTaxArr'];            
?>
			  	<td>Tax Exempt</td>
				<td><strong>
<?				
				for($c=0;count($arrTaxExempt)>$c;$c++)
					if ($edit[0][1]==$arrTaxExempt[$c])
						    echo $arrTaxExempt[$c];
?>
				</strong></td>
				<td width="50">&nbsp;</td>
				<td>ETF Eligibility</td>
				<td>
<?				if(isset($_POST['chkETFEligibleFlag'])) { ?>
				<input type="checkbox" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'readonly'?> value="1" <?=($_POST['chkETFEligibleFlag']=='1'?'checked':'')?> name="chkETFEligibleFlag">
<?				} else { ?>
				<input type="checkbox" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'readonly'?> value="1" <?=($edit[0][9]=='1'?'checked':'')?> name="chkETFEligibleFlag">
<?				} ?>
				</td>
              </tr>
			  <tr>
			  <td>Tax on Tax</td>
			  <td>
<?			if(isset($_POST['chkTaxOnTaxFlag'])) { ?>
			  <input type="checkbox" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'readonly'?> value="1" <?=($_POST['chkTaxOnTaxFlag']=='1'?'checked':'')?> name="chkTaxOnTaxFlag">
<?			} else { ?>
			  <input type="checkbox" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'readonly'?> value="1" <?=($edit[0][2]=='1'?'checked':'')?> name="chkTaxOnTaxFlag">
<?			} ?>
			  
			  </td>
			  <td width="50">&nbsp;</td>
			  <td>ETF No.</td>
			  <td><strong><?=(isset($_POST['txtETFNo']))?$_POST['txtETFNo']:$edit[0][10]?></strong></td>
			  </tr>
			  <tr>
			  <td>Tax ID</td>
			  <td><strong><?=(isset($_POST['txtTaxID']))?$_POST['txtTaxID']:$edit[0][3]?></strong></td>
			  <td width="50">&nbsp;</td>
			  <td>Employee %</td>
			  <td><select><?=(isset($_POST['txtETFEmployeePercen']))?$_POST['txtETFEmployeePercen']:$edit[0][11]?></select></td>
			  </tr>
			  <tr>
			  <td>EPF Eligible</td>
			  <td>
<?			  if(isset($_POST['chkEPFEligibleFlag'])) { ?>
			  <input type="checkbox" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'readonly'?> value="1" <?=($_POST['chkEPFEligibleFlag']=='1'?'checked':'')?> name="chkEPFEligibleFlag">
<?			} else { ?>
			  <input type="checkbox" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'readonly'?> value="1" <?=($edit[0][4]=='1'?'checked':'')?> name="chkEPFEligibleFlag">
<?			} ?>  
			  </td>
			  <td width="50">&nbsp;</td>
			  <td>ETF Date</td>
			  <td><strong><?=(isset($_POST['txtETFDat']))?$_POST['txtETFDat']:$edit[0][12]?></strong>;</td>
			  </tr>
			  <tr>
			  <td>EPF No.</td>
			  <td><strong><?=(isset($_POST['txtEPFNo']))?$_POST['txtEPFNo']:$edit[0][5]?></strong></td>
			  <td width="50">&nbsp;</td>
			  <td>MSPS</td>
			  <td>
<? 			if(isset($_POST['chkMSPSEligibleFlag'])) { ?>
			  <input type="checkbox" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'readonly'?> name="chkMSPSEligibleFlag" <?=($_POST['chkMSPSEligibleFlag']=='1'?'checked':'')?> value="1">
<?			} else { ?>
			  <input type="checkbox" <?=(isset($_POST['EditMode']) && $_POST['EditMode']=='1') ? '' : 'readonly'?> name="chkMSPSEligibleFlag" <?=$edit[0][13]=='1'?'checked':''?> value="1">
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
	<? }
	
   ?>
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
          <td><table onclick="setUpdate(5)" onkeypress="setUpdate(5)" height="350" border="0" cellpadding="0" cellspacing="0">
<?
		$edit = $this->popArr['editPermResArr'];
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
<?				$list = $this->popArr['rescntlist'];
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
				$plist = $this->popArr['resplist'];
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
						$plist = $this->popArr['resplist'];
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
				$dlist = $this->popArr['resdlist'];
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
						$dlist = $this->popArr['resdlist']; 
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
			$elelist = $this->popArr['elelist'];
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
			  <td>Passport <input type="radio" checked name="PPType" value="1"></td><td> Visa <input type="radio" name="PPType" value="2"></td>
			  <td width="50">&nbsp;</td>
		  	  <td>Issued Place</td>
		  	  <td><input type="text" name="txtPPIssPlace"></td>
              </tr>
              <tr>
                <td>Passport/Visa No</td>
                <td><input type="text"  name="txtPPNo"></td>
                <td width="50">&nbsp;</td>
                <td>Issued Date</td>
                <td><input type="text" readonly name="txtPPIssDat">&nbsp;<input type="button" class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtPPIssDat);return false;"></td>
              </tr>
              <tr>
                <td>Passport/Visa Type</td>
                <td><input name="txtVisaType" type="text">
                <td width="50">&nbsp;</td>
                <td>Date of Expiry</td>
                <td><input type="text" readonly name="txtPPExpDat">&nbsp;<input type="button" class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtPPExpDat);return false;"></td>
              </tr>
              <tr>
                <td>Country</td>
                <td><select name="cmbPPCountry">
                		<option value="0">-Select Country-</option>
<?				$list = $this->popArr['ppcntlist'];
				for($c=0;$list && count($list)>$c;$c++)
				    echo "<option value='" . $list[$c][0] . "'>" . $list[$c][1]. "</option>";
?>			 
				</td>
				<td width="50">&nbsp;</td>
				<td>Comments</td>
				<td><textarea name="PPComment"></textarea></td>
				<tr>
				  <td>No of Entries</td>
				  <td><input type="text"  name="txtPPNoOfEntries"></td>
				  <td width="50">&nbsp;</td>
				  <td width="50">&nbsp;</td>
				  <td>

        <img border="0" title="Save" onClick="addPassport();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">

				  </td>
				</tr>
				<tr>
				<td>

        <img title="Delete" onclick="delPassport();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">


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
	$rset = $this->popArr['empPPAss'];

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
            echo '<td>' . $dtPrint[0] .'</td>';
            $dtPrint = explode(" ",$rset[$c][5]);
            echo '<td>' . $dtPrint[0] .'</td>';
        echo '</tr>';
        }

	} elseif(isset($this->getArr['PPSEQ'])) {
		$edit = $this->popArr['editPPForm'];
?>

          <tr>
              <input type="hidden" name="txtPPSeqNo" value="<?=$edit[0][1]?>">
			  <td>Passport <input type="radio" checked disabled name="PPType" value="1"></td><td> Visa <input type="radio" disabled name="PPType" <?=($edit[0][8]=='2')?'checked':''?> value="2"></td>
			  <td width="50">&nbsp;</td>
		  	  <td>Issued Place</td>
		  	  <td><input type="text" name="txtPPIssPlace" disabled value="<?=$edit[0][4]?>"></td>
              </tr>
              <tr>
                <td>Passport/Visa No</td>
                <td><input type="text" name="txtPPNo" disabled value="<?=$edit[0][2]?>"></td>
                <td width="50">&nbsp;</td>
                <td>Issued Date</td>
                <td><input type="text" name="txtPPIssDat" readonly value=<?=$edit[0][3]?>>&nbsp;<input type="button" disabled class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtPPIssDat);return false;"></td>
              </tr>
              <tr>
                <td>Passport/Visa Type</td>
                <td><input name="txtVisaType" type="text" disabled value="<?=$edit[0][7]?>">
                <td width="50">&nbsp;</td>
                <td>Date of Expiry</td>
                <td><input type="text" name="txtPPExpDat" readonly value=<?=$edit[0][5]?>>&nbsp;<input type="button" disabled class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtPPExpDat);return false;"></td>
              </tr>
              <tr>
                <td>Country</td>
                <td><select disabled name="cmbPPCountry">
<?				$list = $this->popArr['ppcntlist'];
				for($c=0;count($list)>$c;$c++)
					if($edit[0][9]==$list[$c][0])
					    echo "<option selected value='" . $list[$c][0] . "'>" . $list[$c][1]. "</option>";
					else
					    echo "<option value='" . $list[$c][0] . "'>" . $list[$c][1]. "</option>";
?>			 
				</td>
				<td width="50">&nbsp;</td>
				<td>Comments</td>
				<td><textarea disabled name="PPComment"><?=$edit[0][6]?></textarea></td>
				<tr>
				  <td>No of Entries</td>
				  <td><input type="text" disabled name="txtPPNoOfEntries" value="<?=$edit[0][10]?>"></td>
				  <td width="50">&nbsp;</td>
				  <td width="50">&nbsp;</td>
				  <td>
					
					        <img border="0" title="Save" onClick="editPassport();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
					
				  </td>
				</tr>
				<tr>
				<td>

        <img title="Delete" onclick="delPassport();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">

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
	$rset = $this->popArr['empPPAss'];

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
            echo '<td>' . $dtPrint[0] .'</td>';
            $dtPrint = explode(" ",$rset[$c][5]);
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
	  <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><table height="200" border="0" cellpadding="0" cellspacing="0">

          <input type="hidden" name="brchSTAT" value="">

<?   
if(isset($this->getArr['BRCH'])) {
	
		$edit = $this->popArr['editBankForm'];

	    $brchlist = $this->popArr['brchlistAll'];
        for ($a=0;count($brchlist)>$a;$a++)
             if($edit[0][0]==$brchlist[$a][0]) 
                 break;

		?>
          <tr> 
				<td>Bank Name</td> <td></td>
				<td><strong><?=$brchlist[$a][3]?></strong></td>
				<td width="50">&nbsp;</td>
				
				<td>Account Type</td>
				<td><input type="radio" name="optAccType" disabled checked value="1">Current</td>

<?				if($edit[0][3]==2) { ?>
					<td><input type="radio" disabled name="optAccType" checked value="2">Savings</td>
<?				} else { ?>
					<td><input type="radio" disabled name="optAccType" value="2">Savings</td>
<?				} ?>
              </tr>
              <tr>
              	
                <td>Branch Name</td> <td></td>
                <td><input type="hidden" name="cmbBranchCode" value="<?=$edit[0][0]?>">
                    <strong><?=$brchlist[$a][1]?></strong>
				</td>
				<td width="50">&nbsp;</td>
				<td>Amount</td>
				<td><strong><?=$edit[0][4]?></strong></td>
			</tr>
			<tr>
				<td>Account No</td>
				<td><strong><?=$edit[0][2]?></strong></td>
				
				
			</tr>
				
			<table border="0" width="450" align="center" class="tabForm">
			
			    <tr>
                      	
						 <td><strong>Branch</strong></td>
						 <td><strong>Bank</strong></td>
						 <td><strong>Account No</strong></td>
						 <td><strong>Account Type</strong></td>
						 <td><strong>Amount</strong></td>
					</tr>
<?
	$rset = $this->popArr['empBankAss'];
    $brchlist = $this->popArr['brchlistAll'];
	$bankname='';
    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            
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
} else { ?>
			<table border="0" width="450" align="center" class="tabForm">
			
			        <tr> 
						 <td><strong>Branch</strong></td>
						 <td><strong>Bank</strong></td>
						 <td><strong>Account No</strong></td>
						 <td><strong>Account Type</strong></td>
						 <td><strong>Amount</strong></td>
					</tr>
<?
	$rset = $this->popArr['empBankAss'];
    $brchlist = $this->popArr['brchlistAll'];
	$bankname='';
    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
           
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
	
}
?>
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
		

<?  if(isset($this->getArr['ATTACH'])) {
		$edit = $this->popArr['editAttForm'];
?>
              <input type="hidden" name="seqNO" value="<?=$edit[0][1]?>">
              <tr>
              	<td>Description</td>
              	<td><strong><?=$edit[0][2]?></strong>></td>
              </tr>
              <tr>
              	<td><input type="button" value="Show File" class="buton" onclick="dwPopup()"></td>
              </tr>
				<tr>
				
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				
				</tr>
			<table border="0" width="450" align="center" class="tabForm">
			
			        <tr>
						 <td><strong>File Name</strong></td>
						 <td><strong>Size</strong></td>
						 <td><strong>Type</strong></td>
					</tr>
<?
	$rset = $this->popArr['empAttAss'] ;

    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';

            ?> <td><a href="#" title="<?=$rset[$c][2]?>" onmousedown="viewAttach('<?=$rset[$c][1]?>')" ><?=$rset[$c][3]?></a></td> <?
            echo '<td>' . $rset[$c][4] .'byte(s)</td>';     
            echo '<td>' . $rset[$c][6] .'</td>';
        echo '</tr>';
        }
?>


<? }else { ?>
    <table border="0" width="450" align="center" class="tabForm">
			
			        <tr>
						 <td><strong>File Name</strong></td>
						 <td><strong>Size</strong></td>
						 <td><strong>Type</strong></td>
					</tr>
<?
	$rset = $this->popArr['empAttAss'] ;

    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';

            ?> <td><a href="#" title="<?=$rset[$c][2]?>" onmousedown="viewAttach('<?=$rset[$c][1]?>')" ><?=$rset[$c][3]?></a></td> <?
            echo '<td>' . $rset[$c][4] .'byte(s)</td>';     
            echo '<td>' . $rset[$c][6] .'</td>';
        echo '</tr>';
        }       
}   
?>
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
