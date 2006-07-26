<?
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

////xajax header
require_once ROOT_PATH . '/lib/controllers/ViewController.php';
require_once ROOT_PATH . '/lib/confs/sysConf.php';

function populateStates($value) {
	
	$view_controller = new ViewController();
	$provlist = $view_controller->xajaxObjCall($value,'LOC','province');
	
	$objResponse = new xajaxResponse();
	$xajaxFiller = new xajaxElementFiller();
	if ($provlist) {
		$objResponse->addAssign('lrState','innerHTML','<select name="txtState" id="txtState"><option value="0">--- Select ---</option></select>');
		$objResponse = $xajaxFiller->cmbFillerById($objResponse,$provlist,1,'lrState','txtState');
		
	} else {
		$objResponse->addAssign('lrState','innerHTML','<input type="text" name="txtState" id="txtState" value="">');
	}
	$objResponse->addScript('document.getElementById("txtState").Focus();');
	
	$objResponse->addScript("document.frmLocation.txtDistrict.options.length = 1;");
	$objResponse->addAssign('status','innerHTML','');
	
	
	
return $objResponse->getXML();
}

function populateDistrict($value) {
	
	$view_controller = new ViewController();
	$dislist = $view_controller->xajaxObjCall($value,'LOC','district');
	
	$objResponse = new xajaxResponse();
	$xajaxFiller = new xajaxElementFiller();
	$response = $xajaxFiller->cmbFiller($objResponse,$dislist,1,'frmLocation','cmbDistrict');
	$response->addAssign('status','innerHTML','');
	
return $response->getXML();
}

$objAjax = new xajax();
$objAjax->registerFunction('populateStates');
$objAjax->registerFunction('populateDistrict');
$objAjax->processRequests();


	$sysConst = new sysConf(); 
	$locRights=$_SESSION['localRights'];

if ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'addmode')) {
	
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<? $objAjax->printJavascript(); ?>

<? require_once ROOT_PATH . '/scripts/archive.js'; ?>

<script>			
	function goBack() {
		location.href = "./CentralController.php?uniqcode=<?=$this->getArr['uniqcode']?>&VIEW=MAIN";
	}
	
	function addSave() {

		var frm = document.frmLocation;
			
		if (frm.txtLocDescription.value == '') {
			alert ("Location Description empty!");
			frm.txtLocDescription.focus();
			return;
		}
		
		if (frm.cmbCountry.value == '0') {		
			alert ("Country not selected!");
			frm.cmbCountry.focus();
			return;
		}
		
		if ( frm.cmbProvince.value == '0') {		
			alert ("State not selected!");
			frm.cmbProvince.focus();
			return;
		}

		if ( frm.cmbDistrict.value == '0') {
			alert ("City Cannot be empty!");
			frm.cmbDistrict.focus();
			return;
		}
		
		if ( frm.txtAddress.value == '') {		
			alert ("Address empty!");
			frm.txtAddress.focus();
			return;
		}
		
		if ( frm.txtZIP.value == '' ){		
			alert ("Zip - Code Cannot be empty!");
			frm.txtZIP.focus();
			return;
		}
		
		if ( (frm.txtZIP.value != '') && (!numbers(frm.txtZIP)) ){		
			if ( ! confirm ("Zip - Code Contains non-numeric characters! Here they are"+nonNumbers(frm.txtZIP)+". Do you want to continue?") ) {
				frm.txtZIP.focus();
			return;
			}		
		}		
		

		if (frm.txtPhone.value != '' && !numeric(frm.txtPhone)) {
			alert("Should be Numeric!");
			frm.txtPhone.focus();
			return;
		}
		
		 if(frm.txtFax.value != '' && !numeric(frm.txtFax)) {		

			alert("Should be Numeric!");
			frm.txtFax.focus();
			return;
		}
		
		document.getElementById("cmbProvince").value = document.getElementById("txtState").value;
		document.frmLocation.sqlState.value = "NewRecord";
		document.frmLocation.submit();		
	}
	
	function clearAll() {
			document.frmLocation.txtLocDescription.value = '';
			document.frmLocation.cmbCountry.options[0].selected = true;
			document.frmLocation.cmbProvince.options.length = 1;
			document.frmLocation.cmbDistrict,options.length = 1;
			document.frmLocation.txtAddress.value = '';
			document.frmLocation.txtZIP.value = '';
			document.frmLocation.txtPhone.value = '';
			document.frmLocation.txtFax.value = '';
			document.frmLocation.txtComments.value = '';
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
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'>
    <b><div  id="status"></div></b></td>
  </tr>
</table>
<p>
<p> 
<table width="431" border="0" cellspacing="0" cellpadding="0" ><td width="177">
<form name="frmLocation" method="post" action="<?=$_SERVER['PHP_SELF']?>?uniqcode=<?=$this->getArr['uniqcode']?>">

  <tr> 
    <td height="27" valign='top'> <p> <img title="Back" onMouseOut="this.src='../../themes/beyondT/pictures/btn_back.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_back_02.jpg';"  src="../../themes/beyondT/pictures/btn_back.jpg" onClick="goBack();">
        <input type="hidden" name="sqlState" value="">
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
                  <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
					  <tr> 
					    <td><?=$code?></td>
					    <td><strong><?=$this->popArr['newID']?></strong></td> 
					  </tr>
					  <tr> 
					    <td><?=$description?></td>
					    <td> <textarea name='txtLocDescription' rows="3" tabindex='3' cols="30"></textarea></td>
					  <tr>
						  <td><?=$country?></td>
						  <td><select name="cmbCountry" onChange="document.getElementById('status').innerHTML = 'Please Wait....'; xajax_populateStates(this.value);">
						  		<option value="0">--Select Country--</option>
					<?
								$cntlist = $this->popArr['cntlist'];
								for($c=0;$cntlist && count($cntlist)>$c;$c++) { 
									echo "<option value='" .$cntlist[$c][0] . "'>" . $cntlist[$c][1] . '</option>';
								}
					?>
						  </select></td>
					  </tr>
					  <tr>
						  <td><?=$state?></td>
						  <td><div id="lrState" name="lrState">							    
							    <input type="text" name="txtState" id="txtState" >
							  </div>
							  <input type="hidden" name="cmbProvince" id="cmbProvince" >
						   </td>
					  </tr>
					  <tr>
						  <td><?=$city?></td>
						  <td><input type="text" name="cmbDistrict" ></td>
					  </tr>
					  <tr>
						  <td><?=$address?></td>
						  <td><textarea name="txtAddress"></textarea></td>
					  </tr>
					  <tr>
						  <td><?=$ZIP?></td>
						  <td><input type="text" name="txtZIP"></td>
					  </tr>
					  <tr>
						  <td><?=$phone?></td>
						  <td><input type="text" name="txtPhone"></td>
					  </tr>
					  <tr>
						  <td><?=$fax?></td>
						  <td><input type="text" name="txtFax"></td>
					  </tr>
					  <tr>
						  <td><?=$comments?></td>
						  <td><textarea name="txtComments"></textarea></td>
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
</form>
</body>
</html>
<? } else if ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'updatemode')) {
	 $message = $this->popArr['editArr'];
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<? $objAjax->printJavascript(); ?>

<? require_once ROOT_PATH . '/scripts/archive.js'; ?>

<script>

function edit()
{
	if(document.Edit.title=='Save') {
		addUpdate();
		return;
	}
	
	var frm=document.frmLocation;
//  alert(frm.elements.length);
	for (var i=0; i < frm.elements.length; i++)
		frm.elements[i].disabled = false;
	document.Edit.src="../../themes/beyondT/pictures/btn_save.jpg";
	document.Edit.title="Save";
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

	function addUpdate() {

		var frm = document.frmLocation;
			
		if (frm.txtLocDescription.value == '') {
			alert ("Location Description empty!");
			frm.txtLocDescription.focus();
			return;
		}
		
		if (frm.cmbCountry.value == '0') {		
			alert ("Country not selected!");
			frm.cmbCountry.focus();
			return;
		}
		
		if ( frm.cmbProvince.value == '0') {		
			alert ("State not selected!");
			frm.cmbProvince.focus();
			return;
		}

		if ( frm.cmbDistrict.value == '0' ){		
			alert ("City Cannot be empty!");
			frm.cmbDistrict.focus();
			return;
		}
		
		if ( frm.txtAddress.value == '') {		
			alert ("Address empty!");
			frm.txtAddress.focus();
			return;
		}

		if ( frm.txtZIP.value == '' ){		
			alert ("Zip - Code Cannot be empty!");
			frm.txtZIP.focus();
			return;
		}
		
		if ( (frm.txtZIP.value != '') && (!numbers(frm.txtZIP)) ){		
			if ( ! confirm ("Zip - Code Contains non-numeric characters! Here they are"+nonNumbers(frm.txtZIP)+". Do you want to continue?") ) {
				frm.txtZIP.focus();
			return;
			}		
		}	
		
		if (frm.txtPhone.value != '' && !numeric(frm.txtPhone)) {		

			alert("Should be Numeric!");
			frm.txtPhone.focus();
			return;
		}

		if (frm.txtFax.value != '' && !numeric(frm.txtFax)) {		

			alert("Should be Numeric!");
			frm.txtFax.focus();
			return;
		}
		
		document.getElementById("cmbProvince").value = document.getElementById("txtState").value;
		
		document.frmLocation.sqlState.value = "UpdateRecord";
		document.frmLocation.submit();		
	}			

	function clearAll() {
		if(document.Edit.title!='Save') 
			return;

			document.frmLocation.txtLocDescription.value = '';
			document.frmLocation.cmbCountry.options[0].selected = true;
			document.frmLocation.cmbProvince.options.length = 1;
			document.frmLocation.cmbDistrict.options.length = 1;
			document.frmLocation.txtAddress.value = '';
			document.frmLocation.txtZIP.value = '';
			document.frmLocation.txtPhone.value = '';
			document.frmLocation.txtFax.value = '';
			document.frmLocation.txtComments.value = '';
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
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'>
	<b><div align="right" id="status"></div></b></td>
  </tr>
</table>
<p>
<p> 
<table width="431" border="0" cellspacing="0" cellpadding="0" ><td width="177">
<form name="frmLocation" method="post" action="<?=$_SERVER['PHP_SELF']?>?id=<?=$this->getArr['id']?>&uniqcode=<?=$this->getArr['uniqcode']?>">

  <tr> 
    <td height="27" valign='top'> <p> <img title="Back" onMouseOut="this.src='../../themes/beyondT/pictures/btn_back.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_back_02.jpg';"  src="../../themes/beyondT/pictures/btn_back.jpg" onClick="goBack();">
        <input type="hidden" name="sqlState" value="">
      </p></td>
    <td width="254" align='left' valign='bottom'> <font color="red" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
      <?
		if (isset($this->getArr['msg'])) {
			$expString  =$this->getArr['msg'];
			$expString = explode ("%",$expString);
			$length = sizeof($expString);
			for ($x=0; $x < $length; $x++) {		
				echo " " . $expString[$x];		
			}
		}		
		?>
      </font> </td>
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
                  <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
						  <tr> 
						    <td><?=$code?></td>
						   	<input type="hidden" name="txtLocationCode" value=<?=$message[0][0]?>>
						   	<td><strong><?=$message[0][0]?></strong></td>
						  </tr>
						  <tr>
						    <td><?=$description?></td>
						  	<td> <textarea name='txtLocDescription' rows="3" disabled tabindex='3' cols="30"><?=$message[0][1]?></textarea>
						    </td>
						  </tr>
				  <tr>
						  <td><?=$country?></td>
						  <td><select name="cmbCountry" disabled onChange="document.getElementById('status').innerHTML = 'Please Wait....'; xajax_populateStates(this.value);">
						  		<option value="0">--Select Country--</option>
					<?
								$cntlist = $this->popArr['cntlist'];
								for($c=0;$cntlist && count($cntlist)>$c;$c++)  
									if ($message[0][2] == $cntlist[$c][0])
										echo "<option selected value='" .$cntlist[$c][0] . "'>" . $cntlist[$c][1] . '</option>';
									else
										echo "<option value='" .$cntlist[$c][0] . "'>" . $cntlist[$c][1] . '</option>';
					?>
						  </select></td>
					  </tr>
					  <tr>
						  <td><?=$state?></td>
						  <td>
						  	<div id="lrState" name="lrState">
							    <? if (isset($message[0][2]) && ($message[0][2] == 'US')) { ?>
							    	<select name="txtState" id="txtState" disabled>
							    		<option value="0">--Select State--</option>
					<?
								$provlist = $this->popArr['provlist'];
								for($c=0;$provlist && count($provlist)>$c;$c++)  
									if($message[0][3]==$provlist[$c][1])
										echo "<option selected value='" .$provlist[$c][1] . "'>" . $provlist[$c][2] . '</option>';
									else
										echo "<option value='" .$provlist[$c][1] . "'>" . $provlist[$c][2] . '</option>';
					?>
							    	</select>
							    	<? } else { ?>
							    	<input type="text" disabled name="txtState" id="txtState" value="<?=isset($message[0][3]) ? $message[0][3] : ''?>">
							    	<? } ?>
							    	</div>
							    	<input type="hidden" name="cmbProvince" id="cmbProvince" value="<?=isset($message[0][3]) ? $message[0][3] : ''?>">
							    	</td>
					  </tr>
					  <tr>
						  <td><?=$city?></td>
						  <td><input type="text" disabled name="cmbDistrict" value="<?=$message[0][4]?>"></td>
					  </tr>
					  <tr>
						  <td><?=$address?></td>
						  <td><textarea disabled name="txtAddress"><?=$message[0][5]?></textarea></td>
					  </tr>
					  <tr>
						  <td><?=$ZIP?></td>
						  <td><input disabled type="text" name="txtZIP" value="<?=$message[0][6]?>"></td>
					  </tr>
					  <tr>
						  <td><?=$phone?></td>
						  <td><input disabled type="text" name="txtPhone" value="<?=$message[0][7]?>"></td>
					  </tr>
					  <tr>
						  <td><?=$fax?></td>
						  <td><input disabled type="text" name="txtFax" value="<?=$message[0][8]?>"></td>
					  </tr>
					  <tr>
						  <td><?=$comments?></td>
						  <td><textarea disabled name="txtComments"><?=$message[0][9]?></textarea></td>
					  </tr>
						  <tr><td></td><td align="right" width="100%">
<?			if($locRights['edit']) { ?>
			        <img src="../../themes/beyondT/pictures/btn_edit.jpg" title="Edit" onMouseOut="mout();" onMouseOver="mover();" name="Edit" onClick="edit();">
<?			} else { ?>
			        <img src="../../themes/beyondT/pictures/btn_edit.jpg" onClick="alert('<?=$sysConst->accessDenied?>');">
<?			}  ?>
					  <img src="../../themes/beyondT/pictures/btn_clear.jpg" onMouseOut="this.src='../../themes/beyondT/pictures/btn_clear.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_clear_02.jpg';" onClick="clearAll();" >
					  
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
</form>
</body>
</html>
<? } ?>
