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
require_once OpenSourceEIM . '/lib/Models/eimadmin/Designations.php';
require_once OpenSourceEIM . '/lib/Confs/sysConf.php';

	$parent_designation = new Designations();
	$sysConst = new sysConf(); 
	$locRights=$_SESSION['localRights'];

	$lastRecord = $parent_designation ->getLastRecord();
	
if ((isset($_POST['sqlState'])) && ($_POST['sqlState'] == 'NewRecord')) {

	//$parent_currencytype = new CurrencyTypes();	
	$parent_designation -> setDesId(trim($_POST['txtDesignationID']));
	$parent_designation -> setDesDesc(trim($_POST['txtDesignationDesc']));
	$parent_designation -> setCorpTitId(trim($_POST['cmbCorpTit']));
	$parent_designation -> setSenMgmtFlag(isset($_POST['chkSenMgmt'])?'1':'0');
	$parent_designation -> setRevDate(trim($_POST['txtRevDat']));
	$parent_designation -> setDesNxtUpg(trim($_POST['cmbDesNxtUpg']));
	$message = $parent_designation ->addDesignations();
	
	// Checking whether the $message Value returned is 1 or 0
	if ($message) { 
		
		$showMsg = "Addition%Successful!"; //If $message is 1 setting up the 
		
		$uniqcode = $_GET['uniqcode'];
		$pageID = $_POST['pageID'];
		header("Location: ./view.php?message=$showMsg&uniqcode=$uniqcode&pageID=$pageID");
		
	} else {
		
		$showMsg = "Addition Unsuccessful!";
		
		$uniqcode = $_GET['uniqcode'];
		$pageID = $_GET['pageID'];
		header("Location: ./designations.php?msg=$showMsg&capturemode=addmode&uniqcode=$uniqcode&pageID=$pageID");
	}
	
} else if ((isset($_POST['sqlState'])) && ($_POST['sqlState'] == 'UpdateRecord')) {

	$parent_designation -> setDesId(trim($_POST['txtDesignationID']));
	$parent_designation -> setDesDesc(trim($_POST['txtDesignationDesc']));
	$parent_designation -> setCorpTitId(trim($_POST['cmbCorpTit']));
	$parent_designation -> setSenMgmtFlag(isset($_POST['chkSenMgmt'])?'1':'0');
	$parent_designation -> setRevDate(trim($_POST['txtRevDat']));
	$parent_designation -> setDesNxtUpg(trim($_POST['cmbDesNxtUpg']));
	$message = $parent_designation ->updateDesignations();
	
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
		header("Location: ./designations.php?msg=$showMsg&id=$id&capturemode=updatemode&uniqcode=$uniqcode&pageID=$pageID");
	}

}
?>
<?
	if ((isset($_GET['capturemode'])) && ($_GET['capturemode'] == 'addmode')) {
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript">
	function goBack() {
		location.href = "view.php?uniqcode=<?=$_GET['uniqcode']?>";
	}

	function addSave() {
		
		if (document.frmDesignations.txtDesignationDesc.value == '') {
			alert ("Description Cannot be a Blank Value!");
			return false;
		}
		
	if(document.frmDesignations.cmbCorpTit.value==0) {
		alert("should be selected");
		document.frmDesignations.cmbCorpTit.focus();
		return;
	}
		
		document.frmDesignations.sqlState.value = "NewRecord";
		document.frmDesignations.pageID.value = "<?=$_GET['pageID']?>";
		document.frmDesignations.submit();
	}
	
function clearAll() {
	document.frmDesignations.txtDesignationDesc.value = '';
	document.frmDesignations.cmbCorpTit.options[0].selected=true;
	document.frmDesignations.chkSenMgmt.checked=false;
	document.frmDesignations.txtRevDat.value='';
	document.frmDesignations.cmbDesNxtUpg.options[0].selected=true;
}
</script>
<link href="./themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("./themes/beyondT/css/style.css"); </style>
</head>
<body>
<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
  <tr>
    <td valign='top'></td>
    <td width='100%'><h2>Designation : Job Information</h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'><A href='index.php?module=Contacts&action=index&return_module=Contacts&return_action=DetailView&&print=true' class='utilsLink'></td>
  </tr>
</table>
<p>
<p> 
<table width="431" border="0" cellspacing="0" cellpadding="0" ><td width="177">
<form name="frmDesignations" method="post" action="./designations.php?pageID=<?=$_GET['pageID']?>&uniqcode=<?=$_GET['uniqcode']?>">
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
  </tr><td width="177"><form name="frmDesignations" method="post" action="./designations.php">
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
							    <td> <input type="text" readonly="true" name="txtDesignationID" value=<?=$lastRecord; ?> >
							  </tr>
							  <tr> 
							    <td><strong>Description</strong></td>
							    <td> <textarea name='txtDesignationDesc' rows="3" tabindex='3' cols="30"></textarea>
							    </td>
							  </tr>
							  <tr>
							    <td><strong>Corporate Title</strong></td>
							    <td> <select name="cmbCorpTit">
							    		<option value="0">-Select Corp. Title-</option>
							    <?
							    $corptitles = $parent_designation->getCorpTit();
							    
							    for($c=0;$corptitles && $c < count($corptitles); $c++)
							        echo '<option value=' .$corptitles[$c][0]. '>'.$corptitles[$c][1].'</option>';
							    ?>
							  </tr>
							  <tr>
							    <td><strong>Senior Management</strong></td>
							    <td> <input type="checkbox" name='chkSenMgmt' value="1">
							    </td>
							  </tr>
							  <tr>
							    <td><strong>Review Date</strong></td>
							    <td> <input type="text" readonly name='txtRevDat'>&nbsp;<input type="button" class="button" value="..." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmDesignations.txtRevDat);return false;">
							    </td>
							  </tr>
							  <tr>
							    <td><strong>Next Upgrade Level</strong></td>
							    <td> <select name="cmbDesNxtUpg">
							    		<option value="0">-Select Level-</option>
							    <?
							    $des = $parent_designation->getDes();
							
							    for($c=0;$des && $c < count($des); $c++)
							        echo '<option value=' .$des[$c][0]. '>'.$des[$c][1].'</option>';
							    ?>
							  </tr>
					  <tr><td></td><td align="right" width="100%"><img title="Save" onClick="addSave();" onmouseout="this.src='./themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_save_02.jpg';" src="./themes/beyondT/pictures/btn_save.jpg">
									  <img onmouseout="this.src='./themes/beyondT/pictures/btn_clear.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_clear_02.jpg';" src="./themes/beyondT/pictures/btn_clear.jpg" onClick="clearAll();">
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
</form>
<iframe width=174 height=189 name="gToday:normal:agenda.js" id="gToday:normal:agenda.js" src="scripts/ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; top:-500px; left:-500px;">
</iframe>
</body>
</html>
<? } else if ((isset($_GET['capturemode'])) && ($_GET['capturemode'] == 'updatemode')) {
	 $message = $parent_designation ->filterDesignations($_GET['id']);
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript">
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

	var frm=document.frmDesignations;
//  alert(frm.elements.length);
	for (var i=0; i < frm.elements.length; i++)
		frm.elements[i].disabled = false;
	document.Edit.src="./themes/beyondT/pictures/btn_save.jpg";
	document.Edit.title="Save";
}

	function addUpdate() {
		
		if (document.frmDesignations.txtDesignationDesc.value == '') {
			alert ("Description Cannot be a Blank Value!");
			return false;
		}
		
		document.frmDesignations.sqlState.value = "UpdateRecord";
		document.frmDesignations.pageID.value = "<?=$_GET['pageID']?>";
		document.frmDesignations.submit();
	}			

