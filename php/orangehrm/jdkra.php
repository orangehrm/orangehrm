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
require_once OpenSourceEIM . '/lib/Models/eimadmin/JDKra.php';
require_once OpenSourceEIM . '/lib/Confs/sysConf.php';

	$parent_jdkra = new JDKra();
	$sysConst = new sysConf(); 
	$locRights=$_SESSION['localRights'];

	
if ((isset($_POST['sqlState'])) && ($_POST['sqlState'] == 'NewRecord')) {
	
	$parent_jdkra -> setJDKraId($parent_jdkra ->getLastRecord());
	$parent_jdkra -> setJDKraDesc(trim($_POST['txtJDKraDesc']));
	$parent_jdkra -> setJDKraBrf(trim($_POST['txtJDKraBrf']));
	$parent_jdkra -> setJDTyp(trim($_POST['cmbJDTyp']));
	$parent_jdkra -> setJDKraSkTyp(trim($_POST['cmbJDKraSkTyp']));
	$message = $parent_jdkra ->addJDKra();
	
	// Checking whether the $message Value returned is 1 or 0
	if ($message) { 
		
		$showMsg = "Addition%Successful!"; //If $message is 1 setting up the 
		
		$uniqcode = $_GET['uniqcode'];
		$pageID = $_POST['pageID'];
		header("Location: ./view.php?message=$showMsg&uniqcode=$uniqcode&pageID=$pageID");
		
	} else {
		
		$showMsg = "Addition Unsuccessful!";
		
		$uniqcode = $_GET['uniqcode'];
		$pageID = $_GET['pageid'];
		header("Location: ./jdkra.php?msg=$showMsg&capturemode=addmode&uniqcode=$uniqcode&pageID=$pageID");
	}
	
} else if ((isset($_POST['sqlState'])) && ($_POST['sqlState'] == 'UpdateRecord')) {

	$parent_jdkra -> setJDKraId(trim($_POST['txtJDKraID']));
	$parent_jdkra -> setJDKraDesc(trim($_POST['txtJDKraDesc']));
	$parent_jdkra -> setJDKraBrf(trim($_POST['txtJDKraBrf']));
	$parent_jdkra -> setJDTyp(trim($_POST['cmbJDTyp']));
	$parent_jdkra -> setJDKraSkTyp(trim($_POST['cmbJDKraSkTyp']));
	$message = $parent_jdkra ->updateJDKra();
	
	// Checking whether the $message Value returned is 1 or 0
	if ($message) { 
		
		$showMsg = "Updation%Successful!"; //If $message is 1 setting up the 
		
		$uniqcode = $_GET['uniqcode'];
		$pageID = $_GET['pageID'];
		header("Location: ./view.php?message=$showMsg&uniqcode=$uniqcode&pageID=$pageID");
		
	} else {
		
		$showMsg = "Updation%Unsuccessful!";
		
		$uniqcode = $_GET['uniqcode'];
		$pageID = $_GET['pageID'];
		$id = $_GET['id'];
		header("Location: ./jdkra.php?msg=$showMsg&id=$id&capturemode=updatemode&uniqcode=$uniqcode&pageID=$pageID");
	}

}
?>
<?
	if ((isset($_GET['capturemode'])) && ($_GET['capturemode'] == 'addmode')) {
	$lastRecord = $parent_jdkra ->getLastRecord();
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
		
		if (document.frmJDKra.txtJDKraDesc.value == '') {
			alert ("Description Cannot be a Blank Value!");
			return false;
		}
		
	/*	var txt=document.frmJDKra.txtJDKraBrf;
		if(!alpha(txt)) {
			alert("Breif Description Error!");
			txt.focus();
			return;
		}*/
		
		if(document.frmJDKra.cmbJDTyp.value=='0') {
			alert("Field cannot be left empty");
			document.frmJDKra.cmbJDTyp.focus();
			return;
		}
	
		if(document.frmJDKra.cmbJDKraSkTyp.value=='0') {
			alert("Field cannot be left empty");
			document.frmJDKra.cmbJDKraSkTyp.focus();
			return;
		}
		
		document.frmJDKra.sqlState.value = "NewRecord";
		document.frmJDKra.pageID.value = "<?=$_GET['pageID']?>";
		document.frmJDKra.submit();
	}			
	
function clearAll() {
		document.frmJDKra.txtJDKraDesc.value = '';
		document.frmJDKra.txtJDKraBrf.value='';
		document.frmJDKra.cmbJDTyp.options[0].selected=true;
		document.frmJDKra.cmbJDKraSkTyp.options[0].selected=true;
}
</script>
<link href="./themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("./themes/beyondT/css/style.css"); </style>
</head>
<body>
<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
  <tr>
    <td valign='top'> </td>
    <td width='100%'><h2>JD Key Result Area : Job Profile</h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'><A href='index.php?module=Contacts&action=index&return_module=Contacts&return_action=DetailView&&print=true' class='utilsLink'></td>
  </tr>
