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

	$locRights=$_SESSION['localRights'];
	$sysConst = new sysConf(); 
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Untitled Document</title>

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

function edit(frm)
{

//  alert(frm.elements.length);
	for (var i=0; i < frm.elements.length; i++)
		frm.elements[i].disabled = false;
}

function addRAT()
{
  if(document.frmRatGrd.txtRatName.value =='') {
  	alert("Description Error!");
	return;
  }
  	
  var txt =document.frmRatGrd.txtRatMin;
  var min=eval(txt.value);
  if(!numeric(txt)) {
  	alert("Enter a numeric value!");
	txt.focus();
	return;
  }

  var txt =document.frmRatGrd.txtRatMax;
  var max=eval(txt.value);
  if(!numeric(txt)) {
  	alert("Enter a numeric value!");
	txt.focus();
	return;
  }

  var txt =document.frmRatGrd.txtRatAvg;
  var avg=eval(txt.value);
  if(!numeric(txt)) {
  	alert("Enter a numeric value!");
	txt.focus();
	return;
  }
  
  if(min>max) {
  	alert("Minimum Value should be less than Maximium Value!");
	return;
  }

  if(avg<min ||avg>max) {
  	alert("Avg Value should be in between Minimum/Maximium Value!");
	return;
  }
  
  document.frmRatGrd.STAT.value="ADD";
  document.frmRatGrd.submit();
}

	function goBack() {
		location.href = "./CentralController.php?uniqcode=RTM&id=<?=$this->getArr['id']?>&capturemode=updatemode";
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
		editRAT();
		return;
	}
	
	var frm=document.frmRatGrd;
//  alert(frm.elements.length);
	for (var i=0; i < frm.elements.length; i++)
		frm.elements[i].disabled = false;
	document.Edit.src="../../themes/beyondT/pictures/btn_save.jpg";
	document.Edit.title="Save";
}

function editRAT()
{
  if(document.frmRatGrd.txtRatName.value =='') {
  	alert("Description Error!");
	return;
  }
  	
  var txt =document.frmRatGrd.txtRatMin;
  var min=eval(txt.value);
  if(!numeric(txt)) {
  	alert("Enter a numeric value!");
	txt.focus();
	return;
  }

  var txt =document.frmRatGrd.txtRatMax;
  var max=eval(txt.value);
  if(!numeric(txt)) {
  	alert("Enter a numeric value!");
	txt.focus();
	return;
  }

  var txt =document.frmRatGrd.txtRatAvg;
  var avg=eval(txt.value);
  if(!numeric(txt)) {
  	alert("Enter a numeric value!");
	txt.focus();
	return;
  }
  
  if(min>max) {
  	alert("Minimum Value should be less than Maximium Value!");
	return;
  }

  if(avg<min ||avg>max) {
  	alert("Avg Value should be in between Minimum/Maximium Value!");
	return;
  }

  document.frmRatGrd.STAT.value="EDIT";
  document.frmRatGrd.submit();
}

function delRAT()
{
      var check = 0;
		with (document.frmRatGrd) {
			for (var i=0; i < elements.length; i++) {
				if ((elements[i].type == 'checkbox') && (elements[i].checked == true)){
					check = 1;
				}
			}
        }

        if(check==0)
            {
              alert("Select atleast one check box");
              return;
            }


    //alert(cntrl.value);
    document.frmRatGrd.STAT.value="DEL";
    document.frmRatGrd.submit();
}

</script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<link href="../../themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("../../themes/beyondT/css/style.css"); </style>
</head>
<body>
<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
  <tr>
    <td valign='top'>&nbsp; </td>
    <td width='100%'><h2>Rating Grades: Rating Types</h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>
</table>
<p>
<p>
<table width="431" border="0" cellspacing="0" cellpadding="0" ><td width="177">
<form name="frmRatGrd" method="post" action="<?=$_SERVER['PHP_SELF']?>?uniqcode=<?=$this->getArr['uniqcode']?>&id=<?=$this->getArr['id']?>">
  <tr>
    <td height="27" valign='top'> <p> <img title="Back" onmouseout="this.src='../../themes/beyondT/pictures/btn_back.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_back_02.jpg';" src="../../themes/beyondT/pictures/btn_back.jpg" onclick="goBack();">
        <input type="hidden" name="STAT" value="">
      </p></td>
    <td width="254" align='left' valign='bottom'> <font color="red" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;
      </font> </td>
  </tr><td width="177">
