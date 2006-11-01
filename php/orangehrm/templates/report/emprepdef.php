<?php
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
		location.href = "./CentralController.php?repcode=<?php echo $this->getArr['repcode']?>&VIEW=MAIN";
	}
	
function edit()
{
	if(document.Edit.title=='Save') {
		addUpdate();
		return;
	}
	
	var frm=document.frmRepDef;
	for (var i=0; i < frm.elements.length; i++)
		frm.elements[i].disabled = false;
	document.Edit.src="../../themes/beyondT/pictures/btn_save.jpg";
	document.Edit.title="Save";
}

function chkboxCheck() {
      var check = 0;
		with (document.frmRepDef) {
			for (var i=0; i < elements.length; i++) {
				if ((elements[i].name == 'chkcriteria[]') && (elements[i].checked == true)){
					check = 1;
				}
			}
        }

        if(check==0)
			return false;
			
      check = 0;
		with (document.frmRepDef) {
			for (var i=0; i < elements.length; i++) {
				if ((elements[i].name == 'chkfield[]') && (elements[i].checked == true)){
					check = 1;
				}
			}
        }

        if(check==0)
        	return false;
        	
	return true;
}
	
function addSave() {
		
	if (document.frmRepDef.txtRepName.value == '') {
		alert ("Description Error!");
		document.frmRepDef.txtRepName.focus();
		return;
	}
	
	if(!chkboxCheck()) {
		alert('Select at least one criteria and one fields')
		return;
	}
	
	document.frmRepDef.sqlState.value = "NewRecord";
	document.frmRepDef.submit();		
}

