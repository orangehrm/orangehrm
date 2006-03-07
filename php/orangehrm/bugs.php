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

define('OpenSourceEIM', dirname(__FILE__));
require_once OpenSourceEIM . '/lib/Models/bugs/bugs.php';
require_once OpenSourceEIM . '/lib/Models/bugs/modules.php';
require_once OpenSourceEIM . '/lib/Confs/sysConf.php';

	$sysConst = new sysConf(); 

$new_bug = new Bugs();
$lastRecord = $new_bug->getLastRecord();
$bug_number = $new_bug->getNextNumber();
$date = $new_bug->getDate();


if ((isset($_POST['sqlState'])) && ($_POST['sqlState'] == 'NewRecord')) {
	$new_bug ->setBugId(trim($_POST['txtID']));
	$new_bug ->setBugNumber(trim($_POST['txtBugNumber']));
	$new_bug ->setDateEntered($date);
	$new_bug ->setCreatedBy(trim($_SESSION['user']));
	$new_bug ->setAssignedDeveloperId(trim($_POST['cmbDeveloper']));
	$new_bug ->setDescription(trim($_POST['txtDescription']));
	$new_bug ->setFoundInrelease(trim($_POST['cmbRelease']));
	$new_bug ->setModule(trim($_POST['cmbModule']));
	$new_bug ->setName(trim($_POST['txtName']));
	$new_bug ->setPriority(trim($_POST['cmbPriority']));
	$new_bug ->setResolution(trim($_POST['cmbResolution']));
	$new_bug ->setSource(trim($_POST['cmbSource']));
	$new_bug ->setStatus(trim($_POST['cmbStatus']));
	$new_bug ->setType(trim($_POST['cmbType']));
	$new_bug ->setWorkLog(trim($_POST['txtWorkLog']));
	
	$body = "Reported Date:".trim($_POST['txtEnteredDate'])."\n"."Name:".trim($_POST['txtName'])."\n"."Found in Release:".trim($_POST['cmbRelease']). "\n"."Source:".trim($_POST['cmbSource'])."\n"."Module:".trim($_POST['cmbModule'])."\n"."Type:".trim($_POST['cmbType'])."\n"."Status:".trim($_POST['cmbStatus'])."\n"."Priority:".trim($_POST['cmbPriority'])."\n". "Resolution:" .trim($_POST['cmbResolution']). "\n". "Description:".trim($_POST['txtDescription']). "\n"."WorkLog:".trim($_POST['txtWorkLog']);
	  					
	$message = $new_bug->addBugs();
	if ($message) { 
		
		$module= new Modules();
		
		$res=$module->filterModules($_POST['cmbModule']);
$to = $res[0][3];
$subject = "Report Bug";
$headers = 'From: webmaster@example.com' . "\r\n" .
   					'Reply-To: webmaster@example.com' . "\r\n" ;

		$emailSent = $new_bug->sendMail($to,$subject,$body,$headers);
		if($emailSent)
			$showMsg = "Addition%Successful, Email Sent";
		else 
			$showMsg = "Addition%Successful, Email Failure";

		$bugcode = $_GET['bugcode'];
		$pageID = $_POST['pageID'];
		header("Location: ./bugview.php?message=$showMsg&bugcode=$bugcode&pageID=$pageID");
			
	} else {
		
		$showMsg = "Addition Unsuccessful!";
		$bugcode = $_GET['bugcode'];
		$pageID = $_GET['pageid'];
		header("Location: ./bugs.php?message=$showMsg&captureState=AddMode");
	}	
}
else if((isset($_POST['sqlState'])) && ($_POST['sqlState'] == 'UpdateRecord')) {
	$new_bug ->setBugId(trim($_POST['txtID']));
	$new_bug ->setBugNumber(trim($_POST['txtBugNumber']));
	$new_bug ->setDateModified($date);
	$new_bug ->setAssignedDeveloperId(trim($_POST['cmbDeveloper']));
	$new_bug ->setDeleted(isset($_POST['chkDeleted'])?'1':'0');
	$new_bug ->setDescription(trim($_POST['txtDescription']));
	$new_bug ->setFixedInRelease(trim($_POST['cmbFixedRelease']));
	$new_bug ->setModifiedUserId(trim($_SESSION['user']));
	$new_bug ->setName(trim($_POST['txtName']));
	$new_bug ->setPriority(trim($_POST['cmbPriority']));
	$new_bug ->setResolution(trim($_POST['cmbResolution']));
	$new_bug ->setStatus(trim($_POST['cmbStatus']));
	$new_bug ->setWorkLog(trim($_POST['txtWorkLog']));
	$new_bug ->setModule(trim($_POST['txtModule']));
	$new_bug ->setSource(trim($_POST['txtSource']));
	$message=$new_bug->updateBugs();
	
	$body = "Modified Date:".trim($_POST['txtModifiedDate'])."\n"."Name:".trim($_POST['txtName'])."\n"."Fixed in Release:".trim($_POST['cmbFixedRelease']). "\n"."Status:".trim($_POST['cmbStatus'])."\n"."Priority:".trim($_POST['cmbPriority'])."\n". "Resolution:" .trim($_POST['cmbResolution']). "\n". "Description:".trim($_POST['txtDescription']). "\n"."WorkLog:".trim($_POST['txtWorkLog']);
			
	if ($message) { 
		
		
		$module= new Modules();
		
		$res=$module->filterModules($_POST['txtModule']);
$to = $res[0][3];
$subject = "Report Bug";
$headers = 'From: webmaster@example.com' . "\r\n" .
   					'Reply-To: webmaster@example.com' . "\r\n" ;

   		$emailSent = $new_bug->sendMail($to,$subject,$body,$headers);
		if($emailSent)
			$showMsg = "Updation%Successful, Email Sent";
		else 
			$showMsg = "Updation%Successful, Email Failure";
			
		$bugcode = $_GET['bugcode'];
		$pageID = $_POST['pageID'];
		header("Location: ./bugview.php?message=$showMsg&bugcode=$bugcode&pageID=$pageID");
		
	} else {
		
		$showMsg = "Updation%Unsuccessful!";
		$bugcode = $_GET['bugcode'];
		$pageID = $_GET['pageid'];
		header("Location: ./bugs.php?message=$showMsg&captureState=AddMode");		
	}
	
}
?>
<?
	if ((isset($_GET['capturemode'])) && ($_GET['capturemode'] == 'addmode')) {
?>
<html>
<head>
<title>Bugs-Add</title>
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
		
		if(document.frmBugs.cmbRelease.value=='0') {
			alert("Field should be selected");
			document.frmBugs.cmbRelease.focus();
			return;
		}
		
		if(document.frmBugs.cmbSource.value=='0') {
			alert("Field should be selected");
			document.frmBugs.cmbSource.focus();
			return;
		}
		
		
		if(document.frmBugs.cmbModule.value=='0') {
			alert("Field should be selected");
			document.frmBugs.cmbModulse.focus();
			return;
		}

		if(document.frmBugs.cmbStatus.value=='0') {
			alert("Field should be selected");
			document.frmBugs.cmbStatus.focus();
			return;
		}
		
		if(document.frmBugs.cmbType.value=='0') {
			alert("Field should be selected");
			document.frmBugs.cmbType.focus();
			return;
		}
		
		if(document.frmBugs.cmbPriority.value=='0') {
			alert("Field should be selected");
			document.frmBugs.cmbPriority.focus();
			return;
		}

		if (document.frmBugs.txtName.value == '') {
			alert ("Please specify a Bug Name");
			return false;
		}
		
		document.frmBugs.sqlState.value = "NewRecord";
		document.frmBugs.pageID.value = "<?=$_GET['pageID']?>";
		document.frmBugs.submit();		
	}	

	