</table>
<?
$ratDet = $this->popArr['ratDet'];
?>
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
						    <td>Rating Type ID</td>
						  	  <td><strong><?=$ratDet[0][0]?></strong></td>
						  </tr>
						  <tr>
						    <td>Rating Type</td>
						  	<td><strong><?=$ratDet[0][1]?></strong></td>
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

<?
if(isset($this->popArr['editArr']))
{
	$edit = $this->popArr['editArr'];
?>
<br><br>
         <input type="hidden" name="txtRatGrdID"  value="<?=$this->getArr['editID']?>">
         <input type="hidden" name="txtRatID" value="<?=$this->getArr['id']?>">
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
							         <td>Name</td>
							         <td><input type="text" disabled name="txtRatName" value="<?=$edit[0][2]?>"></td>
							</tr>
							<tr>
							         <td>Min Mark</td>
							         <td><input type="text" disabled name="txtRatMin" value="<?=$edit[0][3]?>"></td>
							</tr>
							<tr>
							         <td>Max Mark</td>
							         <td><input type="text" disabled name="txtRatMax" value="<?=$edit[0][4]?>"></td>
							</tr>
							<tr>
							         <td>Avg Mark</td>
							         <td><input type="text" disabled name="txtRatAvg" value="<?=$edit[0][5]?>"></td>
							</tr>
					  <tr><td></td><td align="right" width="100%">
<?			if($locRights['edit']) { ?>
			        <img src="../../themes/beyondT/pictures/btn_edit.jpg" title="Edit" onmouseout="mout();" onmouseover="mover();" name="Edit" onClick="edit();">
<?			} else { ?>
			        <img src="../../themes/beyondT/pictures/btn_edit.jpg" onClick="alert('<?=$sysConst->accessDenied?>');">
<?			}  ?>
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
<? } else { ?>
<br><br>
         <input type="hidden" name="txtRatGrdID" value="<?=$this->popArr['newID']?>">
         <input type="hidden" name="txtRatID" value="<?=$this->getArr['id']?>">

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
						         <td>Name</td>
						         <td><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtRatName">
						</tr>
						<tr>
						         <td>Min Mark</td>
						         <td><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtRatMin">
						</tr>
						<tr>
						         <td>Max Mark</td>
						         <td><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtRatMax">
						</tr>
						<tr>
						         <td>Avg Mark</td>
						         <td><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtRatAvg">
						</tr>
					  <tr><td></td><td align="right" width="100%">
<?			if($locRights['add']) { ?>
					  <img onClick="addRAT();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
<?			} else { ?>
			        <img src="../../themes/beyondT/pictures/btn_save.jpg" onClick="alert('<?=$sysConst->accessDenied?>');">
<?			}  ?>
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

<? } ?>

<table width='100%' cellpadding='0' cellspacing='0' border='0'>
  <tr>
    <td valign='top'>&nbsp; </td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>

  <tr>

    <td width='100%'><h3>Assigned Rating Grades </h3></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'><A href='index.php?module=Contacts&action=index&return_module=Contacts&return_action=DetailView&&print=true' class='utilsLink'></td>
  </tr>
<tr><td>
<?	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delRAT();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} ?>
</td></tr>
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
							         <td></td>
							         <td><strong>Grade ID</strong></td>
							         <td><strong>Name</strong></td>
							         <td><strong>Min</strong></td>
							         <td><strong>Max</strong></td>
							</tr>
							<?
							$rset = $this->popArr['ratGrdAss'];
							
							    for($c=0;$rset && $c < count($rset); $c++)
							        {
							        echo '<tr>';
							            echo "<td><input type='checkbox' class='checkbox' name='chkdel[]' value='" . $rset[$c][1] ."'></td>";
							            echo "<td><a href='" .$_SERVER['PHP_SELF']. "?uniqcode=" . $this->getArr['uniqcode'] . "&id=" . $this->getArr['id']. "&editID=" . $rset[$c][1] . "'>" . $rset[$c][1] . "</a></td>";
							            echo '<td>' . $rset[$c][2] .'</td>';
							            echo '<td>' . $rset[$c][3] .'</td>';
							            echo '<td>' . $rset[$c][4] .'</td>';
							        echo '</tr>';
							        }
							?>
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