function clearAll() {
	if(document.Edit.title!='Save') 
			return;

	document.frmDesignations.txtDesignationDesc.value = '';
	document.frmDesignations.cmbCorpTit.options[0].selected=true;
	document.frmDesignations.chkSenMgmt.checked=false;
	document.frmDesignations.txtRevDat.value='';
	document.frmDesignations.cmbDesNxtUpg.options[0].selected=true;
}
	
</script>
<link href="./themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("./themes/beyondT/css/style.css"); </style>
</head>
<body>
<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
  <tr>
    <td valign='top'></td>
    <td width='100%'><h2>Designation : Job Information</h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'><A href='index.php?module=Contacts&action=index&return_module=Contacts&return_action=DetailView&&print=true' class='utilsLink'></td>
  </tr>
</table>
<p>
<p> 
<table width="431" border="0" cellspacing="0" cellpadding="0" ><td width="177">
<form name="frmDesignations" method="post" action="./designations.php?pageID=<?=$_GET['pageID']?>&uniqcode=<?=$_GET['uniqcode']?>">
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
						    <td> <input type="text" readonly="true" name="txtDesignationID" value=<?=$_GET['id'] ?> >
						  </tr>
						  <tr> 
						    <td><strong>Description</strong></td>
						  	  <td> <textarea name='txtDesignationDesc' rows="3" disabled tabindex='3' cols="30"><?=$message[0][1]?></textarea>
						    </td>
						  </tr>
						  <tr>
						    <td><strong>Corporate Title</strong></td>
						    <td> <select disabled name="cmbCorpTit">
						    <?
						    $corptitles = $parent_designation->getCorpTit();
						
						    for($c=0;$corptitles && $c < count($corptitles); $c++)
						        if($message[0][2]==$corptitles[$c][0])
						           echo '<option selected value=' .$corptitles[$c][0]. '>'.$corptitles[$c][1].'</option>';
						        else
						           echo '<option value=' .$corptitles[$c][0]. '>'.$corptitles[$c][1].'</option>';
						    ?>
						  </tr>
						  <tr>
						    <td><strong>Senior Management</strong></td>
						    <td> <input type="checkbox" disabled name='chkSenMgmt' value="1"<?=($message[0][3]=='1'?'checked':'')?>>
						    </td>
						  </tr>
						  <tr>
						    <td><strong>Review Date</strong></td>
						    <td> <input type="text" readonly disabled name='txtRevDat' value=<?=$message[0][4]?>>&nbsp;<input type="button" class="button" value="..." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmDesignations.txtRevDat);return false;">
						    </td>
						  </tr>
						  <tr>
						    <td><strong>Next Upgrade Level</strong></td>
						    <td> <select disabled name="cmbDesNxtUpg">
							    		<option value="0">-Select Level-</option>
						    <?
						    $des = $parent_designation->getDes();
						
						    for($c=0;$des && $c < count($des); $c++)
						        if($message[0][5]==$des[$c][0])
						            echo '<option selected value=' .$des[$c][0]. '>'.$des[$c][1].'</option>';
						        else
						            echo '<option value=' .$des[$c][0]. '>'.$des[$c][1].'</option>';
						    ?>
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
<iframe width=174 height=189 name="gToday:normal:agenda.js" id="gToday:normal:agenda.js" src="scripts/ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; top:-500px; left:-500px;">
</iframe>
</body>
</html>
<? } ?>
