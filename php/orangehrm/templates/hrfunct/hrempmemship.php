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

	$subown = $this->popArr['subown'];
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
	
	var frm=document.frmEmpMem;
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

function createDate(str) {
		var yy=eval(str.substr(0,4));
		var mm=eval(str.substr(5,2)) - 1;
		var dd=eval(str.substr(8,2));
		
		var tempDate = new Date(yy,mm,dd);
		
		return tempDate;
}

function goBack() {
		location.href = "./CentralController.php?reqcode=<?=$this->getArr['reqcode']?>&VIEW=MAIN";
	}

function addEXT()
{
	if(document.frmEmpMem.cmbMemCode.value=='0') {
		alert('Field should be selected');
		document.frmEmpMem.cmbMemCode.focus();
		return;
	}

	if(document.frmEmpMem.cmbMemTypeCode.value=='0') {
		alert('Field should be selected');
		document.frmEmpMem.cmbMemTypeCode.focus();
		return;
	}

	if(document.frmEmpMem.cmbMemSubOwn.value=='0') {
		alert('Field should be selected');
		document.frmEmpMem.cmbMemSubOwn.focus();
		return;
	}

	var txt = document.frmEmpMem.txtMemSubAmount;
	if (!numeric(txt)) {
		alert ("Description Error!");
		txt.focus();
		return false;
	}
	
	var commDate = createDate(document.frmEmpMem.txtMemCommDat.value);
	var renDate = createDate(document.frmEmpMem.txtMemRenDat.value);
	
	if(commDate >= renDate) {
		alert("Commence Date should be before renewal date");
		return;
	}
	
  document.frmEmpMem.STAT.value="ADD";
  document.frmEmpMem.submit();
}

function addCat()
{
var cntrl=document.frmEmpMem.cmbMemTypeCode;
document.frmEmpMem.STAT.value="OWN";
document.frmEmpMem.submit();
}

function editEXT()
{
    var txt = document.frmEmpMem.txtMemSubAmount;
	if (!numeric(txt)) {
		alert ("Description Error!");
		txt.focus();
		return false;
	}

	var commDate = createDate(document.frmEmpMem.txtMemCommDat.value);
	var renDate = createDate(document.frmEmpMem.txtMemRenDat.value);
	
	if(commDate >= renDate) {
		alert("Commence Date should be before renewal date");
		return;
	}

  document.frmEmpMem.STAT.value="EDIT";
  document.frmEmpMem.submit();
}

