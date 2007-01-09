<?php
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

require_once ROOT_PATH . '/lib/confs/sysConf.php';

	$sysConst = new sysConf(); 


if ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'addmode')) {
?>
<html>
<head>
<title>Bugs-Add</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
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
		location.href = "./CentralController.php?mtcode=<?php echo $this->getArr['mtcode']?>&VIEW=MAIN";
	}

	function addSave() {
		
		
		if(document.frmBugs.category_id.value=='100') {
			alert("Field should be selected");
			document.frmBugs.cmbSource.focus();
			return;
		}
		
		
		if(document.frmBugs.cmbModule.value=='0') {
			alert("Field should be selected");
			document.frmBugs.cmbModulse.focus();
			return;
		}


		if (document.frmBugs.summary.value == '') {
			alert ("Please specify a Bug Name");
			return false;
		}
		
		document.frmBugs.sqlState.value = "NewRecord";
		document.frmBugs.submit();		
	}	

	
</script>
<link href="../../themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("../../themes/beyondT/css/style.css"); </style>

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
<form name="frmBugs" method="post" action="<?php echo $_SERVER['PHP_SELF']?>?mtcode=<?php echo $this->getArr['mtcode']?>">
  <tr> 
        <td height="27" valign='top'> <p> <!--<img title="Back" onmouseout="this.src='../../themes/beyondT/pictures/btn_back.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_back_02.jpg';"  src="../../themes/beyondT/pictures/btn_back.jpg" onclick="goBack();">-->
            <input type="hidden" name="sqlState" value="">
          </p></td>
    <td width="254" align='left' valign='bottom'> <font color="red" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
          <?php
		if (isset($this->getArr['message'])) {
			$expString  = $this->getArr['message'];
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

<label>

              <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="339" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="11"><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="12" border="0" alt=""></td>
                </tr>
                <tr>
                  <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><table border="0" cellpadding="5" cellspacing="0" class="">
                    
  <tr> 
     <td>Found in Release</td>
    <td><strong>v2.1_alpha_1</strong><input type="hidden" readonly name="artifact_group_id" value="678995"></td>
  </tr>
  <tr> 
    <td>Category</td>
    
    <td><select name="category_id">
		<OPTION VALUE="100">None</OPTION>
				<OPTION VALUE="803416">Interface</OPTION>
				<OPTION VALUE="813016">PHP</OPTION>
				<OPTION VALUE="813015">Database</OPTION>
				<OPTION VALUE="864255">Language Pack</OPTION>
				<OPTION VALUE="883366">Web-Installer</OPTION>
    </select></td>
  </tr>
  <tr> 
    <td>Module</td>
    <td><select name="cmbModule">
        		<option value="0">--Select Module--</option>
    <?php  $module = $this->popArr['module'];
    	 for($c=0;$c < count($module);$c++)
            echo "<option>" . $module[$c][1] ."</option>";
    ?></td>
  </tr>
  <tr> 
    <td>Priority</td>
    <td><select name="priority">
<option value="1">1 - Lowest</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5" selected="selected">5 - Medium</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9 - Highest</option></select>
</td>
  </tr>
  <tr> 
    <td>Summary</td>
    <td><input type="text" name="summary"></td> 
    <td>Your Email</td>
    <td><input type="text" name="txtEmail" value="<?php echo isset($_POST['txtEmail']) ? $_POST['txtEmail'] : ''?>"></td> 
   
  </tr>
   <tr> 
    <td>Description</td>
    <td><textarea name='txtDescription' rows="3" cols="30"></textarea></td>
   </tr>
  					  <tr><td></td><td align="right" width="100%"><img onClick="addSave();" onMouseOut="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
        <img onClick="document.frmBugs.reset();" onMouseOut="this.src='../../themes/beyondT/pictures/btn_clear.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_clear_02.jpg';" src="../../themes/beyondT/pictures/btn_clear.jpg"></td></tr>

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

</label>
</form>
<p> 
<p> 
</body>
</html>
<?php } else if ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'updatemode')) {
	 $message = $this->popArr['editArr'];
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

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
		location.href = "./CentralController.php?mtcode=<?php echo $this->getArr['mtcode']?>&VIEW=MAIN";
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
	document.Edit.src="../../themes/beyondT/pictures/btn_save.jpg";
	document.Edit.title="Save";
}


	function addUpdate() {
		
		if (document.frmBugs.txtName.value == '') {
			alert ("Please specify a Bug Name");
			return false;
		}
		
		document.frmBugs.sqlState.value = "UpdateRecord";
		document.frmBugs.submit();
	}			
