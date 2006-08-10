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

require_once ROOT_PATH . '/lib/controllers/ViewController.php';
require_once ROOT_PATH . '/lib/confs/sysConf.php';

	$sysConst = new sysConf(); 
	$locRights=$_SESSION['localRights'];
		
function showAddCurrencyForm() {
	    
    $objResponse = new xajaxResponse();
	$objResponse->addScript("document.frmSalCurDet.txtCurrencyTypeDesc.disabled = false;");
	$objResponse->addScript("document.frmSalCurDet.txtCurrencyTypeDesc.focus();");

	$objResponse->addAssign('buttonLayer','innerHTML',"<input type='button' value='Save' onClick='addFormData();'>");
	$objResponse->addAssign('status','innerHTML','');
	
	return $objResponse->getXML();
}

function showEditCurrencyForm($currCode) {
	
	$view_controller = new ViewController();
	$editArr = $view_controller->xajaxObjCall($currCode,'SCD','currencyEdit');
	
	$objResponse = new xajaxResponse();
	$objResponse->addScript("document.frmSalCurDet.txtCurrencyTypeDesc.disabled = false;");
	$objResponse->addScript("document.frmSalCurDet.txtCurrencyTypeID.value = '" .$editArr[0][0]."';");
	$objResponse->addScript("document.frmSalCurDet.txtCurrencyTypeDesc.value = '" .$editArr[0][1]."';");
	
	$objResponse->addAssign('buttonLayer','innerHTML',"<input type='button' value='Save' onClick='editFormData();'>");
	$objResponse->addAssign('status','innerHTML','');
	
	return $objResponse->getXML();
}

function addExt($arrElements) {

	$view_controller = new ViewController();
	$ext_currtype = new EXTRACTOR_CurrencyTypes();
	
	$objCurrType = $ext_currtype->parseAddData($arrElements);
	$view_controller -> addData('CUR',$objCurrType,true);
	
	$view_controller = new ViewController();
	$currlist = $view_controller->xajaxObjCall($arrElements['txtSalGrdID'],'SCD','unAssCurrency');
	
	$objResponse = new xajaxResponse();
	$xajaxFiller = new xajaxElementFiller();
	$objResponse = $xajaxFiller->cmbFiller($objResponse,$currlist,0,'frmSalCurDet','cmbUnAssCurrency');
	$objResponse->addScript("document.frmSalCurDet.txtCurrencyTypeDesc.value = '';");
	$objResponse->addScript("document.frmSalCurDet.txtCurrencyTypeDesc.disabled = true;");
	$objResponse->addAssign('buttonLayer','innerHTML','');
	$objResponse->addAssign('status','innerHTML','');
	
return $objResponse->getXML();
}

function editExt($arrElements) {

	$view_controller = new ViewController();
	$ext_currtype = new EXTRACTOR_CurrencyTypes();
	
	$objCurrType = $ext_currtype -> parseEditData($arrElements);
	$view_controller->updateData('CUR',$arrElements['txtCurrencyTypeID'],$objCurrType,true);
	
	$view_controller = new ViewController();
	$currlist = $view_controller->xajaxObjCall($arrElements['txtSalGrdID'],'SCD','unAssCurrency');
	
	$objResponse = new xajaxResponse();
	$xajaxFiller = new xajaxElementFiller();
	$objResponse = $xajaxFiller->cmbFiller($objResponse,$currlist,0,'frmSalCurDet','cmbUnAssCurrency');
	$objResponse->addScript("document.frmSalCurDet.txtCurrencyTypeID.value = '';");
	$objResponse->addScript("document.frmSalCurDet.txtCurrencyTypeDesc.value = '';");
	$objResponse->addScript("document.frmSalCurDet.txtCurrencyTypeDesc.disabled = true;");
	$objResponse->addAssign('buttonLayer','innerHTML','');
	$objResponse->addAssign('status','innerHTML','');
	
return $objResponse->getXML();
}