</script>
<link href="./themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("./themes/beyondT/css/style.css"); </style>

</head>

<body>
<table width="100%" border="0">
  <tr>
    <td valign='top'> </td>
    <td width='100%'><h2>Report Bugs</h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'><A href='index.php?module=Contacts&action=index&return_module=Contacts&return_action=DetailView&&print=true' class='utilsLink'></td>

  </tr>
</table>
<p>

<p>
<table width="500" border="0" cellspacing="0" cellpadding="0" ><!--DWLayoutTable--><td width="177">
<form name="frmBugs" method="post" action="./bugs.php?pageid=<?=$_GET['pageID']?>&bugcode=<?=$_GET['bugcode']?>">
<input type="hidden" name="pageID" value="">
  <tr> 
        <td height="27" valign='top'> <p> <img title="Back" onmouseout="this.src='./themes/beyondT/pictures/btn_back.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_back_02.jpg';"  src="./themes/beyondT/pictures/btn_back.jpg" onclick="goBack();">
            <input type="hidden" name="sqlState" value="">
          </p></td>
    <td width="254" align='left' valign='bottom'> <font color="red" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
      </font> </td>
  </tr><td width="177"><form name="frmBugs" method="post" action="./bugs.php">
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
    <td width="30">Bug ID</td>
    <td><input type="text" name="txtID" readonly="true" value=<?=$lastRecord; ?>></td>
  </tr>
  <tr> 
    <td>Bug Number</td>
    <td><input type="text" name="txtBugNumber" readonly="true" value=<?=$bug_number; ?>> </td>
  </tr>
  <tr> 
     <td>Found in Release</td>
    <td><select name="cmbRelease">
    		<option value="0">--Select Release--</option>
     <?  $version = $new_bug->getAlias('version');
    	 for($c=0;$c < count($version);$c++)
            echo "<option value='" . $version[$c][0] . "'>" . $version[$c][1] ."</option>";
    ?></td>
  </tr>
  <tr> 
    <td>Source</td>
    
    <td><select name="cmbSource">
    		<option value="0">--Select Source--</option>
    <?  $source = $new_bug->getArrayValues('source');
    	 for($c=0;$c < count($source);$c++)
            echo "<option value='" . $source[$c] . "'>" . $source[$c] ."</option>";
    ?>
    </td>