function addUpdate() {
		
	if (document.frmRepDef.txtRepName.value == '') {
		alert ("Description Error!");
		document.frmRepDef.txtRepName.focus();
		return;
	}
		
	if(!chkboxCheck()) {
		alert('Select at least one criteria and one fields')
		return;
	}
	
	document.frmRepDef.sqlState.value = "UpdateRecord";
	document.frmRepDef.submit();		
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
    <td width='100%'><h2>Report Definition</h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>
</table>
<table border="0" >
  <tr>
  <td valign="middle" height="35"><img title="Back" onMouseOut="this.src='../../themes/beyondT/pictures/btn_back.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_back_02.jpg';"  src="../../themes/beyondT/pictures/btn_back.jpg" onClick="goBack();"></td>
  </tr>
</table>
<?php if ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'addmode')) { ?>	
<p>
<p>
<table width="431" border="0" cellspacing="0" cellpadding="0" ><td width="177">
<form name="frmRepDef" method="post" action="<?php echo $_SERVER['PHP_SELF']?>?repcode=<?php echo $this->getArr['repcode']?>">
<input type="hidden" name="sqlState" value="">

 <table width='100%' cellpadding='0' cellspacing='0' border='0'>
   <tr>
    <td width='100%'>
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
                      <td>Report ID</td>
    				  <td ><strong><?php echo $this->popArr['newID']?></strong></td>
    				 </tr>
    				 <tr>
 					  <td>Report Name</td>
						<td ><input type="text"  name="txtRepName"></td>
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
	</td>
	</tr>
  <tr>
    <td height="30" valign="bottom" width='100%'><h4>Selection Criteria</h4></td>
  </tr>
	<tr>
	<td width="100%">
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
                      <td><input type='checkbox' class='checkbox' name='chkcriteria[]' value='EMPNo'></td>
                      <td valign="top">EMP No</td>
   					</tr>
					
					<tr>
					  <td><input type='checkbox' class='checkbox' name='chkcriteria[]' value='AgeGroup'></td> 
					  <td valign="top">Age Group</td>
					</tr>
					
  					<tr>
					  <td><input type='checkbox' class='checkbox' name='chkcriteria[]' value='PayGrade'></td> 
				      <td>Pay Grade</td>
  					</tr>
  					
					<tr>
					  <td><input type='checkbox' class='checkbox' name='chkcriteria[]' value='QualType'></td> 
					    <td>Qualification Type</td>
    				</tr>
    				
					<tr>
					  <td><input type='checkbox' class='checkbox' name='chkcriteria[]' value='EmpType'></td> 
					  <td valign="top">Employee Status</td>
  					</tr>
  					
					<tr>
					  <td><input type='checkbox' class='checkbox' name='chkcriteria[]' value='SerPeriod'></td> 
					  <td valign="top">Service Period</td>
					</tr>
					
					<tr>
					  <td><input type='checkbox' class='checkbox' name='chkcriteria[]' value='JobTitle'></td> 
					 <td>Job Title</td>
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
	</td>
	</tr>
  <tr>
    <td height="30" valign="bottom" width='100%'><h4>Fields</h4></td>
  </tr>
	<tr>
		<td width="100%">
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
                      	 <td><input type='checkbox' class='checkbox' name='chkfield[]' value='EmpNo'></td>
						 <td>Emp No</td>
					</tr>
					<tr>
                      	 <td><input type='checkbox' class='checkbox' name='chkfield[]' value='LastName'></td>
						 <td>Last Name</td>
					</tr>
					<tr>
                      	 <td><input type='checkbox' class='checkbox' name='chkfield[]' value='Address'></td>
						 <td>Address</td>
					</tr>
					<tr>
                      	 <td><input type='checkbox' class='checkbox' name='chkfield[]' value='TelNo'></td>
						 <td>Tel No</td>
					</tr>
					<tr>
                      	 <td><input type='checkbox' class='checkbox' name='chkfield[]' value='DateOfBir'></td>
						 <td>Date Of Birth</td>
					</tr>
					<tr>
                      	 <td><input type='checkbox' class='checkbox' name='chkfield[]' value='JobTitle'></td>
						 <td>Job Title</td>
					</tr>
					<tr>
                      	 <td><input type='checkbox' class='checkbox' name='chkfield[]' value='JoinDate'></td>
						 <td>Join Date</td>
					</tr>
					<tr>
                      	 <td><input type='checkbox' class='checkbox' name='chkfield[]' value='Qualification'></td>
						 <td>Qualification</td>
					</tr>
					<tr>
                      	 <td><input type='checkbox' class='checkbox' name='chkfield[]' value='EmployeeType'></td>
						 <td>Employee Status</td>
					</tr>
					<tr>
                      	 <td><input type='checkbox' class='checkbox' name='chkfield[]' value='PayGrade'></td>
						 <td>Pay Grade</td>
					</tr>
					
              	</table>
                  <td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                </tr>
                <tr>
                  <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
                  <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
                  <td><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="16" border="0" alt=""></td>
                </tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr><td>&nbsp;</td></tr>
              </table>
              </td>
              </tr>
			  <tr><td align="right"><img onClick="addSave();" onMouseOut="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
        <img onClick="clearAll();" onMouseOut="this.src='../../themes/beyondT/pictures/btn_clear.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_clear_02.jpg';" src="../../themes/beyondT/pictures/btn_clear.jpg"></td></tr>
              </table>
<?php } else if ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'updatemode')) {
	 
	$message = $this->popArr['editArr'];
?>
<p>		
<p>
<table width="431" border="0" cellspacing="0" cellpadding="0" ><td width="177">
<form name="frmRepDef" method="post" action="<?php echo $_SERVER['PHP_SELF']?>?repcode=<?php echo $this->getArr['repcode']?>&id=<?php echo $this->getArr['id']?>">
<input type="hidden" name="sqlState" value="">
 
 <table width='100%' cellpadding='0' cellspacing='0' border='0'>
   <tr>
    <td width='100%'>
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
                      <td>Report ID</td>
    				  <td ><strong><?php echo $message[0][0]?><input name="txtRepID" type="hidden" value="<?php echo $message[0][0]?>"></strong></td>
    				 </tr>
    				 <tr>
 					  <td>Report Name</td>
						<td ><input type="text" disabled name="txtRepName" value="<?php echo $message[0][1]?>"></td>
					</tr>
    				 <tr>
 					  <td><a href="<?php echo $_SERVER['PHP_SELF']?>?id=<?php echo $message[0][0]?>&repcode=RUG">Assign User Groups</a></td>
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
	</td>
	</tr>
  <tr>
    <td height="30" valign="bottom" width='100%'><h4>Selection Criteria</h4></td>
  </tr>
	<tr>
	<td width="100%">
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
                  <?php $criteriaArr= $this->popArr['criteriaList'];?>
                    <tr>
                      <td><input disabled type='checkbox' <?php echo in_array('EMPNo',$criteriaArr) ? 'checked' : ''?>  class='checkbox' name='chkcriteria[]' value='EMPNo'></td>
                      <td valign="top">EMP No</td>
   					</tr>
					
					<tr>
					  <td><input disabled type='checkbox' <?php echo in_array('AgeGroup',$criteriaArr) ? 'checked' : ''?> class='checkbox' name='chkcriteria[]' value='AgeGroup'></td> 
					  <td valign="top">Age Group</td>
					</tr>
					
  					<tr>
					  <td><input disabled type='checkbox' <?php echo in_array('PayGrade',$criteriaArr) ? 'checked' : ''?> class='checkbox' name='chkcriteria[]' value='PayGrade'></td> 
				      <td>Pay Grade</td>
  					</tr>
  					
					<tr>
					  <td><input disabled type='checkbox' <?php echo in_array('QualType',$criteriaArr) ? 'checked' : ''?> class='checkbox' name='chkcriteria[]' value='QualType'></td> 
					    <td>Qualification Type</td>
    				</tr>
    				
					<tr>
					  <td><input disabled type='checkbox' <?php echo in_array('EmpType',$criteriaArr) ? 'checked' : ''?> class='checkbox' name='chkcriteria[]' value='EmpType'></td> 
					  <td valign="top">Employee Status</td>
  					</tr>
  					
					<tr>
					  <td><input disabled type='checkbox' <?php echo in_array('SerPeriod',$criteriaArr) ? 'checked' : ''?> class='checkbox' name='chkcriteria[]' value='SerPeriod'></td> 
					  <td valign="top">Service Period</td>
					</tr>
					
					<tr>
					  <td><input disabled type='checkbox' <?php echo in_array('JobTitle',$criteriaArr) ? 'checked' : ''?> class='checkbox' name='chkcriteria[]' value='JobTitle'></td> 
					 <td>Job Title</td>
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
                <tr><td>&nbsp;</td></tr>
                <tr><td>&nbsp;</td></tr>
              </table>
	</td>
	</tr>
  <tr>
    <td height="30" valign="bottom" width='100%'><h4>Fields</h4></td>
  </tr>
	<tr>
		<td width="100%">
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
                  
                  <?php $fieldArr= $this->popArr['fieldList'];?>
                    <tr>
                      	 <td><input disabled type='checkbox' <?php echo in_array('EmpNo',$fieldArr) ? 'checked': ''?> class='checkbox' name='chkfield[]' value='EmpNo'></td>
						 <td>Emp No</td>
					</tr>
					<tr>
                      	 <td><input disabled type='checkbox' <?php echo in_array('LastName',$fieldArr) ? 'checked': ''?> class='checkbox' name='chkfield[]' value='LastName'></td>
						 <td>Last Name</td>
					</tr>
					<tr>
                      	 <td><input disabled type='checkbox' <?php echo in_array('Address',$fieldArr) ? 'checked': ''?> class='checkbox' name='chkfield[]' value='Address'></td>
						 <td>Address</td>
					</tr>
					<tr>
                      	 <td><input disabled type='checkbox' <?php echo in_array('TelNo',$fieldArr) ? 'checked': ''?> class='checkbox' name='chkfield[]' value='TelNo'></td>
						 <td>Tel No</td>
					</tr>
					<tr>
                      	 <td><input disabled type='checkbox' <?php echo in_array('DateOfBir',$fieldArr) ? 'checked': ''?> class='checkbox' name='chkfield[]' value='DateOfBir'></td>
						 <td>Date Of Birth</td>
					</tr>
					<tr>
                      	 <td><input disabled type='checkbox' <?php echo in_array('JobTitle',$fieldArr) ? 'checked': ''?> class='checkbox' name='chkfield[]' value='JobTitle'></td>
						 <td>Job Title</td>
					</tr>
					<tr>
                      	 <td><input disabled type='checkbox' <?php echo in_array('JoinDate',$fieldArr) ? 'checked': ''?> class='checkbox' name='chkfield[]' value='JoinDate'></td>
						 <td>Join Date</td>
					</tr>
					<tr>
                      	 <td><input disabled type='checkbox' <?php echo in_array('Qualification',$fieldArr) ? 'checked': ''?> class='checkbox' name='chkfield[]' value='Qualification'></td>
						 <td>Qualification</td>
					</tr>
					<tr>
                      	 <td><input disabled type='checkbox' <?php echo in_array('EmployeeType',$fieldArr) ? 'checked': ''?>  class='checkbox' name='chkfield[]' value='EmployeeType'></td>
						 <td>Employee Status</td>
					</tr>
					<tr>
                      	 <td><input disabled type='checkbox' <?php echo in_array('PayGrade',$fieldArr) ? 'checked': ''?>  class='checkbox' name='chkfield[]' value='PayGrade'></td>
						 <td>Pay Grade</td>
					</tr>
					
			  <tr> 
						<td valign="top"></td>
						<td align="left" valign="top"> 
  
						</td>
			  </tr>
					  <tr><td></td><td align="right" width="100%">
			        <img src="../../themes/beyondT/pictures/btn_edit.jpg" title="Edit" onMouseOut="mout();" onMouseOver="mover();" name="Edit" onClick="edit();">
				</td>
		       </tr>			
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
                    <tr><td>&nbsp;</td></tr>
                    <tr><td>&nbsp;</td></tr>
              </table>
              </td>
              </tr>
              </table>
<?php	}  ?> 
</form>
</body>
</html>