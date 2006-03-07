<?
/*
OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures 
all the essential functionalities required for any enterprise. 
Copyright (C) 2006 hSenid Software, http://www.hsenid.com

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

define('OpenSourceEIM', dirname(__FILE__) );
require_once OpenSourceEIM . '/lib/Models/eimadmin/CompHier.php';
require_once OpenSourceEIM . '/lib/Confs/sysConf.php';


	$parent_comphier = new CompHierachy();
	$sysConst = new sysConf(); 
	$locRights=$_SESSION['localRights'];

	
if ((isset($_POST['sqlState'])) && ($_POST['sqlState'] == 'NewRecord')) {
	
		$parent_comphier -> setHiId($parent_comphier ->getLastRecord());
		$parent_comphier -> setHiDesc(trim($_POST['txtHiDesc']));
		$parent_comphier -> setHiRelat($_POST['cmbHiRelat']);
		$parent_comphier -> setEmpId($_POST['cmbEmpID']);
		$parent_comphier -> setDefLev($_POST['cmbDefLev']);
		$parent_comphier -> setTelep($_POST['txtTelep']);
		$parent_comphier -> setFax($_POST['txtFax']);
		$parent_comphier -> setEmail($_POST['txtEmail']);
		$parent_comphier -> setUrl($_POST['txtUrl']);
		$parent_comphier -> setLogo($_POST['txtLogo']);
		$parent_comphier -> setLoc($_POST['cmbLoc']);
        $message = $parent_comphier ->addCompHierachy();

	if ($message) { 
		
		$showMsg = "Addition%Successful!"; //If $message is 1 setting up the 
		
		$uniqcode = $_GET['uniqcode'];
		$pageID = $_GET['pageID'];
		header("Location: ./view.php?message=$showMsg&uniqcode=$uniqcode&pageID=$pageID");
		
	} else {
		
		$showMsg = "Addition Unsuccessful!";
		
		$uniqcode = $_GET['uniqcode'];
		$pageID = $_GET['pageID'];
		header("Location: ./comphier.php?msg=$showMsg&capturemode=addmode&uniqcode=$uniqcode&pageID=$pageID");
	}
	
} else if ((isset($_POST['sqlState'])) && ($_POST['sqlState'] == 'UpdateRecord')) {

		$parent_comphier -> setHiId(trim($_POST['txtHiID'])	);
		$parent_comphier -> setHiDesc(trim($_POST['txtHiDesc']));
		$parent_comphier -> setHiRelat($_POST['cmbHiRelat']);
		$parent_comphier -> setEmpId($_POST['cmbEmpID']);
		$parent_comphier -> setDefLev($_POST['cmbDefLev']);
		$parent_comphier -> setTelep($_POST['txtTelep']);
		$parent_comphier -> setFax($_POST['txtFax']);
		$parent_comphier -> setEmail($_POST['txtEmail']);
		$parent_comphier -> setUrl($_POST['txtUrl']);
		$parent_comphier -> setLogo($_POST['txtLogo']);
		$parent_comphier -> setLoc($_POST['cmbLoc']);
        $message = $parent_comphier ->updateCompHierachy();
	
	// Checking whether the $message Value returned is 1 or 0
	if ($message) { 
		
		$showMsg = "Updation%Successful!"; //If $message is 1 setting up the 
		
		$uniqcode = $_GET['uniqcode'];
		$pageID = $_POST['pageID'];
		header("Location: ./view.php?message=$showMsg&uniqcode=$uniqcode&pageID=$pageID");
		
	} else {
		
		$showMsg = "Updation%Unsuccessful!";
		
		$uniqcode = $_GET['uniqcode'];
		$pageID = $_GET['pageID'];
		$id = $_GET['id'];
		header("Location: ./comphier.php?msg=$showMsg&id=$id&capturemode=updatemode&uniqcode=$uniqcode&pageID=$pageID");
	}

}
?>
<?
	if ((isset($_GET['capturemode'])) && ($_GET['capturemode'] == 'addmode')) {
	$lastRecord = $parent_comphier ->getLastRecord();
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

function echeck(str) {

		var lat=str.indexOf('@');
		var lstr=str.length;
		var ldot=str.indexOf('.');
		if (str.indexOf('@')==-1){
		   return false;
		}

		if (str.indexOf('@')==-1 || str.indexOf('@')==0 || str.indexOf('@')==lstr){
		   return false;
		}

		if (str.indexOf('.')==-1 || str.indexOf('.')==0 || str.indexOf('.')==lstr){
		    return false;
		}

		 if (str.indexOf('@',(lat+1))!=-1){
		    return false;
		 }

		 if (str.substring(lat-1,lat)=='.' || str.substring(lat+1,lat+2)=='.'){
		    return false;
		 }

		 if (str.indexOf('.',(lat+2))==-1){
		    return false;
		 }
		
		 if (str.indexOf(" ")!=-1){
		    return false;
		 }

 		 return true;
	}

	function goBack() {
		location.href = "view.php?uniqcode=<?=$_GET['uniqcode']?>";
	}

	function addSave() {
		
		if (document.frmCompDef.txtHiDesc.value == '') {
			alert ("Description Cannot be a Blank Value!");
			return false;
		}
		
			
		if(document.frmCompDef.cmbHiRelat.value=="0") {
			alert("Relation not selected");
		}

		if(document.frmCompDef.cmbEmpID.value=="0") {
			alert("Employee not selected");
		}

		if(document.frmCompDef.cmbDefLev.value=="0") {
			alert("cannot be left empty");
			document.frmCompDef.cmbDefLev.focus();
			return;
		}

		if(document.frmCompDef.cmbLoc.value=="0") {
			alert("cannot be left empty");
			document.frmCompDef.cmbLoc.focus();
			return;
		}

		var frm = document.frmCompDef;
		if(frm.txtTelep.value != '' && !numeric(frm.txtTelep)) {
			alert("Should be Numeric!");
			frm.txtTelep.focus();
			return;
		}

		if(frm.txtFax.value != '' && !numeric(frm.txtFax)) {
			alert("Should be Numeric!");
			frm.txtFax.focus();
			return;
		}

		if(frm.txtEmail.value != '' && !echeck(frm.txtEmail.value)) {
			alert("Invalid Email!");
			frm.txtEmail.focus();
			return;
		}

		document.frmCompDef.sqlState.value = "NewRecord";
		document.frmCompDef.pageID.value = "<?=$_GET['pageID']?>";
		document.frmCompDef.submit();
	}			

	function clearAll() {
		document.frmCompDef.txtHiDesc.value = "";
		document.frmCompDef.cmbHiRelat.options[0].selected=true;
		document.frmCompDef.cmbEmpID.options[0].selected=true;
		document.frmCompDef.cmbDefLev.options[0].selected=true;
		document.frmCompDef.cmbLoc.options[0].selected=true;
		document.frmCompDef.txtTelep.value="";
		document.frmCompDef.txtFax.value="";
		document.frmCompDef.txtEmail.value="";
		document.frmCompDef.txtUrl.value="";
		document.frmCompDef.txtLogo.value="";
}
</script>

<link href="./themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("./themes/beyondT/css/style.css"); </style>
</head>
<body>
<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
  <tr>
    <td valign='top'> </td>
    <td width='100%'><h2>Company Hierarchy: Company Information</h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'><A href='index.php?module=Contacts&action=index&return_module=Contacts&return_action=DetailView&&print=true' class='utilsLink'></td>
  </tr>
</table>
<p>
<p> 
<table width="431" border="0" cellspacing="0" cellpadding="0" ><td width="177">
<form name="frmCompDef" method="post" action="./comphier.php?pageID=<?=$_GET['pageID']?>&uniqcode=<?=$_GET['uniqcode']?>">
<input type="hidden" name="pageID" value="">
  <tr> 
    <td height="27" valign='top'> <p> <img title="Back" onmouseout="this.src='./themes/beyondT/pictures/btn_back.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_back_02.jpg';" src="./themes/beyondT/pictures/btn_back.jpg" onclick="goBack();">
        <input type="hidden" name="sqlState" value="">
      </p></td>
    <td width="254" align='left' valign='bottom'> <font color="red" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
      <?
		if (isset($_GET['msg'])) {
			$expString  = $_GET['msg'];
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
						    <td valign=""> <input type="text" readonly="true" name="txtHiID" value="<?=$lastRecord; ?>">
						  </tr>
						<tr>
						    <td><strong>Description</strong></td>
						    <td><textarea name='txtHiDesc' rows="3" tabindex='3' cols="30"></textarea>
						    </td>
						  </tr>
						  <tr>
						    <td><strong>Relational Hierarchy</strong></td>
						    <td> <select name="cmbHiRelat">
						    		<option value="0">-Select Hierarchy-</option>
							<?
							$hiercodes=$parent_comphier->getHierachyCodes();
						        for($c=0;$hiercodes && $c<count($hiercodes);$c++)
						            echo '<option value =' . $hiercodes[$c][0] . '>' . $hiercodes[$c][1] . '</option>';
							?>
						    </td>
						  </tr>
						  <tr>
						    <td><strong>Employee</strong></td>
						    <td> <select name="cmbEmpID">
						    		<option value="0">-Select-</option>
							<?
							$empcodes=$parent_comphier->getEmpCodes();
						        for($c=0;$empcodes && $c<count($empcodes);$c++)
						            echo '<option value =' . $empcodes[$c][0] . '>' . $empcodes[$c][0] . '</option>';
						
							?>
						    </td>
						  </tr>
						  <tr>
						    <td><strong>Definition Level</strong></td>
						    <td> <select name="cmbDefLev">
						    		<option value="0">-Select Def. Level-</option>
						    <?  $deflev=$parent_comphier->getHierachyDef();
						    
						        for($c=0;$deflev && $c<count($deflev);$c++)
						            echo '<option value =' . $deflev[$c][0] . '>' . $deflev[$c][1] . '</option>';
						    ?>
						    </td>
						  </tr>
						  <tr>
						    <td><strong>Telephone</strong></td>
						    <td> <input type="text" name="txtTelep">
						  </tr>
						  <tr>
						    <td><strong>Fax</strong></td>
						    <td> <input type="text" name="txtFax">
						  </tr>
						  <tr>
						    <td><strong>Email</strong></td>
						    <td> <input type="text" name="txtEmail">
						  </tr>
						  <tr>
						    <td><strong>Url</strong></td>
						    <td> <input type="text" name="txtUrl">
						  </tr>
						  <tr>
						    <td><strong>Logo</strong></td>
						    <td> <input type="text" name="txtLogo">
						  </tr>
						
						  <tr>
						    <td><strong>Location</strong></td>
						    <td> <select name="cmbLoc">
						    		<option value="0">-Select Location-</option>
						    <?  $loccodes= $parent_comphier->getLocCodes();
						
						        for($c=0;$loccodes && $c<count($loccodes);$c++)
						            echo '<option value =' . $loccodes[$c][0] . '>' . $loccodes[$c][1] . '</option>';
						    ?>
						    </td>
						  </tr>
					  <tr><td></td><td align="right" width="100%"><img title="Save" onClick="addSave();" onmouseout="this.src='./themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_save_02.jpg';" src="./themes/beyondT/pictures/btn_save.jpg">
									  <img onmouseout="this.src='./themes/beyondT/pictures/btn_clear.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_clear_02.jpg';" src="./themes/beyondT/pictures/btn_clear.jpg" onClick="clearAll();" >
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

</form> 
</form>
</body>
</html>
<? } else if ((isset($_GET['capturemode'])) && ($_GET['capturemode'] == 'updatemode')) {
	 $message = $parent_comphier ->filterCompHierachy($_GET['id']);
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

function echeck(str) {

		var lat=str.indexOf('@');
		var lstr=str.length;
		var ldot=str.indexOf('.');
		if (str.indexOf('@')==-1){
		   return false;
		}

		if (str.indexOf('@')==-1 || str.indexOf('@')==0 || str.indexOf('@')==lstr){
		   return false;
		}

		if (str.indexOf('.')==-1 || str.indexOf('.')==0 || str.indexOf('.')==lstr){
		    return false;
		}

		 if (str.indexOf('@',(lat+1))!=-1){
		    return false;
		 }

		 if (str.substring(lat-1,lat)=='.' || str.substring(lat+1,lat+2)=='.'){
		    return false;
		 }

		 if (str.indexOf('.',(lat+2))==-1){
		    return false;
		 }
		
		 if (str.indexOf(" ")!=-1){
		    return false;
		 }

 		 return true;
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

	var frm=document.frmCompDef;
//  alert(frm.elements.length);
	for (var i=0; i < frm.elements.length; i++)
		frm.elements[i].disabled = false;
	document.Edit.src="./themes/beyondT/pictures/btn_save.jpg";
	document.Edit.title="Save";
}
	

	function addUpdate() {
		
		if (document.frmCompDef.txtHiDesc.value == '') {
			alert ("Description Cannot be a Blank Value!");
			return false;
		}

		if(document.frmCompDef.cmbHiRelat.value=="0") {
			alert("Relation not selected");
		}

		if(document.frmCompDef.cmbEmpID.value=="0") {
			alert("Employee not selected");
		}
		
		
		var frm = document.frmCompDef;
		if(frm.txtTelep.value != '' && !numeric(frm.txtTelep)) {
			alert("Should be Numeric!");
			frm.txtTelep.focus();
			return;
		}

		if(frm.txtFax.value != '' && !numeric(frm.txtFax)) {
			alert("Should be Numeric!");
			frm.txtFax.focus();
			return;
		}

		if(frm.txtEmail.value != '' && !echeck(frm.txtEmail.value)) {
			alert("Invalid Email!");
			frm.txtEmail.focus();
			return;
		}

		document.frmCompDef.sqlState.value = "UpdateRecord";
		document.frmCompDef.pageID.value = "<?=$_GET['pageID']?>";
		document.frmCompDef.submit();
	}			

	function clearAll() {
		if(document.Edit.title!='Save') 
			return;
			
		document.frmCompDef.txtHiDesc.value = "";
		document.frmCompDef.cmbHiRelat.options[0].selected=true;
		document.frmCompDef.cmbEmpID.options[0].selected=true;
		document.frmCompDef.cmbDefLev.options[0].selected=true;
		document.frmCompDef.cmbLoc.options[0].selected=true;
		document.frmCompDef.txtTelep.value="";
		document.frmCompDef.txtFax.value="";
		document.frmCompDef.txtEmail.value="";
		document.frmCompDef.txtUrl.value="";
		document.frmCompDef.txtLogo.value="";
}
</script>

<link href="./themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("./themes/beyondT/css/style.css"); </style>
</head>
<body>
<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
  <tr>
    <td valign='top'> </td>
    <td width='100%'><h2>Company Hierarchy: Company Information</h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'><A href='index.php?module=Contacts&action=index&return_module=Contacts&return_action=DetailView&&print=true' class='utilsLink'></td>
  </tr>
</table>
<p>
<p> 
<table width="431" border="0" cellspacing="0" cellpadding="0" ><td width="177">
<form name="frmCompDef" method="post" action="./comphier.php?pageID=<?=$_GET['pageID']?>&uniqcode=<?=$_GET['uniqcode']?>">
<input type="hidden" name="pageID" value="">
  <tr> 
    <td height="27" valign='top'> <p> <img title="Back" onmouseout="this.src='./themes/beyondT/pictures/btn_back.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_back_02.jpg';" src="./themes/beyondT/pictures/btn_back.jpg" onclick="goBack();">
        <input type="hidden" name="sqlState" value="">
      </p></td>
    <td width="254" align='left' valign='bottom'> <font color="red" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
      <?
		if (isset($_GET['msg'])) {
			$expString  = $_GET['msg'];
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
						    <td> <input type="text" readonly="true" name="txtHiID" value=<?=$_GET['id'] ?> >
						    <td>&nbsp;</td>
						  </tr>
						<tr>
						    <td><strong>Description</strong></td>
						    <td> <textarea name='txtHiDesc' rows="3" disabled tabindex='3' cols="30"><?=$message[0][1]?></textarea>
						    </td>
						  </tr>
						  <tr>
						    <td><strong>Relational Hierarchy</strong></td>
						    <td> <select disabled name="cmbHiRelat">
				    		<option value="0">-Select Hierarchy-</option>
									<?
							$hiercodes=$parent_comphier->getHierachyCodes();
						        for($c=0;$c<count($hiercodes);$c++)
									if($message[0][2]==$hiercodes[$c][0])
							            echo '<option selected value =' . $hiercodes[$c][0] . '>' . $hiercodes[$c][1] . '</option>';
									else
							            echo '<option value =' . $hiercodes[$c][0] . '>' . $hiercodes[$c][1] . '</option>';
									
							?>
						    </td>
						  </tr>
						  <tr>
						    <td><strong>Employee</strong></td>
						    <td> <select disabled name="cmbEmpID">
						    		<option value="0">-Select-</option>
							<?
							$empcodes=$parent_comphier->getEmpCodes();
						        for($c=0;$c<count($empcodes);$c++)
								    if($message[0][3]==$empcodes[$c][0])
							            echo '<option selected value =' . $empcodes[$c][0] . '>' . $empcodes[$c][0] . '</option>';
									else
										echo '<option value =' . $empcodes[$c][0] . '>' . $empcodes[$c][0] . '</option>';
							?>
						    </td>
						  </tr>
						  <tr>
						    <td><strong>Definition Level</strong></td>
						    <td> <select disabled name="cmbDefLev">
						    <?  $deflev=$parent_comphier->getHierachyDef();
						    
						        for($c=0;$c<count($deflev);$c++)
									if($deflev[$c][0]==$message[0][4])
							            echo '<option selected value =' . $deflev[$c][0] . '>' . $deflev[$c][1] . '</option>';
									else
							            echo '<option value =' . $deflev[$c][0] . '>' . $deflev[$c][1] . '</option>';
						    ?>
						    </td>
						  </tr>
						  <tr>
						    <td><strong>Telephone</strong></td>
						    <td> <input type="text" name="txtTelep" disabled value="<?=$message[0][5]?>">
						  </tr>
						  <tr>
						    <td><strong>Fax</strong></td>
						    <td> <input type="text" name="txtFax" disabled value="<?=$message[0][6]?>">
						  </tr>
						  <tr>
						    <td><strong>Email</strong></td>
						    <td> <input type="text" name="txtEmail" disabled value="<?=$message[0][7]?>">
						  </tr>
						  <tr>
						    <td height="25" valign="middle"><strong>Url</strong></td>
						    <td valign=""> <input type="text" name="txtUrl" disabled value="<?=$message[0][8]?>">
						    <td width="11">&nbsp;</td>
						  </tr>
						  <tr>
						    <td><strong>Logo</strong></td>
						    <td> <input type="text" name="txtLogo" disabled value="<?=$message[0][9]?>">
						  </tr>
						
						  <tr>
						    <td><strong>Location</strong></td>
						    <td> <select disabled name="cmbLoc">
						    <?  $loccodes= $parent_comphier->getLocCodes();
						
						        for($c=0;$c<count($loccodes);$c++)
									if($message[0][10]==$loccodes[$c][0])
							            echo '<option selected value =' . $loccodes[$c][0] . '>' . $loccodes[$c][1] . '</option>';
									else
							            echo '<option value =' . $loccodes[$c][0] . '>' . $loccodes[$c][1] . '</option>';
						    ?>
						    </td>
						  </tr>					  <tr><td></td><td align="right" width="100%">
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



  </form>
</form>
</body>
</html>
<? } ?>
