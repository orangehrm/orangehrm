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
require_once ROOT_PATH . '/lib/exception/ExceptionHandler.php';
require_once ROOT_PATH . '/lib/confs/sysConf.php';
	
	$sysConst = new sysConf(); 
	$locRights=$_SESSION['localRights'];
	
	$arrAgeSim = $this-> popArr['arrAgeSim'];
	$arrEmpType= $this-> popArr['arrEmpType'];
	
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


function goBack() {
	location.href = "./CentralController.php?repcode=<?=$this->getArr['repcode']?>&VIEW=MAIN";
	}
	
function returnEmpDetail(){
		var popup=window.open('../../templates/hrfunct/emppop.php?reqcode=REP','Employees','height=450,width=400');
        if(!popup.opener) popup.opener=self;	
}


function addCat() {
document.frmEmpRepTo.sqlState.value="OWN";
document.frmEmpRepTo.submit();
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
	
	var frm=document.frmEmpRepTo;
	
	frm.txtRepName.disabled = false;
	
	for (var i=0; i < frm.elements.length; i++)
		if(frm.elements[i].type == 'checkbox') {
			frm.elements[i].disabled = false ;					
		}
	
	chkboxCriteriaEnable();	
	document.Edit.src="../../themes/beyondT/pictures/btn_save.jpg";
	document.Edit.title="Save";
}

function addUpdate() {
		
		
	if(!chkboxCheck()) {
		alert('Select at least one criteria and one fields')
		return;
	}
	
	if(!validation())
		return;

	parent.scroll(0,0);
	document.frmEmpRepTo.sqlState.value = "UpdateRecord";
	document.frmEmpRepTo.submit();		
}


function chkboxCriteriaEnable() {	
		
		with (document.frmEmpRepTo) {
			for (var i=0; i < elements.length; i++) {
				if (elements[i].name == 'chkcriteria[]') {
					
					 switch(elements[i].id) {
					 	
					 	case 'EMPNO'      : document.frmEmpRepTo.empPop.disabled = !elements[i].checked;
				 							if(!elements[i].checked){
					 						document.frmEmpRepTo.txtRepEmpID.value='';
				 							} break;
					 	case 'AgeGroup'   : 											
											document.frmEmpRepTo.cmbAgeCode.disabled= !elements[i].checked; 
					 						
											disableAgeField();
											
											if(!elements[i].checked){
					 							document.frmEmpRepTo.cmbAgeCode.options[0].selected = true;
					 							document.frmEmpRepTo.txtEmpAge1.value='';
					 							document.frmEmpRepTo.txtEmpAge2.value='';	
											
					 						} break;
					 	case 'PayGrade'   : document.frmEmpRepTo.cmbSalGrd.disabled= !elements[i].checked;
					 						if(!elements[i].checked){
					 						document.frmEmpRepTo.cmbSalGrd.options[0].selected=true;
					 						} break;
					 	case 'QualType'   : document.frmEmpRepTo.TypeCode.disabled = !elements[i].checked;					 						
				 							if(!elements[i].checked){
					 						document.frmEmpRepTo.TypeCode.options[0].selected=true;
					 						
				 							} break;
					 	case 'EmpType'    : document.frmEmpRepTo.cmbEmpType.disabled= !elements[i].checked;
					 						if(!elements[i].checked){
					 						document.frmEmpRepTo.cmbEmpType.options[0].selected=true;
					 						} break;
					 	case 'SerPeriod'  : document.frmEmpRepTo.cmbSerPerCode.disabled= !elements[i].checked;
											disableSerPeriodField()
											
					 						if(!elements[i].checked){
					 						document.frmEmpRepTo.cmbSerPerCode.options[0].selected = true;
					 						document.frmEmpRepTo.Service1.value='';
					 						document.frmEmpRepTo.Service2.value='';
											
											document.frmEmpRepTo.Service1.disabled = false;
					 						document.frmEmpRepTo.Service2.disabled = false;
					 						} break;
					 	case 'JobTitle': document.frmEmpRepTo.cmbDesig.disabled= !elements[i].checked;
					 						if(!elements[i].checked){
					 						document.frmEmpRepTo.cmbDesig.options[0].selected = true;
					 						} break;
							 	
					 }
				}
			}
        }
}

function chkboxCheck() {
        var check = 0;
		with (document.frmEmpRepTo) {
			for (var i=0; i < elements.length; i++) {
				if ((elements[i].name == 'chkcriteria[]') && (elements[i].checked == true)){
					check = 1;
				}
			}
        }

        if(check==0)
			return false;
			
				
      var check = 0;
		with (document.frmEmpRepTo) {
			for (var i=0; i < elements.length; i++) {
				if ((elements[i].name == 'checkfield[]') && (elements[i].checked == true)){
					check = 1;
				}
			}
        }

        if(check==0)
        	return false;
        	
	return true;
}