function delExt($salgrd,$currCode) {
	
	$arrList[0][0] = $currCode;
	
	$view_controller = new ViewController();
	$view_controller ->delParser('CUR',$arrList);	

	$view_controller = new ViewController();
	$currlist = $view_controller->xajaxObjCall($salgrd,'SCD','unAssCurrency');
	
	$objResponse = new xajaxResponse();
	$xajaxFiller = new xajaxElementFiller();
	$objResponse = $xajaxFiller->cmbFiller($objResponse,$currlist,0,'frmSalCurDet','cmbUnAssCurrency');
	$objResponse->addAssign('status','innerHTML','');
	
return $objResponse->getXML();
}

$objAjax = new xajax();
$objAjax->registerFunction('showAddCurrencyForm');
$objAjax->registerFunction('showEditCurrencyForm');
$objAjax->registerFunction('addExt');
$objAjax->registerFunction('editExt');
$objAjax->registerFunction('delExt');
$objAjax->processRequests();

	$idens = split('uniqcode=', isset($_POST['referer']) ? $_POST['referer'] : $_SERVER['HTTP_REFERER']);
	
	$idens = split('&', $idens[1]);	
	
	if ($idens[0] == 'JOB') {
		$backtype=1;
	} else {
		$backtype=0;
	};
				
if ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'addmode')) { 

	
	if ($backtype == 1) {
	
		$refcapturemode = split('capturemode=', isset($postArr['referer']) ? $postArr['referer'] : $_SERVER['HTTP_REFERER']);
		$refcapturemode = split('&', $refcapturemode[1]);
		
		if ($refcapturemode[0] == 'updatemode') {
		
			$refcapturemode = $refcapturemode[0];
			
			$refid = split('id=', isset($postArr['referer']) ? $postArr['referer'] : $_SERVER['HTTP_REFERER']);
						
			$refid = split('&', $refid[1]);			
			
			$refid = $refid[0];						
		} else {
		
			$refcapturemode = 'addmode';
			$refid = '';
			
		}
	}
		
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script>			
	function goBack() {	
		
	<?	if ($backtype == 1) { ?>
		history.back();
	<? } else { ?>			
		location.href = "./CentralController.php?uniqcode=<?=$this->getArr['uniqcode']?>&VIEW=MAIN";
	<? } ?>
	
	}

	function addSave() {
		
		if (document.frmSalGrd.txtSalGrdDesc.value == '') {
			alert ("Description Cannot be a Blank Value!");
			return false;
		}
		
		document.frmSalGrd.sqlState.value = "NewRecord";
		document.frmSalGrd.submit();
	}
	
	function clearAll() {
		document.frmSalGrd.txtSalGrdDesc.value = '';
	}
</script>
<link href="../../themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("../../themes/beyondT/css/style.css"); </style>
</head>
<body>
<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
  <tr>
    <td valign='top'></td>
    <td width='100%'><h2><?=$heading?></h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>
</table>
<p>
<p> 
<table width="431" border="0" cellspacing="0" cellpadding="0" ><td width="177">
<form name="frmSalGrd" method="post" action="<?=$_SERVER['PHP_SELF']?>?uniqcode=<?=$this->getArr['uniqcode']?>">

  <tr> 
    <td height="27" valign='top'> <p> <img title="Back" onMouseOut="this.src='../../themes/beyondT/pictures/btn_back.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_back_02.jpg';"  src="../../themes/beyondT/pictures/btn_back.jpg" onClick="goBack();">
        <input type="hidden" name="sqlState" value="">
		<input type="hidden" name="refcapturemode" value="<?=$refcapturemode?>">
		<input type="hidden" name="refid" value="<?=$refid?>">
		<input type="hidden" name="backtype" value="<?=$backtype?>">
		<input type="hidden" name="referer" value="<?=$_SERVER['HTTP_REFERER']?>">
      </p></td>
    <td width="254" align='left' valign='bottom'> <font color="red" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
      <?
		if (isset($this->getArr['msg'])) {
			$expString  = $this->getArr['msg'];
			$expString = explode ("%",$expString);
			$length = sizeof($expString);
			for ($x=0; $x < $length; $x++) {		
				echo " " . $expString[$x];		
			}
		}		
		?>
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
						  <tr> 
						    <td><?=$code?></td>
						    <td><strong><?=$this->popArr['newID']?></strong></td>
						  </tr>
						  <tr> 
						    <td><?=$description?></td>
						    <td> <textarea name='txtSalGrdDesc' rows="3" tabindex='3' cols="30"></textarea>
						    </td>
						  </tr>
					  <tr><td></td><td align="right" width="100%"><img onClick="addSave();" onMouseOut="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
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
</body>
</html>
<? } else if ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'updatemode')) {
	 $backtype = isset($_GET['backtype']) ? $_GET['backtype'] : $backtype;
	
	 if (isset($_GET['backtype'])) {
	 	if (isset($_GET['refcapturemode']) && ($_GET['refcapturemode'] == 'addmode')) {
		
			$referer = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?uniqcode=JOB&capturemode=".$_GET['refcapturemode'];
			
		} else {
		
			$referer = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?id=".$_GET['refid']."&uniqcode=JOB&capturemode=".$_GET['refcapturemode'];
			
		}
	 } else { 
	 	$referer = isset($_POST['referer']) ? $_POST['referer'] : $_SERVER['HTTP_REFERER'];
	 }
	 $message = $this->popArr['editArr'];
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<? $objAjax->printJavascript(); ?>
<script language="JavaScript">
	function numeric(txt) {
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
	
	function goBack() {			
	<?	if ($backtype == 1) { ?>
		location.href = "<?=$referer?>";
	<? } else { ?>			
		location.href = "./CentralController.php?uniqcode=<?=$this->getArr['uniqcode']?>&VIEW=MAIN";
	<? } ?>
	
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
		
		var frm=document.frmSalGrd;

		for (var i=0; i < frm.elements.length; i++)
			frm.elements[i].disabled = false;
		document.Edit.src="../../themes/beyondT/pictures/btn_save.jpg";
		document.Edit.title="Save";
	}

	function addUpdate() {
		
		if (document.frmSalGrd.txtSalGrdDesc.value == '') {
			alert ("Description Cannot be a Blank Value!");
			return false;
		}
		
		document.frmSalGrd.sqlState.value = "UpdateRecord";
		document.frmSalGrd.submit();
	}
	
	function addEXT() {
		
		if(document.frmSalCurDet.cmbUnAssCurrency.value=='0') {
			alert("Field should be selected");
			document.frmSalCurDet.cmbUnAssCurrency.focus();
			return;
		}
		
		var cnt=document.frmSalCurDet.txtMinSal;
		
		if(!numeric(cnt)) {
			alert("Field should be Numeric");
			cnt.focus();
			return;
		} else 
			var min=eval(cnt.value);
		
		var cnt=document.frmSalCurDet.txtMaxSal;
		
		if(!numeric(cnt)) {
			alert("Field should be Numeric");
			cnt.focus();
			return;
		} else
			var max=eval(cnt.value);
		
		if(min>max) {
			alert("Minmum Salary > Maximum Salary !");
			return;
		}
		
		var cnt=document.frmSalCurDet.txtStepSal;
		
		if(!numeric(cnt)) {
			alert("Field should be Numeric");
			cnt.focus();
			return;
		}

		document.frmSalCurDet.STAT.value="ADD";
		document.frmSalCurDet.submit();
	}

	function editEXT() {
		
		var cnt=document.frmSalCurDet.txtMinSal;
		
		if(!numeric(cnt)) {
			alert("Field should be Numeric");
			cnt.focus();
			return;
		}
		var min=eval(cnt.value);
		
		var cnt=document.frmSalCurDet.txtMaxSal;
		
		if(!numeric(cnt)) {
			alert("Field should be Numeric");
			cnt.focus();
			return;
		}
		var max=eval(cnt.value);
		
		if(min>max) {
			alert("Minmum Salary < Maximum Salary !");
			return;
		}
		
		var cnt=document.frmSalCurDet.txtStepSal;
		
		if(!numeric(cnt)) {
			alert("Field should be Numeric");
			cnt.focus();
			return;
		}
		
		  document.frmSalCurDet.STAT.value="EDIT";
		  document.frmSalCurDet.submit();
	}

	function delEXT() {
		
	      var check = 0;
			with (document.frmSalCurDet) {
				for (var i=0; i < elements.length; i++) {
					if ((elements[i].type == 'checkbox') && (elements[i].checked == true)){
						check = 1;
					}
				}
	        }
	
	        if(check==0) {
	          alert("Select atleast one check box");
	          return;
	        }
	
		document.frmSalCurDet.STAT.value="DEL";
	    document.frmSalCurDet.submit();
	}

	function moutAss() {
		
		if(document.EditAss.title=='Save') 
			document.EditAss.src='../../themes/beyondT/pictures/btn_save.jpg'; 
		else
			document.EditAss.src='../../themes/beyondT/pictures/btn_edit.jpg'; 
	}
	
	function moverAss() {
		if(document.EditAss.title=='Save') 
			document.EditAss.src='../../themes/beyondT/pictures/btn_save_02.jpg'; 
		else
			document.EditAss.src='../../themes/beyondT/pictures/btn_edit_02.jpg'; 
	}
		
	function editAss() {
		
		if(document.EditAss.title=='Save') {
			editEXT();
			return;
		}
		
		var frm=document.frmSalCurDet;

		for (var i=0; i < frm.elements.length; i++)
			frm.elements[i].disabled = false;
		document.EditAss.src="../../themes/beyondT/pictures/btn_save.jpg";
		document.EditAss.title="Save";
	}
	
	function editCurrency(currID) {
		
		location.href = document.frmSalCurDet.action + "&editID=" + currID;
	}
	
	function clearAll() {
		if(document.Edit.title!='Save') 
			return;
			
		document.frmSalGrd.txtSalGrdDesc.value = '';
	}

function addFormData() {
	
	if(document.frmSalCurDet.txtCurrencyTypeDesc.value == '') {
		alert("Empty Field!");
		document.frmSalCurDet.txtCurrencyTypeDesc.focus();
		return;
	}

	document.getElementById('status').innerHTML = 'Please Wait....'; 
	xajax_addExt(xajax.getFormValues('frmSalCurDet'));
}
	
function showEditForm() {
	
	if(document.frmSalCurDet.cmbUnAssCurrency.value == '0') {
		alert("No Selection!");
		return;
	} else {
		document.getElementById('status').innerHTML = 'Please Wait....'; 
		xajax_showEditCurrencyForm(document.frmSalCurDet.cmbUnAssCurrency.value);
	}
}

function editFormData() {
	
	if(document.frmSalCurDet.txtCurrencyTypeDesc.value == '') {
		alert("Empty Field!");
		document.frmSalCurDet.txtCurrencyTypeDesc.focus();
		return;
	}

	document.getElementById('status').innerHTML = 'Please Wait....'; 
	xajax_editExt(xajax.getFormValues('frmSalCurDet'));
}

function delCurrency() {
	
	if(document.frmSalCurDet.cmbUnAssCurrency.value == '0') {
		alert("No Selection!");
		return;
	} else {
		document.getElementById('status').innerHTML = 'Please Wait....'; 
		xajax_delExt(document.frmSalCurDet.txtSalGrdID.value, document.frmSalCurDet.cmbUnAssCurrency.value);
	}
}
</script>
<link href="../../themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("../../themes/beyondT/css/style.css"); </style>
</head>
<body>
<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
  <tr>
    <td valign='top'></td>
    <td width='100%'><h2><?=$heading?></h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'><div id="status"></div></td>
  </tr>
</table>
<p>
<p> 
<table width="431" border="0" cellspacing="0" cellpadding="0" ><td width="177">
<form name="frmSalGrd" id="frmSalGrd" method="post" action="<?=$_SERVER['PHP_SELF']?>?id=<?=$this->getArr['id']?>&uniqcode=<?=$this->getArr['uniqcode']?>">
	
  <tr> 
    <td height="27" valign='top'> <p>  <img title="Back" onMouseOut="this.src='../../themes/beyondT/pictures/btn_back.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_back_02.jpg';" src="../../themes/beyondT/pictures/btn_back.jpg" onClick="goBack();">
        <input type="hidden" name="sqlState" value="">
		<input type="hidden" name="backtype" value="<?=$backtype?>">
		<input type="hidden" name="referer" value="<?=$referer?>">
      </p></td>
    <td width="254" align='left' valign='bottom'> <font color="red" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
      <?
		if (isset($this->getArr['msg'])) {
			$expString  = $this->getArr['msg'];
			$expString = explode ("%",$expString);
			$length = sizeof($expString);
			for ($x=0; $x < $length; $x++) {		
				echo " " . $expString[$x];		
			}
		}		
		?>
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
                  			<tr><td>
							<table border="0">
                  			<tr> 
							    <td width="100"><?=$code?></td>
							    <td> <input type="hidden" name="txtSalGrdID" value=<?=$message[0][0]?>> <strong><?=$message[0][0]?></strong> </td>
							    <td>&nbsp;</td>
							  </tr>
							  <tr> 
							    <td><?=$description?></td>
							  	  <td> <textarea name='txtSalGrdDesc' rows="3" tabindex='3' disabled cols="30"><?=$message[0][1]?></textarea>
							    </td>
							  </tr>
			<tr>
		  <td></td><td align="right">
<?			if($locRights['edit']) { ?>
			        <img src="../../themes/beyondT/pictures/btn_edit.jpg" title="Edit" onMouseOut="mout();" onMouseOver="mover();" name="Edit" onClick="edit();">
<?			} else { ?>
			        <img src="../../themes/beyondT/pictures/btn_edit.jpg" onClick="alert('<?=$sysConst->accessDenied?>');">
<?			}  ?>
					  <img src="../../themes/beyondT/pictures/btn_clear.jpg" onMouseOut="this.src='../../themes/beyondT/pictures/btn_clear.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_clear_02.jpg';" onClick="clearAll();" >
						</td>
						</form>
						</tr>				  
					  </table>
					  </td>
					  </tr>
					  
				<form name="frmSalCurDet" id="frmSalCurDet" method="post" action="<?=$_SERVER['PHP_SELF']?>?id=<?=$this->getArr['id']?>&uniqcode=<?=$this->getArr['uniqcode']?>&capturemode=updatemode">
					  			<input type="hidden" name="STAT">
								<input type="hidden" name="referer" value="<?=$referer?>">
								<input type="hidden" name="txtSalGrdID" value="<?=$this->getArr['id']?>">
<?			if (!isset($this->getArr['editID'])) { ?>
					  <tr>
					  	<td height="40" valign="bottom"><h3><?=$currAss?></h3></td>
					  </tr>
					  <tr>
					  <td>
					  		<table border="0">
			                  <tr>
									<td><?=$currency?></td>
									<td><select <?=($locRights['add']) ? '' : 'disabled'?> name="cmbUnAssCurrency">
											<option value="0">---Select <?=$currency?>---</option>
			               			<? $unAssCurrency = $this->popArr['unAssCurrency'];
			               				for($c=0;$unAssCurrency && count($unAssCurrency)>$c;$c++) 
				               				echo "<option value='" .$unAssCurrency[$c][0]. "'>" .$unAssCurrency[$c][1]. "</option>";
									?>
									</select>
 									</td>
							</tr>
							
							<tr>
								<td><?=$minSal?></td>
								<td><input type="text" <?=($locRights['add']) ? '' : 'disabled'?> name="txtMinSal"></td>
							</tr>
							<tr>
								<td><?=$maxSal?></td>
								<td><input type="text" <?=($locRights['add']) ? '' : 'disabled'?> name="txtMaxSal"></td>
							</tr>
							<tr>
								<td><?=$stepSal?></td>
								<td><input type="text" <?=($locRights['add']) ? '' : 'disabled'?> name="txtStepSal"></td>
							</tr>
			<tr>
				<td>
<?					if($locRights['add']) { ?>
						<td align="left" valign="top"><img onClick="addEXT();" onMouseOut="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
<?					} else { ?>
						<td align="left" valign="top"><img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_save.jpg">
<?					}

			} elseif(isset($this->getArr['editID'])) { 
				
				$editAssCurrency = $this->popArr['editAssCurrency'];				
				?>
					  <tr>
					  	<td height="40" valign="bottom"><h3><?=$currAss?></h3></td>
					  </tr>
					  <tr>
					  <td>
					  		<table border="0">
			                  <tr>
									<td><?=$currency?></td> <input type="hidden" name="cmbUnAssCurrency" value="<?=$editAssCurrency[0][1]?>">
									<td><strong>
			               			<? $assCurrency = $this->popArr['assCurrency'];
			               				for($c=0;$assCurrency && count($assCurrency)>$c;$c++) 
			               					if($assCurrency[$c][0] == $editAssCurrency[0][1])
				               					echo $assCurrency[$c][1];
									?>
									</strong></td>
							</tr>
							<tr>
								<td><?=$minSal?></td>
								<td><input type="text" disabled name="txtMinSal" value="<?=$editAssCurrency[0][2]?>"></td>
							</tr>
							<tr>
								<td><?=$maxSal?></td>
								<td><input type="text" disabled name="txtMaxSal" value="<?=$editAssCurrency[0][3]?>"></td>
							</tr>
							<tr>
								<td><?=$stepSal?></td>
								<td><input type="text" disabled name="txtStepSal" value="<?=$editAssCurrency[0][4]?>"></td>
							</tr>
			<tr>
		  <td></td><td align="right">
<?			if($locRights['edit']) { ?>
			        <img src="../../themes/beyondT/pictures/btn_edit.jpg" title="Edit" onMouseOut="moutAss();" onMouseOver="moverAss();" name="EditAss" onClick="editAss();">
<?			} else { ?>
			        <img src="../../themes/beyondT/pictures/btn_edit.jpg" onClick="alert('<?=$sysConst->accessDenied?>');">
<?			}  

		}?>
					</td>
					  </tr>	
					  	
					  </table>
					  </td>
					  </tr>	
					<?php
					  $assCurrency = $this->popArr['assCurrency'];	  
					  
					  if ($assCurrency) {
					   ?>
					  <tr>
					  	<td>					  
<?					if($locRights['delete']) { ?>
						<img onClick="delEXT();" onMouseOut="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
<?					} else { ?>
						<img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_delete.jpg">
<?					}		?>						
					  </td>
					  </tr>
					  <tr>
							<td>
							<table border="0">
								<tr>
			                      	<td></td>
									 <td><strong><?=$currency?></strong></td>
									 <td><strong><?=$minSal?></strong></td>
									 <td><strong><?=$maxSal?></strong></td>
									 <td><strong><?=$stepSal?></strong></td>
								</tr>
			               		<? 
			               			for($c=0;$assCurrency && count($assCurrency)>$c;$c++) {
			               				echo '<tr>';
			               				echo "<td><input type='checkbox' name='chkdel[]' value='".$assCurrency[$c][0]."'></td>";
				            			echo "<td><a href=javascript:editCurrency('".$assCurrency[$c][0]."')>" .$assCurrency[$c][1] . "</a></td>";
				            			echo "<td>" .$assCurrency[$c][2]. "</td>";
				            			echo "<td>" .$assCurrency[$c][3]. "</td>";
				            			echo "<td>" .$assCurrency[$c][4]. "</td>";
			               				echo '</tr>';
			               			} 					  
								?>
							</table>
								</td>
						</tr>
						<? } ?>
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
