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

require_once ROOT_PATH . '/lib/confs/sysConf.php';

	$sysConst = new sysConf(); 
	$locRights=$_SESSION['localRights'];

	if ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'addmode')) {
?>
<html>
<head>
<title>Versions- Add New</title>
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
		location.href = "./CentralController.php?mtcode=<?=$this->getArr['mtcode']?>&VIEW=MAIN";
	}

	function addSave() {
		
		if (document.frmVersions.txtName.value == '') {
			alert ("Please specify a Version Name");
			return false;
		}
		
		document.frmVersions.sqlState.value = "NewRecord";
		document.frmVersions.submit();		
	}	

	
</script>
<link href="../../themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("../../themes/beyondT/css/style.css"); </style>

</head>

<body>
<table width="100%" border="0">
  <tr>
    <td valign='top'> </td>
    <td width='100%'><h2>Versions</h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>

  </tr>
</table>
<p>

<p>
<table width="500" border="0" cellspacing="0" cellpadding="0" ><!--DWLayoutTable--><td width="177">
<form name="frmVersions" method="post" action="<?=$_SERVER['PHP_SELF']?>?mtcode=<?=$this->getArr['mtcode']?>">

  <tr> 
        <td height="27" valign='top'> <p> <img title="Back" onmouseout="this.src='../../themes/beyondT/pictures/btn_back.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_back_02.jpg';"  src="../../themes/beyondT/pictures/btn_back.jpg" onclick="goBack();">
            <input type="hidden" name="sqlState" value="">
          </p></td>
    <td width="254" align='left' valign='bottom'> <font color="red" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
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
       							<td>Version ID</td>
							    <td><strong><?=$this->popArr['newID'] ?></strong></td>
							  </tr>
   <tr> 
    <td>Name</td>
    <td><input type="text" name="txtName"></td>  
  </tr>
  <tr>
  <td>DB Version</td>
  <td><select name="cmbdbVersion">
    <?  
    	$db = $this -> popArr['db'];
    	  	 for($c=0;$c < count($db);$c++)
            echo "<option value='" . $db[$c][0] . "'>" . $db[$c][1] ."</option>";
    ?>
    </td>
  </tr>
  <tr>
  <td>File Version</td>
  <td><select name="cmbFileVersion">
    <?  
    	$file = $this -> popArr['file'];
    	 for($c=0;$c < count($file);$c++)
            echo "<option value='" . $file[$c][0] . "'>" . $file[$c][1] ."</option>";
    ?>
    </td>
  </tr>
   <tr> 
    <td>Description</td>
    <td><textarea name='txtDescription' rows="3" cols="30"></textarea></td>
  </tr>
  					  <tr><td></td><td align="right" width="100%"><img onClick="addSave();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
        <img onClick="document.frmVersions.reset();" onmouseout="this.src='../../themes/beyondT/pictures/btn_clear.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_clear_02.jpg';" src="../../themes/beyondT/pictures/btn_clear.jpg"></td></tr>
   
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
<? } else if ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'updatemode')) {
	 $message = $this->popArr['editArr'];
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Versions-Update</title>
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
		location.href = "./CentralController.php?mtcode=<?=$this->getArr['mtcode']?>&VIEW=MAIN";
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
	
	var frm=document.frmVersions;
//  alert(frm.elements.length);
	for (var i=0; i < frm.elements.length; i++)
		frm.elements[i].disabled = false;
	document.Edit.src="../../themes/beyondT/pictures/btn_save.jpg";
	document.Edit.title="Save";
}

	function addUpdate() {
		
		if (document.frmVersions.txtName.value == '') {
			alert ("Please specify a Verion Name");
			return false;
		}
		
		document.frmVersions.sqlState.value = "UpdateRecord";
		document.frmVersions.submit();
	}			
</script>
<link href="../../themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("../../themes/beyondT/css/style.css"); </style>
</head>
<body>
<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
  <tr>
    <td valign='top'></td>
    <td width='100%'><h2>Versions-Update</h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>
</table>
<p>
<p> 
<table width="431" border="0" cellspacing="0" cellpadding="0" ><td width="177">
<form name="frmVersions" method="post" action="<?=$_SERVER['PHP_SELF']?>?id=<?=$this->getArr['mtcode']?>&mtcode=<?=$this->getArr['mtcode']?>&capturemode=updatemode">
<input type="hidden" name="pageID" value="">
  <tr> 
    <td height="27" valign='top'> <p> <img title="Back" onmouseout="this.src='../../themes/beyondT/pictures/btn_back.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_back_02.jpg';" src="../../themes/beyondT/pictures/btn_back.jpg" onclick="goBack();">
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
                  <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="339" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="11"><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="12" border="0" alt=""></td>
                </tr>
                <tr>
                  <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><table border="0" cellpadding="5" cellspacing="0" class="">
 <tr> 
    <td>Version ID</td>
     <td> <input type="hidden" name="txtID" value=<?=$message[0][0]?> ><strong><?=$message[0][0]?></strong> </td>
  </tr>
   <tr> 
    <td>Name</td>
    <td><input type="text" disabled  name="txtName" value=<?=$message[0][1]?>></td>  
  </tr>
  <tr>
  <td>DB Version</td>
  <td><select name="cmbdbVersion" disabled>
      <?  $db = $this->popArr[db];
        for($c=0;$c < count($db);$c++){
            if($db[$c][0]==$message[0][6])
               echo "<option selected value='" . $db[$c][0] . "'>" . $db[$c][1] ."</option>";
           else
                 echo "<option value='" . $db[$c][0] . "'>" . $db[$c][1] ."</option>";
        }
    ?></td>
  </tr>
  <tr>
  <td>File Version</td>
  <td><select name="cmbFileVersion" disabled>
     <?  $file = $this -> popArr['file'];
        for($c=0;$c < count($file);$c++){
            if($file[$c][0]==$message[0][7])
               echo "<option selected value='" . $file[$c][0] . "'>" . $file[$c][1] ."</option>";
           else
                 echo "<option value='" . $file[$c][0] . "'>" . $file[$c][1] ."</option>";
        }
    ?></td>
  </tr>
   <tr> 
  <td>Deleted</td>
    <td><input type="checkbox" disabled name="chkDeleted" <?=($message[0][8]=='1'?'checked':'')?> value="1"></td>
    </tr>
   <tr> 
   <td>Description</td>
    <td><textarea name='txtDescription' disabled rows="3" cols="30"><?=$message[0][9]?></textarea>
    
  </tr>
					  <tr><td></td><td align="right" width="100%">
<?			if($locRights['edit']) { ?>
			        <img src="../../themes/beyondT/pictures/btn_edit.jpg" title="Edit" onmouseout="mout();" onmouseover="mover();" name="Edit" onClick="edit();">
<?			} else { ?>
			        <img src="../../themes/beyondT/pictures/btn_edit.jpg" onClick="alert('<?=$sysConst->accessDenied?>');">
<?			}  ?>
					  <img src="../../themes/beyondT/pictures/btn_clear.jpg" onmouseout="this.src='../../themes/beyondT/pictures/btn_clear.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_clear_02.jpg';" onClick="document.frmVersions.reset();" >
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
<? } ?>