<!--    <td>Assigned Developer</td>
    <td><select name="cmbDeveloper">
        		<option value="0">--Select Developer--</option>
    <?  $developer = $new_bug->getAlias('developer');
    	 for($c=0;$c < count($developer);$c++)
            echo "<option value='" . $developer[$c][0] . "'>" . $developer[$c][1] ."</option>";
    ?>
    </td> -->
  </tr>
  <tr> 
    <td>Module</td>
    <td><select name="cmbModule">
        		<option value="0">--Select Module--</option>
    <?  $module = $new_bug->getAlias('module');
    	 for($c=0;$c < count($module);$c++)
            echo "<option value='" . $module[$c][0] . "'>" . $module[$c][1] ."</option>";
    ?></td>
    <td>Status</td>
    <td><select name="cmbStatus">
        		<option value="0">--Select Status--</option>
    <?  $status = $new_bug->getArrayValues('status');
    	 for($c=0;$c < count($status);$c++)
            echo "<option value='" . $status[$c] . "'>" . $status[$c] ."</option>";
    ?>
    </td>
  
  </tr>
  <tr> 
   <td>Type</td>
    <td><select name="cmbType">
        		<option value="0">--Select Type--</option>
    <?  $type = $new_bug->getArrayValues('type');
    	 for($c=0;$c < count($type);$c++)
            echo "<option value='" . $type[$c] . "'>" . $type[$c] ."</option>";
    ?>
    </td>
    <td>Priority</td>
    <td><select name="cmbPriority">
        		<option value="0">--Select Priority--</option>
    <?  $priority = $new_bug->getArrayValues('priority');
    	 for($c=0;$c < count($priority);$c++)
            echo "<option value='" . $priority[$c] . "'>" . $priority[$c] ."</option>";
    ?></td>
  </tr>
  <tr> 
    <td>Name(50 character maximum)</td>
    <td><input type="text" name="txtName"></td> 
   
  </tr>
   <tr> 
    <td>Description</td>
    <td><textarea name='txtDescription' rows="3" cols="30"></textarea></td>
    <td>Work Log</td>
    <td><textarea name='txtWorkLog' rows="3" cols="30"></textarea></td>
   </tr>
  					  <tr><td></td><td align="right" width="100%"><img onClick="addSave();" onmouseout="this.src='./themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_save_02.jpg';" src="./themes/beyondT/pictures/btn_save.jpg">
        <img onClick="document.frmBugs.reset();" onmouseout="this.src='./themes/beyondT/pictures/btn_clear.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_clear_02.jpg';" src="./themes/beyondT/pictures/btn_clear.jpg"></td></tr>

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
	 $message = $new_bug->filterBugs($_GET['id']);
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
	
	var frm=document.frmBugs;
