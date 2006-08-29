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

	$sysConst = new sysConf(); 
	$locRights=$_SESSION['localRights'];

	$bfilter = $this->popArr['bfilter'];

?>
<!DOCCIDE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
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
	
	var frm=document.frmEmpNonCashBen;
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

function createDate(str) {
		var yy=eval(str.substr(0,4));
		var mm=eval(str.substr(5,2)) - 1;
		var dd=eval(str.substr(8,2));
		
		var tempDate = new Date(yy,mm,dd);
		
		return tempDate;
}

function addEXT()
{
      var check = 0;
		with (document.frmAddEmpNonCashBen) {
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

    document.frmAddEmpNonCashBen.STAT.value="ADD";
    document.frmAddEmpNonCashBen.submit();
}

function addOthEXT()
{
      var check = 0;
		with (document.frmOthEmpNonCashBen) {
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

    document.frmOthEmpNonCashBen.STAT.value="ADDOTH";
    document.frmOthEmpNonCashBen.submit();
}


function editEXT()
{
var cnt=document.frmEmpNonCashBen.txtBenQty;
	if(!numeric(cnt)) {
		alert("Field should be Numeric");
		cnt.focus();
		return;
	}
	

	var issDate=createDate(document.frmEmpNonCashBen.txtBenIssDat.value);
	var retDate=createDate(document.frmEmpNonCashBen.txtBenItmRetDat.value);
	
	if(issDate >= retDate) {
		alert("Issued Date should be before Returned Date");
		return;
	}
	
	
  document.frmEmpNonCashBen.STAT.value="EDIT";
  document.frmEmpNonCashBen.submit();
}

function delEXT()
{
      var check = 0;
		with (document.frmDelEmpNonCashBen) {
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
    document.frmDelEmpNonCashBen.STAT.value="DEL";
    document.frmDelEmpNonCashBen.submit();
}

</script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<link href="../../themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("../../themes/beyondT/css/style1.css"); </style>
</head>
<body>
<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
  <tr>
    <td valign='top'>&nbsp; </td>
    <td width='100%'><h2>Non Cash Benefits</h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'><A href='index.php?module=Contacts&action=index&return_module=Contacts&return_action=DetailView&&print=true' class='utilsLink'></td>
  </tr>
</table>
<p>
<p>
<table width="431" border="0" cellspacing="0" cellpadding="0" ><td width="177">
<form name="frmEmpNonCashBen" method="post" action="<?=$_SERVER['PHP_SELF']?>?reqcode=<?=$this->getArr['reqcode']?>&id=<?=$this->getArr['id']?>">
  <tr>
    <td height="27" valign='top'> <p>  <img title="Back" onmouseout="this.src='../../themes/beyondT/pictures/btn_back.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_back_02.jpg';"  src="../../themes/beyondT/pictures/btn_back.jpg" onclick="goBack();">
        <input type="hidden" name="STAT" value="">
      </p></td>
    <td width="254" align='left' valign='bottom'> <font color="red" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;
      </font> </td>
  </tr><td width="177">
</table>
<?
$empdet = $this->popArr['empdet'];
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
                      <td>Employee ID</td>
    				  <td width="75"><font color="#204242"><strong><?=$empdet[0][0]?></strong></font></td>
    				  <td width="50">&nbsp;</td>
					  <td>Surname</td>
						<td width="300"><font color="#204242"><strong><?=$empdet[0][3]?></strong></font></td>
					</tr>
					  <tr> 
						<td>Calling Name</td>
						<td><font color="#204242"><strong><?=$empdet[0][2]?></strong></font></td>
    				  <td width="50">&nbsp;</td>
						<td>Initials</td>
						<td><font color="#204242"><strong><?=$empdet[0][5]?></</font></td>
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
         <input type="hidden" name="txtEmpID" value="<?=$this->getArr['id']?>">
<?
if(isset($this->popArr['editArr']))
{
  $edit = $this->popArr['editArr'];
?>

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
						<td valign="top">Non Cash Benefit</td>
						<td align="left" valign="top"><input type="hidden" name="cmbBenCode" value="<?=$edit[0][1]?>"><strong>
<?
						$benlist = $this->popArr['benlist'];
						for($c=0;count($benlist)>$c;$c++)
						    if($benlist[$c][0]==$edit[0][1])
						       echo $benlist[$c][1];
?>						
						</strong></td>
					  </tr>
					  <tr> 
						<td valign="top">Issue Date</td>
						<td align="left" valign="top"><input type="text" readonly disabled name="txtBenIssDat" value=<?=$edit[0][2]?>>&nbsp;<input disabled class="button" type="button" value=".." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmpNonCashBen.txtBenIssDat);return false;">
						</td>
					  </tr>
					  <tr> 
						<td valign="top">Quantity</td>
						<td align="left" valign="top"><input type="text" disabled name="txtBenQty" value="<?=$edit[0][3]?>">
						</td>
					  </tr>
					  <tr> 
						<td valign="top">Comment</td>
						<td align="left" valign="top"><input type="text" disabled name="txtBenComment" value="<?=$edit[0][4]?>">
						</td>
					  </tr>
					  <tr> 
						<td valign="top">Item Returnable</td>
						<td align="left" valign="top"><input type="checkbox" disabled name="chkBenItmReturnableFlag" <?=($edit[0][5]=='1'?'checked':'')?> value="1" >
						</td>
					  </tr>
					  <tr> 
						<td valign="top">Returned Date</td>
						<td align="left" valign="top"><input type="text" readonly disabled name="txtBenItmRetDat" value=<?=$edit[0][6]?>>&nbsp;<input disabled class="button" type="button" value=".." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmpNonCashBen.txtBenItmRetDat);return false;">
						</td>
					  </tr>
					  <tr> 
						<td valign="top">Item Returned</td>
						<td align="left" valign="top"><input type="checkbox" disabled name="chkBenItmRetFlag" <?=($edit[0][7]=='1'?'checked':'')?> value="1" >
						</td>
					  </tr>
					  <tr> 
						<td valign="top">Filter</td>
						<? $flt=array_keys($bfilter); ?>
						<td align="left" valign="top"><strong><?=$flt[((int)$edit[0][8]-1)]?></strong></td>
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

<? } else { ?>
&nbsp;
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
						<td valign="top">Non Cash Benefit</td>
						<td align="left" valign="top"><input type="text" disabled></td>
					  </tr>
					  <tr> 
						<td valign="top">Issue Date</td>
						<td align="left" valign="top"><input type="text" disabled></td>
					  </tr>
					  <tr> 
						<td valign="top">Quantity</td>
						<td align="left" valign="top"><input type="text" disabled></td>
					  </tr>
					  <tr> 
						<td valign="top">Comment</td>
						<td align="left" valign="top"><input type="text" disabled></td>
					  </tr>
					  <tr> 
						<td valign="top">Item Returnable</td>
						<td align="left" valign="top"><input type="checkbox" disabled >
						</td>
					  </tr>
					  <tr> 
						<td valign="top">Returned Date</td>
						<td align="left" valign="top"><input type="text" disabled></td>
					  </tr>
					  <tr> 
						<td valign="top">Item Returned</td>
						<td align="left" valign="top"><input type="checkbox" disabled></td>
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

</form>
<table border="0">
<tr>
<td valign="top">
<form name="frmDelEmpNonCashBen" method="post" action="<?=$_SERVER['PHP_SELF']?>?reqcode=<?=$this->getArr['reqcode']?>&id=<?=$this->getArr['id']?>">
<input type="hidden" name="STAT" value="">
<input type="hidden" name="txtEmpID" value="<?=$this->getArr['id']?>">

<table width='100%' cellpadding='0' cellspacing='0' border='0'>
  <tr>
    <td valign='top'>&nbsp; </td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>

  <tr>

    <td><h3>Assigned Benefits</h3></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'><A href='index.php?module=Contacts&action=index&return_module=Contacts&return_action=DetailView&&print=true' class='utilsLink'></td>
  </tr>
  <tr>
  <td>
  <?	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delEXT();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} ?>
  </td>
  <td><a href="<?=$_SERVER['PHP_SELF']?>?reqcode=<?=$this->getArr['reqcode']?>&id=<?=$this->getArr['id']?>&OBEN=1">Add Other Benefits</a></td>
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
						 <td><strong>Benefit</strong></td>
						 <td><strong>Quantity</strong></td>
					</tr>
<?
$rset = $this->popArr['cashbenAss'];
$benlist = $this->popArr['benlist'];;

    for($c=0; $rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkdel[]' value='" . $rset[$c][1] . "'></td>";
			for($a=0;count($benlist)>$a;$a++)
			    if($benlist[$a][0]==$rset[$c][1])
				   $fname=$benlist[$a][1];
            echo "<td><a href='" .$_SERVER['PHP_SELF']. "?reqcode=" . $this->getArr['reqcode'] . "&id=" . $this->getArr['id']. "&editID=" . $rset[$c][1] . "'>" . $fname . "</a></td>";
            echo '<td>' . $rset[$c][3] .'</td>';
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
</td>
<td valign="top">
<form name="frmAddEmpNonCashBen" method="post" action="<?=$_SERVER['PHP_SELF']?>?reqcode=<?=$this->getArr['reqcode']?>&id=<?=$this->getArr['id']?>">
<table width='100%' cellpadding='0' cellspacing='0' border='0'>
  <tr>
    <td valign='top'>&nbsp; </td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>
        <input type="hidden" name="STAT" value="">
		<input type="hidden" name="txtEmpID" value="<?=$this->getArr['id']?>">
  <tr>

    <td width='100%'><h3>Unassigned Benefits</h3></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'><A href='index.php?module=Contacts&action=index&return_module=Contacts&return_action=DetailView&&print=true' class='utilsLink'></td>
  </tr>
  <tr>
  <td>
  <?	if($locRights['add']) { ?>
        <img border="0" title="Add" onClick="addEXT();" onmouseout="this.src='../../themes/beyondT/pictures/btn_add.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_add_02.jpg';" src="../../themes/beyondT/pictures/btn_add.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_add.jpg">
<?	} ?>
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
						 <td><strong>Benefit Code</strong></td>
						 <td><strong>Benefit </strong></td>
					</tr>
<?
$rset = $this->popArr['cashbenUnAss'];
$benlist = $this->popArr['benlist'];

    for($c=0; $rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkdel[]' value='" . $rset[$c][1] . "'></td>";
            for($a=0;$benlist && count($benlist)>$a;$a++)
                if($rset[$c][1]==$benlist[$a][0]) {
			            echo "<td>" . $benlist[$a][0] . "</td>";
			            echo '<td>' . $benlist[$a][1] .'</td>';
                }
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
</td>
</tr>
<tr>
<td valign="top">
<form name="frmOthEmpNonCashBen" method="post" action="<?=$_SERVER['PHP_SELF']?>?reqcode=<?=$this->getArr['reqcode']?>&id=<?=$this->getArr['id']?>">
<? if(isset($this->getArr['OBEN'])) { ?>
<table width='100%' cellpadding='0' cellspacing='0' border='0'>
  <tr>
    <td valign='top'>&nbsp; </td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>
        <input type="hidden" name="STAT" value="">
		<input type="hidden" name="txtEmpID" value="<?=$this->getArr['id']?>">
        <tr>

    <td width='100%'><h3>Other Benefits</h3></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'><A href='index.php?module=Contacts&action=index&return_module=Contacts&return_action=DetailView&&print=true' class='utilsLink'></td>
  </tr>
  <tr>
  <td>
  <?	if($locRights['add']) { ?>
        <img border="0" title="Add" onClick="addOthEXT();" onmouseout="this.src='../../themes/beyondT/pictures/btn_add.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_add_02.jpg';" src="../../themes/beyondT/pictures/btn_add.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_add.jpg">
<?	} ?>
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
						 <td><strong>Benefit Code</strong></td>
						 <td><strong>Benefit </strong></td>
					</tr>
<?
$rset = $this->popArr['cashbenOther'];

    for($c=0; $rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkdel[]' value='" . $rset[$c][0] . "'></td>";
            echo "<td>" . $rset[$c][0] . "</td>";
            echo '<td>' . $rset[$c][1] .'</td>';
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
	<? } ?>
</td>
</tr>
</table>              
<iframe width=174 height=189 name="gToday:normal:agenda.js" id="gToday:normal:agenda.js" src="../../scripts/ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; top:-500px; left:-500px;">
</iframe></body>
</html>
