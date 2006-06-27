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

	
	if ((isset($this ->getArr['capturemode'])) && ($this ->getArr['capturemode'] == 'addmode')) {
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script>			
	function goBack() {
		location.href = "./CentralController.php?mtcode=<?=$this->getArr['mtcode']?>&VIEW=MAIN";
		
	}

	function addSave() {
		
		if (document.frmUserGroup.txtUserGroupName.value == '') {
			alert ("Description Cannot be a Blank Value!");
			return false;
		}
		
		document.frmUserGroup.sqlState.value = "NewRecord";
		document.frmUserGroup.submit();
	}
	
	function clearAll() {
		document.frmUserGroup.txtUserGroupName.value = '';
	}
</script>
<link href="../../themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("../../themes/beyondT/css/style.css"); </style>
</head>
<body>
<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
  <tr>
    <td valign='top'></td>
    <td width='100%'><h2>User Group</h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>
</table>
<p>
<p> 
<table width="431" border="0" cellspacing="0" cellpadding="0" ><td width="177">
<form name="frmUserGroup" method="post" action="<?=$_SERVER['PHP_SELF']?>?mtcode=<?=$this->getArr['mtcode']?>">

  <tr> 
    <td height="27" valign='top'> <p> <img title="Back" onmouseout="this.src='../../themes/beyondT/pictures/btn_back.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_back_02.jpg';"  src="../../themes/beyondT/pictures/btn_back.jpg" onclick="goBack();">
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
                  <td><table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
						  <tr>
       							<td>Code</td>
							    <td><strong><?=$this->popArr['newID'] ?></strong></td>
							  </tr>
						  <tr> 
						    <td>Description</td>
						    <td> <textarea name='txtUserGroupName' rows="3" tabindex='3' cols="30"></textarea>
						    </td>
						  </tr>
						  <tr> 
						    <td>Define Reports</td>
						    <td> <input type="checkbox" name='chkRepDef' value="1"></td>
						  </tr>
					  <tr><td></td><td align="right" width="100%"><img onClick="addSave();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
        <img onClick="clearAll();" onmouseout="this.src='../../themes/beyondT/pictures/btn_clear.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_clear_02.jpg';" src="../../themes/beyondT/pictures/btn_clear.jpg"></td></tr>

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
<? } else if ((isset($this ->getArr['capturemode'])) && ($this ->getArr['capturemode'] == 'updatemode')) {
	 $message = $this -> popArr['editArr'];
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<script>			

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
	
	var frm=document.frmUserGroup;
//  alert(frm.elements.length);
	for (var i=0; i < frm.elements.length; i++)
		frm.elements[i].disabled = false;
	document.Edit.src="../../themes/beyondT/pictures/btn_save.jpg";
	document.Edit.title="Save";
}


	function addUpdate() {
		
		if (document.frmUserGroup.txtUserGroupName.value == '') {
			alert ("Description Cannot be a Blank Value!");
			return false;
		}
		
		document.frmUserGroup.sqlState.value = "UpdateRecord";
		document.frmUserGroup.submit();
	}			

	function clearAll() {
		if(document.Edit.title!='Save') 
			return;
			
		document.frmUserGroup.txtUserGroupName.value = '';
	}
	
</script>
<link href="../../themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("../../themes/beyondT/css/style.css"); </style>
</head>
<body>
<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
  <tr>
    <td valign='top'></td>
    <td width='100%'><h2>User Groups</h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>
</table>
<p>
<p> 
<table width="431" border="0" cellspacing="0" cellpadding="0" ><td width="177">
<form name="frmUserGroup" method="post" action="<?=$_SERVER['PHP_SELF']?>?id=<?=$this->getArr['mtcode']?>&mtcode=<?=$this->getArr['mtcode']?>&capturemode=updatemode">

  <tr> 
    <td height="27" valign='top'> <p>  <img title="Back" onmouseout="this.src='../../themes/beyondT/pictures/btn_back.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_back_02.jpg';" src="../../themes/beyondT/pictures/btn_back.jpg" onclick="goBack();">
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
                  <td><table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
							  <tr> 
    <td>Code</td>
     <td> <input type="hidden" name="txtUserGroupID" value=<?=$message[0][0]?> ><strong><?=$message[0][0]?></strong> </td>
  </tr>
							  <tr> 
							    <td><strong>Description</td>
							  	  <td> <textarea name='txtUserGroupName' rows="3" tabindex='3' disabled cols="30"><?=$message[0][1]?></textarea>
							    </td>
							  </tr>
						  <tr> 
						    <td>Define Reports</td>
						    <td> <input type="checkbox" disabled <?=$message[0][2] == '1' ? 'checked' : ''?> name='chkRepDef' value="1"></td>
						  </tr>
					  <tr><td><a href="<?=$_SERVER['PHP_SELF']?>?id=<?=$this->getArr['id']?>&mtcode=UGR">Assign User Rights</a></td>
					  <td align="right" width="100%">
<?			if($locRights['edit']) { ?>
			        <img src="../../themes/beyondT/pictures/btn_edit.jpg" title="Edit" onmouseout="mout();" onmouseover="mover();" name="Edit" onClick="edit();">
<?			} else { ?>
			        <img src="../../themes/beyondT/pictures/btn_edit.jpg" onClick="alert('<?=$sysConst->accessDenied?>');">
<?			}  ?>
					  <img src="../../themes/beyondT/pictures/btn_clear.jpg" onmouseout="this.src='../../themes/beyondT/pictures/btn_clear.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_clear_02.jpg';" onClick="clearAll();" >
					  
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
