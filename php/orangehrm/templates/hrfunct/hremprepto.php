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

	$arrRepType = array ('Supervisor','Subordinate');
	$arrRepMethod = array ('Direct' => 1,'Indirect' => 2);
	
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
    if(code>=48 && code<=57 || code==46)
	   flag=true;
	else
	   {
	   flag=false;
	   break;
	   }
	}
return flag;
}

function edit()
{
	if(document.Edit.title=='Save') {
		editEXT();
		return;
	}
	
	var frm=document.frmEmpRepTo;
	for (var i=0; i < frm.elements.length; i++)
		frm.elements[i].disabled = false;
	document.Edit.src="../../themes/beyondT/pictures/btn_save.jpg";
	document.Edit.title="Save";
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

function goBack() {
		location.href = "./CentralController.php?reqcode=<?=$this->getArr['reqcode']?>&VIEW=MAIN";
		
	}

function returnEmpDetail(){
		var popup=window.open('../../templates/hrfunct/emppop.php?reqcode=REP','Employees','height=450,width=400');
        if(!popup.opener) popup.opener=self;	
}

function addEXT()
{
	if(document.frmEmpRepTo.cmbRepType.value=='0') {
		alert("Field should be selected");
		document.frmEmpRepTo.cmbRepType.focus();
		return;
	}
	
	if(document.frmEmpRepTo.txtRepEmpID.value=='') {
		alert("Field should be selected");
		document.frmEmpRepTo.txtRepEmpID.focus();
		return;
	}

	if(document.frmEmpRepTo.cmbRepMethod.value=='0') {
		alert("Field should be selected");
		document.frmEmpRepTo.cmbRepMethod.focus();
		return;
	}

	if(document.frmEmpRepTo.cmbRepType.value == 'Supervisor') {	
		
	    document.frmEmpRepTo.txtSubEmpID.value = document.frmEmpRepTo.txtEmpID.value;
		document.frmEmpRepTo.txtSupEmpID.value = document.frmEmpRepTo.txtRepEmpID.value;
		
	} 
	
	if(document.frmEmpRepTo.cmbRepType.value == 'Subordinate') {
		document.frmEmpRepTo.txtSupEmpID.value = document.frmEmpRepTo.txtEmpID.value;
		document.frmEmpRepTo.txtSubEmpID.value = document.frmEmpRepTo.txtRepEmpID.value;
		
	}

  document.frmEmpRepTo.STAT.value="ADD";
  document.frmEmpRepTo.submit();
	
}

function editEXT()
    { 
	 document.frmEmpRepTo.STAT.value="EDIT";
  	 document.frmEmpRepTo.submit();	
	
	}

function delSupEXT()
{
      var check = 0;
		with (document.frmEmpRepTo) {
			for (var i=0; i < elements.length; i++) {
				if ((elements[i].name == 'chksupdel[]') && (elements[i].checked == true)){
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
    document.frmEmpRepTo.delSupSub.value='sup';
    document.frmEmpRepTo.STAT.value="DEL";
    document.frmEmpRepTo.submit();
}

function delSubEXT()
{
      var check = 0;
		with (document.frmEmpRepTo) {
			for (var i=0; i < elements.length; i++) {
				if ((elements[i].name == 'chksubdel[]') && (elements[i].checked == true)){
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
    document.frmEmpRepTo.delSupSub.value='sub';
    document.frmEmpRepTo.STAT.value="DEL";
    document.frmEmpRepTo.submit();
}

</script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<link href="../../themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("../../themes/beyondT/css/style1.css"); </style>
</head>
<body>
<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
  <tr>
    <td valign='top'>&nbsp; </td>
    <td width='100%'><h2><?=$heading?></h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>
</table>
<p>
<p>
<table width="431" border="0" cellspacing="0" cellpadding="0" ><td width="177">
<form name="frmEmpRepTo" method="post" action="<?=$_SERVER['PHP_SELF']?>?reqcode=<?=$this->getArr['reqcode']?>&id=<?=$this->getArr['id']?>">

  <tr>
    <td height="27" valign='top'> <p> <img title="Back" onmouseout="this.src='../../themes/beyondT/pictures/btn_back.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_back_02.jpg';"  src="../../themes/beyondT/pictures/btn_back.jpg" onclick="goBack();">
        <input type="hidden" name="STAT" value="">
      </p></td>
    <td width="254" align='left' valign='bottom'> <font color="red" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;
      </font> </td>
  </tr><td width="177">
</table>
<? 
  $empdet = $this -> popArr['empdet'];
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
                      <td><?=$employeeid?></td>
    				  <td width="75"><font color="#204242"><strong><?=$empdet[0][0]?></strong></font></td>
    				  <td width="50">&nbsp;</td>
					  <td><?=$lastname?></td>
						<td width="300"><font color="#204242"><strong><?=$empdet[0][1]?></strong></font></td>
					</tr>
					  <tr> 
						<td><?=$firstname?></td>
						<td><font color="#204242"><strong><?=$empdet[0][2]?></strong></font></td>
    				  <td width="50">&nbsp;</td>
						<td><?=$nickname?></td>
						<td><font color="#204242"><strong><?=$empdet[0][3]?></</font></td>
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

<br><br>			
<?			
if(isset($this->getArr['editIDSup']))
{	
?>
     <input type="hidden" name="txtSupEmpID" value="<?=$this->getArr['editIDSup']?>">
     <input type="hidden" name="txtSubEmpID" value="<?=$this->getArr['id']?>">
     <input type="hidden" name="oldRepMethod" value="<?=$this->getArr['RepMethod']?>">
     
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
                      <td><?=$supervisorsubordinator?></td>
    				 <td align="left" valign="top"><input type="hidden" name="cmbRepType" value="<?=$arrRepType[0]?>"><strong>
 				 
					<?=$arrRepType[0]?>
					  
					  </strong></td>
					</tr>
					
					<tr> 
						<td valign="top"><?=$employeeid?></td>
<?						$empsupid =$this->getArr['editIDSup']; ?>
						<td align="left" valign="top"><input type="hidden" name="txtRepEmpID" value="<?=$this->getArr['editIDSup']?>"><strong>
						<?=$this->getArr['editIDSup']?>
						</strong></td>
					  </tr>
					  
					  <tr> 
						<td valign="top"><?=$reportingmethod?></td>
						<td align="left" valign="top"><select disabled name='cmbRepMethod'><strong>
						
						
							
<?										$keys = array_keys($arrRepMethod);
										$values = array_values($arrRepMethod);
									for($c=0;count($arrRepMethod)>$c;$c++)
										if($this->getArr['RepMethod']==$values[$c]){
										echo "<option selected value=". $values[$c] . ">" . $keys[$c] . "</option>";
										}else{
										echo "<option value=" . $values[$c] . ">" . $keys[$c] . "</option>";
										}
?>

</tr>
					  
					  <tr> 
						<td valign="top"></td>
						<td align="left" valign="top"> 
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

<?			
}
	elseif (isset($this->getArr['editIDSub']))
{	
	
?>

	 <input type="hidden" name="txtSupEmpID" value="<?=$this->getArr['id']?>">
     <input type="hidden" name="txtSubEmpID" value="<?=$this->getArr['editIDSub']?>">
  	 <input type="hidden" name="oldRepMethod" value="<?=$this->getArr['RepMethod']?>">
     
<br><br>
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
                      <td><?=$supervisorsubordinator?></td>
    				 <td align="left" valign="top"><input type="hidden" name="cmbRepType" value="<?=$arrRepType[1]?>"><strong>
 					 
					<?=$arrRepType[1]?>
					  
					  </strong></td>
					</tr>
					
					<tr> 
						<td valign="top"><?=$employeeid?></td>
<?						$empsubid = $this->getArr['editIDSub'];  ?>
						<td align="left" valign="top"><input type="hidden" name="txtRepEmpID" value="<?=$empsubid?>"><strong>
						<?=$empsubid?>
						</strong></td>
					  </tr>
					  
					  <tr> 
						<td valign="top"><?=$reportingmethod?></td>
						<td align="left" valign="top"><select disabled name="cmbRepMethod"><strong>
						
						
<?							
										$keys = array_keys($arrRepMethod);
										$values = array_values($arrRepMethod);
									for($c=0;count($arrRepMethod)>$c;$c++)
										if($this->getArr['RepMethod']==$values[$c]){
										echo "<option selected value=". $values[$c] . ">" . $keys[$c] . "</option>";
										}else{
										echo "<option value=" . $values[$c] . ">" . $keys[$c] . "</option>";
										}
?>
				
				</tr>
					  
					  <tr> 
						<td valign="top"></td>
						<td align="left" valign="top"> 
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

<? 
} else { 
?>
		<input type="hidden" name="txtSupEmpID" value="">
     	<input type="hidden" name="txtSubEmpID" value="">
	
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
                      <td><?=$supervisorsubordinator?></td>
    				  <td>
					  <select <?=$locRights['add'] ? '':'disabled'?> name="cmbRepType">
					  <option value="0"><?=$selectreporttype?></option>
<?					  
		
							echo "<option value=" . $arrRepType[0] . ">" . $arrRepType[0] . "</option>";
							echo "<option value=" . $arrRepType[1] . ">" . $arrRepType[1] . "</option>";
	
?>					  
					  </select></td>
					</tr>
					<tr><td><?=$employeeid?><td align="left" valign="top"><input type="text" disabled name="txtRepEmpID" value="">&nbsp;<input class="button" type="button" value=".." onclick="returnEmpDetail();">
						</td></tr>
					  <tr> 
						<td valign="top"><?=$reportingmethod?></td>
						<td align="left" valign="top"><select <?=$locRights['add'] ? '':'disabled'?> name='cmbRepMethod'>
						   		<option value="0"><?=$selecttype?></option>
<?
									$keys = array_keys($arrRepMethod);
									$values = array_values($arrRepMethod);
									for($c=0;count($arrRepMethod)>$c;$c++)
										echo "<option value=" . $values[$c] . ">" . $keys[$c] . "</option>";
?>						</select></td>
					  </tr>
					 		 					  
					 
					  <tr> 
						<td valign="top"></td>
						<td align="left" valign="top">
<?	if($locRights['add']) { ?>
        <img border="0" title="Save" onClick="addEXT();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_save.jpg">
<?	} ?>
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
<br><br>
	<input type="hidden" name="delSupSub">
<table><tr><td>
<table width='100%' cellpadding='0' cellspacing='0' border='0'>
  <tr>
    <td valign='top'>&nbsp; </td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>

  <tr>

    <td width='100%'><h3><?=$supervisorinfomation?></h3></td>
     <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>
  <tr>
  <td>
<?	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delSupEXT();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} ?>
  </td>
  </tr>
<tr><td>&nbsp;</td></tr>
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
						 <td><strong><?=$employeeid?></strong></td>
						 <td><strong><?=$employeename?></strong></td>
						 <td><strong><?=$reportingmethod?></strong></td>
					</tr>
<?
$rset = $this->popArr['suprset'];
$empname = $this ->popArr['empname'];							

    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
             echo "<td><input type='checkbox' class='checkbox' name='chksupdel[]' value='" . $rset[$c][1] ."|".$rset[$c][2]. "'></td>";
			
				  
				   echo "<td><a href='". $_SERVER['PHP_SELF'] ."?reqcode=" . $this->getArr['reqcode'] . "&id=" . $this->getArr['id']. "&editIDSup=" . $rset[$c][1] . "&RepMethod=" . $rset[$c][2] ."'>" . $rset[$c][1] . "</a></td>";
				   for($a=0; $empname && $a < count($empname); $a++)
				     if($rset[$c][1]==$empname[$a][0])  
				     echo '<td>' . $empname[$a][1] .'</td>';
				   for($a=0;count($arrRepMethod)>$a;$a++)
						if($rset[$c][2] == $values[$a])
				   	echo '<td>' . $keys[$a] .'</td>';
			   
            
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
              </table></td><td>

 <table width='100%' cellpadding='0' cellspacing='0' border='0'>
  <tr>
    <td valign='top'>&nbsp; </td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>

  <tr>

    <td width='100%'><h3><?=$subordinateinfomation?></h3></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>
  <tr>
  <td>
<?	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delSubEXT();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} ?>
  </td>
  </tr>
<tr><td>&nbsp;</td></tr>
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
						 <td><strong><?=$employeeid?></strong></td>
						 <td><strong><?=$employeename?></strong></td>
						 <td><strong><?=$reportingmethod?></strong></td>
					</tr>
<?

$rset = $this -> popArr['subrset'];
$empname = $this -> popArr['empname'];
							

    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chksubdel[]' value='" . $rset[$c][1] ."|".$rset[$c][2]. "'></td>";
			
				   $subid=$rset[$c][1];
				   echo "<td><a href='". $_SERVER['PHP_SELF'] ."?reqcode=" . $this->getArr['reqcode'] . "&id=" . $this->getArr['id']. "&editIDSub=" . $rset[$c][1] . "&RepMethod=" . $rset[$c][2] ."'>" . $rset[$c][1] . "</a></td>";
				    for($a=0; $empname && $a < count($empname); $a++)
				     if($rset[$c][1]==$empname[$a][0])  
				      echo '<td>' . $empname[$a][1] .'</td>';
				   for($a=0;count($arrRepMethod)>$a;$a++)
						if($rset[$c][2] == $values[$a])
				     echo '<td>' . $keys[$a] .'</td>';
			   
            
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
              </table></td></tr></table>
</form>
</body>
</html>
