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
require_once OpenSourceEIM . '/lib/Models/eimadmin/DistrictInfo.php';
require_once OpenSourceEIM . '/lib/Confs/sysConf.php';

	$parent_districtinfo = new DistrictInfo();
	$sysConst = new sysConf(); 
	$locRights=$_SESSION['localRights'];

	
if ((isset($_POST['sqlState'])) && ($_POST['sqlState'] == 'NewRecord')) {
	$parent_districtinfo -> setDistrictInfoId($parent_districtinfo ->getLastRecord());
	$parent_districtinfo -> setDistrictInfoDesc(trim($_POST['txtDistrictInfoDesc']));
	$parent_districtinfo -> setProvinceId(trim($_POST['selProvinceId']));
	$message = $parent_districtinfo ->addDistrictInfo();
		
	// Checking whether the $message Value returned is 1 or 0
	if ($message) { 
		
		$showMsg = "Addition%Successful!"; //If $message is 1 setting up the 
		
		$uniqcode = $_GET['uniqcode'];
		$pageID = $_POST['pageID'];
		header("Location: ./view.php?message=$showMsg&uniqcode=$uniqcode&pageID=$pageID");
		
	} else {
		
		$showMsg = "Addition%Unsuccessful!";
		
		$uniqcode = $_GET['uniqcode'];
		$pageID = $_GET['pageid'];
		header("Location: ./districtinformation.php?message=$showMsg&captureState=AddMode");
	}
	
} else if ((isset($_POST['sqlState'])) && ($_POST['sqlState'] == 'UpdateRecord')) {
	
	$parent_districtinfo -> setDistrictInfoId(trim($_POST['txtDistrictInfoId']));
	$parent_districtinfo -> setDistrictInfoDesc(trim($_POST['txtDistrictInfoDesc']));
	$parent_districtinfo -> setProvinceId(trim($_POST['selProvinceId']));
	$message = $parent_districtinfo ->updateDistrictInfo();
	
	// Checking whether the $message Value returned is 1 or 0
	if ($message) { 
		
		$showMsg = "Updation%Successful!"; //If $message is 1 setting up the 
		
		$uniqcode = $_GET['uniqcode'];
		$pageID = $_POST['pageID'];
		header("Location: ./view.php?message=$showMsg&uniqcode=$uniqcode&pageID=$pageID");
		
	} else {
		
		$showMsg = "Updation%Unsuccessful!";
		
		$uniqcode = $_GET['uniqcode'];
		$pageID = $_GET['pageid'];
		header("Location: ./districtinformation.php?message=$showMsg&captureState=AddMode");
	}
}
?>
<?
	if ((isset($_GET['capturemode'])) && ($_GET['capturemode'] == 'addmode')) {
	$lastRecord = $parent_districtinfo ->getLastRecord();
	?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script>			
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
	
	function goBack() {
		location.href = "view.php?uniqcode=<?=$_GET['uniqcode']?>";
	}

	function addSave() {
		var txt=document.frmDistrictInformation.txtDistrictInfoDesc;
		if (!alpha(txt)) {
			alert ("Description Error!");
			txt.focus();
			return false;
		}
		
		if(document.frmDistrictInformation.country.value=='0') {
			alert("Field should be selected");
			document.frmDistrictInformation.country.focus();
			return;
		}

		if(document.frmDistrictInformation.selProvinceId.value=='0') {
			alert("Field should be selected");
			document.frmDistrictInformation.selProvinceId.focus();
			return;
		}

		document.frmDistrictInformation.sqlState.value = "NewRecord";
		document.frmDistrictInformation.pageID.value = "<?=$_GET['pageID']?>";
		document.frmDistrictInformation.submit();		
	}
	
	function clearAll() {
		document.frmDistrictInformation.txtDistrictInfoDesc.value = '';
		document.frmDistrictInformation.country.options[0].selected=true;
		document.frmDistrictInformation.selProvinceId.options[0].selected=true;
	}
				
</script>
<link href="./themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("./themes/beyondT/css/style.css"); </style>
</head>
<body>
<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
  <tr>
    <td valign='top'> </td>
    <td width='100%'><h2>County Information : Geo Information</h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'><A href='index.php?module=Contacts&action=index&return_module=Contacts&return_action=DetailView&&print=true' class='utilsLink'></td>
  </tr>
</table>
<p>
<p> 
<table width="431" border="0" cellspacing="0" cellpadding="0" ><td width="177">
<form name="frmDistrictInformation" method="post" action="./districtinformation.php?capturemode=addmode&pageID=<?=$_GET['pageID']?>&uniqcode=<?=$_GET['uniqcode']?>">
<input type="hidden" name="pageID" value="">
  <tr> 
    <td height="27" valign='top'> <p> <img title="Back" onmouseout="this.src='./themes/beyondT/pictures/btn_back.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_back_02.jpg';"  src="./themes/beyondT/pictures/btn_back.jpg" onclick="goBack();">
        <input type="hidden" name="sqlState" value="">
      </p></td>
    <td width="254" align='left' valign='bottom'> <font color="red" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
      </font> </td>
  </tr><td width="177">
</table>
              <table border="0" cellpadding="0" cellspacing="0">
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
							    <td><strong>Code</strong></td>
							    <td> <input type="text" readonly="true" name="txtDistrictInfoId" value=<?=$lastRecord; ?> > </td>
							  </tr>
							  <tr> 
							    <td><strong>Description</strong></td>
							    <td> <textarea name='txtDistrictInfoDesc' rows="3" tabindex='3' cols="30"><?=isset($_POST['txtDistrictInfoDesc']) ? $_POST['txtDistrictInfoDesc']:''?></textarea></td>
							  </tr>
							  <tr> 
							    <td><strong>Country</strong></td>
							    <td> <select onchange="document.frmDistrictInformation.submit();" name="country"> 
							    			<option value="0">--Select Country--</option>
							    <?
							    $rset = $parent_districtinfo->getCountryCodes();
							    for ($j=0; $rset && $j<count($rset);$j++) 
							    	if(isset($_POST['country']) && $_POST['country']==$rset[$j][0])
							    	  echo '<option selected value=' . $rset[$j][0] . '>' . $rset[$j][1] . '</option>';
							    	else
							    	  echo '<option value=' . $rset[$j][0] . '>' . $rset[$j][1] . '</option>';
							    ?>
							    </select></td>
							 </tr>
							  <tr> 
							    <td><strong>State</strong></td>
							    <td> <select name="selProvinceId"> 
							    			<option value="0">--Select State--</option>
							    <? if(isset($_POST['country'])) {
							    $getResultSet = $parent_districtinfo->getProvinceCodes($_POST['country']); 
							    for ($j=0; $getResultSet && $j<count($getResultSet);$j++) 
							    	  echo '<option value=' . $getResultSet[$j][1] . '>' . $getResultSet[$j][2] . '</option>';
							    }
							    ?>
							    </select></td>
							  </tr>
					  <tr><td></td><td align="right" width="100%"><img onClick="addSave();" onmouseout="this.src='./themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_save_02.jpg';" src="./themes/beyondT/pictures/btn_save.jpg">
        <img onClick="clearAll();" onmouseout="this.src='./themes/beyondT/pictures/btn_clear.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_clear_02.jpg';" src="./themes/beyondT/pictures/btn_clear.jpg"></td></tr>

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

</form>
</body>
</html>
<? } else if ((isset($_GET['capturemode'])) && ($_GET['capturemode'] == 'updatemode')) {
	  $message = $parent_districtinfo ->filterDistrictInfo($_GET['id']);
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<script>			
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


	function goBack() {
		location.href = "view.php?uniqcode=<?=$_GET['uniqcode']?>";
	}

function mout() {
	if(document.Edit.title=='Save') 
		document.Edit.src='./themes/beyondT/pictures/btn_save.jpg'; 
	else
		document.Edit.src='./themes/beyondT/pictures/btn_edit.jpg'; 
}

function mover() {
	if(document.Edit.title=='Save') 
		document.Edit.src='./themes/beyondT/pictures/btn_save_02.jpg'; 
	else
		document.Edit.src='./themes/beyondT/pictures/btn_edit_02.jpg'; 
}
	
function edit()
{
	if(document.Edit.title=='Save') {
		addUpdate();
		return;
	}
	
	var frm=document.frmDistrictInformation;
//  alert(frm.elements.length);
	for (var i=0; i < frm.elements.length; i++)
		frm.elements[i].disabled = false;
	document.Edit.src="./themes/beyondT/pictures/btn_save.jpg";
	document.Edit.title="Save";
}

	function addUpdate() {
		
		var txt=document.frmDistrictInformation.txtDistrictInfoDesc;
		if (!alpha(txt)) {
			alert ("Description Error!");
			txt.focus();
			return false;
		}

		if(document.frmDistrictInformation.selProvinceId.value=='0') {
			alert("Field should be selected");
			document.frmDistrictInformation.selProvinceId.focus();
			return;
		}
		
		document.frmDistrictInformation.sqlState.value = "UpdateRecord";
		document.frmDistrictInformation.pageID.value = "<?=$_GET['pageID']?>";
		document.frmDistrictInformation.submit();		
	}
	
	function clearAll() {
		if(document.Edit.title!='Save') 
			return;

		document.frmDistrictInformation.txtDistrictInfoDesc.value = '';
	}			
</script>
<link href="./themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("./themes/beyondT/css/style.css"); </style>
</head>
<body>
<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
  <tr>
    <td valign='top'> </td>
    <td width='100%'><h2>County Information : Geo Information</h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'><A href='index.php?module=Contacts&action=index&return_module=Contacts&return_action=DetailView&&print=true' class='utilsLink'></td>
  </tr>
</table>
<p>
<p> 
<table width="431" border="0" cellspacing="0" cellpadding="0" ><td width="177">
<form name="frmDistrictInformation" method="post" action="./districtinformation.php?id=<?=$_GET['id']?>&capturemode=updatemode&pageID=<?=$_GET['pageID']?>&uniqcode=<?=$_GET['uniqcode']?>">
<input type="hidden" name="pageID" value="">
  <tr> 
    <td height="27" valign='top'> <p> <img title="Back" onmouseout="this.src='./themes/beyondT/pictures/btn_back.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_back_02.jpg';" src="./themes/beyondT/pictures/btn_back.jpg" onclick="goBack();">
        <input type="hidden" name="sqlState" value="">
      </p></td>
    <td width="254" align='left' valign='bottom'> <font color="red" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
      <?
      	
      	if ((isset($message)) && ($message != '')) {
      		
      		if ($message == 1) {
      		
      			$message = "Successfully Added ! ";
      			echo $message;
      				
      		}
      		      		
      	}
      
	 ?>
      </font> </td>
  </tr><td width="177">
</table>

           <table border="0" cellpadding="0" cellspacing="0">
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
							    <td><strong>Code</strong></td>
							    <td> <input type="text" readonly="true" name="txtDistrictInfoId" value=<?=$_GET['id'] ?> ></td>
							  </tr>
							  <tr> 
							    <td><strong>Description</strong></td>
							  	<td><textarea name='txtDistrictInfoDesc' <?=isset($_POST['country']) ? '':'disabled'?> rows="3" tabindex='3' cols="30"><?=isset($_POST['txtDistrictInfoDesc']) ? $_POST['txtDistrictInfoDesc']:$message[0][1]?></textarea></td>
							  <tr> 
							    <td><strong>Country</strong></td>
							    <td> <select <?=isset($_POST['country']) ? '':'disabled'?> onchange="document.frmDistrictInformation.submit();" name="country"> 
							    			<option value="0">-Select Country-</option>
							    <?
								if(isset($_POST['country'])) {
								    $getResultSet = $parent_districtinfo->getCountryCodes();
								    for ($j=0; $getResultSet && $j<count($getResultSet);$j++) 
								    	if($getResultSet[$j][0]==$_POST['country'])
								    	  echo '<option selected value=' . $getResultSet[$j][0] . '>' . $getResultSet[$j][1] . '</option>';
								    	else
								    	  echo '<option value=' . $getResultSet[$j][0] . '>' . $getResultSet[$j][1] . '</option>';
								} else {
								    $temp=$parent_districtinfo->filterGetProvinceCodeInfo($message[0][2]);
								    $getResultSet = $parent_districtinfo->getCountryCodes();
								    for ($j=0; $getResultSet && $j<count($getResultSet);$j++) 
								    	if($getResultSet[$j][0]==$temp[0][2])
								    	  echo '<option selected value=' . $getResultSet[$j][0] . '>' . $getResultSet[$j][1] . '</option>';
								    	else
								    	  echo '<option value=' . $getResultSet[$j][0] . '>' . $getResultSet[$j][1] . '</option>';
								}
							    ?>
							    </select></td>
							 </tr>
							  <tr> 
							    <td><strong>State</strong></td>
							    <td> <select <?=isset($_POST['country']) ? '':'disabled'?> name="selProvinceId"> 
							    			<option value="0">---Select State---</option>
							    <? if(isset($_POST['country'])) {
							    $getResultSet = $parent_districtinfo->getProvinceCodes($_POST['country']); 
							    for ($j=0; $getResultSet && $j<count($getResultSet);$j++) 
							    	if($getResultSet[$j][1]==$message[0][2])
							    	   echo '<option selected value=' . $getResultSet[$j][1] . '>' . $getResultSet[$j][2] . '</option>';
							    	else
							    	   echo '<option value=' . $getResultSet[$j][1] . '>' . $getResultSet[$j][2] . '</option>';
							    } else {
							    $getResultSet = $parent_districtinfo->getProvinceCodes($temp[0][2]); 
							    for ($j=0; $getResultSet && $j<count($getResultSet);$j++) 
							    	if($getResultSet[$j][1]==$message[0][2])
							    	   echo '<option selected value=' . $getResultSet[$j][1] . '>' . $getResultSet[$j][2] . '</option>';
							    	else
							    	   echo '<option value=' . $getResultSet[$j][1] . '>' . $getResultSet[$j][2] . '</option>';
							    }
							    ?>
							    </select></td>
							  </tr>
  			  <tr><td></td><td align="right" width="100%">
<?			if($locRights['edit']) { ?>
			        <img src="./themes/beyondT/pictures/btn_edit.jpg" title="Edit" onmouseout="mout();" onmouseover="mover();" name="Edit" onClick="edit();">
<?			} else { ?>
			        <img src="./themes/beyondT/pictures/btn_edit.jpg" onClick="alert('<?=$sysConst->accessDenied?>');">
<?			}  ?>
					  <img src="./themes/beyondT/pictures/btn_clear.jpg" onmouseout="this.src='./themes/beyondT/pictures/btn_clear.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_clear_02.jpg';" onClick="clearAll();" >
</td>
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
</form></form>
</body>
</html>
<? } ?>