function delEXT()
{
      var check = 0;
		with (document.frmEmpMem) {
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
    document.frmEmpMem.STAT.value="DEL";
    document.frmEmpMem.submit();
}

function addNewEXT(str){
	var EmpID = str;		
	location.href = "./CentralController.php?id="+EmpID+"&capturemode=updatemode&reqcode=<?=$this->getArr['reqcode']?>";
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
    <td width='100%'><h2><?=$employeemembership?></h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>
</table>
<p>
<p>
<table width="431" border="0" cellspacing="0" cellpadding="0" ><td width="177">
<form name="frmEmpMem" method="post" action="<?=$_SERVER['PHP_SELF']?>?reqcode=<?=$this->getArr['reqcode']?>&id=<?=$this->getArr['id']?>">

  <tr>
    <td height="27" valign='top'> <p> <img title="Back" onmouseout="this.src='../../themes/beyondT/pictures/btn_back.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_back_02.jpg';"  src="../../themes/beyondT/pictures/btn_back.jpg" onclick="goBack();">
        <input type="hidden" name="STAT" value="">
      </p></td>
    <td width="254" align='left' valign='bottom'> <font color="red" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;
      </font> </td>
  </tr><td width="177">
</table>
 <?
$empdet = $this->popArr['empDet'];
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
						<td><?=$middlename?></td>
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

<?
if(isset($this->popArr['editArr']))
{
    $edit = $this->popArr['editArr'];
?>
	<?php 
      		 $mship=$this->popArr['mship'];      		 
      		 
      		 if ( ( $mship ) && ( count($mship) > 0 ) ) {
      		 	
      ?>
       <table>
		<tr>
      		<td>      			
      			<?	if ($locRights['edit'] ){ ?>
        			<img title="Add" onClick="addNewEXT('<?php echo $empdet[0][0]; ?>');" onmouseout="this.src='../../themes/beyondT/pictures/btn_add.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_add_02.jpg';" src="../../themes/beyondT/pictures/btn_add.jpg">
				<? 	} ?>
			</td>
			<td></td>
			<td></td>
			<td></td>
      	</tr>
	  </table>	
	  <?php };  ?>  
        
<br>
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
                      <td><?=$membershiptype?></td>
    				  <td><strong>
					  <input type="hidden" name="cmbMemTypeCode" value="<?=$edit[0][2]?>">
<?					    
						$typlist = $this->popArr['typlist'];
						for($c=0;count($typlist)>$c;$c++)
							if($typlist[$c][0]==$edit[0][2])
							   echo $typlist[$c][1];
?>					  
					  </strong></td>
					</tr>
					  <tr> 
						<td valign="top"><?=$membership?></td>
						<td align="left" valign="top"><strong>
						<input type="hidden" name="cmbMemCode" value="<?=$edit[0][1]?>">
<?
						$mship = $this->popArr['mship'];
						for($c=0;count($mship)>$c;$c++)
						    if($mship[$c][1]==$edit[0][1])
						       echo $mship[$c][2];
?>						
						</strong></td>
					  </tr>
					  <tr> 
						<td valign="top"><?=$subownership?></td>
						<td align="left" valign="top"><select disabled name="cmbMemSubOwn">
<?
						for($c=0;count($subown)>$c;$c++)
						    if($edit[0][3]==$subown[$c])
							    echo "<option selected value='" . $subown[$c] . "'>" . $subown[$c] . "</option>";
							else
							    echo "<option value='" . $subown[$c] . "'>" . $subown[$c] . "</option>";
							
?>
						</select></td>
					  </tr>
					  <tr> 
						<td valign="top"><?=$subamount?></td>
						<td align="left" valign="top"><input type="text" disabled name="txtMemSubAmount" value="<?=$edit[0][4]?>">
						</td>
					  </tr>
					  <tr> 
						<td valign="top"><?=$subcomdate?></td>
						<td align="left" valign="top"><input type="text" readonly disabled name="txtMemCommDat" value=<?=$edit[0][5]?>>&nbsp;<input class="button" disabled type="button" value=".." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmpMem.txtMemCommDat);return false;">
						</td>
					  </tr>
					  <tr> 
						<td valign="top"><?=$subredate?></td>
						<td align="left" valign="top"><input type="text" readonly disabled name="txtMemRenDat" value=<?=$edit[0][6]?>>&nbsp;<input class="button" disabled type="button" value=".." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmpMem.txtMemRenDat);return false;">
						</td>
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
                      <td><?=$membershiptype?></td>
    				  <td>
					  <select onChange="addCat();" <?=$locRights['add'] ? '':'disabled'?> name="cmbMemTypeCode">
					  <option value=0><?=$selmemtype?></option>

<?					  	$typlist= $this->popArr['typlist'];
							for($c=0;$typlist && count($typlist)>$c;$c++)
							if(isset($this->popArr['cmbMemTypeCode']) && $this->popArr['cmbMemTypeCode']==$typlist[$c][0]) 
							
							   echo "<option selected value=" . $typlist[$c][0] . ">" . $typlist[$c][1] . "</option>";
							else
							   echo "<option value=" . $typlist[$c][0] . ">" . $typlist[$c][1] . "</option>";

?>					  
					  </select></td>
					</tr>
					  <tr> 
						<td valign="top"><?=$membership?></td>
						<td align="left" valign="top"><select <?=$locRights['add'] ? '':'disabled'?> name='cmbMemCode'>
						   		<option value=0><?=$selmemship?></option>
<?
					if(isset($this->popArr['cmbMemTypeCode'])) {
						
						$mship=$this->popArr['mship'];
						for($c=0;$mship && count($mship)>$c;$c++)
						    echo "<option value=" . $mship[$c][0] . ">" . $mship[$c][1] . "</option>";
						}
?>						
						</select></td>
					  </tr>
					  <tr> 
						<td valign="top"><?=$subownership?></td>
						<td align="left" valign="top"><select <?=$locRights['add'] ? '':'disabled'?> name="cmbMemSubOwn">
						   		<option value=0><?=$selownership?></option>
<?
						for($c=0;count($subown)>$c;$c++)
							    echo "<option value='" . $subown[$c] . "'>" . $subown[$c] . "</option>";
							
?>
						</select></td>
					  </tr>
					  <tr> 
						<td valign="top"><?=$subamount?></td>
						<td align="left" valign="top"><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtMemSubAmount" >
						</td>
					  </tr>
					  <tr> 
						<td valign="top"><?=$subcomdate?></td>
						<td align="left" valign="top"><input type="text" readonly name="txtMemCommDat" >&nbsp;<input class="button" <?=$locRights['add'] ? '':'disabled'?> type="button" value=".." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmpMem.txtMemCommDat);return false;">
						</td>
					  </tr>
					  <tr> 
						<td valign="top"><?=$subredate?></td>
						<td align="left" valign="top"><input type="text" readonly name="txtMemRenDat" >&nbsp;<input class="button" <?=$locRights['add'] ? '':'disabled'?> type="button" value=".." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmpMem.txtMemRenDat);return false;">
						</td>
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

<table width='100%' cellpadding='0' cellspacing='0' border='0'>
  <tr>
    <td valign='top'>&nbsp; </td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>

  <tr>

    <td width='100%'><h3><?=$assignmemship?></h3></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>
  <tr>
  <td>
<?	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delEXT();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
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
						 <td><strong><?=$membership?></strong></td>
						 <td><strong><?=$membershiptype?></strong></td>
						 <td><strong><?=$subownership?></strong></td>
						 <td><strong><?=$subcomdate?></strong></td>
						 <td><strong><?=$subredate?></strong></td>
					</tr>
<?

$mship= $this->popArr['mshipAll'];
$rset = $this->popArr['rset'];


    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkdel[]' value='" . $rset[$c][1] ."|" . $rset[$c][2] . "'></td>";
			for($a=0;count($mship)>$a;$a++)
			    if($mship[$a][1]==$rset[$c][1])
				   $fname=$mship[$a][2];
            echo "<td><a href='".$_SERVER['PHP_SELF']. "?reqcode=" .$this->getArr['reqcode'] . "&id=" . $this->getArr['id']. "&editID1=" . $rset[$c][1] . "&editID2=" . $rset[$c][2] . "'>" . $fname . "</a></td>";
			
            for($a=0;count($typlist)>$a;$a++)
			    if($typlist[$a][0]==$rset[$c][2])
				   $fname=$typlist[$a][1];
            echo '<td>' . $fname .'</td>';
            echo '<td>' . $rset[$c][3] .'</td>';
            $disStr = explode(" ",$rset[$c][5]);
            echo '<td>' . $disStr[0] .'</td>';
            $disStr = explode(" ",$rset[$c][6]);
            echo '<td>' . $disStr[0] .'</td>';
        echo '</tr>';
        }

?>
                  </table></td>
                  <td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
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
<iframe width=174 height=189 name="gToday:normal:agenda.js" id="gToday:normal:agenda.js" src="../../scripts/ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; top:-500px; left:-500px;">
</iframe></body>
</html>