</script>
<link href="../../themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("../../themes/beyondT/css/style.css"); </style>
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
<form name="frmBugs" method="post" action="<?php echo $_SERVER['PHP_SELF']?>?id=<?php echo $this->getArr['id']?>&mtcode=<?php echo $this->getArr['mtcode']?>">
  <tr> 
    <td height="27" valign='top'> <p> <img title="Back" onMouseOut="this.src='../../themes/beyondT/pictures/btn_back.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_back_02.jpg';" src="../../themes/beyondT/pictures/btn_back.jpg" onClick="goBack();">
        <input type="hidden" name="sqlState" value="">
      </p></td>
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
                  <td><table border="0" cellpadding="5" cellspacing="0" class="">
			<tr> 
    <td>Bug ID</td>
    <td><input type="hidden" name="txtID" value=<?php echo $message[0][0]?>><strong><?php echo $message[0][0]?></strong></td>
  </tr>
  <tr> 
    <td>Bug Number</td>
    <td><input type="hidden" name="txtBugNumber" value="<?php echo $message[0][1]?>"><strong><?php echo $message[0][1]?></strong></td>
    <td>Entered Date</td> 
    
    <?php
    $arr = explode(" ",$message[0][16]);
    ?>
    <td><input type="hidden" name="txtDate" value=<?php echo $message[0][16]?>><strong><?php echo $arr[0]?></strong></td>
  </tr>
  <tr> 
   <td>Name</td>
    <td><input type="hidden" name="txtName" value=<?php echo $message[0][9]?>>
    <strong><?php echo $message[0][9]?></strong></td>
    
    <td>Created By</td>
    <td><strong>
    <?php  $user = $this->popArr['user'];
    for($c=0;$c < count($user);$c++){
            if($user[$c][0]==$message[0][17])
               echo $user[$c][1];
    
    }?> </strong></td>
  </tr>
  <tr> 
    <td>Status</td>
    <td><select name="cmbStatus" disabled>
    <?php  $status = $this->popArr['status'];
    	  for($c=0;$c < count($status);$c++){
            if($status[$c]==$message[0][13])
               echo "<option selected value='" . $status[$c] . "'>" . $status[$c] ."</option>";
           else
                 echo "<option value='" . $status[$c] . "'>" . $status[$c] ."</option>";
        }
    ?></td>
    <td>Source</td>
    <td><input type="hidden" name="txtSource" value="<?php echo $message[0][12]?>"><strong><?php echo $message[0][12]?></strong></td>
  </tr>
  <tr> 
    <td>Resolution</td>
    <td><select name="cmbResolution" disabled>
    <?php  $resolution = $this->popArr['resolution'];
    	  for($c=0;$c < count($resolution);$c++){
            if($resolution[$c]==$message[0][11])
               echo "<option selected value='" . $resolution[$c] . "'>" . $resolution[$c] ."</option>";
           else
                 echo "<option value='" . $resolution[$c] . "'>" . $resolution[$c] ."</option>";
        }
    ?></td>
    <td>Found in Release</td>
    <td><strong> <?php  $ver = $this->popArr['version'];
    for($c=0;$c < count($ver);$c++){
            if($ver[$c][0]==$message[0][18])
               echo $ver[$c][1];
    
    }?></strong></td>
  </tr>
  <tr> 
    <td>Priority</td>
    <td><select name="cmbPriority" disabled>
    <?php  $priority = $this->popArr['priority'];
    	  for($c=0;$c < count($priority);$c++){
            if($priority[$c]==$message[0][10])
               echo "<option selected value='" . $priority[$c] . "'>" . $priority[$c] ."</option>";
           else
                 echo "<option value='" . $priority[$c] . "'>" . $priority[$c] ."</option>";
        }
    ?>
    </td>  
    <td>Module</td>
    <td><strong><?php  $mod = $this->popArr['module'];
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
    <td><input type="checkbox" disabled name="chkDeleted" <?php echo ($message[0][4]=='1'?'checked':'')?> value="1"></td>
    <td>Fixed in Release</td>
    <td><select disabled name="cmbFixedRelease"> 
    <?php  $fixedRelease = $this->popArr['version'];

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
    <td><textarea name='txtDescription' disabled rows="3" cols="30"><?php echo $message[0][5]?></textarea></td>
    <td>Work Log</td>
    <td><textarea name='txtWorkLog' disabled rows="3" cols="30"><?php echo $message[0][15]?></textarea> </td>
  </tr>
   <tr> 
    <td></td>
  </tr>
					  <tr><td></td><td align="right" width="100%">
			        <img src="./../../themes/beyondT/pictures/btn_edit.jpg" title="Edit" onmouseout="mout();" onmouseover="mover();" name="Edit" onClick="edit();">
					  <img src="./../../themes/beyondT/pictures/btn_clear.jpg" onmouseout="this.src='./../../themes/beyondT/pictures/btn_clear.jpg';" onmouseover="this.src='./../../themes/beyondT/pictures/btn_clear_02.jpg';" onClick="document.frmBugs.reset();" >
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
</body>
</html>
<?php } ?>
