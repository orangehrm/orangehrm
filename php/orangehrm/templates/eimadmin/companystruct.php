<?php
/*
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures 
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

require_once ROOT_PATH . '/lib/controllers/ViewController.php';
require_once ROOT_PATH . '/lib/extractor/eimadmin/EXTRACTOR_CompStruct.php';
require_once ROOT_PATH . '/lib/confs/sysConf.php';

function showAddCompStructForm($level) {
	    
    $objResponse = new xajaxResponse();

	$objResponse->addScript("document.frmLevData" . $level . ".txtCompStructName.disabled = false;");
	$objResponse->addScript("document.frmLevData" . $level . ".txtCompStructDescription.disabled = false;");
	$objResponse->addScript("document.frmLevData" . $level . ".txtCompStructComments.disabled = false;");
	$objResponse->addScript("document.frmLevData" . $level . ".txtCompStructName.focus();");

	$objResponse->addAssign('buttonLayer'.$level,'innerHTML',"<input type='button' value='Save' onClick='addFormData($level);'>&nbsp;<input type='button' value='Clear' onClick='clearFrmLevData($level);'>");
	$objResponse->addAssign('status','innerHTML','');
	
	return $objResponse->getXML();
}

function showEditCompStructForm($level,$compStruct) {
	
	$view_controller = new ViewController();
	$editArr = $view_controller->xajaxObjCall($compStruct,'CST','edit');
	
	$objResponse = new xajaxResponse();
	$objResponse->addScript("document.frmLevData" . $level . ".txtCompStructName.disabled = false;");
	$objResponse->addScript("document.frmLevData" . $level . ".txtCompStructDescription.disabled = false;");
	$objResponse->addScript("document.frmLevData" . $level . ".txtCompStructComments.disabled = false;");
	
	$objResponse->addScript("document.frmLevData" . $level . ".txtCompStructID.value = '" .$editArr[0][0]."';");
	$objResponse->addScript("document.frmLevData" . $level . ".txtCompStructName.value = '" .$editArr[0][1]."';");
	$objResponse->addScript("document.frmLevData" . $level . ".txtCompStructDescription.value = '" .$editArr[0][2]."';");
	$objResponse->addScript("document.frmLevData" . $level . ".txtCompStructComments.value = '" .$editArr[0][3]."';");
	
	$objResponse->addAssign('buttonLayer'.$level,'innerHTML',"<input type='button' value='Save' onClick='editFormData($level);'>&nbsp;<input type='button' value='Clear' onClick='clearFrmLevData($level);'>");
	$objResponse->addAssign('status','innerHTML','');
	
	return $objResponse->getXML();
}

function addExt($level,$arrElements,$value) {

	$view_controller = new ViewController();
	$ext_compstruct = new EXTRACTOR_CompStruct();
	
	$objCompStruct = $ext_compstruct->parseAddData($arrElements);
	$view_controller ->addData('CST',$objCompStruct);
	
	$arrValue = array((intval($level)+1),$value);
		
	$view_controller = new ViewController();
	$unAssList = $view_controller->xajaxObjCall($arrValue,'CST','unAssigned');
	
	$objResponse = new xajaxResponse();
	$xajaxFiller = new xajaxElementFiller();
	$objResponse = $xajaxFiller->cmbFiller($objResponse,$unAssList,0,'frmCompStruct'.$level,'cmbUnAssCompStruct'.$level,0);
	$objResponse->addScript("clearFrmLevData($level);");
	$objResponse->addAssign('status','innerHTML','');
	
return $objResponse->getXML();
}

function editExt($level,$arrElements,$value) {

	$view_controller = new ViewController();
	$ext_compstruct = new EXTRACTOR_CompStruct();
	
	$objCompStruct = $ext_compstruct->parseEditData($arrElements);
	$view_controller->updateData('CST','',$objCompStruct);
	
	$arrValue = array((intval($level)+1),$value);

	$view_controller = new ViewController();
	$unAssList = $view_controller->xajaxObjCall($arrValue,'CST','unAssigned');
	
	$objResponse = new xajaxResponse();
	$xajaxFiller = new xajaxElementFiller();
	$objResponse = $xajaxFiller->cmbFiller($objResponse,$unAssList,0,'frmCompStruct'.$level,'cmbUnAssCompStruct'.$level,0);
	$objResponse->addScript("clearFrmLevData($level);");
	$objResponse->addAssign('status','innerHTML','');
	
return $objResponse->getXML();
}

function delExt($level,$relat,$delcompstruct) {
	
	$arrList[0][0] = $delcompstruct;
	
	$view_controller = new ViewController();
	$view_controller ->delParser('CST',$arrList);	

	$arrValue = array((intval($level)+1),$relat);

	$view_controller = new ViewController();
	$unAssList = $view_controller->xajaxObjCall($arrValue,'CST','unAssigned');
	
	$objResponse = new xajaxResponse();
	$xajaxFiller = new xajaxElementFiller();
	$objResponse = $xajaxFiller->cmbFiller($objResponse,$unAssList,0,'frmCompStruct'.$level,'cmbUnAssCompStruct'.$level,0);
	$objResponse->addAssign('status','innerHTML','');
	
return $objResponse->getXML();
}

function assignCompStruct($level,$hiCode,$relat) {
	
	$arr = array($hiCode,$relat,intval($level)+1);
	
	$ext_comphier = new EXTRACTOR_CompHier();
	$objCompHier = $ext_comphier->parseData($arr);
	$view_controller = new ViewController();
	$view_controller ->assignData('CST',$objCompHier,'ADD');

	$arrValue = array(intval($level)+1,$relat);
	
	$view_controller = new ViewController();
	$assList = $view_controller->xajaxObjCall($arrValue,'CST','assigned');
	$unAssList = $view_controller->xajaxObjCall($arrValue,'CST','unAssigned');
	
	$objResponse = new xajaxResponse();
	$xajaxFiller = new xajaxElementFiller();
	$objResponse = $xajaxFiller->cmbFiller($objResponse,$assList,0,'frmCompStruct'.$level,'cmbAssCompStruct'.$level,0);
	$objResponse = $xajaxFiller->cmbFiller($objResponse,$unAssList,0,'frmCompStruct'.$level,'cmbUnAssCompStruct'.$level,0);
	$objResponse->addAssign('status','innerHTML','');

	return $objResponse->getXML();
}

function unAssignCompStruct($level,$hiCode,$relat,$maxlen) {
	
	$delArr[0][0] = $hiCode;
	$delArr[1][0] = $relat;
	
	$view_controller = new ViewController();
	$view_controller ->delParser('CHI',$delArr);

	$arrValue = array(intval($level)+1,$relat);
	
	$view_controller = new ViewController();
	$assList = $view_controller->xajaxObjCall($arrValue,'CST','assigned');
	$unAssList = $view_controller->xajaxObjCall($arrValue,'CST','unAssigned');
	
	$objResponse = new xajaxResponse();
	$xajaxFiller = new xajaxElementFiller();
	$objResponse = $xajaxFiller->cmbFiller($objResponse,$assList,0,'frmCompStruct'.$level,'cmbAssCompStruct'.$level,0);
	$objResponse = $xajaxFiller->cmbFiller($objResponse,$unAssList,0,'frmCompStruct'.$level,'cmbUnAssCompStruct'.$level,0);

	for($c = $level+1; ((int)$maxlen) > $c; $c++) {
		$objResponse->addScript("document.getElementById('cmbAssCompStruct" .$c. "').options.length = 0;");
		$objResponse->addScript("document.getElementById('cmbUnAssCompStruct" .$c. "').options.length = 0;");
	}

	$objResponse->addAssign('status','innerHTML','');

	return $objResponse->getXML();
}

function traceCompStruct($level,$value,$maxlev) {
	
	$arrValue = array(intval($level)+2,$value);
	
	$view_controller = new ViewController();
	$assList = $view_controller->xajaxObjCall($arrValue,'CST','assigned');
	$unAssList = $view_controller->xajaxObjCall($arrValue,'CST','unAssigned');
	
	$objResponse = new xajaxResponse();
	$xajaxFiller = new xajaxElementFiller();
	
	$maxlev = ((int)$maxlev)-1;
	
	if(((int)$level) < $maxlev) {
		$objResponse = $xajaxFiller->cmbFiller($objResponse,$assList,0,'frmCompStruct'.((int)$level+1),'cmbAssCompStruct'.((int)$level+1),0);
		$objResponse = $xajaxFiller->cmbFiller($objResponse,$unAssList,0,'frmCompStruct'.((int)$level+1),'cmbUnAssCompStruct'.((int)$level+1),0);

		for($c = $level+2; $maxlev >= $c; $c++) {
			$objResponse->addScript("document.getElementById('cmbAssCompStruct" .$c. "').options.length = 0;");
			$objResponse->addScript("document.getElementById('cmbUnAssCompStruct" .$c. "').options.length = 0;");
		}
	}

	$objResponse->addAssign('status','innerHTML','');

	return $objResponse->getXML();
}

	$objAjax = new xajax();
	$objAjax->registerFunction('showAddCompStructForm');
	$objAjax->registerFunction('addExt');
	$objAjax->registerFunction('showEditCompStructForm');
	$objAjax->registerFunction('editExt');
	$objAjax->registerFunction('delExt');
	$objAjax->registerFunction('assignCompStruct');
	$objAjax->registerFunction('unAssignCompStruct');
	$objAjax->registerFunction('traceCompStruct');
	$objAjax->processRequests();

	$sysConst = new sysConf(); 
	$locRights=$_SESSION['localRights'];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Untitled Document</title>
<?php $objAjax->printJavascript(); ?>
<script language="JavaScript">

function addFormData(level) {
	
	var frm = 'frmLevData' + level;
	
	if(document.getElementById(frm).txtCompStructName.value == '') {
		alert("Empty Field!");
		document.getElementById(frm).txtCompStructName.focus();
		return;
	}
		
	if(document.getElementById(frm).txtCompStructDescription.value == '') {
		alert("Empty Field!");
		document.getElementById(frm).txtCompStructDescription.focus();
		return;
	}
	
	if(level == 0)
		xajax_addExt(level,xajax.getFormValues(frm),'');	
	else
		xajax_addExt(level,xajax.getFormValues(frm),document.getElementById('cmbAssCompStruct'+(eval(level)-1)).value);
}

function editFormData(level) {
	
	if(document.getElementById('frmLevData'+level).txtCompStructName.value == '') {
		alert("Empty Field!");
		document.getElementById('frmLevData'+level).txtCompStructName.focus();
		return;
	}
		
	if(document.getElementById('frmLevData'+level).txtCompStructDescription.value == '') {
		alert("Empty Field!");
		document.getElementById('frmLevData'+level).txtCompStructDescription.focus();
		return;
	}
	
	if(level == 0)
		xajax_editExt(level,xajax.getFormValues('frmLevData'+level),'');	
	else
		xajax_editExt(level,xajax.getFormValues('frmLevData'+level),document.getElementById('cmbAssCompStruct'+(eval(level)-1)).value);
}

function showEditForm(level,value) {
	
	if(document.getElementById('cmbUnAssCompStruct'+level).selectedIndex == -1) {
		alert("No Selection!");
		return;
	} else {
		document.getElementById('status').innerHTML = 'Please Wait....'; 
		xajax_showEditCompStructForm(level,value);	
	}
}

function removeCompStruct(level,value) {
	
	if(document.getElementById('cmbUnAssCompStruct'+level).selectedIndex == -1) {
		alert("No Selection!");
		return;
	}

	document.getElementById('status').innerHTML = 'Please Wait....'; 
	
	if(level == 0)
		xajax_delExt(level,'',value);
	else
		xajax_delExt(level,document.getElementById('cmbAssCompStruct'+(eval(level)-1)).value,value);
}

function assignCompStruct(level) {

	if(document.getElementById('cmbUnAssCompStruct'+level).selectedIndex == -1) {
		alert('No Selection!');
		return;
	} 

	document.getElementById('status').innerHTML = 'Please Wait....'; 

	if(level == 0) 
		xajax_assignCompStruct(level,document.getElementById('cmbUnAssCompStruct'+level).value,'');
	else
		xajax_assignCompStruct(level,document.getElementById('cmbUnAssCompStruct'+level).value,document.getElementById('cmbAssCompStruct'+(level-1)).value);
}

function unAssignCompStruct(level,maxlen) {
	
	if(document.getElementById('cmbAssCompStruct'+level).selectedIndex == -1) {
		alert('No Selection!');
		return;
	}
	
	document.getElementById('status').innerHTML = 'Please Wait....'; 
	alert('count:' + maxlen);
	if(level == 0)
		xajax_unAssignCompStruct(level,document.getElementById('cmbAssCompStruct'+level).value,'',maxlen);
	else
		xajax_unAssignCompStruct(level,document.getElementById('cmbAssCompStruct'+level).value,document.getElementById('cmbAssCompStruct'+(level-1)).value,maxlen);
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

function clearFrmLevData(level) {
	
	document.getElementById('frmLevData'+level).txtCompStructID.value = '';
	document.getElementById('frmLevData'+level).txtCompStructName.value = '';
	document.getElementById('frmLevData'+level).txtCompStructDescription.value = '';
	document.getElementById('frmLevData'+level).txtCompStructComments.value = '';
	
	document.getElementById('frmLevData'+level).txtCompStructName.disabled = true;
	document.getElementById('frmLevData'+level).txtCompStructDescription.disabled = true;
	document.getElementById('frmLevData'+level).txtCompStructComments.disabled = true;
	document.getElementById('buttonLayer'+level).innerHTML = '';
}
</script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="../../themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("../../themes/beyondT/css/style.css"); </style>
</head>
<body>
<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
  <tr>
    <td valign='top'></td>
    <td width='100%'><h2>Company Structure</h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'><b><div  id="status"></div></b></td>
  </tr>
</table>
<p>
<p> 
<table width="431" border="0" cellspacing="0" cellpadding="0" ><td width="177">
  <tr> 
    <td height="27" valign='top'></td>
    <td width="254" align='left' valign='bottom'> <font color="red" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
      </font> </td>
  </tr><td width="177">
</table>

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
                  
						<?php 
							$hierlist = $this->popArr['hierlist'];
							
						for($c=0;$hierlist && count($hierlist) > $c;$c++) { ?>
						
	                  <form name="frmCompStruct<?php echo $c?>" id="frmCompStruct<?php echo $c?>">

						<tr><td><table width="100%">
						  <tr>
						    <td height="25"><strong><?php echo $hierlist[$c][1]?></strong></td>
						  </tr><tr>
						    <td width="16%">
						        <select size="3" id="cmbAssCompStruct<?php echo $c?>" name="cmbAssCompStruct<?php echo $c?>" onchange="document.getElementById('status').innerHTML = 'Please Wait.......'; xajax_traceCompStruct(<?php echo $c?>,this.value,<?php echo count($hierlist)?>);">
							<?php		if($c == 0) {
								
										$toplist = $this->popArr['compstruct'];
										for($i=0; $toplist && count($toplist)>$i ; $i++) {
											echo "<option value='" . $toplist[$i][0] . "'>" . $toplist[$i][1] . "</option>";
										}
									}
							?>
						        </select>
						     </td>
						     <td width="16%" align="center">
						     <input type="button" id="butAssCompStruct<?php echo $c?>" value="< Add" onclick="assignCompStruct(<?php echo $c?>)"><br><br>
						     <input type="button" id="butUnAssCompStruct<?php echo $c?>" value="> Remove" onclick="unAssignCompStruct(<?php echo $c?>,<?php echo count($hierlist)?>)"></td>
						    <td>
						        <select size="3" id="cmbUnAssCompStruct<?php echo $c?>" name="cmbUnAssCompStruct<?php echo $c?>">
							<?php		if($c == 0) {
								
										$unAsslist = $this->popArr['unAssCompStruct'];
										for($i=0; $unAsslist && count($unAsslist)>$i ; $i++) {
											echo "<option value='" . $unAsslist[$i][0] . "'>" . $unAsslist[$i][1] . "</option>";
										}
									}
							?>
						        </select></td><td width="50%">
						        <input type="button" id="butAdd<?php echo $c?>" name="butAddCompStruct" value="New <?php echo $hierlist[$c][1]?>" onclick="document.getElementById('status').innerHTML = 'Please Wait....'; xajax_showAddCompStructForm(<?php echo $c?>);">
						        <input type="button" id="butEdit<?php echo $c?>" name="butEditCompStruct" value="Edit <?php echo $hierlist[$c][1]?>" onclick="showEditForm(<?php echo $c?>,document.getElementById('cmbUnAssCompStruct<?php echo $c?>').value);">
						        <input type="button" id="butRem<?php echo $c?>" name="butRemCompStruct" value="Remove <?php echo $hierlist[$c][1]?>" onclick="document.getElementById('status').innerHTML = 'Please Wait....'; removeCompStruct(<?php echo $c?>,document.getElementById('cmbUnAssCompStruct<?php echo $c?>').value);">
						      </td>
						  </tr></form>
						  <tr><td><form name='frmLevData<?php echo $c?>' id='frmLevData<?php echo $c?>'>
				</table></td></tr>
				<tr><td>
						  <!-- form fits here -->
	<table border='0' cellpadding='0' cellspacing='0'>
    <tr><td width='13'><img name='table_r1_c1' src='../../themes/beyondT/pictures/table_r1_c1.gif' width='13' height='12' border='0' alt=''></td>
    <td width='220' background='../../themes/beyondT/pictures/table_r1_c2.gif'><img name='table_r1_c2' src='../../themes/beyondT/pictures/spacer.gif' width='1' height='1' border='0' alt=''></td>
    <td width='13'><img name='table_r1_c3' src='../../themes/beyondT/pictures/table_r1_c3.gif' width='13' height='12' border='0' alt=''></td>
    <td width='11'><img src='../../themes/beyondT/pictures/spacer.gif' width='1' height='12' border='0' alt=''></td></tr>
    <tr><td background='../../themes/beyondT/pictures/table_r2_c1.gif'><img name='table_r2_c1' src='../../themes/beyondT/pictures/spacer.gif' width='1' height='1' border='0' alt=''></td>
    <td><table width='100%' border='0' cellpadding='5' cellspacing='0' class=''>
	<tr>
	<td>Name:</td>
	<input type='hidden' name='txtCompStructID' value=''>
	<td><input type='text' disabled name='txtCompStructName' value=''></td>
	<td>Description</td>
	<td><textarea disabled name='txtCompStructDescription'></textarea></td>
	<td>Comments</td>
	<td><textarea disabled name='txtCompStructComments'></textarea></td>
	  </tr>
	    <td></td><td></td><td></td><td></td><td></td><td align='right'>
	    <div id="buttonLayer<?php echo $c?>"></div>
		<input type='hidden' name='txtCompStructLevel' value='<?php echo $c+1?>'>
		</td>
	</tr>
    </table></td><td background='../../themes/beyondT/pictures/table_r2_c3.gif'><img name='table_r2_c3' src='../../themes/beyondT/pictures/spacer.gif' width='1' height='1' border='0' alt=''></td>
    <td><img src='../../themes/beyondT/pictures/spacer.gif' width='1' height='1' border='0' alt=''></td></tr>
    <tr><td><img name='table_r3_c1' src='../../themes/beyondT/pictures/table_r3_c1.gif' width='13' height='16' border='0' alt=''></td>
    <td background='../../themes/beyondT/pictures/table_r3_c2.gif'><img name='table_r3_c2' src='../../themes/beyondT/pictures/spacer.gif' width='1' height='1' border='0' alt=''></td>
    <td><img name='table_r3_c3' src='../../themes/beyondT/pictures/table_r3_c3.gif' width='13' height='16' border='0' alt=''></td>
    <td><img src='../../themes/beyondT/pictures/spacer.gif' width='1' height='16' border='0' alt=''></td></tr></table>
						  <!-- form ends here -->
							</form>
							</td></tr>
							</td>
						  </tr>
						  <?php } ?>
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
</body>
</html>