function disableAgeField() {
	if(document.frmEmpRepTo.cmbAgeCode.value == "0") {
		document.frmEmpRepTo.txtEmpAge1.disabled = true;
		document.frmEmpRepTo.txtEmpAge2.disabled = true;
		return;
	} else if(document.frmEmpRepTo.cmbAgeCode.value=="range") {
		document.frmEmpRepTo.txtEmpAge1.disabled = false ;
		document.frmEmpRepTo.txtEmpAge2.disabled = false;
		return;
	} else if(document.frmEmpRepTo.cmbAgeCode.value=='<' || document.frmEmpRepTo.cmbAgeCode.value=='>') {
		document.frmEmpRepTo.txtEmpAge1.disabled = false;
		document.frmEmpRepTo.txtEmpAge2.disabled = true;
		document.frmEmpRepTo.txtEmpAge2.value='';
		
		return;
	}
}

function disableSerPeriodField() {
	if(document.frmEmpRepTo.cmbSerPerCode.value=="0") {
		document.frmEmpRepTo.Service1.disabled = true;
		document.frmEmpRepTo.Service2.disabled = true;
		return;
	} else if(document.frmEmpRepTo.cmbSerPerCode.value=="range") {
		document.frmEmpRepTo.Service1.disabled = false;
		document.frmEmpRepTo.Service2.disabled = false;
		return;
	} else if(document.frmEmpRepTo.cmbSerPerCode.value=='<' || document.frmEmpRepTo.cmbSerPerCode.value=='>') {
		document.frmEmpRepTo.Service1.disabled = false;
		document.frmEmpRepTo.Service2.disabled = true;
		document.frmEmpRepTo.Service2.value='';
		return;
	}
}
 


 function addEXT() {
 	
 	if(!chkboxCheck()) {
		alert('Select at least one criteria and one fields');
		return;
	}
	
	if(!validation())
		return;
	
	parent.scroll(0,0);
	document.frmEmpRepTo.sqlState.value="NewRecord";
	document.frmEmpRepTo.submit(); 
}	

 function validation() {
 	
 		if(document.frmEmpRepTo.txtRepName.value=='') {
 			alert("Report Name Empty");
			document.frmEmpRepTo.txtRepName.focus();
			return false;
 		}
 		
		with (document.frmEmpRepTo) {
			for (var i=0; i < elements.length; i++) {
				if (elements[i].name == 'chkcriteria[]') {
					
					 switch(elements[i].id) {
					 	case 'EMPNO'      : 
					 						if(elements[i].checked && document.frmEmpRepTo.txtRepEmpID.value=='') {
												alert("Field should be selected");
												document.frmEmpRepTo.txtRepEmpID.focus();
												return false;
											}
											break;
						case 'AgeGroup'   :  
											
											if(elements[i].checked && document.frmEmpRepTo.cmbAgeCode.value=="0") {
												alert("Select The Comparison");
												document.frmEmpRepTo.cmbAgeCode.focus();
												return false;
											} else if(elements[i].checked && document.frmEmpRepTo.cmbAgeCode.value=='range') {
											
												if(!numeric(document.frmEmpRepTo.txtEmpAge1)) {
													alert("Age Should Be Numeric");
													document.frmEmpRepTo.txtEmpAge1.focus();
													return false;
												}
												
												if(!numeric(document.frmEmpRepTo.txtEmpAge2)) {
													alert("Age Should Be Numeric");
													document.frmEmpRepTo.txtEmpAge2.focus();
													return false;
												}
										
												if(eval(document.frmEmpRepTo.txtEmpAge1.value) > eval(document.frmEmpRepTo.txtEmpAge2.value)) {
													alert("2nd Selected Age Lager Than The 1st Selected Age");
													document.frmEmpRepTo.txtEmpAge2.focus();
													return flase;
												}
											} else if(elements[i].checked && document.frmEmpRepTo.cmbAgeCode.value=='<' || document.frmEmpRepTo.cmbAgeCode.value=='>') {
												
												if(!numeric(document.frmEmpRepTo.txtEmpAge1)) {
													alert("Age Should Be Numeric");
													document.frmEmpRepTo.txtEmpAge1.focus();
													return false;
												}
											}
											break;
											
						case 'PayGrade'   : 
					 						if(elements[i].checked && document.frmEmpRepTo.cmbSalGrd.value=="0") {
												alert("Field Not Selected");
												document.frmEmpRepTo.cmbSalGrd.focus();
												return false;
											}
											break;
											
						case 'QualType'   : 
											if(elements[i].checked && document.frmEmpRepTo.TypeCode.value=="0") {
												alert("Field Empty");
												document.frmEmpRepTo.TypeCode.focus();
												return false;
											}
											
											break;
											
						case 'Emptype'    : 
											if(elements[i].checked && document.frmEmpRepTo.cmbEmpType.value=="0") {
												alert("Field Empty");
												document.frmEmpRepTo.cmbEmpType.focus();
												return false;
											}
											break;
											
						case 'SerPeriod'  : 
											if(elements[i].checked && document.frmEmpRepTo.cmbSerPerCode.value=="0") {
												alert("Select The Comparison");
												document.frmEmpRepTo.cmbSerPerCode.focus();
												return false;
											} else if(elements[i].checked && document.frmEmpRepTo.cmbSerPerCode.value=='range') {
												
												if(!numeric(document.frmEmpRepTo.Service1)) {
													alert("Date Should Be Numeric");
													document.frmEmpRepTo.Service1.focus();
													return false;
												}
												
												if(!numeric(document.frmEmpRepTo.Service2)) {
													alert("Date Should Be Numeric");
													document.frmEmpRepTo.Service2.focus();
													return false;
												}
										
												if(eval(document.frmEmpRepTo.Service1.value) > eval(document.frmEmpRepTo.Service2.value)) {
													alert("2nd Selected Date Lager Than The 1st Selected Date");
													document.frmEmpRepTo.Service2.focus();
													return false;
												}
																
															
											} else if(elements[i].checked && document.frmEmpRepTo.cmbSerPerCode.value=='<' || document.frmEmpRepTo.cmbSerPerCode.value=='>') {
												
												if(!numeric(document.frmEmpRepTo.Service1)) {
													alert("Age Should Be Numeric");
													document.frmEmpRepTo.Service1.focus();
													return false;
												}
											}
											break;
											
						case 'JobTitle': 
											if(elements[i].checked && document.frmEmpRepTo.cmbDesig.value=='0') {
												alert("Field Empty");
												document.frmEmpRepTo.cmbDesig.focus();
												return false;
											}
											break;						
					 }
				}
			}
		}
		
 return true;
 }		