</table>
<p>
<p> 
<table width="431" border="0" cellspacing="0" cellpadding="0" ><td width="177">
<form name="frmJDKra" method="post" action="./jdkra.php?pageID=<?=$_GET['pageID']?>&uniqcode=<?=$_GET['uniqcode']?>">
<input type="hidden" name="pageID" value="">
  <tr> 
    <td height="27" valign='top'> <p>  <img title="Back" onmouseout="this.src='./themes/beyondT/pictures/btn_back.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_back_02.jpg';" src="./themes/beyondT/pictures/btn_back.jpg" onclick="goBack();">
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
						    <td> <input type="text" readonly="true" name="txtJDKraID" value="<?=$lastRecord?>">
						  </tr>
						  <tr> 
						    <td><strong>Description</strong></td>
						    <td> <textarea name='txtJDKraDesc' rows="3" tabindex='3' cols="30"></textarea></td>
						  </tr>
						  <tr>
						    <td><strong>Breif Description</strong></td>
						    <td> <input type="text" name="txtJDKraBrf"></td>
						  </tr>
					    <tr>
						    <td><strong>JD Type</strong></td>
						    <td> <select name="cmbJDTyp">
								<option value='0'>-Select JD Type-</option>
						    <?
							$jdktyplist = $parent_jdkra ->getJDTypeCodes();
							for($c=0; $c < count($jdktyplist); $c++)
						        echo '<option value=' . $jdktyplist[$c][0] . '>' . $jdktyplist[$c][1] . '</option>';
						    ?>
						  </tr>
						    <tr>
						    <td><strong>Skill Type</strong></td>
						    <td> <select name="cmbJDKraSkTyp">
						    		<option value="0">-Select Skill Type-</option>
						    <?
							$sktyplist = $parent_jdkra ->getSkillCodes();
						
							for($c=0; $c < count($sktyplist); $c++)
						        echo '<option value=' . $sktyplist[$c][0] . '>' . $sktyplist[$c][1] . '</option>';
						    ?>
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
</form>
</body>
</html>
<? } else if ((isset($_GET['capturemode'])) && ($_GET['capturemode'] == 'updatemode')) {
	 $message = $parent_jdkra ->filterJDKra($_GET['id']);
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

	var frm=document.frmJDKra;
//  alert(frm.elements.length);
	for (var i=0; i < frm.elements.length; i++)
		frm.elements[i].disabled = false;
	document.Edit.src="./themes/beyondT/pictures/btn_save.jpg";
	document.Edit.title="Save";
}

	function addUpdate() {
		
		if (document.frmJDKra.txtJDKraDesc.value == '') {
			alert ("Description Cannot be a Blank Value!");
			return false;
		}
		
		var txt=document.frmJDKra.txtJDKraBrf;
		if(!alpha(txt)) {
			alert("Breif Description Error!");
			txt.focus();
			return;
		}

		document.frmJDKra.sqlState.value = "UpdateRecord";
		document.frmJDKra.pageID.value = "<?=$_GET['pageID']?>";
		document.frmJDKra.submit();
	}			

function clearAll() {

	if(document.Edit.title!='Save') 
			return;

		document.frmJDKra.txtJDKraDesc.value = '';
		document.frmJDKra.txtJDKraBrf.value='';
		document.frmJDKra.cmbJDTyp.options[0].selected=true;
		document.frmJDKra.cmbJDKraSkTyp.options[0].selected=true;
}
</script>
<link href="./themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("./themes/beyondT/css/style.css"); </style>
</head>
<body>
<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
  <tr>
    <td valign='top'></td>
    <td width='100%'><h2>JD Key Result Area : Job Profile</h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'><A href='index.php?module=Contacts&action=index&return_module=Contacts&return_action=DetailView&&print=true' class='utilsLink'></td>
  </tr>
</table>
<p>
<p> 
<table width="431" border="0" cellspacing="0" cellpadding="0" ><td width="177">
<form name="frmJDKra" method="post" action="./jdkra.php?pageID=<?=$_GET['pageID']?>&uniqcode=<?=$_GET['uniqcode']?>">
<input type="hidden" name="pageID" value="">
  <tr> 
    <td height="27" valign='top'> <p>  <img title="Back" onmouseout="this.src='./themes/beyondT/pictures/btn_back.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_back_02.jpg';" src="./themes/beyondT/pictures/btn_back.jpg" onclick="goBack();">
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
						    <td> <input type="text" readonly="true" name="txtJDKraID" value=<?=$_GET['id'] ?> >
						  </tr>
						  <tr> 
						    <td><strong>Description</strong></td>
						  	  <td> <textarea name='txtJDKraDesc' rows="3" tabindex='3' disabled cols="30"><?=$message[0][1]?></textarea>
						    </td>
						  </tr>
						  <tr>
						    <td><strong>Breif Description</strong></td>
						    <td><input type="text" disabled name="txtJDKraBrf" value="<?=$message[0][2]?>">  </tr>
						    <tr>
						    <td><strong>JD Type</strong></td>
						    <td> <select disabled name="cmbJDTyp">
						    <?
							$jdktyplist = $parent_jdkra ->getJDTypeCodes();
						
							for($c=0; $c < count($jdktyplist); $c++)
						        if($message[0][3]==$jdktyplist[$c][0])
						            echo '<option selected value=' . $jdktyplist[$c][0] . '>' . $jdktyplist[$c][1] . '</option>';
						        else
						            echo '<option value=' . $jdktyplist[$c][0] . '>' . $jdktyplist[$c][1] . '</option>';
						    ?>
							</select></td>
						    </tr>
						    <tr>
						    <td><strong>Skill Type</strong></td>
						    <td> <select disabled name="cmbJDKraSkTyp">
						    <?
							$sktyplist = $parent_jdkra ->getSkillCodes();
						
							for($c=0; $c < count($sktyplist); $c++)
						        if($message[0][4]==$sktyplist[$c][0])
						            echo '<option selected value=' . $sktyplist[$c][0] . '>' . $sktyplist[$c][1] . '</option>';
						        else
						            echo '<option value=' . $sktyplist[$c][0] . '>' . $sktyplist[$c][1] . '</option>';
						
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

</form> 
</form>
</body>
</html>
<? } ?>