//  alert(frm.elements.length);
	for (var i=0; i < frm.elements.length; i++)
		frm.elements[i].disabled = false;
	document.Edit.src="./themes/beyondT/pictures/btn_save.jpg";
	document.Edit.title="Save";
}


	function addUpdate() {
		
		if (document.frmBugs.txtName.value == '') {
			alert ("Please specify a Bug Name");
			return false;
		}
		
		document.frmBugs.sqlState.value = "UpdateRecord";
		document.frmBugs.pageID.value = "<?=$_GET['pageID']?>";
		document.frmBugs.submit();
	}			
</script>
<link href="./themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("./themes/beyondT/css/style.css"); </style>
</head>
<body>
<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
  <tr>
    <td valign='top'> </td>
    <td width='100%'><h2>Report Bugs: Update</h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'><A href='index.php?module=Contacts&action=index&return_module=Contacts&return_action=DetailView&&print=true' class='utilsLink'></td>
  </tr>
</table>
<p>
<p> 
<table width="431" border="0" cellspacing="0" cellpadding="0" ><td width="177">
<form name="frmBugs" method="post" action="./bugs.php?pageid=<?=$_GET['pageID']?>&bugcode=<?=$_GET['bugcode']?>">
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
  </tr><td width="177"><form name="frmBugs" method="post" action="./bugs.php">
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
    <td>Bug ID</td>
    <td><input type="text" name="txtID" readonly="true" value=<?=$_GET['id'] ?>></td>