</script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<link href="../../themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("../../themes/beyondT/css/style1.css"); </style>
</head>
<? if ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'addmode')) { ?>	
<body>
<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
  <tr>
    <td valign='top'>&nbsp; </td>
    <td width='100%'><h2><?=$headingInfo[0]?></h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>
</table>
<table border="0" >
  <tr>
  <td valign="middle" height="35"><img title="Back" onMouseOut="this.src='../../themes/beyondT/pictures/btn_back.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_back_02.jpg';"  src="../../themes/beyondT/pictures/btn_back.jpg" onClick="goBack();"></td>
  </tr>
</table>

<p>
<p>
<table border="0">
<form name="frmEmpRepTo" method="post" action="<?=$_SERVER['PHP_SELF']?>?repcode=<?=$this->getArr['repcode']?>&capturemode=addmode">
<input type="hidden" name="sqlState">


<tr><td>
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
                      <td ><strong><?=$this->popArr['newID']?></strong></td>
      				 </tr>
    				 <tr>
 					  <td>Report Name</td>
						<td ><input type="text"  name="txtRepName" value="<?=(isset($this->postArr['txtRepName'])  ? $this->postArr['txtRepName'] : '') ?>"  ></td>
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
</td> </tr>
<tr>
    <td valign="bottom" height="35"><h4>Selection Criteria</h4></td>
  </tr>
  <tr><td>
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
                       <td><input type='checkbox' class='checkbox'  name='chkcriteria[]' id='EMPNO' value='EMPNO' onClick="chkboxCriteriaEnable()" <?= (isset($this->postArr['chkcriteria']) && in_array('EMPNO', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> ></td>
                      <td valign="top">EMP No</td>
                      <td align="left" valign="top"><input type="text"  readonly name="txtRepEmpID" value="<?=isset($this->postArr['txtRepEmpID']) ? $this->postArr['txtRepEmpID'] : ''?>"   ></td>
                      <td align="left"><input class="button" type="button"  name="empPop" value=".." onClick="returnEmpDetail();" <?= (isset($this->postArr['chkcriteria']) && in_array('EMPNO', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?>></td>
  					</tr>
 	


					<tr>
					 <td><input type='checkbox' class='checkbox' name='chkcriteria[]' id='AgeGroup' value="AGE" onClick="chkboxCriteriaEnable()" <?= (isset($this->postArr['chkcriteria']) && in_array('AGE', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> ></td>
  					 <td valign="top">Age Group</td>
					 <td align="left" valign="top"> <select   name="cmbAgeCode" onChange="disableAgeField();" <?= (isset($this->postArr['chkcriteria']) && in_array('AGE', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?> class="cmb" >
 					  <option value="0">--Select Comparison-</option>
<?					  
							$keys   = array_keys($arrAgeSim);
							$values = array_values($arrAgeSim);
							
							for($c=0;count($arrAgeSim)>$c;$c++)
								if(isset($this->postArr['cmbAgeCode']) && $this->postArr['cmbAgeCode']==$values[$c])
									echo "<option selected value='" .$values[$c]. "'>" .$keys[$c]. "</option>";
								else
									echo "<option value='" .$values[$c]. "'>" .$keys[$c]. "</option>"; 
?>					  
					  </select>
				    </td> 
					<td> <input type="text" <?=isset($this->postArr['txtEmpAge1']) ? $this->postArr['txtEmpAge1'] : 'disabled'?>  name='txtEmpAge1' value="<?=isset($this->postArr['txtEmpAge1']) ? $this->postArr['txtEmpAge1'] : ''?>" ></td>
					<td> <input type="text" <?=isset($this->postArr['txtEmpAge2']) ? $this->postArr['txtEmpAge2'] : 'disabled'?> name='txtEmpAge2' value="<?=isset($this->postArr['txtEmpAge2']) ? $this->postArr['txtEmpAge2'] : ''?>" ></td>
					
					</tr>
	


               
				<tr>
				  <td><input type='checkbox' class='checkbox' name='chkcriteria[]' id='PayGrade' value="PAYGRD" onClick="chkboxCriteriaEnable()" <?= (isset($this->postArr['chkcriteria']) && in_array('PAYGRD', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> ></td> 
				  <td>Pay Grade</td>
			      <td><select  name="cmbSalGrd" <?= (isset($this->postArr['chkcriteria']) && in_array('PAYGRD', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?> class="cmb" > 
			  		<option value="0">--Select Pay. Grade--</option>
<?					$grdlist = $this->popArr['grdlist'];
					for($c=0;$grdlist && count($grdlist)>$c;$c++)
						if(isset($this->postArr['cmbSalGrd']) && $this->postArr['cmbSalGrd']==$grdlist[$c][0])
							echo "<option selected value='" .$grdlist[$c][0]. "'>" .$grdlist[$c][1]. "</option>";
						else
							echo "<option value='" .$grdlist[$c][0]. "'>" .$grdlist[$c][1]. "</option>";
?>			  
			  </select></td>
					</tr>
			

   					<tr>
					<td><input type='checkbox' class='checkbox' name='chkcriteria[]' id='QualType' value="QUL" onClick="chkboxCriteriaEnable()" <?= (isset($this->postArr['chkcriteria']) && in_array('QUL', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> ></td>
				    <td>Education</td>
    				  <td>
					  <select name="TypeCode"  <?= (isset($this->postArr['chkcriteria']) && in_array('QUL', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?> class="cmb" > 
					  <option value=0>-Select Edu. Type-</option>
<?					  
						$edulist=$this -> popArr['edulist'];
						for($c=0;$edulist && count($edulist)>$c;$c++)
							if(isset($this->postArr['TypeCode']) && $this->postArr['TypeCode']==$edulist[$c][0])
							   echo "<option selected value=" . $edulist[$c][0] . ">" . $edulist[$c][2].', '.$edulist[$c][1] . "</option>";
							else
							   echo "<option value=" . $edulist[$c][0] . ">" .$edulist[$c][2].', '. $edulist[$c][1] . "</option>";
?>					  
					  </select></td>
						<td valign="middle"></td>
						<td align="left" valign="middle"></td>
					</tr>

            	
<tr>																														
					  <td><input type='checkbox' class='checkbox' name='chkcriteria[]' id='EmpType' value="EMPSTATUS" onClick="chkboxCriteriaEnable()" <?= (isset($this->postArr['chkcriteria']) && in_array('EMPSTATUS', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> ></td> 
					  <td valign="top">Employee Status</td>
					  	<td><select name="cmbEmpType" <?= (isset($this->postArr['chkcriteria']) && in_array('EMPSTATUS', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?> class="cmb" >
			  		    <option value="0">--Select Empl. Status--</option>
<?
					//if(isset($this->postArr['cmbEmpType'])) {
						$arrEmpType=$this-> popArr['arrEmpType'];
						for($c=0;$arrEmpType && count($arrEmpType)>$c;$c++)
						    echo "<option value=" . $arrEmpType[$c][0] . ">" . $arrEmpType[$c][1] . "</option>";
						//}
?>			        
			         		</select>
						</td>
					</tr>
					

                
					<tr>
					   <td><input type='checkbox' class='checkbox' name='chkcriteria[]' id='SerPeriod' value="SERPIR" onClick="chkboxCriteriaEnable()" <?= (isset($this->postArr['chkcriteria']) && in_array('SERPIR', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> ></td>  
					  <td valign="top">Service Period</td>
					    <td align="left" valign="middle"> <select  name="cmbSerPerCode" onChange="disableSerPeriodField()" <?= (isset($this->postArr['chkcriteria']) && in_array('SERPIR', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?> class="cmb" >
					     <option value="0">--Select Comparison-</option>
<?					  
							$keys   = array_keys($arrAgeSim);
							$values = array_values($arrAgeSim);
							
							for($c=0;count($arrAgeSim)>$c;$c++)
								if(isset($this->postArr['cmbSerPerCode']) && $this->postArr['cmbSerPerCode']==$values[$c])
									echo "<option selected value='" .$values[$c]. "'>" .$keys[$c]. "</option>";
								else
									echo "<option value='" .$values[$c]. "'>" .$keys[$c]. "</option>"; 
							
							?>					  
					     </select>
					  	</td>
				        <td><input type="text" <?=isset($this->postArr['Service1']) ? $this->postArr['Service1'] : 'disabled'?> name="Service1" value="<?=isset($this->postArr['Service1']) ? $this->postArr['Service1'] : ''?>" ></td>
                        <td><input type="text" <?=isset($this->postArr['Service2']) ? $this->postArr['Service2'] : 'disabled'?> name="Service2" value="<?=isset($this->postArr['Service2']) ? $this->postArr['Service2'] : ''?>" ></td>
					</tr>
					

   					<tr>
					 <td><input type='checkbox' class='checkbox' name='chkcriteria[]' id='JobTitle' value="JOBTITLE" onClick="chkboxCriteriaEnable()" <?= (isset($this->postArr['chkcriteria']) && in_array('JOBTITLE', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> ></td>  
					 <td>Job Title</td>
					  <td><select  name="cmbDesig" <?=(isset($this->postArr['chkcriteria']) && in_array('JOBTITLE', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?> class="cmb" >
					  		<option value="0">---Select JobTitle---</option>
<?
							$deslist = $this->popArr['deslist'];
							for($c=0;$deslist && count($deslist)>$c;$c++)
								if(isset($this->postArr['cmbDesig']) && $this->postArr['cmbDesig']==$deslist[$c][0])
									echo "<option selected value='" .$deslist[$c][0]. "'>" .$deslist[$c][1]. "</option>";
								else
									echo "<option value='" .$deslist[$c][0]. "'>" .$deslist[$c][1]. "</option>";
?>					  
					  </select></td>
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
              </table>
</td> </tr>
<tr>
	<td> <img border="0" title="Save" onClick="addEXT();" onMouseOut="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg"></td>
</tr> 
                <tr><td>&nbsp;</td></tr>
  <tr>
    <td height="15"><h4>Field</h4></td>
  </tr>
<tr><td>
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
                      	 <td><input type='checkbox' checked class='checkbox' name='checkfield[]' value='EMPNO'></td>
						 <td>Emp No</td>
					</tr>
           		
					<tr>
                      	 <td><input type='checkbox' checked class='checkbox' name='checkfield[]' value='EMPLASTNAME'></td>
						 <td>Last Name</td>
					</tr>
                 
                
					<tr>
                      	 <td><input type='checkbox' checked class='checkbox' name='checkfield[]' value='ADDRESS1'></td>
						 <td>Address</td>
					</tr>
                
					<tr>
                      	 <td><input type='checkbox' checked class='checkbox' name='checkfield[]' value='TELENO'></td>
						 <td>Tel No</td>
					</tr>
                 
					<tr>
                      	 <td><input type='checkbox' checked class='checkbox' name='checkfield[]' value='AGE'></td>
						 <td>Date Of Birth</td>
					</tr>
                
					<tr>
                      	 <td><input type='checkbox' checked class='checkbox' name='checkfield[]' value='JOBTITLE'></td>
						 <td>Job Title</td>
					</tr>
               
					<tr>
                      	 <td><input type='checkbox' checked class='checkbox' name='checkfield[]' value='SERPIR'></td>
						 <td>Joined Date</td>
					</tr>
               
					<tr>
                      	 <td><input type='checkbox' checked class='checkbox' name='checkfield[]' value='QUL'></td>
						 <td>Qualification</td>
					</tr>
               
					<tr>
                      	 <td><input type='checkbox' checked class='checkbox' name='checkfield[]' value='EMPSTATUS'></td>
						 <td>Employee Status</td>
					</tr>
               
					<tr>
                      	 <td><input type='checkbox' checked class='checkbox' name='checkfield[]' value='PAYGRD'></td>
						 <td>Pay Grade</td>
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
</td></tr>
</table>
</form>
</body>
<? } else if ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'updatemode')) {  ?>
<body>
<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
  <tr>
    <td valign='top'>&nbsp; </td>
    <td width='100%'><h2><?=$headingInfo[1]?></h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>
</table>
<table border="0" >
  <tr>
  <td valign="middle" height="35"><img title="Back" onMouseOut="this.src='../../themes/beyondT/pictures/btn_back.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_back_02.jpg';"  src="../../themes/beyondT/pictures/btn_back.jpg" onClick="goBack();"></td>
  </tr>
</table>

<p>
<p>
<table border="0">
<form name="frmEmpRepTo" method="post" action="<?=$_SERVER['PHP_SELF']?>?repcode=<?=$this->getArr['repcode']?>&id=<?=$this->getArr['id']?>&capturemode=updatemode">
<input type="hidden" name="sqlState">

<? $edit = $this->popArr['editArr']; ?>
<tr><td>
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
    				  <td ><strong><?=$edit[0][0]?></strong><input type="hidden" name="txtRepID" value="<?=$edit[0][0]?>"></td>
    				 
    				 </tr>
    				 <tr>
 					  <td>Report Name</td>
					  <td ><input type="text" disabled name="txtRepName" value="<?=isset($this->post['txtRepName']) ? $this->post['txtRepName'] : $edit[0][1]?>"></td>
					</tr>
    				 <tr>
 					  <td><b><a href="<?=$_SERVER['PHP_SELF']?>?id=<?=$message[0][0]?>&repcode=RUG">Assign User Groups</a></b></td>
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
</td> </tr>
<tr>
    <td valign="bottom" height="35"><h4>Selection Criteria</h4></td>
  </tr>
  <tr><td>
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
                  
                  <? $editCriteriaChk= $this->popArr['editCriteriaChk'];?>
                   <?$criteriaData=$this->popArr['editCriteriaData'];?>
                    <tr>
                       <td><input <?=isset($_POST['txtRepName']) ? '' : 'disabled'?> type='checkbox' <?=in_array('EMPNO',$editCriteriaChk) ? 'checked' : ''?> class='checkbox'  name='chkcriteria[]' id='EMPNO' value='EMPNO' onClick="chkboxCriteriaEnable()" <?= (isset($this->postArr['chkcriteria']) && in_array('EMPNO', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> ></td>
                      <td valign="top">EMP No</td>
                      <td align="left" valign="top"><input type="text"  readonly name="txtRepEmpID" value="<?=isset($criteriaData['EMPNO'][0]) ? $criteriaData['EMPNO'][0] : ''?>"   ></td>
                      <td align="left"><input class="button" type="button"  name="empPop" value=".." onClick="returnEmpDetail();" <?= (isset($this->postArr['chkcriteria']) && in_array('EMPNO', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?>></td>
  					</tr>
 	


					<tr>
					 <td><input <?=isset($_POST['txtRepName']) ? '' : 'disabled'?> type='checkbox' <?=in_array('AGE',$editCriteriaChk) ? 'checked' : ''?> type='checkbox' class='checkbox' name='chkcriteria[]' id='AgeGroup' value="AGE" onclick="chkboxCriteriaEnable()" <?= (isset($this->postArr['chkcriteria']) && in_array('AGE', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> ></td>
  					 <td valign="top">Age Group</td>
					 <td align="left" valign="top"> <select name="cmbAgeCode" onChange="disableAgeField()" <?= (isset($this->postArr['chkcriteria']) && in_array('AGE', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?> class="cmb" >
 					  <option value="0">--Select Comparison-</option>
<?					  
							$keys   = array_keys($arrAgeSim);
							$values = array_values($arrAgeSim);
							
							for($c=0;count($arrAgeSim)>$c;$c++)
								if(isset($this->postArr['cmbAgeCode'])) { 
									if($this->postArr['cmbAgeCode']==$values[$c])
										echo "<option selected value='" .$values[$c]. "'>" .$keys[$c]. "</option>";
									else
										echo "<option value='" .$values[$c]. "'>" .$keys[$c]. "</option>"; 
								} else {
									if($criteriaData['AGE'][0]==$values[$c])
										echo "<option selected value='" .$values[$c]. "'>" .$keys[$c]. "</option>";
									else
										echo "<option value='" .$values[$c]. "'>" .$keys[$c]. "</option>"; 
								}
?>
					  </select>
				    </td> 
					<td> <input type="text" disabled name='txtEmpAge1' value="<?=isset($criteriaData['AGE'][1]) ? $criteriaData['AGE'][1] : ''?>" ></td>
					<td> <input type="text" disabled name='txtEmpAge2' value="<?=isset($criteriaData['AGE'][2]) ? $criteriaData['AGE'][2] : ''?>" ></td>
					
					</tr>
	
<?/*$exception_handler = new ExceptionHandler();
	  	 	$exception_handler->logW(print_r($criteriaData).'ddd');*/?>

               
					<tr>
				  <td><input <?=isset($_POST['txtRepName']) ? '' : 'disabled'?> type='checkbox' <?=in_array('PAYGRD',$editCriteriaChk) ? 'checked' : ''?> type='checkbox' class='checkbox' name='chkcriteria[]' id='PayGrade' value="PAYGRD" onclick="chkboxCriteriaEnable()" <?= (isset($this->postArr['chkcriteria']) && in_array('PAYGRD', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> ></td> 
				  <td>Pay Grade</td>
			      <td><select  name="cmbSalGrd"   <?= (isset($this->postArr['chkcriteria']) && in_array('PAYGRD', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?> class="cmb" > 
		<option value="0">--Select Sal. Grade--</option>
<?					$grdlist = $this->popArr['grdlist'];
					for($c=0;$grdlist && count($grdlist)>$c;$c++)
						if(isset($this->postArr['cmbSalGrd'])) {
							if($this->postArr['cmbSalGrd']==$grdlist[$c][0])
								echo "<option selected value='" .$grdlist[$c][0]. "'>" .$grdlist[$c][1]. "</option>";
							else
								echo "<option value='" .$grdlist[$c][0]. "'>" .$grdlist[$c][1]. "</option>";
						} else {
							if($criteriaData['PAYGRD'][0]==$grdlist[$c][0])
								echo "<option selected value='" .$grdlist[$c][0]. "'>" .$grdlist[$c][1]. "</option>";
							else
								echo "<option value='" .$grdlist[$c][0]. "'>" .$grdlist[$c][1]. "</option>";
						}
?>			  
			  </select></td>
					</tr>
			

   					<tr>
					<td><input <?=isset($_POST['txtRepName']) ? '' : 'disabled'?> type='checkbox' <?=in_array('QUL',$editCriteriaChk) ? 'checked' : ''?> type='checkbox' class='checkbox' name='chkcriteria[]' id='QualType' value="QUL" onclick="chkboxCriteriaEnable()" <?= (isset($this->postArr['chkcriteria']) && in_array('QUL', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> ></td>
				    <td>Education</td>
    				  <td>
					  <select name="TypeCode"  <?= (isset($this->postArr['chkcriteria']) && in_array('QUL', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?> class="cmb" > 
					  <option value=0>-Select Edu. Type-</option>
<?					  
						$edulist=$this -> popArr['edulist'];
						for($c=0;$edulist && count($edulist)>$c;$c++)
							if(isset($this->postArr['TypeCode']) && $this->postArr['TypeCode']==$edulist[$c][0])
							   echo "<option selected value=" . $edulist[$c][0] . ">" . $edulist[$c][2].', '.$edulist[$c][1] . "</option>";
							else
							   echo "<option value=" . $edulist[$c][0] . ">" .$edulist[$c][2].', '. $edulist[$c][1] . "</option>";
?>					  
					  </select></td>
						<td valign="middle"></td>
						<td align="left" valign="middle"></td>
					</tr>

            	
<tr>					
																									
					  <td><input <?=isset($_POST['txtRepName']) ? '' : 'disabled'?> type='checkbox' <?=in_array('EMPSTATUS',$editCriteriaChk) ? 'checked' : ''?> type='checkbox' class='checkbox' name='chkcriteria[]' id='EmpType' value="EMPSTATUS" onclick="chkboxCriteriaEnable()" <?= (isset($this->postArr['chkcriteria']) && in_array('EMPSTATUS', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> ></td> 
					  <td valign="top">Employee States</td>
					  	<td><select name="cmbEmpType"  <?= (isset($this->postArr['chkcriteria']) && in_array('EMPSTATUS', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?> class="cmb" >
			  		<option value="0">--Select Empl. Type--</option>
<?							for($c=0;count($arrEmpType)>$c;$c++)
								if(isset($this->postArr['cmbEmpType'])){
									if($this->postArr['cmbEmpType']==$arrEmpType[$c])
										echo "<option selected>" .$arrEmpType[$c]. "</option>";
									else
										echo "<option>" .$arrEmpType[$c]. "</option>";
								} else {
									if($criteriaData['EMPSTATUS'][0]==$arrEmpType[$c])
										echo "<option selected>" .$arrEmpType[$c]. "</option>";
									else
										echo "<option>" .$arrEmpType[$c]. "</option>";
								}
?>			        
			         		</select>
						</td>
					</tr>
					

                
					<tr>
					   <td><input <?=isset($_POST['txtRepName']) ? '' : 'disabled'?> type='checkbox' <?=in_array('SERPIR',$editCriteriaChk) ? 'checked' : ''?> type='checkbox' class='checkbox' name='chkcriteria[]' id='SerPeriod' value="SERPIR" onclick="chkboxCriteriaEnable()" <?= (isset($this->postArr['chkcriteria']) && in_array('SERPIR', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> ></td>  
					  <td valign="top">Service Period</td>
					    <td align="left" valign="middle"> <select  name="cmbSerPerCode" onChange="disableSerPeriodField()"  <?= (isset($this->postArr['chkcriteria']) && in_array('SERPIR', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?>  class="cmb" >
					     <option value="0">--Select Comparison-</option>
<?					  
							$keys   = array_keys($arrAgeSim);
							$values = array_values($arrAgeSim);
							
							for($c=0;count($arrAgeSim)>$c;$c++)
								if(isset($this->postArr['cmbSerPerCode'])){
									 if($this->postArr['cmbSerPerCode']==$values[$c])
										echo "<option selected value='" .$values[$c]. "'>" .$keys[$c]. "</option>";
									else
										echo "<option value='" .$values[$c]. "'>" .$keys[$c]. "</option>"; 
								} else {
									  if($criteriaData['SERPIR'][0]==$values[$c])
										echo "<option selected value='" .$values[$c]. "'>" .$keys[$c]. "</option>";
									else
										echo "<option value='" .$values[$c]. "'>" .$keys[$c]. "</option>";
								}
							?>					  
					     </select>
					  	</td>
				        <td><input type="text" disabled name="Service1" value="<?=isset($criteriaData['SERPIR'][1]) ? $criteriaData['SERPIR'][1] : ''?>" ></td>
                        <td><input type="text" disabled name="Service2" value="<?=isset($criteriaData['SERPIR'][2]) ? $criteriaData['SERPIR'][2] : ''?>" ></td>
					</tr>
					
   					<tr>
					 <td><input <?=isset($_POST['txtRepName']) ? '' : 'disabled'?> type='checkbox' <?=in_array('JOBTITLE',$editCriteriaChk) ? 'checked' : ''?> type='checkbox' class='checkbox' name='chkcriteria[]' id='JobTitle' value="JOBTITLE" onclick="chkboxCriteriaEnable()" <?= (isset($this->postArr['chkcriteria']) && in_array('JOBTITLE', $this->postArr['chkcriteria'] )) ? 'checked' : '' ?> ></td> 
					  
					 <td>Job Title</td>
					  <td><select  name="cmbDesig"  <?=(isset($this->postArr['chkcriteria']) && in_array('JOBTITLE', $this->postArr['chkcriteria'] )) ? '' : 'disabled' ?> class="cmb" >
				  		<option value="0">---Select JobTitle---</option>
<?
							$deslist = $this->popArr['deslist'];
							for($c=0;$deslist && count($deslist)>$c;$c++)
								if(isset($this->postArr['cmbDesig'])) {
									if($this->postArr['cmbDesig']==$deslist[$c][0])
										
											echo "<option selected value='" .$deslist[$c][0]. "'>" .$deslist[$c][1]. "</option>";
										else
											echo "<option value='" .$deslist[$c][0]. "'>" .$deslist[$c][1]. "</option>";
								} else {
									 if($criteriaData['JOBTITLE'][0]==$deslist[$c][0])
										
											echo "<option selected value='" .$deslist[$c][0]. "'>" .$deslist[$c][1]. "</option>";
										else
											echo "<option value='" .$deslist[$c][0]. "'>" .$deslist[$c][1]. "</option>";
								}
		?>					  
					  </select></td>
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
              </table>
</td> </tr>
<tr>
	<td><img src="../../themes/beyondT/pictures/btn_edit.jpg" title="Edit" onMouseOut="mout();" onMouseOver="mover();" name="Edit" onClick="edit();"></td>
</tr> 
                <tr><td>&nbsp;</td></tr>
  <tr>
    <td height="15"><h4>Field</h4></td>
  </tr>
<tr><td>
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
                  <? $fieldArr= $this->popArr['fieldList'];?>
                  
                    <tr>
                      	 <td><input disabled type='checkbox' <?=in_array('EMPNO',$fieldArr) ? 'checked': ''?>  class='checkbox' name='checkfield[]' value='EMPNO'></td>
						 <td>Emp No</td>
					</tr>
                
                 
                  	
					<tr>
                      	 <td><input disabled type='checkbox' <?=in_array('EMPLASTNAME',$fieldArr) ? 'checked': ''?>  class='checkbox' name='checkfield[]' value='EMPLASTNAME'></td>
						 <td>Last Name</td>
					</tr>
                 
                
					<tr>
                      	 <td><input disabled type='checkbox' <?=in_array('ADDRESS1',$fieldArr) ? 'checked': ''?>  class='checkbox' name='checkfield[]' value='ADDRESS1'></td>
						 <td>Address</td>
					</tr>
                
                 
                  	
					<tr>
                      	 <td><input disabled type='checkbox' <?=in_array('TELENO',$fieldArr) ? 'checked': ''?>  class='checkbox' name='checkfield[]' value='TELENO'></td>
						 <td>Tel No</td>
					</tr>
                
					<tr>
                      	 <td><input disabled type='checkbox' <?=in_array('AGE',$fieldArr) ? 'checked': ''?>  class='checkbox' name='checkfield[]' value='AGE'></td>
						 <td>Date Of Birth</td>
					</tr>
                
					<tr>
                      	 <td><input disabled type='checkbox' <?=in_array('JOBTITLE',$fieldArr) ? 'checked': ''?>  class='checkbox' name='checkfield[]' value='JOBTITLE'></td>
						 <td>Job Title</td>
					</tr>
               
					<tr>
                      	 <td><input disabled type='checkbox' <?=in_array('SERPIR',$fieldArr) ? 'checked': ''?>  class='checkbox' name='checkfield[]' value='SERPIR'></td>
						 <td>Join Date</td>
					</tr>
              
					<tr>
                      	 <td><input disabled type='checkbox' <?=in_array('QUL',$fieldArr) ? 'checked': ''?>  class='checkbox' name='checkfield[]' value='QUL'></td>
						 <td>Qualification</td>
					</tr>
               
					<tr>
                      	 <td><input disabled type='checkbox' <?=in_array('EMPSTATUS',$fieldArr) ? 'checked': ''?>  class='checkbox' name='checkfield[]' value='EMPSTATUS'></td>
						 <td>Employee States</td>
					</tr>
               
					<tr>
                      	 <td><input disabled type='checkbox' <?=in_array('PAYGRD',$fieldArr) ? 'checked': ''?>  class='checkbox' name='checkfield[]' value='PAYGRD'></td>
						 <td>Pay Grade</td>
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
</td></tr>
</table>
</form>
</body>	
<?}?>
</html>