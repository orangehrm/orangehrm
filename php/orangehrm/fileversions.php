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
require_once OpenSourceEIM . '/lib/Models/bugs/fileversions.php';
require_once OpenSourceEIM . '/lib/Confs/sysConf.php';

	$sysConst = new sysConf(); 
	$locRights=$_SESSION['localRights'];

$new_fileversions = new fileVersions();
$lastRecord = $new_fileversions->getLastRecord();

if ((isset($_POST['sqlState'])) && ($_POST['sqlState'] == 'NewRecord')) {
	$new_fileversions ->setFileVersionId(trim($_POST['txtID']));
	$new_fileversions ->setFileVersionName(trim($_POST['txtName']));
	$new_fileversions ->setDescription(trim($_POST['txtDescription']));
	$new_fileversions->setCreatedUser($_SESSION['user']);
	$new_fileversions->setModifiedUser($_SESSION['user']);
	$new_fileversions->setDateEntered(date('Y-m-d'));
	$new_fileversions->setModule($_POST['cmbModule']);
	$message = $new_fileversions->addFileVersions();

	if ($message) { 
		
		$showMsg = "Addition%Successful!";
		$bugcode = $_GET['bugcode'];
		$pageID = $_POST['pageID'];
		header("Location: ./bugview.php?message=$showMsg&bugcode=$bugcode&pageID=$pageID");
			
	} else {
		
		$showMsg = "Addition Unsuccessful!";
		$bugcode = $_GET['bugcode'];
		$pageID = $_GET['pageid'];
		header("Location: ./fileversions.php?message=$showMsg&captureState=AddMode");
	}	

} else if((isset($_POST['sqlState'])) && ($_POST['sqlState'] == 'UpdateRecord')) {
	$new_fileversions ->setFileVersionId(trim($_POST['txtID']));
	$new_fileversions ->setFileVersionName(trim($_POST['txtName']));
	$new_fileversions ->setDescription(trim($_POST['txtDescription']));
	$new_fileversions->setModifiedUser($_SESSION['user']);
	$new_fileversions->setDateModified(date('Y-m-d'));
	$new_fileversions->setModule($_POST['cmbModule']);
	
	$message = $new_fileversions->updateFileVersions();
	
	if ($message) { 
		$showMsg = "Updation%Successful!"; 
		$bugcode = $_GET['bugcode'];
		$pageID = $_POST['pageID'];
		header("Location: ./bugview.php?message=$showMsg&bugcode=$bugcode&pageID=$pageID");
		
	} else {
		
		$showMsg = "Updation%Unsuccessful!";
		$bugcode = $_GET['bugcode'];
		$pageID = $_GET['pageid'];
		header("Location: ./fileversions.php?message=$showMsg&captureState=AddMode");		
	}
	
}
?>
<?
	if ((isset($_GET['capturemode'])) && ($_GET['capturemode'] == 'addmode')) {
?>
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

function goBack() {
		location.href = "bugview.php?bugcode=<?=$_GET['bugcode']?>";
	}

	function addSave() {
		
		if (document.frmFileVersions.txtName.value == '') {
			alert ("Please specify a File Version Name");
			return false;
		}
		
		if (document.frmFileVersions.cmbModule.value == '0') {
			alert ("Field should be selected");
			document.frmFileVersions.cmbModule.focus();
			return false;
		}
		
		document.frmFileVersions.sqlState.value = "NewRecord";
		document.frmFileVersions.pageID.value = "<?=$_GET['pageID']?>";
		document.frmFileVersions.submit();		
	}	

	
</script>
<link href="./themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("./themes/beyondT/css/style.css"); </style>

</head>

<body>
<table width="100%" border="0">
  <tr>
    <td valign='top'> </td>
    <td width='100%'><h2>File Version</h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'><A href='index.php?module=Contacts&action=index&return_module=Contacts&return_action=DetailView&&print=true' class='utilsLink'></td>

  </tr>
</table>
<p>

<p>
<table width="500" border="0" cellspacing="0" cellpadding="0" ><!--DWLayoutTable--><td width="177">
<form name="frmFileVersions" method="post" action="./fileversions.php?pageID=<?=$_GET['pageID']?>&bugcode=<?=$_GET['bugcode']?>">
<input type="hidden" name="pageID" value="">
  <tr> 
        <td height="27" valign='top'> <p>  <img title="Back" onmouseout="this.src='./themes/beyondT/pictures/btn_back.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_back_02.jpg';"  src="./themes/beyondT/pictures/btn_back.jpg" onclick="goBack();">
            <input type="hidden" name="sqlState" value="">
          </p></td>
    <td width="254" align='left' valign='bottom'> <font color="red" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
      </font> </td>
  </tr><td width="177"><form name="frmFileVersions" method="post" action="./fileversions.php">
</table>

<label>

              <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="13"><img name="table_r1_c1" src="themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="339" background="themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td width="13"><img name="table_r1_c3" src="themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="11"><img src="themes/beyondT/pictures/spacer.gif" width="1" height="12" border="0" alt=""></td>
                </tr>
                <tr>
                  <td background="themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><table border="0" cellpadding="5" cellspacing="0" class="">
                    
  <tr> 
    <td>File Version ID</td>
    <td><input type="text" name="txtID" readonly="true" value=<?=$lastRecord; ?>></td>
  </tr>
   <tr> 
    <td>Name</td>
    <td><input type="text" name="txtName"></td>  
  </tr>
   <tr> 
    <td>Description</td>
    <td><textarea name='txtDescription' rows="3" cols="30"></textarea></td>
  </tr>
   <tr> 
    <td>Altered Module</td>
    <td><select name="cmbModule">
    		<option value="0">--Select Module--</option>
<?			$modlist=$new_fileversions->getModuleList();
			for($c=0;$modlist && count($modlist)>$c;$c++)
			   echo "<option value='" .$modlist[$c][0]. "'>" .$modlist[$c][1]. "</option>";
?>    
    </select></td>
  </tr>
  					  <tr><td></td><td align="right" width="100%"><img onClick="addSave();" onmouseout="this.src='./themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_save_02.jpg';" src="./themes/beyondT/pictures/btn_save.jpg">
        <img onClick="document.frmFileVersions.reset();" onmouseout="this.src='./themes/beyondT/pictures/btn_clear.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_clear_02.jpg';" src="./themes/beyondT/pictures/btn_clear.jpg"></td></tr>
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

</label>
</form>
<p> 
<p> 
</body>
</html>
<? } else if ((isset($_GET['capturemode'])) && ($_GET['capturemode'] == 'updatemode')) {
	 $message = $new_fileversions->filterFileVersions($_GET['id']);
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>File Versions-Update</title>
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

	function goBack() {
		location.href = "bugview.php?bugcode=<?=$_GET['bugcode']?>";
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
	
	var frm=document.frmFileVersions;
//  alert(frm.elements.length);
	for (var i=0; i < frm.elements.length; i++)
		frm.elements[i].disabled = false;
	document.Edit.src="./themes/beyondT/pictures/btn_save.jpg";
	document.Edit.title="Save";
}

	function addUpdate() {
		if (document.frmFileVersions.txtName.value == '') {
			alert ("Please specify a File Version Name");
			return false;
		}

		if (document.frmFileVersions.cmbModule.value == '0') {
			alert ("Field should be selected");
			document.frmFileVersions.cmbModule.focus();
			return false;
		}

		document.frmFileVersions.sqlState.value = "UpdateRecord";
		document.frmFileVersions.pageID.value = "<?=$_GET['pageID']?>";
		document.frmFileVersions.submit();
	}			
</script>
<link href="./themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("./themes/beyondT/css/style.css"); </style>
</head>
<body>
<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
  <tr>
    <td valign='top'></td>
    <td width='100%'><h2>File Versions: Update</h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'><A href='index.php?module=Contacts&action=index&return_module=Contacts&return_action=DetailView&&print=true' class='utilsLink'></td>
  </tr>
</table>
<p>
<p> 
<table width="431" border="0" cellspacing="0" cellpadding="0" ><td width="177">
<form name="frmFileVersions" method="post" action="./fileversions.php?pageID=<?=$_GET['pageID']?>&bugcode=<?=$_GET['bugcode']?>">
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
  </tr><td width="177"><form name="frmFileVersions" method="post" action="./fileversions.php">
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
                  <td><table border="0" cellpadding="5" cellspacing="0" class="">
			<tr> 
    <td>File Version ID</td>
    <td><input type="text" name="txtID" disabled readonly="true" value=<?=$_GET['id'] ?>></td>
  </tr>
  <tr> 
    <td>Name</td>
    <td><input type="text" name="txtName" disabled value="<?=$message[0][1]?>"> </td>
  </tr>
   <tr> 
    <td>Description</td>
    <td><textarea name='txtDescription' disabled rows="3" cols="30"><?=$message[0][6]?></textarea>
    </td>  
  </tr>
   <tr> 
    <td>Altered Module</td>
    <td><select name="cmbModule" disabled>
    		<option value="0">--Select Module--</option>
<?			$modlist=$new_fileversions->getModuleList();
			for($c=0;$modlist && count($modlist)>$c;$c++)
			   if($modlist[$c][0]==$message[0][7])
				   echo "<option selected value='" .$modlist[$c][0]. "'>" .$modlist[$c][1]. "</option>";
				else
				   echo "<option value='" .$modlist[$c][0]. "'>" .$modlist[$c][1]. "</option>";
?>    
    </select></td>
  </tr>
  <tr><td></td><td>
<?			if($locRights['edit']) { ?>
			        <img src="./themes/beyondT/pictures/btn_edit.jpg" title="Edit" onmouseout="mout();" onmouseover="mover();" name="Edit" onClick="edit();">
<?			} else { ?>
			        <img src="./themes/beyondT/pictures/btn_edit.jpg" onClick="alert('<?=$sysConst->accessDenied?>');">
<?			}  ?>
					  <img src="./themes/beyondT/pictures/btn_clear.jpg" onmouseout="this.src='./themes/beyondT/pictures/btn_clear.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_clear_02.jpg';" onClick="document.frmFileVersions.reset();" >
					  </td></tr>
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
<? } ?>