<!--    <td>Assigned Developer</td>
    <td><select name="cmbDeveloper" disabled>
	 <?  $developer = $new_bug->getAlias('developer');

        for($c=0;$c < count($developer);$c++){
            if($developer[$c][0]==$message[0][3])
               echo "<option selected value='" . $developer[$c][0] . "'>" . $developer[$c][1] ."</option>";
           else
                 echo "<option value='" . $developer[$c][0] . "'>" . $developer[$c][1] ."</option>";
        }
    ?>
    
   </td> -->
  </tr>
  <tr> 
    <td>Bug Number</td>
    <td><input type="text" name="txtBugNumber" disabled readonly="true" value="<?=$message[0][1]?>"> </td>
    <td>Entered Date</td>
    <td><input type="text" name="txtDate" disabled readonly="true" value=<?= $message[0][16]?>> </td>
  </tr>
  <tr> 
   <td>Name</td>
    <td><input type="hidden" name="txtName" value=<?=$message[0][9]?>>
    <strong><?=$message[0][9]?></strong></td>
    
    <td>Created By</td>
    <td><strong>
    <?  $user = $new_bug->getAlias('user');
    for($c=0;$c < count($user);$c++){
            if($user[$c][0]==$message[0][17])
               echo $user[$c][1];
    
    }?> </strong></td>
  </tr>
  <tr> 
    <td>Status</td>
    <td><select name="cmbStatus" disabled>
    <?  $status = $new_bug->getArrayValues('status');
    	  for($c=0;$c < count($status);$c++){
            if($status[$c]==$message[0][13])//3
               echo "<option selected value='" . $status[$c] . "'>" . $status[$c] ."</option>";
           else
                 echo "<option value='" . $status[$c] . "'>" . $status[$c] ."</option>";
        }
    ?></td>
    <td>Source</td>
    <td><input type="hidden" name="txtSource" value="<?=$message[0][12]?>"><strong><?=$message[0][12]?></strong></td>
  </tr>
  <tr> 
    <td>Resolution</td>
    <td><select name="cmbResolution" disabled>
    <?  $resolution = $new_bug->getArrayValues('resolution');
    	  for($c=0;$c < count($resolution);$c++){
            if($resolution[$c]==$message[0][11])
               echo "<option selected value='" . $resolution[$c] . "'>" . $resolution[$c] ."</option>";
           else
                 echo "<option value='" . $resolution[$c] . "'>" . $resolution[$c] ."</option>";
        }
    ?></td>
    <td>Found in Release</td>
    <td><strong> <?  $ver = $new_bug->getAlias('version');
    for($c=0;$c < count($ver);$c++){
            if($ver[$c][0]==$message[0][18])
               echo $ver[$c][1];
    
    }?></strong></td>
  </tr>
  <tr> 
    <td>Priority</td>
    <td><select name="cmbPriority" disabled>
    <?  $priority = $new_bug->getArrayValues('priority');
    	  for($c=0;$c < count($priority);$c++){
            if($priority[$c]==$message[0][10])//2
               echo "<option selected value='" . $priority[$c] . "'>" . $priority[$c] ."</option>";
           else
                 echo "<option value='" . $priority[$c] . "'>" . $priority[$c] ."</option>";
        }
    ?>
    </td>  
    <td>Module</td>
    <td><strong><?  $mod = $new_bug->getAlias('module');
    for($c=0;$c < count($mod);$c++){
            if($mod[$c][0]==$message[0][8])
            {
               echo $mod[$c][1];
               echo "<input type='hidden' name='txtModule' value='" . $mod[$c][0] . "'>";                  
            }
    }?></strong></td>
  </tr>
  <tr> 
  <td>Deleted</td>
    <td><input type="checkbox" disabled name="chkDeleted" <?=($message[0][4]=='1'?'checked':'')?> value="1"></td>
    <td>Fixed in Release</td>
    <td><select disabled name="cmbFixedRelease"> 
    <?  $fixedRelease = $new_bug->getAlias('version');

        for($c=0;$c < count($fixedRelease);$c++){
            if($fixedRelease[$c][0]==$message[0][6])
               echo "<option selected value='" . $fixedRelease[$c][0] . "'>" . $fixedRelease[$c][1] ."</option>";
           else
                 echo "<option value='" . $fixedRelease[$c][0] . "'>" . $fixedRelease[$c][1] ."</option>";
        }
    ?></td>
   
  
    </tr>
   <tr> 
    <td>Description</td>
    <td><textarea name='txtDescription' disabled rows="3" cols="30"><?=$message[0][5]?></textarea></td>
    <td>Work Log</td>
    <td><textarea name='txtWorkLog' disabled rows="3" cols="30"><?=$message[0][15]?></textarea> </td>
  </tr>
   <tr> 
    <td></td>
  </tr>
					  <tr><td></td><td align="right" width="100%">
			        <img src="./themes/beyondT/pictures/btn_edit.jpg" title="Edit" onmouseout="mout();" onmouseover="mover();" name="Edit" onClick="edit();">
					  <img src="./themes/beyondT/pictures/btn_clear.jpg" onmouseout="this.src='./themes/beyondT/pictures/btn_clear.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_clear_02.jpg';" onClick="document.frmBugs.reset();" >
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
</body>
</html>
<? } ?>
